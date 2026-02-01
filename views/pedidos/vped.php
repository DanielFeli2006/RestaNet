<?php require_once __DIR__ . '/../../models/seg.php'; require_login(); ?>
<?php include __DIR__ . '/../layout/cabezote.php'; ?>

<!-- Header -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
  <div>
    <h2 class="h3 mb-1 fw-bold">
      <i class="fa-solid fa-clipboard-list me-2" style="color: #C41E3A;"></i>Gestión de Pedidos
    </h2>
    <p class="text-muted mb-0">Visualiza y administra los pedidos del restaurante</p>
  </div>
  <?php if (has_role(['admin', 'mesero'])): ?>
  <a class="btn btn-brand" href="<?php echo BASE_PATH; ?>controllers/pedidos/cped.php?a=create">
    <i class="fa-solid fa-plus me-2"></i>Nuevo pedido
  </a>
  <?php endif; ?>
</div>

<!-- Orders Table -->
<div class="card shadow-soft">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead>
          <tr>
            <th class="ps-4"># Pedido</th>
            <th>Usuario</th>
            <th>Mesa</th>
            <th>Estado</th>
            <th>Fecha</th>
            <th class="text-center pe-4">Acciones</th>
          </tr>
        </thead>
        <tbody>
        <?php if (empty($pedidos ?? [])): ?>
          <tr>
            <td colspan="6" class="text-center py-4 text-muted">
              <i class="fa-solid fa-clipboard-list fa-2x mb-2 opacity-25 d-block"></i>
              No hay pedidos registrados
            </td>
          </tr>
        <?php else: ?>
          <?php foreach ($pedidos as $p): ?>
            <tr>
              <td class="ps-4">
                <span class="fw-bold text-brand">#<?php echo e($p['id']); ?></span>
              </td>
              <td><?php echo htmlspecialchars($p['usuario'] ?? 'N/A'); ?></td>
              <td>
                <?php if (!empty($p['mesa'])): ?>
                  <span class="badge bg-secondary-subtle text-secondary-emphasis">
                    <i class="fa-solid fa-table-cells me-1"></i><?php echo htmlspecialchars($p['mesa']); ?>
                  </span>
                <?php else: ?>
                  <span class="text-muted small">-</span>
                <?php endif; ?>
              </td>
              <td>
                <?php 
                $statusConfig = [
                  'pendiente' => ['class' => 'bg-warning-subtle text-warning-emphasis', 'icon' => 'fa-clock'],
                  'en_preparacion' => ['class' => 'bg-info-subtle text-info-emphasis', 'icon' => 'fa-fire'],
                  'completado' => ['class' => 'bg-success-subtle text-success-emphasis', 'icon' => 'fa-check'],
                  'cancelado' => ['class' => 'bg-danger-subtle text-danger-emphasis', 'icon' => 'fa-xmark'],
                ];
                $config = $statusConfig[$p['estado']] ?? ['class' => 'bg-secondary-subtle text-secondary-emphasis', 'icon' => 'fa-question'];
                ?>
                <span class="badge <?php echo $config['class']; ?> px-3 py-2">
                  <i class="fa-solid <?php echo $config['icon']; ?> me-1"></i>
                  <?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $p['estado']))); ?>
                </span>
              </td>
              <td>
                <small class="text-muted">
                  <i class="fa-regular fa-calendar me-1"></i>
                  <?php echo htmlspecialchars($p['fecha_creacion']); ?>
                </small>
              </td>
              <td class="text-center pe-4">
                <a class="btn btn-sm btn-outline-primary" href="<?php echo BASE_PATH; ?>controllers/pedidos/cped.php?a=detalle&id=<?php echo e($p['id']); ?>" title="Ver detalles">
                  <i class="fa-solid fa-eye me-1"></i>Ver
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<style>
.text-brand { color: #C41E3A; }
</style>

<?php include __DIR__ . '/../layout/pie.php'; ?>