-- phpMyAdmin SQL Dump
-- version 3.3.2deb1ubuntu1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 02, 2015 at 11:43 PM
-- Server version: 5.1.73
-- PHP Version: 5.3.2-1ubuntu4.27

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `global`
--

-- --------------------------------------------------------

--
-- Table structure for table `fom_anuncios`
--

DROP TABLE IF EXISTS `fom_anuncios`;
CREATE TABLE IF NOT EXISTS `fom_anuncios` (
  `id` mediumint(6) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Consecutivo interno para la base de datos',
  `id_imagen` mediumint(8) unsigned NOT NULL COMMENT 'Consecutivo interno para la base de datos de la imagen del anuncio',
  `titulo` varchar(255) NOT NULL COMMENT 'Titulo del anuncio',
  `descripcion` text NOT NULL,
  `vinculo` varchar(255) NOT NULL COMMENT 'Vinculo al cual apunta el banner',
  `fecha_creacion` datetime NOT NULL COMMENT 'Fecha de creaci?n del anuncio',
  `fecha_inicial` datetime NOT NULL COMMENT 'Fecha desde la cual se publica el anuncio',
  `fecha_final` datetime DEFAULT NULL COMMENT 'Fecha hasta la cual se publica el anuncio',
  `activo` enum('0','1') NOT NULL DEFAULT '1' COMMENT 'El anuncio se encuentra activo: 0=No, 1=Si',
  PRIMARY KEY (`id`),
  KEY `id_imagen` (`id_imagen`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `fom_chat`
--

DROP TABLE IF EXISTS `fom_chat`;
CREATE TABLE IF NOT EXISTS `fom_chat` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `from` varchar(255) NOT NULL DEFAULT '',
  `to` varchar(255) NOT NULL DEFAULT '',
  `message` text NOT NULL,
  `sent` datetime NOT NULL,
  `recd` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `fom_imagenes`
--

DROP TABLE IF EXISTS `fom_imagenes`;
CREATE TABLE IF NOT EXISTS `fom_imagenes` (
  `id` bigint(12) unsigned zerofill NOT NULL AUTO_INCREMENT COMMENT 'Consecutivo interno de la tabla imagenes',
  `id_modulo` smallint(4) unsigned zerofill DEFAULT '0000' COMMENT 'Id de la tabla modulos',
  `id_registro` bigint(12) unsigned zerofill DEFAULT '000000000000' COMMENT 'Id de la tabla registros',
  `id_usuario` int(8) unsigned zerofill DEFAULT '00000000' COMMENT 'Id de la tabla usuarios',
  `titulo` varchar(255) NOT NULL COMMENT 'Titulo de la imagen',
  `descripcion` varchar(255) NOT NULL COMMENT 'DescripciÃ³n de la imagen',
  `fecha` datetime DEFAULT NULL COMMENT 'Fecha de creaciÃ³n de la imagen',
  `ruta` varchar(255) NOT NULL COMMENT 'Ruta de la imagen',
  PRIMARY KEY (`id`),
  KEY `imagen_usuario` (`id_usuario`),
  KEY `id_modulo` (`id_modulo`),
  KEY `id_registro` (`id_registro`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='info. sobre las imagnes q se manejen en el sistema';

-- --------------------------------------------------------

--
-- Table structure for table `fom_mensajes`
--

DROP TABLE IF EXISTS `fom_mensajes`;
CREATE TABLE IF NOT EXISTS `fom_mensajes` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Consecutivo interno para la base de datos',
  `id_usuario_remitente` mediumint(8) unsigned zerofill NOT NULL COMMENT 'Codigo interno del usuario que envia el mensaje',
  `id_usuario_destinatario` mediumint(8) unsigned zerofill NOT NULL COMMENT 'Codigo interno del usuario destino del mensaje',
  `nombre_remitente` varchar(250) NOT NULL DEFAULT 'nomRemite',
  `titulo` varchar(255) NOT NULL COMMENT 'Título del mensaje',
  `contenido` text COMMENT 'Contenido del mensaje',
  `fecha` datetime DEFAULT NULL COMMENT 'Fecha de creación del mensaje',
  `leido` enum('0','1') NOT NULL DEFAULT '0' COMMENT 'El mensaje ya fue leido: 0 = No, 1= Si',
  PRIMARY KEY (`id`),
  KEY `mensaje_usuario_remitente` (`id_usuario_remitente`),
  KEY `mensaje_usuario_destinatario` (`id_usuario_destinatario`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `fom_noticias`
--

DROP TABLE IF EXISTS `fom_noticias`;
CREATE TABLE IF NOT EXISTS `fom_noticias` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Consecutivo interno para la base de datos',
  `id_usuario` mediumint(8) unsigned NOT NULL COMMENT 'Consecutivo interno del usuario que publica la noticia',
  `autor` varchar(250) NOT NULL COMMENT 'nombre del autor que publico la noticia',
  `id_imagen` bigint(12) unsigned NOT NULL COMMENT 'Consecutivo interno para la base de datos de la imagen normal de la noticia',
  `titulo` varchar(255) NOT NULL COMMENT 'Titulo de la noticia',
  `resumen` text COMMENT 'Texto con el resumen de la noticia',
  `contenido` text COMMENT 'Contenido de la noticia',
  `fecha_creacion` datetime NOT NULL COMMENT 'Fecha de creación de la noticia',
  `fecha_publicacion` datetime NOT NULL COMMENT 'Fecha de publicación de la noticia',
  `fecha_actualizacion` datetime DEFAULT NULL COMMENT 'Fecha de ultima actualizacion de la noticia',
  `id_categoria` int(10) DEFAULT '0' COMMENT 'identificador de la categoria a la cual pertenece la noticia',
  `activo` enum('0','1') NOT NULL DEFAULT '1' COMMENT 'La noticia se encuentra activa: 0 = No, 1= Si',
  `visitas` int(20) NOT NULL DEFAULT '0' COMMENT 'almacena el numero de visitas de una determinada noticia',
  PRIMARY KEY (`id`),
  KEY `noticia_usuario` (`id_usuario`),
  KEY `id_categoria` (`id_categoria`),
  KEY `id_imagen` (`id_imagen`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `fom_usuarios_conectados`
--

DROP TABLE IF EXISTS `fom_usuarios_conectados`;
CREATE TABLE IF NOT EXISTS `fom_usuarios_conectados` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `id_usuario` mediumint(8) unsigned NOT NULL,
  `usuario` varchar(100) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `tiempo` datetime NOT NULL COMMENT 'este campo determina a que usuario se borra y a cual no',
  `visible` enum('0','1') NOT NULL DEFAULT '1' COMMENT 'Este campo determina si un usuario es visible o no a los demas usuarios',
  PRIMARY KEY (`id`),
  KEY `id_usuario` (`id_usuario`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
