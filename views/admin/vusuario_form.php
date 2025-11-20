<?php require_once __DIR__ . '/../../models/seg.php'; require_login(); require_role(['admin']); ?>
<?php include __DIR__ . '/../layout/cabezote.php'; ?>
<?php $editing = isset($usuario); ?>
<div class="row justify-content-center">
  <div class="col-12 col-md-8 col-lg-6">
    <div class="card shadow-soft">
      <div class="card-body p-4">
        <h2 class="h5 mb-3"><i class="fa-solid fa-user-edit me-2"></i><?php echo $editing ? 'Editar Usuario' : 'Nuevo Usuario'; ?></h2>
        <?php if (!empty($form_errors)): ?>
          <div class="alert alert-danger py-2 small mb-2">
            <ul class="m-0 ps-3">
            <?php foreach ($form_errors as $fe): ?><li><?php echo htmlspecialchars($fe); ?></li><?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>
        <form method="post" action="" class="row g-3" novalidate>
          <?php echo csrf_field(); ?>
          <div class="col-12">
            <label class="form-label" for="nombre">Nombre</label>
            <input class="form-control" id="nombre" type="text" name="nombre" required value="<?php echo $editing ? htmlspecialchars($usuario['nombre']) : ''; ?>">
          </div>
          <div class="col-12">
            <label class="form-label" for="email">Email</label>
            <input class="form-control" id="email" type="email" name="email" required value="<?php echo $editing ? htmlspecialchars($usuario['email']) : ''; ?>">
          </div>
          <div class="col-md-6">
            <label class="form-label" for="rol">Rol</label>
            <select class="form-select" id="rol" name="rol" required>
              <?php foreach (['admin','mesero','cajero','cliente'] as $r): ?>
                <option value="<?php echo e($r); ?>" <?php echo ($editing && $usuario['rol']==$r)?'selected':''; ?>><?php echo ucfirst($r); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <?php if (!$editing): ?>
          <div class="col-md-6">
            <label class="form-label" for="password">Contraseña</label>
            <input class="form-control" id="password" type="password" name="password" required minlength="8">
          </div>
          <?php endif; ?>
          <div class="col-12 d-flex gap-2">
            <button class="btn btn-brand" type="submit"><i class="fa-solid fa-floppy-disk me-1"></i>Guardar</button>
            <a class="btn btn-outline-secondary" href="<?php echo BASE_PATH; ?>controllers/usuarios/cusu.php">Cancelar</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php include __DIR__ . '/../layout/pie.php'; ?>