-- Migration 007: Actualizaciones para RestaNet v4
-- Ejecutar en BD existente (seguro para columnas duplicadas)

-- Agregar columna imagen a productos (si no existe)
SET @dbname = DATABASE();
SET @tablename = 'productos';
SET @columnname = 'imagen';
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
   WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = @columnname) > 0,
  'SELECT 1',
  'ALTER TABLE productos ADD COLUMN imagen VARCHAR(255) DEFAULT NULL AFTER precio'
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Agregar token_acceso a facturas (si no existe)
SET @tablename = 'facturas';
SET @columnname = 'token_acceso';
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
   WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = @columnname) > 0,
  'SELECT 1',
  'ALTER TABLE facturas ADD COLUMN token_acceso VARCHAR(64) DEFAULT NULL'
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Agregar token_expira a facturas (si no existe)
SET @columnname = 'token_expira';
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
   WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = @columnname) > 0,
  'SELECT 1',
  'ALTER TABLE facturas ADD COLUMN token_expira DATETIME DEFAULT NULL'
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Eliminar columna qr_path de facturas (ya no se usa)
SET @columnname = 'qr_path';
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
   WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = @columnname) > 0,
  'ALTER TABLE facturas DROP COLUMN qr_path',
  'SELECT 1'
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Índice para token (si no existe)
CREATE INDEX IF NOT EXISTS idx_facturas_token ON facturas (token_acceso);

SELECT 'Migración 007 completada correctamente' AS resultado;
