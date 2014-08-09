<?php

/**
 *
 * @package     FOM
 * @subpackage  Compras Mercancia
 * @author      Pablo Andrés Vélez Vidal <pavelez8@misena.edu.co>
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2012 GENESYS Corporation.
 * @version     0.2
 *
 * */
global $sesion_usuarioSesion, $modulo, $configuracion, $textos, $forma_idFactTemp, $forma_idFactura, $forma_crearOrden, $forma_idOrden, $sql, $sesion_configuracionGlobal;

$idFactura      = $forma_idFactura; //si lo que se va es a modificar una factura existente
$idOrden        = $forma_idOrden; //si lo que se va es a crear una factura desde una orden de compra
$idFactTemp     = $forma_idFactTemp; //si lo que se va es a crear una factura desde una factura temporal
$crearOrden     = $forma_crearOrden; //si se va a crear una orden de compra desde el modulo de ordenes de compra

//verificar que despues que se realiza una factura desde una orden de compra, recarge el formulario en limpio
//pues al recargar a veces vuelve y envia por post el "id de la orden", pero esta orden ya no existe porque fue facturada
if(!empty($idOrden)){
    $existeOrden = $sql->existeItem('ordenes_compra', 'id', $idOrden);
    if (!$existeOrden) {
        $idOrden = '';
        
    }
    
}

//verificar que despues que se realiza una factura desde una factura temporal, recarge el formulario en limpio
$existeFactTemp = $sql->existeItem('facturas_temporales_compra', 'id', $idFactTemp);
if (!$existeFactTemp) {
    $idFactTemp = '';
}

if (!empty($idFactura)) {
    $objeto         = new FacturaCompra($idFactura); /* creacion del objeto */
    $tituloBloque   = $textos->id('MODULO_ACTUAL') . ' :: ' . $textos->id('EDITAR_FACTURA_PROVEEDOR');

} elseif (!empty($idOrden)) {
    $objeto         = new OrdenCompra($idOrden); /* creacion del objeto */
    $tituloBloque   = $textos->id('MODULO_ACTUAL') . ' :: ' . str_replace('%1', (int)$idOrden, $textos->id('GENERAR_FACTURA_DESDE_ORDEN'));

} elseif ($crearOrden) {
    $objeto         = new OrdenCompra(); /* creacion del objeto */
    $tituloBloque   = $textos->id('MODULO_ACTUAL') . ' :: ' . $textos->id('CREAR_ORDEN_COMPRA');

} elseif (!empty($idFactTemp)) {
    $objeto     = new FacturaTemporalCompra(); /* creacion del objeto */
    
    $objeto->cargarFacturaTemporal($idFactTemp);
    
    $tituloBloque = $textos->id('MODULO_ACTUAL') . ' :: ' . str_replace('%1', $objeto->fechaFactura, $textos->id('GENERAR_FACTURA_DESDE_FACTURA_TEMPORAL'));
    
} else {
    $objeto         = new FacturaCompra(); /* creacion del objeto */
    $tituloBloque   = $textos->id('MODULO_ACTUAL') . ' :: ' . $textos->id('CREAR_FACTURA_PROVEEDOR');
    
}


//$tituloBloque .= HTML::imagen($configuracion['SERVIDOR']['media'] . $configuracion['RUTAS']['imagenesEstilos'] . '/' . strtolower($modulo->url) . '.png', 'iconoModulo');

$contenido  = '';
$contenido .= HTML::contenedor(HTML::contenedor($textos->id('AYUDA_MODULO'), 'ui-corner-all'), 'ui-widget-shadow ui-corner-all oculto', 'contenedorAyudaUsuario');

$destino = '/ajax/compras_mercancia/add';

//Verificar si viene de una recuperacion de factura, o modificacion y poner el porcentaje retecree adecuado en e campo
$porcentajeRetecree = '';

if ($objeto->idProveedor) {
    $idActividad = $sql->obtenerValor("proveedores", 'id_actividad_economica', 'id = "'.$objeto->idProveedor.'"');
    
    $porcentajeRetecree = $sql->obtenerValor("actividades_economicas", 'porcentaje_retecree', 'id = "'.$idActividad.'"');
    
}

//obtener el regimen de la empresa
$regimenEmpresa = $sesion_configuracionGlobal->empresa->regimen;

//capturar el regimen del proveedor
$regimenProveedor = '';

if ($objeto->idProveedor) {
    $regimenProveedor = $sql->obtenerValor("proveedores", 'regimen', 'id = "'.$objeto->idProveedor.'"');

}

$cantidadDecimales = $sesion_configuracionGlobal->cantidadDecimales;

//campo oculto del cual el javascript sacara el nombre del modulo actual ->para??
$contenido .= HTML::contenedor('', 'contenedorNotificaciones', 'contenedorNotificaciones', array("style" => "top:0px !important;"));
$contenido .= HTML::campoOculto('nombreModulo', ucwords(strtolower($modulo->nombre)), 'nombreModulo');
$contenido .= HTML::campoOculto('cantidadDecimales', $cantidadDecimales, 'cantidadDecimales');
$contenido .= HTML::campoOculto('tipoMonedaColombiana', $sesion_configuracionGlobal->tipoMoneda, 'tipoMonedaColombiana');
$contenido .= HTML::campoOculto('porcentajeRetecree', $porcentajeRetecree, 'porcentajeRetecree');
$contenido .= HTML::campoOculto('regimenProveedor', $regimenProveedor, 'regimenProveedor');
//campo que determina el % de ganancia predeterminado a un articulo segun su precio de compra
$contenido .= HTML::campoOculto('porcPredGanancia', $sesion_configuracionGlobal->porcPredGanancia, 'porcPredGanancia');
$contenido .= HTML::campoOculto('valorIva', $sesion_configuracionGlobal->ivaGeneral, 'valorIva');


/**
 * Verifico que se haya iniciado una sesion y que tenga permisos para ver el modulo
 * */
if ((isset($sesion_usuarioSesion) && Perfil::verificarPermisosModulo($modulo->id)) || isset($sesion_usuarioSesion) && $sesion_usuarioSesion->idTipo == 0) {
 
    //arreglo que contiene todo el tutorial para este formulario de compras 
    $tutorial = array(
            '1'  => array('data-step' => '1',  'data-intro' => $textos->id("AYUDA_SELECCIONE_EL_PROVEEDOR")),
            '3'  => array('data-step' => '3',  'data-intro' => $textos->id("AYUDA_INGRESA_NUM_FACTURA_PROVEEDOR")),
            '4'  => array('data-step' => '4',  'data-intro' => $textos->id("AYUDA_INGRESA_FECHA_COMPRA")),
            '7'  => array('data-step' => '7',  'data-intro' => $textos->id("AYUDA_BUSCAR_FACTURAS_ANTERIORES")),
            '8'  => array('data-step' => '8',  'data-intro' => $textos->id("AYUDA_BUSCAR_CATALOGO_MOTO")),
            '9'  => array('data-step' => '9',  'data-intro' => $textos->id("AYUDA_BUSCAR_Y_CARGAR_ORDEN_DE_COMPRA")),
            '10' => array('data-step' => '10', 'data-intro' => $textos->id("AYUDA_INGRESAR_ARTICULO_A_LA_FACTURA")),
            '12' => array('data-step' => '12', 'data-intro' => $textos->id("AYUDA_CARGAR_MULTIPLES_ARTICULOS")),
            '13' => array('data-step' => '13', 'data-intro' => $textos->id("AYUDA_DESCUENTO_GENERAL")),
            '14' => array('data-step' => '14', 'data-intro' => $textos->id("AYUDA_GANANCIA_GENERAL")),
            '15' => array('data-step' => '15', 'data-intro' => $textos->id("AYUDA_LISTADO_ARTICULOS")),
            '16' => array('data-step' => '16', 'data-intro' => $textos->id("AYUDA_BORRAR_LISTADO_ARTICULOS")),
            '17' => array('data-step' => '17', 'data-intro' => $textos->id("AYUDA_VALOR_FLETE")),
            '18' => array('data-step' => '18', 'data-intro' => $textos->id("AYUDA_CONCEPTO_DESCUENTO")),
            '19' => array('data-step' => '19', 'data-intro' => $textos->id("AYUDA_DESCUENTO")),
            '20' => array('data-step' => '20', 'data-intro' => $textos->id("AYUDA_MAS_DESCUENTO")),
            '21' => array('data-step' => '21', 'data-intro' => $textos->id("AYUDA_GUIA_SUBTOTAL")),
            '22' => array('data-step' => '22', 'data-intro' => $textos->id("AYUDA_OBSERVACIONES")),
            '23' => array('data-step' => '23', 'data-intro' => $textos->id("AYUDA_IVA")),
            '24' => array('data-step' => '24', 'data-intro' => $textos->id("AYUDA_TOTAL")),
            '25' => array('data-step' => '25', 'data-intro' => $textos->id("AYUDA_FINALIZAR_FACTURA")),
 
    );
    
    //Selecciono las cajas existentes en el sistema
    $listaProveedores = array();//arreglo que almacenará el listado de cajas y será pasado como parametro al metodo HTML::listaDesplegable

    $idDelProveedor = '1'; //determinar si no viene ningun dato para el cliente que sea el base
    
    if (!empty($objeto->idProveedor)) {
        $idDelProveedor = $objeto->idProveedor;
    }    
    
    $regimenProveedor = $sql->obtenerValor("proveedores", "regimen", "id = '".$idDelProveedor."'");
    
    $consulta = $sql->seleccionar(array('proveedores'), array('id', 'nombre', 'id_proveedor'), 'id != "0"', '', 'nombre ASC');
    
    if ($sql->filasDevueltas) {
        while ($dato = $sql->filaEnObjeto($consulta)) {//recorro las respuesta, y voy guardandolas en el arreglo
            $listaProveedores[$dato->id] = (int)$dato->id_proveedor.' - '.$dato->nombre;
        }

    }

    //elaboro la lista deplegable con la info de las cajas
    $selectorProveedores = HTML::listaDesplegable('datos[id_proveedor]', $listaProveedores, $idDelProveedor, 'selectChosen', 'selectorProveedores', '', array(), $textos->id('AYUDA_SELECCIONAR_PROVEEDOR'), '/ajax/proveedores/add');   
    
    $linea1 = HTML::frase($textos->id('PROVEEDOR'), '');
    $linea1 .= $selectorProveedores; 
    $linea1 .= HTML::frase($textos->id('NUMERO_FACTURA_PROVEEDOR'), 'margenIzquierda');
    $linea1 .= HTML::campoTexto('datos[num_factura_proveedor]', 15, 30, $objeto->numeroFacturaProveedor, '', 'campoNumeroFacturaProveedor', array()+$tutorial["3"]);

    $linea1 .= HTML::frase($textos->id('FECHA'), 'margenIzquierda');
    
    $fechaFact      = explode(' ', $objeto->fechaFactura);
    $fechaFactura   = $fechaFact[0];
    
    if (empty($fechaFactura)) {
        $fechaFactura = date('Y-m-d');
    }

    $linea1 .= HTML::campoTexto('datos[fecha_factura]', 9, 12, $fechaFactura, 'campoObligatorio fechaAntigua campoCalendario', '', array()+$tutorial["4"], $textos->id('AYUDA_FECHA_FACTURA'));
    $linea1 .= HTML::frase( isset($fechaFact[1]) ? $fechaFact[1] : "", 'margenIzquierda negrilla');

    $linea1 .= HTML::campoOculto('datos[es_orden_compra]', '', 'esOrdenDeCompra'); //Segun se ingrese o se quite el numero de factura de proveedor, este campo va a determinar si solo puede ser una orden de compra
    $linea1 .= HTML::campoOculto('datos[es_modificacion]', $forma_idFactura, 'esModificacion'); //el valor de este campo determina si se trata de la edicion de una factura
    $linea1 .= HTML::campoOculto('datos[modificacion_orden]', $forma_idOrden, 'modificacionOrden'); //el valor de este campo determina si es generar una factura desde una orden de compra previa

    $linea1 .= HTML::campoOculto('datos[id_factura_temporal]', $idFactTemp, 'idFacturaTemporal'); //Es utilizado por javascript (compras_mercancia.js metodo guardarFacturaTemporal() ) para verificar si se debe crear la factura temporal o se debe modificar

    $linea1 .= HTML::campoOculto('datos[es_factura_temporal]', $idFactTemp, 'idEsFacturaTemporal'); //Identificador de la factura temporal, para irla guardando o eliminarla al crear la factura, o la orden de compra
    
    
    $linea1 .= HTML::campoOculto('nombreModulo', ucwords(strtolower($modulo->nombre)), 'nombreModulo');
    
    //imagen de ayuda para el tutorial intro js
    $linea1 .= HTML::contenedor(HTML::contenedor('', 'contenedorImagenTeachme', '', array('ayuda' => 'Enseñame a realizar una <br>compra de mercancia')), 'ayudaFormularioCompra');
    
    
    //comienzo a armar el código del formulario
    $codigo  = '';
    
    $codigo .= HTML::parrafo($linea1, 'linea1');

//    $listaSedes = array();
//    $consulta = $sql->seleccionar(array('sedes_empresa'), array('id', 'nombre'), '', '', 'nombre ASC');
//    if ($sql->filasDevueltas) {
//        while ($dato = $sql->filaEnObjeto($consulta)) {
//            $listaSedes[$dato->id] = $dato->nombre;
//        }
//    }

    //Selecciono las bodegas existentes en el sistema
    $listaBodegas   = array();//arreglo que almacenará el listado de bodegas y será pasado como parametro al metodo HTML::listaDesplegable
    
    $consulta       = $sql->seleccionar(array('bodegas'), array('id', 'nombre'), 'id_sede = "' . $sesion_usuarioSesion->sede->id . '"', '', 'nombre ASC');//consulto las bodegas de la sede actual del usuario
    
    if ($sql->filasDevueltas) {
        while ($dato = $sql->filaEnObjeto($consulta)) {//recorro las respuesta, y voy guardandolas en el arreglo
            $listaBodegas[$dato->id] = (int)$dato->id.' :: '.$dato->nombre;
        }
        
    }

    //Selecciono las cajas existentes en el sistema
    $listaCajas = array();//arreglo que almacenará el listado de cajas y será pasado como parametro al metodo HTML::listaDesplegable
    
    $consulta = $sql->seleccionar(array('cajas'), array('id', 'nombre'), 'id_sede = "' . $sesion_usuarioSesion->sede->id . '"', '', 'nombre ASC');
    
    if ($sql->filasDevueltas) {
        while ($dato = $sql->filaEnObjeto($consulta)) {//recorro las respuesta, y voy guardandolas en el arreglo
            $listaCajas[$dato->id] = $dato->nombre;
        }
        
    }

    //elaboro la lista deplegable con la info de las cajas
    $selectorCajas = HTML::listaDesplegable('datos[id_caja]', $listaCajas, '', 'selectChosen', 'selectorCaja', '', array(), '');

    //obtengo la bodega principal de la sede actual del usuario (en cada sede hay una bodega marcada como principal) para usarla como valor predeterminado en la lista de bodegas
    $idBodegaPrincipal = $sql->obtenerValor('bodegas', 'id', 'id_sede = "' . $sesion_usuarioSesion->sede->id . '" ANd principal = "1"');

//    $selectorSedes = HTML::listaDesplegable('datos[sede]', $listaSedes, $sesion_usuarioSesion->sede->id, $clase, 'selectorSedes', '', array(), '');
    //armo la lista desplegable de las bodegas
    $selectorBodega = HTML::listaDesplegable('datos[bodega]', $listaBodegas, $idBodegaPrincipal, 'selectChosen', 'selectorBodegas', '', array(), '');

//    $linea2 .= HTML::frase($textos->id('SEDE').': ', 'margenIzquierdaDoble');
//    $linea2 .= HTML::frase($sesion_usuarioSesion->sede->nombre, 'subtitulo negrilla');
    $linea2  = HTML::frase($textos->id('BODEGA'), '');
    $linea2 .= $selectorBodega;
    $linea2 .= HTML::campoOculto('datos[id_bodega]', (int)$idBodegaPrincipal, 'idBodegaGeneral');//campo oculto en el DOM que almacena el idBodegaPrincipal. Este es usado por javascript para ciertas funciones
    $linea2 .= HTML::frase($textos->id('CAJA') . ': ', 'margenIzquierda');
    $linea2 .= $selectorCajas;
    
    $rutaImagen = $configuracion['RUTAS']['media'] . $configuracion['RUTAS']['imagenesEstilos'] . 'buscar_factura2.png';
//    $linea1 .= HTML::frase($textos->id('BUSCAR_FACTURA'), 'margenIzquierdaDoble');
    $linea2 .= HTML::imagen($rutaImagen, 'estiloEnlace margenIzquierda', 'imagenBuscarFactura', array('ayuda' => $textos->id('BUSCAR_FACTURAS'))+$tutorial["7"]);

    $rutaImagen = $configuracion['RUTAS']['media'] . $configuracion['RUTAS']['imagenesEstilos'] . 'buscar_catalogo.png';
//    $linea1 .= HTML::frase($textos->id('CATALOGOS'), 'margenIzquierdaDoble');
    $linea2 .= HTML::imagen($rutaImagen, 'estiloEnlace margenIzquierda', 'imagenBuscarCatalogo', array('ayuda' => $textos->id('BUSCAR_CATALOGOS'))+$tutorial["8"]);

    $rutaImagen = $configuracion['RUTAS']['media'] . $configuracion['RUTAS']['imagenesEstilos'] . 'orden-cotizacion.png';
//    $linea1 .= HTML::frase($textos->id('CARGAR_ORDEN_COMPRA'), 'margenIzquierdaDoble');
    $linea2 .= HTML::imagen($rutaImagen, 'estiloEnlace margenIzquierda', 'imagenCargarOrden', array('ayuda' => $textos->id('AYUDA_CARGAR_ORDEN_COMPRA'))+$tutorial["9"]);
    

    $codigo .= HTML::parrafo($linea2, 'linea2');

    $linea3 = HTML::frase($textos->id('ARTICULO'), 'margenSuperior');//titulo y abajo el campo principal del cual se seleccionan los articulos para la factura
    $linea3 .= HTML::campoTexto('', 37, 255, '', 'autocompletable campoObligatorio margenSuperior', 'articuloFactura', array('title' => '/ajax/articulos/listarArticulosCompra?extra=' . $idBodegaPrincipal)+$tutorial["10"], $textos->id('AYUDA_USO_AUTOCOMPLETAR'), '/ajax/articulos/add');
//    $linea3 .= HTML::campoOculto('', '', 'identificadorArticulo');
//    $linea3 .= HTML::campoOculto('', '', 'precioArticulo');
    $rutaImagen = $configuracion['SERVIDOR']['media'] . $configuracion['RUTAS']['imagenesEstilos'] . 'listaAgregarArticulos.png';
    $linea3 .= HTML::frase($textos->id('AGREGAR_VARIOS_ARTICULOS'), 'margenIzquierda margenIzquierdaDoble');
    $linea3 .= HTML::imagen($rutaImagen, 'estiloEnlace', 'imagenAgregarVariosArticulos', array('ayuda' => $textos->id('AYUDA_AGREGAR_VARIOS_ARTICULOS'))+$tutorial["12"]);
    $linea3 .= HTML::frase($textos->id('DESCUENTO_GENERAL'), 'margenIzquierdaDoble');
    $linea3 .= HTML::campoTexto('datos[dcto_general]', 4, 4, '', 'rangoNumeros campoPorcentaje', 'campoDescuentoListadoArticulos', array('rango' => '1-99')+$tutorial["13"], $textos->id('AYUDA_DESCUENTO_GENERAL'));
    $linea3 .= HTML::frase($textos->id('GANANCIA_GENERAL'), 'margenIzquierdaDoble');
    $linea3 .= HTML::campoTexto('datos[ganancia_general]', 4, 4, $sesion_configuracionGlobal->porcPredGanancia, 'rangoNumeros campoPorcentaje', 'campoGananciaListadoArticulos', array('rango' => '1-200')+$tutorial["14"], $textos->id('AYUDA_GANANCIA_GENERAL'));

    $ayuda = '';
    $linea3 .= HTML::contenedor($ayuda, 'contenedorAyudaCompraMercancia');
    
    //codigo que verifica si es una factura existente y genera los articulos
    $listadoDeArticulos = '';
    
    $filas = array();
    $opcionesFilas = array();    
    

    $cadenaListadoDeArticulos   = '';
    
    if (!empty($objeto->listaArticulos)) {
        $codigoLista                = '';
        $counter                    = 0;
        
        
        foreach ($objeto->listaArticulos as $article) {            

            $counter++;
            
            if ($article->descuento == 0 || $article->descuento == '0') {
                $article->subtotal = $article->cantidad * $article->precio;
                
            } else {
                $article->subtotal = ($article->cantidad * $article->precio) - ( ( ($article->cantidad * $article->precio) * $article->descuento) / 100 );
                
            }
            
            //formatear los datos a presentar
            $article->subtotal      = Recursos::formatearNumero($article->subtotal, '$');
            $article->precio        = Recursos::formatearNumero($article->precio, '$');
            $article->precioVenta   = Recursos::formatearNumero($article->precioVenta, '$');   
            
            $precioVenta      = $article->precioVenta;
            $porcPredGanancia = $sesion_configuracionGlobal->porcPredGanancia;
            
            //si no tiene precio de venta, o el precio de venta es igual al precio de compra del articulo, le
            //pongo un precio de venta
            if($precioVenta == '0' || $precioVenta == $article->precio){
                $precioVenta = $article->precio + (($article->precio * $sesion_configuracionGlobal->porcPredGanancia) / 100);
                
            } else {//si no, calculo el porcentaje de ganancia
                $porcPredGanancia = (($precioVenta - $article->precio) * 100) / $article->precio; 
                
                if ($objeto->iva > 0){
                    $porcPredGanancia  -= $article->iva;
                }
                                
            }
            
            $precioVenta        = Recursos::formatearNumero($precioVenta, '$'); 
            $porcPredGanancia   = Recursos::formatearNumero($porcPredGanancia, '$', '', 'entero');

            //opciones que se le pasan al parrafo padre para realizar los calculos
            $opciones = array(
                'subtotal'      => $article->subtotal,
                'precio'        => $article->precio,//este precio que se carga, es el ultimo precio de compra
                'precioVenta'   => $precioVenta,
                'descuento'     => $article->descuento,
                'cantidad'      => $article->cantidad,
                'cod'           => (int)$article->idArticulo,
                'bodega'        => (int)$article->idBodega,
                'iva'           => (int)$article->iva,
                'class'         => "filaArticuloCompra",
                'id'            => 'fila_'.$counter,
            );

            $cod = array();
            //Armo el parrafo con los campos del listado de articulos de compra
            $cod[] = HTML::campoTexto('', 50, 255, $article->articulo, 'campoDescripcionArticulo  medioMargenSuperior', 'articuloFactura_' . (int)$article->id, array('disabled' => 'disabled'));
            $cod[] = HTML::campoTexto('', 3, 2, $article->cantidad, ' soloNumeros cantidadArticuloCompra campoCantidadArticulo valorMinimo ', '', array('valor_minimo' => '1'));
            $cod[] =  HTML::campoTexto('', 3, 2, $article->descuento, ' soloNumeros descuentoGeneralArticuloCompra campoDescuentoArticulo campoPorcentaje rangoNumeros ', '', array('rango' => '1-99'));
            $cod[] =  HTML::campoTexto('', 8, 15, number_format($article->precio, $cantidadDecimales, '.', ''), '  precioUnitarioArticuloCompra campoPrecioUnitario campoDinero', '');
            $cod[] =  HTML::campoTexto('', 8, 10, number_format($article->subtotal, $cantidadDecimales, '.', '') . '', '  subtotalArticuloCompra campoSubtotalArticulo campoDinero', '', array('disabled' => 'disabled'));
            $cod[] =  HTML::imagen($configuracion['SERVIDOR']['media'] . $configuracion['RUTAS']['imagenesEstilos'] . 'eliminar.png', 'imagenEliminarItemFila', 'imagenEliminarItemFila' . $counter, array('ayuda' => 'Eliminar este articulo'));
            $cod[] =  HTML::imagen($configuracion['SERVIDOR']['media'] . $configuracion['RUTAS']['imagenesEstilos'] . 'consultar.png', 'imagenConsultarItemFila', 'imagenConsultarItemFila' . $counter, array('ayuda' => 'Consultar este articulo'));
            $cod[] =  HTML::campoTexto('', 3, 3, $porcPredGanancia, 'porcentajeGanancia campoPorcentajeGanancia campoPorcentaje', '');
            $cod[] =  HTML::campoTexto('', 8, 15, number_format($precioVenta, $cantidadDecimales, '.', ''), ' precioVentaArticulo campoPrecioVentaArticulo campoDinero ', '');            
            $cod[] =  HTML::imagen($configuracion['SERVIDOR']['media'] . $configuracion['RUTAS']['imagenesEstilos'] . 'mover.png', 'imagenMoverItemBodega ', 'imagenConsultarItemFila' . $counter, array('ayuda' => 'Mover a bodega diferente'));
            $cod[] =  HTML::frase($textos->id('BODEGA').HTML::frase((int)$article->idBodega, 'idBodegaCompra'), 'txtBodegaCompra');
            
            $filas[]            = $cod;
            $opcionesFilas[]    = $opciones;
            //cadena listado de articulos para la compra, esta cadena es la que se envia al control para hacer la insercion en la tabla "articulos_factura_compra"
            $cadenaListadoDeArticulos .= (int) $article->idArticulo . ';' . $article->cantidad . ';' . $article->descuento . ';' . $article->precio . ';' . $article->idBodega . ';' . $precioVenta . '|';
            
        }
        
        $listadoDeArticulos = $codigoLista;        
    }
    
    $columnasTabla  = array(
                            $textos->id('ARTICULO'),
                            $textos->id('CANTIDAD'),
                            $textos->id('DESCUENTO'),
                            $textos->id('PRECIO_UNITARIO'),
                            $textos->id('SUBTOTAL'),
                            $textos->id('ELIMINAR'),
                            $textos->id('CONSULTAR'),
                            $textos->id('PORCENTAJE_GANANCIA'),
                            $textos->id('PRECIO_VENTA'),
                            $textos->id('MOVER')
        
                        );
    
    $claseColumnas = array(
                        'columna_1', 
                        'columna_2', 
                        'columna_3', 
                        'columna_4', 
                        'columna_5', 
                        'columna_6', 
                        'columna_7', 
                        'columna_8', 
                        'columna_9', 
                        'columna_10'
                        );
    $claseFilas    = array();
    $opciones      = array();
    $idFila        = array();
    
    $tablaArticulos = HTML::tabla($columnasTabla, $filas, 'tablaListaArticulosFactura', 'tablaListaArticulosFactura', $claseColumnas, $claseFilas, $opciones, $idFila, $opcionesFilas);
        
    
    $linea3 .= HTML::contenedor($tablaArticulos . HTML::frase($listadoDeArticulos, 'fraseListaArticulos') . HTML::campoOculto('datos[cadenaArticulosPrecios]', $cadenaListadoDeArticulos, 'cadenaArticulosPrecios'), 'contenedorListadoArticulosFactura margenSuperior', '', array()+$tutorial["15"]);

    $codigo .= HTML::parrafo($linea3, 'linea3');


    $contenedorInfoArticulo = HTML::contenedor('', 'contenedorInfoArticulo', 'contenedorInfoArticulo');
    
    $codigo .= HTML::parrafo($contenedorInfoArticulo, '');

    $linea4 = HTML::frase($textos->id('VALOR_FLETE'), 'margenSuperior');
    $linea4 .= HTML::campoTexto('datos[valor_flete]', 8, 10, $objeto->valorFlete, 'margenSuperior campoDinero', 'campoValorFlete', array()+$tutorial["17"]);
        
    
    $linea4 .= HTML::frase($textos->id('CONCEPTO'), 'margenSuperior margenIzquierda');
    $linea4 .= HTML::campoTexto('datos[concepto1]', 20, 30, $objeto->concepto1, 'margenSuperior', 'campoConcepto1', array()+$tutorial["18"]);
    $linea4 .= HTML::frase($textos->id('DESCUENTO1'), 'margenSuperior medioMargenIzquierda');
    $linea4 .= HTML::campoTexto('datos[descuento1]', 3, 2, $objeto->descuento1, 'margenSuperior campoPorcentaje', 'campoDescuento1', array()+$tutorial["19"]);

    $linea4 .= HTML::frase($textos->id('MAS_DESCUENTO'), 'margenSuperior estiloEnlace margenIzquierda', 'fraseMasDescuento', array()+$tutorial["20"]);

    if (empty($objeto->concepto2) || empty($objeto->descuento2)) {
        $claseDescuento2 = 'oculto';
    }

    $linea4 .= HTML::frase($textos->id('CONCEPTO'), 'margenSuperior margenIzquierda dctoOculto ' . $claseDescuento2);
    $linea4 .= HTML::campoTexto('datos[concepto2]', 20, 30, $objeto->concepto2, 'margenSuperior dctoOculto ' . $claseDescuento2, 'campoConcepto1', '');
    $linea4 .= HTML::frase($textos->id('DESCUENTO2'), 'margenSuperior medioMargenIzquierda dctoOculto ' . $claseDescuento2);
    $linea4 .= HTML::campoTexto('datos[descuento2]', 3, 2, $objeto->descuento2, 'margenSuperior campoPorcentaje dctoOculto ' . $claseDescuento2, 'campoDescuento2');    
   
    //subtotal, si viene algo en el objeto se pone, si no es 0
    $subtotal = ($objeto->subtotal) ? $objeto->subtotal  : '0';
    $linea4 .= HTML::frase($textos->id('SUBTOTAL'), 'margenIzquierdaDoble subtitulo');
    $linea4 .= HTML::frase("<span class='prefijo_numero'>$</span>".Recursos::formatearNumero($subtotal, '$', ''), 'margenIzquierda negrilla titulo', 'campo_subtotal', array()+$tutorial["21"]);
    $linea4 .= HTML::campoOculto('datos[subtotal]', $subtotal, 'subtotal');

    $claseCampoNumFac = '';
    
    if (!empty($crearOrden)) {
        $claseCampoNumFac = 'oculto';
        
    }

    $linea4 .= HTML::imagen($configuracion['SERVIDOR']['media'] . $configuracion['RUTAS']['imagenesEstilos'] . 'eliminar.png', 'imagenEliminarTodosArticulos flotanteDerecha', 'imagenEliminarTodosArticulos', array('ayuda' => 'Eliminar todos los <br>articulos de la lista')+$tutorial["16"]);

    $codigo .= HTML::parrafo($linea4, 'linea4');
    
    $linea5  = HTML::frase($textos->id('OBSERVACIONES'), '');
    $linea5 .= HTML::campoTexto('datos[observaciones]', 35, 250, $objeto->observaciones, '', 'campoObservaciones', array()+$tutorial["22"]); 
    
    $valorIva = ($objeto->iva == '') ? 0 : $objeto->iva;
    
    $clase = ($regimenProveedor == "1") ? "oculto" : "";
   
    $linea5 .= HTML::frase($textos->id('IVA'), 'margenIzquierda campoIva '.$clase);
    $linea5 .= HTML::campoTexto('datos[iva]', 8, 10, $valorIva, 'campoDinero campoIva '.$clase, 'campoIva', array()+$tutorial["23"]);
    
    $linea5 .= HTML::frase($textos->id('TOTAL'), 'margenIzquierdaTriple margenSuperiorDoble margenInferiorDoble masGrande2');

    $total = ($objeto->total == '') ? '0 ' : $objeto->total;

    $linea5 .= HTML::frase("<span class='prefijo_numero'>$</span>".Recursos::formatearNumero($total, '$', ''), 'medioMargenIzquierda negrilla ', 'campoTotal', array()+$tutorial["24"]);;
    $linea5 .= HTML::campoOculto('datos[total]', $objeto->total, 'totalFactura', 'campoTotal');
    $linea5 .= HTML::boton('chequeo', $textos->id('FINALIZAR_FACTURA'), 'margenDerecha5PorCien margensuperiorDoble margenInferior25 flotanteDerecha directo', '', 'botonFinalizarFactura', '', array('validar' => 'NoValidar', 'ayuda' => 'Ctrl + Enter')+$tutorial["25"]);

    $codigo .= HTML::parrafo($linea5, 'linea5');

    //$codigo .= HTML::parrafo($linea6, '');

    $codigo = HTML::forma($destino, $codigo, 'P', true, "formaCompraMercancias");

    $contenido .= HTML::bloque('bloqueContenidoPrincipal', $tituloBloque, $codigo, '', 'overflowVisible');
    
} else {
    $contenido .= HTML::contenedor($textos->id('SIN_PERMISOS_ACCESO_MODULO'), 'textoError');
    
}

Plantilla::$etiquetas['BLOQUE_CENTRAL'] = $contenido;
