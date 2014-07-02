<?php

/**
 * @package     FOM (Framework OpenSource Multiplatform)
 * @subpackage  Catalogos
 * @author      Pablo Andres Velez Vidal <pavelez@genesyscorp.com.co>
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2013 GENESYS
 * @version     0.1
 * 
 * Framework basado en FOLCS, desarrollado por Francisco Lozano de FELINUX ltda. Cali - Colombia
 * */
global $url_accion, $forma_procesar, $forma_id, $forma_datos, $forma_cantidadRegistros, $forma_pagina, $forma_orden, $forma_nombreOrden, $url_cadena, $forma_dialogo, $forma_consultaGlobal, $forma_patron, $forma_item, $forma_cantidad, $forma_cadenaItems;


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
        case 'listarCatalogosMoto' : listarCatalogosMoto($url_cadena);
            break;
        case 'listarCatalogosLinea' : listarCatalogosLinea($url_cadena);
            break;
        case 'buscarCatalogos' : buscarCatalogos();
            break;

        case 'mostrarTablaCatalogos' : mostrarTablaCatalogos($forma_patron, $forma_item);
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

    $objeto = new Catalogo($id);
    $respuesta = array();

    $codigo = HTML::campoOculto('procesar', 'true');

    $codigo .= HTML::parrafo($textos->id('NOMBRE'), 'negrilla margenSuperior');
    $codigo .= HTML::parrafo($objeto->nombre, '', '');
    $codigo .= HTML::parrafo($textos->id('LINEA'), 'negrilla margenSuperior');
    $codigo .= HTML::parrafo($objeto->moto->linea, '', '');
    $codigo .= HTML::parrafo($textos->id('MOTO'), 'negrilla margenSuperior');
    $codigo .= HTML::parrafo($objeto->moto->nombre, '', '');
    $codigo .= HTML::parrafo($textos->id('ARCHIVO'), 'negrilla margenSuperior');
    $codigo .= HTML::parrafo($objeto->enlaceArchivo, 'margenSuperior subtitulo');


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
    global $textos, $sql, $archivo_archivo;


    $objeto = new Catalogo();
    $destino = '/ajax' . $objeto->urlBase . '/add';
    $respuesta = array();

    if (empty($datos)) {
        //Se agrega el campo oculto datos[dialogo] que va a ser el que almacene el id de la ventana de dialogo donde sera mostrado este formulario
        $codigo = HTML::campoOculto('procesar', 'true');
        $codigo .= HTML::campoOculto('datos[dialogo]', '', 'idDialogo');
        $codigo .= HTML::parrafo($textos->id('MOTO'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[moto]', 40, 255, '', 'autocompletable campoObligatorio', '', array('title' => HTML::urlInterna('MOTOS', 0, true, 'listar')), $textos->id('AYUDA_USO_AUTOCOMPLETAR'), HTML::urlInterna('MOTOS', 0, true, 'add'), 'datos[id_moto]');
        $codigo .= HTML::parrafo($textos->id('NOMBRE'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[nombre]', 40, 255, '', 'campoObligatorio');
        $codigo .= HTML::parrafo($textos->id('ARCHIVO'), 'negrilla margenSuperior');
        $codigo .= HTML::campoArchivo('archivo', 50, 255);
        $codigo .= HTML::parrafo(HTML::campoChequeo('datos[activo]', true) . $textos->id('ACTIVO'), 'margenSuperior');
        $codigo .= HTML::parrafo(HTML::boton('chequeo', $textos->id('ACEPTAR'), '', '', ''), 'margenSuperior');
        $codigo .= HTML::parrafo($textos->id('REGISTRO_AGREGADO'), 'textoExitoso', 'textoExitoso');
        $codigo1 = HTML::forma($destino, $codigo, 'P', false);

        $respuesta['generar'] = true;
        $respuesta['codigo'] = $codigo1;
        $respuesta['titulo'] = HTML::parrafo($textos->id('ADICIONAR_ITEM'), 'letraBlanca negrilla subtitulo');
        $respuesta['destino'] = '#cuadroDialogo';
        $respuesta['ancho'] = 550;
        $respuesta['alto'] = 400;
    } else {

        $respuesta['error'] = true;

        $existeMoto = $sql->existeItem('motos', 'nombre', $datos['moto']);
        if (!empty($archivo_archivo['tmp_name'])) {
            $validarFormato = Archivo::validarArchivo($archivo_archivo, array("doc", "docx", "pdf", "ppt", "pptx", "pps", "ppsx", "xls", "xlsx", "odt", "rtf", "txt", "ods", "odp", "jpg", "jpeg", "png"));
//           
        }
        if (empty($datos['moto'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_MOTO');
        } elseif (!empty($datos['moto']) && !$existeMoto) {
            $respuesta['mensaje'] = $textos->id('ERROR_MOTO');
        } elseif (empty($datos['nombre'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_NOMBRE');
        } elseif ($validarFormato) {
            $respuesta['mensaje'] = $textos->id('ERROR_FORMATO_ARCHIVO');
        } else {
            $idItem = $objeto->adicionar($datos);
            if ($idItem) {
                /*                 * ************** Creo el nuevo item que se insertara via ajax *************** */
                $objeto = new Catalogo($idItem);

                $estado = ($objeto->activo) ? HTML::frase($textos->id('ACTIVO'), 'activo') : HTML::frase($textos->id('INACTIVO'), 'inactivo');

                $celdas = array($objeto->id, $objeto->moto->linea, $objeto->moto->nombre, $objeto->enlaceArchivo, $estado);
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
 * 
 * @global type $textos
 * @global type $sql
 * @param type $id
 * @param type $datos 
 */
function modificarItem($id, $datos = array()) {
    global $textos, $sql, $archivo_archivo;

    $objeto = new Catalogo($id);
    $destino = '/ajax' . $objeto->urlBase . '/edit';
    $respuesta = array();

    if (empty($datos)) {

        $codigo = HTML::campoOculto('procesar', 'true');
        $codigo .= HTML::campoOculto('id', $id);
        $codigo .= HTML::campoOculto('datos[dialogo]', '', 'idDialogo');
        $codigo .= HTML::parrafo($textos->id('MOTO'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[moto]', 40, 255, $objeto->moto->nombre, 'autocompletable campoObligatorio', '', array('title' => HTML::urlInterna('MOTOS', 0, true, 'listar')), $textos->id('AYUDA_USO_AUTOCOMPLETAR'), HTML::urlInterna('MOTOS', 0, true, 'add'), 'datos[id_moto]', $objeto->idMoto);
        $codigo .= HTML::parrafo($textos->id('NOMBRE'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[nombre]', 40, 255, $objeto->nombre, 'campoObligatorio');
        $codigo .= HTML::parrafo($textos->id('ARCHIVO'), 'negrilla margenSuperior');
        $codigo .= HTML::campoArchivo('archivo', 50, 255) . HTML::parrafo($objeto->enlaceArchivo, 'estiloEnlace margenSuperior', '');
        $codigo .= HTML::parrafo(HTML::campoChequeo('datos[activo]', $objeto->activo) . $textos->id('ACTIVO'), 'margenSuperior');
        $codigo .= HTML::parrafo(HTML::boton('chequeo', $textos->id('ACEPTAR'), 'botonOk', 'botonOk', 'botonOk'), 'margenSuperior');
        $codigo .= HTML::parrafo($textos->id('REGISTRO_MODIFICADO'), 'textoExitoso', 'textoExitoso');
        $codigo1 = HTML::forma($destino, $codigo, 'P', false);

        $respuesta['generar'] = true;
        $respuesta['codigo'] = $codigo1;
        $respuesta['titulo'] = HTML::parrafo($textos->id('MODIFICAR_ITEM'), 'letraBlanca negrilla subtitulo');
        $respuesta['destino'] = '#cuadroDialogo';
        $respuesta['ancho'] = 550;
        $respuesta['alto'] = 400;
    } else {

        $respuesta['error'] = true;

        $existeNombre = $sql->existeItem('catalogos', 'nombre', $datos['nombre'], 'id != "' . $id . '"');
        $existeMoto = $sql->existeItem('motos', 'nombre', $datos['moto']);
        if (!empty($archivo_archivo['tmp_name'])) {
            $validarFormato = Archivo::validarArchivo($archivo_archivo, array('doc', 'docx', 'pdf', 'ppt', 'pptx', 'pps', 'ppsx', 'xls', 'xlsx', 'odt', 'rtf', 'txt', 'ods', 'odp', 'jpg', 'jpeg', 'png'));
//           
        }
        if (empty($datos['moto'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_MOTO');
        } elseif (!empty($datos['moto']) && !$existeMoto) {
            $respuesta['mensaje'] = $textos->id('ERROR_MOTO');
        } elseif (empty($datos['nombre'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_NOMBRE');
        } elseif ($validarFormato) {
            $respuesta['mensaje'] = $textos->id('ERROR_FORMATO_ARCHIVO');
        } elseif ($existeNombre) {
            $respuesta['mensaje'] = $textos->id('ERROR_CATALOGO_EXISTENTE');
        } else {

            $idItem = $objeto->modificar($datos);
            if ($idItem) {
                /*                 * ************** Creo el nuevo item que se insertara via ajax *************** */
                $objeto = new Catalogo($id);

                $estado = ($objeto->activo) ? HTML::frase($textos->id('ACTIVO'), 'activo') : HTML::frase($textos->id('INACTIVO'), 'inactivo');

                $celdas = array($objeto->id, $objeto->moto->linea, $objeto->moto->nombre, $objeto->enlaceArchivo, $estado);
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

    $objeto = new Catalogo($id);
    $destino = '/ajax' . $objeto->urlBase . '/delete';
    $respuesta = array();

    if (!$confirmado) {
        $titulo = HTML::frase($objeto->nombre, 'negrilla');
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
        $objeto = new Catalogo();
        $registros = $configuracion['GENERAL']['registrosPorPagina'];
        if (!empty($cantidadRegistros)) {
            $registros = (int) $cantidadRegistros;
        }
        $pagina = 1;
        $registroInicial = 0;


        $palabras = explode(' ', $datos);

        $condicionales = $data[1];

        if ($condicionales == '') {
            $condicion = '(c.nombre REGEXP "(' . implode('|', $palabras) . ')")';
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

        $arregloItems = $objeto->listar($registroInicial, $registros, array('0', '999'), $condicion, 'c.nombre');

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

/*
 * Funcion que se encarga de recargar la tabla de datos paginando
 */

function paginador($pagina, $orden = NULL, $nombreOrden = NULL, $consultaGlobal = NULL, $cantidadRegistros = NULL) {
    global $configuracion;

    $item = '';
    $respuesta = array();
    $objeto = new Catalogo();

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
            $consultaGlobal = '(c.nombre REGEXP "(' . implode('|', $palabras) . ')")';
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

    $consulta = $sql->seleccionar(array('catalogos'), array('id', 'nombre'), 'nombre LIKE "%' . $cadena . '%"  AND activo = "1"', '', 'nombre ASC', 0, 20);

    while ($fila = $sql->filaEnObjeto($consulta)) {
        $respuesta1 = array();
        $respuesta1['label'] = $fila->nombre;
        $respuesta1['value'] = $fila->id;
        $respuesta[] = $respuesta1;
    }

    Servidor::enviarJSON($respuesta);
}

/**
 *
 * @global type $textos
 * @global type $sql
 * @global type $archivo_imagen
 * @param type $datos 
 */
function buscarCatalogos() {
    global $textos, $configuracion;

    $respuesta = array();
    $codigo .= HTML::campoOculto('datos[dialogo]', '', 'idDialogo');

    $codigo .= HTML::frase($textos->id('MOTO'), ' estiloEnlace margenSuperiorDoble margenIzquierda', 'textoBuscarMoto', array('ayuda' => $textos->id('AYUDA_BUSCAR_MOTO')));
    $codigo .= HTML::frase($textos->id('LINEA'), ' estiloEnlace margenSuperiorDoble margenIzquierdaTriple', 'textoBuscarLinea', array('ayuda' => $textos->id('AYUDA_BUSCAR_LINEA')));


    $codigo2 .= HTML::parrafo($textos->id('MOTO'), 'negrilla margenSuperior', 'textoBusquedaMarca');
    $codigo2 .= HTML::campoTexto('datos[moto]', 40, 100, '', 'autocompletable campoObligatorio campoTextoBusquedaCatalogos', 'campoTextoBusquedaMotos', array('title' => '/ajax/catalogos/listarCatalogosMoto'), $textos->id('AYUDA_USO_AUTOCOMPLETAR'), HTML::urlInterna('CATALOGOS', 0, true, 'add'));

    $codigo .= HTML::contenedor($codigo2, '', 'contenedorBusquedaMoto');

    $codigo1 = HTML::parrafo($textos->id('LINEA'), 'negrilla margenSuperior');
    $codigo1 .= HTML::campoTexto('datos[linea]', 40, 100, '', 'autocompletable campoObligatorio campoTextoBusquedaCatalogos', 'campoTextoBusquedaLineas', array('title' => '/ajax/catalogos/listarCatalogosLinea'), $textos->id('AYUDA_USO_AUTOCOMPLETAR'), HTML::urlInterna('CATALOGOS', 0, true, 'add'));

    $codigo .= HTML::contenedor($codigo1, 'oculto', 'contenedorBusquedaLinea');


    $codigo .= HTML::contenedor('', 'contenedorListadoCatalogos margenSuperior', 'contenedorListadoCatalogos');


    $respuesta['generar'] = true;
    $respuesta['cargarJs'] = true;
    $respuesta['archivoJs'] = $configuracion['SERVIDOR']['media'] . $configuracion['RUTAS']['javascript'] . '/modulos/catalogos/funcionesBuscarCatalogo.js';
    $respuesta['codigo'] = $codigo;
    $respuesta['titulo'] = HTML::parrafo($textos->id('BUSCAR_CATALOGOS'), 'letraBlanca negrilla subtitulo');
    $respuesta['destino'] = '#cuadroDialogo';
    $respuesta['ancho'] = 498;
    $respuesta['alto'] = 383;


    Servidor::enviarJSON($respuesta);
}

/**
 *
 * @global type $textos
 * @global type $sql
 * @param type $datos 
 */
function mostrarTablaCatalogos($patron, $item) {
    global $textos;

    $objeto = new Catalogo();

    if ($patron == 'moto') {
        $condicion = 'm.id = "' . $item . '"';
        $orden = 'm.nombre';
    } else {
        $condicion = 'l.id = "' . $item . '"';
        $orden = 'l.nombre';
    }


    $excluidas = array(0);
    $respuesta = array();
    $objeto->listaAscendente = true;
    $listaCatalogos = $objeto->listar(0, 100, $excluidas, $condicion, $orden);

    if (!empty($objeto->registrosConsulta)) {
        //crear los formularios con la info para las demas sedes
        $datosTablaArticulosCompra = array(
            HTML::parrafo($textos->id('NOMBRE'), 'centrado') => 'enlaceArchivo',
        );

        $rutas = array();
        $idTabla = 'tablaListaCatalogos';
        $claseTabla = 'tablaListarItems';
        $estilosColumnas = array('', '', '', '', '');
        $contenedor = HTML::contenedor(Recursos::generarTablaListaArticulos($listaCatalogos, $datosTablaArticulosCompra, $rutas, $idTabla, $estilosColumnas, $claseTabla), 'flotanteIzquierda margenDerecha');
    } else {
        $contenedor = HTML::contenedor(HTML::parrafo($textos->id('BUSQUEDA_SIN_RESULTADOS'), 'textoError'));
    }

    $codigo .= $contenedor;

    $respuesta['accion'] = 'insertar';
    $respuesta['contenido'] = $codigo;
    $respuesta['destino'] = '#contenedorListadoCatalogos';

    Servidor::enviarJSON($respuesta);
}

/**
 * Funcion que devuelve la respuesta para el autocompletar
 * @global type $sql
 * @param type $cadena 
 */
function listarCatalogosMoto($cadena) {
    global $sql;
    $respuesta = array();

    $tablas = array(
        'c' => 'catalogos',
        'm' => 'motos'
    );
    $columnas = array(
        'id' => 'c.id',
        'idMoto' => 'm.id',
        'nombre' => 'm.nombre'
    );
    $condicion = 'c.id_moto = m.id AND m.nombre LIKE "%' . $cadena . '%"  AND m.activo = "1"';

    $consulta = $sql->seleccionar($tablas, $columnas, $condicion, '', 'm.nombre ASC', 0, 20);

    while ($fila = $sql->filaEnObjeto($consulta)) {
        $respuesta1 = array();
        $respuesta1['label'] = $fila->nombre;
        $respuesta1['value'] = $fila->idMoto;
        $respuesta1['patron'] = 'moto';
        $respuesta[] = $respuesta1;
    }

    Servidor::enviarJSON($respuesta);
}

/**
 * Funcion que devuelve la respuesta para el autocompletar
 * @global type $sql
 * @param type $cadena 
 */
function listarCatalogosLinea($cadena) {
    global $sql;
    $respuesta = array();

    $tablas = array(
        'c' => 'catalogos',
        'm' => 'motos',
        'l' => 'lineas'
    );
    $columnas = array(
        'id' => 'c.id',
        'idLinea' => 'l.id',
        'nombre' => 'l.nombre'
    );
    $condicion = 'c.id_moto = m.id AND m.id_linea = l.id AND l.nombre LIKE "%' . $cadena . '%"  AND m.activo = "1"';

    $consulta = $sql->seleccionar($tablas, $columnas, $condicion, 'l.id', 'm.nombre ASC', 0, 20);

    while ($fila = $sql->filaEnObjeto($consulta)) {
        $respuesta1 = array();
        $respuesta1['label'] = $fila->nombre;
        $respuesta1['value'] = $fila->idLinea;
        $respuesta[] = $respuesta1;
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


    $destino = '/ajax/catalogos/eliminarVarios';
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
            $objeto = new Catalogo($val);
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