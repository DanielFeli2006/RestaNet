<?php
// Controlador para restablecimiento de contraseña
require_once __DIR__ . '/../../models/conexion.php';
require_once __DIR__ . '/../../models/seg.php';
require_once __DIR__ . '/../../models/mailer.php';

start_secure_session();

$action = $_GET['a'] ?? 'request';

// Mostrar formulario para solicitar restablecimiento
if ($action === 'request') {
    include __DIR__ . '/../../views/auth/vforgot.php';
    exit;
}

// Enviar email con token
if ($action === 'send' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validate_csrf($_POST['csrf_token'] ?? null)) {
        $_SESSION['error'] = 'Token CSRF inválido.';
        header('Location: ' . BASE_PATH . 'controllers/auth/creset.php?a=request');
        exit;
    }

    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = 'Email inválido';
        header('Location: ' . BASE_PATH . 'controllers/auth/creset.php?a=request');
        exit;
    }
    // Verificar usuario existe
    $stmt = $pdo->prepare('SELECT id, nombre FROM usuarios WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user) {
        // No indicar al usuario por seguridad, pero mostrar mensaje genérico
        $_SESSION['success'] = 'Si el correo existe en nuestro sistema, recibirás un mensaje con instrucciones.';
        header('Location: ' . BASE_PATH . 'views/auth/vlogin.php');
        exit;
    }

    // Generar token y guardarlo en tabla password_resets
    $token = bin2hex(random_bytes(16));
    $expires = date('Y-m-d H:i:s', time() + 3600); // 1 hora
    $pdo->prepare('DELETE FROM password_resets WHERE email=?')->execute([$email]);
    $pdo->prepare('INSERT INTO password_resets (email, token, expires_at, created_at) VALUES (?,?,?,NOW())')
        ->execute([$email, $token, $expires]);

    // Construir enlace
    $resetLink = BASE_URL . 'controllers/auth/creset.php?a=form&token=' . urlencode($token);

    $subject = 'RestaNet - Restablecer contraseña';
    $safeName = e($user['nombre']);
    $htmlBody = '<div style="font-family:Arial,sans-serif;color:#1f2937">'
        . '<h2 style="margin:0 0 12px">Recuperación de contraseña</h2>'
        . '<p>Hola ' . $safeName . ',</p>'
        . '<p>Recibimos una solicitud para restablecer tu contraseña.</p>'
        . '<p><a href="' . e($resetLink) . '" style="display:inline-block;padding:10px 16px;background:#C41E3A;color:#fff;text-decoration:none;border-radius:6px">Restablecer contraseña</a></p>'
        . '<p>Si no solicitaste este cambio, puedes ignorar este mensaje.</p>'
        . '<p><small>Este enlace expira en 1 hora.</small></p>'
        . '</div>';
    $textBody = "Hola {$user['nombre']},\n\n"
        . "Recibimos una solicitud para restablecer tu contraseña.\n"
        . "Usa este enlace: {$resetLink}\n\n"
        . "Si no solicitaste este cambio, ignora este mensaje.\n"
        . "Este enlace expira en 1 hora.\n";

    $sent = send_transactional_mail($email, $user['nombre'], $subject, $htmlBody, $textBody);

    // Mensaje genérico para seguridad. Si el envío no se realizó y MAIL_ENABLED=false, incluir el enlace de prueba en la sesión (solo dev).
    if ($sent) {
        $_SESSION['success'] = 'Si el correo existe en nuestro sistema, recibirás un mensaje con instrucciones.';
    } else {
        if (defined('MAIL_ENABLED') && MAIL_ENABLED) {
            $_SESSION['error'] = 'No se pudo enviar el correo de recuperación en este momento. Intenta de nuevo en unos minutos.';
        } else {
            // En entorno de desarrollo mostramos el enlace para pruebas manuales.
            $_SESSION['success'] = 'DEBUG: Enlace de restablecimiento (solo para pruebas): ' . $resetLink;
        }
    }
    header('Location: ' . BASE_PATH . 'views/auth/vlogin.php');
    exit;
}

// Mostrar formulario para nueva contraseña
if ($action === 'form') {
    $token = $_GET['token'] ?? '';
    if (!$token) {
        http_response_code(400);
        echo 'Token inválido';
        exit;
    }
    // comprobar token
    $stmt = $pdo->prepare('SELECT email, expires_at FROM password_resets WHERE token = ? LIMIT 1');
    $stmt->execute([$token]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row || strtotime($row['expires_at']) < time()) {
        $_SESSION['error'] = 'Token inválido o expirado.';
        header('Location: ' . BASE_PATH . 'views/auth/vlogin.php');
        exit;
    }
    include __DIR__ . '/../../views/auth/vreset.php';
    exit;
}

// Actualizar contraseña
if ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'] ?? '';
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';
    if ($password !== $password2) {
        $_SESSION['error'] = 'Las contraseñas no coinciden.';
        header('Location: ' . BASE_PATH . 'controllers/auth/creset.php?a=form&token=' . urlencode($token));
        exit;
    }
    if (strlen($password) < 8) {
        $_SESSION['error'] = 'La contraseña debe tener al menos 8 caracteres.';
        header('Location: ' . BASE_PATH . 'controllers/auth/creset.php?a=form&token=' . urlencode($token));
        exit;
    }
    $stmt = $pdo->prepare('SELECT email FROM password_resets WHERE token = ? LIMIT 1');
    $stmt->execute([$token]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        $_SESSION['error'] = 'Token inválido.';
        header('Location: ' . BASE_PATH . 'views/auth/vlogin.php');
        exit;
    }
    $email = $row['email'];
    // Actualizar contraseña del usuario
    $pdo->prepare('UPDATE usuarios SET password = ? WHERE email = ?')->execute([Seg::hashPassword($password), $email]);
    // Eliminar tokens
    $pdo->prepare('DELETE FROM password_resets WHERE email = ?')->execute([$email]);
    $_SESSION['success'] = 'Contraseña actualizada. Puedes iniciar sesión con tu nueva contraseña.';
    header('Location: ' . BASE_PATH . 'views/auth/vlogin.php');
    exit;
}

http_response_code(400);
echo 'Acción no soportada';

