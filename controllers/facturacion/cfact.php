<?php
require_once __DIR__ . '/../../models/conexion.php';
require_once __DIR__ . '/../../models/seg.php';
require_login();
require_role(['admin','cajero']);

$action = $_GET['a'] ?? 'list';

switch ($action) {
    case 'list':
        $facturas = $pdo->query('SELECT p.id pedido_id, p.fecha_creacion, SUM(d.cantidad * d.precio) total FROM pedidos p INNER JOIN detalle_pedido d ON d.pedido_id=p.id WHERE p.estado="completado" GROUP BY p.id ORDER BY p.fecha_creacion DESC')->fetchAll(PDO::FETCH_ASSOC);
        include __DIR__ . '/../../views/facturacion/vfact.php';
        break;
        case 'pdf':
                // Generar PDF de la factura usando Dompdf
                require_login(); require_role(['admin','cajero']);
                $pedido_id = (int)($_GET['id'] ?? 0);
                if (!$pedido_id) { header('Location: cfact.php'); exit; }
                // Datos de factura
                $stmt = $pdo->prepare('SELECT * FROM facturas WHERE pedido_id=?');
                $stmt->execute([$pedido_id]);
                $factura = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!$factura) { header('Location: cfact.php?a=generar&id=' . $pedido_id); exit; }
                $stmt = $pdo->prepare('SELECT d.*, p.nombre FROM detalle_pedido d INNER JOIN productos p ON p.id=d.producto_id WHERE d.pedido_id=?');
                $stmt->execute([$pedido_id]);
                $detalle = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Construir HTML simple
                // Preparar QR como data URI para incrustar en el PDF (mejor que rutas relativas)
                $qr_img_src = null;
                if (!empty($factura['qr_path'])) {
                    $possible = __DIR__ . '/../../' . $factura['qr_path'];
                    if (file_exists($possible)) {
                        $b = base64_encode(file_get_contents($possible));
                        $qr_img_src = 'data:image/png;base64,' . $b;
                    }
                }
                if ($qr_img_src === null) {
                    // Intentar generar data URI al vuelo
                    $items_count = count($detalle);
                    $qr_text = 'Pedido #' . $pedido_id . "|Total:" . ($factura['total'] ?? 0) . "|Items:" . $items_count;
                    if (function_exists('generate_qr_data_uri')) {
                        $dq = generate_qr_data_uri($qr_text, ['size' => 140, 'format' => 'png']);
                        if ($dq) $qr_img_src = $dq;
                    }
                }

                ob_start();
                ?>
                <html><head><meta charset="utf-8"><style>body{font-family: DejaVu Sans, sans-serif;} table{width:100%;border-collapse:collapse} th,td{border:1px solid #ccc;padding:6px;font-size:12px} .right{text-align:right}</style></head><body>
                <h2>Factura - Pedido #<?php echo $pedido_id; ?></h2>
                <table><thead><tr><th>Producto</th><th>Cant</th><th class="right">Precio</th><th class="right">Importe</th></tr></thead><tbody>
                <?php foreach ($detalle as $d): $imp=$d['cantidad']*$d['precio']; ?>
                    <tr><td><?php echo htmlspecialchars($d['nombre']); ?></td><td><?php echo (int)$d['cantidad']; ?></td><td class="right">$<?php echo number_format($d['precio'],2); ?></td><td class="right">$<?php echo number_format($imp,2); ?></td></tr>
                <?php endforeach; ?>
                </tbody></table>
                <p class="right">Subtotal: $<?php echo number_format($factura['subtotal'],2); ?><br>IVA (19%): $<?php echo number_format($factura['impuestos'],2); ?><br><strong>Total: $<?php echo number_format($factura['total'],2); ?></strong></p>
                <?php if (!empty($qr_img_src)): ?><p><img src="<?php echo $qr_img_src; ?>" width="140" height="140"></p><?php endif; ?>
                </body></html>
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
        require_once __DIR__ . '/../../models/qr.php';
        $pedido_id = (int)($_GET['id'] ?? 0);
        if (!$pedido_id) { header('Location: cfact.php'); exit; }
        // Si ya existe factura, usarla; si no, crearla
        $stmt = $pdo->prepare('SELECT * FROM facturas WHERE pedido_id=?');
        $stmt->execute([$pedido_id]);
        $factura = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$factura) {
            // Calcular totales desde detalle_pedido
            $stmt = $pdo->prepare('SELECT producto_id, cantidad, precio FROM detalle_pedido WHERE pedido_id=?');
            $stmt->execute([$pedido_id]);
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $subtotal = 0.0; foreach ($items as $it) { $subtotal += $it['cantidad'] * $it['precio']; }
            $impuestos = round($subtotal * 0.19, 2);
            $total = round($subtotal + $impuestos, 2);
            $qr_text = 'Pedido #' . $pedido_id . "|Total:" . $total . "|Items:" . count($items);
            $qr_path_rel = 'img/qr/pedido_' . $pedido_id . '.png';
            $qr_full = __DIR__ . '/../../' . $qr_path_rel;
            $qr_ok = generate_qr_png($qr_text, $qr_full);
            $stmtF = $pdo->prepare('INSERT INTO facturas (pedido_id, subtotal, impuestos, total, qr_path) VALUES (?,?,?,?,?)');
            $stmtF->execute([$pedido_id, $subtotal, $impuestos, $total, $qr_ok ? $qr_path_rel : null]);
            $stmt = $pdo->prepare('SELECT * FROM facturas WHERE pedido_id=?');
            $stmt->execute([$pedido_id]);
            $factura = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        // Items y datos para vista
        $stmt = $pdo->prepare('SELECT d.*, p.nombre FROM detalle_pedido d INNER JOIN productos p ON p.id=d.producto_id WHERE d.pedido_id=?');
        $stmt->execute([$pedido_id]);
        $detalle = $stmt->fetchAll(PDO::FETCH_ASSOC);
        include __DIR__ . '/../../views/facturacion/vfact_det.php';
        break;
    default:
        http_response_code(400);
        echo 'Acción no válida';
}
