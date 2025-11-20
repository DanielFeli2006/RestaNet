<?php
// Seguridad centralizada: sesiones, roles y helpers de hash
require_once __DIR__ . '/config.php';

class Seg {
    /**
     * Esquema por defecto: SHA-256(MD5(password)) → hex de 64 chars
     */
    public static function hashPassword(string $password): string {
        return hash('sha256', md5($password));
    }
    public static function verifyPassword(string $plain, string $stored): bool {
        // Compatibilidad con bcrypt/argon/sha256/md5 y nuestro esquema sha256(md5())
        if (str_starts_with($stored, '$2y$') || str_starts_with($stored, '$argon2')) {
            return password_verify($plain, $stored);
        }
        $s = strtolower($stored);
        // 64 hex: intentar primero nuestro esquema sha256(md5(plain)), luego sha256(plain) para compatibilidad
        if (preg_match('/^[a-f0-9]{64}$/', $s)) {
            $sha256_md5 = hash('sha256', md5($plain));
            if (hash_equals($sha256_md5, $s)) return true;
            $sha256_plain = hash('sha256', $plain);
            if (hash_equals($sha256_plain, $s)) return true;
            return false;
        }
        // 32 hex: MD5 plano (compatibilidad heredada)
        if (preg_match('/^[a-f0-9]{32}$/', $s)) {
            return hash_equals(md5($plain), $s);
        }
        return false;
    }
}

function start_secure_session(): void {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        // Configurar cookies seguras.
        // Usar path '/' y no forzar domain (evita problemas con host:port).
        $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || ($_SERVER['SERVER_PORT'] ?? '') === '443';
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'domain' => '',
            'secure' => $secure,
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
        session_start();
    }
    // Enforce inactivity timeout
    $timeout = defined('SESSION_TIMEOUT') ? (int)SESSION_TIMEOUT : 1800; // 30min
    $last = $_SESSION['last_activity'] ?? null;
    if ($last !== null && (time() - $last) > $timeout) {
        // destruir sesión por inactividad
        session_unset();
        session_destroy();
        // iniciar una nueva sesión limpia
        session_start();
    }
    $_SESSION['last_activity'] = time();
}

function is_logged_in(): bool {
    start_secure_session();
    return isset($_SESSION['id']) && isset($_SESSION['rol']);
}

function require_login(): void {
    if (!is_logged_in()) {
        header('Location: ' . BASE_PATH . 'views/auth/vlogin.php');
        exit();
    }
}

function has_role($roles): bool {
    start_secure_session();
    $current = $_SESSION['rol'] ?? null;
    if (!$current) return false;
    if (is_string($roles)) $roles = [$roles];
    return in_array($current, $roles, true);
}

function require_role($roles): void {
    if (!has_role($roles)) {
        header('HTTP/1.1 403 Forbidden');
        header('Location: ' . BASE_PATH . 'views/auth/vlogin.php');
        exit();
    }
}

// Helpers adicionales para granularidad futura
function require_exact_role(string $role): void {
    if (!has_role($role)) {
        header('HTTP/1.1 403 Forbidden');
        exit('Acceso denegado');
    }
}

function can_access(array|string $roles): bool {
    return has_role($roles);
}

// CSRF helpers
function csrf_token(): string {
    start_secure_session();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string {
    $t = csrf_token();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($t, ENT_QUOTES|ENT_SUBSTITUTE, 'UTF-8') . '">';
}

function validate_csrf(?string $token): bool {
    start_secure_session();
    if (empty($token) || empty($_SESSION['csrf_token'])) return false;
    return hash_equals($_SESSION['csrf_token'], $token);
}

// Simple escaping helper for views
function e($s): string {
    return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// Simple rate limiter (file-based). Retorna true si permitido, false si se excedió.
function check_rate_limit(string $key, int $maxRequests = 120, int $windowSeconds = 60): bool {
    $dir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'restanet_rate';
    if (!is_dir($dir)) @mkdir($dir, 0755, true);
    $file = $dir . DIRECTORY_SEPARATOR . preg_replace('/[^a-z0-9_\-]/i', '_', $key) . '.log';
    $now = time();
    $entries = [];
    if (file_exists($file)) {
        $contents = @file_get_contents($file);
        if ($contents !== false) {
            $entries = array_filter(explode("\n", $contents), fn($l) => is_numeric($l));
        }
    }
    // Purge old
    $entries = array_filter($entries, fn($ts) => ($now - (int)$ts) <= $windowSeconds);
    if (count($entries) >= $maxRequests) return false;
    $entries[] = (string)$now;
    @file_put_contents($file, implode("\n", $entries));
    return true;
}
