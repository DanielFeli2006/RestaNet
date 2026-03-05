<?php
/**
 * Controlador de Productos
 * 
 * SEGURIDAD:
 * - Requiere autenticación
 * - Requiere rol admin para crear/editar/eliminar
 * - Validación CSRF en operaciones de escritura
 * - Validación de tipos de archivo para imágenes
 * - Sanitización de nombres de archivo
 */
require_once __DIR__ . '/../../models/conexion.php';
require_once __DIR__ . '/../../models/seg.php';
require_login();

$action = $_GET['a'] ?? 'list';

// Configuración de imágenes
define('UPLOAD_DIR', __DIR__ . '/../../img/productos/');
define('ALLOWED_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB

/**
 * Procesa y guarda una imagen de producto de forma segura
 * 
 * @param array $file El archivo $_FILES['imagen']
 * @param int|null $productoId ID del producto (para nombre único)
 * @return array ['success' => bool, 'filename' => string|null, 'error' => string|null]
 */
function procesarImagenProducto(array $file, ?int $productoId = null): array {
    // Verificar si hay archivo
    if (empty($file['tmp_name']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return ['success' => true, 'filename' => null, 'error' => null];
    }
    
    // Verificar errores de upload
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errores = [
            UPLOAD_ERR_INI_SIZE => 'El archivo excede el tamaño máximo permitido.',
            UPLOAD_ERR_FORM_SIZE => 'El archivo excede el tamaño del formulario.',
            UPLOAD_ERR_PARTIAL => 'El archivo se subió parcialmente.',
            UPLOAD_ERR_NO_TMP_DIR => 'Falta carpeta temporal.',
            UPLOAD_ERR_CANT_WRITE => 'Error al escribir el archivo.',
            UPLOAD_ERR_EXTENSION => 'Extensión no permitida.',
        ];
        return ['success' => false, 'filename' => null, 'error' => $errores[$file['error']] ?? 'Error desconocido al subir.'];
    }
    
    // SEGURIDAD: Validar tamaño
    if ($file['size'] > MAX_FILE_SIZE) {
        return ['success' => false, 'filename' => null, 'error' => 'El archivo excede el tamaño máximo de 5MB.'];
    }
    
    // SEGURIDAD: Validar tipo MIME real (no confiar en extensión)
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);
    
    if (!in_array($mimeType, ALLOWED_TYPES, true)) {
        return ['success' => false, 'filename' => null, 'error' => 'Tipo de archivo no permitido. Solo JPEG, PNG, GIF y WebP.'];
    }
    
    // SEGURIDAD: Verificar que es una imagen válida
    $imageInfo = @getimagesize($file['tmp_name']);
    if ($imageInfo === false) {
        return ['success' => false, 'filename' => null, 'error' => 'El archivo no es una imagen válida.'];
    }
    
    // Crear directorio si no existe
    if (!is_dir(UPLOAD_DIR)) {
        if (!mkdir(UPLOAD_DIR, 0755, true)) {
            return ['success' => false, 'filename' => null, 'error' => 'Error al crear directorio de imágenes.'];
        }
    }
    
    // SEGURIDAD: Generar nombre único y seguro
    $extension = match($mimeType) {
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp',
        default => 'jpg'
    };
    
    $uniqueId = $productoId ? sprintf('%d_%s', $productoId, bin2hex(random_bytes(8))) : bin2hex(random_bytes(16));
    $filename = 'producto_' . $uniqueId . '.' . $extension;
    $filepath = UPLOAD_DIR . $filename;
    
    // Mover archivo
    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        return ['success' => false, 'filename' => null, 'error' => 'Error al guardar la imagen.'];
    }
    
    return ['success' => true, 'filename' => $filename, 'error' => null];
}

/**
 * Elimina una imagen de producto
 */
function eliminarImagenProducto(?string $filename): void {
    if ($filename && file_exists(UPLOAD_DIR . $filename)) {
        @unlink(UPLOAD_DIR . $filename);
    }
}

switch ($action) {
  case 'list':
    $stmt = $pdo->query('SELECT p.*, c.nombre AS categoria FROM productos p LEFT JOIN categorias c ON p.categoria_id=c.id ORDER BY p.nombre');
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    include __DIR__ . '/../../views/catalogo/vprd.php';
    break;
    
  case 'create':
    require_role(['admin']);
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      // SEGURIDAD: Validar CSRF
      if (!validate_csrf($_POST['csrf_token'] ?? null)) {
        $_SESSION['error'] = 'Token CSRF inválido.';
        header('Location: cprd.php?a=create');
        exit;
      }
      
      // Validar datos requeridos
      $nombre = trim($_POST['nombre'] ?? '');
      $descripcion = trim($_POST['descripcion'] ?? '');
      $precio = (float)($_POST['precio'] ?? 0);
      $categoria_id = (int)($_POST['categoria_id'] ?? 0);
      $activo = isset($_POST['activo']) ? 1 : 0;
      
      if (empty($nombre) || $precio <= 0 || $categoria_id <= 0) {
        $_SESSION['error'] = 'Por favor completa todos los campos requeridos.';
        header('Location: cprd.php?a=create');
        exit;
      }
      
      // Procesar imagen
      $imagenResult = procesarImagenProducto($_FILES['imagen'] ?? []);
      if (!$imagenResult['success']) {
        $_SESSION['error'] = $imagenResult['error'];
        header('Location: cprd.php?a=create');
        exit;
      }
      
      $stmt = $pdo->prepare('INSERT INTO productos (nombre, descripcion, precio, imagen, categoria_id, activo) VALUES (?,?,?,?,?,?)');
      $stmt->execute([$nombre, $descripcion, $precio, $imagenResult['filename'], $categoria_id, $activo]);
      
      $_SESSION['success'] = 'Producto creado exitosamente.';
      header('Location: cprd.php');
      exit;
    }
    $cats = $pdo->query('SELECT id, nombre FROM categorias ORDER BY nombre')->fetchAll(PDO::FETCH_ASSOC);
    $producto = null;
    include __DIR__ . '/../../views/catalogo/vprd_form.php';
    break;
    
  case 'edit':
    require_role(['admin']);
    $id = (int)($_GET['id'] ?? 0);
    if (!$id) { header('Location: cprd.php'); exit; }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      // SEGURIDAD: Validar CSRF
      if (!validate_csrf($_POST['csrf_token'] ?? null)) {
        $_SESSION['error'] = 'Token CSRF inválido.';
        header('Location: cprd.php?a=edit&id=' . $id);
        exit;
      }
      
      // Validar datos
      $nombre = trim($_POST['nombre'] ?? '');
      $descripcion = trim($_POST['descripcion'] ?? '');
      $precio = (float)($_POST['precio'] ?? 0);
      $categoria_id = (int)($_POST['categoria_id'] ?? 0);
      $activo = isset($_POST['activo']) ? 1 : 0;
      
      if (empty($nombre) || $precio <= 0 || $categoria_id <= 0) {
        $_SESSION['error'] = 'Por favor completa todos los campos requeridos.';
        header('Location: cprd.php?a=edit&id=' . $id);
        exit;
      }
      
      // Obtener producto actual para imagen anterior
      $stmt = $pdo->prepare('SELECT imagen FROM productos WHERE id=?');
      $stmt->execute([$id]);
      $productoActual = $stmt->fetch(PDO::FETCH_ASSOC);
      $imagenAnterior = $productoActual['imagen'] ?? null;
      
      // Procesar nueva imagen si se subió
      $imagenResult = procesarImagenProducto($_FILES['imagen'] ?? [], $id);
      if (!$imagenResult['success']) {
        $_SESSION['error'] = $imagenResult['error'];
        header('Location: cprd.php?a=edit&id=' . $id);
        exit;
      }
      
      // Si hay nueva imagen, eliminar la anterior
      $nuevaImagen = $imagenResult['filename'];
      if ($nuevaImagen && $imagenAnterior) {
        eliminarImagenProducto($imagenAnterior);
      }
      
      // Si no hay nueva imagen, mantener la anterior
      $imagenFinal = $nuevaImagen ?? $imagenAnterior;
      
      // Verificar si se solicitó eliminar imagen
      if (isset($_POST['eliminar_imagen']) && $_POST['eliminar_imagen'] === '1') {
        eliminarImagenProducto($imagenAnterior);
        $imagenFinal = null;
      }
      
      $stmt = $pdo->prepare('UPDATE productos SET nombre=?, descripcion=?, precio=?, imagen=?, categoria_id=?, activo=? WHERE id=?');
      $stmt->execute([$nombre, $descripcion, $precio, $imagenFinal, $categoria_id, $activo, $id]);
      
      $_SESSION['success'] = 'Producto actualizado exitosamente.';
      header('Location: cprd.php');
      exit;
    }
    
    $cats = $pdo->query('SELECT id, nombre FROM categorias ORDER BY nombre')->fetchAll(PDO::FETCH_ASSOC);
    $stmt = $pdo->prepare('SELECT * FROM productos WHERE id=?');
    $stmt->execute([$id]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);
    include __DIR__ . '/../../views/catalogo/vprd_form.php';
    break;
    
  case 'delete':
    require_role(['admin']);
    
    // SEGURIDAD: Solo aceptar POST para eliminar
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      // Redirigir a confirmación si es GET
      $id = (int)($_GET['id'] ?? 0);
      if ($id) {
        $stmt = $pdo->prepare('SELECT * FROM productos WHERE id=?');
        $stmt->execute([$id]);
        $producto = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($producto) {
          include __DIR__ . '/../../views/catalogo/vprd_delete.php';
          exit;
        }
      }
      header('Location: cprd.php');
      exit;
    }
    
    // SEGURIDAD: Validar CSRF
    if (!validate_csrf($_POST['csrf_token'] ?? null)) {
      $_SESSION['error'] = 'Token CSRF inválido.';
      header('Location: cprd.php');
      exit;
    }
    
    $id = (int)($_POST['id'] ?? 0);
    if ($id) {
      // Obtener imagen para eliminarla
      $stmt = $pdo->prepare('SELECT imagen FROM productos WHERE id=?');
      $stmt->execute([$id]);
      $producto = $stmt->fetch(PDO::FETCH_ASSOC);
      
      if ($producto) {
        eliminarImagenProducto($producto['imagen']);
        $pdo->prepare('DELETE FROM productos WHERE id=?')->execute([$id]);
        $_SESSION['success'] = 'Producto eliminado exitosamente.';
      }
    }
    header('Location: cprd.php');
    exit;
    
  default:
    http_response_code(400);
    echo 'Acción no válida';
}
