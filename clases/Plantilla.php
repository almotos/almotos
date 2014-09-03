<?php

/**
 * @package     FOM
 * @subpackage  Base
 * @author      Francisco J. Lozano B. <fjlozano@felinux.com.co>
 * @author      Julian A. Mondragón <jmondragon@felinux.com.co>
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2009 FELINUX LTDA
 * @version     0.1
 * */

/**
 * Clase encargada de la gestión automática de plantillas de código HTML para ser enviadas como respuesta al navegador.
 * Esta es la clase principal para el renderizado total de la aplicacion, como su nombre lo indica, la clase plantilla
 * es la encargada de gestionar la plantilla a ser utilizada, es decir, si es la pagina principal la pagina que esta siendo
 * solicitada, entonces la clase plantilla utilizara esta plantilla localizada en Plantillas/principal.html, si no es la pagina
 * principal, la clase plantilla llamará a interna, localizada en Plantillas/interna.html. Dentro de esta clase es que toda la magia ocurre,
 * pues con con el llamado principal gran parte del layout de la aplicacion ya es generado, entre ello, la cabecera, el icono de la
 * aplicacion, el footer y el menu lateral. Una vez esto ha sucedido, cada uno de los modulos se encargan de ejecutar su logica y de
 * generar la respuesta de codigo html a ser mostrada, es alli cuando entonces la clase plantilla es llamada nuevamente para reemplazar
 * dentro de la plantilla (principal o interna) el codigo html generado.
 * */
class Plantilla {

    /**
     * Determina si se trata de la página principal
     * @var lógico
     */
    public static $principal = false;

    /**
     * Contenido de la página solicitada
     * @var cadena
     */
    public static $contenido = "";

    /**
     * Etiquetas reemplazables de la plantilla
     * @var arreglo
     */
    public static $etiquetas = array();

    /**
     * Inicializar la plantilla
     */
    public static function iniciar($modulo) {
        global $configuracion, $textos, $sesion_tituloPagina, $sesion_descripcionPagina, $sesion_palabrasClavePagina, $sesion_codificacionPagina, $sesion_iconoPagina, $sesion_pieDePagina;

        if (self::$principal) {
            $plantilla = $configuracion['RUTAS']['plantillas'] . '/' . $configuracion['PLANTILLAS']['principal'];
        } else {
            $plantilla = $configuracion['RUTAS']['plantillas'] . '/' . $configuracion['PLANTILLAS']['interna'];
        }

//        if (file_exists($plantilla) && is_readable($plantilla)) {
        self::$contenido = file_get_contents($plantilla);
//        }

        preg_match_all("/\{\%(.*)\%\}/", self::$contenido, $etiquetas);

        foreach ($etiquetas[0] as $etiqueta) {
            $nombre = preg_replace("/(\{\%)|(\%\})/", "", $etiqueta);
            self::$etiquetas[$nombre] = "";
        }

        /*         * * Definir el texto para la barra de título del navegador ** */
        (!isset($sesion_tituloPagina)) ? self::$etiquetas['TITULO_PAGINA'] = $configuracion['PAGINA']['titulo'] . '::' . str_replace('_', ' ', $modulo->nombre) : self::$etiquetas['TITULO_PAGINA'] = $sesion_tituloPagina;

        /*         * * Definir el texto con la descripción de la página ** */
        (!isset($sesion_descripcionPagina)) ? self::$etiquetas['DESCRIPCION'] = $configuracion['PAGINA']['descripcion'] : self::$etiquetas['DESCRIPCION'] = $sesion_descripcionPagina;

        /*         * * Definir la lista de palabras clave de la página ** */
        (!isset($sesion_palabrasClavePagina)) ? self::$etiquetas['PALABRAS_CLAVE'] = $configuracion['PAGINA']['palabrasClave'] : self::$etiquetas['PALABRAS_CLAVE'] = $sesion_palabrasClavePagina;

        /*         * * Definir el ícono de la página ** */
        (!isset($sesion_codificacionPagina)) ? self::$etiquetas['CODIFICACION'] = $configuracion['PAGINA']['codificacion'] : self::$etiquetas["CODIFICACION"] = $sesion_codificacionPagina;

        /*         * * Definir el ícono de la página ** */
        (!isset($sesion_iconoPagina)) ? self::$etiquetas['ICONO'] = $configuracion['SERVIDOR']['media'] . $configuracion['RUTAS']['imagenesEstaticas'] . '/' . $configuracion['PAGINA']['icono'] : self::$etiquetas['ICONO'] = $sesion_iconoPagina;

        /*         * * Definir el texto del pie de página ** */
        (!isset($sesion_pieDePagina)) ? self::$etiquetas['PIE_PAGINA'] = $configuracion['PAGINA']['pieDePagina'] : self::$etiquetas['PIE_PAGINA'] = $sesion_pieDePagina;

        self::$etiquetas['TEXTO_BUSCADOR'] = $textos->id('TEXTO_BUSCADOR');
        self::$etiquetas['TEXTO_ESPERA'] = $textos->id('TEXTO_ESPERA');
        //
        self::cargarEstilos($modulo); //agregue la variable modulo
        self::cargarJavaScript($modulo); //agregue la variable modulo
        self::cargarSelectorEstilo();
        self::cargarMenus();
        self::cargarUsuarioSesion();
        self::codigoColumnaIzquierda();
    }

    /** Incluir referencias a archivos de hojas de estilos (CSS) 
     * 
     * @global type $configuracion
     * @global type $sesion_tema
     * @param type $modulo
     */
    protected static function cargarEstilos($modulo) {
        global $configuracion, $sesion_tema;

        $estilos = '';


        if (isset($sesion_tema)) {

            if ($sesion_tema == 'Gris') {
                $estilo = 'GRIS';
            } elseif ($sesion_tema == 'Azul-Blanco') {
                $estilo = 'AZUL-BLANCO';
            } else {
                $estilo = 'AZUL-VERDE';
            }


            foreach ($configuracion['ESTILOS'][$estilo] as $archivo) {
                $ruta = $configuracion['SERVIDOR']['media'] . $configuracion['RUTAS']['estilos'] . "/" . $archivo;
                $estilos .= "   <link href=\"$ruta\" rel=\"stylesheet\" type=\"text/css\" media=\"screen\" />\n";
            }
        }

        //cargar los estilos del modulo actual
        if (isset($configuracion['ESTILOS'][$modulo->nombre])) {
            foreach ($configuracion['ESTILOS'][$modulo->nombre] as $archivo) {
                $ruta = $configuracion['SERVIDOR']['media'] . $configuracion['RUTAS']['estilos'] . "/" . $archivo;

//                if(file_exists($ruta)){
                $estilos .= "   <link href=\"$ruta\" rel=\"stylesheet\" type=\"text/css\" media=\"screen\" />\n";

//                }
            }
        }

        $ruta = $configuracion['RUTAS']['media'] . "/" . $configuracion['RUTAS']['estilos'] . "/modulos/" . strtolower($modulo->nombre) . "/" . strtolower($modulo->nombre) . ".css";
        if (file_exists($ruta)) {
            $ruta = $configuracion['SERVIDOR']['media'] . $configuracion['RUTAS']['estilos'] . "/modulos/" . strtolower($modulo->nombre) . "/" . strtolower($modulo->nombre) . ".css";
            $estilos .= "   <link href=\"$ruta\" rel=\"stylesheet\" type=\"text/css\" media=\"screen\" />\n";
        }


        self::$etiquetas['HOJAS_ESTILOS'] = $estilos;
    }

    /** Incluir referencias a archivos de código JavaScript 
     * 
     * @global type $configuracion
     * @param type $modulo
     */
    protected static function cargarJavaScript($modulo) {
        global $configuracion;

        $JavaScript = "";

        foreach ($configuracion['JAVASCRIPT']['GENERAL'] as $archivo) {

            if (preg_match("|^https?\:\/\/|", $archivo)) {
                $JavaScript .= "  <script type=\"text/javascript\" src=\"$archivo\"></script>\n";
            } else {
                $ruta = $configuracion['SERVIDOR']['media'] . $configuracion['RUTAS']['javascript'] . "/" . $archivo;
                $JavaScript .= "  <script type=\"text/javascript\" src=\"$ruta\"></script>\n";
            }
        }


        $ruta = $configuracion['RUTAS']['media'] . "/" . $configuracion['RUTAS']['javascript'] . "/modulos/" . strtolower($modulo->nombre) . "/" . strtolower($modulo->nombre) . ".js";
        if (file_exists($ruta)) {
            $ruta = $configuracion['SERVIDOR']['media'] . $configuracion['RUTAS']['javascript'] . "/modulos/" . strtolower($modulo->nombre) . "/" . strtolower($modulo->nombre) . ".js";
            $JavaScript .= "  <script type=\"text/javascript\" src=\"$ruta\"></script>\n";
        }

        //cargar los javascript del modulo actual
        if (isset($configuracion['JAVASCRIPT'][$modulo->nombre])) {
            foreach ($configuracion['JAVASCRIPT'][$modulo->nombre] as $archivo) {

                if (preg_match("|^https?\:\/\/|", $archivo)) {
                    $JavaScript .= "  <script type=\"text/javascript\" src=\"$archivo\"></script>\n";
                } else {
                    $ruta = $configuracion['SERVIDOR']['media'] . $configuracion['RUTAS']['javascript'] . "/" . $archivo;
                    $JavaScript .= "  <script type=\"text/javascript\" src=\"$ruta\"></script>\n";
                }
            }
        }


        self::$etiquetas['JAVASCRIPT'] = $JavaScript;
    }

    /**
     * Insertar código HTML para mostrar buscador 
     *
     * @global type $sesion_tema
     * @global type $configuracion
     */
    protected static function cargarSelectorEstilo() {
        global $sesion_tema, $configuracion;

        $temas = array(
            'Gris'          => 'Gris',
            'Azul-Blanco'   => 'Azul-Blanco',
            'Azul-Verde'    => 'Azul-Verde'
        );


        $listaDesplegable = HTML::listaDesplegable('tema', $temas, $sesion_tema, '', 'selectTemas');


        $select = $listaDesplegable;
        $select .= HTML::campoOculto('tema', '', 'campoTema');
        $ruta = '/ajax/inicio/cambiarApariencia';
        $formulario = HTML::forma($ruta, $select, '', '', '', '', 'formuCategoria');


        self::$etiquetas['BLOQUE_CAMBIO_ESTILOS'] = HTML::contenedor($formulario, 'formaBuscador') . HTML::campoOculto('rutaServidor', $configuracion['SERVIDOR']['principal'], 'rutaServidor');
    }

    /**
     * Insertar código HTML para la barra de enlaces corporativos
     */
    protected static function cargarMenus() {

        $enlaces = '';
        $enlaces .= '<ul class="sf-menu" id="mainMenu">';
        $enlaces .= self::arbolMenuPrincipal($elemento = '');
        $enlaces .= "</ul>\n";

        self::$etiquetas['ENLACES_CORPORATIVOS'] = $enlaces;
    }

    /**
     * Requerida por self::cargarMenus()
     * 
     * @global type $sql
     * @global type $enlaces
     * @global type $sesion_usuarioSesion
     * @param type $elemento
     * @return string
     */
    protected static function arbolMenuPrincipal($elemento) {
        global $sql, $enlaces, $sesion_usuarioSesion;

        if (!empty($sesion_usuarioSesion->id)) {
            $validar_usuario = true;
            $condicion_extra = 'AND (m.tipo_menu = "0" OR m.global = "1")';
        } else {
            $validar_usuario = false;
            $condicion_extra = 'AND (m.tipo_menu = "1" OR m.global = "1")';
        }

        if ($validar_usuario) {
            if ($sesion_usuarioSesion->id == 0) {
                $tablas = array('m' => 'modulos');
                $columnas = array('m.id', 'm.id_padre', 'm.nombre_menu', 'm.nombre', 'm.url');
                $condicion = 'm.id = m.id_padre AND m.menu = "1" ' . $condicion_extra;
            } else {
                $tablas = array('m' => 'modulos', 'pmu' => 'permisos_modulos_usuarios');
                $columnas = array('m.id', 'm.id_padre', 'm.nombre_menu', 'm.nombre', 'm.url');
                $condicion = 'm.id = m.id_padre AND m.menu = "1" AND m.id = pmu.id_modulo AND pmu.id_usuario = "' . $sesion_usuarioSesion->id . '" ' . $condicion_extra;
            }
        } else {
            $tablas = array('m' => 'modulos');
            $columnas = array('m.id', 'm.id_padre', 'm.nombre_menu', 'm.nombre', 'm.url');
            $condicion = 'm.id = m.id_padre AND m.menu = "1" ' . $condicion_extra;
        }

        $ordenamiento = 'orden ASC';
        $agrupamiento = 'id';

        if ($elemento == '') {
            $resultado = $sql->seleccionar($tablas, $columnas, $condicion, $agrupamiento, $ordenamiento);
            if ($sql->filasDevueltas) {
                while ($datos = $sql->filaEnObjeto($resultado)) {
                    $item = $datos->id;
                    $nombre = $datos->nombre_menu;
                    if ($datos->url == '') {
                        $url = '#a';
                    } else {
                        $url = HTML::urlInterna($datos->nombre);
                    }

                    $enlaces .='<li><a href="' . $url . '">' . $nombre . '</a>';

                    if (isset($sesion_usuarioSesion) && $sesion_usuarioSesion->id == 0) {
                        $condicion = 'm.id_padre = "' . $item . '" AND m.id != "' . $item . '" AND m.menu = "1"';
                    } else {
                        $condicion = 'm.id_padre = "' . $item . '" AND m.id != "' . $item . '" AND m.menu = "1" AND m.id = pmu.id_modulo AND pmu.id_usuario = "' . $sesion_usuarioSesion->id . '"';
                    }
                    $resultado2 = $sql->seleccionar($tablas, $columnas, $condicion, $agrupamiento, $ordenamiento);

                    if ($sql->filasDevueltas) {
                        $enlaces .= '<ul>';
                        self::arbolMenuPrincipal($item);
                        $enlaces .= '</ul>';
                    }
                    $enlaces .= '</li>';
                }
            }
        } else {
            if ($sesion_usuarioSesion->id == 0) {
                $condicion = 'm.id_padre = "' . $elemento . '" AND m.id != "' . $elemento . '" AND m.menu = "1"';
            } else {
                $condicion = 'm.id_padre = "' . $elemento . '" AND m.id != "' . $elemento . '" AND m.menu = "1" AND m.id = pmu.id_modulo AND pmu.id_usuario = "' . $sesion_usuarioSesion->id . '"';
            }
            $resultado = $sql->seleccionar($tablas, $columnas, $condicion, $agrupamiento, $ordenamiento);

            if ($sql->filasDevueltas) {
                while ($datos = $sql->filaEnObjeto($resultado)) {
                    $item = $datos->id;
                    $nombre = $datos->nombre_menu;
                    if ($datos->url == '') {
                        $url = '#a';
                    } else {
                        $url = HTML::urlInterna($datos->nombre);
                    }

                    $enlaces .='<li style="border:1px solid #ffffff"><a href="' . $url . '">' . $nombre . '</a>';

                    if ($sesion_usuarioSesion->id == 0) {
                        $condicion = 'm.id_padre = "' . $item . '" AND m.id != "' . $item . '" AND m.menu = "1"';
                    } else {
                        $condicion = 'm.id_padre = "' . $item . '" AND m.id != "' . $item . '" AND m.menu = "1" AND m.id = pmu.id_modulo AND pmu.id_usuario = "' . $sesion_usuarioSesion->id . '"';
                    }
                    $resultado2 = $sql->seleccionar($tablas, $columnas, $condicion, $agrupamiento, $ordenamiento);

                    if ($sql->filasDevueltas) {
                        $enlaces .= '<ul>';
                        self::arbolMenuPrincipal($item);
                        $enlaces .= '</ul>';
                    }
                    $enlaces .= '</li>';
                }
            }
        }
        return $enlaces;
    }

    /*     * * Insertar código HTML con las opciones para el inicio de sesión del usuario ** */

    protected static function cargarUsuarioSesion() {
        global $sesion_usuarioSesion, $textos, $sql;

        /*         * * El usuario no ha iniciado sesión ** */
        if (!isset($sesion_usuarioSesion)) {

            $sedesEmpresa = $sql->seleccionar(array('sedes_empresa'), array('id', 'nombre'), 'id NOT IN (0)', 'id', 'nombre ASC');
            $sedes = array();
            while ($objeto = $sql->filaEnObjeto($sedesEmpresa)) {
                $sedes[$objeto->id] = $objeto->nombre;
            }
            $listaSedesEmpresa = HTML::listaDesplegable('datos[sede]', $sedes, '', '', 'listaSedesEmpresa');

            $formaUsuarioExistente = "";
            /*             * * Formulario para el inicio de sesión de usuarios existentes ** */
            $formaUsuarioExistente .= HTML::etiqueta($textos->id('USUARIO'));
            $formaUsuarioExistente .= HTML::parrafo(HTML::campoTexto('usuario', 12, 12, '', '', 'campoUsuario'), '');

            $formaUsuarioExistente .= HTML::etiqueta($textos->id('CONTRASENA'));
            $formaUsuarioExistente .= HTML::parrafo(HTML::campoClave('contrasena', '12', 12), '');

            $formaUsuarioExistente .= HTML::parrafo($listaSedesEmpresa, 'margenSuperior');
            $claseSlider = 'oculto estiloSlider ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all';
            $formaUsuarioExistente .= HTML::parrafo($textos->id('DESLICE_LA_BARRA'), 'oculto negrilla margenSuperior', 'parrafoMensajeSlider');
            $formaUsuarioExistente .= HTML::parrafo("", "negrilla margenSuperior", "parrafoSlider");
            $formaUsuarioExistente .= HTML::contenedor("", $claseSlider, "sliderInicio");

            $formaUsuarioExistente .= HTML::boton('usuario', $textos->id('INICIAR_SESION'));
            $formaUsuarioExistente1 = HTML::forma('/ajax/usuarios/validate', $formaUsuarioExistente);

            $titulosPestana = array(
                HTML::frase($textos->id('INICIAR_SESION'))
            );
            $contenidoPestana = array($formaUsuarioExistente1);

            self::$etiquetas['BLOQUE_SESION'] = HTML::contenedor(HTML::acordeon($titulosPestana, $contenidoPestana, 'pestanasInicioSesion', ""), "bloqueLogueo");
        } else {
            $contenido = HTML::enlace(HTML::imagen($sesion_usuarioSesion->persona->imagenPrincipal, 'imagenPrincipalUsuario'), $sesion_usuarioSesion->url, '', '');
            $contenido .= HTML::parrafo($sesion_usuarioSesion->persona->nombreCompleto, 'nombreSesion cursiva');
            $contenido .= HTML::parrafo($sesion_usuarioSesion->sede->nombre, '', 'sedeEmpresa');
            $contenido .= HTML::contenedor('', 'contenedorCerrarSesion enlaceAjaxRuta', 'contenedorCerrarSesion', array('ruta' => '/ajax/usuarios/logout', 'ayuda' => $textos->id('FINALIZAR')));

            self::$etiquetas['BLOQUE_SESION'] = $contenido;
        }
    }

    /**
     *
     * @global type $textos 
     */
    protected static function codigoColumnaIzquierda() {
        global $textos;
        $codigo = '';

        $pestana1 = '';
        $pestana1 .= HTML::contenedor('', 'imagenCalculadora', 'mostrarCalc');
        $pestana1 .= HTML::parrafo($textos->id('MOSTRAR_CALCULADORA'), 'flotanteDerecha margenDerecha', '');


        $pestanas = array(
            HTML::frase($textos->id('HERRAMIENTAS_DE_OPERACION'), 'letraBlanca') => $pestana1
        );

        $codigo .= HTML::pestanas2('pestanasColumnaIzquierda', $pestanas, 'margen30 pestanasMenuIzquierdo');


        self::$etiquetas['BLOQUE_IZQUIERDO'] = $codigo;
    }

    /*     * * Enviar código HTML generado al cliente ** */

    public static function generarCodigo() {

        foreach (self::$etiquetas as $etiqueta => $valor) {
            self::$contenido = preg_replace("/\{\%" . $etiqueta . "\%\}/", rtrim($valor), self::$contenido);
        }
    }

}