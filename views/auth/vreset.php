<?php require_once __DIR__ . '/../../models/seg.php'; ?>
<?php include __DIR__ . '/../layout/cabezote.php'; ?>
<?php $token = $_GET['token'] ?? ''; ?>

<div class="row justify-content-center align-items-center min-vh-75">
  <div class="col-12 col-sm-10 col-md-7 col-lg-5 col-xl-4">
    <div class="card shadow-soft border-0 animate-fade-in">
      <div class="card-body p-4 p-md-5">
        <!-- Header -->
        <div class="text-center mb-4">
          <div class="d-inline-flex align-items-center justify-content-center rounded-circle p-3 mb-3" style="width: 70px; height: 70px; background: linear-gradient(135deg, #C41E3A, #8B0000);">
            <i class="fa-solid fa-key fa-2x text-white"></i>
          </div>
          <h2 class="h4 mb-1 fw-bold">Restablecer contraseña</h2>
          <p class="text-muted small mb-0">Ingresa tu nueva contraseña</p>
        </div>
        
        <?php if (!empty($_SESSION['error'])): ?>
          <div class="alert alert-danger" role="alert">
            <i class="fa-solid fa-circle-exclamation me-2"></i>
            <?php echo e($_SESSION['error']); unset($_SESSION['error']); ?>
          </div>
        <?php endif; ?>
        
        <form method="post" action="<?php echo BASE_PATH; ?>controllers/auth/creset.php?a=update" class="needs-validation" novalidate>
          <?php echo csrf_field(); ?>
          <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
          
          <div class="mb-3">
            <label class="form-label fw-semibold" for="password">
              <i class="fa-solid fa-lock me-1" style="color: #C41E3A;"></i>Nueva contraseña
            </label>
            <div class="input-group">
              <input class="form-control form-control-lg" id="password" type="password" name="password" 
                     placeholder="Mínimo 8 caracteres" required minlength="8">
              <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password', this)">
                <i class="fa-solid fa-eye"></i>
              </button>
            </div>
          </div>
          
          <div class="mb-4">
            <label class="form-label fw-semibold" for="password2">
              <i class="fa-solid fa-lock me-1" style="color: #C41E3A;"></i>Confirmar contraseña
            </label>
            <div class="input-group">
              <input class="form-control form-control-lg" id="password2" type="password" name="password2" 
                     placeholder="Repite tu contraseña" required minlength="8">
              <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password2', this)">
                <i class="fa-solid fa-eye"></i>
              </button>
            </div>
          </div>
          
          <div class="d-grid mb-3">
            <button class="btn btn-brand btn-lg" type="submit">
              <i class="fa-solid fa-shield-check me-2"></i>Actualizar contraseña
            </button>
          </div>
          
          <div class="text-center">
            <a href="<?php echo BASE_PATH; ?>views/auth/vlogin.php" class="text-muted small">
              <i class="fa-solid fa-arrow-left me-1"></i>Volver al inicio de sesión
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<style>
.min-vh-75 { min-height: 75vh; }
.animate-fade-in {
  animation: fadeIn 0.5s ease;
}
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>

<script>
function togglePassword(inputId, btn) {
  const input = document.getElementById(inputId);
  const icon = btn.querySelector('i');
  if (input.type === 'password') {
    input.type = 'text';
    icon.classList.replace('fa-eye', 'fa-eye-slash');
  } else {
    input.type = 'password';
    icon.classList.replace('fa-eye-slash', 'fa-eye');
  }
}
</script>

<?php include __DIR__ . '/../layout/pie.php'; ?>
