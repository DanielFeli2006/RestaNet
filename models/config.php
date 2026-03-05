<?php
/**
 * Configuración general del sistema RestaNet
 * 
 * SEGURIDAD:
 * - Headers de seguridad HTTP
 * - Configuración de sesiones seguras
 * - Constantes de configuración centralizadas
 */

// =====================================================
// CONFIGURACIÓN DE BASE DE DATOS
// =====================================================
define('DB_HOST', 'localhost');
define('DB_NAME', 'restanetV1');
define('DB_USER', 'root');
define('DB_PASS', '');

// Zona horaria
date_default_timezone_set('America/Bogota');

// =====================================================
// CONFIGURACIÓN DE RUTAS
// =====================================================
// Ajusta la ruta base según tu ubicación en htdocs.
define('BASE_PATH', '/restanet/');
define('BASE_URL', (isset($_SERVER['HTTP_HOST'])
	? ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST']
	: '') . BASE_PATH);

// =====================================================
// CONFIGURACIÓN DE PAGINACIÓN
// =====================================================
define('ITEMS_PER_PAGE', 10);

// =====================================================
// CONFIGURACIÓN DE SESIONES
// =====================================================
if (!defined('SESSION_TIMEOUT')) {
    $envTimeout = getenv('RESTANET_SESSION_TIMEOUT');
    define('SESSION_TIMEOUT', ($envTimeout !== false ? max(300, (int) $envTimeout) : 600)); // 10 min por defecto
}
if (!defined('SESSION_TIMEOUT_GRACE')) {
    define('SESSION_TIMEOUT_GRACE', 120); // ventana de gracia opcional en segundos
}

// =====================================================
// CONFIGURACIÓN DE SUBIDA DE ARCHIVOS
// =====================================================
if (!defined('UPLOAD_MAX_SIZE')) {
    define('UPLOAD_MAX_SIZE', 5 * 1024 * 1024); // 5MB
}
if (!defined('UPLOAD_ALLOWED_TYPES')) {
    define('UPLOAD_ALLOWED_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
}

// =====================================================
// HEADERS DE SEGURIDAD HTTP
// =====================================================
// Solo aplicar si no estamos en CLI y aún no se enviaron headers
if (php_sapi_name() !== 'cli' && !headers_sent()) {
    // Prevenir que el navegador interprete contenido como tipo diferente
    header('X-Content-Type-Options: nosniff');
    
    // Protección XSS del navegador
    header('X-XSS-Protection: 1; mode=block');
    
    // Prevenir clickjacking
    header('X-Frame-Options: SAMEORIGIN');
    
    // Política de referencia
    header('Referrer-Policy: strict-origin-when-cross-origin');
    
    // HSTS solo si es HTTPS (descomentar en producción con HTTPS)
    // if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
    //     header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    // }
}

// =====================================================
// CONFIGURACIÓN DE ERRORES (ajustar en producción)
// =====================================================
// En producción, cambiar a:
// error_reporting(0);
// ini_set('display_errors', '0');
// ini_set('log_errors', '1');
if (!defined('ENVIRONMENT')) {
    define('ENVIRONMENT', 'development'); // 'development' o 'production'
}

if (ENVIRONMENT === 'production') {
    error_reporting(0);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    ini_set('error_log', __DIR__ . '/../logs/error.log');
}

// =====================================================
// AUTOLOAD DE COMPOSER
// =====================================================
$autoloadPath = __DIR__ . '/../vendor/autoload.php';
if (file_exists($autoloadPath)) {
	require_once $autoloadPath;
}

// =====================================================
// CONFIGURACIÓN DE CORREO (PHPMailer)
// =====================================================
/*
 * - Para activar el envío real establece MAIL_ENABLED en true y completa los datos.
 * - En entornos de desarrollo puedes dejar MAIL_ENABLED = false; el sistema 
 *   mostrará el enlace de restablecimiento en pantalla para pruebas.
 */
if (!defined('MAIL_ENABLED')) define('MAIL_ENABLED', false);
if (!defined('MAIL_HOST')) define('MAIL_HOST', 'smtp.example.com');
if (!defined('MAIL_PORT')) define('MAIL_PORT', 587);
if (!defined('MAIL_USERNAME')) define('MAIL_USERNAME', 'usuario@example.com');
if (!defined('MAIL_PASSWORD')) define('MAIL_PASSWORD', 'tu-contraseña');
if (!defined('MAIL_SMTP_SECURE')) define('MAIL_SMTP_SECURE', 'tls'); // 'tls' o 'ssl' o ''
if (!defined('MAIL_FROM')) define('MAIL_FROM', 'no-reply@localhost');
if (!defined('MAIL_FROM_NAME')) define('MAIL_FROM_NAME', 'RestaNet');

// =====================================================
// CONFIGURACIÓN DE TOKENS DE ACCESO
// =====================================================
if (!defined('TOKEN_FACTURA_EXPIRACION_DIAS')) {
    define('TOKEN_FACTURA_EXPIRACION_DIAS', 30);
}

?>