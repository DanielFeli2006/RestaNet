<?php
require_once __DIR__ . '/../../models/conexion.php';
require_once __DIR__ . '/../../models/seg.php';
require_login();

$action = $_GET['a'] ?? 'list';

switch ($action) {
  case 'list':
    $stmt = $pdo->query('SELECT p.*, c.nombre AS categoria FROM productos p LEFT JOIN categorias c ON p.categoria_id=c.id ORDER BY p.nombre');
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    include __DIR__ . '/../../views/catalogo/vprd.php';
    break;
  case 'create':
    require_role(['admin']);
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $stmt = $pdo->prepare('INSERT INTO productos (nombre, descripcion, precio, categoria_id) VALUES (?,?,?,?)');
      $stmt->execute([
        trim($_POST['nombre']),
        trim($_POST['descripcion']),
        (float)$_POST['precio'],
        (int)$_POST['categoria_id']
      ]);
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
      $stmt = $pdo->prepare('UPDATE productos SET nombre=?, descripcion=?, precio=?, categoria_id=? WHERE id=?');
      $stmt->execute([
        trim($_POST['nombre']),
        trim($_POST['descripcion']),
        (float)$_POST['precio'],
        (int)$_POST['categoria_id'],
        $id
      ]);
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
    $id = (int)($_GET['id'] ?? 0);
    if ($id) {
      $pdo->prepare('DELETE FROM productos WHERE id=?')->execute([$id]);
    }
    header('Location: cprd.php');
    exit;
  default:
    http_response_code(400);
    echo 'Acción no válida';
}
