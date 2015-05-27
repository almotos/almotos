<?php

/**
 * @package     FOM (Framework OpenSource Multiplatform)
 * @subpackage  Compras de Mercancia
 * @author      Pablo Andres Velez Vidal <pavelez8@misena.edu.co>
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2012 GENESYS
 * @version     0.1
 * Framework basado en FOLCS, desarrollado por Francisco Lozano de FELINUX ltda. Cali - Colombia
 * */

if (isset($url_accion)) {
    
    switch ($url_accion) {
        
        case 'add'                      :   adicionarItem($forma_datos);
                                            break;
        
        case 'buscarFactura'            :   ($forma_procesar) ? $datos = $forma_datos : $datos = array();
                                            buscarFactura($forma_datos);
                                            break;
        
        case 'imprimirFacturaCompraPdf' :   imprimirFacturaCompraPdf($forma_datos);
                                            break;
        
        case 'imprimirFacturaCompraPos' :   imprimirFacturaCompraPos($forma_datos);
                                            break;
                                        
        case 'guardarFacturaTemporal'   :   guardarFacturaTemporal($forma_datos);
                                            break;
        
        case 'modificarFacturaTemporal' :   modificarFacturaTemporal($forma_datos);
                                            break;
        
        case 'moverArticuloBodega'      :   moverArticuloBodega($forma_id);
                                            break;
        
//        case 'calcularRetecree' : calcularRetecree($forma_idProveedor);
//            break;        
        
        case 'buscarOrdenCompra'        :   ($forma_procesar) ? $datos = $forma_datos : $datos = array();
                                            buscarOrdenCompra($forma_datos);
                                            break;
        
        case 'listarOrdenes'            :   listarOrdenes($url_cadena);
                                            break;
        
    }
    
}

/**
 * Funcion que carga la ventana modal de opciones cuando se hace click en el botón
 * finalizar factura.
 * 
 * @todo refactorizar esta funcion urgente, sobre todo la forma de recibir esos datos
 * 
 * @global objeto $textos objeto global para la traducción de textos
 * @global arreglo $configuracion arreglo global que contiene la información de configuración del sistema
 * @param arreglo $datos arreglo con los datos a ser utilizados en esta función
 */
function adicionarItem($datos) {
    global $textos, $configuracion;
    
    //validar los datos necesarios para la factura
    $continuar = validarDatosBasicosFactura($datos);

    if ($continuar) {
        @$datosFactura = array(
            'datos[id_proveedor]'               => $datos['id_proveedor'],
            'datos[num_factura_proveedor]'      => $datos['num_factura_proveedor'],
            'datos[fecha_factura]'              => $datos['fecha_factura'],
            'datos[iva]'                        => $datos['iva'],
            'datos[id_caja]'                    => $datos['id_caja'],
            'datos[concepto1]'                  => $datos['concepto1'],
            'datos[descuento1]'                 => $datos['descuento1'],
            'datos[concepto2]'                  => $datos['concepto2'],
            'datos[descuento2]'                 => $datos['descuento2'],
            'datos[valor_flete]'                => $datos['valor_flete'],
            'datos[retenciones]'                => $datos['retenciones'],
            'datos[subtotal]'                   => $datos['subtotal'],
            'datos[total]'                      => $datos['total'],
            'datos[observaciones]'              => $datos['observaciones'],
            'datos[es_modificacion]'            => $datos['es_modificacion'], //determina si es una factura exisente a ser modificada
            'datos[modificacion_orden]'         => $datos['modificacion_orden'], //determina si se trata de modificar una orden de compra, y si se ejecuta, elimina la orden de compra del sistema
            'datos[cadenaArticulosPrecios]'     => $datos['cadenaArticulosPrecios'],
            'datos[id_factura_temporal]'        => $datos['es_factura_temporal'],
            'datos[imprimir_codigos_barras]'    => '0'
        );

        $avisoOrden = '';

        if (!empty($datos['es_orden_compra'])) {
            $avisoOrden = HTML::contenedor(HTML::parrafo($textos->id('SOLO_PUEDE_SER_ORDEN_COMPRA'), 'negrilla textoRojo'), 'textoAdvertencia');
        }

        $idItem  = '';
        $codigo  = HTML::campoOculto('procesar', 'true');

        $botones = '';
        $campos  = '';
        $camposRetencion = '';

        if (empty($datos['es_orden_compra'])) {

            /* Inicio manejo lógica de campos retenciones */            
            
            $contabilidad = new Contabilidad();
            $respuestaRetenciones  = $contabilidad->generarCamposRetenciones($datos['id_proveedor'], $datos['total'], $datos['iva']);   
            
            $camposRetencion    = $respuestaRetenciones["campos_retencion"];
            $totalRetenciones   = $respuestaRetenciones["total_retenciones"];
            $totalAPagar        = $respuestaRetenciones["total_a_pagar"];            
            
            
            $campos .= HTML::campoChequeo('', true, '', 'checkEfectivo');
            $campos .= HTML::frase($textos->id('EFECTIVO').' ......', 'negrilla margenSuperior margenIzquierda');
            $campos .= HTML::campoTexto('datos[efectivo]', 20, 255, $respuestaRetenciones["total_a_pagar"], 'margenIzquierda campoDinero soloNumeros valorMaximo', 'campoEfectivo', array("valor_maximo" => $respuestaRetenciones["total_a_pagar"]));  
            $campos .= HTML::parrafo('', 'negrilla margenSuperior');
            $campos .= HTML::campoChequeo('', false, '', 'checkTarjeta');
            $campos .= HTML::frase($textos->id('TARJETA').' .........', 'negrilla margenSuperior margenIzquierda');
            $campos .= HTML::campoTexto('datos[tarjeta]', 20, 255, '', 'margenIzquierda campoDinero soloNumeros valorMaximo', 'campoTarjeta', array('disabled' => 'disabled', "valor_maximo" => $respuestaRetenciones["total_a_pagar"]));
            $campos .= HTML::parrafo('', 'negrilla margenSuperior');
            $campos .= HTML::campoChequeo('', false, '', 'checkCheque');
            $campos .= HTML::frase($textos->id('CHEQUE').' .......', 'negrilla margenSuperior margenIzquierda');
            $campos .= HTML::campoTexto('datos[cheque]', 20, 255, '', 'margenIzquierda campoDinero soloNumeros valorMaximo', 'campoCheque', array('disabled' => 'disabled', "valor_maximo" => $respuestaRetenciones["total_a_pagar"]));
            $campos .= HTML::parrafo('', 'negrilla margenSuperior');
            $campos .= HTML::campoChequeo('', false, '', 'checkCredito');
            $campos .= HTML::frase($textos->id('CREDITO').' ........', 'negrilla margenSuperior margenIzquierda');
            $campos .= HTML::campoTexto('datos[credito]', 20, 255, '', 'margenIzquierda campoDinero soloNumeros valorMaximo', 'campoCredito', array('disabled' => 'disabled', "valor_maximo" => $respuestaRetenciones["total_a_pagar"]));            
            
            //tener en cuenta que esta factura tambien vence
            $campos .= HTML::frase($textos->id('VENCIMIENTO'), 'margenIzquierdaDoble campoFechaVtoFact oculto letraAzul');
            $campos .= HTML::campoTexto('datos[fecha_vto_factura]', 15, 15, '', 'fechaReciente campoFechaVtoFact oculto campoCalendario', '', array('ayuda' => $textos->id('AYUDA_FECHA_VTO_FACTURA')));

            $datosFactura['datos[retenciones]'] = $respuestaRetenciones["datos_retenciones"];
            
            /* Fin campos retenciones*/
            $pestana1 = $campos;
            $pestana2 = $camposRetencion;
            
            $pestanas = array(
                HTML::frase($textos->id('MEDIOS_PAGO'), 'letraBlanca')    => $pestana1,
                HTML::frase($textos->id('RETENCIONES'), 'letraBlanca')    => $pestana2,
            );

            $codigo .= HTML::pestanas2('pestanasAgregar', $pestanas);     
            
            $botones .= HTML::divisionHorizontal();
            $botones .= HTML::frase($textos->id('IMPRIMIR_CODIGOS_BARRA'), 'negrilla margenIzquierda');             
            $botones .= HTML::campoChequeo('chkImprimirBarcodes', false, 'margenIzquierdaDoble', 'chkImprimirBarcodes');      

            if ($totalRetenciones > 0){
                $botones .= HTML::parrafo('', 'negrilla margenSuperiorDoble');
                $botones .= HTML::frase($textos->id("TOTAL"), 'negrilla subtitulo margenIzquierda');
                $botones .= HTML::frase('$'.Recursos::formatearNumero($datos['total'], '$'), 'negrilla masGrande2 letraAzul margenIzquierdaDoble');   
                $botones .= HTML::campoOculto('campoTotalFinFactura', $datos['total'], 'campoTotalFinFactura');

                $botones .= HTML::divisionHorizontal();
                $botones .= HTML::parrafo('', 'negrilla');
                $botones .= HTML::frase($textos->id("TOTAL_RETENCIONES"), 'negrilla subtitulo margenIzquierda');
                $botones .= HTML::frase('$'.Recursos::formatearNumero($totalRetenciones, '$'), 'negrilla masGrande2 margenIzquierdaDoble', 'textoTotalRetenciones');   
                $botones .= HTML::campoOculto('campoTotalRetenciones', $totalRetenciones, 'campoTotalRetenciones'); 

            }
            
            $botones .= HTML::divisionHorizontal();
            $botones .= HTML::parrafo('', 'negrilla');
            $botones .= HTML::frase($textos->id("TOTAL_A_PAGAR"), 'negrilla subtitulo margenIzquierda');
            $botones .= HTML::frase('$'.Recursos::formatearNumero($totalAPagar, '$'), 'negrilla masGrande3 letraVerde margenIzquierdaDoble', 'textoTotalAPagar');   
            $botones .= HTML::campoOculto('campoTotalAPagar', $totalAPagar, 'campoTotalAPagar');               
            
            $botonesPdf = HTML::botonAjax('impresora', $textos->id('IMPRIMIR_FACTURA_IMPRESORA'), '/ajax/compras_mercancia/imprimirFacturaCompraPdf', $datosFactura, 'btnImpresionFactura ', 'btnImprimirFacturaPdf');
            $botonesPos = HTML::botonAjax('impresora', $textos->id('IMPRIMIR_FACTURA_POS'), '/ajax/compras_mercancia/imprimirFacturaCompraPos', $datosFactura, ' btnImpresionFactura', 'btnImprimirFacturaPos');

            $botones .= HTML::contenedorCampos($botonesPdf, $botonesPos, 'margenSuperiorDoble margenIzquierda');

        }

        $botones .= $avisoOrden;

        $botonesOrdComp  = HTML::botonAjax('libreta', $textos->id('GENERAR_ORDEN_COMPRA'), '/ajax/ordenes_compra/add', $datosFactura, ' btnImpresionFactura', 'btnGenerarOrdenCompra');
        $botonesCancela  = HTML::boton('cancelar', $textos->id('CANCELAR'), 'directo  botonCancelar', '', 'botonCancelarAccionFactura');

        $botones .= HTML::contenedorCampos($botonesOrdComp, $botonesCancela, 'margenSuperior margenIzquierda');

        $codigo  .= HTML::parrafo($botones, 'margenSuperior');
        $codigo  .= HTML::parrafo($textos->id('ACCION_EJECUTADA'), 'textoExitoso', 'textoExitoso');
        $codigo1  = HTML::contenedor($codigo, 'contenedorFormaImpresion', 'contenedorFormaImpresion');

        $respuesta = array();
        $respuesta['generar']           = true;
        $respuesta['cargarJs']          = true;
        $respuesta['archivoJs']         = $configuracion['SERVIDOR']['media'] . $configuracion['RUTAS']['javascript'] . '/modulos/compras_mercancia/funcionesFinFactura.js';
        $respuesta['codigo']            = $codigo1;
        $respuesta['destino']           = '#cuadroDialogo';
        $respuesta['titulo']            = HTML::parrafo($textos->id('CONFIRMAR_ACCION_FACTURA'), 'letraBlanca negrilla subtitulo');
        $respuesta['ancho']             = 600;
        $respuesta['alto']              = 500;

        Servidor::enviarJSON($respuesta);        
    }

    
}

/**
 * Funcion que carga la ventana modal para realizar el movimiento de un articulo de una bodega a otra diferente
 * durante la realización de una factura de compra.
 * 
 * @global objeto $textos objeto global para la traducción de textos
 * @global objeto $sql objeto global de interacción con la BD
 * @param entero $id identificador único del registro en la BD
 */
function moverArticuloBodega($id) {
    global $textos, $sql;

    $respuesta = array();

    $codigo     = HTML::campoOculto('procesar', 'true');
    $codigo    .= HTML::campoOculto('datos[dialogo]', '', 'idDialogo');
    $listaSedes = array();
    $consulta   = $sql->seleccionar(array('sedes_empresa'), array('id', 'nombre'), 'id != 0', '', 'nombre ASC');
    
    if ($sql->filasDevueltas) {
        while ($dato = $sql->filaEnObjeto($consulta)) {
            $listaSedes[$dato->id] = $dato->nombre;
        }
    }

    $selectorSedes  = HTML::listaDesplegable('datos[sede]', $listaSedes, '', 'margenSuperior', 'selectorSedes2', $textos->id('SELECCIONAR') . '...', $opciones, $ayuda);
    $selectorBodega = HTML::listaDesplegable('datos[bodega]', '', '', 'margenSuperior', 'selectorBodegas2', '', $opciones, $ayuda);

    $codigo .= HTML::campoOculto('id_fila', $id, 'idFilaArticuloAMover');
    $codigo .= HTML::frase($textos->id('SEDE'), ' margenSuperior');
    $codigo .= $selectorSedes;
    $codigo .= HTML::frase($textos->id('BODEGA'), 'margenIzquierda margenSuperior');
    $codigo .= $selectorBodega;
    $codigo .= HTML::parrafo(HTML::boton('chequeo', $textos->id('ACEPTAR'), 'directo', '', 'botonMoverArticuloBodega', '', array('validar' => 'NoValidar')), 'margenSuperior');

    $respuesta['generar']       = true;
    $respuesta['codigo']        = $codigo;
    $respuesta['titulo']        = HTML::parrafo($textos->id('MOVER_ARTICULO_BODEGA'), 'letraBlanca negrilla subtitulo');
    $respuesta['destino']       = '#cuadroDialogo';
    $respuesta['ancho']         = 470;
    $respuesta['alto']          = 150;

    Servidor::enviarJSON($respuesta);
}

/**
 * Funcion que recibe el id de un proveedor, con este consulta su actividad economica
 * y con la actividad economica consulta el retecree correspondiente
 * 
 * @global objeto $textos objeto global para la traducción de textos
 * @global objeto $sql objeto global de interacción con la BD
 * @param entero $idProveedor identificador único del proveedoro en la BD
 * 
 * @return float  cantidad de retecree en porcentaje
 */
//function calcularRetecree($idProveedor) {
//    global $textos, $sql;
//
//    $respuesta = array();
//
//    $idActividad = $sql->obtenerValor("proveedores", 'id_actividad_economica', 'id = "'.$idProveedor.'"');
//    
//    $retecree = $sql->obtenerValor("actividades_economicas", 'porcentaje_retecree', 'id = "'.$idActividad.'"');
//    
//    $respuesta['retecree']       = $retecree;
//
//
//    Servidor::enviarJSON($respuesta);
//}

/**
 * Función que genera la tabla con la info de facturas existentes en el sistema según los parametros recibidos
 * 
 * @global objeto $textos objeto global para la traducción de textos
 * @global arreglo $configuracion arreglo global que contiene la información de configuración del sistema
 * @param arreglo $datos arreglo con los datos a ser utilizados en esta función
 */
function buscarFactura($datos) {
    global $textos, $configuracion, $sql;

    $destino    = '/ajax/compras_mercancia/buscarFactura';
    $respuesta  = array();
    $codigo     = '';
    
    if (empty($datos)) {
        $codigo  = HTML::campoOculto('procesar', 'true');
        $codigo .= HTML::campoOculto('datos[dialogo]', '', 'idDialogo');
        $codigo .= HTML::parrafo($textos->id('PROVEEDOR'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[proveedor]', 40, 255, '', 'autocompletable campoObligatorio', 'campoIdProveedor', array('title' => '/ajax/proveedores/listar'), $textos->id('AYUDA_USO_AUTOCOMPLETAR'), '/ajax/proveedores/add', 'datos[id_proveedor]', '');
        $codigo .= HTML::parrafo($textos->id('FECHA_INICIAL'), 'margenSuperior negrilla');
        $codigo .= HTML::campoTexto("datos[fecha_inicial]", 12, 12, '', "fechaAntigua campoObligatorio", "", array("alt" => $textos->id("SELECCIONE_FECHA_INICIAL")));
        $codigo .= HTML::parrafo($textos->id('FECHA_FINAL'), 'margenSuperior negrilla');
        $codigo .= HTML::campoTexto("datos[fecha_final]", 12, 12, '', "fechaAntigua campoObligatorio", "", array("alt" => $textos->id("SELECCIONE_FECHA_FINAL")));
        $codigo .= HTML::parrafo(HTML::boton('chequeo', $textos->id('ACEPTAR'), 'directo', '', 'tablaBusquedaFacturas', '', array('validar' => 'NoValidar')), 'margenSuperior');
        $codigo  = HTML::forma($destino, $codigo, 'P', true);

        $respuesta['generar']       = true;
        $respuesta['codigo']        = $codigo;
        $respuesta['titulo']        = HTML::parrafo($textos->id('BUSCAR_FACTURAS'), 'letraBlanca negrilla subtitulo');
        $respuesta['destino']       = '#cuadroDialogo';
        $respuesta['ancho']         = 450;
        $respuesta['alto']          = 250;
        
    } else {
        
        if (empty($datos['proveedor']) || (!empty($datos['proveedor']) && !$sql->existeItem('proveedores', 'id', $datos['id_proveedor'])) || empty($datos['fecha_inicial']) || empty($datos['fecha_final'])) {
            $respuesta              = array();
            $respuesta['error']     = true;
            $respuesta['mensaje']   = $textos->id('ERROR_DEBE_LLENAR_TODOS_LOS_CAMPOS_DEL_FORMULARIO');

            Servidor::enviarJSON($respuesta);
            return NULL;
        }           

        $excluidas  = array(0);
        $objeto     = new FacturaCompra();
        
        $objeto->listaAscendente = true;

        $condicion = ' fc.id_proveedor = "' . $datos["id_proveedor"] . '" AND fecha_factura BETWEEN "' . $datos["fecha_inicial"] . '" AND "' . $datos["fecha_final"] . '"';

        $listaFacturas = $objeto->listar(0, 100, $excluidas, $condicion, $orden);

        if (!empty($objeto->registrosConsulta)) {
            //crear los formularios con la info para las demas sedes
            $datosTablaFacturasCompra = array(
                HTML::parrafo($textos->id('NUMERO_DE_FACTURA'), 'centrado')     => 'id', //concateno el nombre del alias para usarlo al armar la tabla con el fila en objeto
                HTML::parrafo($textos->id('SEDE'), 'centrado')                  => 'sede', //y concateno el alias de la tabla junto con el campo para usarlo al realizar
                HTML::parrafo($textos->id('PROVEEDOR'), 'centrado')             => 'proveedor', //la busqueda, al armar la tabla dividira la cadena y usara el que necesite
                HTML::parrafo($textos->id('FECHA_FACTURA'), 'centrado')         => 'fechaFactura'
            );


            $rutas              = array();
            $idTabla            = 'tablaListarFacturas';
            $claseTabla         = 'tablaListarItems';
            $estilosColumnas    = array('', '', '', '', '');
            $contenedor         = HTML::contenedor(Recursos::generarTablaLista($listaFacturas, $datosTablaFacturasCompra, $rutas, $idTabla, $estilosColumnas, $claseTabla), 'flotanteIzquierda margenDerecha');
         
        } else {
            $contenedor = HTML::contenedor(HTML::parrafo($textos->id('BUSQUEDA_SIN_RESULTADOS'), 'textoError'));
            
        }

        $codigo = HTML::parrafo($textos->id('AYUDA_SELECCIONAR_ITEMS_TABLA_FACTURAS'), 'letraVerde negrilla subtitulo');
        $codigo .= $contenedor;

        $respuesta['generar']       = true;
        $respuesta['cargarJs']      = true;
        $respuesta['archivoJs']     = $configuracion['SERVIDOR']['media'] . $configuracion['RUTAS']['javascript'] . '/modulos/compras_mercancia/funcionesBuscarFacturas.js';
        $respuesta['codigo']        = $codigo;
        $respuesta['titulo']        = HTML::parrafo($textos->id('BUSCAR_FACTURAS'), 'letraBlanca negrilla subtitulo');
        $respuesta['destino']       = '#cuadroDialogo';
        $respuesta['ancho']         = 800;
        $respuesta['alto']          = 540;
        
    }

    Servidor::enviarJSON($respuesta);
    
}

/**
 * Funcion que genera la info para buscar y cargar una orden de compra para ser ejecutada como una factura
 * 
 * @global objeto $textos objeto global para la traducción de textos
 * @global arreglo $configuracion arreglo global que contiene la información de configuración del sistema
 * @param arreglo $datos arreglo con los datos a ser utilizados en esta función
 */
function buscarOrdenCompra($datos) {
    global $textos, $configuracion, $sql;

    $respuesta  = array();
    $codigo     = '';
    
    if (empty($datos)) {
        $codigo  = HTML::campoOculto('procesar', 'true');
        $codigo .= HTML::campoOculto('datos[dialogo]', '', 'idDialogo');

        $codigo2    = HTML::parrafo($textos->id('INGRESE_NUM_ORDEN_COMPRA'), 'negrilla margenSuperior');
        $codigo2    .= HTML::campoTexto('datos[num_orden_compra]', 40, 255, '', 'autocompletable campoObligatorio', 'campoIdOrden', array('title' => '/ajax/compras_mercancia/listarOrdenes'), $textos->id('AYUDA_USO_AUTOCOMPLETAR'), '', 'datos[id_orden]');
        $codigo2    .= HTML::campoOculto('idOrden', '', 'ocultoIdOrden');
        $destino2   = $configuracion['SERVIDOR']['principal'] . 'compras_mercancia';
        $codigo2    = HTML::forma($destino2, $codigo2, 'P', false);

        $codigo .= HTML::contenedor($codigo2, '', 'contenedorBusquedaRapida');
        $codigo .= HTML::parrafo($textos->id('BUSQUEDA_AVANZADA'), 'negrilla margenSuperior margenInferior estiloEnlace', 'textoBusquedaAvanzada');


        $codigo1  = HTML::parrafo($textos->id('PROVEEDOR'), 'negrilla margenSuperior');
        $codigo1 .= HTML::campoTexto('datos[proveedor]', 40, 255, '', 'autocompletable campoObligatorio', 'campoIdProveedor', array('title' => '/ajax/proveedores/listar'), $textos->id('AYUDA_USO_AUTOCOMPLETAR'), '', 'datos[id_proveedor]', '');
        $codigo1 .= HTML::parrafo($textos->id('FECHA_INICIAL'), 'margenSuperior negrilla');
        $codigo1 .= HTML::campoTexto("datos[fecha_inicial]", 12, 12, '', "fechaAntigua campoObligatorio", "", array("alt" => $textos->id("SELECCIONE_FECHA_INICIAL")));
        $codigo1 .= HTML::parrafo($textos->id('FECHA_FINAL'), 'margenSuperior negrilla');
        $codigo1 .= HTML::campoTexto("datos[fecha_final]", 12, 12, '', "fechaAntigua campoObligatorio", "", array("alt" => $textos->id("SELECCIONE_FECHA_FINAL")));
        $codigo1 .= HTML::parrafo(HTML::boton('chequeo', $textos->id('ACEPTAR'), '', '', 'btnCargarOrden', '', array('validar' => 'NoValidar')), 'margenSuperior');
        $destino1 = '/ajax/compras_mercancia/buscarOrdenCompra';
        $codigo1  = HTML::forma($destino1, $codigo1, 'P', true);
        $codigo  .= HTML::contenedor($codigo1, 'oculto', 'contenedorBusquedaAvanzada');



        $respuesta['generar']       = true;
        $respuesta['cargarJs']      = true;
        $respuesta['archivoJs']     = $configuracion['SERVIDOR']['media'] . $configuracion['RUTAS']['javascript'] . '/modulos/compras_mercancia/funcionesBuscarOrdenes.js';
        $respuesta['codigo']        = $codigo;
        $respuesta['titulo']        = HTML::parrafo($textos->id('BUSCAR_ORDEN_COMPRA'), 'letraBlanca negrilla subtitulo');
        $respuesta['destino']       = '#cuadroDialogo';
        $respuesta['ancho']         = 450;
        $respuesta['alto']          = 350;
        
    } else {
        
        if (empty($datos['proveedor']) || (!empty($datos['proveedor']) && !$sql->existeItem('proveedores', 'id', $datos['id_proveedor'])) || empty($datos['fecha_inicial']) || empty($datos['fecha_final'])) {
            $respuesta              = array();
            $respuesta['error']     = true;
            $respuesta['mensaje']   = $textos->id('ERROR_DEBE_LLENAR_TODOS_LOS_CAMPOS_DEL_FORMULARIO');

            Servidor::enviarJSON($respuesta);
            return NULL;
        }         

        $excluidas  = array(0);
        $objeto     = new OrdenCompra();
        
        $objeto->listaAscendente = true;

        $condicion = ' oc.id_proveedor = "' . $datos["id_proveedor"] . '" AND fecha_orden BETWEEN "' . $datos["fecha_inicial"] . '" AND "' . $datos["fecha_final"] . '"';

        $listaOrdenes = $objeto->listar(0, 100, $excluidas, $condicion, $orden);

        if (!empty($objeto->registrosConsulta)) {
            //crear los formularios con la info para las demas sedes
            $datosTablaFacturasCompra = array(
                HTML::parrafo($textos->id('NUMERO_DE_ORDEN'), 'centrado')   => 'id', //concateno el nombre del alias para usarlo al armar la tabla con el fila en objeto
                HTML::parrafo($textos->id('SEDE'), 'centrado')              => 'sede', //y concateno el alias de la tabla junto con el campo para usarlo al realizar
                HTML::parrafo($textos->id('PROVEEDOR'), 'centrado')         => 'proveedor', //la busqueda, al armar la tabla dividira la cadena y usara el que necesite
                HTML::parrafo($textos->id('FECHA_ORDEN'), 'centrado')       => 'fechaOrden'
            );


            $rutas              = array();
            $idTabla            = 'tablaListarOrdenes';
            $claseTabla         = 'tablaListarItems';
            $estilosColumnas    = array('', '', '', '', '');
            
            $contenedor = HTML::contenedor(Recursos::generarTablaLista($listaOrdenes, $datosTablaFacturasCompra, $rutas, $idTabla, $estilosColumnas, $claseTabla), 'flotanteIzquierda margenDerecha');
            
        } else {
            $contenedor = HTML::contenedor(HTML::parrafo($textos->id('BUSQUEDA_SIN_RESULTADOS'), 'textoError'));
            
        }

        $codigo  = HTML::parrafo($textos->id('AYUDA_SELECCIONAR_ITEMS_TABLA_FACTURAS'), 'letraVerde negrilla subtitulo');
        $codigo .= $contenedor;

        $respuesta['generar']       = true;
        $respuesta['cargarJs']      = true;
        $respuesta['archivoJs']     = $configuracion['SERVIDOR']['media'] . $configuracion['RUTAS']['javascript'] . '/modulos/compras_mercancia/funcionesBuscarOrden2.js';
        $respuesta['codigo']        = $codigo;
        $respuesta['titulo']        = HTML::parrafo($textos->id('BUSCAR_ORDENES'), 'letraBlanca negrilla subtitulo');
        $respuesta['destino']       = '#cuadroDialogo';
        $respuesta['ancho']         = 800;
        $respuesta['alto']          = 540;
        
    }

    Servidor::enviarJSON($respuesta);
    
}

/**
 * Función encargada de imprimir una factura de compra en PDF. Puede ser impresa en la impresora normal
 * 
 * @global objeto $textos objeto global para la traducción de textos
 * @global objeto $sql objeto global de interacción con la BD
 * @global objeto $sesion_configuracionGlobal objeto global que contiene un objeto configuración con la información de configuración parametrizable del sistema
 * @param arreglo $datos arreglo con las datos a ser ingresados en la factura
 * @return boolean true o false dependiendo del exito de la operacion
 */
function imprimirFacturaCompraPdf($datos) {
    global $textos, $sql, $sesion_configuracionGlobal;
    
    $respuesta = array();

    if (!empty($datos['es_modificacion'])) {
        $objeto     = new FacturaCompra($datos['es_modificacion']);
        $idItem     = $objeto->modificar($datos);
        $idItem     = $datos['es_modificacion'];
        
    } else if (!empty($datos['modificacion_orden'])) { //si se trata de generar una factura desde una orden de compra
        $objeto     = new FacturaCompra();        
        $idItem     = $objeto->adicionar($datos);

        if (!empty($idItem)) {//si se pudo crear la factura de compra
            $orden = new OrdenCompra($datos['modificacion_orden']); //creo el objeto de la orden de compra
            $orden->eliminar(); //y la elimino, pues ya no es necesaria  
            
        }
        
    } else if (!empty($datos['id_factura_temporal'])) { //si se trata de generar una factura desde una factura que no se finalizo previamente
        $objeto     = new FacturaCompra();
        $idItem     = $objeto->adicionar($datos);

        if (!empty($idItem)) {//si se pudo crear la factura de compra
            FacturaTemporalCompra::eliminarFacturaTemporal($datos['id_factura_temporal']); //eliminar la factura temporal creada  
            
        }
        
    } else {
        $objeto = new FacturaCompra();
        $idItem = $objeto->adicionar($datos);


    }
    
    //verificar que del mismo proveedor no vayamos a ingresar la misma orden de compra
    if ($objeto->existeFacturaProveedor) {//siempre se realiza el metodo adicionar, si este da un error, entonces se le pone un valor a existeFacturaProveedor
        $respuesta              = array();
        $respuesta['error']     = true;
        $respuesta['mensaje']   = str_replace('%1', $objeto->existeFacturaProveedor, $textos->id('NUMERO_FACTURA_EXISTENTE_EN_PROVEEDOR'));

        Servidor::enviarJSON($respuesta);
        return NULL;
    }


    define('FPDF_FONTPATH', 'recursos/fuentes/');
    
    $nombrePdf = 'media/pdfs/facturas_compra/factura_compra_' . $idItem . '.pdf';
    $nombrePdf = trim($nombrePdf);

    if (!isset($idItem) || (isset($idItem) && !$sql->existeItem('facturas_compras', 'id', $idItem))) {
        $respuesta              = array();
        $respuesta["error"]     = true;
        $respuesta["mensaje"]   = $textos->id("NO_HA_SELECCIONADO_ITEM");

        Servidor::enviarJSON($respuesta);
        return NULL;
    }


    $objeto = new FacturaCompra($idItem);


    $pdf = new PdfFacturaCompra('P', 'mm', 'letter');

    $pdf->SetTopMargin(0.7);
    $pdf->AddPage();


    $pdf->Ln(3);


    //primeros datos de la factura
    $pdf->SetFont('times', 'B', 11);
    $pdf->Cell(70, 7, $textos->id("TIPO_DE_FACTURA") . '  ', 0, 0, 'L');
    $pdf->SetFont('times', 'B', 7);
    $pdf->Cell(40, 7, $textos->id("NUMERO_FACTURA_PROVEEDOR") . ': ', 0, 0, 'L');
    $pdf->SetFont('times', '', 7);
    $pdf->Cell(45, 7, $objeto->numeroFacturaProveedor, 0, 0, 'L');
    $pdf->Ln(4);


    $pdf->SetFont('times', 'B', 7);
    $pdf->Cell(8, 7, $textos->id("NIT") . ': ', 0, 0, 'L');
    $pdf->SetFont('times', '', 7);
    $pdf->Cell(27, 7, $objeto->proveedor->idProveedor, 0, 0, 'L');

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

//    $pdf->Ln(3);
//
//    $pdf->SetFont('times', 'B', 7);
//    $pdf->Cell(22, 7, $textos->id("MODO_PAGO") . ': ', 0, 0, 'L');
//    $pdf->SetFont('times', '', 7);
//    $pdf->Cell(20, 7, $textos->id("MODO_PAGO" . $objeto->modoPago), 0, 0, 'L');

    if ($objeto->fechaVtoFactura != '' && $objeto->fechaVtoFactura != '0000-00-00') {
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
    
    if ($idPrincipalArticulo == "id") {
        $idPrincipalArticulo = "idArticulo";
    }        
    
    $arrayIdArticulo     = array('idArticulo' => $textos->id('ID_AUTOMATICO'), 'plu_interno' => $textos->id('PLU'));
    

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
    $pdf->Cell(5, 8, '', 0, 0, 'L');
    $pdf->SetFont('times', 'B', 8);
    $pdf->Cell(10, 8, $textos->id("PRECIO"), 0, 0, 'C');
    $pdf->Cell(5, 8, '', 0, 0, 'L');
    $pdf->SetFont('times', 'B', 8);
    $pdf->Cell(12, 8, $textos->id("SUBTOTAL"), 0, 0, 'C');
    $pdf->Cell(5, 8, '', 0, 0, 'L');
    $pdf->SetFont('times', 'B', 8);
    $pdf->Cell(10, 8, $textos->id("BODEGA"), 0, 0, 'C');    
    $pdf->Cell(5, 8, '', 0, 0, 'L');
    $pdf->Cell(10, 8, $textos->id("PRECIO_VENTA"), 0, 0, 'C');    
    $pdf->Cell(5, 8, '', 0, 0, 'L');    

    $pdf->Ln(1);

    $subtotalFactura = 0;
    
    //en el arreglo de articulos al id del articulo se hace referencia como idArticulo
    if($idPrincipalArticulo == 'id'){
        $idPrincipalArticulo = 'idArticulo';
    }    
    
    $dctoTotalSobreArticulos = 0;

    //ciclo que va recorriendo el listado de articulos de una factura determinada y los imprime
    foreach ($objeto->listaArticulos as $obj) {

        $pdf->Ln(3);

        if (strlen($obj->articulo) > 45) {
            $obj->articulo = substr($obj->articulo, 0, 44) . '.';
        }
        if ($obj->descuento == "0") {
            $obj->subtotal = $obj->cantidad * $obj->precio;
        } else {
            $descuentoArticulo = ( ( ($obj->cantidad * $obj->precio) * $obj->descuento) / 100 );
            $obj->subtotal = ($obj->cantidad * $obj->precio) - $descuentoArticulo;
            $dctoTotalSobreArticulos += $descuentoArticulo;
        }
        
        $obj->descuento = Recursos::formatearNumero($obj->descuento, '%', '0');
        $obj->precio    = '$'.Recursos::formatearNumero($obj->precio, '$');
        
        $subtotalFactura += $obj->subtotal;

        $obj->subtotal = '$'.Recursos::formatearNumero($obj->subtotal, '$');

        //consulto la referencia del articulo
        //$sql->depurar = true;
        $referencia = $sql->obtenerValor('articulos', 'referencia', 'id = "'.$obj->idArticulo.'" ');
        
        
        $pdf->SetFont('times', '', 7);
        $pdf->Cell(18, 8, (int)$obj->$idPrincipalArticulo, 0, 0, 'C');
        $pdf->SetFont('times', '', 7);
        $pdf->Cell(20, 8, $referencia, 0, 0, 'L');        
        $pdf->SetFont('times', '', 7);
        $pdf->Cell(68, 8, $obj->articulo, 0, 0, 'L');
        $pdf->Cell(3, 8, '', 0, 0, 'L');
        $pdf->SetFont('times', '', 7);
        $pdf->Cell(5, 8, $obj->cantidad, 0, 0, 'L');
        $pdf->Cell(10, 8, '', 0, 0, 'L');
        $pdf->SetFont('times', '', 7);
        $pdf->Cell(5, 8, $obj->descuento, 0, 0, 'L');
        $pdf->Cell(5, 8, '', 0, 0, 'L');
        $pdf->SetFont('times', '', 7);
        $pdf->Cell(10, 8, $obj->precio, 0, 0, 'L');
        $pdf->Cell(5, 8, '', 0, 0, 'L');
        $pdf->SetFont('times', '', 7);
        $pdf->Cell(12, 8, $obj->subtotal, 0, 0, 'L');
        $pdf->Cell(5, 8, '', 0, 0, 'L');
        $pdf->SetFont('times', '', 7);
        $pdf->Cell(10, 8, (int)$obj->idBodega, 0, 0, 'C');        
        $pdf->Cell(5, 8, '', 0, 0, 'L');
        $pdf->Cell(10, 8, '$'.Recursos::formatearNumero($obj->precioVenta, '$'), 0, 0, 'C');        
        $pdf->Cell(5, 8, '', 0, 0, 'L');        
    }

    $pdf->Ln(1);
    $pdf->Cell(197, 7, '', 'B', 0, 'L');

    $subtotalFactura += $objeto->valorFlete;
    
    $impuestoIva = $objeto->iva;

    $pdf->Ln(7);
    if ($objeto->valorFlete > 0) {
        $pdf->SetFont('times', 'B', 7);
        $pdf->Cell(170, 7, $textos->id("VALOR_FLETE") . ': ', 0, 0, 'R');
        $pdf->SetFont('times', '', 7);
        $pdf->Cell(30, 7, '$'.Recursos::formatearNumero($objeto->valorFlete, '$'), 0, 0, 'R');    
    }
    
    if ($dctoTotalSobreArticulos > 0) {
        $pdf->Ln(4);
        $pdf->SetFont('times', 'B', 8);
        $pdf->Cell(170, 7, $textos->id("DCTO_TOTAL_ARTICULOS") . ':   ', 0, 0, 'R');
        $pdf->SetFont('times', 'B', 8);
        $pdf->Cell(30, 7, '$'.Recursos::formatearNumero($dctoTotalSobreArticulos, '$'), 0, 0, 'R'); 
    }
        
    $pdf->Ln(4);
    $pdf->SetFont('times', 'B', 10);
    $pdf->Cell(170, 7, $textos->id("SUBTOTAL") . ':   ', 0, 0, 'R');
    $pdf->SetFont('times', 'B', 10);
    $pdf->Cell(30, 7, '$'.Recursos::formatearNumero($subtotalFactura, '$'), 0, 0, 'R');   
    
    if ($impuestoIva > 0) {
        $pdf->Ln(6);

        $pdf->SetFont('times', 'B', 7);
        $pdf->Cell(170, 7, $textos->id("IVA_TOTAL") . ': ', 0, 0, 'R');
        $pdf->SetFont('times', '', 7);
        $pdf->Cell(30, 7, '$'.Recursos::formatearNumero($impuestoIva, '$'), 0, 0, 'R');    

    }


    $totalFactura = $subtotalFactura + $objeto->iva;

    $totalDescuentos = 0;

    if (!empty($objeto->concepto1) && !empty($objeto->descuento1)) {
        
        $pdf->Ln(6);
        $pdf->SetFont('times', 'B', 8);
        $pdf->Cell(170, 7, $textos->id("DESCUENTOS") . ': ', 0, 0, 'R');

        $pdf->Ln(2);        

        $pesosDcto1 = ($totalFactura * $objeto->descuento1) / 100;
        $totalFactura = $totalFactura - $pesosDcto1;

        $totalDescuentos += $pesosDcto1;

        $pdf->Ln(2);
        $pdf->SetFont('times', 'B', 7);
        $pdf->Cell(170, 7, $objeto->concepto1 . ': ' . $objeto->descuento1 . '%', 0, 0, 'R');
        $pdf->SetFont('times', '', 7);
        $pdf->Cell(30, 7, '$'.Recursos::formatearNumero($pesosDcto1, '$'), 0, 0, 'R');

        $pdf->Ln(1);
        

        if (!empty($objeto->concepto2) && !empty($objeto->descuento2)) {

            $pesosDcto2 = ($totalFactura * $objeto->descuento2) / 100;
            $totalFactura = $totalFactura - $pesosDcto2;

            $totalDescuentos += $pesosDcto2;

            $pdf->Ln(3);
            $pdf->SetFont('times', 'B', 7);
            $pdf->Cell(170, 7, $objeto->concepto2 . ': ' . $objeto->descuento2 . '%', 0, 0, 'R');
            $pdf->SetFont('times', '', 7);
            $pdf->Cell(30, 7, '$'.Recursos::formatearNumero($pesosDcto2, '$'), 0, 0, 'R');

            $pdf->Ln(1);
        }

        $pdf->Ln(4);
        $pdf->SetFont('times', 'B', 10);
        $pdf->Cell(170, 7, $textos->id("TOTAL_DESCUENTOS") . ':   ', 0, 0, 'R');
        $pdf->SetFont('times', 'B', 10);
        $pdf->Cell(30, 7, '$'.Recursos::formatearNumero($totalDescuentos, '$'), 0, 0, 'R');        
        
    }

    //Agregar las retenciones realizadas en la compra a la factura de compra
    
    if (count($objeto->arregloRetenciones) > 0) {
        $pdf->Ln(1);
        foreach ($objeto->arregloRetenciones as $key => $value) {
            //si la retencion es diferente al iva teorico
            if ($key != "Iva Teorico") {
                $pdf->Ln(4);

                $pdf->SetFont('times', 'B', 7);
                $pdf->Cell(170, 7, $key . ': ', 0, 0, 'R');
                $pdf->SetFont('times', '', 7);
                $pdf->Cell(30, 7, '$'.Recursos::formatearNumero($value, '$'), 0, 0, 'R');
 
            }
        }
        
        $pdf->Ln(4);
        $pdf->SetFont('times', 'B', 10);
        $pdf->Cell(170, 7, $textos->id("TOTAL_RETENCIONES") . ':   ', 0, 0, 'R');
        $pdf->SetFont('times', 'B', 10);
        $pdf->Cell(30, 7, '$'.Recursos::formatearNumero($objeto->totalRetenciones, '$'), 0, 0, 'R');           
        
    }
   

    $pdf->Ln(7);
    $pdf->SetFont('times', 'B', 14);
    $pdf->Cell(170, 7, $textos->id("TOTAL") . ':  ', 0, 0, 'R');
    $pdf->SetFont('times', 'B', 13);
    $pdf->Cell(30, 7, '$'.Recursos::formatearNumero( ($totalFactura - $objeto->totalRetenciones), '$'), 0, 0, 'R');

    $pdf->Output($nombrePdf, 'F');
    chmod($nombrePdf, 0777);

    //aqui se pregunta si se desean tambien imprimir los códigos de barra
    if (isset($datos["imprimir_codigos_barras"]) && !empty($datos["imprimir_codigos_barras"])) {


        $pdf2 = new eFPDF('P', 'pt');



        $nombrePdf2 = 'media/pdfs/temporales/codigos_barra_fact_' . $idItem . '.pdf';
        $nombrePdf2 = trim($nombrePdf2);

        foreach ($objeto->listaArticulos as $objArti) {
            $pdf2->AddPage();
            $tablas = array('a' => 'articulos');
            $columnas = array('nombre' => 'a.nombre', 'datoCodigoBarra' => 'a.' . $sesion_configuracionGlobal->datoCodigoBarra,);
            $condicion = ' id = "' . $objArti->idArticulo . '" ';

            $obj = $sql->filaEnObjeto($sql->seleccionar($tablas, $columnas, $condicion));

            $fontSize   = 10;
            $marge      = 10;   // between barcode and hri in pixel
            $x          = 100;  // barcode center
            $y          = 50;  // barcode center
            $height     = 50;   // barcode height in 1D ; module size in 2D
            $width      = 1;    // barcode height in 1D ; not use in 2D
            $angle      = 0;   // rotation in degrees
            // barcode, of course ;)
            $type       = 'code39';
            $black      = '000000'; // color in hexa
            // -------------------------------------------------- //
            //            ALLOCATE FPDF RESSOURCE
            // -------------------------------------------------- //
            // -------------------------------------------------- //
            //                      BARCODE
            // -------------------------------------------------- //
            // -------------------------------------------------- //
            //                      HRI
            // -------------------------------------------------- //
            $contador = 0; //va contando los barcode que se imprimen en una linea, salta de linea cuando se imprimen 3
            $nuevaPagina = 0; //cuenta las lineas que se imprimen, cuando hay siete, toca agregar una nueva página(teniendo en cuenta los tamaños actuales)

            for ($i = 0; $i < $objArti->cantidad; $i++) {

                if ($contador >= 3) {//ya imprimio los que caben en una linea
                    $contador = 0; //reinicio el contador
                    $y += 120; //aumentola posicion del y
                    $x = 100; //reinicio el x
                    $nuevaPagina++; //sumo que se acaba de imprimir una nueva linea
                }

                if ($nuevaPagina == 7) {//Termino de imprimir una página
                    $nuevaPagina = 0;
                    $pdf2->AddPage();
                    $x = 100;  // reinicio x a los valores de una página nueva
                    $y = 50;  // reinicio y a los valores de una página nueva              
                }

                $code = Recursos::completarCeros($obj->datoCodigoBarra, 8);
                $data = Barcode::fpdf($pdf2, $black, $x, $y, $angle, $type, array('code' => $code), $width, $height);

                $pdf2->SetFont('Arial', 'B', $fontSize);
                $pdf2->SetTextColor(0, 0, 0);
                $len = $pdf2->GetStringWidth($data['hri']);
                Barcode::rotate(-$len / 2, ($data['height'] / 2) + $fontSize + $marge, $angle, $xt, $yt);
                $pdf2->TextWithRotation($x + $xt, $y + $yt, $data['hri'], $angle);
                $nomArticulo = $obj->nombre;
                if (strlen($nomArticulo) > 32) {
                    $nomArticulo = (int)$objArti->idProveedor.'-'.substr($nomArticulo, 0, 30);
                }
                $len2 = $pdf2->GetStringWidth($nomArticulo);
                Barcode::rotate(-$len2 / 2, (($data['height'] + 20) / 2) + $fontSize + $marge, $angle, $xt, $yt);
                $pdf2->TextWithRotation($x + $xt, $y + $yt, $nomArticulo, $angle);

                $contador++; // incremento contador porque se ha impreso un codigo
                $x += 200; //corro el centro de impresion del codigo de barras sobre el eje x
            }
        }

        $pdf2->Output($nombrePdf2, 'F');
        chmod($nombrePdf2, 0777);
        //se determina en true la variable otro archivo que le dice al metodo procesaRespuesta() de varios.js
        //que debe abrir dos archivos, en este caso 2 pdfs
        $respuesta["otro_archivo"] = true;
        $respuesta["destino2"]     = 'media/pdfs/temporales/codigos_barra_fact_' . $idItem . '.pdf';
        
    }

    $respuesta["error"]         = NULL;
    $respuesta["accion"]        = "abrir_ubicacion";
    $respuesta["destino"]       = 'media/pdfs/facturas_compra/factura_compra_' . $idItem . '.pdf';
    $respuesta["info"]          = true;
    $respuesta["recargar"]      = true;
    $respuesta["textoInfo"]     = $textos->id('FACTURA_GENERADA_EXITOSAMENTE');

    Servidor::enviarJSON($respuesta);
    
}

/**
 * Función encargada de imprimir el txt para la tirilla de impresion por POS
 * 
 * @global arreglo $configuracion arreglo global que contiene la información de configuración del sistema
 * @global objeto $textos objeto global para la traducción de textos
 * @global objeto $sql objeto global de interacción con la BD
 * @global type $sesion_usuarioSesion
 * @global objeto $sesion_configuracionGlobal objeto global que contiene un objeto configuración con la información de configuración parametrizable del sistema
 * @param type $datos
 * @return boolean 
 */
function imprimirFacturaCompraPos($datos) {
    global $configuracion, $textos, $sql, $sesion_usuarioSesion, $sesion_configuracionGlobal;

    $respuesta = array();

    if (!empty($datos['es_modificacion'])) {
        $objeto     = new FacturaCompra($datos['es_modificacion']);
        $idItem     = $objeto->modificar($datos);
        $idItem     = $datos['es_modificacion'];

    } else if (!empty($datos['modificacion_orden'])) { //si se trata de generar una factura desde una orden de compra
        $objeto     = new FacturaCompra();
        $idItem     = $objeto->adicionar($datos);

        if (!empty($idItem)) {//si se pudo crear la factura de compra
            $orden = new OrdenCompra($datos['modificacion_orden']); //creo el objeto de la orden de compra
            $orden->eliminar(); //y la elimino, pues ya no es necesaria  

        }

    } else if (!empty($datos['id_factura_temporal'])) { //si se trata de generar una factura desde una factura que no se finalizo previamente
        $objeto     = new FacturaCompra();
        $idItem     = $objeto->adicionar($datos);

        if (!empty($idItem)) {//si se pudo crear la factura de compra
            FacturaTemporalCompra::eliminarFacturaTemporal($datos['id_factura_temporal']); //eliminar la factura temporal creada  

        }

    } else {
        $objeto     = new FacturaCompra();
        $idItem     = $objeto->adicionar($datos);
        
    }
    

    //verificar que del mismo proveedor no vayamos a ingresar la misma orden de compra
    if ($objeto->existeFacturaProveedor) {
        $respuesta              = array();
        $respuesta['error']     = true;
        $respuesta['mensaje']   = str_replace('%1', $objeto->existeFacturaProveedor, $textos->id('NUMERO_FACTURA_EXISTENTE_EN_PROVEEDOR'));

        Servidor::enviarJSON($respuesta);
        return false;

    }

    if (!isset($idItem) || (isset($idItem) && !$sql->existeItem('facturas_compras', 'id', $idItem))) {
        $respuesta                  = array();
        $respuesta["error"]         = true;
        $respuesta["mensaje"]       = $textos->id("NO_HA_SELECCIONADO_ITEM");

        Servidor::enviarJSON($respuesta);
        return false;
    }

    $objeto     = new FacturaCompra($idItem);
    $empresa    = new Empresa('1');

    $nombreArchivo = substr(md5(uniqid(rand(), true)), 0, 8);

    $fichero = $configuracion["RUTAS"]["media"] . "/" . $configuracion["RUTAS"]["archivos"] . '/facturas_compra_pos/' . $nombreArchivo . '.txt';

    while (file_exists($fichero)) {
        $nombre_archivo = substr(md5(uniqid(rand(), true)), 0, 8);
        $fichero = $configuracion["RUTAS"]["media"] . "/" . $configuracion["RUTAS"]["archivos"] . '/facturas_compra_pos/' . "/$nombre_archivo.txt";

    }

    $tirilla_encabezado  = $empresa->nombre . "\n";
    $tirilla_encabezado .= $sesion_usuarioSesion->sede->nombre . "\n";
    $tirilla_encabezado .= $textos->id("NIT") . $empresa->nit . "\n";
    $tirilla_encabezado .= $sql->obtenerValor('configuraciones', 'nota_factura', 'id = "1"') . "\n\n";

    $tirilla_encabezado .= $textos->id('COPIA_FACTURA_COMPRA') . ": N° " . $objeto->numeroFacturaProveedor . "\n\n";

    $tirilla_encabezado .= $textos->id('FECHA_FACTURA') . ": " . $objeto->fechaFactura . "\n";
    $tirilla_proveedor  = $textos->id("PROVEEDOR") . "  : " . $objeto->proveedor->nombre . "\n";
    $tirilla_proveedor .= $textos->id("NIT_PROVEEDOR") . " : " . $objeto->proveedor->idProveedor . "\n";

    if ($objeto->observaciones != "") {
        $tirilla_comentario = wordwrap($textos->id("OBSERVACIONES") . " : " . $objeto->observaciones, 38, "\n ") . "\n";

    } else {
        $tirilla_comentario = "";

    }

    $tirilla_usuario = " " . $textos->id('USUARIO_QUE_FACTURA') . "  : " . $objeto->usuario . "   \n\n";

    //verificar el identificador escogido para los articulos en la configuracion global y usarlo para mostrar los datos
    $idPrincipalArticulo = (string)$sesion_configuracionGlobal->idPrincipalArticulo;
    
    if ($idPrincipalArticulo == "id") {
        $idPrincipalArticulo = "idArticulo";
    }  

    $arrayIdArticulo     = array('idArticulo' => $textos->id('ID_AUTOMATICO'), 'plu_interno' => $textos->id('PLU'));    

    $tituArtiFact1 .= $textos->id($arrayIdArticulo[$idPrincipalArticulo]) . ' | ';
    $tituArtiFact1 .= $textos->id("ARTICULO") . ' | ';
    $tituArtiFact1 .= $textos->id("CANTIDAD") . ' | ';
    $tituArtiFact1 .= $textos->id("DESCUENTO") . ' | ';
    $tituArtiFact1 .= $textos->id("PRECIO") . ' | ';
    $tituArtiFact1 .= $textos->id("SUBTOTAL");

    $tituArtiFact = wordwrap($tituArtiFact1, 40, "\n");

    $subtotalFactura = 0;
    $descArtiFact    = '';

    //ciclo que va recorriendo el listado de articulos de una factura determinada y los imprime

    $contador = 0;
    $dctoTotalSobreArticulos = 0;

    foreach ($objeto->listaArticulos as $obj) {
        $contador++;
        if (strlen($obj->articulo) > 30) {
            $obj->articulo = substr($obj->articulo, 0, 29) . '.';
            
        }
        
        if ($obj->descuento == 0 || $obj->descuento == "0") {
            $obj->subtotal = $obj->cantidad * $obj->precio;
            
        } else {
            $descuentoArticulo = ( ( ($obj->cantidad * $obj->precio) * $obj->descuento) / 100 );
            $obj->subtotal = ($obj->cantidad * $obj->precio) - $descuentoArticulo;
            $dctoTotalSobreArticulos += $descuentoArticulo;
            
        }

        $obj->descuento     = Recursos::formatearNumero($obj->descuento, '%', '0');
        $obj->precio        = Recursos::formatearNumero($obj->precio, '$');
        $subtotalFactura   += $obj->subtotal;

        $obj->subtotal = Recursos::formatearNumero($obj->subtotal, '$');

        $descArtiFact1 = '';
        
        if($idPrincipalArticulo == 'id'){
            $idPrincipalArticulo = 'idArticulo';
        }        

        $descArtiFact1 .= $contador . ') ' . Recursos::completarCeros((int) $obj->$idPrincipalArticulo, 5) . ' | ';
        $descArtiFact1 .= $obj->articulo . ' | ';
        $descArtiFact1 .= $obj->cantidad . ' | ';
        $descArtiFact1 .= $obj->descuento . ' | ';
        $descArtiFact1 .= $obj->precio . ' | ';
        $descArtiFact1 .= $obj->subtotal;

        $descArtiFact2 = wordwrap($descArtiFact1, 40, "\n") . "\n";

        $descArtiFact .= $descArtiFact2;
    }

    $subtotalFactura += $objeto->valorFlete;
    
    $impuestoIva = $objeto->iva;

    $pieTirilla = '';
    
    $totalFactura = $subtotalFactura + $impuestoIva;
    
    if($objeto->valorFlete > 0){
    $pieTirilla .= $textos->id("VALOR_FLETE") . ": $" . $objeto->valorFlete . "\n\n";    
    
    }
    
    if ($dctoTotalSobreArticulos > 0){
        $pieTirilla .= $textos->id("DCTO_TOTAL_ARTICULOS") . ': $' . Recursos::formatearNumero($dctoTotalSobreArticulos, '$') . "\n";
    }
        
    $pieTirilla .= $textos->id("SUBTOTAL") . ': $' . Recursos::formatearNumero($subtotalFactura, '$') . "\n";  
    
    if ($impuestoIva > 0) {
        $pieTirilla .= $textos->id("IVA") . ": $" . $impuestoIva . "\n\n";     
        //$subtotalFactura += $impuestoIva;
    
    }
    
    if (!empty($objeto->concepto1) && !empty($objeto->descuento1)) {
        $pieTirilla .= $textos->id("DESCUENTOS") . ": \n";
        $pesosDcto1 = ($totalFactura * $objeto->descuento1) / 100;
        $totalFactura = $totalFactura - $pesosDcto1;
        $pieTirilla .= $objeto->concepto1 . ': ' . $objeto->descuento1 . '%   =>  $' . Recursos::formatearNumero($pesosDcto1, '$') . "\n";
        
    }

    if (!empty($objeto->concepto2) && !empty($objeto->descuento2)) {
        $pesosDcto2 = ($totalFactura * $objeto->descuento2) / 100;
        $totalFactura = $totalFactura - $pesosDcto2;
        $pieTirilla .= $objeto->concepto2 . ': ' . $objeto->descuento2 . '%   =>  $' . Recursos::formatearNumero($pesosDcto2, '$') . "\n";
        
    }

    //Agregar las retenciones realizadas en la compra a la factura de compra
    $totalRetenciones = 0;

    if (count($objeto->arregloRetenciones) > 0) {
        foreach ($objeto->arregloRetenciones as $key => $value) {
            if ($key != "Iva Teorico"){
                $pieTirilla .= "\n" . $key . ': $' . Recursos::formatearNumero($value, '$') . "\n";
                $totalRetenciones += $value;
            }

        }

       $pieTirilla .= "\n" . $textos->id("TOTAL_RETENCIONES") . ': $' . Recursos::formatearNumero($totalRetenciones, '$') . "\n";

    }

    $pieTirilla .= "\n" . $textos->id("TOTAL") . ': $' . Recursos::formatearNumero( ($totalFactura - $totalRetenciones), '$') . "\n";

    $imprimir = "" .
            "\n\n" . wordwrap($tirilla_encabezado, 40) .
            $tirilla_proveedor .
            $tirilla_comentario . "\n" .
            $tituArtiFact . "\n\n" .
            $descArtiFact . "\n" .
            wordwrap($tirilla_usuario, 40) .
            "  -----------------------------------  \n" .
            wordwrap($pieTirilla, 40) .
            "\n\n\n\n\n\n\n\n";
    
    file_put_contents($fichero, $imprimir);
    
    chmod($fichero, 0777);
    
    //exec("cat $fichero > /dev/usb/lp0");

    //aqui se pregunta si se desean tambien imprimir los códigos de barra
    if (isset($datos["imprimir_codigos_barras"]) && !empty($datos["imprimir_codigos_barras"])) {
        
        
        define('FPDF_FONTPATH', 'recursos/fuentes/');

        $pdf2 = new eFPDF('P', 'pt');

        $nombrePdf2 = 'media/pdfs/temporales/codigos_barra_fact_' . $idItem . '.pdf';
        $nombrePdf2 = trim($nombrePdf2);

        foreach ($objeto->listaArticulos as $objArti) {
            $pdf2->AddPage();
            
            $tablas     = array('a' => 'articulos');
            $columnas   = array('nombre' => 'a.nombre', 'datoCodigoBarra' => 'a.' . $sesion_configuracionGlobal->datoCodigoBarra,);
            $condicion  = ' id = "' . $objArti->idArticulo . '" ';

            $obj = $sql->filaEnObjeto($sql->seleccionar($tablas, $columnas, $condicion));

            $fontSize   = 10;
            $marge      = 10;   // between barcode and hri in pixel
            $x          = 100;  // barcode center
            $y          = 50;  // barcode center
            $height     = 50;   // barcode height in 1D ; module size in 2D
            $width      = 1;    // barcode height in 1D ; not use in 2D
            $angle      = 0;   // rotation in degrees
            // barcode, of course ;)
            $type   = 'code39';
            $black  = '000000'; // color in hexa
            // -------------------------------------------------- //
            //            ALLOCATE FPDF RESSOURCE
            // -------------------------------------------------- //
            // -------------------------------------------------- //
            //                      BARCODE
            // -------------------------------------------------- //
            // -------------------------------------------------- //
            //                      HRI
            // -------------------------------------------------- //
            $contador = 0; //va contando los barcode que se imprimen en una linea, salta de linea cuando se imprimen 3
            $nuevaPagina = 0; //cuenta las lineas que se imprimen, cuando hay siete, toca agregar una nueva página(teniendo en cuenta los tamaños actuales)

            for ($i = 0; $i < $objArti->cantidad; $i++) {

                if ($contador >= 3) {//ya imprimio los que caben en una linea
                    $contador    = 0; //reinicio el contador
                    $y          += 120; //aumentola posicion del y
                    $x           = 100; //reinicio el x
                    $nuevaPagina++; //sumo que se acaba de imprimir una nueva linea
                }

                if ($nuevaPagina == 7) {//Termino de imprimir una página
                    $nuevaPagina = 0;
                    $pdf2->AddPage();
                    $x          = 100;  // reinicio x a los valores de una página nueva
                    $y          = 50;  // reinicio y a los valores de una página nueva              
                }

                $code   = Recursos::completarCeros($obj->datoCodigoBarra, 8);
                $data   = Barcode::fpdf($pdf2, $black, $x, $y, $angle, $type, array('code' => $code), $width, $height);

                $pdf2->SetFont('Arial', 'B', $fontSize);
                $pdf2->SetTextColor(0, 0, 0);
                $len = $pdf2->GetStringWidth($data['hri']);
                Barcode::rotate(-$len / 2, ($data['height'] / 2) + $fontSize + $marge, $angle, $xt, $yt);
                $pdf2->TextWithRotation($x + $xt, $y + $yt, $data['hri'], $angle);
                $nomArticulo = $obj->nombre;
                
                if (strlen($nomArticulo) > 32) {
                    $nomArticulo = substr($nomArticulo, 0, 32);
                }
                
                $len2 = $pdf2->GetStringWidth($nomArticulo);
                Barcode::rotate(-$len2 / 2, (($data['height'] + 20) / 2) + $fontSize + $marge, $angle, $xt, $yt);
                $pdf2->TextWithRotation($x + $xt, $y + $yt, $nomArticulo, $angle);

                $contador++; // incremento contador porque se ha impreso un codigo
                $x += 200; //corro el centro de impresion del codigo de barras sobre el eje x
                
            }
            
        }

        $pdf2->Output($nombrePdf2, 'F');
        chmod($nombrePdf2, 0777);

        $respuesta["otro_archivo"] = true;
        $respuesta["destino2"] = 'media/pdfs/temporales/codigos_barra_fact_' . $idItem . '.pdf';
    }

    $respuesta["error"]             = NULL;
    $respuesta["accion"]            = "abrir_ubicacion";
    $respuesta["destino"]           = $fichero;
    $respuesta["info"]              = true;
    $respuesta["recargar"]          = true;
    $respuesta["textoInfo"]         = $textos->id('FACTURA_GENERADA_EXITOSAMENTE');
    
    //unlink($fichero);

    Servidor::enviarJSON($respuesta);

    // exec("/usr/bin/lp -d $forma_id_impresora $ruta");
    //unlink($ruta);    
}

/**
 *
 * funcion que se encarga de ir guardando la factura mientras un empleado la va
 * realizando, esto teniendo en cuenta que se cierre la sesion del usuario por
 * cualquier motivo, pueda recuperar lo avanzado mientras realizaba la factura
 * 
 * @param arreglo $datos arreglo con los datos a ser utilizados en esta funcióninfo de la factura a ser guardada
 */
function guardarFacturaTemporal($datos) {
    
    $objeto     = new FacturaTemporalCompra();
    $idItem     = $objeto->adicionarFacturaTemporal($datos);

    $respuesta              = array();
    $respuesta['codigo']    = $idItem;

    Servidor::enviarJSON($respuesta);
    
}

/**
 *
 * funcion que se encarga de ir modificando una  factura temporal mientras el empleado la va
 * realizando, esto teniendo en cuenta que se cierre la sesion del usuario por
 * cualquier motivo, pueda recuperar lo avanzado mientras realizaba la factura.
 * 
 * @param arreglo $datos arreglo con los datos a ser utilizados en esta funcióninfo de la factura a ser guardada
 */
function modificarFacturaTemporal($datos) {

    $objeto     = new FacturaTemporalCompra();
    $idItem     = $objeto->modificarFacturaTemporal($datos);

    $respuesta              = array();
    $respuesta['codigo']    = $idItem;

    Servidor::enviarJSON($respuesta);
    
}

/**
 * Funcion que devuelve la respuesta para el autocompletar de listar las ordenes de compra
 * 
 * @global objeto $sql objeto global de interaccion con la BD
 * @param string $cadena cadena para realizar la comparación durante la busqueda
 */
function listarOrdenes($cadena) {
    global $sql;
    
    $respuesta  = array();

    $tablas     = array('oc' => 'ordenes_compra', 'p' => 'proveedores');
    $columnas   = array('id' => 'oc.id', 'idProveedor' => 'oc.id_proveedor', 'proveedor' => 'p.nombre');
    $condicion  = 'oc.id_proveedor = p.id AND oc.id LIKE "%' . $cadena . '%"';

    $consulta   = $sql->seleccionar($tablas, $columnas, $condicion, '', 'p.nombre ASC', 0, 20);

    while ($fila = $sql->filaEnObjeto($consulta)) {
        $respuesta1             = array();
        $respuesta1["label"]    = $fila->id . ' :: ' . $fila->proveedor;
        $respuesta1["value"]    = $fila->id;
        $respuesta1["nombre"]   = $fila->proveedor;
        
        $respuesta[]            = $respuesta1;
        
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
