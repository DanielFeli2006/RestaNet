<?php require_once __DIR__ . '/../../models/seg.php'; require_login(); require_role(['admin', 'mesero']); ?>
<?php include __DIR__ . '/../layout/cabezote.php'; ?>

<!-- Header -->
<div class="d-flex align-items-center mb-4">
  <a href="<?php echo BASE_PATH; ?>controllers/pedidos/cped.php" class="btn btn-outline-secondary btn-sm me-3">
    <i class="fa-solid fa-arrow-left"></i>
  </a>
  <div>
    <h2 class="h4 mb-0 fw-bold">
      <i class="fa-solid fa-clipboard-list me-2" style="color: #C41E3A;"></i>
      Nuevo Pedido
    </h2>
    <small class="text-muted">Crea un nuevo pedido para una mesa</small>
  </div>
</div>

<div class="row justify-content-center">
  <div class="col-12 col-lg-8">
    <div class="card shadow-soft border-0">
      <div class="card-body p-4 p-md-5">
        <form method="post" action="" class="needs-validation" novalidate>
          <?php echo csrf_field(); ?>
          
          <div class="mb-4">
            <label class="form-label fw-semibold" for="mesa_id">
              <i class="fa-solid fa-table-cells me-1 text-muted"></i>Mesa
            </label>
            <select class="form-select form-select-lg" id="mesa_id" name="mesa_id" required>
              <option value="">Selecciona una mesa...</option>
              <?php foreach ($mesas ?? [] as $m): ?>
                <option value="<?php echo e($m['id']); ?>" <?php echo ($m['estado'] === 'ocupada') ? 'class="text-warning"' : ''; ?>>
                  Mesa <?php echo e($m['numero']); ?>
                  <?php if ($m['estado'] === 'ocupada'): ?> (Ocupada)<?php endif; ?>
                </option>
              <?php endforeach; ?>
            </select>
            <div class="form-text">
              <i class="fa-solid fa-info-circle me-1"></i>
              El pedido se creará con estado "Pendiente"
            </div>
          </div>
          
          <hr class="my-4">
          
          <div class="d-flex gap-3 justify-content-end">
            <a class="btn btn-outline-secondary px-4" href="<?php echo BASE_PATH; ?>controllers/pedidos/cped.php">
              <i class="fa-solid fa-xmark me-1"></i>Cancelar
            </a>
            <button class="btn btn-brand px-4" type="submit">
              <i class="fa-solid fa-plus me-2"></i>Crear pedido
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../layout/pie.php'; ?>
