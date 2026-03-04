/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `caja_apertura` (
  `idcaja_a` int(11) NOT NULL AUTO_INCREMENT,
  `fecha` date NOT NULL,
  `caja` varchar(255) NOT NULL,
  `turno` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish2_ci NOT NULL,
  `hora` varchar(255) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `usuario` varchar(255) NOT NULL,
  `estado` enum('Abierto','Cerrado') NOT NULL,
  PRIMARY KEY (`idcaja_a`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `caja_cierre` (
  `idcaja_c` int(11) NOT NULL AUTO_INCREMENT,
  `fecha` date NOT NULL,
  `caja` varchar(255) NOT NULL,
  `turno` varchar(255) NOT NULL,
  `hora` varchar(255) NOT NULL,
  `usuario` varchar(255) NOT NULL,
  `pagos_efectivo` decimal(10,2) NOT NULL,
  `pagos_tarjeta` decimal(10,2) NOT NULL,
  `pagos_deposito` varchar(255) NOT NULL,
  `total_venta` decimal(10,2) NOT NULL,
  `monto_a` decimal(10,2) NOT NULL,
  `caja_sistema` decimal(10,2) NOT NULL,
  `efectivo_caja` decimal(10,2) NOT NULL,
  `diferencia` decimal(10,2) NOT NULL,
  PRIMARY KEY (`idcaja_c`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `carrito` (
  `idproducto` int(11) NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `presentacion` varchar(255) NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `valor_unitario` decimal(10,2) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `igv` decimal(10,2) NOT NULL,
  `porcentaje_igv` decimal(10,2) NOT NULL,
  `valor_total` decimal(10,2) NOT NULL,
  `importe_total` decimal(10,2) NOT NULL,
  `operacion` varchar(100) NOT NULL,
  `session_id` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `carritoc` (
  `idproducto` int(11) NOT NULL,
  `descripcion` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish2_ci NOT NULL,
  `presentacion` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish2_ci NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio` decimal(18,2) NOT NULL,
  `importe` decimal(18,2) NOT NULL,
  `session_id` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `categoria` (
  `idcategoria` int(11) NOT NULL AUTO_INCREMENT,
  `forma_farmaceutica` varchar(255) NOT NULL,
  `ff_simplificada` varchar(255) NOT NULL,
  PRIMARY KEY (`idcategoria`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `certificado` (
  `idcertificado` int(11) NOT NULL AUTO_INCREMENT,
  `certificado` varchar(255) DEFAULT NULL,
  `clave_certificado` varchar(255) DEFAULT NULL,
  `estado` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`idcertificado`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish2_ci;

CREATE TABLE `cliente` (
  `idcliente` int(11) NOT NULL AUTO_INCREMENT,
  `nombres` varchar(255) NOT NULL,
  `id_tipo_docu` int(11) NOT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `nrodoc` varchar(30) DEFAULT 'NULL',
  `tipo` enum('cliente','laboratorio') NOT NULL,
  PRIMARY KEY (`idcliente`),
  KEY `FK_cliente_tipo_documento_idtipo_docu` (`id_tipo_docu`),
  CONSTRAINT `FK_cliente_tipo_documento_idtipo_docu` FOREIGN KEY (`id_tipo_docu`) REFERENCES `tipo_documento` (`idtipo_docu`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=87 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `compra` (
  `idcompra` int(11) NOT NULL AUTO_INCREMENT,
  `idcliente` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `subtotal` decimal(18,2) NOT NULL,
  `igv` decimal(18,2) NOT NULL,
  `total` decimal(18,2) NOT NULL,
  `docu` varchar(30) NOT NULL,
  `num_docu` char(50) NOT NULL,
  PRIMARY KEY (`idcompra`),
  KEY `FK_compra_proveedor_idprovedor` (`idcliente`),
  CONSTRAINT `FK_compra_cliente_idcliente` FOREIGN KEY (`idcliente`) REFERENCES `cliente` (`idcliente`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `confi_backup` (
  `idbackup` int(11) NOT NULL,
  `host` varchar(50) NOT NULL,
  `db_name` varchar(50) NOT NULL,
  `user` varchar(50) NOT NULL,
  `pass` varchar(50) NOT NULL,
  PRIMARY KEY (`idbackup`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish2_ci;

CREATE TABLE `configuracion` (
  `idconfi` int(11) NOT NULL AUTO_INCREMENT,
  `logo` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `tipodoc` char(1) NOT NULL,
  `ruc` varchar(255) NOT NULL,
  `razon_social` varchar(255) NOT NULL,
  `nombre_comercial` varchar(255) NOT NULL,
  `direccion` varchar(255) NOT NULL,
  `pais` varchar(255) NOT NULL,
  `departamento` varchar(255) NOT NULL,
  `provincia` varchar(255) NOT NULL,
  `distrito` varchar(255) NOT NULL DEFAULT 'NULL',
  `ubigeo` char(6) NOT NULL,
  `usuario_sol` varchar(50) NOT NULL,
  `clave_sol` varchar(50) NOT NULL,
  `simbolo_moneda` char(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `impuesto` decimal(10,2) NOT NULL,
  PRIMARY KEY (`idconfi`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci;

CREATE TABLE `detallecompra` (
  `idcompra` int(11) NOT NULL,
  `idproducto` int(11) NOT NULL,
  `cantidad` decimal(18,2) NOT NULL,
  `precio` decimal(18,2) NOT NULL,
  `importe` decimal(18,2) NOT NULL,
  KEY `FK_detallecompra_productos_idproducto` (`idproducto`),
  KEY `FK_detallecompra_compra_idcompra` (`idcompra`),
  CONSTRAINT `FK_detallecompra_compra_idcompra` FOREIGN KEY (`idcompra`) REFERENCES `compra` (`idcompra`),
  CONSTRAINT `FK_detallecompra_productos_idproducto` FOREIGN KEY (`idproducto`) REFERENCES `productos` (`idproducto`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `detalleventa` (
  `idventa` int(11) NOT NULL,
  `item` int(11) NOT NULL,
  `idproducto` int(11) NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `valor_unitario` decimal(10,2) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `igv` decimal(10,2) NOT NULL,
  `porcentaje_igv` decimal(10,2) NOT NULL,
  `valor_total` decimal(10,2) NOT NULL,
  `importe_total` decimal(10,2) NOT NULL,
  `descuento` decimal(18,2) NOT NULL,
  KEY `FK_detalleventa_productos_idproducto` (`idproducto`),
  KEY `FK_detalleventa_venta_idventa` (`idventa`),
  CONSTRAINT `FK_detalleventa_productos_idproducto` FOREIGN KEY (`idproducto`) REFERENCES `productos` (`idproducto`),
  CONSTRAINT `FK_detalleventa_venta_idventa` FOREIGN KEY (`idventa`) REFERENCES `venta` (`idventa`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `lote` (
  `idlote` int(11) NOT NULL AUTO_INCREMENT,
  `numero` varchar(255) NOT NULL,
  `fecha_vencimiento` date NOT NULL,
  `idsucu_c` int(11) NOT NULL,
  PRIMARY KEY (`idlote`)
) ENGINE=InnoDB AUTO_INCREMENT=206 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `moneda` (
  `codigo` char(3) NOT NULL,
  `descripcion` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  UNIQUE KEY `codigo` (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

CREATE TABLE `nota_credito` (
  `idnota` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `idconf` bigint(20) unsigned DEFAULT NULL,
  `tipocomp` varchar(10) NOT NULL DEFAULT '07',
  `idcliente` bigint(20) unsigned DEFAULT NULL,
  `idusuario` bigint(20) unsigned DEFAULT NULL,
  `idserie` bigint(20) unsigned DEFAULT NULL,
  `fecha_emision` date NOT NULL,
  `op_gravadas` decimal(12,2) NOT NULL DEFAULT 0.00,
  `op_exoneradas` decimal(12,2) NOT NULL DEFAULT 0.00,
  `op_inafectas` decimal(12,2) NOT NULL DEFAULT 0.00,
  `igv` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `serie_ref` varchar(20) DEFAULT NULL,
  `correlativo_ref` varchar(20) DEFAULT NULL,
  `codmotivo` varchar(10) NOT NULL DEFAULT '01',
  `feestado` varchar(20) DEFAULT NULL,
  `idventa` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`idnota`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `pago_venta` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `idventa` int(10) unsigned NOT NULL,
  `tipo_pago` varchar(50) NOT NULL,
  `monto` decimal(14,2) NOT NULL,
  `recibo` decimal(14,2) DEFAULT NULL,
  `numope` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pago_venta_idventa_index` (`idventa`),
  KEY `pago_venta_idventa_tipo_pago_index` (`idventa`,`tipo_pago`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `presentacion` (
  `idpresentacion` int(11) NOT NULL AUTO_INCREMENT,
  `presentacion` varchar(255) NOT NULL,
  `idsucu_c` int(11) NOT NULL,
  PRIMARY KEY (`idpresentacion`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `producto_similar` (
  `idp_similar` int(11) NOT NULL AUTO_INCREMENT,
  `idproducto` int(11) NOT NULL,
  `producto` varchar(255) NOT NULL,
  `presentacion` varchar(255) NOT NULL,
  PRIMARY KEY (`idp_similar`),
  KEY `FK_producto_similar_productos_idproducto` (`idproducto`),
  CONSTRAINT `FK_producto_similar_productos_idproducto` FOREIGN KEY (`idproducto`) REFERENCES `productos` (`idproducto`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `productos` (
  `idproducto` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(255) DEFAULT NULL,
  `idlote` int(11) NOT NULL,
  `descripcion` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish2_ci NOT NULL,
  `tipo` varchar(50) NOT NULL,
  `stock` int(11) NOT NULL,
  `stockminimo` int(11) NOT NULL,
  `precio_compra` decimal(18,2) NOT NULL,
  `precio_venta` decimal(10,2) NOT NULL,
  `descuento` decimal(18,2) DEFAULT NULL,
  `ventasujeta` varchar(50) NOT NULL,
  `fecha_registro` date NOT NULL,
  `reg_sanitario` varchar(255) DEFAULT NULL,
  `idcategoria` int(11) NOT NULL,
  `idpresentacion` int(11) NOT NULL,
  `idcliente` int(11) NOT NULL,
  `idsintoma` int(11) NOT NULL,
  `idunidad` int(11) NOT NULL,
  `idtipoaf` int(11) NOT NULL,
  `estado` varchar(50) NOT NULL,
  `tipo_precio` char(2) NOT NULL,
  PRIMARY KEY (`idproducto`),
  KEY `FK_productos_presentacion_idpresentacion` (`idpresentacion`),
  KEY `FK_productos_categoria_idcategoria` (`idcategoria`),
  KEY `FK_productos_laboratorio_idlab` (`idcliente`),
  KEY `FK_productos_sintoma_idsintoma` (`idsintoma`),
  KEY `FK_productos_lote_idlote` (`idlote`),
  KEY `FK_productos_unidad_iduni` (`idunidad`),
  KEY `FK_productos` (`idtipoaf`),
  CONSTRAINT `FK_productos` FOREIGN KEY (`idtipoaf`) REFERENCES `tipo_afectacion` (`idtipoa`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_productos_categoria_idcategoria` FOREIGN KEY (`idcategoria`) REFERENCES `categoria` (`idcategoria`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_productos_cliente_idcliente` FOREIGN KEY (`idcliente`) REFERENCES `cliente` (`idcliente`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_productos_lote_idlote` FOREIGN KEY (`idlote`) REFERENCES `lote` (`idlote`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_productos_presentacion_idpresentacion` FOREIGN KEY (`idpresentacion`) REFERENCES `presentacion` (`idpresentacion`),
  CONSTRAINT `FK_productos_sintoma_idsintoma` FOREIGN KEY (`idsintoma`) REFERENCES `sintoma` (`idsintoma`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_productos_unidad_iduni` FOREIGN KEY (`idunidad`) REFERENCES `unidad` (`iduni`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1929 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `serie` (
  `idserie` int(11) NOT NULL,
  `tipocomp` char(2) DEFAULT NULL,
  `serie` char(4) DEFAULT NULL,
  `correlativo` int(11) DEFAULT NULL,
  PRIMARY KEY (`idserie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `sintoma` (
  `idsintoma` int(11) NOT NULL AUTO_INCREMENT,
  `sintoma` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `idsucu_c` int(11) NOT NULL,
  PRIMARY KEY (`idsintoma`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `tipo_afectacion` (
  `idtipoa` int(11) NOT NULL,
  `codigo` char(2) NOT NULL,
  `descripcion` varchar(50) DEFAULT NULL,
  `codigo_afectacion` char(4) DEFAULT NULL,
  `nombre_afectacion` char(3) DEFAULT NULL,
  `tipo_afectacion` char(3) DEFAULT NULL,
  PRIMARY KEY (`idtipoa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `tipo_comprobante` (
  `codigo` char(2) NOT NULL,
  `descripcion` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  UNIQUE KEY `codigo` (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

CREATE TABLE `tipo_documento` (
  `idtipo_docu` int(11) NOT NULL,
  `codigo` char(1) NOT NULL,
  `descripcion` varchar(50) NOT NULL,
  PRIMARY KEY (`idtipo_docu`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `unidad` (
  `iduni` int(11) NOT NULL,
  `codigo` char(3) NOT NULL,
  `descripcion` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`iduni`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `usuario` (
  `idusu` int(11) NOT NULL AUTO_INCREMENT,
  `usuario` varchar(50) NOT NULL,
  `clave` varchar(50) NOT NULL,
  `cargo_usu` varchar(100) NOT NULL,
  `nombres` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telefono` varchar(15) NOT NULL,
  `fechaingreso` date NOT NULL,
  `tipo` varchar(255) NOT NULL,
  `estado` varchar(30) NOT NULL,
  PRIMARY KEY (`idusu`),
  UNIQUE KEY `usuario` (`usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `venta` (
  `idventa` int(11) NOT NULL AUTO_INCREMENT,
  `idconf` int(11) NOT NULL,
  `tipocomp` char(2) NOT NULL,
  `idcliente` int(11) NOT NULL,
  `idusuario` int(11) NOT NULL,
  `idserie` int(11) NOT NULL,
  `fecha_emision` datetime NOT NULL,
  `op_gravadas` decimal(10,2) NOT NULL,
  `op_exoneradas` decimal(10,2) NOT NULL,
  `op_inafectas` decimal(10,2) NOT NULL,
  `igv` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `feestado` varchar(50) DEFAULT NULL,
  `fecodigoerror` char(10) DEFAULT NULL,
  `femensajesunat` text DEFAULT NULL,
  `nombrexml` varchar(50) DEFAULT NULL,
  `xmlbase64` text DEFAULT NULL,
  `cdrbase64` text DEFAULT NULL,
  `efectivo` decimal(18,2) DEFAULT NULL,
  `vuelto` decimal(18,2) DEFAULT NULL,
  `formadepago` varchar(50) NOT NULL,
  `tire` char(50) NOT NULL,
  `estado` enum('no_enviado','enviado','anulado') NOT NULL,
  `numope` varchar(255) NOT NULL,
  PRIMARY KEY (`idventa`),
  KEY `FK_venta_usuario_idusu` (`idusuario`),
  KEY `FK_venta_cliente_idcliente` (`idcliente`),
  KEY `FK_venta_configuracion_idconfi` (`idconf`),
  KEY `FK_venta_serie_idserie` (`idserie`),
  CONSTRAINT `FK_venta_cliente_idcliente` FOREIGN KEY (`idcliente`) REFERENCES `cliente` (`idcliente`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_venta_configuracion_idconfi` FOREIGN KEY (`idconf`) REFERENCES `configuracion` (`idconfi`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_venta_serie_idserie` FOREIGN KEY (`idserie`) REFERENCES `serie` (`idserie`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_venta_usuario_idusu` FOREIGN KEY (`idusuario`) REFERENCES `usuario` (`idusu`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=119 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;



/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;