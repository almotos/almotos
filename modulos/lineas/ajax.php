<?php

/**
 * @package     FOM (Framework OpenSource Multiplatform)
 * @subpackage  Lineas
 * @author      Pablo Andres Velez Vidal <pavelez@genesyscorp.com.co>
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2013 GENESYS
 * @version     0.1
 * 
 * Framework basado en FOLCS, desarrollado por Francisco Lozano de FELINUX ltda. Cali - Colombia
 * */


if (isset($url_accion)) {
    switch ($url_accion) {
        case 'add'              :   $datos = ($forma_procesar) ? $forma_datos : array();
                                    adicionarItem($datos);
                                    break;
        
        case 'see'              :   cosultarItem($forma_id);
                                    break;
        
        case 'edit'             :   $datos = ($forma_procesar) ? $forma_datos : array();
                                    modificarItem($forma_id, $datos);
                                    break;
        
        case 'delete'           :   $confirmado = ($forma_procesar) ? true : false;
                                    eliminarItem($forma_id, $confirmado, $forma_dialogo);
                                    break;
        
        case 'addMassive'       :   $datos = ($forma_procesar) ? $forma_datos : array();
                                    adicionarMasivo($datos);    
                                    break;
        
        case 'search'           :   buscarItem($forma_datos, $forma_cantidadRegistros);
                                    break;
        
        case 'move'             :   paginador($forma_pagina, $forma_orden, $forma_nombreOrden, $forma_consultaGlobal, $forma_cantidadRegistros);
                                    break;
        
        case 'listar'           :   listarItems($url_cadena);
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

    if (empty($id) || (!empty($id) && !$sql->existeItem('lineas', 'id', $id))) {
        $respuesta = array();
        $respuesta['error'] = true;
        $respuesta['mensaje'] = $textos->id('NO_HA_SELECCIONADO_ITEM');

        Servidor::enviarJSON($respuesta);
        return NULL;
    }

    $objeto     = new Linea($id);
    $respuesta  = array();

    $codigo  = HTML::campoOculto('procesar', 'true');
    $codigo .= HTML::campoOculto('id', $id);
    $codigo .= HTML::parrafo($textos->id('CODIGO'), 'negrilla margenSuperior');
    $codigo .= HTML::parrafo($objeto->id, '', '');
    $codigo .= HTML::parrafo($textos->id('NOMBRE'), 'negrilla margenSuperior');
    $codigo .= HTML::parrafo($objeto->nombre, '', '');
    $codigo .= HTML::parrafo($textos->id('IMAGEN'), 'negrilla margenSuperior');
    $codigo .= HTML::enlace(HTML::imagen($objeto->imagen->imagenMiniatura, 'imagenItem', ''), $objeto->imagen->imagenPrincipal, '', '', array('rel' => 'prettyPhoto[]'));


    $codigo .= HTML::parrafo($textos->id('ESTADO'), 'negrilla margenSuperior');
    $activo  = ($objeto->activo) ? HTML::frase($textos->id('ACTIVO'), 'activo') : HTML::frase($textos->id('INACTIVO'), 'inactivo');
    $codigo .= HTML::parrafo($activo, '', '');

    $respuesta['generar']   = true;
    $respuesta['codigo']    = $codigo;
    $respuesta['titulo']    = HTML::parrafo($textos->id('CONSULTAR_ITEM'), 'letraBlanca negrilla subtitulo');
    $respuesta['destino']   = '#cuadroDialogo';
    $respuesta['ancho']     = 550;
    $respuesta['alto']      = 400;


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
    global $textos, $sql, $archivo_imagen;


    $objeto         = new Linea();
    $destino        = '/ajax' . $objeto->urlBase . '/add';
    $respuesta      = array();

    if (empty($datos)) {

        $codigo = HTML::campoOculto('procesar', 'true');
        $codigo .= HTML::campoOculto('datos[dialogo]', '', 'idDialogo');
        $codigo .= HTML::parrafo($textos->id('NOMBRE'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[nombre]', 40, 255, '', 'campoObligatorio');
        $codigo .= HTML::parrafo($textos->id('IMAGEN'), 'negrilla margenSuperior');
        $codigo .= HTML::campoArchivo('imagen', 50, 255);
        $codigo .= HTML::parrafo(HTML::campoChequeo('datos[activo]', true) . $textos->id('ACTIVO'), 'margenSuperior');
        $codigo .= HTML::parrafo(HTML::boton('chequeo', $textos->id('ACEPTAR'), '', '', ''), 'margenSuperior');
        $codigo .= HTML::parrafo($textos->id('REGISTRO_AGREGADO'), 'textoExitoso', 'textoExitoso');
        $codigo1 = HTML::forma($destino, $codigo, 'P', true);

        $respuesta['generar']       = true;
        $respuesta['codigo']        = $codigo1;
        $respuesta['titulo']        = HTML::parrafo($textos->id('ADICIONAR_ITEM'), 'letraBlanca negrilla subtitulo');
        $respuesta['destino']       = '#cuadroDialogo';
        $respuesta['ancho']         = 550;
        $respuesta['alto']          = 400;
        
    } else {

        $respuesta['error'] = true;

        $existeNombre = $sql->existeItem('lineas', 'nombre', $datos['nombre']);
        
        if (!empty($archivo_imagen['tmp_name'])) {
            $validarFormato = Recursos::validarArchivo($archivo_imagen, array('jpg', 'png', 'gif', 'jpeg'));
        }
        
        if (empty($datos['nombre'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_NOMBRE');
            
        } elseif ($existeNombre) {
            $respuesta['mensaje'] = $textos->id('ERROR_EXISTE_NOMBRE');
            
        } elseif ($validarFormato) {
            $respuesta['mensaje'] = $textos->id('ERROR_FORMATO_IMAGEN');
            
        } else {
            
            $idItem = $objeto->adicionar($datos);
            
            if ($idItem) {
                /*                 * ************** Creo el nuevo item que se insertara via ajax *************** */
                $objeto = new Linea($idItem);

                $estado = ($objeto->activo) ? HTML::frase($textos->id('ACTIVO'), 'activo') : HTML::frase($textos->id('INACTIVO'), 'inactivo');

                $celdas     = array($objeto->id, $objeto->nombre, $estado);
                $claseFila  = '';
                $idFila     = $idItem;
                $celdas1    = HTML::crearNuevaFila($celdas, $claseFila, $idFila);

                $respuesta['error']         = false;
                $respuesta['accion']        = 'insertar';
                $respuesta['contenido']     = $celdas1;
                $respuesta['idContenedor']  = '#tr_' . $idItem;
                $respuesta['idDestino']     = '#tablaRegistros';
                $respuesta['modulo']        = 'lineas';                

                if ($datos['dialogo'] == '') {
                    $respuesta['insertarNuevaFila'] = true;
                    
                } else {
                    $respuesta['insertarNuevaFilaDialogo']  = true;
                    $respuesta['ventanaDialogo']            = $datos['dialogo'];
                    
                }
                
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


    $objeto     = new Linea();
    $destino    = '/ajax' . $objeto->urlBase . '/addMassive';
    $respuesta  = array();

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
                HTML::parrafo($textos->id('ID'), 'negrilla margenSuperior'),
                HTML::listaDesplegable('datos[id]', array('' => ''), '', 'selectorCampo', 'id', '', array('onChange' => 'seleccionarCampo(this)'))
            ),            
            array(
                HTML::parrafo($textos->id('NOMBRE'), 'negrilla margenSuperior'),
                HTML::listaDesplegable('datos[nombre]', array('' => ''), '', 'selectorCampo', 'nombre', '', array('onChange' => 'seleccionarCampo(this)'))
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
                $respuesta['mensaje'] = $textos->id('ERROR_FORMATO_ARCHIVO');

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
    global $textos, $sql, $archivo_imagen;

    if ( (!empty($id) && !$sql->existeItem('lineas', 'id', $id)) || empty($id) ) {
        $respuesta = array();
        $respuesta['error'] = true;
        $respuesta['mensaje'] = $textos->id('NO_HA_SELECCIONADO_ITEM');

        Servidor::enviarJSON($respuesta);
        return NULL;
    }

    $objeto         = new Linea($id);
    $destino        = '/ajax' . $objeto->urlBase . '/edit';
    $respuesta      = array();

    if (empty($datos)) {

        $codigo  = HTML::campoOculto('procesar', 'true');
        $codigo .= HTML::campoOculto('id', $id);
        $codigo .= HTML::campoOculto('datos[dialogo]', '', 'idDialogo');
        $codigo .= HTML::parrafo($textos->id('NOMBRE'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[nombre]', 40, 255, $objeto->nombre, 'campoObligatorio');
        $codigo .= HTML::parrafo($textos->id('IMAGEN'), 'negrilla margenSuperior');
        $codigo .= HTML::campoArchivo('imagen', 50, 255) . HTML::imagen($objeto->imagen->imagenMiniatura, 'imagenLinea', 'margenIzquierda');
        $codigo .= HTML::parrafo(HTML::campoChequeo('datos[activo]', $objeto->activo) . $textos->id('ACTIVO'), 'margenSuperior');
        $codigo .= HTML::parrafo(HTML::boton('chequeo', $textos->id('ACEPTAR'), 'botonOk', 'botonOk', 'botonOk'), 'margenSuperior');
        $codigo .= HTML::parrafo($textos->id('REGISTRO_MODIFICADO'), 'textoExitoso', 'textoExitoso');
        $codigo1 = HTML::forma($destino, $codigo, 'P');

        $respuesta['generar']   = true;
        $respuesta['codigo']    = $codigo1;
        $respuesta['titulo']    = HTML::parrafo($textos->id('MODIFICAR_ITEM'), 'letraBlanca negrilla subtitulo');
        $respuesta['destino']   = '#cuadroDialogo';
        $respuesta['ancho']     = 550;
        $respuesta['alto']      = 400;

    } else {

        $respuesta['error'] = true;

        $existeNombre = $sql->existeItem('lineas', 'nombre', $datos['nombre'], 'id != "' . $id . '"');

        if (!empty($archivo_imagen['tmp_name'])) {
            $validarFormato = Recursos::validarArchivo($archivo_imagen, array('jpg', 'png', 'gif', 'jpeg'));

        }

        if (empty($datos['nombre'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_NOMBRE');

        } elseif ($existeNombre) {
            $respuesta['mensaje'] = $textos->id('ERROR_EXISTE_NOMBRE');

        } elseif ($validarFormato) {
            $respuesta['mensaje'] = $textos->id('ERROR_FORMATO_IMAGEN');

        } else {
            $idItem = $objeto->modificar($datos);

            if ($idItem) {
                /*                 * ************** Creo el nuevo item que se insertara via ajax *************** */
                $objeto = new Linea($id);

                $estado = ($objeto->activo) ? HTML::frase($textos->id('ACTIVO'), 'activo') : HTML::frase($textos->id('INACTIVO'), 'inactivo');


                $celdas = array($objeto->id, $objeto->nombre, $estado);
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

//Fin del metodo de modificar

/**
 * Funcion Eliminar
 * @global type $textos
 * @param type $id
 * @param type $confirmado 
 */
function eliminarItem($id, $confirmado, $dialogo) {
    global $textos, $sql;

    if ( (!empty($id) && !$sql->existeItem('lineas', 'id', $id)) || empty($id) ) {
        $respuesta              = array();
        $respuesta['error']     = true;
        $respuesta['mensaje']   = $textos->id('NO_HA_SELECCIONADO_ITEM');

        Servidor::enviarJSON($respuesta);
        return NULL;

    }

    $objeto     = new Linea($id);
    $destino    = '/ajax' . $objeto->urlBase . '/delete';
    $respuesta  = array();

    if (!$confirmado) {
        $titulo  = HTML::frase($objeto->nombre, 'negrilla');
        $titulo1 = str_replace('%1', $titulo, $textos->id('CONFIRMAR_ELIMINACION'));
        $codigo  = HTML::campoOculto('procesar', 'true');
        $codigo .= HTML::campoOculto('id', $id);
        $codigo .= HTML::campoOculto('dialogo', '', 'idDialogo');
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
 * 
 * @global type $textos
 * @global type $sql
 * @param type $datos 
 */
function buscarItem($data, $cantidadRegistros = NULL) {
    global $textos, $configuracion;

    $data   = explode('[', $data);
    $datos  = $data[0];

    if (empty($datos)) {
        $respuesta['error'] = true;
        $respuesta['mensaje'] = $textos->id('ERROR_FALTA_CADENA_BUSQUEDA');

    } else if (!empty($datos) && strlen($datos) < 2) {
        $respuesta['error']     = true;
        $respuesta['mensaje']   = str_replace('%1', '2', $textos->id('ERROR_TAMAÑO_CADENA_BUSQUEDA'));
        
    } else {
        $item           = '';
        $respuesta      = array();
        $objeto         = new Linea();
        $registros      = $configuracion['GENERAL']['registrosPorPagina'];
        
        if (!empty($cantidadRegistros)) {
            $registros = (int) $cantidadRegistros;
            
        }
        $pagina = 1;
        $registroInicial = 0;


        $palabras = explode(' ', $datos);

        $condicionales = $data[1];

        if ($condicionales == '') {
            $condicion = '(l.nombre REGEXP "(' . implode('|', $palabras) . ')")';
            
        } else {

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

        $arregloItems = $objeto->listar($registroInicial, $registros, array('0'), $condicion, 'l.nombre');

        if ($objeto->registrosConsulta) {//si la consulta trajo registros
            $datosPaginacion = array($objeto->registrosConsulta, $registroInicial, $registros, $pagina, $objeto->registrosConsulta);
            $item .= $objeto->generarTabla($arregloItems, $datosPaginacion);
            $info = HTML::parrafo(str_replace('%', $objeto->registrosConsulta, $textos->id('RESULTADOS_BUSQUEDA')), 'textoExitosoNotificaciones');
            
        } else {
            $datosPaginacion = 0;
            $item .= $objeto->generarTabla($textos->id('NO_HAY_REGISTROS'), $datosPaginacion);
            $info = HTML::parrafo($textos->id('BUSQUEDA_SIN_RESULTADOS'), 'textoErrorNotificaciones');
            
        }

        $respuesta['error'] = false;
        $respuesta['accion'] = 'insertar';
        $respuesta['contenido'] = $item;
        $respuesta['idContenedor'] = '#tablaRegistros';
        $respuesta['idDestino'] = '#contenedorTablaRegistros';
        $respuesta['paginarTabla'] = true;
        $respuesta['info'] = $info;
    }

    Servidor::enviarJSON($respuesta);
}

/*
 * Funcion que se encarga de recargar la tabla de datos paginando
 */

function paginador($pagina, $orden = NULL, $nombreOrden = NULL, $consultaGlobal = NULL, $cantidadRegistros = NULL) {
    global $configuracion;

    $item           = '';
    $respuesta      = array();
    $objeto         = new Linea();

    $registros = $configuracion['GENERAL']['registrosPorPagina'];

    if (!empty($cantidadRegistros)) {
        $registros = (int) $cantidadRegistros;
        
    }

    if (!empty($pagina)) {
        $pagina = $pagina;
        
    } else {
        $pagina = 1;
        
    }

    if (!empty($consultaGlobal)) {

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
            $consultaGlobal = '(l.nombre REGEXP "(' . implode('|', $palabras) . ')")';
            
        }
        
    } else {
        $consultaGlobal = '';
        
    }

    if (empty($nombreOrden)) {
        $nombreOrden = $objeto->ordenInicial;
    }


    if (!empty($orden) && $orden == 'ascendente') {//ordenamiento
        $objeto->listaAscendente = true;
        
    } else {
        $objeto->listaAscendente = false;
        
    }

    if (!empty($nombreOrden) && $nombreOrden == 'estado') {//ordenamiento
        $nombreOrden = 'activo';
    }

    $registroInicial = ($pagina - 1) * $registros;


    $arregloItems = $objeto->listar($registroInicial, $registros, array('0'), $consultaGlobal, $nombreOrden);

    if ($objeto->registrosConsulta) {//si la consulta trajo registros
        $datosPaginacion = array($objeto->registrosConsulta, $registroInicial, $registros, $pagina);
        $item           .= $objeto->generarTabla($arregloItems, $datosPaginacion);
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
 * @global type $sql
 * @param type $cadena 
 */
function listarItems($cadena) {
    global $sql;

    $respuesta = array();
    
    $consulta = $sql->seleccionar(array('lineas'), array('id', 'nombre'), 'nombre LIKE "%' . $cadena . '%" AND activo = "1" AND id NOT IN(0, 999)', '', 'nombre ASC', 0, 20);

    while ($fila = $sql->filaEnObjeto($consulta)) {
        $respuesta1             = array();
        $respuesta1['label']    = $fila->nombre;
        $respuesta1['value']    = $fila->id;
        $respuesta[]            = $respuesta1;
        
    }

    Servidor::enviarJSON($respuesta);
}

/**
 * Funcion Eliminar
 * @global type $textos
 * @param type $id
 * @param type $confirmado 
 */
function eliminarVarios($confirmado, $cantidad, $cadenaItems) {
    global $textos;


    $destino   = '/ajax/lineas/eliminarVarios';
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
            $objeto = new Linea($val);
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
