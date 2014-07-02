<?php

/**
 * @package     FOM (Framework OpenSource Multiplatform)
 * @subpackage  Resoluciones
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
                                    adicionarItem($datos);
                                    break;
        
        case 'see' :                cosultarItem($forma_id);
                                    break;
        
        case 'edit' :               $datos = ($forma_procesar) ? $forma_datos : array();
                                    modificarItem($forma_id, $datos);
                                    break;
        
        case 'delete' :             $confirmado = ($forma_procesar) ? true : false;
                                    eliminarItem($forma_id, $confirmado, $forma_dialogo);
                                    break;
        
        case 'search' :             buscarItem($forma_datos, $forma_cantidadRegistros);
                                    break;
        
        case 'move' :               paginador($forma_pagina, $forma_orden, $forma_nombreOrden, $forma_consultaGlobal, $forma_cantidadRegistros);
                                    break;
        
        case 'listar' :             listarItems($url_cadena);
                                    break;
        
        case 'eliminarVarios' :     $confirmado = ($forma_procesar) ? true : false;
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

    $objeto = new Resolucion($id);
    $respuesta = array();

    $codigo  = HTML::campoOculto('procesar', 'true');
    $codigo .= HTML::campoOculto('id', $id);
    $codigo .= HTML::parrafo($textos->id('SEDE_RESOLUCION'), 'negrilla margenSuperior');
    $codigo .= HTML::parrafo($objeto->sede->nombre, '', '');
    $codigo .= HTML::parrafo($textos->id('PREFIJO'), 'negrilla margenSuperior');
    $codigo .= HTML::parrafo($objeto->prefijo, '', '');
    $codigo .= HTML::parrafo($textos->id('NUMERO'), 'negrilla margenSuperior');
    $codigo .= HTML::parrafo($objeto->numero, '', '');
    $codigo .= HTML::parrafo($textos->id('FECHA_RESOLUCION'), 'negrilla margenSuperior');
    $codigo .= HTML::parrafo($objeto->fechaResolucion, '', '');
    $codigo .= HTML::parrafo($textos->id('NUMERO_FACTURA_INICIAL'), 'negrilla margenSuperior');
    $codigo .= HTML::parrafo($objeto->numeroFacturaInicial, '', '');
    $codigo .= HTML::parrafo($textos->id('NUMERO_FACTURA_FINAL'), 'negrilla margenSuperior');
    $codigo .= HTML::parrafo($objeto->numeroFacturaFinal, '', '');
    $codigo .= HTML::parrafo($textos->id('FECHA_INICIAL'), 'negrilla margenSuperior');
    $codigo .= HTML::parrafo($objeto->fechaInicial, '', '');
    $codigo .= HTML::parrafo($textos->id('FECHA_FINAL'), 'negrilla margenSuperior');
    $codigo .= HTML::parrafo($objeto->fechaFinal, '', '');
    $codigo .= HTML::parrafo($textos->id('NUMERO_FACTURAS_ALERTA'), 'negrilla margenSuperior');
    $codigo .= HTML::parrafo($objeto->numeroFacturasAlerta, '', '');
    
    if (!empty($objeto->numeroRetomaFacturacion)) {
        $codigo .= HTML::parrafo($textos->id('NUMERO_QUE_RETOMA_FACTURACION'), 'negrilla margenSuperior');
        $codigo .= HTML::parrafo($objeto->numeroRetomaFacturacion, 'negrilla margenSuperior');
    }

    $codigo .= HTML::parrafo($textos->id('ESTADO'), 'negrilla margenSuperior');
    $activo  = ($objeto->activo) ? HTML::frase($textos->id('ACTIVO'), 'activo') : HTML::frase($textos->id('INACTIVO'), 'inactivo');
    $codigo .= HTML::parrafo($activo, '', '');

    $respuesta['generar']       = true;
    $respuesta['codigo']        = $codigo;
    $respuesta['titulo']        = HTML::parrafo($textos->id('CONSULTAR_ITEM'), 'letraBlanca negrilla subtitulo');
    $respuesta['destino']       = '#cuadroDialogo';
    $respuesta['ancho']         = 550;
    $respuesta['alto']          = 450;


    Servidor::enviarJSON($respuesta);
}

/**
 *
 * @global type $textos
 * @global type $sql
 * @param type $datos 
 */
function adicionarItem($datos = array()) {
    global $textos, $sql;

    $objeto         = new Resolucion();
    $destino        = '/ajax' . $objeto->urlBase . '/add';
    $respuesta      = array();

    if (empty($datos)) {

        $codigo = HTML::campoOculto('procesar', 'true');
        $codigo .= HTML::campoOculto('datos[dialogo]', '', 'idDialogo');

        $sedesEmpresa = $sql->seleccionar(array('sedes_empresa'), array('id', 'nombre'), 'id NOT IN (0)', 'id', 'nombre ASC');
        $sedes = array();
        
        while ($objeto = $sql->filaEnObjeto($sedesEmpresa)) {
            $sedes[$objeto->id] = $objeto->nombre;
        }
        
        $listaSedesEmpresa = HTML::listaDesplegable('datos[id_sede]', $sedes, '', '', 'listaSedesEmpresa', '', array('ayuda' => $textos->id('AYUDA_SEDE_RESOLUCION')));

        $codigo .= HTML::parrafo($textos->id('SEDE_RESOLUCION'), 'negrilla margenSuperior');
        $codigo .= $listaSedesEmpresa;
        $codigo .= HTML::parrafo($textos->id('PREFIJO'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[prefijo]', 20, 3, '', '', '', '');
        $codigo .= HTML::parrafo($textos->id('NUMERO'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[numero]', 40, 255, '', 'campoObligatorio');
        $codigo .= HTML::parrafo($textos->id('FECHA_RESOLUCION'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[fecha_resolucion]', 20, 255, '', 'campoObligatorio fechaAntigua campoCalendario');
        $codigo .= HTML::parrafo($textos->id('NUMERO_FACTURA_INICIAL'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[num_factura_inicio]', 30, 255, '', 'campoObligatorio');
        $codigo .= HTML::parrafo($textos->id('NUMERO_FACTURA_FINAL'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[num_factura_final]', 30, 255, '', 'campoObligatorio');
        $codigo .= HTML::parrafo($textos->id('FECHA_INICIAL'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[fecha_inicio]', 20, 255, '', 'campoObligatorio fechaAntigua campoCalendario');
        $codigo .= HTML::parrafo($textos->id('FECHA_FINAL'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[fecha_final]', 20, 255, '', 'campoObligatorio fechaAntigua campoCalendario');
        $codigo .= HTML::parrafo($textos->id('NUMERO_FACTURAS_ALERTA'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[numero_facturas_alerta]', 20, 255, '', 'campoObligatorio');
        $codigo .= HTML::parrafo($textos->id('NUMERO_QUE_RETOMA_FACTURACION'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[numero_retoma_facturacion]', 20, 255, '', 'campoObligatorio', '', array(), $textos->id('AYUDA_NUMERO_RETOMA_FACTURACION'));
        $codigo .= HTML::parrafo(HTML::campoChequeo('datos[activo]', true) . $textos->id('ACTIVO'), 'margenSuperior');
        $codigo .= HTML::parrafo(HTML::boton('chequeo', $textos->id('ACEPTAR'), '', '', ''), 'margenSuperior');
        $codigo .= HTML::parrafo($textos->id('REGISTRO_AGREGADO'), 'textoExitoso', 'textoExitoso');
        $codigo1 = HTML::forma($destino, $codigo, 'P');

        $respuesta['generar']       = true;
        $respuesta['codigo']        = $codigo1;
        $respuesta['titulo']        = HTML::parrafo($textos->id('ADICIONAR_ITEM'), 'letraBlanca negrilla subtitulo');
        $respuesta['destino']       = '#cuadroDialogo';
        $respuesta['ancho']         = 550;
        $respuesta['alto']          = 600;
        
    } else {

        $respuesta['error'] = true;

        $existeNumero = $sql->existeItem('resoluciones', 'numero', $datos['numero']);
        $resolucionActivaSede = $sql->obtenerValor('resoluciones', 'numero', 'activo = "1" AND id_sede = "' . $datos['id_sede'] . '"');

        if (empty($datos['numero'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_NUMERO_RESOLUCION');
            
        } elseif (empty($datos['num_factura_inicio'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_NUMERO_FACTURA_INICIO');
            
        } elseif (empty($datos['fecha_inicio'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_FECHA_INICIO');
            
        } elseif ($existeNumero) {
            $respuesta['mensaje'] = $textos->id('ERROR_EXISTE_NUMERO');
            
        } elseif ($resolucionActivaSede) {
            $nomSede = $sql->obtenerValor('sedes_empresa', 'nombre', 'id = "' . $datos['id_sede'] . '"');
            $nota1 = str_replace('%1', $nomSede, $textos->id('ERROR_EXISTE_RESOLUCION_ACTIVA_SEDE'));
            $respuesta['mensaje'] = str_replace('%2', $resolucionActivaSede, $nota1);
            
        } else {
            $idItem = $objeto->adicionar($datos);
            
            if ($idItem) {
                /*                 * ************** Creo el nuevo item que se insertara via ajax *************** */
                $objeto = new Resolucion($idItem);

                $estado = ($objeto->activo) ? HTML::frase($textos->id('ACTIVO'), 'activo') : HTML::frase($textos->id('INACTIVO'), 'inactivo');

                $celdas = array($objeto->sede->nombre, $objeto->prefijo, $objeto->numero, $objeto->fechaResolucion, $objeto->numeroFacturaInicial, $objeto->numeroFacturaFinal, $objeto->fechaInicial, $objeto->fechafinal, $estado);
                $claseFila = '';
                $idFila = $idItem;
                $celdas1 = HTML::crearNuevaFila($celdas, $claseFila, $idFila);

                $respuesta['error']             = false;
                $respuesta['accion']            = 'insertar';
                $respuesta['contenido']         = $celdas1;
                $respuesta['idContenedor']      = '#tr_' . $idItem;
                $respuesta['idDestino']         = '#tablaRegistros';

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
    global $textos, $sql;

    $objeto     = new Resolucion($id);
    $destino    = '/ajax' . $objeto->urlBase . '/edit';
    $respuesta  = array();

    if (empty($datos)) {

        $codigo = HTML::campoOculto('procesar', 'true');
        $codigo .= HTML::campoOculto('id', $id);
        $codigo .= HTML::campoOculto('datos[dialogo]', '', 'idDialogo');

        $sedesEmpresa = $sql->seleccionar(array('sedes_empresa'), array('id', 'nombre'), 'id NOT IN (0)', 'id', 'nombre ASC');
        
        $sedes = array();
        
        while ($obj = $sql->filaEnObjeto($sedesEmpresa)) {
            $sedes[$obj->id] = $obj->nombre;
        }
        
        $listaSedesEmpresa = HTML::listaDesplegable('datos[id_sede]', $sedes, $objeto->idSede, '', 'listaSedesEmpresa', '', array('ayuda' => $textos->id('AYUDA_SEDE_RESOLUCION')));

        $codigo .= HTML::parrafo($textos->id('SEDE_RESOLUCION'), 'negrilla margenSuperior');
        $codigo .= $listaSedesEmpresa;
        $codigo .= HTML::parrafo($textos->id('PREFIJO'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[prefijo]', 20, 3, $objeto->prefijo, '', '', '');
        $codigo .= HTML::parrafo($textos->id('NUMERO'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[numero]', 40, 255, $objeto->numero);
        $codigo .= HTML::parrafo($textos->id('FECHA_RESOLUCION'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[fecha_resolucion]', 20, 255, $objeto->fechaResolucion, 'campoObligatorio fechaAntigua campoCalendario');
        $codigo .= HTML::parrafo($textos->id('NUMERO_FACTURA_INICIAL'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[num_factura_inicio]', 30, 255, $objeto->numeroFacturaInicial, 'campoObligatorio');
        $codigo .= HTML::parrafo($textos->id('NUMERO_FACTURA_FINAL'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[num_factura_final]', 30, 255, $objeto->numeroFacturaFinal, 'campoObligatorio');
        $codigo .= HTML::parrafo($textos->id('FECHA_INICIAL'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[fecha_inicio]', 20, 255, $objeto->fechaInicial, 'campoObligatorio fechaAntigua campoCalendario');
        $codigo .= HTML::parrafo($textos->id('FECHA_FINAL'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[fecha_final]', 20, 255, $objeto->fechaFinal, 'campoObligatorio fechaAntigua campoCalendario');
        $codigo .= HTML::parrafo($textos->id('NUMERO_FACTURAS_ALERTA'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[numero_facturas_alerta]', 30, 255, $objeto->numeroFacturasAlerta, 'campoObligatorio');
        
        if (!empty($objeto->numeroRetomaFacturacion)) {
            $codigo .= HTML::parrafo($textos->id('NUMERO_QUE_RETOMA_FACTURACION'), 'negrilla margenSuperior');
            $codigo .= HTML::campoTexto('datos[numero_retoma_facturacion]', 30, 255, $objeto->numeroRetomaFacturacion, 'campoObligatorio', '', array(), $textos->id('AYUDA_NUMERO_RETOMA_FACTURACION'));
        }
        
        $codigo .= HTML::parrafo(HTML::campoChequeo('datos[activo]', $objeto->activo) . $textos->id('ACTIVO'), 'margenSuperior');
        $codigo .= HTML::parrafo(HTML::boton('chequeo', $textos->id('ACEPTAR'), '', '', ''), 'margenSuperior');
        $codigo .= HTML::parrafo($textos->id('REGISTRO_MODIFICADO'), 'textoExitoso', 'textoExitoso');
        $codigo1 = HTML::forma($destino, $codigo, 'P');

        $respuesta['generar']       = true;
        $respuesta['codigo']        = $codigo1;
        $respuesta['titulo']        = HTML::parrafo($textos->id('MODIFICAR_ITEM'), 'letraBlanca negrilla subtitulo');
        $respuesta['destino']       = '#cuadroDialogo';
        $respuesta['ancho']         = 550;
        $respuesta['alto']          = 590;
        
    } else {

        $respuesta['error'] = true;

        $existeNumero           = $sql->existeItem('resoluciones', 'numero', $datos['numero'], 'id != "' . $objeto->id . '"');
        $resolucionActivaSede   = $sql->obtenerValor('resoluciones', 'numero', 'activo = "1" AND id_sede = "' . $datos['id_sede'] . '" AND id != "' . $objeto->id . '"');

        if (empty($datos['numero'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_NUMERO_RESOLUCION');
            
        } elseif (empty($datos['num_factura_inicio'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_NUMERO_FACTURA_INICIO');
            
        } elseif (empty($datos['fecha_inicio'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_FECHA_INICIO');
            
        } elseif ($existeNumero) {
            $respuesta['mensaje'] = $textos->id('ERROR_EXISTE_NUMERO');
            
        } elseif ($resolucionActivaSede) {
            $nomSede                = $sql->obtenerValor('sedes_empresa', 'nombre', 'id = "' . $datos['id_sede'] . '"');
            $nota1                  = str_replace('%1', $nomSede, $textos->id('ERROR_EXISTE_RESOLUCION_ACTIVA_SEDE'));
            $respuesta['mensaje']   = str_replace('%2', $resolucionActivaSede, $nota1);
            
        } else {
            $idItem = $objeto->modificar($datos);
            
            if ($idItem) {
                /*                 * ************** Creo el nuevo item que se insertara via ajax *************** */
                $objeto = new Resolucion($id);

                $estado = ($objeto->activo) ? HTML::frase($textos->id('ACTIVO'), 'activo') : HTML::frase($textos->id('INACTIVO'), 'inactivo');

                $celdas = array($objeto->sede->nombre, $objeto->prefijo, $objeto->numero, $objeto->fechaResolucion, $objeto->numeroFacturaInicial, $objeto->numeroFacturaFinal, $objeto->fechaInicial, $objeto->fechafinal, $estado);
                $celdas1 = HTML::crearFilaAModificar($celdas);

                $respuesta['error']             = false;
                $respuesta['accion']            = 'insertar';
                $respuesta['contenido']         = $celdas1;
                $respuesta['idContenedor']      = '#tr_' . $id;
                $respuesta['idDestino']         = '#tr_' . $id;

                if ($datos['dialogo'] == '') {
                    $respuesta['modificarFilaTabla']    = true;
                    
                } else {
                    $respuesta['modificarFilaDialogo']      = true;
                    $respuesta['ventanaDialogo']            = $datos['dialogo'];
                    
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

    $objeto     = new Resolucion($id);
    $destino    = '/ajax' . $objeto->urlBase . '/delete';
    $respuesta  = array();

    if (!empty($objeto->prefijo)) {
        $prefijo = $objeto->prefijo . '_';
    }

    if (!$confirmado) {
        $titulo  = HTML::frase($prefijo . $objeto->numero, 'negrilla');
        $titulo1 = str_replace('%1', $titulo, $textos->id('CONFIRMAR_ELIMINACION'));
        $codigo  = HTML::campoOculto('procesar', 'true');
        $codigo .= HTML::campoOculto('id', $id);
        $codigo .= HTML::campoOculto('datos[dialogo]', '', 'idDialogo');
        $codigo .= HTML::parrafo($titulo1);
        $codigo .= HTML::parrafo(HTML::boton('chequeo', $textos->id('ACEPTAR'), '', 'botonOk', 'botonOk'), 'margenSuperior');
        $codigo .= HTML::parrafo($textos->id('REGISTRO_ELIMINADO'), 'textoExitoso', 'textoExitoso');
        $codigo1 = HTML::forma($destino, $codigo);

        $respuesta['generar']   = true;
        $respuesta['codigo']    = $codigo1;
        $respuesta['destino']   = '#cuadroDialogo';
        $respuesta['titulo']    = HTML::parrafo($textos->id('ELIMINAR_ITEM'), 'letraBlanca negrilla subtitulo');
        $respuesta['ancho']     = 350;
        $respuesta['alto']      = 150;
        
    } else {

        if ($objeto->eliminar()) {

            $respuesta['error']     = false;
            $respuesta['accion']    = 'insertar';
            $respuesta['idDestino'] = '#tr_' . $id;
            
            if ($dialogo == '') {
                $respuesta['eliminarFilaTabla'] = true;
                
            } else {
                $respuesta['eliminarFilaDialogo']    = true;
                $respuesta['ventanaDialogo']         = $dialogo;
                
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

    $data   = explode('[', $data);
    $datos  = $data[0];

    if (empty($datos)) {
        $respuesta['error']     = true;
        $respuesta['mensaje']   = $textos->id('ERROR_FALTA_CADENA_BUSQUEDA');
        
    } else if (!empty($datos) && strlen($datos) < 2) {
        $respuesta['error']     = true;
        $respuesta['mensaje']   = str_replace('%1', '2', $textos->id('ERROR_TAMAÑO_CADENA_BUSQUEDA'));
        
    } else {
        $item       = '';
        $respuesta  = array();
        $objeto     = new Resolucion();
        $registros  = $configuracion['GENERAL']['registrosPorPagina'];
        
        if (!empty($cantidadRegistros)) {
            $registros = (int) $cantidadRegistros;
        }
        
        $pagina = 1;
        $registroInicial = 0;

        $palabras = explode(' ', $datos);

        $condicionales = $data[1];

        if ($condicionales == '') {
            $condicion = '(r.numero REGEXP "(' . implode('|', $palabras) . ')")';
            
        } else {
            //$condicion = str_replace(']', ''', $data[1]);
            $condicionales = explode('|', $condicionales);

            $condicion = '(';
            $tam = sizeof($condicionales) - 1;
            
            for ($i = 0; $i < $tam; $i++) {
                $condicion .= $condicionales[$i] . ' REGEXP "(' . implode("|", $palabras) . ')" ';
                
                if ($i != $tam - 1) {
                    $condicion .= ' OR ';
                    
                }
                
            }
            $condicion .= ')';
            
        }

        $arregloItems = $objeto->listar($registroInicial, $registros, array('0'), $condicion, 'r.numero');

        if ($objeto->registrosConsulta) {//si la consulta trajo registros
            $datosPaginacion = array($objeto->registrosConsulta, $registroInicial, $registros, $pagina, $objeto->registrosConsulta);
            $item .= $objeto->generarTabla($arregloItems, $datosPaginacion);
            $info = HTML::parrafo(str_replace('%', $objeto->registrosConsulta, $textos->id('RESULTADOS_BUSQUEDA')), 'textoExitosoNotificaciones');
            
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
 * Funcion que se encarga de recargar la tabla de datos paginando
 * @global type $configuracion
 * @param type $pagina
 * @param type $orden
 * @param type $nombreOrden
 * @param type $consultaGlobal 
 */
function paginador($pagina, $orden = NULL, $nombreOrden = NULL, $consultaGlobal = NULL, $cantidadRegistros = NULL) {
    global $configuracion;

    $item           = '';
    $respuesta      = array();
    $objeto         = new Resolucion();

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
                $condicion .= $condicionales[$i] . ' REGEXP "(' . implode("|", $palabras) . ')" ';
                
                if ($i != $tam - 1) {
                    $condicion .= ' OR ';
                    
                }
            }
            
            $condicion .= ')';

            $consultaGlobal = $condicion;
            
        } else {
            $consultaGlobal = '(r.numero REGEXP "(' . implode("|", $palabras) . ')")';
            
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
 * @global type $sql
 * @param type $cadena 
 */
function listarItems($cadena) {
    global $sql;
    $respuesta = array();
    $consulta = $sql->seleccionar(array('resoluciones'), array('numero'), 'numero LIKE "%' . $cadena . '%" AND activo = "1"', '', 'numero ASC', 0, 20);

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


    $destino = '/ajax/resoluciones/eliminarVarios';
    $respuesta = array();

    if (!$confirmado) {
        $titulo  = HTML::frase($cantidad, 'negrilla');
        $titulo1 = str_replace('%1', $titulo, $textos->id('CONFIRMAR_ELIMINACION_VARIOS'));
        $codigo  = HTML::campoOculto('procesar', 'true');
        $codigo .= HTML::campoOculto('cadenaItems', $cadenaItems, 'cadenaItems');
        $codigo .= HTML::parrafo($titulo1);
        $codigo .= HTML::parrafo(HTML::boton('chequeo', $textos->id('ACEPTAR'), '', 'botonOk', 'botonOk'), 'margenSuperior');
        $codigo .= HTML::parrafo($textos->id('REGISTRO_ELIMINADO'), 'textoExitoso', 'textoExitoso');
        $codigo1 = HTML::forma($destino, $codigo);

        $respuesta['generar']   = true;
        $respuesta['codigo']    = $codigo1;
        $respuesta['destino']   = '#cuadroDialogo';
        $respuesta['titulo']    = HTML::parrafo($textos->id('ELIMINAR_VARIOS_REGISTROS'), 'letraBlanca negrilla subtitulo');
        $respuesta['ancho']     = 350;
        $respuesta['alto']      = 150;
        
    } else {

        $cadenaIds  = substr($cadenaItems, 0, -1);
        $arregloIds = explode(',', $cadenaIds);

        $eliminarVarios = true;
        
        foreach ($arregloIds as $val) {
            $objeto         = new Resolucion($val);
            $eliminarVarios = $objeto->eliminar();
            
        }

        if ($eliminarVarios) {

            $respuesta['error']         = false;
            $respuesta['textoExito']    = true;
            $respuesta['mensaje']       = $textos->id('ITEMS_ELIMINADOS_CORRECTAMENTE');
            $respuesta['accion']        = 'recargar';
            
        } else {
            $respuesta['mensaje'] = $textos->id('ERROR_DESCONOCIDO');
            
        }
        
    }

    Servidor::enviarJSON($respuesta);
    
}
