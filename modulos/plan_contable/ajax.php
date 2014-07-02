<?php

/**
 * @package     FOM (Framework OpenSource Multiplatform)
 * @subpackage  Plan contable
 * @author      Julian Mondragon <bugshoo@gmail.com>
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2012 GENESYS
 * @version     0.1
 * 
 * Framework basado en FOLCS, desarrollado por Francisco Lozano de FELINUX ltda. Cali - Colombia
 **/

if (isset($url_accion)) {
    switch ($url_accion) {
        case "add"              :   ($forma_procesar) ? $datos = $forma_datos : $datos = array();
                                    adicionarItem($datos);
                                    break;
                                
        case "see"              :   cosultarItem($forma_id);
                                    break;
                                
        case "edit"             :   ($forma_procesar) ? $datos = $forma_datos : $datos = array();
                                    modificarItem($forma_id, $datos);
                                    break;
                                
        case "delete"           :   ($forma_procesar) ? $confirmado = true : $confirmado = false;
                                    eliminarItem($forma_id, $confirmado, $forma_dialogo);
                                    break;
                                
        case "search"           :   ($forma_procesar) ? $datos = $forma_datos : $datos = array();
                                    buscarItem($forma_datos);
                                    break;
                                
        case "move"             :   paginador($forma_pagina, $forma_orden, $forma_nombreOrden, $forma_consultaGlobal, $forma_cantidadRegistros);
                                    break;   
                                
        case "listar"           :   listarItems($url_cadena);
                                    break;     
                                
        case 'addMassive'       :   $datos = ($forma_procesar) ? $forma_datos : array();
                                    adicionarMasivo($datos);
                                    break;    
                                
        case 'eliminarVarios'   :   $confirmado = ($forma_procesar) ? true : false;
                                    eliminarVarios($confirmado, $forma_cantidad, $forma_cadenaItems);
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
    
    if (empty($id) || (!empty($id) && !$sql->existeItem('plan_contable', 'id', $id))) {
        $respuesta              = array();
        $respuesta['error']     = true;
        $respuesta['mensaje']   = $textos->id('NO_HA_SELECCIONADO_ITEM');

        Servidor::enviarJSON($respuesta);
        return NULL;
    }    
        
    $objeto     = new PlanContable($id);
    $respuesta  = array();
    
    $clases         = array("1" => $textos->id("CUENTA_MOVIMIENTO"), "2" => $textos->id("CUENTA_MAYOR"));
    $tipos          = array("1" => $textos->id("BALANCE"), "2" => $textos->id("GANANCIAS_Y_PERDIDAS"), "3" => $textos->id("ORDEN"));
    $certificados   = array("1" => $textos->id("NO_APLICA"), "2" => $textos->id("RETENCION_FUENTE"), "3" => $textos->id("RETENCION_ICA"), "4" => $textos->id("RETENCION_IVA"));
    $flujos         = array("1" => $textos->id("NO_AFECTA_FLUJO"), "2" => $textos->id("CAJA"), "3" => $textos->id("BANCOS"));

    $pestana1   = HTML::campoOculto("id", $id);
    $pestana1  .= HTML::parrafo($textos->id("NOMBRE"), "negrilla margenSuperior");
    $pestana1  .= HTML::parrafo($objeto->nombre, "", "");          
    $pestana1  .= HTML::parrafo($textos->id("CODIGO_CONTABLE"), "negrilla margenSuperior");
    $pestana1  .= HTML::parrafo($objeto->codigoContable, "", "");        
    $pestana1  .= HTML::parrafo($textos->id("DESCRIPCION"), "negrilla margenSuperior");
    $pestana1  .= HTML::parrafo($objeto->descripcion, "", "");
    $pestana1  .= HTML::parrafo($textos->id("CUENTA_PADRE"), "negrilla margenSuperior");
    $pestana1  .= HTML::parrafo($objeto->cuentaPadre->nombre, "", "");
    $pestana1  .= HTML::parrafo($textos->id("NATURALEZA_CUENTA"), "negrilla margenSuperior");
    $pestana1  .= HTML::parrafo($objeto->naturaleza, "", "");
    $pestana1  .= HTML::parrafo($textos->id("CLASE_CUENTA"), "negrilla margenSuperior");
    $pestana1  .= HTML::parrafo($clases[$objeto->clase], "", "");
    $pestana1  .= HTML::parrafo($textos->id("TIPO_CUENTA"), "negrilla margenSuperior");
    $pestana1  .= HTML::parrafo($tipos[$objeto->tipo], "", "");

//    $pestana2   = "";
//    $pestana2  .= HTML::parrafo($textos->id('CUENTA_PADRE'), 'negrilla', '');
//    $pestana2  .= HTML::parrafo($objeto->cuentaPadre->codigoContable.'::'.$objeto->cuentaPadre->nombre, 'negrilla', '');


//    $pestana3   = HTML::parrafo($textos->id("ANEXO_CONTABLE"), "negrilla margenSuperior");
//    $pestana3  .= HTML::parrafo($objeto->anexoContable, "", "");
//    $pestana3  .= HTML::parrafo($textos->id("TASA_APLICAR_1"), "negrilla margenSuperior");
//    $pestana3  .= HTML::parrafo($objeto->tasa1, "", "");
//    $pestana3  .= HTML::parrafo($textos->id("TASA_APLICAR_2"), "negrilla margenSuperior");
//    $pestana3  .= HTML::parrafo($objeto->tasa2, "", "");
//    $pestana3  .= HTML::parrafo($textos->id("CONCEPTO_DIAN"), "negrilla margenSuperior");
//    $pestana3  .= HTML::parrafo($objeto->concepto_DIAN, "", "");
//    $pestana3  .= HTML::parrafo($textos->id("TIPO_CERTIFICADO"), "negrilla margenSuperior");
//    $pestana3  .= HTML::parrafo($certificados[$objeto->tipoCertificado], "", "");
//    $pestana3  .= HTML::parrafo($textos->id("FLUJO_EFECTIVO"), "negrilla margenSuperior");
//    $pestana3  .= HTML::parrafo($flujos[$objeto->flujoEfectivo], "", "");


    $pestanas = array(
        HTML::frase($textos->id("INFORMACION_GENERAL"), "letraBlanca")      => $pestana1,
        HTML::frase($textos->id("CUENTA_PADRE"), "letraBlanca")             => $pestana2,
        //HTML::frase($textos->id("INFORMACION_MOVIMIENTOS"), "letraBlanca")  => $pestana3,
    );

    $codigo = HTML::pestanas2("", $pestanas);        

    $respuesta["generar"] = true;
    $respuesta["codigo"]  = $codigo;
    $respuesta["titulo"]  = HTML::parrafo($textos->id("CONSULTAR_ITEM"), "letraBlanca negrilla subtitulo");
    $respuesta["destino"] = "#cuadroDialogo";
    $respuesta["ancho"]   = 550;
    $respuesta["alto"]    = 400;


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
    global $textos, $sql;

    $objeto     = new PlanContable();
    $destino    = "/ajax".$objeto->urlBase."/add";
    $respuesta  = array();

    if (empty($datos)) {
        $pestana1   = HTML::campoOculto("procesar", "true");
        $pestana1  .= HTML::campoOculto("datos[dialogo]", "", "idDialogo");
        $pestana1  .= HTML::parrafo($textos->id("NOMBRE"), "negrilla margenSuperior");
        $pestana1  .= HTML::campoTexto("datos[nombre]", 30, 255, "", "campoObligatorio");        
        $pestana1  .= HTML::parrafo($textos->id("CODIGO_CONTABLE"), "negrilla margenSuperior");
        $pestana1  .= HTML::campoTexto("datos[codigo_contable]", 30, 255, "", "campoObligatorio");
        $pestana1  .= HTML::parrafo($textos->id("DESCRIPCION"), "negrilla margenSuperior");
        $pestana1  .= HTML::areaTexto("datos[descripcion]", 2, 45, "",  "campoObligatorio", "", "", $textos->id("AYUDA_DESCRIPCION"));
        $pestana1  .= HTML::parrafo($textos->id("NATURALEZA_CUENTA"), "negrilla margenSuperior");
        $pestana1  .= HTML::listaDesplegable("datos[naturaleza_cuenta]", array("D" => $textos->id("DEBITO"), "C" => $textos->id("CREDITO")), "", "campoObligatorio", "", "", array("alt" => $textos->id("AYUDA_NATURALEZA_CUENTA")));
        $pestana1  .= HTML::parrafo($textos->id("CLASE_CUENTA"), "negrilla margenSuperior");
        $pestana1  .= HTML::listaDesplegable("datos[clase_cuenta]", array("1" => $textos->id("CUENTA_MOVIMIENTO"), "2" => $textos->id("CUENTA_MAYOR")), "", "campoObligatorio", "", "", array("alt" => $textos->id("AYUDA_CLASE_CUENTA")));
        $pestana1  .= HTML::parrafo($textos->id("TIPO_CUENTA"), "negrilla margenSuperior");
        $pestana1  .= HTML::listaDesplegable("datos[tipo_cuenta]", array("1" => $textos->id("BALANCE"), "2" => $textos->id("GANANCIAS_Y_PERDIDAS"), "3" => $textos->id("ORDEN")), "", "campoObligatorio", "", "", array("alt" => $textos->id("AYUDA_TIPO_CUENTA")));
        
        $pestana2   = "";
        $pestana2  .= HTML::parrafo($textos->id('CUENTA_PADRE'), 'negrilla', '');
        $pestana2  .= HTML::campoTexto('', 40, 255, '', 'autocompletable campoObligatorio margenSuperior', 'cuentaPadre', array('title' => '/ajax/plan_contable/listar'), $textos->id('AYUDA_USO_CUENTA_PADRE'));
        
//        $consultaAnexos = $sql->seleccionar(array("anexos_contables"), array("id", "nombre"), "id NOT IN (0)", "id", "nombre ASC");
//        $anexos         = array('0' => '');
//        
//        while ($datosAnexo = $sql->filaEnObjeto($consultaAnexos)) {
//            $anexos[$datosAnexo->id] = $datosAnexo->nombre;
//        }
//        
//        $pestana3   = HTML::parrafo($textos->id("ANEXO_CONTABLE"), "negrilla margenSuperior");
//        $pestana3  .= HTML::listaDesplegable("datos[id_anexo_contable]", $anexos, "", "campoObligatorio");
//        
//        $consultaTasas  = $sql->seleccionar(array("tasas"), array("id", "nombre"), "id NOT IN (0)", "id", "nombre ASC");
//        $tasas          = array('0' => '');
//        
//        while ($datosTasa = $sql->filaEnObjeto($consultaTasas)) {
//            $tasas[$datosTasa->id] = $datosTasa->nombre;
//        }
//        
//        $pestana3  .= HTML::parrafo($textos->id("TASA_APLICAR_1"), "negrilla margenSuperior");
//        $pestana3  .= HTML::listaDesplegable("datos[id_tasa_aplicar_1]", $tasas, "", "campoObligatorio");
//        $pestana3  .= HTML::parrafo($textos->id("TASA_APLICAR_2"), "negrilla margenSuperior");
//        $pestana3  .= HTML::listaDesplegable("datos[id_tasa_aplicar_2]", $tasas, "", "campoObligatorio");
//        $pestana3  .= HTML::parrafo($textos->id("TIPO_CERTIFICADO"), "negrilla margenSuperior");
//        $pestana3  .= HTML::listaDesplegable("datos[tipo_certificado]", array("1" => $textos->id("NO_APLICA"), "2" => $textos->id("RETENCION_FUENTE"), "3" => $textos->id("RETENCION_ICA"), "4" => $textos->id("RETENCION_IVA")), "", "campoObligatorio", "", "");
//        $pestana3  .= HTML::parrafo($textos->id("FLUJO_EFECTIVO"), "negrilla margenSuperior");
//        $pestana3  .= HTML::listaDesplegable("datos[flujo_efectivo]", array("1" => $textos->id("NO_AFECTA_FLUJO"), "2" => $textos->id("CAJA"), "3" => $textos->id("BANCOS")), "", "campoObligatorio", "", "");
//        
//        $consultaConceptos  = $sql->seleccionar(array("conceptos_DIAN"), array("id", "nombre"), "id NOT IN (0)", "id", "nombre ASC");
//        $conceptos          = array('0' => '');
//        
//        while ($datosConcepto = $sql->filaEnObjeto($consultaConceptos)) {
//            $conceptos[$datosConcepto->id] = $datosConcepto->nombre;
//        }
//        
//        $pestana3  .= HTML::parrafo($textos->id("CONCEPTO_DIAN"), "negrilla margenSuperior");
//        $pestana3  .= HTML::listaDesplegable("datos[id_concepto_DIAN]", $conceptos, "", "campoObligatorio");
        
        $pestanas = array(
            HTML::frase($textos->id("INFORMACION_GENERAL"), "letraBlanca")      => $pestana1,
            HTML::frase($textos->id("CUENTA_PADRE"), "letraBlanca")             => $pestana2,
            //HTML::frase($textos->id("INFORMACION_MOVIMIENTOS"), "letraBlanca")  => $pestana3,
        );

        $codigo .= HTML::pestanas2("", $pestanas);
        
        $codigo .= HTML::parrafo(HTML::boton("chequeo", $textos->id("ACEPTAR"), "directo", "", "formaEditarPlanContable", "", "").HTML::frase("     ".$textos->id("REGISTRO_AGREGADO"), "textoExitoso", "textoExitoso"), "margenSuperior");
        $codigo  = HTML::forma($destino, $codigo, "P", true, "formaEditarPlanContable", "", "formaEditarPlanContable");

        $respuesta["generar"] = true;
        $respuesta["codigo"]  = $codigo;
        $respuesta["destino"] = "#cuadroDialogo";
        $respuesta["titulo"]  = HTML::parrafo($textos->id("ADICIONAR_ITEM"), "letraBlanca negrilla");
        $respuesta["ancho"]   = 600;
        $respuesta["alto"]    = 500;

    } else {
        $respuesta["error"]   = true;

        if (empty($datos["codigo_contable"])) {
            $respuesta["mensaje"] = $textos->id("ERROR_FALTA_CODIGO");

        } elseif (empty($datos["descripcion"])) {
            $respuesta["mensaje"] = $textos->id("ERROR_FALTA_DESCRIPCION");

        } elseif (empty($datos["naturaleza_cuenta"])) {
            $respuesta["mensaje"] = $textos->id("ERROR_FALTA_NATURALEZA");

        } elseif (empty($datos["tipo_cuenta"])) {
            $respuesta["mensaje"] = $textos->id("ERROR_FALTA_TIPO");
            
        } elseif (empty($datos["clase_cuenta"])) {
            $respuesta["mensaje"] = $textos->id("ERROR_FALTA_CLASE");

        } elseif ($sql->existeItem("plan_contable", "codigo_contable", $datos["codigo_contable"])) {
            $respuesta["mensaje"] = $textos->id("ERROR_EXISTE_CODIGO");

        } else {

            $idItem = $objeto->adicionar($datos);
            if ($idItem) {                
            /**************** Creo el nuevo item que se insertara via ajax ****************/
                $objeto  = new PlanContable($idItem);  
                
                $clases     = array("1" => $textos->id("CUENTA_MOVIMIENTO"), "2" => $textos->id("CUENTA_MAYOR"));
                
                $celdas    = array($objeto->codigo_contable, $objeto->descripcion, $objeto->naturaleza, $clases[$objeto->clase]); 
                $claseFila = "";
                $idFila    = $idItem;
                $celdas    = HTML::crearNuevaFila($celdas, $claseFila, $idFila);
                
                $respuesta['error']             = false;
                $respuesta['accion']            = 'insertar';
                $respuesta['contenido']         = $celdas1;
                $respuesta['idContenedor']      = '#tr_' . $idItem;
                $respuesta['idDestino']         = '#tablaRegistros';

                if ($datos['dialogo'] == '') {
                    $respuesta['insertarNuevaFila'] = true;
                    
                } else {
                    $respuesta['insertarNuevaFilaDialogo']  = true;
                    $respuesta['ventanaDialogo']            = $datos['dialogo'];
                    
                }

            } else {
                $respuesta["mensaje"] = $textos->id("ERROR_DESCONOCIDO");
                
            }
            
        }
        
    }

    Servidor::enviarJSON($respuesta);
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
    global $textos, $configuracion, $archivo_masivo;


    $objeto = new PlanContable();
    $destino = '/ajax' . $objeto->urlBase . '/addMassive';
    $respuesta = array();

    if (empty($datos)) {

        $codigo  = HTML::campoOculto('procesar', 'true');
        $codigo .= HTML::campoOculto('datos[dialogo]', '', 'idDialogo');

        $nombre  = HTML::parrafo($textos->id('ARCHIVO_MASIVO'), 'negrilla margenSuperior');
        $nombre .= HTML::campoArchivo('masivo', '', 'archivoMasivo', 'masivo');
        $nombre .= HTML::campoOculto('datos[inicial]', '0', 'inicial');
        $codigo1 = HTML::contenedorCampos($nombre, '');


        $columnas = array(
            $textos->id('CAMPO_BASE_DATOS'),
            $textos->id('CAMPO_ARCHIVO')
        );
        
        $filas = array(
            array(
                HTML::parrafo($textos->id('CODIGO_CONTABLE'), 'negrilla margenSuperior'),
                HTML::listaDesplegable('datos[codigo]', array('' => ''), '', 'selectorCampo', 'id', '', array('onChange' => 'seleccionarCampo(this)'))
            ),            
            array(
                HTML::parrafo($textos->id('NOMBRE_CUENTA'), 'negrilla margenSuperior'),
                HTML::listaDesplegable('datos[nombre]', array('' => ''), '', 'selectorCampo', 'nombre', '', array('onChange' => 'seleccionarCampo(this)'))
            ),
            array(
                HTML::parrafo($textos->id('CLASIFICACION_CUENTA'), 'negrilla margenSuperior'),
                HTML::listaDesplegable('datos[clasificacion]', array('' => ''), '', 'selectorCampo', 'id_grupo', '', array('onChange' => 'seleccionarCampo(this)'))
            )
        );

        $tabla      = HTML::tabla($columnas, $filas, '', 'tablaRelacionCampos');
        $codigo2    = HTML::contenedorCampos($tabla, '');
        $pestana1   = HTML::contenedor($codigo1 . $codigo2, 'altura400px');


        $texto1     = HTML::parrafo($textos->id('INDICACIONES_ARCHIVO_MASIVO_1'), 'negrilla margenSuperior');
        $imagen1    = HTML::imagen($configuracion['RUTAS']['media'] . $configuracion['RUTAS']['imagenesEstaticas'] . '/indicaciones2.jpg', 'imagenItem margenIzquierda', '');
        $texto2     = HTML::parrafo($textos->id('INDICACIONES_ARCHIVO_MASIVO_2'), 'negrilla margenSuperior');
        $imagen2    = HTML::imagen($configuracion['RUTAS']['media'] . $configuracion['RUTAS']['imagenesEstaticas'] . '/indicaciones1.jpg', 'imagenItem margenIzquierda', '');
        $codigo3    = HTML::contenedor($texto1 . $imagen1 . $texto2 . $imagen2);

        $pestana2 = HTML::contenedor($codigo3, 'altura400px');

        $pestanas = array(
            HTML::frase($textos->id('INFORMACION_ARCHIVO'), 'letraBlanca') => $pestana1,
            HTML::frase($textos->id('AYUDA_INFORMACION_ARCHIVO'), 'letraBlanca') => $pestana2,
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

                if ( $datos['inicial'] == 1 && $datos['nombre'] == 0 ) {
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
    global $textos, $sql;

    $objeto     = new PlanContable($id);
    $destino    = "/ajax".$objeto->urlBase."/add";
    $respuesta  = array();

    if (empty($datos)) {
        $pestana1   = HTML::campoOculto("procesar", "true");
        $pestana1  .= HTML::campoOculto("datos[dialogo]", "", "idDialogo");
        $pestana1  .= HTML::parrafo($textos->id("NOMBRE"), "negrilla margenSuperior");
        $pestana1  .= HTML::campoTexto("datos[nombre]", 30, 255, $objeto->nombre, "campoObligatorio");        
        $pestana1  .= HTML::parrafo($textos->id("CODIGO_CONTABLE"), "negrilla margenSuperior");
        $pestana1  .= HTML::campoTexto("datos[codigo_contable]", 30, 255, $objeto->codigoContable, "campoObligatorio");
        $pestana1  .= HTML::parrafo($textos->id("DESCRIPCION"), "negrilla margenSuperior");
        $pestana1  .= HTML::areaTexto("datos[descripcion]", 2, 45, $objeto->descripcion,  "campoObligatorio", "", "", $textos->id("AYUDA_DESCRIPCION"));
        $pestana1  .= HTML::parrafo($textos->id("NATURALEZA_CUENTA"), "negrilla margenSuperior");
        $pestana1  .= HTML::listaDesplegable("datos[naturaleza_cuenta]", array("D" => $textos->id("DEBITO"), "C" => $textos->id("CREDITO")), $objeto->naturaleza, "campoObligatorio", "", "", array("alt" => $textos->id("AYUDA_NATURALEZA_CUENTA")));
        $pestana1  .= HTML::parrafo($textos->id("CLASE_CUENTA"), "negrilla margenSuperior");
        $pestana1  .= HTML::listaDesplegable("datos[clase_cuenta]", array("1" => $textos->id("CUENTA_MOVIMIENTO"), "2" => $textos->id("CUENTA_MAYOR")), $objeto->clase, "campoObligatorio", "", "", array("alt" => $textos->id("AYUDA_CLASE_CUENTA")));
        $pestana1  .= HTML::parrafo($textos->id("TIPO_CUENTA"), "negrilla margenSuperior");
        $pestana1  .= HTML::listaDesplegable("datos[tipo_cuenta]", array("1" => $textos->id("BALANCE"), "2" => $textos->id("GANANCIAS_Y_PERDIDAS"), "3" => $textos->id("ORDEN")), $objeto->tipo, "campoObligatorio", "", "", array("alt" => $textos->id("AYUDA_TIPO_CUENTA")));
        
        $pestana2   = "";
        $pestana2  .= HTML::parrafo($textos->id('CUENTA_PADRE'), 'negrilla', '');
        $pestana2  .= $linea3 .= HTML::campoTexto('', 40, 255, $objeto->cuentaPadre->nombre, 'autocompletable campoObligatorio margenSuperior', 'cuentaPadre', array('title' => '/ajax/plan_contable/listar'), $textos->id('AYUDA_USO_CUENTA_PADRE'));
//        
//        $consultaAnexos = $sql->seleccionar(array("anexos_contables"), array("id", "nombre"), "id NOT IN (0)", "id", "nombre ASC");
//        $anexos         = array('0' => '');
//        
//        while ($datosAnexo = $sql->filaEnObjeto($consultaAnexos)) {
//            $anexos[$datosAnexo->id] = $datosAnexo->nombre;
//        }
//        
//        $pestana3   = HTML::parrafo($textos->id("ANEXO_CONTABLE"), "negrilla margenSuperior");
//        $pestana3  .= HTML::listaDesplegable("datos[id_anexo_contable]", $anexos, $objeto->anexoContable, "campoObligatorio");
//        
//        $consultaTasas  = $sql->seleccionar(array("tasas"), array("id", "nombre"), "id NOT IN (0)", "id", "nombre ASC");
//        $tasas          = array('0' => '');
//        
//        while ($datosTasa = $sql->filaEnObjeto($consultaTasas)) {
//            $tasas[$datosTasa->id] = $datosTasa->nombre;
//        }
//        
//        $pestana3  .= HTML::parrafo($textos->id("TASA_APLICAR_1"), "negrilla margenSuperior");
//        $pestana3  .= HTML::listaDesplegable("datos[id_tasa_aplicar_1]", $tasas, $objeto->tasa1, "campoObligatorio");
//        $pestana3  .= HTML::parrafo($textos->id("TASA_APLICAR_2"), "negrilla margenSuperior");
//        $pestana3  .= HTML::listaDesplegable("datos[id_tasa_aplicar_2]", $tasas, $objeto->tasa2, "campoObligatorio");
//        $pestana3  .= HTML::parrafo($textos->id("TIPO_CERTIFICADO"), "negrilla margenSuperior");
//        $pestana3  .= HTML::listaDesplegable("datos[tipo_certificado]", array("1" => $textos->id("NO_APLICA"), "2" => $textos->id("RETENCION_FUENTE"), "3" => $textos->id("RETENCION_ICA"), "4" => $textos->id("RETENCION_IVA")), "", "campoObligatorio", "", "");
//        $pestana3  .= HTML::parrafo($textos->id("FLUJO_EFECTIVO"), "negrilla margenSuperior");
//        $pestana3  .= HTML::listaDesplegable("datos[flujo_efectivo]", array("1" => $textos->id("NO_AFECTA_FLUJO"), "2" => $textos->id("CAJA"), "3" => $textos->id("BANCOS")), $objeto->flujoEfectivo, "campoObligatorio", "", "");
//        
//        $consultaConceptos  = $sql->seleccionar(array("conceptos_DIAN"), array("id", "nombre"), "id NOT IN (0)", "id", "nombre ASC");
//        $conceptos          = array('0' => '');
//        
//        while ($datosConcepto = $sql->filaEnObjeto($consultaConceptos)) {
//            $conceptos[$datosConcepto->id] = $datosConcepto->nombre;
//        }
//        
//        $pestana3  .= HTML::parrafo($textos->id("CONCEPTO_DIAN"), "negrilla margenSuperior");
//        $pestana3  .= HTML::listaDesplegable("datos[id_concepto_DIAN]", $conceptos, $objeto->conceptoDIAN, "campoObligatorio");
        
        $pestanas = array(
            HTML::frase($textos->id("INFORMACION_GENERAL"), "letraBlanca")      => $pestana1,
            HTML::frase($textos->id("CUENTA_PADRE"), "letraBlanca")             => $pestana2,
            //HTML::frase($textos->id("INFORMACION_MOVIMIENTOS"), "letraBlanca")  => $pestana3,
        );

        $codigo .= HTML::pestanas2("", $pestanas);
        
        $codigo .= HTML::parrafo(HTML::boton("chequeo", $textos->id("ACEPTAR"), "directo", "", "formaEditarPlanContable", "", "").HTML::frase("     ".$textos->id("REGISTRO_AGREGADO"), "textoExitoso", "textoExitoso"), "margenSuperior");
        $codigo  = HTML::forma($destino, $codigo, "P", true, "formaEditarPlanContable", "", "formaEditarPlanContable");

        $respuesta["generar"] = true;
        $respuesta["codigo"]  = $codigo;
        $respuesta["destino"] = "#cuadroDialogo";
        $respuesta["titulo"]  = HTML::parrafo($textos->id("ADICIONAR_ITEM"), "letraBlanca negrilla");
        $respuesta["ancho"]   = 600;
        $respuesta["alto"]    = 500;

    } else {
        $respuesta["error"]   = true;
        
        $existeNombre = $sql->existeItem("plan_contable", "nombre", $datos["nombre"], "id != '".$id."'");
        $existeCodigo = $sql->existeItem("plan_contable", "codigo_contable", $datos["codigo_contable"], "id != '".$id."'");

        if (empty($datos["codigo_contable"])) {
            $respuesta["mensaje"] = $textos->id("ERROR_FALTA_CODIGO");

        } elseif (empty($datos["nombre"])) {
            $respuesta["mensaje"] = $textos->id("ERROR_FALTA_NOMBRE");

        } elseif (empty($datos["naturaleza_cuenta"])) {
            $respuesta["mensaje"] = $textos->id("ERROR_FALTA_NATURALEZA");

        } elseif (empty($datos["tipo_cuenta"])) {
            $respuesta["mensaje"] = $textos->id("ERROR_FALTA_TIPO");
            
        } elseif (empty($datos["clase_cuenta"])) {
            $respuesta["mensaje"] = $textos->id("ERROR_FALTA_CLASE");

        } elseif ($existeCodigo) {
            $respuesta["mensaje"] = $textos->id("ERROR_EXISTE_CODIGO");

        } elseif ($existeNombre) {
            $respuesta["mensaje"] = $textos->id("ERROR_EXISTE_NOMBRE");

        } else {

            $idItem = $objeto->modificar($datos);
            if ($idItem) {                
            /**************** Creo el nuevo item que se insertara via ajax ****************/
                $objeto  = new PlanContable($idItem);  
                
                $clases     = array("1" => $textos->id("CUENTA_MOVIMIENTO"), "2" => $textos->id("CUENTA_MAYOR"));
                
                $celdas    = array($objeto->codigo_contable, $objeto->descripcion, $objeto->naturaleza, $clases[$objeto->clase]); 
                $claseFila = "";
                $idFila    = $idItem;
                $celdas1   = HTML::crearNuevaFila($celdas, $claseFila, $idFila);
                
                $respuesta['error']             = false;
                $respuesta['accion']            = 'insertar';
                $respuesta['contenido']         = $celdas1;
                $respuesta['idContenedor']      = '#tr_' . $idItem;
                $respuesta['idDestino']         = '#tablaRegistros';

                if ($datos['dialogo'] == '') {
                    $respuesta['insertarNuevaFila'] = true;
                    
                } else {
                    $respuesta['insertarNuevaFilaDialogo']  = true;
                    $respuesta['ventanaDialogo']            = $datos['dialogo'];
                    
                }

            } else {
                $respuesta["mensaje"] = $textos->id("ERROR_DESCONOCIDO");
                
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
    global $textos, $sql;
    
    if (empty($id) || (!empty($id) && !$sql->existeItem('plan_contable', 'id', $id))) {
        $respuesta              = array();
        $respuesta['error']     = true;
        $respuesta['mensaje']   = $textos->id('NO_HA_SELECCIONADO_ITEM');

        Servidor::enviarJSON($respuesta);
        return NULL;
    }     

    $objeto     = new PlanContable($id);
    $destino    = '/ajax' . $objeto->urlBase . '/delete';
    $respuesta  = array();

    if (!$confirmado) {
        $titulo  = HTML::frase($objeto->nombre, 'negrilla');
        $titulo1 = str_replace('%1', $titulo, $textos->id('CONFIRMAR_ELIMINACION')).$objeto->nombre;
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
        //Aqui verificar la integridad de las cuentas
        
        /*$arreglo1 = array('inventarios',            'id_bodega = "'.$id.'"', $textos->id('REGISTROS_INVENTARIOS'));//arreglo del que sale la info a consultar
        $arreglo2 = array('movimientos_mercancia',  'id_bodega_origen = "'.$id.'"', $textos->id('MOVIMIENTOS_MERCANCIA'));
        $arreglo3 = array('movimientos_mercancia',  'id_bodega_destino = "'.$id.'"', $textos->id('MOVIMIENTOS_MERCANCIA'));
        
        $arregloIntegridad  = array($arreglo1, $arreglo2, $arreglo3);//arreglo de arreglos para realizar las consultas de integridad referencial, (ver documentacion de metodo)
        $integridad         = Recursos::verificarIntegridad($textos->id('BODEGA'), $arregloIntegridad); */
        
        $integridad = 'temp';
        
        if ($integridad != '') {
            $respuesta['error']     = true;
            $respuesta['mensaje']   = $integridad;
            
        } else {          

            if ($objeto->eliminar()) {

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
                $respuesta['mensaje'] = $textos->id('ERROR_DESCONOCIDO');

            }
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
function buscarItem($data) {
    global $textos, $configuracion;
     
    $data = explode("[", $data);    
    $datos = $data[0];
    
     if(empty($datos)) { 
         $respuesta["error"]   = true;
         $respuesta["mensaje"] = $textos->id("ERROR_FALTA_CADENA_BUSQUEDA");
            
     }else if(!empty($datos) && strlen($datos) < 2){
         $respuesta["error"]   = true;
         $respuesta["mensaje"] = str_replace("%1", "2", $textos->id("ERROR_TAMAÑO_CADENA_BUSQUEDA"));
         
         
     } else {
            $item       = "";
            $respuesta  = array();
            $objeto = new PlanContable();
            $registros = $configuracion["GENERAL"]["registrosPorPagina"];
            $pagina = 1;
            $registroInicial = 0;
           
            
            $palabras = explode(" ", $datos);
            
            $condicionales = $data[1];
            
            if($condicionales == ""){
                $condicion = "(p.nombre REGEXP '(".implode("|", $palabras).")' OR codigo_contable REGEXP '(".implode("|", $palabras).")')";
                
            }else{
                //$condicion = str_replace("]", "'", $data[1]);
                $condicionales = explode("|", $condicionales);
                
                $condicion = "(";
                $tam = sizeof($condicionales) - 1;
                for($i = 0; $i < $tam; $i++){
                    $condicion .=  $condicionales[$i]." REGEXP '(".implode("|", $palabras).")' ";
                    if($i != $tam -1){
                        $condicion .= " OR ";
                    }
                }
                $condicion .= ")";            
                
            }

            $arregloItems = $objeto->listar($registroInicial, $registros, array("0"), $condicion, "p.nombre");

        if ($objeto->registrosConsulta) {//si la consulta trajo registros
            $datosPaginacion = array($objeto->registrosConsulta, $registroInicial, $registros, $pagina, $objeto->registrosConsulta);
            $item .= $objeto->generarTabla($arregloItems, $datosPaginacion);
            $info = HTML::parrafo(str_replace('%', $objeto->registrosConsulta, $textos->id('RESULTADOS_BUSQUEDA')), 'textoExitosoNotificaciones');
            
        } else {
            $datosPaginacion = 0;
            $item .= $objeto->generarTabla($textos->id('NO_HAY_REGISTROS'), $datosPaginacion);
            $info = HTML::parrafo($textos->id('BUSQUEDA_SIN_RESULTADOS'), 'textoErrorNotificaciones');
            
        }

            $respuesta["error"]                = false;
            $respuesta["accion"]               = "insertar";
            $respuesta["contenido"]            = $item;
            $respuesta["idContenedor"]         = "#tablaRegistros";
            $respuesta["idDestino"]            = "#contenedorTablaRegistros";
            $respuesta["paginarTabla"]         = true;
            $respuesta["info"]                 = $info; 
          

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
function paginador($pagina, $orden = NULL, $nombreOrden = NULL, $consultaGlobal = NULL, $cantidadRegistros = NULL){
    global $configuracion;
    
    $item       = "";
    $respuesta  = array();
    $objeto     = new PlanContable();
    
    $registros = $configuracion["GENERAL"]["registrosPorPagina"];
    
    if (!empty($cantidadRegistros)) {
        $registros = (int) $cantidadRegistros;
    }    
    
    if (isset($pagina)) {
        $pagina = $pagina;
        
    } else {
        $pagina = 1;
        
    }
    
    if(isset($consultaGlobal) && $consultaGlobal != ""){

         $data      = explode("[", $consultaGlobal);
         $datos     = $data[0];
         $palabras  = explode(" ", $datos);

         if($data[1] != ""){    
            $condicionales = explode("|",  $data[1]);
                
                $condicion = "(";
                $tam = sizeof($condicionales) - 1;
                for($i = 0; $i < $tam; $i++){
                    $condicion .=  $condicionales[$i]." REGEXP '(".implode("|", $palabras).")' ";
                    if($i != $tam -1){
                        $condicion .= " OR ";
                    }
                }
                $condicion .= ")";     

             $consultaGlobal = $condicion; 
             
           }else{
             $consultaGlobal = "(codigo_contable REGEXP '(".implode("|", $palabras).")' OR nombre REGEXP '(".implode("|", $palabras).")')";
             
           } 
  
    }else{
      $consultaGlobal = "";
      
    }
    
    if(!isset($nombreOrden)){
        $nombreOrden = $objeto->ordenInicial;
    }    
    
    
    if(isset($orden) && $orden == "ascendente"){//ordenamiento
        $objeto->listaAscendente = true;
    }else{
        $objeto->listaAscendente = false;
    }
    
    if(isset($nombreOrden) && $nombreOrden == "estado"){//ordenamiento
        $nombreOrden = "activo";
    }
    
    $registroInicial = ($pagina - 1) * $registros;
        
    
    $arregloItems = $objeto->listar($registroInicial, $registros, array("0"), $consultaGlobal, $nombreOrden);
    
    if ($objeto->registrosConsulta) {//si la consulta trajo registros
        $datosPaginacion = array($objeto->registrosConsulta, $registroInicial, $registros, $pagina);        
        $item  .= $objeto->generarTabla($arregloItems, $datosPaginacion);    

    }
    
    $respuesta["error"]                = false;
    $respuesta["accion"]               = "insertar";
    $respuesta["contenido"]            = $item;
    $respuesta["idContenedor"]         = "#tablaRegistros";
    $respuesta["idDestino"]            = "#contenedorTablaRegistros";
    $respuesta["paginarTabla"]         = true;   
    
    Servidor::enviarJSON($respuesta);    
    
}

/**
 * Funcion que devuelve la respuesta para el autocompletar
 * 
 * @global recurso $sql     = objeto global de interaccion con la BD
 * @param string $cadena    = cadena de busqueda
 */
function listarItems($cadena) {
    global $sql;
    $respuesta = array(); 
    $sql->depurar = true;
    $consulta  = $sql->seleccionar(array('plan_contable'), array('id', 'codigo_contable', 'nombre'), "codigo_contable LIKE '%$cadena%' OR nombre LIKE '%$cadena%'", "", "nombre ASC", 0, 20);

    while ($fila = $sql->filaEnObjeto($consulta)) {
        $respuesta1 = array(); 
        $respuesta1['label'] = $fila->codigo_contable.' :: '.$fila->nombre;
        $respuesta1['value'] = $fila->id;
        $respuesta[] = $respuesta1;
    }
   
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


    $destino    = '/ajax/plan_contable/eliminarVarios';
    $respuesta  = array();

    if (!$confirmado) {
        $titulo     = HTML::frase($cantidad, 'negrilla');
        $titulo1    = str_replace('%1', $titulo, $textos->id('CONFIRMAR_ELIMINACION_VARIOS'));
        $codigo     = HTML::campoOculto('procesar', 'true');
        $codigo    .= HTML::campoOculto('cadenaItems', $cadenaItems, 'cadenaItems');
        $codigo    .= HTML::parrafo($titulo1);
        $codigo    .= HTML::parrafo(HTML::boton('chequeo', $textos->id('ACEPTAR'), '', 'botonOk', 'botonOk'), 'margenSuperior');
        $codigo    .= HTML::parrafo($textos->id('REGISTRO_ELIMINADO'), 'textoExitoso', 'textoExitoso');
        $codigo1    = HTML::forma($destino, $codigo);

        $respuesta['generar']   = true;
        $respuesta['codigo']    = $codigo1;
        $respuesta['destino']   = '#cuadroDialogo';
        $respuesta['titulo']    = HTML::parrafo($textos->id('ELIMINAR_VARIOS_REGISTROS'), 'letraBlanca negrilla subtitulo');
        $respuesta['ancho']     = 350;
        $respuesta['alto']      = 150;
        
    } else {

        $cadenaIds  = substr($cadenaItems, 0, -1);
        $arregloIds = explode(",", $cadenaIds);

        $eliminarVarios = true;
        
        foreach ($arregloIds as $val) {
            
            $objeto = new PlanContable($val);
            
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
