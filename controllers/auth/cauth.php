<?php
// Controlador de autenticación
require_once __DIR__ . '/../../models/conexion.php';
require_once __DIR__ . '/../../models/seg.php';

$action = $_GET['a'] ?? 'login';

function redirect_by_role(string $rol): void {
    switch ($rol) {
        case 'admin':
            header('Location: ' . BASE_PATH . 'views/admin/vdashboard.php');
            break;
        case 'mesero':
            header('Location: ' . BASE_PATH . 'views/pedidos/vdashboard_mesero.php');
            break;
        case 'cajero':
            header('Location: ' . BASE_PATH . 'views/facturacion/vdashboard_cajero.php');
            break;
        case 'cliente':
            header('Location: ' . BASE_PATH . 'views/catalogo/vdashboard_cliente.php');
            break;
        default:
            header('Location: ' . BASE_PATH . 'index.php');
    }
    exit;
}

if ($action === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    start_secure_session();
    // Rate limit per IP for login attempts
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    if (!check_rate_limit('login_' . $ip, 10, 60)) {
        $_SESSION['error'] = 'Demasiadas peticiones. Intenta más tarde.';
        header('Location: ' . BASE_PATH . 'views/auth/vlogin.php');
        exit;
    }
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    // CSRF check
    if (!validate_csrf($_POST['csrf_token'] ?? null)) {
        // Log para diagnóstico de fallos CSRF / session
        error_log('CSRF validation failed for login from IP: ' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
        $_SESSION['error'] = 'Token CSRF inválido.';
        header('Location: ' . BASE_PATH . 'views/auth/vlogin.php');
        exit;
    }
    // Intentos fallidos (rate limit sencillo)
    $attempts = $_SESSION['login_attempts'] ?? 0;
    $locked_until = $_SESSION['login_locked_until'] ?? 0;
    if ($locked_until && time() < $locked_until) {
        $_SESSION['error'] = 'Demasiados intentos fallidos. Intenta de nuevo en unos minutos.';
        header('Location: ' . BASE_PATH . 'views/auth/vlogin.php');
        exit;
    }

    $stmt = $pdo->prepare('SELECT id, nombre, email, password, rol FROM usuarios WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && Seg::verifyPassword($password, $user['password'])) {
        // Regenerar id de sesión para evitar fijación
        session_regenerate_id(true);
        $_SESSION['id'] = $user['id'];
        $_SESSION['nombre'] = $user['nombre'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['rol'] = $user['rol'];
        unset($_SESSION['login_attempts'], $_SESSION['login_locked_until']);
        redirect_by_role($user['rol']);
    }
    // fallo
    $attempts++;
    $_SESSION['login_attempts'] = $attempts;
    if ($attempts >= 5) {
        $_SESSION['login_locked_until'] = time() + 120; // 2 minutos
    }
    $_SESSION['error'] = 'Credenciales inválidas';
    header('Location: ' . BASE_PATH . 'views/auth/vlogin.php');
    exit;
}

if ($action === 'logout') {
    start_secure_session();
    session_destroy();
    header('Location: ' . BASE_PATH . 'views/auth/vlogin.php');
    exit;
}

if ($action === 'heartbeat') {
    header('Content-Type: application/json');
    start_secure_session();
    if (!is_logged_in()) {
        http_response_code(401);
        echo json_encode(['ok' => false]);
        exit;
    }
    $_SESSION['last_activity'] = time();
    echo json_encode([
        'ok' => true,
        'warning' => $_SESSION['timeout_warning'] ?? null,
        'timedOut' => $_SESSION['timed_out'] ?? false,
    ]);
    exit;
}

http_response_code(400);
echo 'Acción no soportada';