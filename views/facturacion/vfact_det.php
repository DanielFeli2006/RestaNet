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
        <!-- FIX: Eliminado botón "Imprimir". Solo queda "Descargar PDF" -->
        <a href="<?php echo BASE_PATH; ?>controllers/facturacion/cfact.php?a=pdf&id=<?php echo (int)($_GET['id'] ?? 0); ?>" class="btn btn-accent">
          <i class="fa-solid fa-file-pdf me-2"></i>Descargar PDF
        </a>
      </div>
    </div>
    
    <!-- Mensajes de error/éxito -->
    <?php if (!empty($_SESSION['error'])): ?>
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-exclamation-circle me-2"></i>
        <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>
    <?php if (!empty($_SESSION['success'])): ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-check-circle me-2"></i>
        <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>
    
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
            <h5 class="mb-0 fw-bold">Factura #<?php echo (int)($factura['id'] ?? 0); ?></h5>
            <small class="text-muted"><?php echo !empty($factura['fecha_creacion']) ? date('d/m/Y H:i', strtotime($factura['fecha_creacion'])) : date('d/m/Y H:i'); ?></small>
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
            <?php echo ucfirst($estado); ?>
          </span>
        </div>
        
        <!-- Tabla de productos -->
        <div class="table-responsive">
          <table class="table table-hover">
            <thead class="table-light">
              <tr>
                <th>Producto</th>
                <th class="text-center">Cantidad</th>
                <th class="text-end">Precio Unit.</th>
                <th class="text-end">Importe</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($detalle as $d): 
                $importe = $d['cantidad'] * $d['precio'];
              ?>
              <tr>
                <td>
                  <i class="fa-solid fa-utensils me-2 text-muted small"></i>
                  <?php echo htmlspecialchars($d['nombre']); ?>
                </td>
                <td class="text-center"><?php echo (int)$d['cantidad']; ?></td>
                <td class="text-end">$<?php echo number_format($d['precio'], 2); ?></td>
                <td class="text-end fw-semibold">$<?php echo number_format($importe, 2); ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        
        <!-- Totales -->
        <div class="row justify-content-end mt-3">
          <div class="col-12 col-md-5">
            <div class="d-flex justify-content-between mb-2">
              <span class="text-muted">Subtotal:</span>
              <span>$<?php echo number_format($factura['subtotal'] ?? 0, 2); ?></span>
            </div>
            <div class="d-flex justify-content-between mb-2">
              <span class="text-muted">IVA (19%):</span>
              <span>$<?php echo number_format($factura['impuestos'] ?? 0, 2); ?></span>
            </div>
            <hr>
            <div class="d-flex justify-content-between">
              <span class="fw-bold fs-5">Total:</span>
              <span class="fw-bold fs-5" style="color: #C41E3A;">$<?php echo number_format($factura['total'] ?? 0, 2); ?></span>
            </div>
          </div>
        </div>
        
        <!-- Enlace público -->
        <?php if (!empty($factura['token_acceso'])): ?>
        <div class="mt-4 pt-4 border-top">
          <h6 class="fw-bold mb-3">
            <i class="fa-solid fa-link me-2 text-muted"></i>Enlace público
          </h6>
          <div class="input-group">
            <input type="text" class="form-control" id="enlacePublico" readonly
                   value="<?php echo BASE_URL; ?>controllers/facturacion/cfact.php?a=publica&token=<?php echo htmlspecialchars($factura['token_acceso']); ?>">
            <button class="btn btn-outline-success" type="button" onclick="copiarEnlace()">
              <i class="fa-solid fa-copy me-1"></i>Copiar
            </button>
          </div>
          <small class="text-muted mt-1 d-block">
            <i class="fa-solid fa-info-circle me-1"></i>
            Este enlace permite ver la factura sin iniciar sesión.
            <?php if (!empty($factura['token_expiracion'])): ?>
              Expira el <?php echo date('d/m/Y', strtotime($factura['token_expiracion'])); ?>.
            <?php endif; ?>
          </small>

          <?php if (!empty($factura['codigo_validacion'])): ?>
          <div class="mt-3">
            <label class="form-label small text-muted mb-1">Código de validación</label>
            <div class="input-group">
              <input type="text" class="form-control" id="codigoValidacion" readonly value="<?php echo htmlspecialchars($factura['codigo_validacion']); ?>">
              <button class="btn btn-outline-secondary" type="button" onclick="copiarCodigoValidacion()">
                <i class="fa-solid fa-copy me-1"></i>Copiar
              </button>
              <a class="btn btn-outline-primary" href="<?php echo BASE_URL; ?>controllers/facturacion/cfact.php?a=publica&codigo=<?php echo urlencode($factura['codigo_validacion']); ?>" target="_blank">
                <i class="fa-solid fa-shield-check me-1"></i>Validar
              </a>
            </div>
          </div>
          <?php endif; ?>
        </div>
        <?php endif; ?>
        
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

function copiarCodigoValidacion() {
  const input = document.getElementById('codigoValidacion');
  if (!input) return;
  input.select();
  input.setSelectionRange(0, 99999);
  navigator.clipboard.writeText(input.value);
}
</script>

<?php include __DIR__ . '/../layout/pie.php'; ?>
