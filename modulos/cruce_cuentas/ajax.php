<?php

/**
 * @package     FOM (Framework OpenSource Multiplatform)
 * @subpackage  Cruce de Cuentas
 * @author      Pablo Andres Velez Vidal <pavelez@genesyscorp.com.co>
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2013 GENESYS
 * @version     0.1
 * 
 * */
if (isset($url_accion)) {
    
    switch ($url_accion) {
        case 'add'                      :   $datos = ($forma_procesar) ? $forma_datos : array();
                                            adicionarItem($datos);
                                            break;
        
        case 'see'                      :   cosultarItem($forma_id);
                                            break;
        
        case 'edit'                     :   $datos = ($forma_procesar) ? $forma_datos : array();
                                            modificarItem($forma_id, $datos);
                                            break;
        
        case 'delete'                   :   $confirmado = ($forma_procesar) ? true : false;
                                            eliminarItem($forma_id, $confirmado, $forma_dialogo);
                                            break;

        case 'search'                   :   buscarItem($forma_datos, $forma_cantidadRegistros);
                                            break;
        
        case 'move'                     :   paginador($forma_pagina, $forma_orden, $forma_nombreOrden, $forma_consultaGlobal, $forma_cantidadRegistros);
                                            break;
        
        case 'listar'                   :   listarItems($url_cadena);
                                            break;
        
        case 'eliminarVarios'           :   $confirmado = ($forma_procesar) ? true : false;
                                            eliminarVarios($confirmado, $forma_cantidad, $forma_cadenaItems);
                                            break;
                                        
        case 'adicionarCuenta'          :   $datos = ($forma_procesar) ? $forma_datos : array();
                                            adicionarCuenta($forma_id, $datos);
                                            break;
        
        case 'eliminarCuenta'           :   $confirmado = ($forma_procesar) ? true : false;
                                            eliminarCuenta($forma_id, $confirmado, $forma_dialogo);
                                            break;                                        
        
    }
    
}

/**
 * Consultar un cruce de cuentas
 * 
 * @global object $textos objeto global de gestión de textos
 * @param int $id identificador del objeto que sera consultado
 */
function cosultarItem($id) {
    global $textos, $sql;

    $objeto         = new TipoCompra($id);
    $respuesta      = array();

    $codigo  = HTML::campoOculto('procesar', 'true');
    $codigo .= HTML::campoOculto('id', $id);
    
    $codigo1  = HTML::parrafo($textos->id('TIPO'), 'negrilla margenSuperior');
    $codigo1 .= HTML::parrafo($textos->id('TIPO_'.$objeto->tipo), '', '');    
    $codigo1 .= HTML::parrafo($textos->id('NOMBRE'), 'negrilla margenSuperior');
    $codigo1 .= HTML::parrafo($objeto->nombre, '', '');
    $codigo1 .= HTML::parrafo($textos->id('DESCRIPCION'), 'negrilla margenSuperior');
    $codigo1 .= HTML::parrafo($objeto->descripcion, '', '');   

    $codigo1 .= HTML::parrafo($textos->id('ESTADO'), 'negrilla margenSuperior');
    $activo = ($objeto->activo) ? HTML::frase($textos->id('ACTIVO'), 'activo') : HTML::frase($textos->id('INACTIVO'), 'inactivo');
    $codigo1 .= HTML::parrafo($activo, '', '');
    $principal = ($objeto->principal) ? HTML::parrafo($textos->id('PRINCIPAL'), 'activo margenSuperior') : HTML::parrafo($textos->id('SECUNDARIA'), 'inactivo margenSuperior');
    $codigo1 .= HTML::parrafo($principal, '', '');    
    
    $pestana1 = HTML::contenedor($codigo1, 'pestana1');
    
    if (!empty($objeto->listaCuentas)) {
        //crear los formularios con la info para las demas sedes
        $datosTablaCuentas = array(
            HTML::parrafo($textos->id('CUENTA'), 'centrado')        => 'cuenta',
            HTML::parrafo($textos->id('CODIGO_CONTABLE'), 'centrado')        => 'codigoCuenta',
            HTML::parrafo($textos->id('TIPO'), 'centrado')          => 'tipoCuenta',
            HTML::parrafo($textos->id('ELIMINAR'), 'centrado')      => 'botonEliminar',
        );

        $rutas = array(
            'ruta_modificar'    => '/ajax/cruce_cuentas/modificarCuenta',
            'ruta_eliminar'     => '/ajax/cruce_cuentas/eliminarCuenta',
        );

        $listaCuentas = array();
        
        while ($fila = $sql->filaEnObjeto($objeto->listaCuentas)) {
            $fila->tipoCuenta       = $textos->id('TIPO_CUENTA_' . $fila->tipoCuenta);
            $fila->botonEliminar    = HTML::contenedor('', 'eliminarRegistro');     
            
            $listaCuentas[] = $fila;
            
        }

        $idTabla            = 'tablaCuentasContables';
        $estilosColumnas    = array();
        $contenedorCuentas  = HTML::contenedor(Recursos::generarTablaRegistrosInterna($listaCuentas, $datosTablaCuentas, $rutas, $idTabla, $estilosColumnas), 'flotanteIzquierda margenDerecha');
    }
    
    $pestana2 = HTML::contenedor($contenedorCuentas, 'pestana1');   

    $pestanas = array(
        HTML::frase($textos->id('INFO_TIPO_COMPRA'), 'letraBlanca')    => $pestana1,
        HTML::frase($textos->id('LISTA_CUENTAS'), 'letraBlanca')       => $pestana2
    );

    $codigo .= HTML::pestanas2('consultarTipoCompra', $pestanas); //al id concatenarle la fecha con segundos para que no haya problema
    
    $respuesta['generar']       = true;
    $respuesta['codigo']        = $codigo;
    $respuesta['titulo']        = HTML::parrafo($textos->id('CONSULTAR_ITEM'), 'letraBlanca negrilla subtitulo');
    $respuesta['destino']       = '#cuadroDialogo';
    $respuesta['ancho']         = 650;
    $respuesta['alto']          = 450;

    Servidor::enviarJSON($respuesta);
    
}

/**
 * 
 * @global type $textos
 * @global type $sql
 * @param type $datos
 */
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

    $objeto         = new TipoCompra();
    $destino        = '/ajax' . $objeto->urlBase . '/add';
    $respuesta      = array();

    if (empty($datos)) {
        //creo una lista desplegable para el cruce de cuentas
        $listaDesplegableTipo = HTML::listaDesplegable('datos[tipo]', array('1' => $textos->id('TIPO_1'), '2' => $textos->id('TIPO_2'), '3' => $textos->id('TIPO_3')), '', '', 'listaTipoGrupo', '', '', $textos->id('AYUDA_SELECCIONAR_TIPO'));

//        //creo una lista desplegable para el tipo de categoria o modulo
//        $listaDesplegableCategoria = HTML::listaDesplegable('datos[categoria]', array('1' => $textos->id('CATEGORIA_1'), '2' => $textos->id('CATEGORIA_2'), '3' => $textos->id('CATEGORIA_3')), '', '', 'listaTipoGrupo', '', '', $textos->id('AYUDA_SELECCIONAR_CATEGORIA'));
        
        $codigo = HTML::campoOculto('procesar', 'true');
        $codigo .= HTML::campoOculto('datos[dialogo]', '', 'idDialogo');
        $codigo .= HTML::parrafo($textos->id('TIPO'), 'negrilla margenSuperior');
        $codigo .= $listaDesplegableTipo;          
        $codigo .= HTML::parrafo($textos->id('NOMBRE'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[nombre]', 40, 255);
        $codigo .= HTML::parrafo($textos->id('DESCRIPCION'), 'negrilla margenSuperior');
        $codigo .= HTML::areaTexto('datos[descripcion]', 5, 45);        
        $codigo .= HTML::parrafo(HTML::campoChequeo('datos[activo]', true) . $textos->id('ACTIVO'), 'margenSuperior');
        $codigo .= HTML::parrafo(HTML::campoChequeo('datos[principal]', false) . $textos->id('PRINCIPAL'), 'margenSuperior');
        $codigo .= HTML::parrafo(HTML::boton('chequeo', $textos->id('ACEPTAR'), '', '', ''), 'margenSuperior');        
        $codigo .= HTML::parrafo($textos->id('REGISTRO_AGREGADO'), 'textoExitoso', 'textoExitoso');
        $codigo1 = HTML::forma($destino, $codigo, 'P');

        $respuesta['generar']       = true;
        $respuesta['codigo']        = $codigo1;
        $respuesta['titulo']        = HTML::parrafo($textos->id('ADICIONAR_ITEM'), 'letraBlanca negrilla subtitulo');
        $respuesta['destino']       = '#cuadroDialogo';
        $respuesta['ancho']         = 550;
        $respuesta['alto']          = 400;
        
    } else {

        $respuesta['error'] = true;

        $existeNombre = $sql->existeItem('cruce_cuentas', 'nombre', $datos['nombre']);

        if (empty($datos['tipo'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_TIPO');
            
        } elseif (empty($datos['nombre'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_NOMBRE');
            
        } elseif ($existeNombre) {
            $respuesta['mensaje'] = $textos->id('ERROR_EXISTE_NOMBRE');
            
        } else {
            $idItem = $objeto->adicionar($datos);
            
            if ($idItem) {
                /*                 * ************** Creo el nuevo item que se insertara via ajax *************** */
                $objeto = new TipoCompra($idItem);

                $estado = ($objeto->activo) ? HTML::frase($textos->id('ACTIVO'), 'activo') : HTML::frase($textos->id('INACTIVO'), 'inactivo');
                
                $principal = ($objeto->principal) ? HTML::frase($textos->id('PRINCIPAL'), 'activo') : HTML::frase($textos->id('SECUNDARIA'), 'inactivo');
                
                
                $celdas     = array($objeto->nombre, $textos->id('TIPO_'.$objeto->tipo), $estado, $principal);
                $claseFila  = '';
                $idFila     = $idItem;
                $celdas1    = HTML::crearNuevaFila($celdas, $claseFila, $idFila);
                
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

    $objeto     = new TipoCompra($id);
    $destino    = '/ajax' . $objeto->urlBase . '/edit';
    $respuesta  = array();

    if (empty($datos)) {

        //creo una lista desplegable para el cruce de cuentas
        $listaDesplegableTipo = HTML::listaDesplegable('datos[tipo]', array('1' => $textos->id('TIPO_1'), '2' => $textos->id('TIPO_2'), '3' => $textos->id('TIPO_3')), $objeto->tipo, '', 'listaTipoGrupo', '', '', $textos->id('AYUDA_SELECCIONAR_TIPO'));

//        //creo una lista desplegable para el tipo de categoria o modulo
//        $listaDesplegableCategoria = HTML::listaDesplegable('datos[categoria]', array('1' => $textos->id('CATEGORIA_1'), '2' => $textos->id('CATEGORIA_2'), '3' => $textos->id('CATEGORIA_3')), $objeto->categoria, '', 'listaTipoGrupo', '', '', $textos->id('AYUDA_SELECCIONAR_CATEGORIA'));

        $codigo = HTML::campoOculto('procesar', 'true');
        $codigo .= HTML::campoOculto('id', $id);
        $codigo .= HTML::campoOculto('datos[dialogo]', '', 'idDialogo');
        $codigo .= HTML::parrafo($textos->id('TIPO'), 'negrilla margenSuperior');
        $codigo .= $listaDesplegableTipo; 
//        $codigo .= HTML::parrafo($textos->id('CATEGORIA'), 'negrilla margenSuperior');
//        $codigo .= $listaDesplegableCategoria;           
        $codigo .= HTML::parrafo($textos->id('NOMBRE'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[nombre]', 40, 255, $objeto->nombre);
        $codigo .= HTML::parrafo($textos->id('DESCRIPCION'), 'negrilla margenSuperior');
        $codigo .= HTML::areaTexto('datos[descripcion]', 5, 45, $objeto->descripcion);
        $codigo .= HTML::parrafo(HTML::campoChequeo('datos[activo]', $objeto->activo) . $textos->id('ACTIVO'), 'margenSuperior');
        $codigo .= HTML::parrafo(HTML::campoChequeo('datos[principal]', $objeto->principal) . $textos->id('PRINCIPAL'), 'margenSuperior');
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

        $existeNombre = $sql->existeItem('cruce_cuentas', 'nombre', $datos['nombre'], 'id != "' . $id . '" ');

        if (empty($datos['tipo'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_TIPO');
            
        } elseif (empty($datos['nombre'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_NOMBRE');
            
        } elseif ($existeNombre) {
            $respuesta['mensaje'] = $textos->id('ERROR_EXISTE_NOMBRE');
            
        } else {
            $idItem = $objeto->modificar($datos);
            if ($idItem) {
                /*                 * ************** Creo el nuevo item que se insertara via ajax *************** */
                $objeto = new TipoCompra($id);

                $estado = ($objeto->activo) ? HTML::frase($textos->id('ACTIVO'), 'activo') : HTML::frase($textos->id('INACTIVO'), 'inactivo');
                
                $principal = ($objeto->principal) ? HTML::frase($textos->id('PRINCIPAL'), 'activo') : HTML::frase($textos->id('SECUNDARIA'), 'inactivo');
                
                $celdas = $celdas = array($objeto->nombre, $textos->id('TIPO_'.$objeto->tipo), $estado, $principal);
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
    
    $objeto     = new TipoCompra($id);
    $destino    = '/ajax' . $objeto->urlBase . '/delete';
    $respuesta  = array();

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

        $respuesta['generar']   = true;
        $respuesta['codigo']    = $codigo1;
        $respuesta['destino']   = '#cuadroDialogo';
        $respuesta['titulo']    = HTML::parrafo($textos->id('ELIMINAR_ITEM'), 'letraBlanca negrilla subtitulo');
        $respuesta['ancho']     = 350;
        $respuesta['alto']      = 150;
        
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
        $respuesta['error'] = true;
        $respuesta['mensaje'] = str_replace('%1', '2', $textos->id('ERROR_TAMAÑO_CADENA_BUSQUEDA'));
        
    } else {
        $item       = '';
        $respuesta  = array();
        $objeto     = new TipoCompra();
        
        $registros = $configuracion['GENERAL']['registrosPorPagina'];
        
        if (!empty($cantidadRegistros)) {
            $registros = (int) $cantidadRegistros;
            
        }        
        $pagina = 1;
        $registroInicial = 0;


        $palabras = explode(' ', $datos);

        $condicionales = $data[1];

        if ($condicionales == '') {
            $condicion = '(g.nombre REGEXP "(' . implode("|", $palabras) . ')")';
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

        $arregloItems = $objeto->listar($registroInicial, $registros, array('0'), $condicion, 'g.nombre');

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
    $objeto = new TipoCompra();

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
            $consultaGlobal = '(g.nombre REGEXP "(' . implode('|', $palabras) . ')" )';
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
    $consulta = $sql->seleccionar(array('cruce_cuentas'), array('id', 'nombre'), 'nombre LIKE "%' . $cadena . '%" AND activo = "1" ', '', 'nombre ASC', 0, 20);

    while ($fila = $sql->filaEnObjeto($consulta)) {
        $respuesta1 = array();
        $respuesta1['label'] = $fila->nombre;
        $respuesta1['value'] = $fila->id;
        $respuesta[] = $respuesta1;
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
    $puedeEliminarMasivo = Perfil::verificarPermisosBoton('botonEliminarMasivoTipos_compra', $modulo->nombre);
        
    if(!$puedeEliminarMasivo && $sesion_usuarioSesion->id != 0) {
        $respuesta            = array();
        $respuesta['error']   = true;
        $respuesta['mensaje'] = $textos->id('ACCESO_DENEGADO');
        
        Servidor::enviarJSON($respuesta);
        return FALSE;
        
    }

    $destino   = '/ajax/cruce_cuentas/eliminarVarios';
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
            $objeto = new TipoCompra($val);
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
 * Función que carga el formulario para adicionar cuentas contables a un cruce de cuentas
 * 
 * @global recurso $textos  = objeto global de gestion de los textos de idioma
 * @global recurso $sql     = objeto global de interaccion con la BD
 * @param array $datos      = arreglo con los datos provenientes del formulario
 */
function adicionarCuenta($id, $datos = array()) {
    global $textos, $sql, $modulo, $sesion_usuarioSesion;
    
    $sqlGlobal = new SqlGlobal(); 
    /**
    * Verificar si el usuario que esta en la sesion tiene permisos para esta accion
    */
    $puedeAgregarCuenta = Perfil::verificarPermisosBoton('botonAgregarCuentaAfectadaCompra', $modulo->nombre);
    
    if(!$puedeAgregarCuenta && $sesion_usuarioSesion->id != 0) {
        $respuesta            = array();
        $respuesta['error']   = true;
        $respuesta['mensaje'] = $textos->id('ACCESO_DENEGADO');
        
        Servidor::enviarJSON($respuesta);
        return FALSE;
        
    }

    $destino = '/ajax/cruce_cuentas/adicionarCuenta';
    $respuesta = array();

    if (empty($datos)) {
        
    
        if (empty($id) || (!$sqlGlobal->existeItem('cruce_cuentas', 'id', $id))) {
            $respuesta              = array();
            $respuesta['error']     = true;
            $respuesta['mensaje']   = $textos->id('NO_HA_SELECCIONADO_ITEM');

            Servidor::enviarJSON($respuesta);
            return NULL;
        }         

        $codigo = HTML::campoOculto('procesar', 'true');
        $codigo .= HTML::campoOculto('datos[dialogo]', '', 'idDialogo');
        $codigo .= HTML::campoOculto('datos[id_tipo_compra]', $id, ''); //id del cruce de cuentas

        $arregloTipoCuenta = array(
            '1' => 'Credito',
            '2' => 'Debito'
        );
        $listaTipoCuenta = HTML::listaDesplegable('datos[tipo]', $arregloTipoCuenta, '', '', 'listaTipoCuenta');

        $codigo .= HTML::parrafo($textos->id('CUENTA_CONTABLE'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[cuenta]', 40, 255, '', 'autocompletable campoObligatorio', 'campoNombreCuenta', array('title' => HTML::urlInterna('PLAN_CONTABLE', 0, true, 'listar')), $textos->id('AYUDA_USO_AUTOCOMPLETAR'), HTML::urlInterna('PLAN_CONTABLE', 0, true, 'add'), 'datos[id_cuenta]');
        $codigo .= HTML::parrafo($textos->id('TIPO_CUENTA'), 'negrilla margenSuperior');
        $codigo .= HTML::frase($listaTipoCuenta, '');

        $codigo .= HTML::parrafo(HTML::boton('chequeo', $textos->id('ACEPTAR'), 'botonOk', 'botonOk', 'botonOk'), 'margenSuperior');
        $codigo .= HTML::parrafo($textos->id('REGISTRO_AGREGADO'), 'textoExitoso', 'textoExitoso');
        $codigo1 = HTML::forma($destino, $codigo, 'P');

        $respuesta['generar']   = true;
        $respuesta['codigo']    = $codigo1;
        $respuesta['titulo']    = HTML::parrafo($textos->id('ADICIONAR_CUENTA_CONTABLE'), 'letraBlanca negrilla subtitulo');
        $respuesta['destino']   = '#cuadroDialogo';
        $respuesta['ancho']     = 450;
        $respuesta['alto']      = 320;
        
    } else {

        $respuesta['error'] = true;
        
        $cuentaExistente = $sql->existeItem("cuentas_tipo_compra", "id_cuenta", $datos['id_cuenta']);

        if (empty($datos['id_cuenta'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_CUENTA');
            
        } else if ($cuentaExistente) {
            $respuesta['mensaje'] = $textos->id('ERROR_CUENTA_EXISTENTE');
            
        }  else {
            
            $dialogo            = $datos['dialogo'];
            $cuenta             = $datos['cuenta'];
            
            unset($datos['dialogo']);
            unset($datos['cuenta']);

            $consulta   = $sql->insertar('cuentas_tipo_compra', $datos);
            $idItem     = $sql->ultimoId;

            if ($consulta) {                
                $botonEliminar  = HTML::contenedor('', 'eliminarRegistro');

                $idDestino          = '#tablaCuentasContables';
                $idContenedor       = '#tablaCuentasContablesTr_';
                $idFila             = 'tablaCuentasContables_';
                $celdas1            = array($cuenta, $datos['numero_cuenta'], $textos->id('TIPO_CUENTA_' . $datos['tipo']), $botonEliminar);

                $celdas = HTML::crearNuevaFilaDesdeModal($celdas1, '', $idFila . $idItem);

                if ($dialogo == '') {
                    $respuesta['error']             = false;
                    $respuesta['accion']            = 'insertar';
                    $respuesta['insertarNuevaFila'] = true;
                    
                } else {
                    $respuesta['error']                     = false;
                    $respuesta['accion']                    = 'insertar';
                    $respuesta['contenido']                 = $celdas;
                    $respuesta['idContenedor']              = $idContenedor . $idItem;
                    $respuesta['insertarNuevaFilaDialogo']  = true;
                    $respuesta['idDestino']                 = $idDestino;
                    $respuesta['ventanaDialogo']            = $dialogo;
                }
                
            } else {
                $respuesta['mensaje'] = $textos->id('ERROR_DESCONOCIDO');
                
            }
            
        }
        
    }

    Servidor::enviarJSON($respuesta);
}

/**
 * Función que carga el formulario para eliminar una cuenta bancaria del  proveedor
 * 
 * @global recurso $textos  = objeto global de gestion de los textos de idioma
 * @param int $id identificador del recurso en la BD
 * @param bolean $confirmado determina si se confirma la orden 
 */
function eliminarcuenta($id, $confirmado, $dialogo) {
    global $textos, $sql;

    $destino    = '/ajax/cruce_cuentas/eliminarCuenta';
    $respuesta  = array();

    $idCuenta   = $sql->obtenerValor('cuentas_tipo_compra', 'id_cuenta', 'id = "' . $id . '"');
    
    $cuenta     = $sql->obtenerValor('plan_contable', 'nombre', 'id = "' . $idCuenta . '"');

    if (!$confirmado) {
        $titulo = HTML::frase($cuenta, 'negrilla');
        $titulo1 = str_replace('%1', $titulo, $textos->id('CONFIRMAR_ELIMINACION'));
        $codigo = HTML::campoOculto('procesar', 'true');
        $codigo .= HTML::campoOculto('id', $id);
        $codigo .= HTML::campoOculto('dialogo', '', 'idDialogo');
        $codigo .= HTML::parrafo($titulo1);
        $codigo .= HTML::parrafo(HTML::boton('chequeo', $textos->id('ACEPTAR'), '', 'botonOk', 'botonOk'), 'margenSuperior');
        $codigo .= HTML::parrafo($textos->id('REGISTRO_ELIMINADO'), 'textoExitoso', 'textoExitoso');
        $codigo1 = HTML::forma($destino, $codigo);

        $respuesta['generar']       = true;
        $respuesta['codigo']        = $codigo1;
        $respuesta['destino']       = '#cuadroDialogo';
        $respuesta['titulo']        = HTML::parrafo($textos->id('ELIMINAR_CUENTA'), 'letraBlanca negrilla subtitulo');
        $respuesta['ancho']         = 350;
        $respuesta['alto']          = 150;
        
    } else {

        $consulta = $sql->eliminar('cuentas_tipo_compra', 'id = "' . $id . '"');

        if ($consulta) {

            $respuesta['error']     = false;
            $respuesta['accion']    = 'insertar';
            $respuesta['idDestino'] = '#tablaCuentasContablesTr_' . $id;

            if ($dialogo == '') {
                $respuesta['eliminarFilaTabla'] = true;
                
            } else {
                $respuesta['eliminarFilaDialogo']   = true;
                $respuesta['ventanaDialogo']        = $dialogo;
                
            }
            
        } else {
            $respuesta['mensaje'] = $textos->id('ERROR_DESCONOCIDO');
            
        }
        
    }

    Servidor::enviarJSON($respuesta);
}