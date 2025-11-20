<?php require_once __DIR__ . '/../../models/seg.php'; require_login(); require_role(['admin']); ?>
<?php include __DIR__ . '/../layout/cabezote.php'; ?>
<?php $editing = isset($categoria) && $categoria; ?>
<div class="row justify-content-center">
  <div class="col-12 col-md-8 col-lg-6">
    <div class="card shadow-soft">
      <div class="card-body p-4">
        <h2 class="h5 mb-3"><i class="fa-solid fa-list me-2"></i><?php echo $editing ? 'Editar Categoría' : 'Nueva Categoría'; ?></h2>
        <form method="post" action="" class="row g-3">
          <?php echo csrf_field(); ?>
          <div class="col-12">
            <label class="form-label" for="nombre">Nombre</label>
            <input class="form-control" id="nombre" name="nombre" required value="<?php echo $editing ? htmlspecialchars($categoria['nombre']) : ''; ?>">
          </div>
          <div class="col-12">
            <label class="form-label" for="descripcion">Descripción</label>
            <textarea class="form-control" id="descripcion" name="descripcion" rows="3"><?php echo $editing ? htmlspecialchars($categoria['descripcion']) : ''; ?></textarea>
          </div>
          <div class="col-12 d-flex gap-2">
            <button class="btn btn-brand" type="submit"><i class="fa-solid fa-floppy-disk me-1"></i>Guardar</button>
            <a class="btn btn-outline-secondary" href="<?php echo BASE_PATH; ?>controllers/catalogo/ccat.php">Cancelar</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php include __DIR__ . '/../layout/pie.php'; ?>