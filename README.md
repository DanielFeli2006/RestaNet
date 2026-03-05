# Restanet

Sistema de gestión de restaurante (PHP + MySQL) con autenticación por roles, CRUD de usuarios, menú y carrito, gestión de mesas, pedidos y facturación con IVA.

## Funcionalidades clave
- Autenticación con redirección por rol (admin, mesero, cajero, cliente) y protección de rutas.
- Dashboards específicos por rol:
	- Administrador: Usuarios, Categorías/Productos, Mesas, Pedidos, Facturación.
	- Mesero: Órdenes del día y gestión de mesas.
	- Cajero: Facturación y pedidos pendientes.
	- Cliente: Menú, Carrito y pedidos.
- CRUD completo de usuarios con validaciones (email único, contraseña mínima y bloqueo de auto-eliminación).
- Gestión de mesas con capacidad, ubicación y estados (disponible/ocupada).
- Gestión de productos con soporte de imágenes.
- Menú y categorías con datos de ejemplo.
- Carrito: activación de checkout con 2+ ítems, totales con IVA (19%).
- Facturación: cálculo de subtotal, IVA y total; emisión de factura con acceso vía token seguro.
- Acceso público a facturas mediante enlace único (sin requerir inicio de sesión).
- Recursos integrados: Bootstrap 5, DataTables, Font Awesome y Google Fonts.

## Requisitos
- PHP 8.1+
- MySQL/MariaDB
- Composer (para instalar librerías)
- XAMPP en Windows

## Instalación
1. Crear la base de datos y tablas:
   - Ejecuta `bd/Base/Versions/#3/restanetv1.sql` (o la versión más reciente).
   - Ejecuta migraciones en orden numérico desde `bd/Base/migrations/`:
     - `001_update_roles_enum.sql`
     - `002_seed_core_data.sql`
     - `003_add_facturas.sql`
     - `004_seed_more_data.sql`
     - `005_password_resets.sql`
     - `006_add_fecha_actualizacion.sql`
     - `007_refactor_qr_to_tokens.sql`
     - `008_seguridad_mejoras.sql`
2. Ajusta credenciales en `models/config.php` (host, usuario, clave).
3. Instala dependencias de PHP:
   ```powershell
   composer install
   ```
4. Asegúrate de que `img/productos/` tenga permisos de escritura.
5. Abre la app: `http://localhost/restanet/`.

## Credenciales de ejemplo
- Admin: `admin@restanet.local` / `Admin123`
- Mesero: `mesero@restanet.local` / `Mesero123`
- Cajero: `cajero@restanet.local` / `Cajero123`
- Cliente: `cliente@restanet.local` / `Cliente123`

> Nota: Las contraseñas requieren al menos 8 caracteres con mayúscula, minúscula y número.

## Estructura relevante
- `controllers/auth/cauth.php`: login/logout con redirección por rol y rate limit.
- `models/seg.php`: sesiones, verificación de roles, CSRF y helpers de seguridad.
- `controllers/usuarios/cusu.php`: CRUD de usuarios con validaciones robustas.
- `controllers/catalogo/cprd.php`: Gestión de productos con imágenes.
- `controllers/mesas/cmesa.php`: Gestión de mesas del restaurante.
- `controllers/pedidos/cped.php`: Gestión de pedidos con estados.
- `controllers/facturacion/cfact.php`: Facturación con acceso público vía token.
- `controllers/carrito/ccar.php`: Carrito y checkout.

## Acceso público a facturas

Los clientes pueden ver sus facturas sin iniciar sesión usando un enlace único con token:
```
/controllers/facturacion/cfact.php?a=ver_publica&token=<token>
```
- El token se genera al crear la factura
- Válido por 30 días
- Se muestra al completar una compra

## Notas de recursos
- Bootstrap, DataTables y Font Awesome se cargan desde CDN.
- Las imágenes de productos se almacenan en `img/productos/`.

## Troubleshooting
- Verifica que `BASE_PATH` en `models/config.php` coincida con tu ruta en XAMPP.
- Si tienes problemas con imágenes, verifica permisos en `img/productos/`.
- Para errores de sesión, verifica la configuración de cookies.

## Seguridad

Este proyecto implementa:
- Protección CSRF en todos los formularios
- Rate limiting en login y accesos públicos
- Headers de seguridad HTTP
- Validación estricta de entradas
- Sesiones seguras con timeout
- Tokens seguros para acceso sin login

Consulta `CHANGELOG.md` para detalles completos.

