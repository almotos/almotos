<?php

/**
 * @package     FOM (Framework OpenSource Multiplatform)
 * @subpackage  Ventas de Mercancia
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
        
        case 'search'                   :   $datos = ($forma_procesar) ? $forma_datos : array();
                                            buscarItem($forma_datos);
                                            break;
        
        case 'move'                     :   paginador($forma_pagina, $forma_orden, $forma_nombreOrden, $forma_consultaGlobal);
                                            break;
        
        case 'listar'                   :   listarItems($url_cadena);
                                            break;
        
        case 'buscarFactura'            :   $datos = ($forma_procesar) ? $forma_datos : array();
                                            buscarFactura($forma_datos);
                                            break;
        
        case 'imprimirFacturaVentaPdf'  :   imprimirFacturaVentaPdf($forma_datos);
                                            break;
        
        case 'imprimirFacturaVentaPos'  :   imprimirFacturaVentaPos($forma_datos);
                                            break;
        
        case 'guardarFacturaTemporal'   :   guardarFacturaTemporal($forma_datos);
                                            break;
        
        case 'modificarFacturaTemporal' :   modificarFacturaTemporal($forma_datos);
                                            break;
        
        case 'escogerBodega'            :   escogerBodega($forma_idSede);
                                            break;
        
        case 'buscarCotizacion'         :   $datos = ($forma_procesar) ? $forma_datos : array();
                                            buscarCotizacion($forma_datos);
                                            break;
        
        case 'listarCotizaciones'       :   listarCotizaciones($url_cadena);
                                            break;
        
        case 'validarPermisoDcto'       :   $datos = ($forma_procesar) ? $forma_datos : array();
                                            validarPermisoDcto($forma_datos);
                                            break;
        
    }
    
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
    global $textos, $configuracion, $sql;

    $datosFactura = array(
        'datos[id_cliente]'                 => $datos['id_cliente'],
        'datos[fecha_factura]'              => $datos['fecha_factura'],
        'datos[fecha_vto_factura]'          => ($datos['fecha_vto_factura']) ? $datos['fecha_vto_factura'] : "",
        'datos[modo_pago]'                  => ($datos['modo_pago']) ? $datos['modo_pago'] : "",
        'datos[iva]'                        => $datos['iva'],
        'datos[id_caja]'                    => $datos['id_caja'],
        'datos[id_usuario]'                 => $datos['id_usuario'],
        'datos[concepto1]'                  => $datos['concepto1'],
        'datos[descuento1]'                 => $datos['descuento1'],
        'datos[concepto2]'                  => $datos['concepto2'],
        'datos[descuento2]'                 => $datos['descuento2'],
        'datos[fecha_limite_dcto_1]'        => $datos['fecha_limite_dcto_1'],
        'datos[porcentaje_dcto_1]'          => $datos['porcentaje_dcto_1'],
        'datos[fecha_limite_dcto_2]'        => $datos['fecha_limite_dcto_2'],
        'datos[porcentaje_dcto_2]'          => $datos['porcentaje_dcto_2'],
        'datos[valor_flete]'                => $datos['valor_flete'],
        'datos[subtotal]'                   => $datos['subtotal'],
        'datos[total]'                      => $datos['total'],
        'datos[observaciones]'              => $datos['observaciones'],
        'datos[es_modificacion]'            => $datos['es_modificacion'], //determina si es una factura exisente a ser modificada
        'datos[modificacion_cotizacion]'    => $datos['modificacion_cotizacion'], //determina si se trata de modificar una orden de venta, y si se ejecuta, elimina la orden de venta del sistema
        'datos[cadenaArticulosPrecios]'     => $datos['cadenaArticulosPrecios'],
        'datos[id_factura_temporal]'        => $datos['es_factura_temporal']
    );


    $codigo  = HTML::campoOculto('procesar', 'true');
    
    //consulto cuales son las posibles retenciones que me haria el comprador
    $contabilidad           = new Contabilidad();
    $respuestaRetenciones   = $contabilidad->generarCamposRetenciones($datos['id_cliente'], $datos['total'], $datos['iva'], "ventas");   

    $camposRetencion    = $respuestaRetenciones["campos_retencion"];
    $totalRetenciones   = $respuestaRetenciones["total_retenciones"];
    $totalAPagar        = $respuestaRetenciones["total_a_pagar"];            

    $campos  = HTML::campoChequeo('', true, '', 'checkEfectivo');
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
    
    $botones = "";
    
    if ($totalRetenciones > 0){
        $botones .= HTML::parrafo('', 'negrilla margenSuperiorDoble');
        $botones .= HTML::frase($textos->id("TOTAL"), 'negrilla subtitulo margenIzquierda');
        $botones .= HTML::frase('$'.Recursos::formatearNumero($datos['total'], '$'), 'negrilla masGrande2 letraAzul margenIzquierdaDoble');   
        $botones .= HTML::campoOculto('campoTotalAPagar', $datos['total'], 'campoTotalAPagar');

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
    $botones .= HTML::campoOculto('campoTotalFinFactura', $totalAPagar, 'campoTotalFinFactura');               

    $botones .= HTML::parrafo('', 'negrilla margenSuperior');
    
    $botones .= HTML::frase($textos->id('CANCELA_CON'), 'negrilla subtitulo margenSuperior margenIzquierda');
    $botones .= HTML::campoTexto('datos[total_pago_cliente]', 15, 255, '', 'margenIzquierda campoDinero', 'campoTotalPagoCliente');  
    
    $botones .= HTML::frase($textos->id("DEBE_DEVOLVER"), 'negrilla subtitulo margenSuperior');
    $botones .= HTML::frase('$0', 'negrilla masGrande2 letraVerde', 'valADevolver');     
    
    $botonesPdf = HTML::botonAjax('impresora', $textos->id('IMPRIMIR_FACTURA_IMPRESORA'), '/ajax/ventas_mercancia/imprimirFacturaVentaPdf', $datosFactura, ' btnImpresionFactura', 'btnImprimirFacturaPdf');
    $botonesPos = HTML::botonAjax('impresora', $textos->id('IMPRIMIR_FACTURA_POS'), '/ajax/ventas_mercancia/imprimirFacturaVentaPos', $datosFactura, ' btnImpresionFactura', 'btnImprimirFacturaPos');

    $botones .= HTML::contenedorCampos($botonesPdf, $botonesPos, 'margenSuperiorDoble margenIzquierda');  
    
    $botonesOrdComp  = HTML::botonAjax('libreta', $textos->id('GENERAR_COTIZACION'), '/ajax/cotizaciones/add', $datosFactura, ' btnImpresionFactura', 'btnGenerarCotizacion');
    $botonesCancela  = HTML::boton('cancelar', $textos->id('CANCELAR'), 'directo  botonCancelar', '', 'botonCancelarAccionFactura');    

    $botones .= HTML::contenedorCampos($botonesOrdComp, $botonesCancela, 'margenSuperior margenIzquierda');    
    
    $codigo  .= HTML::parrafo($botones, 'margenSuperior');
    $codigo  .= HTML::parrafo($textos->id('ACCION_EJECUTADA'), 'textoExitoso', 'textoExitoso');

    $respuesta                  = array();
    $respuesta['generar']       = true;
    $respuesta['cargarJs']      = true;
    $respuesta['archivoJs']     = $configuracion['SERVIDOR']['media'] . $configuracion['RUTAS']['javascript'] . '/modulos/ventas_mercancia/funcionesFinFactura.js';
    $respuesta['codigo']        = $codigo;
    $respuesta['destino']       = '#cuadroDialogo';
    $respuesta['titulo']        = HTML::parrafo($textos->id('CONFIRMAR_ACCION_FACTURA'), 'letraBlanca negrilla subtitulo');
    $respuesta['ancho']         = 600;
    $respuesta['alto']          = 500;

    Servidor::enviarJSON($respuesta);
    
}

/**
 * Función que se encarga de realizar una busqueda de acuerdo a una condicion que se
 * le pasa. Es llamada cuando se ingresa un texto en el campo de busqueda en la pantalla principal del modulo.
 * Una vez es llamada esta función, se encarga de recargar la tabla de registros con los datos coincidientes 
 * en el patrón de busqueda.
 *
 * @global objeto $textos               = objeto global que gestiona los textos a traducir
 * @global arreglo $configuracion       = arreglo global de configuracion
 * @param arreglo $data                 = arreglo con los parametros de busqueda
 * @param int $cantidadRegistros        = cantidad de registros aincluir por busqueda
 */
function buscarItem($data) {
    global $textos, $configuracion;

    $data  = explode('[', $data);
    $datos = $data[0];

    if (empty($datos)) {
        $respuesta['error']     = true;
        $respuesta['mensaje']   = $textos->id('ERROR_FALTA_CADENA_BUSQUEDA');
        
    } else if (!empty($datos) && strlen($datos) < 2) {
        $respuesta['error']     = true;
        $respuesta['mensaje']   = str_replace('%1', '2', $textos->id('ERROR_TAMAÑO_CADENA_BUSQUEDA'));
        
    } else {
        $item               = '';
        $respuesta          = array();
        
        $objeto             = new FacturaVenta();
        
        $registros          = $configuracion['GENERAL']['registrosPorPagina'];
        $pagina             = 1;
        $registroInicial    = 0;

        $palabras = explode(" ", $datos);

        $condicionales = $data[1];

        if ($condicionales == "") {
            $condicion = "(c.nombre REGEXP '(" . implode("|", $palabras) . ")')";
            
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

        $arregloItems = $objeto->listar($registroInicial, $registros, array("0"), $condicion, "c.nombre");

        if ($objeto->registrosConsulta) {//si la consulta trajo registros
            $datosPaginacion = array($objeto->registrosConsulta, $registroInicial, $registros, $pagina, $objeto->registrosConsulta);
            $item           .= $objeto->generarTabla($arregloItems, $datosPaginacion);
            $info            = HTML::parrafo(str_replace('%', $objeto->registrosConsulta, $textos->id('RESULTADOS_BUSQUEDA')), 'textoExitosoNotificaciones');
            
        } else {
            $datosPaginacion = 0;
            $item .= $objeto->generarTabla($textos->id('NO_HAY_REGISTROS'), $datosPaginacion);
            $info  = HTML::parrafo($textos->id('BUSQUEDA_SIN_RESULTADOS'), 'textoErrorNotificaciones');
            
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
function paginador($pagina, $orden = NULL, $nombreOrden = NULL, $consultaGlobal = NULL) {
    global $configuracion;

    $item = '';
    $respuesta = array();
    $objeto = new FacturaVenta();

    $registros = $configuracion['GENERAL']['registrosPorPagina'];

    if (isset($pagina)) {
        $pagina = $pagina;
    } else {
        $pagina = 1;
    }

    if (isset($consultaGlobal) && $consultaGlobal != "") {

        $data       = explode("[", $consultaGlobal);
        $datos      = $data[0];
        $palabras   = explode(" ", $datos);

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
            $consultaGlobal = "(c.nombre REGEXP '(" . implode("|", $palabras) . ")')";
            
        }
    } else {
        $consultaGlobal = "";
        
    }

    if (!isset($nombreOrden)) {
        $nombreOrden = $objeto->ordenInicial;
        
    }


    if (isset($orden) && $orden == "ascendente") {//ordenamiento
        $objeto->listaAscendente = true;
        
    } else {
        $objeto->listaAscendente = false;
        
    }

    if (isset($nombreOrden) && $nombreOrden == "estado") {//ordenamiento
        $nombreOrden = "activo";
        
    }

    $registroInicial = ($pagina - 1) * $registros;


    $arregloItems = $objeto->listar($registroInicial, $registros, array("0"), $consultaGlobal, $nombreOrden);

    if ($objeto->registrosConsulta) {//si la consulta trajo registros
        $datosPaginacion = array($objeto->registrosConsulta, $registroInicial, $registros, $pagina);
        $item           .= $objeto->generarTabla($arregloItems, $datosPaginacion);
    }

    $respuesta['error']         = false;
    $respuesta['accion']        = 'insertar';
    $respuesta['contenido']     = $item;
    $respuesta['idContenedor']  = '#tablaRegistros';
    $respuesta['idDestino']     = '#contenedorTablaRegistros';
    $respuesta['paginarTabla']  = true;

    Servidor::enviarJSON($respuesta);
}

/**
 *
 * @global type $textos
 * @global type $configuracion
 * @param type $datos 
 */
function buscarFactura($datos) {
    global $textos, $configuracion, $sql;

    $destino        = '/ajax/ventas_mercancia/buscarFactura';
    $respuesta      = array();
    $codigo         = '';
    
    if (empty($datos)) {
        $codigo  = HTML::campoOculto('procesar', 'true');
        $codigo .= HTML::campoOculto('datos[dialogo]', '', 'idDialogo');
        $codigo .= HTML::parrafo($textos->id('CLIENTE'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[cliente]', 40, 255, '', 'autocompletable campoObligatorio', 'campoIdCliente', array('title' => '/ajax/clientes/listar'), $textos->id('AYUDA_USO_AUTOCOMPLETAR'), '/ajax/clientes/add', 'datos[id_cliente]', '');
        $codigo .= HTML::parrafo($textos->id('FECHA_INICIAL'), 'margenSuperior negrilla');
        $codigo .= HTML::campoTexto("datos[fecha_inicial]", 12, 12, '', "fechaAntigua", "", array("alt" => $textos->id("SELECCIONE_FECHA_INICIAL")));
        $codigo .= HTML::parrafo($textos->id('FECHA_FINAL'), 'margenSuperior negrilla');
        $codigo .= HTML::campoTexto("datos[fecha_final]", 12, 12, '', "fechaAntigua", "", array("alt" => $textos->id("SELECCIONE_FECHA_FINAL")));
        $codigo .= HTML::parrafo(HTML::boton('chequeo', $textos->id('ACEPTAR'), 'directo', '', 'tablaBusquedaFacturas', '', array('validar' => 'NoValidar')), 'margenSuperior');
        $codigo  = HTML::forma($destino, $codigo, 'P', true);

        $respuesta['generar']   = true;
        $respuesta['codigo']    = $codigo;
        $respuesta['titulo']    = HTML::parrafo($textos->id('BUSCAR_FACTURAS'), 'letraBlanca negrilla subtitulo');
        $respuesta['destino']   = '#cuadroDialogo';
        $respuesta['ancho']     = 450;
        $respuesta['alto']      = 250;
        
    } else {
        
        if (empty($datos['id_cliente']) || (!empty($datos['id_cliente']) && !$sql->existeItem('clientes', 'id', $datos['id_cliente'])) || empty($datos['fecha_inicial']) || empty($datos['fecha_final'])) {
            $respuesta              = array();
            $respuesta['error']     = true;
            $respuesta['mensaje']   = $textos->id('ERROR_DEBE_LLENAR_TODOS_LOS_CAMPOS_DEL_FORMULARIO');

            Servidor::enviarJSON($respuesta);
            return NULL;
        }          

        $excluidas = array(0);
        
        $objeto = new FacturaVenta();
        
        $objeto->listaAscendente = true;

        $condicion = ' fv.id_cliente = "' . $datos["id_cliente"] . '" AND fecha_factura BETWEEN "' . $datos["fecha_inicial"] . '" AND "' . $datos["fecha_final"] . '"';

        $listaFacturas = $objeto->listar(0, 100, $excluidas, $condicion, $orden);

        if (!empty($objeto->registrosConsulta)) {
            //crear los formularios con la info para las demas sedes
            $datosTablaFacturasVenta = array(
                HTML::parrafo($textos->id('NUMERO_DE_FACTURA'), 'centrado')     => 'id', //concateno el nombre del alias para usarlo al armar la tabla con el fila en objeto
                HTML::parrafo($textos->id('SEDE'), 'centrado')                  => 'sede', //y concateno el alias de la tabla junto con el campo para usarlo al realizar
                HTML::parrafo($textos->id('CLIENTE'), 'centrado')               => 'cliente', //la busqueda, al armar la tabla dividira la cadena y usara el que necesite
                HTML::parrafo($textos->id('FECHA_FACTURA'), 'centrado')         => 'fechaFactura'
            );


            $rutas              = array();
            $idTabla            = 'tablaListarFacturas';
            $claseTabla         = 'tablaListarItems';
            $estilosColumnas    = array('', '', '', '', '');
            $contenedor         = HTML::contenedor(Recursos::generarTablaRegistrosInterna($listaFacturas, $datosTablaFacturasVenta, $rutas, $idTabla, $estilosColumnas, $claseTabla), 'flotanteIzquierda margenDerecha');
        
            
        } else {
            $contenedor = HTML::contenedor(HTML::parrafo($textos->id('BUSQUEDA_SIN_RESULTADOS'), 'textoError'));
            
        }

        $codigo  = HTML::parrafo($textos->id('AYUDA_SELECCIONAR_ITEMS_TABLA_FACTURAS'), 'letraVerde negrilla subtitulo');
        $codigo .= $contenedor;

        $respuesta['generar']       = true;
        $respuesta['cargarJs']      = true;
        $respuesta['archivoJs']     = $configuracion['SERVIDOR']['media'] . $configuracion['RUTAS']['javascript'] . '/modulos/ventas_mercancia/funcionesBuscarFacturas.js';
        $respuesta['codigo']        = $codigo;
        $respuesta['titulo']        = HTML::parrafo($textos->id('BUSCAR_FACTURAS'), 'letraBlanca negrilla subtitulo');
        $respuesta['destino']       = '#cuadroDialogo';
        $respuesta['ancho']         = 800;
        $respuesta['alto']          = 540;
    }

    Servidor::enviarJSON($respuesta);
    
}

/**
 *
 * @global type $configuracion
 * @param type $prueba 
 */
function imprimirFacturaVentaPdf($datos) {
    global $textos, $sql, $sesion_configuracionGlobal;

    $respuesta = array();

    if (!empty($datos['es_modificacion'])) {
        $objeto     = new FacturaVenta($datos['es_modificacion']);
        $idItem     = $objeto->modificar($datos);
        $idItem     = $datos['es_modificacion'];
        
    } else if (!empty($datos['modificacion_cotizacion'])) { //si se trata de generar una factura desde una orden de venta
        $objeto     = new FacturaVenta();
        $idItem     = $objeto->adicionar($datos);
        
        if (!empty($idItem)) {//si se pudo crear la factura de venta
            $orden = new Cotizacion($datos['modificacion_cotizacion']); //creo el objeto de la orden de venta
            $orden->eliminar(); //y la elimino, pues ya no es necesaria    
            
        }
        
    } else if (!empty($datos['id_factura_temporal'])) { //si se trata de generar una factura desde una factura que no se finalizo previamente
        $objeto     = new FacturaVenta();
        $idItem     = $objeto->adicionar($datos);
        
        if (!empty($idItem)) {//si se pudo crear la factura de venta
            FacturaTemporalVenta::eliminarFacturaTemporal($datos['id_factura_temporal']); //eliminar la factura temporal creada   

        }
        
    } else {
        $objeto     = new FacturaVenta();
        $idItem     = $objeto->adicionar($datos);
        
    }

    //verificar que la sede en la cual se quiere facturar tenga una resolucion de la DIAN activa
    if ($objeto->sinResolucion) {
        $respuesta              = array();
        $respuesta['error']     = true;
        $respuesta['mensaje']   = $objeto->sinResolucion;

        Servidor::enviarJSON($respuesta);
        return NULL;
        
    } else if ($objeto->fueraResolucion) {//verificar que el numero de factura este dentro del numero de factura de la DIAN
        $respuesta              = array();
        $respuesta['error']     = true;
        $respuesta['mensaje']   = $objeto->fueraResolucion;

        Servidor::enviarJSON($respuesta);
        return NULL;
        
    }

    define('FPDF_FONTPATH', 'recursos/fuentes/');
    $nombrePdf = 'media/pdfs/facturas_venta/factura_venta_' . $idItem . '.pdf';
    $nombrePdf = trim($nombrePdf);


    if (!isset($idItem) || (isset($idItem) && !$sql->existeItem('facturas_venta', 'id', $idItem))) {
        $respuesta              = array();
        $respuesta["error"]     = true;
        $respuesta["mensaje"]   = $textos->id("NO_HA_SELECCIONADO_ITEM");

        Servidor::enviarJSON($respuesta);
        return NULL;
    }

    $objeto = new FacturaVenta($idItem);

    $pdf = new PdfFacturaVenta('P', 'mm', 'letter');

    $pdf->SetTopMargin(0.7);
    $pdf->AddPage();
    
    //Aqui en las facturas de venta es necesario especificar la actividad economica y el
    //porcentaje de retecree


    $pdf->Ln(3);

    //primeros datos de la factura
    $pdf->SetFont('times', 'B', 11);
    $pdf->Cell(70, 7, $textos->id("TIPO_DE_FACTURA") . '  ', 0, 0, 'L');
    $pdf->SetFont('times', 'B', 7);
    $pdf->Cell(15, 7, $textos->id("NUMERO") . ': ', 0, 0, 'L');
    $pdf->SetFont('times', '', 7);
    $pdf->Cell(45, 7, $objeto->idFactura, 0, 0, 'L');
    $pdf->Ln(4);


    $pdf->SetFont('times', 'B', 7);
    $pdf->Cell(8, 7, $textos->id("NIT") . ': ', 0, 0, 'L');
    $pdf->SetFont('times', '', 7);
    $pdf->Cell(27, 7, $objeto->idCliente, 0, 0, 'L');

    $pdf->SetFont('times', 'B', 7);
    $pdf->Cell(19, 7, '', 0, 0, 'L');
    $pdf->Cell(15, 7, $textos->id("CLIENTE") . ': ', 0, 0, 'L');
    $pdf->SetFont('times', '', 7);
    $pdf->Cell(20, 7, $objeto->cliente, 0, 0, 'L');

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

    if ($objeto->fechaVtoFactura != '' && $objeto->fechaVtoFactura != "0000-00-00") {
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
    $pdf->Cell(15, 8, $textos->id($arrayIdArticulo[$idPrincipalArticulo]), 0, 0, 'C');
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
    $pdf->Cell(16, 8, $textos->id("DESCUENTO"), 0, 0, 'C');
    $pdf->Cell(10, 8, '', 0, 0, 'L');
    
    if ($objeto->iva > 0){
        $pdf->SetFont('times', 'B', 8);
        $pdf->Cell(10, 8, $textos->id("IVA"), 0, 0, 'C');    
        $pdf->Cell(5, 8, '', 0, 0, 'L');        
    }
    
    $pdf->SetFont('times', 'B', 8);
    $pdf->Cell(12, 8, $textos->id("PRECIO_UNITARIO"), 0, 0, 'C');
    $pdf->Cell(8, 8, '', 0, 0, 'L');
    $pdf->SetFont('times', 'B', 8);
    $pdf->Cell(18, 8, $textos->id("SUBTOTAL"), 0, 0, 'C');
    $pdf->Cell(9, 8, '', 0, 0, 'L');

    $pdf->Ln(1);

    $subtotalFactura    = 0;
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
        $obj->precio    = '$ '.Recursos::formatearNumero($obj->precio, '$');

        $subtotalFactura += $obj->subtotal;

        $iva = '$ '.Recursos::formatearNumero($obj->subtotal * ( $obj->iva / 100) );

        $obj->subtotal = '$ '.Recursos::formatearNumero($obj->subtotal, '$');

        //consulto la referencia del articulo
        //$sql->depurar = true;
        $referencia = $sql->obtenerValor('articulos', 'referencia', 'id = "'.$obj->idArticulo.'" ');
        
        $referencia = ($referencia) ? $referencia : "000000";

        $pdf->SetFont('times', '', 7);
        $pdf->Cell(15, 8, (int)$obj->$idPrincipalArticulo, 0, 0, 'C');
        $pdf->SetFont('times', '', 7);
        $pdf->Cell(20, 8, $referencia, 0, 0, 'L');        
        $pdf->SetFont('times', '', 7);
        $pdf->Cell(65, 8, $obj->articulo, 0, 0, 'L');
        $pdf->Cell(3, 8, '', 0, 0, 'L');
        $pdf->SetFont('times', '', 7);
        $pdf->Cell(10, 8, $obj->cantidad, 0, 0, 'L');
        $pdf->Cell(5, 8, '', 0, 0, 'L');
        $pdf->SetFont('times', '', 7);
        $pdf->Cell(15, 8, $obj->descuento, 0, 0, 'L');
        $pdf->Cell(8, 8, '', 0, 0, 'L');
        
        if ($objeto->iva > 0){
            $pdf->SetFont('times', '', 7);
            $pdf->Cell(10, 8, $iva, 0, 0, 'L');        
            $pdf->Cell(5, 8, '', 0, 0, 'L');
            
        } 
        
        $pdf->SetFont('times', '', 7);
        $pdf->Cell(12, 8, $obj->precio, 0, 0, 'L');
        $pdf->Cell(8, 8, '', 0, 0, 'L');
        $pdf->SetFont('times', '', 7);
        $pdf->Cell(18, 8, $obj->subtotal, 0, 0, 'L');
        $pdf->Cell(9, 8, '', 0, 0, 'L');
    }

    $pdf->Ln(1);
    $pdf->Cell(197, 7, '', 'B', 0, 'L');

    $subtotalFactura   += $objeto->valorFlete;
    
    $pdf->Ln(7);

    $pdf->SetFont('times', 'B', 7);
    $pdf->Cell(170, 7, $textos->id('VALOR_FLETE'), 0, 0, 'R');
    $pdf->SetFont('times', '', 7);
    $pdf->Cell(30, 7, '$'.Recursos::formatearNumero($objeto->valorFlete, '$'), 0, 0, 'R');
    
    if ($objeto->iva > 0) {
        $subtotalFactura -= $objeto->iva;
    }
    
    $pdf->Ln(4);
    $pdf->SetFont('times', 'B', 8);
    $pdf->Cell(170, 7, $textos->id("DCTO_TOTAL_ARTICULOS") . ':   ', 0, 0, 'R');
    $pdf->SetFont('times', 'B', 8);
    $pdf->Cell(30, 7, '$'.Recursos::formatearNumero($dctoTotalSobreArticulos, '$'), 0, 0, 'R');
    
    $pdf->Ln(4);
    $pdf->SetFont('times', 'B', 10);
    $pdf->Cell(170, 7, $textos->id("SUBTOTAL") . ':   ', 0, 0, 'R');
    $pdf->SetFont('times', 'B', 10);
    $pdf->Cell(30, 7, '$'.Recursos::formatearNumero($subtotalFactura, '$'), 0, 0, 'R');    
    
//    $pdf->SetFont('times', 'B', 7);
//    $pdf->Cell(5, 7, '', 0, 0, 'L');
//    $pdf->SetFont('times', 'B', 7);
//    $pdf->Cell(13, 7, $textos->id("IVA_FLETE") . ': ', 0, 0, 'L');
//    $pdf->SetFont('times', '', 7);
//    $pdf->Cell(18, 7, '$ '.Recursos::formatearNumero( ($objeto->valorFlete * ($sesion_configuracionGlobal-> ivaGeneral / 100) ), '$'), 0, 0, 'L');    

    if ($objeto->iva > 0) {
        $pdf->Ln(6);

        $pdf->SetFont('times', 'B', 7);
        $pdf->Cell(170, 7, $textos->id('TOTAL_IVA'), 0, 0, 'R');
        $pdf->SetFont('times', '', 7);
        $pdf->Cell(30, 7, '$'.Recursos::formatearNumero($objeto->iva, '$'), 0, 0, 'R');
        
        //$subtotalFactura += $objeto->iva;
    }

    $totalFactura = $subtotalFactura;

    $totalDescuentos = 0;

    if (!empty($objeto->concepto1) && !empty($objeto->descuento1)) {
        $pdf->Ln(5);
        $pdf->SetFont('times', 'B', 8);
        $pdf->Cell(170, 7, $textos->id("DESCUENTOS") . ': ', 0, 0, 'R');

        $pdf->Ln(2);        

        $pesosDcto1   = ($totalFactura * $objeto->descuento1) / 100;
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

    $totalFactura += ($objeto->iva) ? $objeto->iva : 0;

    $pdf->Ln(7);
    $pdf->SetFont('times', 'B', 14);
    $pdf->Cell(170, 7, $textos->id("TOTAL") . ':  ', 0, 0, 'R');
    $pdf->SetFont('times', 'B', 14);
    $pdf->Cell(30, 7, '$'.Recursos::formatearNumero($totalFactura, '$'), 0, 0, 'R');
    
    if (!empty($objeto->fechaLimiteDcto1) && !empty($objeto->porcentajeDcto1)) {

        $pesosDctoExtra1 = ($totalFactura * $objeto->porcentajeDcto1) / 100;
        $pdf->Ln(3);
        $pdf->SetFont('times', 'B', 7);
        $pdf->Cell(190, 7, '*'.$textos->id('FECHA_LIMITE_PAGO_PARA_DESCUENTO') . ': ' . $objeto->fechaLimiteDcto1 . ' ' . $textos->id('PORCENTAJE_DESCUENTO') . ': ' . $objeto->porcentajeDcto1 . '% --> ' . '$'.Recursos::formatearNumero($pesosDctoExtra1, '$') . ' --> ' . $textos->id('PAGANDO_EN_TOTAL') . ' ' . '$'.Recursos::formatearNumero(($totalFactura - $pesosDctoExtra1), '$'), 0, 0, 'L');
    }

    if (!empty($objeto->fechaLimiteDcto2) && !empty($objeto->porcentajeDcto2)) {

        $pesosDctoExtra2 = ($totalFactura * $objeto->porcentajeDcto2) / 100;
        $pdf->Ln(3);
        $pdf->SetFont('times', 'B', 7);
        $pdf->Cell(190, 7, '*'.$textos->id('FECHA_LIMITE_PAGO_PARA_DESCUENTO') . ': ' . $objeto->fechaLimiteDcto2 . ' ' . $textos->id('PORCENTAJE_DESCUENTO') . ': ' . $objeto->porcentajeDcto2 . '% --> ' . '$'.Recursos::formatearNumero($pesosDctoExtra2, '$') . ' --> ' . $textos->id('PAGANDO_EN_TOTAL') . ' ' . '$'.Recursos::formatearNumero(($totalFactura - $pesosDctoExtra2), '$'), 0, 0, 'L');
    }    

    $pdf->Output($nombrePdf, 'F');
    chmod($nombrePdf, 777);

    $respuesta["error"]         = NULL;
    $respuesta["accion"]        = "abrir_ubicacion";
    $respuesta["destino"]       = 'media/pdfs/facturas_venta/factura_venta_' . $idItem . '.pdf';
    $respuesta["info"]          = true;
    $respuesta["recargar"]      = true;
    $respuesta["textoInfo"]     = $textos->id('FACTURA_GENERADA_EXITOSAMENTE');

    Servidor::enviarJSON($respuesta);
    
}

/**
 *
 * @global type $configuracion
 * @param type $prueba 
 */
function imprimirFacturaVentaPos($datos) {
    global $configuracion, $textos, $sql, $sesion_usuarioSesion, $sesion_configuracionGlobal;

    $respuesta = array();

    if (!empty($datos['es_modificacion'])) {
        $objeto = new FacturaVenta($datos['es_modificacion']);
        $idItem = $objeto->modificar($datos);
        $idItem = $datos['es_modificacion'];
        
    } else if (!empty($datos['modificacion_cotizacion'])) { //si se trata de generar una factura desde una orden de venta
        $objeto = new FacturaVenta();
        $idItem = $objeto->adicionar($datos);
        
        if (!empty($idItem)) {//si se pudo crear la factura de venta
            $orden = new Cotizacion($datos['modificacion_cotizacion']); //creo el objeto de la orden de venta
            $orden->eliminar(); //y la elimino, pues ya no es necesaria    
            
        }
    } else if (!empty($datos['id_factura_temporal'])) { //si se trata de generar una factura desde una factura que no se finalizo previamente
        $objeto = new FacturaVenta();
        $idItem = $objeto->adicionar($datos);
        
        if (!empty($idItem)) {//si se pudo crear la factura de venta
            FacturaTemporalVenta::eliminarFacturaTemporal($datos['id_factura_temporal']); //eliminar la factura temporal creada    
            
        }
    } else {
        $objeto = new FacturaVenta();
        $idItem = $objeto->adicionar($datos);
        
    }

    //verificar que la sede en la cual se quiere facturar tenga una resolucion de la DIAN activa
    if ($objeto->sinResolucion) {
        $respuesta              = array();
        $respuesta['error']     = true;
        $respuesta['mensaje']   = $objeto->sinResolucion;

        Servidor::enviarJSON($respuesta);
        return NULL;
        
    } else if ($objeto->fueraResolucion) {//verificar que el numero de factura este dentro del numero de factura de la DIAN
        $respuesta              = array();
        $respuesta['error']     = true;
        $respuesta['mensaje']   = $objeto->fueraResolucion;

        Servidor::enviarJSON($respuesta);
        return NULL;
        
    }

    if (!isset($idItem) || (isset($idItem) && !$sql->existeItem('facturas_venta', 'id', $idItem))) {
        $respuesta              = array();
        $respuesta["error"]     = true;
        $respuesta["mensaje"]   = $textos->id("NO_HA_SELECCIONADO_ITEM");

        Servidor::enviarJSON($respuesta);
        return NULL;
    }

    $objeto     = new FacturaVenta($idItem);
    $empresa    = $sesion_configuracionGlobal->empresa;

    $nombreArchivo = substr(md5(uniqid(rand(), true)), 0, 8);

    $fichero = $configuracion["RUTAS"]["media"] . "/" . $configuracion["RUTAS"]["archivos"] . '/facturas_venta_pos/' . $nombreArchivo . '.txt';


    while (file_exists($fichero)) {
        $nombre_archivo = substr(md5(uniqid(rand(), true)), 0, 8);
        $fichero        = $configuracion["RUTAS"]["media"] . "/" . $configuracion["RUTAS"]["archivos"] . '/facturas_venta_pos/' . "/$nombre_archivo.txt";
    }

    $tirilla_encabezado  = $empresa->nombre . "\n";
    $tirilla_encabezado .= $sesion_usuarioSesion->sede->nombre . "\n";
    $tirilla_encabezado .= $textos->id("NIT") . $empresa->nit . "\n";
    $tirilla_encabezado .= $sql->obtenerValor('configuraciones', 'nota_factura', 'id = "1"') . "\n\n";
    $tirilla_encabezado .= $textos->id('FECHA_FACTURA') . ": " . $objeto->fechaFactura . "\n";
    $tirilla_encabezado .= $textos->id('NUMERO_FACTURA') . ": " . $objeto->idFactura . "\n";
//
    $tirilla_cliente     = $textos->id("CLIENTE") . "  : " . $objeto->cliente . "\n";
    $tirilla_cliente    .= $textos->id("ID_CLIENTE") . " : " . $objeto->idCliente . "\n";

    if ($objeto->observaciones != "") {
        $tirilla_comentario = wordwrap($textos->id("OBSERVACIONES") . " : " . $objeto->observaciones, 38, "\n ") . "\n";
        
    } else {
        $tirilla_comentario = "";
        
    }
    
    $tirilla_nota_resolucion = "";
    
    if ($empresa->regimen != "1"){
        $idResolucion   = $sql->obtenerValor('resoluciones', 'id', 'id_sede = "'.$sesion_usuarioSesion->sede->id.'" AND activo = "1"');
        $resolucion     = new Resolucion($idResolucion);

        $notaResolucion = str_replace('%1', $resolucion->numero, $textos->id('NOTA_RESOLUCION_DIAN_EN_FACTURA'));
        $notaResolucion = str_replace('%2', $resolucion->fechaResolucion, $notaResolucion);
        $notaResolucion = str_replace('%3', $resolucion->numeroFacturaInicial, $notaResolucion);
        $notaResolucion = str_replace('%4', $resolucion->numeroFacturaFinal, $notaResolucion);      

        $tirilla_nota_resolucion = wordwrap($notaResolucion, 38, "\n ") . "\n";;        
    }
    
    $tirilla_usuario = " " . $textos->id('USUARIO_QUE_FACTURA') . "  : " . $objeto->usuario . "   \n\n";

    $idPrincipalArticulo = (string)$sesion_configuracionGlobal->idPrincipalArticulo;
    
    if ($idPrincipalArticulo == "id") {
        $idPrincipalArticulo = "idArticulo";
    }
    
    $arrayIdArticulo     = array('idArticulo' => $textos->id('ID_AUTOMATICO'), 'plu_interno' => $textos->id('PLU'));
    
    $tituArtiFact1 .= $textos->id($arrayIdArticulo[$idPrincipalArticulo])  . ' | ';
    $tituArtiFact1 .= $textos->id("ARTICULO") . ' | ';
    $tituArtiFact1 .= $textos->id("CANTIDAD") . ' | ';
    $tituArtiFact1 .= $textos->id("DESCUENTO") . ' | ';
    $tituArtiFact1 .= $textos->id("IVA") . ' | ';
    $tituArtiFact1 .= $textos->id("PRECIO_UNITARIO") . ' | ';
    $tituArtiFact1 .= $textos->id("SUBTOTAL");

    $tituArtiFact = wordwrap($tituArtiFact1, 40, "\n");

    $subtotalFactura    = 0;
    $descArtiFact       = '';

    //ciclo que va recorriendo el listado de articulos de una factura determinada y los imprime

    $contador = 0;
    $dctoTotalSobreArticulos = 0;

    foreach ($objeto->listaArticulos as $obj) {
        $contador++;
        if (strlen($obj->articulo) > 30) {
            $obj->articulo = substr($obj->articulo, 0, 29) . '.';
            
        }
        if ($obj->descuento == "0") {
            $obj->subtotal = $obj->cantidad * $obj->precio;
            
        } else {
            $descuentoArticulo = ((($obj->cantidad * $obj->precio) * $obj->descuento) / 100 );
            $obj->subtotal = ($obj->cantidad * $obj->precio) - $descuentoArticulo;
            $dctoTotalSobreArticulos += $descuentoArticulo;
            
        }
        
        $obj->descuento     = Recursos::formatearNumero($obj->descuento, '%', '0');
        $obj->precio        = '$'.Recursos::formatearNumero($obj->precio, '$');
        
        $subtotalFactura += $obj->subtotal;
        
        $iva = '$'.Recursos::formatearNumero($obj->subtotal * ( $obj->iva / 100) );

        $obj->subtotal = '$'.Recursos::formatearNumero($obj->subtotal, '$');

        $descArtiFact1 = '';

        $descArtiFact1 .= $contador . ') ' . Recursos::completarCeros((int) $obj->$idPrincipalArticulo, 5) . ' | ';
        $descArtiFact1 .= $obj->articulo . ' | ';
        $descArtiFact1 .= $obj->cantidad . ' | ';
        $descArtiFact1 .= $obj->descuento . ' | ';
        $descArtiFact1 .= $iva . ' | ';
        $descArtiFact1 .= $obj->precio . ' | ';
        $descArtiFact1 .= $obj->subtotal;

        $descArtiFact2 = wordwrap($descArtiFact1, 40, "\n") . "\n";

        $descArtiFact .= $descArtiFact2;
    }

    $subtotalFactura += $objeto->valorFlete;
    
    
    //$impuestoIva = ($subtotalFactura * $objeto->iva) / 100;

    //$subtotalFactura += $impuestoIva;

    $pieTirilla     = '';
    
    $pieTirilla    .= $textos->id("VALOR_FLETE") . ": " . '$'.Recursos::formatearNumero($objeto->valorFlete, '$') . "\n";
    
    $pieTirilla    .= $textos->id("IVA_FLETE") . ": " . '$'.Recursos::formatearNumero( ($objeto->valorFlete * ($sesion_configuracionGlobal->ivaGeneral / 100) ), '$') . "\n";
    
    if ($objeto->iva){
        $pieTirilla    .= $textos->id("IVA") . ": " . '$'.Recursos::formatearNumero($objeto->iva, '$') . "\n";
    }
    
    $pieTirilla    .= $textos->id("DCTO_TOTAL_ARTICULOS") . ': ' . '$'.Recursos::formatearNumero($dctoTotalSobreArticulos, '$') . "\n";
    
    $pieTirilla    .= $textos->id("SUBTOTAL") . ': ' . '$'.Recursos::formatearNumero($subtotalFactura, '$') . "\n";

    $totalFactura   = $subtotalFactura;

    if (!empty($objeto->concepto1) && !empty($objeto->descuento1)) {

        $pieTirilla .= $textos->id("DESCUENTOS") . ": \n";

        $pesosDcto1     = ($totalFactura * $objeto->descuento1) / 100;
        $totalFactura   = $totalFactura - $pesosDcto1;
        $pieTirilla    .= $objeto->concepto1 . ': ' . $objeto->descuento1 . '%   =>  ' . '$'.Recursos::formatearNumero($pesosDcto1, '$') . "\n";
        
    }

    if (!empty($objeto->concepto2) && !empty($objeto->descuento2)) {
        $pesosDcto2     = ($totalFactura * $objeto->descuento2) / 100;
        $totalFactura   = $totalFactura - $pesosDcto2;
        $pieTirilla    .= $objeto->concepto2 . ': ' . $objeto->descuento2 . '%   =>  ' . '$'.Recursos::formatearNumero($pesosDcto2, '$') . "\n";
        
    }

    $totalFactura += $objeto->iva;   

    $pieTirilla .= "\n" . $textos->id("TOTAL") . ': ' . '$'.Recursos::formatearNumero($totalFactura, '$') . "\n";
    
    
    /**
     * Nota para los descuentos extras
     */
    
    $dctoExtrasTirilla = '';
    
    if (!empty($objeto->fechaLimiteDcto1) && !empty($objeto->porcentajeDcto1)) {
        $pesosDctoExtra1 = ($totalFactura * $objeto->porcentajeDcto1) / 100;
        $dctoExtrasTirilla .= '*'.$textos->id('FECHA_LIMITE_PAGO_PARA_DESCUENTO') . ': ' . $objeto->fechaLimiteDcto1 . ' ' . $textos->id('PORCENTAJE_DESCUENTO') . ': ' . $objeto->porcentajeDcto1 . '% --> ' . '$'.Recursos::formatearNumero($pesosDctoExtra1, '$') . ' --> ' . $textos->id('PAGANDO_EN_TOTAL') . ' ' . '$'.Recursos::formatearNumero(($totalFactura - $pesosDctoExtra1), '$');
    }

    if (!empty($objeto->fechaLimiteDcto2) && !empty($objeto->porcentajeDcto2)) {
        $pesosDctoExtra2 = ($totalFactura * $objeto->porcentajeDcto2) / 100;
        $dctoExtrasTirilla .=  '*'.$textos->id('FECHA_LIMITE_PAGO_PARA_DESCUENTO') . ': ' . $objeto->fechaLimiteDcto2 . ' ' . $textos->id('PORCENTAJE_DESCUENTO') . ': ' . $objeto->porcentajeDcto2 . '% --> ' . '$'.Recursos::formatearNumero($pesosDctoExtra2, '$') . ' --> ' . $textos->id('PAGANDO_EN_TOTAL') . ' ' . '$'.Recursos::formatearNumero(($totalFactura - $pesosDctoExtra2), '$');
    }     


    $imprimir = "" .
            "\n\n" . wordwrap($tirilla_encabezado, 40) .
            $tirilla_cliente .
            $tirilla_comentario . "\n" .
            $tirilla_nota_resolucion . "\n" .
            $tituArtiFact . "\n\n" .
            $descArtiFact . "\n" .
            wordwrap($tirilla_usuario, 40) .
            "-----------------------------------  \n" .
            wordwrap($pieTirilla, 40) .
            "-----------------------------------  \n" .
            wordwrap($dctoExtrasTirilla, 40) .            
            "\n\n\n\n\n\n\n\n";

    file_put_contents($fichero, $imprimir);

    chmod($fichero, 0777);

    $respuesta["error"]         = NULL;
    $respuesta["accion"]        = "abrir_ubicacion";
    $respuesta["destino"]       = $fichero;
    $respuesta["info"]          = true;
    $respuesta["recargar"]      = true;
    $respuesta["textoInfo"]     = $textos->id('FACTURA_GENERADA_EXITOSAMENTE');
    
    //unlink($fichero);

    Servidor::enviarJSON($respuesta);

    // exec("/usr/bin/lp -d $forma_id_impresora $ruta");
    //unlink($ruta);    
    
}

/**
 *
 * @global type $textos
 * @global type $sql
 * @global type $archivo_imagen
 * @param type $datos 
 */
function buscarCotizacion($datos) {
    global $textos, $configuracion, $sql;

    $respuesta  = array();
    $codigo     = '';
    
    if (empty($datos)) {
        $codigo      = HTML::campoOculto('procesar', 'true');
        $codigo     .= HTML::campoOculto('datos[dialogo]', '', 'idDialogo');

        $codigo2     = HTML::parrafo($textos->id('INGRESE_NUM_COTIZACION'), 'negrilla margenSuperior');
        $codigo2    .= HTML::campoTexto('datos[num_cotizacion]', 40, 255, '', 'autocompletable campoObligatorio', 'campoIdCotizacion', array('title' => '/ajax/ventas_mercancia/listarCotizaciones'), $textos->id('AYUDA_USO_AUTOCOMPLETAR'), '', 'datos[id_cotizacion]');
        $codigo2    .= HTML::campoOculto('idCotizacion', '', 'ocultoIdCotizacion');
        $destino2    = $configuracion['SERVIDOR']['principal'] . 'ventas_mercancia';
        $codigo2     = HTML::forma($destino2, $codigo2, 'P', false);

        $codigo     .= HTML::contenedor($codigo2, '', 'contenedorBusquedaRapida');
        $codigo     .= HTML::parrafo($textos->id('BUSQUEDA_AVANZADA'), 'negrilla margenSuperior margenInferior estiloEnlace', 'textoBusquedaAvanzada');

        $codigo1     = HTML::parrafo($textos->id('CLIENTE'), 'negrilla margenSuperior');
        $codigo1    .= HTML::campoTexto('datos[cliente]', 40, 255, '', 'autocompletable campoObligatorio', 'campoIdCliente', array('title' => '/ajax/clientes/listar'), $textos->id('AYUDA_USO_AUTOCOMPLETAR'), '', 'datos[id_cliente]', '');
        $codigo1    .= HTML::parrafo($textos->id('FECHA_INICIAL'), 'margenSuperior negrilla');
        $codigo1    .= HTML::campoTexto("datos[fecha_inicial]", 12, 12, '', "fechaAntigua", "", array("alt" => $textos->id("SELECCIONE_FECHA_INICIAL")));
        $codigo1    .= HTML::parrafo($textos->id('FECHA_FINAL'), 'margenSuperior negrilla');
        $codigo1    .= HTML::campoTexto("datos[fecha_final]", 12, 12, '', "fechaAntigua", "", array("alt" => $textos->id("SELECCIONE_FECHA_FINAL")));
        $codigo1    .= HTML::parrafo(HTML::boton('chequeo', $textos->id('ACEPTAR'), '', '', 'btnCargarOrden', '', array('validar' => 'NoValidar')), 'margenSuperior');
        $destino1    = '/ajax/ventas_mercancia/buscarCotizacion';
        $codigo1     = HTML::forma($destino1, $codigo1, 'P', true);
        $codigo     .= HTML::contenedor($codigo1, 'oculto', 'contenedorBusquedaAvanzada');

        $respuesta['generar']   = true;
        $respuesta['cargarJs']  = true;
        $respuesta['archivoJs'] = $configuracion['SERVIDOR']['media'] . $configuracion['RUTAS']['javascript'] . '/modulos/ventas_mercancia/funcionesBuscarCotizaciones.js';
        $respuesta['codigo']    = $codigo;
        $respuesta['titulo']    = HTML::parrafo($textos->id('BUSCAR_COTIZACION'), 'letraBlanca negrilla subtitulo');
        $respuesta['destino']   = '#cuadroDialogo';
        $respuesta['ancho']     = 450;
        $respuesta['alto']      = 350;
        
    } else {
        
        if (empty($datos['id_cliente']) || (!empty($datos['id_cliente']) && !$sql->existeItem('clientes', 'id', $datos['id_cliente'])) || empty($datos['fecha_inicial']) || empty($datos['fecha_final'])) {
            $respuesta              = array();
            $respuesta['error']     = true;
            $respuesta['mensaje']   = $textos->id('ERROR_DEBE_LLENAR_TODOS_LOS_CAMPOS_DEL_FORMULARIO');

            Servidor::enviarJSON($respuesta);
            return NULL;
        }          

        $excluidas  = array(0);
        $objeto     = new Cotizacion();
        
        $objeto->listaAscendente = true;

        $condicion = ' oc.id_cliente = "' . $datos["id_cliente"] . '" AND fecha_cotizacion BETWEEN "' . $datos["fecha_inicial"] . '" AND "' . $datos["fecha_final"] . '"';

        $listaCotizaciones = $objeto->listar(0, 100, $excluidas, $condicion, $orden);

        if (!empty($objeto->registrosConsulta)) {
            //crear los formularios con la info para las demas sedes
            $datosTablaFacturasCompra = array(
                HTML::parrafo($textos->id('NUMERO_COTIZACION'), 'centrado') => 'id', //concateno el nombre del alias para usarlo al armar la tabla con el fila en objeto
                HTML::parrafo($textos->id('SEDE'), 'centrado')              => 'sede', //y concateno el alias de la tabla junto con el campo para usarlo al realizar
                HTML::parrafo($textos->id('CLIENTE'), 'centrado')           => 'cliente', //la busqueda, al armar la tabla dividira la cadena y usara el que necesite
                HTML::parrafo($textos->id('FECHA_COTIZACION'), 'centrado')  => 'fechaCotizacion'
            );

            $rutas              = array();
            $idTabla            = 'tablaListarCotizaciones';
            $claseTabla         = 'tablaListarItems';
            $estilosColumnas    = array('', '', '', '', '');
            $contenedor         = HTML::contenedor(Recursos::generarTablaRegistrosInterna($listaCotizaciones, $datosTablaFacturasCompra, $rutas, $idTabla, $estilosColumnas, $claseTabla), 'flotanteIzquierda margenDerecha');
        
        } else {
            $contenedor = HTML::contenedor(HTML::parrafo($textos->id('BUSQUEDA_SIN_RESULTADOS'), 'textoError'));
            
        }

        $codigo  = HTML::parrafo($textos->id('AYUDA_SELECCIONAR_ITEMS_TABLA_FACTURAS'), 'letraVerde negrilla subtitulo');
        $codigo .= $contenedor;

        $respuesta['generar']       = true;
        $respuesta['cargarJs']      = true;
        $respuesta['archivoJs']     = $configuracion['SERVIDOR']['media'] . $configuracion['RUTAS']['javascript'] . '/modulos/ventas_mercancia/funcionesBuscarCotizacion2.js';
        $respuesta['codigo']        = $codigo;
        $respuesta['titulo']        = HTML::parrafo($textos->id('BUSCAR_COTIZACIONES'), 'letraBlanca negrilla subtitulo');
        $respuesta['destino']       = '#cuadroDialogo';
        $respuesta['ancho']         = 800;
        $respuesta['alto']          = 540;
        
    }

    Servidor::enviarJSON($respuesta);
    
}

/**
 *
 * funcion que se encarga de ir guardando la factura mientras un empleado la va
 * realizando, esto teniendo en cuenta que se cierre la sesion del usuario por
 * cualquier motivo, pueda recuperar lo avanzado mientras realizaba la factura
 * @global type $textos
 * @global type $sql
 * @global type $archivo_imagen
 * @param type $datos 
 */
function guardarFacturaTemporal($datos) {
    $objeto = new FacturaTemporalVenta();
    $idItem = $objeto->adicionarFacturaTemporal($datos);

    $respuesta = array();
    $respuesta['codigo'] = $idItem;

    Servidor::enviarJSON($respuesta);
    
}

function modificarFacturaTemporal($datos) {
    $objeto = new FacturaTemporalVenta();
    $idItem = $objeto->modificarFacturaTemporal($datos);

    $respuesta = array();
    $respuesta['codigo'] = $idItem;

    Servidor::enviarJSON($respuesta);
    
}

/**
 *
 * @global type $sql
 * @param type $idSede 
 */
function escogerBodega($idSede) {
    global $sql;

    $listaBodegas = array();
    $respuesta = array();
    $consulta = $sql->seleccionar(array('bodegas'), array('id', 'nombre'), 'id_sede = "' . $idSede . '"', '', 'nombre ASC');
    if ($sql->filasDevueltas) {
        while ($dato = $sql->filaEnObjeto($consulta)) {
            $listaBodegas[$dato->id] = $dato->nombre;
        }
        
    }
    $selectores = '';
    $selectores .= '<option value= "">Seleccionar bodega...</option>';
    foreach ($listaBodegas as $id => $valor) {
        $selectores .= '<option value= "' . $id . '">' . $valor . '</option>';
    }

    $respuesta['error']                 = false;
    $respuesta['accion']                = 'insertar';
    $respuesta['contenido']             = $selectores;
    $respuesta['insertarNuevaFila']     = true;
    $respuesta['idDestino']             = '#selectorBodegas';

    Servidor::enviarJSON($respuesta);
}

/**
 * Funcion que devuelve la respuesta para el autocompletar
 * @global type $sql
 * @param type $cadena 
 */
function listarCotizaciones($cadena) {
    global $sql;
    $respuesta = array();

    $tablas     = array('oc' => 'cotizaciones', 'c' => 'clientes');

    $columnas   = array('id' => 'oc.id', 'idCliente' => 'oc.id_cliente', 'cliente' => 'c.nombre');

    $condicion  = 'oc.id_cliente = c.id AND oc.id LIKE "%' . $cadena . '%"';

    $consulta   = $sql->seleccionar($tablas, $columnas, $condicion, '', 'c.nombre ASC', 0, 20);

    while ($fila = $sql->filaEnObjeto($consulta)) {
        $respuesta1 = array();
        $respuesta1["label"]        = $fila->id . ' :: ' . $fila->cliente;
        $respuesta1["value"]        = $fila->id;
        $respuesta1["nombre"]       = $fila->proveedor;
        $respuesta[]                = $respuesta1;
        
    }

    Servidor::enviarJSON($respuesta);
}

/**
 *
 * Funcion que se encarga de validar el descuento maximo autorizado por un usuario
 * 
 * @global type $sql
 * @param type $datos 
 */
function validarPermisoDcto($datos) {
    global $sql;
    
    $respuesta = array();

    $maxPermiso = $sql->obtenerValor('usuarios', 'dcto_maximo', 'usuario = "' . $datos["usuario"] . '" AND contrasena = "' . $datos["contrasena"] . '"');

    if ($maxPermiso > $datos['dcto_maximo']) {
        $respuesta['autorizar'] = true;
        $respuesta['fila']      = "#" . $datos['fila'];
        $respuesta['dcto']      = $datos['dcto_maximo'];
        
    } else {
        $respuesta['autorizar'] = false;
        $respuesta['fila']      = "#" . $datos['fila'];
        $respuesta['dcto']      = $datos['dcto_maximo'];
        
    }


    Servidor::enviarJSON($respuesta);
}
