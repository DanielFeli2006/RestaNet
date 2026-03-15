-- 003_add_facturas.sql
-- Agrega tabla facturas y ajusta enum roles removiendo 'usuario'.
USE restanetV1;

-- Actualizar enum roles si aún existe 'usuario'
ALTER TABLE usuarios MODIFY rol ENUM('admin','mesero','cajero','cliente') DEFAULT 'cliente';

CREATE TABLE IF NOT EXISTS facturas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  pedido_id INT NOT NULL,
  subtotal DECIMAL(10,2) NOT NULL,
  impuestos DECIMAL(10,2) NOT NULL,
  total DECIMAL(10,2) NOT NULL,
  qr_path VARCHAR(255),
  fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (pedido_id) REFERENCES pedidos(id)
);

-- Índices adicionales para performance
CREATE INDEX idx_facturas_pedido ON facturas(pedido_id);
