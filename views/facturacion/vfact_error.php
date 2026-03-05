<?php
/**
 * Vista de error para acceso a facturas públicas
 * Muestra mensajes de error amigables sin exponer información sensible
 */
$error_msg = $error_msg ?? 'Ha ocurrido un error al procesar tu solicitud.';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Error - RestaNet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .error-card {
            max-width: 500px;
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }
        .error-icon {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: linear-gradient(135deg, #dc3545, #c82333);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="card error-card border-0 text-center">
            <div class="card-body p-5">
                <div class="error-icon mb-4">
                    <i class="fa-solid fa-exclamation-triangle fa-3x text-white"></i>
                </div>
                
                <h2 class="h4 mb-3 fw-bold text-danger">Acceso no disponible</h2>
                
                <p class="text-muted mb-4">
                    <?php echo e($error_msg); ?>
                </p>
                
                <div class="d-flex gap-2 justify-content-center flex-wrap">
                    <a href="javascript:history.back()" class="btn btn-outline-secondary">
                        <i class="fa-solid fa-arrow-left me-1"></i>Volver
                    </a>
                </div>
            </div>
            
            <div class="card-footer bg-light py-3">
                <small class="text-muted">
                    <i class="fa-solid fa-info-circle me-1"></i>
                    Si crees que esto es un error, contacta al restaurante.
                </small>
            </div>
        </div>
    </div>
</body>
</html>