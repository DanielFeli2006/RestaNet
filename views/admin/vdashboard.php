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
      <div class="card h-100 border-0 shadow-soft hover-lift">
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
      <div class="card h-100 border-0 shadow-soft hover-lift">
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
      <div class="card h-100 border-0 shadow-soft hover-lift">
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
      <div class="card h-100 border-0 shadow-soft hover-lift">
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
  <!-- Menú -->
  <div class="col-12 col-md-6">
    <a class="text-decoration-none" href="<?php echo BASE_PATH; ?>controllers/catalogo/cmenu.php">
      <div class="card border-0 shadow-soft hover-lift bg-gradient-warm">
        <div class="card-body p-4 d-flex align-items-center">
          <div class="icon-box bg-white bg-opacity-25 rounded-3 p-3 me-4">
            <i class="fa-solid fa-utensils fa-2x text-white"></i>
          </div>
          <div class="text-white">
            <h5 class="mb-1 fw-semibold">Ver Menú Público</h5>
            <p class="mb-0 opacity-75 small">Revisa cómo ven los clientes el menú del restaurante</p>
          </div>
          <i class="fa-solid fa-arrow-right ms-auto text-white opacity-50 fa-lg"></i>
        </div>
      </div>
    </a>
  </div>
  
  <!-- Productos -->
  <div class="col-12 col-md-6">
    <a class="text-decoration-none" href="<?php echo BASE_PATH; ?>controllers/catalogo/cprd.php">
      <div class="card border-0 shadow-soft hover-lift bg-gradient-dark">
        <div class="card-body p-4 d-flex align-items-center">
          <div class="icon-box bg-white bg-opacity-25 rounded-3 p-3 me-4">
            <i class="fa-solid fa-bowl-food fa-2x text-white"></i>
          </div>
          <div class="text-white">
            <h5 class="mb-1 fw-semibold">Gestionar Productos</h5>
            <p class="mb-0 opacity-75 small">Añadir, editar o eliminar platos del menú</p>
          </div>
          <i class="fa-solid fa-arrow-right ms-auto text-white opacity-50 fa-lg"></i>
        </div>
      </div>
    </a>
  </div>
</div>

<style>
.hover-lift { transition: all 0.3s ease; }
.hover-lift:hover { transform: translateY(-6px); box-shadow: 0 12px 40px rgba(0,0,0,0.15) !important; }
.icon-box { display: inline-flex; align-items: center; justify-content: center; width: 52px; height: 52px; }
.bg-gradient-warm { background: linear-gradient(135deg, #C41E3A 0%, #F4A900 100%); }
.bg-gradient-dark { background: linear-gradient(135deg, #1A1A1A 0%, #4A4A4A 100%); }
</style>

<?php include __DIR__ . '/../layout/pie.php'; ?>