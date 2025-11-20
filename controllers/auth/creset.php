<?php
// Controlador para restablecimiento de contraseña
require_once __DIR__ . '/../../models/conexion.php';
require_once __DIR__ . '/../../models/seg.php';

$action = $_GET['a'] ?? 'request';

// Mostrar formulario para solicitar restablecimiento
if ($action === 'request') {
    include __DIR__ . '/../../views/auth/vforgot.php';
    exit;
}

// Enviar email con token
if ($action === 'send' && $_SERVER['REQUEST_METHOD'] === 'POST') {
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

    // Envío de email usando PHPMailer configurado en models/config.php si MAIL_ENABLED
    $sent = false;
    if (defined('MAIL_ENABLED') && MAIL_ENABLED && class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
        try {
            $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
            // Configurar SMTP si se indicó
            $mail->isSMTP();
            $mail->Host = MAIL_HOST;
            $mail->Port = MAIL_PORT;
            if (!empty(MAIL_SMTP_SECURE)) $mail->SMTPSecure = MAIL_SMTP_SECURE;
            $mail->SMTPAuth = true;
            $mail->Username = MAIL_USERNAME;
            $mail->Password = MAIL_PASSWORD;
            $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
            $mail->addAddress($email, $user['nombre']);
            $mail->Subject = 'Restablecer contraseña';
            $body = "Hola " . htmlspecialchars($user['nombre']) . ",\n\n" .
                "Recibimos una solicitud para restablecer tu contraseña. Si la solicitaste, visita el siguiente enlace:\n\n" .
                $resetLink . "\n\n" .
                "Si no solicitaste este cambio, ignora este mensaje.\n\n" .
                "Atentamente,\nRestaNet";
            $mail->Body = $body;
            $mail->send();
            $sent = true;
        } catch (Exception $e) {
            // Si falla el envío SMTP, seguir y mostrar enlace en modo debug
            $sent = false;
        }
    }

    // Mensaje genérico para seguridad. Si el envío no se realizó y MAIL_ENABLED=false, incluir el enlace de prueba en la sesión (solo dev).
    if ($sent) {
        $_SESSION['success'] = 'Si el correo existe en nuestro sistema, recibirás un mensaje con instrucciones.';
    } else {
        if (defined('MAIL_ENABLED') && MAIL_ENABLED) {
            $_SESSION['success'] = 'Se intentó enviar el correo. Si no lo recibes, contacta al administrador.';
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

