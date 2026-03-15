<?php require_once __DIR__ . '/../../models/seg.php'; ?>
<?php include __DIR__ . '/../layout/cabezote.php'; ?>

<div class="row justify-content-center align-items-center min-vh-75">
  <div class="col-12 col-sm-10 col-md-7 col-lg-5 col-xl-4">
    <div class="card shadow-soft border-0 animate-fade-in">
      <div class="card-body p-4 p-md-5">
        <!-- Header -->
        <div class="text-center mb-4">
          <div class="d-inline-flex align-items-center justify-content-center rounded-circle p-3 mb-3" style="width: 70px; height: 70px; background: linear-gradient(135deg, #F4A900, #CC8800);">
            <i class="fa-solid fa-envelope-open-text fa-2x text-white"></i>
          </div>
          <h2 class="h4 mb-1 fw-bold">Recuperar contraseña</h2>
          <p class="text-muted small mb-0">Te enviaremos instrucciones a tu correo</p>
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
        
        <form method="post" action="<?php echo BASE_PATH; ?>controllers/auth/creset.php?a=send" class="needs-validation" novalidate>
          <?php echo csrf_field(); ?>
          <div class="mb-4">
            <label class="form-label fw-semibold" for="email">
              <i class="fa-solid fa-envelope me-1" style="color: #C41E3A;"></i>Correo electrónico
            </label>
            <input class="form-control form-control-lg" id="email" type="email" name="email" 
                   placeholder="tu@email.com" required autocomplete="email">
            <div class="form-text">Ingresa el correo asociado a tu cuenta.</div>
          </div>
          <div class="d-grid mb-3">
            <button class="btn btn-accent btn-lg" type="submit">
              <i class="fa-solid fa-paper-plane me-2"></i>Enviar instrucciones
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

<?php include __DIR__ . '/../layout/pie.php'; ?>
