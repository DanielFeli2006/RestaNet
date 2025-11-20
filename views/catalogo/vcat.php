<?php require_once __DIR__ . '/../../models/seg.php'; require_login(); ?>
<?php include __DIR__ . '/../layout/cabezote.php'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h2 class="h4 m-0"><i class="fa-solid fa-list me-2"></i>Categorías</h2>
  <?php if (has_role(['admin'])): ?>
  <a class="btn btn-sm btn-brand" href="<?php echo BASE_PATH; ?>controllers/catalogo/ccat.php?a=create"><i class="fa-solid fa-plus me-1"></i>Nueva</a>
  <?php endif; ?>
</div>
<div class="table-responsive shadow-soft">
  <table class="table table-striped align-middle datatable" id="tblCategorias">
    <thead><tr><th>Nombre</th><th>Descripción</th><?php if (has_role(['admin'])): ?><th class="text-center">Acciones</th><?php endif; ?></tr></thead>
    <tbody>
    <?php foreach (($cats ?? []) as $c): ?>
      <tr>
        <td><?php echo htmlspecialchars($c['nombre']); ?></td>
        <td><?php echo htmlspecialchars($c['descripcion']); ?></td>
        <?php if (has_role(['admin'])): ?>
        <td class="text-center" style="white-space:nowrap;">
          <a class="btn btn-sm btn-outline-primary" href="<?php echo BASE_PATH; ?>controllers/catalogo/ccat.php?a=edit&id=<?php echo e($c['id']); ?>"><i class="fa-solid fa-pen-to-square"></i></a>
          <a class="btn btn-sm btn-outline-danger" href="<?php echo BASE_PATH; ?>controllers/catalogo/ccat.php?a=delete&id=<?php echo e($c['id']); ?>" onclick="return confirm('¿Eliminar categoría?');"><i class="fa-solid fa-trash"></i></a>
        </td>
        <?php endif; ?>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php include __DIR__ . '/../layout/pie.php'; ?>