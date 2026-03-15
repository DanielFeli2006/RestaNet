<?php require_once __DIR__ . '/../../models/seg.php'; require_login(); require_role(['cliente','admin']); ?>
<?php include __DIR__ . '/../layout/cabezote.php'; ?>

<!-- Welcome Header -->
<div class="welcome-banner mb-4 p-4 rounded-4" style="background:#C41E3A;">
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
      <div class="card h-100 shadow-soft border-0">
        <div class="card-body p-4 d-flex align-items-center gap-3">
          <div class="d-flex align-items-center justify-content-center"
               style="width:56px;height:56px;flex-shrink:0;border-radius:50%;background:rgba(196,30,58,0.08);border:2px solid #C41E3A;">
            <i class="fa-solid fa-bowl-food fa-lg" style="color:#C41E3A;"></i>
          </div>
          <div>
            <h5 class="fw-bold mb-1">Menú</h5>
            <p class="text-muted small mb-0">Explora nuestra variedad de platos deliciosos</p>
          </div>
          <i class="fa-solid fa-arrow-right ms-auto" style="color:#C41E3A; opacity:0.5;"></i>
        </div>
      </div>
    </a>
  </div>

  <!-- Carrito -->
  <div class="col-12 col-md-4">
    <a class="text-decoration-none" href="<?php echo BASE_PATH; ?>controllers/carrito/ccar.php">
      <div class="card h-100 shadow-soft border-0">
        <div class="card-body p-4 d-flex align-items-center gap-3">
          <div class="d-flex align-items-center justify-content-center"
               style="width:56px;height:56px;flex-shrink:0;border-radius:50%;background:rgba(31,31,31,0.06);border:2px solid #1F1F1F;">
            <i class="fa-solid fa-cart-shopping fa-lg" style="color:#1F1F1F;"></i>
          </div>
          <div>
            <h5 class="fw-bold mb-1">Carrito</h5>
            <p class="text-muted small mb-0">Revisa tus productos seleccionados</p>
          </div>
          <i class="fa-solid fa-arrow-right ms-auto" style="color:#1F1F1F; opacity:0.35;"></i>
        </div>
      </div>
    </a>
  </div>

  <!-- Pedidos -->
  <div class="col-12 col-md-4">
    <a class="text-decoration-none" href="<?php echo BASE_PATH; ?>controllers/pedidos/cped.php">
      <div class="card h-100 shadow-soft border-0">
        <div class="card-body p-4 d-flex align-items-center gap-3">
          <div class="d-flex align-items-center justify-content-center"
               style="width:56px;height:56px;flex-shrink:0;border-radius:50%;background:rgba(196,30,58,0.08);border:2px solid #C41E3A;">
            <i class="fa-solid fa-receipt fa-lg" style="color:#C41E3A;"></i>
          </div>
          <div>
            <h5 class="fw-bold mb-1">Mis Pedidos</h5>
            <p class="text-muted small mb-0">Revisa el estado de tus órdenes</p>
          </div>
          <i class="fa-solid fa-arrow-right ms-auto" style="color:#C41E3A; opacity:0.5;"></i>
        </div>
      </div>
    </a>
  </div>
</div>

<style>
.icon-box { display: inline-flex; align-items: center; justify-content: center; }
</style>

<?php include __DIR__ . '/../layout/pie.php'; ?>