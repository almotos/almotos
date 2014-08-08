<?php

/**
 *
 * @package     FOM
 * @subpackage  Usuarios
 * @author      Pablo Andrés Vélez Vidal <pavelez8@misena.edu.co>
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2012 GENESYS Soft.
 * @version     0.2
 *
 * */
global $url_ruta, $sql, $configuracion, $sesion_usuarioSesion, $textos, $modulo;

if (isset($url_ruta)) {

    $contenido  = "";
    $contenido .= HTML::campoOculto('nombreModulo', ucwords(strtolower($modulo->nombre)), 'nombreModulo');
    

    $usuario = new Usuario($url_ruta);


    if (isset($usuario->id)) {
        Plantilla::$etiquetas["TITULO_PAGINA"] .= " :: " . $textos->id("MODULO_ACTUAL");
        Plantilla::$etiquetas["DESCRIPCION"] = $usuario->usuario;


        $contenidoUsuario  = HTML::imagen($usuario->persona->imagenPrincipal, "imagenUsuario");
        $contenidoUsuario .= HTML::parrafo($textos->id("NOMBRE_COMPLETO"), "negrilla");
        $contenidoUsuario .= HTML::parrafo($usuario->persona->nombreCompleto, "justificado margenInferior ancho250px");

        if ($usuario->persona->ciudadResidencia) {
            $contenidoUsuario .= HTML::parrafo($textos->id("CIUDAD"), "negrilla");
            $contenidoUsuario .= HTML::parrafo($usuario->persona->ciudadResidencia . ", " . $usuario->persona->paisResidencia, "justificado margenInferior ancho250px");
        }

        if ($usuario->persona->observaciones != "" && $usuario->persona->observaciones != "no registra") {
            $contenidoUsuario .= HTML::parrafo($textos->id("OBSERVACIONES"), "negrilla");
            $contenidoUsuario .= HTML::parrafo($usuario->persona->observaciones, "justificado margenInferior ancho250px");
        }

        if ($usuario->tipo) {
            $contenidoUsuario .= HTML::parrafo($textos->id("PERFIL"), "negrilla");
            $contenidoUsuario .= HTML::parrafo($usuario->tipo, "justificado margenInferior ancho250px");
        }

        $contenidoUsuario1 = HTML::contenedor($contenidoUsuario, "", "contenidoUsuario");

        if ((isset($sesion_usuarioSesion) && $sesion_usuarioSesion->id == $usuario->id) || ( isset($sesion_usuarioSesion) && $sesion_usuarioSesion->id == 0 )) {

            /**
             *  Inicio contenido pesta?a de notificaciones 
             */
            $listaNotificaciones = array();

            $notificaciones = $sql->seleccionar(array("notificaciones"), array("fecha" => "UNIX_TIMESTAMP(fecha)", "contenido", "id"), "id_usuario = '" . $usuario->id . "'", "", "fecha DESC", 0, 7);

            if ($sql->filasDevueltas) {

                while ($notificacion = $sql->filaEnObjeto($notificaciones)) {

                    if ((isset($sesion_usuarioSesion) && $sesion_usuarioSesion->id == $usuario->id) || (isset($sesion_usuarioSesion) && $sesion_usuarioSesion->idTipo == 0)) {
                        $botonEliminar = "";
                        $botonEliminar .= HTML::botonAjax("basura", "ELIMINAR", HTML::urlInterna("INICIO", "", true, "borrarNotificacion"), array("id" => $notificacion->id));
                        $botonEliminar1 = HTML::contenedor(HTML::contenedor($botonEliminar, "contenedorBotonesLista", "contenedorBotonesLista"), "botonesLista", "botonesLista");
                    }
                    $texto_notificacion = $botonEliminar1;
                    $texto_notificacion .= HTML::parrafo($notificacion->contenido, "margenSuperior");
                    $texto_notificacion .= HTML::parrafo(date("D, d M Y h:i:s A", $notificacion->fecha), "pequenia cursiva negrilla");
                    $texto_notificaciones = HTML::contenedor($texto_notificacion, "contenedorGris");
                    $texto_notificaciones1 = HTML::contenedor($texto_notificaciones, "contenedorNotificacion", "contenedorNotificacion" . $notificacion->id);
                    $listaNotificaciones[] = $texto_notificaciones1;
                }
            } else {
                $listaNotificaciones[] = HTML::parrafo($textos->id("SIN_NOTIFICACIONES"));
            }

            $listaNotificaciones1 = HTML::lista($listaNotificaciones, "listaVertical bordeInferiorLista");
            
            //Codigo que arma el calendario
            $calendar = HTML::contenedor('', '', 'eventsCalendar');
            
            $contenedorCalendario = HTML::contenedor($calendar, 'contenedorCalendarioeventos');

            $barraPersonal = array(
                HTML::frase($textos->id("NOTIFICACIONES"), "letraBlanca", "listaNotificaciones") => $listaNotificaciones1,
                HTML::frase($textos->id("DOCUMENTOS"), "letraBlanca") => Recursos::bloqueArchivos("USUARIOS", $usuario->id, $usuario->id),
                HTML::frase($textos->id('MIS_DATOS'), 'letraBlanca') => $contenidoUsuario1,
                HTML::frase($textos->id('CALENDARIO'), 'letraBlanca') => $contenedorCalendario
            );

            $contenido .= HTML::contenedor(HTML::pestanas2("barraPersonal", $barraPersonal), "pestanasRecursosUsuarios");
        }
    }
} else {

    global $sesion_usuarioSesion, $modulo, $configuracion;

    $tituloBloque = $textos->id("MODULO_ACTUAL");
    $tituloBloque .= HTML::imagen($configuracion['SERVIDOR']['media'] . $configuracion['RUTAS']['imagenesEstilos'] . '/' . strtolower($modulo->url) . '.png', 'iconoModulo');
    $objeto = new Usuario(); /* creacion del objeto */
    $excluidas = array("0"); //items excluidos en la consulta
    $item = "";
    $contenido = "";
    $contenido .= HTML::contenedor(HTML::contenedor($textos->id("AYUDA_MODULO"), 'ui-corner-all'), 'ui-widget-shadow ui-corner-all oculto', 'contenedorAyudaUsuario');

    //campo oculto del cual el javascript sacara el nombre del modulo actual ->para??
    $item .= HTML::campoOculto("nombreModulo", ucwords(strtolower($modulo->nombre)), "nombreModulo");

    $item .= HTML::campoOculto("orden" . ucwords(strtolower($modulo->nombre)), "descendente|" . $objeto->ordenInicial, "ordenGlobal");
    $item .= HTML::campoOculto("condicion" . ucwords(strtolower($modulo->nombre)), "", "condicionGlobal");

    /**
     * Datos para la paginacion
     * */
    $registros = $configuracion["GENERAL"]["registrosPorPagina"];
    $pagina = 1;
    $registroInicial = 0;

    /**
     * Formulario para adicionar un nuevo elemento
     * */
    //verificar si el usuario actual tiene permisos para agregar en este modulo
    $puedeAgregar = Perfil::verificarPermisosAdicion($modulo->nombre);
    
    $botonAdicionar = "";

    if ((isset($sesion_usuarioSesion) && $puedeAgregar) || isset($sesion_usuarioSesion) && $sesion_usuarioSesion->idTipo == 0) {
        $botonAdicionar = HTML::contenedor(HTML::botonAdicionarItem($objeto->urlBase, $textos->id("ADICIONAR_ITEM")), "flotanteDerecha margenInferior", "botonAdicionar" . ucwords(strtolower($modulo->nombre)) . "");
    }
    
    /* Formulario para eliminar un masivo de elementos */
    $puedeEliminar = Perfil::verificarPermisosEliminacion($modulo->nombre);
    
    $botonEliminar = '';
    
    if ((isset($sesion_usuarioSesion) && $puedeEliminar) || isset($sesion_usuarioSesion) && $sesion_usuarioSesion->idTipo == 0) {
        $botonEliminar .= HTML::contenedor(HTML::boton('basura', $textos->id('ELIMINAR_MASIVO'), 'directo', '', 'botonBorrarMasivo', '', array('ruta' => '/ajax/' . $modulo->url . '/eliminarVarios')), 'flotanteDerecha margenInferior botonEliminarMasivo oculto', 'botonEliminarMasivo' . ucwords(strtolower($modulo->nombre)) . '');
    }

    /* Checkbox que se encarga de marcar todas las filas de la tabla */
    $chkMarcarFilas = '';
    $chkMarcarFilas .= HTML::campoChequeo('chkMarcarFilas', false, 'chkMarcarFilas', 'chkMarcarFilas', array('ayuda' => $textos->id('AYUDA_MARCAR_FILAS')));
    $chkMarcarFilas .= HTML::frase($textos->id('MARCAR_FILAS'), 'alineadoIzquierda medioMargenIzquierda negrilla');

    /* campo de texto para seleccionar cuantos registros traer en la consulta */
    $campoNumRegistros = '';
    $campoNumRegistros .= HTML::frase($textos->id('NUMERO_FILAS'), 'margenIzquierdaDoble medioMargenDerecha');
    $campoNumRegistros .= HTML::campoTexto('cantidad_registros', 5, 5, $registros . '', 'soloNumerosEnter', 'campoNumeroRegistros', array('ruta' => '/ajax/' . $modulo->url . '/move'), $textos->id('AYUDA_SELECCIONAR_CANTIDAD_REGISTROS'));

    /**
     * Boton que carga la ventana modal para realizar la busqueda
     * */
    $destino                    = HTML::urlInterna($modulo->nombre, 0, true, "search");
    $botonRestaurar             = HTML::contenedor("", "botonRestaurarConsulta", "botonRestaurarConsulta", array("alt" => HTML::urlInterna($modulo->nombre, 0, true, "move"), "title" => $textos->id("RESTAURAR_CONSULTA")));
    $botonBuscador              = HTML::contenedor("", "botonBuscador", "botonBuscador", array("alt" => HTML::urlInterna($modulo->nombre, 0, true, "search"), "title" => $textos->id("BUSCAR_ITEM")));
    $buscador                   = HTML::campoTexto("datos[patron]", 22, "", "", "campoBuscador margenIzquierdaDoble", "campoBuscador") . $botonRestaurar . $botonBuscador;
    $buscador                   = HTML::forma($destino, $buscador);
    $buscador1                  = HTML::contenedor($buscador, "flotanteDerecha", "botonBuscar" . ucwords(strtolower($modulo->nombre)) . "");
    $contenedorNotificaciones   = HTML::contenedor("", "contenedorNotificaciones", "contenedorNotificaciones");
    $botonesSuperiores          = HTML::contenedor($buscador1 . $botonAdicionar . $botonEliminar . $chkMarcarFilas . $campoNumRegistros . $contenedorNotificaciones, "", "botonesSuperioresModulo");

    /**
     * Verifico que se haya iniciado una sesion y que tenga permisos para ver el modulo
     * */
    if ((isset($sesion_usuarioSesion) && Perfil::verificarPermisosModulo($objeto->idModulo)) || isset($sesion_usuarioSesion) && $sesion_usuarioSesion->idTipo == 0) {
        //Declaracion del arreglo lista... y carga de datos en él
        $arregloItems = $objeto->listar($registroInicial, $registros, $excluidas, "");

        $datosPaginacion = array($objeto->registros, $registroInicial, $registros, $pagina);
        $item .= $objeto->generarTabla($arregloItems, $datosPaginacion);


        $codigo = HTML::contenedor($botonesSuperiores . "<br>" . $item, "listaItem", "listaItem");
        $contenido .= HTML::bloque("bloqueContenidoPrincipal", $tituloBloque, $codigo, "", "overflowVisible");
    } else {
        $contenido = HTML::contenedor($textos->id("SIN_PERMISOS_ACCESO_MODULO"), "textoError");
    }
}

Plantilla::$etiquetas["BLOQUE_CENTRAL"] = $contenido;
