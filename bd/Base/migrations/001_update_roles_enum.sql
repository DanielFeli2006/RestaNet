-- Migra esquema de usuarios.rol para soportar roles del sistema
-- Compatible con base existente de RestaNetBorrador
ALTER TABLE usuarios MODIFY rol ENUM('admin','usuario','mesero','cajero','cliente') DEFAULT 'usuario';
-- Opcional: migrar usuarios existentes con rol 'usuario' a 'cliente'
-- UPDATE usuarios SET rol='cliente' WHERE rol='usuario';
