<?php

/**
 *
 * @package     FOM
 * @subpackage  Componentes
 * @author      Pablo Andres Velez Vidal <pavelez@genesyscorp.com.co>
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2013 GENESYS
 * @version     0.1
 *
 * */
if (isset($url_accion)) {
    switch ($url_accion) {
        case 'add'              :   $datos = ($forma_procesar) ? $forma_datos : array();
                                    adicionarItem($datos);
                                    break;
        
        case 'see'              :   consultarItem($forma_id);
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
function consultarItem($id) {
    global $textos, $sql, $configuracion;

    $objeto         = new Componentes($id);
    $destino        = '/ajax' . $objeto->urlBase . '/see';
    $respuesta      = array();

    $consulta = $sql->seleccionar(array('modulos'), array('*'), 'id = "' . $id . '"', '', 'nombre ASC');
    
    if ($sql->filasDevueltas) {
        $datos_componente = $sql->filaEnObjeto($consulta);
        $componente_padre = $sql->obtenerValor('modulos', 'nombre', 'id = ' . $datos_componente->id_padre . '');

        if ($datos_componente->menu == '0') {
            $mostra_menu = $textos->id('NO');
        } else {
            $mostra_menu = $textos->id('SI');
        }

        if ($datos_componente->tipo_menu == '0') {
            $tipo_menu = $textos->id('APLICACION_WEB');
            
        } else {
            $tipo_menu = $textos->id('PAGINA_WEB');
            
        }

        if ($datos_componente->visible == '0') {
            $visible = $textos->id('NO');
        } else {
            $visible = $textos->id('SI');
        }

        if ($datos_componente->global == '0') {
            $global = $textos->id('NO');
        } else {
            $global = $textos->id('SI');
        }

        if ($datos_componente->valida_usuario == '0') {
            $valida_usuario = $textos->id('NO');
        } else {
            $valida_usuario = $textos->id('SI');
        }

        if ($datos_componente->clase == '1') {
            $clases = $textos->id('CONFIGURACION_SITIO');
            
        } else if ($datos_componente->clase == '2') {
            $clases = $textos->id('CONFIGURACION_PERSONAL');
            
        } else if ($datos_componente->clase == '3') {
            $clases = $textos->id('USO_GLOBAL');
            
        } else if ($datos_componente->clase == '4') {
            $clases = $textos->id('OTROS');
            
        }
    }

    $pestana1a = HTML::campoOculto('id', $id);
    $pestana1a .= HTML::parrafo($textos->id('COMPONENTE_PADRE'), 'negrilla margenSuperior');
    $pestana1a .= HTML::frase($componente_padre);
    $pestana1a .= HTML::parrafo($textos->id('MOSTRAR_MENU'), 'negrilla margenSuperior');
    $pestana1a .= HTML::frase($mostra_menu);
    $pestana1a .= HTML::parrafo($textos->id('TIPO_MENU'), 'negrilla margenSuperior');
    $pestana1a .= HTML::frase($tipo_menu);
    $pestana1a .= HTML::parrafo($textos->id('NOMBRE_MENU'), 'negrilla margenSuperior');
    $pestana1a .= HTML::frase($datos_componente->nombre_menu);
    $pestana1a .= HTML::parrafo($textos->id('CLASE'), 'negrilla margenSuperior');
    $pestana1a .= HTML::frase($clases);
    $pestana1a .= HTML::parrafo($textos->id('ORDEN'), 'negrilla margenSuperior');
    $pestana1a .= HTML::frase($datos_componente->orden);
    $pestana1a .= HTML::parrafo($textos->id('NOMBRE_COMPONENTE'), 'negrilla margenSuperior');
    $pestana1a .= HTML::frase($datos_componente->nombre);



    $pestana1b .= HTML::parrafo($textos->id('URL'), 'negrilla margenSuperior');
    $pestana1b .= HTML::frase($datos_componente->url);
    $pestana1b .= HTML::parrafo($textos->id('CARPETA'), 'negrilla margenSuperior');
    $pestana1b .= HTML::frase($datos_componente->carpeta);
    $pestana1b .= HTML::parrafo($textos->id('VISIBLE'), 'negrilla margenSuperior');
    $pestana1b .= HTML::frase($visible);
    $pestana1b .= HTML::parrafo($textos->id('GLOBAL'), 'negrilla margenSuperior');
    $pestana1b .= HTML::frase($global);
    $pestana1b .= HTML::parrafo($textos->id('TABLA_PRINICPAL'), 'negrilla margenSuperior');
    $pestana1b .= HTML::frase($datos_componente->tabla_principal);
    $pestana1b .= HTML::parrafo($textos->id('VALIDA_USUARIO'), 'negrilla margenSuperior');
    $pestana1b .= HTML::frase($valida_usuario);


    $contenedora = HTML::contenedor($pestana1a, 'contenedorIzquierdo');
    $contenedorb = HTML::contenedor($pestana1b, 'contenedorDerecho');


    $pestana1 .= HTML::contenedor($contenedora . $contenedorb, 'altura400px');


    $pestana2 .= HTML::parrafo($textos->id('OBSERVACIONES'), 'negrilla margenSuperior');
    $pestana2 .= HTML::contenedor($objeto->documentacion, 'justificado');

    $pestana2 = HTML::contenedor($pestana2, 'pestana1');
    
    /**
     * Listado de componentes de este modulo
     */
    $pestana3 = '';
    
    if (!empty($objeto->listaAcciones)) {
        //crear los formularios con la info para las demas sedes
        $datosTablaAcciones = array(
            HTML::parrafo($textos->id('NOMBRE_MODULO'), 'centrado')         => 'modulo',
            HTML::parrafo($textos->id('NOMBRE_ACCION'), 'centrado')         => 'nombreAccion',
            HTML::parrafo($textos->id('NOMBRE_ACCION_MENU'), 'centrado')    => 'nombreAccionMenu',
            HTML::parrafo($textos->id('EDITAR'), 'centrado')                => 'botonEditar',
            HTML::parrafo($textos->id('ELIMINAR'), 'centrado')              => 'botonEliminar'
        );

        $rutas = array(
        );

        $listaAcciones = array();
        
        while ($fila = $sql->filaEnObjeto($objeto->listaAcciones)) {
            $fila->botonEditar      = HTML::contenedor('', 'editarRegistroSinAccion editarAccion', 'editarAccion');
            $fila->botonEliminar    = HTML::contenedor('', 'eliminarRegistroSinAccion eliminarAccion', 'eliminarAccion');
            $listaAcciones[] = $fila;
        }

        $idTabla = 'tablaEditarAcciones';
        $estilosColumnas = array('ancho200px', 'ancho200px', 'ancho200px', 'columnaPequena50', 'columnaPequena50');
        $contenedorAcciones = HTML::contenedor(Recursos::generarTablaRegistrosInterna($listaAcciones, $datosTablaAcciones, $rutas, $idTabla, $estilosColumnas), 'flotanteIzquierda margenDerecha');
    
        $pestana3 .= HTML::campoOculto('idModulo', $id, 'idModulo');
        
        
    }
    
    $pestana3 .= HTML::contenedor($contenedorAcciones, 'pestana3');    


    $pestanas = array(
        HTML::frase($textos->id('INFORMACION_MODULO'), 'letraBlanca')           => $pestana1,
        HTML::frase($textos->id('DESCRIPCION_FUNCIONALIDAD'), 'letraBlanca')    => $pestana2,
        HTML::frase($textos->id('ACCIONES_MODULO'), 'letraBlanca')              => $pestana3,
    );

    $codigo .= HTML::pestanas2('pestanasAgregar', $pestanas);


    $codigo_f = HTML::forma($destino, $codigo);

    $respuesta['generar']       = true;
    $respuesta['cargarJs']      = true;
    $respuesta['archivoJs']     = $configuracion['SERVIDOR']['media'] . $configuracion['RUTAS']['javascript'] . '/modulos/componentes/funcionesVentanaModal.js';     
    $respuesta['codigo']        = $codigo_f;
    $respuesta['destino']       = '#cuadroDialogo';
    $respuesta['titulo']        = HTML::parrafo($textos->id('CONSULTAR_ITEM'), 'letraBlanca negrilla');
    $respuesta['ancho']         = 800;
    $respuesta['alto']          = 580;

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

    $objeto         = new Componentes();
    $destino        = '/ajax' . $objeto->urlBase . '/add';
    $respuesta      = array();

    $lista_componentes = array();
    $consulta = $sql->seleccionar(array('modulos'), array('id', 'nombre'), '', '', 'nombre ASC');
    
    if ($sql->filasDevueltas) {
        while ($dato = $sql->filaEnObjeto($consulta)) {
            $lista_componentes[$dato->id] = $dato->nombre;
            
        }
        
    }

    $mostra_menu    = array('0' => $textos->id('NO'), '1' => $textos->id('SI'));
    $visible        = array('0' => $textos->id('NO'), '1' => $textos->id('SI'));
    $global         = array('0' => $textos->id('NO'), '1' => $textos->id('SI'));
    $valida_usuario = array('0' => $textos->id('NO'), '1' => $textos->id('SI'));
    $tipo_menu      = array('0' => $textos->id('APLICACION_WEB'), '1' => $textos->id('PAGINA_WEB'));

    if (empty($datos)) {
        $codigo = HTML::campoOculto('procesar', 'true');

        $pestana1a = HTML::parrafo($textos->id('COMPONENTE_PADRE'), 'negrilla margenSuperior');
        $pestana1a .= HTML::listaDesplegable('datos[componente_padre]', $lista_componentes);
        $pestana1a .= HTML::parrafo($textos->id('MOSTRAR_MENU'), 'negrilla margenSuperior');
        $pestana1a .= HTML::listaDesplegable('datos[mostrar_menu]', $mostra_menu);
        $pestana1a .= HTML::parrafo($textos->id('TIPO_MENU'), 'negrilla margenSuperior');
        $pestana1a .= HTML::listaDesplegable('datos[tipo_menu]', $tipo_menu);
        $pestana1a .= HTML::parrafo($textos->id('NOMBRE_MENU'), 'negrilla margenSuperior');
        $pestana1a .= HTML::campoTexto('datos[nombre_menu]', 40, 50);
        $pestana1a .= HTML::parrafo($textos->id('ORDEN'), 'negrilla margenSuperior');
        $pestana1a .= HTML::campoTexto('datos[orden]', 4, 4);
        $pestana1a .= HTML::parrafo($textos->id('NOMBRE_COMPONENTE'), 'negrilla margenSuperior');
        $pestana1a .= HTML::campoTexto('datos[nombre]', 40, 50);


        $pestana1b .= HTML::parrafo($textos->id('URL'), 'negrilla margenSuperior');
        $pestana1b .= HTML::campoTexto('datos[url]', 40, 50);
        $pestana1b .= HTML::parrafo($textos->id('CARPETA'), 'negrilla margenSuperior');
        $pestana1b .= HTML::campoTexto('datos[carpeta]', 40, 50);
        $pestana1b .= HTML::parrafo($textos->id('VISIBLE'), 'negrilla margenSuperior');
        $pestana1b .= HTML::listaDesplegable('datos[visible]', $visible);
        $pestana1b .= HTML::parrafo($textos->id('GLOBAL'), 'negrilla margenSuperior');
        $pestana1b .= HTML::listaDesplegable('datos[global]', $global);
        $pestana1b .= HTML::parrafo($textos->id('TABLA_PRINICPAL'), 'negrilla margenSuperior');
        $pestana1b .= HTML::campoTexto('datos[tabla_principal]', 40, 255);
        $pestana1b .= HTML::parrafo($textos->id('VALIDA_USUARIO'), 'negrilla margenSuperior');
        $pestana1b .= HTML::listaDesplegable('datos[valida_usuario]', $valida_usuario);

        $contenedora = HTML::contenedor($pestana1a, 'contenedorIzquierdo');
        $contenedorb = HTML::contenedor($pestana1b, 'contenedorDerecho');


        $pestana1 .= HTML::contenedor($contenedora . $contenedorb, 'altura400px');


        $pestana2 .= HTML::parrafo($textos->id('OBSERVACIONES'), 'negrilla margenSuperior');
        $pestana2 .= HTML::areaTexto('datos[documentacion]', 7, 80, '', 'editor');

        $pestana2 = HTML::contenedor($pestana2, 'pestana1');


        $pestanas = array(
            HTML::frase($textos->id('INFORMACION_MODULO'), 'letraBlanca')       => $pestana1,
            HTML::frase($textos->id('DESCRIPCION_FUNCIONALIDAD'), 'letraBlanca') => $pestana2,
        );

        $codigo .= HTML::pestanas2('pestanasAgregar', $pestanas);



        $codigo .= HTML::parrafo(HTML::boton('chequeo', $textos->id('ACEPTAR')), 'margenSuperior');
        $codigo .= HTML::parrafo($textos->id('REGISTRO_AGREGADO'), 'textoExitoso', 'textoExitoso');



        $codigo1 = HTML::forma($destino, $codigo);

        $respuesta['generar']       = true;
        $respuesta['codigo']        = $codigo1;
        $respuesta['destino']       = '#cuadroDialogo';
        $respuesta['titulo']        = HTML::parrafo($textos->id('ADICIONAR_ITEM'), 'letraBlanca negrilla');
        $respuesta['ancho']         = 800;
        $respuesta['alto']          = 580;
        
    } else {
        $respuesta['error'] = true;

        $existeOrden = $sql->obtenerValor('modulos', 'id', 'id_padre= "' . $datos['componente_padre'] . '" AND orden = "' . $datos['orden'] . '"');


        if (empty($datos['nombre'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_NOMBRE');
            
        } else if ($sql->existeItem('modulos', 'nombre', $datos['nombre'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_EXISTE_NOMBRE');
            
        } else if ($existeOrden) {
            $respuesta['mensaje'] = $textos->id('ERROR_EXISTE_ORDEN');
            
        } else {
            $idItem = $objeto->adicionar($datos);
            
            if ($idItem) {
                /*                 * ************** Creo el nuevo item que se insertara via ajax *************** */
                $objeto = new Componentes($idItem);

                $celdas = array($objeto->nombre, $objeto->nombreMenu, $objeto->padre, $objeto->orden);
                $claseFila = '';
                $idFila = $idItem;
                $celdas_f = HTML::crearNuevaFila($celdas, $claseFila, $idFila);

                $respuesta['error']             = false;
                $respuesta['accion']            = 'insertar';
                $respuesta['contenido']         = $celdas_f;
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
    global $textos, $sql, $configuracion, $modulo, $sesion_usuarioSesion;
    
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

    $objeto         = new Componentes($id);
    $destino        = '/ajax' . $objeto->urlBase . '/edit';
    $respuesta      = array();

    $lista_componentes = array();
    $consulta = $sql->seleccionar(array('modulos'), array('id', 'nombre'), '', '', 'nombre ASC');
    
    if ($sql->filasDevueltas) {
        while ($dato = $sql->filaEnObjeto($consulta)) {
            $lista_componentes[$dato->id] = $dato->nombre;
            
        }
        
    }

    $consulta = $sql->seleccionar(array('modulos'), array('*'), 'id = ' . $id . ' ', '', 'nombre ASC');
    if ($sql->filasDevueltas) {
        $datos_componente = $sql->filaEnObjeto($consulta);
    }

    $mostra_menu    = array('0' => $textos->id('NO'), '1' => $textos->id('SI'));
    $visible        = array('0' => $textos->id('NO'), '1' => $textos->id('SI'));
    $global         = array('0' => $textos->id('NO'), '1' => $textos->id('SI'));
    $valida_usuario = array('0' => $textos->id('NO'), '1' => $textos->id('SI'));
    $tipo_menu      = array('0' => $textos->id('APLICACION_WEB'), '1' => $textos->id('PAGINA_WEB'));
    $clases         = array('1' => $textos->id('CONFIGURACION_SITIO'), '2' => $textos->id('CONFIGURACION_PERSONAL'), '3' => $textos->id('USO_GLOBAL'), '4' => $textos->id('OTROS'));

    if (empty($datos)) {
        $codigo = HTML::campoOculto('procesar', 'true');
        $codigo .= HTML::campoOculto('id', $id);

        $pestana1a .= HTML::parrafo($textos->id('COMPONENTE_PADRE'), 'negrilla margenSuperior');
        $pestana1a .= HTML::listaDesplegable('datos[componente_padre]', $lista_componentes, $datos_componente->id_padre);
        $pestana1a .= HTML::parrafo($textos->id('MOSTRAR_MENU'), 'negrilla margenSuperior');
        $pestana1a .= HTML::listaDesplegable('datos[mostrar_menu]', $mostra_menu, $datos_componente->menu);
        $pestana1a .= HTML::parrafo($textos->id('TIPO_MENU'), 'negrilla margenSuperior');
        $pestana1a .= HTML::listaDesplegable('datos[tipo_menu]', $tipo_menu, $datos_componente->tipo_menu);
        $pestana1a .= HTML::parrafo($textos->id('NOMBRE_MENU'), 'negrilla margenSuperior');
        $pestana1a .= HTML::campoTexto('datos[nombre_menu]', 40, 50, $datos_componente->nombre_menu);
        $pestana1a .= HTML::parrafo($textos->id('CLASE'), 'negrilla margenSuperior');
        $pestana1a .= HTML::listaDesplegable('datos[clase]', $clases, $datos_componente->clase);
        $pestana1a .= HTML::parrafo($textos->id('ORDEN'), 'negrilla margenSuperior');
        $pestana1a .= HTML::campoTexto('datos[orden]', 4, 4, $datos_componente->orden);
        $pestana1a .= HTML::parrafo($textos->id('NOMBRE_COMPONENTE'), 'negrilla margenSuperior');
        $pestana1a .= HTML::campoTexto('datos[nombre]', 40, 50, $datos_componente->nombre);


        $pestana1b .= HTML::parrafo($textos->id('URL'), 'negrilla margenSuperior');
        $pestana1b .= HTML::campoTexto('datos[url]', 40, 50, $datos_componente->url);
        $pestana1b .= HTML::parrafo($textos->id('CARPETA'), 'negrilla margenSuperior');
        $pestana1b .= HTML::campoTexto('datos[carpeta]', 40, 50, $datos_componente->carpeta);
        $pestana1b .= HTML::parrafo($textos->id('VISIBLE'), 'negrilla margenSuperior');
        $pestana1b .= HTML::listaDesplegable('datos[visible]', $visible, $datos_componente->visible);
        $pestana1b .= HTML::parrafo($textos->id('GLOBAL'), 'negrilla margenSuperior');
        $pestana1b .= HTML::listaDesplegable('datos[global]', $global, $datos_componente->global);
        $pestana1b .= HTML::parrafo($textos->id('TABLA_PRINICPAL'), 'negrilla margenSuperior');
        $pestana1b .= HTML::campoTexto('datos[tabla_principal]', 40, 255, $datos_componente->tabla_principal);
        $pestana1b .= HTML::parrafo($textos->id('VALIDA_USUARIO'), 'negrilla margenSuperior');
        $pestana1b .= HTML::listaDesplegable('datos[valida_usuario]', $valida_usuario, $datos_componente->valida_usuario);


        $contenedora = HTML::contenedor($pestana1a, 'contenedorIzquierdo');
        $contenedorb = HTML::contenedor($pestana1b, 'contenedorDerecho');


        $pestana1 .= HTML::contenedor($contenedora . $contenedorb, 'altura400px');


        $pestana2 .= HTML::parrafo($textos->id('OBSERVACIONES'), 'negrilla margenSuperior');
        $pestana2 .= HTML::areaTexto('datos[documentacion]', 7, 80, $objeto->documentacion, 'editor');

        $pestana2 = HTML::contenedor($pestana2, 'pestana1');

        /**
         * Listado de componentes de este modulo
         */
        $pestana3 = '';

        $nomAccion = HTML::parrafo($textos->id('NOMBRE_ACCION'), 'negrilla margenSuperior');
        $nomAccion .= HTML::campoTexto('datos[nombre_accion]', 40, 50, '', '', 'campoNomAccion');

        $nomAccionMenu = HTML::parrafo($textos->id('NOMBRE_ACCION_MENU'), 'negrilla margenSuperior');
        $nomAccionMenu .= HTML::campoTexto('datos[nombre_accion_menu]', 40, 50, '', '', 'campoNomAccionMenu');

        $codigo1 = HTML::contenedorCampos($nomAccion, $nomAccionMenu);

        $boton = HTML::parrafo(HTML::boton('mas', $textos->id('ADICIONAR_ACCION'), ' directo margenSuperiorDoble', '', 'botonAdicionarAccion', '', array('validar' => 'NoValidar')) . HTML::frase('', 'margenIzquierda oculto', 'textoNotificacionAcciones'), 'margensuperior');

        $columnas = array($textos->id('NOMBRE_ACCION'), $textos->id('NOMBRE_ACCION_MENU'), $textos->id('ELIMINAR'));

        $claseColumnas = array('ancho200px', 'ancho200px', 'ancho75px');

        $tabla = HTML::contenedor(HTML::tabla($columnas, '', 'ancho100por100', 'tablaAccionesModulo', $claseColumnas), 'margenSuperior contenedorTablaAccionesModulo');
        $pestana3 .= HTML::campoOculto('datos[acciones_modulo]', '', 'campoCadenaAccionesModulo');
        $pestana3 .= HTML::campoOculto('idModulo', $id, 'idModulo');
        $pestana3 .= HTML::contenedor($codigo1 . $boton . $tabla, 'pestana1');        


        $pestanas = array(
            HTML::frase($textos->id('INFORMACION_MODULO'), 'letraBlanca')           => $pestana1,
            HTML::frase($textos->id('DESCRIPCION_FUNCIONALIDAD'), 'letraBlanca')    => $pestana2,
            HTML::frase($textos->id('ACCIONES_MODULO'), 'letraBlanca')              => $pestana3,
        );

        $codigo .= HTML::pestanas2('pestanasAgregar', $pestanas);

        $codigo .= HTML::parrafo(HTML::boton('chequeo', $textos->id('ACEPTAR')), 'margenSuperior');
        $codigo .= HTML::parrafo($textos->id('REGISTRO_MODIFICADO'), 'textoExitoso', 'textoExitoso');
        $codigo1 = HTML::forma($destino, $codigo);

        $respuesta['generar']       = true;
        $respuesta['cargarJs']      = true;
        $respuesta['archivoJs']     = $configuracion['SERVIDOR']['media'] . $configuracion['RUTAS']['javascript'] . '/modulos/componentes/funcionesVentanaModal.js';        
        $respuesta['codigo']        = $codigo1;
        $respuesta['destino']       = '#cuadroDialogo';
        $respuesta['titulo']        = HTML::parrafo($textos->id('MODIFICAR_ITEM'), 'letraBlanca negrilla');
        $respuesta['ancho']         = 800;
        $respuesta['alto']          = 580;
        
    } else {

        $respuesta['error'] = true;
        
        if (empty($datos['nombre'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_NOMBRE');
            
        } else if ($sql->existeItem('modulos', 'nombre', $datos['nombre'], 'id != ' . $id . ' ')) {
            $respuesta['mensaje'] = $textos->id('ERROR_EXISTE_NOMBRE');
            
        } else {
            $idItem = $objeto->modificar($datos);
            
            if ($idItem) {
                /*                 * ************** Creo el nuevo item que se insertara via ajax *************** */
                $objeto = new Componentes($id);

                $celdas = array($objeto->nombre, $objeto->nombreMenu, $objeto->padre, $objeto->orden);
                $celdas1 = HTML::crearFilaAModificar($celdas);

                $respuesta['error']         = false;
                $respuesta['accion']        = 'insertar';
                $respuesta['contenido']     = $celdas1;
                $respuesta['idContenedor']  = '#tr_' . $id;
                $respuesta['idDestino']     = '#tr_' . $id;

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

    $objeto = new Componentes($id);
    $destino = '/ajax' . $objeto->urlBase . '/delete';
    $respuesta = array();

    if (!$confirmado) {
        $nombre     = HTML::frase($objeto->nombre, 'negrilla');
        $nombre1    = str_replace('%1', $nombre, $textos->id('CONFIRMAR_ELIMINACION'));
        $codigo     = HTML::campoOculto('procesar', 'true');
        $codigo     .= HTML::campoOculto('id', $id);
        $codigo     .= HTML::parrafo($nombre1);
        $codigo     .= HTML::parrafo(HTML::boton('chequeo', $textos->id('ACEPTAR')), 'margenSuperior');
        $codigo     .= HTML::parrafo($textos->id('REGISTRO_ELIMINADO'), 'textoExitoso', 'textoExitoso');
        $codigo1    = HTML::forma($destino, $codigo);

        $respuesta['generar']       = true;
        $respuesta['codigo']        = $codigo1;
        $respuesta['destino']       = '#cuadroDialogo';
        $respuesta['titulo']        = HTML::parrafo($textos->id('ELIMINAR_ITEM'), 'letraBlanca negrilla subtitulo');
        $respuesta['ancho']         = 350;
        $respuesta['alto']          = 150;
        
    } else {
        if ($objeto->eliminar()) {
            $respuesta['error']     = false;
            $respuesta['accion']    = 'insertar';
            $respuesta['idDestino'] = '#tr_' . $id;
            
            if ($dialogo == '') {
                $respuesta['eliminarFilaTabla'] = true;
                
            } else {
                $respuesta['eliminarFilaDialogo']   = true;
                $respuesta['ventanaDialogo']        = $dialogo;
                
            }
            
        } else {
            $respuesta['error'] = true;
            $respuesta['mensaje'] = $textos->id('ERROR_DESCONOCIDO');
            
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
        $item       = '';
        $respuesta  = array();
        $objeto     = new Componentes();
        $registros  = $configuracion['GENERAL']['registrosPorPagina'];
        
        if (!empty($cantidadRegistros)) {
            $registros = (int) $cantidadRegistros;
        }
        
        $pagina             = 1;
        $registroInicial    = 0;


        $palabras = explode(' ', $datos);

        $condicionales = $data[1];

        if ($condicionales == '') {
            $condicion = '(m1.nombre REGEXP "(' . implode('|', $palabras) . ')" OR m1.nombre_menu REGEXP "(' . implode('|', $palabras) . ')")';
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

        $arregloItems = $objeto->listar($registroInicial, $registros, array('0'), $condicion, 'm1.nombre');

        if ($objeto->registrosConsulta) {//si la consulta trajo registros
            $datosPaginacion = array($objeto->registrosConsulta, $registroInicial, $registros, $pagina, $objeto->registrosConsulta);
            $item .= $objeto->generarTabla($arregloItems, $datosPaginacion);
            $info = HTML::parrafo(str_replace('%1', $objeto->registrosConsulta, $textos->id('RESULTADOS_BUSQUEDA')), 'textoExitosoNotificaciones');
            
        } else {
            $datosPaginacion = 0;
            $item .= $objeto->generarTabla($textos->id('NO_HAY_REGISTROS'), $datosPaginacion);
            $info = HTML::parrafo($textos->id('BUSQUEDA_SIN_RESULTADOS'), 'textoErrorNotificaciones');
            
        }

        $respuesta['error']         = false;
        $respuesta['accion']        = 'insertar';
        $respuesta['contenido']     = $item;
        $respuesta['idContenedor']  = '#tablaRegistros';
        $respuesta['idDestino']     = '#contenedorTablaRegistros';
        $respuesta['paginarTabla']  = true;
        $respuesta['info']          = $info;
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
    $objeto     = new Componentes();

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
            $consultaGlobal = '(m1.nombre REGEXP "(' . implode('|', $palabras) . ')")';
            
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
        $puedeEliminarMasivo = Perfil::verificarPermisosEliminacion($modulo->nombre);
    
     if(!$puedeEliminarMasivo && $sesion_usuarioSesion->id != 0) {
        $respuesta            = array();
        $respuesta['error']   = true;
        $respuesta['mensaje'] = $textos->id('ACCESO_DENEGADO');
        
        Servidor::enviarJSON($respuesta);
        return FALSE;
        
    }

    $destino = '/ajax/componentes/eliminarVarios';
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

        $respuesta['generar']           = true;
        $respuesta['codigo']            = $codigo1;
        $respuesta['destino']           = '#cuadroDialogo';
        $respuesta['titulo']            = HTML::parrafo($textos->id('ELIMINAR_VARIOS_REGISTROS'), 'letraBlanca negrilla subtitulo');
        $respuesta['ancho']             = 350;
        $respuesta['alto']              = 150;
        
    } else {

        $cadenaIds  = substr($cadenaItems, 0, -1);
        $arregloIds = explode(',', $cadenaIds);

        $eliminarVarios = true;
        foreach ($arregloIds as $val) {
            $objeto = new Componentes($val);
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
