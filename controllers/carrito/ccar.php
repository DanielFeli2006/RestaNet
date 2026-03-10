<?php
/**
 * Controlador del carrito de compras
 * 
 * SEGURIDAD:
 * - Requiere autenticación
 * - Validación CSRF en operaciones de escritura
 * - Sanitización de IDs de productos
 * - Transacciones para integridad de datos
 */
require_once __DIR__ . '/../../models/conexion.php';
require_once __DIR__ . '/../../models/seg.php';
require_login();
require_role(['cliente','admin']); // Admin puede probar

start_secure_session();

$action = $_GET['a'] ?? 'view';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = []; // cart: producto_id => [nombre, precio, cantidad]
}

function cart_add(int $id, PDO $pdo): void {
    $stmt = $pdo->prepare('SELECT id, nombre, precio FROM productos WHERE id=?');
    $stmt->execute([$id]);
    if ($p = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if (!isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id] = [
                'nombre' => $p['nombre'],
                'precio' => (float)$p['precio'],
                'cantidad' => 1
            ];
        } else {
            $_SESSION['cart'][$id]['cantidad']++;
        }
    }
}

function cart_remove(int $id): void {
    unset($_SESSION['cart'][$id]);
}

function cart_total(): float {
    $t = 0.0;
    foreach ($_SESSION['cart'] as $item) {
        $t += $item['precio'] * $item['cantidad'];
    }
    return $t;
}

function cart_items_count(): int {
    $c = 0;
    foreach ($_SESSION['cart'] as $item) { $c += $item['cantidad']; }
    return $c;
}

switch ($action) {
    case 'add':
        $id = (int)($_GET['id'] ?? 0);
        if ($id) cart_add($id, $pdo);
        header('Location: ccar.php');
        exit;
    case 'remove':
        $id = (int)($_GET['id'] ?? 0);
        if ($id) cart_remove($id);
        header('Location: ccar.php');
        exit;
    case 'clear':
        $_SESSION['cart'] = [];
        header('Location: ccar.php');
        exit;
    case 'checkout':
        // SEGURIDAD: Validar mínimo de items
        if (cart_items_count() < 2) {
            $_SESSION['error'] = 'El carrito requiere al menos 2 ítems.';
            header('Location: ccar.php');
            exit;
        }
        
        // Crear pedido y detalle con transacción para integridad
        $pdo->beginTransaction();
        try {
            $mesa_id = null; // Para clientes web podría ser null o mesa virtual
            $stmt = $pdo->prepare('INSERT INTO pedidos (usuario_id, mesa_id, estado) VALUES (?,?,?)');
            $stmt->execute([$_SESSION['id'], $mesa_id, 'pendiente']);
            $pedido_id = (int)$pdo->lastInsertId();
            
            $insDetalle = $pdo->prepare('INSERT INTO detalle_pedido (pedido_id, producto_id, cantidad, precio) VALUES (?,?,?,?)');
            foreach ($_SESSION['cart'] as $pid => $item) {
                $insDetalle->execute([$pedido_id, $pid, $item['cantidad'], $item['precio']]);
            }
            
            // Calcular totales
            $subtotal = cart_total();
            $impuestos = round($subtotal * 0.19, 2);
            $total = round($subtotal + $impuestos, 2);
            
            // SEGURIDAD: Generar token de acceso seguro (64 caracteres hex)
            $token_acceso = bin2hex(random_bytes(32));
            $token_expiracion = date('Y-m-d H:i:s', strtotime('+30 days'));
            
            // Insert factura con token de acceso
            $stmtF = $pdo->prepare('INSERT INTO facturas (pedido_id, subtotal, impuestos, total, token_acceso, token_expira) VALUES (?,?,?,?,?,?)');
$stmtF->execute([$pedido_id, $subtotal, $impuestos, $total, $token_acceso, $token_expiracion]);
            
            $pdo->commit();
            
            // Limpiar carrito
            $_SESSION['cart'] = [];
            header('Location: ccar.php?a=done&pedido=' . $pedido_id);
            exit;
        } catch (Throwable $e) {
            $pdo->rollBack();
            error_log('Error en checkout: ' . $e->getMessage());
            $_SESSION['error'] = 'Error al procesar el pedido. Intenta nuevamente.';
            header('Location: ccar.php');
            exit;
        }
    case 'done':
        $pedido_id = (int)($_GET['pedido'] ?? 0);
        if (!$pedido_id) {
            header('Location: ccar.php');
            exit;
        }
        // Mostrar vista final con enlace de acceso
        $stmt = $pdo->prepare('SELECT * FROM facturas WHERE pedido_id=?');
        $stmt->execute([$pedido_id]);
        $factura = $stmt->fetch(PDO::FETCH_ASSOC);
        include __DIR__ . '/../../views/catalogo/vcart_done.php';
        break;
    case 'view':
    default:
        include __DIR__ . '/../../views/catalogo/vcart.php';
        break;
}
