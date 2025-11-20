<?php start_secure_session();
// Seguridad HTTP headers básicos
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header("Referrer-Policy: no-referrer-when-downgrade");
header("Permissions-Policy: geolocation=(), microphone=()");
// Content Security Policy: permitir recursos del propio dominio y CDN conocidos
header("Content-Security-Policy: default-src 'self' https:; script-src 'self' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://fonts.googleapis.com https://cdnjs.cloudflare.com; img-src 'self' data: https:; font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com; frame-ancestors 'self';");
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Restanet</title>
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <!-- DataTables Bootstrap 5 CSS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/2.0.3/css/dataTables.bootstrap5.min.css" />
  <!-- Font Awesome (CDN actualizado) -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <!-- Fallback local (si CDN falla) -->
  <style>
    /* Si por algún motivo el CDN no carga, mostramos un indicador sencillo */
    .fa[class*='fa-']::before { font-family: 'Font Awesome 6 Free','Font Awesome 6 Brands',sans-serif; }
  </style>
  <!-- App styles -->
  <link rel="stylesheet" href="<?php echo BASE_PATH; ?>css/style.css">
</head>
<body>
<header class="mb-3 shadow-sm">
  <nav class="navbar navbar-expand-lg navbar-dark gradient-navbar">
    <div class="container">
  <a class="navbar-brand fw-bold" href="<?php echo BASE_PATH; ?>index.php"><i class="fa-solid fa-utensils me-2"></i>Restanet</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="mainNav">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
          <?php if (is_logged_in()): ?>
            <li class="nav-item"><a class="nav-link" href="<?php echo BASE_PATH; ?>index.php"><i class="fa-solid fa-house me-1"></i>Inicio</a></li>
            <li class="nav-item"><a class="nav-link" href="<?php echo BASE_PATH; ?>controllers/catalogo/cmenu.php"><i class="fa-solid fa-utensils me-1"></i>Menú</a></li>
            <?php if (has_role(['cliente','admin'])): ?>
              <?php $cartCount = 0; if (isset($_SESSION['cart'])) { foreach ($_SESSION['cart'] as $it) { $cartCount += (int)($it['cantidad'] ?? 0); } } ?>
              <li class="nav-item position-relative">
                <a class="nav-link" href="<?php echo BASE_PATH; ?>controllers/carrito/ccar.php"><i class="fa-solid fa-cart-shopping me-1"></i>Carrito</a>
                <?php if ($cartCount>0): ?>
                  <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"><?php echo e($cartCount); ?></span>
                <?php endif; ?>
              </li>
            <?php endif; ?>
            <?php if (has_role(['admin'])): ?>
              <li class="nav-item"><a class="nav-link" href="<?php echo BASE_PATH; ?>controllers/usuarios/cusu.php"><i class="fa-solid fa-users-gear me-1"></i>Usuarios</a></li>
              <li class="nav-item"><a class="nav-link" href="<?php echo BASE_PATH; ?>controllers/catalogo/ccat.php"><i class="fa-solid fa-list me-1"></i>Categorías</a></li>
            <?php endif; ?>
            <?php if (has_role(['admin','mesero'])): ?>
              <li class="nav-item"><a class="nav-link" href="<?php echo BASE_PATH; ?>controllers/pedidos/cped.php"><i class="fa-solid fa-clipboard-list me-1"></i>Pedidos</a></li>
            <?php endif; ?>
            <?php if (has_role(['admin','cajero'])): ?>
              <li class="nav-item"><a class="nav-link" href="<?php echo BASE_PATH; ?>controllers/facturacion/cfact.php"><i class="fa-solid fa-file-invoice-dollar me-1"></i>Facturación</a></li>
            <?php endif; ?>
            <li class="nav-item"><a class="nav-link" href="<?php echo BASE_PATH; ?>controllers/auth/cauth.php?a=logout"><i class="fa-solid fa-right-from-bracket me-1"></i>Salir</a></li>
          <?php else: ?>
            <li class="nav-item"><a class="nav-link" href="<?php echo BASE_PATH; ?>views/auth/vlogin.php"><i class="fa-solid fa-right-to-bracket me-1"></i>Login</a></li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </nav>
</header>
<main class="container py-4">
