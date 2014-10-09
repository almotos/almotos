<?php

/**
 * @package     FOM (Framework OpenSource Multiplatform)
 * @subpackage  Dias Festivos
 * @author      Pablo Andres Velez Vidal <pavelez@genesyscorp.com.co>
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2013 GENESYS
 * @version     0.1
 * 
 * Framework basado en FOLCS, desarrollado por Francisco Lozano de FELINUX ltda. Cali - Colombia
 * */
global $url_accion, $forma_procesar, $forma_id, $forma_datos, $forma_cantidadRegistros, $forma_pagina, $forma_orden, $forma_nombreOrden, $url_cadena, $forma_dialogo, $forma_consultaGlobal, $forma_cantidad, $forma_cadenaItems;


if (isset($url_accion)) {
    switch ($url_accion) {
        case 'add' : $datos = ($forma_procesar) ? $forma_datos : array();
            adicionarItem($datos);
            break;
        case 'see' : cosultarItem($forma_id);
            break;
        case 'edit' : $datos = ($forma_procesar) ? $forma_datos : array();
            modificarItem($forma_id, $datos);
            break;
        case 'delete' : $confirmado = ($forma_procesar) ? true : false;
            eliminarItem($forma_id, $confirmado, $forma_dialogo);
            break;

        case 'search' : buscarItem($forma_datos, $forma_cantidadRegistros);
            break;
        case 'move' : paginador($forma_pagina, $forma_orden, $forma_nombreOrden, $forma_consultaGlobal, $forma_cantidadRegistros);
            break;
        case 'listar' : listarItems($url_cadena);
            break;
        case 'eliminarVarios' : $confirmado = ($forma_procesar) ? true : false;
            eliminarVarios($confirmado, $forma_cantidad, $forma_cadenaItems);
            break;
    }
}

/**
 *
 * @global type $textos
 * @param type $id 
 */
function cosultarItem($id) {
    global $textos;

    $objeto = new DiaFestivo($id);
    $respuesta = array();

    $codigo = HTML::campoOculto('procesar', 'true');
    $codigo .= HTML::campoOculto('id', $id);
    $codigo .= HTML::parrafo($textos->id('FECHA'), 'negrilla margenSuperior');
    $codigo .= HTML::parrafo($objeto->fecha, '', '');
    $codigo .= HTML::parrafo($textos->id('DESCRIPCION'), 'negrilla margenSuperior');
    $codigo .= HTML::parrafo($objeto->descripcion, '', '');
    $codigo .= HTML::parrafo($textos->id('ESTADO'), 'negrilla margenSuperior');
    $activo = ($objeto->activo) ? HTML::frase($textos->id('ACTIVO'), 'activo') : HTML::frase($textos->id('INACTIVO'), 'inactivo');
    $codigo .= HTML::parrafo($activo, '', '');

    $respuesta['generar'] = true;
    $respuesta['codigo'] = $codigo;
    $respuesta['titulo'] = HTML::parrafo($textos->id('CONSULTAR_ITEM'), 'letraBlanca negrilla subtitulo');
    $respuesta['destino'] = '#cuadroDialogo';
    $respuesta['ancho'] = 450;
    $respuesta['alto'] = 300;



    Servidor::enviarJSON($respuesta);
}

/**
 * Metodo adicionar 
 * */
function adicionarItem($datos = array()) {
    global $textos, $modulo, $sesion_usuarioSesion;
    
    /**
    * Verificar si el usuario que esta en la sesion tiene permisos para esta accion
    */
        $puedeAgregar = Perfil::verificarPermisosAdicion($modulo->nombre);
    
    if(!$puedeAgregar && $sesion_usuarioSesion->id != 0) {
        $respuesta            = array();
        $respuesta['error']   = true;
        $respuesta['mensaje'] = $textos->id('ACCESO_DENEGADO');
        
        Servidor::enviarJSON($respuesta);
        return FALSE;
        
    }

    $objeto = new DiaFestivo();
    $destino = '/ajax' . $objeto->urlBase . '/add';
    $respuesta = array();

    if (empty($datos)) {

        $codigo = HTML::campoOculto('procesar', 'true');
        $codigo .= HTML::campoOculto('datos[dialogo]', '', 'idDialogo');
        $codigo .= HTML::parrafo($textos->id('FECHA'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[fecha]', 20, 20, '', 'fechaAntigua');
        $codigo .= HTML::parrafo($textos->id('DESCRIPCION'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[descripcion]', 40, 255);
        $codigo .= HTML::parrafo(HTML::campoChequeo('datos[activo]', true) . $textos->id('ACTIVO'), 'margenSuperior');
        $codigo .= HTML::parrafo(HTML::boton('chequeo', $textos->id('ACEPTAR'), 'botonOk', 'botonOk', 'botonOk'), 'margenSuperior');
        $codigo .= HTML::parrafo(HTML::frase($textos->id('REGISTRO_AGREGADO'), 'textoExitoso', 'textoExitoso'), 'margenSuperior');
        $codigo1 = HTML::forma($destino, $codigo, 'P');

        $respuesta['generar'] = true;
        $respuesta['codigo'] = $codigo1;
        $respuesta['titulo'] = HTML::parrafo($textos->id('ADICIONAR_ITEM'), 'letraBlanca negrilla subtitulo');
        $respuesta['destino'] = '#cuadroDialogo';
        $respuesta['ancho'] = 450;
        $respuesta['alto'] = 300;
    } else {

        $respuesta['error'] = true;


        if (empty($datos['fecha'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_FECHA');
        } elseif (empty($datos['descripcion'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_DESCRIPCION');
        } else {
            $idItem = $objeto->adicionar($datos);
            if ($idItem) {
                /*                 * ************** Creo el nuevo item que se insertara via ajax *************** */
                $objeto = new DiaFestivo($idItem);

                $estado = ($objeto->activo) ? HTML::frase($textos->id('ACTIVO'), 'activo') : HTML::frase($textos->id('INACTIVO'), 'inactivo');

                $celdas = array($objeto->fecha, $objeto->descripcion, $estado);
                $claseFila = '';
                $idFila = $idItem;
                $celdas1 = HTML::crearNuevaFila($celdas, $claseFila, $idFila);

                $respuesta['error'] = false;
                $respuesta['accion'] = 'insertar';
                $respuesta['contenido'] = $celdas1;
                $respuesta['idContenedor'] = '#tr_' . $idItem;
                $respuesta['idDestino'] = '#tablaRegistros';

                if ($datos['dialogo'] == '') {
                    $respuesta['insertarNuevaFila'] = true;
                } else {
                    $respuesta['insertarNuevaFilaDialogo'] = true;
                    $respuesta['ventanaDialogo'] = $datos['dialogo'];
                }
            } else {
                $respuesta['mensaje'] = $textos->id('ERROR_DESCONOCIDO');
            }
        }
    }

    Servidor::enviarJSON($respuesta);
}

/**
 * Funcion
 * @global type $textos
 * @global type $sql
 * @param type $id
 * @param type $datos 
 */
function modificarItem($id, $datos = array()) {
    global $textos, $modulo, $sesion_usuarioSesion;
 
    /**
    * Verificar si el usuario que esta en la sesion tiene permisos para esta accion
    */
        $puedeModificar = Perfil::verificarPermisosModificacion($modulo->nombre);
    
    if(!$puedeModificar && $sesion_usuarioSesion->id != 0) {
        $respuesta            = array();
        $respuesta['error']   = true;
        $respuesta['mensaje'] = $textos->id('ACCESO_DENEGADO');
        
        Servidor::enviarJSON($respuesta);
        return FALSE;
        
    }
    
    $objeto = new DiaFestivo($id);
    $destino = '/ajax' . $objeto->urlBase . '/edit';
    $respuesta = array();

    if (empty($datos)) {
        $codigo = HTML::campoOculto('procesar', 'true');
        $codigo .= HTML::campoOculto('id', $id);
        $codigo .= HTML::campoOculto('datos[dialogo]', '', 'idDialogo');
        $codigo .= HTML::parrafo($textos->id('FECHA'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[fecha]', 20, 20, $objeto->fecha, 'fechaAntigua');
        $codigo .= HTML::parrafo($textos->id('DESCRIPCION'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[descripcion]', 40, 255, $objeto->descripcion);
        $codigo .= HTML::parrafo(HTML::campoChequeo('datos[activo]', $objeto->activo) . $textos->id('ACTIVO'), 'margenSuperior');
        $codigo .= HTML::parrafo(HTML::boton('chequeo', $textos->id('ACEPTAR'), 'botonOk', 'botonOk', 'botonOk'), 'margenSuperior');
        $codigo .= HTML::parrafo(HTML::frase($textos->id('REGISTRO_MODIFICADO'), 'textoExitoso', 'textoExitoso'), 'margenSuperior');
        $codigo1 = HTML::forma($destino, $codigo, 'P');


        $respuesta['generar'] = true;
        $respuesta['codigo'] = $codigo1;
        $respuesta['titulo'] = HTML::parrafo($textos->id('MODIFICAR_ITEM'), 'letraBlanca negrilla subtitulo');
        $respuesta['destino'] = '#cuadroDialogo';
        $respuesta['ancho'] = 450;
        $respuesta['alto'] = 300;
    } else {

        $respuesta['error'] = true;

        if (empty($datos['fecha'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_FECHA');
        } elseif (empty($datos['descripcion'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_DESCRIPCION');
        } else {
            $idItem = $objeto->modificar($datos);
            if ($idItem) {
                /*                 * ************** Creo el nuevo item que se insertara via ajax *************** */
                $objeto = new DiaFestivo($id);

                $estado = ($objeto->activo) ? HTML::frase($textos->id('ACTIVO'), 'activo') : HTML::frase($textos->id('INACTIVO'), 'inactivo');

                $celdas = array($objeto->fecha, $objeto->descripcion, $estado);
                $celdas1 = HTML::crearFilaAModificar($celdas);

                $respuesta['error'] = false;
                $respuesta['accion'] = 'insertar';
                $respuesta['contenido'] = $celdas1;
                $respuesta['idContenedor'] = '#tr_' . $id;
                $respuesta['idDestino'] = '#tr_' . $id;

                if ($datos['dialogo'] == '') {
                    $respuesta['modificarFilaTabla'] = true;
                } else {
                    $respuesta['modificarFilaDialogo'] = true;
                    $respuesta['ventanaDialogo'] = $datos['dialogo'];
                }
            } else {
                $respuesta['mensaje'] = $textos->id('ERROR_DESCONOCIDO');
            }
        }
    }

    Servidor::enviarJSON($respuesta);
}

/**
 * Funcion Eliminar
 * @global type $textos
 * @param type $id
 * @param type $confirmado 
 */
function eliminarItem($id, $confirmado, $dialogo) {
    global $textos, $modulo, $sesion_usuarioSesion;
    
    /**
    * Verificar si el usuario que esta en la sesion tiene permisos para esta accion
    */
        $puedeEliminar = Perfil::verificarPermisosEliminacion($modulo->nombre);    
    
     if(!$puedeEliminar && $sesion_usuarioSesion->id != 0) {
        $respuesta            = array();
        $respuesta['error']   = true;
        $respuesta['mensaje'] = $textos->id('ACCESO_DENEGADO');
        
        Servidor::enviarJSON($respuesta);
        return FALSE;
        
    }

    $objeto = new DiaFestivo($id);
    $destino = '/ajax' . $objeto->urlBase . '/delete';
    $respuesta = array();

    if (!$confirmado) {
        $titulo = HTML::frase($objeto->descripcion, 'negrilla');
        $titulo1 = str_replace('%1', $titulo, $textos->id('CONFIRMAR_ELIMINACION'));
        $codigo = HTML::campoOculto('procesar', 'true');
        $codigo .= HTML::campoOculto('id', $id);
        $codigo .= HTML::campoOculto('datos[dialogo]', '', 'idDialogo');
        $codigo .= HTML::parrafo($titulo1);
        $codigo .= HTML::parrafo(HTML::boton('chequeo', $textos->id('ACEPTAR'), '', 'botonOk', 'botonOk'), 'margenSuperior');
        $codigo .= HTML::parrafo($textos->id('REGISTRO_ELIMINADO'), 'textoExitoso', 'textoExitoso');
        $codigo1 = HTML::forma($destino, $codigo);

        $respuesta['generar'] = true;
        $respuesta['codigo'] = $codigo1;
        $respuesta['destino'] = '#cuadroDialogo';
        $respuesta['titulo'] = HTML::parrafo($textos->id('ELIMINAR_ITEM'), 'letraBlanca negrilla subtitulo');
        $respuesta['ancho'] = 350;
        $respuesta['alto'] = 150;
    } else {

        if ($objeto->eliminar()) {
            $respuesta['error'] = false;
            $respuesta['accion'] = 'insertar';
            $respuesta['idDestino'] = '#tr_' . $id;
            if ($dialogo == '') {
                $respuesta['eliminarFilaTabla'] = true;
            } else {
                $respuesta['eliminarFilaDialogo'] = true;
                $respuesta['ventanaDialogo'] = $dialogo;
            }
        } else {
            $respuesta['mensaje'] = $textos->id('ERROR_DESCONOCIDO');
        }
    }

    Servidor::enviarJSON($respuesta);
}

/**
 * 
 * @global type $textos
 * @global type $sql
 * @param type $datos 
 */
function buscarItem($data, $cantidadRegistros = NULL) {
    global $textos, $configuracion;

    $data = explode('[', $data);
    $datos = $data[0];

    if (empty($datos)) {
        $respuesta['error'] = true;
        $respuesta['mensaje'] = $textos->id('ERROR_FALTA_CADENA_BUSQUEDA');
    } else if (!empty($datos) && strlen($datos) < 2) {
        $respuesta['error'] = true;
        $respuesta['mensaje'] = str_replace('%1', '2', $textos->id('ERROR_TAMAÑO_CADENA_BUSQUEDA'));
    } else {
        $item = '';
        $respuesta = array();
        $objeto = new DiaFestivo();
        $registros = $configuracion['GENERAL']['registrosPorPagina'];
        if (!empty($cantidadRegistros)) {
            $registros = (int) $cantidadRegistros;
        }
        $pagina = 1;
        $registroInicial = 0;


        $palabras = explode(' ', $datos);

        $condicionales = $data[1];

        if ($condicionales == '') {
            $condicion = "(df.descripcion REGEXP '(" . implode("|", $palabras) . ")')";
        } else {
            //$condicion = str_replace("]", "'", $data[1]);
            $condicionales = explode("|", $condicionales);

            $condicion = "(";
            $tam = sizeof($condicionales) - 1;
            for ($i = 0; $i < $tam; $i++) {
                $condicion .= $condicionales[$i] . " REGEXP '(" . implode("|", $palabras) . ")' ";
                if ($i != $tam - 1) {
                    $condicion .= " OR ";
                }
            }
            $condicion .= ")";
        }

        $arregloItems = $objeto->listar($registroInicial, $registros, array('0'), $condicion, 'df.descripcion');

        if ($objeto->registrosConsulta) {//si la consulta trajo registros
            $datosPaginacion = array($objeto->registrosConsulta, $registroInicial, $registros, $pagina, $objeto->registrosConsulta);
            $item .= $objeto->generarTabla($arregloItems, $datosPaginacion);
            $info = HTML::parrafo('Tu busqueda trajo ' . $objeto->registrosConsulta . ' resultados', 'textoExitosoNotificaciones');
        } else {
            $datosPaginacion = 0;
            $item .= $objeto->generarTabla($textos->id('NO_HAY_REGISTROS'), $datosPaginacion);
            $info = HTML::parrafo('Tu busqueda no trajo resultados, por favor intenta otra busqueda', 'textoErrorNotificaciones');
        }

        $respuesta['error'] = false;
        $respuesta['accion'] = 'insertar';
        $respuesta['contenido'] = $item;
        $respuesta['idContenedor'] = '#tablaRegistros';
        $respuesta['idDestino'] = '#contenedorTablaRegistros';
        $respuesta['paginarTabla'] = true;
        $respuesta['info'] = $info;
    }

    Servidor::enviarJSON($respuesta);
}

/**
 * 
 * @global type $configuracion
 * @param type $pagina
 * @param type $orden
 * @param type $nombreOrden
 * @param type $consultaGlobal
 * @param type $cantidadRegistros
 */
function paginador($pagina, $orden = NULL, $nombreOrden = NULL, $consultaGlobal = NULL, $cantidadRegistros = NULL) {
    global $configuracion;

    $item = '';
    $respuesta = array();
    $objeto = new DiaFestivo();

    $registros = $configuracion['GENERAL']['registrosPorPagina'];

    if (!empty($cantidadRegistros)) {
        $registros = (int) $cantidadRegistros;
    }

    if (isset($pagina)) {
        $pagina = $pagina;
    } else {
        $pagina = 1;
    }

    if (isset($consultaGlobal) && $consultaGlobal != "") {

        $data = explode("[", $consultaGlobal);
        $datos = $data[0];
        $palabras = explode(" ", $datos);

        if ($data[1] != "") {
            $condicionales = explode("|", $data[1]);

            $condicion = "(";
            $tam = sizeof($condicionales) - 1;
            for ($i = 0; $i < $tam; $i++) {
                $condicion .= $condicionales[$i] . " REGEXP '(" . implode("|", $palabras) . ")' ";
                if ($i != $tam - 1) {
                    $condicion .= " OR ";
                }
            }
            $condicion .= ")";

            $consultaGlobal = $condicion;
        } else {
            $consultaGlobal = "(df.descripcion REGEXP '(" . implode("|", $palabras) . ")')";
        }
    } else {
        $consultaGlobal = "";
    }

    if (!isset($nombreOrden)) {
        $nombreOrden = $objeto->ordenInicial;
    }


    if (isset($orden) && $orden == 'ascendente') {//ordenamiento
        $objeto->listaAscendente = true;
    } else {
        $objeto->listaAscendente = false;
    }

    if (isset($nombreOrden) && $nombreOrden == 'estado') {//ordenamiento
        $nombreOrden = 'activo';
    }

    $registroInicial = ($pagina - 1) * $registros;


    $arregloItems = $objeto->listar($registroInicial, $registros, array('0'), $consultaGlobal, $nombreOrden);

    if ($objeto->registrosConsulta) {//si la consulta trajo registros
        $datosPaginacion = array($objeto->registrosConsulta, $registroInicial, $registros, $pagina);
        $item .= $objeto->generarTabla($arregloItems, $datosPaginacion);
    }

    $respuesta['error'] = false;
    $respuesta['accion'] = 'insertar';
    $respuesta['contenido'] = $item;
    $respuesta['idContenedor'] = '#tablaRegistros';
    $respuesta['idDestino'] = '#contenedorTablaRegistros';
    $respuesta['paginarTabla'] = true;

    Servidor::enviarJSON($respuesta);
}

/**
 * Funcion que devuelve la respuesta para el autocompletar
 * @global type $sql
 * @param type $cadena 
 */
function listarItems($cadena) {
    global $sql;
    $respuesta = array();
    $consulta = $sql->seleccionar(array('dias_festivos'), array('descripcion'), "(descripcion LIKE '%$cadena%') AND activo = '1'", "", "descripcion ASC", 0, 20);

    while ($fila = $sql->filaEnObjeto($consulta)) {
        $respuesta[] = $fila->nombre;
    }

    Servidor::enviarJSON($respuesta);
}

/**
 * Funcion Eliminar
 * @global type $textos
 * @param type $id
 * @param type $confirmado 
 */
function eliminarVarios($confirmado, $cantidad, $cadenaItems) {
    global $textos, $modulo, $sesion_usuarioSesion;
    
    /**
    * Verificar si el usuario que esta en la sesion tiene permisos para esta accion
    */
        $puedeEliminarMasivo = Perfil::verificarPermisosEliminacion($modulo->nombre);
    
     if(!$puedeEliminarMasivo && $sesion_usuarioSesion->id != 0) {
        $respuesta            = array();
        $respuesta['error']   = true;
        $respuesta['mensaje'] = $textos->id('ACCESO_DENEGADO');
        
        Servidor::enviarJSON($respuesta);
        return FALSE;
        
    }
    
    $destino = '/ajax/dias_festivos/eliminarVarios';
    $respuesta = array();

    if (!$confirmado) {
        $titulo = HTML::frase($cantidad, 'negrilla');
        $titulo1 = str_replace('%1', $titulo, $textos->id('CONFIRMAR_ELIMINACION_VARIOS'));
        $codigo = HTML::campoOculto('procesar', 'true');
        $codigo .= HTML::campoOculto('cadenaItems', $cadenaItems, 'cadenaItems');
        $codigo .= HTML::parrafo($titulo1);
        $codigo .= HTML::parrafo(HTML::boton('chequeo', $textos->id('ACEPTAR'), '', 'botonOk', 'botonOk'), 'margenSuperior');
        $codigo .= HTML::parrafo($textos->id('REGISTRO_ELIMINADO'), 'textoExitoso', 'textoExitoso');
        $codigo1 = HTML::forma($destino, $codigo);

        $respuesta['generar'] = true;
        $respuesta['codigo'] = $codigo1;
        $respuesta['destino'] = '#cuadroDialogo';
        $respuesta['titulo'] = HTML::parrafo($textos->id('ELIMINAR_VARIOS_REGISTROS'), 'letraBlanca negrilla subtitulo');
        $respuesta['ancho'] = 350;
        $respuesta['alto'] = 150;
    } else {

        $cadenaIds = substr($cadenaItems, 0, -1);
        $arregloIds = explode(",", $cadenaIds);

        $eliminarVarios = true;
        foreach ($arregloIds as $val) {
            $objeto = new DiaFestivo($val);
            $eliminarVarios = $objeto->eliminar();
        }

        if ($eliminarVarios) {

            $respuesta['error'] = false;
            $respuesta['textoExito'] = true;
            $respuesta['mensaje'] = $textos->id('ITEMS_ELIMINADOS_CORRECTAMENTE');
            $respuesta['accion'] = 'recargar';
        } else {
            $respuesta['mensaje'] = $textos->id('ERROR_DESCONOCIDO');
        }
    }

    Servidor::enviarJSON($respuesta);
}

?>