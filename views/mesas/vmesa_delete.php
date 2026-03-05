<?php require_once __DIR__ . '/../../models/seg.php'; require_login(); require_role(['admin']); ?>
<?php include __DIR__ . '/../layout/cabezote.php'; ?>

<!-- Header -->
<div class="d-flex align-items-center mb-4">
  <a href="<?php echo BASE_PATH; ?>controllers/mesas/cmesa.php" class="btn btn-outline-secondary btn-sm me-3">
    <i class="fa-solid fa-arrow-left"></i>
  </a>
  <div>
    <h2 class="h4 mb-0 fw-bold">
      <i class="fa-solid fa-trash me-2 text-danger"></i>
      Eliminar Mesa
    </h2>
    <small class="text-muted">Confirma la eliminación de la mesa</small>
  </div>
</div>

<div class="row justify-content-center">
  <div class="col-12 col-md-8 col-lg-6">
    <div class="card shadow-soft border-0">
      <div class="card-body p-4 p-md-5 text-center">
        
        <div class="mb-4">
          <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
            <i class="fa-solid fa-exclamation-triangle fa-2x text-danger"></i>
          </div>
        </div>
        
        <h4 class="mb-2">¿Estás seguro?</h4>
        <p class="text-muted mb-4">
          Estás a punto de eliminar la mesa:<br>
          <strong class="text-dark fs-3">Mesa #<?php echo e($mesa['numero']); ?></strong>
        </p>
        
        <div class="bg-light rounded-3 p-3 mb-4 text-start">
          <div class="row">
            <div class="col-6">
              <small class="text-muted d-block">Capacidad</small>
              <span><?php echo (int)$mesa['capacidad']; ?> personas</span>
            </div>
            <div class="col-6">
              <small class="text-muted d-block">Estado</small>
              <span><?php echo ucfirst(e($mesa['estado'])); ?></span>
            </div>
            <?php if (!empty($mesa['ubicacion'])): ?>
            <div class="col-12 mt-2">
              <small class="text-muted d-block">Ubicación</small>
              <span><?php echo e($mesa['ubicacion']); ?></span>
            </div>
            <?php endif; ?>
          </div>
        </div>
        
        <div class="alert alert-warning text-start small">
          <i class="fa-solid fa-warning me-1"></i>
          <strong>Advertencia:</strong> Esta acción no se puede deshacer. La mesa será eliminada permanentemente.
        </div>
        
        <form method="post" action="<?php echo BASE_PATH; ?>controllers/mesas/cmesa.php?a=delete" class="d-inline">
          <?php echo csrf_field(); ?>
          <input type="hidden" name="id" value="<?php echo e($mesa['id']); ?>">
          
          <div class="d-flex gap-3 justify-content-center">
            <a class="btn btn-outline-secondary px-4" href="<?php echo BASE_PATH; ?>controllers/mesas/cmesa.php">
              <i class="fa-solid fa-xmark me-1"></i>Cancelar
            </a>
            <button type="submit" class="btn btn-danger px-4">
              <i class="fa-solid fa-trash me-2"></i>Sí, eliminar
            </button>
          </div>
        </form>
        
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../layout/pie.php'; ?>
