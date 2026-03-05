<?php require_once __DIR__ . '/../../models/seg.php'; require_login(); require_role(['admin']); ?>
<?php include __DIR__ . '/../layout/cabezote.php'; ?>

<!-- Header -->
<div class="d-flex align-items-center mb-4">
  <a href="<?php echo BASE_PATH; ?>controllers/catalogo/cprd.php" class="btn btn-outline-secondary btn-sm me-3">
    <i class="fa-solid fa-arrow-left"></i>
  </a>
  <div>
    <h2 class="h4 mb-0 fw-bold">
      <i class="fa-solid fa-trash me-2 text-danger"></i>
      Eliminar Producto
    </h2>
    <small class="text-muted">Confirma la eliminación del producto</small>
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
          Estás a punto de eliminar el producto:<br>
          <strong class="text-dark fs-5"><?php echo e($producto['nombre']); ?></strong>
        </p>
        
        <?php if (!empty($producto['imagen'])): ?>
        <div class="mb-4">
          <img src="<?php echo BASE_PATH; ?>img/productos/<?php echo e($producto['imagen']); ?>" 
               alt="Imagen del producto" class="img-thumbnail" style="max-height: 150px;">
          <p class="small text-muted mt-2">La imagen también será eliminada.</p>
        </div>
        <?php endif; ?>
        
        <div class="alert alert-warning text-start small">
          <i class="fa-solid fa-warning me-1"></i>
          <strong>Advertencia:</strong> Esta acción no se puede deshacer. El producto y su imagen serán eliminados permanentemente.
        </div>
        
        <form method="post" action="<?php echo BASE_PATH; ?>controllers/catalogo/cprd.php?a=delete" class="d-inline">
          <?php echo csrf_field(); ?>
          <input type="hidden" name="id" value="<?php echo e($producto['id']); ?>">
          
          <div class="d-flex gap-3 justify-content-center">
            <a class="btn btn-outline-secondary px-4" href="<?php echo BASE_PATH; ?>controllers/catalogo/cprd.php">
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
