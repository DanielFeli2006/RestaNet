<?php require_once __DIR__ . '/../../models/seg.php'; require_login(); ?>
<?php include __DIR__ . '/../layout/cabezote.php'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h2 class="h4 m-0"><i class="fa-solid fa-utensils me-2"></i>Menú</h2>
  <a class="btn btn-sm btn-accent" href="<?php echo BASE_PATH; ?>controllers/carrito/ccar.php"><i class="fa-solid fa-cart-shopping me-1"></i>Ver carrito</a>
</div>
<!-- TODO: AGREGAR IMAGEN - Hero/banner del menú -->
<div class="row g-3">
<?php foreach (($productos ?? []) as $p): ?>
  <div class="col-12 col-sm-6 col-lg-4">
    <div class="card h-100 shadow-soft">
      <!-- TODO: AGREGAR IMAGEN - Foto del plato: <?php echo htmlspecialchars($p['nombre']); ?> -->
      <div class="card-body d-flex flex-column">
        <div class="small text-muted mb-1"><?php echo htmlspecialchars($p['categoria'] ?? ''); ?></div>
        <h3 class="h6 fw-semibold mb-2"><?php echo htmlspecialchars($p['nombre']); ?></h3>
        <p class="text-muted small flex-grow-1"><?php echo htmlspecialchars($p['descripcion']); ?></p>
        <div class="d-flex justify-content-between align-items-center mt-2">
          <div class="fw-bold">$<?php echo number_format($p['precio'],2); ?></div>
          <div class="d-flex gap-2">
            <a class="btn btn-sm btn-brand" href="<?php echo BASE_PATH; ?>controllers/carrito/ccar.php?a=add&id=<?php echo e($p['id']); ?>"><i class="fa-solid fa-plus me-1"></i>Añadir</a>
            <?php if (has_role('admin')): ?>
              <a class="btn btn-sm btn-outline-secondary" href="<?php echo BASE_PATH; ?>controllers/catalogo/cprd.php?a=edit&id=<?php echo e($p['id']); ?>">Editar</a>
              <a class="btn btn-sm btn-danger" href="<?php echo BASE_PATH; ?>controllers/catalogo/cprd.php?a=delete&id=<?php echo e($p['id']); ?>" onclick="return confirm('¿Eliminar este producto?');">Eliminar</a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php endforeach; ?>
</div>
<?php include __DIR__ . '/../layout/pie.php'; ?>