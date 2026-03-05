<?php require_once __DIR__ . '/../../models/seg.php'; require_login(); require_role(['admin']); ?>
<?php include __DIR__ . '/../layout/cabezote.php'; ?>
<?php $editing = isset($producto) && $producto; ?>

<!-- Header -->
<div class="d-flex align-items-center mb-4">
  <a href="<?php echo BASE_PATH; ?>controllers/catalogo/cprd.php" class="btn btn-outline-secondary btn-sm me-3">
    <i class="fa-solid fa-arrow-left"></i>
  </a>
  <div>
    <h2 class="h4 mb-0 fw-bold">
      <i class="fa-solid fa-bowl-food me-2" style="color: #C41E3A;"></i>
      <?php echo $editing ? 'Editar Producto' : 'Nuevo Producto'; ?>
    </h2>
    <small class="text-muted"><?php echo $editing ? 'Modifica los datos del producto' : 'Agrega un nuevo producto al menú'; ?></small>
  </div>
</div>

<?php if (!empty($_SESSION['error'])): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
  <i class="fa-solid fa-exclamation-circle me-2"></i><?php echo e($_SESSION['error']); unset($_SESSION['error']); ?>
  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="row justify-content-center">
  <div class="col-12 col-xl-10">
    <div class="card shadow-soft border-0">
      <div class="card-body p-4 p-md-5">
        <form method="post" action="" class="needs-validation" enctype="multipart/form-data" novalidate>
          <?php echo csrf_field(); ?>
          
          <div class="row g-4">
            <!-- Nombre -->
            <div class="col-md-8">
              <label class="form-label fw-semibold" for="nombre">
                <i class="fa-solid fa-utensils me-1 text-muted"></i>Nombre del producto <span class="text-danger">*</span>
              </label>
              <input class="form-control form-control-lg" id="nombre" name="nombre" 
                     placeholder="Ej: Hamburguesa clásica, Ensalada César..." required 
                     value="<?php echo $editing ? e($producto['nombre']) : ''; ?>"
                     maxlength="200">
              <div class="invalid-feedback">El nombre es obligatorio.</div>
            </div>
            
            <!-- Precio -->
            <div class="col-md-4">
              <label class="form-label fw-semibold" for="precio">
                <i class="fa-solid fa-dollar-sign me-1 text-muted"></i>Precio <span class="text-danger">*</span>
              </label>
              <div class="input-group input-group-lg">
                <span class="input-group-text" style="background: #F4A900; color: white; border-color: #F4A900;">$</span>
                <input class="form-control" id="precio" name="precio" type="number" step="0.01" min="0.01" 
                       placeholder="0.00" required 
                       value="<?php echo $editing ? e($producto['precio']) : ''; ?>">
              </div>
              <div class="invalid-feedback">Ingresa un precio válido mayor a 0.</div>
            </div>
            
            <!-- Descripción -->
            <div class="col-12">
              <label class="form-label fw-semibold" for="descripcion">
                <i class="fa-solid fa-align-left me-1 text-muted"></i>Descripción
              </label>
              <textarea class="form-control" id="descripcion" name="descripcion" rows="3" 
                        placeholder="Describe el producto, ingredientes, porciones..."
                        maxlength="1000"><?php echo $editing ? e($producto['descripcion']) : ''; ?></textarea>
            </div>
            
            <!-- Categoría -->
            <div class="col-md-6">
              <label class="form-label fw-semibold" for="categoria_id">
                <i class="fa-solid fa-folder me-1 text-muted"></i>Categoría <span class="text-danger">*</span>
              </label>
              <select class="form-select form-select-lg" id="categoria_id" name="categoria_id" required>
                <option value="" disabled selected>Selecciona una categoría</option>
                <?php foreach (($cats ?? []) as $c): ?>
                  <option value="<?php echo e($c['id']); ?>" <?php echo ($editing && $producto['categoria_id']==$c['id'])?'selected':''; ?>>
                    <?php echo e($c['nombre']); ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <div class="invalid-feedback">Selecciona una categoría.</div>
            </div>
            
            <!-- Estado activo -->
            <div class="col-md-6 d-flex align-items-end">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="activo" name="activo" value="1"
                       <?php echo (!$editing || ($editing && !empty($producto['activo']))) ? 'checked' : ''; ?>>
                <label class="form-check-label fw-semibold" for="activo">
                  <i class="fa-solid fa-eye me-1 text-muted"></i>Producto activo (visible en menú)
                </label>
              </div>
            </div>
            
            <!-- Imagen del producto -->
            <div class="col-12">
              <label class="form-label fw-semibold">
                <i class="fa-solid fa-image me-1 text-muted"></i>Imagen del producto
              </label>
              
              <?php if ($editing && !empty($producto['imagen'])): ?>
              <div class="mb-3 p-3 bg-light rounded-3">
                <div class="row align-items-center">
                  <div class="col-auto">
                    <img src="<?php echo BASE_PATH; ?>img/productos/<?php echo e($producto['imagen']); ?>" 
                         alt="Imagen actual" class="img-thumbnail" style="max-height: 120px; max-width: 120px; object-fit: cover;">
                  </div>
                  <div class="col">
                    <p class="mb-1 small text-muted">Imagen actual: <code><?php echo e($producto['imagen']); ?></code></p>
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" id="eliminar_imagen" name="eliminar_imagen" value="1">
                      <label class="form-check-label text-danger small" for="eliminar_imagen">
                        <i class="fa-solid fa-trash me-1"></i>Eliminar imagen actual
                      </label>
                    </div>
                  </div>
                </div>
              </div>
              <?php endif; ?>
              
              <div class="input-group">
                <input type="file" class="form-control" id="imagen" name="imagen" 
                       accept="image/jpeg,image/png,image/gif,image/webp">
                <label class="input-group-text" for="imagen">
                  <i class="fa-solid fa-upload"></i>
                </label>
              </div>
              <div class="form-text">
                <i class="fa-solid fa-info-circle me-1"></i>
                Formatos permitidos: JPEG, PNG, GIF, WebP. Tamaño máximo: 5MB.
                <?php if ($editing && !empty($producto['imagen'])): ?>
                <br>Sube una nueva imagen para reemplazar la actual.
                <?php endif; ?>
              </div>
            </div>
          </div>
          
          <hr class="my-4">
          
          <div class="d-flex gap-3 justify-content-end">
            <a class="btn btn-outline-secondary px-4" href="<?php echo BASE_PATH; ?>controllers/catalogo/cprd.php">
              <i class="fa-solid fa-xmark me-1"></i>Cancelar
            </a>
            <button class="btn btn-brand px-4" type="submit">
              <i class="fa-solid fa-floppy-disk me-2"></i>Guardar producto
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
// Preview de imagen antes de subir
document.getElementById('imagen').addEventListener('change', function(e) {
  const file = e.target.files[0];
  if (file) {
    const maxSize = 5 * 1024 * 1024; // 5MB
    if (file.size > maxSize) {
      alert('El archivo excede el tamaño máximo de 5MB.');
      e.target.value = '';
      return;
    }
    
    const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!allowedTypes.includes(file.type)) {
      alert('Tipo de archivo no permitido. Solo JPEG, PNG, GIF y WebP.');
      e.target.value = '';
      return;
    }
  }
});

// Deshabilitar checkbox de eliminar si se selecciona nueva imagen
document.getElementById('imagen').addEventListener('change', function(e) {
  const eliminarCheckbox = document.getElementById('eliminar_imagen');
  if (eliminarCheckbox && e.target.files.length > 0) {
    eliminarCheckbox.checked = false;
    eliminarCheckbox.disabled = true;
  } else if (eliminarCheckbox) {
    eliminarCheckbox.disabled = false;
  }
});
</script>

<?php include __DIR__ . '/../layout/pie.php'; ?>