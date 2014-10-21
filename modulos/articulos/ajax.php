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
        case 'add'                  :   $datos = ($forma_procesar) ? $forma_datos : array();
                                        adicionarItem($datos);
                                        break;
        
        case 'see'                  :   cosultarItem($forma_id, $forma_pestana);
                                        break;
        
        case 'seeMore'              :   cosultarMasDelItem($forma_id, $forma_destino, $forma_pestana);
                                        break;        
        
        case 'edit'                 :   $datos = ($forma_procesar) ? $forma_datos : array();
                                        modificarItem($forma_id, $datos);
                                        break;
        
        case 'delete'               :   $confirmado = ($forma_procesar) ? true : false;
                                        eliminarItem($forma_id, $confirmado, $forma_dialogo);
                                        break;
        
        case 'search'               :   buscarItem($forma_datos, $forma_cantidadRegistros);
                                        break;
                                    
        case 'searchModal'          :   buscarItemModal($forma_datos, $forma_cantidadRegistros);
                                        break;                                    
        
        case 'move'                 :   paginador($forma_pagina, $forma_orden, $forma_nombreOrden, $forma_consultaGlobal, $forma_cantidadRegistros);
                                        break;
                                    
        case 'moveModal'            :   paginadorModal($forma_pagina, $forma_orden, $forma_nombreOrden, $forma_consultaGlobal, $forma_cantidadRegistros);
                                        break;                                    
        
        case 'moveNew'              :   paginadorNuevo($forma_pagina, $forma_orden, $forma_nombreOrden, $forma_consultaGlobal);
                                        break;
        
        case 'listar'               :   listarItems($url_cadena);
                                        break;
        
        case 'listarArticulosVenta' :   listarArticulosVenta($url_cadena, $url_extra);
                                        break;
        
        case 'listarArticulosCompra' :  listarArticulosCompra($url_cadena, $url_extra);
            break;
        
        case 'verificar'            :   verificarItem($forma_datos);
                                        break;
        
        case 'addMassive'           :   $datos = ($forma_procesar) ? $forma_datos : array();
                                        adicionarMasivo($datos);
                                        break;
        
        case 'eliminarVarios'       :   $confirmado = ($forma_procesar) ? true : false;
                                        eliminarVarios($confirmado, $forma_cantidad, $forma_cadenaItems);
                                        break;
        
        case 'verTodos'             :   cosultarTodos();
                                        break;
        
        case 'moverMercancia'       :   moverMercancia($forma_id, $datos);
                                        break;
        
        case 'accionMoverMercancia' :   accionMoverMercancia($forma_bodegaO, $forma_bodegaD, $forma_cantidad, $forma_idArticulo);
                                        break;
        
        case 'imprimirBarcode'      :   imprimirBarcode($forma_id, $forma_cantidad);
                                        break;
        
        case 'imprimirVariosBarcode' :  $confirmado = ($forma_procesar) ? true : false;
                                        imprimirVariosBarcode($confirmado, $forma_cantidad, $forma_cadenaItems);
                                        break;
        
        case 'consultarKardex'      :   consultarKardex($forma_id, $forma_fechaInicio, $forma_fechaFin, $forma_idBodega);
                                        break;  
        
        case 'datosGrafico'         :   datosGrafico($forma_id, $forma_tipo, $forma_fechaInicio, $forma_fechaFin);
                                        break; 
        
    }
    
}

/**
 * Funcion que muestra la ventana modal de consultar un articulo
 * 
 * @global type $textos
 * @param type $id = id del banco a consultar 
 */
function cosultarItem($id, $pestana = '') {
    global $textos, $sql, $configuracion, $sesion_configuracionGlobal, $sesion_usuarioSesion;

    if (!isset($id) || (isset($id) && !$sql->existeItem('articulos', 'id', $id))) {
        $respuesta              = array();
        $respuesta['error']     = true;
        $respuesta['mensaje']   = $textos->id('NO_HA_SELECCIONADO_ITEM');

        Servidor::enviarJSON($respuesta);
        return NULL;
    }

    //Aqui debo de poner un registro de los ultimos 5 precios a los que  he comprado, a que proveedor y en que fecha
    //hacer un promedio de cuantos vendí al mes, y hacer un estimado de cuantos debo comprar

    $objeto         = new Articulo($id);
    $inventario     = new Inventario($id);
    $aplicacionMoto = new ArticuloMotos();
    $respuesta      = array();
    
    $pestana1   = $pestana2   = $pestana3   = $pestana4   = $pestana5   = HTML::contenedor("", "cargando");
 
    $codigo1  = HTML::parrafo($textos->id('NOMBRE') . ': ' . HTML::frase($objeto->nombre, 'sinNegrilla'), 'negrilla margenSuperior');
    $codigo1 .= HTML::parrafo($textos->id('MOTO') . ': ' . HTML::frase($objeto->moto, 'sinNegrilla'), 'negrilla margenSuperior');
    $codigo1 .= HTML::parrafo($textos->id('GRUPO') . ': ' . HTML::frase($objeto->grupo . ' :: ' . $objeto->subgrupo, 'sinNegrilla'), 'negrilla margenSuperior');

    if($objeto->referencia)         $codigo1 .= HTML::parrafo($textos->id('REFERENCIA') . ': ' .    HTML::frase($objeto->referencia, 'sinNegrilla'), 'negrilla margenSuperior');
    if($objeto->plu_interno)        $codigo1 .= HTML::parrafo($textos->id('PLU_INTERNO') . ': ' .   HTML::frase($objeto->plu_interno, 'sinNegrilla'), 'negrilla margenSuperior');
    if($objeto->unidad)             $codigo1 .= HTML::parrafo($textos->id('PRESENTACION') . ': ' .  HTML::frase($objeto->unidad, 'sinNegrilla'), 'negrilla margenSuperior');
    if($objeto->pais)               $codigo1 .= HTML::parrafo($textos->id('NACIONALIDAD') . ': ' .  HTML::frase($objeto->pais, 'sinNegrilla'), 'negrilla margenSuperior');
    if($objeto->marca)              $codigo1 .= HTML::parrafo($textos->id('MARCA') . ': ' .         HTML::frase($objeto->marca, 'sinNegrilla'), 'negrilla margenSuperior');
    if($objeto->cantidadArticulo)   $codigo1 .= HTML::parrafo($textos->id('CANTIDAD') . ': ' .      $inventario->cantidadArticulo, 'negrilla margenSuperior');
    if($objeto->modelo)             $codigo1 .= HTML::parrafo($textos->id('MODELO') . ': ' .        HTML::frase($objeto->modelo, 'sinNegrilla'), 'negrilla margenSuperior');
    if($objeto->codigo_oem)         $codigo1 .= HTML::parrafo($textos->id('CODIGO_OEM') . ': ' .    HTML::frase($objeto->codigo_oem, 'sinNegrilla'), 'negrilla margenSuperior');

    $imagen     = HTML::enlace(HTML::imagen($objeto->imagenMiniatura, 'imagenItem imagenMiniatura margenIzquierda', ''), $objeto->imagenPrincipal, '', '', array('rel' => 'prettyPhoto[' . $id . ']'));
    $codigo1   .= HTML::parrafo($textos->id('IMAGEN1') . $imagen, 'negrilla margenSuperior');

    $imagen2    = HTML::enlace(HTML::imagen($objeto->imagenMiniatura2, 'imagenItem imagenMiniatura margenIzquierda', ''), $objeto->imagenPrincipal2, '', '', array('rel' => 'prettyPhoto[' . $id . ']'));
    $codigo1   .= HTML::parrafo($textos->id('IMAGEN2') . $imagen2, 'negrilla margenSuperior');

    if($objeto->largo || $objeto->ancho || $objeto->alto) $codigo2 .= HTML::parrafo($textos->id('MEDIDAS'), 'negrilla margenSuperior');
    if($objeto->largo) $codigo2 .= HTML::frase($textos->id('LARGO') . ': ', 'negrilla') . $objeto->largo;
    if($objeto->ancho) $codigo2 .= HTML::frase($textos->id('ANCHO') . ': ', 'negrilla margenIzquierda') . $objeto->ancho;
    if($objeto->alto) $codigo2 .= HTML::frase($textos->id('ALTO') . ': ', 'negrilla margenIzquierda') . $objeto->alto;

    $informacionIva = ($objeto->gravadoIva) ? HTML::frase($textos->id('IVA') . ': ', 'negrilla') . $objeto->iva . '% ' : $textos->id('ARTICULO_NO_GRAVADO');

    $codigo2 .= HTML::parrafo($informacionIva . HTML::frase($textos->id('STOCK_MINIMO') . ': ', 'negrilla margenIzquierda') . $objeto->stockMinimo . ' ' . HTML::frase($textos->id('STOCK_MAXIMO') . ': ', 'negrilla margenIzquierda') . $objeto->stockMaximo, 'margenSuperior');

    if($objeto->aplicacionExtra) $codigo2 .= HTML::parrafo($textos->id('APLICACION_EXTRA') . ': ' . HTML::frase($objeto->aplicacionExtra, 'sinNegrilla'), 'negrilla margenSuperior');

    $activo = ($objeto->activo) ? HTML::frase($textos->id('ACTIVO'), 'activo') : HTML::frase($textos->id('INACTIVO'), 'inactivo');

    $codigo2 .= HTML::parrafo($textos->id('ESTADO') . ': ' . $activo, 'negrilla margenSuperior');

    $codigo2 .= HTML::parrafo($textos->id('MOTOS_APLICABLE'), 'negrilla margenSuperior');
    $codigo2 .= $aplicacionMoto->cargarMotosAplicables($id);
    $codigo2 .= HTML::parrafo($textos->id('PRECIOS'), 'negrilla margenSuperior');

    if (isset($objeto->precio1)) {
        $codigo2 .= HTML::parrafo($objeto->concepto1 . ': ' . HTML::frase('$ '.$objeto->precio1, 'sinNegrilla margenIzquierda'), 'negrilla margenSuperior');
    }
    if (isset($objeto->precio2)) {
        $codigo2 .= HTML::parrafo($objeto->concepto2 . ': ' . HTML::frase('$ '.$objeto->precio2, 'sinNegrilla margenIzquierda'), 'negrilla margenSuperior');
    }
    if (isset($objeto->precio3)) {
        $codigo2 .= HTML::parrafo($objeto->concepto3 . ': ' . HTML::frase('$ '.$objeto->precio3, 'sinNegrilla margenIzquierda'), 'negrilla margenSuperior');
    }
    if (isset($objeto->precio4)) {
        $codigo2 .= HTML::parrafo($objeto->concepto4 . ': ' . HTML::frase('$ '.$objeto->precio4, 'sinNegrilla margenIzquierda'), 'negrilla margenSuperior');
    }

    $contenedor1 = HTML::contenedor($codigo1, 'contenedorIzquierdo');
    $contenedor2 = HTML::contenedor($codigo2, 'contenedorDerecho');

    $pestana1 = HTML::contenedor($contenedor1 . $contenedor2, 'altura450px');   

    $codigo  .= HTML::campoOculto('id', $id, 'idArticuloConsulta');

    $pestanas = array(
        HTML::frase($textos->id('INFORMACION_ARTICULO'), 'letraBlanca')             => $pestana1,
        HTML::frase($textos->id('INFORMACION_INVENTARIO'), 'letraBlanca')           => $pestana2,
        HTML::frase($textos->id('CODIGO_DE_BARRAS'), 'letraBlanca')                 => $pestana3,
        HTML::frase($textos->id('INFORMACION_ECONOMICA_ARTICULO'), 'letraBlanca')   => $pestana4,
        HTML::frase($textos->id('KARDEX'), 'letraBlanca')                           => $pestana5,        
    );
    
    //ids de los titulos de las pestañas
    $idsTitulos = array("info-articulos", "info-existencias", "cod-barras", "info-economica", "kardex");

    $codigo .= HTML::pestanas2('pestanasAgregar', $pestanas, '', 'titulosConsultarArticulo', $idsTitulos);

    $destino1   = '/ajax/articulos/edit';
    $codigo2    = HTML::campoOculto('id', $id, 'ocultoIdArticulo');
    $codigo2   .= HTML::parrafo(HTML::boton('lapiz', $textos->id('MODIFICAR_ITEM'), 'botonOk margenSuperior', 'botonOk', 'botonOk'));
    $codigo    .= HTML::forma($destino1, $codigo2, 'P', '', '');

    $respuesta['generar']       = true;
    $respuesta['cargarJs']      = true;
    $respuesta['archivoJs']     = $configuracion['SERVIDOR']['media'] . $configuracion['RUTAS']['javascript'] . '/modulos/articulos/funcionesConsultarArticulo.js';
    $respuesta['codigo']        = $codigo;
    $respuesta['titulo']        = HTML::parrafo($textos->id('CONSULTAR_ITEM'), 'letraBlanca negrilla subtitulo');
    $respuesta['destino']       = '#cuadroDialogo';
    $respuesta['ancho']         = 750;
    $respuesta['alto']          = 600;



    Servidor::enviarJSON($respuesta);
}

/**
 * Funcion que muestra la ventana modal de consultar un articulo
 * 
 * @global type $textos
 * @param type $id = id del banco a consultar 
 */
function cosultarMasDelItem($id, $destino, $pestana = '') {
    global $textos, $sql, $configuracion, $sesion_configuracionGlobal, $sesion_usuarioSesion;

    if (!isset($id) || (isset($id) && !$sql->existeItem('articulos', 'id', $id))) {
        $respuesta              = array();
        $respuesta['error']     = true;
        $respuesta['mensaje']   = $textos->id('NO_HA_SELECCIONADO_ITEM');

        Servidor::enviarJSON($respuesta);
        return false;
        
    }

    //Aqui debo de poner un registro de los ultimos 5 precios a los que  he comprado, a que proveedor y en que fecha
    //hacer un promedio de cuantos vendí al mes, y hacer un estimado de cuantos debo comprar

    $objeto         = new Articulo($id);
    $inventario     = new Inventario($id);
    $aplicacionMoto = new ArticuloMotos();
    $respuesta      = array();
    
    $pestana1   = $pestana2   = $pestana3   = $pestana4   = $pestana5   = HTML::contenedor("", "cargando");
    
    $codigo = '';
    
    if($pestana == 'kardex'){
        $fechaInicial .= HTML::parrafo($textos->id('FECHA_INICIAL'), 'margenSuperior negrilla');
        $fechaInicial .= HTML::campoTexto("datos[fecha_inicial]", 12, 12, '', "fechaAntigua", "fechaInicioKardex", array("alt" => $textos->id("SELECCIONE_FECHA_INICIAL")));
        $fechaFinal   .= HTML::parrafo($textos->id('FECHA_FINAL'), 'margenSuperior negrilla');
        $fechaFinal   .= HTML::campoTexto("datos[fecha_final]", 12, 12, '', "fechaAntigua", "fechaFinKardex", array("alt" => $textos->id("SELECCIONE_FECHA_FINAL")));

        $listaSedes = array();
        
        $consulta = $sql->seleccionar(array('sedes_empresa'), array('id', 'nombre'), 'id !="0"', '', 'nombre ASC');

        if ($sql->filasDevueltas) {
            while ($dato = $sql->filaEnObjeto($consulta)) {
                $listaSedes[$dato->id] = $dato->nombre;
            }
        }   

        $selectorSedes = HTML::listaDesplegable('datos[sede]', $listaSedes, $sesion_usuarioSesion->sede->id, 'selectChosen', 'selectorSedes', '', array("onchange" => "seleccionarBodegas($(this))"), '');    


        $listaBodegas = array();//arreglo que almacenará el listado de bodegas y será pasado como parametro al metodo HTML::listaDesplegable
        $consulta = $sql->seleccionar(array('bodegas'), array('id', 'nombre'), 'id_sede = "' . $sesion_usuarioSesion->sede->id . '" AND id !="0"', '', 'nombre ASC');//consulto las bodegas de la sede actual del usuario
       
        if ($sql->filasDevueltas) {
            while ($dato = $sql->filaEnObjeto($consulta)) {//recorro las respuesta, y voy guardandolas en el arreglo
                $listaBodegas[$dato->id] = $dato->nombre;
            }
        } 

        $idBodegaPrincipal = $sql->obtenerValor('bodegas', 'id', 'id_sede = "' . $sesion_usuarioSesion->sede->id . '" ANd principal = "1"');    

        $selectorBodega = HTML::listaDesplegable('datos[bodega]', $listaBodegas, $idBodegaPrincipal, 'margenIzquierdaDoble ', 'selectorBodegas', '', array(), '');

        $filtrarPorBodega = HTML::parrafo(HTML::campoChequeo("datos[filtrar_por_bodega]", false, '', 'filtrarKardexBodega', array('onclick' => 'filtrarKardexBodega($(this))'), $textos->id("FILTRAR_POR_BODEGA")), 'negrilla');

        $pestana5  = HTML::contenedorCampos($fechaInicial, $fechaFinal);
        $pestana5 .= HTML::contenedorCampos($filtrarPorBodega, '', 'margensuperior');
        $pestana5 .= HTML::contenedorCampos($selectorSedes, $selectorBodega, 'oculto', 'contenedorSelectorBodegas');
        $pestana5 .= HTML::parrafo(HTML::boton('chequeo', $textos->id('CONSULTAR_KARDEX'), 'directo', '', 'botonConsultarKardexArticulo', '', array('validar' => 'NoValidar', 'id_articulo' => $id, 'onclick' => 'consultarKardex($(this))')), 'margenSuperior');
        $pestana5 .= HTML::contenedor('', 'contenedorKardex');     
        
        $codigo        = $pestana5;
        
    } else if($pestana == 'info-existencias'){
        $cabecera = array(
            HTML::parrafo($textos->id('SEDE'), 'centrado')      => 'sede',
            HTML::parrafo($textos->id('BODEGA'), 'centrado')    => 'bodega',
            HTML::parrafo($textos->id('CANTIDAD'), 'centrado')  => 'cantidad'
        );

        $inventario->cantidadArticuloPorBodega($id);

        $arregloExistencias = array();

        foreach ($inventario->cantidadesArticuloBodega as $inv) {
            unset($inv->articulo);
            $arregloExistencias[] = $inv;
        }

        $idTabla            = 'tablaConsultarInventario';
        $estilosColumnas    = array('', '', '');
        $pestana2           = Recursos::generarTablaRegistrosInterna($arregloExistencias, $cabecera, array(), $idTabla, $estilosColumnas);  
        
        $codigo       = $pestana2;
        
    } else if($pestana == 'cod-barras'){
        $pestana3  = HTML::contenedor('', 'contenedorCodigoBarras', 'contenedorCodigoBarras');

        $datoCodBarra = $sql->obtenerValor('articulos', $sesion_configuracionGlobal->datoCodigoBarra, 'id = "' . $id . '"');

        $pestana3 .= HTML::campoOculto('idCodigoBarras', Recursos::completarCeros($datoCodBarra, 8), 'idCodigoBarras');
        $pestana3 .= HTML::frase('* '.$textos->id('CANTIDAD_A_IMPRIMIR'), 'margenDerecha negrilla subtitulo');
        $pestana3 .= HTML::campoTexto('cantidad_cod_barras', 5, 5, '1', ' ', 'campoCantidadBarcode', array(), $textos->id('AYUDA_CANTIDAD_BARCODE_A_IMPRIMIR'));
        $pestana3 .= HTML::frase($textos->id('CLICK_EN_ICONO'), 'negrilla subtitulo margenIzquierda');
        $pestana3 .= HTML::imagen($configuracion['SERVIDOR']['media'] . $configuracion['RUTAS']['imagenesEstilos'] . '/pdf_icon.png', 'botonImprimirBarcodePdf', 'botonImprimirBarcodePdf', array('ayuda' => $textos->id('AYUDA_IMPRIMIR_CODIGO_BARRAS_PDF'), 'onclick' => 'imprimirBarcode($(this))', 'id_articulo' => $objeto->id));

        $codigo       = $pestana3;
        
    } else if($pestana == 'info-economica'){
        $diasPromedio = $sesion_configuracionGlobal->diasPromedioPonderado;
        
        $pestana4  = HTML::parrafo( HTML::frase($textos->id('ULTIMO_PRECIO_COMPRA'), 'negrilla ').HTML::frase( ($objeto->ultimoPrecioCompra != '') ? '$ '.$objeto->ultimoPrecioCompra : '$ 0', ' margenIzquierdaDoble'), 'margenSuperior');
        $pestana4 .= HTML::parrafo( HTML::frase( str_replace('%1', $diasPromedio, $textos->id('PRECIO_PROMEDIO_COMPRA')), 'negrilla ').HTML::frase(($objeto->precioPromedioCompra != '0') ? '$ '.$objeto->precioPromedioCompra : '$ 0', ' margenIzquierdaDoble') , 'margenSuperior');

        $comprados = HTML::frase($textos->id('COMPRADOS'), 'comprados negrilla');
        $vendidos  = HTML::frase($textos->id('VENDIDOS'), 'vendidos margenIzquierda negrilla');
        
        $pestana4 .= HTML::parrafo($comprados.$vendidos, 'margenSuperior');
        $pestana4 .= HTML::canvas('', 'graficoBarras', 'graficoKardexArticulo', array('width' => '300px', 'height' => '250px', 'data-tipo-grafico' => 'barras'));
        
        $codigo        = $pestana4;
        
    } else {
        $codigo1  = HTML::parrafo($textos->id('NOMBRE') . ': ' . HTML::frase($objeto->nombre, 'sinNegrilla'), 'negrilla margenSuperior');
        $codigo1 .= HTML::parrafo($textos->id('MOTO') . ': ' . HTML::frase($objeto->moto, 'sinNegrilla'), 'negrilla margenSuperior');
        $codigo1 .= HTML::parrafo($textos->id('GRUPO') . ': ' . HTML::frase($objeto->grupo . ' :: ' . $objeto->subgrupo, 'sinNegrilla'), 'negrilla margenSuperior');

        if($objeto->referencia)         $codigo1 .= HTML::parrafo($textos->id('REFERENCIA') . ': ' .    HTML::frase($objeto->referencia, 'sinNegrilla'), 'negrilla margenSuperior');
        if($objeto->plu_interno)        $codigo1 .= HTML::parrafo($textos->id('PLU_INTERNO') . ': ' .   HTML::frase($objeto->plu_interno, 'sinNegrilla'), 'negrilla margenSuperior');
        if($objeto->unidad)             $codigo1 .= HTML::parrafo($textos->id('PRESENTACION') . ': ' .  HTML::frase($objeto->unidad, 'sinNegrilla'), 'negrilla margenSuperior');
        if($objeto->pais)               $codigo1 .= HTML::parrafo($textos->id('NACIONALIDAD') . ': ' .  HTML::frase($objeto->pais, 'sinNegrilla'), 'negrilla margenSuperior');
        if($objeto->marca)              $codigo1 .= HTML::parrafo($textos->id('MARCA') . ': ' .         HTML::frase($objeto->marca, 'sinNegrilla'), 'negrilla margenSuperior');
        if($objeto->cantidadArticulo)   $codigo1 .= HTML::parrafo($textos->id('CANTIDAD') . ': ' .      $inventario->cantidadArticulo, 'negrilla margenSuperior');
        if($objeto->modelo)             $codigo1 .= HTML::parrafo($textos->id('MODELO') . ': ' .        HTML::frase($objeto->modelo, 'sinNegrilla'), 'negrilla margenSuperior');
        if($objeto->codigo_oem)         $codigo1 .= HTML::parrafo($textos->id('CODIGO_OEM') . ': ' .    HTML::frase($objeto->codigo_oem, 'sinNegrilla'), 'negrilla margenSuperior');

        $imagen     = HTML::enlace(HTML::imagen($objeto->imagenMiniatura, 'imagenItem imagenMiniatura margenIzquierda', ''), $objeto->imagenPrincipal, '', '', array('rel' => 'prettyPhoto[' . $id . ']'));
        $codigo1   .= HTML::parrafo($textos->id('IMAGEN1') . $imagen, 'negrilla margenSuperior');

        $imagen2    = HTML::enlace(HTML::imagen($objeto->imagenMiniatura2, 'imagenItem imagenMiniatura margenIzquierda', ''), $objeto->imagenPrincipal2, '', '', array('rel' => 'prettyPhoto[' . $id . ']'));
        $codigo1   .= HTML::parrafo($textos->id('IMAGEN2') . $imagen2, 'negrilla margenSuperior');

        if($objeto->largo || $objeto->ancho || $objeto->alto) $codigo2 .= HTML::parrafo($textos->id('MEDIDAS'), 'negrilla margenSuperior');
        if($objeto->largo) $codigo2 .= HTML::frase($textos->id('LARGO') . ': ', 'negrilla') . $objeto->largo;
        if($objeto->ancho) $codigo2 .= HTML::frase($textos->id('ANCHO') . ': ', 'negrilla margenIzquierda') . $objeto->ancho;
        if($objeto->alto) $codigo2 .= HTML::frase($textos->id('ALTO') . ': ', 'negrilla margenIzquierda') . $objeto->alto;

        $informacionIva = ($objeto->gravadoIva) ? HTML::frase($textos->id('IVA') . ': ', 'negrilla') . $objeto->iva . '% ' : $textos->id('ARTICULO_NO_GRAVADO');

        $codigo2 .= HTML::parrafo($informacionIva . HTML::frase($textos->id('STOCK_MINIMO') . ': ', 'negrilla margenIzquierda') . $objeto->stockMinimo . ' ' . HTML::frase($textos->id('STOCK_MAXIMO') . ': ', 'negrilla margenIzquierda') . $objeto->stockMaximo, 'margenSuperior');

        if($objeto->aplicacionExtra) $codigo2 .= HTML::parrafo($textos->id('APLICACION_EXTRA') . ': ' . HTML::frase($objeto->aplicacionExtra, 'sinNegrilla'), 'negrilla margenSuperior');

        $activo = ($objeto->activo) ? HTML::frase($textos->id('ACTIVO'), 'activo') : HTML::frase($textos->id('INACTIVO'), 'inactivo');

        $codigo2 .= HTML::parrafo($textos->id('ESTADO') . ': ' . $activo, 'negrilla margenSuperior');

        $codigo2 .= HTML::parrafo($textos->id('MOTOS_APLICABLE'), 'negrilla margenSuperior');
        $codigo2 .= $aplicacionMoto->cargarMotosAplicables($id);
        $codigo2 .= HTML::parrafo($textos->id('PRECIOS'), 'negrilla margenSuperior');

        if (isset($objeto->precio1)) {
            $codigo2 .= HTML::parrafo($objeto->concepto1 . ': ' . HTML::frase($objeto->precio1, 'sinNegrilla margenIzquierda') . ' $', 'negrilla margenSuperior');
        }
        if (isset($objeto->precio2)) {
            $codigo2 .= HTML::parrafo($objeto->concepto2 . ': ' . HTML::frase($objeto->precio2, 'sinNegrilla margenIzquierda') . ' $', 'negrilla margenSuperior');
        }
        if (isset($objeto->precio3)) {
            $codigo2 .= HTML::parrafo($objeto->concepto3 . ': ' . HTML::frase($objeto->precio3, 'sinNegrilla margenIzquierda') . ' $', 'negrilla margenSuperior');
        }
        if (isset($objeto->precio4)) {
            $codigo2 .= HTML::parrafo($objeto->concepto4 . ': ' . HTML::frase($objeto->precio4, 'sinNegrilla margenIzquierda') . ' $', 'negrilla margenSuperior');
        }

        $contenedor1 = HTML::contenedor($codigo1, 'contenedorIzquierdo');
        $contenedor2 = HTML::contenedor($codigo2, 'contenedorDerecho');

        $pestana1 = HTML::contenedor($contenedor1 . $contenedor2, 'altura450px');   
        
        $codigo        = $pestana1;
        
    } 

    $respuesta["error"]             = false;
    $respuesta["cargarFunciones"]   = true;
    $respuesta['accion']            = "insertar";
    $respuesta['destino']           = $destino;
    $respuesta['contenido']         = $codigo;

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
function adicionarItem($datos = array()) {
    global $textos, $sql, $archivo_imagen, $archivo_imagen2, $sesion_configuracionGlobal, $configuracion, $modulo, $sesion_usuarioSesion;

     /**
     * Verificar si el usuario que esta en la sesion tiene permisos para esta accion
     */
    $puedeAgregar = Perfil::verificarPermisosAdicion($modulo->nombre);
    
    if(!$puedeAgregar && $sesion_usuarioSesion->id != 0) {
        $respuesta            = array();
        $respuesta['error']   = true;
        $respuesta['mensaje'] = $textos->id('ACCESO_DENEGADO');
        
        Servidor::enviarJSON($respuesta);
        return FALSE;
        
    }

    $objeto         = new Articulo();
    $destino        = '/ajax' . $objeto->urlBase . '/add';
    $respuesta      = array();

    if (empty($datos)) {

        $codigo = HTML::campoOculto('procesar', 'true');
        $codigo .= HTML::campoOculto('datos[dialogo]', '', 'idDialogo');

        $nombre = HTML::parrafo($textos->id('NOMBRE'), 'negrilla margenSuperior');
        $nombre .= HTML::campoTexto('datos[nombre]', 40, 255, '', 'campoObligatorio', 'campoNombreArticulo', '', $textos->id('AYUDA_DESCRIPCION_ARTICULO'));
        $subgrupo .= HTML::parrafo($textos->id('SUBGRUPO'), 'negrilla margenSuperior');
        $subgrupo .= HTML::campoTexto('datos[subgrupo]', 40, 255, '', 'autocompletable campoObligatorio', '', array('title' => HTML::urlInterna('SUBGRUPOS', 0, true, 'listar')), $textos->id('AYUDA_USO_AUTOCOMPLETAR'), HTML::urlInterna('SUBGRUPOS', 0, true, 'add'), 'datos[id_subgrupo]');

        $codigo1 = HTML::contenedorCampos($nombre, $subgrupo);

        $codigo_oem .= HTML::parrafo($textos->id('CODIGO_OEM'), 'negrilla margenSuperior');
        $codigo_oem .= HTML::campoTexto('datos[codigo_oem]', 30, 255, '', '', '', array(), $textos->id('AYUDA_CODIGO_OEM'));
        $referencia .= HTML::parrafo($textos->id('REFERENCIA'), 'negrilla margenSuperior');
        $referencia .= HTML::campoTexto('datos[referencia]', 30, 255, '', '', '', array(), $textos->id('AYUDA_REFERENCIA'));

        $codigo1 .= HTML::contenedorCampos($codigo_oem, $referencia);

        $presentacion .= HTML::parrafo($textos->id('PRESENTACION'), 'negrilla margenSuperior');
        $presentacion .= HTML::campoTexto('datos[unidad]', 40, 255, '', 'autocompletable campoObligatorio', '', array('title' => HTML::urlInterna('UNIDADES', 0, true, 'listar')), $textos->id('AYUDA_USO_AUTOCOMPLETAR'), HTML::urlInterna('UNIDADES', 0, true, 'add'), 'datos[id_unidad]');
        $nacionalidad .= HTML::parrafo($textos->id('NACIONALIDAD'), 'negrilla margenSuperior');
        $nacionalidad .= HTML::campoTexto('datos[pais]', 40, 255, '', 'autocompletable campoObligatorio', '', array('title' => HTML::urlInterna('PAISES', 0, true, 'listar')), $textos->id('AYUDA_USO_AUTOCOMPLETAR'), HTML::urlInterna('PAISES', 0, true, 'add'), 'datos[id_pais]');

        $codigo1 .= HTML::contenedorCampos($presentacion, $nacionalidad);

        $marca = HTML::parrafo($textos->id('MARCA'), 'negrilla margenSuperior');
        $marca .= HTML::campoTexto('datos[marca]', 40, 255, '', 'autocompletable', '', array('title' => HTML::urlInterna('MARCAS', 0, true, 'listar')), $textos->id('AYUDA_USO_AUTOCOMPLETAR'), HTML::urlInterna('MARCAS', 0, true, 'add'), 'datos[id_marca]');

        $modelo = HTML::parrafo($textos->id('MODELO'), 'negrilla margenSuperior');
        $modelo .= HTML::campoTexto('datos[modelo]', 30, 255);

        $codigo1 .= HTML::contenedorCampos($marca, $modelo);

        $imagen = HTML::parrafo($textos->id('IMAGEN1'), 'negrilla margenSuperior');
        $imagen .= HTML::campoArchivo('imagen', 20, 255);
        $imagen2 = HTML::parrafo($textos->id('IMAGEN2'), 'negrilla margenSuperior');
        $imagen2 .= HTML::campoArchivo('imagen2', 20, 255);

        $codigo1 .= HTML::contenedorCampos($imagen, $imagen2);
        $linea = HTML::parrafo($textos->id('LINEA'), 'negrilla margenSuperior');
        $linea .= HTML::campoTexto('datos[linea]', 40, 255, '', 'autocompletable campoObligatorio', '', array('title' => HTML::urlInterna('LINEAS', 0, true, 'listar')), $textos->id('AYUDA_USO_AUTOCOMPLETAR'), HTML::urlInterna('LINEAS', 0, true, 'add'), 'datos[id_linea]');

        $gravado = HTML::frase($textos->id('SI'), '').HTML::campoChequeo('datos[gravado_iva]', true,'margenDerecha','gravado_iva', array('ayuda' => $textos->id('AYUDA_GRAVADO_IVA')));
        $iva = HTML::parrafo($textos->id('GRAVADO_IVA'), 'negrilla margenSuperior');
        $iva .= $gravado.HTML::campoTexto('datos[iva]', 4, 2, $sesion_configuracionGlobal->ivaGeneral, '', '', array(), $textos->id('AYUDA_IVA'));
               
        $codigo1 .= HTML::contenedorCampos($iva, $linea);

        $plu_interno = HTML::parrafo($textos->id('PLU_INTERNO'), 'negrilla margenSuperior');
        $plu_interno .= HTML::campoTexto('datos[plu_interno]', 30, 50);
        
        $stockMinMax = HTML::parrafo(HTML::frase($textos->id('STOCK_MINIMO'), '').HTML::frase($textos->id('STOCK_MAXIMO'), 'margenIzquierdaTriple'), 'negrilla margenSuperior');
        $stockMinMax .= HTML::campoTexto('datos[stock_minimo]', 8, 5, '', '', '', array(), $textos->id('AYUDA_STOCK_MINIMO')).HTML::campoTexto('datos[stock_maximo]', 8, 5, '', 'margenIzquierdaTriple', '', array(), $textos->id('AYUDA_STOCK_MAXIMO'));
       
        $codigo1 .= HTML::contenedorCampos($stockMinMax, $plu_interno);
        
        $pestana1 = HTML::contenedor($codigo1, 'altura400px');

        //**Pestañas 2 de aplicabilidad del articulo, en distintas motos

        $medidas = HTML::parrafo($textos->id('MEDIDAS'), 'negrilla margenSuperior');
        $medidas .= HTML::frase($textos->id('LARGO')) . HTML::campoTexto('datos[largo]', 6, 10) . HTML::frase($textos->id('ANCHO'), 'margenIzquierda') . HTML::campoTexto('datos[ancho]', 6, 10) . HTML::frase($textos->id('ALTO'), 'margenIzquierda') . HTML::campoTexto('datos[alto]', 6, 10);

        $aplicacionExtra = HTML::parrafo($textos->id('APLICACION_EXTRA'), 'negrilla margenSuperior');
        $aplicacionExtra .= HTML::areaTexto('datos[aplicacion_extra]', 2, 42);

        $codigo2 = HTML::contenedorCampos($medidas, $aplicacionExtra);


        $codigo2 .= HTML::parrafo($textos->id('MOTO'), 'negrilla margenSuperior');
        $codigo2 .= HTML::campoTexto('datos[moto]', 40, 255, '', 'autocompletable', 'campoMotosAplicacion', array('title' => HTML::urlInterna('MOTOS', 0, true, 'listarMotos')), $textos->id('AYUDA_AGREGAR_MOTOS_APLICACION'), '/ajax/motos/add');
        $codigo2 .= HTML::contenedor(HTML::lista(array(''), 'listaOrdenable listaVertical ui-sortable', '', 'listaMotosAplicacion'), 'contenedorListaMotos', 'contenedorListaMotos', array());
        $codigo2 .= HTML::ayuda($textos->id('AYUDA_PANEL_MOTOS_APLICACION'), 'ayudaPanelMotosAplicacion');
        $codigo2 .= HTML::campoOculto('datos[listaMotos]', '', 'campoListaMotos');
        $pestana2 = HTML::contenedor($codigo2, 'altura400px');


        $concepto1 = HTML::parrafo($textos->id('CONCEPTO1'), 'negrilla margenSuperior');
        $concepto1 .= HTML::campoTexto('datos[concepto1]', 30, 255, $textos->id('PRECIO_MOSTRADOR'), 'campoObligatorio');

        $precio1 = HTML::parrafo($textos->id('PRECIO1'), 'negrilla margenSuperior');
        $precio1 .= HTML::campoTexto('datos[precio1]', 30, 255, '', 'campoObligatorio');

        $codigo3 .= HTML::contenedorCampos($concepto1, $precio1);

        $concepto2 = HTML::parrafo($textos->id('CONCEPTO2'), 'negrilla margenSuperior');
        $concepto2 .= HTML::campoTexto('datos[concepto2]', 30, 255, $textos->id('PRECIO_MAYOR'), '');

        $precio2 = HTML::parrafo($textos->id('PRECIO2'), 'negrilla margenSuperior');
        $precio2 .= HTML::campoTexto('datos[precio2]', 30, 255, '', '');

        $codigo3 .= HTML::contenedorCampos($concepto2, $precio2);

        $concepto3 = HTML::parrafo($textos->id('CONCEPTO3'), 'negrilla margenSuperior');
        $concepto3 .= HTML::campoTexto('datos[concepto3]', 30, 255);

        $precio3 = HTML::parrafo($textos->id('PRECIO3'), 'negrilla margenSuperior');
        $precio3 .= HTML::campoTexto('datos[precio3]', 30, 255);

        $codigo3 .= HTML::contenedorCampos($concepto3, $precio3);

        $concepto4 = HTML::parrafo($textos->id('CONCEPTO4'), 'negrilla margenSuperior');
        $concepto4 .= HTML::campoTexto('datos[concepto4]', 30, 255);

        $precio4 = HTML::parrafo($textos->id('PRECIO4'), 'negrilla margenSuperior');
        $precio4 .= HTML::campoTexto('datos[precio4]', 30, 255);

        $codigo3 .= HTML::contenedorCampos($concepto4, $precio4);

        $pestana3 = HTML::contenedor($codigo3, 'altura400px');

        $pestanas = array(
            HTML::frase($textos->id('INFORMACION_ARTICULO'), 'letraBlanca')         => $pestana1,
            HTML::frase($textos->id('INFORMACION_APLICABILIDAD'), 'letraBlanca')    => $pestana2,
            HTML::frase($textos->id('INFORMACION_PRECIOS'), 'letraBlanca')          => $pestana3,
        );

        $codigo .= HTML::pestanas2('pestanasAgregar', $pestanas);


        $codigo .= HTML::parrafo(HTML::campoChequeo('datos[activo]', true) . $textos->id('ACTIVO'), 'margenSuperior');
        
        $textoExitoso = HTML::frase($textos->id('REGISTRO_AGREGADO'), 'textoExitoso margenIzquierda', 'textoExitoso');
        
        $codigo .= HTML::parrafo(HTML::boton('chequeo', $textos->id('ACEPTAR'), 'botonOk', 'botonOk', 'botonOk') . $textoExitoso, 'margenSuperior');


        $codigo_f = HTML::forma($destino, $codigo, 'P', true, "formaAdicionarArticulos");

        $respuesta['generar']       = true;
        $respuesta['codigo']        = $codigo_f;
        $respuesta['cargarJs']      = true;
        $respuesta['archivoJs']     = $configuracion['SERVIDOR']['media'] . $configuracion['RUTAS']['javascript'] . '/modulos/articulos/funcionesAdicionarArticulo.js';
        $respuesta['titulo']        = HTML::parrafo($textos->id('ADICIONAR_ITEM'), 'letraBlanca negrilla subtitulo');
        $respuesta['destino']       = '#cuadroDialogo';
        $respuesta['ancho']         = 795;
        $respuesta['alto']          = 580;
        
    } else {

        $respuesta['error'] = true;
        $existeNombre = $sql->existeItem('articulos', 'nombre', $datos['nombre']);

        if (isset($datos['marca'])) {
            $existeMarca = $sql->existeItem('marcas', 'nombre', $datos['marca']);
        }
        
        $existeSubgrupo     = $sql->existeItem('subgrupos', 'nombre', $datos['subgrupo']);
        $existeLinea        = $sql->existeItem('lineas', 'nombre', $datos['linea']);
        $existeUnidad       = $sql->existeItem('unidades', 'nombre', $datos['unidad']);
        $existePais         = $sql->existeItem('paises', 'nombre', $datos['pais']);

        if (!empty($archivo_imagen['tmp_name'])) {
            $validarFormato = Recursos::validarArchivo($archivo_imagen, array('jpg', 'jpeg', 'png', 'gif', 'jpeg'));
        }

        if (!empty($archivo_imagen2['tmp_name'])) {
            $validarFormato2 = Recursos::validarArchivo($archivo_imagen2, array('jpg', 'jpeg', 'png', 'gif', 'jpeg'));
        }    

        if (empty($datos['nombre'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_NOMBRE');
            
        } elseif (empty($datos['subgrupo'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_SUBGRUPO');
            
        } elseif (!empty($datos['subgrupo']) && !$existeSubgrupo) {
            $respuesta['mensaje'] = $textos->id('ERROR_SUBGRUPO_INEXISTENTE');
            
        } elseif (!empty($datos['linea']) && !$existeLinea) {
            $respuesta['mensaje'] = $textos->id('ERROR_LINEA_INEXISTENTE');
            
        }  elseif ($existeNombre) {
            $respuesta['mensaje'] = $textos->id('ERROR_EXISTE_NOMBRE');
            
        } elseif (empty($datos['unidad'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_UNIDAD');
            
        } elseif (!empty($datos['unidad']) && !$existeUnidad) {
            $respuesta['mensaje'] = $textos->id('ERROR_UNIDAD_INEXISTENTE');
            
        } elseif (empty($datos['pais'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_PAIS');
            
        } elseif (!empty($datos['pais']) && !$existePais) {
            $respuesta['mensaje'] = $textos->id('ERROR_PAIS_INEXISTENTE');
            
        } elseif (!empty($datos['marca']) && !$existeMarca) {
            $respuesta['mensaje'] = $textos->id('ERROR_MARCA_INEXISTENTE');
            
        } /*elseif (empty($datos['precio1']) && empty($datos['precio2'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_PRECIO_BASE');
            
        }*/ elseif ($validarFormato || $validarFormato2) {
            $respuesta['mensaje'] = $textos->id('ERROR_FORMATO_IMAGEN');
            
        } else {
            $idItem = $objeto->adicionar($datos);

            if ($idItem) {
                /*                 * ************** Creo el nuevo item que se insertara via ajax *************** */
                $objeto                 = new Articulo($idItem);
                $idPrincipalArticulo    = (string)$sesion_configuracionGlobal->idPrincipalArticulo;
                $indicador_completo     = ($objeto->completo == '1') ? 'activo' : 'inactivo';
                $objeto->completo       = HTML::frase($textos->id('COMPLETO_' . $objeto->completo), $indicador_completo);
                
                if($objeto->referencia){
                    $objeto->nombre = $objeto->nombre.' :: ('.$objeto->referencia.')';
                }                 
                
                $idPrincipal = (int) $objeto->$idPrincipalArticulo;
                
                $arregloContenido   = array("id"                => $idPrincipal, 
                                            "nombre"            => $objeto->nombre, 
                                            "linea"             => $objeto->linea, 
                                            "subgrupo"          => $objeto->subgrupo,
                                            "codigoPais"        => $objeto->codigoPais, 
                                            "precioVenta"       => $objeto->precio1, 
                                            "precioCompra"      => '0', 
                                            "completo"          => $objeto->completo,
                                            );
                
                //si el regimen es diferente al simplificado muestro el iva en los articulos
                if ($sesion_configuracionGlobal->empresa->regimen != "1"){
                    $arregloContenido['iva'] = $objeto->iva;            

                } 

                $respuesta['error']             = false;
                $respuesta['accion']            = 'insertar';
                $respuesta['contenido']         = $arregloContenido;
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
function modificarItem($id, $datos = array()) {
    global $textos, $sql, $configuracion, $archivo_imagen, $archivo_imagen2, $sesion_configuracionGlobal, $modulo, $sesion_usuarioSesion;
    
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
    
    if (empty($id) || (!empty($id) && !$sql->existeItem('articulos', 'id', $id))) {
        $respuesta              = array();
        $respuesta['error']     = true;
        $respuesta['mensaje']   = $textos->id('NO_HA_SELECCIONADO_ITEM');

        Servidor::enviarJSON($respuesta);
        return NULL;
    }     

    $objeto     = new Articulo($id);
    $destino    = '/ajax' . $objeto->urlBase . '/edit';
    $respuesta  = array();

    if (empty($datos)) {

        $codigo  = HTML::campoOculto('procesar', 'true');
        $codigo .= HTML::campoOculto('id', $id);
        $codigo .= HTML::campoOculto('datos[dialogo]', '', 'idDialogo');

        $nombre = HTML::parrafo($textos->id('NOMBRE'), 'negrilla margenSuperior');
        $nombre .= HTML::campoTexto('datos[nombre]', 40, 255, $objeto->nombre, 'campoObligatorio', 'campoNombreArticulo', '', $textos->id('AYUDA_DESCRIPCION_ARTICULO'));
        $codigo_oem .= HTML::parrafo($textos->id('CODIGO_OEM'), 'negrilla margenSuperior');
        $codigo_oem .= HTML::campoTexto('datos[codigo_oem]', 30, 255, $objeto->codigoOem, '', '', array(), $textos->id('AYUDA_CODIGO_OEM'));

        $codigo1 = HTML::contenedorCampos($nombre, $codigo_oem);

        $subgrupo = HTML::parrafo($textos->id('SUBGRUPO'), 'negrilla margenSuperior');
        $subgrupo .= HTML::campoTexto('datos[subgrupo]', 40, 255, $objeto->subgrupo, 'autocompletable campoObligatorio', '', array('title' => HTML::urlInterna('SUBGRUPOS', 0, true, 'listar')), $textos->id('AYUDA_USO_AUTOCOMPLETAR'), HTML::urlInterna('SUBGRUPOS', 0, true, 'add'), 'datos[id_subgrupo]', $objeto->idSubgrupo);
        $referencia .= HTML::parrafo($textos->id('REFERENCIA'), 'negrilla margenSuperior');
        $referencia .= HTML::campoTexto('datos[referencia]', 30, 255, $objeto->referencia, '', '', array(), $textos->id('AYUDA_REFERENCIA'));

        $codigo1 .= HTML::contenedorCampos($subgrupo, $referencia);

        $presentacion = HTML::parrafo($textos->id('PRESENTACION'), 'negrilla margenSuperior');
        $presentacion .= HTML::campoTexto('datos[unidad]', 40, 255, $objeto->unidad, 'autocompletable campoObligatorio', '', array('title' => HTML::urlInterna('UNIDADES', 0, true, 'listar')), $textos->id('AYUDA_USO_AUTOCOMPLETAR'), HTML::urlInterna('UNIDADES', 0, true, 'add'), 'datos[id_unidad]', $objeto->idUnidad);
        $nacionalidad = HTML::parrafo($textos->id('NACIONALIDAD'), 'negrilla margenSuperior');
        $nacionalidad .= HTML::campoTexto('datos[pais]', 40, 255, $objeto->pais, 'autocompletable campoObligatorio', '', array('title' => HTML::urlInterna('PAISES', 0, true, 'listar')), $textos->id('AYUDA_USO_AUTOCOMPLETAR'), HTML::urlInterna('PAISES', 0, true, 'add'), 'datos[id_pais]', $objeto->idPais);

        $codigo1 .= HTML::contenedorCampos($presentacion, $nacionalidad);

        $marca   = HTML::parrafo($textos->id('MARCA'), 'negrilla margenSuperior');
        $marca  .= HTML::campoTexto('datos[marca]', 40, 255, $objeto->marca, 'autocompletable', '', array('title' => HTML::urlInterna('MARCAS', 0, true, 'listar')), $textos->id('AYUDA_USO_AUTOCOMPLETAR'), HTML::urlInterna('MARCAS', 0, true, 'add'), 'datos[id_marca]', $objeto->idMarca);
        $modelo  = HTML::parrafo($textos->id('MODELO'), 'negrilla margenSuperior');
        $modelo .= HTML::campoTexto('datos[modelo]', 30, 255, $objeto->modelo);

        $codigo1 .= HTML::contenedorCampos($marca, $modelo);

        $imagen   = HTML::parrafo($textos->id('IMAGEN1'), 'negrilla margenSuperior');
        $imagen  .= HTML::campoArchivo('imagen', 20, 255) . HTML::enlace(HTML::imagen($objeto->imagenMiniatura, 'imagenMiniaturaEditarArticulo', 'imagenArticulo'), $objeto->imagenPrincipal, '', '', array('rel' => 'prettyPhoto[""]'));
        $imagen2  = HTML::parrafo($textos->id('IMAGEN2'), 'negrilla margenSuperior');
        $imagen2 .= HTML::campoArchivo('imagen2', 20, 255) . HTML::enlace(HTML::imagen($objeto->imagenMiniatura2, 'imagenMiniaturaEditarArticulo', 'imagenArticulo'), $objeto->imagenPrincipal2, '', '', array('rel' => 'prettyPhoto[""]'));
        $codigo1 .= HTML::contenedorCampos($imagen, $imagen2);

        $linea  = HTML::parrafo($textos->id('LINEA'), 'negrilla margenSuperior');
        $linea .= HTML::campoTexto('datos[linea]', 40, 255, $objeto->linea, 'autocompletable campoObligatorio', '', array('title' => HTML::urlInterna('LINEAS', 0, true, 'listar')), $textos->id('AYUDA_USO_AUTOCOMPLETAR'), HTML::urlInterna('LINEAS', 0, true, 'add'), 'datos[id_linea]', $objeto->idLinea);

        $gravado = HTML::frase($textos->id('SI'), '').HTML::campoChequeo('datos[gravado_iva]', $objeto->gravadoIva,'margenDerecha','gravado_iva', array('ayuda' => $textos->id('AYUDA_GRAVADO_IVA')));
        $iva = HTML::parrafo($textos->id('GRAVADO_IVA'), 'negrilla margenSuperior');
        $claseGravado = ($objeto->gravadoIva) ? '' : 'oculto';
        $iva .= $gravado.HTML::campoTexto('datos[iva]', 4, 2, $objeto->iva, ''.$claseGravado, '', array(), $textos->id('AYUDA_IVA'));

        $codigo1 .= HTML::contenedorCampos($iva, $linea);

        $plu_interno = HTML::parrafo($textos->id('PLU_INTERNO'), 'negrilla margenSuperior');
        $plu_interno .= HTML::campoTexto('datos[plu_interno]', 30, 50, $objeto->plu_interno);
        
        $stockMinMax = HTML::parrafo(HTML::frase($textos->id('STOCK_MINIMO'), '').HTML::frase($textos->id('STOCK_MAXIMO'), 'margenIzquierdaTriple'), 'negrilla margenSuperior');
        $stockMinMax .= HTML::campoTexto('datos[stock_minimo]', 8, 5, $objeto->stockMinimo, '', '', array(), $textos->id('AYUDA_STOCK_MINIMO')).HTML::campoTexto('datos[stock_maximo]', 8, 5, $objeto->stockMaximo, 'margenIzquierdaTriple', '', array(), $textos->id('AYUDA_STOCK_MAXIMO'));
       
        $codigo1 .= HTML::contenedorCampos($stockMinMax, $plu_interno);

        $pestana1 = HTML::contenedor($codigo1, 'altura400px');

        //**Pestañas 2 de aplicabilidad del articulo, en distintas motos
        $medidas = HTML::parrafo($textos->id('MEDIDAS'), 'negrilla margenSuperior');
        $medidas .= HTML::frase($textos->id('LARGO')) . HTML::campoTexto('datos[largo]', 6, 10, $objeto->largo) . HTML::frase($textos->id('ANCHO'), 'margenIzquierda') . HTML::campoTexto('datos[ancho]', 6, 10, $objeto->ancho) . HTML::frase($textos->id('ALTO'), 'margenIzquierda') . HTML::campoTexto('datos[alto]', 6, 10, $objeto->alto);

        $aplicacionExtra = HTML::parrafo($textos->id('APLICACION_EXTRA'), 'negrilla margenSuperior');
        $aplicacionExtra .= HTML::areaTexto('datos[aplicacion_extra]', 3, 42, $objeto->aplicacionExtra);

        $codigo2  = HTML::contenedorCampos($medidas, $aplicacionExtra);
        $codigo2 .= HTML::parrafo($textos->id('MOTO'), 'negrilla margenSuperior');
        $codigo2 .= HTML::campoTexto('datos[moto]', 40, 255, '', 'autocompletable', 'campoMotosAplicacion', array('title' => HTML::urlInterna('MOTOS', 0, true, 'listarMotos')), $textos->id('AYUDA_AGREGAR_MOTOS_APLICACION'));
        $codigo2 .= Moto::cargarListaMotosEditar($id, $objeto->idMoto);
        $codigo2 .= HTML::ayuda($textos->id('AYUDA_PANEL_MOTOS_APLICACION'), 'ayudaPanelMotosAplicacion');
        $pestana2 = HTML::contenedor($codigo2, 'altura400px');

        /* Pestañas 3 informacion de precios */
        $concepto1 = HTML::parrafo($textos->id('CONCEPTO1'), 'negrilla margenSuperior');
        $concepto1 .= HTML::campoTexto('datos[concepto1]', 30, 255, $objeto->concepto1, 'campoObligatorio');

        $precio1 = HTML::parrafo($textos->id('PRECIO1'), 'negrilla margenSuperior');
        $precio1 .= HTML::campoTexto('datos[precio1]', 30, 255, $objeto->precio1, 'campoObligatorio');

        $codigo3 = HTML::contenedorCampos($concepto1, $precio1);

        $concepto2 = HTML::parrafo($textos->id('CONCEPTO2'), 'negrilla margenSuperior');
        $concepto2 .= HTML::campoTexto('datos[concepto2]', 30, 255, $objeto->concepto2);

        $precio2 = HTML::parrafo($textos->id('PRECIO2'), 'negrilla margenSuperior');
        $precio2 .= HTML::campoTexto('datos[precio2]', 30, 255, $objeto->precio2);

        $codigo3 .= HTML::contenedorCampos($concepto2, $precio2);

        $concepto3 = HTML::parrafo($textos->id('CONCEPTO3'), 'negrilla margenSuperior');
        $concepto3 .= HTML::campoTexto('datos[concepto3]', 30, 255, $objeto->concepto3);

        $precio3 = HTML::parrafo($textos->id('PRECIO3'), 'negrilla margenSuperior');
        $precio3 .= HTML::campoTexto('datos[precio3]', 30, 255, $objeto->precio3);

        $codigo3 .= HTML::contenedorCampos($concepto3, $precio3);

        $concepto4 = HTML::parrafo($textos->id('CONCEPTO4'), 'negrilla margenSuperior');
        $concepto4 .= HTML::campoTexto('datos[concepto4]', 30, 255, $objeto->concepto4);

        $precio4 = HTML::parrafo($textos->id('PRECIO4'), 'negrilla margenSuperior');
        $precio4 .= HTML::campoTexto('datos[precio4]', 30, 255, $objeto->precio4);

        $codigo3 .= HTML::contenedorCampos($concepto4, $precio4);

        $pestana3 = HTML::contenedor($codigo3, 'altura400px');

        $pestanas = array(
            HTML::frase($textos->id('INFORMACION_ARTICULO'), 'letraBlanca')         => $pestana1,
            HTML::frase($textos->id('INFORMACION_APLICABILIDAD'), 'letraBlanca')    => $pestana2,
            HTML::frase($textos->id('INFORMACION_PRECIOS'), 'letraBlanca')          => $pestana3,
        );

        $codigo .= HTML::pestanas2('pestanasModificar', $pestanas);

        $codigo .= HTML::parrafo(HTML::campoChequeo('datos[activo]', $objeto->activo) . $textos->id('ACTIVO'), 'margenSuperior');
        $textoExitoso = HTML::frase($textos->id('REGISTRO_MODIFICADO'), 'textoExitoso margenIzquierda', 'textoExitoso');
        $codigo .= HTML::parrafo(HTML::boton('chequeo', $textos->id('ACEPTAR'), 'botonOk', 'botonOk', 'botonOk') . $textoExitoso, 'margenSuperior');

        $codigof = HTML::forma($destino, $codigo, 'P', true);

        $respuesta['generar']       = true;
        $respuesta['codigo']        = $codigof;
        $respuesta['cargarJs']      = true;
        $respuesta['archivoJs']     = $configuracion['SERVIDOR']['media'] . $configuracion['RUTAS']['javascript'] . '/modulos/articulos/funcionesModificarArticulo.js';
        $respuesta['titulo']        = HTML::parrafo($textos->id('MODIFICAR_ITEM'), 'letraBlanca negrilla subtitulo');
        $respuesta['destino']       = '#cuadroDialogo';
        $respuesta['ancho']         = 795;
        $respuesta['alto']          = 595;
        
    } else {
        $respuesta['error'] = true;

        $existeNombre = $sql->existeItem('articulos', 'nombre', $datos['nombre'], 'id != "' . $id . '"');

        if (isset($datos['marca'])) {
            $existeMarca = $sql->existeItem('marcas', 'nombre', $datos['marca']);
            
        }
        
        $existeLinea        = $sql->existeItem('lineas', 'nombre', $datos['linea']);
        $existeSubgrupo     = $sql->existeItem('subgrupos', 'nombre', $datos['subgrupo']);
        $existeUnidad       = $sql->existeItem('unidades', 'nombre', $datos['unidad']);
        $existePais         = $sql->existeItem('paises', 'nombre', $datos['pais']);

        $validarFormato = '';
        
        if (!empty($archivo_imagen['tmp_name'])) {
            $validarFormato = Recursos::validarArchivo($archivo_imagen, array('jpg', 'jpeg', 'png', 'gif', 'jpeg'));
        }
        
        if (!empty($archivo_imagen2['tmp_name'])) {
            $validarFormato2 = Recursos::validarArchivo($archivo_imagen2, array('jpg', 'jpeg', 'png', 'gif', 'jpeg'));
        }

        if (empty($datos['subgrupo'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_SUBGRUPO');
            
        } elseif (!empty($datos['subgrupo']) && !$existeSubgrupo) {
            $respuesta['mensaje'] = $textos->id('ERROR_SUBGRUPO_INEXISTENTE');
            
        } elseif (!empty($datos['linea']) && !$existeLinea) {
            $respuesta['mensaje'] = $textos->id('ERROR_LINEA_INEXISTENTE');
            
        } elseif (empty($datos['nombre'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_NOMBRE');
            
        } elseif ($existeNombre) {
            $respuesta['mensaje'] = $textos->id('ERROR_EXISTE_NOMBRE');
            
        } elseif (empty($datos['unidad'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_UNIDAD');
            
        } elseif (!$existeUnidad) {
            $respuesta['mensaje'] = $textos->id('ERROR_UNIDAD_INEXISTENTE');
            
        } elseif (empty($datos['pais'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_PAIS');
            
        } elseif (!empty($datos['pais']) && !$existePais) {
            $respuesta['mensaje'] = $textos->id('ERROR_PAIS_INEXISTENTE');
            
        } elseif (!empty($datos['marca']) && !$existeMarca) {
            $respuesta['mensaje'] = $textos->id('ERROR_MARCA_INEXISTENTE');
            
        } elseif (empty($datos['precio1']) && empty($datos['precio2'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_PRECIO_BASE');
            
        } elseif ($validarFormato || $validarFormato2) {
            $respuesta['mensaje'] = $textos->id('ERROR_FORMATO_IMAGEN');
            
        } else {

            $idItem = $objeto->modificar($datos);

            if ($idItem) {
                /*                 * ************** Creo el nuevo item que se insertara via ajax *************** */
                $objeto                 = new Articulo($idItem);
                $idPrincipalArticulo    = (string)$sesion_configuracionGlobal->idPrincipalArticulo;
                $indicador_completo     = ($objeto->completo == '1') ? 'activo' : 'inactivo';
                $objeto->completo       = HTML::frase($textos->id('COMPLETO_' . $objeto->completo), $indicador_completo);
                
                if($objeto->referencia){
                    $objeto->nombre = $objeto->nombre.' :: ('.$objeto->referencia.')';
                }               

                $celdas = array((int) $objeto->$idPrincipalArticulo, $objeto->nombre, $objeto->linea, $objeto->subgrupo, $objeto->codigoPais, '$ '.$objeto->precio1, $objeto->iva, '$ '.$objeto->ultimoPrecioCompra, $objeto->completo);

                $celdas1 = HTML::crearFilaAModificar($celdas);

                $respuesta['error']         = false;
                $respuesta['accion']        = 'insertar';
                $respuesta['contenido']     = $celdas1;
                $respuesta['idContenedor']  = '#tr_' . $id;
                $respuesta['idDestino']     = '#tr_' . $id;

                if ($datos['dialogo'] == '') {
                    $respuesta['modificarFilaTabla'] = true;
                    
                } else {
                    $respuesta['modificarFilaDialogo'] = true;
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
    global $textos, $modulo, $sesion_usuarioSesion;
    
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

    $objeto         = new Articulo($id);
    $destino        = '/ajax' . $objeto->urlBase . '/delete';
    $respuesta      = array();

    if (!$confirmado) {
        $titulo     = HTML::frase($objeto->nombre, 'negrilla');
        $titulo_f   = str_replace('%1', $titulo, $textos->id('CONFIRMAR_ELIMINACION'));
        $codigo     = HTML::campoOculto('procesar', 'true');
        $codigo    .= HTML::campoOculto('id', (int)$id);
        $codigo    .= HTML::campoOculto('datos[dialogo]', '', 'idDialogo');
        $codigo    .= HTML::parrafo($titulo_f);
        $codigo    .= HTML::parrafo(HTML::boton('chequeo', $textos->id('ACEPTAR'), '', 'botonOk', 'botonOk'), 'margenSuperior');
        $codigo    .= HTML::parrafo($textos->id('REGISTRO_ELIMINADO'), 'textoExitoso', 'textoExitoso');
        $codigo1    = HTML::forma($destino, $codigo);

        $respuesta['generar']       = true;
        $respuesta['codigo']        = $codigo1;
        $respuesta['destino']       = '#cuadroDialogo';
        $respuesta['titulo']        = HTML::parrafo($textos->id('ELIMINAR_ITEM'), 'letraBlanca negrilla subtitulo');
        $respuesta['ancho']         = 350;
        $respuesta['alto']          = 150;
        
 } else {
                
        $respuesta['error']     = true;
        $respuestaEliminar = $objeto->eliminar();
        
        if ($respuestaEliminar['respuesta']) {

                $respuesta['error']     = false;
                $respuesta['accion']    = 'insertar';
                $respuesta['idDestino'] = '#tr_' . $id;            

            if ($dialogo == '') {
                $respuesta['eliminarFilaTabla'] = true;

            } else {
                $respuesta['eliminarFilaDialogo'] = true;
                $respuesta['ventanaDialogo'] = $dialogo;

            }
        } else {
            $respuesta['mensaje'] = $respuestaEliminar['mensaje'];

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
        $item               = '';
        $respuesta          = array();
        $objeto             = new Articulo();
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
function buscarItemModal($data, $cantidadRegistros = NULL) {
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
        $objeto             = new Articulo();
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
            $item .= $objeto->generarTablaModal($arregloItems, $datosPaginacion);
            $info = HTML::parrafo(str_replace('%1', $objeto->registrosConsulta, $textos->id('RESULTADOS_BUSQUEDA')), 'textoExitosoNotificaciones');
            
        } else {
            $datosPaginacion = 0;
            $item .= $objeto->generarTablaModal($textos->id('NO_HAY_REGISTROS'), $datosPaginacion);
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
    $objeto         = new Articulo();


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
function paginadorModal($pagina, $orden = NULL, $nombreOrden = NULL, $consultaGlobal = NULL, $cantidadRegistros = NULL) {
    global $configuracion;

    $item           = '';
    $respuesta      = array();
    $objeto         = new Articulo();


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
        $item .= $objeto->generarTablaModal($arregloItems, $datosPaginacion, true, false);
    }

    $respuesta['error']                 = false;
    $respuesta['accion']                = 'insertar';
    $respuesta['contenido']             = $item;
    $respuesta['idContenedor']          = '.ui-dialog #tablaRegistros';
    $respuesta['idDestino']             = '.ui-dialog #contenedorTablaRegistros';
    $respuesta['paginarTabla']          = true;

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
//function paginadorNuevo($pagina, $orden = NULL, $nombreOrden = NULL, $consultaGlobal = NULL) {
//    global $configuracion;
//
//    $item           = '';
//    $respuesta      = array();
//    $objeto         = new Articulo();
//
//    $registros = $configuracion['GENERAL']['registrosPorPagina'];
//
//    if (isset($pagina)) {
//        $pagina = $pagina;
//    } else {
//        $pagina = 1;
//    }
//
//    if (isset($consultaGlobal) && $consultaGlobal != '') {
//        $data       = explode('[', $consultaGlobal);
//        $datos      = $data[0];
//        $palabras   = explode(' ', $datos);
//
//        if ($data[1] != '') {
//            $condicionales = explode('|', $data[1]);
//
//            $condicion = '(';
//            $tam = sizeof($condicionales) - 1;
//            
//            for ($i = 0; $i < $tam; $i++) {
//                $condicion .= $condicionales[$i] . ' REGEXP "(' . implode('|', $palabras) . ')" ';
//                if ($i != $tam - 1) {
//                    $condicion .= ' OR ';
//                }
//            }
//            $condicion .= ')';
//
//            $consultaGlobal = $condicion;
//            
//        } else {
//            $consultaGlobal = '(a.nombre REGEXP "(' . implode('|', $palabras) . ')")';
//            
//        }
//        
//    } else {
//        $consultaGlobal = '';
//        
//    }
//
//    if (!isset($nombreOrden)) {
//        $nombreOrden = $objeto->ordenInicial;
//    }
//
//
//    if (isset($orden) && $orden == 'ascendente') {//ordenamiento
//        $objeto->listaAscendente = true;
//    } else {
//        $objeto->listaAscendente = false;
//    }
//
//    if (isset($nombreOrden) && $nombreOrden == 'estado') {//ordenamiento
//        $nombreOrden = 'activo';
//    }
//
//    $registroInicial = ($pagina - 1) * $registros;
//
//
//    $arregloItems = $objeto->listar($registroInicial, $registros, array('0'), $consultaGlobal, $nombreOrden);
//
//    if ($objeto->registrosConsulta) {//si la consulta trajo registros
//        $datosPaginacion = array($objeto->registrosConsulta, $registroInicial, $registros, $pagina);
//        $item .= $objeto->generarTablaReducida($arregloItems, $datosPaginacion);
//    }
//
//    $respuesta['error']             = false;
//    $respuesta['accion']            = 'insertar';
//    $respuesta['contenido']         = $item;
//    $respuesta['idContenedor']      = '#tablaRegistros';
//    $respuesta['idDestino']         = '#contenedorTablaRegistros';
//    $respuesta['paginarTabla']      = true;
//
//    Servidor::enviarJSON($respuesta);
//}

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

/**
 * Funcion que devuelve la respuesta para el autocompletar desde el modulo de venta de mercancia
 * 
 * @global recurso $sql = objeto global de interacción con la BD
 * @global object $sesion_configuracionGlobal objeto almacenado en una variable de sesion que contiene toda la información acerca de la
 *         configuración del sistema
 * @param string $cadena cadena introducida por el udsuario para realizar la busqueda del autocompletable
 * @param string $bodega identificador unico de la bodega, por el transporte a traves del formulario llega como cadena, pero es un entero 
 * 
 * @return array de arrays. cada uno de estos array contiene información particular acerca del articulo
 */
function listarArticulosVenta($cadena, $bodega) {
    global $sql, $sesion_configuracionGlobal;
    
    $respuesta = array();//declaracion del arreglo que contendra cada uno de los arreglos con la info. de cada articulo

    $idPrincipalArticulo = (string)$sesion_configuracionGlobal->idPrincipalArticulo;
    
    if (ctype_digit($cadena)) { //verifico si la cadena que llega es un numero    
        $consulta = $sql->seleccionar(array('articulos'), array('id', 'nombre', 'precio1', 'iva', $idPrincipalArticulo), '('.$idPrincipalArticulo.' LIKE "' . $cadena . '%" OR referencia LIKE "' . $cadena . '%") ', '', 'nombre ASC', 0, 150);
    
    } else { //si no es un numero, busco por nombre y referencia
        //$consulta = $sql->seleccionar(array('articulos'), array('id', 'nombre', 'precio1', 'iva', $idPrincipalArticulo), '(nombre LIKE "%' . $cadena . '" OR referencia LIKE "' . $cadena . '%")  ', '', 'nombre ASC', 0, 150);
        $consulta = $sql->seleccionar(array('articulos'), array('id', 'nombre', 'precio1', 'iva', $idPrincipalArticulo), '(replace(nombre, " ", "") LIKE "' . $cadena . '%" OR referencia LIKE "' . $cadena . '%")  ', '', 'nombre ASC', 0, 150);
     
    }
    
    //declaro las tablas y las columnas de la consulta al inventario
    $tablas1 = array('i'  => 'inventarios',
                    'i1'    => 'inventarios'
                    );
    $columnas1 = array(
        'cantidadBodega'  => 'i.cantidad',
        'cantidadTotal'   => 'SUM(i1.cantidad)',
    );      

    while ($fila = $sql->filaEnObjeto($consulta)) { //recorro la respuesta de la BD y voy armando el arreglo de arreglos con la respuesta que se va a enviar a la vista
        $respuesta1 = array();//declaro el arreglo que contendrá la respuesta de u articulo particular, se reinicia con cada ciclo
        
        $respuesta1["id"]   = (int)$fila->id;
        $respuesta1["iva"]  = (int)$fila->iva;

        if (strlen($fila->nombre) > 90) { //si el nombre es muy largo lo recorto
            $fila->nombre = substr($fila->nombre, 0, 88) . '..';
        }
        
        //condicion de la consulta a inventarios
        $condicion1 = ' i1.id_articulo = "' . $fila->id . '" AND i.id_articulo = "' . $fila->id . '" AND i.id_bodega = "' . $bodega . '"';
        //consulta inventarios
        $consulta1  = $sql->seleccionar($tablas1, $columnas1, $condicion1);
        
        $fila1              = $sql->filaEnObjeto($consulta1);
        $cantidadExistencia = $fila1->cantidadBodega;
        $totalExistencias   = $fila1->cantidadTotal;

        $respuesta1['label'] = (int) $fila->$idPrincipalArticulo.' :: '.$fila->nombre;//el nombre del articulo y se le concatena el idPrincipal del Articulo
        $respuesta1['value'] = $fila->precio1;//lleva el precio de venta al publico
        
        if ($cantidadExistencia == '') {
            $cantidadExistencia = '0 ';
        }
        
        $respuesta1['cant'] = $cantidadExistencia;
        
        if ($totalExistencias == '') {
            $totalExistencias = '0 ';
            
        }
        
        $respuesta1['cant_total'] = $totalExistencias;           

        $respuesta[] = $respuesta1;//voy guardando cada uno de los arreglos con la info del articulo en el arreglo de respuesta general
    }

    Servidor::enviarJSON($respuesta);
    
}

/**
 * Funcion que devuelve la respuesta para el autocompletar desde el modulo de compra de mercancia
 * 
 * @global recurso $sql objeto global de interaccion con la BD
 * @global object $sesion_configuracionGlobal objeto almacenado en una variable de sesion que contiene toda la información acerca de la
 *         configuración del sistema
 * @param string $cadena cadena introducida por el udsuario para realizar la busqueda del autocompletable
 * @param string $bodega identificador unico de la bodega, por el transporte a traves del formulario llega como cadena, pero es un entero 
 * 
 * @return array de arrays. cada uno de estos array contiene información particular acerca del articulo
 */
function listarArticulosCompra($cadena, $bodega) {
    global $sql, $sesion_configuracionGlobal;
    //La documentación de esta funcion es igual a la de arriba que lista los articulos de venta
    $respuesta  = array();
    
    $cadena     = str_replace(" ", "", $cadena);
    
    $idPrincipalArticulo = (string)$sesion_configuracionGlobal->idPrincipalArticulo;

    $tablas = array('a' => 'articulos'
    );
    $columnas = array(
        'id'            => 'a.id',
        'nombre'        => 'a.nombre',
        'precio1'       => 'a.ultimo_precio_compra',
        'idArticulo'    => $idPrincipalArticulo,
        'iva'           => 'a.iva'
    );

    
    if (ctype_digit($cadena)) {
        $consulta = $sql->seleccionar($tablas, $columnas, '('.$idPrincipalArticulo.' LIKE "' . $cadena . '%" OR referencia LIKE "' . $cadena . '%")  ', '', 'nombre ASC', 0, 150);
        
    } else {
        $consulta = $sql->seleccionar($tablas, $columnas, '(replace(nombre, " ", "") LIKE "' . $cadena . '%" OR referencia LIKE "' . $cadena . '%")  ', '', 'nombre ASC', 0, 150);
        
    }

    //declaro las tablas y las columnas de la consulta al inventario
    $tablas1 = array('i'  => 'inventarios',
                    'i1' => 'inventarios'
                );
    $columnas1 = array(
        'cantidadBodega'  => 'i.cantidad',
        'cantidadTotal'   => 'SUM(i1.cantidad)',
    );      

    while ($fila = $sql->filaEnObjeto($consulta)) {
        $respuesta1         = array();
        $respuesta1['id']   = (int)$fila->id;
        $respuesta1['iva']  = (int)$fila->iva;

        if (strlen($fila->nombre) > 90) {
            $fila->nombre = substr($fila->nombre, 0, 88) . '..';
        }

        //condicion de la consulta a inventarios
        $condicion1 = ' i1.id_articulo = "' . $fila->id . '" AND i.id_articulo = "' . $fila->id . '" AND i.id_bodega = "' . $bodega . '"';
        //consulta inventarios
        $consulta1 = $sql->seleccionar($tablas1, $columnas1, $condicion1);

        $fila1              = $sql->filaEnObjeto($consulta1);
        $cantidadExistencia = $fila1->cantidadBodega;
        $totalExistencias   = $fila1->cantidadTotal;


        $respuesta1['label'] = (int) $fila->idArticulo.' :: '.$fila->nombre;
        $respuesta1['value'] = ($fila->precio1) ? $fila->precio1 : '0';
        
        if ($cantidadExistencia == '') {
            $cantidadExistencia = '0 ';
        }
        
        $respuesta1['cant'] = $cantidadExistencia;
        
        if ($totalExistencias == '') {
            $totalExistencias = '0 ';
        }
        $respuesta1['cant_total'] = $totalExistencias;       

        $respuesta[] = $respuesta1;
    }

    Servidor::enviarJSON($respuesta);
    
}

/**
 * Funcion que devuelve una respuesta para verificar la existencia de un item via ajax
 * 
 * @global type $sql
 * @param type $cadena 
 */
function verificarItem($cadena) {
    global $sql;
    
    $respuesta = array();
    $consulta = $sql->existeItem('articulos', 'nombre', $cadena);

    $respuesta['verificaExistenciaArticulo'] = true; //determina que lo que se consulta es la existencia del item
    $respuesta['consultaExistenciaArticulo'] = $consulta; //determina si se encontro o no el item

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
    global $textos, $modulo, $sesion_usuarioSesion;
    
     /**
     * Verificar si el usuario que esta en la sesion tiene permisos para esta accion
     */
    $puedeEliminarMasivo = Perfil::verificarPermisosBoton('botonEliminarMasivoArticulos', $modulo->nombre);
    
    if(!$puedeEliminarMasivo && $sesion_usuarioSesion->id != 0) {
        $respuesta            = array();
        $respuesta['error']   = true;
        $respuesta['mensaje'] = $textos->id('ACCESO_DENEGADO');
        
        Servidor::enviarJSON($respuesta);
        return FALSE;
        
    }
    
    $destino = '/ajax/articulos/eliminarVarios';
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
        
        /**
         * arreglo que va a contener la respuesta a enviar al javascript, contendra las siguientes posiciones
         * -numero de items eliminados7
         * -numero de items que no se pudieron eliminar
         * -nombre(s) de los items que no se pudieron eliminar 
         */
        $arregloRespuesta = array(
            'items_eliminados'          => 0,
            'items_no_eliminados'       => 0,
            'lista_items_no_eliminados' => array(),
        );

        
        foreach ($arregloIds as $val) {
            $objeto = new Articulo($val);
            $eliminarVarios = $objeto->eliminar();
            
            if ($eliminarVarios['respuesta']) {
                $arregloRespuesta['items_eliminados']++;
                
            } else {
                $arregloRespuesta['items_no_eliminados']++;
                $arregloRespuesta['lista_items_no_eliminados'][] = $objeto->nombre;
            }
            
        }

        if ($arregloRespuesta['items_eliminados']) {
            //por defecto asumimos que se pudieron eliminar todos los items
            $mensajeEliminarVarios = $textos->id('ITEMS_ELIMINADOS_CORRECTAMENTE');
            //por eso enviamos texto exito como "true" para que muestre el "chulo verde" en la alerta
            $respuesta['textoExito']   = true;
            //Aqui verificamos si hubo algun item que no se pudo eliminar
            if ($arregloRespuesta['items_no_eliminados']) {
                $respuesta['textoExito']   = false;//para que muestre el signo de admiracion o advertencia
                
                /**
                 * reemplazo los valores de lo sucedido en la cadena a ser mostrada en la alerta
                 */
                $mensajeEliminarVarios     = str_replace('%1', $arregloRespuesta['items_eliminados'], $textos->id('ELIMINAR_VARIOS_EXITOSO_Y_FALLIDO'));//modificamos el texto
                $mensajeEliminarVarios     = str_replace('%2', $arregloRespuesta['items_no_eliminados'], $mensajeEliminarVarios);
                $mensajeEliminarVarios     = str_replace('%3', implode(', ', $arregloRespuesta['lista_items_no_eliminados']), $mensajeEliminarVarios);
            }
            
            $respuesta['error']         = false;

            $respuesta['mensaje']       = $mensajeEliminarVarios;
            $respuesta['accion']        = 'recargar';
            
        } else {
            $respuesta['mensaje'] = $textos->id('NINGUN_ITEM_ELIMINADO');
            
        }
    }

    Servidor::enviarJSON($respuesta);
}
/**
 * Función que se encarga de mostrar la tabla de los articulos en la ventana modal
 * ademas muestra un boton que es el que permite agregar a las facturas los articulos
 * seleccionados, los articulos se pueden seleccionar haciendoles click encima, o marcando
 * la casilla "seleccionar todos". Esta tabla se muestra en los modulos compras/ventas mercancia
 * 
 * @global objeto $modulo objeto que representa al modulo actual
 * @global arreglo $configuracion arreglo con todos los datos de configuracion
 * @global objeto $textos objeto global que maneja los textos que seran reemplazados
 */
function cosultarTodos() {
    global  $modulo, $configuracion, $textos;

    $objeto         = new Articulo(); /* creacion del objeto */
    $excluidas      = array('0'); //items excluidos en la consulta
    $item           = '';
    $contenido      = '';
    $contenido     .= HTML::contenedor(HTML::contenedor($textos->id('AYUDA_MODULO'), 'ui-corner-all'), 'ui-widget-shadow ui-corner-all oculto', 'contenedorAyudaUsuario');
    

    //campo oculto del cual el javascript sacara el nombre del modulo actual ->para??
    $item .= HTML::campoOculto('nombreModulo', ucwords(strtolower($modulo->nombre)), 'nombreModulo');

    $item .= HTML::campoOculto('orden' . ucwords(strtolower($modulo->nombre)), 'ascendente|' . $objeto->ordenInicial, 'ordenGlobal');
    $item .= HTML::campoOculto('condicion' . ucwords(strtolower($modulo->nombre)), '', 'condicionGlobal');

    /* Datos para la paginacion */
    $registros          = $configuracion['GENERAL']['registrosPorPagina'];
    $pagina             = 1;
    $registroInicial    = 0;

    /* campo de texto para seleccionar cuantos registros traer en la consulta */
    $campoNumRegistros  = '';
    $campoNumRegistros .= HTML::frase($textos->id('NUMERO_FILAS'), 'margenIzquierdaDoble medioMargenDerecha');
    $campoNumRegistros .= HTML::campoTexto('cantidad_registros', 5, 5, $registros . ' ', 'soloNumerosEnter', 'campoNumeroRegistros', array('ruta' => '/ajax/articulos/moveModal'), $textos->id('AYUDA_SELECCIONAR_CANTIDAD_REGISTROS'));

    $rutaImagen = $configuracion['SERVIDOR']['media'] . $configuracion['RUTAS']['imagenesEstilos'] . 'adicionar.png';
    $botonAgregarArticulos = HTML::imagen($rutaImagen, 'imagenAdicionarVariosArticulos', 'imagenAdicionarVariosArticulos', array('ayuda' => $textos->id('ADICIONAR_ARTICULOS_FACTURA')));


    /* Checkbox que se encarga de marcar todas las filas de la tabla */
    $chkMarcarFilas  = '';
    $chkMarcarFilas .= HTML::campoChequeo('chkMarcarFilas', false, 'chkMarcarFilas', 'chkMarcarFilas', array('ayuda' => $textos->id('AYUDA_MARCAR_FILAS')));
    $chkMarcarFilas .= HTML::frase($textos->id('MARCAR_FILAS'), 'alineadoIzquierda medioMargenIzquierda negrilla');
    
    
    /* Boton que carga la ventana modal para realizar la busqueda */
    $destino            = HTML::urlInterna($modulo->nombre, 0, true, 'search');
    $botonRestaurar     = HTML::contenedor('', 'botonRestaurarConsulta', 'botonRestaurarConsulta', array('alt' => HTML::urlInterna($modulo->nombre, 0, true, 'moveModal'), 'ayuda' => $textos->id('RESTAURAR_CONSULTA')));
    $botonBuscador      = HTML::contenedor('', 'botonBuscador', 'botonBuscador', array('alt' => HTML::urlInterna($modulo->nombre, 0, true, 'searchModal'), 'title' => $textos->id('BUSCAR_ITEM')));
    $buscador           = HTML::campoTexto('datos[patron]', 22, '', '', 'campoBuscador margenIzquierdaDoble', 'campoBuscador') . $botonRestaurar . $botonBuscador;
    $buscador           = HTML::forma($destino, $buscador);
    $buscador           = HTML::contenedor($buscador, 'flotanteDerecha', 'botonBuscar' . ucwords(strtolower($modulo->nombre)) . '');
    $botonesSuperiores  = HTML::contenedor($buscador . $botonAgregarArticulos . $chkMarcarFilas . $campoNumRegistros , '', 'botonesSuperioresModulo');

    $arregloItems = $objeto->listar($registroInicial, $registros, $excluidas, '');

    $datosPaginacion = array($objeto->registros, $registroInicial, $registros, $pagina);
    $item .= $objeto->generarTablaModal($arregloItems, $datosPaginacion, true, false);


    $codigo = HTML::contenedor($botonesSuperiores. '<br><br>' . $item, 'listaItem', 'listaItem');
    $contenido .= HTML::contenedor($codigo, 'overflowVisible margenSuperior');

    $respuesta                  = array();
    $respuesta['generar']       = true;
    $respuesta['cargarJs']      = true;
    $respuesta['archivoJs']     = $configuracion['SERVIDOR']['media'] . $configuracion['RUTAS']['javascript'] . '/modulos/articulos/funcionesFormaTablaMasivoArticulos.js';
    $respuesta['codigo']        = $contenido;
    $respuesta['titulo']        = HTML::parrafo($textos->id('TODOS_LOS_ARTICULOS'), 'letraBlanca negrilla subtitulo');
    $respuesta['destino']       = '#cuadroDialogo';
    $respuesta['ancho']         = 900;
    $respuesta['alto']          = 550;

    Servidor::enviarJSON($respuesta);
    
}

/**
 * Metodo Funcion
 * @global type $textos
 * @global type $sql
 * @param type $id
 * @param type $datos 
 */
function moverMercancia($id) {
    global $textos, $sql, $configuracion, $modulo, $sesion_usuarioSesion;
       
     /**
     * Verificar si el usuario que esta en la sesion tiene permisos para esta accion
     */
    $puedeMoverMercancia = Perfil::verificarPermisosBoton('botonMoverMercanciaBodega', $modulo->nombre);
    
    if(!$puedeMoverMercancia && $sesion_usuarioSesion->id != 0) {
        $respuesta            = array();
        $respuesta['error']   = true;
        $respuesta['mensaje'] = $textos->id('ACCESO_DENEGADO');
        
        Servidor::enviarJSON($respuesta);
        return FALSE;
        
    }

    $objeto     = new Articulo($id);
    $inventario = new Inventario($id);
    
    $inventario->cantidadArticuloPorBodega($id);
    
    $respuesta  = array();

    if (sizeof($inventario->cantidadesArticuloBodega) < 1) {
        $respuesta              = array();
        $respuesta['error']     = true;
        $respuesta['mensaje']   = $textos->id('NO_EXISTENCIAS_NO_MOVIMIENTOS');

        Servidor::enviarJSON($respuesta);
        return NULL;
    }

    $codigo  = HTML::campoOculto('procesar', 'true');
    $codigo .= HTML::campoOculto('datos[id_articulo]', $id, 'idArticulo');
    $codigo .= HTML::campoOculto('datos[dialogo]', '', 'idDialogo');
    $codigo .= HTML::campoOculto('datos[id_bodega_origen]', '', 'idBodegaOrigen');

    //iconos de ayuda
    $imagen1 = HTML::imagen($configuracion['SERVIDOR']['media'] . $configuracion['RUTAS']['imagenesEstilos'] . '/1_verde.png', 'imagenAyuda1 margen3', 'imagenAyuda1', array('ayuda' => $textos->id('AYUDA_MOVER_MERCANCIA_1')));
    $imagen2 = HTML::imagen($configuracion['SERVIDOR']['media'] . $configuracion['RUTAS']['imagenesEstilos'] . '/2_verde.png', 'imagenAyuda2 margen3', 'imagenAyuda2', array('ayuda' => $textos->id('AYUDA_MOVER_MERCANCIA_2')));
    $imagen3 = HTML::imagen($configuracion['SERVIDOR']['media'] . $configuracion['RUTAS']['imagenesEstilos'] . '/3_verde.png', 'imagenAyuda3 margen3', 'imagenAyuda3', array('ayuda' => $textos->id('AYUDA_MOVER_MERCANCIA_3')));
    $imagen4 = HTML::imagen($configuracion['SERVIDOR']['media'] . $configuracion['RUTAS']['imagenesEstilos'] . '/4_verde.png', 'imagenAyuda4 margen3', 'imagenAyuda4', array('ayuda' => $textos->id('AYUDA_MOVER_MERCANCIA_4')));

    $imagen5 = HTML::imagen($configuracion['SERVIDOR']['media'] . $configuracion['RUTAS']['imagenesEstilos'] . '/pregunta_azul.png', 'imagenPregunta4 margen3', 'imagenPregunta4', array('ayuda' => $textos->id('AYUDA_RESTAURAR_VALORES')));

    //si el nombre del articulo es largo, lo recortamos
    if (strlen($objeto->nombre) > 60) {
        $objeto->nombre = HTML::frase(substr($objeto->nombre, 0, 60) . '...', '', '', array('ayuda' => $objeto->nombre));
    }    
    
    $articulo  = HTML::parrafo($textos->id("ARTICULO"), 'subtitulo');
    $articulo .= HTML::parrafo($objeto->nombre, '');

    $presentacion =  HTML::parrafo($textos->id('PRESENTACION'), 'negrilla subtitulo');
    $presentacion .= HTML::parrafo($objeto->unidad, '');
    
    $codigo .= HTML::contenedorCampos($articulo, $presentacion);
    

    $bodegaOrigen .= HTML::parrafo($imagen1 . HTML::frase($textos->id("BODEGA_ORIGEN"), 'subtitulo') . HTML::frase($textos->id('CANTIDAD'), 'flotanteDerecha margenDerecha300 negrilla'), "negrilla margenSuperior50");

    $codigo1 = '';
    
    foreach ($inventario->cantidadesArticuloBodega as $inv) {
        $contenido = '';

        if (strlen($inv->sede) > 30) {
            $inv->sede = HTML::frase(substr($inv->sede, 0, 29) . '.', '', '', array('ayuda' => $inv->sede));
        }
        if (strlen($inv->bodega) > 30) {
            $inv->bodega = HTML::frase(substr($inv->bodega, 0, 29) . '.', '', '', array('ayuda' => $inv->bodega));
        }
        
        $contenido .= HTML::campoChequeo('datos[bodega]', false, 'medioMargenIzquierda checkBodegaOrigen', $inv->idBodega, array('bodega' => $inv->sede . ' :: ' . $inv->bodega, 'cantidad' => $inv->cantidad));
        $contenido .= HTML::frase($inv->sede . ' :: ' . $inv->bodega, 'medioMargenIzquierda');
        $contenido .= HTML::frase($inv->cantidad, 'flotanteDerecha margenDerecha300');

        $codigo1 .= HTML::parrafo($contenido, 'margenSuperior bordeInferior espacioInferior3 espacio3');
    }

    $bodegaOrigen .= HTML::contenedor($codigo1, 'contenedorListaBodegasMovimiento', 'contenedorListaBodegasMovimiento');    
    
    $codigo .= $bodegaOrigen;


    $bodegaDestino = HTML::frase($imagen2 . $textos->id('BODEGA_DESTINO'), '  negrilla subtitulo');
    
    $listaSedes = array();
    $consulta   = $sql->seleccionar(array('sedes_empresa'), array('id', 'nombre'), '', '', 'nombre ASC');
    
    if ($sql->filasDevueltas) {
        while ($dato = $sql->filaEnObjeto($consulta)) {
            $listaSedes[$dato->id] = $dato->nombre;
        }
    }
    
    $listaSedes     = array("0" => "Seleccione la sede")+$listaSedes;
    $selectorSedes  = HTML::listaDesplegable('datos[sede]', $listaSedes, '', 'margenDerecha', 'selectorSedes', '', array(), '');
    $selectorBodega = HTML::listaDesplegable('datos[id_bodega_destino]', '', '', 'medioMargenSuperior margenDerecha100', 'selectorBodegas', '', array(), '');
    $listaBodegas   = $selectorSedes . $selectorBodega;
    
    $bodegaDestino .= HTML::parrafo($listaBodegas, '');

    $cantidadAMover  = HTML::parrafo($textos->id('CANTIDAD_A_MOVER'), 'negrilla margenSuperior', 'textoCantidadAMover');
    $cantidadAMover .= $imagen3 . HTML::campoTexto('datos[cantidad]', 10, 10, '', 'margenSuperior margenIzquierda rangoNumeros soloNumeros', 'campoCantidadAMover', array('rango' => ''));


    $codigo .= HTML::contenedorCampos($bodegaDestino, $cantidadAMover);    
    
    $reset          = HTML::parrafo($imagen5 . HTML::boton("cancelar", $textos->id("RESTAURAR"), " margenIzquierda directo", "botonOk", "botonRestaurarValores"), "margenSuperiorDoble");
    $moverMercancia = HTML::parrafo($imagen4 . HTML::boton("chequeo", $textos->id("MOVER_MERCANCIA"), "directo", "", "botonAceptarMovimiento", "realizarMovimiento(event, $(this))" ), "margenSuperiorDoble flotanteDerecha margenDerechaDoble");

    
    $codigo .= HTML::contenedorCampos($reset, $moverMercancia);     

    $respuesta["generar"]       = true;
    $respuesta['cargarJs']      = true;
    $respuesta['archivoJs']     = $configuracion['SERVIDOR']['media'] . $configuracion['RUTAS']['javascript'] . '/modulos/articulos/funcionesMoverMercancia.js';
    $respuesta["codigo"]        = $codigo;
    $respuesta["titulo"]        = HTML::parrafo($textos->id("MOVER_MERCANCIA"), "letraBlanca negrilla subtitulo");
    $respuesta["destino"]       = "#cuadroDialogo";
    $respuesta["ancho"]         = 750;
    $respuesta["alto"]          = 600;


    Servidor::enviarJSON($respuesta);
    
}

/**
 *
 * @param type $bodegaO
 * @param type $bodegaD
 * @param type $cantidad
 * @param type $idArticulo
 * @return type 
 */
function accionMoverMercancia($bodegaO, $bodegaD, $cantidad, $idArticulo) {
    $objeto         = new Inventario();
    $movimiento     = $objeto->moverMercanciaEntreBodegas($bodegaO, $bodegaD, $cantidad, $idArticulo);

    return $movimiento;
}

/**
 * @global type $configuracion
 * @param type $id 
 */
function imprimirBarcode($id, $cantidad = NULL) {
    global $textos, $sesion_configuracionGlobal, $sql;
    

    $tablas     = array('a' => 'articulos');
    $columnas   = array('nombre' => 'a.nombre', 'datoCodigoBarra' => 'a.' . $sesion_configuracionGlobal->datoCodigoBarra,);
    $condicion  = ' id = "' . $id . '" ';

    $obj = $sql->filaEnObjeto($sql->seleccionar($tablas, $columnas, $condicion));

    $respuesta = array();
    $nombrePdf = 'media/archivos/temporales/' . $id . '.pdf';
    $nombrePdf = trim($nombrePdf);


    if ($cantidad <= 1) {
        $fontSize       = 10;
        $marge          = 10;   // between barcode and hri in pixel
        $x              = 300;  // barcode center
        $y              = 200;  // barcode center
        $height         = 50;   // barcode height in 1D ; module size in 2D
        $width          = 2;    // barcode height in 1D ; not use in 2D
        $angle          = 0;   // rotation in degrees
        // barcode, of course ;)
        $type           = 'code39';
        $black          = '000000'; // color in hexa
        // -------------------------------------------------- //
        //            ALLOCATE FPDF RESSOURCE
        // -------------------------------------------------- //

        define('FPDF_FONTPATH', 'recursos/fuentes/');

        $pdf = new eFPDF('P', 'pt');
        $pdf->AddPage();

        // -------------------------------------------------- //
        //                      BARCODE
        // -------------------------------------------------- //
        // -------------------------------------------------- //
        //                      HRI
        // -------------------------------------------------- //

        $code = Recursos::completarCeros($obj->datoCodigoBarra, 8);
        $data = Barcode::fpdf($pdf, $black, $x, $y, $angle, $type, array('code' => $code), $width, $height);

        $pdf->SetFont('Arial', 'B', $fontSize);
        $pdf->SetTextColor(0, 0, 0);
        $len = $pdf->GetStringWidth($data['hri']);
        
        Barcode::rotate(-$len / 2, ($data['height'] / 2) + $fontSize + $marge, $angle, $xt, $yt);
        
        $pdf->TextWithRotation($x + $xt, $y + $yt, $data['hri'], $angle);
        $len2 = $pdf->GetStringWidth($obj->nombre);
        
        Barcode::rotate(-$len2 / 2, (($data['height'] + 20) / 2) + $fontSize + $marge, $angle, $xt, $yt);
        
        $pdf->TextWithRotation($x + $xt, $y + $yt, $obj->nombre, $angle);
        
        
    } else {
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

        define('FPDF_FONTPATH', 'recursos/fuentes/');
        $pdf = new eFPDF('P', 'pt');
        $pdf->AddPage();

        // -------------------------------------------------- //
        //                      BARCODE
        // -------------------------------------------------- //
        // -------------------------------------------------- //
        //                      HRI
        // -------------------------------------------------- //
        $contador = 0; //va contando los barcode que se imprimen en una linea, salta de linea cuando se imprimen 3
        $nuevaPagina = 0; //cuenta las lineas que se imprimen, cuando hay siete, toca agregar una nueva página(teniendo en cuenta los tamaños actuales)

        for ($i = 0; $i < $cantidad; $i++) {

            if ($contador >= 3) {//ya imprimio los que caben en una linea
                $contador = 0; //reinicio el contador
                $y += 120; //aumentola posicion del y
                $x = 100; //reinicio el x
                $nuevaPagina++; //sumo que se acaba de imprimir una nueva linea
            }

            if ($nuevaPagina == 7) {//Termino de imprimir una página
                $nuevaPagina = 0;
                $pdf->AddPage();
                $x = 100;  // reinicio x a los valores de una página nueva
                $y = 50;  // reinicio y a los valores de una página nueva              
            }

            $code = Recursos::completarCeros($obj->datoCodigoBarra, 8);
            $data = Barcode::fpdf($pdf, $black, $x, $y, $angle, $type, array('code' => $code), $width, $height);

            $pdf->SetFont('Arial', 'B', $fontSize);
            $pdf->SetTextColor(0, 0, 0);
            $len = $pdf->GetStringWidth($data['hri']);
            Barcode::rotate(-$len / 2, ($data['height'] / 2) + $fontSize + $marge, $angle, $xt, $yt);
            $pdf->TextWithRotation($x + $xt, $y + $yt, $data['hri'], $angle);
            $nomArticulo = $obj->nombre;
            if (strlen($nomArticulo) > 32) {
                $nomArticulo = substr($nomArticulo, 0, 32);
            }
            $len2 = $pdf->GetStringWidth($nomArticulo);
            Barcode::rotate(-$len2 / 2, (($data['height'] + 20) / 2) + $fontSize + $marge, $angle, $xt, $yt);
            $pdf->TextWithRotation($x + $xt, $y + $yt, $nomArticulo, $angle);

            $contador++; // incremento contador porque se ha impreso un codigo
            $x += 200; //corro el centro de impresion del codigo de barras sobre el eje x
        }

    }

    $pdf->Output($nombrePdf, 'F');
    chmod($nombrePdf, 0777);

    $respuesta["error"] = NULL;
    $respuesta["accion"] = "abrir_ubicacion";
    $respuesta["destino"] = $nombrePdf;
    $respuesta["info"] = true;
    $respuesta["textoInfo"] = $textos->id('Codigo de barra generado exitosamente'); //machete

    Servidor::enviarJSON($respuesta);
    
}

/**
 * @global type $configuracion
 * @param type $id 
 */
function imprimirVariosBarcode($confirmado, $cantidad, $cadenaItems) {
    global $textos, $sesion_configuracionGlobal, $sql, $modulo, $sesion_usuarioSesion;

     /**
     * Verificar si el usuario que esta en la sesion tiene permisos para esta accion
     */
    $puedeImprimirBarcodePdf = Perfil::verificarPermisosBoton('botonImprimirVariosBarcodeArticulos', $modulo->id);
    
    if(!$puedeImprimirBarcodePdf && $sesion_usuarioSesion->id != 0) {
        $respuesta            = array();
        $respuesta['error']   = true;
        $respuesta['mensaje'] = $textos->id('ACCESO_DENEGADO');
        
        Servidor::enviarJSON($respuesta);
        return FALSE;
        
    }
    
    if (!$confirmado) {
        $destino    = '/ajax/articulos/imprimirVariosBarcode';
        $respuesta  = array();

        $titulo   = HTML::frase($cantidad, 'negrilla');
        $titulo_f = str_replace('%1', $titulo, $textos->id('CONFIRMAR_IMPRIMIR_VARIOS'));
        $codigo   = HTML::campoOculto('procesar', 'true');
        $codigo  .= HTML::campoOculto('cadenaItems', $cadenaItems, 'cadenaItems');
        $codigo  .= HTML::campoOculto('cantidad', $cantidad, 'cantidad');
        $codigo  .= HTML::parrafo($titulo_f);
        $codigo  .= HTML::parrafo(HTML::boton('chequeo', $textos->id('ACEPTAR'), '', 'botonOk', 'botonOk'), 'margenSuperior');
        $codigo_f = HTML::forma($destino, $codigo);

        $respuesta['generar']       = true;
        $respuesta['codigo']        = $codigo_f;
        $respuesta['destino']       = '#cuadroDialogo';
        $respuesta['titulo']        = HTML::parrafo($textos->id('IMPRIMIR_VARIOS_BARCODE'), 'letraBlanca negrilla subtitulo');
        $respuesta['ancho']         = 350;
        $respuesta['alto']          = 150;

        Servidor::enviarJSON($respuesta);
        
    } else {
        $lista = substr($cadenaItems, 0, -1);

        $nom = substr($cadenaItems, 0, 5);

        $respuesta = array();
        $nombrePdf = 'media/archivos/temporales/' . $nom . '.pdf';
        $nombrePdf = trim($nombrePdf);

        //seleccionar los articulos teniendo en cuenta el dato a ser utilizado para imprimir el codigo de barra
        $tablas     = array('a' => 'articulos');
        $columnas   = array('nombre' => 'a.nombre', 'datoCodigoBarra' => 'a.' . $sesion_configuracionGlobal->datoCodigoBarra,);
        $condicion  = ' a.id IN (' . $lista . ')';

        $consulta = $sql->seleccionar($tablas, $columnas, $condicion);

        $arreglo = array();

        while ($objeto = $sql->filaEnObjeto($consulta)) {
            $arreglo[] = $objeto;
        }


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

        define('FPDF_FONTPATH', 'recursos/fuentes/');
        
        $pdf = new eFPDF('P', 'pt');
        $pdf->AddPage();

        // -------------------------------------------------- //
        //                      BARCODE
        // -------------------------------------------------- //
        // -------------------------------------------------- //
        //                      HRI
        // -------------------------------------------------- //
        $contador       = 0; //va contando los barcode que se imprimen en una linea, salta de linea cuando se imprimen 3
        $nuevaPagina    = 0; //cuenta las lineas que se imprimen, cuando hay siete, toca agregar una nueva página(teniendo en cuenta los tamaños actuales)

        foreach ($arreglo as $objt) {

            if ($contador >= 3) {//ya imprimio los que caben en una linea
                $contador = 0; //reinicio el contador
                $y += 120; //aumentola posicion del y
                $x = 100; //reinicio el x
                $nuevaPagina++; //sumo que se acaba de imprimir una nueva linea
            }

            if ($nuevaPagina == 7) {//Termino de imprimir una página
                $nuevaPagina = 0;
                $pdf->AddPage();
                $x = 100;  // reinicio x a los valores de una página nueva
                $y = 50;  // reinicio y a los valores de una página nueva              
            }

            $code = Recursos::completarCeros($objt->datoCodigoBarra, 8);
            $data = Barcode::fpdf($pdf, $black, $x, $y, $angle, $type, array('code' => $code), $width, $height);

            $pdf->SetFont('Arial', 'B', $fontSize);
            $pdf->SetTextColor(0, 0, 0);
            $len = $pdf->GetStringWidth($data['hri']);
            Barcode::rotate(-$len / 2, ($data['height'] / 2) + $fontSize + $marge, $angle, $xt, $yt);
            $pdf->TextWithRotation($x + $xt, $y + $yt, $data['hri'], $angle);
            $nomArticulo = $objt->nombre;
            
            if (strlen($nomArticulo) > 32) {
                $nomArticulo = substr($nomArticulo, 0, 32);
            }
            
            $len2 = $pdf->GetStringWidth($nomArticulo);
            Barcode::rotate(-$len2 / 2, (($data['height'] + 20) / 2) + $fontSize + $marge, $angle, $xt, $yt);
            $pdf->TextWithRotation($x + $xt, $y + $yt, $nomArticulo, $angle);

            $contador++; // incremento contador porque se ha impreso un codigo
            $x += 200; //corro el centro de impresion del codigo de barras sobre el eje x
            
        }

        $pdf->Output($nombrePdf, 'F');
        chmod($nombrePdf, 0777);

        $respuesta["error"]         = NULL;
        $respuesta["accion"]        = "abrir_ubicacion";
        $respuesta["destino"]       = $nombrePdf;
        $respuesta["info"]          = true;
        $respuesta["textoInfo"]     = $textos->id('Codigo de barra generado exitosamente'); //machete

        Servidor::enviarJSON($respuesta);
        
    }
    
}

/**
 * Función adicionar masivo.Tiene un doble comportamiento. La primera llamada 
 * muestra el formulario para cargar el archivo y seleccionar los campos. El 
 * destino de este formulario es esta misma función, pero una vez viene desde 
 * el formulario con el archivo llama al objeto y hace la inserción de la 
 * información en la BD.
 * 
 * @global recurso $textos  = objeto global de gestion de los textos de idioma
 * @global recurso $sql     = objeto global de interaccion con la BD
 * @global recurso $archivo_masivo = archivo con la información a ser cargada en la BD
 * @param array $datos      = arreglo con la información a mostrar en el formulario
 */
function adicionarMasivo($datos = array()) {
    global $textos, $configuracion, $archivo_masivo, $modulo, $sesion_usuarioSesion;

    /**
    * Verificar si el usuario que esta en la sesion tiene permisos para esta accion
    */
    $puedeAdicionarMasivo = Perfil::verificarPermisosBoton('botonCargarMasivoArticulos', $modulo->id);

    if(!$puedeAdicionarMasivo && $sesion_usuarioSesion->id != 0) {
        $respuesta = array();
        $respuesta['error'] = true;
        $respuesta['mensaje'] = $textos->id('ACCESO_DENEGADO');

        Servidor::enviarJSON($respuesta);
        return FALSE;

    }
    
    $objeto     = new Articulo();
    $destino    = '/ajax' . $objeto->urlBase . '/addMassive';
    $respuesta  = array();

    if (empty($datos)) {
        $codigo  = HTML::campoOculto('procesar', 'true');
        $codigo .= HTML::campoOculto('datos[dialogo]', '', 'idDialogo');

        $nombre  = HTML::parrafo($textos->id('ARCHIVO_MASIVO'), 'negrilla margenSuperior');
        $nombre .= HTML::campoArchivo('masivo', 50, 255, '', $textos->id("AYUDA_SELECCIONAR_ARCHIVO_MASIVO"));
        $nombre .= HTML::campoOculto('datos[inicial]', '0', 'inicial');
        $codigo1 = HTML::contenedorCampos($nombre, '');


        $columnas = array(
            $textos->id('CAMPO_BASE_DATOS'),
            $textos->id('CAMPO_ARCHIVO')
        );
        
        $filas = array(
            array(
                HTML::parrafo($textos->id('PLU_INTERNO'), 'negrilla margenSuperior'),
                HTML::listaDesplegable('datos[plu_interno]', array('' => ''), '', 'selectorCampo', 'plu_interno', '', array('onChange' => 'seleccionarCampo(this)'))
            ),
            array(
                HTML::parrafo($textos->id('NOMBRE'), 'negrilla margenSuperior'),
                HTML::listaDesplegable('datos[nombre]', array('' => ''), '', 'selectorCampo', 'nombre', '', array('onChange' => 'seleccionarCampo(this)'))
            ),
            array(
                HTML::parrafo($textos->id('CODIGO_OEM'), 'negrilla margenSuperior'),
                HTML::listaDesplegable('datos[codigo_oem]', array('' => ''), '', 'selectorCampo', 'codigo_oem', '', array('onChange' => 'seleccionarCampo(this)'))
            ),
            array(
                HTML::parrafo($textos->id('SUBGRUPO'), 'negrilla margenSuperior'),
                HTML::listaDesplegable('datos[id_subgrupo]', array('' => ''), '', 'selectorCampo', 'id_subgrupo', '', array('onChange' => 'seleccionarCampo(this)'))
            ),
            array(
                HTML::parrafo($textos->id('LINEA'), 'negrilla margenSuperior'),
                HTML::listaDesplegable('datos[id_linea]', array('' => ''), '', 'selectorCampo', 'id_linea', '', array('onChange' => 'seleccionarCampo(this)'))
            ),
            array(
                HTML::parrafo($textos->id('NACIONALIDAD'), 'negrilla margenSuperior'),
                HTML::listaDesplegable('datos[id_pais]', array('' => ''), '', 'selectorCampo', 'id_pais', '', array('onChange' => 'seleccionarCampo(this)'))
            ),
            array(
                HTML::parrafo($textos->id('PRESENTACION'), 'negrilla margenSuperior'),
                HTML::listaDesplegable('datos[id_unidad]', array('' => ''), '', 'selectorCampo', 'id_unidad', '', array('onChange' => 'seleccionarCampo(this)'))
            ),
            array(
                HTML::parrafo($textos->id('PRECIO1'), 'negrilla margenSuperior'),
                HTML::listaDesplegable('datos[precio1]', array('' => ''), '', 'selectorCampo', 'precio1', '', array('onChange' => 'seleccionarCampo(this)'))
            )
        );

        $tabla      = HTML::tabla($columnas, $filas, '', 'tablaRelacionCampos');
        $codigo2    = HTML::contenedorCampos($tabla, '');
        $pestana1   = HTML::contenedor($codigo1 . $codigo2, 'altura400px');


        $texto1     = HTML::parrafo($textos->id('INDICACIONES_ARCHIVO_MASIVO_1'), 'negrilla margenSuperior');
        $imagen1    = HTML::imagen($configuracion['RUTAS']['media'] . '/' . $configuracion['RUTAS']['imagenesEstaticas'] . '/indicaciones2.jpg', 'imagenItem margenIzquierda', '');
        $texto2     = HTML::parrafo($textos->id('INDICACIONES_ARCHIVO_MASIVO_2'), 'negrilla margenSuperior');
        $imagen2    = HTML::imagen($configuracion['RUTAS']['media'] . '/'  . $configuracion['RUTAS']['imagenesEstaticas'] . '/indicaciones1.jpg', 'imagenItem margenIzquierda', '');
        $codigo3    = HTML::contenedor($texto1 . $imagen1 . $texto2 . $imagen2);

        $pestana2 = HTML::contenedor($codigo3, 'altura400px');

        $pestanas = array(
            HTML::frase($textos->id('INFORMACION_ARCHIVO'), 'letraBlanca')          => $pestana1,
            HTML::frase($textos->id('AYUDA_INFORMACION_ARCHIVO'), 'letraBlanca')    => $pestana2,
            
        );

        $codigo         .= HTML::pestanas2('pestanasAgregar', $pestanas);
        $textoExitoso    = HTML::frase($textos->id('REGISTRO_AGREGADO'), 'textoExitoso margenIzquierda', 'textoExitoso');
        $codigo         .= HTML::parrafo(HTML::boton('chequeo', $textos->id('ACEPTAR'), 'botonOk', 'botonOk', 'botonOk') . $textoExitoso, 'margenSuperior');

        $codigof = HTML::forma($destino, $codigo, 'P', true);

        $respuesta['generar']   = true;
        $respuesta['codigo']    = $codigof;
        $respuesta['titulo']    = HTML::parrafo($textos->id('ADICIONAR_MASIVO'), 'letraBlanca negrilla subtitulo');
        $respuesta['destino']   = '#cuadroDialogo';
        $respuesta['ancho']     = 795;
        $respuesta['alto']      = 595;
               
    } else {
        $respuesta['error'] = true;

        if (empty($archivo_masivo['tmp_name'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_ARCHIVO');
            
        } else {
            $formato = strtolower(substr($archivo_masivo['name'], strrpos($archivo_masivo['name'], '.') + 1));

            if (!in_array($formato, array('xls'))) {
                $respuesta['mensaje'] = $textos->id('ERROR_FORMATO_ARCHIVO' . $formato);
                
            } else {

                if ($datos['inicial'] == 1 && $datos['nombre'] == 0 && $datos['plu_interno'] == 0) {
                    $respuesta['mensaje'] = $textos->id('ERROR_FALTAN_DATOS');
                    
                } else {

                    $exitosa = $objeto->adicionarMasivo($datos);

                    if ($exitosa) {
                        if ($datos['inicial'] == 0) {
                            $respuesta['error']     = false;
                            $respuesta['campos']    = $exitosa;
                            
                        } else {
                            $respuesta['error']         = false;
                            $respuesta['accion']        = 'recargar';
                            $respuesta['textoExito']    = true;
                            $respuesta['mensaje']       = $textos->id('MASIVO_CARGADO_CORRECTAMENTE');
                            
                        }
                        
                    } else {
                        $respuesta['mensaje'] = $textos->id('ERROR_DESCONOCIDO');
                        
                    }
                    
                }
                
            }
            
        }
        
    }

    Servidor::enviarJSON($respuesta);
    
}

/**
 * Funcion que me permite consultar el kardex de un articulo determinado
 *
 * @global array $textos objeto global de traduccion de textos
 * @param int $id identificador del articulo en la BD
 * @param string $fechaInicio primera fecha en el rango de fechas de la consulta
 * @param string $fechaFin segunda fecha en el rango de fechas
 */
function consultarKardex($id, $fechaInicio, $fechaFin, $idBodega){
    global  $textos, $sql;
    
    if (!isset($id) || (isset($id) && !$sql->existeItem('articulos', 'id', $id))) {
        $respuesta              = array();
        $respuesta['error']     = true;
        $respuesta['mensaje']   = $textos->id('NO_HA_SELECCIONADO_ITEM');

        Servidor::enviarJSON($respuesta);
        return false;
        
    } else if (empty($fechaInicio) || empty($fechaFin)) {
        $respuesta              = array();
        $respuesta['error']     = true;
        $respuesta['mensaje']   = $textos->id('ERROR_DEBE_SELECCIONAR_RANGO_FECHAS');

        Servidor::enviarJSON($respuesta);
        return false;
        
    }
    
    $respuesta = array();
    
    $objeto = new Articulo($id);
    
    $cabecera = array(
        HTML::parrafo($textos->id('FECHA'), 'centrado')         => 'fecha',
        HTML::parrafo($textos->id('PROVEEDOR'), 'centrado')     => 'proveedor',
        HTML::parrafo($textos->id('PRECIO_COMPRA'), 'centrado') => 'precioC',
        HTML::parrafo($textos->id('CLIENTE'), 'centrado')       => 'cliente',
        HTML::parrafo($textos->id('PRECIO_VENTA'), 'centrado')  => 'precioV',       
        HTML::parrafo($textos->id('CANTIDAD'), 'centrado')      => 'cantidad',
    );

    $arregloKardex = $objeto->consultarKardex($fechaInicio, $fechaFin, $idBodega);


    $idTabla            = 'tablaConsultarKardex';
    $estilosColumnas    = array('', '', '');
    $contenido          = Recursos::generarTablaRegistrosInterna($arregloKardex, $cabecera, array(), $idTabla, $estilosColumnas);
    
    
    $respuesta['error']             = false;
    $respuesta['accion']            = 'insertar';
    $respuesta['destino']           = '.contenedorKardex';
    $respuesta['contenido']         = $contenido;//"Aqui si llego-> id: ".$id." fecha 1: ".$fechaInicio." - fechaFin: ".$fechaFin;   
    
    Servidor::enviarJSON($respuesta);
    
}

/**
 * Funcion que consulta y devuelve los datos para generar el grafico actual en los articulos
 * 
 * @global type $textos
 * @param type $id
 * @param type $fechaInicio
 * @param type $fechaFin 
 */
function datosGrafico($id, $tipo, $fechaInicio, $fechaFin){
    
    $respuesta  = array();    
    $objeto     = new Articulo($id);   
    

    if($tipo == "barras"){
        $datosGrafico     =  $objeto->datosGraficoBarras($fechaInicio, $fechaFin);
        
    }
    
    $arregloGrafico  = array(
                            'labels'    => $datosGrafico['labels'],
                            'datasets'  => $datosGrafico['datos']
                             );    
    
    $respuesta['datos']  = $arregloGrafico;//$arregloGrafico;
    
    
    Servidor::enviarJSON($respuesta);
    
}
