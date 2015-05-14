<?php

/**
*
* Copyright (C) 2009 FELINUX LTDA
*
* Autores:
* Francisco J. Lozano B. <fjlozano@felinux.com.co>
* Julián Mondragón <jmondragon@felinux.com.co>
*
* Este archivo es parte de:
* FOLCS :: FELINUX online community system
*
* Este programa es software libre: usted puede redistribuirlo y/o
* modificarlo  bajo los términos de la Licencia Pública General GNU
* publicada por la Fundación para el Software Libre, ya sea la versión 3
* de la Licencia, o (a su elección) cualquier versión posterior.
*
* Este programa se distribuye con la esperanza de que sea útil, pero
* SIN GARANTÍA ALGUNA; ni siquiera la garantía implícita MERCANTIL o
* de APTITUD PARA UN PROPÓSITO DETERMINADO. Consulte los detalles de
* la Licencia Pública General GNU para obtener una información más
* detallada.
*
* Debería haber recibido una copia de la Licencia Pública General GNU
* junto a este programa. En caso contrario, consulte:
* <http://www.gnu.org/licenses/>.
*
**/

/*** Parámetros de configuración general ***/
$configuracion["GENERAL"] = array(
    "idioma"                    => "es",
    "tema"                      => "Azul-Verde",
    "registrosPorPagina"        => 10,
    "longitudMinimaNombre"      => 2,
    "longitudMinimaApellido"    => 2,
    "longitudMinimaUsuario"     => 4,
    "longitudMinimaContrasena"  => 6,
    "cantidadDecimales"         => 2,
    "tipoMonedaColombiana"      => "tradicional",
    "numeroItemsResumen"        => "10",
    
);

/*** Parametros para la conexión con la base de datos ***/
$configuracion["BASEDATOS"] = array(
    "servidor"   => "localhost",
    "usuario"    => "root",
    "contraseña" => "admin",
    "nombre"     => "almotos",
    "prefijo"    => "fom_"
);



$configuracion["BASEDATOS_GLOBAL"] = array(
    "servidor"   => "localhost",
    "usuario"    => "root",
    "contraseña" => "admin",
    "nombre"     => "global",
    "prefijo"    => "fom_"
);



/*** Asociación de nombres de iconos ***/
$configuracion["ICONOS"] = array(
    "anguloArriba"            => "carat-1-n",
    "anguloArribaDerecha"     => "carat-1-ne",
    "anguloDerecha"           => "carat-1-e",
    "anguloAbajoDerecha"      => "carat-1-se",
    "anguloAbajo"             => "carat-1-s",
    "anguloAbajoIzquierda"    => "carat-1-sw",
    "anguloIzquierda"         => "carat-1-w",
    "anguloArribaIzquierda"   => "carat-1-nw",
    "angulosArribaAbajo"      => "carat-2-n-s",
    "angulosIzquierdaDerecha" => "carat-2-e-w",
    "puntaArriba"             => "triangle-1-n",
    "puntaArribaDerecha"      => "triangle-1-ne",
    "puntaDerecha"            => "triangle-1-e",
    "puntaAbajoDerecha"       => "triangle-1-se",
    "puntaAbajo"              => "triangle-1-s",
    "puntaAbajoIzquierda"     => "triangle-1-sw",
    "puntaIzquierda"          => "triangle-1-w",
    "puntaArribaIzquierda"    => "triangle-1-nw",
    "puntasArribaAbajo"       => "triangle-2-n-s",
    "puntasIzquierdaDerecha"  => "triangle-2-e-w",
    "flechaArriba"            => "arrow-1-n",
    "flechaAbajo"             => "arrow-1-s",
    "flechaGruesaArriba"      => "arrowthick-1-n",
    "flechaGruesaAbajo"       => "arrowthick-1-s",
    "mover"                   => "arrow-4",
    "ampliar"                 => "arrow-4-diag",
    "desprender"              => "extlink",
    "ventanaNueva"            => "newwin",
    "recargar"                => "refresh",
    "aleatorio"               => "shuffle",
    "transferir"              => "transfer-e-w",
    "transferirGrueso"        => "transferthick-e-w",
    "carpetaCerrada"          => "folder-collapsed",
    "carpetaAbierta"          => "folder-open",
    "documentoNuevo"          => "document",
    "libreta"                 => "note",
    "sobreCerrado"            => "mail-closed",
    "sobreAbierto"            => "mail-open",
    "maletin"                 => "suitcase",
    "comentario"              => "comment",
    "usuario"                 => "person",
    "impresora"               => "print",
    "basura"                  => "trash",
    "bloqueado"               => "locked",
    "desbloqueado"            => "unlocked",
    "marcador"                => "bookmark",
    "etiqueta"                => "tag",
    "inicio"                  => "home",
    "bandera"                 => "flag",
    "calendario"              => "calendar",
    "carrito"                 => "cart",
    "lapiz"                   => "pencil",
    "reloj"                   => "clock",
    "disco"                   => "disk",
    "calculadora"             => "calculator",
    "acercar"                 => "zoomin",
    "alejar"                  => "zoomout",
    "buscar"                  => "search",
    "herramienta"             => "wrench",
    "piñon"                   => "gear",
    "corazon"                 => "heart",
    "estrella"                => "star",
    "enlace"                  => "link",
    "cancelar"                => "cancel",
    "mas"                     => "plus",
    "masGrueso"               => "plusthick",
    "menos"                   => "minus",
    "menosGrueso"             => "minusthick",
    "cerrar"                  => "close",
    "cerrarGrueso"            => "closethick",
    "llave"                   => "key",
    "bombillo"                => "lightbulb",
    "tijeras"                 => "scissors",
    "pizarra"                 => "clipboard",
    "copiar"                  => "copy",
    "contacto"                => "contact",
    "imagen"                  => "image",
    "video"                   => "video",
    "script"                  => "script",
    "alerta"                  => "alert",
    "informacion"             => "info",
    "notificacion"            => "notice",
    "ayuda"                   => "help",
    "chequeo"                 => "check",
    "circuloLleno"            => "bullet",
    "circuloGrueso"           => "radio-off",
    "circuloDelgado"          => "radio-on",
    "reproducir"              => "play",
    "pausar"                  => "pause",
    "siguiente"               => "seek-next",
    "anterior"                => "seek-prev",
    "ultimo"                  => "seek-end",
    "primero"                 => "seek-first",
    "detener"                 => "stop",
    "expulsar"                => "eject",
    "sinVolumen"              => "volume-off",
    "conVolumen"              => "volume-on",
    "encender"                => "power",
    "rss"                     => "signal-diag",
    "señal"                   => "signal",
    "cargaBateria0"           => "battery-0",
    "cargaBateria1"           => "battery-1",
    "cargaBateria2"           => "battery-2",
    "cargaBateria3"           => "battery-3",
    "circuloMas"              => "circle-plus",
    "circuloMenos"            => "circle-minus",
    "circuloCerrar"           => "circle-close",
    "circuloPuntaDerecha"     => "circle-triangle-e",
    "circuloPuntaAbajo"       => "circle-triangle-s",
    "circuloPuntaIzquierda"   => "circle-triangle-w",
    "circuloPuntaArriba"      => "circle-triangle-n",
    "circuloFlechaDerecha"    => "circle-arrow-e",
    "circuloFlechaAbajo"      => "circle-arrow-s",
    "circuloFlechaIzquierda"  => "circle-arrow-w",
    "circuloFlechaArriba"     => "circle-arrow-n",
    "circuloAcercar"          => "circle-zoomin",
    "circuloAlejar"           => "circle-zoomout",
    "circuloChequeo"          => "circle-check"
);

/**
 *
 * Rutas de los programas (binarios ejecutables del sistema operativo) utilizados
 *
 */
$configuracion["DIMENSIONES"]["LINEA"]               = array(320, 240, 90, 90);
$configuracion["DIMENSIONES"]["MOTO"]                = array(320, 240, 90, 90);
$configuracion["DIMENSIONES"]["BANNER"]              = array(198, 198, 100, 100);
$configuracion["DIMENSIONES"]["NOTICIAS"]            = array(320, 240, 150, 150);
$configuracion["DIMENSIONES"]["BOLETINES"]           = array(300, 300, 90, 90);
$configuracion["DIMENSIONES"]["USUARIOS"]            = array(200, 250, 80, 90);
$configuracion["DIMENSIONES"]["anchoNoticiaNormal"]  = 320;
$configuracion["DIMENSIONES"]["altoNoticiaNormal"]   = 240;
$configuracion["DIMENSIONES"]["anchoCentroNormal"]   = 320;
$configuracion["DIMENSIONES"]["altoCentroNormal"]    = 240;
$configuracion["DIMENSIONES"]["maximoPesoArchivo"]   = 1000000;


/*** Rutas de archivos y directorios al interior de cada módulo ***/
$configuracion["MODULOS"] = array(

    /*** Carpetas ***/
    "clases"     => "clases/modulos",
    "javascript" => "javascript/modulos",

    /*** Archivos ***/
    "principal"  => "principal.php",
    "ajax"       => "ajax.php"
);



/*** Temas para la selección de la apariencia de la interfaz gráfica de usuario ***/
$configuracion["PAGINA"] = array(
    "titulo"        => "SAMI",
    "descripcion"   => "Sistema de Administracion Multiplataforma Integrado",
    "palabrasClave" => "Repuestos, mantenimiento, motopartes, repuestos la 15, repuestos moto cali",
    "codificacion"  => "iso8859-1",
    "icono"         => "moto.ico",
    "pieDePagina"   => "<p class='estilosPiePagina'>&copy; ".date("Y")." ALMOTOS - All Rights Reserved :: <a href= \"mailto:webmaster@almotosonline.org\">webmaster@almotosonline.com</a>..::Developed by <a href=\"http://www.almotosonline.com\">GENESYS</a>::..</p>"
);

/**
 *
 * Rutas de los programas (binarios ejecutables del sistema operativo) utilizados
 *
 */
$configuracion["PROGRAMAS"] = array(
    "convert"   => "/usr/bin/convert -resize %1 %2 %3",
    "ffmpeg"    => "/usr/bin/ffmpeg -y -i %1 -f flv -vcodec flv -threads 4 -s 320x240 -r 30.00 -pix_fmt yuv420p -g 300 -qmin 3 -b 512k -async 50 -acodec libmp3lame -ar 11025 -ac 2 -ab 16k %2",
);


/**
 * Rutas de archivos y directorios principales
 */
$configuracion["RUTAS"] = array(
    "base"               => "/",
    "media"              => "media/",
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
    'pdfs'               => 'pdfs/',
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
    //"jquery-azul-verde_2.css",
    "plugins/sexyalertbox.css",
    "plugins/prettyPhoto.css",
    "plugins/introjs.min.css",
    "plugins/chosen.min.css",  
    "plugins/jquery.pnotify.default.css",
    
);

/**
 *Rutas de estilos segun el modulo 
 */
$configuracion["ESTILOS"]["USUARIOS"] = array(
    "plugins/prettyPhoto.css",
    "plugins/jquery.checkboxtree.min.css",
    "plugins/fullcalendar.css",
    "plugins/fullcalendar.print.css",    
);

$configuracion["ESTILOS"]["PERFILES"] = array(
    "plugins/jquery.checkboxtree.min.css"
);

$configuracion["ESTILOS"]["PRIVILEGIOS"] = array(
    "plugins/jquery.checkboxtree.min.css"
);

$configuracion["ESTILOS"]["COMPRAS_MERCANCIA"] = array( 
    "modulos/proveedores/proveedores.css",
    "modulos/articulos/articulos.css",

);

$configuracion["ESTILOS"]["CLIENTES"] = array( 

);

$configuracion["ESTILOS"]["ARTICULOS"] = array( 

);

$configuracion["ESTILOS"]["PROVEEDORES"] = array( 

);

$configuracion["ESTILOS"]["VENTAS_MERCANCIA"] = array( 
    "modulos/clientes/clientes.css",
    "modulos/articulos/articulos.css",
    
);

$configuracion["ESTILOS"]["CUADRE_CAJA"] = array(
    
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
    "plugins/jquery.hoverIntent.js",
    "plugins/jquery.superfish.js",
    "plugins/sexyalertbox.mini.js",
    "plugins/intro.min.js",
    //"plugins/bootstrap.min.js",
    "plugins/jquery.prettyPhoto.js",
    "plugins/chosen.jquery.min.js",  
    "plugins/jquery.pnotify.min.js",   
);






/**
 * Rutas de archivos por modulos
 */
$configuracion["JAVASCRIPT"]["USUARIOS"] = array( 
    "plugins/jquery.prettyPhoto.js",
    "plugins/jquery.checkboxtree.min.js",
    "plugins/fullcalendar.min.js",
    "plugins/ui.timepickr.js",
);

$configuracion["JAVASCRIPT"]["ARTICULOS"] = array( 
    "plugins/jquery-barcode.min.js",
    "plugins/chart.min.js",
    'plugins/underscore-min.js',

);


/**
 * Rutas de archivos por modulos
 */
$configuracion["JAVASCRIPT"]["COMPRAS_MERCANCIA"] = array( 
    "modulos/proveedores/proveedores.js",
    "modulos/articulos/articulos.js",
    "plugins/jquery-barcode.min.js",
    "plugins/chart.min.js",
    'plugins/underscore-min.js',
);


$configuracion["JAVASCRIPT"]["VENTAS_MERCANCIA"] = array( 
    "modulos/clientes/clientes.js",
    "modulos/articulos/articulos.js",
    "plugins/jquery-barcode.min.js",
    "plugins/chart.min.js",
    'plugins/underscore-min.js',
    
);

$configuracion["JAVASCRIPT"]["CLIENTES"] = array( 
);

$configuracion["JAVASCRIPT"]["PROVEEDORES"] = array( 
);

$configuracion["JAVASCRIPT"]["CUADRE_CAJA"] = array(
    'plugins/underscore-min.js',
    'plugins/backbone-min.js', 
);

/**
 * Rutas de archivos por modulos
 */

$configuracion["JAVASCRIPT"]["PRIVILEGIOS"] = array( 
    "plugins/jquery.checkboxtree.min.js"
);

$configuracion["JAVASCRIPT"]["PERFILES"] = array( 
    "plugins/jquery.checkboxtree.min.js"
);

/*** Parámetros propios del lenguaje y/o del servidor web ***/
$configuracion["SERVIDOR"] = array(
    "principal"       => "http://localhost/",
    "media"           => "http://localhost/media/",
    "nombreRemitente" => "ALMOTOS Notification System",
    "correoRemitente" => "notifications@almotos.org",
    "depuracion"      => false,
    "codificacion"    => "iso8859-1"
);

/**
 * Meses en español para ser traducidos desde numeros 
 */
$configuracion['MESES'] = array(
    '1'     => 'Enero',
    '2'     => 'Febrero',
    '3'     => 'Marzo',
    '4'     => 'Abril',
    '5'     => 'Mayo',
    '6'     => 'Junio',
    '7'     => 'Julio',
    '9'     => 'Agosto',
    '9'     => 'Septiembre',
    '10'    => 'Octubre',
    '11'    => 'Noviembre',
    '12'    => 'Diciembre',
);

/**
 * Archivo que almacena los archivos de configuracion de validaciones realizadas
 * en el sistema. Por ejemplo validaciones de white list, o denegaciones por black list 
 */

$configuracion['VALIDACIONES'] = array(
        'notas_credito' => array("doc", "docx", "pdf", "ppt", "pptx", "pps", "ppsx", "xls", "xlsx", "odt", "rtf", "txt", "ods", "odp", "jpg", "jpeg", "png"),
        'notas_debito' => array("doc", "docx", "pdf", "ppt", "pptx", "pps", "ppsx", "xls", "xlsx", "odt", "rtf", "txt", "ods", "odp", "jpg", "jpeg", "png"),
                                    );

/**
 * Arreglo que determina que tablas son particulares a cada cliente
 **/
$configuracion['TABLAS']['PARTICULARES'] = array(
        'cuentas_operacion',
        'asientos_contables',
        /*'articulos_cotizacion',
        'articulos_factura_compra',
        'articulos_factura_temporal_compra',
        'articulos_factura_venta',
        'articulos_factura_temporal_venta',
        'articulos_modificados_ncc',
        'articulos_modificados_ncp',
        'articulos_modificados_ndc',
        'articulos_modificados_ndp',
        'articulos_orden_compra',
        'bodegas',
        'cajas',
        'cambios_inventarios',
        'cargos',
        'catalogos',
        'centros_costo',
        'clientes',
        'configuraciones',
        'contactos_cliente',
        'contactos_proveedor',
        'cotizaciones',
        'cuentas_proveedor',
        'documentos',
        'empleados',
        'eventos',
        'facturas_compras',
        'facturas_temporales_compra',
        'facturas_temporales_venta',
        'facturas_venta',
        'gondolas',
        'grupos',
        'imagenes',
        'inventarios',
        'kardex',
        'lineas',
        'marcas',
        'motos',
        'movimientos_mercancia',
        'notas_credito_clientes',
        'notas_credito_proveedores',
        'notas_debito_clientes',
        'notas_debito_proveedores',
        'notificaciones',
        'ordenes_compra',
        'paginas',
        'periodos_contables',
        'permisos_componentes_perfiles',
        'permisos_componentes_usuarios',
        'permisos_modulos_perfiles',
        'permisos_modulos_usuarios',
        'personas',
        'plan_contable',
        'precios_articulo_bodega',
        'proveedores',
        'resoluciones',
        'sedes_cliente',*/
        'sedes_empresa',
        /*'sedes_proveedor',
        'subgrupos',
        'tipos_compra',
        'tipos_documento',
        'tipos_empleado',
        'tipos_unidades',
        'tipos_venta',
        'unidades',
        'usuarios',*/
    );

/**
 * Arreglo que determina que tablas son globales a todos los clientes cliente
 **/
$configuracion['TABLAS']['GLOBALES'] = array(
        'articulos_base',
        'bancos_base',
        'cargos_base',
        'catalogos_base',
        'ciudades',
        'componentes_modulos',
        'conceptos_DIAN',
        'dias_festivo',
        'empresas',
        'estados',
        'grupos_base',
        'imagenes_base',
        'impuestos',
        'impuesto_actividad',
        'lineas_base',
        'localidades',
        'marcas_base',
        'modulos',
        'motos_base',
        'paises',
        'plan_contable_base',
        'profesiones_oficios',
        'subgrupos_base',
        'noticias',
        'notificaciones',
        'actividades_economicas',
        'plan_contable_base',

    );

