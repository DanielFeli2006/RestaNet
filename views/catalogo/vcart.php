<?php require_once __DIR__ . '/../../models/seg.php'; require_login(); ?>
<?php start_secure_session(); ?>
<?php include __DIR__ . '/../layout/cabezote.php'; ?>
<h2 class="h4 mb-3"><i class="fa-solid fa-cart-shopping me-2"></i>Carrito</h2>
<?php if (!empty($_SESSION['error'])): ?><div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div><?php endif; ?>
<div class="card shadow-soft">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table align-middle mb-0">
        <thead><tr><th>Producto</th><th class="text-end">Precio</th><th class="text-center">Cant.</th><th class="text-end">Subtotal</th><th class="text-center">Acciones</th></tr></thead>
        <tbody>
        <?php
          $items = $_SESSION['cart'] ?? [];
          $count = 0; $subtotal = 0.0;
          foreach ($items as $pid => $it) { $count += $it['cantidad']; $subtotal += $it['precio']*$it['cantidad']; }
          if (!$items) {
            echo '<tr><td colspan="5" class="text-center text-muted">No hay productos en el carrito.</td></tr>';
          }
          foreach ($items as $pid => $it): ?>
          <tr>
            <td><?php echo htmlspecialchars($it['nombre']); ?></td>
            <td class="text-end">$<?php echo number_format($it['precio'],2); ?></td>
            <td class="text-center"><?php echo (int)$it['cantidad']; ?></td>
            <td class="text-end">$<?php echo number_format($it['precio']*$it['cantidad'],2); ?></td>
            <td class="text-center"><a class="btn btn-sm btn-outline-danger" href="<?php echo BASE_PATH; ?>controllers/carrito/ccar.php?a=remove&id=<?php echo $pid; ?>"><i class="fa-solid fa-trash"></i></a></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <div class="p-3 border-top">
      <?php $iva = round($subtotal*0.19,2); $total = round($subtotal+$iva,2); ?>
      <div class="d-flex justify-content-end small text-muted">
        <div class="me-3">Items: <span id="cartCount"><?php echo $count; ?></span></div>
        <div class="me-3">Subtotal: $<?php echo number_format($subtotal,2); ?></div>
        <div class="me-3">IVA (19%): $<?php echo number_format($iva,2); ?></div>
        <div class="fw-semibold">Total: $<?php echo number_format($total,2); ?></div>
      </div>
      <div class="d-flex justify-content-end mt-2 gap-2">
        <a class="btn btn-outline-secondary btn-sm" href="<?php echo BASE_PATH; ?>controllers/carrito/ccar.php?a=clear">Vaciar</a>
        <a class="btn btn-brand btn-sm <?php echo ($count<2?'disabled':''); ?>" id="btnCheckout" href="<?php echo BASE_PATH; ?>controllers/carrito/ccar.php?a=checkout" <?php echo ($count<2?'aria-disabled="true"':''); ?>>
          <i class="fa-solid fa-check me-1"></i>Confirmar pedido (QR)
        </a>
      </div>
      <?php if ($count < 2): ?>
      <div class="small text-warning mt-1"><i class="fa-solid fa-circle-exclamation me-1"></i>Agrega al menos 2 platos para activar el checkout.</div>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php include __DIR__ . '/../layout/pie.php'; ?>