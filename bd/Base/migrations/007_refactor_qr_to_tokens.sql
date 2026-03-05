-- 007_refactor_qr_to_tokens.sql
-- Elimina funcionalidad QR y añade sistema de tokens de acceso seguro para facturas
-- También agrega soporte de imágenes para productos y gestión mejorada de mesas & pedidos
USE restanetV1;

-- =====================================================
-- 1. FACTURAS: Reemplazar QR por tokens de acceso seguro
-- =====================================================
-- Agregar columna de token de acceso con expiración
ALTER TABLE facturas 
    ADD COLUMN token_acceso VARCHAR(64) NULL AFTER qr_path,
    ADD COLUMN token_expiracion DATETIME NULL AFTER token_acceso,
    ADD COLUMN estado ENUM('pendiente','pagada','anulada') DEFAULT 'pendiente' AFTER token_expiracion,
    ADD COLUMN metodo_pago VARCHAR(50) NULL AFTER estado,
    ADD COLUMN notas TEXT NULL AFTER metodo_pago;

-- Índice para búsqueda rápida por token
CREATE UNIQUE INDEX idx_facturas_token ON facturas(token_acceso);

-- Generar tokens para facturas existentes (32 bytes hex = 64 caracteres)
UPDATE facturas SET token_acceso = SHA2(CONCAT(id, pedido_id, NOW(), RAND()), 256), 
                    token_expiracion = DATE_ADD(NOW(), INTERVAL 30 DAY)
WHERE token_acceso IS NULL;

-- Eliminar columna qr_path (ya no se usa)
ALTER TABLE facturas DROP COLUMN qr_path;

-- =====================================================
-- 2. PRODUCTOS: Agregar soporte para imágenes
-- =====================================================
ALTER TABLE productos 
    ADD COLUMN imagen VARCHAR(255) NULL AFTER descripcion,
    ADD COLUMN activo TINYINT(1) DEFAULT 1 AFTER categoria_id,
    ADD COLUMN fecha_actualizacion TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- =====================================================
-- 3. MESAS: Mejoras para gestión completa
-- =====================================================
-- Agregar campos adicionales si no existen
ALTER TABLE mesas 
    ADD COLUMN ubicacion VARCHAR(100) NULL AFTER capacidad,
    ADD COLUMN notas TEXT NULL AFTER ubicacion;

-- =====================================================
-- 4. PEDIDOS: Mejoras para gestión y trazabilidad
-- =====================================================
ALTER TABLE pedidos 
    ADD COLUMN notas TEXT NULL AFTER estado,
    ADD COLUMN fecha_actualizacion TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP;

-- Índice para búsquedas por estado
CREATE INDEX idx_pedidos_estado ON pedidos(estado);
CREATE INDEX idx_pedidos_fecha ON pedidos(fecha_creacion);

-- =====================================================
-- 5. DETALLE_PEDIDO: Agregar notas por item
-- =====================================================
ALTER TABLE detalle_pedido 
    ADD COLUMN notas VARCHAR(255) NULL AFTER precio;

-- =====================================================
-- 6. Log de acceso a facturas (auditoría)
-- =====================================================
CREATE TABLE IF NOT EXISTS factura_accesos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    factura_id INT NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    user_agent VARCHAR(500) NULL,
    fecha_acceso TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (factura_id) REFERENCES facturas(id) ON DELETE CASCADE
);

CREATE INDEX idx_factura_accesos_factura ON factura_accesos(factura_id);
CREATE INDEX idx_factura_accesos_fecha ON factura_accesos(fecha_acceso);
