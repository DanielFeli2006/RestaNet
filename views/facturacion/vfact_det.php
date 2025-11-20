<?php require_once __DIR__ . '/../../models/seg.php'; require_login(); require_role(['admin','cajero']); ?>
<?php include __DIR__ . '/../layout/cabezote.php'; ?>
<div class="row justify-content-center">
	<div class="col-12 col-md-9 col-lg-8">
		<div class="card shadow-soft">
			<div class="card-body">
				<h2 class="h5 mb-1"><i class="fa-solid fa-file-invoice-dollar me-2"></i>Factura - Pedido #<?php echo (int)($_GET['id'] ?? 0); ?></h2>
				<!-- TODO: AGREGAR IMAGEN - Logo del restaurante en factura -->
				<hr>
				<div class="table-responsive small">
					<table class="table align-middle mb-2">
						<thead><tr><th>Producto</th><th class="text-center">Cant.</th><th class="text-end">Precio</th><th class="text-end">Importe</th></tr></thead>
						<tbody>
							<?php $subtotal_calc=0.0; foreach (($detalle ?? []) as $d): $importe=$d['cantidad']*$d['precio']; $subtotal_calc+=$importe; ?>
							<tr>
								<td><?php echo htmlspecialchars($d['nombre']); ?></td>
								<td class="text-center"><?php echo (int)$d['cantidad']; ?></td>
								<td class="text-end">$<?php echo number_format($d['precio'],2); ?></td>
								<td class="text-end">$<?php echo number_format($importe,2); ?></td>
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
				<?php $subtotal = $factura['subtotal'] ?? ($subtotal_calc ?? 0); $impuestos = $factura['impuestos'] ?? round($subtotal*0.19,2); $total = $factura['total'] ?? round($subtotal+$impuestos,2); ?>
				<div class="d-flex justify-content-end">
					<div style="min-width:280px">
						<div class="d-flex justify-content-between"><span class="text-muted">Subtotal</span><span>$<?php echo number_format($subtotal,2); ?></span></div>
						<div class="d-flex justify-content-between"><span class="text-muted">IVA (19%)</span><span>$<?php echo number_format($impuestos,2); ?></span></div>
						<div class="d-flex justify-content-between fw-bold"><span>Total</span><span>$<?php echo number_format($total,2); ?></span></div>
					</div>
				</div>
				<div class="mt-3 d-flex align-items-center gap-3">
					<?php if (!empty($factura['qr_path'])): ?>
						<img src="<?php echo BASE_PATH . $factura['qr_path']; ?>" alt="QR Pedido" style="width:140px;height:140px"/>
					<?php else: ?>
						<div class="alert alert-warning small m-0">QR no disponible. Ejecuta composer install.</div>
					<?php endif; ?>
					<div class="small text-muted">Escanee el código QR para confirmar el pedido y ver detalles del pago.</div>
				</div>
				<div class="mt-3 d-flex gap-2">
					<button class="btn btn-brand btn-sm" disabled><i class="fa-solid fa-print me-1"></i>Imprimir</button>
					<button class="btn btn-accent btn-sm" disabled><i class="fa-solid fa-file-pdf me-1"></i>Descargar PDF</button>
				</div>
				<!-- TODO: AGREGAR IMAGEN - Sello de pagado -->
			</div>
		</div>
	</div>
</div>
<?php include __DIR__ . '/../layout/pie.php'; ?>