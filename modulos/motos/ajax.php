<?php

/**
 * @package     FOM (Framework OpenSource Multiplatform)
 * @subpackage  Motos
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
        
        case 'search'           :   buscarItem($forma_datos, $forma_cantidadRegistros);
                                    break;
        
        case 'move'             :   paginador($forma_pagina, $forma_orden, $forma_nombreOrden, $forma_consultaGlobal, $forma_cantidadRegistros);
                                    break;
        
        case 'listar'           :   listarItems($url_cadena);
                                    break;
        
        case 'listarMotos'      :   listarMotos($url_cadena);
                                    break;
        
        case 'eliminarVarios'   :   $confirmado = ($forma_procesar) ? true : false;
                                    eliminarVarios($confirmado, $forma_cantidad, $forma_cadenaItems);
                                    break;
        
        case 'buscarCatalogos'  :   buscarCatalogos();
                                    break;
        
        case 'cargarCatalogos'  :   cargarCatalogos($forma_idMoto);
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
    
    if (empty($id) || (!empty($id) && !$sql->existeItem('motos', 'id', $id))) {
        $respuesta              = array();
        $respuesta['error']     = true;
        $respuesta['mensaje']   = $textos->id('NO_HA_SELECCIONADO_ITEM');

        Servidor::enviarJSON($respuesta);
        return NULL;
    }    

    $objeto = new Moto($id);
    $respuesta = array();

    $codigo      = HTML::parrafo($textos->id('NOMBRE').': '.HTML::frase($objeto->nombre, 'margenIzquierda'), 'negrilla margenSuperior');
    $codigo     .= HTML::parrafo($textos->id('CATALOGO').': '.HTML::frase($objeto->enlaceArchivo, 'margenIzquierda'), 'negrilla margenSuperior');
    $codigo     .= HTML::parrafo($textos->id('MARCA').': '.HTML::frase($objeto->marca, 'margenIzquierda'), 'negrilla margenSuperior');
    $codigo     .= HTML::parrafo(HTML::imagen($objeto->imagenMarca, 'margenIzquierda imagenMiniaturaMarca', ''), '', '');
    $codigo     .= HTML::parrafo($textos->id('IMAGEN'), 'negrilla margenSuperior');
    $codigo     .= HTML::enlace(HTML::imagen($objeto->imagenMiniatura, 'imagenItem', ''), $objeto->imagenPrincipal, '', '', array('rel' => 'prettyPhoto[]'));
    $codigo     .= HTML::parrafo($textos->id('ESTADO'), 'negrilla margenSuperior');
    $activo      = ($objeto->activo) ?  HTML::frase($textos->id('ACTIVO'), 'activo') : HTML::frase($textos->id('INACTIVO'), 'inactivo');
    $codigo     .= HTML::parrafo($activo, '', '');
    $codigo      = HTML::contenedor($codigo, 'margenIzquierda');

    $respuesta['generar']       = true;
    $respuesta['codigo']        = $codigo;
    $respuesta['titulo']        = HTML::parrafo($textos->id('CONSULTAR_ITEM'), 'letraBlanca negrilla subtitulo');
    $respuesta['destino']       = '#cuadroDialogo';
    $respuesta['ancho']         = 450;
    $respuesta['alto']          = 400;


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
    global $textos, $sql, $archivo_imagen, $archivo_archivo;

    $objeto = new Moto();
    $destino = '/ajax' . $objeto->urlBase . '/add';
    $respuesta = array();

    if (empty($datos)) {
        //Se agrega el campo oculto datos[dialogo] que va a ser el que almacene el id de la ventana de dialogo donde sera mostrado este formulario
        $codigo  = HTML::campoOculto('procesar', 'true');
        $codigo .= HTML::campoOculto('datos[dialogo]', '', 'idDialogo');
        $codigo .= HTML::parrafo($textos->id('MARCA'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[marca]', 40, 255, '', 'autocompletable campoObligatorio', '', array('title' => HTML::urlInterna('MARCAS', 0, true, 'listar')), $textos->id('AYUDA_USO_AUTOCOMPLETAR'), HTML::urlInterna('MARCAS', 0, true, 'add'), 'datos[id_marca]');
        $codigo .= HTML::parrafo($textos->id('NOMBRE'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[nombre]', 40, 255, '', 'campoObligatorio');
        $codigo .= HTML::parrafo($textos->id('IMAGEN'), 'negrilla margenSuperior');
        $codigo .= HTML::campoArchivo('imagen', 50, 255);
        $codigo .= HTML::parrafo($textos->id('CATALOGO'), 'negrilla margenSuperior');
        $codigo .= HTML::campoArchivo('archivo', 50, 255);        
        $codigo .= HTML::parrafo(HTML::campoChequeo('datos[activo]', true) . $textos->id('ACTIVO'), 'margenSuperior');
        $codigo .= HTML::parrafo(HTML::boton('chequeo', $textos->id('ACEPTAR'), '', '', ''), 'margenSuperior');
        $codigo .= HTML::parrafo($textos->id('REGISTRO_AGREGADO'), 'textoExitoso', 'textoExitoso');
        $codigo1 = HTML::forma($destino, $codigo, 'P', true);

        $respuesta['generar']   = true;
        $respuesta['codigo']    = $codigo1;
        $respuesta['titulo']    = HTML::parrafo($textos->id('ADICIONAR_ITEM'), 'letraBlanca negrilla subtitulo');
        $respuesta['destino']   = '#cuadroDialogo';
        $respuesta['ancho']     = 550;
        $respuesta['alto']      = 400;
        
    } else {

        $respuesta['error'] = true;

        $existeNombre   = $sql->existeItem('motos', 'nombre', $datos['nombre'], 'id_marca = "'.$datos['id_marca'].'"');
        $existeMarca    = $sql->existeItem('marcas', 'nombre', $datos['marca']);
        

        if (!empty($archivo_archivo['tmp_name'])) {
            $validarFormatoArchivo = Archivo::validarArchivo($archivo_archivo, array('doc', 'docx', 'pdf', 'ppt', 'pptx', 'pps', 'ppsx', 'xls', 'xlsx', 'odt', 'rtf', 'txt', 'ods', 'odp', 'jpg', 'jpeg', 'png'));       
        }        
        if (!empty($archivo_imagen['tmp_name'])) {
            $validarFormato = Recursos::validarArchivo($archivo_imagen, array('jpg', 'jpeg', 'png', 'gif', 'jpeg'));
        }
        
        
        if (empty($datos['marca'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_MARCA');
            
        } else if (!empty($datos['marca']) && !$existeMarca) {
            $respuesta['mensaje'] = $textos->id('ERROR_MARCA');
            
        } else if (empty($datos['nombre'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_NOMBRE');
            
        } else if ($validarFormatoArchivo) {
            $respuesta['mensaje'] = $textos->id('ERROR_FORMATO_ARCHIVO');
            
        } else if ($existeNombre) {
            $respuesta['mensaje'] = $textos->id('ERROR_EXISTE_NOMBRE');
            
        } else if ($validarFormato) {
            $respuesta['mensaje'] = $textos->id('ERROR_FORMATO_IMAGEN');
            
        } else {
            $idItem = $objeto->adicionar($datos);
            
            if ($idItem) {
                /*                 * ************** Creo el nuevo item que se insertara via ajax *************** */
                $objeto = new Moto($idItem);

                $estado =  ($objeto->activo) ? HTML::frase($textos->id('ACTIVO'), 'activo') : HTML::frase($textos->id('INACTIVO'), 'inactivo');
                
                $celdas         = array($objeto->id, $objeto->nombre, $objeto->marca, $objeto->enlaceArchivo, $estado);
                $claseFila      = '';
                $idFila         = $idItem;
                $celdas1        = HTML::crearNuevaFila($celdas, $claseFila, $idFila);
                

                    $respuesta['error']         = false;
                    $respuesta['accion']        = 'insertar';
                    $respuesta['contenido']     = $celdas1;
                    $respuesta['idContenedor']  = '#tr_' . $idItem;
                    $respuesta['idDestino']     = '#tablaRegistros';                

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
function modificarItem($id, $datos = array()) {
    global $textos, $sql, $archivo_imagen, $archivo_archivo;
    
    if (empty($id) || (!empty($id) && !$sql->existeItem('motos', 'id', $id))) {
        $respuesta              = array();
        $respuesta['error']     = true;
        $respuesta['mensaje']   = $textos->id('NO_HA_SELECCIONADO_ITEM');

        Servidor::enviarJSON($respuesta);
        return NULL;
    }    

    $objeto     = new Moto($id);
    $destino    = '/ajax' . $objeto->urlBase . '/edit';
    $respuesta  = array();

    if (empty($datos)) {

        $codigo  = HTML::campoOculto('procesar', 'true');
        $codigo .= HTML::campoOculto('id', $id);
        $codigo .= HTML::campoOculto('datos[dialogo]', '', 'idDialogo');
        $codigo .= HTML::parrafo($textos->id('MARCA'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[marca]', 40, 255, $objeto->marca, 'autocompletable campoObligatorio', '', array('title' => HTML::urlInterna('MARCAS', 0, true, 'listar')), $textos->id('AYUDA_USO_AUTOCOMPLETAR'), HTML::urlInterna('MARCAS', 0, true, 'add'), 'datos[id_marca]', $objeto->idMarca);
        $codigo .= HTML::parrafo($textos->id('NOMBRE'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[nombre]', 40, 255, $objeto->nombre, 'campoObligatorio');
        $codigo .= HTML::parrafo($textos->id('IMAGEN'), 'negrilla margenSuperior');
        $codigo .= HTML::campoArchivo('imagen', 50, 255) . HTML::imagen($objeto->imagenMiniatura, 'imagenMarca', 'margenIzquierda');
        $codigo .= HTML::parrafo($textos->id('CATALOGO'), 'negrilla margenSuperior');
        $codigo .= HTML::campoArchivo('archivo', 50, 255) . HTML::parrafo($objeto->enlaceArchivo, 'estiloEnlace margenSuperior', '');        
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

        $existeNombre = $sql->existeItem('motos', 'nombre', $datos['nombre'], 'id != "' . $id . '" AND id_marca = "'.$datos['id_marca'].'"');
        $existeMarca = $sql->existeItem('marcas', 'nombre', $datos['marca']);

        if (!empty($archivo_imagen['tmp_name'])) {
            $validarFormato = Recursos::validarArchivo($archivo_imagen, array('jpg', 'jpeg', 'png', 'gif', 'jpeg'));
//            $area = getimagesize($archivo_imagen['tmp_name']);
        }
        if (!empty($archivo_archivo['tmp_name'])) {
            $validarFormatoArchivo = Archivo::validarArchivo($archivo_archivo, array('doc', 'docx', 'pdf', 'ppt', 'pptx', 'pps', 'ppsx', 'xls', 'xlsx', 'odt', 'rtf', 'txt', 'ods', 'odp', 'jpg', 'jpeg', 'png'));       
        }        

        if (empty($datos['marca'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_MARCA');
            
        } elseif (!empty($datos['marca']) && !$existeMarca) {
            $respuesta['mensaje'] = $textos->id('ERROR_MARCA');
            
        } elseif (empty($datos['nombre'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_NOMBRE');
            
        } elseif ($validarFormatoArchivo) {
            $respuesta['mensaje'] = $textos->id('ERROR_FORMATO_ARCHIVO');
            
        } elseif ($existeNombre) {
            $respuesta['mensaje'] = $textos->id('ERROR_EXISTE_NOMBRE');
            
        } elseif ($validarFormato) {
            $respuesta['mensaje'] = $textos->id('ERROR_FORMATO_IMAGEN');
            
        } else {
            $idItem = $objeto->modificar($datos);
            if ($idItem) {
                /*                 * ************** Creo el nuevo item que se insertara via ajax *************** */
                $objeto = new Moto($id);

                $estado = ($objeto->activo) ? HTML::frase($textos->id('ACTIVO'), 'activo') : HTML::frase($textos->id('INACTIVO'), 'inactivo');
                    
      
                $celdas = array($objeto->id, $objeto->nombre, $objeto->marca, $objeto->enlaceArchivo, $estado);
                $celdas1 = HTML::crearFilaAModificar($celdas);
                
                $respuesta['error']             = false;
                $respuesta['accion']            = 'insertar';
                $respuesta['contenido']         = $celdas1;
                $respuesta['idContenedor']      = '#tr_' . $id;
                $respuesta['idDestino']         = '#tr_' . $id;                

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
 * se encarga de validar la información y llamar al metodo modificar del objeto.
 * 
 * @global recurso $textos  = objeto global de gestion de los textos de idioma
 * @global recurso $sql     = objeto global de interaccion con la BD
 * @param int $id           = id del registro a modificar
 * @param array $datos      = arreglo con la informacion a adicionar
 */
function eliminarItem($id, $confirmado, $dialogo) {
    global $textos, $sql;
    
    if (empty($id) || (!empty($id) && !$sql->existeItem('motos', 'id', $id))) {
        $respuesta              = array();
        $respuesta['error']     = true;
        $respuesta['mensaje']   = $textos->id('NO_HA_SELECCIONADO_ITEM');

        Servidor::enviarJSON($respuesta);
        return NULL;
    }    

    $objeto     = new Moto($id);
    $destino    = '/ajax' . $objeto->urlBase . '/delete';
    $respuesta  = array();

    if (!$confirmado) {
        $titulo  = HTML::frase($objeto->nombre, 'negrilla');
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

        $arreglo1 = array('articulos',    'id_moto = "'.$id.'"', $textos->id('ARTICULOS'));//arreglo del que sale la info a consultar
        $arreglo2 = array('articulo_moto',     'id_moto = "'.$id.'"', $textos->id('ARTICULOS'));
        
        $arregloIntegridad = array($arreglo1, $arreglo2, $arreglo3, $arreglo4, $arreglo5, $arreglo6);//arreglo de arreglos para realizar las consultas de integridad referencial, (ver documentacion de metodo)
        $integridad = Recursos::verificarIntegridad($textos->id('MOTO'), $arregloIntegridad);
        
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
        $item           = '';
        $respuesta      = array();
        $objeto         = new Moto();
        $registros      = $configuracion['GENERAL']['registrosPorPagina'];
        
        if (!empty($cantidadRegistros)) {
            $registros = (int) $cantidadRegistros;
            
        }        
        $pagina             = 1;
        $registroInicial    = 0;


        $palabras = explode(' ', $datos);

        $condicionales = $data[1];

        if ($condicionales == '') {
            $condicion = '(m.nombre REGEXP "(' . implode('|', $palabras) . ')")';
            
        } else {
            $condicionales = explode('|', $condicionales);

            $condicion  = '(';
            $tam        = sizeof($condicionales) - 1;
            
            for ($i = 0; $i < $tam; $i++) {
                $condicion .= $condicionales[$i] . ' REGEXP "(' . implode('|', $palabras) . ')" ';
                if ($i != $tam - 1) {
                    $condicion .= ' OR ';
                    
                }
            }
            
            $condicion .= ')';
            
        }

        $arregloItems = $objeto->listar($registroInicial, $registros, array('0', '999'), $condicion, 'm.nombre');

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
    $objeto         = new Moto();

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
            $consultaGlobal = '(m.nombre REGEXP "(' . implode('|', $palabras) . ')")';
            
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


    $arregloItems = $objeto->listar($registroInicial, $registros, array('0', '999'), $consultaGlobal, $nombreOrden);

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
    $respuesta = array();

    $consulta = $sql->seleccionar(array('motos'), array('id', 'nombre'), 'nombre LIKE "%' . $cadena . '%"  AND activo = "1"', '', 'nombre ASC', 0, 20);

    while ($fila = $sql->filaEnObjeto($consulta)) {
        $respuesta1 = array();
        $respuesta1['label']    = $fila->nombre;
        $respuesta1['value']    = $fila->id;
        $respuesta[]            = $respuesta1;
    }

    Servidor::enviarJSON($respuesta);
}

/**
 * Funcion que devuelve la respuesta para el autocompletar
 * @global type $sql
 * @param type $cadena 
 */
function listarMotos($cadena) {
    global $sql;
    $respuesta = array();

    $tablas = array(
        'm'     => 'motos',
        'ma'    => 'marcas'
    );
    $columnas = array(
        'id'        => 'm.id',
        'nombre'    => 'm.nombre',
        'marca'     => 'ma.nombre'
    );
    $condicion = 'm.id_marca = ma.id AND m.nombre LIKE "%' . $cadena . '%"  AND m.activo = "1"';

    $consulta = $sql->seleccionar($tablas, $columnas, $condicion, '', 'ma.nombre ASC', 0, 20);

    while ($fila = $sql->filaEnObjeto($consulta)) {
        $respuesta1 = array();
        $respuesta1['label']    = $fila->marca . HTML::frase(' - ', 'margenIzquierda') . HTML::frase($fila->nombre, 'flotanteDerecha margenDerechaDoble');
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


    $destino = '/ajax/motos/eliminarVarios';
    $respuesta = array();

    if (!$confirmado) {
        $titulo = HTML::frase($cantidad, 'negrilla');
        $titulo1 = str_replace('%1', $titulo, $textos->id('CONFIRMAR_ELIMINACION_VARIOS'));
        $codigo = HTML::campoOculto('procesar', 'true');
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

        $cadenaIds = substr($cadenaItems, 0, -1);
        $arregloIds = explode(',', $cadenaIds);

        $eliminarVarios = true;
        foreach ($arregloIds as $val) {
            $objeto = new Moto($val);
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
 *
 * @global type $textos
 * @global type $sql
 * @global type $archivo_imagen
 * @param type $datos 
 */
function buscarCatalogos() {
    global $textos, $configuracion;

    $respuesta = array();
    
    $codigo .= HTML::campoOculto('datos[dialogo]', '', 'idDialogo');

    $codigo2 .= HTML::parrafo($textos->id('MOTO'), 'negrilla margenSuperior', 'textoBusquedaMarca');
    $codigo2 .= HTML::campoTexto('datos[moto]', 50, 100, '', 'autocompletable campoObligatorio', 'campoTextoBusquedaCatalogos', array('title' => '/ajax/motos/listarMotos'), $textos->id('AYUDA_BUSCAR_CATALOGO'));


    $respuesta['generar']       = true;
    $respuesta['cargarJs']      = true;
    $respuesta['archivoJs']     = $configuracion['SERVIDOR']['media'] . $configuracion['RUTAS']['javascript'] . '/modulos/motos/funcionesBuscarCatalogo.js';
    $respuesta['codigo']        = $codigo2;
    $respuesta['titulo']        = HTML::parrafo($textos->id('BUSCAR_CATALOGOS'), 'letraBlanca negrilla subtitulo');
    $respuesta['destino']       = '#cuadroDialogo';
    $respuesta['ancho']         = 498;
    $respuesta['alto']          = 250;


    Servidor::enviarJSON($respuesta);
}

/**
 *
 * @global type $textos
 * @global type $sql
 * @global type $archivo_imagen
 * @param type $datos 
 */
function cargarCatalogos($idMoto) {
    global $sql, $configuracion;

    $respuesta = array();
    
    $catalogo = $sql->obtenerValor("motos", "archivo", "id = '".$idMoto."'");
    
    sleep(2);//mostrar el efecto del piñon :)
    
    $respuesta['accion']     = "abrir_ubicacion";
    $respuesta['destino']    = $configuracion['RUTAS']['media'] . $configuracion['RUTAS']['archivosCatalogos'].$catalogo;


    Servidor::enviarJSON($respuesta);
    
}