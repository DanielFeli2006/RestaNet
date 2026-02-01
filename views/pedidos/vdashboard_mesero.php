<?php require_once __DIR__ . '/../../models/seg.php'; require_login(); require_role(['mesero','admin']); ?>
<?php include __DIR__ . '/../layout/cabezote.php'; ?>

<!-- Header -->
<div class="mb-4">
  <h2 class="h3 mb-0 fw-bold">
    <i class="fa-solid fa-gauge-high me-2" style="color: #C41E3A;"></i>Dashboard Mesero
  </h2>
  <small class="text-muted">Gestiona pedidos y mesas del restaurante</small>
</div>

<div class="row g-4">
  <!-- Ordenes del dia -->
  <div class="col-12 col-md-6 col-lg-4">
    <a class="text-decoration-none" href="<?php echo BASE_PATH; ?>controllers/pedidos/cped.php">
      <div class="card shadow-soft border-0 h-100 hover-lift">
        <div class="card-body d-flex align-items-center p-4">
          <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px; background: linear-gradient(135deg, #C41E3A, #8B0000);">
            <i class="fa-solid fa-clipboard-list fa-xl text-white"></i>
          </div>
          <div>
            <div class="fw-bold fs-5">Ordenes del dia</div>
            <small class="text-muted">Crear y gestionar pedidos</small>
          </div>
          <i class="fa-solid fa-chevron-right ms-auto text-muted"></i>
        </div>
      </div>
    </a>
  </div>
  
  <!-- Menu -->
  <div class="col-12 col-md-6 col-lg-4">
    <a class="text-decoration-none" href="<?php echo BASE_PATH; ?>controllers/catalogo/cprd.php">
      <div class="card shadow-soft border-0 h-100 hover-lift">
        <div class="card-body d-flex align-items-center p-4">
          <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px; background: linear-gradient(135deg, #F4A900, #CC8800);">
            <i class="fa-solid fa-bowl-food fa-xl text-white"></i>
          </div>
          <div>
            <div class="fw-bold fs-5">Menu</div>
            <small class="text-muted">Consulta rapida de productos</small>
          </div>
          <i class="fa-solid fa-chevron-right ms-auto text-muted"></i>
        </div>
      </div>
    </a>
  </div>
  
  <!-- Gestion de mesas -->
  <div class="col-12 col-lg-4">
    <div class="card shadow-soft border-0 h-100">
      <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div class="fw-bold">
            <i class="fa-solid fa-table me-2" style="color: #C41E3A;"></i>Gestion de mesas
          </div>
          <span class="badge" style="background: rgba(196, 30, 58, 0.1); color: #C41E3A;">
            <i class="fa-solid fa-sync-alt me-1"></i>15s
          </span>
        </div>
        <div class="mesa-grid" data-mesas-grid>
          <div class="text-center py-4">
            <div class="spinner-border spinner-border-sm text-muted mb-2" role="status"></div>
            <p class="text-muted small mb-0">Cargando estado de mesas...</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
.hover-lift {
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.hover-lift:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
}
</style>

<?php include __DIR__ . '/../layout/pie.php'; ?>
