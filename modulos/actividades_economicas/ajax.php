<?php

/**
 * @package     FOM (Framework OpenSource Multiplatform)
 * @subpackage  Actividades Economicas
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
        
        case 'see'                  :   cosultarItem($forma_id);
                                        break;
        
        case 'edit'                 :   $datos = ($forma_procesar) ?  $forma_datos : array();
                                        modificarItem($forma_id, $datos);
                                        break;
        
        case 'delete'               :   $confirmado = ($forma_procesar) ? true : false;
                                        eliminarItem($forma_id, $confirmado, $forma_dialogo);
                                        break;

        case 'search'               :   buscarItem($forma_datos, $forma_cantidadRegistros);
                                        break;
        
        case 'move'                 :   paginador($forma_pagina, $forma_orden, $forma_nombreOrden, $forma_consultaGlobal, $forma_cantidadRegistros);
                                        break;
        
        case 'listar'               :   listarItems($url_cadena);
                                        break;
        
        case 'addMassive'           :   $datos = ($forma_procesar) ? $forma_datos : array();
                                        adicionarMasivo($datos);
                                        break;         
        
        case 'eliminarVarios'       :   $confirmado = ($forma_procesar) ? true : false;
                                        eliminarVarios($confirmado, $forma_cantidad, $forma_cadenaItems);
                                        break;
        
    }
    
}

/**
 * Funcion que muestra la ventana modal de consultar un banco
 * @global type $textos
 * @param type $id = id del banco a consultar 
 */
function cosultarItem($id) {
    global $textos;

    $objeto     = new ActividadEconomica($id);
    $respuesta  = array();

    $codigo  = HTML::campoOculto('procesar', 'true');
    $codigo .= HTML::campoOculto('id', $id);
    $codigo .= HTML::parrafo($textos->id('CODIGO_DIAN'), 'negrilla margenSuperior');
    $codigo .= HTML::parrafo($objeto->codigoDian, '', '');
    $codigo .= HTML::parrafo($textos->id('NOMBRE'), 'negrilla margenSuperior');
    $codigo .= HTML::parrafo($objeto->nombre, '', '');
    $codigo .= HTML::parrafo($textos->id('RETECREE'), 'negrilla margenSuperior');
    $codigo .= HTML::parrafo($objeto->porcentajeRetecree, '', '');    
    $codigo .= HTML::parrafo($textos->id('ESTADO'), 'negrilla margenSuperior');
    $activo  = ($objeto->activo) ?  HTML::frase($textos->id('ACTIVO'), 'activo') : HTML::frase($textos->id('INACTIVO'), 'inactivo');
    $codigo .= HTML::parrafo($activo, '', '');

    $respuesta['generar']       = true;
    $respuesta['codigo']        = $codigo;
    $respuesta['titulo']        = HTML::parrafo($textos->id('CONSULTAR_ITEM'), 'letraBlanca negrilla subtitulo');
    $respuesta['destino']       = '#cuadroDialogo';
    $respuesta['ancho']         = 450;
    $respuesta['alto']          = 300;

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


    $objeto         = new ActividadEconomica();
    $destino        = '/ajax' . $objeto->urlBase . '/add';
    $respuesta      = array();

    if (empty($datos)) {

        $codigo  = HTML::campoOculto('procesar', 'true');
        $codigo .= HTML::campoOculto('datos[dialogo]', '', 'idDialogo');
        $codigo .= HTML::parrafo($textos->id('CODIGO_DIAN'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[codigo_dian]', 10, 5, '', 'soloNumeros campoObligatorio', '', '', str_replace('%1', '3', $textos->id('TEXTO_NUMERICO_DE_X_CARACTERES')));
        $codigo .= HTML::parrafo($textos->id('NOMBRE'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[nombre]', 40, 255, '', 'campoObligatorio');
        $codigo .= HTML::parrafo($textos->id('RETECREE'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[porcentaje_retecree]', 5, 5, '', 'campoPorcentaje campoObligatorio ');        
        $codigo .= HTML::parrafo(HTML::campoChequeo('datos[activo]', true) . $textos->id('ACTIVO'), 'margenSuperior');
        $codigo .= HTML::parrafo(HTML::boton('chequeo', $textos->id('ACEPTAR'), 'botonOk', 'botonOk', 'botonOk'), 'margenSuperior');
        $codigo .= HTML::parrafo(HTML::frase($textos->id('REGISTRO_AGREGADO'), 'textoExitoso', 'textoExitoso'), 'margenSuperior');
        $codigo  = HTML::forma($destino, $codigo, 'P');

        $respuesta['generar']       = true;
        $respuesta['codigo']        = $codigo;
        $respuesta['titulo']        = HTML::parrafo($textos->id('ADICIONAR_ITEM'), 'letraBlanca negrilla subtitulo');
        $respuesta['destino']       = '#cuadroDialogo';
        $respuesta['ancho']         = 450;
        $respuesta['alto']          = 300;
        
    } else {
        $respuesta['error'] = true;

        $existeCodigo = $sql->existeItem('actividades_economicas', 'codigo_dian', $datos['codigo_dian']);
        $existeNombre = $sql->existeItem('actividades_economicas', 'nombre', $datos['nombre']);

        if (empty($datos['codigo_dian'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_CODIGO_DIAN');
            
        } elseif ($existeCodigo) {
            $respuesta['mensaje'] = $textos->id('ERROR_EXISTE_CODIGO');
            
        } elseif (empty($datos['nombre'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_NOMBRE');
            
        } elseif ($existeNombre) {
            $respuesta['mensaje'] = $textos->id('ERROR_EXISTE_NOMBRE');
            
        } else {
            $idItem = $objeto->adicionar($datos);
            
            if ($idItem) {
                /*                 * ************** Creo el nuevo item que se insertara via ajax *************** */
                $objeto = new ActividadEconomica($idItem);

                $estado = ($objeto->activo) ? HTML::frase($textos->id('ACTIVO'), 'activo') : $estado = HTML::frase($textos->id('INACTIVO'), 'inactivo');
                
                $celdas         = array($objeto->codigoDian, $objeto->nombre, $objeto->porcentajeRetecree, $estado);
                $claseFila      = '';
                $idFila         = $idItem;
                $celdas         = HTML::crearNuevaFila($celdas, $claseFila, $idFila);

                if ($datos['dialogo'] == '') {
                    $respuesta['error']                 = false;
                    $respuesta['accion']                = 'insertar';
                    $respuesta['contenido']             = $celdas;
                    $respuesta['idContenedor']          = '#tr_' . $idItem;
                    $respuesta['insertarNuevaFila']     = true;
                    $respuesta['idDestino']             = '#tablaRegistros';
                    
                } else {
                    $respuesta['error']                     = false;
                    $respuesta['accion']                    = 'insertar';
                    $respuesta['contenido']                 = $celdas;
                    $respuesta['idContenedor']              = '#tr_' . $idItem;
                    $respuesta['insertarNuevaFilaDialogo']  = true;
                    $respuesta['idDestino']                 = '#tablaRegistros';
                    $respuesta['ventanaDialogo']            = $datos['dialogo'];
                    
                }
                
                $respuesta['modulo'] = 'actividades_economicas'; 
                
            } else {
                $respuesta['mensaje'] = $textos->id('ERROR_DESCONOCIDO');
                
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


    $objeto = new ActividadEconomica();
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
                HTML::parrafo($textos->id('CODIGO_DIAN'), 'negrilla margenSuperior'),
                HTML::listaDesplegable('datos[codigo_dian]', array('' => ''), '', 'selectorCampo', 'id', '', array('onChange' => 'seleccionarCampo(this)'))
            ),            
            array(
                HTML::parrafo($textos->id('NOMBRE_ACTIVIDAD'), 'negrilla margenSuperior'),
                HTML::listaDesplegable('datos[nombre]', array('' => ''), '', 'selectorCampo', 'nombre', '', array('onChange' => 'seleccionarCampo(this)'))
            ),
            array(
                HTML::parrafo($textos->id('PORCENTAJE_RETECREE'), 'negrilla margenSuperior'),
                HTML::listaDesplegable('datos[porcentaje_retecree]', array('' => ''), '', 'selectorCampo', 'id_grupo', '', array('onChange' => 'seleccionarCampo(this)'))
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

    $objeto     = new ActividadEconomica($id);
    $destino    = '/ajax' . $objeto->urlBase . '/edit';
    $respuesta  = array();

    if (empty($datos)) {
        $codigo  = HTML::campoOculto('procesar', 'true');
        $codigo .= HTML::campoOculto('id', $id);
        $codigo .= HTML::campoOculto('datos[dialogo]', '', 'idDialogo');
        $codigo .= HTML::parrafo($textos->id('CODIGO_DIAN'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[codigo_dian]', 10, 5, $objeto->codigoDian, 'soloNumeros campoObligatorio', '', '', str_replace('%1', '3', $textos->id('TEXTO_NUMERICO_DE_X_CARACTERES')));
        $codigo .= HTML::parrafo($textos->id('NOMBRE'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[nombre]', 40, 255, $objeto->nombre, 'campoObligatorio');
        $codigo .= HTML::parrafo($textos->id('RETECREE'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[porcentaje_retecree]', 5, 5, $objeto->porcentajeRetecree, 'campoPorcentaje campoObligatorio ');         
        $codigo .= HTML::parrafo(HTML::campoChequeo('datos[activo]', $objeto->activo) . $textos->id('ACTIVO'), 'margenSuperior');
        $codigo .= HTML::parrafo(HTML::boton('chequeo', $textos->id('ACEPTAR'), 'botonOk', 'botonOk', 'botonOk'), 'margenSuperior');
        $codigo .= HTML::parrafo(HTML::frase($textos->id('REGISTRO_MODIFICADO'), 'textoExitoso', 'textoExitoso'), 'margenSuperior');
        $codigo  = HTML::forma($destino, $codigo, 'P');


        $respuesta['generar']       = true;
        $respuesta['codigo']        = $codigo;
        $respuesta['titulo']        = HTML::parrafo($textos->id('MODIFICAR_ITEM'), 'letraBlanca negrilla subtitulo');
        $respuesta['destino']       = '#cuadroDialogo';
        $respuesta['ancho']         = 450;
        $respuesta['alto']          = 300;
        
    } else {
        $respuesta['error'] = true;

        $existeCodigo = $sql->existeItem('actividades_economicas', 'codigo_dian', $datos['codigo_dian'], 'id != "'.$id.'"');
        $existeNombre = $sql->existeItem('actividades_economicas', 'nombre', $datos['nombre'], 'id != "'.$id.'"');

        if (empty($datos['codigo_dian'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_CODIGO_DIAN');
            
        } elseif ($existeCodigo) {
            $respuesta['mensaje'] = $textos->id('ERROR_EXISTE_CODIGO');
            
        } elseif (empty($datos['nombre'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_NOMBRE');
            
        } elseif ($existeNombre) {
            $respuesta['mensaje'] = $textos->id('ERROR_EXISTE_NOMBRE');
            
        } else {
            $idItem = $objeto->modificar($datos);
            
            if ($idItem) {
                /*                 * ************** Creo el nuevo item que se insertara via ajax *************** */
                $objeto = new ActividadEconomica($id);

                $estado = ($objeto->activo) ? HTML::frase($textos->id('ACTIVO'), 'activo') : $estado = HTML::frase($textos->id('INACTIVO'), 'inactivo');
               
                $celdas = array($objeto->codigoDian, $objeto->nombre, $objeto->porcentajeRetecree, $estado);
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
    global $textos;

    $objeto         = new ActividadEconomica($id);
    $destino        = '/ajax' . $objeto->urlBase . '/delete';
    $respuesta      = array();

    if (!$confirmado) {
        $titulo  = HTML::frase($objeto->nombre, 'negrilla');
        $titulo  = str_replace('%1', $titulo, $textos->id('CONFIRMAR_ELIMINACION'));
        $codigo  = HTML::campoOculto('procesar', 'true');
        $codigo .= HTML::campoOculto('id', $id);
        $codigo .= HTML::campoOculto('datos[dialogo]', '', 'idDialogo');
        $codigo .= HTML::parrafo($titulo);
        $codigo .= HTML::parrafo(HTML::boton('chequeo', $textos->id('ACEPTAR'), '', 'botonOk', 'botonOk'), 'margenSuperior');
        $codigo .= HTML::parrafo($textos->id('REGISTRO_ELIMINADO'), 'textoExitoso', 'textoExitoso');
        $codigo  = HTML::forma($destino, $codigo);

        $respuesta['generar']       = true;
        $respuesta['codigo']        = $codigo;
        $respuesta['destino']       = '#cuadroDialogo';
        $respuesta['titulo']        = HTML::parrafo($textos->id('ELIMINAR_ITEM'), 'letraBlanca negrilla subtitulo');
        $respuesta['ancho']         = 350;
        $respuesta['alto']          = 150;
        
    } else {
        
        $arreglo1 = array('proveedores',            'id_actividad_economica = "'.$id.'"', $textos->id('PROVEEDORES'));//arreglo del que sale la info a consultar
        
        $arregloIntegridad  = array($arreglo1);//arreglo de arreglos para realizar las consultas de integridad referencial, (ver documentacion de metodo)
        $integridad         = Recursos::verificarIntegridad($textos->id('ACTIVIDAD_ECONOMICA'), $arregloIntegridad);
        
        if ($integridad != '') {
            $respuesta['error']     = true;
            $respuesta['mensaje']   = $integridad;
            
        } else {           

            if ($objeto->eliminar()) {
                if ($dialogo == '') {
                    $respuesta['error']                 = false;
                    $respuesta['accion']                = 'insertar';
                    $respuesta['idDestino']             = '#tr_' . $id;
                    $respuesta['eliminarFilaTabla']     = true;

                } else {
                    $respuesta['error']                 = false;
                    $respuesta['accion']                = 'insertar';
                    $respuesta['idDestino']             = '#tr_' . $id;
                    $respuesta['eliminarFilaDialogo']   = true;
                    $respuesta['ventanaDialogo']        = $dialogo;

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
function buscarItem($data, $cantidadRegistros = NULL) {
    global $textos, $configuracion;

    $data   = explode('[', $data);
    $datos  = $data[0];

    if (empty($datos)) {
        $respuesta['error'] = true;
        $respuesta['mensaje'] = $textos->id('ERROR_FALTA_CADENA_BUSQUEDA');
        
    } else if (!empty($datos) && strlen($datos) < 2) {
        $respuesta['error'] = true;
        $respuesta['mensaje'] = str_replace('%1', '2', $textos->id('ERROR_TAMAÑO_CADENA_BUSQUEDA'));
        
    } else {
        $item = '';
        $respuesta = array();
        $objeto = new ActividadEconomica();
        
        $registros = $configuracion['GENERAL']['registrosPorPagina'];
        
        if (!empty($cantidadRegistros)) {
            $registros = (int) $cantidadRegistros;
        }
        
        $pagina             = 1;
        $registroInicial    = 0;


        $palabras = explode(' ', $datos);

        $condicionales = $data[1];

        if ($condicionales == "") {
            $condicion = "(ae.nombre REGEXP '(" . implode("|", $palabras) . ")')";
            
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

        $arregloItems = $objeto->listar($registroInicial, $registros, array("0"), $condicion, "ae.nombre");

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

    $item       = "";
    $respuesta  = array();
    $objeto     = new ActividadEconomica();

    $registros = $configuracion['GENERAL']['registrosPorPagina'];
    if (!empty($cantidadRegistros)) {
        $registros = (int) $cantidadRegistros;
    }

    if (isset($pagina)) {
        $pagina = $pagina;
    } else {
        $pagina = 1;
    }

    if (isset($consultaGlobal) && $consultaGlobal != "") {

        $data = explode("[", $consultaGlobal);
        $datos = $data[0];
        $palabras = explode(" ", $datos);

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
            $consultaGlobal = "(ae.nombre REGEXP '(" . implode("|", $palabras) . ")')";
            
        }
        
    } else {
        $consultaGlobal = "";
        
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


    $arregloItems = $objeto->listar($registroInicial, $registros, array("0"), $consultaGlobal, $nombreOrden);

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
 * Funcion que devuelve la respuesta para el autocompletar
 * 
 * @global type $sql
 * @param type $cadena 
 */
function listarItems($cadena) {
    global $sql;
    $respuesta = array();
    $consulta = $sql->seleccionar(array('actividades_economicas'), array('id', 'nombre'), "(nombre LIKE '$cadena%') AND activo = '1' AND id != '0'", "", "nombre ASC", 0, 20);

    while ($fila = $sql->filaEnObjeto($consulta)) {
        $respuesta1 = array();
        $respuesta1['label']    = $fila->nombre;
        $respuesta1['value']    = $fila->id;
        $respuesta[]            = $respuesta1;
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

    $destino    = '/ajax/actividades_economicas/eliminarVarios';
    $respuesta  = array();

    if (!$confirmado) {
        $titulo  = HTML::frase($cantidad, 'negrilla');
        $titulo  = str_replace('%1', $titulo, $textos->id('CONFIRMAR_ELIMINACION_VARIOS'));
        $codigo  = HTML::campoOculto('procesar', 'true');
        $codigo .= HTML::campoOculto('cadenaItems', $cadenaItems, 'cadenaItems');
        $codigo .= HTML::parrafo($titulo);
        $codigo .= HTML::parrafo(HTML::boton('chequeo', $textos->id('ACEPTAR'), '', 'botonOk', 'botonOk'), 'margenSuperior');
        $codigo .= HTML::parrafo($textos->id('REGISTRO_ELIMINADO'), 'textoExitoso', 'textoExitoso');
        $codigo  = HTML::forma($destino, $codigo);

        $respuesta['generar']       = true;
        $respuesta['codigo']        = $codigo;
        $respuesta['destino']       = '#cuadroDialogo';
        $respuesta['titulo']        = HTML::parrafo($textos->id('ELIMINAR_VARIOS_REGISTROS'), 'letraBlanca negrilla subtitulo');
        $respuesta['ancho']         = 350;
        $respuesta['alto']          = 150;
        
    } else {

        $cadenaIds = substr($cadenaItems, 0, -1);
        $arregloIds = explode(",", $cadenaIds);

        $eliminarVarios = true;
        foreach ($arregloIds as $val) {
            $objeto         = new ActividadEconomica($val);
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
