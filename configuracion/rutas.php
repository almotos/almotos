<?php

/**
 *
 * @package     FOLCS
 * @author      Francisco J. Lozano B. <fjlozano@felinux.com.co>
 * @author      Julian A. Mondragón <jmondragon@felinux.com.co>
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2009 FELINUX LTDA
 * @version     0.1
 *
 **/

/**
 * Rutas de archivos y directorios principales
 */
$configuracion["RUTAS"] = array(
    "base"               => "/",
    "media"              => "media",
    "clases"             => "clases",
    "idiomas"            => "idiomas",
    "modulos"            => "modulos",
    "plantillas"         => "plantillas",
    "imagenesEstaticas"  => "imagen/estaticas",
    "iconosBanderas"     => "imagen/estaticas/banderas",
    "imagenesDinamicas"  => "imagen/dinamicas/normales",
    "imagenesMiniaturas" => "imagen/dinamicas/miniaturas",
    "fuentes"            => "recursos/fuentes",
    "archivos"           => "archivos",
    'archivos_temporales' => 'pdfs/facturas_compra',
    "audios"             => "audios",
    "iconoUsuario"       => "00000001.png",
    "video"              => "video",
    "audio"              => "audio",
    "documentos"         => "documentos",
    "estilos"            => "estilos",
    "imagenesEstilos"    => "estilos/imagenes/",
    "javascript"         => "javascript",
    "archivosCatalogos"  => "archivos/catalogos/",
    "archivoGeneral"     => "general",

);

/**
 * Rutas de plantillas de código HTML
 */
$configuracion["PLANTILLAS"] = array(
    "principal" => "principal.htm",
    "interna"   => "interna.htm"
);



/**
 * Rutas de hojas de estilos (CSS) estándar
 */
$configuracion["ESTILOS"]["GRIS"] = array(
    "general.css",
    "jquery.css",
    "sexyalertbox.css",
    "prettyPhoto.css"
);


$configuracion["ESTILOS"]["AZUL-BLANCO"] = array(
    "general-azul-blanco.css",
    "jquery-azul-blanco.css",
    "sexyalertbox.css",
    "prettyPhoto.css"
);

$configuracion["ESTILOS"]["AZUL-VERDE"] = array(
    "general-azul-verde.css",
    "jquery-azul-verde.css",
    "sexyalertbox.css",
    "prettyPhoto.css",
    "introjs.min.css"
);

/**
 *Rutas de estilos segun el modulo 
 */
$configuracion["ESTILOS"]["USUARIOS"] = array(
    "prettyPhoto.css",
    "jquery.checkboxtree.min.css",
    "plugins/fullcalendar.css",
    "plugins/fullcalendar.print.css",    
);

$configuracion["ESTILOS"]["PRIVILEGIOS"] = array(
    "prettyPhoto.css",
    "jquery.checkboxtree.min.css"
);

$configuracion["ESTILOS"]["COMPRAS_MERCANCIA"] = array( 
    "modulos/proveedores/proveedores.css",
    "modulos/articulos/articulos.css",
    "modulos/catalogos/catalogos.css"
);

$configuracion["ESTILOS"]["VENTAS_MERCANCIA"] = array( 
    "modulos/clientes/clientes.css",
    "modulos/articulos/articulos.css",
    "modulos/catalogos/catalogos.css"
);




/**
 * Rutas de archivos de JavaScript
 */
$configuracion["JAVASCRIPT"]["GENERAL"] = array(    
    "general.js",
    "funciones.js",
    "varios.js",
    "jquery.ui.js",
    "general_modulos.js",
    "editor/ckeditor.js",
    "editor/adapters/jquery.js",
    "jquery.hoverIntent.js",
    "jquery.superfish.js",
    "plugins/sexyalertbox.mini.js",
    "plugins/intro.min.js",
    "prettyPhoto/js/jquery.prettyPhoto.js"    
);






/**
 * Rutas de archivos por modulos
 */
$configuracion["JAVASCRIPT"]["USUARIOS"] = array( 
    "prettyPhoto/js/jquery.prettyPhoto.js",
    "jquery.checkboxtree.min.js",
    "plugins/fullcalendar.min.js",
    "plugins/ui.timepickr.js",
);

$configuracion["JAVASCRIPT"]["ARTICULOS"] = array( 
    "plugins/jquery-barcode.min.js",

);


/**
 * Rutas de archivos por modulos
 */
$configuracion["JAVASCRIPT"]["COMPRAS_MERCANCIA"] = array( 
    "modulos/proveedores/proveedores.js",
    "modulos/articulos/articulos.js",
    "modulos/catalogos/catalogos.js",
    "plugins/jquery-barcode.min.js",
);


$configuracion["JAVASCRIPT"]["VENTAS_MERCANCIA"] = array( 
    "modulos/clientes/clientes.js",
    "modulos/articulos/articulos.js",
    "modulos/catalogos/catalogos.js",
    "plugins/jquery-barcode.min.js",
);

/**
 * Rutas de archivos por modulos
 */

$configuracion["JAVASCRIPT"]["PRIVILEGIOS"] = array( 
    "jquery.checkboxtree.min.js"
);

