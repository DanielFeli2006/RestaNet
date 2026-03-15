-- 004_seed_more_data.sql
-- Datos adicionales para pruebas: pedidos completados y factura de ejemplo
USE restanetV1;

-- Pedido completado para el cajero
INSERT INTO pedidos (usuario_id, mesa_id, estado) VALUES (4, 2, 'completado');
SET @pedido_comp = LAST_INSERT_ID();
INSERT INTO detalle_pedido (pedido_id, producto_id, cantidad, precio) VALUES
(@pedido_comp, 3, 1, 18.90),
(@pedido_comp, 5, 2, 4.00);
-- Calcular totales y crear factura sin QR (se puede regenerar desde el sistema)
SET @subtotal = (SELECT SUM(cantidad*precio) FROM detalle_pedido WHERE pedido_id=@pedido_comp);
SET @impuestos = ROUND(@subtotal*0.19,2);
SET @total = ROUND(@subtotal+@impuestos,2);
INSERT INTO facturas (pedido_id, subtotal, impuestos, total, qr_path) VALUES (@pedido_comp, @subtotal, @impuestos, @total, NULL);
