<?php

/**
 *
 * @package     FOLCS
 * @subpackage  Acciones
 * @author      Pablo Andres Velez Vidal <pavelez@genesyscorp.com.co>
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2013 GENESYS
 * @version     0.1
 *
 * */

if (isset($url_accion)) {
    switch ($url_accion) {
        case 'see'              :   cosultarItem($forma_id);
                                    break;
                                
        case "add"              :   $datos = ($forma_procesar) ? $forma_datos : array();
                                    adicionar($datos);
                                    break;                                         
                                
        case 'edit'             :   $datos = ($forma_procesar) ? $forma_datos : array();
                                    modificar($forma_id, $datos);
                                    break;
                                
        case 'editarAccion'     :   $datos = ($forma_procesar) ? $forma_datos : array();
                                    modificar($forma_id, $datos, $forma_idModulo);
                                    break;                                
                                
        case 'delete'           :   $confirmado = ($forma_procesar) ? true : false;
                                    eliminarItem($forma_id, $confirmado, $forma_dialogo);
                                    break;                              
                                
        case 'search'           :   buscarItem($forma_datos, $forma_cantidadRegistros);
                                    break;
                                
        case 'move'             :   paginador($forma_pagina, $forma_orden, $forma_nombreOrden, $forma_consultaGlobal, $forma_cantidadRegistros);
                                    break;
                                
        case 'listar'           :   listarItems($url_cadena);
                                    break;
                                
        case 'verificarExistenciaAccion'  :     verificarExistenciaAccion($forma_idModulo, $forma_nomAccion);
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
    
    if (empty($id) || (!empty($id) && !$sql->existeItem('componentes_modulos', 'id', $id))) {
        $respuesta              = array();
        $respuesta['error']     = true;
        $respuesta['mensaje']   = $textos->id('NO_HA_SELECCIONADO_ITEM');

        Servidor::enviarJSON($respuesta);
        return NULL;
    }        

    $objeto     = new Accion($id);
    $respuesta  = array();
    $codigo     = '';

    $codigo .= HTML::parrafo($textos->id('MODULO'), 'negrilla margenSuperior');
    $codigo .= HTML::parrafo($objeto->nombreModulo, '', '');
    $codigo .= HTML::parrafo($textos->id('NOMBRE_BOTON'), 'negrilla margenSuperior');
    $codigo .= HTML::parrafo($objeto->nombre, '', '');
    $codigo .= HTML::parrafo($textos->id('NOMBRE_BOTON_MENU'), 'negrilla margenSuperior');
    $codigo .= HTML::parrafo($objeto->nombreMenu, '', '');


    $respuesta['generar'] = true;
    $respuesta['codigo'] = $codigo;
    $respuesta['titulo'] = HTML::parrafo($textos->id('CONSULTAR_ITEM'), 'letraBlanca negrilla subtitulo');
    $respuesta['destino'] = '#cuadroDialogo';
    $respuesta['ancho'] = 450;
    $respuesta['alto'] = 300;



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
function adicionar($datos = array()) {
    global $textos, $sql, $configuracion;

    $accion     = new Accion();
    $destino    = '/ajax' . $accion->urlBase . '/add';
    $respuesta  = array();

    $lista_componentes = array();
    
    $consulta = $sql->seleccionar(array('modulos'), array('id', 'nombre'), 'menu = "1"', '', 'nombre ASC');
    
    if ($sql->filasDevueltas) {
        while ($dato = $sql->filaEnObjeto($consulta)) {
            $lista_componentes[$dato->id] = $dato->nombre;
        }
        
    }

    if (empty($datos)) {
        $codigo = HTML::campoOculto('procesar', 'true');
        $codigo .= HTML::campoOculto('id', $id);
        $codigo .= HTML::campoOculto("datos[dialogo]", "", "idDialogo");
        $codigo .= HTML::parrafo($textos->id('COMPONENTE'), 'negrilla margenSuperior');
        $codigo .= HTML::listaDesplegable('datos[componente]', $lista_componentes, '', '', 'selectorModulos');
        $codigo .= HTML::parrafo($textos->id('NOMBRE_ACCION'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[nombre]', 50, 50, '', 'campoObligatorio');
        $codigo .= HTML::parrafo($textos->id('NOMBRE_MENU'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[nombre_menu]', 50, 50, '', 'campoObligatorio');
        $codigo .= HTML::parrafo(HTML::boton('chequeo', $textos->id('ACEPTAR')), 'margenSuperior');
        $codigo .= HTML::parrafo($textos->id("REGISTRO_AGREGADO"), "textoExitoso", "textoExitoso");
        $codigo = HTML::forma($destino, $codigo);

        $respuesta['generar']       = true;
        $respuesta['cargarJs']      = true;
        $respuesta['archivoJs']     = $configuracion['SERVIDOR']['media'] . $configuracion['RUTAS']['javascript'] . '/modulos/acciones/funcionesVentanaModal.js';        
        $respuesta['codigo']        = $codigo;
        $respuesta['destino']       = '#cuadroDialogo';
        $respuesta['titulo']        = HTML::parrafo($textos->id('ADICIONAR_ITEM'), 'letraBlanca negrilla');
        $respuesta['ancho']         = 500;
        $respuesta['alto']          = 400;
        
    } else {
        $respuesta['error'] = true;
        
        $sql->depurar   = true;
        $existeAccion   = $sql->existeItem('componentes_modulos', 'componente', $datos['nombre'], 'id_modulo = "' . $datos['componente'] . '"');

        if (empty($datos['nombre'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_NOMBRE');
            
        } else if (empty($datos['nombre_menu'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_NOMBRE_MENU');
            
        } else if ($existeAccion) {
            $respuesta['mensaje'] = $textos->id('ERROR_EXISTE_ACCION');
            
        } else {
            
            $idItem = $accion->adicionar($datos);
            
            if ($idItem) {
                /*                 * ************** Creo el nuevo item que se insertara via ajax *************** */
                $objeto = new Accion($idItem);

                $celdas     = array($objeto->nombreModulo, $objeto->nombre, $objeto->nombreMenu);
                $claseFila  = "";
                $idFila     = $idItem;
                $celdas1     = HTML::crearNuevaFila($celdas, $claseFila, $idFila);

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
function modificar($id, $datos = array(), $modulo = NULL) {
    global $textos, $sql, $configuracion;
    
    if (empty($id) || (!empty($id) && !$sql->existeItem('componentes_modulos', 'id', $id))) {
        $respuesta              = array();
        $respuesta['error']     = true;
        $respuesta['mensaje']   = $textos->id('NO_HA_SELECCIONADO_ITEM');

        Servidor::enviarJSON($respuesta);
        return NULL;
    }        

    $accion     = new Accion($id);
    $destino    = '/ajax' . $accion->urlBase . '/edit';
    $respuesta  = array();
    
    $consulta = $sql->seleccionar(array('componentes_modulos'), array('*'), 'id = "' . $id . '"', '', 'nombre ASC');
    
    if ($sql->filasDevueltas) {
        $datos_accion = $sql->filaEnObjeto($consulta);
        
    }    

    if (!$modulo) {
        $lista_componentes = array();

        $consulta = $sql->seleccionar(array('modulos'), array('id', 'nombre'), '', '', 'nombre ASC');

        if ($sql->filasDevueltas) {
            while ($dato = $sql->filaEnObjeto($consulta)) {
                $lista_componentes[$dato->id] = $dato->nombre;

            }

        }

        $listaModulos = HTML::listaDesplegable('datos[componente]', $lista_componentes, $datos_accion->id_modulo, '', 'selectorModulos');

    } else {
        $nomModulo = $sql->obtenerValor("modulos", "nombre", "id = '".$modulo."'");
        
        $listaModulos  = HTML::parrafo($nomModulo, 'negrilla subtitulo');
        $listaModulos .= HTML::campoOculto("datos[componente]", $modulo, "componente");
        
    }

    if (empty($datos)) {
        $codigo  = HTML::campoOculto('procesar', 'true');
        $codigo .= HTML::campoOculto('id', $id);
        $codigo .= HTML::campoOculto("datos[dialogo]", "", "idDialogo");
        $codigo .= HTML::campoOculto("datos[modulo]", $modulo, "modulo");
        $codigo .= HTML::parrafo($textos->id('COMPONENTE'), 'negrilla margenSuperior');
        $codigo .= $listaModulos;
        $codigo .= HTML::parrafo($textos->id('NOMBRE_ACCION'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[nombre]', 50, 50, $datos_accion->componente, 'campoObligatorio');
        $codigo .= HTML::parrafo($textos->id('NOMBRE_MENU'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[nombre_menu]', 50, 50, $datos_accion->nombre, 'campoObligatorio');
        $codigo .= HTML::parrafo(HTML::boton('chequeo', $textos->id('ACEPTAR')), 'margenSuperior');
        $codigo .= HTML::parrafo($textos->id('REGISTRO_MODIFICADO'), 'textoExitoso', 'textoExitoso');
        $codigo = HTML::forma($destino, $codigo);

        $respuesta['generar']       = true;
        $respuesta['cargarJs']      = true;
        $respuesta['archivoJs']     = $configuracion['SERVIDOR']['media'] . $configuracion['RUTAS']['javascript'] . '/modulos/acciones/funcionesVentanaModal.js';         
        $respuesta['codigo']        = $codigo;
        $respuesta['destino']       = '#cuadroDialogo';
        $respuesta['titulo']        = HTML::parrafo($textos->id('MODIFICAR_ITEM'), 'letraBlanca negrilla');
        $respuesta['ancho']         = 500;
        $respuesta['alto']          = 400;
        
    } else {
        $respuesta['error'] = true;
        
        $existeAccion = $sql->existeItem('componentes_modulos', 'componente', $datos['nombre'], 'id_modulo = "' . $datos['componente'] . '" AND id != "' . $id . '"');
        
        if (empty($datos['nombre'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_NOMBRE');
            
        } else if (empty($datos['nombre_menu'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_NOMBRE_MENU');
            
        } else if ($existeAccion) {
            $respuesta['mensaje'] = $textos->id('ERROR_EXISTE_ACCION');
            
        } else {
            
            $idItem = $accion->modificar($datos);
            
            if ($idItem) {
                /*                 * ************** Creo el nuevo item que se insertara via ajax *************** */
                $objeto = new Accion($id);
                
                if (!empty($datos["modulo"]) ) {
                    $botonEditar    = HTML::contenedor('', 'editarRegistroSinAccion editarAccion');
                    $botonEliminar  = HTML::contenedor('', 'eliminarRegistroSinAccion eliminarAccion');
                    
                    $celdas = array($objeto->nombreModulo, $objeto->nombre, $objeto->nombreMenu, $botonEditar, $botonEliminar);
                    
                    $respuesta['idContenedor']          = '#tablaEditarAccionesTr_' . $id;
                    $respuesta['modificarFilaDialogo']  = true;
                    $respuesta['idDestino']             = '#tablaEditarAccionesTr_' . $id;
                    
                } else {                    
                    $respuesta['idContenedor']  = '#tr_' . $id;
                    $respuesta['idDestino']     = '#tr_' . $id;
                    
                    $celdas = array($objeto->nombreModulo, $objeto->nombre, $objeto->nombreMenu);                   
                    
                }
                
                $respuesta['error']                 = false;
                $respuesta['accion']                = 'insertar';
                
                $celdas1 = HTML::crearFilaAModificar($celdas);
                $respuesta['contenido']     = $celdas1;                   
                
                if ($datos['dialogo'] == '') {
                    $respuesta['modificarFilaTabla'] = true;

                } else {
                    $respuesta['modificarFilaDialogo']  = true;
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
 * se encarga de validar la información y llamar al metodo eliminar del objeto.
 * 
 * @global recurso $textos  = objeto global de gestion de los textos de idioma
 * @global recurso $sql     = objeto global de interaccion con la BD
 * @param int $id           = id del registro a modificar
 * @param array $datos      = arreglo con la informacion a adicionar
 */
function eliminarItem($id, $confirmado, $dialogo) {
    global $textos, $sql;
    
    if (empty($id) || (!empty($id) && !$sql->existeItem('componentes_modulos', 'id', $id))) {
        $respuesta              = array();
        $respuesta['error']     = true;
        $respuesta['mensaje']   = $textos->id('NO_HA_SELECCIONADO_ITEM');

        Servidor::enviarJSON($respuesta);
        return NULL;
    }        

    $objeto     = new Accion($id);
    $destino    = '/ajax' . $objeto->urlBase . '/delete';
    $respuesta  = array();

    if (!$confirmado) {
        $nombre = HTML::frase($objeto->nombre, 'negrilla');
        $nombre = str_replace('%1', $nombre, $textos->id('CONFIRMAR_ELIMINACION'));
        $codigo = HTML::campoOculto('procesar', 'true');
        $codigo .= HTML::campoOculto('id', $id);
        $codigo .= HTML::campoOculto('dialogo', '', 'idDialogo');
        $codigo .= HTML::parrafo($nombre);
        $codigo .= HTML::parrafo(HTML::boton('chequeo', $textos->id('ACEPTAR')), 'margenSuperior');
        $codigo = HTML::forma($destino, $codigo);

        $respuesta['generar']       = true;
        $respuesta['codigo']        = $codigo;
        $respuesta['destino']       = '#cuadroDialogo';
        $respuesta['titulo']        = HTML::parrafo($textos->id("ELIMINAR_ITEM"), "letraBlanca negrilla subtitulo");
        $respuesta['ancho']         = 350;
        $respuesta['alto']          = 160;
        
    } else {
                
            $respuesta['error']     = true;
            $respuestaEliminar = $objeto->eliminar();
        
        if ($respuestaEliminar['respuesta']) {

                $respuesta['error']     = false;
                $respuesta['accion']    = 'insertar';           

            if ($dialogo == '') {
                $respuesta['eliminarFilaTabla'] = true;
                $respuesta['idDestino']         = '#tr_' . $id; 

            } else {
                $respuesta['eliminarFilaDialogo']   = true;
                $respuesta['ventanaDialogo']        = $dialogo;
                $respuesta['idDestino']             = '#tablaEditarAccionesTr_' . $id; 

            }
        } else {
            $respuesta['mensaje'] = $respuestaEliminar['mensaje'];

        }  

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
    $objeto     = new Accion();

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
            $consultaGlobal = '(cm.nombre REGEXP "(' . implode('|', $palabras) . ')")';
            
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
        $item       = '';
        $respuesta  = array();
        $objeto     = new Accion();
        
        $registros = $configuracion['GENERAL']['registrosPorPagina'];
        
        if (!empty($cantidadRegistros)) {
            $registros = (int) $cantidadRegistros;
        }
        
        $pagina = 1;
        $registroInicial = 0;


        $palabras = explode(' ', $datos);

        $condicionales = $data[1];

        if ($condicionales == '') {
            $condicion = '(cm.componente REGEXP "(' . implode('|', $palabras) . ')")';
            
        } else {
            //$condicion = str_replace(']', '"', $data[1]);
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

        $arregloItems = $objeto->listar($registroInicial, $registros, array('0'), $condicion, 'cm.id');

        if ($objeto->registrosConsulta) {//si la consulta trajo registros
            $datosPaginacion = array($objeto->registrosConsulta, $registroInicial, $registros, $pagina, $objeto->registrosConsulta);
            $item .= $objeto->generarTabla($arregloItems, $datosPaginacion);
            $info = HTML::parrafo('Tu busqueda trajo ' . $objeto->registrosConsulta . ' resultados', 'textoExitosoNotificaciones');
            
        } else {
            $datosPaginacion = 0;
            $item .= $objeto->generarTabla($textos->id('NO_HAY_REGISTROS'), $datosPaginacion);
            $info = HTML::parrafo('Tu busqueda no trajo resultados, por favor intenta otra busqueda', 'textoErrorNotificaciones');
            
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
 * Funcion Eliminar varios. llamada cuando se seleccionan varios registros y se presiona el
 * botón que aparece llamado "Eliminar varios"
 * 
 * @global boolean $confirmado  = objeto global de gestion de textos
 * @param int $cantidad         = cantidad a ser eliminada
 * @param string $cadenaItems   = cadena que tiene cada uno de los ides del objeto a ser eliminados, ejemplo se eliminan el objeto de id 1, 2, 3, la cadena sería (1,2,3)
 */
function eliminarVarios($confirmado, $cantidad, $cadenaItems) {
    global $textos;


    $destino = '/ajax/acciones/eliminarVarios';
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
            $objeto = new Accion($val);
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
 * 
 * @global recurso $sql
 * @param type $nomAccion
 */
function verificarExistenciaAccion($idModulo, $nomAccion) {
    global $sql;
    
    $sql->depurar = true;
    $existeAccion = $sql->existeItem('componentes_modulos', 'componente', $nomAccion, 'id_modulo = "'.$idModulo.'"' );
    
    $respuesta = array();
    
    $respuesta['verificaExistenciaAccion'] = true; //determina que lo que se consulta es la existencia del item
    $respuesta['consultaExistenciaAccion'] = $existeAccion; //determina si se encontro o no el item

    Servidor::enviarJSON($respuesta);
    
}