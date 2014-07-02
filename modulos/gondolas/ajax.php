<?php

/**
 * @package     FOM (Framework OpenSource Multiplatform)
 * @subpackage  Gondolas
 * @author      Pablo Andres Velez Vidal <pavelez@genesyscorp.com.co>
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2013 GENESYS
 * @version     0.1
 * 
 * Framework basado en FOLCS, desarrollado por Francisco Lozano de FELINUX ltda. Cali - Colombia
 * */
global $url_accion, $forma_procesar, $forma_id, $forma_cantidadRegistros, $forma_datos, $forma_pagina, $forma_orden, $forma_nombreOrden, $url_cadena, $forma_dialogo, $forma_consultaGlobal, $forma_cantidad, $forma_cadenaItems;


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

    $objeto = new Gondola($id);
    $respuesta = array();

    $codigo = HTML::campoOculto('procesar', 'true');
    $codigo .= HTML::campoOculto('id', $id);
    $codigo .= HTML::parrafo($textos->id('NOMBRE'), 'negrilla margenSuperior');
    $codigo .= HTML::parrafo($objeto->nombre, '', '');
    $codigo .= HTML::parrafo($textos->id('BODEGA'), 'negrilla margenSuperior');
    $codigo .= HTML::parrafo($objeto->bodega, '', '');
    $codigo .= HTML::parrafo($textos->id('LADOS'), 'negrilla margenSuperior');
    $codigo .= HTML::parrafo($objeto->ubicacion, '', '');


    $codigo .= HTML::parrafo($textos->id('ESTADO'), 'negrilla margenSuperior');
    $activo = ($objeto->activo) ? HTML::frase($textos->id('ACTIVO'), 'activo') : HTML::frase($textos->id('INACTIVO'), 'inactivo');
    $codigo .= HTML::parrafo($activo, '', '');

    $respuesta['generar'] = true;
    $respuesta['codigo'] = $codigo;
    $respuesta['titulo'] = HTML::parrafo($textos->id('CONSULTAR_ITEM'), 'letraBlanca negrilla subtitulo');
    $respuesta['destino'] = '#cuadroDialogo';
    $respuesta['ancho'] = 550;
    $respuesta['alto'] = 400;


    Servidor::enviarJSON($respuesta);
}


/**
 *
 * @global type $textos
 * @global type $sql
 * @global type $archivo_imagen
 * @param type $datos 
 */
function adicionarItem($datos = array()) {
    global $textos, $sql;


    $objeto = new Gondola();
    $destino = '/ajax' . $objeto->urlBase . '/add';
    $respuesta = array();

    if (empty($datos)) {

        $arregloLados = array('1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6');
        $arregloBandejas = array('1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7', '8' => '8', '9' => '9', '10' => '10', '11' => '11', '12' => '12', '13' => '13', '14' => '14', '15' => '15', '16' => '16', '17' => '17', '18' => '18', '19' => '19', '20' => '20');

        $listaLados = HTML::listaDesplegable('datos[lados]', $arregloLados, '', '', 'listaLados', '');
        $listaBandejas = HTML::listaDesplegable('datos[bandejas]', $arregloBandejas, '', '', 'listaBandejas', '');
        //Se agrega el campo oculto datos[dialogo] que va a ser el que almacene el id de la ventana de dialogo donde sera mostrado este formulario
        $codigo = HTML::campoOculto('procesar', 'true');
        $codigo .= HTML::campoOculto('datos[dialogo]', '', 'idDialogo');
        $codigo .= HTML::parrafo($textos->id('BODEGA'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[id_bodega]', 40, 255, '', 'autocompletable campoObligatorio', '', array('title' => HTML::urlInterna('BODEGAS', 0, true, 'listar')), $textos->id('AYUDA_USO_AUTOCOMPLETAR'), HTML::urlInterna('BODEGAS', 0, true, 'add'));
        $codigo .= HTML::parrafo($textos->id('NOMBRE'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[nombre]', 40, 255, '', 'campoObligatorio');
        $codigo .= HTML::parrafo($textos->id('LADOS'), 'negrilla margenSuperior');
        $codigo .= $listaLados;
        $codigo .= HTML::parrafo($textos->id('BANDEJAS'), 'negrilla margenSuperior');
        $codigo .= $listaBandejas;

        $codigo .= HTML::parrafo(HTML::campoChequeo('datos[activo]', true) . $textos->id('ACTIVO'), 'margenSuperior');
        $codigo .= HTML::parrafo(HTML::boton('chequeo', $textos->id('ACEPTAR'), '', '', ''), 'margenSuperior');
        $codigo .= HTML::parrafo($textos->id('REGISTRO_AGREGADO'), 'textoExitoso', 'textoExitoso');
        $codigo1 = HTML::forma($destino, $codigo, 'P', true);

        $respuesta['generar'] = true;
        $respuesta['codigo'] = $codigo1;
        $respuesta['titulo'] = HTML::parrafo($textos->id('ADICIONAR_ITEM'), 'letraBlanca negrilla subtitulo');
        $respuesta['destino'] = '#cuadroDialogo';
        $respuesta['ancho'] = 550;
        $respuesta['alto'] = 400;
    } else {

        $respuesta['error'] = true;

        $datos['id_bodega'] = explode('|', $datos['id_bodega']);
        $datos['id_bodega'] = trim($datos['id_bodega'][1]);

        $existeNombre = $sql->existeItem('gondolas', 'nombre', $datos['nombre']);
        $existeBodega = $sql->existeItem('bodegas', 'nombre', $datos['id_bodega']);

        if (empty($datos['lados'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_LADOS');
        } elseif (empty($datos['id_bodega'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_BODEGA');
        } elseif (!$existeBodega) {
            $respuesta['mensaje'] = $textos->id('ERROR_EXISTENCIA_BODEGA');
        } elseif ($existeNombre) {
            $respuesta['mensaje'] = $textos->id('ERROR_EXISTE_NOMBRE');
        } elseif (empty($datos['nombre'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_NOMBRE');
        } else {
            $idItem = $objeto->adicionar($datos);
            if ($idItem) {
                /*                 * ************** Creo el nuevo item que se insertara via ajax *************** */
                $objeto = new Gondola($idItem);

                $estado =  ($objeto->activo) ? HTML::frase($textos->id('ACTIVO'), 'activo') : HTML::frase($textos->id('INACTIVO'), 'inactivo');
                
                $celdas = array($objeto->sede, $objeto->bodega, $objeto->nombre, $objeto->lados, $objeto->bandejas, $estado);
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
 * Metodo Funcion
 * @global type $textos
 * @global type $sql
 * @param type $id
 * @param type $datos 
 */
function modificarItem($id, $datos = array()) {
    global $textos, $sql;

    $objeto = new Gondola($id);
    $destino = '/ajax' . $objeto->urlBase . '/edit';
    $respuesta = array();

    if (empty($datos)) {

        $arregloLados = array('1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6');
        $arregloBandejas = array('1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7', '8' => '8', '9' => '9', '10' => '10', '11' => '11', '12' => '12', '13' => '13', '14' => '14', '15' => '15', '16' => '16', '17' => '17', '18' => '18', '19' => '19', '20' => '20');

        $listaLados = HTML::listaDesplegable('datos[lados]', $arregloLados, $objeto->lados, '', 'listaLados', '');
        $listaBandejas = HTML::listaDesplegable('datos[bandejas]', $arregloBandejas, $objeto->bandejas, '', 'listaBandejas', '');

        $codigo = HTML::campoOculto('procesar', 'true');
        $codigo .= HTML::campoOculto('id', $id);
        $codigo .= HTML::campoOculto('datos[dialogo]', '', 'idDialogo');
        $codigo .= HTML::parrafo($textos->id('BODEGA'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[id_bodega]', 40, 255, $objeto->sede . ' | ' . $objeto->bodega, 'autocompletable campoObligatorio', '', array('title' => HTML::urlInterna('BODEGAS', 0, true, 'listar')), $textos->id('AYUDA_USO_AUTOCOMPLETAR'), HTML::urlInterna('BODEGAS', 0, true, 'add'));
        $codigo .= HTML::parrafo($textos->id('NOMBRE'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[nombre]', 40, 255, $objeto->nombre, 'campoObligatorio');
        $codigo .= HTML::parrafo($textos->id('LADOS'), 'negrilla margenSuperior');
        $codigo .= $listaLados;
        $codigo .= HTML::parrafo($textos->id('BANDEJAS'), 'negrilla margenSuperior');
        $codigo .= $listaBandejas;
        $codigo .= HTML::parrafo(HTML::campoChequeo('datos[activo]', $objeto->activo) . $textos->id('ACTIVO'), 'margenSuperior');
        $codigo .= HTML::parrafo(HTML::boton('chequeo', $textos->id('ACEPTAR'), 'botonOk', 'botonOk', 'botonOk'), 'margenSuperior');
        $codigo .= HTML::parrafo($textos->id('REGISTRO_MODIFICADO'), 'textoExitoso', 'textoExitoso');
        $codigo1 = HTML::forma($destino, $codigo, 'P');

        $respuesta['generar'] = true;
        $respuesta['codigo'] = $codigo1;
        $respuesta['titulo'] = HTML::parrafo($textos->id('MODIFICAR_ITEM'), 'letraBlanca negrilla subtitulo');
        $respuesta['destino'] = '#cuadroDialogo';
        $respuesta['ancho'] = 550;
        $respuesta['alto'] = 400;
    } else {

        $respuesta['error'] = true;

        $datos['id_bodega'] = explode('|', $datos['id_bodega']);
        $datos['id_bodega'] = trim($datos['id_bodega'][1]);

        $existeNombre = $sql->existeItem('gondolas', 'nombre', $datos['nombre'], 'id != "'.$id.'"');
        $existeBodega = $sql->existeItem('bodegas', 'nombre', $datos['id_bodega']);

        if (empty($datos['lados'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_LADOS');
        } elseif (empty($datos['id_bodega'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_BODEGA');
        } elseif (!$existeBodega) {
            $respuesta['mensaje'] = $textos->id('ERROR_EXISTENCIA_BODEGA');
        } elseif ($existeNombre) {
            $respuesta['mensaje'] = $textos->id('ERROR_EXISTE_NOMBRE');
        } elseif (empty($datos['nombre'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_NOMBRE');
        } else {
            $idItem = $objeto->modificar($datos);
            if ($idItem) {
                /*                 * ************** Creo el nuevo item que se insertara via ajax *************** */
                $objeto = new Gondola($id);

                $estado =  ($objeto->activo) ? HTML::frase($textos->id('ACTIVO'), 'activo') : HTML::frase($textos->id('INACTIVO'), 'inactivo');
                 
                $celdas = array($objeto->sede, $objeto->bodega, $objeto->nombre, $objeto->lados, $objeto->bandejas, $estado);
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
    global $textos;

    $objeto = new Gondola($id);
    $destino = '/ajax' . $objeto->urlBase . '/delete';
    $respuesta = array();

    if (!$confirmado) {
        $titulo = HTML::frase($objeto->nombre . ' ' . $textos->id('DE') . ' ' . $objeto->bodega, 'negrilla');
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
        $objeto = new Gondola();
        $registros = $configuracion['GENERAL']['registrosPorPagina'];
        if (!empty($cantidadRegistros)) {
            $registros = (int) $cantidadRegistros;
        }
        $pagina = 1;
        $registroInicial = 0;


        $palabras = explode(' ', $datos);

        $condicionales = $data[1];

        if ($condicionales == '') {
            $condicion = '(g.nombre REGEXP "(' . implode('|', $palabras) . ')")';
        } else {
            //$condicion = str_replace(']', ''', $data[1]);
            $condicionales = explode('|', $condicionales);

            $condicion = '(';
            $tam = sizeof($condicionales) - 1;
            for ($i = 0; $i < $tam; $i++) {
                $condicion .= $condicionales[$i] . ' REGEXP "(' . implode('|', $palabras) . ')" ';
                if ($i != $tam - 1) {
                    $condicion .= ' OR ';
                }
            }
            $condicion .= ')';
        }

        $arregloItems = $objeto->listar($registroInicial, $registros, array('0', '999'), $condicion, 'g.nombre');

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
    $objeto = new Gondola();

    $registros = $configuracion['GENERAL']['registrosPorPagina'];

    if (!empty($cantidadRegistros)) {
        $registros = (int) $cantidadRegistros;
    }

    if (isset($pagina)) {
        $pagina = $pagina;
    } else {
        $pagina = 1;
    }

    if (isset($consultaGlobal) && $consultaGlobal != '') {

        $data = explode('[', $consultaGlobal);
        $datos = $data[0];
        $palabras = explode(' ', $datos);

        if ($data[1] != '') {
            $condicionales = explode('|', $data[1]);

            $condicion = '(';
            $tam = sizeof($condicionales) - 1;
            for ($i = 0; $i < $tam; $i++) {
                $condicion .= $condicionales[$i] . ' REGEXP "(' . implode('|', $palabras) . ')" ';
                if ($i != $tam - 1) {
                    $condicion .= ' OR ';
                }
            }
            $condicion .= ')';

            $consultaGlobal = $condicion;
        } else {
            $consultaGlobal = '(g.nombre REGEXP "(' . implode('|', $palabras) . ')")';
        }
    } else {
        $consultaGlobal = '';
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


    $arregloItems = $objeto->listar($registroInicial, $registros, array('0', '999'), $consultaGlobal, $nombreOrden);

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
    $consulta = $sql->seleccionar(array('gondolas'), array('nombre'), '(nombre LIKE "%'.$cadena.'%" ||  codigo LIKE "%'.$cadena.'%") AND activo = "1" AND id != 0', '', 'nombre ASC', 0, 20);

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
    global $textos;


    $destino = '/ajax/gondolas/eliminarVarios';
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
        $arregloIds = explode(',', $cadenaIds);

        $eliminarVarios = true;
        foreach ($arregloIds as $val) {
            $objeto = new Gondola($val);
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