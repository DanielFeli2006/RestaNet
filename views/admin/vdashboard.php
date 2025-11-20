<?php require_once __DIR__ . '/../../models/seg.php'; require_login(); require_role(['admin']); ?>
<?php include __DIR__ . '/../layout/cabezote.php'; ?>
<h2 class="h4 mb-3"><i class="fa-solid fa-gauge-high me-2"></i>Panel Administrador</h2>
<div class="row g-3">
  <div class="col-12 col-md-6 col-lg-3">
  <a class="text-decoration-none" href="<?php echo BASE_PATH; ?>controllers/usuarios/cusu.php">
      <div class="card shadow-soft h-100">
        <div class="card-body d-flex align-items-center">
          <i class="fa-solid fa-users-gear fa-2x me-3 text-primary"></i>
          <div>
            <div class="fw-semibold">Usuarios</div>
            <small class="text-muted">Altas, bajas y cambios</small>
          </div>
        </div>
      </div>
    </a>
  </div>
  <div class="col-12 col-md-6 col-lg-3">
  <a class="text-decoration-none" href="<?php echo BASE_PATH; ?>controllers/catalogo/ccat.php">
      <div class="card shadow-soft h-100">
        <div class="card-body d-flex align-items-center">
          <i class="fa-solid fa-list fa-2x me-3 text-success"></i>
          <div>
            <div class="fw-semibold">Categorías</div>
            <small class="text-muted">Menú y productos</small>
          </div>
        </div>
      </div>
    </a>
  </div>
  <div class="col-12 col-md-6 col-lg-3">
  <a class="text-decoration-none" href="<?php echo BASE_PATH; ?>controllers/pedidos/cped.php">
      <div class="card shadow-soft h-100">
        <div class="card-body d-flex align-items-center">
          <i class="fa-solid fa-clipboard-list fa-2x me-3 text-info"></i>
          <div>
            <div class="fw-semibold">Pedidos</div>
            <small class="text-muted">Crear y gestionar</small>
          </div>
        </div>
      </div>
    </a>
  </div>
  <div class="col-12 col-md-6 col-lg-3">
  <a class="text-decoration-none" href="<?php echo BASE_PATH; ?>controllers/facturacion/cfact.php">
      <div class="card shadow-soft h-100">
        <div class="card-body d-flex align-items-center">
          <i class="fa-solid fa-file-invoice-dollar fa-2x me-3 text-warning"></i>
          <div>
            <div class="fw-semibold">Facturación</div>
            <small class="text-muted">Totales y comprobantes</small>
          </div>
        </div>
      </div>
    </a>
  </div>
</div>
<?php include __DIR__ . '/../layout/pie.php'; ?>