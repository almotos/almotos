<?php

/**
 * @package     FOM (Framework OpenSource Multiplatform)
 * @subpackage  Cajas
 * @author      Pablo Andres Velez Vidal <pavelez8@genesyscorp.com.co>
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2012 GENESYS
 * @version     0.1
 * 
 * Framework basado en FOLCS, desarrollado por Francisco Lozano de FELINUX ltda. Cali - Colombia
 * */

if (isset($url_accion)) {
    switch ($url_accion) {
        case 'add'                  :   adicionarItem($forma_datos);
                                        break;
        
        case 'see'                  :   cosultarItem($forma_id);
                                        break;
        
        case 'edit'                 :   $datos = ($forma_procesar) ? $forma_datos : array();
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
        
        case 'eliminarVarios'       :   $confirmado = ($forma_procesar) ? true : false;
                                        eliminarVarios($confirmado, $forma_cantidad, $forma_cadenaItems);
                                        break;
                                    
        case 'escogerCaja'          :   escogerCaja($forma_idSede);
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
    
    if (empty($id) || (!empty($id) && !$sql->existeItem('cajas', 'id', $id))) {
        $respuesta              = array();
        $respuesta['error']     = true;
        $respuesta['mensaje']   = $textos->id('NO_HA_SELECCIONADO_ITEM');

        Servidor::enviarJSON($respuesta);
        return NULL;
    }    

    $objeto = new Caja($id);
    $respuesta = array();

    $codigo  = HTML::campoOculto('procesar', 'true');
    $codigo .= HTML::campoOculto('id', $id);
    $codigo .= HTML::parrafo($textos->id('NOMBRE'), 'negrilla margenSuperior');
    $codigo .= HTML::parrafo($objeto->nombre, '', '');
    $codigo .= HTML::parrafo($textos->id('SEDE'), 'negrilla margenSuperior');
    $codigo .= HTML::parrafo($objeto->sede, '', '');


    $codigo .= HTML::parrafo($textos->id('ESTADO'), 'negrilla margenSuperior');
    $activo = ($objeto->activo) ? HTML::frase($textos->id('ACTIVO'), 'activo') : HTML::frase($textos->id('INACTIVO'), 'inactivo');
    $codigo .= HTML::parrafo($activo, '', '');
    
    $codigo .= HTML::parrafo($textos->id('TIPO_CAJA'), 'negrilla margenSuperior');
    $activo = ($objeto->principal) ? HTML::frase($textos->id('PRINCIPAL'), 'activo') : HTML::frase($textos->id('SECUNDARIA'), 'inactivo');
    $codigo .= HTML::parrafo($activo, '', '');    

    $respuesta['generar']       = true;
    $respuesta['codigo']        = $codigo;
    $respuesta['titulo']        = HTML::parrafo($textos->id('CONSULTAR_ITEM'), 'letraBlanca negrilla subtitulo');
    $respuesta['destino']       = '#cuadroDialogo';
    $respuesta['ancho']         = 550;
    $respuesta['alto']          = 400;


    Servidor::enviarJSON($respuesta);
}

/**
 * Funci�n con doble comportamiento. La primera llamada (con el arreglo de datos vacio)
 * muestra el formulario para el ingreso del registro. El destino de este formulario es esta 
 * misma funci�n, pero una vez viene desde el formulario con el arreglo datos cargado de valores
 * se encarga de validar la informaci�n y llamar al metodo adicionar del objeto.
 * 
 * @global recurso $textos  = objeto global de gestion de los textos de idioma
 * @global recurso $sql     = objeto global de interaccion con la BD
 * @param array $datos      = arreglo con la informacion a adicionar
 */
function adicionarItem($datos = array()) {
    global $textos, $sql;


    $objeto = new Caja();
    $destino = '/ajax' . $objeto->urlBase . '/add';
    $respuesta = array();

    if (empty($datos)) {
        //Se agrega el campo oculto datos[dialogo] que va a ser el que almacene el id de la ventana de dialogo donde sera mostrado este formulario
        $codigo  = HTML::campoOculto('procesar', 'true');
        $codigo .= HTML::campoOculto('datos[dialogo]', '', 'idDialogo');
        $codigo .= HTML::parrafo($textos->id('SEDE'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[sede]', 40, 255, '', 'autocompletable campoObligatorio', '', array('title' => HTML::urlInterna('SEDES_EMPRESA', 0, true, 'listar')), $textos->id('AYUDA_USO_AUTOCOMPLETAR'), HTML::urlInterna('SEDES_EMPRESA', 0, true, 'add'), 'datos[id_sede]');
        $codigo .= HTML::parrafo($textos->id('NOMBRE'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[nombre]', 40, 255, '', 'campoObligatorio');

        $codigo .= HTML::parrafo(HTML::campoChequeo('datos[activo]', true) . $textos->id('ACTIVO'), 'margenSuperior');
        $codigo .= HTML::parrafo(HTML::campoChequeo('datos[principal]', false) . $textos->id('PRINCIPAL'), 'margenSuperior');
        $codigo .= HTML::parrafo(HTML::boton('chequeo', $textos->id('ACEPTAR'), '', '', ''), 'margenSuperior');
        
        $codigof = HTML::forma($destino, $codigo, 'P', true);

        $respuesta['generar']       = true;
        $respuesta['codigo']        = $codigof;
        $respuesta['titulo']        = HTML::parrafo($textos->id('ADICIONAR_ITEM'), 'letraBlanca negrilla subtitulo');
        $respuesta['destino']       = '#cuadroDialogo';
        $respuesta['ancho']         = 550;
        $respuesta['alto']          = 400;
        
    } else {

        $respuesta['error'] = true;


        $existeNombre = $sql->existeItem('cajas', 'nombre', $datos['nombre'], ' id_sede = "'.$datos['id_sede'].'"');

        if (empty($datos['id_sede'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_SEDE');
            
        } elseif ($existeNombre) {
            $respuesta['mensaje'] = $textos->id('ERROR_EXISTE_NOMBRE');
            
        } elseif (empty($datos['nombre'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_NOMBRE');
            
        } else {
            $idItem = $objeto->adicionar($datos);
            if ($idItem) {
                /*                 * ************** Creo el nuevo item que se insertara via ajax *************** */
                $objeto = new Caja($idItem);

                $estado = ($objeto->activo) ? HTML::frase($textos->id('ACTIVO'), 'activo') : $estado = HTML::frase($textos->id('INACTIVO'), 'inactivo');
                
                $categoria = ($objeto->principal) ? HTML::frase($textos->id('PRINCIPAL'), 'activo') : $estado = HTML::frase($textos->id('SECUNDARIA'), 'inactivo');

                $celdas         = array($objeto->sede, $objeto->nombre, $estado, $categoria);
                $claseFila      = '';
                $idFila         = $idItem;
                $celdas1        = HTML::crearNuevaFila($celdas, $claseFila, $idFila);

                //se carga el arreglo respuesta con los datos que seran recibidos por el javascript
                $respuesta['error']             = false;
                $respuesta['accion']            = 'insertar';
                $respuesta['contenido']         = $celdas1;
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
 * Funci�n con doble comportamiento. La primera llamada (con el arreglo de datos vacio)
 * muestra el formulario con los datos del registro a ser modificado. El destino de este formulario es esta 
 * misma funci�n, pero una vez viene desde el formulario con el arreglo datos cargado de valores
 * se encarga de validar la informaci�n y llamar al metodo modificar del objeto.
 * 
 * @global recurso $textos  = objeto global de gestion de los textos de idioma
 * @global recurso $sql     = objeto global de interaccion con la BD
 * @param int $id           = id del registro a modificar
 * @param array $datos      = arreglo con la informacion a adicionar
 */
function modificarItem($id, $datos = array()) {
    global $textos, $sql;
    
    if (empty($id) || (!empty($id) && !$sql->existeItem('cajas', 'id', $id))) {
        $respuesta              = array();
        $respuesta['error']     = true;
        $respuesta['mensaje']   = $textos->id('NO_HA_SELECCIONADO_ITEM');

        Servidor::enviarJSON($respuesta);
        return NULL;
    }    

    $objeto     = new Caja($id);
    $destino    = '/ajax' . $objeto->urlBase . '/edit';
    $respuesta  = array();

    if (empty($datos)) {

        $codigo  = HTML::campoOculto('procesar', 'true');
        $codigo .= HTML::campoOculto('id', $id);
        $codigo .= HTML::campoOculto('datos[dialogo]', '', 'idDialogo');
        $codigo .= HTML::parrafo($textos->id('SEDE'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[sede]', 40, 255, $objeto->sede, 'autocompletable campoObligatorio', '', array('title' => HTML::urlInterna('SEDES_EMPRESA', 0, true, 'listar')), $textos->id('AYUDA_USO_AUTOCOMPLETAR'), HTML::urlInterna('SEDES_EMPRESA', 0, true, 'add'), 'datos[id_sede]', $objeto->idSede);
        $codigo .= HTML::parrafo($textos->id('NOMBRE'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[nombre]', 40, 255, $objeto->nombre, 'campoObligatorio');
        $codigo .= HTML::parrafo(HTML::campoChequeo('datos[activo]', $objeto->activo) . $textos->id('ACTIVO'), 'margenSuperior');
        $codigo .= HTML::parrafo(HTML::campoChequeo('datos[principal]', $objeto->principal) . $textos->id('PRINCIPAL'), 'margenSuperior');
        $codigo .= HTML::parrafo(HTML::boton('chequeo', $textos->id('ACEPTAR'), 'botonOk', 'botonOk', 'botonOk'), 'margenSuperior');
        $codigo .= HTML::parrafo($textos->id('REGISTRO_MODIFICADO'), 'textoExitoso', 'textoExitoso');
        $codigo1 = HTML::forma($destino, $codigo, 'P');

        $respuesta['generar']       = true;
        $respuesta['codigo']        = $codigo1;
        $respuesta['titulo']        = HTML::parrafo($textos->id('MODIFICAR_ITEM'), 'letraBlanca negrilla subtitulo');
        $respuesta['destino']       = '#cuadroDialogo';
        $respuesta['ancho']         = 550;
        $respuesta['alto']          = 400;
        
    } else {

        $respuesta['error'] = true;

        $existeNombre = $sql->existeItem('cajas', 'nombre', $datos['nombre'], 'id != "' . $id . '" AND id_sede = "'.$datos['id_sede'].'"');

        if (empty($datos['id_sede'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_SEDE');
            
        } elseif ($existeNombre) {
            $respuesta['mensaje'] = $textos->id('ERROR_EXISTE_NOMBRE');
            
        } elseif (empty($datos['nombre'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_NOMBRE');
            
        } else {
            $idItem = $objeto->modificar($datos);
            if ($idItem) {
                /*                 * ************** Creo el nuevo item que se insertara via ajax *************** */
                $objeto     = new Caja($id);

                $estado     = ($objeto->activo) ? HTML::frase($textos->id('ACTIVO'), 'activo') : HTML::frase($textos->id('INACTIVO'), 'inactivo');
                $categoria  = ($objeto->principal) ? HTML::frase($textos->id('PRINCIPAL'), 'activo') : HTML::frase($textos->id('SECUNDARIA'), 'inactivo');

                $celdas     = array($objeto->sede, $objeto->nombre, $estado, $categoria);
                $celdas1    = HTML::crearFilaAModificar($celdas);

                $respuesta['error']             = false;
                $respuesta['accion']            = 'insertar';
                $respuesta['contenido']         = $celdas1;
                $respuesta['idContenedor']      = '#tr_' . $id;
                $respuesta['idDestino']         = '#tr_' . $id;

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
 * Funci�n con doble comportamiento. La primera llamada (con el parametro $confirmado vacio)
 * muestra el formulario de confirmaci�n de eliminaci�n del registro. El destino de este formulario es esta 
 * misma funci�n, pero una vez viene desde el formulario con el parametro $confirmado en "true"
 * se encarga de validar la informaci�n y llamar al metodo modificar del objeto.
 * 
 * @global recurso $textos  = objeto global de gestion de los textos de idioma
 * @global recurso $sql     = objeto global de interaccion con la BD
 * @param int $id           = id del registro a modificar
 * @param array $datos      = arreglo con la informacion a adicionar
 */
function eliminarItem($id, $confirmado, $dialogo) {
    global $textos, $sql;
    
    if (empty($id) || (!empty($id) && !$sql->existeItem('cajas', 'id', $id))) {
        $respuesta              = array();
        $respuesta['error']     = true;
        $respuesta['mensaje']   = $textos->id('NO_HA_SELECCIONADO_ITEM');

        Servidor::enviarJSON($respuesta);
        return NULL;
    }    

    $objeto     = new Caja($id);
    $destino    = '/ajax' . $objeto->urlBase . '/delete';
    $respuesta  = array();

    if (!$confirmado) {
        $titulo  = HTML::frase($objeto->nombre.' :: '.$objeto->sede, 'negrilla');
        $titulo1 = str_replace('%1', $titulo, $textos->id('CONFIRMAR_ELIMINACION'));
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
        
        $arreglo1 = array('cotizaciones',                   'id_caja = "'.$id.'"', $textos->id('COTIZACIONES'));//arreglo del que sale la info a consultar
        $arreglo2 = array('facturas_compras',               'id_caja = "'.$id.'"', $textos->id('FACTURAS_COMPRA'));
        $arreglo3 = array('facturas_venta',                 'id_caja = "'.$id.'"', $textos->id('FACTURAS_VENTA'));
        $arreglo4 = array('facturas_temporales_venta',      'id_caja = "'.$id.'"', $textos->id('FACTURAS_TEMPORALES_VENTA'));
        $arreglo5 = array('facturas_temporales_compra',     'id_caja = "'.$id.'"', $textos->id('FACTURAS_TEMPORALES_COMPRA'));
        $arreglo6 = array('ordenes_compra',                 'id_caja = "'.$id.'"', $textos->id('ORDENES_COMPRA'));
        
        $arregloIntegridad = array($arreglo1, $arreglo2, $arreglo3, $arreglo4, $arreglo5, $arreglo6);//arreglo de arreglos para realizar las consultas de integridad referencial, (ver documentacion de metodo)
        $integridad = Recursos::verificarIntegridad($textos->id('CAJA'), $arregloIntegridad);
        
        if ($integridad != '') {
            $respuesta['error']     = true;
            $respuesta['mensaje']   = $integridad;
            
        } else {        

            if ($objeto->eliminar()) {

                $respuesta['error'] = false;
                $respuesta['accion'] = 'insertar';
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
 * Funci�n que se encarga de realizar una busqueda de acuerdo a una condicion que se
 * le pasa. Es llamada cuando se ingresa un texto en el campo de busqueda en la pantalla principal del modulo.
 * Una vez es llamada esta funci�n, se encarga de recargar la tabla de registros con los datos coincidientes 
 * en el patr�n de busqueda.
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
        $respuesta['error']     =    true;
        $respuesta['mensaje']   =   $textos->id('ERROR_FALTA_CADENA_BUSQUEDA');
        
    } else if (!empty($datos) && strlen($datos) < 2) {
        $respuesta['error'] = true;
        $respuesta['mensaje'] = str_replace('%1', '2', $textos->id('ERROR_TAMA�O_CADENA_BUSQUEDA'));
        
    } else {
        $item           = '';
        $respuesta      = array();
        $objeto         = new Caja();
        $registros      = $configuracion['GENERAL']['registrosPorPagina'];
        
        if (!empty($cantidadRegistros)) {
            $registros = (int) $cantidadRegistros;
        }
        
        $pagina = 1;
        $registroInicial = 0;


        $palabras = explode(' ', $datos);

        $condicionales = $data[1];

        if ($condicionales == '') {
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

        $arregloItems = $objeto->listar($registroInicial, $registros, array('0'), $condicion, 'c.nombre');

        if ($objeto->registrosConsulta) {//si la consulta trajo registros
            $datosPaginacion = array($objeto->registrosConsulta, $registroInicial, $registros, $pagina, $objeto->registrosConsulta);
            $item .= $objeto->generarTabla($arregloItems, $datosPaginacion);
            $info = HTML::parrafo(str_replace('%', $objeto->registrosConsulta, $textos->id('RESULTADOS_BUSQUEDA')), 'textoExitosoNotificaciones');
            
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
function paginador($pagina, $orden = NULL, $nombreOrden = NULL, $consultaGlobal = NULL, $cantidadRegistros = NULL) 
{
    global $configuracion;

    $item       = '';
    $respuesta  = array();
    $objeto     = new Caja();

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

        $data = explode('[', $consultaGlobal);
        $datos = $data[0];
        $palabras = explode(' ', $datos);

        if ($data[1] != '') {
            $condicionales = explode('|', $data[1]);

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
 * Funcion que devuelve la respuesta para el autocompletar
 * 
 * @global recurso $sql     = objeto global de interaccion con la BD
 * @param string $cadena    = cadena de busqueda
 */
function listarItems($cadena) {
    global $sql;
    $respuesta  = array();
    $tablas     = array('c' => 'cajas', 's' => 'sedes_empresa');
    $columnas   = array('nombre' => 'c.nombre', 'sede' => 's.nombre');
    $consulta   = $sql->seleccionar($tablas, $columnas, "(c.nombre LIKE '%$cadena%') AND c.id_sede = s.id AND c.activo = '1' AND c.id != 0", "", "s.nombre ASC", 0, 20);

    while ($fila = $sql->filaEnObjeto($consulta)) {
        $respuesta[] = $fila->sede . " | " . $fila->nombre;
    }

    Servidor::enviarJSON($respuesta);
}

/**
 * Funcion Eliminar varios. llamada cuando se seleccionan varios registros y se presiona el
 * bot�n que aparece llamado "Eliminar varios"
 * 
 * @global boolean $confirmado  = objeto global de gestion de textos
 * @param int $cantidad         = cantidad a ser eliminada
 * @param string $cadenaItems   = cadena que tiene cada uno de los ides del objeto a ser eliminados, ejemplo se eliminan el objeto de id 1, 2, 3, la cadena ser�a (1,2,3)
 */
function eliminarVarios($confirmado, $cantidad, $cadenaItems) {
    global $textos;

    $destino    = '/ajax/cajas/eliminarVarios';
    $respuesta  = array();

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

        $cadenaIds = substr($cadenaItems, 0, -1);
        $arregloIds = explode(",", $cadenaIds);

        $eliminarVarios = true;
        foreach ($arregloIds as $val) {
            $objeto = new Caja($val);
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
 * Funcion encargada de seleccionar las cajas existentes en una sede.
 * Es llamada via ajax cuando se selecciona una sede del selector de sedes
 * e inmediatamente consulta las cajas pertenecientes a esa sede y las carga
 * en el selector de cajas. Utilizada en articulos en movimientos de mercancia, y en consultar kardex.
 *
 * @global recurso $sql = objeto global de interaccion con la BD
 * @param int $idSede   = identificador de la sede donde se van a escoger las cajas
 */
function escogerCaja($idSede) {
    global $sql, $textos;
    
    if (empty($idSede) || (!empty($idSede) && !$sql->existeItem('sedes_empresa', 'id', $idSede))) {
        $respuesta              = array();
        $respuesta['error']     = true;
        $respuesta['mensaje']   = $textos->id('NO_HA_SELECCIONADO_ITEM');

        Servidor::enviarJSON($respuesta);
        return NULL;
    }     

    $listaCajas   = array();
    $respuesta      = array();
    $consulta       = $sql->seleccionar(array('cajas'), array('id', 'nombre'), 'id_sede = "' . $idSede . '" AND id !="0"', '', 'nombre ASC');
    
    if ($sql->filasDevueltas) {
        while ($dato = $sql->filaEnObjeto($consulta)) {
            $listaCajas[$dato->id] = $dato->nombre;
            
        }
        
    }
    
    $selectores  = '';
    $selectores .= '<option value= "">Seleccionar caja...</option>';
    
    foreach ($listaCajas as $id => $valor) {
        $selectores .= '<option value= "' . $id . '">' . $valor . '</option>';
    }

    $respuesta['error']             = false;
    $respuesta['accion']            = 'insertar';
    $respuesta['contenido']         = $selectores;
    $respuesta['insertarNuevaFila'] = true;
    $respuesta['idDestino']         = '#selectorCajas';

    Servidor::enviarJSON($respuesta);
    
}
