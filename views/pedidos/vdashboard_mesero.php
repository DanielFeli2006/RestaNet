<?php require_once __DIR__ . '/../../models/seg.php'; require_login(); require_role(['mesero','admin']); ?>
<?php include __DIR__ . '/../layout/cabezote.php'; ?>
<h2 class="h4 mb-3"><i class="fa-solid fa-gauge-high me-2"></i>Dashboard Mesero</h2>
<!-- TODO: AGREGAR IMAGEN - Ilustración de servicio de mesa -->
<div class="row g-3">
  <div class="col-12 col-md-6 col-lg-4">
    <a class="text-decoration-none" href="<?php echo BASE_PATH; ?>controllers/pedidos/cped.php">
      <div class="card shadow-soft h-100">
        <div class="card-body d-flex align-items-center">
          <i class="fa-solid fa-clipboard-list fa-2x me-3 text-primary"></i>
          <div>
            <div class="fw-semibold">Órdenes del día</div>
            <small class="text-muted">Crear y gestionar</small>
          </div>
        </div>
      </div>
    </a>
  </div>
  <div class="col-12 col-md-6 col-lg-4">
    <a class="text-decoration-none" href="<?php echo BASE_PATH; ?>controllers/catalogo/cprd.php">
      <div class="card shadow-soft h-100">
        <div class="card-body d-flex align-items-center">
          <i class="fa-solid fa-bowl-food fa-2x me-3 text-success"></i>
          <div>
            <div class="fw-semibold">Menú</div>
            <small class="text-muted">Consulta rápida</small>
          </div>
        </div>
      </div>
    </a>
  </div>
  <div class="col-12 col-lg-4">
    <div class="card shadow-soft h-100">
      <div class="card-body">
        <div class="fw-semibold mb-2"><i class="fa-solid fa-table me-1"></i>Gestión de mesas</div>
        <!-- TODO: AGREGAR IMAGEN - Plano de mesas del salón -->
        <p class="small text-muted mb-0">Próximamente: asignación y estado en tiempo real.</p>
      </div>
    </div>
  </div>
</div>
<?php include __DIR__ . '/../layout/pie.php'; ?>