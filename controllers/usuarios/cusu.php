<?php
/**
 * Controlador de Usuarios
 * 
 * SEGURIDAD:
 * - Requiere autenticación y rol admin
 * - Validación CSRF en todas las operaciones de escritura
 * - Validación estricta de email y contraseña
 * - Sanitización de datos de entrada
 * - Prevención de auto-eliminación
 * - Confirmación de eliminación con POST
 */
require_once __DIR__ . '/../../models/conexion.php';
require_once __DIR__ . '/../../models/seg.php';
require_login();
require_role(['admin']);

$action = $_GET['a'] ?? 'list';

// SEGURIDAD: Lista de roles permitidos
const ALLOWED_ROLES = ['admin', 'mesero', 'cajero', 'cliente'];

switch ($action) {
    case 'list':
        $usuarios = $pdo->query('SELECT id, nombre, email, rol, fecha_creacion FROM usuarios ORDER BY nombre')->fetchAll(PDO::FETCH_ASSOC);
        include __DIR__ . '/../../views/admin/vusuarios.php';
        break;
        
    case 'create':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // SEGURIDAD: Validar CSRF
            if (!validate_csrf($_POST['csrf_token'] ?? null)) {
                $_SESSION['error'] = 'Token CSRF inválido.';
                header('Location: cusu.php?a=create');
                exit;
            }
            
            // SEGURIDAD: Sanitización y validación de datos
            $nombre = trim(strip_tags($_POST['nombre'] ?? ''));
            $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
            $rol = $_POST['rol'] ?? 'cliente';
            $password = $_POST['password'] ?? '';
            
            $errors = [];
            
            // Validar nombre (mínimo 2 caracteres)
            if (strlen($nombre) < 2 || strlen($nombre) > 100) {
                $errors[] = 'El nombre debe tener entre 2 y 100 caracteres.';
            }
            
            // Validar email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Email inválido.';
            }
            
            // SEGURIDAD: Validar rol contra lista blanca
            if (!in_array($rol, ALLOWED_ROLES, true)) {
                $rol = 'cliente'; // Default seguro
            }
            
            // SEGURIDAD: Validar contraseña fuerte
            if (strlen($password) < 8) {
                $errors[] = 'La contraseña debe tener al menos 8 caracteres.';
            }
            if (!preg_match('/[A-Z]/', $password)) {
                $errors[] = 'La contraseña debe contener al menos una mayúscula.';
            }
            if (!preg_match('/[a-z]/', $password)) {
                $errors[] = 'La contraseña debe contener al menos una minúscula.';
            }
            if (!preg_match('/[0-9]/', $password)) {
                $errors[] = 'La contraseña debe contener al menos un número.';
            }
            
            // Email único
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM usuarios WHERE email=?');
            $stmt->execute([$email]);
            if ($stmt->fetchColumn() > 0) {
                $errors[] = 'El email ya se encuentra registrado.';
            }
            
            if (empty($errors)) {
                $stmt = $pdo->prepare('INSERT INTO usuarios (nombre, email, password, rol) VALUES (?,?,?,?)');
                $stmt->execute([$nombre, $email, Seg::hashPassword($password), $rol]);
                $_SESSION['success'] = 'Usuario creado exitosamente.';
                header('Location: cusu.php');
                exit;
            }
            
            $usuario = ['nombre' => $nombre, 'email' => $email, 'rol' => $rol];
            $form_errors = $errors;
        }
        include __DIR__ . '/../../views/admin/vusuario_form.php';
        break;
        
    case 'edit':
        $id = (int)($_GET['id'] ?? 0);
        if (!$id) { header('Location: cusu.php'); exit; }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // SEGURIDAD: Validar CSRF
            if (!validate_csrf($_POST['csrf_token'] ?? null)) {
                $_SESSION['error'] = 'Token CSRF inválido.';
                header('Location: cusu.php?a=edit&id=' . $id);
                exit;
            }
            
            // SEGURIDAD: Sanitización y validación
            $nombre = trim(strip_tags($_POST['nombre'] ?? ''));
            $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
            $rol = $_POST['rol'] ?? 'cliente';
            $password = $_POST['password'] ?? ''; // Opcional en edición
            
            $errors = [];
            
            if (strlen($nombre) < 2 || strlen($nombre) > 100) {
                $errors[] = 'El nombre debe tener entre 2 y 100 caracteres.';
            }
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Email inválido.';
            }
            
            // SEGURIDAD: Validar rol
            if (!in_array($rol, ALLOWED_ROLES, true)) {
                $rol = 'cliente';
            }
            
            // SEGURIDAD: Prevenir que admin se quite su propio rol admin
            if ($id === (int)$_SESSION['id'] && $rol !== 'admin') {
                $errors[] = 'No puedes cambiar tu propio rol de administrador.';
            }
            
            // Validar contraseña solo si se proporciona
            if (!empty($password)) {
                if (strlen($password) < 8) {
                    $errors[] = 'La contraseña debe tener al menos 8 caracteres.';
                }
                if (!preg_match('/[A-Z]/', $password)) {
                    $errors[] = 'La contraseña debe contener al menos una mayúscula.';
                }
                if (!preg_match('/[a-z]/', $password)) {
                    $errors[] = 'La contraseña debe contener al menos una minúscula.';
                }
                if (!preg_match('/[0-9]/', $password)) {
                    $errors[] = 'La contraseña debe contener al menos un número.';
                }
            }
            
            // Email único (excluyendo el actual)
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM usuarios WHERE email=? AND id<>?');
            $stmt->execute([$email, $id]);
            if ($stmt->fetchColumn() > 0) {
                $errors[] = 'El email ya se encuentra registrado en otro usuario.';
            }
            
            if (empty($errors)) {
                if (!empty($password)) {
                    $stmt = $pdo->prepare('UPDATE usuarios SET nombre=?, email=?, password=?, rol=? WHERE id=?');
                    $stmt->execute([$nombre, $email, Seg::hashPassword($password), $rol, $id]);
                } else {
                    $stmt = $pdo->prepare('UPDATE usuarios SET nombre=?, email=?, rol=? WHERE id=?');
                    $stmt->execute([$nombre, $email, $rol, $id]);
                }
                $_SESSION['success'] = 'Usuario actualizado exitosamente.';
                header('Location: cusu.php');
                exit;
            }
            
            $usuario = ['id' => $id, 'nombre' => $nombre, 'email' => $email, 'rol' => $rol];
            $form_errors = $errors;
        } else {
            $stmt = $pdo->prepare('SELECT id, nombre, email, rol FROM usuarios WHERE id=?');
            $stmt->execute([$id]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$usuario) {
                $_SESSION['error'] = 'Usuario no encontrado.';
                header('Location: cusu.php');
                exit;
            }
        }
        include __DIR__ . '/../../views/admin/vusuario_form.php';
        break;
        
    case 'delete':
        // SEGURIDAD: Solo aceptar POST para eliminar
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $id = (int)($_GET['id'] ?? 0);
            if ($id) {
                $stmt = $pdo->prepare('SELECT id, nombre, email, rol FROM usuarios WHERE id=?');
                $stmt->execute([$id]);
                $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($usuario) {
                    include __DIR__ . '/../../views/admin/vusuario_delete.php';
                    exit;
                }
            }
            header('Location: cusu.php');
            exit;
        }
        
        // SEGURIDAD: Validar CSRF
        if (!validate_csrf($_POST['csrf_token'] ?? null)) {
            $_SESSION['error'] = 'Token CSRF inválido.';
            header('Location: cusu.php');
            exit;
        }
        
        $id = (int)($_POST['id'] ?? 0);
        
        // SEGURIDAD: Prevenir auto-eliminación
        if ($id === (int)$_SESSION['id']) {
            $_SESSION['error'] = 'No puedes eliminar tu propio usuario.';
            header('Location: cusu.php');
            exit;
        }
        
        if ($id) {
            $pdo->prepare('DELETE FROM usuarios WHERE id=?')->execute([$id]);
            $_SESSION['success'] = 'Usuario eliminado exitosamente.';
        }
        header('Location: cusu.php');
        exit;
        
    default:
        http_response_code(400);
        echo 'Acción no válida';
}
