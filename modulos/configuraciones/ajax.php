<?php

/**
 * @package     FOLCS
 * @subpackage  Configuraciones
 * @author      Pablo Andres Velez Vidal <pavelez@genesyscorp.com.co>
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2013 GENESYS
 * @version     0.1
 * */

if (isset($url_accion)) {
    switch ($url_accion) {
        case 'see'      :   cosultarItem($forma_id);
                            break;
                        
        case 'edit'     :   $datos = ($forma_procesar) ? $forma_datos : array();
                            modificarItem($forma_id, $datos);
                            break;
                        
    }
    
}

/**
 * Funcion que muestra la ventana modal de consulta para un item
 * 
 * @global objeto $textos   = objeto global encargado de la traduccion de los textos     
 * @param int $id           = id del item a consultar 
 */
function cosultarItem($id) {
    global $textos, $sql;

    if (!isset($id) || (isset($id) && !$sql->existeItem('empresas', 'id', $id))) {
        $respuesta = array();
        $respuesta['error'] = true;
        $respuesta['mensaje'] = $textos->id('NO_HA_SELECCIONADO_ITEM');

        Servidor::enviarJSON($respuesta);
        return NULL;
    }

    $objeto = new Configuracion($id);
    $respuesta = array();

    $codigo = HTML::parrafo($textos->id('CANTIDAD_DECIMALES'), 'negrilla margenSuperior');
    $codigo .= HTML::parrafo($objeto->cantidadDecimales, '', '');
    $codigo .= HTML::parrafo($textos->id('TIPO_MONEDA'), 'negrilla margenSuperior');
    $codigo .= HTML::parrafo($objeto->tipoMoneda, '', '');
    $codigo .= HTML::parrafo($textos->id('NOTA_FACTURA'), 'negrilla margenSuperior');
    $codigo .= HTML::parrafo($objeto->notaFactura, '', '');
    $codigo .= HTML::parrafo($textos->id('TIPO_DATO_IMPRESION_PDF'), 'negrilla margenSuperior');
    $codigo .= HTML::parrafo($objeto->datoCodigoBarra);  
    $codigo .= HTML::parrafo($textos->id('VALOR_PREDETERMINADO_IVA'), 'negrilla margenSuperior');
    $codigo .= HTML::parrafo($objeto->ivaGeneral); 
    $codigo .= HTML::parrafo($textos->id('VALOR_PREDETERMINADO_PORCENTAJE_GANANCIA'), 'negrilla margenSuperior');
    $codigo .= HTML::parrafo($objeto->porcPredGanancia);  
    $codigo .= HTML::parrafo($textos->id('FACTURAR_NEGATIVO'), 'negrilla margenSuperior');
    $codigo .= HTML::parrafo($textos->id('FACTURAR_NEGATIVO_'.$objeto->facturarNegativo) );     
    $codigo .= HTML::parrafo($textos->id('DIAS_PROMEDIO'), 'negrilla margenSuperior');
    $codigo .= HTML::parrafo($objeto->diasPromedioPonderado);   
    $codigo .= HTML::parrafo($textos->id('ID_PRINCIPAL_ARTICULO'), 'negrilla margenSuperior');
    $arrayIdArticulo = array('id' => $textos->id('ID_AUTOMATICO'), 'codigo_oem' => $textos->id('CODIGO_OEM'), 'plu_interno' => $textos->id('PLU_INTERNO'));
    $codigo .= HTML::parrafo($arrayIdArticulo[$objeto->idPrincipalArticulo]);    
    $codigo .= HTML::parrafo($textos->id('VALOR_UVT'), 'negrilla margenSuperior');
    $codigo .= HTML::parrafo($objeto->valorUvt); 
    $respuesta['generar']       = true;
    $respuesta['codigo']        = $codigo;
    $respuesta['titulo']        = HTML::parrafo($textos->id('CONSULTAR_ITEM'), 'letraBlanca negrilla subtitulo');
    $respuesta['destino']       = '#cuadroDialogo';
    $respuesta['ancho']         = 450;
    $respuesta['alto']          = 450;

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
    global $textos, $sesion_configuracionGlobal;

    $objeto = new Configuracion($id);
    $destino = '/ajax' . $objeto->urlBase . '/edit';
    $respuesta = array();

    if (empty($datos)) {

        $numeros = array();

        for ($i = 1; $i < 10; $i++) {
            $numeros["$i"] = $i;
        }

        $listaCantidadDecimales = HTML::listaDesplegable('datos[cantidad_decimales]', $numeros, $objeto->cantidadDecimales, '', 'listaCantidadDecimales', '', array('ayuda' => $textos->id('SELECCIONE_CANTIDAD_DECIMALES')));
        $listaTipoMoneda = HTML::listaDesplegable('datos[tipo_moneda]', array('tradicional' => $textos->id('TRADICIONAL'), 'nueva' => $textos->id('NUEVA')), $objeto->tipoMoneda, '', 'listaTipoMoneda', '', array('ayuda' => $textos->id('SELECCIONE_TIPO_MONEDA')));
        
        $arregloDatoCodigoBarra = array(
            'plu_interno' => 'PLU de la empresa',
            'referencia'  => 'Referencia',
            'id'          => 'Identificador Autonúmerico'
        );
        //determina el dato con el que se va a imprimir el código de barras
        $listaDatoCodigoBarra = HTML::listaDesplegable('datos[dato_codigo_barra]', $arregloDatoCodigoBarra, $objeto->datoCodigoBarra, '', '', '', array(), $textos->id('AYUDA_TIPO_DATO_IMPRESION_PDF'));        

        $codigo = HTML::campoOculto('procesar', 'true');
        $codigo .= HTML::campoOculto('id', $id);
        $codigo .= HTML::campoOculto('datos[dialogo]', '', 'idDialogo');
        $codigo .= HTML::parrafo($textos->id('CANTIDAD_DECIMALES'), 'negrilla margenSuperior');
        $codigo .= $listaCantidadDecimales;
        $codigo .= HTML::parrafo($textos->id('TIPO_MONEDA'), 'negrilla margenSuperior');
        $codigo .= $listaTipoMoneda;
        $codigo .= HTML::parrafo($textos->id('NOTA_FACTURA'), 'negrilla margenSuperior');
        $codigo .= HTML::areaTexto('datos[nota_factura]', 4, 55, $objeto->notaFactura, '', 'atNotaFactura');
        $codigo .= HTML::parrafo($textos->id('TIPO_DATO_IMPRESION_PDF'), 'negrilla margenSuperior');
        $codigo .= $listaDatoCodigoBarra;    
        $codigo .= HTML::parrafo($textos->id('VALOR_PREDETERMINADO_IVA'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[iva_general]', 3, 2, $objeto->ivaGeneral, 'campoPorcentaje', 'ivaGeneral', array(), $textos->id('AYUDA_IVA_GENERAL')); 
        $codigo .= HTML::parrafo($textos->id('VALOR_PREDETERMINADO_PORCENTAJE_GANANCIA'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[porc_pred_ganancia]', 3, 2, $objeto->porcPredGanancia, 'campoPorcentaje', 'gananciaGeneral', array(), $textos->id('AYUDA_GANANCIA_VENTA_GENERAL'));        
        $codigo .= HTML::parrafo($textos->id('DIAS_PROMEDIO'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[dias_promedio_ponderado]', 3, 3, $objeto->diasPromedioPonderado, '', 'diasPromedioPonderado', array(), $textos->id('AYUDA_DIAS_PROMEDIO'));
        
        $arrayIdArticulo = array('id' => $textos->id('ID_AUTOMATICO'), 'plu_interno' => $textos->id('PLU_INTERNO'));
        $listaIdPrincipal = HTML::listaDesplegable('datos[id_principal_articulo]', $arrayIdArticulo, $objeto->idPrincipalArticulo, 'listaIdPrincipalArticulo' , 'listaIdPrincipalArticulo', '', array());
        
        $codigo .= HTML::parrafo($textos->id('ID_PRINCIPAL_ARTICULO'), 'negrilla margenSuperior');
        $codigo .= $listaIdPrincipal;
        
        $codigo .= HTML::parrafo(HTML::campoChequeo('datos[facturar_negativo]', $objeto->facturarNegativo, '', '', array('ayuda' => $textos->id('AYUDA_FACTURAR_NEGATIVO'))) . $textos->id('FACTURAR_NEGATIVO'), 'margenSuperior');
        $codigo .= HTML::parrafo($textos->id('VALOR_UVT'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[valor_uvt]', 10, 15, $objeto->valorUvt, 'campoDinero', 'valorUvt', array(), $textos->id('AYUDA_VALOR_UVT')); 
        
        
        $codigo .= HTML::parrafo(HTML::boton('chequeo', $textos->id('ACEPTAR')), 'margenSuperior');
        $codigo .= HTML::parrafo($textos->id('REGISTRO_MODIFICADO'), 'textoExitoso', 'textoExitoso');
        $codigo = HTML::forma($destino, $codigo);

        $respuesta['generar']       = true;
        $respuesta['codigo']        = $codigo;
        $respuesta['destino']       = '#cuadroDialogo';
        $respuesta['titulo']        = HTML::parrafo($textos->id('MODIFICAR_ITEM'), 'letraBlanca negrilla subtitulo');
        $respuesta['ancho']         = 450;
        $respuesta['alto']          = 500;
        
    } else {
        $respuesta['error'] = true;

        $idItem = $objeto->modificar($datos);
        if ($idItem) {
            /*             * ************** Creo el nuevo item que se insertara via ajax *************** */
            $objeto = new Configuracion($id);
            
            //destruir y crear de nuevo la sesion
            $configuracionGlobal = $objeto;
            Sesion::borrar('configuracionGlobal');
            Sesion::registrar('configuracionGlobal', $configuracionGlobal);  
            
            $celdas = array($objeto->cantidadDecimales, $objeto->tipoMoneda);
            $celdas = HTML::crearFilaAModificar($celdas);

            if ($datos['dialogo'] == '') {
                $respuesta['error']                 = false;
                $respuesta['accion']                = 'insertar';
                $respuesta['contenido']             = $celdas;
                $respuesta['idContenedor']          = '#tr_' . $id;
                $respuesta['modificarFilaTabla']    = true;
                $respuesta['idDestino']             = '#tr_' . $id;
                
            } else {
                $respuesta['error']                 = false;
                $respuesta['accion']                = 'insertar';
                $respuesta['contenido']             = $celdas;
                $respuesta['idContenedor']          = '#tr_' . $id;
                $respuesta['modificarFilaDialogo']  = true;
                $respuesta['idDestino']             = '#tr_' . $id;
                $respuesta['ventanaDialogo']        = $datos['dialogo'];
                
            }
            
        } else {
            $respuesta['mensaje'] = $textos->id('ERROR_DESCONOCIDO');
            
        }

    }

    Servidor::enviarJSON($respuesta);
}
