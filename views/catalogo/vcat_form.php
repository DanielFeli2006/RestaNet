<?php require_once __DIR__ . '/../../models/seg.php'; require_login(); require_role(['admin']); ?>
<?php include __DIR__ . '/../layout/cabezote.php'; ?>
<?php $editing = isset($categoria) && $categoria; ?>

<!-- Header -->
<div class="d-flex align-items-center mb-4">
  <a href="<?php echo BASE_PATH; ?>controllers/catalogo/ccat.php" class="btn btn-outline-secondary btn-sm me-3">
    <i class="fa-solid fa-arrow-left"></i>
  </a>
  <div>
    <h2 class="h4 mb-0 fw-bold">
      <i class="fa-solid fa-list me-2" style="color: #C41E3A;"></i>
      <?php echo $editing ? 'Editar Categoría' : 'Nueva Categoría'; ?>
    </h2>
    <small class="text-muted"><?php echo $editing ? 'Modifica los datos de la categoría' : 'Crea una nueva categoría de productos'; ?></small>
  </div>
</div>

<div class="row justify-content-center">
  <div class="col-12 col-lg-8">
    <div class="card shadow-soft border-0">
      <div class="card-body p-4 p-md-5">
        <form method="post" action="" class="needs-validation" novalidate>
          <?php echo csrf_field(); ?>
          
          <div class="mb-4">
            <label class="form-label fw-semibold" for="nombre">
              <i class="fa-solid fa-tag me-1 text-muted"></i>Nombre de la categoría
            </label>
            <input class="form-control form-control-lg" id="nombre" name="nombre" 
                   placeholder="Ej: Postres, Bebidas, Entradas..." required 
                   value="<?php echo $editing ? htmlspecialchars($categoria['nombre']) : ''; ?>">
          </div>
          
          <div class="mb-4">
            <label class="form-label fw-semibold" for="descripcion">
              <i class="fa-solid fa-align-left me-1 text-muted"></i>Descripción
            </label>
            <textarea class="form-control" id="descripcion" name="descripcion" rows="4" 
                      placeholder="Describe brevemente esta categoría..."><?php echo $editing ? htmlspecialchars($categoria['descripcion']) : ''; ?></textarea>
          </div>
          
          <hr class="my-4">
          
          <div class="d-flex gap-3 justify-content-end">
            <a class="btn btn-outline-secondary px-4" href="<?php echo BASE_PATH; ?>controllers/catalogo/ccat.php">
              <i class="fa-solid fa-xmark me-1"></i>Cancelar
            </a>
            <button class="btn btn-brand px-4" type="submit">
              <i class="fa-solid fa-floppy-disk me-2"></i>Guardar categoría
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../layout/pie.php'; ?>