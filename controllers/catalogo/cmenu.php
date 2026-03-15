<?php
require_once __DIR__ . '/../../models/conexion.php';
require_once __DIR__ . '/../../models/seg.php';
require_login();
require_role(['cliente','admin','mesero']);

$action = $_GET['a'] ?? 'list';

switch ($action) {
  case 'list':
  default:
    $stmt = $pdo->query('SELECT p.*, c.nombre AS categoria FROM productos p LEFT JOIN categorias c ON p.categoria_id=c.id ORDER BY c.nombre, p.nombre');
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    include __DIR__ . '/../../views/catalogo/vmenu.php';
}
