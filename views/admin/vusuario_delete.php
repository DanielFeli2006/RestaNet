<?php require_once __DIR__ . '/../../models/seg.php'; require_login(); require_role(['admin']); ?>
<?php include __DIR__ . '/../layout/cabezote.php'; ?>

<!-- Header -->
<div class="d-flex align-items-center mb-4">
  <a href="<?php echo BASE_PATH; ?>controllers/usuarios/cusu.php" class="btn btn-outline-secondary btn-sm me-3">
    <i class="fa-solid fa-arrow-left"></i>
  </a>
  <div>
    <h2 class="h4 mb-0 fw-bold">
      <i class="fa-solid fa-trash me-2 text-danger"></i>
      Eliminar Usuario
    </h2>
    <small class="text-muted">Confirma la eliminación del usuario</small>
  </div>
</div>

<div class="row justify-content-center">
  <div class="col-12 col-md-8 col-lg-6">
    <div class="card shadow-soft border-0">
      <div class="card-body p-4 p-md-5 text-center">
        
        <?php if ($usuario['id'] === (int)$_SESSION['id']): ?>
          <div class="mb-4">
            <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
              <i class="fa-solid fa-ban fa-2x text-warning"></i>
            </div>
          </div>
          
          <h4 class="mb-2 text-warning">Operación no permitida</h4>
          <p class="text-muted mb-4">
            No puedes eliminar tu propio usuario.
          </p>
          
          <a href="<?php echo BASE_PATH; ?>controllers/usuarios/cusu.php" class="btn btn-outline-secondary px-4">
            <i class="fa-solid fa-arrow-left me-1"></i>Volver
          </a>
        <?php else: ?>
          <div class="mb-4">
            <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
              <i class="fa-solid fa-exclamation-triangle fa-2x text-danger"></i>
            </div>
          </div>
          
          <h4 class="mb-2">¿Estás seguro?</h4>
          <p class="text-muted mb-4">
            Estás a punto de eliminar al usuario:<br>
            <strong class="text-dark fs-5"><?php echo e($usuario['nombre']); ?></strong>
          </p>
          
          <div class="bg-light rounded-3 p-3 mb-4 text-start">
            <div class="row">
              <div class="col-12 mb-2">
                <small class="text-muted d-block">Email</small>
                <span><?php echo e($usuario['email']); ?></span>
              </div>
              <div class="col-12">
                <small class="text-muted d-block">Rol</small>
                <span class="badge bg-<?php 
                  echo match($usuario['rol']) {
                    'admin' => 'danger',
                    'mesero' => 'info',
                    'cajero' => 'warning',
                    default => 'secondary'
                  };
                ?>"><?php echo ucfirst(e($usuario['rol'])); ?></span>
              </div>
            </div>
          </div>
          
          <div class="alert alert-warning text-start small">
            <i class="fa-solid fa-warning me-1"></i>
            <strong>Advertencia:</strong> Esta acción no se puede deshacer. El usuario será eliminado permanentemente.
          </div>
          
          <form method="post" action="<?php echo BASE_PATH; ?>controllers/usuarios/cusu.php?a=delete" class="d-inline">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="id" value="<?php echo e($usuario['id']); ?>">
            
            <div class="d-flex gap-3 justify-content-center">
              <a class="btn btn-outline-secondary px-4" href="<?php echo BASE_PATH; ?>controllers/usuarios/cusu.php">
                <i class="fa-solid fa-xmark me-1"></i>Cancelar
              </a>
              <button type="submit" class="btn btn-danger px-4">
                <i class="fa-solid fa-trash me-2"></i>Sí, eliminar
              </button>
            </div>
          </form>
        <?php endif; ?>
        
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../layout/pie.php'; ?>
