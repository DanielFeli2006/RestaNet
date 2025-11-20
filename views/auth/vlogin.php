<?php require_once __DIR__ . '/../../models/seg.php'; ?>
<?php include __DIR__ . '/../layout/cabezote.php'; ?>
<div class="row justify-content-center">
  <div class="col-12 col-sm-10 col-md-6 col-lg-5">
    <div class="card shadow-soft">
      <div class="card-body p-4">
        <h2 class="h4 mb-3"><i class="fa-solid fa-right-to-bracket me-2"></i>Iniciar sesión</h2>
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
  <form method="post" action="<?php echo BASE_PATH; ?>controllers/auth/cauth.php?a=login" class="needs-validation" novalidate>
          <?php echo csrf_field(); ?>
          <div class="mb-3">
            <label class="form-label" for="email"><i class="fa-solid fa-envelope me-1"></i>Email</label>
            <input class="form-control" id="email" type="email" name="email" required>
          </div>
          <div class="mb-3">
            <label class="form-label" for="password"><i class="fa-solid fa-lock me-1"></i>Contraseña</label>
            <input class="form-control" id="password" type="password" name="password" required>
          </div>
          <div class="d-grid">
            <button class="btn btn-brand" type="submit"><i class="fa-solid fa-arrow-right-to-bracket me-2"></i>Entrar</button>
          </div>
          <div class="mt-2 text-center">
            <a href="<?php echo BASE_PATH; ?>controllers/auth/creset.php?a=request">¿Olvidaste tu contraseña?</a>
          </div>
        </form>
      </div>
    </div>
  </div>
  </div>
<?php include __DIR__ . '/../layout/pie.php'; ?>