<?php
/**
 * Controlador de Facturación (CORREGIDO)
 * 
 * CAMBIOS REALIZADOS (Problema 2 - Error 500 en PDF):
 * 
 * 1. FIX: case 'pdf' - Añadido try-catch completo alrededor de Dompdf.
 *    Sin esto, cualquier error de Dompdf causaba un 500 sin información.
 * 
 * 2. FIX: Configuración explícita de Dompdf con fontDir y fontCache
 *    apuntando a un directorio escribible (fuera de vendor/).
 *    En shared hosting, vendor/dompdf/dompdf/lib/fonts/ NO es escribible,
 *    lo que causaba el Error 500 al intentar cachear fuentes.
 * 
 * 3. FIX: Verificación de que la clase Dompdf existe antes de usarla.
 *    Si el autoloader no cargó correctamente (vendor/ incompleto),
 *    ahora muestra un error claro en vez de un 500 críptico.
 * 
 * 4. FIX: ob_end_clean() antes de enviar el PDF para eliminar cualquier
 *    output previo que pudiera corromper el stream del PDF.
 * 
 * 5. Refactor Marzo 2026: eliminado flujo heredado y añadida validación pública
 *    por token y por código alfanumérico.
 * 
 * 6. Eliminado botón "Imprimir" de la vista (ver vfact_det.php).
 */
require_once __DIR__ . '/../../models/conexion.php';
require_once __DIR__ . '/../../models/seg.php';

// =====================================================
// Acceso público a factura vía token (sin autenticación)
// =====================================================
$action = $_GET['a'] ?? 'list';

if ($action === 'publica') {
    $token = trim($_GET['token'] ?? '');
    $codigo = trim($_GET['codigo'] ?? '');

    $useToken = $token !== '' && preg_match('/^[a-f0-9]{64}$/i', $token);
    $useCodigo = $codigo !== '' && preg_match('/^[a-z0-9]{10,20}$/i', $codigo);

    if (!$useToken && !$useCodigo) {
        $error_msg = 'Debes proporcionar un token o código de validación válido.';
        include __DIR__ . '/../../views/facturacion/vfact_error.php';
        exit;
    }

    if ($useToken) {
        $stmt = $pdo->prepare('SELECT f.* FROM facturas f WHERE f.token_acceso = ? AND (f.token_expiracion IS NULL OR f.token_expiracion > NOW())');
        $stmt->execute([$token]);
    } else {
        $stmt = $pdo->prepare('SELECT f.* FROM facturas f WHERE f.codigo_validacion = ? AND (f.token_expiracion IS NULL OR f.token_expiracion > NOW())');
        $stmt->execute([strtolower($codigo)]);
    }
    $factura = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$factura) {
        http_response_code(404);
        $error_msg = 'Factura no encontrada o validación expirada.';
        include __DIR__ . '/../../views/facturacion/vfact_error.php';
        exit;
    }
    
    // Registrar acceso
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    try {
        $stmtLog = $pdo->prepare('INSERT INTO accesos_factura (factura_id, ip, user_agent) VALUES (?, ?, ?)');
        $stmtLog->execute([
            $factura['id'], 
            $ip, 
            substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500)
        ]);
    } catch (Throwable $e) {
        // Log opcional, no bloquear acceso si la tabla no existe
        error_log('accesos_factura log failed: ' . $e->getMessage());
    }
    
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
        // =====================================================
        // FIX: Generación de PDF con manejo robusto de errores
        // =====================================================
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
            // Si no hay factura, redirigir a generarla primero
            header('Location: cfact.php?a=generar&id=' . $pedido_id); 
            exit; 
        }
        
        $stmt = $pdo->prepare('SELECT d.*, p.nombre FROM detalle_pedido d 
                               INNER JOIN productos p ON p.id = d.producto_id 
                               WHERE d.pedido_id = ?');
        $stmt->execute([$pedido_id]);
        $detalle = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // =====================================================
        // FIX 1: Verificar que Dompdf está disponible
        // =====================================================
        if (!class_exists('Dompdf\\Dompdf')) {
            http_response_code(500);
            error_log('ERROR PDF: Clase Dompdf\\Dompdf no encontrada. Verificar que vendor/ fue subido correctamente.');
            $_SESSION['error'] = 'Error: La librería de generación de PDF no está disponible. Contacte al administrador.';
            header('Location: cfact.php?a=generar&id=' . $pedido_id);
            exit;
        }

        // =====================================================
        // FIX 2: Crear directorio de caché de fuentes escribible
        // =====================================================
        $fontCacheDir = __DIR__ . '/../../storage/fonts';
        if (!is_dir($fontCacheDir)) {
            @mkdir($fontCacheDir, 0755, true);
        }
        
        // Si no se pudo crear, intentar directorio temporal del sistema
        if (!is_dir($fontCacheDir) || !is_writable($fontCacheDir)) {
            $fontCacheDir = sys_get_temp_dir() . '/restanet_dompdf_fonts';
            if (!is_dir($fontCacheDir)) {
                @mkdir($fontCacheDir, 0755, true);
            }
        }

        try {
            // Construir HTML para el PDF
            ob_start();
            ?>
            <html>
            <head>
                <meta charset="utf-8">
                <style>
                    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; }
                    table { width: 100%; border-collapse: collapse; margin: 20px 0; }
                    th, td { border: 1px solid #ddd; padding: 8px; }
                    th { background-color: #C41E3A; color: white; text-align: left; }
                    .right { text-align: right; }
                    .header { text-align: center; margin-bottom: 30px; }
                    .header h1 { color: #C41E3A; margin: 0; font-size: 24px; }
                    .header p { margin: 5px 0; color: #666; }
                    .totals { margin-top: 20px; text-align: right; }
                    .totals p { margin: 5px 0; }
                    .footer { margin-top: 40px; text-align: center; font-size: 10px; color: #666; border-top: 1px solid #ddd; padding-top: 15px; }
                </style>
            </head>
            <body>
                <div class="header">
                    <h1>RestaNet</h1>
                    <p>Factura - Pedido #<?php echo (int)$pedido_id; ?></p>
                    <p>Fecha: <?php echo date('d/m/Y H:i', strtotime($factura['fecha_creacion'])); ?></p>
                    <?php if (!empty($factura['estado'])): ?>
                    <p>Estado: <?php echo ucfirst(htmlspecialchars($factura['estado'])); ?></p>
                    <?php endif; ?>
                </div>
                
                <table>
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th class="right">Precio Unit.</th>
                            <th class="right">Importe</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($detalle as $d): $imp = $d['cantidad'] * $d['precio']; ?>
                        <tr>
                            <td><?php echo htmlspecialchars($d['nombre']); ?></td>
                            <td><?php echo (int)$d['cantidad']; ?></td>
                            <td class="right">$<?php echo number_format($d['precio'], 2); ?></td>
                            <td class="right">$<?php echo number_format($imp, 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                
                <div class="totals">
                    <p>Subtotal: $<?php echo number_format($factura['subtotal'], 2); ?></p>
                    <p>IVA (19%): $<?php echo number_format($factura['impuestos'], 2); ?></p>
                    <p><strong>Total: $<?php echo number_format($factura['total'], 2); ?></strong></p>
                </div>
                
                <div class="footer">
                    <p>Gracias por su compra - RestaNet</p>
                    <p>Este documento es un comprobante de su pedido</p>
                </div>
            </body>
            </html>
            <?php
            $html = ob_get_clean();
            
            // =====================================================
            // FIX 3: Configurar Dompdf con opciones explícitas
            // =====================================================
            $options = new \Dompdf\Options();
            $options->set('isRemoteEnabled', false);     // Seguridad: no cargar recursos remotos
            $options->set('isHtml5ParserEnabled', true);  // Mejor compatibilidad HTML
            $options->set('defaultFont', 'DejaVu Sans');  // Fuente que existe en Dompdf
            
            // FIX: Apuntar caché de fuentes a directorio escribible
            if (is_dir($fontCacheDir) && is_writable($fontCacheDir)) {
                $options->set('fontDir', $fontCacheDir);
                $options->set('fontCache', $fontCacheDir);
            }
            
            $dompdf = new \Dompdf\Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            
            // =====================================================
            // FIX 4: Limpiar CUALQUIER output previo antes de stream
            // =====================================================
            // Esto evita que whitespace, BOM, o notices corrompan el PDF
            while (ob_get_level() > 0) {
                ob_end_clean();
            }
            
            // Enviar PDF al navegador
            $dompdf->stream('factura_pedido_' . $pedido_id . '.pdf', [
                'Attachment' => true  // Forzar descarga
            ]);
            exit;
            
        } catch (Throwable $e) {
            // =====================================================
            // FIX 5: Manejo de errores amigable
            // =====================================================
            // Limpiar cualquier output buffer pendiente
            while (ob_get_level() > 0) {
                ob_end_clean();
            }
            
            error_log('ERROR generando PDF para pedido #' . $pedido_id . ': ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());
            
            $_SESSION['error'] = 'Error al generar el PDF: ' . $e->getMessage() . 
                                 '. Verifique que la carpeta storage/fonts tenga permisos de escritura (755).';
            header('Location: cfact.php?a=generar&id=' . $pedido_id);
            exit;
        }
        
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
            
            if (empty($items)) {
                $_SESSION['error'] = 'El pedido no tiene productos. No se puede generar factura.';
                header('Location: ' . BASE_PATH . 'controllers/pedidos/cped.php');
                exit;
            }
            
            $subtotal = 0.0;
            foreach ($items as $it) { 
                $subtotal += $it['cantidad'] * $it['precio']; 
            }
            $impuestos = round($subtotal * 0.19, 2);
            $total = round($subtotal + $impuestos, 2);
            
            // SEGURIDAD: Generar token y código de validación seguros
            $token_acceso = bin2hex(random_bytes(32));
            $alphabet = 'abcdefghijklmnopqrstuvwxyz0123456789';
            $codigo_validacion = '';
            for ($i = 0; $i < 12; $i++) {
                $codigo_validacion .= $alphabet[random_int(0, strlen($alphabet) - 1)];
            }
            $token_expiracion = date('Y-m-d H:i:s', strtotime('+30 days'));
            
            // Intentar insertar con token y código (tabla puede tener o no estos campos)
            try {
                $stmtF = $pdo->prepare('INSERT INTO facturas (pedido_id, subtotal, impuestos, total, token_acceso, codigo_validacion, token_expiracion, estado) VALUES (?,?,?,?,?,?,?,?)');
                $stmtF->execute([$pedido_id, $subtotal, $impuestos, $total, $token_acceso, $codigo_validacion, $token_expiracion, 'pendiente']);
            } catch (Throwable $e) {
                // Fallback: insertar sin código de validación (esquema legado)
                error_log('Inserción con token/código falló, intentando esquema legado: ' . $e->getMessage());
                $stmtF = $pdo->prepare('INSERT INTO facturas (pedido_id, subtotal, impuestos, total, token_acceso, token_expiracion, estado) VALUES (?,?,?,?,?,?,?)');
                $stmtF->execute([$pedido_id, $subtotal, $impuestos, $total, $token_acceso, $token_expiracion, 'pendiente']);
            }
            
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
        
        // Si se marca como pagada, liberar la mesa asociada
        if ($estado === 'pagada') {
            try {
                $stmt = $pdo->prepare('
                    SELECT p.mesa_id FROM facturas f 
                    INNER JOIN pedidos p ON p.id = f.pedido_id 
                    WHERE f.id = ? AND p.mesa_id IS NOT NULL
                ');
                $stmt->execute([$factura_id]);
                $mesa_id = $stmt->fetchColumn();
                
                if ($mesa_id) {
                    // Verificar si hay otros pedidos activos en esa mesa
                    $stmtCheck = $pdo->prepare('SELECT COUNT(*) FROM pedidos WHERE mesa_id=? AND estado IN ("pendiente","en_preparacion","listo","entregado")');
                    $stmtCheck->execute([$mesa_id]);
                    if ($stmtCheck->fetchColumn() == 0) {
                        $pdo->prepare('UPDATE mesas SET estado="disponible", fecha_actualizacion=NOW() WHERE id=?')
                            ->execute([$mesa_id]);
                    }
                }
            } catch (Throwable $e) {
                error_log('Error liberando mesa tras pago: ' . $e->getMessage());
            }
        }
        
        $_SESSION['success'] = 'Estado de factura actualizado.';
        header('Location: cfact.php');
        exit;
        
    default:
        http_response_code(400);
        echo 'Acción no válida';
}
