<?php require_once __DIR__ . '/../../models/seg.php'; require_login(); ?>
<?php include __DIR__ . '/../layout/cabezote.php'; ?>
<!-- TODO: AGREGAR IMAGEN - Iconografía de lista de pedidos -->
<div class="d-flex justify-content-between align-items-center mb-3">
  <h2 class="h4 m-0"><i class="fa-solid fa-clipboard-list me-2"></i>Pedidos</h2>
  <a class="btn btn-brand btn-sm" href="<?php echo BASE_PATH; ?>controllers/pedidos/cped.php?a=create"><i class="fa-solid fa-plus me-1"></i>Nuevo</a>
</div>
<div class="table-responsive shadow-soft">
  <table class="table table-hover align-middle mb-0">
    <thead><tr><th>#</th><th>Usuario</th><th>Mesa</th><th>Estado</th><th>Fecha</th><th class="text-center">Acciones</th></tr></thead>
    <tbody>
    <?php foreach (($pedidos ?? []) as $p): ?>
      <tr>
        <td class="fw-semibold"><?php echo e($p['id']); ?></td>
        <td><?php echo htmlspecialchars($p['usuario'] ?? ''); ?></td>
        <td><?php echo htmlspecialchars($p['mesa'] ?? ''); ?></td>
        <td>
          <span class="badge <?php echo match($p['estado']) { 'pendiente' => 'text-bg-warning','completado' => 'text-bg-success','cancelado' => 'text-bg-danger', default => 'text-bg-secondary'}; ?>"><?php echo htmlspecialchars($p['estado']); ?></span>
        </td>
        <td><?php echo htmlspecialchars($p['fecha_creacion']); ?></td>
        <td class="text-center">
          <a class="btn btn-sm btn-outline-primary" href="<?php echo BASE_PATH; ?>controllers/pedidos/cped.php?a=detalle&id=<?php echo e($p['id']); ?>"><i class="fa-solid fa-eye"></i></a>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php include __DIR__ . '/../layout/pie.php'; ?>