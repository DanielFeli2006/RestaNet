<?php require_once __DIR__ . '/../../models/seg.php'; require_login(); ?>
<?php start_secure_session(); ?>
<?php include __DIR__ . '/../layout/cabezote.php'; ?>

<!-- Header -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
  <div>
    <h2 class="h3 mb-1 fw-bold">
      <i class="fa-solid fa-cart-shopping me-2" style="color: #C41E3A;"></i>Tu Carrito
    </h2>
    <p class="text-muted mb-0">Revisa y confirma tu pedido</p>
  </div>
  <a class="btn btn-outline-secondary" href="<?php echo BASE_PATH; ?>controllers/catalogo/cmenu.php">
    <i class="fa-solid fa-arrow-left me-2"></i>Volver al menú
  </a>
</div>

<?php if (!empty($_SESSION['error'])): ?>
  <div class="alert alert-danger" role="alert">
    <?php echo e($_SESSION['error']); unset($_SESSION['error']); ?>
  </div>
<?php endif; ?>

<?php
$items = $_SESSION['cart'] ?? [];
$count = 0; $subtotal = 0.0;
foreach ($items as $pid => $it) { 
  $count += $it['cantidad']; 
  $subtotal += $it['precio'] * $it['cantidad']; 
}
$iva = round($subtotal * 0.19, 2); 
$total = round($subtotal + $iva, 2);
?>

<?php if (!$items): ?>
  <!-- Empty Cart State -->
  <div class="card shadow-soft">
    <div class="card-body text-center py-5">
      <div class="mb-4">
        <i class="fa-solid fa-cart-shopping fa-4x text-muted opacity-25"></i>
      </div>
      <h4 class="text-muted mb-2">Tu carrito está vacío</h4>
      <p class="text-muted small mb-4">Explora nuestro menú y añade deliciosos platillos</p>
      <a class="btn btn-brand px-4" href="<?php echo BASE_PATH; ?>controllers/catalogo/cmenu.php">
        <i class="fa-solid fa-utensils me-2"></i>Ver menú
      </a>
    </div>
  </div>
<?php else: ?>
  <div class="row g-4">
    <!-- Cart Items -->
    <div class="col-12 col-lg-8">
      <div class="card shadow-soft">
        <div class="card-header bg-transparent py-3">
          <h5 class="mb-0 fw-semibold">
            <i class="fa-solid fa-list me-2 text-muted"></i>Productos seleccionados
            <span class="badge bg-secondary-subtle text-secondary-emphasis ms-2"><?php echo $count; ?></span>
          </h5>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th class="ps-4">Producto</th>
                  <th class="text-end">Precio</th>
                  <th class="text-center">Cantidad</th>
                  <th class="text-end">Subtotal</th>
                  <th class="text-center pe-4">Acción</th>
                </tr>
              </thead>
              <tbody>
              <?php foreach ($items as $pid => $it): ?>
                <tr class="cart-item">
                  <td class="ps-4">
                    <div class="d-flex align-items-center">
                      <div class="cart-item-icon me-3 d-none d-sm-flex">
                        <div class="rounded-3 bg-light p-2">
                          <i class="fa-solid fa-bowl-food text-muted"></i>
                        </div>
                      </div>
                      <div>
                        <div class="fw-semibold"><?php echo htmlspecialchars($it['nombre']); ?></div>
                      </div>
                    </div>
                  </td>
                  <td class="text-end">$<?php echo number_format($it['precio'], 2); ?></td>
                  <td class="text-center">
                    <span class="badge bg-secondary-subtle text-secondary-emphasis px-3 py-2">
                      <?php echo (int)$it['cantidad']; ?>
                    </span>
                  </td>
                  <td class="text-end fw-semibold">$<?php echo number_format($it['precio'] * $it['cantidad'], 2); ?></td>
                  <td class="text-center pe-4">
                    <a class="btn btn-sm btn-outline-danger" href="<?php echo BASE_PATH; ?>controllers/carrito/ccar.php?a=remove&id=<?php echo $pid; ?>" title="Eliminar">
                      <i class="fa-solid fa-trash"></i>
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Order Summary -->
    <div class="col-12 col-lg-4">
      <div class="card shadow-soft sticky-lg-top" style="top: 100px;">
        <div class="card-header bg-transparent py-3">
          <h5 class="mb-0 fw-semibold">
            <i class="fa-solid fa-receipt me-2 text-muted"></i>Resumen
          </h5>
        </div>
        <div class="card-body">
          <div class="d-flex justify-content-between mb-2">
            <span class="text-muted">Productos (<span id="cartCount"><?php echo $count; ?></span>)</span>
            <span>$<?php echo number_format($subtotal, 2); ?></span>
          </div>
          <div class="d-flex justify-content-between mb-2">
            <span class="text-muted">IVA (19%)</span>
            <span>$<?php echo number_format($iva, 2); ?></span>
          </div>
          <hr>
          <div class="d-flex justify-content-between mb-3">
            <span class="fw-bold fs-5">Total</span>
            <span class="fw-bold fs-5" style="color: #C41E3A;">$<?php echo number_format($total, 2); ?></span>
          </div>
          
          <?php if ($count < 2): ?>
          <div class="alert alert-warning small mb-3">
            <i class="fa-solid fa-circle-exclamation me-1"></i>
            Agrega al menos <strong>2 productos</strong> para confirmar tu pedido.
          </div>
          <?php endif; ?>
          
          <div class="d-grid gap-2">
            <a class="btn btn-brand btn-lg <?php echo ($count < 2 ? 'disabled' : ''); ?>" 
               id="btnCheckout" 
               href="<?php echo BASE_PATH; ?>controllers/carrito/ccar.php?a=checkout" 
               <?php echo ($count < 2 ? 'aria-disabled="true" tabindex="-1"' : ''); ?>>
              <i class="fa-solid fa-check me-2"></i>Confirmar pedido
            </a>
            <a class="btn btn-outline-secondary" href="<?php echo BASE_PATH; ?>controllers/carrito/ccar.php?a=clear">
              <i class="fa-solid fa-trash me-1"></i>Vaciar carrito
            </a>
          </div>
        </div>
        <div class="card-footer bg-transparent text-center">
          <small class="text-muted">
            <i class="fa-solid fa-qrcode me-1"></i>
            Recibirás un código QR al confirmar
          </small>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>

<style>
.cart-item {
  transition: background 0.2s ease;
}
.cart-item:hover {
  background: rgba(244, 169, 0, 0.05);
}
</style>

<?php include __DIR__ . '/../layout/pie.php'; ?>