<?php require_once __DIR__ . '/../../models/seg.php'; require_login(); require_role(['admin']); ?>
<?php include __DIR__ . '/../layout/cabezote.php'; ?>
<?php if (!empty($_SESSION['error'])): ?>
  <div class="alert alert-warning small"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
<?php endif; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h2 class="h4 m-0"><i class="fa-solid fa-users me-2"></i>Usuarios</h2>
  <a class="btn btn-accent" href="<?php echo BASE_PATH; ?>controllers/usuarios/cusu.php?a=create"><i class="fa-solid fa-user-plus me-1"></i>Nuevo usuario</a>
</div>
<div class="table-responsive shadow-soft">
  <table class="table table-sm align-middle mb-0">
    <thead>
      <tr><th>Nombre</th><th>Email</th><th>Rol</th><th class="text-center">Acciones</th></tr>
    </thead>
    <tbody>
    <?php foreach (($usuarios ?? []) as $u): ?>
      <tr>
        <td><?php echo htmlspecialchars($u['nombre']); ?></td>
        <td><?php echo htmlspecialchars($u['email']); ?></td>
        <td><span class="badge text-bg-secondary"><?php echo htmlspecialchars($u['rol']); ?></span></td>
        <td class="text-center" style="white-space:nowrap;">
          <a class="btn btn-sm btn-outline-primary" href="<?php echo BASE_PATH; ?>controllers/usuarios/cusu.php?a=edit&id=<?php echo $u['id']; ?>"><i class="fa-solid fa-pen-to-square"></i></a>
          <a class="btn btn-sm btn-outline-danger" href="<?php echo BASE_PATH; ?>controllers/usuarios/cusu.php?a=delete&id=<?php echo $u['id']; ?>" onclick="return confirm('¿Eliminar usuario?');"><i class="fa-solid fa-trash"></i></a>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php include __DIR__ . '/../layout/pie.php'; ?>