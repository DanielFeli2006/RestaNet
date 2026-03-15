<?php
/**
 * Vista de Factura para Clientes (sin sesión)
 * Acceso mediante token seguro con expiración
 * 
 * REFACTORIZADO: Marzo 2026 - Implementado acceso por token
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Factura #<?php echo (int)($_GET['id'] ?? 0); ?> - RestaNet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        body { 
            background: linear-gradient(135deg, #f5f5f5 0%, #e8e8e8 100%); 
            min-height: 100vh; 
            padding: 20px;
        }
        .invoice-card {
            max-width: 700px;
            margin: 0 auto;
            background: white;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .invoice-header {
            background: linear-gradient(135deg, #C41E3A 0%, #8B0000 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .invoice-body { padding: 30px; }
        .brand-icon { 
            font-size: 48px; 
            margin-bottom: 15px;
            color: #F4A900;
        }
        .total-box {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            margin-top: 20px;
        }
        .total-amount {
            color: #C41E3A;
            font-size: 2rem;
            font-weight: 700;
        }
    </style>
</head>
<body>
    <div class="invoice-card">
        <div class="invoice-header">
            <i class="fa-solid fa-utensils brand-icon"></i>
            <h1 class="h3 mb-0">RestaNet</h1>
            <p class="mb-0 opacity-75">Factura de Pedido</p>
        </div>
        
        <div class="invoice-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-0">Pedido #<?php echo (int)($_GET['id'] ?? 0); ?></h4>
                    <small class="text-muted"><?php echo date('d/m/Y H:i'); ?></small>
                </div>
                <span class="badge bg-success px-3 py-2">
                    <i class="fa-solid fa-check me-1"></i>Confirmado
                </span>
            </div>
            
            <table class="table">
                <thead class="table-light">
                    <tr>
                        <th>Producto</th>
                        <th class="text-center">Cant.</th>
                        <th class="text-end">Precio</th>
                        <th class="text-end">Total</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($detalle)): ?>
                    <?php foreach ($detalle as $item): $subtotal = $item['cantidad'] * $item['precio']; ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['nombre']); ?></td>
                        <td class="text-center"><?php echo (int)$item['cantidad']; ?></td>
                        <td class="text-end">$<?php echo number_format($item['precio'], 2); ?></td>
                        <td class="text-end">$<?php echo number_format($subtotal, 2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center text-muted">Sin detalles disponibles</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
            
            <?php if ($factura): ?>
            <div class="total-box">
                <div class="d-flex justify-content-between mb-2">
                    <span>Subtotal</span>
                    <span>$<?php echo number_format($factura['subtotal'], 2); ?></span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>IVA (19%)</span>
                    <span>$<?php echo number_format($factura['impuestos'], 2); ?></span>
                </div>
                <hr>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="fw-bold fs-5">Total</span>
                    <span class="total-amount">$<?php echo number_format($factura['total'], 2); ?></span>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="text-center mt-4 pt-4 border-top">
                <p class="text-muted small mb-0">
                    <i class="fa-solid fa-shield-check me-1"></i>
                    Este enlace es válido por tiempo limitado
                </p>
                <p class="text-muted small">
                    Gracias por su preferencia
                </p>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
