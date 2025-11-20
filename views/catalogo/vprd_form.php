<?php require_once __DIR__ . '/../../models/seg.php'; require_login(); require_role(['admin']); ?>
<?php include __DIR__ . '/../layout/cabezote.php'; ?>
<?php $editing = isset($producto) && $producto; ?>
<div class="row justify-content-center">
  <div class="col-12 col-md-10 col-lg-8">
    <div class="card shadow-soft">
      <div class="card-body p-4">
        <h2 class="h5 mb-3"><i class="fa-solid fa-bowl-food me-2"></i><?php echo $editing ? 'Editar Producto' : 'Nuevo Producto'; ?></h2>
        <form method="post" action="" class="row g-3">
          <?php echo csrf_field(); ?>
          <div class="col-md-6">
            <label class="form-label" for="nombre">Nombre</label>
            <input class="form-control" id="nombre" name="nombre" required value="<?php echo $editing ? htmlspecialchars($producto['nombre']) : ''; ?>">
          </div>
          <div class="col-md-6">
            <label class="form-label" for="precio">Precio</label>
            <div class="input-group">
              <span class="input-group-text">$</span>
              <input class="form-control" id="precio" name="precio" type="number" step="0.01" min="0" required value="<?php echo $editing ? htmlspecialchars($producto['precio']) : ''; ?>">
            </div>
          </div>
          <div class="col-12">
            <label class="form-label" for="descripcion">Descripción</label>
            <textarea class="form-control" id="descripcion" name="descripcion" rows="3"><?php echo $editing ? htmlspecialchars($producto['descripcion']) : ''; ?></textarea>
          </div>
          <div class="col-md-6">
            <label class="form-label" for="categoria_id">Categoría</label>
            <select class="form-select" id="categoria_id" name="categoria_id" required>
              <option value="" disabled selected>Seleccione...</option>
              <?php foreach (($cats ?? []) as $c): ?>
                <option value="<?php echo e($c['id']); ?>" <?php echo ($editing && $producto['categoria_id']==$c['id'])?'selected':''; ?>><?php echo htmlspecialchars($c['nombre']); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-12 d-flex gap-2">
            <button class="btn btn-brand" type="submit"><i class="fa-solid fa-floppy-disk me-1"></i>Guardar</button>
            <a class="btn btn-outline-secondary" href="<?php echo BASE_PATH; ?>controllers/catalogo/cprd.php">Cancelar</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php include __DIR__ . '/../layout/pie.php'; ?>