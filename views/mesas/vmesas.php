<?php require_once __DIR__ . '/../../models/seg.php'; require_login(); require_role(['admin', 'mesero', 'cajero']); ?>
<?php include __DIR__ . '/../layout/cabezote.php'; ?>

<!-- Header -->
<div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-3">
  <div>
    <h2 class="h3 mb-0 fw-bold">
      <i class="fa-solid fa-chair me-2" style="color: #C41E3A;"></i>Mesas
    </h2>
    <small class="text-muted">Gestión y estado de las mesas del restaurante</small>
  </div>
  <?php if (has_role(['admin'])): ?>
    <a class="btn btn-brand" href="<?php echo BASE_PATH; ?>controllers/mesas/cmesa.php?a=create">
      <i class="fa-solid fa-plus me-2"></i>Nueva mesa
    </a>
  <?php endif; ?>
</div>

<!-- Resumen de estados -->
<div class="row mb-4">
  <?php 
  $disponibles = count(array_filter($mesas ?? [], fn($m) => $m['estado'] === 'disponible'));
  $ocupadas = count(array_filter($mesas ?? [], fn($m) => $m['estado'] === 'ocupada'));
  ?>
  <div class="col-6 col-md-3 mb-3">
    <div class="card border-0 bg-success bg-opacity-10 h-100">
      <div class="card-body text-center py-3">
        <div class="h3 mb-0 text-success"><?php echo $disponibles; ?></div>
        <small class="text-muted">Disponibles</small>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3 mb-3">
    <div class="card border-0 bg-danger bg-opacity-10 h-100">
      <div class="card-body text-center py-3">
        <div class="h3 mb-0 text-danger"><?php echo $ocupadas; ?></div>
        <small class="text-muted">Ocupadas</small>
      </div>
    </div>
  </div>
</div>

<?php if (empty($mesas ?? [])): ?>
  <div class="card shadow-soft border-0">
    <div class="card-body text-center py-5">
      <div class="mb-3">
        <i class="fa-solid fa-chair fa-4x" style="color: #dee2e6;"></i>
      </div>
      <h5 class="text-muted">No hay mesas registradas</h5>
      <p class="text-muted small">Agrega la primera mesa del restaurante</p>
      <?php if (has_role(['admin'])): ?>
        <a class="btn btn-brand mt-2" href="<?php echo BASE_PATH; ?>controllers/mesas/cmesa.php?a=create">
          <i class="fa-solid fa-plus me-2"></i>Crear mesa
        </a>
      <?php endif; ?>
    </div>
  </div>
<?php else: ?>
  <!-- Vista de mesas en cuadrícula -->
  <div class="row g-4">
    <?php foreach (($mesas ?? []) as $m): 
      // FIX: Solo manejar los 2 estados válidos del ENUM de la BD
      $estadoClass = match($m['estado']) {
        'disponible' => 'success',
        'ocupada' => 'danger',
        default => 'light'
      };
      $estadoIcon = match($m['estado']) {
        'disponible' => 'check-circle',
        'ocupada' => 'users',
        default => 'circle'
      };
    ?>
    <div class="col-6 col-md-4 col-lg-3">
      <div class="card shadow-soft border-0 h-100 mesa-card" data-mesa-id="<?php echo e($m['id']); ?>">
        <div class="card-body text-center p-4">
          <!-- Número de mesa -->
          <div class="rounded-circle bg-<?php echo $estadoClass; ?> bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
            <span class="h2 mb-0 fw-bold text-<?php echo $estadoClass; ?>"><?php echo e($m['numero']); ?></span>
          </div>
          
          <!-- Estado -->
          <div class="mb-2">
            <span class="badge bg-<?php echo $estadoClass; ?>">
              <i class="fa-solid fa-<?php echo $estadoIcon; ?> me-1"></i>
              <?php echo ucfirst(e($m['estado'])); ?>
            </span>
          </div>
          
          <!-- Info -->
          <div class="small text-muted mb-3">
            <i class="fa-solid fa-users me-1"></i><?php echo (int)$m['capacidad']; ?> personas
            <?php if (!empty($m['ubicacion'])): ?>
              <br><i class="fa-solid fa-map-marker-alt me-1"></i><?php echo e($m['ubicacion']); ?>
            <?php endif; ?>
            <?php if ($m['pedidos_activos'] > 0): ?>
              <br><span class="text-warning"><i class="fa-solid fa-receipt me-1"></i><?php echo (int)$m['pedidos_activos']; ?> pedido(s)</span>
            <?php endif; ?>
          </div>
          
          <!-- FIX: Cambio rápido de estado - Solo 2 botones válidos (disponible/ocupada) -->
          <?php if (has_role(['admin', 'mesero'])): ?>
          <div class="btn-group btn-group-sm w-100" role="group">
            <button type="button" class="btn btn-outline-success btn-estado <?php echo $m['estado'] === 'disponible' ? 'active' : ''; ?>" 
                    data-estado="disponible" title="Marcar como Disponible">
              <i class="fa-solid fa-check me-1"></i>Libre
            </button>
            <button type="button" class="btn btn-outline-danger btn-estado <?php echo $m['estado'] === 'ocupada' ? 'active' : ''; ?>"
                    data-estado="ocupada" title="Marcar como Ocupada">
              <i class="fa-solid fa-users me-1"></i>Ocupada
            </button>
          </div>
          <?php endif; ?>
        </div>
        
        <?php if (has_role(['admin'])): ?>
        <div class="card-footer bg-transparent border-0 text-center py-2">
          <a href="<?php echo BASE_PATH; ?>controllers/mesas/cmesa.php?a=edit&id=<?php echo e($m['id']); ?>" class="btn btn-sm btn-outline-primary me-1" title="Editar">
            <i class="fa-solid fa-pen-to-square"></i>
          </a>
          <a href="<?php echo BASE_PATH; ?>controllers/mesas/cmesa.php?a=delete&id=<?php echo e($m['id']); ?>" class="btn btn-sm btn-outline-danger" title="Eliminar">
            <i class="fa-solid fa-trash"></i>
          </a>
        </div>
        <?php endif; ?>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<style>
.mesa-card {
  transition: transform 0.2s, box-shadow 0.2s;
}
.mesa-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important;
}
.btn-estado.active {
  pointer-events: none;
}
</style>

<script>
// SEGURIDAD: Token CSRF para peticiones AJAX
const csrfToken = '<?php echo csrf_token(); ?>';

document.querySelectorAll('.btn-estado').forEach(btn => {
  btn.addEventListener('click', async function() {
    const card = this.closest('.mesa-card');
    const mesaId = card.dataset.mesaId;
    const nuevoEstado = this.dataset.estado;
    
    // Deshabilitar botones temporalmente
    const btns = card.querySelectorAll('.btn-estado');
    btns.forEach(b => b.disabled = true);
    
    // FIX: Feedback visual inmediato (spinner)
    const originalHtml = this.innerHTML;
    this.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
    
    try {
      const formData = new FormData();
      formData.append('mesa_id', mesaId);
      formData.append('estado', nuevoEstado);
      formData.append('csrf_token', csrfToken);
      
      const response = await fetch('<?php echo BASE_PATH; ?>controllers/mesas/cmesa.php?a=cambiar_estado', {
        method: 'POST',
        body: formData,
        credentials: 'same-origin'
      });
      
      const data = await response.json();
      
      if (data.ok) {
        // Recargar página para reflejar cambios
        location.reload();
      } else {
        // FIX: Mostrar error detallado al usuario
        alert('Error: ' + (data.error || 'No se pudo actualizar el estado'));
        this.innerHTML = originalHtml;
        btns.forEach(b => b.disabled = false);
      }
    } catch (error) {
      console.error('Error:', error);
      alert('Error de conexión al servidor');
      this.innerHTML = originalHtml;
      btns.forEach(b => b.disabled = false);
    }
  });
});
</script>

<?php include __DIR__ . '/../layout/pie.php'; ?>
