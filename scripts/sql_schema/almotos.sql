-- phpMyAdmin SQL Dump
-- version 3.3.2deb1ubuntu1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 02, 2015 at 11:34 PM
-- Server version: 5.1.73
-- PHP Version: 5.3.2-1ubuntu4.27

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `almotos`
--

-- --------------------------------------------------------

--
-- Table structure for table `fom_1_archivos_clientes`
--

DROP TABLE IF EXISTS `fom_1_archivos_clientes`;
CREATE TABLE IF NOT EXISTS `fom_1_archivos_clientes` (
  `id` bigint(15) NOT NULL AUTO_INCREMENT,
  `id_cliente` int(8) unsigned zerofill NOT NULL COMMENT 'id del cliente de la empresa',
  `archivo` varchar(100) COLLATE latin1_spanish_ci NOT NULL COMMENT 'ruta al archivo',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='archivos que pertenecen a los clientes de la empresa 1';

-- --------------------------------------------------------

--
-- Table structure for table `fom_actividades_economicas`
--

DROP TABLE IF EXISTS `fom_actividades_economicas`;
CREATE TABLE IF NOT EXISTS `fom_actividades_economicas` (
  `id` smallint(4) unsigned zerofill NOT NULL AUTO_INCREMENT COMMENT 'Consecutivo interno de la tabla de actividades económicas',
  `codigo_dian` smallint(4) unsigned zerofill NOT NULL COMMENT 'CÃ³digo definido por la DIAN',
  `nombre` varchar(255) NOT NULL COMMENT 'Detalle que describe la actividad econÃ³mica',
  `porcentaje_retecree` decimal(4,2) NOT NULL DEFAULT '0.30' COMMENT 'porcentaje de impuesto a aplicar por el impuesto retecree',
  `activo` enum('0','1') NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `codigo_DIAN` (`codigo_dian`),
  UNIQUE KEY `descripcion` (`nombre`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `fom_articulos`
--

DROP TABLE IF EXISTS `fom_articulos`;
CREATE TABLE IF NOT EXISTS `fom_articulos` (
  `id` bigint(12) unsigned NOT NULL AUTO_INCREMENT,
  `id_moto` mediumint(6) unsigned zerofill NOT NULL DEFAULT '000001' COMMENT 'id de la moto a la que aplica este articulo principalmente',
  `id_linea` smallint(3) unsigned zerofill NOT NULL DEFAULT '001' COMMENT 'identificador de la linea a la que pertenece el articulo',
  `codigo_oem` varchar(15) COLLATE latin1_general_ci NOT NULL COMMENT 'codigo oem universal de referencia al articulo',
  `id_subgrupo` smallint(3) unsigned zerofill NOT NULL DEFAULT '001' COMMENT 'subgrupo al que pertenece el articulo',
  `referencia` varchar(50) COLLATE latin1_general_ci NOT NULL COMMENT 'codigo que viene en el catalogo',
  `nombre` varchar(250) COLLATE latin1_general_ci NOT NULL,
  `id_unidad` smallint(2) unsigned zerofill NOT NULL DEFAULT '14' COMMENT 'presentacion',
  `id_pais` smallint(3) unsigned zerofill NOT NULL,
  `plu_interno` varchar(50) COLLATE latin1_general_ci NOT NULL COMMENT 'codigo que tiene cada empresa para identificar sus productos',
  `id_marca` smallint(3) unsigned zerofill NOT NULL DEFAULT '001' COMMENT 'marca especifica del articulo',
  `modelo` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `largo` varchar(30) COLLATE latin1_general_ci DEFAULT NULL,
  `ancho` varchar(30) COLLATE latin1_general_ci DEFAULT NULL,
  `alto` varchar(30) COLLATE latin1_general_ci DEFAULT NULL,
  `otra_medida` varchar(100) COLLATE latin1_general_ci DEFAULT NULL COMMENT 'medidas del articulo de ser necesarias',
  `aplicacion_extra` varchar(250) COLLATE latin1_general_ci NOT NULL COMMENT 'aplicacion adicional del articulo',
  `fecha_registro` date NOT NULL,
  `id_imagen` varchar(20) COLLATE latin1_general_ci NOT NULL DEFAULT '00/00000001.png' COMMENT 'imagen principal del articulo',
  `id_imagen2` varchar(20) COLLATE latin1_general_ci NOT NULL DEFAULT '00/00000001.png' COMMENT 'imagen 2 del articulo',
  `concepto1` varchar(70) COLLATE latin1_general_ci DEFAULT 'Precio de mostrador',
  `precio1` varchar(20) COLLATE latin1_general_ci DEFAULT NULL,
  `concepto2` varchar(70) COLLATE latin1_general_ci DEFAULT 'Precio al por mayor',
  `precio2` varchar(20) COLLATE latin1_general_ci DEFAULT NULL,
  `concepto3` varchar(70) COLLATE latin1_general_ci DEFAULT NULL,
  `precio3` varchar(20) COLLATE latin1_general_ci DEFAULT NULL,
  `concepto4` varchar(70) COLLATE latin1_general_ci DEFAULT NULL,
  `precio4` varchar(20) COLLATE latin1_general_ci DEFAULT NULL,
  `ultimo_precio_compra` varchar(20) COLLATE latin1_general_ci NOT NULL COMMENT 'ultimo precio al que se compro este articulo',
  `activo` enum('0','1') COLLATE latin1_general_ci NOT NULL,
  `gravado_iva` enum('0','1') COLLATE latin1_general_ci NOT NULL COMMENT 'Determina si el articulo esta grabado con iva.',
  `iva` smallint(2) NOT NULL COMMENT 'Impuesto de valor añadido del articulo en porcentaje',
  `stock_minimo` smallint(5) unsigned NOT NULL COMMENT 'cantidad minima de existencias en el inventario',
  `stock_maximo` smallint(5) unsigned NOT NULL COMMENT 'cantidad maxima de existencias en el inventario',
  PRIMARY KEY (`id`),
  KEY `referencia` (`referencia`),
  KEY `plu_interno` (`plu_interno`),
  KEY `id_subgrupo` (`id_subgrupo`),
  KEY `id_unidad` (`id_unidad`),
  KEY `id_pais` (`id_pais`),
  KEY `id_imagen` (`id_imagen`),
  KEY `id_imagen2` (`id_imagen2`),
  KEY `id_moto` (`id_moto`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci COMMENT='Articulos del sistema';

-- --------------------------------------------------------

--
-- Table structure for table `fom_articulos_cotizacion`
--

DROP TABLE IF EXISTS `fom_articulos_cotizacion`;
CREATE TABLE IF NOT EXISTS `fom_articulos_cotizacion` (
  `id` bigint(15) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `id_cotizacion` bigint(12) unsigned zerofill NOT NULL COMMENT 'cotizacion en la que aparece este registro',
  `id_articulo` bigint(12) unsigned zerofill NOT NULL COMMENT 'identificador del articulo',
  `cantidad` smallint(5) NOT NULL COMMENT 'cantidad de articulos cotizados',
  `descuento` smallint(5) NOT NULL COMMENT 'descuento unitario al articulo',
  `iva` smallint(2) NOT NULL COMMENT 'porcentaje de iva con el que esta gravado el articulo',
  `precio` decimal(15,2) NOT NULL COMMENT 'precio al que se cotiza el articulo',
  PRIMARY KEY (`id`),
  KEY `id_cotizacion` (`id_cotizacion`),
  KEY `id_articulo` (`id_articulo`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='almacena los articulos que aperecen en una cotizacion';

-- --------------------------------------------------------

--
-- Table structure for table `fom_articulos_factura_compra`
--

DROP TABLE IF EXISTS `fom_articulos_factura_compra`;
CREATE TABLE IF NOT EXISTS `fom_articulos_factura_compra` (
  `id` bigint(15) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `id_factura` bigint(12) unsigned zerofill NOT NULL COMMENT 'factura en la que aparece este registro',
  `id_articulo` bigint(12) unsigned zerofill NOT NULL COMMENT 'identificador del articulo',
  `cantidad` int(10) NOT NULL COMMENT 'cantidad de articulos comprada',
  `descuento` int(20) NOT NULL COMMENT 'descuento unitario al articulo',
  `precio` decimal(15,2) unsigned NOT NULL COMMENT 'precio al que se compra el articulo',
  `id_bodega` mediumint(8) unsigned zerofill NOT NULL DEFAULT '00000001' COMMENT 'id bodega de destino del articulo',
  `id_proveedor` int(8) unsigned zerofill NOT NULL DEFAULT '00000001' COMMENT 'no es la mejor estrutura de datos, pero facilita velocidad en busquedas',
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'fecha de la compra, *aqui para generar el promedio*',
  `precio_venta` decimal(15,2) unsigned NOT NULL COMMENT 'precio al que decide venderse este articulo en esta factura',
  PRIMARY KEY (`id`),
  KEY `id_factura` (`id_factura`,`id_articulo`),
  KEY `id_articulo` (`id_articulo`),
  KEY `id_bodega` (`id_bodega`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='almacena los articulos que aperecen en una factura de compra';

-- --------------------------------------------------------

--
-- Table structure for table `fom_articulos_factura_temporal_compra`
--

DROP TABLE IF EXISTS `fom_articulos_factura_temporal_compra`;
CREATE TABLE IF NOT EXISTS `fom_articulos_factura_temporal_compra` (
  `id` bigint(15) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `id_factura_temporal` bigint(12) unsigned zerofill NOT NULL COMMENT 'factura en la que aparece este registro',
  `id_articulo` bigint(12) unsigned zerofill NOT NULL COMMENT 'identificador del articulo',
  `cantidad` int(10) NOT NULL COMMENT 'cantidad de articulos comprada',
  `descuento` int(20) NOT NULL COMMENT 'descuento unitario al articulo',
  `precio` decimal(15,2) NOT NULL COMMENT 'precio al que se compra el articulo',
  `id_bodega` mediumint(8) unsigned zerofill NOT NULL DEFAULT '00000001' COMMENT 'id_bodega destino del articulo',
  `precio_venta` decimal(15,2) unsigned NOT NULL COMMENT 'precio de venta para la venta del articulo',
  PRIMARY KEY (`id`),
  KEY `id_factura_temporal` (`id_factura_temporal`),
  KEY `id_articulo` (`id_articulo`),
  KEY `id_bodega` (`id_bodega`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='almacena los articulos que aperecen en una factura temporal ';

-- --------------------------------------------------------

--
-- Table structure for table `fom_articulos_factura_temporal_venta`
--

DROP TABLE IF EXISTS `fom_articulos_factura_temporal_venta`;
CREATE TABLE IF NOT EXISTS `fom_articulos_factura_temporal_venta` (
  `id` bigint(15) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `id_factura_temporal` bigint(12) unsigned zerofill NOT NULL COMMENT 'factura en la que aparece este registro',
  `id_articulo` bigint(12) unsigned zerofill NOT NULL COMMENT 'identificador del articulo',
  `cantidad` smallint(5) NOT NULL COMMENT 'cantidad de articulos vendida',
  `descuento` smallint(5) NOT NULL COMMENT 'descuento unitario al articulo',
  `iva` smallint(2) NOT NULL COMMENT 'iva que aplica al articulo, 0 si no esta gravado',
  `precio` decimal(15,2) NOT NULL COMMENT 'precio al que se compra el articulo',
  PRIMARY KEY (`id`),
  KEY `id_factura_temporal` (`id_factura_temporal`),
  KEY `id_articulo` (`id_articulo`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='almacena los articulos que aperecen en una factura temporal ';

-- --------------------------------------------------------

--
-- Table structure for table `fom_articulos_factura_venta`
--

DROP TABLE IF EXISTS `fom_articulos_factura_venta`;
CREATE TABLE IF NOT EXISTS `fom_articulos_factura_venta` (
  `id` bigint(15) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `id_factura` bigint(12) unsigned zerofill NOT NULL COMMENT 'factura en la que aparece este registro',
  `id_articulo` bigint(12) unsigned zerofill NOT NULL COMMENT 'identificador del articulo',
  `cantidad` smallint(5) NOT NULL COMMENT 'cantidad de articulos vendida',
  `descuento` smallint(5) NOT NULL COMMENT 'descuento unitario al articulo',
  `iva` smallint(2) NOT NULL COMMENT 'iva del articulo, 0 si no esta gravado',
  `precio` decimal(15,2) NOT NULL COMMENT 'precio al que se vende el articulo',
  `id_cliente` int(8) unsigned zerofill NOT NULL DEFAULT '00000001' COMMENT 'id del cliente, no es buen diseño, pero agiliza busquedas',
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `id_bodega` mediumint(8) unsigned zerofill NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_factura` (`id_factura`),
  KEY `id_articulo` (`id_articulo`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='almacena los articulos que aperecen en una factura de venta';

-- --------------------------------------------------------

--
-- Table structure for table `fom_articulos_modificados_ncc`
--

DROP TABLE IF EXISTS `fom_articulos_modificados_ncc`;
CREATE TABLE IF NOT EXISTS `fom_articulos_modificados_ncc` (
  `id` bigint(15) unsigned NOT NULL AUTO_INCREMENT,
  `id_articulo_factura_venta` bigint(15) unsigned zerofill NOT NULL COMMENT 'id del registro en la tabla artiulos_factura_venta',
  `id_nota_credito_cliente` bigint(12) unsigned zerofill NOT NULL COMMENT 'identificador de la nota credito cliente a la que se relaciona este registro',
  `id_articulo` bigint(12) unsigned zerofill NOT NULL COMMENT 'id del articulo que fue modificado',
  `cantidad_anterior` int(10) NOT NULL COMMENT 'cantidad que se registro en la factura de venta',
  `cantidad_nueva` int(10) NOT NULL COMMENT 'cantidad que se modifico con la nota credito',
  `fecha` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_articulo_factura_venta` (`id_articulo_factura_venta`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='articulos que se modificaron en una nota credito de un clien';

-- --------------------------------------------------------

--
-- Table structure for table `fom_articulos_modificados_ncp`
--

DROP TABLE IF EXISTS `fom_articulos_modificados_ncp`;
CREATE TABLE IF NOT EXISTS `fom_articulos_modificados_ncp` (
  `id` bigint(15) unsigned NOT NULL AUTO_INCREMENT,
  `id_articulo_factura_compra` bigint(15) unsigned zerofill NOT NULL COMMENT 'id del registro en la tabla artiulos_factura_compra',
  `id_nota_credito_proveedor` bigint(12) unsigned zerofill NOT NULL COMMENT 'identificador de la nota credito proveedor a la que se relaciona este registro',
  `id_articulo` bigint(12) unsigned zerofill NOT NULL COMMENT 'id del articulo que fue modificado',
  `cantidad_anterior` int(10) NOT NULL COMMENT 'cantidad que se registro en la factura de compra',
  `cantidad_nueva` int(10) NOT NULL COMMENT 'cantidad que se modifico con la nota credito',
  `fecha` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_articulo_factura_compra` (`id_articulo_factura_compra`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='articulos que se modificaron en una nota credito de un prove';

-- --------------------------------------------------------

--
-- Table structure for table `fom_articulos_modificados_ndc`
--

DROP TABLE IF EXISTS `fom_articulos_modificados_ndc`;
CREATE TABLE IF NOT EXISTS `fom_articulos_modificados_ndc` (
  `id` bigint(15) unsigned NOT NULL AUTO_INCREMENT,
  `id_articulo_factura_venta` bigint(15) unsigned zerofill NOT NULL COMMENT 'id del registro en la tabla artiulos_factura_venta',
  `id_nota_debito_cliente` bigint(12) unsigned zerofill NOT NULL COMMENT 'identificador de la nota debito cliente a la que se relaciona este registro',
  `id_articulo` bigint(12) unsigned zerofill NOT NULL COMMENT 'id del articulo que fue modificado',
  `cantidad_anterior` int(10) NOT NULL COMMENT 'cantidad que se registro en la factura de venta',
  `cantidad_nueva` int(10) NOT NULL COMMENT 'cantidad que se modifico con la nota debito',
  `fecha` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_articulo_factura_venta` (`id_articulo_factura_venta`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='articulos que se modificaron en una nota debito de un client';

-- --------------------------------------------------------

--
-- Table structure for table `fom_articulos_modificados_ndp`
--

DROP TABLE IF EXISTS `fom_articulos_modificados_ndp`;
CREATE TABLE IF NOT EXISTS `fom_articulos_modificados_ndp` (
  `id` bigint(15) unsigned NOT NULL AUTO_INCREMENT,
  `id_articulo_factura_compra` bigint(15) unsigned zerofill NOT NULL COMMENT 'id del registro en la tabla artiulos_factura_compra',
  `id_nota_debito_proveedor` bigint(12) unsigned zerofill NOT NULL COMMENT 'identificador de la nota debito proveedor a la que se relaciona este registro',
  `id_articulo` bigint(12) unsigned zerofill NOT NULL COMMENT 'id del articulo que fue modificado',
  `cantidad_anterior` int(10) NOT NULL COMMENT 'cantidad que se registro en la factura de compra',
  `cantidad_nueva` int(10) NOT NULL COMMENT 'cantidad que se modifico con la nota debito',
  `fecha` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_articulo_factura_compra` (`id_articulo_factura_compra`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='articulos que se modificaron en una nota debito de un provee';

-- --------------------------------------------------------

--
-- Table structure for table `fom_articulos_orden_compra`
--

DROP TABLE IF EXISTS `fom_articulos_orden_compra`;
CREATE TABLE IF NOT EXISTS `fom_articulos_orden_compra` (
  `id` bigint(15) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `id_orden` bigint(12) unsigned zerofill NOT NULL COMMENT 'Orden de compra en la que aparece este registro',
  `id_articulo` bigint(12) unsigned zerofill NOT NULL COMMENT 'identificador del articulo',
  `cantidad` int(10) NOT NULL COMMENT 'cantidad de articulos comprada',
  `descuento` int(20) NOT NULL COMMENT 'descuento unitario al articulo',
  `precio` decimal(15,2) unsigned NOT NULL COMMENT 'precio al que se compra el articulo',
  `id_bodega` mediumint(8) unsigned zerofill NOT NULL DEFAULT '00000001' COMMENT 'id bodega de destino del articulo',
  `id_proveedor` int(8) unsigned zerofill NOT NULL DEFAULT '00000001' COMMENT 'no es la mejor estrutura de datos, pero facilita velocidad en busquedas',
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'fecha de la compra, *aqui para generar el promedio*',
  `precio_venta` decimal(15,2) unsigned NOT NULL COMMENT 'precio al que decide venderse este articulo en esta factura',
  PRIMARY KEY (`id`),
  KEY `id_orden` (`id_orden`,`id_articulo`),
  KEY `id_articulo` (`id_articulo`),
  KEY `id_bodega` (`id_bodega`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='almacena los articulos que aperecen en una factura de compra';

-- --------------------------------------------------------

--
-- Table structure for table `fom_articulo_moto`
--

DROP TABLE IF EXISTS `fom_articulo_moto`;
CREATE TABLE IF NOT EXISTS `fom_articulo_moto` (
  `id` int(10) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `id_articulo` bigint(12) unsigned zerofill NOT NULL,
  `id_moto` mediumint(6) unsigned zerofill NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_articulo` (`id_articulo`),
  KEY `id_moto` (`id_moto`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='almacena la relacion de aplicacion de un articulo a varios m';

-- --------------------------------------------------------

--
-- Table structure for table `fom_asientos_contables`
--

DROP TABLE IF EXISTS `fom_asientos_contables`;
CREATE TABLE IF NOT EXISTS `fom_asientos_contables` (
  `id` bigint(15) unsigned NOT NULL AUTO_INCREMENT,
  `id_cuenta` mediumint(6) unsigned NOT NULL,
  `comprobante` enum('1','2','3','4','5','6') COLLATE latin1_spanish_ci NOT NULL COMMENT 'determina que generó el asiento contable 1->factura compra, 2->factura venta, 3->NC Proveedor, 4->ND Proveedor, 5->NC Cliente, 6->ND Cliente  (NC = nota credito y ND= nota debito)',
  `num_comprobante` bigint(12) NOT NULL COMMENT 'identificador del comprobante',
  `fecha` datetime NOT NULL,
  `concepto` varchar(250) COLLATE latin1_spanish_ci NOT NULL,
  `credito` decimal(15,2) NOT NULL,
  `debito` decimal(15,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='tabla que lamacena la info de los asientos contables';

-- --------------------------------------------------------

--
-- Table structure for table `fom_bancos`
--

DROP TABLE IF EXISTS `fom_bancos`;
CREATE TABLE IF NOT EXISTS `fom_bancos` (
  `id` smallint(2) unsigned zerofill NOT NULL AUTO_INCREMENT COMMENT 'Consecutivo interno de la tabla bancos',
  `nombre` varchar(255) NOT NULL COMMENT 'Nombre que identifica el banco',
  `activo` enum('0','1') NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `fom_bitacora`
--

DROP TABLE IF EXISTS `fom_bitacora`;
CREATE TABLE IF NOT EXISTS `fom_bitacora` (
  `id` bigint(12) unsigned zerofill NOT NULL AUTO_INCREMENT COMMENT 'Consecutivo interno de la tabla bitacora',
  `fecha` datetime DEFAULT NULL COMMENT 'Fecha de la actividad',
  `ip` varchar(15) NOT NULL COMMENT 'Direccion IP desde la cual se realiza la actividad',
  `agente` varchar(255) NOT NULL COMMENT 'Informacion del navegador',
  `id_modulo` smallint(4) unsigned zerofill NOT NULL DEFAULT '0000' COMMENT 'Id de la tabla modulos',
  `actividad` varchar(255) NOT NULL COMMENT 'Descripci�n de la actividad',
  PRIMARY KEY (`id`),
  KEY `actividad_modulo` (`id_modulo`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='almacna la inf. sobre las actividades realizadas en el siste';

-- --------------------------------------------------------

--
-- Table structure for table `fom_bodegas`
--

DROP TABLE IF EXISTS `fom_bodegas`;
CREATE TABLE IF NOT EXISTS `fom_bodegas` (
  `id` mediumint(8) unsigned zerofill NOT NULL AUTO_INCREMENT COMMENT 'identificador en la bd de la bodega',
  `id_sede` mediumint(8) unsigned zerofill NOT NULL COMMENT 'identifica a que sede pertenece la bodega',
  `nombre` varchar(150) NOT NULL COMMENT 'nombre de la bodega',
  `ubicacion` varchar(250) NOT NULL COMMENT 'describe la ubicacion de la bodega',
  `principal` enum('0','1') NOT NULL COMMENT 'determina si la bodega es la bodega principal de la sede',
  PRIMARY KEY (`id`),
  KEY `id_sede` (`id_sede`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='Bodegas existentes en una sede de la empresa';

-- --------------------------------------------------------

--
-- Table structure for table `fom_cajas`
--

DROP TABLE IF EXISTS `fom_cajas`;
CREATE TABLE IF NOT EXISTS `fom_cajas` (
  `id` mediumint(8) unsigned zerofill NOT NULL AUTO_INCREMENT COMMENT 'identificador en la bd de la caja',
  `id_sede` mediumint(8) unsigned zerofill NOT NULL COMMENT 'identifica a que sede pertenece la caja',
  `nombre` varchar(150) NOT NULL COMMENT 'nombre de la caja',
  `activo` enum('0','1') NOT NULL,
  `principal` enum('0','1') NOT NULL COMMENT 'determina si la caja es la caja principal del la sede',
  PRIMARY KEY (`id`),
  KEY `id_sede` (`id_sede`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='Cajas existentes en una sede de la empresa';

-- --------------------------------------------------------

--
-- Table structure for table `fom_cambios_inventarios`
--

DROP TABLE IF EXISTS `fom_cambios_inventarios`;
CREATE TABLE IF NOT EXISTS `fom_cambios_inventarios` (
  `id` bigint(15) unsigned zerofill NOT NULL AUTO_INCREMENT COMMENT 'Identificador automatico interno de la base de datos',
  `fecha` datetime NOT NULL COMMENT 'fecha en la que se genero el combio en el inventario',
  `id_usuario` int(8) unsigned zerofill NOT NULL COMMENT 'identificador interno de la base datos del usuario que realizo el cambio en el inventario',
  `descuento` int(4) NOT NULL COMMENT 'cantidad que desconto en el inventario del articulo',
  `aumento` int(4) NOT NULL COMMENT 'Cantidad que aumento en el inventario del articulo',
  `id_articulo` bigint(12) unsigned zerofill NOT NULL COMMENT 'identificador del articulo que se afecta en el inventario',
  `id_bodega` mediumint(8) unsigned zerofill NOT NULL COMMENT 'identificador de la bodega donde se encuentra el articulo',
  `ip` varchar(20) NOT NULL COMMENT 'IP del equipo donde se realizo el cambio en el inventario',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `fom_cargos`
--

DROP TABLE IF EXISTS `fom_cargos`;
CREATE TABLE IF NOT EXISTS `fom_cargos` (
  `id` smallint(3) unsigned zerofill NOT NULL AUTO_INCREMENT COMMENT 'Consecutivo interno de la tabla cargos',
  `nombre` varchar(255) NOT NULL COMMENT 'Nombre del cargo',
  `responsabilidades` text NOT NULL COMMENT 'Responsabilidades del cargo',
  `activo` enum('0','1') NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='los roles que puede representar una persona en el sistema';

-- --------------------------------------------------------

--
-- Table structure for table `fom_catalogos`
--

DROP TABLE IF EXISTS `fom_catalogos`;
CREATE TABLE IF NOT EXISTS `fom_catalogos` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id del registro',
  `nombre` varchar(150) COLLATE latin1_spanish_ci NOT NULL,
  `id_proveedor` int(8) unsigned zerofill NOT NULL,
  `id_articulo` bigint(12) unsigned zerofill NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `archivo` varchar(100) COLLATE latin1_spanish_ci NOT NULL,
  `activo` enum('0','1') COLLATE latin1_spanish_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_moto` (`id_proveedor`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='Almacena la info. de los catÃ¡logos de las motos del sistema';

-- --------------------------------------------------------

--
-- Table structure for table `fom_centros_costo`
--

DROP TABLE IF EXISTS `fom_centros_costo`;
CREATE TABLE IF NOT EXISTS `fom_centros_costo` (
  `id` smallint(3) unsigned zerofill NOT NULL AUTO_INCREMENT COMMENT 'Consecutivo interno de la tabla centros costo',
  `id_sede` smallint(3) unsigned zerofill NOT NULL DEFAULT '000' COMMENT 'Id de la tabla sedes',
  `codigo` mediumint(7) unsigned zerofill NOT NULL COMMENT 'Código asignado al centro de costo',
  `nombre` varchar(255) NOT NULL COMMENT 'Nombre que identifica el centro de costo',
  `activo` enum('0','1') NOT NULL DEFAULT '1' COMMENT 'Indica si el centro de costo esta activo (0=No, 1=Si)',
  `global` enum('0','1') NOT NULL DEFAULT '0' COMMENT 'Indica si el centro de costo es global (0=No, 1=Si)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `codigo` (`codigo`),
  UNIQUE KEY `nombre` (`nombre`),
  KEY `id_sede` (`id_sede`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `fom_ciudades`
--

DROP TABLE IF EXISTS `fom_ciudades`;
CREATE TABLE IF NOT EXISTS `fom_ciudades` (
  `id` int(8) unsigned zerofill NOT NULL AUTO_INCREMENT COMMENT 'Consecutivo interno de la tabla ciudades',
  `id_estado` mediumint(6) unsigned zerofill NOT NULL DEFAULT '000000' COMMENT 'Id de la tabla estados',
  `nombre` varchar(255) NOT NULL COMMENT 'Nombre de la ciudad',
  PRIMARY KEY (`id`),
  KEY `ciudad_estado` (`id_estado`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `fom_clientes`
--

DROP TABLE IF EXISTS `fom_clientes`;
CREATE TABLE IF NOT EXISTS `fom_clientes` (
  `id` int(8) unsigned zerofill NOT NULL AUTO_INCREMENT COMMENT 'Consecutivo interno para la base de datos',
  `id_cliente` varchar(50) COLLATE latin1_spanish_ci NOT NULL COMMENT 'identificador del cliente',
  `nombre` varchar(250) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL COMMENT 'nombre comercial de la empresa',
  `razon_social` varchar(150) COLLATE latin1_spanish_ci NOT NULL COMMENT 'razon social del cliente',
  `tipo_persona` enum('1','2','3') COLLATE latin1_spanish_ci NOT NULL COMMENT '1=natural, 2= juridica, 3= codigo interno',
  `regimen` enum('1','2','3','4','5','6','7') COLLATE latin1_spanish_ci DEFAULT '1' COMMENT '1->Gran contribuyente 2->Empresa del estado 3->Regimen comun 4->Regimen simplificado 5->Simplificado no residente 6->No residente 7->No responsable',
  `id_actividad_economica` smallint(4) unsigned zerofill NOT NULL COMMENT 'actividad economica del cliente',
  `call_center` varchar(15) COLLATE latin1_spanish_ci DEFAULT NULL COMMENT 'Numero de telefono de contacto con el cliente, como call center',
  `max_cupo_credito` bigint(15) NOT NULL COMMENT 'maximo cupo de credito de este cliente',
  `activo` enum('0','1') COLLATE latin1_spanish_ci NOT NULL DEFAULT '0' COMMENT 'El cliente se encuentra activo: 0->No, 1->Si',
  `id_usuario_crea` smallint(4) unsigned zerofill NOT NULL COMMENT 'identificador de la tabla de usuarios del usuario que crea el articulo',
  `fecha_creacion` datetime NOT NULL COMMENT 'Fecha en que se crea el cliente',
  `observaciones` text COLLATE latin1_spanish_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_cliente` (`id_cliente`),
  KEY `id_usuario_crea` (`id_usuario_crea`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fom_componentes_modulos`
--

DROP TABLE IF EXISTS `fom_componentes_modulos`;
CREATE TABLE IF NOT EXISTS `fom_componentes_modulos` (
  `id` mediumint(6) unsigned zerofill NOT NULL AUTO_INCREMENT COMMENT 'Consecutivo interno de la tabla modulos',
  `id_modulo` smallint(4) unsigned zerofill NOT NULL COMMENT 'Id de la tabla modulos',
  `componente` varchar(50) COLLATE latin1_general_ci NOT NULL COMMENT 'Nombre del componente del modulo',
  `nombre` varchar(50) COLLATE latin1_general_ci NOT NULL COMMENT 'Nombre que se muestra del componente',
  `activo` enum('0','1') COLLATE latin1_general_ci NOT NULL DEFAULT '1' COMMENT 'determina si el registro se encuentra activo o no',
  PRIMARY KEY (`id`),
  KEY `id_modulo` (`id_modulo`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fom_conceptos_DIAN`
--

DROP TABLE IF EXISTS `fom_conceptos_DIAN`;
CREATE TABLE IF NOT EXISTS `fom_conceptos_DIAN` (
  `id` smallint(4) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) COLLATE latin1_spanish_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='conceptos de la dian para medios magneticos';

-- --------------------------------------------------------

--
-- Table structure for table `fom_configuraciones`
--

DROP TABLE IF EXISTS `fom_configuraciones`;
CREATE TABLE IF NOT EXISTS `fom_configuraciones` (
  `id` smallint(3) unsigned NOT NULL AUTO_INCREMENT,
  `cantidad_decimales` varchar(100) COLLATE latin1_spanish_ci NOT NULL COMMENT 'cantidad de decimales a mostrar',
  `tipo_moneda` varchar(100) COLLATE latin1_spanish_ci NOT NULL COMMENT 'tipo de moneda a utilizar en colombia',
  `nota_factura` text COLLATE latin1_spanish_ci NOT NULL COMMENT 'notas promocionales que se imprimiran en las facturas',
  `dato_codigo_barra` varchar(50) COLLATE latin1_spanish_ci NOT NULL DEFAULT 'plu_interno' COMMENT 'este dato determina con que se va a imp. el cod. barras, ej: plu, id, referencia',
  `iva_general` smallint(3) unsigned NOT NULL DEFAULT '16' COMMENT 'iva que se tomara como base para todo',
  `dias_promedio_ponderado` smallint(4) unsigned NOT NULL DEFAULT '180' COMMENT 'dias que serán utilizados para generar el precio promedio de un articulo',
  `porc_pred_ganancia` smallint(4) unsigned NOT NULL DEFAULT '10' COMMENT 'Porcentaje de ganancia predeterminada a un articulo',
  `id_principal_articulo` varchar(40) COLLATE latin1_spanish_ci NOT NULL DEFAULT 'plu_interno' COMMENT 'cual de los campos de la tb articulos será el predeterminado',
  `facturar_negativo` enum('0','1') COLLATE latin1_spanish_ci NOT NULL DEFAULT '0' COMMENT 'determina si el sistema permite facturar en negativo',
  `valor_uvt` decimal(15,2) NOT NULL DEFAULT '27485.00' COMMENT 'determina el valor del uvt para los calculos contables',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='almacena las configuraciones del sistema';

-- --------------------------------------------------------

--
-- Table structure for table `fom_contactos_cliente`
--

DROP TABLE IF EXISTS `fom_contactos_cliente`;
CREATE TABLE IF NOT EXISTS `fom_contactos_cliente` (
  `id` int(10) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `id_cliente` int(8) unsigned zerofill NOT NULL,
  `id_persona` int(8) unsigned zerofill NOT NULL,
  `observaciones` varchar(250) COLLATE latin1_spanish_ci DEFAULT NULL,
  `principal` enum('0','1') COLLATE latin1_spanish_ci NOT NULL DEFAULT '0' COMMENT 'Determina si este contacto es o no el principal',
  PRIMARY KEY (`id`),
  KEY `id_cliente` (`id_cliente`),
  KEY `id_persona` (`id_persona`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='personas que sean contactos con el negocio de un cliente';

-- --------------------------------------------------------

--
-- Table structure for table `fom_contactos_proveedor`
--

DROP TABLE IF EXISTS `fom_contactos_proveedor`;
CREATE TABLE IF NOT EXISTS `fom_contactos_proveedor` (
  `id` int(10) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `id_proveedor` int(8) unsigned zerofill NOT NULL,
  `id_persona` int(8) unsigned zerofill NOT NULL,
  `observaciones` varchar(250) COLLATE latin1_spanish_ci DEFAULT NULL,
  `principal` enum('0','1') COLLATE latin1_spanish_ci NOT NULL DEFAULT '0' COMMENT 'Determina si este contacto es o no el principal',
  PRIMARY KEY (`id`),
  KEY `id_proveedor` (`id_proveedor`),
  KEY `id_persona` (`id_persona`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='personas que sean contactos con el negocio de un proveedor';

-- --------------------------------------------------------

--
-- Table structure for table `fom_cotizaciones`
--

DROP TABLE IF EXISTS `fom_cotizaciones`;
CREATE TABLE IF NOT EXISTS `fom_cotizaciones` (
  `id` bigint(12) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `id_cotizacion` varchar(50) COLLATE latin1_spanish_ci NOT NULL,
  `id_cliente` int(8) unsigned zerofill NOT NULL COMMENT 'identificador del proveedor al cual se realiza la cotizacion',
  `fecha_cotizacion` datetime NOT NULL COMMENT 'fecha en que se genera la cotizacion',
  `fecha_vencimiento` date NOT NULL,
  `modo_pago` enum('1','2') COLLATE latin1_spanish_ci NOT NULL,
  `id_caja` mediumint(8) unsigned zerofill NOT NULL COMMENT 'caja donde se genera la cotizacion',
  `id_usuario` int(8) unsigned zerofill NOT NULL COMMENT 'usuario que genera la cotizacion',
  `subtotal` decimal(15,2) NOT NULL COMMENT 'subtotal de la cotizacion',
  `iva` decimal(15,2) NOT NULL COMMENT 'valor del iva',
  `concepto1` varchar(100) COLLATE latin1_spanish_ci NOT NULL COMMENT 'concepto del descuento',
  `descuento1` smallint(2) NOT NULL COMMENT 'total del descuento',
  `concepto2` varchar(100) COLLATE latin1_spanish_ci NOT NULL COMMENT 'concepto del descuento',
  `descuento2` smallint(2) NOT NULL COMMENT 'total del descuento',
  `valor_flete` decimal(15,2) NOT NULL COMMENT 'valor del flete',
  `total` decimal(15,2) NOT NULL COMMENT 'total',
  `observaciones` text COLLATE latin1_spanish_ci NOT NULL COMMENT 'observaciones',
  `activo` enum('0','1') COLLATE latin1_spanish_ci NOT NULL COMMENT 'determina si la cotizacion se encuentra activa o no',
  `porcentaje_dcto_1` mediumint(5) NOT NULL,
  `fecha_limite_dcto_1` date NOT NULL,
  `porcentaje_dcto_2` mediumint(5) NOT NULL,
  `fecha_limite_dcto_2` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_cliente` (`id_cliente`),
  KEY `id_usuario` (`id_usuario`),
  KEY `id_caja` (`id_caja`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='almacena la info. de cada cotizacion que se realize';

-- --------------------------------------------------------

--
-- Table structure for table `fom_cuentas_proveedor`
--

DROP TABLE IF EXISTS `fom_cuentas_proveedor`;
CREATE TABLE IF NOT EXISTS `fom_cuentas_proveedor` (
  `id` int(8) unsigned zerofill NOT NULL AUTO_INCREMENT COMMENT 'Consecutivo interno de la tabla cuentas proveedor',
  `id_banco` smallint(2) unsigned zerofill DEFAULT NULL COMMENT 'identificador del banco',
  `id_proveedor` int(8) unsigned zerofill DEFAULT NULL COMMENT 'identificador del proveedor',
  `numero_cuenta` varchar(255) NOT NULL COMMENT 'numero de la cuenta',
  `tipo_cuenta` enum('1','2') NOT NULL DEFAULT '1' COMMENT 'tipo de cuenta 1= ahorros, 2 = corriente',
  `fecha_transaccion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id_banco` (`id_banco`),
  KEY `id_proveedor` (`id_proveedor`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `fom_cuentas_tipo_compra`
--

DROP TABLE IF EXISTS `fom_cuentas_tipo_compra`;
CREATE TABLE IF NOT EXISTS `fom_cuentas_tipo_compra` (
  `id` bigint(15) unsigned NOT NULL AUTO_INCREMENT,
  `id_tipo_compra` smallint(3) unsigned zerofill NOT NULL,
  `id_cuenta` mediumint(6) unsigned NOT NULL,
  `tipo` enum('1','2') COLLATE latin1_spanish_ci NOT NULL DEFAULT '1' COMMENT '1= afecta la cuenta por el credito, 2= por el debito',
  `campo_oculto` enum('0','1') COLLATE latin1_spanish_ci NOT NULL,
  `base_total_pesos` decimal(15,2) NOT NULL,
  `base_total_porcentaje` smallint(2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='Almacena las cuentas directas (no de impuestos) afectadas po';

-- --------------------------------------------------------

--
-- Table structure for table `fom_dias_festivos`
--

DROP TABLE IF EXISTS `fom_dias_festivos`;
CREATE TABLE IF NOT EXISTS `fom_dias_festivos` (
  `id` smallint(5) unsigned zerofill NOT NULL AUTO_INCREMENT COMMENT 'Consecutivo interno de la tabla días festivos',
  `fecha` date NOT NULL COMMENT 'Fecha del dia festivo',
  `descripcion` varchar(50) NOT NULL COMMENT 'Descripcion del dia festivo',
  `activo` enum('0','1') NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `fom_documentos`
--

DROP TABLE IF EXISTS `fom_documentos`;
CREATE TABLE IF NOT EXISTS `fom_documentos` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Consecutivo interno para la base de datos',
  `id_modulo` smallint(4) unsigned zerofill NOT NULL COMMENT 'Consecutivo interno para la base de datos del mÃ³dulo al que pertenece',
  `id_registro` bigint(12) unsigned zerofill NOT NULL COMMENT 'Consecutivo interno para la base de datos del registro al que pertenece el archivo',
  `id_usuario` int(8) unsigned zerofill NOT NULL COMMENT 'Consecutivo interno para la base de datos del usuario creador del archivo',
  `titulo` varchar(255) NOT NULL COMMENT 'Titulo del archivo',
  `descripcion` varchar(255) NOT NULL COMMENT 'DescripciÃ³n del archivo',
  `fecha` datetime DEFAULT NULL COMMENT 'Fecha de creaciÃ³n del archivo',
  `ruta` varchar(255) NOT NULL COMMENT 'Ruta del archivo',
  PRIMARY KEY (`id`),
  KEY `archivo_modulo` (`id_modulo`),
  KEY `archivo_usuario` (`id_usuario`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `fom_empleados`
--

DROP TABLE IF EXISTS `fom_empleados`;
CREATE TABLE IF NOT EXISTS `fom_empleados` (
  `id` mediumint(8) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `id_tipo_empleado` mediumint(8) unsigned zerofill NOT NULL,
  `id_persona` int(8) unsigned zerofill NOT NULL COMMENT 'identifica a la persona relacionada con el empleado',
  `fecha_inicio` date NOT NULL COMMENT 'fecha_inicio de labores en la empresa',
  `fecha_fin` date NOT NULL COMMENT 'fecha finalizacion de labores en la empresa',
  `id_cargo` smallint(3) unsigned zerofill NOT NULL COMMENT 'identifica la labor o cargo realizada por el empleado',
  `id_sede` mediumint(8) unsigned zerofill NOT NULL COMMENT 'sede de la empresa a la que pertenece el empleado',
  `salario` varchar(150) DEFAULT NULL COMMENT 'Salario devengado por el colaborador en pesos colombianos mc',
  `activo` enum('0','1') NOT NULL COMMENT 'determina si el empleado esta activo o no',
  `observaciones` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_tipo_empleado` (`id_tipo_empleado`),
  KEY `id_persona` (`id_persona`),
  KEY `id_cargo` (`id_cargo`),
  KEY `id_sede` (`id_sede`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Tabla que almacena la info de los empleados';

-- --------------------------------------------------------

--
-- Table structure for table `fom_empresas`
--

DROP TABLE IF EXISTS `fom_empresas`;
CREATE TABLE IF NOT EXISTS `fom_empresas` (
  `id` mediumint(8) unsigned zerofill NOT NULL AUTO_INCREMENT COMMENT 'identificador de la empresa',
  `nit` varchar(50) NOT NULL COMMENT 'nit de la empresa propietaria del software',
  `direccion_principal` varchar(150) NOT NULL COMMENT 'direccion sede principal',
  `telefono` varchar(50) NOT NULL COMMENT 'telefono',
  `email` varchar(60) NOT NULL COMMENT 'email',
  `pagina_web` varchar(60) NOT NULL COMMENT 'pagina web',
  `nombre_original` varchar(250) DEFAULT NULL,
  `nombre` varchar(250) NOT NULL COMMENT 'nombre con el cual es conocida la empresa popular o comercialmente',
  `regimen` enum('1','2','3','4','5','6','7','8') DEFAULT NULL COMMENT '1->Regimen simplificado 2->Regimen comun 3->Gran contribuyente 4->Gran contribuyente autoretenedor 5->Simplificado no residente 6->No residente 7->Empresa del estado 8->No responsable',
  `id_actividad_economica` smallint(4) unsigned zerofill NOT NULL,
  `base_retefuente` bigint(15) NOT NULL,
  `retiene_fuente` enum('0','1') DEFAULT NULL COMMENT 'determina si la empresa retiene fuente 1->si, 0->no',
  `retiene_ica` enum('0','1') DEFAULT NULL COMMENT 'determina si la empresa retiene ica 1->si, 0->no',
  `retiene_iva` enum('0','1') DEFAULT NULL COMMENT 'determina si la empresa retiene iva 1->si, 0->no',
  `autoretenedor` enum('0','1') DEFAULT NULL COMMENT 'determina si la empresa es autoretenedora1->si, 0->no',
  `grancontribuyente` enum('0','1') DEFAULT NULL COMMENT 'determina si la empresa es grancontribuyente 1->si, 0->no',
  `ingreso_mercancia` enum('0','1') NOT NULL COMMENT 'determina si la empresa que va a utilizar el software realiza ingreso de mercancia para control de ubicacion de la misma',
  `id_imagen` bigint(12) unsigned zerofill NOT NULL COMMENT 'logo de la empresa',
  `activo` enum('0','1') DEFAULT NULL COMMENT 'determina si la empresa esta activa o no',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='Tabla que almacena la informacion basica de la empresa';

-- --------------------------------------------------------

--
-- Table structure for table `fom_estados`
--

DROP TABLE IF EXISTS `fom_estados`;
CREATE TABLE IF NOT EXISTS `fom_estados` (
  `id` mediumint(6) unsigned zerofill NOT NULL AUTO_INCREMENT COMMENT 'Consecutivo interno de la tabla estados',
  `id_pais` smallint(3) unsigned zerofill NOT NULL COMMENT 'Id de la tabla paises',
  `nombre` varchar(255) NOT NULL COMMENT 'Nombre del estado',
  PRIMARY KEY (`id`),
  KEY `pais_estado` (`id_pais`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `fom_eventos`
--

DROP TABLE IF EXISTS `fom_eventos`;
CREATE TABLE IF NOT EXISTS `fom_eventos` (
  `id` bigint(15) unsigned NOT NULL AUTO_INCREMENT,
  `id_usuario` int(8) unsigned zerofill NOT NULL,
  `fecha_inicio` date NOT NULL,
  `hora_inicio` time NOT NULL,
  `fecha_fin` date DEFAULT NULL,
  `hora_fin` time DEFAULT NULL,
  `titulo` varchar(150) COLLATE latin1_spanish_ci NOT NULL,
  `descripcion` text COLLATE latin1_spanish_ci NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'fecha en la que se crea el evento o actividad',
  `activo` enum('0','1') COLLATE latin1_spanish_ci NOT NULL COMMENT 'determina si un evento esta activo o no',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='tabla que almacena los eventos o actividades de un usuario d';

-- --------------------------------------------------------

--
-- Table structure for table `fom_facturas_compras`
--

DROP TABLE IF EXISTS `fom_facturas_compras`;
CREATE TABLE IF NOT EXISTS `fom_facturas_compras` (
  `id` bigint(12) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `id_factura` varchar(50) COLLATE latin1_spanish_ci NOT NULL COMMENT 'id de factura donde se concatena la sede, y la forma de impresion',
  `id_proveedor` int(8) unsigned zerofill NOT NULL COMMENT 'identificador del proveedor al cual se realiza la compra',
  `num_factura_proveedor` varchar(50) COLLATE latin1_spanish_ci NOT NULL COMMENT 'numero de factura de venta que genera el proveedor',
  `fecha_factura` datetime NOT NULL COMMENT 'fecha en que se genera la factura',
  `modo_pago` enum('1','2') COLLATE latin1_spanish_ci NOT NULL COMMENT '1= contado, 2 = credito',
  `fecha_vencimiento` date NOT NULL COMMENT 'indica cuando se vence la factura',
  `id_caja` mediumint(8) unsigned zerofill NOT NULL COMMENT 'caja donde se genera la factura',
  `id_usuario` int(8) unsigned zerofill NOT NULL COMMENT 'usuario que genera la compra',
  `subtotal` decimal(15,2) NOT NULL COMMENT 'subtotal de la compra',
  `iva` decimal(15,2) NOT NULL COMMENT 'valor del iva',
  `concepto1` varchar(100) COLLATE latin1_spanish_ci NOT NULL COMMENT 'concepto del descuento',
  `descuento1` smallint(2) NOT NULL COMMENT 'total del descuento',
  `concepto2` varchar(100) COLLATE latin1_spanish_ci NOT NULL COMMENT 'concepto del descuento',
  `descuento2` smallint(2) NOT NULL COMMENT 'total del descuento',
  `valor_flete` decimal(15,2) NOT NULL COMMENT 'valor del flete de la mercancia',
  `total` decimal(15,2) NOT NULL COMMENT 'total',
  `estado_factura` enum('1','2') COLLATE latin1_spanish_ci NOT NULL COMMENT '1= abiera, 2 = cerrada',
  `observaciones` text COLLATE latin1_spanish_ci NOT NULL COMMENT 'observaciones',
  `archivo` varchar(100) COLLATE latin1_spanish_ci NOT NULL COMMENT 'archivo digitalizado que contiene copia de la factura de compra al proveedor',
  `retenciones` varchar(250) COLLATE latin1_spanish_ci NOT NULL COMMENT 'string del tipo "1;1000|2;2000" . donde el primer numro antes del ; representa el id de la retencion y el segundo numero el valor de dicha retencion, y los valores estaran separados por |',
  `campo_efectivo` decimal(15,2) NOT NULL COMMENT 'cuanto de la factura se pago en efectivo ',
  `campo_tarjeta` decimal(15,2) NOT NULL COMMENT 'cuanto de la factura se pago con tarjeta ',
  `campo_cheque` decimal(15,2) NOT NULL COMMENT 'cuanto de la factura se pago con cheque',
  `campo_credito` decimal(15,2) NOT NULL COMMENT 'cuanto de la factura se pago a credito',
  `activo` enum('0','1') COLLATE latin1_spanish_ci NOT NULL DEFAULT '1' COMMENT 'determina si el registro esta activo o no',
  PRIMARY KEY (`id`),
  KEY `id_proveedor` (`id_proveedor`),
  KEY `id_caja` (`id_caja`),
  KEY `id_usuario` (`id_usuario`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='almacena la info. de cada factura de compras que se realizen';

-- --------------------------------------------------------

--
-- Table structure for table `fom_facturas_temporales_compra`
--

DROP TABLE IF EXISTS `fom_facturas_temporales_compra`;
CREATE TABLE IF NOT EXISTS `fom_facturas_temporales_compra` (
  `id` bigint(12) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `id_proveedor` int(8) unsigned zerofill NOT NULL COMMENT 'identificador del proveedor al cual se realiza la compra',
  `num_factura_proveedor` varchar(50) COLLATE latin1_spanish_ci NOT NULL COMMENT 'numero de factura de venta que genera el proveedor',
  `fecha_factura` datetime NOT NULL COMMENT 'fecha en que se genera la factura',
  `modo_pago` enum('1','2') COLLATE latin1_spanish_ci NOT NULL COMMENT '1= contado, 2 = credito',
  `fecha_vencimiento` date NOT NULL COMMENT 'indica cuando se vence la factura',
  `id_caja` mediumint(8) unsigned zerofill NOT NULL COMMENT 'sede donde se genera la factura',
  `id_usuario` int(8) unsigned zerofill NOT NULL COMMENT 'usuario que genera la compra',
  `subtotal` decimal(15,2) NOT NULL COMMENT 'subtotal de la compra',
  `iva` decimal(15,2) NOT NULL COMMENT 'valor del iva',
  `concepto1` varchar(100) COLLATE latin1_spanish_ci NOT NULL COMMENT 'concepto del descuento',
  `descuento1` smallint(2) NOT NULL COMMENT 'total del descuento',
  `concepto2` varchar(100) COLLATE latin1_spanish_ci NOT NULL COMMENT 'concepto del descuento',
  `descuento2` smallint(2) NOT NULL COMMENT 'total del descuento',
  `valor_flete` decimal(15,2) NOT NULL COMMENT 'valor del flete de la mercancia',
  `total` decimal(15,2) NOT NULL COMMENT 'total',
  `estado_factura` enum('1','2') COLLATE latin1_spanish_ci NOT NULL COMMENT '1= abiera, 2 = cerrada',
  `observaciones` text COLLATE latin1_spanish_ci NOT NULL COMMENT 'observaciones',
  `id_notificacion` bigint(15) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_proveedor` (`id_proveedor`),
  KEY `id_caja` (`id_caja`),
  KEY `id_usuario` (`id_usuario`),
  KEY `id_notificacion` (`id_notificacion`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='va almacenando la info. de cada factura de compra que por al';

-- --------------------------------------------------------

--
-- Table structure for table `fom_facturas_temporales_venta`
--

DROP TABLE IF EXISTS `fom_facturas_temporales_venta`;
CREATE TABLE IF NOT EXISTS `fom_facturas_temporales_venta` (
  `id` bigint(12) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `id_cliente` int(8) unsigned zerofill NOT NULL COMMENT 'identificador del cliente al cual se realiza la venta',
  `fecha_factura` datetime NOT NULL COMMENT 'fecha en que se genera la factura',
  `modo_pago` enum('1','2') COLLATE latin1_spanish_ci NOT NULL COMMENT '1= contado, 2 = credito',
  `fecha_vencimiento` date NOT NULL COMMENT 'indica cuando se vence la factura',
  `id_caja` mediumint(8) unsigned zerofill NOT NULL COMMENT 'sede donde se genera la factura',
  `id_usuario` int(8) unsigned zerofill NOT NULL COMMENT 'usuario que genera la venta',
  `subtotal` decimal(15,2) NOT NULL COMMENT 'subtotal de la compra',
  `iva` decimal(15,2) NOT NULL COMMENT 'valor del iva',
  `concepto1` varchar(100) COLLATE latin1_spanish_ci NOT NULL COMMENT 'concepto del descuento',
  `descuento1` smallint(2) NOT NULL COMMENT 'total del descuento',
  `concepto2` varchar(100) COLLATE latin1_spanish_ci NOT NULL COMMENT 'concepto del descuento',
  `descuento2` smallint(2) NOT NULL COMMENT 'total del descuento',
  `valor_flete` decimal(15,2) NOT NULL COMMENT 'valor del flete de la mercancia',
  `total` decimal(15,2) NOT NULL COMMENT 'total',
  `estado_factura` enum('1','2') COLLATE latin1_spanish_ci NOT NULL COMMENT '1= abiera, 2 = cerrada',
  `observaciones` text COLLATE latin1_spanish_ci NOT NULL COMMENT 'observaciones',
  `porcentaje_dcto_1` mediumint(3) NOT NULL COMMENT '% dcto pago temprano',
  `fecha_limite_dcto_1` date NOT NULL COMMENT 'fecha limite para aplicar el %',
  `porcentaje_dcto_2` mediumint(3) NOT NULL COMMENT '% dcto pago temprano',
  `fecha_limite_dcto_2` date NOT NULL COMMENT 'fecha limite para aplicar el %',
  `id_notificacion` bigint(15) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_cliente` (`id_cliente`),
  KEY `id_caja` (`id_caja`),
  KEY `id_usuario` (`id_usuario`),
  KEY `id_notificacion` (`id_notificacion`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='va almacenando la info. de cada factura de venta que por al';

-- --------------------------------------------------------

--
-- Table structure for table `fom_facturas_venta`
--

DROP TABLE IF EXISTS `fom_facturas_venta`;
CREATE TABLE IF NOT EXISTS `fom_facturas_venta` (
  `id` bigint(12) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `id_resolucion` mediumint(8) unsigned zerofill NOT NULL,
  `id_factura` bigint(12) NOT NULL COMMENT 'id de factura segÃºn su resolucion',
  `id_cliente` int(8) unsigned zerofill NOT NULL COMMENT 'identificador del cliente al cual se realiza la venta',
  `fecha_factura` datetime NOT NULL COMMENT 'fecha en que se genera la factura',
  `fecha_vencimiento` date NOT NULL COMMENT 'Fecha de vencimiento de una factura generada a credito',
  `modo_pago` enum('1','2') COLLATE latin1_spanish_ci NOT NULL COMMENT '1= contado, 2 = credito',
  `id_caja` mediumint(8) unsigned zerofill NOT NULL COMMENT 'sede donde se genera la factura',
  `id_usuario` int(8) unsigned zerofill NOT NULL COMMENT 'usuario que genera la venta',
  `subtotal` decimal(15,2) NOT NULL COMMENT 'subtotal de la venta',
  `iva` decimal(15,2) NOT NULL COMMENT 'valor del iva',
  `concepto1` varchar(100) COLLATE latin1_spanish_ci NOT NULL COMMENT 'concepto del descuento',
  `descuento1` smallint(2) NOT NULL COMMENT 'total del descuento',
  `concepto2` varchar(100) COLLATE latin1_spanish_ci NOT NULL COMMENT 'concepto del descuento',
  `descuento2` smallint(2) NOT NULL COMMENT 'total del descuento',
  `valor_flete` decimal(15,2) NOT NULL COMMENT 'valor del flete para la venta de la factura',
  `total` decimal(15,2) NOT NULL COMMENT 'total',
  `estado_factura` enum('1','2') COLLATE latin1_spanish_ci NOT NULL COMMENT '1= abiera, 2 = cerrada',
  `observaciones` text COLLATE latin1_spanish_ci NOT NULL COMMENT 'observaciones',
  `retenciones` varchar(250) COLLATE latin1_spanish_ci NOT NULL COMMENT 'string del tipo "1;1000|2;2000" . donde el primer numro antes del ; representa el id de la retencion y el segundo numero el valor de dicha retencion, y los valores estaran separados por |',
  `porcentaje_dcto_1` mediumint(5) NOT NULL COMMENT 'porcentaje adicional 1 por pronto pago',
  `fecha_limite_dcto_1` date NOT NULL COMMENT 'fecha limite de pago para poder acceder al dcto',
  `porcentaje_dcto_2` mediumint(5) NOT NULL COMMENT 'porcentaje adicional 2 por pronto pago',
  `fecha_limite_dcto_2` date NOT NULL COMMENT 'fecha limite de pago para poder acceder al dcto',
  `campo_efectivo` decimal(15,2) NOT NULL COMMENT 'cuanto de la factura se pago en efectivo',
  `campo_tarjeta` decimal(15,2) NOT NULL COMMENT 'cuanto de la factura se pago con tarjeta',
  `campo_cheque` decimal(15,2) NOT NULL COMMENT 'cuanto de la factura se pago con cheque',
  `campo_credito` decimal(15,2) NOT NULL COMMENT 'cuanto de la factura se pago a credito',
  `activo` enum('0','1') COLLATE latin1_spanish_ci NOT NULL DEFAULT '1' COMMENT 'dtermina si el registro esta activo o no',
  PRIMARY KEY (`id`),
  KEY `id_resolucion` (`id_resolucion`),
  KEY `id_cliente` (`id_cliente`),
  KEY `id_caja` (`id_caja`),
  KEY `id_usuario` (`id_usuario`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='almacena la info. de cada factura de venta que se realize';

-- --------------------------------------------------------

--
-- Table structure for table `fom_gondolas`
--

DROP TABLE IF EXISTS `fom_gondolas`;
CREATE TABLE IF NOT EXISTS `fom_gondolas` (
  `id` mediumint(8) unsigned zerofill NOT NULL AUTO_INCREMENT COMMENT 'identificador de la gondola en la bd',
  `id_bodega` mediumint(8) unsigned zerofill NOT NULL COMMENT 'identifica en que bodega se encuentra ubicada la gondola',
  `nombre` varchar(100) NOT NULL COMMENT 'nombre para la gondola',
  `lados` varchar(5) NOT NULL COMMENT 'lados posibles de la gondola,',
  `bandejas` varchar(5) NOT NULL COMMENT 'bandejas que tiene la gondola',
  `activo` enum('0','1') NOT NULL COMMENT 'determina si la gondola esta activa o no',
  PRIMARY KEY (`id`),
  KEY `id_bodega` (`id_bodega`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='almacena la info acerca de las gondolas existentes en la emp';

-- --------------------------------------------------------

--
-- Table structure for table `fom_grupos`
--

DROP TABLE IF EXISTS `fom_grupos`;
CREATE TABLE IF NOT EXISTS `fom_grupos` (
  `id` smallint(3) unsigned zerofill NOT NULL AUTO_INCREMENT COMMENT 'Consecutivo interno de la tabla grupos',
  `nombre` varchar(255) NOT NULL COMMENT 'Nombre del grupo',
  `tipo` enum('I','M') NOT NULL DEFAULT 'I' COMMENT 'Tipo de grupo (I=Inventario, M=Miscelanea)',
  `activo` enum('0','1') NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `fom_imagenes`
--

DROP TABLE IF EXISTS `fom_imagenes`;
CREATE TABLE IF NOT EXISTS `fom_imagenes` (
  `id` bigint(12) unsigned zerofill NOT NULL AUTO_INCREMENT COMMENT 'Consecutivo interno de la tabla imagenes',
  `titulo` varchar(255) NOT NULL COMMENT 'Titulo de la imagen',
  `fecha` datetime DEFAULT NULL COMMENT 'Fecha de creación de la imagen',
  `ruta` varchar(255) NOT NULL COMMENT 'Ruta de la imagen',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='info. sobre las imagnes q se manejen en el sistema';

-- --------------------------------------------------------

--
-- Table structure for table `fom_impuestos`
--

DROP TABLE IF EXISTS `fom_impuestos`;
CREATE TABLE IF NOT EXISTS `fom_impuestos` (
  `id` smallint(3) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(250) COLLATE latin1_spanish_ci NOT NULL COMMENT 'nombre del impuesto',
  `apellido` varchar(250) COLLATE latin1_spanish_ci NOT NULL,
  `direccion` varchar(250) COLLATE latin1_spanish_ci NOT NULL,
  `celular` varchar(30) COLLATE latin1_spanish_ci NOT NULL,
  `aplica_clientes` enum('0','1') COLLATE latin1_spanish_ci NOT NULL COMMENT 'determina si se aplica a clientes',
  `aplica_proveedores` enum('0','1') COLLATE latin1_spanish_ci NOT NULL COMMENT 'determina si se aplica a proveedores',
  `activo` enum('0','1') COLLATE latin1_spanish_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='Tabla que almacena los impuestos existentes en el pais de tr';

-- --------------------------------------------------------

--
-- Table structure for table `fom_impuesto_actividad`
--

DROP TABLE IF EXISTS `fom_impuesto_actividad`;
CREATE TABLE IF NOT EXISTS `fom_impuesto_actividad` (
  `id` bigint(15) NOT NULL,
  `id_actividad_economica` smallint(4) unsigned zerofill NOT NULL,
  `id_impuesto` smallint(3) unsigned NOT NULL,
  `tipo` enum('1','2') COLLATE latin1_spanish_ci NOT NULL COMMENT '1 = porcentaje, 2= pesos por mil',
  `valor` int(10) NOT NULL,
  `activo` enum('0','1') COLLATE latin1_spanish_ci NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='relaciona actividades economicas(1) a impuestos (*)';

-- --------------------------------------------------------

--
-- Table structure for table `fom_inventarios`
--

DROP TABLE IF EXISTS `fom_inventarios`;
CREATE TABLE IF NOT EXISTS `fom_inventarios` (
  `id` bigint(15) unsigned NOT NULL AUTO_INCREMENT,
  `id_articulo` bigint(12) unsigned zerofill NOT NULL,
  `cantidad` bigint(12) NOT NULL,
  `id_bodega` mediumint(8) unsigned zerofill NOT NULL COMMENT 'identificador de la bodega donde se encuentra el articulo',
  PRIMARY KEY (`id`),
  KEY `id_articulo` (`id_articulo`,`id_bodega`),
  KEY `id_bodega` (`id_bodega`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='almacena la cantidad de articulos ingresados en el sistema';

-- --------------------------------------------------------

--
-- Table structure for table `fom_kardex`
--

DROP TABLE IF EXISTS `fom_kardex`;
CREATE TABLE IF NOT EXISTS `fom_kardex` (
  `id` bigint(15) unsigned NOT NULL AUTO_INCREMENT,
  `id_articulo` bigint(12) unsigned zerofill NOT NULL,
  `fecha` datetime NOT NULL,
  `concepto` varchar(250) COLLATE latin1_spanish_ci NOT NULL,
  `num_factura` bigint(12) unsigned zerofill NOT NULL,
  `cantidad_compra` mediumint(8) NOT NULL,
  `val_unitario_compra` decimal(15,2) NOT NULL,
  `val_total_compra` decimal(15,2) NOT NULL,
  `cantidad_venta` mediumint(8) NOT NULL,
  `val_unitario_venta` decimal(15,2) NOT NULL,
  `val_total_venta` decimal(15,2) NOT NULL,
  `cantidad_saldo` int(10) NOT NULL,
  `val_unitario_saldo` decimal(15,2) NOT NULL,
  `total_saldo` decimal(15,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='almacena la info de kardex de todos los articulos';

-- --------------------------------------------------------

--
-- Table structure for table `fom_lineas`
--

DROP TABLE IF EXISTS `fom_lineas`;
CREATE TABLE IF NOT EXISTS `fom_lineas` (
  `id` smallint(3) unsigned zerofill NOT NULL AUTO_INCREMENT COMMENT 'Consecutivo interno de la tabla grupos',
  `nombre` varchar(255) NOT NULL COMMENT 'Nombre del grupo',
  `id_imagen` bigint(12) unsigned zerofill NOT NULL DEFAULT '000000000000',
  `activo` enum('0','1') NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `id_imagen` (`id_imagen`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Stand-in structure for view `fom_lista_ciudades`
--
DROP VIEW IF EXISTS `fom_lista_ciudades`;
CREATE TABLE IF NOT EXISTS `fom_lista_ciudades` (
`id` int(8) unsigned zerofill
,`nombre` varchar(255)
,`cadena` longtext
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `fom_lista_estados`
--
DROP VIEW IF EXISTS `fom_lista_estados`;
CREATE TABLE IF NOT EXISTS `fom_lista_estados` (
`id` mediumint(6) unsigned zerofill
,`nombre` varchar(255)
,`cadena` longtext
);
-- --------------------------------------------------------

--
-- Table structure for table `fom_localidades`
--

DROP TABLE IF EXISTS `fom_localidades`;
CREATE TABLE IF NOT EXISTS `fom_localidades` (
  `id` smallint(4) unsigned zerofill NOT NULL AUTO_INCREMENT COMMENT 'Consecutivo interno de la tabla localidades',
  `id_ciudad` int(8) unsigned zerofill NOT NULL DEFAULT '00000000' COMMENT 'Id de la tabla ciudades',
  `tipo` enum('B','C') DEFAULT 'B' COMMENT 'Tipo (B=Barrio, C=Corregimiento)',
  `codigo_municipal` varchar(3) DEFAULT NULL COMMENT 'Código oficial asignado por el municipio (sólo para barrios)',
  `codigo_dane` varchar(3) DEFAULT NULL COMMENT 'Código DANE (sólo para corregimientos)',
  `nombre` varchar(255) NOT NULL COMMENT 'Nombre de la localidad',
  `comuna` tinyint(2) NOT NULL DEFAULT '0' COMMENT 'Comuna a la que pertenece (sólo para barrios)',
  `estrato` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Estrato al que pertenece (sólo para barrios)',
  PRIMARY KEY (`id`),
  KEY `id_municipio` (`id_ciudad`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `fom_marcas`
--

DROP TABLE IF EXISTS `fom_marcas`;
CREATE TABLE IF NOT EXISTS `fom_marcas` (
  `id` smallint(3) unsigned zerofill NOT NULL AUTO_INCREMENT COMMENT 'Consecutivo interno de la tabla marcas',
  `nombre` varchar(255) NOT NULL COMMENT 'Nombre que identifica',
  `id_imagen` bigint(12) unsigned zerofill NOT NULL DEFAULT '000000000000',
  `activo` enum('0','1') NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `fom_modulos`
--

DROP TABLE IF EXISTS `fom_modulos`;
CREATE TABLE IF NOT EXISTS `fom_modulos` (
  `id` smallint(4) unsigned zerofill NOT NULL AUTO_INCREMENT COMMENT 'Consecutivo interno de la tabla modulos',
  `id_padre` smallint(4) unsigned zerofill NOT NULL DEFAULT '0000' COMMENT 'Id de la tabla modulos',
  `menu` enum('0','1') NOT NULL DEFAULT '0' COMMENT 'Indica si el componente saldra en el menu principal (0=No, 1=Si)',
  `tipo_menu` enum('0','1') NOT NULL DEFAULT '0' COMMENT 'Indica si el tipo de menú (0=Aplicación web, 1=Página Web)',
  `nombre_menu` varchar(50) NOT NULL COMMENT 'Nombre del modulo que saldra en el menu prinicpal',
  `clase` enum('1','2','3','4') CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL DEFAULT '2' COMMENT 'Tipo de módulo para definir su ubicación: 1: Configuración del sitio 2: Configuración personal 3: Uso global 4: otros',
  `orden` smallint(4) unsigned zerofill NOT NULL COMMENT 'Consecutivo para ordenar el elemento dentro de los listados o menús',
  `nombre` varchar(50) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL COMMENT 'Nombre del módulo (en mayúsculas) para su búsqueda en los archivos de textos (idiomas)',
  `url` varchar(50) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL COMMENT 'Texto visible del módulo en la URL (Ej: "news" en http://servidor/news/12)',
  `carpeta` varchar(50) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL COMMENT 'Nombre de la carpeta principal del módulo',
  `visible` enum('0','1') CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL DEFAULT '0' COMMENT 'El módulo es visible en los listados o menús',
  `global` enum('0','1') CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL DEFAULT '0' COMMENT 'Requiere verificación de permisos: 0=No, 1=Si',
  `tabla_principal` char(255) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL COMMENT 'Nombre de la tabla principal relacionada con el módulo',
  `valida_usuario` enum('0','1') CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL DEFAULT '0' COMMENT 'Define si se debe verificar el propietario del registro en la tabla principal : 0=No, 1=Si',
  `documentacion` text NOT NULL COMMENT 'documentacion funcional del modulo',
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`),
  KEY `id_padre` (`id_padre`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='info. basica d los distintos modulos q componen el sistema';

-- --------------------------------------------------------

--
-- Table structure for table `fom_modulos_bu`
--

DROP TABLE IF EXISTS `fom_modulos_bu`;
CREATE TABLE IF NOT EXISTS `fom_modulos_bu` (
  `id` smallint(4) unsigned zerofill NOT NULL AUTO_INCREMENT COMMENT 'Consecutivo interno de la tabla modulos',
  `id_padre` smallint(4) unsigned zerofill NOT NULL DEFAULT '0000' COMMENT 'Id de la tabla modulos',
  `menu` enum('0','1') NOT NULL DEFAULT '0' COMMENT 'Indica si el componente saldra en el menu principal (0=No, 1=Si)',
  `tipo_menu` enum('0','1') NOT NULL DEFAULT '0' COMMENT 'Indica si el tipo de menú (0=Aplicación web, 1=Página Web)',
  `nombre_menu` varchar(50) NOT NULL COMMENT 'Nombre del modulo que saldra en el menu prinicpal',
  `clase` enum('1','2','3','4') CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL DEFAULT '2' COMMENT 'Tipo de módulo para definir su ubicación: 1: Configuración del sitio 2: Configuración personal 3: Uso global 4: otros',
  `orden` smallint(4) unsigned zerofill NOT NULL COMMENT 'Consecutivo para ordenar el elemento dentro de los listados o menús',
  `nombre` varchar(50) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL COMMENT 'Nombre del módulo (en mayúsculas) para su búsqueda en los archivos de textos (idiomas)',
  `url` varchar(50) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL COMMENT 'Texto visible del módulo en la URL (Ej: "news" en http://servidor/news/12)',
  `carpeta` varchar(50) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL COMMENT 'Nombre de la carpeta principal del módulo',
  `visible` enum('0','1') CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL DEFAULT '0' COMMENT 'El módulo es visible en los listados o menús',
  `global` enum('0','1') CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL DEFAULT '0' COMMENT 'Requiere verificación de permisos: 0=No, 1=Si',
  `tabla_principal` char(255) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL COMMENT 'Nombre de la tabla principal relacionada con el módulo',
  `valida_usuario` enum('0','1') CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL DEFAULT '0' COMMENT 'Define si se debe verificar el propietario del registro en la tabla principal : 0=No, 1=Si',
  `documentacion` text NOT NULL COMMENT 'documentacion funcional del modulo',
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`),
  KEY `id_padre` (`id_padre`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='info. basica d los distintos modulos q componen el sistema';

-- --------------------------------------------------------

--
-- Table structure for table `fom_motos`
--

DROP TABLE IF EXISTS `fom_motos`;
CREATE TABLE IF NOT EXISTS `fom_motos` (
  `id` mediumint(6) unsigned zerofill NOT NULL AUTO_INCREMENT COMMENT 'Consecutivo interno de la tabla motos',
  `id_marca` smallint(3) unsigned zerofill NOT NULL COMMENT 'identificador de la linea a la que pertenece',
  `nombre` varchar(255) NOT NULL COMMENT 'Nombre de la moto',
  `id_imagen` bigint(12) unsigned zerofill NOT NULL DEFAULT '000000000000',
  `archivo` varchar(100) NOT NULL COMMENT 'archivo que representa el catalogo de la moto',
  `activo` enum('0','1') NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `id_linea` (`id_marca`),
  KEY `id_imagen` (`id_imagen`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `fom_movimientos_mercancia`
--

DROP TABLE IF EXISTS `fom_movimientos_mercancia`;
CREATE TABLE IF NOT EXISTS `fom_movimientos_mercancia` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_articulo` bigint(12) unsigned NOT NULL,
  `cantidad` mediumint(7) NOT NULL,
  `id_bodega_origen` mediumint(8) unsigned zerofill NOT NULL,
  `id_bodega_destino` mediumint(8) unsigned zerofill NOT NULL,
  `id_usuario` int(8) unsigned NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id_articulo` (`id_articulo`),
  KEY `id_bodega_origen` (`id_bodega_origen`),
  KEY `id_bodega_destino` (`id_bodega_destino`),
  KEY `id_usuario` (`id_usuario`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='Tabla que registra los movimientos de mercancia realizados d';

-- --------------------------------------------------------

--
-- Table structure for table `fom_notas_credito_clientes`
--

DROP TABLE IF EXISTS `fom_notas_credito_clientes`;
CREATE TABLE IF NOT EXISTS `fom_notas_credito_clientes` (
  `id` bigint(12) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `id_factura` bigint(12) unsigned zerofill NOT NULL,
  `monto_nota` int(15) NOT NULL,
  `iva_nota` int(10) NOT NULL,
  `concepto_nota` text COLLATE latin1_spanish_ci NOT NULL,
  `fecha_nota` date NOT NULL,
  `inventario_modificado` enum('0','1') COLLATE latin1_spanish_ci NOT NULL DEFAULT '0' COMMENT 'determina si se modificaron cantidades de articulos',
  PRIMARY KEY (`id`),
  KEY `id_factura` (`id_factura`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='almacena la info de las notas credito generadas a una factur';

-- --------------------------------------------------------

--
-- Table structure for table `fom_notas_credito_proveedores`
--

DROP TABLE IF EXISTS `fom_notas_credito_proveedores`;
CREATE TABLE IF NOT EXISTS `fom_notas_credito_proveedores` (
  `id` bigint(12) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `id_factura` bigint(12) unsigned zerofill NOT NULL,
  `monto_nota` int(15) NOT NULL,
  `iva_nota` int(10) NOT NULL,
  `concepto_nota` text COLLATE latin1_spanish_ci NOT NULL,
  `fecha_nota` date NOT NULL,
  `archivo` varchar(100) COLLATE latin1_spanish_ci NOT NULL,
  `inventario_modificado` enum('0','1') COLLATE latin1_spanish_ci NOT NULL DEFAULT '0' COMMENT 'determina si se modificaron cantidades de articulos',
  PRIMARY KEY (`id`),
  KEY `id_factura` (`id_factura`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='almacena la info de las notas credito enviadas por los prove';

-- --------------------------------------------------------

--
-- Table structure for table `fom_notas_debito_clientes`
--

DROP TABLE IF EXISTS `fom_notas_debito_clientes`;
CREATE TABLE IF NOT EXISTS `fom_notas_debito_clientes` (
  `id` bigint(12) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `id_factura` bigint(12) unsigned zerofill NOT NULL,
  `monto_nota` int(15) NOT NULL,
  `iva_nota` int(10) NOT NULL,
  `concepto_nota` text COLLATE latin1_spanish_ci NOT NULL,
  `fecha_nota` date NOT NULL,
  `inventario_modificado` enum('0','1') COLLATE latin1_spanish_ci NOT NULL DEFAULT '0' COMMENT 'determina si se modificaron cantidades de articulos',
  PRIMARY KEY (`id`),
  KEY `id_factura` (`id_factura`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='almacena la info de las notas debito generadas a una factura';

-- --------------------------------------------------------

--
-- Table structure for table `fom_notas_debito_proveedores`
--

DROP TABLE IF EXISTS `fom_notas_debito_proveedores`;
CREATE TABLE IF NOT EXISTS `fom_notas_debito_proveedores` (
  `id` bigint(12) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `id_factura` bigint(12) unsigned zerofill NOT NULL,
  `monto_nota` int(15) NOT NULL,
  `iva_nota` int(10) NOT NULL,
  `concepto_nota` text COLLATE latin1_spanish_ci NOT NULL,
  `fecha_nota` date NOT NULL,
  `archivo` varchar(100) COLLATE latin1_spanish_ci NOT NULL,
  `inventario_modificado` enum('0','1') COLLATE latin1_spanish_ci NOT NULL DEFAULT '0' COMMENT 'determina si se modificaron cantidades de articulos',
  PRIMARY KEY (`id`),
  KEY `id_factura` (`id_factura`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='almacena la info de las notas debito enviadas por los prove';

-- --------------------------------------------------------

--
-- Table structure for table `fom_notificaciones`
--

DROP TABLE IF EXISTS `fom_notificaciones`;
CREATE TABLE IF NOT EXISTS `fom_notificaciones` (
  `id` bigint(15) unsigned NOT NULL AUTO_INCREMENT,
  `id_usuario` int(8) unsigned NOT NULL COMMENT 'id del usuario propietario de la notificacion',
  `fecha` datetime NOT NULL COMMENT 'fecha en la cual se hizo la notificacion',
  `contenido` text COLLATE latin1_spanish_ci NOT NULL COMMENT 'almacena el contenido de la notificacion',
  `leido` enum('0','1') CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL COMMENT 'determina si el item se encuentra activo o no',
  `id_modulo` smallint(4) unsigned zerofill NOT NULL,
  `id_registro` int(10) NOT NULL COMMENT 'identificador del registro',
  PRIMARY KEY (`id`),
  KEY `id_usuario` (`id_usuario`),
  KEY `id_modulo` (`id_modulo`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='almacena las notificaciones de los usuarios del sistema almo';

-- --------------------------------------------------------

--
-- Table structure for table `fom_ordenes_compra`
--

DROP TABLE IF EXISTS `fom_ordenes_compra`;
CREATE TABLE IF NOT EXISTS `fom_ordenes_compra` (
  `id` bigint(12) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `id_orden` varchar(50) COLLATE latin1_spanish_ci NOT NULL COMMENT 'id de factura donde se concatena la sede, y la forma de impresion',
  `id_proveedor` int(8) unsigned zerofill NOT NULL COMMENT 'identificador del proveedor al cual se realiza la compra',
  `fecha_orden` datetime NOT NULL COMMENT 'fecha en que se genera la factura',
  `id_caja` mediumint(8) unsigned zerofill NOT NULL COMMENT 'sede donde se genera la orden',
  `id_usuario` int(8) unsigned zerofill NOT NULL COMMENT 'usuario que genera la compra',
  `subtotal` decimal(15,2) NOT NULL COMMENT 'subtotal de la compra',
  `iva` decimal(15,2) NOT NULL COMMENT 'valor del iva',
  `concepto1` varchar(100) COLLATE latin1_spanish_ci NOT NULL COMMENT 'concepto del descuento',
  `descuento1` smallint(2) NOT NULL COMMENT 'total del descuento',
  `concepto2` varchar(100) COLLATE latin1_spanish_ci NOT NULL COMMENT 'concepto del descuento',
  `descuento2` smallint(2) NOT NULL COMMENT 'total del descuento',
  `valor_flete` decimal(15,2) NOT NULL COMMENT 'valor del flete de la mercancia',
  `total` decimal(15,2) NOT NULL COMMENT 'total',
  `observaciones` text COLLATE latin1_spanish_ci NOT NULL COMMENT 'observaciones',
  `activo` enum('0','1') COLLATE latin1_spanish_ci NOT NULL COMMENT 'determina si la orden se encuentra activa o no',
  PRIMARY KEY (`id`),
  KEY `id_proveedor` (`id_proveedor`),
  KEY `id_orden` (`id_orden`),
  KEY `id_caja` (`id_caja`),
  KEY `id_usuario` (`id_usuario`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='almacena la info. de cada orden de compras que se realizen';

-- --------------------------------------------------------

--
-- Table structure for table `fom_paginas`
--

DROP TABLE IF EXISTS `fom_paginas`;
CREATE TABLE IF NOT EXISTS `fom_paginas` (
  `id` mediumint(6) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Consecutivo interno para la base de datos',
  `id_usuario` int(8) unsigned zerofill NOT NULL COMMENT 'Consecutivo interno del usuario que publica la página',
  `titulo` varchar(255) NOT NULL COMMENT 'Titulo de la página',
  `contenido` longtext COMMENT 'Contenido de la página',
  `fecha_creacion` datetime NOT NULL COMMENT 'Fecha de creación de la página',
  `fecha_publicacion` datetime DEFAULT NULL COMMENT 'Fecha de publicación de la página',
  `fecha_actualizacion` datetime DEFAULT NULL COMMENT 'Fecha de ultima actualizacion de la página',
  `activo` enum('0','1') NOT NULL DEFAULT '1' COMMENT 'La página se encuentra activa: 0 = No, 1= Si',
  `orden` smallint(4) unsigned zerofill NOT NULL,
  PRIMARY KEY (`id`),
  KEY `pagina_usuario` (`id_usuario`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='paginas web publicadas desde un editor wysiwyg';

-- --------------------------------------------------------

--
-- Table structure for table `fom_paises`
--

DROP TABLE IF EXISTS `fom_paises`;
CREATE TABLE IF NOT EXISTS `fom_paises` (
  `id` smallint(3) unsigned zerofill NOT NULL AUTO_INCREMENT COMMENT 'Consecutivo interno para la base de datos',
  `codigo_iso` varchar(2) NOT NULL COMMENT 'C�digo ISO',
  `nombre` varchar(255) NOT NULL DEFAULT '' COMMENT 'Nombre del pa�s',
  `codigo_comercial` varchar(10) DEFAULT 'COD' COMMENT 'codigo utilizado comercialmente',
  PRIMARY KEY (`id`),
  UNIQUE KEY `codigo_iso` (`codigo_iso`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `fom_perfiles`
--

DROP TABLE IF EXISTS `fom_perfiles`;
CREATE TABLE IF NOT EXISTS `fom_perfiles` (
  `id` smallint(3) unsigned zerofill NOT NULL AUTO_INCREMENT COMMENT 'Consecutivo interno de la tabla tipos de usuario',
  `nombre` varchar(50) NOT NULL COMMENT 'Nombre del tipo de usuario',
  `visibilidad` enum('0','1') NOT NULL DEFAULT '0' COMMENT 'Visibilidad del perfil del usuario (0=No, 1=Si)',
  `activo` enum('0','1') NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='tipos de usuarios del sistema';

-- --------------------------------------------------------

--
-- Table structure for table `fom_periodos_contables`
--

DROP TABLE IF EXISTS `fom_periodos_contables`;
CREATE TABLE IF NOT EXISTS `fom_periodos_contables` (
  `id` bigint(8) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `codigo` varchar(50) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `fecha_inicial` date NOT NULL,
  `fecha_final` date NOT NULL,
  `activo` enum('0','1') CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `Codigo` (`codigo`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `fom_permisos_componentes_perfiles`
--

DROP TABLE IF EXISTS `fom_permisos_componentes_perfiles`;
CREATE TABLE IF NOT EXISTS `fom_permisos_componentes_perfiles` (
  `id_componente` mediumint(6) unsigned zerofill NOT NULL COMMENT 'Id de la tabla componentes modulo',
  `id_perfil` int(8) unsigned zerofill NOT NULL COMMENT 'Id de la tabla perfiles',
  KEY `id_componente` (`id_componente`),
  KEY `id_usuario` (`id_perfil`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fom_permisos_componentes_usuarios`
--

DROP TABLE IF EXISTS `fom_permisos_componentes_usuarios`;
CREATE TABLE IF NOT EXISTS `fom_permisos_componentes_usuarios` (
  `id_componente` mediumint(6) unsigned zerofill NOT NULL COMMENT 'Id de la tabla componentes modulo',
  `id_sede` mediumint(8) unsigned zerofill NOT NULL COMMENT 'Id de la tabla sedes empresa',
  `id_usuario` int(8) unsigned zerofill NOT NULL COMMENT 'Id de la tabla usuarios',
  KEY `id_componente` (`id_componente`),
  KEY `id_usuario` (`id_usuario`),
  KEY `id_sede` (`id_sede`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fom_permisos_modulos_perfiles`
--

DROP TABLE IF EXISTS `fom_permisos_modulos_perfiles`;
CREATE TABLE IF NOT EXISTS `fom_permisos_modulos_perfiles` (
  `id_modulo` smallint(4) unsigned zerofill NOT NULL COMMENT 'Id de la tabla modulos',
  `id_perfil` int(8) unsigned zerofill NOT NULL COMMENT 'Id de la tabla perfiles',
  KEY `id_modulo` (`id_modulo`),
  KEY `id_usuario` (`id_perfil`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fom_permisos_modulos_usuarios`
--

DROP TABLE IF EXISTS `fom_permisos_modulos_usuarios`;
CREATE TABLE IF NOT EXISTS `fom_permisos_modulos_usuarios` (
  `id_modulo` smallint(4) unsigned zerofill NOT NULL COMMENT 'Id de la tabla modulos',
  `id_sede` mediumint(8) unsigned zerofill NOT NULL COMMENT 'Id de la tabla sedes empresa',
  `id_usuario` int(8) unsigned zerofill NOT NULL COMMENT 'Id de la tabla usuarios',
  KEY `id_modulo` (`id_modulo`),
  KEY `id_usuario` (`id_usuario`),
  KEY `id_sede` (`id_sede`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fom_personas`
--

DROP TABLE IF EXISTS `fom_personas`;
CREATE TABLE IF NOT EXISTS `fom_personas` (
  `id` int(8) unsigned zerofill NOT NULL AUTO_INCREMENT COMMENT 'Consecutivo interno de la tabla personas',
  `documento_identidad` varchar(20) NOT NULL COMMENT 'Número del documento de identidad',
  `id_tipo_documento` smallint(2) unsigned zerofill NOT NULL COMMENT 'Id de la tabla tipos documento identidad',
  `id_ciudad_documento` int(8) unsigned zerofill NOT NULL DEFAULT '00000000' COMMENT 'Id de la tabla municipios',
  `primer_nombre` varchar(30) NOT NULL DEFAULT '' COMMENT 'Primer nombre (persona natural)',
  `segundo_nombre` varchar(30) NOT NULL DEFAULT '' COMMENT 'Segundo nombre (persona natural)',
  `primer_apellido` varchar(30) NOT NULL DEFAULT '' COMMENT 'Primer apellido (persona natural)',
  `segundo_apellido` varchar(30) NOT NULL DEFAULT '' COMMENT 'Segundo apellido (persona natural)',
  `fecha_nacimiento` date DEFAULT NULL COMMENT 'Fecha de nacimiento de la persona ó constitución de la sociedad',
  `id_ciudad_residencia` int(8) unsigned zerofill NOT NULL DEFAULT '00000000' COMMENT 'Id de la tabla municipios',
  `direccion` varchar(255) DEFAULT NULL COMMENT 'Dirección de residencia de la persona',
  `telefono` varchar(30) DEFAULT NULL COMMENT 'Número de teléfono de la persona',
  `celular` varchar(30) DEFAULT NULL COMMENT 'Número de celular de la persona',
  `fax` varchar(30) DEFAULT NULL COMMENT 'Número de fax de la persona',
  `correo` varchar(255) DEFAULT NULL COMMENT 'Dirección de correo electrónico de la persona',
  `sitio_web` varchar(255) DEFAULT NULL COMMENT 'Dirección del sitio web de la persona',
  `genero` enum('M','F','N') NOT NULL DEFAULT 'N' COMMENT 'Género de la persona (M=Masculino, F=Femenino, N=No aplica)',
  `id_imagen` bigint(12) unsigned zerofill NOT NULL DEFAULT '000000000000' COMMENT 'Id de la tabla imagenes',
  `observaciones` varchar(255) DEFAULT 'no registra.' COMMENT 'Descripción corta de la persona',
  `activo` enum('0','1') NOT NULL DEFAULT '1' COMMENT 'Indica si el tercero está activo (0=No, 1=Si)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `documento_identidad` (`documento_identidad`),
  KEY `id_tipo_documento` (`id_tipo_documento`),
  KEY `id_municipio_documento` (`id_ciudad_documento`),
  KEY `id_municipio_residencia` (`id_ciudad_residencia`),
  KEY `id_imagen` (`id_imagen`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `fom_plan_contable`
--

DROP TABLE IF EXISTS `fom_plan_contable`;
CREATE TABLE IF NOT EXISTS `fom_plan_contable` (
  `id` mediumint(6) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Consecutivo interno de la base de datos',
  `nombre` varchar(50) NOT NULL DEFAULT 'Cuenta base' COMMENT 'nombre corto de la cuenta',
  `codigo_contable` varchar(15) NOT NULL COMMENT 'Código definido por el PUC(Plan unico de cuentas)',
  `descripcion` text NOT NULL COMMENT 'Detalle que describe la cuenta contable',
  `id_cuenta_padre` mediumint(6) unsigned DEFAULT NULL COMMENT 'Cuenta de nivel superior dentro de la estructura',
  `nivel` smallint(2) unsigned zerofill NOT NULL COMMENT 'Número de orden que tiene dentro de la estructura según la cuenta padre',
  `naturaleza_cuenta` enum('D','C') NOT NULL COMMENT 'Naturaleza cuenta: D->Debito C->Credito',
  `clase_cuenta` enum('1','2') NOT NULL COMMENT '1->Cuenta de movimiento la cual no podra ser padre y registra transacciones, 2->Cuenta mayor donde no se pueden registrar transacciones',
  `tipo_cuenta` enum('1','2','3') NOT NULL COMMENT '1->Cuenta de balance 2->Ganacias y perdidas 3->Cuenta de orden',
  `clasificacion` enum('K','G','C','S','A') NOT NULL DEFAULT 'S' COMMENT 'Clasificacion: K => clase , G => grupo, , C=> cuenta, S => subcuenta, A => auxiliar',
  `id_anexo_contable` smallint(3) unsigned zerofill DEFAULT NULL COMMENT 'Id de la tabla de anexos contables(ver tabla de subcuentas)',
  `id_tasa_aplicar_1` smallint(3) unsigned zerofill DEFAULT NULL COMMENT 'Para las cuentas de impuestos y gravámenes que requieren un valor base a ser reportado, como el iva, la retención en la fuente, ica y demás se debe colocar el código de tasa ',
  `id_tasa_aplicar_2` smallint(3) unsigned zerofill DEFAULT NULL COMMENT 'Para las cuentas de impuestos y gravámenes que requieren un valor base a ser reportado, como el iva, la retención en la fuente, ica y demás se debe colocar el código de tasa ',
  `id_concepto_DIAN` smallint(4) unsigned zerofill DEFAULT NULL COMMENT 'Código del concepto asignado por la DIAN para los informes de medios magnéticos ',
  `tipo_certificado` enum('1','2','3','4','5') NOT NULL COMMENT 'Con este parámetro se identifican las cuentas de retenciones  para las cuales se requiere expedir el certificado a terceros, 1->No aplica, 2-> Retención en la fuente 3-> industria y comercio (ica), 4-> Retención de iva, 5 => retecree',
  `flujo_efectivo` enum('1','2','3') NOT NULL COMMENT '1->No afecta flujo 2->Caja 3->Bancos',
  `activo` enum('0','1') NOT NULL DEFAULT '1' COMMENT 'determina si el registro se encuentra activo',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='informacion del plan contable uilizado en el sistema';

-- --------------------------------------------------------

--
-- Table structure for table `fom_precios_articulo_bodega`
--

DROP TABLE IF EXISTS `fom_precios_articulo_bodega`;
CREATE TABLE IF NOT EXISTS `fom_precios_articulo_bodega` (
  `id` bigint(12) NOT NULL AUTO_INCREMENT COMMENT 'id autonumerico',
  `id_articulo` bigint(12) unsigned zerofill NOT NULL COMMENT 'id de la bodega',
  `id_bodega` mediumint(8) unsigned zerofill NOT NULL COMMENT 'id del articulo',
  `concepto` varchar(250) COLLATE latin1_spanish_ci NOT NULL COMMENT 'Concepto del precio',
  `precio` bigint(12) NOT NULL COMMENT 'precio del articulo',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='almacena la relacion precios -articulos / bodega';

-- --------------------------------------------------------

--
-- Table structure for table `fom_profesiones_oficios`
--

DROP TABLE IF EXISTS `fom_profesiones_oficios`;
CREATE TABLE IF NOT EXISTS `fom_profesiones_oficios` (
  `id` smallint(4) unsigned zerofill NOT NULL AUTO_INCREMENT COMMENT 'Consecutivo interno de la tabla profesiones oficios',
  `codigo_DANE` smallint(4) unsigned zerofill NOT NULL COMMENT 'Código universal que identifica una profesión u oficio aprobado por el DANE',
  `descripcion` varchar(255) NOT NULL COMMENT 'Descripción de la profesión u oficio',
  PRIMARY KEY (`id`),
  UNIQUE KEY `codigo_DANE` (`codigo_DANE`),
  UNIQUE KEY `descripcion` (`descripcion`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `fom_proveedores`
--

DROP TABLE IF EXISTS `fom_proveedores`;
CREATE TABLE IF NOT EXISTS `fom_proveedores` (
  `id` int(8) unsigned zerofill NOT NULL AUTO_INCREMENT COMMENT 'Consecutivo interno para la base de datos',
  `id_proveedor` varchar(50) COLLATE latin1_spanish_ci NOT NULL COMMENT 'identificador del proveedor',
  `nombre` varchar(250) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL COMMENT 'nombre comercial de la empresa',
  `razon_social` varchar(250) COLLATE latin1_spanish_ci NOT NULL COMMENT 'razon social del proveedor',
  `tipo_persona` enum('1','2','3') COLLATE latin1_spanish_ci NOT NULL COMMENT '1=natural, 2= juridica, 3= codigo interno',
  `regimen` enum('1','2','3','4','5','6','7','8') COLLATE latin1_spanish_ci DEFAULT '1' COMMENT '1->Regimen simplificado 2->Regimen comun 3->Gran contribuyente 4->Gran contribuyente autoretenedor 5->Simplificado no residente 6->No residente 7->Empresa del estado 8->No responsable',
  `autoretenedor` enum('0','1') COLLATE latin1_spanish_ci NOT NULL DEFAULT '0' COMMENT 'Autoretenedor 0->No 1->Si',
  `retefuente` enum('0','1') COLLATE latin1_spanish_ci NOT NULL COMMENT '1 = el proveedor retiene fuente',
  `reteica` enum('0','1') COLLATE latin1_spanish_ci NOT NULL COMMENT '1 = el proveedor retiene ica',
  `retecree` enum('0','1') COLLATE latin1_spanish_ci NOT NULL DEFAULT '0' COMMENT 'impuesto retecre',
  `call_center` varchar(15) COLLATE latin1_spanish_ci DEFAULT NULL COMMENT 'Numero de telefono de contacto con el proveedor, como call center',
  `activo` enum('0','1') COLLATE latin1_spanish_ci NOT NULL DEFAULT '0' COMMENT 'El proveedor se encuentra activo: 0->No, 1->Si',
  `id_actividad_economica` smallint(4) unsigned zerofill NOT NULL COMMENT 'id actividad economica dian',
  `id_usuario_crea` smallint(4) unsigned zerofill NOT NULL COMMENT 'identificador de la tabla de usuarios del usuario que crea el articulo',
  `fecha_creacion` datetime NOT NULL COMMENT 'Fecha en que se crea el proveedor',
  `observaciones` text COLLATE latin1_spanish_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_proveedor` (`id_proveedor`),
  KEY `id_usuario_crea` (`id_usuario_crea`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fom_resoluciones`
--

DROP TABLE IF EXISTS `fom_resoluciones`;
CREATE TABLE IF NOT EXISTS `fom_resoluciones` (
  `id` mediumint(8) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `id_sede` mediumint(8) unsigned NOT NULL COMMENT 'sede que utiliza esta resolucion',
  `prefijo` varchar(15) COLLATE latin1_spanish_ci DEFAULT NULL COMMENT 'Prefijo que pueda a llegar a tener las facturas de esta resolucion',
  `numero` bigint(12) NOT NULL COMMENT 'numero de la resolucion',
  `fecha_resolucion` date NOT NULL COMMENT 'fecha en que se expide la resolucion',
  `num_factura_inicio` varchar(50) COLLATE latin1_spanish_ci NOT NULL COMMENT 'numero de la factura con la que inicia esta resolcucion',
  `num_factura_final` varchar(50) COLLATE latin1_spanish_ci NOT NULL COMMENT 'numero de la factura con la que finaliza esta resolcucion',
  `fecha_inicio` date NOT NULL COMMENT 'fecha en la que inicia esta resolucion',
  `fecha_final` date NOT NULL COMMENT 'fecha en la que finaliza esta resolucion',
  `numero_facturas_alerta` mediumint(8) unsigned NOT NULL COMMENT 'numero de facturas restantes donde el sistema debe avisar de que esta por acabarse la resolucion',
  `id_factura_retoma` bigint(12) NOT NULL COMMENT 'si se retoma la resolucion desde una factura especifica',
  `activo` enum('0','1') COLLATE latin1_spanish_ci NOT NULL COMMENT 'si la resolucion se encuentra activa o no',
  PRIMARY KEY (`id`),
  KEY `id_sede` (`id_sede`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='resoluciones expedidas por la Dian para la organizacion';

-- --------------------------------------------------------

--
-- Table structure for table `fom_sedes_cliente`
--

DROP TABLE IF EXISTS `fom_sedes_cliente`;
CREATE TABLE IF NOT EXISTS `fom_sedes_cliente` (
  `id` int(8) unsigned zerofill NOT NULL AUTO_INCREMENT COMMENT 'Consecutivo interno de la tabla sedes',
  `id_cliente` int(8) unsigned zerofill NOT NULL COMMENT 'Id de la tabla clientes',
  `nombre` varchar(255) NOT NULL COMMENT 'Nombre la sede',
  `id_ciudad` int(8) unsigned zerofill NOT NULL COMMENT 'Id de la tabla municipios',
  `direccion` varchar(255) NOT NULL COMMENT 'Dirección de la sede',
  `telefono` varchar(30) DEFAULT NULL COMMENT 'Teléfono de la sede',
  `celular` varchar(15) NOT NULL COMMENT 'Celular de la sede del  cliente',
  `fax` varchar(30) DEFAULT NULL COMMENT 'Teléfono 2 de la sede',
  `principal` enum('0','1') NOT NULL DEFAULT '0' COMMENT 'Determina si esta sede es o no la principal',
  PRIMARY KEY (`id`),
  KEY `id_cliente` (`id_cliente`),
  KEY `id_ciudad` (`id_ciudad`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `fom_sedes_empresa`
--

DROP TABLE IF EXISTS `fom_sedes_empresa`;
CREATE TABLE IF NOT EXISTS `fom_sedes_empresa` (
  `id` mediumint(8) unsigned zerofill NOT NULL AUTO_INCREMENT COMMENT 'identificador de la sede de la empresa en la BD',
  `nombre` varchar(255) NOT NULL COMMENT 'nombre de la sede',
  `id_ciudad` int(8) unsigned zerofill NOT NULL COMMENT 'ciudad de ubicacion de la sede',
  `direccion` varchar(150) NOT NULL COMMENT 'direccion de la sede',
  `celular` varchar(50) NOT NULL COMMENT 'telefono principal',
  `telefono` varchar(50) DEFAULT NULL COMMENT 'telefono secundario',
  `fax` varchar(50) NOT NULL COMMENT 'fax de la sede',
  `email` varchar(255) NOT NULL COMMENT 'email de contacto con la sede',
  `fecha_apertura` date DEFAULT NULL COMMENT 'fecha de apertura de la sede',
  `fecha_cierre` date DEFAULT NULL COMMENT 'fecha de cierre de la sede',
  `activo` enum('0','1') NOT NULL COMMENT 'determina si la sede esta activa o no',
  PRIMARY KEY (`id`),
  KEY `id_ciudad` (`id_ciudad`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='tabla que almacena la información de las sedes de una empre';

-- --------------------------------------------------------

--
-- Table structure for table `fom_sedes_proveedor`
--

DROP TABLE IF EXISTS `fom_sedes_proveedor`;
CREATE TABLE IF NOT EXISTS `fom_sedes_proveedor` (
  `id` int(8) unsigned zerofill NOT NULL AUTO_INCREMENT COMMENT 'Consecutivo interno de la tabla sedes',
  `id_proveedor` int(8) unsigned zerofill NOT NULL COMMENT 'Id de la tabla proveedores',
  `nombre` varchar(255) NOT NULL COMMENT 'Nombre la sede',
  `id_ciudad` int(8) unsigned zerofill NOT NULL COMMENT 'Id de la tabla municipios',
  `direccion` varchar(255) NOT NULL COMMENT 'DirecciÃ³n de la sede',
  `telefono` varchar(30) DEFAULT NULL COMMENT 'TelÃ©fono de la sede',
  `celular` varchar(15) NOT NULL COMMENT 'celular de la sede',
  `fax` varchar(30) DEFAULT NULL COMMENT 'TelÃ©fono 2 de la sede',
  `principal` enum('0','1') NOT NULL DEFAULT '0' COMMENT 'Determina si esta sede es o no el principal',
  PRIMARY KEY (`id`),
  KEY `id_proveedor` (`id_proveedor`),
  KEY `id_ciudad` (`id_ciudad`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `fom_subgrupos`
--

DROP TABLE IF EXISTS `fom_subgrupos`;
CREATE TABLE IF NOT EXISTS `fom_subgrupos` (
  `id` smallint(3) unsigned zerofill NOT NULL AUTO_INCREMENT COMMENT 'Consecutivo interno de la tabla subgrupos',
  `id_grupo` smallint(3) unsigned zerofill NOT NULL COMMENT 'Id de la tabla grupos',
  `nombre` varchar(255) NOT NULL COMMENT 'Nombre del subgrupo',
  `activo` enum('0','1') NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_grupo` (`id_grupo`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `fom_tipos_compra`
--

DROP TABLE IF EXISTS `fom_tipos_compra`;
CREATE TABLE IF NOT EXISTS `fom_tipos_compra` (
  `id` smallint(3) unsigned zerofill NOT NULL AUTO_INCREMENT COMMENT 'identificador del registro en la BD',
  `nombre` varchar(200) COLLATE latin1_spanish_ci NOT NULL COMMENT 'nombre del tipo de compra',
  `tipo` enum('1','2','3') COLLATE latin1_spanish_ci NOT NULL COMMENT '''1'' => credito,''2'' => contado, ''3'' => mixto(parte credito-parte contado)',
  `descripcion` text COLLATE latin1_spanish_ci NOT NULL COMMENT 'descripcion del tipo de compra',
  `activo` enum('0','1') COLLATE latin1_spanish_ci NOT NULL COMMENT 'determina si el registro se encuentra activo en la BD',
  `principal` enum('0','1') COLLATE latin1_spanish_ci NOT NULL DEFAULT '1' COMMENT 'determina si es el tipo de compra principal',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='almacena los tipos de compra para la facturacion c';

-- --------------------------------------------------------

--
-- Table structure for table `fom_tipos_documento`
--

DROP TABLE IF EXISTS `fom_tipos_documento`;
CREATE TABLE IF NOT EXISTS `fom_tipos_documento` (
  `id` smallint(2) unsigned zerofill NOT NULL AUTO_INCREMENT COMMENT 'Consecutivo interno de la tabla tipos documento identidad',
  `codigo_dian` smallint(3) unsigned zerofill NOT NULL COMMENT 'Código manejo por la DIAN',
  `nombre` varchar(255) NOT NULL COMMENT 'Nombre del tipo de documento de identidad',
  `activo` enum('0','1') NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `codigo_DIAN` (`codigo_dian`),
  UNIQUE KEY `descripcion` (`nombre`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `fom_tipos_empleado`
--

DROP TABLE IF EXISTS `fom_tipos_empleado`;
CREATE TABLE IF NOT EXISTS `fom_tipos_empleado` (
  `id` mediumint(8) unsigned zerofill NOT NULL AUTO_INCREMENT COMMENT 'identificador en la bd del tipo de empleado',
  `nombre` varchar(200) NOT NULL COMMENT 'nombre del tipo de empleado',
  `activo` enum('0','1') NOT NULL COMMENT 'determina si el registro se encuentra activo o no',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='info. sobre los tipos de empleado, ej: prestador servicio, n';

-- --------------------------------------------------------

--
-- Table structure for table `fom_tipos_unidades`
--

DROP TABLE IF EXISTS `fom_tipos_unidades`;
CREATE TABLE IF NOT EXISTS `fom_tipos_unidades` (
  `id` smallint(2) unsigned zerofill NOT NULL AUTO_INCREMENT COMMENT 'Consecutivo interno de la tabla tipos unidades',
  `nombre` varchar(255) NOT NULL COMMENT 'Nombre que identifica el tipo de unidad',
  `activo` enum('0','1') NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `fom_tipos_venta`
--

DROP TABLE IF EXISTS `fom_tipos_venta`;
CREATE TABLE IF NOT EXISTS `fom_tipos_venta` (
  `id` smallint(3) unsigned zerofill NOT NULL AUTO_INCREMENT COMMENT 'identificador del registro en la BD',
  `nombre` varchar(200) COLLATE latin1_spanish_ci NOT NULL COMMENT 'nombre del tipo de venta',
  `tipo` enum('1','2','3') COLLATE latin1_spanish_ci NOT NULL COMMENT '''1'' => credito,''2'' => contado, ''3'' => mixto(parte credito-parte contado)',
  `descripcion` text COLLATE latin1_spanish_ci NOT NULL COMMENT 'descripcion del tipo de venta',
  `activo` enum('0','1') COLLATE latin1_spanish_ci NOT NULL COMMENT 'determina si el registro se encuentra activo en la BD',
  `principal` enum('0','1') COLLATE latin1_spanish_ci NOT NULL DEFAULT '0' COMMENT 'determina si es el tipo de venta principal a ser utilizado',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='almacena los tipos de venta para la facturacion c';

-- --------------------------------------------------------

--
-- Table structure for table `fom_tipo_compra_cuenta_afectada`
--

DROP TABLE IF EXISTS `fom_tipo_compra_cuenta_afectada`;
CREATE TABLE IF NOT EXISTS `fom_tipo_compra_cuenta_afectada` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_tipo_compra` smallint(3) unsigned NOT NULL COMMENT 'tipo de compra',
  `id_cuenta` mediumint(6) unsigned NOT NULL COMMENT 'cuenta afectada por este tipo de transaccion',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='almacena la relacion entre un tipo de venta y las cuentas';

-- --------------------------------------------------------

--
-- Table structure for table `fom_tipo_venta_cuenta_afectada`
--

DROP TABLE IF EXISTS `fom_tipo_venta_cuenta_afectada`;
CREATE TABLE IF NOT EXISTS `fom_tipo_venta_cuenta_afectada` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_tipo_venta` smallint(3) unsigned NOT NULL COMMENT 'tipo de compra',
  `id_cuenta` mediumint(6) unsigned NOT NULL COMMENT 'cuenta afectada por este tipo de transaccion',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='almacena la relacion entre un tipo de venta y las cuentas';

-- --------------------------------------------------------

--
-- Table structure for table `fom_unidades`
--

DROP TABLE IF EXISTS `fom_unidades`;
CREATE TABLE IF NOT EXISTS `fom_unidades` (
  `id` smallint(2) unsigned zerofill NOT NULL AUTO_INCREMENT COMMENT 'Consecutivo interno de la tabla unidades',
  `id_tipo_unidad` smallint(2) unsigned zerofill NOT NULL COMMENT 'Id de la tabla tipos_unidades',
  `codigo` varchar(10) NOT NULL COMMENT 'Código interno de la unidad',
  `nombre` varchar(255) NOT NULL COMMENT 'Nombre de la unidad de medida',
  `factor_conversion` bigint(12) unsigned NOT NULL DEFAULT '10000' COMMENT 'Factor de conversion en relación a otra unidad',
  `id_unidad_principal` smallint(2) unsigned zerofill NOT NULL COMMENT 'Si la unidad es principal va en cero, de lo contrario va el id de la unidad principal',
  `activo` enum('0','1') NOT NULL COMMENT 'determina si el registro esta activo o no',
  PRIMARY KEY (`id`),
  UNIQUE KEY `codigo` (`codigo`),
  UNIQUE KEY `nombre` (`nombre`),
  KEY `id_tipo_unidad` (`id_tipo_unidad`),
  KEY `id_unidad_principal` (`id_unidad_principal`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `fom_usuarios`
--

DROP TABLE IF EXISTS `fom_usuarios`;
CREATE TABLE IF NOT EXISTS `fom_usuarios` (
  `id` int(8) unsigned zerofill NOT NULL AUTO_INCREMENT COMMENT 'Consecutivo interno de la tabla usuarios',
  `dcto_maximo` smallint(2) NOT NULL DEFAULT '15' COMMENT 'descuento maximo que tiene u usuario del sistema para otorgar a un cliente',
  `id_tipo` smallint(3) unsigned zerofill NOT NULL DEFAULT '099' COMMENT 'Id de la tabla tipos usuario',
  `id_persona` int(8) unsigned zerofill NOT NULL COMMENT 'Id de la tabla personas',
  `usuario` varchar(12) NOT NULL COMMENT 'Nombre de usuario para el acceso',
  `contrasena` char(32) NOT NULL COMMENT 'Contraseña del usuario para el acceso',
  `fecha_registro` datetime NOT NULL COMMENT 'Fecha de registro del usuario',
  `confirmacion` char(32) NOT NULL COMMENT 'Cadena aleatoria para la confirmaciÃ³n de registro de usuarios, entre otras acciones',
  `vendedor` enum('0','1') NOT NULL DEFAULT '0' COMMENT 'define si el usuario puede realizar ventas en el sistema',
  `notificacion_vto_fc` enum('0','1') NOT NULL DEFAULT '0' COMMENT 'determina si un usuario debe ser notificado del vencimiento de una factura de Compra',
  `notificacion_vto_fv` enum('0','1') NOT NULL DEFAULT '0' COMMENT 'determina si un usuario debe ser notificado del vencimiento de una factura de Venta',
  `porcentaje_ganancia` tinyint(2) unsigned NOT NULL COMMENT 'porcentaje de ganancia del vendedor respecto al total de sus ventas',
  `activo` enum('0','1') NOT NULL DEFAULT '1' COMMENT 'El usuario se encuentra activo: 0 = No, 1= Si',
  `bloqueado` enum('0','1') NOT NULL DEFAULT '0' COMMENT 'Este campo determina si un usuario ha sido bloqueado por haber realizado mas de 3 intentos de acceso a abla',
  `observaciones` text NOT NULL COMMENT 'observaciones hechas al usuario',
  PRIMARY KEY (`id`),
  UNIQUE KEY `usuario` (`usuario`),
  KEY `usuario_tipo` (`id_tipo`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='informacion completa de los usuarios del sistema';

-- --------------------------------------------------------

--
-- Structure for view `fom_lista_ciudades`
--
DROP TABLE IF EXISTS `fom_lista_ciudades`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `fom_lista_ciudades` AS select `c`.`id` AS `id`,`c`.`nombre` AS `nombre`,concat(`c`.`nombre`,', ',`e`.`nombre`,', ',`p`.`nombre`) AS `cadena` from ((`fom_ciudades` `c` join `fom_estados` `e`) join `fom_paises` `p`) where ((`c`.`id_estado` = `e`.`id`) and (`e`.`id_pais` = `p`.`id`));

-- --------------------------------------------------------

--
-- Structure for view `fom_lista_estados`
--
DROP TABLE IF EXISTS `fom_lista_estados`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `fom_lista_estados` AS select `e`.`id` AS `id`,`e`.`nombre` AS `nombre`,concat(`e`.`nombre`,' - ',`p`.`nombre`) AS `cadena` from (`fom_estados` `e` join `fom_paises` `p`) where (`e`.`id_pais` = `p`.`id`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `fom_contactos_proveedor`
--
ALTER TABLE `fom_contactos_proveedor`
  ADD CONSTRAINT `fom_contactos_proveedor_ibfk_1` FOREIGN KEY (`id_persona`) REFERENCES `fom_personas` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fom_contactos_proveedor_ibfk_2` FOREIGN KEY (`id_proveedor`) REFERENCES `fom_proveedores` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `fom_cuentas_proveedor`
--
ALTER TABLE `fom_cuentas_proveedor`
  ADD CONSTRAINT `fom_cuentas_proveedor_ibfk_1` FOREIGN KEY (`id_banco`) REFERENCES `fom_bancos` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fom_cuentas_proveedor_ibfk_2` FOREIGN KEY (`id_proveedor`) REFERENCES `fom_proveedores` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `fom_empleados`
--
ALTER TABLE `fom_empleados`
  ADD CONSTRAINT `fom_empleados_ibfk_1` FOREIGN KEY (`id_tipo_empleado`) REFERENCES `fom_tipos_empleado` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fom_empleados_ibfk_2` FOREIGN KEY (`id_sede`) REFERENCES `fom_sedes_empresa` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fom_empleados_ibfk_3` FOREIGN KEY (`id_persona`) REFERENCES `fom_personas` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fom_empleados_ibfk_4` FOREIGN KEY (`id_cargo`) REFERENCES `fom_cargos` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `fom_gondolas`
--
ALTER TABLE `fom_gondolas`
  ADD CONSTRAINT `fom_gondolas_ibfk_1` FOREIGN KEY (`id_bodega`) REFERENCES `fom_bodegas` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `fom_notas_credito_clientes`
--
ALTER TABLE `fom_notas_credito_clientes`
  ADD CONSTRAINT `fom_notas_credito_clientes_ibfk_1` FOREIGN KEY (`id_factura`) REFERENCES `fom_facturas_venta` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `fom_notas_debito_clientes`
--
ALTER TABLE `fom_notas_debito_clientes`
  ADD CONSTRAINT `fom_notas_debito_clientes_ibfk_1` FOREIGN KEY (`id_factura`) REFERENCES `fom_facturas_venta` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `fom_permisos_componentes_usuarios`
--
ALTER TABLE `fom_permisos_componentes_usuarios`
  ADD CONSTRAINT `fom_permisos_componentes_usuarios_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `fom_usuarios` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fom_permisos_componentes_usuarios_ibfk_2` FOREIGN KEY (`id_componente`) REFERENCES `fom_componentes_modulos` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fom_permisos_componentes_usuarios_ibfk_3` FOREIGN KEY (`id_sede`) REFERENCES `fom_sedes_empresa` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `fom_permisos_modulos_usuarios`
--
ALTER TABLE `fom_permisos_modulos_usuarios`
  ADD CONSTRAINT `fom_permisos_modulos_usuarios_ibfk_2` FOREIGN KEY (`id_sede`) REFERENCES `fom_sedes_empresa` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fom_permisos_modulos_usuarios_ibfk_3` FOREIGN KEY (`id_usuario`) REFERENCES `fom_usuarios` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `fom_sedes_empresa`
--
ALTER TABLE `fom_sedes_empresa`
  ADD CONSTRAINT `fom_sedes_empresa_ibfk_1` FOREIGN KEY (`id_ciudad`) REFERENCES `fom_ciudades` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `fom_sedes_proveedor`
--
ALTER TABLE `fom_sedes_proveedor`
  ADD CONSTRAINT `fom_sedes_proveedor_ibfk_1` FOREIGN KEY (`id_proveedor`) REFERENCES `fom_proveedores` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fom_sedes_proveedor_ibfk_2` FOREIGN KEY (`id_ciudad`) REFERENCES `fom_ciudades` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `fom_unidades`
--
ALTER TABLE `fom_unidades`
  ADD CONSTRAINT `fom_unidades_ibfk_1` FOREIGN KEY (`id_unidad_principal`) REFERENCES `fom_unidades` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fom_unidades_ibfk_2` FOREIGN KEY (`id_tipo_unidad`) REFERENCES `fom_tipos_unidades` (`id`) ON UPDATE CASCADE;
