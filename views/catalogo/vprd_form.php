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

<div class="row justify-content-center">
  <div class="col-12 col-xl-10">
    <div class="card shadow-soft border-0">
      <div class="card-body p-4 p-md-5">
        <form method="post" action="" class="needs-validation" novalidate>
          <?php echo csrf_field(); ?>
          
          <div class="row g-4">
            <!-- Nombre -->
            <div class="col-md-8">
              <label class="form-label fw-semibold" for="nombre">
                <i class="fa-solid fa-utensils me-1 text-muted"></i>Nombre del producto
              </label>
              <input class="form-control form-control-lg" id="nombre" name="nombre" 
                     placeholder="Ej: Hamburguesa clásica, Ensalada César..." required 
                     value="<?php echo $editing ? htmlspecialchars($producto['nombre']) : ''; ?>">
            </div>
            
            <!-- Precio -->
            <div class="col-md-4">
              <label class="form-label fw-semibold" for="precio">
                <i class="fa-solid fa-dollar-sign me-1 text-muted"></i>Precio
              </label>
              <div class="input-group input-group-lg">
                <span class="input-group-text" style="background: #F4A900; color: white; border-color: #F4A900;">$</span>
                <input class="form-control" id="precio" name="precio" type="number" step="0.01" min="0" 
                       placeholder="0.00" required 
                       value="<?php echo $editing ? htmlspecialchars($producto['precio']) : ''; ?>">
              </div>
            </div>
            
            <!-- Descripción -->
            <div class="col-12">
              <label class="form-label fw-semibold" for="descripcion">
                <i class="fa-solid fa-align-left me-1 text-muted"></i>Descripción
              </label>
              <textarea class="form-control" id="descripcion" name="descripcion" rows="3" 
                        placeholder="Describe el producto, ingredientes, porciones..."><?php echo $editing ? htmlspecialchars($producto['descripcion']) : ''; ?></textarea>
            </div>
            
            <!-- Categoría -->
            <div class="col-md-6">
              <label class="form-label fw-semibold" for="categoria_id">
                <i class="fa-solid fa-folder me-1 text-muted"></i>Categoría
              </label>
              <select class="form-select form-select-lg" id="categoria_id" name="categoria_id" required>
                <option value="" disabled selected>Selecciona una categoría</option>
                <?php foreach (($cats ?? []) as $c): ?>
                  <option value="<?php echo e($c['id']); ?>" <?php echo ($editing && $producto['categoria_id']==$c['id'])?'selected':''; ?>>
                    <?php echo htmlspecialchars($c['nombre']); ?>
                  </option>
                <?php endforeach; ?>
              </select>
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

<?php include __DIR__ . '/../layout/pie.php'; ?>