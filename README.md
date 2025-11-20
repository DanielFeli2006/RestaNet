# Restanet

Sistema de gestión de restaurante (PHP + MySQL) con autenticación por roles, CRUD de usuarios, menú y carrito con generación de código QR, pedidos y facturación con IVA.

## Funcionalidades clave
- Autenticación con redirección por rol (admin, mesero, cajero, cliente) y protección de rutas.
- Dashboards específicos por rol:
	- Administrador: Usuarios, Categorías/Productos, Pedidos, Facturación.
	- Mesero: Órdenes del día y gestión de mesas (placeholder).
	- Cajero: Facturación y pedidos pendientes.
	- Cliente: Menú, Carrito y pedidos.
- CRUD completo de usuarios con validaciones (email único, contraseña mínima y bloqueo de auto-eliminación).
- Menú y categorías con datos de ejemplo.
- Carrito: activación de checkout con 2+ ítems, totales con IVA (19%) y generación de QR del pedido.
- Facturación: cálculo de subtotal, IVA y total; emisión de factura y visualización con QR.
- Recursos integrados: Bootstrap 5, DataTables, Font Awesome y Google Fonts.

## Requisitos
- PHP 8+
- MySQL/MariaDB
- Composer (para instalar librerías)
- XAMPP en Windows

## Instalación
1. Crear la base de datos y tablas:
	 - Ejecuta `bd/restanetV1.sql`.
	 - Ejecuta migraciones en orden: `bd/migrations/001_update_roles_enum.sql`, `003_add_facturas.sql`, `002_seed_core_data.sql`, `004_seed_more_data.sql`.
2. Ajusta credenciales en `models/config.php` (host, usuario, clave).
3. Instala dependencias de PHP:
	 - En la carpeta del proyecto, ejecuta (PowerShell):
		 ```powershell
		 composer install
		 ```
4. Abre la app: `http://localhost/Proyects/restanet/`.

## Credenciales de ejemplo
- Admin: `admin@restanet.local` / `admin123`
- Mesero: `mesero@restanet.local` / `mesero123`
- Cajero: `cajero@restanet.local` / `cajero123`
- Cliente: `cliente@restanet.local` / `cliente123`

## Estructura relevante
- `controllers/auth/cauth.php`: login/logout con redirección por rol y rate limit básico.
- `models/seg.php`: sesiones, verificación de roles y helpers `require_exact_role`, `can_access`.
- `controllers/usuarios/cusu.php`: CRUD con validaciones.
- `controllers/catalogo/cmenu.php` y `views/catalogo/vmenu.php`: Menú para clientes.
- `controllers/carrito/ccar.php`, `views/catalogo/vcart*.php`: Carrito, checkout y QR.
- `controllers/facturacion/cfact.php`, `views/facturacion/vfact*.php`: Listado y detalle de facturas.
- `models/qr.php`: generación de QR (Endroid QR Code).

## Notas de recursos
- Bootstrap, DataTables y Font Awesome se cargan desde CDN en `views/layout/cabezote.php` y `views/layout/pie.php`.
- WebFonts locales en `webfonts/` (no obligatorio si usas CDN).
- Comentarios `// TODO: AGREGAR IMAGEN - [descripción]` ubicados en vistas que requieren imágenes.

## Troubleshooting
- Si no ves el QR en checkout o factura, asegúrate de ejecutar `composer install`. La imagen se almacena en `img/qr/`.
- Verifica que `BASE_PATH` en `models/config.php` coincida con tu ruta en XAMPP (por defecto `/Proyects/restanet/`).

## Restablecimiento de contraseña (nuevo)

Se agregó un flujo para "Olvidé mi contraseña" que usa tokens guardados en la tabla `password_resets`.

Pasos para habilitar:
- Ejecutar la migración SQL: `bd/migrations/005_password_resets.sql` en tu base de datos.
- Configurar envío de emails si deseas que el sistema mande correos automáticos. Puedes configurar PHPMailer en `models/config.php` o modificar `controllers/auth/creset.php` para ajustar SMTP.

Si no configuras SMTP, el sistema no fallará: mostrará un mensaje genérico y el token quedará creado en la base de datos (puedes recuperar el enlace manualmente para pruebas).

