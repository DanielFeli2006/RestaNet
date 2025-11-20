<?php require_once __DIR__ . '/../../models/seg.php'; ?>
<?php include __DIR__ . '/../layout/cabezote.php'; ?>
<?php $token = $_GET['token'] ?? ''; ?>
<div class="row justify-content-center">
  <div class="col-12 col-sm-10 col-md-6 col-lg-5">
    <div class="card shadow-soft">
      <div class="card-body p-4">
        <h2 class="h4 mb-3"><i class="fa-solid fa-key me-2"></i>Restablecer contraseña</h2>
        <?php if (!empty($_SESSION['error'])): ?>
          <div class="alert alert-danger" role="alert"><?php echo e($_SESSION['error']); unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        <form method="post" action="<?php echo BASE_PATH; ?>controllers/auth/creset.php?a=update" class="needs-validation" novalidate>
          <?php echo csrf_field(); ?>
          <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
          <div class="mb-3">
            <label class="form-label" for="password">Nueva contraseña</label>
            <input class="form-control" id="password" type="password" name="password" required minlength="8">
          </div>
          <div class="mb-3">
            <label class="form-label" for="password2">Confirmar contraseña</label>
            <input class="form-control" id="password2" type="password" name="password2" required minlength="8">
          </div>
          <div class="d-grid">
            <button class="btn btn-brand" type="submit">Actualizar contraseña</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php include __DIR__ . '/../layout/pie.php'; ?>
