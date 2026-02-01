<?php require_once __DIR__ . '/../../models/seg.php'; require_login(); require_role(['admin','cajero']); ?>
<?php include __DIR__ . '/../layout/cabezote.php'; ?>

<div class="row justify-content-center">
  <div class="col-12 col-lg-10 col-xl-9">
    <!-- Invoice Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-3">
      <div class="d-flex align-items-center">
        <a href="<?php echo BASE_PATH; ?>controllers/facturacion/cfact.php" class="btn btn-outline-secondary btn-sm me-3">
          <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
          <h2 class="h3 mb-0 fw-bold">
            <i class="fa-solid fa-file-invoice-dollar me-2" style="color: #C41E3A;"></i>
            Factura
          </h2>
          <small class="text-muted">Pedido #<?php echo (int)($_GET['id'] ?? 0); ?></small>
        </div>
      </div>
      <div class="d-flex gap-2">
        <button class="btn btn-brand" disabled>
          <i class="fa-solid fa-print me-2"></i>Imprimir
        </button>
        <button class="btn btn-accent" disabled>
          <i class="fa-solid fa-file-pdf me-2"></i>Descargar PDF
        </button>
      </div>
    </div>
    
    <!-- Invoice Card -->
    <div class="card shadow-soft border-0">
      <div class="card-body p-4 p-md-5">
        <!-- Brand Header -->
        <div class="d-flex justify-content-between align-items-center mb-4 pb-4 border-bottom">
          <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; background: linear-gradient(135deg, #C41E3A, #8B0000);">
              <i class="fa-solid fa-utensils fa-lg text-white"></i>
            </div>
            <div>
              <h4 class="mb-0 fw-bold" style="color: #C41E3A;">RestaNet</h4>
              <small class="text-muted">Sistema de Restaurante</small>
            </div>
          </div>
          <div class="text-end">
            <h5 class="mb-0 fw-bold">Factura #<?php echo (int)($_GET['id'] ?? 0); ?></h5>
            <small class="text-muted"><?php echo date('d/m/Y H:i'); ?></small>
          </div>
        </div>
        
        <!-- Products Table -->
        <div class="table-responsive mb-4">
          <table class="table align-middle">
            <thead class="table-light">
              <tr>
                <th class="ps-3">Producto</th>
                <th class="text-center">Cantidad</th>
                <th class="text-end">Precio</th>
                <th class="text-end pe-3">Importe</th>
              </tr>
            </thead>
            <tbody>
              <?php $subtotal_calc=0.0; foreach (($detalle ?? []) as $d): $importe=$d['cantidad']*$d['precio']; $subtotal_calc+=$importe; ?>
              <tr>
                <td class="ps-3">
                  <div class="d-flex align-items-center gap-2">
                    <i class="fa-solid fa-circle-check" style="color: #28A745;"></i>
                    <?php echo htmlspecialchars($d['nombre']); ?>
                  </div>
                </td>
                <td class="text-center">
                  <span class="badge bg-light text-dark"><?php echo (int)$d['cantidad']; ?></span>
                </td>
                <td class="text-end text-muted">$<?php echo number_format($d['precio'],2); ?></td>
                <td class="text-end pe-3 fw-semibold">$<?php echo number_format($importe,2); ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        
        <!-- Totals -->
        <?php $subtotal = $factura['subtotal'] ?? ($subtotal_calc ?? 0); $impuestos = $factura['impuestos'] ?? round($subtotal*0.19,2); $total = $factura['total'] ?? round($subtotal+$impuestos,2); ?>
        <div class="row justify-content-end">
          <div class="col-12 col-md-5">
            <div class="bg-light rounded-4 p-4">
              <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Subtotal</span>
                <span>$<?php echo number_format($subtotal,2); ?></span>
              </div>
              <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">IVA (19%)</span>
                <span>$<?php echo number_format($impuestos,2); ?></span>
              </div>
              <hr class="my-2">
              <div class="d-flex justify-content-between">
                <span class="fw-bold fs-5">Total</span>
                <span class="fw-bold fs-4" style="color: #C41E3A;">$<?php echo number_format($total,2); ?></span>
              </div>
            </div>
          </div>
        </div>
        
        <!-- QR Section -->
        <div class="mt-4 pt-4 border-top">
          <div class="row align-items-center">
            <div class="col-auto">
              <?php if (!empty($factura['qr_path'])): ?>
                <img src="<?php echo BASE_PATH . $factura['qr_path']; ?>" alt="QR Pedido" class="rounded-3 shadow-sm" style="width:140px;height:140px"/>
              <?php else: ?>
                <div class="d-flex align-items-center justify-content-center bg-light rounded-3" style="width:140px;height:140px">
                  <i class="fa-solid fa-qrcode fa-3x text-muted"></i>
                </div>
              <?php endif; ?>
            </div>
            <div class="col">
              <?php if (!empty($factura['qr_path'])): ?>
                <h6 class="fw-bold mb-2">
                  <i class="fa-solid fa-qrcode me-2" style="color: #F4A900;"></i>Código QR del pedido
                </h6>
                <p class="text-muted mb-0 small">Escanea este código para confirmar el pedido y ver los detalles del pago desde cualquier dispositivo.</p>
              <?php else: ?>
                <div class="alert alert-warning small mb-0">
                  <i class="fa-solid fa-triangle-exclamation me-2"></i>
                  Código QR no disponible. Ejecuta <code>composer install</code> para instalar la librería.
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../layout/pie.php'; ?>