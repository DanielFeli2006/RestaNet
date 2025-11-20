<?php
// Configuración general del sistema Restanet
define('DB_HOST', 'localhost');
define('DB_NAME', 'restanetV1');
define('DB_USER', 'root');
define('DB_PASS', '');

date_default_timezone_set('America/Bogota');

// Ajusta la ruta base según tu ubicación en htdocs.
// En este workspace el proyecto está en: /Proyects/restanet/
define('BASE_PATH', '/Proyects/restanet/');
define('BASE_URL', (isset($_SERVER['HTTP_HOST'])
	? ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST']
	: '') . BASE_PATH);

define('ITEMS_PER_PAGE', 10);

if (!defined('SESSION_TIMEOUT')) {
    $envTimeout = getenv('RESTANET_SESSION_TIMEOUT');
    define('SESSION_TIMEOUT', ($envTimeout !== false ? max(300, (int) $envTimeout) : 600)); // 10 min por defecto
}
if (!defined('SESSION_TIMEOUT_GRACE')) {
    define('SESSION_TIMEOUT_GRACE', 120); // ventana de gracia opcional en segundos
}

$autoloadPath = __DIR__ . '/../vendor/autoload.php';
if (file_exists($autoloadPath)) {
	require_once $autoloadPath;
}

/*
 * Configuración de correo (PHPMailer)
 * - Para activar el envío real establece MAIL_ENABLED en true y completa los datos.
 * - En entornos de desarrollo puedes dejar MAIL_ENABLED = false; el sistema mostrará el enlace de restablecimiento en pantalla para pruebas.
 */
if (!defined('MAIL_ENABLED')) define('MAIL_ENABLED', false);
if (!defined('MAIL_HOST')) define('MAIL_HOST', 'smtp.example.com');
if (!defined('MAIL_PORT')) define('MAIL_PORT', 587);
if (!defined('MAIL_USERNAME')) define('MAIL_USERNAME', 'usuario@example.com');
if (!defined('MAIL_PASSWORD')) define('MAIL_PASSWORD', 'tu-contraseña');
if (!defined('MAIL_SMTP_SECURE')) define('MAIL_SMTP_SECURE', 'tls'); // 'tls' o 'ssl' o ''
if (!defined('MAIL_FROM')) define('MAIL_FROM', 'no-reply@localhost');
if (!defined('MAIL_FROM_NAME')) define('MAIL_FROM_NAME', 'RestaNet');

?>