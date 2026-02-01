<?php require_once __DIR__ . '/../../models/seg.php'; require_login(); require_role(['admin']); ?>
<?php include __DIR__ . '/../layout/cabezote.php'; ?>
<?php $editing = isset($usuario); ?>

<!-- Header -->
<div class="d-flex align-items-center mb-4">
  <a href="<?php echo BASE_PATH; ?>controllers/usuarios/cusu.php" class="btn btn-outline-secondary btn-sm me-3">
    <i class="fa-solid fa-arrow-left"></i>
  </a>
  <div>
    <h2 class="h4 mb-0 fw-bold">
      <i class="fa-solid <?php echo $editing ? 'fa-user-pen' : 'fa-user-plus'; ?> me-2" style="color: #C41E3A;"></i>
      <?php echo $editing ? 'Editar Usuario' : 'Nuevo Usuario'; ?>
    </h2>
    <small class="text-muted"><?php echo $editing ? 'Modifica los datos del usuario' : 'Crea una nueva cuenta de usuario'; ?></small>
  </div>
</div>

<div class="row justify-content-center">
  <div class="col-12 col-lg-8">
    <div class="card shadow-soft border-0">
      <div class="card-body p-4 p-md-5">
        <?php if (!empty($form_errors)): ?>
          <div class="alert alert-danger" role="alert">
            <div class="d-flex align-items-start gap-2">
              <i class="fa-solid fa-circle-exclamation mt-1"></i>
              <div>
                <strong>Por favor corrige los siguientes errores:</strong>
                <ul class="mb-0 mt-2 ps-3">
                <?php foreach ($form_errors as $fe): ?><li><?php echo htmlspecialchars($fe); ?></li><?php endforeach; ?>
                </ul>
              </div>
            </div>
          </div>
        <?php endif; ?>
        
        <form method="post" action="" class="needs-validation" novalidate>
          <?php echo csrf_field(); ?>
          
          <div class="row g-4">
            <!-- Nombre -->
            <div class="col-12">
              <label class="form-label fw-semibold" for="nombre">
                <i class="fa-solid fa-user me-1 text-muted"></i>Nombre completo
              </label>
              <input class="form-control form-control-lg" id="nombre" type="text" name="nombre" 
                     placeholder="Ingresa el nombre" required 
                     value="<?php echo $editing ? htmlspecialchars($usuario['nombre']) : ''; ?>">
            </div>
            
            <!-- Email -->
            <div class="col-12">
              <label class="form-label fw-semibold" for="email">
                <i class="fa-solid fa-envelope me-1 text-muted"></i>Correo electrónico
              </label>
              <input class="form-control form-control-lg" id="email" type="email" name="email" 
                     placeholder="usuario@email.com" required 
                     value="<?php echo $editing ? htmlspecialchars($usuario['email']) : ''; ?>">
            </div>
            
            <!-- Rol -->
            <div class="col-md-6">
              <label class="form-label fw-semibold" for="rol">
                <i class="fa-solid fa-user-tag me-1 text-muted"></i>Rol
              </label>
              <select class="form-select form-select-lg" id="rol" name="rol" required>
                <?php 
                $rolIcons = ['admin' => 'fa-crown', 'mesero' => 'fa-bell-concierge', 'cajero' => 'fa-cash-register', 'cliente' => 'fa-user'];
                foreach (['admin','mesero','cajero','cliente'] as $r): ?>
                  <option value="<?php echo e($r); ?>" <?php echo ($editing && $usuario['rol']==$r)?'selected':''; ?>>
                    <?php echo ucfirst($r); ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            
            <?php if (!$editing): ?>
            <!-- Password -->
            <div class="col-md-6">
              <label class="form-label fw-semibold" for="password">
                <i class="fa-solid fa-lock me-1 text-muted"></i>Contraseña
              </label>
              <input class="form-control form-control-lg" id="password" type="password" name="password" 
                     placeholder="Mínimo 8 caracteres" required minlength="8">
              <div class="form-text">La contraseña debe tener al menos 8 caracteres.</div>
            </div>
            <?php endif; ?>
          </div>
          
          <hr class="my-4">
          
          <div class="d-flex gap-3 justify-content-end">
            <a class="btn btn-outline-secondary px-4" href="<?php echo BASE_PATH; ?>controllers/usuarios/cusu.php">
              <i class="fa-solid fa-xmark me-1"></i>Cancelar
            </a>
            <button class="btn btn-brand px-4" type="submit">
              <i class="fa-solid fa-floppy-disk me-2"></i>Guardar usuario
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../layout/pie.php'; ?>