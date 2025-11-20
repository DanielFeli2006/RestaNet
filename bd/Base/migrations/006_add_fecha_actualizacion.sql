-- bd/Base/migrations/006_add_fecha_actualizacion.sql
ALTER TABLE mesas
    ADD COLUMN fecha_actualizacion TIMESTAMP NULL DEFAULT NULL AFTER fecha_creacion;

CREATE INDEX idx_mesas_estado ON mesas (estado);
ALTER TABLE mesas ADD UNIQUE KEY uq_mesas_numero (numero);