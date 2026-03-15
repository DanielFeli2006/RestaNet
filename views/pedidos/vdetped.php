<?php require_once __DIR__ . '/../../models/seg.php'; require_login(); ?>
<?php include __DIR__ . '/../layout/cabezote.php'; ?>

<!-- Header -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
  <div class="d-flex align-items-center">
    <a href="<?php echo BASE_PATH; ?>controllers/pedidos/cped.php" class="btn btn-outline-secondary btn-sm me-3">
      <i class="fa-solid fa-arrow-left"></i>
    </a>
    <div>
      <h2 class="h3 mb-0 fw-bold">
        <i class="fa-solid fa-receipt me-2" style="color: #C41E3A;"></i>
        Pedido <span style="color: #F4A900;">#<?php echo (int)($pedido['id'] ?? 0); ?></span>
      </h2>
      <small class="text-muted">Detalle de productos solicitados</small>
    </div>
  </div>
  <?php if (has_role(['admin','cajero'])): ?>
    <a class="btn btn-accent" href="<?php echo BASE_PATH; ?>controllers/facturacion/cfact.php?a=generar&id=<?php echo (int)($pedido['id'] ?? 0); ?>">
      <i class="fa-solid fa-file-invoice-dollar me-2"></i>Generar factura
    </a>
  <?php endif; ?>
</div>

<div class="row">
  <div class="col-12 col-lg-8">
    <div class="card shadow-soft border-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-dark">
            <tr>
              <th class="ps-4">Producto</th>
              <th class="text-center">Cantidad</th>
              <th class="text-end">Precio</th>
              <th class="text-end pe-4">Subtotal</th>
            </tr>
          </thead>
          <tbody>
          <?php $total = 0; foreach (($detalle ?? []) as $d): $sub = $d['cantidad']*$d['precio']; $total += $sub; ?>
            <tr>
              <td class="ps-4">
                <div class="d-flex align-items-center gap-3">
                  <div class="rounded-3 d-flex align-items-center justify-content-center bg-light" style="width: 40px; height: 40px;">
                    <i class="fa-solid fa-utensils text-muted"></i>
                  </div>
                  <span class="fw-medium"><?php echo htmlspecialchars($d['producto']); ?></span>
                </div>
              </td>
              <td class="text-center">
                <span class="badge" style="background: #C41E3A; font-size: 1rem;"><?php echo (int)$d['cantidad']; ?></span>
              </td>
              <td class="text-end text-muted">$<?php echo number_format($d['precio'], 2); ?></td>
              <td class="text-end pe-4 fw-semibold">$<?php echo number_format($sub, 2); ?></td>
            </tr>
          <?php endforeach; ?>
          </tbody>
          <tfoot class="table-light">
            <tr>
              <th colspan="3" class="text-end fs-5">Total del pedido</th>
              <th class="text-end pe-4 fs-4" style="color: #C41E3A;">$<?php echo number_format($total, 2); ?></th>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>
  
  <!-- Info sidebar -->
  <div class="col-12 col-lg-4 mt-4 mt-lg-0">
    <div class="card shadow-soft border-0">
      <div class="card-body">
        <h6 class="fw-bold mb-3">
          <i class="fa-solid fa-circle-info me-2" style="color: #F4A900;"></i>Información del pedido
        </h6>
        <ul class="list-unstyled mb-0">
          <li class="d-flex justify-content-between py-2 border-bottom">
            <span class="text-muted">Número</span>
            <span class="fw-semibold">#<?php echo (int)($pedido['id'] ?? 0); ?></span>
          </li>
          <li class="d-flex justify-content-between py-2 border-bottom">
            <span class="text-muted">Estado</span>
            <span class="fw-semibold"><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $pedido['estado'] ?? 'pendiente'))); ?></span>
          </li>
          <?php if (!empty($pedido['mesa_numero'])): ?>
          <li class="d-flex justify-content-between py-2 border-bottom">
            <span class="text-muted">Mesa</span>
            <span class="fw-semibold">#<?php echo (int)$pedido['mesa_numero']; ?></span>
          </li>
          <?php endif; ?>
          <li class="d-flex justify-content-between py-2 border-bottom">
            <span class="text-muted">Productos</span>
            <span class="fw-semibold"><?php echo count($detalle ?? []); ?> items</span>
          </li>
          <li class="d-flex justify-content-between py-2">
            <span class="text-muted">Total</span>
            <span class="fw-bold" style="color: #C41E3A;">$<?php echo number_format($total, 2); ?></span>
          </li>
        </ul>

        <?php if (has_role(['admin', 'mesero', 'cajero'])): ?>
        <hr>
        <form method="post" action="<?php echo BASE_PATH; ?>controllers/pedidos/cped.php?a=actualizar_estado" class="d-flex gap-2">
          <?php echo csrf_field(); ?>
          <input type="hidden" name="pedido_id" value="<?php echo (int)$pedido['id']; ?>">
          <input type="hidden" name="return_to" value="detalle">
          <select name="estado" class="form-select form-select-sm">
            <?php foreach (['pendiente','en_preparacion','listo','entregado','completado','cancelado'] as $estadoOpt): ?>
              <option value="<?php echo $estadoOpt; ?>" <?php echo ($pedido['estado'] ?? '') === $estadoOpt ? 'selected' : ''; ?>>
                <?php echo ucfirst(str_replace('_', ' ', $estadoOpt)); ?>
              </option>
            <?php endforeach; ?>
          </select>
          <button class="btn btn-sm btn-outline-secondary" type="submit">Actualizar</button>
        </form>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../layout/pie.php'; ?>