<?php require_once __DIR__ . '/../../models/seg.php'; require_login(); require_role(['admin']); ?>
<?php include __DIR__ . '/../layout/cabezote.php'; ?>

<!-- Header Section -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
  <div>
    <h2 class="h3 mb-1 fw-bold">
      <i class="fa-solid fa-gauge-high me-2" style="color: #C41E3A;"></i>Panel de Administración
    </h2>
    <p class="text-muted mb-0">
      Bienvenido, <strong><?php echo e($_SESSION['nombre'] ?? 'Administrador'); ?></strong>. Gestiona tu restaurante desde aquí.
    </p>
  </div>
  <div class="d-flex gap-2">
    <span class="badge bg-success-subtle text-success-emphasis px-3 py-2">
      <i class="fa-solid fa-circle fa-xs me-1"></i>Sistema activo
    </span>
  </div>
</div>

<!-- Quick Actions Grid -->
<div class="row g-4">
  <!-- Usuarios -->
  <div class="col-12 col-sm-6 col-lg-3">
    <a class="text-decoration-none" href="<?php echo BASE_PATH; ?>controllers/usuarios/cusu.php">
      <div class="card h-100 border-0 shadow-soft">
        <div class="card-body p-4">
          <div class="d-flex align-items-center mb-3">
            <div class="icon-box bg-danger-subtle text-danger rounded-3 p-3 me-3">
              <i class="fa-solid fa-users-gear fa-lg"></i>
            </div>
            <div>
              <h5 class="mb-0 fw-semibold">Usuarios</h5>
              <small class="text-muted">Gestionar cuentas</small>
            </div>
          </div>
          <p class="text-muted small mb-0">Altas, bajas y permisos de usuarios del sistema.</p>
        </div>
        <div class="card-footer bg-transparent border-0 pt-0 pb-3 px-4">
          <span class="text-danger small fw-medium">
            Administrar <i class="fa-solid fa-arrow-right ms-1"></i>
          </span>
        </div>
      </div>
    </a>
  </div>

  <!-- Categorías -->
  <div class="col-12 col-sm-6 col-lg-3">
    <a class="text-decoration-none" href="<?php echo BASE_PATH; ?>controllers/catalogo/ccat.php">
      <div class="card h-100 border-0 shadow-soft">
        <div class="card-body p-4">
          <div class="d-flex align-items-center mb-3">
            <div class="icon-box bg-warning-subtle text-warning rounded-3 p-3 me-3">
              <i class="fa-solid fa-list fa-lg"></i>
            </div>
            <div>
              <h5 class="mb-0 fw-semibold">Categorías</h5>
              <small class="text-muted">Organizar menú</small>
            </div>
          </div>
          <p class="text-muted small mb-0">Clasificación y estructura de productos.</p>
        </div>
        <div class="card-footer bg-transparent border-0 pt-0 pb-3 px-4">
          <span class="text-warning small fw-medium">
            Administrar <i class="fa-solid fa-arrow-right ms-1"></i>
          </span>
        </div>
      </div>
    </a>
  </div>

  <!-- Pedidos -->
  <div class="col-12 col-sm-6 col-lg-3">
    <a class="text-decoration-none" href="<?php echo BASE_PATH; ?>controllers/pedidos/cped.php">
      <div class="card h-100 border-0 shadow-soft">
        <div class="card-body p-4">
          <div class="d-flex align-items-center mb-3">
            <div class="icon-box bg-info-subtle text-info rounded-3 p-3 me-3">
              <i class="fa-solid fa-clipboard-list fa-lg"></i>
            </div>
            <div>
              <h5 class="mb-0 fw-semibold">Pedidos</h5>
              <small class="text-muted">Ver órdenes</small>
            </div>
          </div>
          <p class="text-muted small mb-0">Seguimiento y gestión de pedidos activos.</p>
        </div>
        <div class="card-footer bg-transparent border-0 pt-0 pb-3 px-4">
          <span class="text-info small fw-medium">
            Ver todos <i class="fa-solid fa-arrow-right ms-1"></i>
          </span>
        </div>
      </div>
    </a>
  </div>

  <!-- Facturación -->
  <div class="col-12 col-sm-6 col-lg-3">
    <a class="text-decoration-none" href="<?php echo BASE_PATH; ?>controllers/facturacion/cfact.php">
      <div class="card h-100 border-0 shadow-soft">
        <div class="card-body p-4">
          <div class="d-flex align-items-center mb-3">
            <div class="icon-box bg-success-subtle text-success rounded-3 p-3 me-3">
              <i class="fa-solid fa-file-invoice-dollar fa-lg"></i>
            </div>
            <div>
              <h5 class="mb-0 fw-semibold">Facturación</h5>
              <small class="text-muted">Ingresos y reportes</small>
            </div>
          </div>
          <p class="text-muted small mb-0">Comprobantes, totales y estadísticas.</p>
        </div>
        <div class="card-footer bg-transparent border-0 pt-0 pb-3 px-4">
          <span class="text-success small fw-medium">
            Ver facturas <i class="fa-solid fa-arrow-right ms-1"></i>
          </span>
        </div>
      </div>
    </a>
  </div>
</div>

<!-- Secondary Actions -->
<div class="row g-4 mt-2">
  <!-- Ver Menú Público -->
  <div class="col-12 col-md-6">
    <a class="text-decoration-none" href="<?php echo BASE_PATH; ?>controllers/catalogo/cmenu.php">
      <div class="card card-outline-red shadow-soft">
        <div class="card-body p-4 d-flex align-items-center">
          <div class="icon-box rounded-3 p-3 me-4" style="background: rgba(196,30,58,0.08); width:56px; height:56px;">
            <i class="fa-solid fa-eye fa-xl" style="color:#C41E3A;"></i>
          </div>
          <div>
            <h5 class="mb-1 fw-semibold" style="color:#C41E3A;">Ver Menú Público</h5>
            <p class="mb-0 text-muted small">Revisa cómo ven los clientes el menú del restaurante</p>
          </div>
          <i class="fa-solid fa-arrow-right ms-auto fa-lg" style="color:#C41E3A; opacity:0.5;"></i>
        </div>
      </div>
    </a>
  </div>

  <!-- Gestionar Productos -->
  <div class="col-12 col-md-6">
    <a class="text-decoration-none" href="<?php echo BASE_PATH; ?>controllers/catalogo/cprd.php">
      <div class="card card-outline-dark shadow-soft">
        <div class="card-body p-4 d-flex align-items-center">
          <div class="icon-box rounded-3 p-3 me-4" style="background: rgba(31,31,31,0.06); width:56px; height:56px;">
            <i class="fa-solid fa-bowl-food fa-xl" style="color:#1F1F1F;"></i>
          </div>
          <div>
            <h5 class="mb-1 fw-semibold" style="color:#1F1F1F;">Gestionar Productos</h5>
            <p class="mb-0 text-muted small">Añadir, editar o eliminar platos del menú</p>
          </div>
          <i class="fa-solid fa-arrow-right ms-auto fa-lg" style="color:#1F1F1F; opacity:0.35;"></i>
        </div>
      </div>
    </a>
  </div>
</div>

<style>
.icon-box { display: inline-flex; align-items: center; justify-content: center; }
.card-outline-red  { background: #fff; border: 2px solid #C41E3A !important; border-radius: 0.75rem; }
.card-outline-dark { background: #fff; border: 2px solid #1F1F1F !important; border-radius: 0.75rem; }
</style>

<?php include __DIR__ . '/../layout/pie.php'; ?>