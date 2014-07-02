<?php

/**
 * @package     FOM (Framework OpenSource Multiplatform)
 * @subpackage  Articulos
 * @author      Pablo Andres Velez Vidal <pavelez8@misena.edu.co>
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2012 GENESYS
 * @version     0.1
 * 
 * Framework basado en FOLCS, desarrollado por Francisco Lozano de FELINUX ltda. Cali - Colombia
 * */

if (isset($url_accion)) {
    switch ($url_accion) {
        case 'see'      :   consultarItem($forma_id, $forma_pestana);
            break;
        
        case 'search'   :   buscarItem($forma_datos, $forma_cantidadRegistros);
                            break;
        
        case 'move'     :   paginador($forma_pagina, $forma_orden, $forma_nombreOrden, $forma_consultaGlobal, $forma_cantidadRegistros);
                            break;
        
        case 'listar' :     listarItems($url_cadena);
                            break;
                        
    }
}

/**
 * Funcion que muestra la ventana modal de consultar un articulo
 * 
 * @global type $textos
 * @param type $id = id del banco a consultar 
 */
function consultarItem($id) {
    global $textos, $sql, $sesion_configuracionGlobal;

    if (!isset($id) || (isset($id) && !$sql->existeItem('cambios_inventarios', 'id', $id))) {
        $respuesta              = array();
        $respuesta['error']     = true;
        $respuesta['mensaje']   = $textos->id('NO_HA_SELECCIONADO_ITEM');

        Servidor::enviarJSON($respuesta);
        return NULL;
    }

    //Aqui debo de poner un registro de los ultimos 5 precios a los que  he comprado, a que proveedor y en que fecha
    //hacer un promedio de cuantos vendí al mes, y hacer un estimado de cuantos debo comprar

    $objeto         = new CambioInventario($id);
    $objeto         = $objeto->cargarRegistroInventario($id);
    
    $respuesta      = array();
    if ($objeto->aumento != 0) {
        $cantidad   = $objeto->aumento;
        $texto      = $textos->id('AUMENTO');
    } else {
        $cantidad   = $objeto->desconto;
        $texto      = $textos->id('DESCONTO');
    }
    
    $codigo .= HTML::parrafo($textos->id('USUARIO'), 'negrilla margenSuperior');
    $codigo .= HTML::parrafo($objeto->usuario);
    $codigo .= HTML::parrafo($textos->id('ARTICULO'), 'negrilla margenSuperior');
    $codigo .= HTML::parrafo($objeto->articulo);
    $codigo .= HTML::parrafo($textos->id('BODEGA'), 'negrilla margenSuperior');
    $codigo .= HTML::parrafo($objeto->bodega);
    $codigo .= HTML::parrafo($textos->id('FECHA_HORA'), 'negrilla margenSuperior');
    $codigo .= HTML::parrafo($objeto->fecha);
    $codigo .= HTML::parrafo($textos->id('IP'), 'negrilla margenSuperior');
    $codigo .= HTML::parrafo($objeto->ipEquipo);
    $codigo .= HTML::parrafo($texto, 'negrilla margenSuperior');
    $codigo .= HTML::parrafo($cantidad);
    
    $respuesta['generar']   = true;
    $respuesta['codigo']    = $codigo;
    $respuesta['titulo']    = HTML::parrafo($textos->id('CONSULTAR_ITEM'), 'letraBlanca negrilla subtitulo');
    $respuesta['destino']   = '#cuadroDialogo';
    $respuesta['ancho']     = 400;
    $respuesta['alto']      = 400;



    Servidor::enviarJSON($respuesta);
}

/**
 * Función que se encarga de realizar una busqueda de acuerdo a una condicion que se
 * le pasa. Es llamada cuando se ingresa un texto en el campo de busqueda en la pantalla principal del modulo.
 * Una vez es llamada esta función, se encarga de recargar la tabla de registros con los datos coincidientes 
 * en el patrón de busqueda.
 *
 * @global objeto $textos             = objeto global que gestiona los textos a traducir
 * @global arreglo $configuracion      = arreglo global de configuracion
 * @param arreglo $data                = arreglo con los parametros de busqueda
 * @param int $cantidadRegistros   = cantidad de registros aincluir por busqueda
 */
function buscarItem($data, $cantidadRegistros = NULL) {
    global $textos, $configuracion;

    $data   = explode('[', $data);
    $datos  = $data[0];

    if (empty($datos)) {
        $respuesta['error']     = true;
        $respuesta['mensaje']   = $textos->id('ERROR_FALTA_CADENA_BUSQUEDA');
        
    } else if (!empty($datos) && strlen($datos) < 2) {
        $respuesta['error']     = true;
        $respuesta['mensaje']   = str_replace('%1', '2', $textos->id('ERROR_TAMAÑO_CADENA_BUSQUEDA'));
        
    } else {
        $item               = '';
        $respuesta          = array();
        $objeto             = new CambioInventario();
        $registros          = $configuracion['GENERAL']['registrosPorPagina'];
        
        if (!empty($cantidadRegistros)) {
            $registros = (int) $cantidadRegistros;
        }
        
        $pagina             = 1;
        $registroInicial    = 0;

        $palabras = explode(' ', $datos);

        $condicionales = $data[1];

        if ($condicionales == '') {
            $condicion = '(a.nombre REGEXP "(' . implode('|', $palabras) . ')")';
            
        } else {
            $condicionales = explode('|', $condicionales);

            $condicion      = '(';
            $tam            = sizeof($condicionales) - 1;
            
            for ($i = 0; $i < $tam; $i++) {
                $condicion .= $condicionales[$i] . ' REGEXP "(' . implode('|', $palabras) . ')" ';
                if ($i != $tam - 1) {
                    $condicion .= ' OR ';
                    
                }
                
            }
            
            $condicion .= ')';
            
        }

        $arregloItems = $objeto->listar($registroInicial, $registros, array('0'), $condicion, 'a.nombre');

        if ($objeto->registrosConsulta) {//si la consulta trajo registros
            $datosPaginacion = array($objeto->registrosConsulta, $registroInicial, $registros, $pagina, $objeto->registrosConsulta);
            $item .= $objeto->generarTabla($arregloItems, $datosPaginacion);
            $info = HTML::parrafo(str_replace('%1', $objeto->registrosConsulta, $textos->id('RESULTADOS_BUSQUEDA')), 'textoExitosoNotificaciones');
            
        } else {
            $datosPaginacion = 0;
            $item .= $objeto->generarTabla($textos->id('NO_HAY_REGISTROS'), $datosPaginacion);
            $info = HTML::parrafo($textos->id('BUSQUEDA_SIN_RESULTADOS'), 'textoErrorNotificaciones');
            
        }

        $respuesta['error']             = false;
        $respuesta['accion']            = 'insertar';
        $respuesta['contenido']         = $item;
        $respuesta['idContenedor']      = '#tablaRegistros';
        $respuesta['idDestino']         = '#contenedorTablaRegistros';
        $respuesta['paginarTabla']      = true;
        $respuesta['info']              = $info;
    }

    Servidor::enviarJSON($respuesta);
    
}

/**
 * Funcion que se encarga de realizar la paginacion del listado de registros.
 * Una vez llamada recarga la tabla de registros con la info de acuerdo a los
 * parametros de paginacion, es decir de acuerdo a la pagina, al total de registros.
 * esto realiza una nueva consulta modificando los valores SQL (LIMIT X, Y)
 *
 * @global array $configuracion     = arreglo global de configuracion
 * @param int $pagina               = pagina en la cual inicia la paginacion
 * @param string $orden             = orden ascendente o descendente
 * @param string $nombreOrden       = nombre de la columna por la cual se va a ordenar
 * @param string $consultaGlobal    = la consulta que debe mantenerse (al realizar el filtro de registros) mientras se pagina
 * @param int $cantidadRegistros    = cantidad de registros a incluir en la paginacion
 */
function paginador($pagina, $orden = NULL, $nombreOrden = NULL, $consultaGlobal = NULL, $cantidadRegistros = NULL) {
    global $configuracion;

    $item           = '';
    $respuesta      = array();
    $objeto         = new CambioInventario();


    $registros = $configuracion['GENERAL']['registrosPorPagina'];

    if (!empty($cantidadRegistros)) {
        $registros = (int) $cantidadRegistros;
    }


    if (isset($pagina) && !empty($pagina)) {
        $pagina = $pagina;
    } else {
        $pagina = 1;
    }


    if (isset($consultaGlobal) && $consultaGlobal != '') {
        $data       = explode('[', $consultaGlobal);
        $datos      = $data[0];
        $palabras   = explode(' ', $datos);

        if ($data[1] != '') {
            $condicionales = explode('|', $data[1]);

            $condicion  = '(';
            $tam        = sizeof($condicionales) - 1;
            
            for ($i = 0; $i < $tam; $i++) {
                $condicion .= $condicionales[$i] . ' REGEXP "(' . implode('|', $palabras) . ')" ';
                
                if ($i != $tam - 1) {
                    $condicion .= ' OR ';
                }
                
            }
            $condicion .= ')';

            $consultaGlobal = $condicion;
            
        } else {
            $consultaGlobal = '(a.nombre REGEXP "(' . implode('|', $palabras) . ')")';
            
        }
        
    } else {
        $consultaGlobal = '';
    }

    if (!isset($nombreOrden)) {
        $nombreOrden = $objeto->ordenInicial;
    }


    if (isset($orden) && $orden == 'descendente') {//ordenamiento
        $objeto->listaAscendente = false;
        
    } else {
        $objeto->listaAscendente = true;
        
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

    $respuesta['error']                 = false;
    $respuesta['accion']                = 'insertar';
    $respuesta['contenido']             = $item;
    $respuesta['idContenedor']          = '#tablaRegistros';
    $respuesta['idDestino']             = '#contenedorTablaRegistros';
    $respuesta['paginarTabla']          = true;

    Servidor::enviarJSON($respuesta);
}

/**
 * Funcion que devuelve la respuesta para el autocompletar
 * 
 * @global recurso $sql  = objeto global de interacción con la BD
 * @param string $cadena = cadena recibida para buscar coincidencias en la BD y gnerar la respuesta
 */
function listarItems($cadena) {
    global $sql;
    $respuesta = array();

    $consulta = $sql->seleccionar(array('articulos'), array('id', 'nombre', 'precio1'), '(nombre LIKE "%' . $cadena . '%") AND activo = "1"', '', 'nombre ASC', 0, 20);

    while ($fila = $sql->filaEnObjeto($consulta)) {
        $respuesta1         = array();
        $respuesta1['id']   = $fila->id;

        if (strlen($fila->nombre) > 90) {
            $fila->nombre = substr($fila->nombre, 0, 88) . '..';
        }

        $respuesta1['label']    = $fila->nombre;
        $respuesta1['value']    = $fila->precio1;
        $respuesta[]            = $respuesta1;
        
    }

    Servidor::enviarJSON($respuesta);
}
