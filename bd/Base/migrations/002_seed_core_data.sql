-- 002_seed_core_data.sql
-- Semilla inicial de datos para Restanet
-- Ejecutar después de crear el esquema base (restanetV1.sql) y aplicar 001_update_roles_enum.sql

USE restanetV1;

SET FOREIGN_KEY_CHECKS=0;

-- Usuarios (passwords calculados dinámicamente con SHA2(MD5(...),256) para coincidir con Seg::hashPassword)
-- Contraseñas: admin123, mesero123, cajero123, cliente123
INSERT INTO usuarios (nombre, email, password, rol) VALUES
('Administrador', 'admin@restanet.local', SHA2(MD5('admin123'), 256), 'admin'),
('Mesero Principal', 'mesero@restanet.local', SHA2(MD5('mesero123'), 256), 'mesero'),
('Cajero Principal', 'cajero@restanet.local', SHA2(MD5('cajero123'), 256), 'cajero'),
('Cliente Demo', 'cliente@restanet.local', SHA2(MD5('cliente123'), 256), 'cliente');

-- Mesas
INSERT INTO mesas (numero, capacidad, estado) VALUES
(1, 4, 'disponible'),
(2, 2, 'disponible'),
(3, 6, 'disponible');

-- Categorías
INSERT INTO categorias (nombre, descripcion) VALUES
('Entradas', 'Pequeños platos para iniciar'),
('Platos Fuertes', 'Selección principal del menú'),
('Bebidas', 'Refrescantes y calientes'),
('Postres', 'Dulces finales');

-- Productos (asumiendo IDs de categorías en orden de inserción 1..4)
INSERT INTO productos (nombre, descripcion, precio, categoria_id) VALUES
('Bruschetta', 'Pan tostado con tomate y albahaca', 8500, 1),
('Sopa del Día', 'Preparación casera fresca', 6000, 1),
('Filete a la Parrilla', 'Corte premium con guarnición', 18900, 2),
('Pasta Alfredo', 'Pasta en salsa cremosa de queso', 15000, 2),
('Limonada Natural', 'Bebida refrescante de limón', 4000, 3),
('Café Espresso', 'Café intenso recién preparado', 3000, 3),
('Cheesecake', 'Tarta de queso con salsa de frutos rojos', 7500, 4),
('Brownie', 'Chocolate intenso con nueces', 6500, 4);

-- Pedido de ejemplo (usuario cliente y mesa 1)
INSERT INTO pedidos (usuario_id, mesa_id, estado) VALUES (4, 1, 'pendiente');
SET @pedido_demo = LAST_INSERT_ID();

-- Detalle del pedido (selección de productos)
INSERT INTO detalle_pedido (pedido_id, producto_id, cantidad, precio) VALUES
(@pedido_demo, 1, 2, 8.50),  -- Bruschetta
(@pedido_demo, 5, 2, 4.00),  -- Limonada
(@pedido_demo, 7, 1, 7.50);  -- Cheesecake

SET FOREIGN_KEY_CHECKS=1;

-- Verificación rápida (opcional)
-- SELECT * FROM usuarios;
-- SELECT * FROM categorias;
-- SELECT * FROM productos;
-- SELECT * FROM pedidos;
-- SELECT * FROM detalle_pedido;
