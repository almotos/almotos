<?php

/**
 * @package     FOM (Framework OpenSource Multiplatform)
 * @subpackage  Tipos de Documento
 * @author      Pablo Andres Velez Vidal <pavelez@genesyscorp.com.co>
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2013 GENESYS
 * @version     0.1 
 * */
global $url_accion, $forma_procesar, $forma_id, $forma_datos, $forma_cantidadRegistros, $forma_cantidad, $forma_cadenaItems, $forma_pagina, $forma_orden, $forma_nombreOrden, $url_cadena, $forma_dialogo, $forma_consultaGlobal;


if (isset($url_accion)) {
    switch ($url_accion) {
        case 'add' : $datos = ($forma_procesar) ? $forma_datos : array();
            adicionarItem($datos);
            break;
        case 'see' : cosultarItem($forma_id);
            break;
        case 'edit' : $datos = ($forma_procesar) ? $forma_datos : array();
            modificarItem($forma_id, $datos);
            break;
        case 'delete' : $confirmado = ($forma_procesar) ? true : false;
            eliminarItem($forma_id, $confirmado, $forma_dialogo);
            break;
        case 'search' : buscarItem($forma_datos, $forma_cantidadRegistros);
            break;
        case 'move' : paginador($forma_pagina, $forma_orden, $forma_nombreOrden, $forma_consultaGlobal, $forma_cantidadRegistros);
            break;
        case 'listar' : listarItems($url_cadena);
            break;
        case 'eliminarVarios' : $confirmado = ($forma_procesar) ? true : false;
            eliminarVarios($confirmado, $forma_cantidad, $forma_cadenaItems);
            break;
    }
}

/**
 *
 * @global type $textos
 * @param type $id 
 */
function cosultarItem($id) {
    global $textos;

    $objeto = new TipoDocumento($id);
    $respuesta = array();

    $codigo = HTML::campoOculto('procesar', 'true');
    $codigo .= HTML::campoOculto('id', $id);
    $codigo .= HTML::parrafo($textos->id('CODIGO_DIAN'), 'negrilla margenSuperior');
    $codigo .= HTML::parrafo($objeto->codigoDian, '', '');
    $codigo .= HTML::parrafo($textos->id('NOMBRE'), 'negrilla margenSuperior');
    $codigo .= HTML::parrafo($objeto->nombre, '', '');
    $codigo .= HTML::parrafo($textos->id('ESTADO'), 'negrilla margenSuperior');
    $activo = ($objeto->activo) ? HTML::frase($textos->id('ACTIVO'), 'activo') : HTML::frase($textos->id('INACTIVO'), 'inactivo');
    $codigo .= HTML::parrafo($activo, '', '');

    $respuesta['generar'] = true;
    $respuesta['codigo'] = $codigo;
    $respuesta['titulo'] = HTML::parrafo($textos->id('CONSULTAR_ITEM'), 'letraBlanca negrilla subtitulo');
    $respuesta['destino'] = '#cuadroDialogo';
    $respuesta['ancho'] = 450;
    $respuesta['alto'] = 300;



    Servidor::enviarJSON($respuesta);
}

//Fin del metodo consultar

/**
 * Metodo adicionar 
 * */
function adicionarItem($datos = array()) {
    global $textos, $sql, $modulo, $sesion_usuarioSesion;
        
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

    $objeto = new TipoDocumento();
    $destino = '/ajax' . $objeto->urlBase . '/add';
    $respuesta = array();

    if (empty($datos)) {

        $codigo = HTML::campoOculto('procesar', 'true');
        $codigo .= HTML::campoOculto('datos[dialogo]', '', 'idDialogo');
        $codigo .= HTML::parrafo($textos->id('CODIGO_DIAN'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[codigo_dian]', 20, 3, '', 'soloNumeros', '', '', str_replace('%1', '3', $textos->id('TEXTO_NUMERICO_DE_X_CARACTERES')));
        $codigo .= HTML::parrafo($textos->id('NOMBRE'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[nombre]', 40, 255);
        $codigo .= HTML::parrafo(HTML::campoChequeo('datos[activo]', true) . $textos->id('ACTIVO'), 'margenSuperior');
        $codigo .= HTML::parrafo(HTML::boton('chequeo', $textos->id('ACEPTAR'), 'botonOk', 'botonOk', 'botonOk'), 'margenSuperior');
        $codigo .= HTML::parrafo(HTML::frase($textos->id('REGISTRO_AGREGADO'), 'textoExitoso', 'textoExitoso'), 'margenSuperior');
        $codigo1 = HTML::forma($destino, $codigo, 'P');

        $respuesta['generar'] = true;
        $respuesta['codigo'] = $codigo1;
        $respuesta['titulo'] = HTML::parrafo($textos->id('ADICIONAR_ITEM'), 'letraBlanca negrilla subtitulo');
        $respuesta['destino'] = '#cuadroDialogo';
        $respuesta['ancho'] = 450;
        $respuesta['alto'] = 300;
    } else {

        $respuesta['error'] = true;

        $existeCodigo = $sql->existeItem('tipos_documento', 'codigo_dian', $datos['codigo_dian']);
        $existeNombre = $sql->existeItem('tipos_documento', 'nombre', $datos['nombre']);

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
                $objeto = new TipoDocumento($idItem);

                $estado = ($objeto->activo) ? HTML::frase($textos->id('ACTIVO'), 'activo') : HTML::frase($textos->id('INACTIVO'), 'inactivo');

                $celdas = array($objeto->codigoDian, $objeto->nombre, $estado);
                $claseFila = '';
                $idFila = $idItem;
                $celdas1 = HTML::crearNuevaFila($celdas, $claseFila, $idFila);

                $respuesta['error'] = false;
                $respuesta['accion'] = 'insertar';
                $respuesta['contenido'] = $celdas1;
                $respuesta['idContenedor'] = '#tr_' . $idItem;
                $respuesta['idDestino'] = '#tablaRegistros';

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
 * Metodo Funcion
 * @global type $textos
 * @global type $sql
 * @param type $id
 * @param type $datos 
 */
function modificarItem($id, $datos = array()) {
    global $textos, $sql, $modulo, $sesion_usuarioSesion;
    
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

    $objeto = new TipoDocumento($id);
    $destino = '/ajax' . $objeto->urlBase . '/edit';
    $respuesta = array();

    if (empty($datos)) {
        $codigo = HTML::campoOculto('procesar', 'true');
        $codigo .= HTML::campoOculto('id', $id);
        $codigo .= HTML::campoOculto('datos[dialogo]', '', 'idDialogo');
        $codigo .= HTML::parrafo($textos->id('CODIGO_DIAN'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[codigo_dian]', 20, 3, $objeto->codigoDian, 'soloNumeros', '', '', str_replace('%1', '3', $textos->id('TEXTO_NUMERICO_DE_X_CARACTERES')));
        $codigo .= HTML::parrafo($textos->id('NOMBRE'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[nombre]', 40, 255, $objeto->nombre);
        $codigo .= HTML::parrafo(HTML::campoChequeo('datos[activo]', $objeto->activo) . $textos->id('ACTIVO'), 'margenSuperior');
        $codigo .= HTML::parrafo(HTML::boton('chequeo', $textos->id('ACEPTAR'), 'botonOk', 'botonOk', 'botonOk'), 'margenSuperior');
        $codigo .= HTML::parrafo(HTML::frase($textos->id('REGISTRO_MODIFICADO'), 'textoExitoso', 'textoExitoso'), 'margenSuperior');
        $codigo1 = HTML::forma($destino, $codigo, 'P');


        $respuesta['generar'] = true;
        $respuesta['codigo'] = $codigo1;
        $respuesta['titulo'] = HTML::parrafo($textos->id('MODIFICAR_ITEM'), 'letraBlanca negrilla subtitulo');
        $respuesta['destino'] = '#cuadroDialogo';
        $respuesta['ancho'] = 450;
        $respuesta['alto'] = 300;
    } else {

        $respuesta['error'] = true;

        $existeCodigo = $sql->existeItem('tipos_documento', 'codigo_dian', $datos['codigo_dian'], 'id != "' . $id . '"');
        $existeNombre = $sql->existeItem('tipos_documento', 'nombre', $datos['nombre'], 'id != "' . $id . '"');

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
                $objeto = new TipoDocumento($id);

                $estado = ($objeto->activo) ? HTML::frase($textos->id('ACTIVO'), 'activo') : HTML::frase($textos->id('INACTIVO'), 'inactivo');

                $celdas = array($objeto->codigoDian, $objeto->nombre, $estado);
                $celdas1 = HTML::crearFilaAModificar($celdas);

                $respuesta['error'] = false;
                $respuesta['accion'] = 'insertar';
                $respuesta['contenido'] = $celdas1;
                $respuesta['idContenedor'] = '#tr_' . $id;
                $respuesta['idDestino'] = '#tr_' . $id;

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
 * Funcion Eliminar
 * @global type $textos
 * @param type $id
 * @param type $confirmado 
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
    
    $objeto = new TipoDocumento($id);
    $destino = '/ajax' . $objeto->urlBase . '/delete';
    $respuesta = array();

    if (!$confirmado) {
        $titulo = HTML::frase($objeto->nombre, 'negrilla');
        $titulo1 = str_replace('%1', $titulo, $textos->id('CONFIRMAR_ELIMINACION'));
        $codigo = HTML::campoOculto('procesar', 'true');
        $codigo .= HTML::campoOculto('id', $id);
        $codigo .= HTML::campoOculto('datos[dialogo]', '', 'idDialogo');
        $codigo .= HTML::parrafo($titulo1);
        $codigo .= HTML::parrafo(HTML::boton('chequeo', $textos->id('ACEPTAR'), '', 'botonOk', 'botonOk'), 'margenSuperior');
        $codigo .= HTML::parrafo($textos->id('REGISTRO_ELIMINADO'), 'textoExitoso', 'textoExitoso');
        $codigo1 = HTML::forma($destino, $codigo);

        $respuesta['generar'] = true;
        $respuesta['codigo'] = $codigo1;
        $respuesta['destino'] = '#cuadroDialogo';
        $respuesta['titulo'] = HTML::parrafo($textos->id('ELIMINAR_ITEM'), 'letraBlanca negrilla subtitulo');
        $respuesta['ancho'] = 350;
        $respuesta['alto'] = 150;
        
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

    $data = explode('[', $data);
    $datos = $data[0];

    if (empty($datos)) {
        $respuesta['error'] = true;
        $respuesta['mensaje'] = $textos->id('ERROR_FALTA_CADENA_BUSQUEDA');
    } else if (!empty($datos) && strlen($datos) < 2) {
        $respuesta['error'] = true;
        $respuesta['mensaje'] = str_replace('%1', '2', $textos->id('ERROR_TAMAÑO_CADENA_BUSQUEDA'));
    } else {
        $item = '';
        $respuesta = array();
        $objeto = new TipoDocumento();
        $registros = $configuracion['GENERAL']['registrosPorPagina'];
        if (!empty($cantidadRegistros)) {
            $registros = (int) $cantidadRegistros;
        }
        $pagina = 1;
        $registroInicial = 0;


        $palabras = explode(' ', $datos);

        $condicionales = $data[1];

        if ($condicionales == '') {
            $condicion = '(td.nombre REGEXP "(' . implode('|', $palabras) . ')")';
        } else {
            //$condicion = str_replace(']', ''', $data[1]);
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

        $arregloItems = $objeto->listar($registroInicial, $registros, array('0'), $condicion, 'td.nombre');

        if ($objeto->registrosConsulta) {//si la consulta trajo registros
            $datosPaginacion = array($objeto->registrosConsulta, $registroInicial, $registros, $pagina, $objeto->registrosConsulta);
            $item .= $objeto->generarTabla($arregloItems, $datosPaginacion);
            $info = HTML::parrafo('Tu busqueda trajo ' . $objeto->registrosConsulta . ' resultados', 'textoExitosoNotificaciones');
        } else {
            $datosPaginacion = 0;
            $item .= $objeto->generarTabla($textos->id('NO_HAY_REGISTROS'), $datosPaginacion);
            $info = HTML::parrafo('Tu busqueda no trajo resultados, por favor intenta otra busqueda', 'textoErrorNotificaciones');
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

/**
 * 
 * @global type $configuracion
 * @param type $pagina
 * @param type $orden
 * @param type $nombreOrden
 * @param type $consultaGlobal
 * @param type $cantidadRegistros
 */
function paginador($pagina, $orden = NULL, $nombreOrden = NULL, $consultaGlobal = NULL, $cantidadRegistros = NULL) {
    global $configuracion;

    $item = '';
    $respuesta = array();
    $objeto = new TipoDocumento();

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
            $consultaGlobal = '(td.nombre REGEXP "(' . implode('|', $palabras) . ')")';
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

    $respuesta['error'] = false;
    $respuesta['accion'] = 'insertar';
    $respuesta['contenido'] = $item;
    $respuesta['idContenedor'] = '#tablaRegistros';
    $respuesta['idDestino'] = '#contenedorTablaRegistros';
    $respuesta['paginarTabla'] = true;

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
    $consulta = $sql->seleccionar(array('tipos_documento'), array('nombre'), '(nombre LIKE "%' . $cadena . '%") AND activo = "1"', '', 'nombre ASC', 0, 20);

    while ($fila = $sql->filaEnObjeto($consulta)) {
        $respuesta[] = $fila->nombre;
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
    global $textos, $modulo, $sesion_usuarioSesion;
    
    /**
    * Verificar si el usuario que esta en la sesion tiene permisos para esta accion
    */
    $puedeEliminarMasivo = Perfil::verificarPermisosBoton('botonEliminarMasivoTipos_documento', $modulo->nombre);
    
    if(!$puedeEliminarMasivo && $sesion_usuarioSesion->id != 0) {
        $respuesta            = array();
        $respuesta['error']   = true;
        $respuesta['mensaje'] = $textos->id('ACCESO_DENEGADO');
        
        Servidor::enviarJSON($respuesta);
        return FALSE;
        
    }

    $destino   = '/ajax/tipos_documento/eliminarVarios';
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
            $objeto = new TipoDocumento($val);
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
