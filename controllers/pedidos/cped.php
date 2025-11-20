<?php
require_once __DIR__ . '/../../models/conexion.php';
require_once __DIR__ . '/../../models/seg.php';
require_login();
require_role(['admin','mesero','cajero']);

$action = $_GET['a'] ?? 'list';

switch ($action) {
    case 'list':
        // Listar pedidos
        $pedidos = $pdo->query('SELECT p.*, u.nombre usuario, m.numero mesa FROM pedidos p LEFT JOIN usuarios u ON p.usuario_id=u.id LEFT JOIN mesas m ON p.mesa_id=m.id ORDER BY p.fecha_creacion DESC')->fetchAll(PDO::FETCH_ASSOC);
        include __DIR__ . '/../../views/pedidos/vped.php';
        break;
    case 'create':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $stmt = $pdo->prepare('INSERT INTO pedidos (usuario_id, mesa_id, estado) VALUES (?,?,?)');
            $stmt->execute([$_SESSION['id'], $_POST['mesa_id'], 'pendiente']);
            header('Location: cped.php');
            exit;
        }
        include __DIR__ . '/../../views/pedidos/vped_form.php';
        break;
    case 'detalle':
        $id = (int)($_GET['id'] ?? 0);
        $stmt = $pdo->prepare('SELECT * FROM pedidos WHERE id=?');
        $stmt->execute([$id]);
        $pedido = $stmt->fetch(PDO::FETCH_ASSOC);
        $items = $pdo->prepare('SELECT d.*, pr.nombre producto FROM detalle_pedido d LEFT JOIN productos pr ON d.producto_id=pr.id WHERE pedido_id=?');
        $items->execute([$id]);
        $detalle = $items->fetchAll(PDO::FETCH_ASSOC);
        include __DIR__ . '/../../views/pedidos/vdetped.php';
        break;
    default:
        http_response_code(400);
        echo 'Acción no válida';
}
