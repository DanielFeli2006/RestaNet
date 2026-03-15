-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 15-03-2026 a las 18:02:02
-- Versión del servidor: 11.4.10-MariaDB-cll-lve
-- Versión de PHP: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `restydvp_restanet_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `orden` int(11) DEFAULT 0 COMMENT 'Orden de visualización en menú',
  `activa` tinyint(1) DEFAULT 1,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id`, `nombre`, `descripcion`, `orden`, `activa`, `fecha_creacion`) VALUES
(1, 'Entradas', 'Pequeños platos para iniciar la experiencia gastronómica', 1, 1, '2026-03-07 19:36:12'),
(2, 'Platos Fuertes', 'Selección principal del menú con las mejores preparaciones', 2, 1, '2026-03-07 19:36:12'),
(3, 'Bebidas', 'Refrescantes, calientes y especiales de la casa', 3, 1, '2026-03-07 19:36:12'),
(4, 'Postres', 'Dulces finales para cerrar con broche de oro', 4, 1, '2026-03-07 19:36:12');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_pedido`
--

CREATE TABLE `detalle_pedido` (
  `id` int(11) NOT NULL,
  `pedido_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL DEFAULT 1,
  `precio` decimal(10,2) NOT NULL COMMENT 'Precio unitario al momento del pedido',
  `notas` varchar(255) DEFAULT NULL COMMENT 'Notas específicas del producto'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `detalle_pedido`
--

INSERT INTO `detalle_pedido` (`id`, `pedido_id`, `producto_id`, `cantidad`, `precio`, `notas`) VALUES
(15, 8, 9, 1, 3500.00, NULL),
(16, 8, 13, 1, 8500.00, NULL),
(17, 11, 9, 1, 3500.00, NULL),
(18, 11, 11, 1, 8000.00, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `facturas`
--

CREATE TABLE `facturas` (
  `id` int(11) NOT NULL,
  `pedido_id` int(11) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `impuestos` decimal(10,2) NOT NULL COMMENT 'IVA 19%',
  `total` decimal(10,2) NOT NULL,
  `metodo_pago` enum('efectivo','tarjeta','transferencia','otro') DEFAULT 'efectivo',
  `token_acceso` varchar(64) DEFAULT NULL COMMENT 'Token para acceso sin sesión',
  `codigo_validacion` varchar(20) DEFAULT NULL COMMENT 'Codigo alfanumerico para validar factura',
  `token_expiracion` datetime DEFAULT NULL,
  `notas` text DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `estado` enum('pendiente','pagada','anulada') DEFAULT 'pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `facturas`
--

INSERT INTO `facturas` (`id`, `pedido_id`, `subtotal`, `impuestos`, `total`, `metodo_pago`, `token_acceso`, `codigo_validacion`, `token_expiracion`, `notas`, `fecha_creacion`, `estado`) VALUES
(1, 8, 12000.00, 2280.00, 14280.00, 'efectivo', '1047b06a7b753957f3fdeff1922aa0d391e91a7ec14e9813cb596dabd222a22d', 'e8c2d72b1fae', '2026-04-09 11:20:51', NULL, '2026-03-10 16:20:51', 'pagada'),
(2, 11, 11500.00, 2185.00, 13685.00, 'efectivo', 'f9bedb956f1f4f1eb2f9eac1c419a4664508a4f927d617f50a771ec2116fb287', 'e8c2d9e41fae', '2026-04-09 19:47:23', NULL, '2026-03-11 00:47:23', 'pendiente');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mesas`
--

CREATE TABLE `mesas` (
  `id` int(11) NOT NULL,
  `numero` int(11) NOT NULL,
  `capacidad` int(11) NOT NULL DEFAULT 4,
  `estado` enum('disponible','ocupada','reservada','mantenimiento') DEFAULT 'disponible',
  `ubicacion` varchar(50) DEFAULT NULL COMMENT 'Interior, Terraza, VIP, etc.',
  `notas` text DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `mesas`
--

INSERT INTO `mesas` (`id`, `numero`, `capacidad`, `estado`, `ubicacion`, `notas`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(1, 1, 4, 'disponible', 'Interior', NULL, '2026-03-07 19:36:12', '2026-03-14 16:07:10'),
(2, 2, 2, 'disponible', 'Interior', NULL, '2026-03-07 19:36:12', '2026-03-14 16:08:12'),
(3, 3, 6, 'disponible', 'Interior', NULL, '2026-03-07 19:36:12', NULL),
(4, 4, 4, 'disponible', 'Terraza', NULL, '2026-03-07 19:36:12', NULL),
(5, 5, 2, 'disponible', 'Terraza', NULL, '2026-03-07 19:36:12', NULL),
(6, 6, 8, 'disponible', 'Interior', NULL, '2026-03-07 19:36:12', NULL),
(7, 7, 4, 'disponible', 'Interior', NULL, '2026-03-07 19:36:12', NULL),
(8, 8, 2, 'disponible', 'Barra', NULL, '2026-03-07 19:36:12', NULL),
(9, 9, 6, 'disponible', 'VIP', NULL, '2026-03-07 19:36:12', NULL),
(10, 10, 4, 'disponible', 'Terraza', NULL, '2026-03-07 19:36:12', NULL);

--
-- Disparadores `mesas`
--
DELIMITER $$
CREATE TRIGGER `trg_mesas_update` BEFORE UPDATE ON `mesas` FOR EACH ROW BEGIN
  SET NEW.fecha_actualizacion = CURRENT_TIMESTAMP;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(128) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `used` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `password_resets`
--

INSERT INTO `password_resets` (`id`, `email`, `token`, `expires_at`, `created_at`, `used`) VALUES
(2, 'admin@restanet.local', '0159a1c42b05154984d5650368111ffa', '2026-03-10 16:28:54', '2026-03-10 16:28:54', 0),
(3, 'danfelixmg2006@proton.me', '18c899ebd3f08c70cc9938b3797421a9', '2026-03-10 20:48:17', '2026-03-10 20:48:17', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `mesa_id` int(11) DEFAULT NULL,
  `estado` enum('pendiente','en_preparacion','listo','entregado','completado','cancelado') DEFAULT 'pendiente',
  `notas` text DEFAULT NULL COMMENT 'Instrucciones especiales',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `pedidos`
--

INSERT INTO `pedidos` (`id`, `usuario_id`, `mesa_id`, `estado`, `notas`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(8, 5, NULL, 'completado', NULL, '2026-03-10 16:20:51', '2026-03-14 16:07:03'),
(9, 2, 1, 'completado', '', '2026-03-10 20:32:26', '2026-03-14 16:07:10'),
(10, 2, 2, 'completado', '', '2026-03-11 00:45:17', '2026-03-14 16:08:12'),
(11, 4, NULL, 'completado', NULL, '2026-03-11 00:47:23', '2026-03-14 16:08:18');

--
-- Disparadores `pedidos`
--
DELIMITER $$
CREATE TRIGGER `trg_pedidos_update` BEFORE UPDATE ON `pedidos` FOR EACH ROW BEGIN
  SET NEW.fecha_actualizacion = CURRENT_TIMESTAMP;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL,
  `imagen` varchar(255) DEFAULT NULL COMMENT 'Nombre del archivo en img/productos/',
  `categoria_id` int(11) DEFAULT NULL,
  `disponible` tinyint(1) DEFAULT 1 COMMENT '1=disponible, 0=agotado',
  `destacado` tinyint(1) DEFAULT 0 COMMENT '1=mostrar como destacado',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `nombre`, `descripcion`, `precio`, `imagen`, `categoria_id`, `disponible`, `destacado`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(1, 'Bruschetta Clásica', 'Pan artesanal tostado con tomate fresco, albahaca y aceite de oliva extra virgen', 8500.00, NULL, 1, 1, 0, '2026-03-07 19:36:12', NULL),
(2, 'Sopa del Día', 'Preparación casera fresca con ingredientes de temporada', 6000.00, NULL, 1, 1, 0, '2026-03-07 19:36:12', NULL),
(3, 'Aros de Cebolla', 'Crujientes aros de cebolla con salsa especial de la casa', 7500.00, NULL, 1, 1, 0, '2026-03-07 19:36:12', NULL),
(4, 'Filete a la Parrilla', 'Corte premium de res a la parrilla con guarnición de vegetales', 28900.00, NULL, 2, 1, 1, '2026-03-07 19:36:12', NULL),
(5, 'Pasta Alfredo', 'Fettuccine en cremosa salsa de queso parmesano', 15000.00, NULL, 2, 1, 0, '2026-03-07 19:36:12', NULL),
(6, 'Pollo a la Plancha', 'Pechuga de pollo jugosa con ensalada y papa al horno', 18500.00, NULL, 2, 1, 0, '2026-03-07 19:36:12', NULL),
(7, 'Salmón Grillado', 'Filete de salmón con salsa de eneldo y limón', 32000.00, NULL, 2, 1, 1, '2026-03-07 19:36:12', NULL),
(8, 'Limonada Natural', 'Refrescante limonada con menta fresca', 4000.00, NULL, 3, 1, 0, '2026-03-07 19:36:12', NULL),
(9, 'Café Espresso', 'Café intenso recién preparado con granos seleccionados', 3500.00, NULL, 3, 1, 0, '2026-03-07 19:36:12', NULL),
(10, 'Jugo Natural', 'Variedad de frutas frescas de temporada', 5000.00, NULL, 3, 1, 0, '2026-03-07 19:36:12', NULL),
(11, 'Cerveza Artesanal', 'Selección de cervezas locales', 8000.00, NULL, 3, 1, 0, '2026-03-07 19:36:12', NULL),
(12, 'Cheesecake NY', 'Tarta de queso estilo New York con salsa de frutos rojos', 9500.00, NULL, 4, 1, 1, '2026-03-07 19:36:12', NULL),
(13, 'Brownie con Helado', 'Brownie de chocolate intenso con helado de vainilla', 8500.00, NULL, 4, 1, 0, '2026-03-07 19:36:12', NULL),
(14, 'Tiramisú', 'Postre italiano clásico con café y mascarpone', 10000.00, NULL, 4, 1, 0, '2026-03-07 19:36:12', NULL);

--
-- Disparadores `productos`
--
DELIMITER $$
CREATE TRIGGER `trg_productos_update` BEFORE UPDATE ON `productos` FOR EACH ROW BEGIN
  SET NEW.fecha_actualizacion = CURRENT_TIMESTAMP;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL COMMENT 'Hash SHA-256',
  `rol` enum('admin','usuario','mesero','cajero','cliente') DEFAULT 'cliente',
  `activo` tinyint(1) DEFAULT 1,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `email`, `password`, `rol`, `activo`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(1, 'Administrador', 'admin@restanet.local', '5df8888ae023f6a22820fc01a6201ecc5934943861b100ab18e8707fddf80686', 'admin', 1, '2026-03-07 19:36:12', NULL),
(2, 'Mesero Principal', 'mesero@restanet.local', 'ef92b5831dae2a1d70ca14567e686d7f2df39e32f1476d8d30518a79bcf72341', 'mesero', 1, '2026-03-07 19:36:12', NULL),
(3, 'Cajero Principal', 'cajero@restanet.local', 'a349358c8a5ef21e10a5cd90be3711a2524238d07ddfd5df664401dc385178a1', 'cajero', 1, '2026-03-07 19:36:12', NULL),
(4, 'Cliente Demo', 'cliente@restanet.local', '214bc26fbdf87cb344631381e81c9415cfd3d6aa9249d04bb8d9ad835b2deb13', 'cliente', 1, '2026-03-07 19:36:12', NULL),
(5, 'Owner', 'danfelixmg2006@proton.me', 'd04fb88dab9219b51c4323106d0ea604b8950c6607dff151e13a1c0285c0086f', 'admin', 1, '2026-03-10 15:31:51', NULL);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_facturas_completas`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_facturas_completas` (
`id` int(11)
,`pedido_id` int(11)
,`subtotal` decimal(10,2)
,`impuestos` decimal(10,2)
,`total` decimal(10,2)
,`metodo_pago` enum('efectivo','tarjeta','transferencia','otro')
,`fecha_creacion` timestamp
,`cliente` varchar(100)
,`cliente_email` varchar(100)
,`mesa` int(11)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_pedidos_completos`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_pedidos_completos` (
`id` int(11)
,`estado` enum('pendiente','en_preparacion','listo','entregado','completado','cancelado')
,`notas` text
,`fecha_creacion` timestamp
,`usuario_id` int(11)
,`cliente` varchar(100)
,`cliente_email` varchar(100)
,`mesa_id` int(11)
,`mesa` int(11)
,`total_pedido` decimal(42,2)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_productos_catalogo`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_productos_catalogo` (
`id` int(11)
,`nombre` varchar(100)
,`descripcion` text
,`precio` decimal(10,2)
,`imagen` varchar(255)
,`disponible` tinyint(1)
,`destacado` tinyint(1)
,`categoria_id` int(11)
,`categoria` varchar(100)
,`fecha_creacion` timestamp
);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_categorias_orden` (`orden`);

--
-- Indices de la tabla `detalle_pedido`
--
ALTER TABLE `detalle_pedido`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_detalle_pedido` (`pedido_id`),
  ADD KEY `idx_detalle_producto` (`producto_id`);

--
-- Indices de la tabla `facturas`
--
ALTER TABLE `facturas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_facturas_pedido` (`pedido_id`),
  ADD UNIQUE KEY `uq_facturas_codigo_validacion` (`codigo_validacion`),
  ADD KEY `idx_facturas_token` (`token_acceso`),
  ADD KEY `idx_facturas_fecha` (`fecha_creacion`);

--
-- Indices de la tabla `mesas`
--
ALTER TABLE `mesas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_mesas_numero` (`numero`),
  ADD KEY `idx_mesas_estado` (`estado`);

--
-- Indices de la tabla `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_resets_email` (`email`),
  ADD KEY `idx_resets_token` (`token`),
  ADD KEY `idx_resets_expires` (`expires_at`);

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_pedidos_usuario` (`usuario_id`),
  ADD KEY `idx_pedidos_mesa` (`mesa_id`),
  ADD KEY `idx_pedidos_estado` (`estado`),
  ADD KEY `idx_pedidos_fecha` (`fecha_creacion`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_productos_categoria` (`categoria_id`),
  ADD KEY `idx_productos_disponible` (`disponible`),
  ADD KEY `idx_productos_destacado` (`destacado`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_usuarios_email` (`email`),
  ADD KEY `idx_usuarios_rol` (`rol`),
  ADD KEY `idx_usuarios_activo` (`activo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `detalle_pedido`
--
ALTER TABLE `detalle_pedido`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `facturas`
--
ALTER TABLE `facturas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `mesas`
--
ALTER TABLE `mesas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_facturas_completas`
--
DROP TABLE IF EXISTS `v_facturas_completas`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_facturas_completas`  AS SELECT `f`.`id` AS `id`, `f`.`pedido_id` AS `pedido_id`, `f`.`subtotal` AS `subtotal`, `f`.`impuestos` AS `impuestos`, `f`.`total` AS `total`, `f`.`metodo_pago` AS `metodo_pago`, `f`.`fecha_creacion` AS `fecha_creacion`, `u`.`nombre` AS `cliente`, `u`.`email` AS `cliente_email`, `m`.`numero` AS `mesa` FROM (((`facturas` `f` join `pedidos` `p` on(`f`.`pedido_id` = `p`.`id`)) left join `usuarios` `u` on(`p`.`usuario_id` = `u`.`id`)) left join `mesas` `m` on(`p`.`mesa_id` = `m`.`id`)) ORDER BY `f`.`fecha_creacion` DESC ;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_pedidos_completos`
--
DROP TABLE IF EXISTS `v_pedidos_completos`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_pedidos_completos`  AS SELECT `ped`.`id` AS `id`, `ped`.`estado` AS `estado`, `ped`.`notas` AS `notas`, `ped`.`fecha_creacion` AS `fecha_creacion`, `u`.`id` AS `usuario_id`, `u`.`nombre` AS `cliente`, `u`.`email` AS `cliente_email`, `m`.`id` AS `mesa_id`, `m`.`numero` AS `mesa`, (select sum(`dp`.`cantidad` * `dp`.`precio`) from `detalle_pedido` `dp` where `dp`.`pedido_id` = `ped`.`id`) AS `total_pedido` FROM ((`pedidos` `ped` left join `usuarios` `u` on(`ped`.`usuario_id` = `u`.`id`)) left join `mesas` `m` on(`ped`.`mesa_id` = `m`.`id`)) ORDER BY `ped`.`fecha_creacion` DESC ;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_productos_catalogo`
--
DROP TABLE IF EXISTS `v_productos_catalogo`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_productos_catalogo`  AS SELECT `p`.`id` AS `id`, `p`.`nombre` AS `nombre`, `p`.`descripcion` AS `descripcion`, `p`.`precio` AS `precio`, `p`.`imagen` AS `imagen`, `p`.`disponible` AS `disponible`, `p`.`destacado` AS `destacado`, `c`.`id` AS `categoria_id`, `c`.`nombre` AS `categoria`, `p`.`fecha_creacion` AS `fecha_creacion` FROM (`productos` `p` left join `categorias` `c` on(`p`.`categoria_id` = `c`.`id`)) WHERE `p`.`disponible` = 1 ORDER BY `c`.`orden` ASC, `p`.`nombre` ASC ;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `detalle_pedido`
--
ALTER TABLE `detalle_pedido`
  ADD CONSTRAINT `fk_detalle_pedido` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_detalle_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `facturas`
--
ALTER TABLE `facturas`
  ADD CONSTRAINT `fk_facturas_pedido` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `fk_pedidos_mesa` FOREIGN KEY (`mesa_id`) REFERENCES `mesas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pedidos_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `fk_productos_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
