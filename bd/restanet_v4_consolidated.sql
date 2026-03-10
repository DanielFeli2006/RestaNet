-- ┌─────────────────────────────────────────────────────────┐
-- │           RestaNet v4 - Script SQL Consolidado         │
-- │       Sistema de Gestión para Restaurantes             │
-- │                                                        │
-- │   Ejecutar este archivo crea la BD desde cero          │
-- │   Compatible con MySQL 5.7+ / MariaDB 10.3+            │
-- └─────────────────────────────────────────────────────────┘

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET FOREIGN_KEY_CHECKS = 0;
SET time_zone = "+00:00";

-- ═══════════════════════════════════════════════════════════
-- RECREAR BASE DE DATOS
-- ═══════════════════════════════════════════════════════════
DROP DATABASE IF EXISTS `restanetV1`;
CREATE DATABASE `restanetV1` 
  CHARACTER SET utf8mb4 
  COLLATE utf8mb4_unicode_ci;
USE `restanetV1`;

-- ═══════════════════════════════════════════════════════════
-- TABLAS PRINCIPALES (sin dependencias)
-- ═══════════════════════════════════════════════════════════

-- -----------------------------------------------------------
-- USUARIOS
-- -----------------------------------------------------------
CREATE TABLE `usuarios` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `password` VARCHAR(255) NOT NULL COMMENT 'Hash SHA-256',
  `rol` ENUM('admin','usuario','mesero','cajero','cliente') DEFAULT 'cliente',
  `activo` TINYINT(1) DEFAULT 1,
  `fecha_creacion` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_usuarios_email` (`email`),
  INDEX `idx_usuarios_rol` (`rol`),
  INDEX `idx_usuarios_activo` (`activo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------
-- CATEGORÍAS
-- -----------------------------------------------------------
CREATE TABLE `categorias` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(100) NOT NULL,
  `descripcion` TEXT DEFAULT NULL,
  `orden` INT(11) DEFAULT 0 COMMENT 'Orden de visualización en menú',
  `activa` TINYINT(1) DEFAULT 1,
  `fecha_creacion` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_categorias_orden` (`orden`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------
-- MESAS
-- -----------------------------------------------------------
CREATE TABLE `mesas` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `numero` INT(11) NOT NULL,
  `capacidad` INT(11) NOT NULL DEFAULT 4,
  `estado` ENUM('disponible','ocupada','reservada','mantenimiento') DEFAULT 'disponible',
  `ubicacion` VARCHAR(50) DEFAULT NULL COMMENT 'Interior, Terraza, VIP, etc.',
  `fecha_creacion` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_mesas_numero` (`numero`),
  INDEX `idx_mesas_estado` (`estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------
-- PRODUCTOS
-- -----------------------------------------------------------
CREATE TABLE `productos` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(100) NOT NULL,
  `descripcion` TEXT DEFAULT NULL,
  `precio` DECIMAL(10,2) NOT NULL,
  `imagen` VARCHAR(255) DEFAULT NULL COMMENT 'Nombre del archivo en img/productos/',
  `categoria_id` INT(11) DEFAULT NULL,
  `disponible` TINYINT(1) DEFAULT 1 COMMENT '1=disponible, 0=agotado',
  `destacado` TINYINT(1) DEFAULT 0 COMMENT '1=mostrar como destacado',
  `fecha_creacion` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_productos_categoria` (`categoria_id`),
  INDEX `idx_productos_disponible` (`disponible`),
  INDEX `idx_productos_destacado` (`destacado`),
  CONSTRAINT `fk_productos_categoria` 
    FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) 
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ═══════════════════════════════════════════════════════════
-- TABLAS CON DEPENDENCIAS
-- ═══════════════════════════════════════════════════════════

-- -----------------------------------------------------------
-- PEDIDOS
-- -----------------------------------------------------------
CREATE TABLE `pedidos` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` INT(11) DEFAULT NULL,
  `mesa_id` INT(11) DEFAULT NULL,
  `estado` ENUM('pendiente','en_preparacion','listo','entregado','completado','cancelado') DEFAULT 'pendiente',
  `notas` TEXT DEFAULT NULL COMMENT 'Instrucciones especiales',
  `fecha_creacion` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_pedidos_usuario` (`usuario_id`),
  INDEX `idx_pedidos_mesa` (`mesa_id`),
  INDEX `idx_pedidos_estado` (`estado`),
  INDEX `idx_pedidos_fecha` (`fecha_creacion`),
  CONSTRAINT `fk_pedidos_usuario` 
    FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) 
    ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_pedidos_mesa` 
    FOREIGN KEY (`mesa_id`) REFERENCES `mesas` (`id`) 
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------
-- DETALLE DE PEDIDO
-- -----------------------------------------------------------
CREATE TABLE `detalle_pedido` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `pedido_id` INT(11) NOT NULL,
  `producto_id` INT(11) NOT NULL,
  `cantidad` INT(11) NOT NULL DEFAULT 1,
  `precio` DECIMAL(10,2) NOT NULL COMMENT 'Precio unitario al momento del pedido',
  `notas` VARCHAR(255) DEFAULT NULL COMMENT 'Notas específicas del producto',
  PRIMARY KEY (`id`),
  INDEX `idx_detalle_pedido` (`pedido_id`),
  INDEX `idx_detalle_producto` (`producto_id`),
  CONSTRAINT `fk_detalle_pedido` 
    FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) 
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_detalle_producto` 
    FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) 
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------
-- FACTURAS
-- -----------------------------------------------------------
CREATE TABLE `facturas` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `pedido_id` INT(11) NOT NULL,
  `subtotal` DECIMAL(10,2) NOT NULL,
  `impuestos` DECIMAL(10,2) NOT NULL COMMENT 'IVA 19%',
  `total` DECIMAL(10,2) NOT NULL,
  `metodo_pago` ENUM('efectivo','tarjeta','transferencia','otro') DEFAULT 'efectivo',
  `token_acceso` VARCHAR(64) DEFAULT NULL COMMENT 'Token para acceso sin sesión',
  `token_expiracion` DATETIME DEFAULT NULL COMMENT 'Expiración del token',
  `estado` ENUM('pendiente','pagada','anulada') DEFAULT 'pendiente',
  `notas` TEXT DEFAULT NULL,
  `fecha_creacion` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_facturas_pedido` (`pedido_id`),
  INDEX `idx_facturas_token` (`token_acceso`),
  INDEX `idx_facturas_fecha` (`fecha_creacion`),
  CONSTRAINT `fk_facturas_pedido` 
    FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) 
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------
-- RESTABLECIMIENTO DE CONTRASEÑA
-- -----------------------------------------------------------
CREATE TABLE `password_resets` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(255) NOT NULL,
  `token` VARCHAR(128) NOT NULL,
  `expires_at` DATETIME NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `used` TINYINT(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  INDEX `idx_resets_email` (`email`),
  INDEX `idx_resets_token` (`token`),
  INDEX `idx_resets_expires` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ═══════════════════════════════════════════════════════════
-- DATOS INICIALES
-- ═══════════════════════════════════════════════════════════

-- -----------------------------------------------------------
-- USUARIOS (contraseñas: admin123, mesero123, cajero123, cliente123)
-- -----------------------------------------------------------
INSERT INTO `usuarios` (`nombre`, `email`, `password`, `rol`) VALUES
('Administrador', 'admin@restanet.local', '5df8888ae023f6a22820fc01a6201ecc5934943861b100ab18e8707fddf80686', 'admin'),
('Mesero Principal', 'mesero@restanet.local', 'ef92b5831dae2a1d70ca14567e686d7f2df39e32f1476d8d30518a79bcf72341', 'mesero'),
('Cajero Principal', 'cajero@restanet.local', 'a349358c8a5ef21e10a5cd90be3711a2524238d07ddfd5df664401dc385178a1', 'cajero'),
('Cliente Demo', 'cliente@restanet.local', '214bc26fbdf87cb344631381e81c9415cfd3d6aa9249d04bb8d9ad835b2deb13', 'cliente');

-- -----------------------------------------------------------
-- CATEGORÍAS
-- -----------------------------------------------------------
INSERT INTO `categorias` (`nombre`, `descripcion`, `orden`) VALUES
('Entradas', 'Pequeños platos para iniciar la experiencia gastronómica', 1),
('Platos Fuertes', 'Selección principal del menú con las mejores preparaciones', 2),
('Bebidas', 'Refrescantes, calientes y especiales de la casa', 3),
('Postres', 'Dulces finales para cerrar con broche de oro', 4);

-- -----------------------------------------------------------
-- MESAS (10 mesas con variadas capacidades)
-- -----------------------------------------------------------
INSERT INTO `mesas` (`numero`, `capacidad`, `ubicacion`) VALUES
(1, 4, 'Interior'),
(2, 2, 'Interior'),
(3, 6, 'Interior'),
(4, 4, 'Terraza'),
(5, 2, 'Terraza'),
(6, 8, 'Interior'),
(7, 4, 'Interior'),
(8, 2, 'Barra'),
(9, 6, 'VIP'),
(10, 4, 'Terraza');

-- -----------------------------------------------------------
-- PRODUCTOS
-- -----------------------------------------------------------
INSERT INTO `productos` (`nombre`, `descripcion`, `precio`, `categoria_id`, `destacado`) VALUES
-- Entradas
('Bruschetta Clásica', 'Pan artesanal tostado con tomate fresco, albahaca y aceite de oliva extra virgen', 8500.00, 1, 0),
('Sopa del Día', 'Preparación casera fresca con ingredientes de temporada', 6000.00, 1, 0),
('Aros de Cebolla', 'Crujientes aros de cebolla con salsa especial de la casa', 7500.00, 1, 0),
-- Platos Fuertes
('Filete a la Parrilla', 'Corte premium de res a la parrilla con guarnición de vegetales', 28900.00, 2, 1),
('Pasta Alfredo', 'Fettuccine en cremosa salsa de queso parmesano', 15000.00, 2, 0),
('Pollo a la Plancha', 'Pechuga de pollo jugosa con ensalada y papa al horno', 18500.00, 2, 0),
('Salmón Grillado', 'Filete de salmón con salsa de eneldo y limón', 32000.00, 2, 1),
-- Bebidas
('Limonada Natural', 'Refrescante limonada con menta fresca', 4000.00, 3, 0),
('Café Espresso', 'Café intenso recién preparado con granos seleccionados', 3500.00, 3, 0),
('Jugo Natural', 'Variedad de frutas frescas de temporada', 5000.00, 3, 0),
('Cerveza Artesanal', 'Selección de cervezas locales', 8000.00, 3, 0),
-- Postres
('Cheesecake NY', 'Tarta de queso estilo New York con salsa de frutos rojos', 9500.00, 4, 1),
('Brownie con Helado', 'Brownie de chocolate intenso con helado de vainilla', 8500.00, 4, 0),
('Tiramisú', 'Postre italiano clásico con café y mascarpone', 10000.00, 4, 0);

-- ═══════════════════════════════════════════════════════════
-- TRIGGERS
-- ═══════════════════════════════════════════════════════════

DELIMITER //

-- Actualizar fecha_actualizacion en pedidos automáticamente
CREATE TRIGGER trg_pedidos_update 
BEFORE UPDATE ON `pedidos`
FOR EACH ROW
BEGIN
  SET NEW.fecha_actualizacion = CURRENT_TIMESTAMP;
END//

-- Actualizar fecha_actualizacion en productos automáticamente
CREATE TRIGGER trg_productos_update 
BEFORE UPDATE ON `productos`
FOR EACH ROW
BEGIN
  SET NEW.fecha_actualizacion = CURRENT_TIMESTAMP;
END//

-- Actualizar fecha_actualizacion en mesas automáticamente
CREATE TRIGGER trg_mesas_update 
BEFORE UPDATE ON `mesas`
FOR EACH ROW
BEGIN
  SET NEW.fecha_actualizacion = CURRENT_TIMESTAMP;
END//

DELIMITER ;

-- ═══════════════════════════════════════════════════════════
-- VISTAS ÚTILES
-- ═══════════════════════════════════════════════════════════

-- Vista de productos con categoría
CREATE OR REPLACE VIEW `v_productos_catalogo` AS
SELECT 
  p.id,
  p.nombre,
  p.descripcion,
  p.precio,
  p.imagen,
  p.disponible,
  p.destacado,
  c.id AS categoria_id,
  c.nombre AS categoria,
  p.fecha_creacion
FROM productos p
LEFT JOIN categorias c ON p.categoria_id = c.id
WHERE p.disponible = 1
ORDER BY c.orden, p.nombre;

-- Vista de pedidos con usuario y mesa
CREATE OR REPLACE VIEW `v_pedidos_completos` AS
SELECT 
  ped.id,
  ped.estado,
  ped.notas,
  ped.fecha_creacion,
  u.id AS usuario_id,
  u.nombre AS cliente,
  u.email AS cliente_email,
  m.id AS mesa_id,
  m.numero AS mesa,
  (SELECT SUM(dp.cantidad * dp.precio) 
   FROM detalle_pedido dp WHERE dp.pedido_id = ped.id) AS total_pedido
FROM pedidos ped
LEFT JOIN usuarios u ON ped.usuario_id = u.id
LEFT JOIN mesas m ON ped.mesa_id = m.id
ORDER BY ped.fecha_creacion DESC;

-- Vista de facturas con información completa
CREATE OR REPLACE VIEW `v_facturas_completas` AS
SELECT 
  f.id,
  f.pedido_id,
  f.subtotal,
  f.impuestos,
  f.total,
  f.metodo_pago,
  f.fecha_creacion,
  u.nombre AS cliente,
  u.email AS cliente_email,
  m.numero AS mesa
FROM facturas f
INNER JOIN pedidos p ON f.pedido_id = p.id
LEFT JOIN usuarios u ON p.usuario_id = u.id
LEFT JOIN mesas m ON p.mesa_id = m.id
ORDER BY f.fecha_creacion DESC;

-- ═══════════════════════════════════════════════════════════
-- FINALIZAR
-- ═══════════════════════════════════════════════════════════
SET FOREIGN_KEY_CHECKS = 1;
COMMIT;

-- ┌─────────────────────────────────────────────────────────┐
-- │              INSTALACIÓN COMPLETADA                    │
-- │                                                        │
-- │   Usuarios creados (contraseñas):                      │
-- │   - admin@restanet.local (admin123)                    │
-- │   - mesero@restanet.local (mesero123)                  │  
-- │   - cajero@restanet.local (cajero123)                  │
-- │   - cliente@restanet.local (cliente123)                │
-- │                                                        │
-- │   Para verificar: SELECT * FROM usuarios;              │
-- └─────────────────────────────────────────────────────────┘
