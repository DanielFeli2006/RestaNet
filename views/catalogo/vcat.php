<?php require_once __DIR__ . '/../../models/seg.php'; require_login(); ?>
<?php include __DIR__ . '/../layout/cabezote.php'; ?>

<!-- Header -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
  <div>
    <h2 class="h3 mb-0 fw-bold">
      <i class="fa-solid fa-list me-2" style="color: #C41E3A;"></i>Categorías
    </h2>
    <small class="text-muted">Gestiona las categorías del menú</small>
  </div>
  <?php if (has_role(['admin'])): ?>
  <a class="btn btn-brand" href="<?php echo BASE_PATH; ?>controllers/catalogo/ccat.php?a=create">
    <i class="fa-solid fa-plus me-2"></i>Nueva categoría
  </a>
  <?php endif; ?>
</div>

<?php if (empty($cats ?? [])): ?>
  <div class="card shadow-soft border-0">
    <div class="card-body text-center py-5">
      <div class="mb-3">
        <i class="fa-solid fa-folder-open fa-4x" style="color: #dee2e6;"></i>
      </div>
      <h5 class="text-muted">No hay categorías registradas</h5>
      <p class="text-muted small">Crea la primera categoría para organizar tus productos</p>
      <?php if (has_role(['admin'])): ?>
        <a class="btn btn-brand mt-2" href="<?php echo BASE_PATH; ?>controllers/catalogo/ccat.php?a=create">
          <i class="fa-solid fa-plus me-2"></i>Crear categoría
        </a>
      <?php endif; ?>
    </div>
  </div>
<?php else: ?>
  <div class="card shadow-soft border-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0 datatable" id="tblCategorias">
        <thead class="table-dark">
          <tr>
            <th class="ps-4">Nombre</th>
            <th>Descripción</th>
            <?php if (has_role(['admin'])): ?><th class="text-center pe-4">Acciones</th><?php endif; ?>
          </tr>
        </thead>
        <tbody>
        <?php foreach (($cats ?? []) as $c): ?>
          <tr>
            <td class="ps-4">
              <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: #F4A900;">
                  <i class="fa-solid fa-tag text-white"></i>
                </div>
                <span class="fw-semibold"><?php echo htmlspecialchars($c['nombre']); ?></span>
              </div>
            </td>
            <td>
              <span class="text-muted"><?php echo htmlspecialchars($c['descripcion'] ?? '-'); ?></span>
            </td>
            <?php if (has_role(['admin'])): ?>
            <td class="text-center pe-4" style="white-space:nowrap;">
              <div class="btn-group" role="group">
                <a class="btn btn-sm btn-outline-primary" href="<?php echo BASE_PATH; ?>controllers/catalogo/ccat.php?a=edit&id=<?php echo e($c['id']); ?>" title="Editar">
                  <i class="fa-solid fa-pen-to-square"></i>
                </a>
                <a class="btn btn-sm btn-outline-danger" href="<?php echo BASE_PATH; ?>controllers/catalogo/ccat.php?a=delete&id=<?php echo e($c['id']); ?>" 
                   onclick="return confirm('¿Eliminar esta categoría?');" title="Eliminar">
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