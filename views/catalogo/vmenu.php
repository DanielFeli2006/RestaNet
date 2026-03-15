<?php require_once __DIR__ . '/../../models/seg.php'; require_login(); ?>
<?php include __DIR__ . '/../layout/cabezote.php'; ?>

<!-- Header con búsqueda -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
  <div>
    <h2 class="h3 mb-1 fw-bold">
      <i class="fa-solid fa-utensils me-2" style="color: #C41E3A;"></i>Nuestro Menú
    </h2>
    <p class="text-muted mb-0">Descubre nuestra selección de platillos preparados con pasión</p>
  </div>
  <a class="btn btn-accent px-4 py-2" href="<?php echo BASE_PATH; ?>controllers/carrito/ccar.php">
    <i class="fa-solid fa-cart-shopping me-2"></i>Ver carrito
    <?php 
    $cartCount = 0; 
    if (isset($_SESSION['cart'])) { 
      foreach ($_SESSION['cart'] as $it) { 
        $cartCount += (int)($it['cantidad'] ?? 0); 
      } 
    }
    if ($cartCount > 0): ?>
      <span class="badge bg-dark ms-1"><?php echo $cartCount; ?></span>
    <?php endif; ?>
  </a>
</div>

<!-- Productos Grid -->
<?php if (empty($productos ?? [])): ?>
  <div class="text-center py-5">
    <div class="mb-3">
      <i class="fa-solid fa-bowl-food fa-4x text-muted opacity-50"></i>
    </div>
    <h5 class="text-muted">No hay productos disponibles</h5>
    <p class="text-muted small">Vuelve pronto para ver nuestras deliciosas opciones.</p>
  </div>
<?php else: ?>
  <div class="row g-4">
  <?php foreach (($productos ?? []) as $p): ?>
    <div class="col-12 col-sm-6 col-lg-4">
      <div class="card h-100 shadow-soft product-card">
        <!-- Imagen del producto (actualizado para mostrar imagen real) -->
        <?php if (!empty($p['imagen'])): ?>
          <img src="<?php echo BASE_PATH . htmlspecialchars($p['imagen']); ?>" 
               class="card-img-top" 
               alt="<?php echo htmlspecialchars($p['nombre']); ?>"
               style="height: 180px; object-fit: cover;">
        <?php else: ?>
          <div class="card-img-placeholder d-flex align-items-center justify-content-center" style="height: 180px; background: linear-gradient(135deg, #f8f9fa, #e9ecef);">
            <i class="fa-solid fa-bowl-food fa-3x text-muted opacity-25"></i>
          </div>
        <?php endif; ?>
        <div class="card-body d-flex flex-column">
          <div class="d-flex align-items-center gap-2 mb-2">
            <span class="badge bg-secondary-subtle text-secondary-emphasis px-2 py-1 small">
              <?php echo htmlspecialchars($p['categoria'] ?? 'General'); ?>
            </span>
          </div>
          <h5 class="card-title fw-bold mb-2"><?php echo htmlspecialchars($p['nombre']); ?></h5>
          <p class="card-text text-muted small flex-grow-1"><?php echo htmlspecialchars($p['descripcion']); ?></p>
          <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
            <div class="price-tag">
              <span class="fs-4 fw-bold" style="color: #C41E3A;">$<?php echo number_format($p['precio'],2); ?></span>
            </div>
            <div class="d-flex gap-2">
              <a class="btn btn-brand" href="<?php echo BASE_PATH; ?>controllers/carrito/ccar.php?a=add&id=<?php echo e($p['id']); ?>">
                <i class="fa-solid fa-plus me-1"></i>Añadir
              </a>
              <?php if (has_role('admin')): ?>
                <a class="btn btn-sm btn-outline-secondary" href="<?php echo BASE_PATH; ?>controllers/catalogo/cprd.php?a=edit&id=<?php echo e($p['id']); ?>" title="Editar">
                  <i class="fa-solid fa-pen"></i>
                </a>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
  </div>
<?php endif; ?>

<style>
.product-card {
  transition: all 0.3s ease;
  overflow: hidden;
}
.product-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 16px 48px rgba(0,0,0,0.15) !important;
}
.product-card:hover .card-img-placeholder {
  background: linear-gradient(135deg, #fef3cd, #fff3cd) !important;
}
.product-card:hover .card-img-placeholder i {
  color: #F4A900 !important;
  opacity: 0.5 !important;
  transform: scale(1.1);
  transition: all 0.3s ease;
}
.price-tag {
  position: relative;
}
</style>

<?php include __DIR__ . '/../layout/pie.php'; ?>