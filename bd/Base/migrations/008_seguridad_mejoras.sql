-- 008_seguridad_mejoras.sql
-- Mejoras de seguridad y consolidación de esquema
-- Ejecutar DESPUÉS de las migraciones anteriores
USE restanetV1;

-- =====================================================
-- 1. MESAS: Asegurar estructura completa
-- =====================================================
-- Verificar y agregar columnas si no existen
ALTER TABLE mesas 
    MODIFY COLUMN estado ENUM('disponible','ocupada','reservada','mantenimiento') DEFAULT 'disponible';

-- Agregar estado reservada y mantenimiento si no existen
-- Nota: MySQL no permite agregar valores a ENUM existente sin MODIFY

-- =====================================================
-- 2. PRODUCTOS: Asegurar campo activo tiene valor por defecto
-- =====================================================
UPDATE productos SET activo = 1 WHERE activo IS NULL;

-- =====================================================
-- 3. PEDIDOS: Mejorar estados
-- =====================================================
ALTER TABLE pedidos 
    MODIFY COLUMN estado ENUM('pendiente','en_proceso','completado','cancelado') DEFAULT 'pendiente';

-- =====================================================
-- 4. SEGURIDAD: Índices adicionales para consultas frecuentes
-- =====================================================
-- Índice para búsqueda de usuarios por email (login)
CREATE INDEX IF NOT EXISTS idx_usuarios_email ON usuarios(email);

-- Índice para tokens de facturas
CREATE INDEX IF NOT EXISTS idx_facturas_token ON facturas(token_acceso);

-- Índice para búsqueda de pedidos por usuario
CREATE INDEX IF NOT EXISTS idx_pedidos_usuario ON pedidos(usuario_id);

-- Índice para detalle de pedidos
CREATE INDEX IF NOT EXISTS idx_detalle_pedido ON detalle_pedido(pedido_id);

-- =====================================================
-- 5. LIMPIEZA: Eliminar datos huérfanos
-- =====================================================
-- Eliminar detalles de pedidos que referencien productos eliminados
DELETE FROM detalle_pedido WHERE producto_id NOT IN (SELECT id FROM productos);

-- =====================================================
-- 6. LOGS DE AUDITORÍA (si no existe)
-- =====================================================
CREATE TABLE IF NOT EXISTS auditoria_accesos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NULL,
    ip_address VARCHAR(45) NOT NULL,
    accion VARCHAR(100) NOT NULL,
    detalles TEXT NULL,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
);

CREATE INDEX idx_auditoria_fecha ON auditoria_accesos(fecha);
CREATE INDEX idx_auditoria_usuario ON auditoria_accesos(usuario_id);
