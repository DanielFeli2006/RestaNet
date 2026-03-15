<?php require_once __DIR__ . '/../../models/seg.php'; require_login(); require_role(['cajero','admin']); ?>
<?php include __DIR__ . '/../layout/cabezote.php'; ?>

<!-- Header -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
  <div>
    <h2 class="h3 mb-1 fw-bold">
      <i class="fa-solid fa-cash-register me-2" style="color: #C41E3A;"></i>Panel de Cajero
    </h2>
    <p class="text-muted mb-0">
      Bienvenido, <strong><?php echo e($_SESSION['nombre'] ?? 'Cajero'); ?></strong>. Gestiona la facturacion del dia.
    </p>
  </div>
  <span class="badge bg-success-subtle text-success-emphasis px-3 py-2">
    <i class="fa-solid fa-circle fa-xs me-1 fa-beat"></i>Caja activa
  </span>
</div>

<!-- Quick Actions -->
<div class="row g-4">
  <!-- Facturas -->
  <div class="col-12 col-md-6 col-lg-4">
    <a class="text-decoration-none" href="<?php echo BASE_PATH; ?>controllers/facturacion/cfact.php">
      <div class="card h-100 shadow-soft border-0">
        <div class="card-body p-4 d-flex align-items-center gap-3">
          <div class="icon-box rounded-circle d-flex align-items-center justify-content-center"
               style="width:60px;height:60px;flex-shrink:0;background:rgba(196,30,58,0.08);border:2px solid #C41E3A;">
            <i class="fa-solid fa-file-invoice-dollar fa-lg" style="color:#C41E3A;"></i>
          </div>
          <div>
            <h5 class="mb-0 fw-semibold">Facturas</h5>
            <small class="text-muted">Generar y revisar comprobantes</small>
          </div>
          <i class="fa-solid fa-arrow-right ms-auto" style="color:#C41E3A; opacity:0.5;"></i>
        </div>
      </div>
    </a>
  </div>

  <!-- Pedidos -->
  <div class="col-12 col-md-6 col-lg-4">
    <a class="text-decoration-none" href="<?php echo BASE_PATH; ?>controllers/pedidos/cped.php">
      <div class="card h-100 shadow-soft border-0">
        <div class="card-body p-4 d-flex align-items-center gap-3">
          <div class="icon-box rounded-circle d-flex align-items-center justify-content-center"
               style="width:60px;height:60px;flex-shrink:0;background:rgba(31,31,31,0.06);border:2px solid #1F1F1F;">
            <i class="fa-solid fa-clipboard-list fa-lg" style="color:#1F1F1F;"></i>
          </div>
          <div>
            <h5 class="mb-0 fw-semibold">Pedidos</h5>
            <small class="text-muted">Pendientes de pago</small>
          </div>
          <i class="fa-solid fa-arrow-right ms-auto" style="color:#1F1F1F; opacity:0.35;"></i>
        </div>
      </div>
    </a>
  </div>

  <!-- Indicadores -->
  <div class="col-12 col-lg-4">
    <div class="card h-100 shadow-soft border-0">
      <div class="card-header bg-transparent border-0 py-3">
        <h5 class="mb-0 fw-semibold">
          <i class="fa-solid fa-chart-line me-2 text-muted"></i>Indicadores rapidos
        </h5>
      </div>
      <div class="card-body pt-0">
        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
          <span class="text-muted small">Facturas hoy</span>
          <span class="fw-bold" id="stat-facturas-hoy">—</span>
        </div>
        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
          <span class="text-muted small">Pedidos pendientes</span>
          <span class="fw-bold" id="stat-pedidos-pendientes">—</span>
        </div>
        <div class="d-flex justify-content-between align-items-center py-2">
          <span class="text-muted small">Total del dia</span>
          <span class="fw-bold text-success" id="stat-total-dia">—</span>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
.icon-box { display: inline-flex; align-items: center; justify-content: center; }
</style>

<?php include __DIR__ . '/../layout/pie.php'; ?>