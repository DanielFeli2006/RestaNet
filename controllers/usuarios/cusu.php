<?php
require_once __DIR__ . '/../../models/conexion.php';
require_once __DIR__ . '/../../models/seg.php';
require_login();
require_role(['admin']);

$action = $_GET['a'] ?? 'list';

switch ($action) {
    case 'list':
        $usuarios = $pdo->query('SELECT id, nombre, email, rol, fecha_creacion FROM usuarios ORDER BY nombre')->fetchAll(PDO::FETCH_ASSOC);
        include __DIR__ . '/../../views/admin/vusuarios.php';
        break;
    case 'create':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = trim($_POST['nombre'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $rol = $_POST['rol'] ?? 'cliente';
            $password = $_POST['password'] ?? '';
            $errors = [];
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email inválido';
            if (strlen($password) < 8) $errors[] = 'La contraseña debe tener al menos 8 caracteres';
            // Email único
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM usuarios WHERE email=?');
            $stmt->execute([$email]);
            if ($stmt->fetchColumn() > 0) $errors[] = 'El email ya se encuentra registrado';
            if (empty($errors)) {
                $stmt = $pdo->prepare('INSERT INTO usuarios (nombre, email, password, rol) VALUES (?,?,?,?)');
                $stmt->execute([$nombre, $email, Seg::hashPassword($password), $rol]);
                header('Location: cusu.php');
                exit;
            }
            $usuario = ['nombre'=>$nombre,'email'=>$email,'rol'=>$rol]; // para repoblar
            $form_errors = $errors;
        }
        include __DIR__ . '/../../views/admin/vusuario_form.php';
        break;
    case 'edit':
        $id = (int)($_GET['id'] ?? 0);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = trim($_POST['nombre'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $rol = $_POST['rol'] ?? 'cliente';
            $errors = [];
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email inválido';
            // Email único (excluyendo el actual)
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM usuarios WHERE email=? AND id<>?');
            $stmt->execute([$email, $id]);
            if ($stmt->fetchColumn() > 0) $errors[] = 'El email ya se encuentra registrado en otro usuario';
            if (empty($errors)) {
                $stmt = $pdo->prepare('UPDATE usuarios SET nombre=?, email=?, rol=? WHERE id=?');
                $stmt->execute([$nombre, $email, $rol, $id]);
                header('Location: cusu.php');
                exit;
            }
            $usuario = ['id'=>$id,'nombre'=>$nombre,'email'=>$email,'rol'=>$rol];
            $form_errors = $errors;
        }
        $stmt = $pdo->prepare('SELECT id, nombre, email, rol FROM usuarios WHERE id=?');
        $stmt->execute([$id]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        include __DIR__ . '/../../views/admin/vusuario_form.php';
        break;
    case 'delete':
        $id = (int)($_GET['id'] ?? 0);
        if ($id === (int)$_SESSION['id']) {
            $_SESSION['error'] = 'No puedes eliminar tu propio usuario.';
        } else {
            $pdo->prepare('DELETE FROM usuarios WHERE id=?')->execute([$id]);
        }
        header('Location: cusu.php');
        exit;
    default:
        http_response_code(400);
        echo 'Acción no válida';
}
