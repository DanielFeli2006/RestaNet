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
      <div class="card h-100 shadow-soft hover-lift border-0">
        <div class="card-body p-4">
          <div class="d-flex align-items-center mb-3">
            <div class="icon-box bg-warning-subtle text-warning rounded-3 p-3 me-3">
              <i class="fa-solid fa-file-invoice-dollar fa-lg"></i>
            </div>
            <div>
              <h5 class="mb-0 fw-semibold">Facturas</h5>
              <small class="text-muted">Generar y revisar comprobantes</small>
            </div>
          </div>
          <span class="text-warning small fw-medium">
            Ir a facturas <i class="fa-solid fa-arrow-right ms-1"></i>
          </span>
        </div>
      </div>
    </a>
  </div>
  
  <!-- Pedidos -->
  <div class="col-12 col-md-6 col-lg-4">
    <a class="text-decoration-none" href="<?php echo BASE_PATH; ?>controllers/pedidos/cped.php">
      <div class="card h-100 shadow-soft hover-lift border-0">
        <div class="card-body p-4">
          <div class="d-flex align-items-center mb-3">
            <div class="icon-box bg-info-subtle text-info rounded-3 p-3 me-3">
              <i class="fa-solid fa-clipboard-list fa-lg"></i>
            </div>
            <div>
              <h5 class="mb-0 fw-semibold">Pedidos</h5>
              <small class="text-muted">Pendientes de pago</small>
            </div>
          </div>
          <span class="text-info small fw-medium">
            Ver pedidos <i class="fa-solid fa-arrow-right ms-1"></i>
          </span>
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
        <div class="d-flex flex-column gap-3">
          <div class="d-flex justify-content-between align-items-center p-3 bg-success-subtle rounded-3">
            <div>
              <small class="text-muted">Facturas hoy</small>
              <div class="fw-bold fs-5 text-success">--</div>
            </div>
            <i class="fa-solid fa-receipt fa-2x text-success opacity-25"></i>
          </div>
          <div class="d-flex justify-content-between align-items-center p-3 bg-warning-subtle rounded-3">
            <div>
              <small class="text-muted">Pedidos pendientes</small>
              <div class="fw-bold fs-5 text-warning">--</div>
            </div>
            <i class="fa-solid fa-hourglass-half fa-2x text-warning opacity-25"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
.hover-lift { transition: all 0.3s ease; }
.hover-lift:hover { transform: translateY(-6px); box-shadow: 0 12px 40px rgba(0,0,0,0.15) !important; }
.icon-box { display: inline-flex; align-items: center; justify-content: center; width: 48px; height: 48px; }
</style>

<?php include __DIR__ . '/../layout/pie.php'; ?>