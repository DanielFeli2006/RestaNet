<?php require_once __DIR__ . '/../../models/seg.php'; require_login(); ?>
<?php include __DIR__ . '/../layout/cabezote.php'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h2 class="h4 m-0"><i class="fa-solid fa-receipt me-2"></i>Detalle Pedido #<?php echo (int)($pedido['id'] ?? 0); ?></h2>
  <div>
    <?php if (has_role(['admin','cajero'])): ?>
  <a class="btn btn-sm btn-accent" href="<?php echo BASE_PATH; ?>controllers/facturacion/cfact.php?a=generar&id=<?php echo (int)($pedido['id'] ?? 0); ?>"><i class="fa-solid fa-file-invoice-dollar me-1"></i>Generar factura</a>
    <?php endif; ?>
  </div>
  </div>
<div class="table-responsive shadow-soft">
  <table class="table table-striped align-middle">
    <thead><tr><th>Producto</th><th>Cantidad</th><th>Precio</th><th>Subtotal</th></tr></thead>
    <tbody>
    <?php $total = 0; foreach (($detalle ?? []) as $d): $sub = $d['cantidad']*$d['precio']; $total += $sub; ?>
      <tr>
        <td><?php echo htmlspecialchars($d['producto']); ?></td>
        <td><?php echo (int)$d['cantidad']; ?></td>
        <td><?php echo number_format($d['precio'], 2); ?></td>
        <td><?php echo number_format($sub, 2); ?></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
    <tfoot>
      <tr><th colspan="3" class="text-end">Total</th><th><?php echo number_format($total, 2); ?></th></tr>
    </tfoot>
  </table>
</div>
<?php include __DIR__ . '/../layout/pie.php'; ?>