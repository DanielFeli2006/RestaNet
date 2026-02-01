<?php require_once __DIR__ . '/../../models/seg.php'; require_login(); require_role(['admin']); ?>
<?php include __DIR__ . '/../layout/cabezote.php'; ?>

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

<!-- Header -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
  <div>
    <h2 class="h3 mb-1 fw-bold">
      <i class="fa-solid fa-users me-2" style="color: #C41E3A;"></i>Gestión de Usuarios
    </h2>
    <p class="text-muted mb-0">Administra las cuentas de acceso al sistema</p>
  </div>
  <a class="btn btn-brand" href="<?php echo BASE_PATH; ?>controllers/usuarios/cusu.php?a=create">
    <i class="fa-solid fa-user-plus me-2"></i>Nuevo usuario
  </a>
</div>

<!-- Users Table -->
<div class="card shadow-soft">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead>
          <tr>
            <th class="ps-4">Usuario</th>
            <th>Email</th>
            <th>Rol</th>
            <th class="text-center pe-4">Acciones</th>
          </tr>
        </thead>
        <tbody>
        <?php if (empty($usuarios ?? [])): ?>
          <tr>
            <td colspan="4" class="text-center py-4 text-muted">
              <i class="fa-solid fa-users fa-2x mb-2 opacity-25 d-block"></i>
              No hay usuarios registrados
            </td>
          </tr>
        <?php else: ?>
          <?php foreach ($usuarios as $u): ?>
            <tr>
              <td class="ps-4">
                <div class="d-flex align-items-center">
                  <div class="avatar-circle me-3">
                    <i class="fa-solid fa-user"></i>
                  </div>
                  <div>
                    <div class="fw-semibold"><?php echo htmlspecialchars($u['nombre']); ?></div>
                  </div>
                </div>
              </td>
              <td>
                <span class="text-muted"><?php echo htmlspecialchars($u['email']); ?></span>
              </td>
              <td>
                <?php 
                $rolColors = [
                  'admin' => 'bg-danger-subtle text-danger-emphasis',
                  'mesero' => 'bg-info-subtle text-info-emphasis',
                  'cajero' => 'bg-warning-subtle text-warning-emphasis',
                  'cliente' => 'bg-success-subtle text-success-emphasis',
                ];
                $rolClass = $rolColors[$u['rol']] ?? 'bg-secondary-subtle text-secondary-emphasis';
                ?>
                <span class="badge <?php echo $rolClass; ?> px-3 py-2">
                  <?php echo htmlspecialchars(ucfirst($u['rol'])); ?>
                </span>
              </td>
              <td class="text-center pe-4">
                <div class="btn-group" role="group">
                  <a class="btn btn-sm btn-outline-primary" href="<?php echo BASE_PATH; ?>controllers/usuarios/cusu.php?a=edit&id=<?php echo $u['id']; ?>" title="Editar">
                    <i class="fa-solid fa-pen-to-square"></i>
                  </a>
                  <a class="btn btn-sm btn-outline-danger" href="<?php echo BASE_PATH; ?>controllers/usuarios/cusu.php?a=delete&id=<?php echo $u['id']; ?>" onclick="return confirm('¿Estás seguro de eliminar este usuario?');" title="Eliminar">
                    <i class="fa-solid fa-trash"></i>
                  </a>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<style>
.avatar-circle {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: linear-gradient(135deg, #C41E3A, #8B0000);
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 0.875rem;
}
</style>

<?php include __DIR__ . '/../layout/pie.php'; ?>