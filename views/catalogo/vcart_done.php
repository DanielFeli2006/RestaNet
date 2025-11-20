<?php require_once __DIR__ . '/../../models/seg.php'; require_login(); ?>
<?php include __DIR__ . '/../layout/cabezote.php'; ?>
<div class="row justify-content-center">
  <div class="col-12 col-md-7 col-lg-6">
    <div class="card shadow-soft">
      <div class="card-body text-center">
        <h2 class="h5 mb-2"><i class="fa-solid fa-circle-check text-success me-2"></i>¡Pedido confirmado!</h2>
        <p class="text-muted">Número de pedido: <span class="fw-bold">#<?php echo (int)($_GET['pedido'] ?? 0); ?></span></p>
        <?php if ($factura && !empty($factura['qr_path'])): ?>
          <img class="img-fluid" style="max-width:240px" src="<?php echo BASE_PATH . $factura['qr_path']; ?>" alt="QR del pedido">
        <?php else: ?>
          <div class="alert alert-warning small">El código QR no está disponible. Asegúrate de ejecutar "composer install" para instalar la librería de QR.</div>
        <?php endif; ?>
        <div class="mt-3">
          <a class="btn btn-brand" href="<?php echo BASE_PATH; ?>index.php"><i class="fa-solid fa-house me-1"></i>Ir al inicio</a>
        </div>
        <!-- TODO: AGREGAR IMAGEN - Ilustración de confirmación de pedido -->
      </div>
    </div>
  </div>
</div>
<?php include __DIR__ . '/../layout/pie.php'; ?>