<?php require_once __DIR__ . '/../../models/seg.php'; require_login(); require_role(['cajero','admin']); ?>
<?php include __DIR__ . '/../layout/cabezote.php'; ?>
<h2 class="h4 mb-3"><i class="fa-solid fa-gauge-high me-2"></i>Dashboard Cajero</h2>
<!-- TODO: AGREGAR IMAGEN - Ilustración de caja registradora -->
<div class="row g-3">
  <div class="col-12 col-md-6 col-lg-4">
    <a class="text-decoration-none" href="<?php echo BASE_PATH; ?>controllers/facturacion/cfact.php">
      <div class="card shadow-soft h-100">
        <div class="card-body d-flex align-items-center">
          <i class="fa-solid fa-file-invoice-dollar fa-2x me-3 text-warning"></i>
          <div>
            <div class="fw-semibold">Facturas</div>
            <small class="text-muted">Generar y revisar</small>
          </div>
        </div>
      </div>
    </a>
  </div>
  <div class="col-12 col-md-6 col-lg-4">
    <a class="text-decoration-none" href="<?php echo BASE_PATH; ?>controllers/pedidos/cped.php">
      <div class="card shadow-soft h-100">
        <div class="card-body d-flex align-items-center">
          <i class="fa-solid fa-clipboard-list fa-2x me-3 text-info"></i>
          <div>
            <div class="fw-semibold">Pedidos</div>
            <small class="text-muted">Pendientes de pago</small>
          </div>
        </div>
      </div>
    </a>
  </div>
  <div class="col-12 col-lg-4">
    <div class="card shadow-soft h-100">
      <div class="card-body">
        <div class="fw-semibold mb-2"><i class="fa-solid fa-bolt me-1"></i>Indicadores rápidos</div>
        <!-- TODO: AGREGAR IMAGEN - Ícono representativo de métricas -->
        <ul class="small m-0 ps-3">
          <li>Facturas hoy: (placeholder)</li>
          <li>Pedidos pendientes: (placeholder)</li>
        </ul>
      </div>
    </div>
  </div>
</div>
<?php include __DIR__ . '/../layout/pie.php'; ?>