<?php
require_once __DIR__ . '/../../models/conexion.php';
require_once __DIR__ . '/../../models/seg.php';
require_login();

$action = $_GET['a'] ?? 'list';

switch ($action) {
    case 'list':
        $cats = $pdo->query('SELECT * FROM categorias ORDER BY nombre')->fetchAll(PDO::FETCH_ASSOC);
        include __DIR__ . '/../../views/catalogo/vcat.php';
        break;
    case 'create':
        require_role(['admin']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $stmt = $pdo->prepare('INSERT INTO categorias (nombre, descripcion) VALUES (?, ?)');
            $stmt->execute([
                trim($_POST['nombre']),
                trim($_POST['descripcion'])
            ]);
            header('Location: ccat.php');
            exit;
        }
        $categoria = null; // Para reutilizar el formulario
        include __DIR__ . '/../../views/catalogo/vcat_form.php';
        break;
    case 'edit':
        require_role(['admin']);
        $id = (int)($_GET['id'] ?? 0);
        if (!$id) { header('Location: ccat.php'); exit; }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $stmt = $pdo->prepare('UPDATE categorias SET nombre=?, descripcion=? WHERE id=?');
            $stmt->execute([
                trim($_POST['nombre']),
                trim($_POST['descripcion']),
                $id
            ]);
            header('Location: ccat.php');
            exit;
        }
        $stmt = $pdo->prepare('SELECT * FROM categorias WHERE id=?');
        $stmt->execute([$id]);
        $categoria = $stmt->fetch(PDO::FETCH_ASSOC);
        include __DIR__ . '/../../views/catalogo/vcat_form.php';
        break;
    case 'delete':
        require_role(['admin']);
        $id = (int)($_GET['id'] ?? 0);
        if ($id) {
            $stmt = $pdo->prepare('DELETE FROM categorias WHERE id=?');
            $stmt->execute([$id]);
        }
        header('Location: ccat.php');
        exit;
    default:
        http_response_code(400);
        echo 'Acción no válida';
}
