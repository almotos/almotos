<?php

/**
 *
 * @package     FOM
 * @subpackage  Ventas Mercancia
 * @author      Pablo Andrés Vélez Vidal <pavelez8@misena.edu.co>
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2012 GENESYS Corporation.
 * @version     0.2
 *
 * */
global $sesion_usuarioSesion, $modulo, $configuracion, $textos, $forma_idFactura, $forma_crearCotizacion, $forma_idCotizacion, $sql, $sesion_configuracionGlobal;

$idFactura          = $forma_idFactura; //si lo que se va es a modificar una factura existente
$idCotizacion       = $forma_idCotizacion; //si lo que se va es a crear una factura desde una cotizacion 
$idFactTemp         = $forma_idFactTemp; //si lo que se va es a crear una factura desde una factura temporal
$crearCotizacion    = $forma_crearCotizacion; //si se va a crear una cotizacion desde el modulo de cotizaciones
//verificar que despues que se realiza una factura desde una cotización, recarge el formulario en limpio
$existeCotizacion = $sql->existeItem('cotizaciones', 'id', $idCotizacion);

if (!$existeCotizacion) {
    $idCotizacion = '';
}

//verificar que despues que se realiza una factura desde una factura temporal, recarge el formulario en limpio
$existeFactTemp = $sql->existeItem('facturas_temporales_venta', 'id', $idFactTemp);

if (!$existeFactTemp) {
    $idFactTemp = '';
}

if (!empty($idFactura)) {
    $objeto         = new FacturaVenta($idFactura); /* creacion del objeto */
    $tituloBloque   = $textos->id('MODULO_ACTUAL') . ' :: ' . $textos->id('EDITAR_FACTURA_CLIENTE');

} elseif (!empty($idCotizacion)) {
    $objeto         = new Cotizacion($idCotizacion); /* creacion del objeto */
    $tituloBloque   = $textos->id('MODULO_ACTUAL') . ' :: ' . str_replace('%1', (int)$idCotizacion, $textos->id('GENERAR_FACTURA_DESDE_COTIZACION'));

} elseif ($crearCotizacion) {
    $objeto         = new Cotizacion(); /* creacion del objeto */
    $tituloBloque   = $textos->id('MODULO_ACTUAL') . ' :: ' . $textos->id('CREAR_COTIZACION');

} elseif (!empty($idFactTemp)) {
    $objeto     = new FacturaVenta(); /* creacion del objeto */
    
    $objeto->cargarFacturaTemporal($idFactTemp);
    
    $tituloBloque = $textos->id('MODULO_ACTUAL') . ' :: ' . str_replace('%1', $objeto->fechaFactura, $textos->id('GENERAR_FACTURA_DESDE_FACTURA_TEMPORAL'));
 
} else {
    $objeto         = new FacturaVenta(); /* creacion del objeto */
    $tituloBloque   = $textos->id('MODULO_ACTUAL') . ' :: ' . $textos->id('CREAR_FACTURA_CLIENTE');
    
}

$tituloBloque .= HTML::imagen($configuracion['SERVIDOR']['media'] . $configuracion['RUTAS']['imagenesEstilos'] . '/' . strtolower($modulo->url) . '.png', 'iconoModulo');

$contenido  = '';
$contenido .= HTML::contenedor(HTML::contenedor($textos->id('AYUDA_MODULO'), 'ui-corner-all'), 'ui-widget-shadow ui-corner-all oculto', 'contenedorAyudaUsuario');


//capturar el regimen del proveedor
$regimenCliente = '';

if ($objeto->idCliente) {
    $regimenCliente = $sql->obtenerValor("clientes", 'regimen', 'id = "'.$objeto->idCliente.'"');
    
}

$destino = '/ajax/ventas_mercancia/add';

$cantidadDecimales = $sesion_configuracionGlobal->cantidadDecimales;

//obtener el regimen de la empresa para validar si factura iva o no

$regimenEmpresa = $sesion_configuracionGlobal->empresa->regimen;

//campo oculto del cual el javascript sacara el nombre del modulo actual ->para??
$contenido .= HTML::contenedor('', 'contenedorNotificaciones', 'contenedorNotificaciones', array("style" => "top:0px !important;"));
$contenido .= HTML::campoOculto('nombreModulo', ucwords(strtolower($modulo->nombre)), 'nombreModulo');
$contenido .= HTML::campoOculto('cantidadDecimales', $cantidadDecimales, 'cantidadDecimales');
$contenido .= HTML::campoOculto('tipoMonedaColombiana', $sesion_configuracionGlobal->tipoMoneda, 'tipoMonedaColombiana');
$contenido .= HTML::campoOculto('regimenCliente', $regimenCliente, 'regimenCliente');
$contenido .= HTML::campoOculto('regimenEmpresa', $regimenEmpresa, 'regimenEmpresa');
//campo que determina si se le cobra al flete el iva, temporal quemado, funcional variable desde configuracion
$contenido .= HTML::campoOculto('porcentajeIvaFleteVentas', $sesion_configuracionGlobal->ivaGeneral, 'porcentajeIvaFleteVentas');


//campo que determina el maximo valor de descuento a autorizar
$contenido .= HTML::campoOculto('dcto_maximo', $sesion_usuarioSesion->dctoMaximo, 'dctoMaximo');

//$contenido .= HTML::campoOculto('orden' . ucwords(strtolower($modulo->nombre)), 'descendente|' . $objeto->ordenInicial, 'ordenGlobal');
//$ayuda = HTML::cargarIconoAyuda($textos->id('AYUDA_MODULO'));
/**
 * Verifico que se haya iniciado una sesion y que tenga permisos para ver el modulo
 * */
if ((isset($sesion_usuarioSesion) && Perfil::verificarPermisosModulo($modulo->id)) || isset($sesion_usuarioSesion) && $sesion_usuarioSesion->idTipo == 0) {
    
    $tutorial = array(
            '0'  => array('data-step' => '0',  'data-intro' => $textos->id("AYUDA_SELECCIONE_EL_VENDEDOR")),
            '1'  => array('data-step' => '1',  'data-intro' => $textos->id("AYUDA_SELECCIONE_EL_CLIENTE")),
            '3'  => array('data-step' => '3',  'data-intro' => $textos->id("AYUDA_INGRESA_FECHA_VENTA")),
            '4'  => array('data-step' => '4',  'data-intro' => $textos->id("AYUDA_BUSCAR_FACTURAS_ANTERIORES")),
            '8'  => array('data-step' => '8',  'data-intro' => $textos->id("AYUDA_BUSCAR_CATALOGO_MOTO")),
            '9'  => array('data-step' => '9',  'data-intro' => $textos->id("AYUDA_BUSCAR_Y_CARGAR_ORDEN_DE_VENTA")),
            '10' => array('data-step' => '10', 'data-intro' => $textos->id("AYUDA_INGRESAR_ARTICULO_A_LA_FACTURA")),
            '12' => array('data-step' => '12', 'data-intro' => $textos->id("AYUDA_CARGAR_MULTIPLES_ARTICULOS")),
            '13' => array('data-step' => '13', 'data-intro' => $textos->id("AYUDA_DESCUENTO_GENERAL")),
            '14' => array('data-step' => '14', 'data-intro' => $textos->id("AYUDA_LISTADO_ARTICULOS")),
            '15' => array('data-step' => '15', 'data-intro' => $textos->id("AYUDA_VALOR_FLETE")),
            '16' => array('data-step' => '16', 'data-intro' => $textos->id("AYUDA_CONCEPTO_DESCUENTO")),
            '17' => array('data-step' => '17', 'data-intro' => $textos->id("AYUDA_DESCUENTO")),
            '18' => array('data-step' => '18', 'data-intro' => $textos->id("AYUDA_MAS_DESCUENTO")),
            '19' => array('data-step' => '19', 'data-intro' => $textos->id("AYUDA_GUIA_SUBTOTAL")),
            '20' => array('data-step' => '20', 'data-intro' => $textos->id("AYUDA_DCTO_PRONTO_PAGO")),        
            '21' => array('data-step' => '21', 'data-intro' => $textos->id("AYUDA_BORRAR_LISTADO_ARTICULOS")),
            '22' => array('data-step' => '22', 'data-intro' => $textos->id("AYUDA_OBSERVACIONES")),
            '23' => array('data-step' => '23', 'data-intro' => $textos->id("AYUDA_IVA")),
            '24' => array('data-step' => '24', 'data-intro' => $textos->id("AYUDA_TOTAL")),
            '25' => array('data-step' => '25', 'data-intro' => $textos->id("AYUDA_FINALIZAR_FACTURA")),
 
    );    
    
    
    $codigo = '';
    
    $linea1 = HTML::frase($textos->id('VENDEDOR'), '');

    //Selecciono las cajas existentes en el sistema
    $listaUsuarios = array();//arreglo que almacenará el listado de cajas y será pasado como parametro al metodo HTML::listaDesplegable

    $idDelUsuario = '1'; //determinar si no viene ningun dato para el cliente que sea el base
    
    if (!empty($sesion_usuarioSesion->id)) {
        $idDelUsuario = $sesion_usuarioSesion->idCliente;
    }     
    //codigo que genera la lista desplegable con los vendedores
    $consulta = $sql->seleccionar(array('usuarios'), array('id', 'usuario'), 'activo = "1" && vendedor="1"', '', 'usuario ASC');
    
    if ($sql->filasDevueltas) {
        while ($dato = $sql->filaEnObjeto($consulta)) {//recorro las respuesta, y voy guardandolas en el arreglo
            $listaUsuarios[$dato->id] = (int)$dato->id.' - '.$dato->usuario;
        }
        
    }

    //elaboro la lista deplegable con la info de los usuarios vendedores
    $selectorUsuario = HTML::listaDesplegable('datos[id_usuario]', $listaUsuarios, $idDelUsuario, 'selectChosen', 'selectorUsuario', '', array(), $textos->id('AYUDA_SELECCIONAR_VENDEDOR'));   


    $linea1 .= $selectorUsuario;//HTML::campoTexto('datos[cliente]', 35, 255, $nomDelCliente, 'autocompletable campoObligatorio', 'campoIdCliente', array('title' => '/ajax/clientes/listar'), $textos->id('AYUDA_USO_AUTOCOMPLETAR'), '/ajax/clientes/add', 'datos[id_cliente]', $idDelCliente);
    
    
    $linea1 .= HTML::frase($textos->id('CLIENTE'), 'margenIzquierdaDoble');

    //Selecciono las cajas existentes en el sistema
    $listaCliente = array();//arreglo que almacenará el listado de cajas y será pasado como parametro al metodo HTML::listaDesplegable

    $idDelCliente = '1'; //determinar si no viene ningun dato para el cliente que sea el base
    
    if (!empty($objeto->idCliente)) {
        $idDelCliente = $objeto->idCliente;
    }    
    
    $regimenCliente = $sql->obtenerValor("clientes", "regimen", "id = '".$idDelCliente."'");  
    
    $consulta = $sql->seleccionar(array('clientes'), array('id', 'nombre'), 'id != "0"', '', 'nombre ASC');
    
    if ($sql->filasDevueltas) {
        while ($dato = $sql->filaEnObjeto($consulta)) {//recorro las respuesta, y voy guardandolas en el arreglo
            $listaCliente[$dato->id] = (int)$dato->id.' - '.$dato->nombre;
        }
        
    }

    //elaboro la lista deplegable con la info de las cajas
    $selectorCliente = HTML::listaDesplegable('datos[id_cliente]', $listaCliente, $idDelCliente, 'selectChosen', 'selectorCliente', '', array(), $textos->id('AYUDA_SELECCIONAR_CLIENTE'), '/ajax/clientes/add');   


    $linea1 .= $selectorCliente;//HTML::campoTexto('datos[cliente]', 35, 255, $nomDelCliente, 'autocompletable campoObligatorio', 'campoIdCliente', array('title' => '/ajax/clientes/listar'), $textos->id('AYUDA_USO_AUTOCOMPLETAR'), '/ajax/clientes/add', 'datos[id_cliente]', $idDelCliente);

    $claseCampoNumFac = array();
    
    if (!empty($crearCotizacion)) {
        $claseCampoNumFac = 'oculto';
    }

    $linea1 .= HTML::frase($textos->id('FECHA'), 'margenIzquierda');

    $fechaFact      = explode(' ', $objeto->fechaFactura);
    $fechaFactura   = $fechaFact[0];
    
    if (empty($fechaFactura)) {
        $fechaFactura = date('Y-m-d');
    }

    $linea1 .= HTML::campoTexto('datos[fecha_factura]', 12, 15, $fechaFactura, 'campoObligatorio fechaReciente campoCalendario', '', array()+$tutorial["3"], $textos->id('AYUDA_FECHA_FACTURA'));
    $linea1 .= HTML::frase($fechaFact[1], 'margenIzquierda negrilla');

    $linea1 .= HTML::campoOculto('datos[es_cotizacion]', '', 'esCotizacion'); //Segun se ingrese o se quite el numero de factura de cliente, este campo va a determinar si solo puede ser una cotizacion
    $linea1 .= HTML::campoOculto('datos[es_modificacion]', $forma_idFactura, 'esModificacion'); //el valor de este campo determina si se trata de la edicion de una factura
    $linea1 .= HTML::campoOculto('datos[modificacion_cotizacion]', $forma_idCotizacion, 'modificacionCotizacion'); //el valor de este campo determina si es generar una factura desde una cotizacion de venta previa

    $linea1 .= HTML::campoOculto('datos[id_factura_temporal]', $idFactTemp, 'idFacturaTemporal'); //Es utilizado por javascript (compras_mercancia.js metodo guardarFacturaTemporal() ) para verificar si se debe crear la factura temporal o se debe modificar

    $linea1 .= HTML::campoOculto('datos[es_factura_temporal]', $idFactTemp, 'idEsFacturaTemporal'); //Identificador de la factura temporal, para irla guardando o eliminarla al crear la factura, o la orden de compra
    
    $linea1 .= HTML::campoOculto('nombreModulo', ucwords(strtolower($modulo->nombre)), 'nombreModulo');
    
    $linea1 .= HTML::contenedor(HTML::contenedor('', 'contenedorImagenTeachme', '', array('ayuda' => 'Enseñame a realizar una Venta')), 'ayudaFormularioVenta');    
    
    
    $codigo .= HTML::parrafo($linea1, 'linea1');
    
      

//    $listaSedes = array();
//    $consulta = $sql->seleccionar(array('sedes_empresa'), array('id', 'nombre'), '', '', 'nombre ASC');
//    if ($sql->filasDevueltas) {
//        while ($dato = $sql->filaEnObjeto($consulta)) {
//            $listaSedes[$dato->id] = $dato->nombre;
//        }
//    }

    $listaBodegas   = array();
    
    $consulta       = $sql->seleccionar(array('bodegas'), array('id', 'nombre'), 'id_sede = "' . $sesion_usuarioSesion->sede->id . '"', '', 'nombre ASC');
    
    if ($sql->filasDevueltas) {
        while ($dato = $sql->filaEnObjeto($consulta)) {
            $listaBodegas[$dato->id] = (int)$dato->id.' :: '.$dato->nombre;
        }
    }

    $listaCajas = array();
    
    $consulta = $sql->seleccionar(array('cajas'), array('id', 'nombre'), 'id_sede = "' . $sesion_usuarioSesion->sede->id . '"', '', 'nombre ASC');
    
    if ($sql->filasDevueltas) {
        while ($dato = $sql->filaEnObjeto($consulta)) {
            $listaCajas[$dato->id] = $dato->nombre;
            
        }
        
    }

    $selectorCajas      = HTML::listaDesplegable('datos[id_caja]', $listaCajas, '', 'selectChosen', 'selectorCaja', '', array(), '');

    $idBodegaPrincipal  = $sql->obtenerValor('bodegas', 'id', 'id_sede = "' . $sesion_usuarioSesion->sede->id . '" ANd principal = "1"');

//    $selectorSedes = HTML::listaDesplegable('datos[sede]', $listaSedes, $sesion_usuarioSesion->sede->id, '', 'selectorSedes', $textos->id('SELECCIONAR') . '...', array(), '');
    $selectorBodega     = HTML::listaDesplegable('datos[bodega]', $listaBodegas, $idBodegaPrincipal, 'selectChosen', 'selectorBodegas', '', array(), '');

//    $linea2 .= HTML::frase($textos->id('SEDE').': ', 'margenIzquierdaDoble');
//    $linea2 .= HTML::frase($sesion_usuarioSesion->sede->nombre, 'negrilla subtitulo');
    $linea2 .= HTML::frase($textos->id('BODEGA'), '');
    $linea2 .= $selectorBodega;
    $linea2 .= HTML::campoOculto('datos[id_bodega]', (int)$idBodegaPrincipal, 'idBodegaGeneral');
    $linea2 .= HTML::frase($textos->id('CAJA') . ': ', 'margenIzquierda');
    $linea2 .= $selectorCajas;
    
    $rutaImagen = $configuracion['SERVIDOR']['media'] . $configuracion['RUTAS']['imagenesEstilos'] . 'buscar_catalogo.png';
//    $linea1 .= HTML::frase($textos->id('CATALOGOS'), 'margenIzquierdaDoble');
    $linea2 .= HTML::imagen($rutaImagen, 'estiloEnlace margenIzquierdaDoble', 'imagenBuscarCatalogo', array('ayuda' => $textos->id('BUSCAR_CATALOGOS'))+$tutorial["8"] );

    $rutaImagen = $configuracion['SERVIDOR']['media'] . $configuracion['RUTAS']['imagenesEstilos'] . 'orden-cotizacion.png';
//    $linea1 .= HTML::frase($textos->id('CARGAR_COTIZACION'), 'margenIzquierdaDoble');
    $linea2 .= HTML::imagen($rutaImagen, 'estiloEnlace margenIzquierdaDoble', 'imagenCargarCotizacion', array('ayuda' => $textos->id('AYUDA_CARGAR_COTIZACION'))+$tutorial["9"] );
    
    $rutaImagen = $configuracion['SERVIDOR']['media'] . $configuracion['RUTAS']['imagenesEstilos'] . 'buscar_factura2.png';
//    $linea1 .= HTML::frase($textos->id('BUSCAR_FACTURA'), 'margenIzquierdaDoble');
    $linea2 .= HTML::imagen($rutaImagen, 'estiloEnlace margenIzquierdaDoble', 'imagenBuscarFactura', array('ayuda' => $textos->id('BUSCAR_FACTURAS'))+$tutorial["4"] );    

    $codigo .= HTML::parrafo($linea2, 'linea2');

    $linea3 = HTML::frase($textos->id('ARTICULO'), 'margenSuperior');
    $linea3 .= HTML::campoTexto('', 40, 255, '', 'autocompletable campoObligatorio margenSuperior', 'articuloFactura', array('title' => '/ajax/articulos/listarArticulosVenta?extra=' . $idBodegaPrincipal)+$tutorial["10"], $textos->id('AYUDA_USO_AUTOCOMPLETAR'), '/ajax/articulos/add');
    $rutaImagen = $configuracion['SERVIDOR']['media'] . $configuracion['RUTAS']['imagenesEstilos'] . 'listaAgregarArticulos.png';
    //$rutaImagen2 = $configuracion['SERVIDOR']['media'] . $configuracion['RUTAS']['imagenesEstilos'] . 'lista_completa.png';
    $linea3 .= HTML::frase($textos->id('AGREGAR_VARIOS_ARTICULOS'), 'margenIzquierdaDoble');
    $linea3 .= HTML::imagen($rutaImagen, 'estiloEnlace', 'imagenAgregarVariosArticulos', array('ayuda' => $textos->id('AGREGAR_VARIOS_ARTICULOS'))+$tutorial["12"]);
    //$linea3 .= HTML::imagen($rutaImagen2, 'estiloEnlace margenIzquierdaDoble', 'imagenListarTodosArticulos', array('ayuda' => $textos->id('LISTAR_TODOS_ARTICULOS')));
    $linea3 .= HTML::frase($textos->id('DESCUENTO_GENERAL'), 'margenIzquierdaDoble');
    $linea3 .= HTML::campoTexto('datos[dcto_general]', 4, 4, '', 'rangoNumeros campoPorcentaje', 'campoDescuentoListadoArticulos', array('rango' => '1-99')+$tutorial["13"], str_replace("%1", $sesion_usuarioSesion->dctoMaximo, $textos->id('MAXIMO_DESCUENTO_PERMITIDO') ) );

    $linea3 .= HTML::contenedor('', 'contenedorAyudaVentaMercancia');
    

    
    $filas = array();
    $opcionesFilas = array();
    
    //codigo que verifica si es una factura existente y genera los articulos
    $listadoDeArticulos = '';
    if (!empty($objeto->listaArticulos)) {
        
        //print_r($objeto->listaArticulos);
        
        $codigoLista = '';
        
        $cadenaListadoDeArticulos = '';
        
        $counter = 0;
        
        foreach ($objeto->listaArticulos as $article) {

            $counter++;
            if ($article->descuento == 0 || $article->descuento == '0') {
                $article->subtotal = $article->cantidad * $article->precio;
            } else {
                $article->subtotal = ($article->cantidad * $article->precio) - ( ( ($article->cantidad * $article->precio) * $article->descuento) / 100 );
            }

            $id_bodega = $article->idBodega;
            
            if ( empty($id_bodega)) {
                $id_bodega = $idBodegaPrincipal;
            }
            
            $opciones = array(
                'subtotal'  => $article->subtotal,
                'precio'    => $article->precio,
                'descuento' => $article->descuento,
                'cantidad'  => $article->cantidad,
                'cod'       => (int)$article->idArticulo,
                'precio_base' => $article->precio,
                'bodega'    => (int)$id_bodega,
                'class'     => 'filaArticuloVenta',
                'id'        => 'fila_'.$counter,
            );
            
            $ivaTotal = 0;
            
            //si es diferente al regimen simplificado muestro y sumo el iva
            if ($regimenEmpresa != "1") {
                $opciones['iva'] = (int)$article->iva;
                
                $ivaTotal = ($article->subtotal * $article->iva) / 100;
                
            }
            
            $idPrincipalArticulo = (string)$sesion_configuracionGlobal->idPrincipalArticulo;
            
            if ($idPrincipalArticulo == "id") {
                $idPrincipalArticulo = "idArticulo";                
            } 

            $cod = array();
            $cod[] = HTML::campoTexto('', 50, 50, (int)$article->$idPrincipalArticulo."::".$article->articulo, 'campoDescripcionArticulo margenIzquierda medioMargenSuperior', 'articuloFactura_' . $article->id, array('disabled' => 'disabled'));
            $cod[] = HTML::campoTexto('', 3, 2, $article->cantidad, 'soloNumeros cantidadArticuloVenta', '');
            $cod[] = HTML::campoTexto('', 3, 2, $article->descuento, 'soloNumeros descuentoGeneralArticuloVenta campoPorcentaje', '');
            $cod[] = HTML::campoTexto('', 10, 10, number_format($article->precio, $cantidadDecimales, '.', ''), ' precioUnitarioArticuloVenta campoDinero', '');
            //si el regimen de la empresa es diferente al simplificado genero los campos del iva
            if ($regimenEmpresa != "1") {
                $cod[] = HTML::campoTexto('', 5, 5, $article->iva, ' ivaArticuloVenta campoDisabledConColor campoPorcentaje ', '', array('disabled' => 'disabled'));
                $cod[] = HTML::campoTexto('', 10, 10, number_format($ivaTotal, $cantidadDecimales, '.', ''), ' ivaTotalArticuloVenta campoDisabledConColor campoDinero ', '', array('disabled' => 'disabled'));
            }
            
            $cod[] = HTML::campoTexto('', 10, 10, number_format($article->subtotal, $cantidadDecimales, '.', '') . '', ' subtotalArticuloVenta  campoDisabledConColor campoDinero', '', array('disabled' => 'disabled'));
            $cod[] = HTML::imagen($configuracion['SERVIDOR']['media'] . $configuracion['RUTAS']['imagenesEstilos'] . 'eliminar.png', 'imagenEliminarItemFila margenIzquierdaDoble cursorManito', 'imagenEliminarItemFila' . $counter, array("ayuda" => "Eliminar este articulo<br>del listado"));
            $cod[] = HTML::imagen($configuracion['SERVIDOR']['media'] . $configuracion['RUTAS']['imagenesEstilos'] . 'consultar.png', 'imagenConsultarItemFila margenIzquierdaDoble cursorManito', 'imagenConsultarItemFila' . $counter, array("ayuda" => "Consultar articulo"));

            $filas[]            = $cod;
            $opcionesFilas[]    = $opciones;
            //$codigoLista .= HTML::parrafo($cod, 'filaArticuloVenta', 'fila_' . $counter, $opciones);
            $cadenaListadoDeArticulos .= (int) $article->idArticulo . ';' . $article->cantidad . ';' . $article->descuento . ';' . $article->precio . ';' . (int)$idBodegaPrincipal . ';' . $article->iva .'|';
        }
       // $listadoDeArticulos = $codigoLista;

    }    
      
    $columnasTabla  = array(
                            $textos->id('ARTICULO'),
                            $textos->id('CANTIDAD'),
                            $textos->id('DESCUENTO'),
                            $textos->id('PRECIO_UNITARIO'),
                            $textos->id('PORCENTAJE_IVA'),
                            $textos->id('IVA_TOTAL'),
                            $textos->id('SUBTOTAL'),
                            $textos->id('ELIMINAR'),
                            $textos->id('CONSULTAR')
        
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
    
    //si la empresa factura iva, se muestran los campos...nota, toco a las malas
    if ($regimenEmpresa == "1"){
        $claseColumnas = array_diff($claseColumnas, array('columna_5', 'columna_6'));
        $columnasTabla = array_diff($columnasTabla, array($textos->id('PORCENTAJE_IVA'), $textos->id('IVA_TOTAL')));
    } 
    
    $claseFilas    = array();
    $opciones      = array();
    $idFila        = array();
    
    $tablaArticulos = HTML::tabla($columnasTabla, $filas, 'tablaListaArticulosFactura', 'tablaListaArticulosFactura', $claseColumnas, $claseFilas, $opciones, $idFila, $opcionesFilas);
    
    
    $linea3 .= HTML::contenedor($tablaArticulos . HTML::frase($listadoDeArticulos, 'fraseListaArticulos') . HTML::campoOculto('datos[cadenaArticulosPrecios]', $cadenaListadoDeArticulos, 'cadenaArticulosPrecios'), 'contenedorListadoArticulosFactura margenSuperior', '', array()+$tutorial["14"]);

    $codigo .= HTML::parrafo($linea3, 'linea3');

    $contenedorInfoArticulo = HTML::contenedor('', 'contenedorInfoArticulo', 'contenedorInfoArticulo');
    $codigo .= HTML::parrafo($contenedorInfoArticulo, '');

    $linea4  = HTML::frase($textos->id('VALOR_FLETE'), 'margenSuperior');
    $linea4 .= HTML::campoTexto('datos[valor_flete]', 8, 15, $objeto->valorFlete, 'margenSuperior campoDinero', 'campoValorFlete',  array()+$tutorial["15"]);

    $linea4 .= HTML::frase($textos->id('CONCEPTO'), 'margenSuperior');
    $linea4 .= HTML::campoTexto('datos[concepto1]', 15, 30, $objeto->concepto1, 'margenSuperior', 'campoConcepto1', array()+$tutorial["16"]);
    $linea4 .= HTML::frase($textos->id('DESCUENTO1'), 'margenSuperior medioMargenIzquierda');
    $linea4 .= HTML::campoTexto('datos[descuento1]', 3, 4, $objeto->descuento1, 'margenSuperior campoPorcentaje rangoNumeros', 'campoDescuento1', array('rango' => '1-99')+$tutorial["17"]);

    $linea4 .= HTML::frase($textos->id('MAS_DESCUENTO'), 'margenSuperior estiloEnlace margenIzquierda', 'fraseMasDescuento', array()+$tutorial["18"]);

    if (empty($objeto->concepto2) || empty($objeto->descuento2)) {
        $claseDescuento2 = 'oculto';
    }

    $linea4 .= HTML::frase($textos->id('CONCEPTO'), 'margenSuperior margenIzquierdaTriple dctoOculto ' . $claseDescuento2);
    $linea4 .= HTML::campoTexto('datos[concepto2]', 15, 30, $objeto->concepto2, 'margenSuperior dctoOculto ' . $claseDescuento2, 'campoConcepto1', '');
    $linea4 .= HTML::frase($textos->id('DESCUENTO2'), 'margenSuperior medioMargenIzquierda dctoOculto ' . $claseDescuento2);
    $linea4 .= HTML::campoTexto('datos[descuento2]', 3, 4, $objeto->descuento2, 'margenSuperior dctoOculto campoPorcentaje rangoNumeros ' . $claseDescuento2, 'campoDescuento2', array('rango' => '1-99'));
        
    
    //subtotal, si viene algo en el objeto se pone, si no es 0
    $subtotal = ($objeto->subtotal) ? $objeto->subtotal  : '0';
    $linea4 .= HTML::frase($textos->id('SUBTOTAL'), 'margenIzquierdaDoble subtitulo');
    $linea4 .= HTML::frase("<span class='prefijo_numero'>$</span>".Recursos::formatearNumero($subtotal, '$', ''), 'margenIzquierda negrilla titulo', 'campo_subtotal', array()+$tutorial["19"]);
    $linea4 .= HTML::campoOculto('datos[subtotal]', $subtotal, 'subtotal');          
    
    $linea4 .= HTML::frase($textos->id('TEXTO_DCTO_EXTRA'), 'margenIzquierda estiloEnlace flotanteDerecha margenDerecha margenSuperior', 'textoDctoExtra', array('ayuda' => $textos->id('AYUDA_TEXTO_DCTO_EXTRA'))+$tutorial["20"]);

    //Codigo para las funcionalidades de descuento extra
    $textoDctoExtra1  = HTML::parrafo($textos->id('FECHA_LIMITE_PAGO_PARA_DESCUENTO'));
    $textoDctoExtra1 .= HTML::campoTexto('datos[fecha_limite_dcto_1]', 20, 20, $objeto->fechaLimiteDcto1, 'campoObligatorio fechaReciente campoCalendario', '', '', $textos->id('AYUDA_FECHA_DCTO'));
    $textoDctoExtra1 .= HTML::parrafo($textos->id('PORCENTAJE_DESCUENTO'), 'margenSuperior');
    $textoDctoExtra1 .= HTML::campoTexto('datos[porcentaje_dcto_1]', 5, 5, $objeto->porcentajeDcto1, 'campoPorcentaje campoObligatorio soloNumeros rangoNumeros', '', array('ayuda' => $textos->id('AYUDA_PORCENTAJE_DCTO_1'), 'rango' => '1-100')) . ' %';
    
    $contenedorIzq = HTML::contenedor($textoDctoExtra1, 'contenedorIzquierdo');

    $textoDctoExtra2 = HTML::parrafo($textos->id('FECHA_LIMITE_PAGO_PARA_DESCUENTO'), 'alineadoDerecha margenDerecha');
    $textoDctoExtra2 .= HTML::campoTexto('datos[fecha_limite_dcto_2]', 20, 20, $objeto->fechaLimiteDcto2, 'campoObligatorio campoCalendario fechaReciente alineadoDerecha', '', '', $textos->id('AYUDA_FECHA_DCTO'));
    $textoDctoExtra2 .= HTML::parrafo($textos->id('PORCENTAJE_DESCUENTO'), 'alineadoDerecha margenDerecha margenSuperior');
    $textoDctoExtra2 .= HTML::campoTexto('datos[porcentaje_dcto_2]', 5, 5, $objeto->porcentajeDcto2, 'campoPorcentaje campoObligatorio soloNumeros rangoNumeros alineadoDerecha', '', array('ayuda' => $textos->id('AYUDA_PORCENTAJE_DCTO_1'), 'rango' => '1-100')) . ' %';
    $contenedorDer = HTML::contenedor($textoDctoExtra2, 'contenedorDerecho espacioIzquierda');

    $botonCerrar = HTML::contenedor('X', 'cerrarContenedorDctoExtra');
    
    $linea4 .= HTML::contenedor($contenedorIzq . $contenedorDer . $botonCerrar, 'contenedorDctoExtra oculto');

    


    //aqui se arma el cuadro de dialogo que va a mostrar el formulario para validar los permisos del descuento

    $formaValidarDcto = HTML::campoOculto('datos[dcto_maximo]', $dcto, 'idDcto');
    $formaValidarDcto .= HTML::campoOculto('datos[fila]', $fila, 'idFila');
    $formaValidarDcto .= HTML::parrafo($textos->id('EXPLICACION_AUTORIZACION_DCTO'), 'negrilla subtitulo margensuperior', '');
    $formaValidarDcto .= HTML::parrafo($textos->id('USUARIO'), 'negrilla margenSuperior');
    $formaValidarDcto .= HTML::campoTexto('datos[usuario]', 25, 255, '', '', 'campoValidarDctoUsuario', array());
    $formaValidarDcto .= HTML::parrafo($textos->id('CONTRASENA'), 'negrilla margenSuperior');
    $formaValidarDcto .= HTML::campoClave('datos[contrasena]', 25, 255, '', '', 'campoValidarDctoPassword', array());
    $formaValidarDcto .= HTML::parrafo(HTML::boton('chequeo', $textos->id('ACEPTAR'), 'directo', '', 'btnValidarDcto'), 'margenSuperior');
    $formaValidarDcto .= HTML::parrafo('', 'oculto subtitulo negrilla margenSuperior', 'textoInfoDcto');
   
    $botonCerrar        = HTML::contenedor('X', 'cerrarContenedorValidarDcto');
    
    $linea4 .=  HTML::contenedor($formaValidarDcto.$botonCerrar, 'oculto', 'contenedorValidarDescuento');
    $linea4 .= HTML::imagen($configuracion['SERVIDOR']['media'] . $configuracion['RUTAS']['imagenesEstilos'] . 'eliminar.png', 'imagenEliminarTodosArticulos flotanteDerecha', 'imagenEliminarTodosArticulos', array('ayuda' => 'Eliminar todos los <br>articulos de la lista')+$tutorial["21"]);

    
    $codigo .= HTML::parrafo($linea4, 'linea4');
    
    $linea5 .= HTML::frase($textos->id('OBSERVACIONES'), '');  
    $linea5 .= HTML::campoTexto('datos[observaciones]', 38, 250, $objeto->observaciones, '', 'campoObservaciones', array()+$tutorial["22"]);     
    
    
    $clase = ($regimenEmpresa == "1") ? "oculto" : "";
   
    
    $linea5 .= HTML::frase($textos->id('IVA'), 'margenIzquierdaDoble campoIva '.$clase);
    $linea5 .= HTML::campoTexto('datos[val_iva]', 10, 15, $objeto->iva, 'campoDinero campoDisabledConColor campoIva '.$clase, 'campoIva', array("disabled" => "disabled")+$tutorial["23"]); 
    
    $linea5 .= HTML::campoOculto('datos[iva]', $objeto->iva, 'campoOcultoIva');
    
    
    $linea5 .= HTML::frase($textos->id('TOTAL'), 'margenIzquierdaDoble  margenSuperiorTriple margenInferior masGrande2');
    
    if ($objeto->total == '') {
        $total = '0 ';
        
    } else {
        $total = $objeto->total;
        
    }
    $linea5 .= HTML::frase("<span class='prefijo_numero'>$</span>".Recursos::formatearNumero($total, '$', ''), ' ', 'campoTotal', array()+$tutorial["24"]);
    $linea5 .= HTML::campoOculto('datos[total]', $objeto->total, 'totalFactura');
    
    $linea5 .= HTML::boton('chequeo', $textos->id('FINALIZAR_FACTURA'), 'margenDerecha5PorCien margensuperiorDoble margenInferior25 flotanteDerecha directo', '', 'botonFinalizarFactura', '', array('validar' => 'NoValidar', 'ayuda' => 'Ctrl + Enter')+$tutorial["25"]);

    $codigo .= HTML::parrafo($linea5, 'linea5');

    //$codigo .= HTML::parrafo($linea6, '');

    $codigo = HTML::forma($destino, $codigo, 'P', true);

    $contenido .= HTML::bloque('bloqueContenidoPrincipal', $tituloBloque, $codigo, '', 'overflowVisible');
    
} else {
    $contenido .= HTML::contenedor($textos->id('SIN_PERMISOS_ACCESO_MODULO'), 'textoError');
    
}

Plantilla::$etiquetas['BLOQUE_CENTRAL'] = $contenido;
