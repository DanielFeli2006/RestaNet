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
        <button class="btn btn-outline-secondary" onclick="window.print()">
          <i class="fa-solid fa-print me-2"></i>Imprimir
        </button>
        <a href="<?php echo BASE_PATH; ?>controllers/facturacion/cfact.php?a=pdf&id=<?php echo (int)($_GET['id'] ?? 0); ?>" class="btn btn-accent">
          <i class="fa-solid fa-file-pdf me-2"></i>Descargar PDF
        </a>
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
        
        <!-- Estado de la factura -->
        <?php 
        $estado = $factura['estado'] ?? 'pendiente';
        $estadoClass = match($estado) {
            'pagada' => 'bg-success',
            'anulada' => 'bg-danger',
            default => 'bg-warning text-dark'
        };
        ?>
        <div class="mb-4">
          <span class="badge <?php echo $estadoClass; ?> px-3 py-2">
            <i class="fa-solid fa-<?php echo $estado === 'pagada' ? 'check-circle' : ($estado === 'anulada' ? 'times-circle' : 'clock'); ?> me-1"></i>
            Estado: <?php echo ucfirst($estado); ?>
          </span>
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
                    <?php echo e($d['nombre']); ?>
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
        
        <!-- Enlace público de acceso -->
        <div class="mt-4 pt-4 border-top">
          <div class="row align-items-center">
            <div class="col-auto">
              <div class="d-flex align-items-center justify-content-center bg-success-subtle rounded-3" style="width:80px;height:80px">
                <i class="fa-solid fa-link fa-2x text-success"></i>
              </div>
            </div>
            <div class="col">
              <?php if (!empty($factura['token_acceso'])): ?>
                <h6 class="fw-bold mb-2">
                  <i class="fa-solid fa-share-from-square me-2" style="color: #28A745;"></i>Enlace de acceso público
                </h6>
                <p class="text-muted mb-2 small">
                  Comparte este enlace con el cliente para que pueda ver su factura sin iniciar sesión.
                </p>
                <?php $enlace_publico = BASE_URL . 'controllers/facturacion/cfact.php?a=ver_publica&token=' . urlencode($factura['token_acceso']); ?>
                <div class="input-group input-group-sm">
                  <input type="text" class="form-control" value="<?php echo e($enlace_publico); ?>" id="enlacePublico" readonly>
                  <button class="btn btn-outline-success" type="button" onclick="copiarEnlace()">
                    <i class="fa-solid fa-copy"></i>
                  </button>
                </div>
                <small class="text-muted d-block mt-2">
                  <i class="fa-solid fa-clock me-1"></i>
                  Válido hasta: <?php echo date('d/m/Y', strtotime($factura['token_expiracion'])); ?>
                </small>
              <?php else: ?>
                <div class="alert alert-warning small mb-0">
                  <i class="fa-solid fa-triangle-exclamation me-2"></i>
                  No se ha generado un enlace de acceso público para esta factura.
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
        
        <!-- Acciones de estado -->
        <div class="mt-4 pt-4 border-top">
          <h6 class="fw-bold mb-3">
            <i class="fa-solid fa-gear me-2 text-muted"></i>Cambiar estado
          </h6>
          <form method="post" action="<?php echo BASE_PATH; ?>controllers/facturacion/cfact.php?a=actualizar_estado" class="d-flex gap-2 flex-wrap">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="factura_id" value="<?php echo e($factura['id']); ?>">
            <select name="estado" class="form-select form-select-sm" style="width: auto;">
              <option value="pendiente" <?php echo $estado === 'pendiente' ? 'selected' : ''; ?>>Pendiente</option>
              <option value="pagada" <?php echo $estado === 'pagada' ? 'selected' : ''; ?>>Pagada</option>
              <option value="anulada" <?php echo $estado === 'anulada' ? 'selected' : ''; ?>>Anulada</option>
            </select>
            <button type="submit" class="btn btn-sm btn-primary">
              <i class="fa-solid fa-save me-1"></i>Guardar
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
function copiarEnlace() {
  const input = document.getElementById('enlacePublico');
  input.select();
  input.setSelectionRange(0, 99999);
  navigator.clipboard.writeText(input.value).then(() => {
    // Feedback visual
    const btn = input.nextElementSibling;
    const originalHtml = btn.innerHTML;
    btn.innerHTML = '<i class="fa-solid fa-check"></i>';
    btn.classList.remove('btn-outline-success');
    btn.classList.add('btn-success');
    setTimeout(() => {
      btn.innerHTML = originalHtml;
      btn.classList.remove('btn-success');
      btn.classList.add('btn-outline-success');
    }, 2000);
  });
}
</script>

<?php include __DIR__ . '/../layout/pie.php'; ?>