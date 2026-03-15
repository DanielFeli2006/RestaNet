# CHANGELOG - Refactorización RestaNet

## Fecha: Marzo 2026

## Resumen de Cambios

Este documento detalla las modificaciones realizadas al proyecto RestaNet como parte de una refactorización integral enfocada en seguridad, eliminación de funcionalidad QR y adición de nuevas características.

---

## 1. Eliminación de Funcionalidad QR

### Archivos/Carpetas Eliminados
- `vendor/bacon/` - Librería bacon-qr-code
- `vendor/endroid/` - Librería endroid QR Code  
- `vendor/dasprid/` - Dependencia de enum para QR
- `img/qr/` - Carpeta de imágenes QR generadas

### Referencias Actualizadas
- `README.md` - Eliminadas todas las menciones a códigos QR
- Migraciones SQL - La columna `qr_path` se mantiene en migraciones antiguas por compatibilidad pero la migración 007 la elimina

---

## 2. Sistema de Tokens para Facturas (Sin Login)

### Implementación
- **Archivo**: `controllers/facturacion/cfact.php`
- Endpoint público: `cfact.php?a=ver_publica&token=<token>`
- Tokens de 64 caracteres hexadecimales (32 bytes)
- Expiración configurable (30 días por defecto)

### Seguridad del Sistema de Tokens
- Validación de formato regex para tokens
- Rate limiting por IP (30 peticiones/minuto)
- Log de accesos en tabla `factura_accesos` para auditoría
- Token único por factura con índice en BD
- Expiración automática verificada en cada acceso

### Vistas Relacionadas
- `views/facturacion/vfact_publica.php` - Vista pública de factura
- `views/facturacion/vfact_error.php` - Vista de errores
- `views/catalogo/vcart_done.php` - Muestra enlace de acceso al completar compra

---

## 3. Gestión de Productos con Imágenes

### Controlador Mejorado
- **Archivo**: `controllers/catalogo/cprd.php`

### Funcionalidades de Imagen
- Subida segura de imágenes (JPEG, PNG, GIF, WebP)
- Validación de tipo MIME real (no solo extensión)
- Verificación de imagen válida con `getimagesize()`
- Límite de tamaño: 5MB
- Nombres de archivo únicos y seguros
- Eliminación automática de imagen al borrar producto
- Opción de eliminar imagen sin subir nueva

### Seguridad de Uploads
```php
// Validaciones implementadas:
- Verificación de errores de upload
- Validación de tamaño máximo
- Validación de tipo MIME real
- Verificación de imagen válida
- Generación de nombre único aleatorio
- Sanitización de nombre de archivo
```

### Archivos Nuevos/Modificados
- `controllers/catalogo/cprd.php` - Controlador mejorado
- `views/catalogo/vprd_form.php` - Formulario con subida de imagen
- `views/catalogo/vprd.php` - Listado con miniaturas
- `views/catalogo/vprd_delete.php` - Confirmación de eliminación (nuevo)
- `img/productos/` - Carpeta para imágenes (nueva)

---

## 4. Gestión de Mesas

### Nuevos Archivos
- `controllers/mesas/cmesa.php` - Controlador completo CRUD
- `views/mesas/vmesas.php` - Vista de listado con grid visual
- `views/mesas/vmesa_form.php` - Formulario crear/editar
- `views/mesas/vmesa_delete.php` - Confirmación eliminación

### Funcionalidades
- Estados: disponible, ocupada, reservada, mantenimiento
- Capacidad y ubicación configurables
- Cambio rápido de estado con AJAX
- Resumen visual de estados
- Protección contra eliminación con pedidos activos
- Actualización automática de estado con pedidos

### Seguridad
- Validación CSRF en todas las operaciones
- Transacciones con bloqueo FOR UPDATE
- Validación de estados contra lista blanca
- Verificación de número único

---

## 5. Mejoras de Seguridad General

### Headers HTTP de Seguridad
Configurados en `models/config.php`:
- `X-Content-Type-Options: nosniff`
- `X-XSS-Protection: 1; mode=block`
- `X-Frame-Options: SAMEORIGIN`
- `Referrer-Policy: strict-origin-when-cross-origin`

### Sesiones Seguras
- Cookie segura con flags httponly, samesite
- Regeneración de ID en login
- Timeout de inactividad configurable
- Bloqueo tras intentos fallidos

### Protección CSRF
- Token CSRF en todas las formas
- Validación obligatoria en operaciones POST
- Helper `csrf_field()` para vistas
- Soporte para token en header (AJAX)

### Validación de Entrada
- Sanitización con `strip_tags()`, `filter_var()`
- Validación de roles contra lista blanca
- Validación estricta de IDs (cast a int)
- Límites de longitud en campos

### Contraseñas
- Mínimo 8 caracteres
- Requiere mayúscula, minúscula y número
- Hash con SHA-256(MD5()) o bcrypt compatible
- Comparación segura con `hash_equals()`

### Rate Limiting
- Login: 10 intentos/minuto por IP
- Facturas públicas: 30 peticiones/minuto
- Index: 300 peticiones/minuto
- Almacenamiento en archivos temporales

### Operaciones de Eliminación
- Requieren método POST (no GET)
- Confirmación en vista separada
- Validación CSRF obligatoria
- Verificación de dependencias

---

## 6. Controladores Mejorados

### cusu.php (Usuarios)
- Validación CSRF en crear/editar/eliminar
- Validación de contraseña robusta
- Prevención de auto-eliminación
- Prevención de cambio de rol propio
- Confirmación de eliminación

### cped.php (Pedidos)
- Estados ampliados: pendiente, en_proceso, completado, cancelado
- Agregar productos a pedido existente
- Actualización automática de estado de mesa
- Filtrado por estado
- Cálculo de totales

### cfact.php (Facturación)
- Acceso público con token seguro
- Actualización de estados con CSRF
- Generación de PDF con Dompdf
- Log de accesos para auditoría

---

## 7. Migraciones SQL

### 007_refactor_qr_to_tokens.sql (existente)
- Elimina columna qr_path
- Agrega token_acceso y token_expiracion
- Mejora tabla productos (imagen, activo)
- Mejora tabla mesas (ubicación, notas)4
- Tabla factura_accesos para auditoría

### 008_seguridad_mejoras.sql (nuevo)
- Estados ampliados para mesas y pedidos
- Índices de rendimiento
- Tabla de auditoría de accesos
- Limpieza de datos huérfanos

---

## 8. Archivos de Configuración

### models/config.php
- Headers de seguridad HTTP
- Configuración de uploads
- Constantes de tokens
- Modo desarrollo/producción

### composer.json
- Solo dependencias necesarias (sin QR)
- PHPMailer para correos
- Dompdf para PDFs

---

## Recomendaciones Adicionales

### Para Producción
1. Cambiar `ENVIRONMENT` a `'production'` en config.php
2. Configurar HTTPS y habilitar header HSTS
3. Cambiar credenciales de base de datos
4. Configurar rotación de logs
5. Implementar backup de imágenes

### Seguridad Adicional Recomendada
1. **Content Security Policy (CSP)**: Implementar header CSP
2. **2FA**: Autenticación de dos factores para admin
3. **Auditoría**: Extender logs de auditoría
4. **WAF**: Firewall de aplicación web
5. **Backup**: Sistema de respaldo automatizado

### Mantenimiento
1. Revisar y actualizar dependencias de Composer
2. Monitorear logs de errores
3. Limpiar tokens expirados periódicamente
4. Revisar accesos a facturas públicas

---

## Estructura Final del Proyecto

```
RestaNet/
├── controllers/
│   ├── admin/cadmin.php
│   ├── auth/cauth.php, creset.php
│   ├── carrito/ccar.php
│   ├── catalogo/ccat.php, cmenu.php, cprd.php
│   ├── facturacion/cfact.php
│   ├── mesas/cmesa.php (NUEVO)
│   ├── pedidos/cped.php
│   └── usuarios/cusu.php
├── views/
│   ├── mesas/ (NUEVO)
│   │   ├── vmesas.php
│   │   ├── vmesa_form.php
│   │   └── vmesa_delete.php
│   └── ...
├── models/
│   ├── config.php (MEJORADO)
│   ├── conexion.php
│   └── seg.php
├── img/
│   └── productos/ (NUEVO)
├── bd/Base/migrations/
│   └── 008_seguridad_mejoras.sql (NUEVO)
└── ...
```

---

*Documento generado como parte de la refactorización del proyecto RestaNet*
