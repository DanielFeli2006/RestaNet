<?php require_once __DIR__ . '/../../models/seg.php'; require_login(); require_role(['admin','cajero']); ?>
<?php include __DIR__ . '/../layout/cabezote.php'; ?>

<!-- Header -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
  <div>
    <h2 class="h3 mb-1 fw-bold">
      <i class="fa-solid fa-file-invoice-dollar me-2" style="color: #C41E3A;"></i>Facturación
    </h2>
    <p class="text-muted mb-0">Gestiona los comprobantes y genera reportes de ventas</p>
  </div>
</div>

<!-- Stats Cards -->
<div class="row g-3 mb-4">
  <div class="col-12 col-sm-6 col-lg-3">
    <div class="card border-0 shadow-soft">
      <div class="card-body d-flex align-items-center">
        <div class="icon-box bg-success-subtle text-success rounded-3 p-3 me-3">
          <i class="fa-solid fa-receipt fa-lg"></i>
        </div>
        <div>
          <div class="text-muted small">Total Facturas</div>
          <div class="fw-bold fs-4"><?php echo count($facturas ?? []); ?></div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Invoices Table -->
<div class="card shadow-soft">
  <div class="card-header bg-transparent py-3">
    <h5 class="mb-0 fw-semibold">
      <i class="fa-solid fa-list me-2 text-muted"></i>Listado de Facturas
    </h5>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead>
          <tr>
            <th class="ps-4">Pedido</th>
            <th>Fecha</th>
            <th class="text-end">Total</th>
            <th class="text-center pe-4">Acciones</th>
          </tr>
        </thead>
        <tbody>
        <?php if (empty($facturas ?? [])): ?>
          <tr>
            <td colspan="4" class="text-center py-4 text-muted">
              <i class="fa-solid fa-file-invoice fa-2x mb-2 opacity-25 d-block"></i>
              No hay facturas registradas
            </td>
          </tr>
        <?php else: ?>
          <?php foreach ($facturas as $f): ?>
            <tr>
              <td class="ps-4">
                <span class="fw-bold" style="color: #C41E3A;">
                  <i class="fa-solid fa-hashtag me-1 opacity-50"></i><?php echo e($f['pedido_id']); ?>
                </span>
              </td>
              <td>
                <small class="text-muted">
                  <i class="fa-regular fa-calendar me-1"></i>
                  <?php echo htmlspecialchars($f['fecha_creacion']); ?>
                </small>
              </td>
              <td class="text-end">
                <span class="fw-bold fs-5 text-success">$<?php echo number_format($f['total'], 2); ?></span>
              </td>
              <td class="text-center pe-4">
                <div class="btn-group" role="group">
                  <a class="btn btn-sm btn-outline-primary" href="<?php echo BASE_PATH; ?>controllers/facturacion/cfact.php?a=generar&id=<?php echo e($f['pedido_id']); ?>" title="Ver detalle">
                    <i class="fa-solid fa-eye me-1"></i>Ver
                  </a>
                  <a class="btn btn-sm btn-brand" href="<?php echo BASE_PATH; ?>controllers/facturacion/cfact.php?a=pdf&id=<?php echo e($f['pedido_id']); ?>" title="Descargar PDF">
                    <i class="fa-solid fa-file-pdf me-1"></i>PDF
                  </a>
                </div>
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
.icon-box {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 48px;
  height: 48px;
}
</style>

<?php include __DIR__ . '/../layout/pie.php'; ?>