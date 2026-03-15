<?php require_once __DIR__ . '/../../models/seg.php'; require_login(); ?>
<?php include __DIR__ . '/../layout/cabezote.php'; ?>

<div class="row justify-content-center align-items-center min-vh-50">
  <div class="col-12 col-md-8 col-lg-6">
    <div class="card shadow-soft border-0 animate-fade-in">
      <div class="card-body text-center p-5">
        <!-- Success Icon -->
        <div class="success-icon mb-4">
          <div class="success-circle">
            <i class="fa-solid fa-check fa-3x"></i>
          </div>
        </div>
        
        <h2 class="h3 mb-2 fw-bold" style="color: #28A745;">¡Pedido confirmado!</h2>
        <p class="text-muted mb-4">
          Tu pedido ha sido registrado exitosamente.<br>
          <span class="fs-4 fw-bold" style="color: #C41E3A;">#<?php echo (int)($_GET['pedido'] ?? 0); ?></span>
        </p>
        
        <?php if ($factura && !empty($factura['token_acceso'])): ?>
          <div class="access-link-container mb-4 p-4 bg-light rounded-4">
            <p class="small text-muted mb-2">
              <i class="fa-solid fa-link me-1"></i>Guarda este enlace para ver tu factura
            </p>
            <?php $enlace_publico = BASE_URL . 'controllers/facturacion/cfact.php?a=publica&token=' . urlencode($factura['token_acceso']); ?>
            <div class="input-group mb-2">
              <input type="text" class="form-control form-control-sm" value="<?php echo e($enlace_publico); ?>" id="enlaceFactura" readonly>
              <button class="btn btn-outline-success btn-sm" type="button" onclick="copiarEnlaceFactura()">
                <i class="fa-solid fa-copy"></i>
              </button>
            </div>
            <?php if (!empty($factura['codigo_validacion'])): ?>
              <p class="small mb-2"><strong>Código de validación:</strong> <?php echo e($factura['codigo_validacion']); ?></p>
            <?php endif; ?>
            <a href="<?php echo e($enlace_publico); ?>" class="btn btn-success btn-sm" target="_blank">
              <i class="fa-solid fa-external-link me-1"></i>Ver factura
            </a>
          </div>
          <div class="alert alert-info small text-start">
            <i class="fa-solid fa-info-circle me-1"></i>
            <strong>Importante:</strong> Este enlace te permite ver tu factura sin necesidad de iniciar sesión. 
            Es válido por 30 días. Guárdalo o compártelo si lo necesitas.
          </div>
        <?php else: ?>
          <div class="alert alert-success d-flex align-items-center gap-2 text-start" role="alert">
            <i class="fa-solid fa-circle-check fa-lg"></i>
            <div class="small">Tu pedido ha sido registrado. Puedes ver el estado desde tu cuenta.</div>
          </div>
        <?php endif; ?>
        
        <div class="d-flex gap-3 justify-content-center flex-wrap">
          <a class="btn btn-brand btn-lg px-4" href="<?php echo BASE_PATH; ?>index.php">
            <i class="fa-solid fa-house me-2"></i>Ir al inicio
          </a>
          <a class="btn btn-outline-secondary btn-lg px-4" href="<?php echo BASE_PATH; ?>controllers/catalogo/cmenu.php">
            <i class="fa-solid fa-utensils me-2"></i>Ver menú
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
.min-vh-50 { min-height: 50vh; }
.success-circle {
  width: 100px;
  height: 100px;
  border-radius: 50%;
  background: linear-gradient(135deg, #28A745, #20C997);
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto;
  color: white;
  animation: scaleIn 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}
@keyframes scaleIn {
  from { transform: scale(0); }
  to { transform: scale(1); }
}
.animate-fade-in {
  animation: fadeIn 0.5s ease;
}
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>

<script>
function copiarEnlaceFactura() {
  const input = document.getElementById('enlaceFactura');
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
</script>

<?php include __DIR__ . '/../layout/pie.php'; ?>