<?php require_once __DIR__ . '/../../models/seg.php'; require_login(); require_role(['cliente','admin']); ?>
<?php include __DIR__ . '/../layout/cabezote.php'; ?>
<h2 class="h4 mb-3"><i class="fa-solid fa-gauge-high me-2"></i>Dashboard Cliente</h2>
<!-- TODO: AGREGAR IMAGEN - Banner atractivo para clientes -->
<div class="row g-3">
  <div class="col-12 col-md-4">
    <a class="text-decoration-none" href="<?php echo BASE_PATH; ?>controllers/catalogo/cprd.php">
      <div class="card shadow-soft h-100">
        <div class="card-body d-flex align-items-center">
          <i class="fa-solid fa-bowl-food fa-2x me-3 text-primary"></i>
          <div>
            <div class="fw-semibold">Menú</div>
            <small class="text-muted">Explora los platos</small>
          </div>
        </div>
      </div>
    </a>
  </div>
  <div class="col-12 col-md-4">
    <a class="text-decoration-none" href="<?php echo BASE_PATH; ?>controllers/carrito/ccar.php">
      <div class="card shadow-soft h-100">
        <div class="card-body d-flex align-items-center">
          <i class="fa-solid fa-cart-shopping fa-2x me-3 text-success"></i>
          <div>
            <div class="fw-semibold">Carrito</div>
            <small class="text-muted">Tus selecciones</small>
          </div>
        </div>
      </div>
    </a>
  </div>
  <div class="col-12 col-md-4">
    <a class="text-decoration-none" href="<?php echo BASE_PATH; ?>controllers/pedidos/cped.php">
      <div class="card shadow-soft h-100">
        <div class="card-body d-flex align-items-center">
          <i class="fa-solid fa-receipt fa-2x me-3 text-info"></i>
          <div>
            <div class="fw-semibold">Pedidos</div>
            <small class="text-muted">Historial</small>
          </div>
        </div>
      </div>
    </a>
  </div>
</div>
<?php include __DIR__ . '/../layout/pie.php'; ?>