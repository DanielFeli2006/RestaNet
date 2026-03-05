<?php require_once __DIR__ . '/../../models/seg.php'; require_login(); require_role(['admin']); ?>
<?php include __DIR__ . '/../layout/cabezote.php'; ?>
<?php $editing = isset($mesa) && $mesa; ?>

<!-- Header -->
<div class="d-flex align-items-center mb-4">
  <a href="<?php echo BASE_PATH; ?>controllers/mesas/cmesa.php" class="btn btn-outline-secondary btn-sm me-3">
    <i class="fa-solid fa-arrow-left"></i>
  </a>
  <div>
    <h2 class="h4 mb-0 fw-bold">
      <i class="fa-solid fa-chair me-2" style="color: #C41E3A;"></i>
      <?php echo $editing ? 'Editar Mesa' : 'Nueva Mesa'; ?>
    </h2>
    <small class="text-muted"><?php echo $editing ? 'Modifica los datos de la mesa' : 'Agrega una nueva mesa al restaurante'; ?></small>
  </div>
</div>

<?php if (!empty($_SESSION['error'])): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
  <i class="fa-solid fa-exclamation-circle me-2"></i><?php echo e($_SESSION['error']); unset($_SESSION['error']); ?>
  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="row justify-content-center">
  <div class="col-12 col-lg-8">
    <div class="card shadow-soft border-0">
      <div class="card-body p-4 p-md-5">
        <form method="post" action="" class="needs-validation" novalidate>
          <?php echo csrf_field(); ?>
          
          <div class="row g-4">
            <!-- Número de mesa -->
            <div class="col-md-6">
              <label class="form-label fw-semibold" for="numero">
                <i class="fa-solid fa-hashtag me-1 text-muted"></i>Número de mesa <span class="text-danger">*</span>
              </label>
              <input class="form-control form-control-lg" id="numero" name="numero" type="number" min="1" 
                     placeholder="Ej: 1, 2, 3..." required 
                     value="<?php echo $editing ? e($mesa['numero']) : ''; ?>">
              <div class="invalid-feedback">El número de mesa es obligatorio.</div>
            </div>
            
            <!-- Capacidad -->
            <div class="col-md-6">
              <label class="form-label fw-semibold" for="capacidad">
                <i class="fa-solid fa-users me-1 text-muted"></i>Capacidad <span class="text-danger">*</span>
              </label>
              <div class="input-group input-group-lg">
                <input class="form-control" id="capacidad" name="capacidad" type="number" min="1" max="20"
                       placeholder="4" required 
                       value="<?php echo $editing ? e($mesa['capacidad']) : ''; ?>">
                <span class="input-group-text">personas</span>
              </div>
              <div class="invalid-feedback">La capacidad es obligatoria.</div>
            </div>
            
            <!-- Ubicación -->
            <div class="col-md-6">
              <label class="form-label fw-semibold" for="ubicacion">
                <i class="fa-solid fa-map-marker-alt me-1 text-muted"></i>Ubicación
              </label>
              <input class="form-control" id="ubicacion" name="ubicacion" 
                     placeholder="Ej: Terraza, Interior, Ventana..."
                     maxlength="100"
                     value="<?php echo $editing ? e($mesa['ubicacion']) : ''; ?>">
            </div>
            
            <!-- Estado -->
            <div class="col-md-6">
              <label class="form-label fw-semibold" for="estado">
                <i class="fa-solid fa-circle me-1 text-muted"></i>Estado
              </label>
              <select class="form-select" id="estado" name="estado">
                <option value="disponible" <?php echo ($editing && $mesa['estado'] === 'disponible') ? 'selected' : (!$editing ? 'selected' : ''); ?>>
                  Disponible
                </option>
                <option value="ocupada" <?php echo ($editing && $mesa['estado'] === 'ocupada') ? 'selected' : ''; ?>>
                  Ocupada
                </option>
                <option value="reservada" <?php echo ($editing && $mesa['estado'] === 'reservada') ? 'selected' : ''; ?>>
                  Reservada
                </option>
                <option value="mantenimiento" <?php echo ($editing && $mesa['estado'] === 'mantenimiento') ? 'selected' : ''; ?>>
                  Mantenimiento
                </option>
              </select>
            </div>
            
            <!-- Notas -->
            <div class="col-12">
              <label class="form-label fw-semibold" for="notas">
                <i class="fa-solid fa-sticky-note me-1 text-muted"></i>Notas
              </label>
              <textarea class="form-control" id="notas" name="notas" rows="3" 
                        placeholder="Observaciones adicionales sobre la mesa..."
                        maxlength="500"><?php echo $editing ? e($mesa['notas']) : ''; ?></textarea>
            </div>
          </div>
          
          <hr class="my-4">
          
          <div class="d-flex gap-3 justify-content-end">
            <a class="btn btn-outline-secondary px-4" href="<?php echo BASE_PATH; ?>controllers/mesas/cmesa.php">
              <i class="fa-solid fa-xmark me-1"></i>Cancelar
            </a>
            <button class="btn btn-brand px-4" type="submit">
              <i class="fa-solid fa-floppy-disk me-2"></i>Guardar mesa
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../layout/pie.php'; ?>
