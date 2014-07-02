<?php

/**
 * @package     FOM (Framework OpenSource Multiplatform)
 * @subpackage  Eventos
 * @author      Pablo Andres Velez Vidal <pavelez@genesyscorp.com.co>
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2013 GENESYS
 * @version     0.1
 * 
 * Framework basado en FOLCS, desarrollado por Francisco Lozano de FELINUX ltda. Cali - Colombia
 * */

if (isset($url_accion)) {
    switch ($url_accion) {
        case 'add' :                $datos = ($forma_procesar) ? $forma_datos : array();
                                    adicionarItem($datos, $forma_fecha);
                                    break;
        
        case 'see' :                cosultarItem($forma_id);
                                    break;
        
        case 'edit' :               $datos = ($forma_procesar) ? $forma_datos : array();
                                    modificarItem($forma_id, $datos);
                                    break;
        
        case 'delete' :             $confirmado = ($forma_procesar) ? TRUE : FALSE;
                                    eliminarItem($forma_id, $confirmado, $forma_dialogo);
                                    break;
        
        case 'listar' :             listarItems($url_cadena);
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

    $objeto     = new Evento($id);
    $respuesta  = array();

    $codigo      = HTML::campoOculto('id', $id);
    $codigo     .= HTML::parrafo($textos->id('FECHA_INICIO'), 'negrilla margenSuperior');
    $codigo     .= HTML::parrafo($objeto->fechaInicio.' '.$objeto->horaInicio, '', '');
    
    if (!empty($objeto->fechaFin)) {
        $codigo .= HTML::parrafo($textos->id('FECHA_FIN'), 'negrilla margenSuperior');
        $codigo .= HTML::parrafo($objeto->fechaFin.' '.$objeto->horaFin, '', '');
    }

    $codigo .= HTML::parrafo($textos->id('TITULO'), 'negrilla margenSuperior');
    $codigo .= HTML::parrafo($objeto->titulo, '', '');
    $codigo .= HTML::parrafo($textos->id('DESCRIPCION'), 'negrilla margenSuperior');
    $codigo .= HTML::parrafo($objeto->descripcion, '', '');

    $codigo .= HTML::parrafo($textos->id('ESTADO'), 'negrilla margenSuperior');
    $activo = ($objeto->activo) ? HTML::frase($textos->id('ACTIVO'), 'activo') : HTML::frase($textos->id('INACTIVO'), 'inactivo');
    $codigo .= HTML::parrafo($activo, '', '');
    
    
    $destino1 = '/ajax/eventos/edit';
    $codigo2 = HTML::campoOculto('id', $id, 'ocultoIdEvento');
    $codigo2 .= HTML::boton('lapiz', $textos->id('MODIFICAR_ITEM'), 'botonOk margenSuperior', 'botonOk', 'botonOk');
    $codigo .= HTML::contenedor(HTML::forma($destino1, $codigo2, 'P', '', ''), 'botonEditarEvento');    
    
    
    $destino1 = '/ajax/eventos/delete';
    $codigo2 = HTML::campoOculto('id', $id, 'ocultoIdEvento');
    $codigo2 .= HTML::boton('lapiz', $textos->id('ELIMINAR_ITEM'), 'botonOk margenSuperior', 'botonOk', 'botonOk');
    $codigo .= HTML::contenedor(HTML::forma($destino1, $codigo2, 'P', '', ''), 'botonEliminarEvento');      

    $respuesta['generar']       = TRUE;
    $respuesta['codigo']        = $codigo;
    $respuesta['titulo']        = HTML::parrafo($textos->id('CONSULTAR_ITEM'), 'letraBlanca negrilla subtitulo');
    $respuesta['destino']       = '#cuadroDialogo';
    $respuesta['ancho']         = 500;
    $respuesta['alto']          = 500;


    Servidor::enviarJSON($respuesta);
}

/**
 * 
 * @global type $textos
 * @global type $sql
 * @param type $datos
 */
function adicionarItem($datos = array(), $fecha = NULL) {
    global $textos;


    $objeto     = new Evento();
    $destino    = '/ajax' . $objeto->urlBase . '/add';
    $respuesta  = array();

    if (empty($datos)) {

        $codigo = HTML::campoOculto('procesar', 'TRUE');
        $codigo .= HTML::campoOculto('datos[dialogo]', '', 'idDialogo');
        $codigo .= HTML::parrafo($textos->id('TITULO'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[titulo]', 50, 50, '', '', 'tituloEvento', array(), $textos->id('INGRESE_TITULO_EVENTO'));         
        $codigo .= HTML::parrafo($textos->id('FECHA_INICIO'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto("datos[fecha_inicio]", 12, 12, $fecha, "fechaAntigua campoCalendario", "fechaInicio", array(), $textos->id("SELECCIONE_FECHA_INICIO"));
        $codigo .= HTML::parrafo($textos->id("HORA_INICIO"), "negrilla margenSuperior");
        $codigo .= HTML::campoTexto("datos[hora_inicio]", 10, 10, "", "selectorHora", "horaInicio", array(), $textos->id("SELECCIONE_HORA_INICIO"));
        $codigo .= HTML::parrafo($textos->id('FECHA_FIN'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto("datos[fecha_fin]", 12, 12, "", "fechaAntigua campoCalendario", "fechaFin", array(), $textos->id("SELECCIONE_FECHA_FIN"));
        $codigo .= HTML::parrafo($textos->id("HORA_FIN"), "negrilla margenSuperior");
        $codigo .= HTML::campoTexto("datos[hora_fin]", 10, 10, "", "selectorHora", "horaFin", array(), $textos->id("SELECCIONE_HORA_INICIO"));       
        $codigo .= HTML::parrafo($textos->id('DESCRIPCION'), 'negrilla margenSuperior');
        $codigo .= HTML::areaTexto('datos[descripcion]', 4, 50, '', '', 'tituloEvento', array(), $textos->id('INGRESE_DESCRIPCION_EVENTO'));         

        
        $codigo .= HTML::parrafo(HTML::campoChequeo('datos[activo]', TRUE) . $textos->id('ACTIVO'), 'margenSuperior');
        $codigo .= HTML::parrafo(HTML::boton('chequeo', $textos->id('ACEPTAR'), '', '', ''), 'margenSuperior');
        $codigo .= HTML::parrafo($textos->id('REGISTRO_AGREGADO'), 'textoExitoso', 'textoExitoso');
        $codigo1 = HTML::forma($destino, $codigo, 'P');


        $respuesta['generar']       = TRUE;
        $respuesta['titulo']        = HTML::parrafo($textos->id('ADICIONAR_ITEM'), 'letraBlanca negrilla subtitulo');
        $respuesta['destino']       = '#cuadroDialogo';
        $respuesta['ancho']         = 500;
        $respuesta['alto']          = 500;
        $respuesta['codigo']        = $codigo1;
        
    } else {

        $respuesta['error'] = TRUE;

        if (empty($datos['titulo'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_TITULO');
            
        } elseif (empty($datos['fecha_inicio'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_FECHA_INICIO');
            
        }  else {
            $idItem = $objeto->adicionar($datos);
            
            if ($idItem) {                
                $respuesta['error']         = FALSE;
                $respuesta['accion']        = 'recargar';
                $respuesta['mensaje']       = $textos->id('EVENTO_AGREGADO_EXITOSMENTE');
                $respuesta['textoExito']    = TRUE;

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
    global $textos;

    $objeto     = new Evento($id);
    $destino    = '/ajax' . $objeto->urlBase . '/edit';
    $respuesta  = array();

    if (empty($datos)) {
        $codigo  = HTML::campoOculto('procesar', 'TRUE');
        $codigo .= HTML::campoOculto('id', $id);
        $codigo .= HTML::campoOculto('datos[dialogo]', '', 'idDialogo');
        $codigo .= HTML::parrafo($textos->id('TITULO'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[titulo]', 50, 50, $objeto->titulo, '', 'tituloEvento', array(), $textos->id('INGRESE_TITULO_EVENTO'));         
        $codigo .= HTML::parrafo($textos->id('FECHA_INICIO'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto("datos[fecha_inicio]", 12, 12, $objeto->fechaInicio, "fechaAntigua campoCalendario", "fechaInicio", array(), $textos->id("SELECCIONE_FECHA_INICIO"));
        $codigo .= HTML::parrafo($textos->id("HORA_INICIO"), "negrilla margenSuperior");
        $codigo .= HTML::campoTexto("datos[hora_inicio]", 10, 10, $objeto->horaInicio, "selectorHora", "horaInicio", array(), $textos->id("SELECCIONE_HORA_INICIO"));
        $codigo .= HTML::parrafo($textos->id('FECHA_FIN'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto("datos[fecha_fin]", 12, 12, $objeto->fechaFin, "fechaAntigua campoCalendario", "fechaFin", array(), $textos->id("SELECCIONE_FECHA_FIN"));
        $codigo .= HTML::parrafo($textos->id("HORA_FIN"), "negrilla margenSuperior");
        $codigo .= HTML::campoTexto("datos[hora_fin]", 10, 10, $objeto->horaFin, "selectorHora", "horaFin", array(), $textos->id("SELECCIONE_HORA_INICIO"));       
        $codigo .= HTML::parrafo($textos->id('DESCRIPCION'), 'negrilla margenSuperior');
        $codigo .= HTML::areaTexto('datos[descripcion]', 3, 50, $objeto->descripcion, '', 'descripcionEvento', array(), $textos->id('INGRESE_DESCRIPCION_EVENTO'));         

        
        $codigo .= HTML::parrafo(HTML::campoChequeo('datos[activo]', TRUE) . $textos->id('ACTIVO'), 'margenSuperior');
        $codigo .= HTML::parrafo(HTML::boton('chequeo', $textos->id('ACEPTAR'), '', '', ''), 'margenSuperior');
        $codigo .= HTML::parrafo($textos->id('REGISTRO_AGREGADO'), 'textoExitoso', 'textoExitoso');
        $codigo1 = HTML::forma($destino, $codigo, 'P');

        $respuesta['generar'] = TRUE;
        $respuesta['codigo'] = $codigo1;
        $respuesta['titulo'] = HTML::parrafo($textos->id('MODIFICAR_ITEM'), 'letraBlanca negrilla subtitulo');
        $respuesta['destino'] = '#cuadroDialogo';
        $respuesta['ancho'] = 500;
        $respuesta['alto'] = 500;
        
    } else {
        $respuesta['error'] = TRUE;

        if (empty($datos['titulo'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_TITULO');
            
        } elseif (empty($datos['fecha_inicio'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_FECHA_INICIO');
            
        }  else {
            $idItem = $objeto->modificar($datos);
            
            if ($idItem) {                
                $respuesta['error']         = FALSE;
                $respuesta['accion']        = 'recargar';
                $respuesta['mensaje']       = $textos->id('EVENTO_MODIFICADO_EXITOSMENTE');
                $respuesta['textoExito']    = TRUE;                

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
function eliminarItem($id, $confirmado) {
    global $textos;

    $objeto     = new Evento($id);
    $destino    = '/ajax' . $objeto->urlBase . '/delete';
    $respuesta  = array();

    if (!$confirmado) {
        $titulo  = HTML::frase($objeto->titulo, 'negrilla');
        $titulo1 = str_replace('%1', $titulo, $textos->id('CONFIRMAR_ELIMINACION'));
        $codigo  = HTML::campoOculto('procesar', 'TRUE');
        $codigo .= HTML::campoOculto('id', $id);
        $codigo .= HTML::parrafo($titulo1);
        $codigo .= HTML::parrafo(HTML::boton('chequeo', $textos->id('ACEPTAR'), '', 'botonOk', 'botonOk'), 'margenSuperior');
        $codigo .= HTML::parrafo($textos->id('REGISTRO_ELIMINADO'), 'textoExitoso', 'textoExitoso');
        $codigo1 = HTML::forma($destino, $codigo);

        $respuesta['generar']   = TRUE;
        $respuesta['codigo']    = $codigo1;
        $respuesta['destino']   = '#cuadroDialogo';
        $respuesta['titulo']    = HTML::parrafo($textos->id('ELIMINAR_ITEM'), 'letraBlanca negrilla subtitulo');
        $respuesta['ancho']     = 350;
        $respuesta['alto']      = 150;
        
    } else {
        $query = $objeto->eliminar();
        
        if ($query) {
            $respuesta['error']         = FALSE;
            $respuesta['accion']        = 'recargar';
            $respuesta['mensaje']       = $textos->id('EVENTO_ELIMINADO_EXITOSMENTE');
            $respuesta['textoExito']    = TRUE;            

        } else {
            $respuesta['mensaje'] = $textos->id('ERROR_DESCONOCIDO');
            
        }
        
    }

    Servidor::enviarJSON($respuesta);
    
}

/**
 * Funcion que devuelve la respuesta para el autocompletar
 * @global type $sql
 * @param type $cadena 
 */
function listarItems($cadena) {
    global $sql;
    
    $respuesta  = array();
    $consulta   = $sql->seleccionar(array('subgrupos'), array('id', 'nombre'), 'nombre LIKE "%' . $cadena . '%" AND activo = "1"', '', 'nombre ASC', 0, 20);

    while ($fila = $sql->filaEnObjeto($consulta)) {
        $respuesta1             = array();
        $respuesta1['label']    = $fila->nombre;
        $respuesta1['value']    = $fila->id;
        $respuesta[]            = $respuesta1;
    }

    Servidor::enviarJSON($respuesta);
    
}
