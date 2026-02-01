<?php require_once __DIR__ . '/../../models/seg.php'; require_login(); require_role(['cliente','admin']); ?>
<?php include __DIR__ . '/../layout/cabezote.php'; ?>

<!-- Welcome Header -->
<div class="welcome-banner mb-4 p-4 rounded-4" style="background: linear-gradient(135deg, #C41E3A 0%, #F4A900 100%);">
  <div class="row align-items-center">
    <div class="col-md-8">
      <h2 class="h3 mb-2 text-white fw-bold">
        <i class="fa-solid fa-hand-wave me-2"></i>¡Bienvenido, <?php echo e($_SESSION['nombre'] ?? 'Cliente'); ?>!
      </h2>
      <p class="text-white opacity-75 mb-0">Explora nuestro menú y realiza tu pedido de manera fácil y rápida.</p>
    </div>
    <div class="col-md-4 text-end d-none d-md-block">
      <i class="fa-solid fa-utensils fa-4x text-white opacity-25"></i>
    </div>
  </div>
</div>

<!-- Quick Actions -->
<h5 class="fw-semibold mb-3 text-muted">
  <i class="fa-solid fa-bolt me-2"></i>Acceso rápido
</h5>
<div class="row g-4">
  <!-- Menú -->
  <div class="col-12 col-md-4">
    <a class="text-decoration-none" href="<?php echo BASE_PATH; ?>controllers/catalogo/cmenu.php">
      <div class="card h-100 shadow-soft hover-lift border-0">
        <div class="card-body p-4 text-center">
          <div class="icon-circle bg-danger-subtle text-danger mb-3 mx-auto">
            <i class="fa-solid fa-bowl-food fa-2x"></i>
          </div>
          <h5 class="fw-bold mb-2">Menú</h5>
          <p class="text-muted small mb-3">Explora nuestra variedad de platos deliciosos</p>
          <span class="btn btn-brand btn-sm">
            Ver menú <i class="fa-solid fa-arrow-right ms-1"></i>
          </span>
        </div>
      </div>
    </a>
  </div>
  
  <!-- Carrito -->
  <div class="col-12 col-md-4">
    <a class="text-decoration-none" href="<?php echo BASE_PATH; ?>controllers/carrito/ccar.php">
      <div class="card h-100 shadow-soft hover-lift border-0">
        <div class="card-body p-4 text-center">
          <div class="icon-circle bg-warning-subtle text-warning mb-3 mx-auto">
            <i class="fa-solid fa-cart-shopping fa-2x"></i>
          </div>
          <h5 class="fw-bold mb-2">Carrito</h5>
          <p class="text-muted small mb-3">Revisa tus productos seleccionados</p>
          <span class="btn btn-accent btn-sm">
            Ver carrito <i class="fa-solid fa-arrow-right ms-1"></i>
          </span>
        </div>
      </div>
    </a>
  </div>
  
  <!-- Pedidos -->
  <div class="col-12 col-md-4">
    <a class="text-decoration-none" href="<?php echo BASE_PATH; ?>controllers/pedidos/cped.php">
      <div class="card h-100 shadow-soft hover-lift border-0">
        <div class="card-body p-4 text-center">
          <div class="icon-circle bg-info-subtle text-info mb-3 mx-auto">
            <i class="fa-solid fa-receipt fa-2x"></i>
          </div>
          <h5 class="fw-bold mb-2">Mis Pedidos</h5>
          <p class="text-muted small mb-3">Consulta el historial de tus órdenes</p>
          <span class="btn btn-outline-primary btn-sm">
            Ver historial <i class="fa-solid fa-arrow-right ms-1"></i>
          </span>
        </div>
      </div>
    </a>
  </div>
</div>

<style>
.hover-lift {
  transition: all 0.3s ease;
}
.hover-lift:hover {
  transform: translateY(-8px);
  box-shadow: 0 16px 48px rgba(0,0,0,0.15) !important;
}
.icon-circle {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
}
.hover-lift:hover .icon-circle {
  transform: scale(1.1);
}
</style>

<?php include __DIR__ . '/../layout/pie.php'; ?>