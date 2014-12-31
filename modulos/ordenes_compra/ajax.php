<?php

/**
 * @package     FOM (Framework OpenSource Multiplatform)
 * @subpackage  Compras de Mercancia
 * @author      Pablo Andres Velez Vidal <pavelez8@genesyscorp.com.co>
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2013 GENESYS
 * @version     0.1
 * */

if (isset($url_accion)) {
    
    switch ($url_accion) {
        
        case 'see'                  :   cosultarItem($forma_id);
                                        break;
        
        case 'add'                  :   adicionarItem($forma_datos, $forma_procesar, $forma_dialogo, $forma_accion);
                                        break;
        
        case 'edit'                 :   modificarItem($forma_id);
                                        break;
        
        case 'delete'               :   $confirmado = ($forma_procesar) ? true : false;
                                        eliminarItem($forma_id, $confirmado, $forma_dialogo);
                                        break;
        
        case 'search'               :   buscarItem($forma_datos, $forma_cantidadRegistros);
                                        break;
        
        case 'move'                 :   paginador($forma_pagina, $forma_orden, $forma_nombreOrden, $forma_consultaGlobal, $forma_cantidadRegistros);
                                        break;

        
        case 'eliminarVarios'       :   $confirmado = ($forma_procesar) ? true : false;
                                        eliminarVarios($confirmado, $forma_cantidad, $forma_cadenaItems);
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
    global $textos, $sql, $configuracion, $sesion_configuracionGlobal;

    if (!isset($id) || (isset($id) && !$sql->existeItem('ordenes_compra', 'id', $id))) {
        $respuesta = array();
        $respuesta['error'] = true;
        $respuesta['mensaje'] = $textos->id('NO_HA_SELECCIONADO_ITEM');

        Servidor::enviarJSON($respuesta);
        return false;
    }

    $objeto         = new OrdenCompra($id);
    $respuesta      = array();

    $codigo .= HTML::campoOculto('id', $id);

    $codigo1  = HTML::parrafo($textos->id('PROVEEDOR') . ': ' . HTML::frase($objeto->proveedor->nombre, 'sinNegrilla'), 'negrilla margenSuperior');
    $codigo1 .= HTML::parrafo($textos->id('NIT') . ': ' . HTML::frase($objeto->proveedor->idProveedor, 'sinNegrilla'), 'negrilla margenSuperior');
    $codigo1 .= HTML::parrafo($textos->id('FECHA_ORDEN') . ': ' . HTML::frase($objeto->fechaOrden, 'sinNegrilla'), 'negrilla margenSuperior');
    $codigo1 .= HTML::parrafo($textos->id('USUARIO_QUE_GENERA_ORDEN') . ': ' . HTML::frase($objeto->usuario, 'sinNegrilla'), 'negrilla margenSuperior');
    $codigo1 .= HTML::parrafo('', 'negrilla margenSuperior');


    $codigo2 .= HTML::parrafo($textos->id('SEDE') . ': ' . HTML::frase($objeto->sede, 'sinNegrilla'), 'negrilla margenSuperior');
    $codigo2 .= HTML::parrafo($textos->id('VALOR_FLETE') . ': ' . HTML::frase('$ '.Recursos::formatearNumero($objeto->valorFlete, '$'), 'sinNegrilla'), 'negrilla margenSuperior');
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
        HTML::frase($textos->id('PRECIO'),      'negrilla margenIzquierda'),
        HTML::frase($textos->id('SUBTOTAL'),    'negrilla margenIzquierda')
    );

    $subtotalFactura = 0;

    $listaArticulos = array();
    
    foreach ($objeto->listaArticulos as $article) {

        $object = new stdClass();

        $object->plu            = $article->$idPrincipalArticulo;
        $object->articulo       = $article->articulo;
        $object->cantidad       = $article->cantidad;  
        $object->descuento      = $article->descuento; 
        $object->precio         = $article->precio; 
        $object->subtotal       = $article->subtotal; 
        
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

        $object->subtotal = '$' . Recursos::formatearNumero($object->subtotal + $objeto->iva, '$');

        $listaArticulos[] = $object;
    }

//    $subtotalFactura += $objeto->valorFlete;
    
//    $impuestoIva = ($subtotalFactura * $objeto->iva) / 100;

//    $subtotalFactura += $impuestoIva;

    $idTabla                    = 'tablaListaArticulosConsulta';
    $clasesColumnas             = array('', '', '', '', '');
    $clasesFilas                = array('centrado', 'centrado', 'centrado', 'centrado', 'centrado', 'centrado');
    $opciones                   = array('cellpadding' => '5', 'cellspacing' => '5', 'border' => '2');
    $clase                      = 'tablaListaArticulosConsulta';
    $contenedorListaArticles    = HTML::tabla($datosTabla, $listaArticulos, $clase, $idTabla, $clasesColumnas, $clasesFilas, $opciones);

    if ($objeto->iva > 0){
        $codigo4  = HTML::parrafo($textos->id('IVA') . HTML::frase('$ '.$objeto->iva, 'sinNegrilla') , 'negrilla margenSuperior');
    }    
    
    $totalFactura = $objeto->subtotal;

    if (!empty($objeto->concepto1) && !empty($objeto->descuento1)) {
        $codigo4 .= HTML::parrafo($textos->id('DESCUENTOS') . ': ', 'negrilla margenSuperior letraVerde');
        
        $pesosDcto1 = ($totalFactura * $objeto->descuento1) / 100;
//        $totalFactura = $totalFactura - $pesosDcto1;
        
        $codigo4 .= HTML::parrafo($objeto->concepto1 . ': ' . HTML::frase($objeto->descuento1 . '%', 'sinNegrilla') . HTML::frase('$ '.Recursos::formatearNumero($pesosDcto1, '$'), 'sinNegrilla margenIzquierdaDoble'), 'negrilla margenSuperior');
    }

    if (!empty($objeto->concepto2) && !empty($objeto->descuento2)) {
        $pesosDcto2 = ($totalFactura * $objeto->descuento2) / 100;
//        $totalFactura = $totalFactura - $pesosDcto2;
        
        $codigo4 .= HTML::parrafo($objeto->concepto2 . ': ' . HTML::frase($objeto->descuento2 . '%', 'sinNegrilla') . HTML::frase('$ '.Recursos::formatearNumero($pesosDcto2, '$'), 'sinNegrilla margenIzquierdaDoble'), 'negrilla margenSuperior');
    }

    $codigo5  = HTML::parrafo($textos->id('SUBTOTAL') . ': ' . HTML::frase('$ '.Recursos::formatearNumero($objeto->subtotal, '$'), 'sinNegrilla titulo'), 'negrilla margenSuperior');
    $codigo5 .= HTML::parrafo('', 'negrilla margenSuperior');
    $codigo5 .= HTML::parrafo('', 'negrilla margenSuperior');
    $codigo5 .= HTML::parrafo($textos->id('TOTAL') . HTML::frase('$ '.Recursos::formatearNumero($objeto->total, '$'), 'sinNegrilla letraAzul grande'), 'negrilla margenSuperior titulo');

    $contenedor1 = HTML::contenedor($codigo1, 'contenedorIzquierdo');
    $contenedor2 = HTML::contenedor($codigo2, 'contenedorDerecho');
    $contenedor3 = HTML::contenedor($contenedorListaArticles, 'contenedorListadoArticulos');
    $contenedor4 = HTML::contenedor($codigo4, 'contenedorIzquierdo');
    $contenedor5 = HTML::contenedor($codigo5, 'contenedorDerecho');

    $codigo .= $contenedor1 . $contenedor2 . $contenedor3 . $contenedor4 . $contenedor5;
    
    $destino    = $configuracion['SERVIDOR']['principal'] . 'compras_mercancia';
    $contenido  = HTML::campoOculto('idOrden', $id, 'ocultoIdOrden');
    $contenido .= HTML::boton('lapiz', HTML::frase($textos->id('MODIFICAR_ITEM_DESDE_FORMULARIO'), 'subtitulo'), 'botonOk directo margenSuperiorTriple margenIzquierdaTriple', 'botonOk', 'botonOk');
    
    $formaModificarOrden = HTML::forma($destino, $contenido, 'P', '', '', array('target' => '_blank'));
    
    $codigo .= HTML::contenedor($formaModificarOrden, 'contenedorIzquierdo');

    $respuesta['generar']       = true;
    $respuesta['codigo']        = $codigo;
    $respuesta['titulo']        = HTML::parrafo($textos->id('CONSULTAR_ITEM'), 'letraBlanca negrilla subtitulo');
    $respuesta['destino']       = '#cuadroDialogo';
    $respuesta['ancho']         = 800;
    $respuesta['alto']          = 600;

    Servidor::enviarJSON($respuesta);
    
}

/**
 * Función con doble comportamiento. La primera llamada (con el arreglo de datos vacio)
 * muestra el formulario para el ingreso del registro. El destino de este formulario es esta 
 * misma función, pero una vez viene desde el formulario con el arreglo datos cargado de valores
 * se encarga de validar la información y llamar al metodo adicionar del objeto.
 * 
 * @global recurso $textos  = objeto global de gestion de los textos de idioma
 * @global recurso $sql     = objeto global de interaccion con la BD
 * @param array $datos      = arreglo con la informacion a adicionar
 */
function adicionarItem($datos) {
    global $textos, $configuracion, $sql, $modulo, $sesion_configuracionGlobal;
    
    //validar los datos necesarios para la factura
    $continuar = validarDatosBasicosFactura($datos);

    if ($continuar) {
        $objeto     = new OrdenCompra();
        $respuesta  = array();

        $idItem = $objeto->adicionar($datos);

        if ($idItem) {

            if (!isset($idItem) || (isset($idItem) && !$sql->existeItem('ordenes_compra', 'id', $idItem))) {
                $respuesta = array();
                $respuesta['error'] = true;
                $respuesta['mensaje'] = $textos->id('NO_HA_SELECCIONADO_ITEM');

                Servidor::enviarJSON($respuesta);
                return false;
            }

            define('FPDF_FONTPATH', 'recursos/fuentes/');

            $nombrePdf = 'media/pdfs/ordenes_compra/orden_compra_' . $idItem . '.pdf';
            $nombrePdf = trim($nombrePdf);

            $objeto = new OrdenCompra($idItem);

            $pdf = new PdfFacturaCompra('P', 'mm', 'letter');
            
            $pdf->SetTopMargin(0.7);
            $pdf->AddPage();

            $pdf->Ln(3);

            //primeros datos de la factura
            $pdf->SetFont('times', 'B', 11);
            $pdf->Cell(70, 7, $textos->id("TIPO_DE_FACTURA") . '  ', 0, 0, 'L');
            $pdf->SetFont('times', 'B', 7);
            $pdf->Cell(20, 7, 'N° '.$idItem , 0, 0, 'L');
            $pdf->SetFont('times', '', 7);
            $pdf->Cell(45, 7, $objeto->numeroFacturaProveedor, 0, 0, 'L');
            $pdf->Ln(4);

            $pdf->SetFont('times', 'B', 7);
            $pdf->Cell(8, 7, $textos->id("NIT") . ': ', 0, 0, 'L');
            $pdf->SetFont('times', '', 7);
            $pdf->Cell(27, 7, $objeto->idProveedor, 0, 0, 'L');

            $pdf->SetFont('times', 'B', 7);
            $pdf->Cell(19, 7, '', 0, 0, 'L');
            $pdf->Cell(15, 7, $textos->id("PROVEEDOR") . ': ', 0, 0, 'L');
            $pdf->SetFont('times', '', 7);
            $pdf->Cell(20, 7, $objeto->proveedor->nombre, 0, 0, 'L');

            $pdf->Ln(3);

            $pdf->SetFont('times', 'B', 7);
            $pdf->Cell(22, 7, $textos->id("FECHA_FACTURA") . ': ', 0, 0, 'L');
            $pdf->SetFont('times', '', 7);
            $pdf->Cell(20, 7, $objeto->fechaFactura, 0, 0, 'L');

            $pdf->SetFont('times', 'B', 7);
            $pdf->Cell(12, 7, '', 0, 0, 'L');
            $pdf->SetFont('times', 'B', 7);
            $pdf->Cell(25, 7, $textos->id("USUARIO_QUE_FACTURA") . ': ', 0, 0, 'L');
            $pdf->SetFont('times', '', 7);
            $pdf->Cell(25, 7, $objeto->usuario, 0, 0, 'L');

            $pdf->Ln(3);

            $pdf->SetFont('times', 'B', 7);
            $pdf->Cell(22, 7, $textos->id("SEDE") . ': ', 0, 0, 'L');
            $pdf->SetFont('times', '', 7);
            $pdf->Cell(20, 7, $objeto->sede, 0, 0, 'L');

            $pdf->SetFont('times', 'B', 7);
            $pdf->Cell(12, 7, '', 0, 0, 'L');
            $pdf->SetFont('times', 'B', 7);
            $pdf->Cell(22, 7, $textos->id("VALOR_FLETE") . ': ', 0, 0, 'L');
            $pdf->SetFont('times', '', 7);
            $pdf->Cell(25, 7, Recursos::formatearNumero($objeto->valorFlete, '$'), 0, 0, 'L');

            if ($objeto->fechaVtoFactura != "" && $objeto->fechaVtoFactura != "0000-00-00") {
                $pdf->SetFont('times', 'B', 7);
                $pdf->Cell(12, 7, '', 0, 0, 'L');
                $pdf->SetFont('times', 'B', 7);
                $pdf->Cell(25, 7, $textos->id("FECHA_VENCIMIENTO") . ': ', 0, 0, 'L');
                $pdf->SetFont('times', '', 7);
                $pdf->Cell(25, 7, $objeto->fechaVtoFactura, 0, 0, 'L');
            }

            $pdf->Ln(3);

            $pdf->SetFont('times', 'B', 7);
            $pdf->Cell(22, 7, $textos->id("OBSERVACIONES") . ': ', 0, 0, 'L');
            $pdf->SetFont('times', 'I', 7);
            $pdf->Ln(5);
            $pdf->MultiCell(200, 3, $objeto->observaciones, '', 'L', 0);

            //linea divisora  de la cabecera del listado de articulos
            $pdf->Cell(197, 7, '', 'B', 0, 'L');

            //verificar el identificador escogido para los articulos en la configuracion global y usarlo para mostrar los datos
            $idPrincipalArticulo = (string)$sesion_configuracionGlobal->idPrincipalArticulo;
            $arrayIdArticulo     = array('id' => $textos->id('ID_AUTOMATICO'), 'codigo_oem' => $textos->id('CODIGO_OEM'), 'plu_interno' => $textos->id('PLU'));

            $pdf->Ln(1);
            //cabecera del listado de articulos
            $pdf->SetFont('times', 'B', 8);
            $pdf->Cell(20, 8, $textos->id($arrayIdArticulo[$idPrincipalArticulo]), 0, 0, 'C');
            $pdf->SetFont('times', 'B', 8);
            $pdf->Cell(4, 8, '', 0, 0, 'L');
            $pdf->Cell(10, 8, $textos->id("REFERENCIA"), 0, 0, 'C');
            $pdf->SetFont('times', 'B', 8);    
            $pdf->Cell(5, 8, '', 0, 0, 'L');
            $pdf->Cell(65, 8, $textos->id("ARTICULO"), 0, 0, 'C');
            $pdf->SetFont('times', 'B', 8);
            $pdf->Cell(10, 8, $textos->id("CANTIDAD"), 0, 0, 'C');
            $pdf->Cell(5, 8, '', 0, 0, 'L');
            $pdf->SetFont('times', 'B', 8);
            $pdf->Cell(10, 8, $textos->id("DESCUENTO"), 0, 0, 'C');
            $pdf->Cell(10, 8, '', 0, 0, 'L');
            $pdf->SetFont('times', 'B', 8);
            $pdf->Cell(10, 8, $textos->id("PRECIO"), 0, 0, 'C');
            $pdf->Cell(5, 8, '', 0, 0, 'L');
            $pdf->SetFont('times', 'B', 8);
            $pdf->Cell(15, 8, $textos->id("SUBTOTAL"), 0, 0, 'C');
            $pdf->Cell(5, 8, '', 0, 0, 'L');
            $pdf->SetFont('times', 'B', 8);
//            $pdf->Cell(15, 8, $textos->id("BODEGA"), 0, 0, 'C');    
//            $pdf->Cell(5, 8, '', 0, 0, 'L');

            $pdf->Ln(1);

//            $subtotalFactura = 0;

            //en el arreglo de articulos al id del articulo se hace referencia como idArticulo
            if($idPrincipalArticulo == 'id'){
                $idPrincipalArticulo = 'idArticulo';
            }    

            //ciclo que va recorriendo el listado de articulos de una factura determinada y los imprime
            foreach ($objeto->listaArticulos as $obj) {
                $pdf->Ln(3);

                if (strlen($obj->articulo) > 45) {
                    $obj->articulo = substr($obj->articulo, 0, 44) . '.';
                }
                if ($obj->descuento == 0 || $obj->descuento == "0") {
                    $obj->subtotal = $obj->cantidad * $obj->precio;
                } else {
                    $obj->subtotal = ($obj->cantidad * $obj->precio) - ( ( ($obj->cantidad * $obj->precio) * $obj->descuento) / 100 );
                }

                $obj->descuento = Recursos::formatearNumero($obj->descuento, '%', '0');
                $obj->precio    = '$'.Recursos::formatearNumero($obj->precio, '$');

//                $subtotalFactura += $obj->subtotal;

                $obj->subtotal = '$'.Recursos::formatearNumero($obj->subtotal, '$');

                //consulto la referencia del articulo
                //$sql->depurar = true;
                $referencia = $sql->obtenerValor('articulos', 'referencia', 'id = "'.$obj->idArticulo.'" ');

                $pdf->SetFont('times', '', 7);
                $pdf->Cell(20, 8, (int)$obj->$idPrincipalArticulo, 0, 0, 'C');
                $pdf->SetFont('times', '', 7);
                $pdf->Cell(15, 8, $referencia, 0, 0, 'L');        
                $pdf->SetFont('times', '', 7);
                $pdf->Cell(70, 8, $obj->articulo, 0, 0, 'L');
                $pdf->Cell(3, 8, '', 0, 0, 'L');
                $pdf->SetFont('times', '', 7);
                $pdf->Cell(5, 8, $obj->cantidad, 0, 0, 'L');
                $pdf->Cell(10, 8, '', 0, 0, 'L');
                $pdf->SetFont('times', '', 7);
                $pdf->Cell(5, 8, $obj->descuento, 0, 0, 'L');
                $pdf->Cell(10, 8, '', 0, 0, 'L');
                $pdf->SetFont('times', '', 7);
                $pdf->Cell(10, 8, $obj->precio, 0, 0, 'L');
                $pdf->Cell(5, 8, '', 0, 0, 'L');
                $pdf->SetFont('times', '', 7);
                $pdf->Cell(15, 8, $obj->subtotal, 0, 0, 'L');
                $pdf->Cell(5, 8, '', 0, 0, 'L');
                $pdf->SetFont('times', '', 7);
//                $pdf->Cell(15, 8, (int)$obj->idBodega, 0, 0, 'L');        
//                $pdf->Cell(5, 8, '', 0, 0, 'L');
            }

            $pdf->Ln(1);
            $pdf->Cell(197, 7, '', 'B', 0, 'L');

            $subtotalFactura = $objeto->subtotal;

            $impuestoIva = $objeto->iva;

//            $subtotalFactura += $impuestoIva;

            if ($impuestoIva > 0){
                $pdf->Ln(7);

                $pdf->SetFont('times', 'B', 7);
                $pdf->Cell(170, 7, $textos->id("IVA") . ': ', 0, 0, 'R');
                $pdf->SetFont('times', '', 7);
                $pdf->Cell(30, 7, '$ '.Recursos::formatearNumero($impuestoIva, '$'), 0, 0, 'R');

            }

            $totalFactura = $objeto->subtotal;

            $totalDescuentos = 0;

            if (!empty($objeto->concepto1) && !empty($objeto->descuento1)) {  
                $pdf->Ln(5);
                $pdf->SetFont('times', 'B', 8);
                $pdf->Cell(170, 7, $textos->id("DESCUENTOS") . ': ', 0, 0, 'R');

                $pdf->Ln(2);                

                $pesosDcto1 = ($totalFactura * $objeto->descuento1) / 100;
                
                $totalDescuentos += $pesosDcto1;

                $pdf->Ln(2);
                $pdf->SetFont('times', 'B', 7);
                $pdf->Cell(170, 7, $objeto->concepto1 . ': ' . $objeto->descuento1 . '%', 0, 0, 'R');
                $pdf->SetFont('times', '', 7);
                $pdf->Cell(30, 7, '$ '.Recursos::formatearNumero($pesosDcto1, '$'), 0, 0, 'R');

                $pdf->Ln(1);
            }

            if (!empty($objeto->concepto2) && !empty($objeto->descuento2)) {
                $pesosDcto2 = ($totalFactura * $objeto->descuento2) / 100;

                $totalDescuentos += $pesosDcto2;

                $pdf->Ln(3);
                $pdf->SetFont('times', 'B', 7);
                $pdf->Cell(170, 7, $objeto->concepto2 . ': ' . $objeto->descuento2 . '%', 0, 0, 'R');
                $pdf->SetFont('times', '', 7);
                $pdf->Cell(30, 7, '$ '.Recursos::formatearNumero($pesosDcto2, '$'), 0, 0, 'R');

                $pdf->Ln(1);
            }

            $pdf->Ln(7);
            $pdf->SetFont('times', 'B', 10);
            $pdf->Cell(170, 7, $textos->id("SUBTOTAL") . ':   ', 0, 0, 'R');
            $pdf->SetFont('times', 'B', 10);
            $pdf->Cell(30, 7, '$'.Recursos::formatearNumero($subtotalFactura, '$'), 0, 0, 'R');

            if ($totalDescuentos > 0){
                $pdf->Ln(4);
                $pdf->SetFont('times', 'B', 10);
                $pdf->Cell(170, 7, $textos->id("TOTAL_DESCUENTOS") . ':   ', 0, 0, 'R');
                $pdf->SetFont('times', 'B', 10);
                $pdf->Cell(30, 7, '$'.Recursos::formatearNumero($totalDescuentos, '$'), 0, 0, 'R');

            }

            $pdf->Ln(7);
            $pdf->SetFont('times', 'B', 14);
            $pdf->Cell(170, 7, $textos->id("TOTAL") . ':  ', 0, 0, 'R');
            $pdf->SetFont('times', 'B', 13);
            $pdf->Cell(30, 7, '$'.Recursos::formatearNumero($objeto->total, '$'), 0, 0, 'R');

            $pdf->Output($nombrePdf, 'F');
            chmod($nombrePdf, 0777);
            

            $respuesta['error']         = NULL;
            $respuesta['accion']        = 'abrir_ubicacion';
            $respuesta['destino']       = 'media/pdfs/ordenes_compra/orden_compra_' . $idItem . '.pdf';
            $respuesta['info']          = true;
            $respuesta['redireccionar'] = '/ordenes_compra';
            $respuesta['textoInfo']     = $textos->id('ORDEN_GENERADA_EXITOSAMENTE');
            //remover la variable post idOrden para que recargue el formulario vacio

        } else {
            $respuesta['mensaje'] = $textos->id('ERROR_DESCONOCIDO');

        }

        Servidor::enviarJSON($respuesta);        
        
    }  

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
    global $textos, $sql, $configuracion;


    if (!isset($id) || (isset($id) && !$sql->existeItem('ordenes_compra', 'id', $id))) {
        $respuesta = array();
        $respuesta['error'] = true;
        $respuesta['mensaje'] = $textos->id('NO_HA_SELECCIONADO_ITEM');

        Servidor::enviarJSON($respuesta);
        return NULL;
    }

    $objeto = new OrdenCompra($id);
    $respuesta = array();

    $codigo = HTML::campoOculto('procesar', 'true');
    $codigo .= HTML::campoOculto('id', $id);
    $codigo .= HTML::campoOculto('datos[dialogo]', '', 'idDialogo');

    $codigo .= HTML::parrafo($textos->id('NUMERO_ORDEN') . HTML::frase($objeto->idOrden, 'sinNegrilla'), 'negrilla margenSuperior');
    $codigo .= HTML::parrafo($textos->id('PROVEEDOR') . ': ' . HTML::frase($objeto->proveedor->nombre, 'sinNegrilla'), 'negrilla margenSuperior');
    $codigo .= HTML::parrafo($textos->id('NIT') . ': ' . HTML::frase($objeto->idProveedor, 'sinNegrilla'), 'negrilla margenSuperior');
    $codigo .= HTML::parrafo($textos->id('FECHA_ORDEN') . ': ' . HTML::frase($objeto->fechaOrden, 'sinNegrilla'), 'negrilla margenSuperior');
    $codigo .= HTML::parrafo($textos->id('USUARIO_QUE_GENERA_ORDEN') . ': ' . HTML::frase($objeto->usuario, 'sinNegrilla'), 'negrilla margenSuperior');

    $destino                = $configuracion['SERVIDOR']['principal'] . 'compras_mercancia';
    $contenido              = HTML::campoOculto('idOrden', $id, 'ocultoIdOrden');
    $contenido             .= HTML::boton('lapiz', HTML::frase($textos->id('MODIFICAR_ITEM_DESDE_FORMULARIO'), 'subtitulo'), 'botonOk directo margenSuperiorTriple margenIzquierdaTriple', 'botonOk', 'botonOk');
    $formaModificarOrden    = HTML::forma($destino, $contenido, 'P', '', '', array('target' => '_blank'));

    $textoExitoso = HTML::frase($textos->id('REGISTRO_MODIFICADO'), 'textoExitoso margenIzquierda', 'textoExitoso');
    $codigo .= HTML::parrafo($formaModificarOrden . $textoExitoso, 'margenSuperior');


    $respuesta['generar']       = true;
    $respuesta['codigo']        = $codigo;
    $respuesta['titulo']        = HTML::parrafo($textos->id('MODIFICAR_ITEM'), 'letraBlanca negrilla subtitulo');
    $respuesta['destino']       = '#cuadroDialogo';
    $respuesta['ancho']         = 790;
    $respuesta['alto']          = 580;

    Servidor::enviarJSON($respuesta);
    
}

/**
 * Función con doble comportamiento. La primera llamada (con el parametro $confirmado vacio)
 * muestra el formulario de confirmación de eliminación del registro. El destino de este formulario es esta 
 * misma función, pero una vez viene desde el formulario con el parametro $confirmado en "true"
 * se encarga de validar la información y llamar al metodo modificar del objeto.
 * 
 * @global recurso $textos  = objeto global de gestion de los textos de idioma
 * @global recurso $sql     = objeto global de interaccion con la BD
 * @param int $id           = id del registro a modificar
 * @param array $datos      = arreglo con la informacion a adicionar
 */
function eliminarItem($id, $confirmado, $dialogo) {
    global $textos;

    $objeto         = new OrdenCompra($id);
    $destino        = '/ajax' . $objeto->urlBase . '/delete';
    $respuesta      = array();

    if (!$confirmado) {
        $titulo  = HTML::frase($objeto->idOrden, 'negrilla');
        $titulo1 = str_replace('%1', $titulo, $textos->id('CONFIRMAR_ELIMINACION'));
        $codigo  = HTML::campoOculto('procesar', 'true');
        $codigo .= HTML::campoOculto('id', $id);
        $codigo .= HTML::campoOculto('datos[dialogo]', '', 'idDialogo');
        $codigo .= HTML::parrafo($titulo1);
        $codigo .= HTML::parrafo(HTML::boton('chequeo', $textos->id('ACEPTAR'), '', 'botonOk', 'botonOk'), 'margenSuperior');
        $codigo .= HTML::parrafo($textos->id('REGISTRO_ELIMINADO'), 'textoExitoso', 'textoExitoso');
        $codigo1 = HTML::forma($destino, $codigo);

        $respuesta['generar']       = true;
        $respuesta['codigo']        = $codigo1;
        $respuesta['destino']       = '#cuadroDialogo';
        $respuesta['titulo']        = HTML::parrafo($textos->id('ELIMINAR_ITEM'), 'letraBlanca negrilla subtitulo');
        $respuesta['ancho']         = 350;
        $respuesta['alto']          = 150;
        
    } else {

        if ($objeto->eliminar()) {
            $respuesta['error']         = false;
            $respuesta['accion']        = 'insertar';
            $respuesta['idDestino']     = '#tr_' . $id;
            
            if ($dialogo == '') {
                $respuesta['eliminarFilaTabla'] = true;
                
            } else {
                $respuesta['eliminarFilaDialogo']   = true;
                $respuesta['ventanaDialogo']        = $dialogo;
                
            }
            
        } else {
            $respuesta['mensaje'] = $textos->id('ERROR_DESCONOCIDO');
            
        }
        
    }

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
        $item       = '';
        $respuesta  = array();
        
        $objeto     = new OrdenCompra();
        
        $registros  = $configuracion['GENERAL']['registrosPorPagina'];
        
        if (!empty($cantidadRegistros)) {
            $registros = (int) $cantidadRegistros;
        }
        
        $pagina              = 1;
        $registroInicial    = 0;


        $palabras = explode(' ', $datos);

        $condicionales = $data[1];

        if ($condicionales == '') {
            $condicion = '(p.nombre REGEXP "(' . implode('|', $palabras) . ')")';
            
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

        $arregloItems = $objeto->listar($registroInicial, $registros, array('0'), $condicion, 'p.nombre');

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

    $item       = '';
    $respuesta  = array();
    $objeto     = new OrdenCompra();

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

        $data       = explode('[', $consultaGlobal);
        $datos      = $data[0];
        $palabras   = explode(' ', $datos);

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
            $consultaGlobal = '(p.nombre REGEXP "(' . implode('|', $palabras) . ')")';
            
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

    $respuesta['error']             = false;
    $respuesta['accion']            = 'insertar';
    $respuesta['contenido']         = $item;
    $respuesta['idContenedor']      = '#tablaRegistros';
    $respuesta['idDestino']         = '#contenedorTablaRegistros';
    $respuesta['paginarTabla']      = true;

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
function eliminarVarios($confirmado, $cantidad, $cadenaItems) {
    global $textos;

    $destino        = '/ajax/ordenes_compra/eliminarVarios';
    $respuesta      = array();

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
            $objeto         = new OrdenCompra($val);
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


/**
 * Validaciones
 * 1)si $datos['cadenaArticulosPrecios'] viene vacio, no se selecciono ni un articulo para ser ingresado
 */
function validarDatosBasicosFactura($datos) {
    global $textos;
    
    if (empty($datos['cadenaArticulosPrecios']) /*|| aqui mas posibles validaciones*/) {
        $respuesta              = array();
        $respuesta['error']     = true;
        $respuesta['mensaje']   = $textos->id('ERROR_FACTURA_SIN_ARTICULOS');

        Servidor::enviarJSON($respuesta);
        return NULL;
    }  
    
    return true;
    
}  
