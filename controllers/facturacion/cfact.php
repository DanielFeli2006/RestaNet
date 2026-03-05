<?php
/**
 * Controlador de Facturación
 * 
 * SEGURIDAD:
 * - Requiere autenticación para operaciones administrativas
 * - Permite acceso público solo mediante token válido no expirado
 * - Validación de entrada y sanitización de datos
 * - Rate limiting para acceso público
 * - Registro de accesos para auditoría
 */
require_once __DIR__ . '/../../models/conexion.php';
require_once __DIR__ . '/../../models/seg.php';

$action = $_GET['a'] ?? 'list';

// Acción pública: ver factura con token (sin login)
if ($action === 'ver_publica') {
    $token = trim($_GET['token'] ?? '');
    
    // SEGURIDAD: Validar formato del token (64 caracteres hexadecimales)
    if (!preg_match('/^[a-f0-9]{64}$/i', $token)) {
        http_response_code(400);
        include __DIR__ . '/../../views/facturacion/vfact_error.php';
        exit;
    }
    
    // SEGURIDAD: Rate limiting por IP
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    if (!check_rate_limit('factura_publica_' . $ip, 30, 60)) {
        http_response_code(429);
        $error_msg = 'Demasiadas solicitudes. Intenta nuevamente en un minuto.';
        include __DIR__ . '/../../views/facturacion/vfact_error.php';
        exit;
    }
    
    // Buscar factura por token
    $stmt = $pdo->prepare('SELECT f.*, p.estado AS pedido_estado, p.fecha_creacion AS pedido_fecha 
                           FROM facturas f 
                           INNER JOIN pedidos p ON p.id = f.pedido_id 
                           WHERE f.token_acceso = ? AND f.token_expiracion > NOW()');
    $stmt->execute([$token]);
    $factura = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$factura) {
        http_response_code(404);
        $error_msg = 'Factura no encontrada o el enlace ha expirado.';
        include __DIR__ . '/../../views/facturacion/vfact_error.php';
        exit;
    }
    
    // SEGURIDAD: Registrar acceso para auditoría
    $stmtLog = $pdo->prepare('INSERT INTO factura_accesos (factura_id, ip_address, user_agent) VALUES (?,?,?)');
    $stmtLog->execute([
        $factura['id'], 
        $ip, 
        substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500)
    ]);
    
    // Obtener detalle del pedido
    $stmt = $pdo->prepare('SELECT d.*, p.nombre FROM detalle_pedido d 
                           INNER JOIN productos p ON p.id = d.producto_id 
                           WHERE d.pedido_id = ?');
    $stmt->execute([$factura['pedido_id']]);
    $detalle = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Vista pública (sin autenticación requerida)
    include __DIR__ . '/../../views/facturacion/vfact_publica.php';
    exit;
}

// Todas las demás acciones requieren autenticación
require_login();
require_role(['admin','cajero']);

switch ($action) {
    case 'list':
        $facturas = $pdo->query('
            SELECT f.id, f.pedido_id, f.subtotal, f.impuestos, f.total, f.estado, f.fecha_creacion,
                   p.estado AS pedido_estado, u.nombre AS cliente_nombre
            FROM facturas f 
            INNER JOIN pedidos p ON p.id = f.pedido_id 
            LEFT JOIN usuarios u ON u.id = p.usuario_id
            ORDER BY f.fecha_creacion DESC
        ')->fetchAll(PDO::FETCH_ASSOC);
        include __DIR__ . '/../../views/facturacion/vfact.php';
        break;
        
    case 'pdf':
        // Generar PDF de la factura usando Dompdf
        $pedido_id = (int)($_GET['id'] ?? 0);
        if (!$pedido_id) { 
            header('Location: cfact.php'); 
            exit; 
        }
        
        // Datos de factura
        $stmt = $pdo->prepare('SELECT * FROM facturas WHERE pedido_id = ?');
        $stmt->execute([$pedido_id]);
        $factura = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$factura) { 
            header('Location: cfact.php?a=generar&id=' . $pedido_id); 
            exit; 
        }
        
        $stmt = $pdo->prepare('SELECT d.*, p.nombre FROM detalle_pedido d 
                               INNER JOIN productos p ON p.id = d.producto_id 
                               WHERE d.pedido_id = ?');
        $stmt->execute([$pedido_id]);
        $detalle = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Construir HTML para el PDF
        ob_start();
        ?>
        <html>
        <head>
            <meta charset="utf-8">
            <style>
                body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
                table { width: 100%; border-collapse: collapse; margin: 20px 0; }
                th, td { border: 1px solid #ddd; padding: 8px; }
                th { background-color: #C41E3A; color: white; }
                .right { text-align: right; }
                .header { text-align: center; margin-bottom: 30px; }
                .header h1 { color: #C41E3A; margin: 0; }
                .totals { margin-top: 20px; }
                .footer { margin-top: 40px; text-align: center; font-size: 10px; color: #666; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>RestaNet</h1>
                <p>Factura - Pedido #<?php echo e($pedido_id); ?></p>
                <p>Fecha: <?php echo date('d/m/Y H:i', strtotime($factura['fecha_creacion'])); ?></p>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th class="right">Precio</th>
                        <th class="right">Importe</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($detalle as $d): $imp = $d['cantidad'] * $d['precio']; ?>
                    <tr>
                        <td><?php echo e($d['nombre']); ?></td>
                        <td><?php echo (int)$d['cantidad']; ?></td>
                        <td class="right">$<?php echo number_format($d['precio'], 2); ?></td>
                        <td class="right">$<?php echo number_format($imp, 2); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            
            <div class="totals">
                <p class="right">Subtotal: $<?php echo number_format($factura['subtotal'], 2); ?></p>
                <p class="right">IVA (19%): $<?php echo number_format($factura['impuestos'], 2); ?></p>
                <p class="right"><strong>Total: $<?php echo number_format($factura['total'], 2); ?></strong></p>
            </div>
            
            <div class="footer">
                <p>Gracias por su compra - RestaNet</p>
                <p>Este documento es un comprobante de su pedido</p>
            </div>
        </body>
        </html>
        <?php
        $html = ob_get_clean();
        
        // Render con Dompdf
        $dompdf = new Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4');
        $dompdf->render();
        $dompdf->stream('factura_pedido_' . $pedido_id . '.pdf');
        exit;
        
    case 'generar':
        $pedido_id = (int)($_GET['id'] ?? 0);
        if (!$pedido_id) { 
            header('Location: cfact.php'); 
            exit; 
        }
        
        // Si ya existe factura, usarla; si no, crearla
        $stmt = $pdo->prepare('SELECT * FROM facturas WHERE pedido_id = ?');
        $stmt->execute([$pedido_id]);
        $factura = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$factura) {
            // Calcular totales desde detalle_pedido
            $stmt = $pdo->prepare('SELECT producto_id, cantidad, precio FROM detalle_pedido WHERE pedido_id = ?');
            $stmt->execute([$pedido_id]);
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $subtotal = 0.0;
            foreach ($items as $it) { 
                $subtotal += $it['cantidad'] * $it['precio']; 
            }
            $impuestos = round($subtotal * 0.19, 2);
            $total = round($subtotal + $impuestos, 2);
            
            // SEGURIDAD: Generar token de acceso seguro
            $token_acceso = bin2hex(random_bytes(32));
            $token_expiracion = date('Y-m-d H:i:s', strtotime('+30 days'));
            
            $stmtF = $pdo->prepare('INSERT INTO facturas (pedido_id, subtotal, impuestos, total, token_acceso, token_expiracion, estado) VALUES (?,?,?,?,?,?,?)');
            $stmtF->execute([$pedido_id, $subtotal, $impuestos, $total, $token_acceso, $token_expiracion, 'pendiente']);
            
            $stmt = $pdo->prepare('SELECT * FROM facturas WHERE pedido_id = ?');
            $stmt->execute([$pedido_id]);
            $factura = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        
        // Items y datos para vista
        $stmt = $pdo->prepare('SELECT d.*, p.nombre FROM detalle_pedido d 
                               INNER JOIN productos p ON p.id = d.producto_id 
                               WHERE d.pedido_id = ?');
        $stmt->execute([$pedido_id]);
        $detalle = $stmt->fetchAll(PDO::FETCH_ASSOC);
        include __DIR__ . '/../../views/facturacion/vfact_det.php';
        break;
        
    case 'actualizar_estado':
        // SEGURIDAD: Validar CSRF y datos
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }
        
        if (!validate_csrf($_POST['csrf_token'] ?? null)) {
            $_SESSION['error'] = 'Token CSRF inválido.';
            header('Location: cfact.php');
            exit;
        }
        
        $factura_id = (int)($_POST['factura_id'] ?? 0);
        $estado = $_POST['estado'] ?? '';
        
        if (!$factura_id || !in_array($estado, ['pendiente', 'pagada', 'anulada'], true)) {
            $_SESSION['error'] = 'Datos inválidos.';
            header('Location: cfact.php');
            exit;
        }
        
        $stmt = $pdo->prepare('UPDATE facturas SET estado = ? WHERE id = ?');
        $stmt->execute([$estado, $factura_id]);
        
        $_SESSION['success'] = 'Estado de factura actualizado.';
        header('Location: cfact.php');
        exit;
        
    default:
        http_response_code(400);
        echo 'Acción no válida';
}
