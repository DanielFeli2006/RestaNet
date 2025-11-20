<?php require_once __DIR__ . '/../../models/seg.php'; require_login(); require_role(['admin','cajero']); ?>
<?php include __DIR__ . '/../layout/cabezote.php'; ?>
<!-- TODO: AGREGAR IMAGEN - Icono representativo de facturación / caja -->
<h2 class="h4 mb-3"><i class="fa-solid fa-file-invoice-dollar me-2"></i>Facturación</h2>
<div class="table-responsive shadow-soft">
  <table class="table table-striped align-middle">
    <thead><tr><th>Pedido</th><th>Fecha</th><th>Total</th><th class="text-center">Acciones</th></tr></thead>
    <tbody>
    <?php foreach (($facturas ?? []) as $f): ?>
      <tr>
        <td><?php echo e($f['pedido_id']); ?></td>
        <td><?php echo htmlspecialchars($f['fecha_creacion']); ?></td>
        <td class="fw-semibold">$<?php echo number_format($f['total'],2); ?></td>
        <td class="text-center">
          <a class="btn btn-sm btn-outline-primary" href="<?php echo BASE_PATH; ?>controllers/facturacion/cfact.php?a=generar&id=<?php echo e($f['pedido_id']); ?>" title="Ver"><i class="fa-solid fa-eye"></i></a>
          <a class="btn btn-sm btn-outline-success" href="<?php echo BASE_PATH; ?>controllers/facturacion/cfact.php?a=pdf&id=<?php echo e($f['pedido_id']); ?>" title="PDF"><i class="fa-solid fa-file-pdf"></i></a>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php include __DIR__ . '/../layout/pie.php'; ?>