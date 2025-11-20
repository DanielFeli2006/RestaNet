<?php require_once __DIR__ . '/../../models/seg.php'; require_login(); ?>
<?php include __DIR__ . '/../layout/cabezote.php'; ?>
<!-- TODO: AGREGAR IMAGEN - Banner de productos del catálogo -->
<div class="d-flex justify-content-between align-items-center mb-3">
	<h2 class="h4 m-0"><i class="fa-solid fa-bowl-food me-2"></i>Productos</h2>
		<?php if (has_role(['admin'])): ?>
			<a class="btn btn-sm btn-brand" href="<?php echo BASE_PATH; ?>controllers/catalogo/cprd.php?a=create"><i class="fa-solid fa-plus me-1"></i>Nuevo</a>
	<?php endif; ?>
</div>
<div class="table-responsive shadow-soft">
	<table class="table table-striped align-middle datatable" id="tblProductos">
		<thead><tr><th>Producto</th><th>Categoría</th><th>Precio</th><th>Creado</th><?php if (has_role(['admin'])): ?><th class="text-center">Acciones</th><?php endif; ?></tr></thead>
		<tbody>
		<?php foreach (($productos ?? []) as $p): ?>
			<tr>
				<td>
					<!-- TODO: AGREGAR IMAGEN - Miniatura del producto: <?php echo htmlspecialchars($p['nombre']); ?> -->
					<div class="fw-semibold"><?php echo htmlspecialchars($p['nombre']); ?></div>
					<div class="small text-muted"><?php echo htmlspecialchars($p['descripcion']); ?></div>
				</td>
				    <td><?php echo htmlspecialchars($p['categoria'] ?? ''); ?></td>
				<td>$<?php echo number_format($p['precio'],2); ?></td>
				<td><?php echo htmlspecialchars($p['fecha_creacion']); ?></td>
				<?php if (has_role(['admin'])): ?>
				<td class="text-center" style="white-space:nowrap;">
					    <a class="btn btn-sm btn-outline-primary" href="<?php echo BASE_PATH; ?>controllers/catalogo/cprd.php?a=edit&id=<?php echo e($p['id']); ?>"><i class="fa-solid fa-pen-to-square"></i></a>
					    <a class="btn btn-sm btn-outline-danger" href="<?php echo BASE_PATH; ?>controllers/catalogo/cprd.php?a=delete&id=<?php echo e($p['id']); ?>" onclick="return confirm('¿Eliminar producto?');"><i class="fa-solid fa-trash"></i></a>
				</td>
				<?php endif; ?>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
</div>
<?php include __DIR__ . '/../layout/pie.php'; ?>