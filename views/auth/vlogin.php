<?php require_once __DIR__ . '/../../models/seg.php'; ?>
<?php include __DIR__ . '/../layout/cabezote.php'; ?>
<div class="row justify-content-center align-items-center min-vh-75">
  <div class="col-12 col-sm-10 col-md-7 col-lg-5 col-xl-4">
    <div class="card shadow-soft animate-fade-in">
      <div class="card-body p-4 p-md-5">
        <!-- Logo/Header -->
        <div class="text-center mb-4">
          <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-gradient-brand p-3 mb-3" style="width: 70px; height: 70px; background: linear-gradient(135deg, #C41E3A, #8B0000);">
            <i class="fa-solid fa-utensils fa-2x text-white"></i>
          </div>
          <h2 class="h4 mb-1 fw-bold">Bienvenido a Restanet</h2>
          <p class="text-muted small mb-0">Ingresa tus credenciales para continuar</p>
        </div>
        
        <?php if (!empty($_SESSION['error'])): ?>
          <div class="alert alert-danger" role="alert">
            <?php echo e($_SESSION['error']); unset($_SESSION['error']); ?>
          </div>
        <?php endif; ?>
        <?php if (!empty($_SESSION['success'])): ?>
          <div class="alert alert-success" role="alert">
            <?php echo e($_SESSION['success']); unset($_SESSION['success']); ?>
          </div>
        <?php endif; ?>
        <?php if (isset($_GET['expired'])): ?>
          <div class="alert alert-warning" role="alert">
            Tu sesión ha expirado. Por favor, inicia sesión nuevamente.
          </div>
        <?php endif; ?>
        
        <form method="post" action="<?php echo BASE_PATH; ?>controllers/auth/cauth.php?a=login" class="needs-validation" novalidate>
          <?php echo csrf_field(); ?>
          <div class="mb-3">
            <label class="form-label" for="email">
              <i class="fa-solid fa-envelope me-1 text-brand"></i>Correo electrónico
            </label>
            <input class="form-control form-control-lg" id="email" type="email" name="email" placeholder="tu@email.com" required autocomplete="email">
            <div class="invalid-feedback">Por favor, ingresa un email válido.</div>
          </div>
          <div class="mb-4">
            <label class="form-label" for="password">
              <i class="fa-solid fa-lock me-1 text-brand"></i>Contraseña
            </label>
            <div class="input-group">
              <input class="form-control form-control-lg" id="password" type="password" name="password" placeholder="••••••••" required autocomplete="current-password">
              <button class="btn btn-outline-secondary" type="button" id="togglePassword" aria-label="Mostrar contraseña">
                <i class="fa-solid fa-eye"></i>
              </button>
            </div>
            <div class="invalid-feedback">Por favor, ingresa tu contraseña.</div>
          </div>
          <div class="d-grid mb-3">
            <button class="btn btn-brand btn-lg" type="submit">
              <i class="fa-solid fa-arrow-right-to-bracket me-2"></i>Iniciar sesión
            </button>
          </div>
          <div class="text-center">
            <a href="<?php echo BASE_PATH; ?>controllers/auth/creset.php?a=request" class="text-muted small">
              <i class="fa-solid fa-key me-1"></i>¿Olvidaste tu contraseña?
            </a>
          </div>
        </form>
      </div>
    </div>
    <!-- Info footer -->
    <div class="text-center mt-4">
      <small class="text-muted">
        <i class="fa-solid fa-shield-halved me-1"></i>
        Conexión segura · Tus datos están protegidos
      </small>
    </div>
  </div>
</div>

<style>
.min-vh-75 { min-height: 75vh; }
.bg-gradient-brand { background: linear-gradient(135deg, #C41E3A, #8B0000); }
.text-brand { color: #C41E3A; }
</style>

<script>
// Toggle password visibility
document.getElementById('togglePassword')?.addEventListener('click', function() {
  const password = document.getElementById('password');
  const icon = this.querySelector('i');
  if (password.type === 'password') {
    password.type = 'text';
    icon.classList.replace('fa-eye', 'fa-eye-slash');
  } else {
    password.type = 'password';
    icon.classList.replace('fa-eye-slash', 'fa-eye');
  }
});

// Bootstrap validation
(function () {
  'use strict';
  const forms = document.querySelectorAll('.needs-validation');
  Array.from(forms).forEach(form => {
    form.addEventListener('submit', event => {
      if (!form.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
      }
      form.classList.add('was-validated');
    }, false);
  });
})();
</script>
<?php include __DIR__ . '/../layout/pie.php'; ?>