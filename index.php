<?php
// Punto de entrada. Redirige según sesión/rol.
require_once __DIR__ . '/models/seg.php';
start_secure_session();
// Simple rate limit por IP para evitar abuso de index
$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
if (!check_rate_limit('index_' . $ip, 300, 60)) {
    http_response_code(429);
    echo 'Demasiadas peticiones. Intenta más tarde.';
    exit;
}

$rol = $_SESSION['rol'] ?? null;
if (!$rol) {
    header('Location: views/auth/vlogin.php');
    exit;
}
// Dashboard por rol
switch ($rol) {
    case 'admin':
        header('Location: views/admin/vdashboard.php');
        break;
    case 'mesero':
        header('Location: views/pedidos/vdashboard_mesero.php');
        break;
    case 'cajero':
        header('Location: views/facturacion/vdashboard_cajero.php');
        break;
    case 'cliente':
        header('Location: views/catalogo/vdashboard_cliente.php');
        break;
    default:
        header('Location: views/auth/vlogin.php');
}
exit;