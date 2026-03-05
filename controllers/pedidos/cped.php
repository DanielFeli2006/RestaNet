<?php
/**
 * Controlador de Pedidos
 * 
 * SEGURIDAD:
 * - Requiere autenticación
 * - Validación de roles según operación
 * - Validación CSRF en operaciones de escritura
 * - Validación de datos de entrada
 * - Uso de transacciones para integridad
 */
require_once __DIR__ . '/../../models/conexion.php';
require_once __DIR__ . '/../../models/seg.php';
require_login();
require_role(['admin', 'mesero', 'cajero']);

$action = $_GET['a'] ?? 'list';

// SEGURIDAD: Estados de pedido permitidos
const ESTADOS_PEDIDO = ['pendiente', 'en_proceso', 'completado', 'cancelado'];

switch ($action) {
    case 'list':
        // Listar pedidos con filtros opcionales
        $estado_filtro = $_GET['estado'] ?? null;
        
        $sql = 'SELECT p.*, u.nombre usuario, m.numero mesa 
                FROM pedidos p 
                LEFT JOIN usuarios u ON p.usuario_id=u.id 
                LEFT JOIN mesas m ON p.mesa_id=m.id';
        
        if ($estado_filtro && in_array($estado_filtro, ESTADOS_PEDIDO, true)) {
            $sql .= ' WHERE p.estado = ?';
            $stmt = $pdo->prepare($sql . ' ORDER BY p.fecha_creacion DESC');
            $stmt->execute([$estado_filtro]);
        } else {
            $stmt = $pdo->prepare($sql . ' ORDER BY p.fecha_creacion DESC');
            $stmt->execute();
        }
        
        $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        include __DIR__ . '/../../views/pedidos/vped.php';
        break;
        
    case 'create':
        require_role(['admin', 'mesero']);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // SEGURIDAD: Validar CSRF
            if (!validate_csrf($_POST['csrf_token'] ?? null)) {
                $_SESSION['error'] = 'Token CSRF inválido.';
                header('Location: cped.php?a=create');
                exit;
            }
            
            $mesa_id = (int)($_POST['mesa_id'] ?? 0);
            $notas = trim(strip_tags($_POST['notas'] ?? ''));
            
            // Validar mesa
            if ($mesa_id) {
                $stmt = $pdo->prepare('SELECT id, estado FROM mesas WHERE id=?');
                $stmt->execute([$mesa_id]);
                $mesa = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$mesa) {
                    $_SESSION['error'] = 'Mesa no encontrada.';
                    header('Location: cped.php?a=create');
                    exit;
                }
            }
            
            $pdo->beginTransaction();
            try {
                $stmt = $pdo->prepare('INSERT INTO pedidos (usuario_id, mesa_id, estado, notas) VALUES (?,?,?,?)');
                $stmt->execute([$_SESSION['id'], $mesa_id ?: null, 'pendiente', $notas]);
                $pedido_id = (int)$pdo->lastInsertId();
                
                // Si hay mesa, marcarla como ocupada
                if ($mesa_id) {
                    $pdo->prepare('UPDATE mesas SET estado="ocupada", fecha_actualizacion=NOW() WHERE id=?')
                        ->execute([$mesa_id]);
                }
                
                $pdo->commit();
                $_SESSION['success'] = 'Pedido #' . $pedido_id . ' creado exitosamente.';
                header('Location: cped.php?a=detalle&id=' . $pedido_id);
                exit;
                
            } catch (Throwable $e) {
                $pdo->rollBack();
                error_log('Error al crear pedido: ' . $e->getMessage());
                $_SESSION['error'] = 'Error al crear el pedido.';
                header('Location: cped.php?a=create');
                exit;
            }
        }
        
        // Cargar mesas disponibles
        $mesas = $pdo->query('SELECT id, numero, estado, capacidad, ubicacion FROM mesas ORDER BY numero')->fetchAll(PDO::FETCH_ASSOC);
        include __DIR__ . '/../../views/pedidos/vped_form.php';
        break;
        
    case 'detalle':
        $id = (int)($_GET['id'] ?? 0);
        if (!$id) { header('Location: cped.php'); exit; }
        
        $stmt = $pdo->prepare('SELECT p.*, u.nombre as usuario_nombre, m.numero as mesa_numero 
                               FROM pedidos p 
                               LEFT JOIN usuarios u ON p.usuario_id = u.id 
                               LEFT JOIN mesas m ON p.mesa_id = m.id
                               WHERE p.id=?');
        $stmt->execute([$id]);
        $pedido = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$pedido) {
            $_SESSION['error'] = 'Pedido no encontrado.';
            header('Location: cped.php');
            exit;
        }
        
        $stmt = $pdo->prepare('SELECT d.*, pr.nombre as producto, pr.imagen 
                               FROM detalle_pedido d 
                               LEFT JOIN productos pr ON d.producto_id = pr.id 
                               WHERE d.pedido_id=?');
        $stmt->execute([$id]);
        $detalle = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Calcular totales
        $subtotal = array_reduce($detalle, fn($sum, $item) => $sum + ($item['cantidad'] * $item['precio']), 0);
        
        include __DIR__ . '/../../views/pedidos/vdetped.php';
        break;
        
    case 'agregar_producto':
        require_role(['admin', 'mesero']);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }
        
        // SEGURIDAD: Validar CSRF
        if (!validate_csrf($_POST['csrf_token'] ?? null)) {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode(['ok' => false, 'error' => 'Token CSRF inválido']);
            exit;
        }
        
        $pedido_id = (int)($_POST['pedido_id'] ?? 0);
        $producto_id = (int)($_POST['producto_id'] ?? 0);
        $cantidad = (int)($_POST['cantidad'] ?? 1);
        $notas = trim(strip_tags($_POST['notas'] ?? ''));
        
        if (!$pedido_id || !$producto_id || $cantidad < 1) {
            http_response_code(422);
            header('Content-Type: application/json');
            echo json_encode(['ok' => false, 'error' => 'Datos inválidos']);
            exit;
        }
        
        // Verificar que el pedido existe y no está completado/cancelado
        $stmt = $pdo->prepare('SELECT estado FROM pedidos WHERE id=?');
        $stmt->execute([$pedido_id]);
        $pedido = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$pedido || in_array($pedido['estado'], ['completado', 'cancelado'], true)) {
            http_response_code(422);
            header('Content-Type: application/json');
            echo json_encode(['ok' => false, 'error' => 'El pedido no puede ser modificado']);
            exit;
        }
        
        // Obtener precio del producto
        $stmt = $pdo->prepare('SELECT precio, activo FROM productos WHERE id=?');
        $stmt->execute([$producto_id]);
        $producto = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$producto || !$producto['activo']) {
            http_response_code(422);
            header('Content-Type: application/json');
            echo json_encode(['ok' => false, 'error' => 'Producto no disponible']);
            exit;
        }
        
        $stmt = $pdo->prepare('INSERT INTO detalle_pedido (pedido_id, producto_id, cantidad, precio, notas) VALUES (?,?,?,?,?)');
        $stmt->execute([$pedido_id, $producto_id, $cantidad, $producto['precio'], $notas]);
        
        // Actualizar timestamp del pedido
        $pdo->prepare('UPDATE pedidos SET fecha_actualizacion=NOW() WHERE id=?')->execute([$pedido_id]);
        
        header('Content-Type: application/json');
        echo json_encode(['ok' => true, 'mensaje' => 'Producto agregado']);
        exit;
        
    case 'actualizar_estado':
        require_role(['admin', 'mesero', 'cajero']);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }
        
        // SEGURIDAD: Validar CSRF
        if (!validate_csrf($_POST['csrf_token'] ?? null)) {
            $_SESSION['error'] = 'Token CSRF inválido.';
            header('Location: cped.php');
            exit;
        }
        
        $pedido_id = (int)($_POST['pedido_id'] ?? 0);
        $estado = $_POST['estado'] ?? '';
        
        if (!$pedido_id || !in_array($estado, ESTADOS_PEDIDO, true)) {
            $_SESSION['error'] = 'Datos inválidos.';
            header('Location: cped.php');
            exit;
        }
        
        $pdo->beginTransaction();
        try {
            // Obtener info del pedido
            $stmt = $pdo->prepare('SELECT mesa_id, estado as estado_actual FROM pedidos WHERE id=? FOR UPDATE');
            $stmt->execute([$pedido_id]);
            $pedido = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$pedido) {
                throw new Exception('Pedido no encontrado');
            }
            
            // Actualizar pedido
            $pdo->prepare('UPDATE pedidos SET estado=?, fecha_actualizacion=NOW() WHERE id=?')
                ->execute([$estado, $pedido_id]);
            
            // Si el pedido se completa o cancela, liberar la mesa
            if (in_array($estado, ['completado', 'cancelado'], true) && $pedido['mesa_id']) {
                // Verificar si hay otros pedidos activos en esa mesa
                $stmt = $pdo->prepare('SELECT COUNT(*) FROM pedidos WHERE mesa_id=? AND id<>? AND estado IN ("pendiente","en_proceso")');
                $stmt->execute([$pedido['mesa_id'], $pedido_id]);
                
                if ($stmt->fetchColumn() == 0) {
                    // Liberar mesa si no hay más pedidos activos
                    $pdo->prepare('UPDATE mesas SET estado="disponible", fecha_actualizacion=NOW() WHERE id=?')
                        ->execute([$pedido['mesa_id']]);
                }
            }
            
            $pdo->commit();
            $_SESSION['success'] = 'Estado del pedido actualizado.';
            
        } catch (Throwable $e) {
            $pdo->rollBack();
            error_log('Error al actualizar estado de pedido: ' . $e->getMessage());
            $_SESSION['error'] = 'Error al actualizar el estado.';
        }
        
        // Redirigir al lugar anterior
        $return_to = $_POST['return_to'] ?? 'cped.php';
        if ($return_to === 'detalle' && $pedido_id) {
            header('Location: cped.php?a=detalle&id=' . $pedido_id);
        } else {
            header('Location: cped.php');
        }
        exit;
        
    case 'actualizar_mesa':
        // DEPRECATED: Usar controlador de mesas
        require_role(['admin', 'mesero']);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { 
            http_response_code(405); 
            exit; 
        }
        
        $csrfToken = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
        if (!validate_csrf($csrfToken)) {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode(['ok' => false, 'error' => 'Token CSRF inválido']);
            exit;
        }
        
        $mesaId = (int)($_POST['mesa_id'] ?? 0);
        $estado = $_POST['estado'] ?? '';
        
        if (!$mesaId || !in_array($estado, ['disponible', 'ocupada'], true)) {
            http_response_code(422);
            header('Content-Type: application/json');
            echo json_encode(['ok' => false, 'error' => 'Datos inválidos']);
            exit;
        }
        
        $pdo->beginTransaction();
        $stmt = $pdo->prepare('SELECT estado FROM mesas WHERE id=? FOR UPDATE');
        $stmt->execute([$mesaId]);
        $mesa = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$mesa) {
            $pdo->rollBack();
            http_response_code(404);
            header('Content-Type: application/json');
            echo json_encode(['ok' => false, 'error' => 'Mesa no encontrada']);
            exit;
        }
        
        if ($mesa['estado'] === $estado) {
            $pdo->rollBack();
            header('Content-Type: application/json');
            echo json_encode(['ok' => true, 'estado' => $estado]);
            exit;
        }
        
        $upd = $pdo->prepare('UPDATE mesas SET estado=?, fecha_actualizacion=NOW() WHERE id=?');
        $upd->execute([$estado, $mesaId]);
        $pdo->commit();
        
        header('Content-Type: application/json');
        echo json_encode(['ok' => true, 'estado' => $estado]);
        exit;
        
    default:
        http_response_code(400);
        echo 'Acción no válida';
}
