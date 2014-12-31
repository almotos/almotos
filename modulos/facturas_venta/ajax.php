<?php

/**
 * @package     FOM (Framework OpenSource Multiplatform)
 * @subpackage  Ventas de Mercancia - Facturas de venta
 * @author      Pablo Andres Velez Vidal <pavelez@genesyscorp.com.co>
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2013 GENESYS
 * @version     0.1
 * */

if (isset($url_accion)) {
    
    switch ($url_accion) {
        
        case 'see'                      :   cosultarItem($forma_id);
                                            break;
        
        case 'edit'                     :   modificarItem($forma_id);
                                            break;
        
        case 'delete'                   :   $confirmado = ($forma_procesar) ? true : false;
                                            eliminarItem($forma_id, $confirmado, $forma_dialogo);
                                            break;
        
        case 'search'                   :   buscarItem($forma_datos, $forma_cantidadRegistros);
                                            break;
        
        case 'move'                     :   paginador($forma_pagina, $forma_orden, $forma_nombreOrden, $forma_consultaGlobal, $forma_cantidadRegistros);
                                            break;
        
        case 'buscarFactura'            :   buscarFactura();
                                            break;
        
        case 'modificarFechaFactura'    :   modificarFechaFactura($forma_idFactura, $forma_fechaFactura);
                                            break;
        
        case 'eliminarVarios'           :   $confirmado = ($forma_procesar) ? true : false;
                                            eliminarVarios($confirmado, $forma_cantidad, $forma_cadenaItems);
                                            break;
                                        
        case 'adicionarNotaCredito'     :   adicionarNotaCredito($forma_id, $forma_datos);
                                            break;
                                        
        case 'consultarNotaCredito'     :   consultarNotaCredito($forma_id);
                                            break; 
                                        
        case 'eliminarNotaCreditoDigital' :   eliminarNotaCreditoDigital($forma_id);
                                            break;                                         
        
        case 'adicionarNotaDebito'      :   adicionarNotaDebito($forma_id, $forma_datos);
                                            break;
                                        
        case 'consultarNotaDebito'      :   consultarNotaDebito($forma_id);
                                            break; 
                                        
        case 'eliminarNotaDebitoDigital' :   eliminarNotaDebitoDigital($forma_id);
                                            break;                                            
        
    }
    
}


/**
 * Funcion que muestra la ventana modal de consultar un articulo
 * 
 * @global type $textos
 * @param type $id = id del banco a consultar 
 */
function cosultarItem($id) {
    global $textos, $sql, $sesion_configuracionGlobal;

    if (!isset($id) || (isset($id) && !$sql->existeItem('facturas_venta', 'id', $id))) {
        $respuesta              = array();
        $respuesta['error']     = true;
        $respuesta['mensaje']   = $textos->id('NO_HA_SELECCIONADO_ITEM');

        Servidor::enviarJSON($respuesta);
        return NULL;
    }

    $objeto     = new FacturaVenta($id);
    $respuesta  = array();
    $regimenEmpresa = $sesion_configuracionGlobal->empresa->regimen;
    
    $codigo = '';

    $codigo .= HTML::campoOculto('id', $id);

    $codigo1  = HTML::parrafo($textos->id('CLIENTE') . ': ' . HTML::frase($objeto->cliente, 'sinNegrilla'), 'negrilla margenSuperior');
    $codigo1 .= HTML::parrafo($textos->id('NIT') . ': ' . HTML::frase($objeto->nitCliente, 'sinNegrilla'), 'negrilla margenSuperior');
    $codigo1 .= HTML::parrafo($textos->id('FECHA_FACTURA') . ': ' . HTML::frase($objeto->fechaFactura, 'sinNegrilla'), 'negrilla margenSuperior');
    $codigo1 .= HTML::parrafo($textos->id('USUARIO_QUE_FACTURA') . ': ' . HTML::frase($objeto->usuario, 'sinNegrilla'), 'negrilla margenSuperior');
    $codigo1 .= HTML::parrafo('', 'negrilla margenSuperior');

    if ($objeto->fechaVtoFactura != "" && $objeto->fechaVtoFactura != "0000-00-00") {
        $codigo2 = HTML::parrafo($textos->id('FECHA_VENCIMIENTO') . ': ' . HTML::frase($objeto->fechaVtoFactura, 'sinNegrilla'), 'negrilla margenSuperior');
    }
    
    $codigo2 = '';
    
    $codigo2 .= HTML::parrafo($textos->id('SEDE') . ': ' . HTML::frase($objeto->sede, 'sinNegrilla'), 'negrilla margenSuperior');
    $codigo2 .= HTML::parrafo($textos->id('VALOR_FLETE') . ': ' . HTML::frase('$ '.Recursos::formatearNumero($objeto->valorFlete, '$'), 'sinNegrilla'), 'negrilla margenSuperior');
    
    if (count($objeto->arregloRetenciones) > 0 ) {
        $codigo2 .= HTML::parrafo($textos->id('RETENCIONES'), 'negrilla margenSuperior');
        foreach ($objeto->arregloRetenciones as $id => $valor) {
            $codigo2 .= HTML::parrafo($id . ': ' . HTML::frase($valor, 'sinNegrilla'), 'margenIzquierda');
        }
    
    }
    
    $codigo2 .= HTML::parrafo($textos->id('CAJA') . ': ' . HTML::frase($objeto->caja, 'sinNegrilla'), 'negrilla margenSuperior');
    $codigo2 .= HTML::parrafo($textos->id('OBSERVACIONES') . ': ' . HTML::frase($objeto->observaciones, 'sinNegrilla'), 'negrilla margenSuperior');

    //verificar el identificador escogido para los articulos en la configuracion global y usarlo para mostrar los datos
    $idPrincipalArticulo = (string)$sesion_configuracionGlobal->idPrincipalArticulo;
    
    $arrayIdArticulo     = array('id' => $textos->id('ID_AUTOMATICO'), 'codigo_oem' => $textos->id('CODIGO_OEM'), 'plu_interno' => $textos->id('PLU'));        


    $datosTabla = array(
        HTML::frase($textos->id($arrayIdArticulo[$idPrincipalArticulo]), 'negrilla margenIzquierda'),
        HTML::frase($textos->id('ARTICULO'),    'negrilla margenIzquierda'),
        HTML::frase($textos->id('CANTIDAD'),    'negrilla margenIzquierda'),
        HTML::frase($textos->id('DESCUENTO'),   'negrilla margenIzquierda'),
        HTML::frase($textos->id('IVA'),         'negrilla margenIzquierda'),
        HTML::frase($textos->id('PRECIO'),      'negrilla margenIzquierda'),
        HTML::frase($textos->id('SUBTOTAL'),    'negrilla margenIzquierda')
    );

    //si el regimen es simplificado quito el iva del arreglo
    if ($regimenEmpresa == "1"){
        $datosTabla = array_diff($datosTabla, array(HTML::frase($textos->id('IVA'), 'negrilla margenIzquierda')));
    }
    
    $subtotalFactura = 0;

    $listaArticulos = array();
    
    foreach ($objeto->listaArticulos as $article) {

        $object = new stdClass();

        $object->plu            = $article->$idPrincipalArticulo;
        $object->articulo       = $article->articulo;
        $object->cantidad       = $article->cantidad;  
        $object->descuento      = $article->descuento;
        
        if ($regimenEmpresa != "1"){
            $object->iva = $article->iva;
        }
        
        $object->precio         = $article->precio; 
        $object->subtotal       = ''; 
        
        if (strlen($object->articulo) > 80) {
            $object->articulo = substr($object->articulo, 0, 80) . '.';
        }        
        
        
        if ($object->descuento == 0 || $object->descuento == '0') {
            $object->subtotal = $object->cantidad * $object->precio;
        } else {
            $object->subtotal = ($object->cantidad * $object->precio) - ( ( ($object->cantidad * $object->precio) * $object->descuento) / 100 );
        }
        $object->descuento  = Recursos::formatearNumero($object->descuento, '%', '0');
        $object->precio     = '$' . Recursos::formatearNumero($object->precio, '$');
        
//        $subtotalFactura += $object->subtotal;

        $object->subtotal = '$' . Recursos::formatearNumero($object->subtotal, '$');

        $listaArticulos[] = $object;
    }



    $idTabla                    = 'tablaListaArticulosConsulta';
    $clasesColumnas             = array('', '', '', '', '');
    $clasesFilas                = array('centrado', 'centrado', 'centrado', 'centrado', 'centrado', 'centrado');
    $opciones                   = array('cellpadding' => '5', 'cellspacing' => '5', 'border' => '2');
    $clase                      = 'tablaListaArticulosConsulta';
    $contenedorListaArticles    = HTML::tabla($datosTabla, $listaArticulos, $clase, $idTabla, $clasesColumnas, $clasesFilas, $opciones);

    //si el regimen es diferente al regimen simplificado muestro el iva
    if ($regimenEmpresa != "1"){
        $codigo4  = HTML::parrafo($textos->id('IVA') . '$ '.HTML::frase($objeto->iva . '$ ', 'sinNegrilla'), 'negrilla margenSuperior');
    }
        
    $codigo4 .= HTML::parrafo($textos->id('DESCUENTOS') . ': ', 'negrilla margenSuperior letraVerde');

    $totalFactura = $objeto->subtotal - $objeto->totalRetenciones;

    if (!empty($objeto->concepto1) && !empty($objeto->descuento1)) {

        $pesosDcto1 = ($totalFactura * $objeto->descuento1) / 100;
        $codigo4 .= HTML::parrafo($objeto->concepto1 . ': ' . HTML::frase($objeto->descuento1 . '%', 'sinNegrilla') . HTML::frase('$' . Recursos::formatearNumero($pesosDcto1, '$'), 'sinNegrilla margenIzquierdaDoble'), 'negrilla margenSuperior');
    
    }

    if (!empty($objeto->concepto2) && !empty($objeto->descuento2)) {
        $pesosDcto2 = ($totalFactura * $objeto->descuento2) / 100;
        $codigo4 .= HTML::parrafo($objeto->concepto2 . ': ' . HTML::frase($objeto->descuento2 . '%', 'sinNegrilla') . HTML::frase('$' . Recursos::formatearNumero($pesosDcto2, '$'), 'sinNegrilla margenIzquierdaDoble'), 'negrilla margenSuperior');
    
    }
    
    if (!empty($objeto->fechaLimiteDcto1) && !empty($objeto->porcentajeDcto1)) {
        $pesosDctoExtra1    = ($totalFactura * $objeto->porcentajeDcto1) / 100;
        $codigo4           .= HTML::parrafo($textos->id('FECHA_LIMITE_PAGO_PARA_DESCUENTO') . ': ' . $objeto->fechaLimiteDcto1 . ' ' . $textos->id('PORCENTAJE_DESCUENTO') . ': ' . HTML::frase($objeto->porcentajeDcto1 . '%', 'sinNegrilla') . HTML::frase('$'.Recursos::formatearNumero($pesosDctoExtra1, '$'), 'sinNegrilla margenIzquierdaDoble'), 'negrilla margenSuperior');

    }

    if (!empty($objeto->fechaLimiteDcto2) && !empty($objeto->porcentajeDcto2)) {
        $pesosDctoExtra2    = ($totalFactura * $objeto->porcentajeDcto2) / 100;
        $codigo4           .= HTML::parrafo($textos->id('FECHA_LIMITE_PAGO_PARA_DESCUENTO') . ': ' . $objeto->fechaLimiteDcto2 . ' ' . $textos->id('PORCENTAJE_DESCUENTO') . ': ' . HTML::frase($objeto->porcentajeDcto2 . '%', 'sinNegrilla') . HTML::frase('$'.Recursos::formatearNumero($pesosDctoExtra2, '$'), 'sinNegrilla margenIzquierdaDoble'), 'negrilla margenSuperior');
    
    }    

    $codigo5  = HTML::parrafo($textos->id('SUBTOTAL') . ': $' . HTML::frase(Recursos::formatearNumero($objeto->subtotal, '$'), 'sinNegrilla titulo'), 'negrilla margenSuperior parrafoTotal');
    $codigo5 .= HTML::parrafo('', 'negrilla margenSuperior');
    $codigo5 .= HTML::parrafo('', 'negrilla margenSuperior');
    $codigo5 .= HTML::parrafo($textos->id('TOTAL') . '$' . HTML::frase(Recursos::formatearNumero($objeto->total, '$'), 'sinNegrilla letraAzul grande'), 'negrilla margenSuperiorDoble titulo parrafoTotal');

    $contenedor1 = HTML::contenedor($codigo1, 'contenedorIzquierdo');
    $contenedor2 = HTML::contenedor($codigo2, 'contenedorDerecho');
    $contenedor3 = HTML::contenedor($contenedorListaArticles, 'contenedorListadoArticulos');
    $contenedor4 = HTML::contenedor($codigo4, 'contenedorIzquierdo');
    $contenedor5 = HTML::contenedor($codigo5, 'contenedorDerecho');

    $pestana1 = $contenedor1 . $contenedor2 . $contenedor3 . $contenedor4 . $contenedor5;
    
    
    /**
     * NOTAS CREDITO DE LA FACTURA
     */
    $pestana2 = '';
    
    $notaCredito = new NotaCreditoCliente();
    
    $listaNotas = $notaCredito->listar($id);  
    
    foreach ($listaNotas as $lista) {
        $lista->botonConsultar = HTML::contenedor('', 'consultarRegistro');
    }

    $datosTabla = array(
        HTML::parrafo($textos->id('ID_FACTURA'), 'centrado')        => 'idFactura',
        HTML::parrafo($textos->id('CONCEPTO_NOTA'), 'centrado')     => 'conceptoNota',
        HTML::parrafo($textos->id('TOTAL_NOTA'), 'centrado')        => 'totalNota',
        HTML::parrafo($textos->id('FECHA_NOTA'), 'centrado')        => 'fechaNota',
        HTML::parrafo($textos->id('CONSULTAR'), 'centrado')         => 'botonConsultar',
    );    
    
    $rutas = array(
        'ruta_consultar' => '/ajax/facturas_venta/consultarNotaCredito',
    );    
    
    $idTabla                    = 'tablaListaNotasCredito';
    $clasesColumnas             = array('centrado', 'centrado', 'centrado', 'centrado');
    $tablaListaNotasCredito     = Recursos::generarTablaRegistrosInterna($listaNotas, $datosTabla, $rutas, $idTabla, $clasesColumnas);    
    
    $pestana2 = $tablaListaNotasCredito;
              
    /**
     * NOTAS DEBITO DE LA FACTURA
     */    
    $notaDebito = new NotaDebitoCliente();
    
    $listaNotas = $notaDebito->listar($id);  
    
    foreach ($listaNotas as $lista) {
        $lista->botonConsultar = HTML::contenedor('', 'consultarRegistro');
    }

    $datosTabla = array(
        HTML::parrafo($textos->id('ID_FACTURA'), 'centrado')        => 'idFactura',
        HTML::parrafo($textos->id('CONCEPTO_NOTA'), 'centrado')     => 'conceptoNota',
        HTML::parrafo($textos->id('TOTAL_NOTA'), 'centrado')        => 'totalNota',
        HTML::parrafo($textos->id('FECHA_NOTA'), 'centrado')        => 'fechaNota',
        HTML::parrafo($textos->id('CONSULTAR'), 'centrado')         => 'botonConsultar',
    );    
    
    $rutas = array(
        'ruta_consultar' => '/ajax/facturas_venta/consultarNotaDebito',
    );    
    
    $idTabla                   = 'tablaListaNotasDebito';
    $clasesColumnas            = array('centrado', 'centrado', 'centrado', 'centrado');
    $tablaListaNotasDebito     = Recursos::generarTablaRegistrosInterna($listaNotas, $datosTabla, $rutas, $idTabla, $clasesColumnas);    

    $pestana3 = $tablaListaNotasDebito;
    
   /**
     * ASIENTOS CONTABLES GENERADOS POR LA FACTURA
     */    
    $asientoContable = new AsientoContable();
    
    $tablaListaRegistroContable = $asientoContable->generarTablaRegistroContable("2", $id);

    $pestana4 = $tablaListaRegistroContable;    
    
    $pestanas = array(
        HTML::frase($textos->id('INFORMACION_FACTURA'), 'letraBlanca')      => $pestana1,
        HTML::frase($textos->id('NOTAS_CREDITO'), 'letraBlanca')            => $pestana2,
        HTML::frase($textos->id('NOTAS_DEBITO'), 'letraBlanca')             => $pestana3,
        HTML::frase($textos->id('REGISTRO_CONTABLE'), 'letraBlanca')        => $pestana4,
        
    );

    $codigo .= HTML::pestanas2('pestanasAgregar', $pestanas);   

    $respuesta['generar']       = true;
    $respuesta['codigo']        = $codigo;
    $respuesta['titulo']        = HTML::parrafo($textos->id('CONSULTAR_ITEM'), 'letraBlanca negrilla subtitulo');
    $respuesta['destino']       = '#cuadroDialogo';
    $respuesta['ancho']         = 800;
    $respuesta['alto']          = 600;

    Servidor::enviarJSON($respuesta);
    
}

/**
 * Funcion que muestra la ventana modal de consultar un articulo
 * 
 * @global type $textos
 * @param type $id = id del banco a consultar 
 */
function cosultarItem2($id) {
    global $textos, $sql;

    if (!isset($id) || (isset($id) && !$sql->existeItem('facturas_venta', 'id', $id))) {
        $respuesta = array();
        $respuesta['error'] = true;
        $respuesta['mensaje'] = $textos->id('NO_HA_SELECCIONADO_ITEM');

        Servidor::enviarJSON($respuesta);
        return NULL;
    }

    $objeto = new FacturaVenta($id);
    $respuesta = array();

    $codigo .= HTML::campoOculto('id', $id);

    $codigo1 = HTML::parrafo($textos->id('CLIENTE') . ': ' . HTML::frase($objeto->cliente, 'sinNegrilla'), 'negrilla margenSuperior');
    $codigo1 .= HTML::parrafo($textos->id('NIT') . ': ' . HTML::frase($objeto->idCliente, 'sinNegrilla'), 'negrilla margenSuperior');
    $codigo1 .= HTML::parrafo($textos->id('FECHA_FACTURA') . ': ' . HTML::frase($objeto->fechaFactura, 'sinNegrilla'), 'negrilla margenSuperior');
    $codigo1 .= HTML::parrafo($textos->id('USUARIO_QUE_FACTURA') . ': ' . HTML::frase($objeto->usuario, 'sinNegrilla'), 'negrilla margenSuperior');
    $codigo1 .= HTML::parrafo($textos->id('MODO_PAGO') . ': ' . HTML::frase($textos->id('MODO_PAGO' . $objeto->modoPago), 'sinNegrilla'), 'negrilla margenSuperior');
    $codigo1 .= HTML::parrafo('', 'negrilla margenSuperior');

    if ($objeto->modoPago == '2') {
        $codigo2 = HTML::parrafo($textos->id('FECHA_VENCIMIENTO') . ': ' . HTML::frase($objeto->fechaVtoFactura, 'sinNegrilla'), 'negrilla margenSuperior');
    }
    
    $codigo2 .= HTML::parrafo($textos->id('SEDE') . ': ' . HTML::frase($objeto->sede, 'sinNegrilla'), 'negrilla margenSuperior');
    $codigo2 .= HTML::parrafo($textos->id('VALOR_FLETE') . ': ' . HTML::frase(Recursos::formatearNumero($objeto->valorFlete, '$'), 'sinNegrilla'), 'negrilla margenSuperior');
    $codigo2 .= HTML::parrafo($textos->id('OBSERVACIONES') . ': ' . HTML::frase($objeto->observaciones, 'sinNegrilla'), 'negrilla margenSuperior');

    $datosTabla = array(
        HTML::frase($textos->id('PLU'), 'negrilla margenIzquierda'),
        HTML::frase($textos->id('ARTICULO'), 'negrilla margenIzquierda'),
        HTML::frase($textos->id('CANTIDAD'), 'negrilla margenIzquierda'),
        HTML::frase($textos->id('DESCUENTO'), 'negrilla margenIzquierda'),
        HTML::frase($textos->id('PRECIO'), 'negrilla margenIzquierda'),
        HTML::frase($textos->id('SUBTOTAL'), 'negrilla margenIzquierda')
    );

    $subtotalFactura = 0;

    $listaArticulos = array();
    foreach ($objeto->listaArticulos as $article) {
        unset($article->id);
        unset($article->idFactura);
        
        if (strlen($article->articulo) > 60) {
            $article->articulo = substr($article->articulo, 0, 60) . '.';
        }
        
        if ($article->descuento == 0 || $article->descuento == '0') {
            $article->subtotal = $article->cantidad * $article->precio;
            
        } else {
            $article->subtotal = ($article->cantidad * $article->precio) - ( ( ($article->cantidad * $article->precio) * $article->descuento) / 100 );
            
        }
        
        $article->descuento = Recursos::formatearNumero($article->descuento, '%', '0');
        
        $article->precio = Recursos::formatearNumero($article->precio, '$');
        
        $subtotalFactura += $article->subtotal;

        $article->subtotal = Recursos::formatearNumero($article->subtotal, '$');

        $listaArticulos[] = $article;
    }

    $subtotalFactura += $objeto->valorFlete;
    $impuestoIva = ($subtotalFactura * $objeto->iva) / 100;

    $subtotalFactura += $impuestoIva;

    $idTabla            = 'tablaListaArticulosConsulta';
    $clasesColumnas     = array('', '', '', '', '');
    $clasesFilas        = array('centrado', 'centrado', 'centrado', 'centrado', 'centrado', 'centrado');
    $opciones           = array('cellpadding' => '5', 'cellspacing' => '5', 'border' => '2');
    $clase              = 'tablaListaArticulosConsulta';
    
    $contenedorListaArticles    = HTML::tabla($datosTabla, $listaArticulos, $clase, $idTabla, $clasesColumnas, $clasesFilas, $opciones);


    $codigo4    = HTML::parrafo($textos->id('IVA') . HTML::frase($objeto->iva . '% ', 'sinNegrilla') . HTML::frase(Recursos::formatearNumero($impuestoIva, '$'), 'sinNegrilla margenIzquierdaDoble'), 'negrilla margenSuperior');
    $codigo4   .= HTML::parrafo($textos->id('DESCUENTOS') . ': ', 'negrilla margenSuperior letraVerde');

    $totalFactura = $subtotalFactura;

    if (!empty($objeto->concepto1) && !empty($objeto->descuento1)) {
        $pesosDcto1     = ($totalFactura * $objeto->descuento1) / 100;
        $totalFactura   = $totalFactura - $pesosDcto1;
        $codigo4       .= HTML::parrafo($objeto->concepto1 . ': ' . HTML::frase($objeto->descuento1 . '%', 'sinNegrilla') . HTML::frase(Recursos::formatearNumero($pesosDcto1, '$'), 'sinNegrilla margenIzquierdaDoble'), 'negrilla margenSuperior');
    }

    if (!empty($objeto->concepto2) && !empty($objeto->descuento2)) {
        $pesosDcto2     = ($totalFactura * $objeto->descuento2) / 100;
        $totalFactura   = $totalFactura - $pesosDcto2;
        $codigo4       .= HTML::parrafo($objeto->concepto2 . ': ' . HTML::frase($objeto->descuento2 . '%', 'sinNegrilla') . HTML::frase(Recursos::formatearNumero($pesosDcto2, '$'), 'sinNegrilla margenIzquierdaDoble'), 'negrilla margenSuperior');
    }

    if (!empty($objeto->fechaLimiteDcto1) && !empty($objeto->porcentajeDcto1)) {
        $pesosDctoExtra1    = ($totalFactura * $objeto->porcentajeDcto1) / 100;
        $codigo4           .= HTML::parrafo($textos->id('FECHA_LIMITE_PAGO_PARA_DESCUENTO') . ': ' . $objeto->fechaLimiteDcto1 . ' ' . $textos->id('PORCENTAJE_DESCUENTO') . ': ' . HTML::frase($objeto->porcentajeDcto1 . '%', 'sinNegrilla') . HTML::frase(Recursos::formatearNumero($pesosDctoExtra1, '$'), 'sinNegrilla margenIzquierdaDoble'), 'negrilla margenSuperior');
    }

    if (!empty($objeto->fechaLimiteDcto2) && !empty($objeto->porcentajeDcto2)) {
        $pesosDctoExtra2    = ($totalFactura * $objeto->porcentajeDcto2) / 100;
        $codigo4           .= HTML::parrafo($textos->id('FECHA_LIMITE_PAGO_PARA_DESCUENTO') . ': ' . $objeto->fechaLimiteDcto2 . ' ' . $textos->id('PORCENTAJE_DESCUENTO') . ': ' . HTML::frase($objeto->porcentajeDcto2 . '%', 'sinNegrilla') . HTML::frase(Recursos::formatearNumero($pesosDctoExtra2, '$'), 'sinNegrilla margenIzquierdaDoble'), 'negrilla margenSuperior');
    }

    $codigo5  = HTML::parrafo($textos->id('SUBTOTAL') . ': ' . HTML::frase(Recursos::formatearNumero($subtotalFactura, '$'), 'sinNegrilla titulo'), 'negrilla margenSuperior');
    $codigo5 .= HTML::parrafo('', 'negrilla margenSuperior');
    $codigo5 .= HTML::parrafo('', 'negrilla margenSuperior');
    $codigo5 .= HTML::parrafo($textos->id('TOTAL') . HTML::frase(Recursos::formatearNumero($totalFactura, '$'), 'sinNegrilla letraAzul grande'), 'negrilla margenSuperior titulo');
    $codigo5 .= HTML::parrafo($objeto->facturaDigital, 'negrilla margenSuperior');


    $contenedor1 = HTML::contenedor($codigo1, 'contenedorIzquierdo');
    $contenedor2 = HTML::contenedor($codigo2, 'contenedorDerecho');
    $contenedor3 = HTML::contenedor($contenedorListaArticles, 'contenedorListadoArticulos');
    $contenedor4 = HTML::contenedor($codigo4, 'contenedorIzquierdo');
    $contenedor5 = HTML::contenedor($codigo5, 'contenedorDerecho');

    $codigo .= $contenedor1 . $contenedor2 . $contenedor3 . $contenedor4 . $contenedor5;

    $respuesta['generar']   = true;
    $respuesta['codigo']    = $codigo;
    $respuesta['titulo']    = HTML::parrafo($textos->id('CONSULTAR_ITEM'), 'letraBlanca negrilla subtitulo');
    $respuesta['destino']   = '#cuadroDialogo';
    $respuesta['ancho']     = 800;
    $respuesta['alto']      = 600;

    Servidor::enviarJSON($respuesta);
    
}

/**
 * Función con doble comportamiento. La primera llamada (con el arreglo de datos vacio)
 * muestra el formulario con los datos del registro a ser modificado. El destino de este formulario es esta 
 * misma función, pero una vez viene desde el formulario con el arreglo datos cargado de valores
 * se encarga de validar la información y llamar al metodo modificar del objeto.
 * 
 * @global recurso $textos  = objeto global de gestion de los textos de idioma
 * @global recurso $sql     = objeto global de interaccion con la BD
 * @param int $id           = id del registro a modificar
 * @param array $datos      = arreglo con la informacion a adicionar
 */
function modificarItem($id) {
    global $textos, $sql, $configuracion, $modulo, $sesion_usuarioSesion;
    
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

    if (!isset($id) || (isset($id) && !$sql->existeItem('facturas_venta', 'id', $id))) {
        $respuesta = array();
        $respuesta['error'] = true;
        $respuesta['mensaje'] = $textos->id('NO_HA_SELECCIONADO_ITEM');

        Servidor::enviarJSON($respuesta);
        return NULL;
    }

    $objeto    = new FacturaVenta($id);
    $respuesta = array();

    $codigo  = HTML::campoOculto('id', $id);
    $codigo .= HTML::campoOculto('datos[dialogo]', '', 'idDialogo');

    $codigo1  = HTML::parrafo($textos->id('NUMERO_FACTURA') . HTML::frase($objeto->idFactura, 'sinNegrilla'), 'negrilla margenSuperior');
    $codigo1 .= HTML::parrafo($textos->id('CLIENTE') . ': ' . HTML::frase($objeto->cliente, 'sinNegrilla'), 'negrilla margenSuperior');
    $codigo1 .= HTML::parrafo($textos->id('NIT') . ': ' . HTML::frase($objeto->nitCliente, 'sinNegrilla'), 'negrilla margenSuperior');
    $codigo1 .= HTML::parrafo($textos->id('FECHA_FACTURA') . ': ' . HTML::frase($objeto->fechaFactura, 'sinNegrilla'), 'negrilla margenSuperior');
    $codigo1 .= HTML::parrafo($textos->id('NUMERO_FACTURA_CLIENTE') . ': ' . HTML::frase($objeto->idFactura, 'sinNegrilla'), 'negrilla margenSuperior');
    $codigo1 .= HTML::parrafo($textos->id('USUARIO_QUE_FACTURA') . ': ' . HTML::frase($objeto->usuario, 'sinNegrilla'), 'negrilla margenSuperior');

    $pestanas = array(
        HTML::frase($textos->id('INFORMACION_FACTURA'), 'letraBlanca') => $codigo1
    );

    $puedeModificarFecha = Perfil::verificarPermisosBoton('botonModificarFechaFacturaV');

    $codigo3 = '';
    $codigo3 .= HTML::parrafo($textos->id('FECHA_FACTURA'), 'margenSuperior negrilla');
    $fechaFact = explode(' ', $objeto->fechaFactura);
    $codigo3 .= HTML::campoTexto('fechaFactura', 12, 12, $fechaFact[0], 'fechaAntigua', '', array('ayuda' => $textos->id('SELECCIONE_NUEVA_FECHA_FACTURA')));
    $destino3 = '/ajax/facturas_venta/modificarFechaFactura';
    $codigo3 .= HTML::campoOculto('idFactura', $id, 'ocultoIdFactura');
    $codigo3 .= HTML::parrafo(HTML::boton('documentoNuevo', $textos->id('MODIFICAR_FECHA_FACTURA'), 'botonOk margenSuperior', 'botonOk', 'botonOk'));
    $codigo_3 = HTML::forma($destino3, $codigo3, 'P', '', '');

    if ($puedeModificarFecha) {
        $pestanas[HTML::frase($textos->id('MODIFICAR_FECHA_FACTURA'), 'letraBlanca')] = $codigo_3;
    }

    $codigo .= HTML::pestanas2('pestanasModificar', $pestanas);


    $destino = $configuracion['SERVIDOR']['principal'] . 'ventas_mercancia';
    $contenido = HTML::campoOculto('idFactura', $id, 'ocultoIdFactura');
    $contenido .= HTML::boton('lapiz', HTML::frase($textos->id('MODIFICAR_ITEM_DESDE_FORMULARIO'), 'subtitulo'), 'botonOk directo margenSuperiorTriple margenSuperior100 margenIzquierda150', 'botonOk', 'botonOk');
    $formaModificarFactura = HTML::forma($destino, $contenido, 'P', '', '', array('target' => '_blank'));

    $textoExitoso = HTML::frase($textos->id('REGISTRO_MODIFICADO'), 'textoExitoso margenIzquierda', 'textoExitoso');
    $codigo .= HTML::parrafo($formaModificarFactura . $textoExitoso, 'margenSuperior');


    $respuesta['generar']   = true;
    $respuesta['codigo']    = $codigo;
    $respuesta['titulo']    = HTML::parrafo($textos->id('MODIFICAR_ITEM'), 'letraBlanca negrilla subtitulo');
    $respuesta['destino']   = '#cuadroDialogo';
    $respuesta['ancho']     = 790;
    $respuesta['alto']      = 580;


    Servidor::enviarJSON($respuesta);
    
}

/**
 * Función con doble comportamiento. La primera llamada (con el parametro $confirmado vacio)
 * muestra el formulario de confirmación de eliminación del registro. El destino de este formulario es esta 
 * misma función, pero una vez viene desde el formulario con el parametro $confirmado en "true"
 * se encarga de validar la información y llamar al metodo modificar estado del objeto.
 * 
 * nota: no se debe eliminar ninguna factura del sistema, solo inactivar.
 * 
 * @global recurso $textos  = objeto global de gestion de los textos de idioma
 * @global recurso $sql     = objeto global de interaccion con la BD
 * @param int $id           = id del registro a modificar
 * @param array $datos      = arreglo con la informacion a adicionar
 */
function eliminarItem($id, $confirmado, $dialogo) {
    global $textos, $sql, $modulo, $sesion_usuarioSesion;
        
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

    $objeto     = new FacturaVenta($id);
    $destino    = '/ajax' . $objeto->urlBase . '/delete';
    $respuesta  = array();

    if (!$confirmado) {
        //query para verificar si se activa o se inactiva la factura dependiendo del estado
        $activo = $sql->obtenerValor('facturas_venta', 'activo', 'id = "' . $id . '"');
        
        $pregunta = ($activo == "1") ? 'CONFIRMAR_INACTIVACION' : 'CONFIRMAR_ACTIVACION';
        
        $titulo  = HTML::frase($objeto->idFactura, 'negrilla');
        $titulo1 = str_replace('%1', $titulo, $textos->id($pregunta));
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
        $respuesta['titulo']    = HTML::parrafo($textos->id('ACTIVAR_INACTIVAR_ITEM'), 'letraBlanca negrilla subtitulo');
        $respuesta['ancho']     = 350;
        $respuesta['alto']      = 150;
        
    } else {

        if ($objeto->inactivar()) {

               /*                 * ************** Creo el nuevo item que se insertara via ajax *************** */
                $objeto     = new FacturaVenta($id);

                $estado     = ($objeto->activo) ? HTML::frase($textos->id('ACTIVO'), 'activo') : HTML::frase($textos->id('INACTIVO'), 'inactivo');

                $celdas     = array($objeto->id, $objeto->sede, $objeto->objCliente->id, $objeto->objCliente->nombre, $objeto->usuario, $objeto->fechaFactura, $estado);
                $celdas1    = HTML::crearFilaAModificar($celdas);

                $respuesta['error']         = false;
                $respuesta['accion']        = 'insertar';
                $respuesta['contenido']     = $celdas1;
                $respuesta['idContenedor']  = '#tr_' . $id;
                $respuesta['idDestino']     = '#tr_' . $id;

                if ($dialogo == '') {
                    $respuesta['modificarFilaTabla'] = true;
                    
                } else {
                    $respuesta['modificarFilaDialogo']  = true;
                    $respuesta['ventanaDialogo']        = $dialogo;
                }
            
        } else {
            $respuesta['mensaje'] = $textos->id('ERROR_DESCONOCIDO');
            
        }
        
    }

    Servidor::enviarJSON($respuesta);
    
}
  /* function eliminarItem($id, $confirmado, $dialogo) {
    global $textos;

    $objeto     = new FacturaVenta($id);
    $destino    = '/ajax' . $objeto->urlBase . '/delete';
    $respuesta  = array();

    if (!$confirmado) {
        $titulo  = HTML::frase($objeto->idFactura, 'negrilla');
        $titulo1 = str_replace('%1', $titulo, $textos->id('CONFIRMAR_INACTIVACION'));
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

        if ($objeto->inactivar()) {

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
    
}*/
   
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
        $objeto = new FacturaVenta();
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

        $arregloItems = $objeto->listar($registroInicial, $registros, array('0'), $condicion, 'c.nombre');

        if ($objeto->registrosConsulta) {//si la consulta trajo registros
            $datosPaginacion = array($objeto->registrosConsulta, $registroInicial, $registros, $pagina, $objeto->registrosConsulta);
            $item .= $objeto->generarTabla($arregloItems, $datosPaginacion);
            $info = HTML::parrafo('Tu busqueda trajo ' . $objeto->registrosConsulta . ' resultados', 'textoExitosoNotificaciones');
        } else {
            $datosPaginacion = 0;
            $item .= $objeto->generarTabla($textos->id('NO_HAY_REGISTROS'), $datosPaginacion);
            $info = HTML::parrafo('Tu busqueda no trajo resultados, por favor intenta otra busqueda', 'textoErrorNotificaciones');
        }

        $respuesta['error']         = false;
        $respuesta['accion']        = 'insertar';
        $respuesta['contenido']     = $item;
        $respuesta['idContenedor']  = '#tablaRegistros';
        $respuesta['idDestino']     = '#contenedorTablaRegistros';
        $respuesta['paginarTabla']  = true;
        $respuesta['info']          = $info;
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

    $item = '';
    $respuesta = array();
    $objeto = new FacturaVenta();

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
 * Función que permite modificar la fecha a una factura de venta en particular.
 * 
 * @global type $textos
 * @global type $sql
 * @global type $archivo_archivo
 * @param type $id
 * @return null 
 */
function modificarFechaFactura($id, $fechaFactura) {
    global $textos, $sql;

    if (!isset($id) || ( isset($id) && !$sql->existeItem('facturas_venta', 'id', $id) )) {
        $respuesta = array();
        $respuesta['error'] = true;
        $respuesta['mensaje'] = $textos->id('NO_HA_SELECCIONADO_ITEM');

        Servidor::enviarJSON($respuesta);
        return NULL;
    }

    $objeto = new FacturaVenta();

    $recurso = $objeto->modificarFechaFactura($id, $fechaFactura);
    $respuesta = array();

    if ($recurso) {
        $respuesta['mensaje'] = $textos->id('FECHA_MODIFICADA_CORRECTAMENTE');
        $respuesta['textoExito'] = true;
        $respuesta['recargarTablaRegistros'] = true;
    } else {
        $respuesta['mensaje'] = $textos->id('ERROR_AL_MODIFICAR_FECHA');
    }

    $respuesta['error'] = true;

    Servidor::enviarJSON($respuesta);
}

/**
 *
 * @global type $textos
 * @global type $sql
 * @global type $archivo_imagen
 * @param type $datos 
 */
function buscarFactura() {
    global $textos;

    $destino    = '/ajax/ventas_mercancia/buscarFactura';
    $respuesta  = array();
    
    $codigo = '';
    $codigo .= HTML::campoOculto('datos[dialogo]', '', 'idDialogo');
    $codigo .= HTML::parrafo($textos->id('CLIENTE'), 'negrilla margenSuperior');
    $codigo .= HTML::campoTexto('datos[id_cliente]', 40, 255, '', 'autocompletable campoObligatorio', 'campoIdCliente', array('title' => '/ajax/clientees/listar'), $textos->id('AYUDA_USO_AUTOCOMPLETAR'), '/ajax/clientees/add');
    $codigo .= HTML::parrafo($textos->id('FECHA_INICIAL'), 'margenSuperior negrilla');
    $codigo .= HTML::campoTexto('datos[anyo_factura_i]', 4, 4, '', 'soloNumeros', 'campoAnyoFactura', '');
    $codigo .= HTML::campoTexto('datos[mes_factura_i]', 2, 2, '', 'soloNumeros medioMargenIzquierda', 'campoMesFactura', '');
    $codigo .= HTML::campoTexto('datos[dia_factura_i]', 2, 2, '', 'soloNumeros medioMargenIzquierda', 'campoDiaFactura', '');
    $codigo .= HTML::parrafo($textos->id('FECHA_FINAL'), 'margenSuperior negrilla');
    $codigo .= HTML::campoTexto('datos[anyo_factura_f]', 4, 4, date('Y'), 'soloNumeros', 'campoAnyoFactura', '');
    $codigo .= HTML::campoTexto('datos[mes_factura_f]', 2, 2, date('m'), 'soloNumeros medioMargenIzquierda', 'campoMesFactura', '');
    $codigo .= HTML::campoTexto('datos[dia_factura_f]', 2, 2, date('d'), 'soloNumeros medioMargenIzquierda', 'campoDiaFactura', '');
    $codigo .= HTML::parrafo(HTML::boton('chequeo', $textos->id('ACEPTAR'), 'directo', '', 'tablaBusquedaFacturas', '', array('validar' => 'NoValidar')), 'margenSuperior');
    $codigo1 = HTML::forma($destino, $codigo, 'P', true);

    $respuesta['generar']       = true;
    $respuesta['codigo']        = $codigo1;
    $respuesta['titulo']        = HTML::parrafo($textos->id('BUSCAR_FACTURAS'), 'letraBlanca negrilla subtitulo');
    $respuesta['destino']       = '#cuadroDialogo';
    $respuesta['ancho']         = 450;
    $respuesta['alto']          = 250;


    Servidor::enviarJSON($respuesta);
}

/**
 * Funcion Eliminar varios. llamada cuando se seleccionan varios registros y se presiona el
 * botón que aparece llamado "Eliminar varios"
 * 
 * @global boolean $confirmado  = objeto global de gestion de textos
 * @param int $cantidad         = cantidad a ser eliminada
 * @param string $cadenaItems   = cadena que tiene cada uno de los ides del objeto a ser eliminados, ejemplo se eliminan el objeto de id 1, 2, 3, la cadena sería (1,2,3)
 */
/*function eliminarVarios($confirmado, $cantidad, $cadenaItems) {
    global $textos;

    $destino    = '/ajax/facturas_venta/eliminarVarios';
    $respuesta  = array();

    if (!$confirmado) {
        $titulo  = HTML::frase($cantidad, 'negrilla');
        $titulo1 = str_replace('%1', $titulo, $textos->id('CONFIRMAR_ELIMINACION_VARIOS'));
        $codigo  = HTML::campoOculto('procesar', 'true');
        $codigo .= HTML::campoOculto('cadenaItems', $cadenaItems, 'cadenaItems');
        $codigo .= HTML::parrafo($titulo1);
        $codigo .= HTML::parrafo(HTML::boton('chequeo', $textos->id('ACEPTAR'), '', 'botonOk', 'botonOk'), 'margenSuperior');
        $codigo .= HTML::parrafo($textos->id('REGISTRO_ELIMINADO'), 'textoExitoso', 'textoExitoso');
        $codigo1 = HTML::forma($destino, $codigo);

        $respuesta['generar']       = true;
        $respuesta['codigo']        = $codigo1;
        $respuesta['destino']       = '#cuadroDialogo';
        $respuesta['titulo']        = HTML::parrafo($textos->id('ELIMINAR_VARIOS_REGISTROS'), 'letraBlanca negrilla subtitulo');
        $respuesta['ancho']         = 350;
        $respuesta['alto']          = 150;
        
    } else {

        $cadenaIds  = substr($cadenaItems, 0, -1);
        $arregloIds = explode(',', $cadenaIds);

        $eliminarVarios = true;
        
        foreach ($arregloIds as $val) {
            $objeto         = new FacturaVenta($val);
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
}*/

/**
 * Funcion que muestra la información de una nota credito
 * 
 * 
 * @global type $textos
 * @param type $id 
 */
function consultarNotaCredito($id) {
    global $textos, $sql, $sesion_configuracionGlobal;

    if (!isset($id) || (isset($id) && !$sql->existeItem('notas_credito_clientes', 'id', $id))) {
        $respuesta                  = array();
        $respuesta['error']         = true;
        $respuesta['mensaje']       = $textos->id('NO_HA_SELECCIONADO_ITEM');

        Servidor::enviarJSON($respuesta);
        return false;
        
    }
    
    $objeto     = new NotaCreditoCliente($id);
    

    $respuesta = array();

    $codigo  = '';

    $codigo1  = HTML::parrafo($textos->id('NUMERO_FACTURA_CLIENTE') . ': ' . HTML::frase((int)$objeto->idFactura, 'sinNegrilla subtitulo flotanteDerecha margenDerecha'), 'negrilla margenSuperior espacioInferior10 bordeInferior margenDerechaTriple');
    $codigo1 .= HTML::parrafo($textos->id('CANTIDAD_DINERO_NOTA') . ': ' . HTML::frase('$ '.Recursos::formatearNumero($objeto->montoNota, '$'), 'sinNegrilla subtitulo flotanteDerecha margenDerecha'), 'negrilla margenSuperiorDoble bordeInferior espacioInferior15 margenDerechaTriple');
    $codigo1 .= HTML::parrafo($textos->id('CANTIDAD_IVA_NOTA') . ': ' . HTML::frase('$ '.Recursos::formatearNumero($objeto->ivaNota, '$'), 'sinNegrilla subtitulo flotanteDerecha margenDerecha'), 'negrilla margenSuperiorDoble bordeInferior espacioInferior15 margenDerechaTriple');
    $codigo1 .= HTML::parrafo($textos->id('CANTIDAD_TOTAL_NOTA') . ': ' . HTML::frase('$ '.Recursos::formatearNumero($objeto->totalNota, '$'), 'sinNegrilla subtitulo flotanteDerecha margenDerecha'), 'negrilla margenSuperiorDoble bordeInferior espacioInferior15 margenDerechaTriple');


    $codigo2 = HTML::parrafo($textos->id('CONCEPTO_NOTA'), 'negrilla margenSuperior');
    $codigo2 .= HTML::parrafo($objeto->conceptoNota, 'sinNegrilla subtitulo margenDerecha');
    $codigo2 .= HTML::parrafo($textos->id('FECHA_NOTA') . HTML::frase($objeto->fechaNota, 'sinNegrilla subtitulo  margenIzquierda'), 'negrilla margenSuperior');

    //verificar el identificador escogido para los articulos en la configuracion global y usarlo para mostrar los datos
    $idPrincipalArticulo = (string)$sesion_configuracionGlobal->idPrincipalArticulo;
    $arrayIdArticulo     = array('id' => $textos->id('ID_AUTOMATICO'), 'codigo_oem' => $textos->id('CODIGO_OEM'), 'plu_interno' => $textos->id('PLU'));        


    $datosTabla = array(
        HTML::frase($textos->id($arrayIdArticulo[$idPrincipalArticulo]), 'negrilla margenIzquierda'),
        HTML::frase($textos->id('ARTICULO'), 'negrilla margenIzquierda'),
        HTML::frase($textos->id('CANTIDAD_ANTERIOR'), 'negrilla margenIzquierda'),
        HTML::frase($textos->id('CANTIDAD_NUEVA'), 'negrilla margenIzquierda'),
    );


    $listaArticulos = array();
    /**
     * Recorro el listado de articulos, y voy creando objetos en vivo para agregar al arreglo de objetos
     * para ver el listado exacto de los articulos que se cargan ver la clase FacturaVenta 
     */
    foreach ($objeto->listaArticulos as $article) {
        //declaro un nuevo objeto vacio para poder armar la tabla
        $object = new stdClass();

        $object->plu        = $article->articulo->$idPrincipalArticulo;
        $object->articulo   = $article->articulo->nombre;
        $object->cantidadA   = $article->cantidadAnterior;
        $object->cantidadN   = $article->cantidadNueva;


        if (strlen($object->articulo) > 60) {
            $object->articulo = substr($object->articulo, 0, 60) . '.';
        }

        $listaArticulos[] = $object;
    }
   

    $idTabla            = 'tablaListaArticulosConsulta';
    $clasesColumnas     = array('', '', '', '', '');
    $clasesFilas        = array('centrado', 'centrado', 'centrado', 'centrado', 'centrado', 'centrado');
    $opciones           = array('cellpadding' => '5', 'cellspacing' => '5', 'border' => '1');
    $clase              = 'tablaListaArticulosConsulta';

    $contenedorListaArticles = HTML::tabla($datosTabla, $listaArticulos, $clase, $idTabla, $clasesColumnas, $clasesFilas, $opciones);


    $claseListaArticulos = 'oculto';
    
    if ($objeto->inventarioModificado) {
        $claseListaArticulos = '';
    }

    $contenedor1 = HTML::contenedor($codigo1, 'contenedorIzquierdo margenInferiorDoble');
    $contenedor2 = HTML::contenedor($codigo2, 'contenedorDerecho margenInferiorDoble');
    $contenedor3 = HTML::contenedor($contenedorListaArticles, 'contenedorListadoArticulos '.$claseListaArticulos, 'contenedorListaArticulosNotaC');


    $codigo .= $contenedor1 . $contenedor2 . $contenedor3;


    $respuesta['generar']       = true;
    $respuesta['codigo']        = $codigo;
    $respuesta['titulo']        = HTML::parrafo($textos->id('CONSULTAR_NOTA_CREDITO'), 'letraBlanca negrilla subtitulo');
    $respuesta['destino']       = '#cuadroDialogo';
    $respuesta['ancho']         = 800;
    $respuesta['alto']          = 570;


    Servidor::enviarJSON($respuesta);
    
}


/**
 * Funcion que muestra la información de una nota debito
 * 
 * 
 * @global type $textos
 * @param type $id 
 */
function consultarNotaDebito($id) {
    global $textos, $sql, $sesion_configuracionGlobal;

    if (!isset($id) || (isset($id) && !$sql->existeItem('notas_debito_clientes', 'id', $id))) {
        $respuesta                  = array();
        $respuesta['error']         = true;
        $respuesta['mensaje']       = $textos->id('NO_HA_SELECCIONADO_ITEM');

        Servidor::enviarJSON($respuesta);
        return false;
        
    }
    
    $objeto     = new NotaDebitoCliente($id);
    

    $respuesta = array();

    $codigo  = '';

    $codigo1  = HTML::parrafo($textos->id('NUMERO_FACTURA_CLIENTE') . ': ' . HTML::frase((int)$objeto->idFactura, 'sinNegrilla subtitulo flotanteDerecha margenDerecha'), 'negrilla margenSuperior espacioInferior10 bordeInferior margenDerechaTriple');
    $codigo1 .= HTML::parrafo($textos->id('CANTIDAD_DINERO_NOTA') . ': ' . HTML::frase('$ '.Recursos::formatearNumero($objeto->montoNota, '$'), 'sinNegrilla subtitulo flotanteDerecha margenDerecha'), 'negrilla margenSuperiorDoble bordeInferior espacioInferior15 margenDerechaTriple');
    $codigo1 .= HTML::parrafo($textos->id('CANTIDAD_IVA_NOTA') . ': ' . HTML::frase('$ '.Recursos::formatearNumero($objeto->ivaNota, '$'), 'sinNegrilla subtitulo flotanteDerecha margenDerecha'), 'negrilla margenSuperiorDoble bordeInferior espacioInferior15 margenDerechaTriple');
    $codigo1 .= HTML::parrafo($textos->id('CANTIDAD_TOTAL_NOTA') . ': ' . HTML::frase('$ '.Recursos::formatearNumero($objeto->totalNota, '$'), 'sinNegrilla subtitulo flotanteDerecha margenDerecha'), 'negrilla margenSuperiorDoble bordeInferior espacioInferior15 margenDerechaTriple');


    $codigo2 = HTML::parrafo($textos->id('CONCEPTO_NOTA'), 'negrilla margenSuperior');
    $codigo2 .= HTML::parrafo($objeto->conceptoNota, 'sinNegrilla subtitulo margenDerecha');
    $codigo2 .= HTML::parrafo($textos->id('FECHA_NOTA') . HTML::frase($objeto->fechaNota, 'sinNegrilla subtitulo  margenIzquierda'), 'negrilla margenSuperior');

    //verificar el identificador escogido para los articulos en la configuracion global y usarlo para mostrar los datos
    $idPrincipalArticulo = (string)$sesion_configuracionGlobal->idPrincipalArticulo;
    $arrayIdArticulo     = array('id' => $textos->id('ID_AUTOMATICO'), 'codigo_oem' => $textos->id('CODIGO_OEM'), 'plu_interno' => $textos->id('PLU'));        

    $datosTabla = array(
        HTML::frase($textos->id($arrayIdArticulo[$idPrincipalArticulo]), 'negrilla margenIzquierda'),
        HTML::frase($textos->id('ARTICULO'), 'negrilla margenIzquierda'),
        HTML::frase($textos->id('CANTIDAD_ANTERIOR'), 'negrilla margenIzquierda'),
        HTML::frase($textos->id('CANTIDAD_NUEVA'), 'negrilla margenIzquierda'),
    );

    $listaArticulos = array();
    /**
     * Recorro el listado de articulos, y voy creando objetos en vivo para agregar al arreglo de objetos
     * para ver el listado exacto de los articulos que se cargan ver la clase FacturaVenta 
     */
    foreach ($objeto->listaArticulos as $article) {
        //declaro un nuevo objeto vacio para poder armar la tabla
        $object = new stdClass();

        $object->plu        = $article->articulo->$idPrincipalArticulo;
        $object->articulo   = $article->articulo->nombre;
        $object->cantidadA   = $article->cantidadAnterior;
        $object->cantidadN   = $article->cantidadNueva;


        if (strlen($object->articulo) > 60) {
            $object->articulo = substr($object->articulo, 0, 60) . '.';
        }

        $listaArticulos[] = $object;
    }
   
    $idTabla            = 'tablaListaArticulosConsulta';
    $clasesColumnas     = array('', '', '', '', '');
    $clasesFilas        = array('centrado', 'centrado', 'centrado', 'centrado', 'centrado', 'centrado');
    $opciones           = array('cellpadding' => '5', 'cellspacing' => '5', 'border' => '1');
    $clase              = 'tablaListaArticulosConsulta';

    $contenedorListaArticles = HTML::tabla($datosTabla, $listaArticulos, $clase, $idTabla, $clasesColumnas, $clasesFilas, $opciones);


    $claseListaArticulos = 'oculto';
    
    if ($objeto->inventarioModificado) {
        $claseListaArticulos = '';
    }

    $contenedor1 = HTML::contenedor($codigo1, 'contenedorIzquierdo margenInferiorDoble');
    $contenedor2 = HTML::contenedor($codigo2, 'contenedorDerecho margenInferiorDoble');
    $contenedor3 = HTML::contenedor($contenedorListaArticles, 'contenedorListadoArticulos '.$claseListaArticulos, 'contenedorListaArticulosNotaC');

    $codigo .= $contenedor1 . $contenedor2 . $contenedor3;

    $respuesta['generar']       = true;
    $respuesta['codigo']        = $codigo;
    $respuesta['titulo']        = HTML::parrafo($textos->id('CONSULTAR_NOTA_DEBITO'), 'letraBlanca negrilla subtitulo');
    $respuesta['destino']       = '#cuadroDialogo';
    $respuesta['ancho']         = 800;
    $respuesta['alto']          = 570;


    Servidor::enviarJSON($respuesta);
    
}

/**
 * Funcion que genera el formulario para introducir una  una nota credito enviada por un cliente sobre una factura de venta realizada
 * 
 * @global type $textos
 * @param type $id 
 */
function adicionarNotaCredito($id, $datos = array()) {
    global $textos, $sql, $configuracion, $sesion_configuracionGlobal, $modulo, $sesion_usuarioSesion;

    /**
    * Verificar si el usuario que esta en la sesion tiene permisos para esta accion
    */
    $puedeAgregarNotaCredito = Perfil::verificarPermisosBoton('botonAdicionarNotaCreditoVenta',$modulo->nombre);
    
    if(!$puedeAgregarNotaCredito && $sesion_usuarioSesion->id != 0) {
        $respuesta            = array();
        $respuesta['error']   = true;
        $respuesta['mensaje'] = $textos->id('ACCESO_DENEGADO');
        
        Servidor::enviarJSON($respuesta);
        return FALSE;
        
    }

    if (!isset($id) || (isset($id) && !$sql->existeItem('facturas_venta', 'id', $id))) {
        $respuesta                  = array();
        $respuesta['error']         = true;
        $respuesta['mensaje']       = $textos->id('NO_HA_SELECCIONADO_ITEM');

        Servidor::enviarJSON($respuesta);
        return false;
        
    }
    
    $objeto     = new FacturaVenta($id);
    $destino    = '/ajax/facturas_venta/adicionarNotaCredito';

    if (empty($datos)) {
        $respuesta = array();

        $codigo  = HTML::campoOculto('procesar', 'true');
        $codigo .= HTML::campoOculto('id', $id);
        $codigo .= HTML::campoOculto('datos[dialogo]', '', 'idDialogo');
        $codigo .= HTML::campoOculto('datos[id_factura]', $id, '');

        
        $codigo1  = HTML::parrafo($textos->id('CANTIDAD_DINERO_NOTA') . ': ' . HTML::campoTexto('datos[monto_nota]', 10, 15, '', 'flotanteDerecha campoObligatorio2 campoDinero soloNumeros', 'campoMontoNota'), 'negrilla margenSuperiorDoble bordeInferior espacioInferior15 margenDerechaTriple');
        $codigo1 .= HTML::parrafo($textos->id('CANTIDAD_IVA_NOTA') . ': ' . HTML::campoTexto('datos[iva_nota]', 10, 15, '', 'flotanteDerecha campoObligatorio2 campoDinero soloNumeros', 'campoIvaNota'), 'negrilla margenSuperiorDoble bordeInferior espacioInferior15 margenDerechaTriple');
        $codigo1 .= HTML::parrafo($textos->id('CANTIDAD_TOTAL_NOTA') . ': ' . HTML::campoTexto('datos[total_nota]', 10, 15, '', 'flotanteDerecha campoObligatorio2 campoDinero letraNegra', 'campoTotalNota', array('disabled' => 'disabled')), 'negrilla margenSuperiorDoble bordeInferior espacioInferior15 margenDerechaTriple');
        $codigo1 .= HTML::parrafo($textos->id('AFECTAR_CANTIDADES_INVENTARIO') . HTML::campoChequeo('datos[inventario_modificado]', false, 'chkModInventario margenIzquierda', 'chkModInventario'), 'negrilla margenSuperior');


        $codigo2 = HTML::parrafo($textos->id('CONCEPTO_NOTA'), 'negrilla margenSuperior');
        $codigo2 .= HTML::areaTexto('datos[concepto_nota]', 4, 50, '', 'txtAreaConceptoNotaC campoObligatorio', 'txtAreaConceptoNotaC');
        $codigo2 .= HTML::parrafo($textos->id('FECHA_NOTA') . HTML::campoTexto('datos[fecha_nota]', 12, 12, '', 'fechaAntigua campoCalendario', '', array('ayuda' => $textos->id('SELECCIONE_FECHA_NOTA'))), 'negrilla margenSuperior');

        //verificar el identificador escogido para los articulos en la configuracion global y usarlo para mostrar los datos
        $idPrincipalArticulo = (string)$sesion_configuracionGlobal->idPrincipalArticulo;
        $arrayIdArticulo     = array('id' => $textos->id('ID_AUTOMATICO'), 'codigo_oem' => $textos->id('CODIGO_OEM'), 'plu_interno' => $textos->id('PLU'));        
        

        $datosTabla = array(
            HTML::frase($textos->id($arrayIdArticulo[$idPrincipalArticulo]), 'negrilla margenIzquierda'),
            HTML::frase($textos->id('ARTICULO'), 'negrilla margenIzquierda'),
            HTML::frase($textos->id('CANTIDAD'), 'negrilla margenIzquierda'),
            HTML::frase($textos->id('CANTIDAD_A_MODIFICAR'), 'negrilla margenIzquierdaDoble'),
        );


        $listaArticulos = array();
        /**
         * Recorro el listado de articulos, y voy creando objetos en vivo para agregar al arreglo de objetos
         * para ver el listado exacto de los articulos que se cargan ver la clase FacturaVenta 
         */
        foreach ($objeto->listaArticulos as $article) {
            //declaro un nuevo objeto vacio para poder armar la tabla
            $object = new stdClass();
            
            $object->plu        = $article->$idPrincipalArticulo;
            $object->articulo   = $article->articulo;
            $object->cantidad   = $article->cantidad;
            
            
            $idReg = (int) $article->id ;

            if (strlen($object->articulo) > 60) {
                $object->articulo = substr($object->articulo, 0, 60) . '.';
            }
            /**
             *Aqui se añade un campo de texto que contiene la cantidad de cada uno de los articulos
             * notese que en "valor" se concatena la cantidad del articulo, estos datos son usados 
             * en el metodo encargado de hacer el registro
             */
            $datosArticulo =  $object->cantidad . '_' . (int)$article->idArticulo . '_' . (int)$article->idBodega;
            $object->nuevaCantidad = HTML::campoTexto('datos[nueva_cantidad][' . $datosArticulo . ']', 5, 10, $object->cantidad, 'margenIzquierdaDoble rangoNumeros', $idReg, array("rango" => "1-".$object->cantidad.""));
            $listaArticulos[] = $object;
        }


        $idTabla            = 'tablaListaArticulosConsulta';
        $clasesColumnas     = array('', '', '', '', '');
        $clasesFilas        = array('centrado', 'centrado', 'centrado', 'centrado', 'centrado', 'centrado');
        $opciones           = array('cellpadding' => '5', 'cellspacing' => '5', 'border' => '1');
        $clase              = 'tablaListaArticulosConsulta';
        
        $contenedorListaArticles = HTML::tabla($datosTabla, $listaArticulos, $clase, $idTabla, $clasesColumnas, $clasesFilas, $opciones);



        $contenedor1 = HTML::contenedor($codigo1, 'contenedorIzquierdo margenInferiorDoble');
        $contenedor2 = HTML::contenedor($codigo2, 'contenedorDerecho margenInferiorDoble');
        $contenedor3 = HTML::contenedor($contenedorListaArticles, 'contenedorListadoArticulos oculto', 'contenedorListaArticulosNotaC');


        $codigo .= $contenedor1 . $contenedor2 . $contenedor3;
        $codigo .= HTML::parrafo(HTML::boton('chequeo', $textos->id('ACEPTAR'), ' margenSuperiorTriple', 'botonOk', 'botonOk'), 'margenSuperiorTriple');
        $codigo .= HTML::parrafo($textos->id('NOTA_CREDITO_ADICIONADA_A_FACTURA'), 'textoExitoso', 'textoExitoso');
        $codigo1 = HTML::forma($destino, $codigo, 'P');


        $respuesta['generar']       = true;
        $respuesta['cargarJs']      = true;
        $respuesta['archivoJs']     = $configuracion['SERVIDOR']['media'] . $configuracion['RUTAS']['javascript'] . '/modulos/facturas_venta/funcionesNotaCredito.js';
        $respuesta['codigo']        = $codigo1;
        $respuesta['titulo']        = HTML::parrafo($textos->id('INGRESAR_NOTA_CREDITO'), 'letraBlanca negrilla subtitulo');
        $respuesta['destino']       = '#cuadroDialogo';
        $respuesta['ancho']         = 800;
        $respuesta['alto']          = 570;
        
    } else {
        
        $objeto     = new NotaCreditoCliente($id);

        $respuesta['error'] = true;

        if (empty($datos['monto_nota'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_MONTO_NOTA');
            
        } elseif (empty($datos['iva_nota'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_IVA_NOTA');
            
        } elseif (empty($datos['concepto_nota'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_CONCEPTO_NOTA');
            
        } else {
            $idItem = $objeto->adicionar($datos);
            
            if ($idItem) {
                $respuesta['error']             = false;
                $respuesta['accion']            = 'insertar';
                $respuesta['insertarAjax']      = true;
                $respuesta['mostrarNotificacionDinamica']   = true;

                
                if ($datos['dialogo'] != '') {
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
 * Funcion que genera el formulario para introducir una  una nota débito enviada por un cliente sobre una factura de venta realizada
 * 
 * @global type $textos
 * @param type $id 
 */
function adicionarNotaDebito($id, $datos) {
    global $textos, $sql, $configuracion, $sesion_configuracionGlobal, $modulo, $sesion_usuarioSesion;
    
    /**
    * Verificar si el usuario que esta en la sesion tiene permisos para esta accion
    */
    $puedeAgregarNotaDebito = Perfil::verificarPermisosBoton('botonAdicionarNotaDebitoVenta',$modulo->nombre);
    
    if(!$puedeAgregarNotaDebito && $sesion_usuarioSesion->id != 0) {
        $respuesta            = array();
        $respuesta['error']   = true;
        $respuesta['mensaje'] = $textos->id('ACCESO_DENEGADO');
        
        Servidor::enviarJSON($respuesta);
        return FALSE;
        
    }

    if (!isset($id) || (isset($id) && !$sql->existeItem('facturas_venta', 'id', $id))) {
        $respuesta                  = array();
        $respuesta['error']         = true;
        $respuesta['mensaje']       = $textos->id('NO_HA_SELECCIONADO_ITEM');

        Servidor::enviarJSON($respuesta);
        return false;
        
    }
    
    $objeto     = new FacturaVenta($id);
    $destino    = '/ajax/facturas_venta/adicionarNotaDebito';

    if (empty($datos)) {
        $respuesta = array();

        $codigo  = HTML::campoOculto('procesar', 'true');
        $codigo .= HTML::campoOculto('id', $id);
        $codigo .= HTML::campoOculto('datos[dialogo]', '', 'idDialogo');
        $codigo .= HTML::campoOculto('datos[id_factura]', $id, '');

        $codigo1  = HTML::parrafo($textos->id('NUMERO_FACTURA_CLIENTE') . ': ' . HTML::frase($objeto->numeroFacturaCliente, 'sinNegrilla subtitulo flotanteDerecha margenDerecha'), 'negrilla margenSuperior espacioInferior10 bordeInferior margenDerechaTriple');
        $codigo1 .= HTML::parrafo($textos->id('CANTIDAD_DINERO_NOTA') . ': ' . HTML::campoTexto('datos[monto_nota]', 10, 15, '', 'flotanteDerecha campoObligatorio2 campoDinero soloNumeros', 'campoMontoNota'), 'negrilla margenSuperiorDoble bordeInferior espacioInferior15 margenDerechaTriple');
        $codigo1 .= HTML::parrafo($textos->id('CANTIDAD_IVA_NOTA') . ': ' . HTML::campoTexto('datos[iva_nota]', 10, 15, '', 'flotanteDerecha campoObligatorio2 campoDinero soloNumeros', 'campoIvaNota'), 'negrilla margenSuperiorDoble bordeInferior espacioInferior15 margenDerechaTriple');
        $codigo1 .= HTML::parrafo($textos->id('CANTIDAD_TOTAL_NOTA') . ': ' . HTML::campoTexto('datos[total_nota]', 10, 15, '', 'flotanteDerecha campoObligatorio2 campoDinero letraNegra', 'campoTotalNota', array('disabled' => 'disabled')), 'negrilla margenSuperiorDoble bordeInferior espacioInferior15 margenDerechaTriple');
        $codigo1 .= HTML::parrafo($textos->id('AFECTAR_CANTIDADES_INVENTARIO') . HTML::campoChequeo('datos[inventario_modificado]', false, 'chkModInventario margenIzquierda', 'chkModInventario'), 'negrilla margenSuperior');


        $codigo2 = HTML::parrafo($textos->id('CONCEPTO_NOTA'), 'negrilla margenSuperior');
        $codigo2 .= HTML::areaTexto('datos[concepto_nota]', 4, 50, '', 'txtAreaConceptoNotaC campoObligatorio', 'txtAreaConceptoNotaC');
        $codigo2 .= HTML::parrafo($textos->id('FECHA_NOTA') . HTML::campoTexto('datos[fecha_nota]', 12, 12, '', 'fechaAntigua campoCalendario', '', array('ayuda' => $textos->id('SELECCIONE_FECHA_NOTA'))), 'negrilla margenSuperior');

        //verificar el identificador escogido para los articulos en la configuracion global y usarlo para mostrar los datos
        $idPrincipalArticulo = (string)$sesion_configuracionGlobal->idPrincipalArticulo;
        $arrayIdArticulo     = array('id' => $textos->id('ID_AUTOMATICO'), 'codigo_oem' => $textos->id('CODIGO_OEM'), 'plu_interno' => $textos->id('PLU'));        
        

        $datosTabla = array(
            HTML::frase($textos->id($arrayIdArticulo[$idPrincipalArticulo]), 'negrilla margenIzquierda'),
            HTML::frase($textos->id('ARTICULO'), 'negrilla margenIzquierda'),
            HTML::frase($textos->id('CANTIDAD'), 'negrilla margenIzquierda'),
            HTML::frase($textos->id('CANTIDAD_A_MODIFICAR'), 'negrilla margenIzquierdaDoble'),
        );


        $listaArticulos = array();
        /**
         * Recorro el listado de articulos, y voy creando objetos en vivo para agregar al arreglo de objetos
         * para ver el listado exacto de los articulos que se cargan ver la clase FacturaVenta 
         */
        foreach ($objeto->listaArticulos as $article) {
            //declaro un nuevo objeto vacio para poder armar la tabla
            $object = new stdClass();
            
            $object->plu        = $article->$idPrincipalArticulo;
            $object->articulo   = $article->articulo;
            $object->cantidad   = $article->cantidad;
            
            
            $idReg = (int) $article->id ;

            if (strlen($object->articulo) > 60) {
                $object->articulo = substr($object->articulo, 0, 60) . '.';
            }
            /**
             *Aqui se añade un campo de texto que contiene la cantidad de cada uno de los articulos
             * notese que en "valor" se concatena la cantidad del articulo, estos datos son usados 
             * en el metodo encargado de hacer el registro
             */
            $datosArticulo =  $object->cantidad . '_' . (int)$article->idArticulo . '_' . (int)$article->idBodega;
            $object->nuevaCantidad = HTML::campoTexto('datos[nueva_cantidad][' . $datosArticulo . ']', 5, 10, $object->cantidad, 'margenIzquierdaDoble rangoNumeros', $idReg, array("rango" => "1-".$object->cantidad.""));
            $listaArticulos[] = $object;
        }


        $idTabla            = 'tablaListaArticulosConsulta';
        $clasesColumnas     = array('', '', '', '', '');
        $clasesFilas        = array('centrado', 'centrado', 'centrado', 'centrado', 'centrado', 'centrado');
        $opciones           = array('cellpadding' => '5', 'cellspacing' => '5', 'border' => '1');
        $clase              = 'tablaListaArticulosConsulta';
        
        $contenedorListaArticles = HTML::tabla($datosTabla, $listaArticulos, $clase, $idTabla, $clasesColumnas, $clasesFilas, $opciones);

        $contenedor1 = HTML::contenedor($codigo1, 'contenedorIzquierdo margenInferiorDoble');
        $contenedor2 = HTML::contenedor($codigo2, 'contenedorDerecho margenInferiorDoble');
        $contenedor3 = HTML::contenedor($contenedorListaArticles, 'contenedorListadoArticulos oculto', 'contenedorListaArticulosNotaC');


        $codigo .= $contenedor1 . $contenedor2 . $contenedor3;
        $codigo .= HTML::parrafo(HTML::boton('chequeo', $textos->id('ACEPTAR'), ' margenSuperiorTriple', 'botonOk', 'botonOk'), 'margenSuperiorTriple');
        $codigo .= HTML::parrafo($textos->id('NOTA_DEBITO_ADICIONADA_A_FACTURA'), 'textoExitoso', 'textoExitoso');
        $codigo1 = HTML::forma($destino, $codigo, 'P');


        $respuesta['generar']       = true;
        $respuesta['cargarJs']      = true;
        $respuesta['archivoJs']     = $configuracion['SERVIDOR']['media'] . $configuracion['RUTAS']['javascript'] . '/modulos/facturas_venta/funcionesNotaCredito.js';
        $respuesta['codigo']        = $codigo1;
        $respuesta['titulo']        = HTML::parrafo($textos->id('INGRESAR_NOTA_DEBITO'), 'letraBlanca negrilla subtitulo');
        $respuesta['destino']       = '#cuadroDialogo';
        $respuesta['ancho']         = 800;
        $respuesta['alto']          = 570;
        
    } else {
        
        $objeto     = new NotaDebitoCliente($id);

        $respuesta['error'] = true;

        if (empty($datos['monto_nota'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_MONTO_NOTA');
            
        } elseif (empty($datos['iva_nota'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_IVA_NOTA');
            
        } elseif (empty($datos['concepto_nota'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_CONCEPTO_NOTA');
            
        } else {
            $idItem = $objeto->adicionar($datos);
            
            if ($idItem) {
                $respuesta['error']             = false;
                $respuesta['accion']            = 'insertar';
                $respuesta['insertarAjax']      = true;
                $respuesta['mostrarNotificacionDinamica']   = true;

                
                if ($datos['dialogo'] != '') {
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
 * Función encargada de borrar el archivo digital de una Nota credito/debito
 * 
 * @global type $textos
 * @global type $sql
 * @global type $archivo_archivo
 * @param type $id
 * @return null 
 */
function eliminarNotaCreditoDigital($id) {
    global $textos, $sql;

    if (!isset($id) || (isset($id) && !$sql->existeItem('notas_credito_clientes', 'id', $id) )) {
        $respuesta              = array();
        $respuesta['error']     = true;
        $respuesta['mensaje']   = $textos->id('NO_HA_SELECCIONADO_ITEM');

        Servidor::enviarJSON($respuesta);
        return false;
    }

    $objeto     = new NotaCreditoCliente($id);

    $recurso    = $objeto->eliminarNotaDigital();
    $respuesta  = array();

    if ($recurso) {
        $respuesta['mensaje']       = $textos->id('NOTA_DIGITAL_ELIMINADA');
        $respuesta['textoExito']    = true;
        
    } else {
        $respuesta['mensaje'] = $textos->id('FALLO_ELIMINAR_NOTA');
        
    }

    $respuesta['error'] = true;

    Servidor::enviarJSON($respuesta);
    
}

/**
 * Función encargada de borrar el archivo digital de una Nota credito/debito
 * 
 * @global type $textos
 * @global type $sql
 * @global type $archivo_archivo
 * @param type $id
 * @return null 
 */
function eliminarNotaDebitoDigital($id) {
    global $textos, $sql;

    if (!isset($id) || (isset($id) && !$sql->existeItem('notas_debito_clientes', 'id', $id) )) {
        $respuesta              = array();
        $respuesta['error']     = true;
        $respuesta['mensaje']   = $textos->id('NO_HA_SELECCIONADO_ITEM');

        Servidor::enviarJSON($respuesta);
        return false;
    }

    $objeto     = new NotaDebitoCliente($id);

    $recurso    = $objeto->eliminarNotaDigital();
    $respuesta  = array();

    if ($recurso) {
        $respuesta['mensaje']       = $textos->id('NOTA_DIGITAL_ELIMINADA');
        $respuesta['textoExito']    = true;
        
    } else {
        $respuesta['mensaje'] = $textos->id('FALLO_ELIMINAR_NOTA');
        
    }

    $respuesta['error'] = true;

    Servidor::enviarJSON($respuesta);
    
}