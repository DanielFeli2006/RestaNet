<?php start_secure_session();
// Seguridad HTTP headers básicos
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header("Referrer-Policy: no-referrer-when-downgrade");
header("Permissions-Policy: geolocation=(), microphone=()");
// Content Security Policy: permitir recursos del propio dominio y CDN conocidos
header("Content-Security-Policy: default-src 'self' https:; script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://code.jquery.com; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://fonts.googleapis.com https://cdnjs.cloudflare.com https://cdn.datatables.net; img-src 'self' data: https:; font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com; frame-ancestors 'self';");
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
  <meta name="description" content="Restanet - Sistema de gestión de restaurantes moderno y eficiente">
  <meta name="theme-color" content="#C41E3A">
  <title>Restanet - Gestión de Restaurante</title>
  <!-- Preconnect for performance -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
  <!-- Google Fonts - Inter -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <!-- DataTables Bootstrap 5 CSS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/2.0.3/css/dataTables.bootstrap5.min.css" />
  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <!-- App styles -->
  <link rel="stylesheet" href="<?php echo BASE_PATH; ?>css/style.css">
  <!-- Config for JS -->
  <script>
    window.restanetConfig = {
      baseUrl: '<?php echo BASE_PATH; ?>',
      csrfToken: '<?php echo e(csrf_token()); ?>',
      sessionTimeout: <?php echo defined('SESSION_TIMEOUT') ? (int) SESSION_TIMEOUT : 600; ?>,
      sessionGrace: <?php echo defined('SESSION_TIMEOUT_GRACE') ? (int) SESSION_TIMEOUT_GRACE : 0; ?>
    };
  </script>
</head>
<body>
<header class="sticky-top">
  <nav class="navbar navbar-expand-lg navbar-dark gradient-navbar">
    <div class="container">
      <a class="navbar-brand fw-bold" href="<?php echo BASE_PATH; ?>index.php">
        <i class="fa-solid fa-utensils me-2"></i>Restanet
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Abrir menú de navegación">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="mainNav">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
          <?php if (is_logged_in()): ?>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo BASE_PATH; ?>index.php">
                <i class="fa-solid fa-house me-1"></i>Inicio
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo BASE_PATH; ?>controllers/catalogo/cmenu.php">
                <i class="fa-solid fa-utensils me-1"></i>Menú
              </a>
            </li>
            <?php if (has_role(['cliente','admin'])): ?>
              <?php 
              $cartCount = 0; 
              if (isset($_SESSION['cart'])) { 
                foreach ($_SESSION['cart'] as $it) { 
                  $cartCount += (int)($it['cantidad'] ?? 0); 
                } 
              } 
              ?>
              <li class="nav-item position-relative">
                <a class="nav-link" href="<?php echo BASE_PATH; ?>controllers/carrito/ccar.php">
                  <i class="fa-solid fa-cart-shopping me-1"></i>Carrito
                  <?php if ($cartCount > 0): ?>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark">
                      <?php echo e($cartCount); ?>
                      <span class="visually-hidden">productos en el carrito</span>
                    </span>
                  <?php endif; ?>
                </a>
              </li>
            <?php endif; ?>
            <?php if (has_role(['admin'])): ?>
              <li class="nav-item">
                <a class="nav-link" href="<?php echo BASE_PATH; ?>controllers/usuarios/cusu.php">
                  <i class="fa-solid fa-users-gear me-1"></i>Usuarios
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="<?php echo BASE_PATH; ?>controllers/catalogo/ccat.php">
                  <i class="fa-solid fa-list me-1"></i>Categorías
                </a>
              </li>
            <?php endif; ?>
            <?php if (has_role(['admin','mesero'])): ?>
              <li class="nav-item">
                <a class="nav-link" href="<?php echo BASE_PATH; ?>controllers/pedidos/cped.php">
                  <i class="fa-solid fa-clipboard-list me-1"></i>Pedidos
                </a>
              </li>
            <?php endif; ?>
            <?php if (has_role(['admin','cajero'])): ?>
              <li class="nav-item">
                <a class="nav-link" href="<?php echo BASE_PATH; ?>controllers/facturacion/cfact.php">
                  <i class="fa-solid fa-file-invoice-dollar me-1"></i>Facturación
                </a>
              </li>
            <?php endif; ?>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa-solid fa-user-circle me-1"></i><?php echo e($_SESSION['nombre'] ?? 'Usuario'); ?>
              </a>
              <ul class="dropdown-menu dropdown-menu-end">
                <li><span class="dropdown-item-text text-muted small"><?php echo e($_SESSION['email'] ?? ''); ?></span></li>
                <li><span class="dropdown-item-text"><span class="badge text-bg-secondary"><?php echo e($_SESSION['rol'] ?? ''); ?></span></span></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                  <a class="dropdown-item text-danger" href="<?php echo BASE_PATH; ?>controllers/auth/cauth.php?a=logout">
                    <i class="fa-solid fa-right-from-bracket me-1"></i>Cerrar sesión
                  </a>
                </li>
              </ul>
            </li>
          <?php else: ?>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo BASE_PATH; ?>views/auth/vlogin.php">
                <i class="fa-solid fa-right-to-bracket me-1"></i>Iniciar sesión
              </a>
            </li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </nav>
</header>
<main class="container py-4">
