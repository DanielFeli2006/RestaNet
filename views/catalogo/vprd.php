<?php require_once __DIR__ . '/../../models/seg.php'; require_login(); ?>
<?php include __DIR__ . '/../layout/cabezote.php'; ?>

<!-- Header -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
  <div>
    <h2 class="h3 mb-0 fw-bold">
      <i class="fa-solid fa-bowl-food me-2" style="color: #C41E3A;"></i>Productos
    </h2>
    <small class="text-muted">Gestiona los productos del menú</small>
  </div>
  <?php if (has_role(['admin'])): ?>
    <a class="btn btn-brand" href="<?php echo BASE_PATH; ?>controllers/catalogo/cprd.php?a=create">
      <i class="fa-solid fa-plus me-2"></i>Nuevo producto
    </a>
  <?php endif; ?>
</div>

<?php if (!empty($_SESSION['success'])): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
  <i class="fa-solid fa-check-circle me-2"></i><?php echo e($_SESSION['success']); unset($_SESSION['success']); ?>
  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if (!empty($_SESSION['error'])): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
  <i class="fa-solid fa-exclamation-circle me-2"></i><?php echo e($_SESSION['error']); unset($_SESSION['error']); ?>
  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if (empty($productos ?? [])): ?>
  <div class="card shadow-soft border-0">
    <div class="card-body text-center py-5">
      <div class="mb-3">
        <i class="fa-solid fa-utensils fa-4x" style="color: #dee2e6;"></i>
      </div>
      <h5 class="text-muted">No hay productos registrados</h5>
      <p class="text-muted small">Agrega el primer producto al menú</p>
      <?php if (has_role(['admin'])): ?>
        <a class="btn btn-brand mt-2" href="<?php echo BASE_PATH; ?>controllers/catalogo/cprd.php?a=create">
          <i class="fa-solid fa-plus me-2"></i>Crear producto
        </a>
      <?php endif; ?>
    </div>
  </div>
<?php else: ?>
  <div class="card shadow-soft border-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0 datatable" id="tblProductos">
        <thead class="table-dark">
          <tr>
            <th class="ps-4">Producto</th>
            <th>Categoría</th>
            <th>Precio</th>
            <th>Estado</th>
            <th>Creado</th>
            <?php if (has_role(['admin'])): ?><th class="text-center pe-4">Acciones</th><?php endif; ?>
          </tr>
        </thead>
        <tbody>
        <?php foreach (($productos ?? []) as $p): ?>
          <tr class="<?php echo empty($p['activo']) ? 'table-secondary opacity-75' : ''; ?>">
            <td class="ps-4">
              <div class="d-flex align-items-center gap-3">
                <?php if (!empty($p['imagen'])): ?>
                  <img src="<?php echo BASE_PATH; ?>img/productos/<?php echo e($p['imagen']); ?>" 
                       alt="<?php echo e($p['nombre']); ?>" 
                       class="rounded-3" style="width: 48px; height: 48px; object-fit: cover;">
                <?php else: ?>
                  <div class="rounded-3 d-flex align-items-center justify-content-center bg-light" style="width: 48px; height: 48px;">
                    <i class="fa-solid fa-utensils text-muted"></i>
                  </div>
                <?php endif; ?>
                <div>
                  <div class="fw-semibold"><?php echo e($p['nombre']); ?></div>
                  <div class="small text-muted text-truncate" style="max-width: 250px;">
                    <?php echo e($p['descripcion'] ?? ''); ?>
                  </div>
                </div>
              </div>
            </td>
            <td>
              <span class="badge bg-light text-dark">
                <i class="fa-solid fa-tag me-1" style="color: #F4A900;"></i>
                <?php echo e($p['categoria'] ?? '-'); ?>
              </span>
            </td>
            <td>
              <span class="fw-bold" style="color: #C41E3A;">$<?php echo number_format($p['precio'], 2); ?></span>
            </td>
            <td>
              <?php if (!empty($p['activo'])): ?>
                <span class="badge bg-success"><i class="fa-solid fa-check me-1"></i>Activo</span>
              <?php else: ?>
                <span class="badge bg-secondary"><i class="fa-solid fa-eye-slash me-1"></i>Inactivo</span>
              <?php endif; ?>
            </td>
            <td>
              <span class="small text-muted">
                <i class="fa-regular fa-calendar me-1"></i>
                <?php echo date('d M Y', strtotime($p['fecha_creacion'])); ?>
              </span>
            </td>
            <?php if (has_role(['admin'])): ?>
            <td class="text-center pe-4" style="white-space:nowrap;">
              <div class="btn-group" role="group">
                <a class="btn btn-sm btn-outline-primary" href="<?php echo BASE_PATH; ?>controllers/catalogo/cprd.php?a=edit&id=<?php echo e($p['id']); ?>" title="Editar">
                  <i class="fa-solid fa-pen-to-square"></i>
                </a>
                <a class="btn btn-sm btn-outline-danger" href="<?php echo BASE_PATH; ?>controllers/catalogo/cprd.php?a=delete&id=<?php echo e($p['id']); ?>" title="Eliminar">
                  <i class="fa-solid fa-trash"></i>
                </a>
              </div>
            </td>
            <?php endif; ?>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
<?php endif; ?>

<?php include __DIR__ . '/../layout/pie.php'; ?>