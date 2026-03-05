<?php
/**
 * Vista pública de factura - Accesible sin inicio de sesión mediante token
 * 
 * SEGURIDAD: Esta vista solo muestra información de la factura específica
 * No expone datos sensibles del sistema
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Factura #<?php echo e($factura['pedido_id']); ?> - RestaNet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --brand-primary: #C41E3A;
            --brand-secondary: #F4A900;
        }
        body {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
        }
        .invoice-card {
            max-width: 800px;
            margin: 2rem auto;
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }
        .invoice-header {
            background: linear-gradient(135deg, var(--brand-primary), #8B0000);
            color: white;
            padding: 2rem;
        }
        .status-badge {
            font-size: 0.85rem;
            padding: 0.5rem 1rem;
            border-radius: 2rem;
        }
        .status-pendiente { background: #ffc107; color: #000; }
        .status-pagada { background: #28a745; color: #fff; }
        .status-anulada { background: #dc3545; color: #fff; }
        .table th {
            background: #f8f9fa;
            font-weight: 600;
        }
        .totals-section {
            background: #f8f9fa;
            border-radius: 0.5rem;
            padding: 1.5rem;
        }
        .brand-logo {
            width: 60px;
            height: 60px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        @media print {
            .no-print { display: none !important; }
            .invoice-card { box-shadow: none; margin: 0; }
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="card invoice-card border-0">
            <!-- Header -->
            <div class="invoice-header">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="brand-logo">
                            <i class="fa-solid fa-utensils fa-2x"></i>
                        </div>
                        <div>
                            <h1 class="h3 mb-0 fw-bold">RestaNet</h1>
                            <small class="opacity-75">Sistema de Restaurante</small>
                        </div>
                    </div>
                    <div class="text-end">
                        <h2 class="h4 mb-1">Factura #<?php echo e($factura['pedido_id']); ?></h2>
                        <small class="opacity-75">
                            <i class="fa-regular fa-calendar me-1"></i>
                            <?php echo date('d/m/Y H:i', strtotime($factura['fecha_creacion'])); ?>
                        </small>
                    </div>
                </div>
            </div>
            
            <!-- Body -->
            <div class="card-body p-4">
                <!-- Estado -->
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                    <div>
                        <span class="text-muted">Estado del pedido:</span>
                        <?php 
                        $estado = $factura['estado'] ?? 'pendiente';
                        $estadoClass = 'status-' . $estado;
                        $estadoLabel = ucfirst($estado);
                        ?>
                        <span class="status-badge <?php echo $estadoClass; ?> ms-2">
                            <i class="fa-solid fa-<?php echo $estado === 'pagada' ? 'check-circle' : ($estado === 'anulada' ? 'times-circle' : 'clock'); ?> me-1"></i>
                            <?php echo $estadoLabel; ?>
                        </span>
                    </div>
                    <div class="no-print">
                        <button onclick="window.print()" class="btn btn-outline-secondary btn-sm">
                            <i class="fa-solid fa-print me-1"></i>Imprimir
                        </button>
                    </div>
                </div>
                
                <!-- Productos -->
                <h5 class="fw-semibold mb-3">
                    <i class="fa-solid fa-list me-2 text-muted"></i>Detalle del pedido
                </h5>
                <div class="table-responsive mb-4">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th class="ps-3">Producto</th>
                                <th class="text-center">Cantidad</th>
                                <th class="text-end">Precio</th>
                                <th class="text-end pe-3">Importe</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($detalle as $item): 
                                $importe = $item['cantidad'] * $item['precio'];
                            ?>
                            <tr>
                                <td class="ps-3">
                                    <i class="fa-solid fa-bowl-food text-muted me-2"></i>
                                    <?php echo e($item['nombre']); ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark"><?php echo (int)$item['cantidad']; ?></span>
                                </td>
                                <td class="text-end text-muted">$<?php echo number_format($item['precio'], 2); ?></td>
                                <td class="text-end pe-3 fw-semibold">$<?php echo number_format($importe, 2); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Totales -->
                <div class="row justify-content-end">
                    <div class="col-12 col-md-5">
                        <div class="totals-section">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Subtotal</span>
                                <span>$<?php echo number_format($factura['subtotal'], 2); ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">IVA (19%)</span>
                                <span>$<?php echo number_format($factura['impuestos'], 2); ?></span>
                            </div>
                            <hr class="my-2">
                            <div class="d-flex justify-content-between">
                                <span class="fw-bold fs-5">Total</span>
                                <span class="fw-bold fs-4" style="color: var(--brand-primary);">
                                    $<?php echo number_format($factura['total'], 2); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="card-footer bg-light text-center py-3">
                <small class="text-muted">
                    <i class="fa-solid fa-shield-halved me-1"></i>
                    Este enlace es válido hasta <?php echo date('d/m/Y', strtotime($factura['token_expiracion'])); ?>
                </small>
            </div>
        </div>
        
        <!-- Info adicional -->
        <div class="text-center mt-3 no-print">
            <small class="text-muted">
                ¿Tienes preguntas sobre tu pedido? Contacta al restaurante.
            </small>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>