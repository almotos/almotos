<?php

/**
 * @package     FOM
 * @subpackage  Proveedores
 * @author      Pablo Andres Velez Vidal <pavelez@genesyscorp.com.co>
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2013 GENESYS
 * @version     0.1
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
        
        case 'verificarProveedor'       :   verificarProveedor($forma_proveedor);
                                            break;
        
        case 'verificarPersona'         :   verificarPersona($forma_datos);
                                            break;
        
        case 'adicionarSede'            :   $datos = ($forma_procesar) ? $forma_datos : array();
                                            adicionarSede($forma_id, $datos, $forma_tablaEditarVisible);
                                            break;
        
        case 'modificarSede'            :   $datos = ($forma_procesar) ? $forma_datos : array();
                                            modificarSede($forma_id, $datos);
                                            break;
        
        case 'eliminarSede'             :   $confirmado = ($forma_procesar) ? true : false;
                                            eliminarSede($forma_id, $confirmado, $forma_dialogo);
                                            break;
        
        case 'adicionarContacto'        :   $datos = ($forma_procesar) ? $forma_datos : array();
                                            adicionarContacto($forma_id, $datos, $forma_tablaEditarVisible); //la variable tablaEditarvisible determina si la tabla editar  esta visible y asi se armara la respuesta ajax
                                            break;
        
        case 'modificarContacto'        :   $datos = ($forma_procesar) ? $forma_datos : array();
                                            modificarContacto($forma_id, $datos);
                                            break;
        
        case 'eliminarContacto'         :   $confirmado = ($forma_procesar) ? true : false;
                                            eliminarContacto($forma_id, $confirmado, $forma_dialogo);
                                            break;
        
        case 'adicionarCuenta'          :   $datos = ($forma_procesar) ? $forma_datos : array();
                                            adicionarCuenta($forma_id, $datos, $forma_tablaEditarVisible);
                                            break;
        
        case 'modificarCuenta'          :   $datos = ($forma_procesar) ? $forma_datos : array();
                                            modificarCuenta($forma_id, $datos);
                                            break;
        
        case 'eliminarCuenta'           :   $confirmado = ($forma_procesar) ? true : false;
                                            eliminarCuenta($forma_id, $confirmado, $forma_dialogo);
                                            break;
        
        case 'listar'                   :   listarItems($url_cadena);
                                            break;
        
        case 'eliminarVarios'           :   $confirmado = ($forma_procesar) ? true : false;
                                            eliminarVarios($confirmado, $forma_cantidad, $forma_cadenaItems);
                                            break;
                                        
        case 'getRegimen'               :   getRegimen($forma_idProveedor);
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

    if (empty($id) || (!empty($id) && !$sql->existeItem('proveedores', 'id', $id))) {
        $respuesta              = array();
        $respuesta['error']     = true;
        $respuesta['mensaje']   = $textos->id('NO_HA_SELECCIONADO_ITEM');

        Servidor::enviarJSON($respuesta);
        return NULL;
    }

    $objeto     = new Proveedor($id);
    $respuesta  = array();
    $codigo     = '';

    $pestana1 = HTML::campoOculto('id', $id);
    $pestana1   .= HTML::campoOculto('datos[dialogo]', '', 'idDialogo');
    $tipoDocumento = $objeto->contacto->tipoDocumento;
    

    $ciudadDocumento = '';
    if ($objeto->contacto->idCiudadDocumento != '0') {
        $ciudadDocumento = ' ' . $textos->id('DE') . ' ' . $objeto->contacto->ciudadDocumento;
    }

    $pestana1a = HTML::parrafo($textos->id('CODIGO_PROVEEDOR'), 'negrilla margenSuperior');
    $pestana1a .= $objeto->idProveedor;
    
    $pestana1a .= HTML::parrafo($textos->id('TIPO_PERSONA'), 'negrilla margenSuperior');
    $pestana1a .= HTML::parrafo($textos->id('TIPO_PERSONA_' . $objeto->tipoPersona), 'margenDerecha', ''); 
    
    $pestana1a .= HTML::parrafo($textos->id('RAZON_SOCIAL'), 'negrilla margenSuperior');
    $pestana1a .= $objeto->razonSocial;    

    $pestana1b = HTML::parrafo($textos->id('NOMBRE_PROVEEDOR'), 'negrilla margenSuperior');
    $pestana1b .= $objeto->nombre;

    $pestana1a .= HTML::parrafo($textos->id('SEDE_PRINCIPAL'), 'negrilla margenSuperior margenInferior subtitulo letraVerde');
    if($objeto->callCenter){
        $pestana1a .= HTML::parrafo($textos->id('CALL_CENTER'), 'negrilla margenSuperior');
        $pestana1a .= $objeto->callCenter;
    }
    if($objeto->sede->nombre){
        $pestana1a .= HTML::parrafo($textos->id('NOMBRE_SEDE'), 'negrilla margenSuperior');
        $pestana1a .= $objeto->sede->nombre;
    }
    $pestana1a .= HTML::parrafo($textos->id('CIUDAD_UBICACION'), 'negrilla margenSuperior');
    $pestana1a .= $objeto->sede->nombreCiudad;
    if($objeto->sede->fax){
        $pestana1b .= HTML::parrafo($textos->id('FAX'), 'negrilla margenSuperior');
        $pestana1b .= $objeto->sede->fax;        
    }

    $pestana1a .= HTML::parrafo($textos->id('TELEFONO'), 'negrilla margenSuperior');
    $pestana1a .= $objeto->sede->telefono;
    $pestana1a .= HTML::parrafo($textos->id('CELULAR'), 'negrilla margenSuperior');
    $pestana1a .= $objeto->sede->celular;    
    $pestana1a .= HTML::parrafo($textos->id('DIRECCION'), 'negrilla margenSuperior');
    $pestana1a .= $objeto->sede->direccion;
    if (!empty($objeto->observaciones)) {
        $pestana1a .= HTML::parrafo($textos->id('OBSERVACIONES'), 'negrilla margenSuperior');
        $pestana1a .= $objeto->observaciones;
    }
    $pestana1b .= HTML::parrafo($textos->id('CONTACTO_PRINCIPAL'), 'negrilla margenSuperior margenInferior subtitulo letraVerde');
    $pestana1b .= HTML::parrafo($textos->id('DOCUMENTO'), 'negrilla margenSuperior');
    $pestana1b .= $tipoDocumento . ': ' . $objeto->contacto->documentoIdentidad . $ciudadDocumento;
    $pestana1b .= HTML::parrafo($textos->id('NOMBRES'), 'negrilla margenSuperior');
    $pestana1b .= $objeto->contacto->primerNombre . ' ' . $objeto->contacto->segundoNombre;

    $pestana1b .= HTML::parrafo($textos->id('APELLIDOS'), 'negrilla margenSuperior', '');
    $pestana1b .= $objeto->contacto->primerApellido . ' ' . $objeto->contacto->segundoApellido;
    
    $telefonos  = '';
    $telefonos .= 'Cel: '.$objeto->contacto->celular;
    $telefonos .= ($objeto->contacto->telefono) ? ' - Tel: '.$objeto->contacto->telefono : '';
    $telefonos .= ($objeto->contacto->fax) ? ' - Fax: '.$objeto->contacto->fax : '';        
    
    $pestana1b .= HTML::parrafo($textos->id('TELEFONOS'), 'negrilla margenSuperior');
    $pestana1b .= $telefonos;
    $pestana1b .= HTML::parrafo($textos->id('EMAIL'), 'negrilla margenSuperior');
    $pestana1b .= $objeto->contacto->correo;
  
    if (!empty($objeto->contacto->observacionesContacto)) {
        $pestana1b .= HTML::parrafo($textos->id('OBSERVACIONES'), 'negrilla margenSuperior');
        $pestana1b .= $objeto->contacto->observacionesContacto;
    }

    $contenedora = HTML::contenedor($pestana1a, 'contenedorIzquierdo');
    $contenedorb = HTML::contenedor($pestana1b, 'contenedorDerecho');

    $pestana1 .= HTML::contenedor($contenedora . $contenedorb, 'pestana1 overflowAuto');

    if (!empty($objeto->listaContactos)) {
        //crear los formularios con la info para las demas sedes
        $datosTablaMasContactos = array(
            HTML::parrafo($textos->id('NOMBRE'), 'centrado')        => 'nombres',
            HTML::parrafo($textos->id('APELLIDO'), 'centrado')      => 'apellidos',
            HTML::parrafo($textos->id('TELEFONOS'), 'centrado')     => 'telefonos',
            HTML::parrafo($textos->id('EMAIL'), 'centrado')         => 'correo',
            HTML::parrafo($textos->id('OBSERVACIONES'), 'centrado') => 'observaciones'
        );

        $rutas = array(
            'ruta_consultar' => '/ajax/proveedores/consultarContacto',
            'ruta_modificar' => '/ajax/proveedores/modificarContacto',
            'ruta_eliminar' => '/ajax/proveedores/eliminarContacto'
        );

        $listaContactos = array();
        while ($fila = $sql->filaEnObjeto($objeto->listaContactos)) {
            $persona = new Persona($fila->idPersona);
            
            $fila->nombres      = $persona->primerNombre . ' ' . $persona->segundoNombre;
            $fila->apellidos    = $persona->primerApellido . ' ' . $persona->segundoApellido;            
            $telefonos  = '';
            $telefonos .= 'Cel: '.$persona->celular;
            $telefonos .= ($persona->telefono) ? ' - Tel: '.$persona->telefono : '';
            $telefonos .= ($persona->fax) ? ' - Fax: '.$persona->fax : '';            
            $fila->telefonos    = $telefonos;
            $fila->correo       = $persona->correo;
            $fila->observaciones = ($fila->observaciones) ? $fila->observaciones : 'Ninguna.';  
            
            $listaContactos[]   = $fila;
        }


        $idTabla = 'tablaConsultarMasContactos';
        $estilosColumnas = array('ancho100px', 'ancho100px', 'ancho125px', 'ancho200px', 'ancho150px');
        $contenedorContactos = HTML::contenedor(Recursos::generarTablaRegistrosInterna($listaContactos, $datosTablaMasContactos, $rutas, $idTabla, $estilosColumnas), 'flotanteIzquierda margenDerecha');
    }

    $pestana2 = HTML::contenedor($contenedorContactos, 'pestana1');

    if (!empty($objeto->listaSedes)) {
        //crear los formularios con la info para las demas sedes
        $datosTablaMasSedes = array(
            HTML::parrafo($textos->id('NOMBRE'), 'centrado')    => 'nombre',
            HTML::parrafo($textos->id('CIUDAD'), 'centrado')    => 'ciudad',
            HTML::parrafo($textos->id('DIRECCION'), 'centrado') => 'direccion',
            HTML::parrafo($textos->id('TELEFONO'), 'centrado')  => 'telefono',
            HTML::parrafo($textos->id('CELULAR'), 'centrado')  => 'celular',
            HTML::parrafo($textos->id('FAX'), 'centrado')       => 'fax'
        );

        $rutas = array(
            'ruta_consultar' => '/ajax/proveedores/consultarSede',
            'ruta_modificar' => '/ajax/proveedores/modificarSede',
            'ruta_eliminar' => '/ajax/proveedores/eliminarSede'
        );

        $listaSedes = array();

        while ($fila = $sql->filaEnObjeto($objeto->listaSedes)) {
            $listaSedes[] = $fila;
        }



        $idTabla = 'tablaConsultarMasSedes';
        $estilosColumnas = array('ancho125px', 'ancho150px', 'ancho125px', 'ancho100px', 'ancho100px');
        $contenedorc = HTML::contenedor(Recursos::generarTablaRegistrosInterna($listaSedes, $datosTablaMasSedes, $rutas, $idTabla, $estilosColumnas), 'flotanteIzquierda margenDerecha');
    }

    $pestana3 = HTML::contenedor($contenedorc, 'pestana1');


    if (!empty($objeto->listaCuentas)) {
        //crear los formularios con la info para las demas sedes
        $datosTablaCuentas = array(
            HTML::parrafo($textos->id('BANCO'), 'centrado') => 'banco',
            HTML::parrafo($textos->id('NUMERO_CUENTA'), 'centrado') => 'numeroCuenta',
            HTML::parrafo($textos->id('TIPO_CUENTA'), 'centrado') => 'tipoCuenta',
        );

        $rutas = array(
            'ruta_modificar' => '/ajax/proveedores/modificarCuenta',
            'ruta_eliminar' => '/ajax/proveedores/eliminarCuenta'
        );

        $listaCuentas = array();
        while ($fila = $sql->filaEnObjeto($objeto->listaCuentas)) {
            $fila->tipoCuenta = $textos->id('TIPO_CUENTA' . $fila->tipoCuenta);
            $listaCuentas[] = $fila;
        }

        $idTabla = 'tablaConsultarCuentas';
        $estilosColumnas = array('ancho200px', 'ancho200px', 'ancho200px');
        $contenedorCuentas = HTML::contenedor(Recursos::generarTablaRegistrosInterna($listaCuentas, $datosTablaCuentas, $rutas, $idTabla, $estilosColumnas), 'flotanteIzquierda margenDerecha');
    }
    $pestana4 = HTML::contenedor($contenedorCuentas, 'pestana1');

    $pestana5 = ''; 
    
    $pestana5 .= HTML::parrafo($textos->id('REGIMEN'), 'negrilla margenSuperior');
    $pestana5 .= HTML::parrafo( $textos->id('REGIMEN_' . $objeto->regimen), 'margenDerecha', '');
    $pestana5 .= HTML::parrafo($textos->id('ACTIVIDAD_ECONOMICA') , 'negrilla margenSuperior');
    $pestana5 .= HTML::parrafo($objeto->actividadEconomica->nombre, 'margenDerecha', '');    


//    $fraseAutoretenedor     = HTML::frase($textos->id('AUTORETENEDOR'), 'negrilla ');
//    $autoretenedor          = HTML::frase($textos->id('AUTORETENEDOR_' . $objeto->autoretenedor), '', 'checkAutoretenedor');
//    $fraseRetefuente        = HTML::frase($textos->id('RETEFUENTE'), 'negrilla margenIzquierda');
//    $retefuente             = HTML::frase($textos->id('RETEFUENTE_' . $objeto->retefuente), '', 'checkRetefuente');
//    $fraseReteica           = HTML::frase($textos->id('RETEICA'), 'negrilla margenIzquierda');
//    $reteica                = HTML::frase($textos->id('RETEICA_' . $objeto->reteica), '', 'checkReteica');
//    $fraseRetecree          = HTML::frase($textos->id('RETECREE'), 'negrilla margenIzquierda');
//    $retecree               = HTML::frase($textos->id('RETECREE_' . $objeto->retecree), '', 'checkReteica');    
//
//    $pestana5 .= HTML::parrafo($fraseAutoretenedor . $fraseRetefuente . $fraseReteica . $fraseRetecree, 'negrilla margenSuperior');
//    $pestana5 .= HTML::parrafo($autoretenedor . $retefuente . $reteica . $retecree, '');    
//    

    $pestanas = array(
        HTML::frase($textos->id('INFORMACION_PROVEEDOR'), 'letraBlanca')    => $pestana1,
        HTML::frase($textos->id('LISTA_CONTACTOS'), 'letraBlanca')          => $pestana2,
        HTML::frase($textos->id('LISTA_SEDES'), 'letraBlanca')              => $pestana3,
        HTML::frase($textos->id('INFORMACION_BANCARIA'), 'letraBlanca')     => $pestana4,
        HTML::frase($textos->id('INFORMACION_TRIBUTARIA'), 'letraBlanca')   => $pestana5,
    );

    $codigo .= HTML::pestanas2('consultarProveedor', $pestanas); //al id concatenarle la fecha con segundos para que no haya problema


    $respuesta['generar']   = true;
    $respuesta['titulo']    = HTML::parrafo($textos->id('CONSULTAR_ITEM'), 'letraBlanca negrilla subtitulo');
    $respuesta['destino']   = '#cuadroDialogo';
    $respuesta['ancho']     = 800;
    $respuesta['alto']      = 550;
    $respuesta['codigo']    = $codigo;

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
    global $textos, $sql, $configuracion;

    $objeto     = new Proveedor();
    $destino    = '/ajax' . $objeto->urlBase . '/add';
    $respuesta  = array();
    $codigo     = '';

    if (empty($datos)) {

        $pestana1 = HTML::campoOculto('procesar', 'true');
        $pestana1 .= HTML::campoOculto('datos[dialogo]', '', 'idDialogo');
        //seleccionar los tipos de documentos
        $tiposDocumentos = $sql->seleccionar(array('tipos_documento'), array('id', 'nombre'), 'id NOT IN (0)', 'id', 'nombre ASC');
        $tiposDoc = array();
        while ($tiposDocumento = $sql->filaEnObjeto($tiposDocumentos)) {
            $tiposDoc[$tiposDocumento->id] = $tiposDocumento->nombre;
        }
        $listaTipodocumento = HTML::listaDesplegable('datos[id_tipo_documento]', $tiposDoc, '', '', 'listaTipoDocumentoPersona');

        $arregloRegimen =  $configuracion["REGIMENES"][$configuracion["GENERAL"]["idioma"]];
        
        $listaRegimen = HTML::listaDesplegable('datos[regimen]', $arregloRegimen, '1');


        $listaTipoPersona = HTML::listaDesplegable('datos[tipo_persona]', array('1' => $textos->id('NATURAL'), '2' => $textos->id('JURIDICA')), '', 'listaTipoPersona', 'listaTipoPersona', '', array('alt' => $textos->id('SELECCIONE_TIPO_PERSONA')));
        $pestana1a .= HTML::parrafo($textos->id('TIPO_PERSONA'), 'negrilla margenSuperior');
        $pestana1a .= $listaTipoPersona;
        $pestana1a .= HTML::parrafo($textos->id('NOMBRE_PROVEEDOR'), 'negrilla margenSuperior ', 'textoNombreProveedor');
        $pestana1a .= HTML::campoTexto('datos[nombre_comercial]', 40, 50, '', 'campoObligatorio ', 'nombreProveedor');
        $pestana1a .= HTML::parrafo($textos->id('RAZON_SOCIAL'), 'negrilla margenSuperior ', 'textoNombreProveedor');
        $pestana1a .= HTML::campoTexto('datos[razon_social]', 40, 50, '', 'campoObligatorio ', 'nombreProveedor');        
        $pestana1a .= HTML::parrafo($textos->id('CODIGO_PROVEEDOR'), 'negrilla margenSuperior ', 'textoIdProveedor');
        $pestana1a .= HTML::campoTexto('datos[id_proveedor]', 30, 50, '', 'campoObligatorio ', 'idProveedor');
        $pestana1a .= HTML::parrafo($textos->id('CALL_CENTER'), 'negrilla margenSuperior');
        $pestana1a .= HTML::campoTexto('datos[call_center]', 30, 30, '', '', '');

        $pestana1a .= HTML::parrafo($textos->id('NOMBRE_SEDE'), 'negrilla margenSuperior');
        $pestana1a .= HTML::campoTexto('datos[nombre_sede]', 30, 50, '', '');
        $pestana1a .= HTML::parrafo($textos->id('CIUDAD_UBICACION'), 'negrilla margenSuperior');
        $pestana1a .= HTML::campoTexto('datos[ciudad_sede]', 35, 255, '', 'autocompletable campoObligatorio', 'ciudadSede', array('title' => '/ajax/ciudades/listarCiudades'), $textos->id('SELECCIONE_CIUDAD_SEDE'), '', 'datos[id_ciudad_sede]');
        $pestana1b .= HTML::parrafo($textos->id('FAX'), 'negrilla margenSuperior');
        $pestana1b .= HTML::campoTexto('datos[fax_sede]', 30, 50, '', '', '', array('title' => $textos->id('')));
        $pestana1b .= HTML::parrafo($textos->id('TELEFONO'), 'negrilla margenSuperior');
        $pestana1b .= HTML::campoTexto('datos[telefono_sede]', 30, 50, '', 'campoObligatorio', 'telefonoSede', array('title' => $textos->id('')));
        $pestana1b .= HTML::parrafo($textos->id('CELULAR'), 'negrilla margenSuperior');
        $pestana1b .= HTML::campoTexto('datos[celular_sede]', 30, 50, '', 'campoObligatorio', 'celularSede', array('title' => $textos->id('')));        

        $pestana1b .= HTML::parrafo($textos->id('DIRECCION'), 'negrilla margenSuperior');
        $pestana1b .= HTML::campoTexto('datos[direccion_sede]', 30, 50, '', 'campoObligatorio', 'direccionSede', array('title' => $textos->id('')));

        $pestana1b .= HTML::parrafo($textos->id('OBSERVACIONES'), 'negrilla margenSuperior');
        $pestana1b .= HTML::areaTexto('datos[observaciones]', 3, 40, '');
        $pestana1b .= HTML::parrafo($textos->id('ACTIVO') , 'negrilla margenSuperior');
        $pestana1b .= HTML::parrafo(HTML::campoChequeo('datos[activo]', true), '');        

        $contenedora = HTML::contenedor($pestana1a, 'contenedorIzquierdo');
        $contenedorb = HTML::contenedor($pestana1b, 'contenedorDerecho');

        $pestana1 .= HTML::contenedor($contenedora . $contenedorb, 'pestana1');

        $pestana2a .= HTML::parrafo($textos->id('TIPO_DOCUMENTO'), 'negrilla margenSuperior');
        $pestana2a .= $listaTipodocumento;
        $pestana2a .= HTML::parrafo($textos->id('DOCUMENTO'), 'negrilla margenSuperior');
        $pestana2a .= HTML::campoTexto('datos[documento_identidad]', 25, 50, '', 'autocompletable', 'campoDocumentoPersona', array('title' => HTML::urlInterna('INICIO', 0, true, 'listarPersonas')), $textos->id('AYUDA_USO_AUTOCOMPLETAR')) . HTML::frase('Persona ya registrada.', 'margenIzquierda letraVerde oculto', 'frasePersonaRegistrada');
        $pestana2a .= HTML::parrafo($textos->id('PRIMER_NOMBRE'), 'negrilla margenSuperior');
        $pestana2a .= HTML::campoTexto('datos[primer_nombre]', 30, 50, '', 'campoObligatorio', 'campoPrimerNombrePersona', array('title' => $textos->id('')));
        $pestana2a .= HTML::parrafo($textos->id('SEGUNDO_NOMBRE'), 'negrilla margenSuperior');
        $pestana2a .= HTML::campoTexto('datos[segundo_nombre]', 30, 50, '', '', 'campoSegundoNombrePersona', array('title' => $textos->id('')));
        $pestana2a .= HTML::parrafo($textos->id('PRIMER_APELLIDO'), 'negrilla margenSuperior', '');
        $pestana2a .= HTML::campoTexto('datos[primer_apellido]', 30, 50, '', 'campoObligatorio', 'campoPrimerApellidoPersona');
        $pestana2a .= HTML::parrafo($textos->id('SEGUNDO_APELLIDO'), 'negrilla margenSuperior');
        $pestana2a .= HTML::campoTexto('datos[segundo_apellido]', 30, 50, '', '', 'campoSegundoApellidoPersona');

        $pestana2b .= HTML::parrafo($textos->id('EMAIL'), 'negrilla margenSuperior');
        $pestana2b .= HTML::campoTexto('datos[correo]', 30, 50, '', '', 'campoCorreoPersona', array('title' => $textos->id('')));

        $pestana2b .= HTML::parrafo($textos->id('TELEFONO'), 'negrilla margenSuperior');
        $pestana2b .= HTML::campoTexto('datos[telefono]', 30, 50, '', '', 'campoTelefonoPersona', array('title' => $textos->id('')));

        $pestana2b .= HTML::parrafo($textos->id('CELULAR'), 'negrilla margenSuperior');
        $pestana2b .= HTML::campoTexto('datos[celular]', 30, 50, '', 'campoObligatorio', 'campoCelularPersona', array('title' => $textos->id('')));

        $pestana2b .= HTML::parrafo($textos->id('FAX'), 'negrilla margenSuperior');
        $pestana2b .= HTML::campoTexto('datos[fax]', 30, 50, '', '', 'campoFaxPersona', array('title' => $textos->id('')));
        $pestana2b .= HTML::parrafo($textos->id('OBSERVACIONES'), 'negrilla margenSuperior');
        $pestana2b .= HTML::areaTexto('datos[observaciones_contacto]', 2, 40, '');

        $contenedor1 = HTML::contenedor($pestana2a, 'contenedorIzquierdo');
        $contenedor2 = HTML::contenedor($pestana2b, 'contenedorDerecho');

        $pestana2 = HTML::contenedor($contenedor1 . $contenedor2, 'pestana1');

        $arregloTipoCuenta = array(
            '1' => 'Ahorros',
            '2' => 'Corriente'
        );
        $listaTipoCuenta = HTML::listaDesplegable('datos[tipo_cuenta]', $arregloTipoCuenta, '', '', 'listaTipoCuenta');

        $banco = HTML::parrafo($textos->id('BANCO'), 'negrilla margenSuperior');
        $banco .= HTML::campoTexto('datos[banco]', 40, 255, '', 'autocompletable campoObligatorio', 'campoNombreBanco', array('title' => HTML::urlInterna('BANCOS', 0, true, 'listarBancos')), $textos->id('AYUDA_USO_AUTOCOMPLETAR'), HTML::urlInterna('BANCOS', 0, true, 'add'), 'datos[id_banco]');
        $numCuenta = HTML::parrafo($textos->id('NUMERO_CUENTA'), 'negrilla margenSuperior');
        $numCuenta .= HTML::campoTexto('datos[numero_cuenta]', 25, 50, '', 'campoObligatorio', 'campoNumeroCuenta') . HTML::frase($listaTipoCuenta, 'margenIzquierda');

        $codigo1 = HTML::contenedorCampos($banco, $numCuenta);

        $boton = HTML::parrafo(HTML::boton('mas', $textos->id('ADICIONAR_CUENTA'), ' directo margenSuperiorDoble', '', 'botonAdicionarcuenta', '', array('validar' => 'NoValidar')) . HTML::frase($textos->id('CUENTA_AGREGADA'), 'margenIzquierda letraVerde oculto', 'textoCuentaAgregada'), 'margensuperior');

        $columnas = array($textos->id('BANCO'), $textos->id('NUMERO_CUENTA'), $textos->id('TIPO_CUENTA'), $textos->id('ELIMINAR'));

        $claseColumnas = array('ancho200px', 'ancho200px', 'ancho150px', 'ancho75px');

        $tabla = HTML::contenedor(HTML::tabla($columnas, '', '', 'tablaCuentasBancosProveedores', $claseColumnas), 'margenSuperior contenedorTablaCuentasBancos');

        $pestana3 = HTML::contenedor($codigo1 . $boton . $tabla, 'pestana1');
             
        $pestana4 = '';
        $pestana4 .= HTML::parrafo($textos->id('REGIMEN') , 'negrilla margenSuperior');
        $pestana4 .= $listaRegimen ;
        
        $pestana4 .= HTML::parrafo($textos->id('ACTIVIDAD_ECONOMICA'), 'negrilla margenSuperior');
        $pestana4 .= HTML::campoTexto('datos[actividad_economica]', 40, 255, '', 'autocompletable campoObligatorio', 'campoActividadEconomica', array('title' => HTML::urlInterna('ACTIVIDADES_ECONOMICAS', 0, true, 'listar')), $textos->id('AYUDA_USO_AUTOCOMPLETAR'), HTML::urlInterna('ACTIVIDADES_ECONOMICAS', 0, true, 'add'), 'datos[id_actividad_economica]');        

//        $fraseAutoretenedor = HTML::frase($textos->id('AUTORETENEDOR'), 'negrilla margenIzquierda');
//        $checkAutoretenedor = HTML::campoChequeo('datos[autoretenedor]', '', '', 'checkAutoretenedor');
//        $fraseRetefuente = HTML::frase($textos->id('RETEFUENTE'), 'negrilla margenIzquierda');
//        $checkRetefuente = HTML::campoChequeo('datos[retefuente]', true, 'check-tributo', 'checkRetefuente');
//        $fraseReteica = HTML::frase($textos->id('RETEICA'), 'negrilla margenIzquierda');
//        $checkReteica = HTML::campoChequeo('datos[reteica]', true, 'check-tributo', 'checkReteica');
//        $fraseRetecre = HTML::frase($textos->id('RETECREE'), 'negrilla margenIzquierda');
//        $checkRetecre = HTML::campoChequeo('datos[retecree]', true, 'check-tributo', 'checkRetecre');        
//
//        $pestana4 .= HTML::parrafo($fraseAutoretenedor . $fraseRetefuente . $fraseReteica . $fraseRetecre, 'negrilla margenSuperior');
//        $pestana4 .= HTML::parrafo($checkAutoretenedor . $checkRetefuente . $checkReteica . $checkRetecre, '');        

        $pestanas = array(
            HTML::frase($textos->id('INFORMACION_PROVEEDOR'), 'letraBlanca')   => $pestana1,
            HTML::frase($textos->id('INFORMACION_CONTACTO'), 'letraBlanca')    => $pestana2,
            HTML::frase($textos->id('INFORMACION_BANCARIA'), 'letraBlanca')    => $pestana3,
            HTML::frase($textos->id('INFORMACION_TRIBUTARIA'), 'letraBlanca')  => $pestana4,
        );

        $codigo .= HTML::pestanas2('pestanasAgregar', $pestanas);

        $codigo .= HTML::parrafo(HTML::boton('chequeo', $textos->id('ACEPTAR'), ' directo', '', 'botonAgregarProveedores', '', ''), 'margenSuperior');
        $codigo .= HTML::frase($textos->id('REGISTRO_AGREGADO'), 'textoExitoso', 'textoExitoso');
        //campo oculto donde guardare via javascript la cadena que contrandra las cuentas y sus numeros
        $codigo .= HTML::campoOculto('datos[cuentas_proveedor]', '', 'campoCadenaCuentasProveedor');
        $codigo = HTML::forma($destino, $codigo, 'P', true, 'formaAgregarProveedor', '', 'formaAgregarProveedor');

        $respuesta['generar']       = true;
        $respuesta['cargarJs']      = true;
        $respuesta['archivoJs']     = $configuracion['SERVIDOR']['media'] . $configuracion['RUTAS']['javascript'] . '/modulos/proveedores/funcionesAgregarProveedor.js';
        $respuesta['titulo']        = HTML::parrafo($textos->id('ADICIONAR_ITEM'), 'letraBlanca negrilla subtitulo');
        $respuesta['destino']       = '#cuadroDialogo';
        $respuesta['ancho']         = 800;
        $respuesta['alto']          = 550;
        $respuesta['codigo']        = $codigo;
        
    } else {

        $respuesta['error'] = true;
        
        $existeProveedor    = $sql->existeItem('proveedores', 'nombre', $datos['nombre_comercial']);
        $existeNit          = $sql->existeItem('proveedores', 'id_proveedor', $datos['id_proveedor']);

        if ($existeProveedor) {
            $respuesta['mensaje'] = $textos->id('ERROR_PROVEEDOR_EXISTENTE');
            
        } elseif (empty($datos['nombre_comercial'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_NOMBRE_COMERCIAL');
            
        } elseif (empty($datos['id_proveedor'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_CODIGO_PROVEEDOR');
            
        } elseif (empty($datos['id_ciudad_sede'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_CIUDAD_SEDE');
            
        } elseif (empty($datos['direccion_sede'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_DIRECCION_SEDE');
            
        } elseif (empty($datos['telefono_sede'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_TELEFONO_SEDE');
            
        } elseif (empty($datos['documento_identidad'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_DOCUMENTO_CONTACTO');
            
        } elseif (empty($datos['primer_nombre'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_NOMBRE');
            
        } elseif (empty($datos['primer_apellido'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_APELLIDO');
            
        } elseif (empty($datos['actividad_economica'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_ACTIVIDAD_ECONOMICA');
            
        } elseif ($existeNit) {
            $respuesta['mensaje'] = $textos->id('ERROR_EXISTE_NIT_PROVEEDOR');
            
        } else {

            $idItem = $objeto->adicionar($datos);
            
            if ($idItem) {
                /*                 * ************** Creo el nuevo item que se insertara via ajax *************** */
                $objeto = new Proveedor($idItem);

               $estado = ($objeto->activo) ?  HTML::frase($textos->id('ACTIVO'), 'activo') : HTML::frase($textos->id('INACTIVO'), 'inactivo');
                
                $celdas         = array($objeto->idProveedor, $objeto->nombre, $objeto->contacto->primerNombre, $objeto->contacto->primerApellido, $objeto->contacto->celular, $objeto->callCenter, $estado);
                $claseFila      = '';
                $idFila         = $idItem;
                $celdas1        = HTML::crearNuevaFila($celdas, $claseFila, $idFila);

                $respuesta['error']             = false;
                $respuesta['accion']            = 'insertar';
                $respuesta['contenido']         = $celdas1;
                $respuesta['idContenedor']      = '#tr_' . $idItem;
                $respuesta['idDestino']         = '#tablaRegistros';
                //estos dos son necesarios para agregar el nuevo registro a los plugin chosen
                $respuesta['idSelect']          = '#selectorProveedores';
                $respuesta['idYNombre']         = $objeto->id .'|'. $objeto->nombre;
                
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
    global $textos, $sql, $configuracion;

    if (empty($id) || (!$sql->existeItem('proveedores', 'id', $id))) {
        $respuesta              = array();
        $respuesta['error']     = true;
        $respuesta['mensaje']   = $textos->id('NO_HA_SELECCIONADO_ITEM');

        Servidor::enviarJSON($respuesta);
        return NULL;
    }

    $objeto         = new Proveedor($id);
    $destino        = '/ajax' . $objeto->urlBase . '/edit';
    $respuesta      = array();
    $codigo         = '';

    if (empty($datos)) {

        $pestana1 = HTML::campoOculto('procesar', 'true');
        $pestana1 .= HTML::campoOculto('id', $id, 'idProveedor');
        $pestana1 .= HTML::campoOculto('datos[dialogo]', '', 'idDialogo');
        //seleccionar los tipos de documentos
        $tiposDocumentos = $sql->seleccionar(array('tipos_documento'), array('id', 'nombre'), 'id NOT IN (0)', 'id', 'nombre ASC');
        $tiposDoc = array();
        while ($tiposDocumento = $sql->filaEnObjeto($tiposDocumentos)) {
            $tiposDoc[$tiposDocumento->id] = $tiposDocumento->nombre;
        }
        $listaTipodocumento = HTML::listaDesplegable('datos[id_tipo_documento]', $tiposDoc, $objeto->contacto->tipoDocumento, '', 'listaTipoDocumentoPersona');

        $arregloRegimen =  $configuracion["REGIMENES"][$configuracion["GENERAL"]["idioma"]];
        
        $listaTipoPersona = HTML::listaDesplegable('datos[tipo_persona]', array('1' => $textos->id('NATURAL'), '2' => $textos->id('JURIDICA')),  $objeto->tipoPersona, 'listaTipoPersona', 'listaTipoPersona', '', array('alt' => $textos->id('SELECCIONE_TIPO_PERSONA')));
        $pestana1a  = '';
        $pestana1a .= HTML::parrafo($textos->id('TIPO_PERSONA'), 'negrilla margenSuperior');
        $pestana1a .= $listaTipoPersona;
        
//        $clase = '';
//        if($objeto->tipoPersona == '1'){
//            $clase = 'oculto';
//        }
        
        $pestana1a .= HTML::parrafo($textos->id('NOMBRE_PROVEEDOR'), 'negrilla margenSuperior '/*.$clase*/, 'textoNombreProveedor');
        $pestana1a .= HTML::campoTexto('datos[nombre_comercial]', 40, 50, $objeto->nombre, 'campoObligatorio '/*.$clase*/, 'nombreProveedor');
        $pestana1a .= HTML::parrafo($textos->id('RAZON_SOCIAL'), 'negrilla margenSuperior '/*.$clase*/, 'textoNombreProveedor');
        $pestana1a .= HTML::campoTexto('datos[razon_social]', 40, 50, $objeto->razonSocial, 'campoObligatorio '/*.$clase*/, 'nombreProveedor');        
        $pestana1a .= HTML::parrafo($textos->id('CODIGO_PROVEEDOR'), 'negrilla margenSuperior '/*.$clase*/, 'textoIdProveedor');
        $pestana1a .= HTML::campoTexto('datos[id_proveedor]', 30, 50, $objeto->idProveedor, 'campoObligatorio '/*.$clase*/, 'idProveedor');        
        $pestana1a .= HTML::parrafo($textos->id('CALL_CENTER'), 'negrilla margenSuperior');
        $pestana1a .= HTML::campoTexto('datos[call_center]', 30, 30, $objeto->callCenter, '', '');
        $pestana1a .= HTML::parrafo($textos->id('NOMBRE_SEDE'), 'negrilla margenSuperior');
        $pestana1a .= HTML::campoTexto('datos[nombre_sede]', 30, 50, $objeto->sede->nombre, '');
        $pestana1a .= HTML::parrafo($textos->id('CIUDAD_UBICACION'), 'negrilla margenSuperior');
        $pestana1a .= HTML::campoTexto('datos[ciudad_sede]', 35, 255, $objeto->sede->nombreCiudad, 'autocompletable campoObligatorio', 'ciudadSede', array('title' => '/ajax/ciudades/listarCiudades'), $textos->id('SELECCIONE_CIUDAD_SEDE'), '', 'datos[id_ciudad_sede]', $objeto->sede->id_ciudad);
        $pestana1b  = HTML::parrafo($textos->id('FAX'), 'negrilla margenSuperior');
        $pestana1b .= HTML::campoTexto('datos[fax_sede]', 30, 50, $objeto->sede->fax, '', '', array('title' => $textos->id('')));
        $pestana1b .= HTML::parrafo($textos->id('TELEFONO'), 'negrilla margenSuperior');
        $pestana1b .= HTML::campoTexto('datos[telefono_sede]', 30, 50, $objeto->sede->telefono, 'campoObligatorio', 'telefonoSede', array('title' => $textos->id('')));
        $pestana1b .= HTML::parrafo($textos->id('CELULAR'), 'negrilla margenSuperior');
        $pestana1b .= HTML::campoTexto('datos[celular_sede]', 30, 50, $objeto->sede->celular, 'campoObligatorio', 'celularSede', array('title' => $textos->id('')));         
        $pestana1b .= HTML::parrafo($textos->id('DIRECCION'), 'negrilla margenSuperior');
        $pestana1b .= HTML::campoTexto('datos[direccion_sede]', 30, 50, $objeto->sede->direccion, 'campoObligatorio', 'direccionSede', array('title' => $textos->id('')));
        $pestana1b .= HTML::parrafo($textos->id('OBSERVACIONES'), 'negrilla margenSuperior');
        $pestana1b .= HTML::areaTexto('datos[observaciones]', 5, 40, $objeto->observaciones);
        $pestana1b .= HTML::parrafo($textos->id('ACTIVO') , 'negrilla margenSuperior');
        $pestana1b .= HTML::parrafo(HTML::campoChequeo('datos[activo]', true), '');         

        $contenedora = HTML::contenedor($pestana1a, 'contenedorIzquierdo');
        $contenedorb = HTML::contenedor($pestana1b, 'contenedorDerecho');

        $pestana1 .= HTML::contenedor($contenedora . $contenedorb, 'pestana1 overflowAuto');

        $pestana2a  = '';
        $pestana2a .= HTML::parrafo($textos->id('DOCUMENTO'), 'negrilla margenSuperior');
        $pestana2a .= HTML::campoTexto('datos[documento_identidad]', 30, 50, $objeto->contacto->documentoIdentidad, 'campoObligatorio autocompletable', 'campoDocumentoPersona', array('title' => HTML::urlInterna('INICIO', 0, true, 'listarPersonas')), $textos->id('AYUDA_USO_AUTOCOMPLETAR')) . HTML::frase('Persona ya registrada.', 'margenIzquierda letraVerde oculto', 'frasePersonaRegistrada');
        $pestana2a .= HTML::parrafo($textos->id('TIPO_DOCUMENTO'), 'negrilla margenSuperior');
        $pestana2a .= $listaTipodocumento;
        $pestana2a .= HTML::parrafo($textos->id('PRIMER_NOMBRE'), 'negrilla margenSuperior');
        $pestana2a .= HTML::campoTexto('datos[primer_nombre]', 30, 50, $objeto->contacto->primerNombre, 'campoObligatorio', 'campoPrimerNombrePersona', array('title' => $textos->id('')));
        $pestana2a .= HTML::parrafo($textos->id('SEGUNDO_NOMBRE'), 'negrilla margenSuperior');
        $pestana2a .= HTML::campoTexto('datos[segundo_nombre]', 30, 50, $objeto->contacto->segundoNombre, '', 'campoSegundoNombrePersona', array('title' => $textos->id('')));
        $pestana2a .= HTML::parrafo($textos->id('PRIMER_APELLIDO'), 'negrilla margenSuperior', '');
        $pestana2a .= HTML::campoTexto('datos[primer_apellido]', 30, 50, $objeto->contacto->primerApellido, 'campoObligatorio', 'campoPrimerApellidoPersona');

        $pestana2b  = '';
        $pestana2b .= HTML::parrafo($textos->id('SEGUNDO_APELLIDO'), 'negrilla margenSuperior');
        $pestana2b .= HTML::campoTexto('datos[segundo_apellido]', 30, 50, $objeto->contacto->segundoApellido, '', 'campoSegundoApellidoPersona');
        $pestana2b .= HTML::parrafo($textos->id('EMAIL'), 'negrilla margenSuperior');
        $pestana2b .= HTML::campoTexto('datos[correo]', 30, 50, $objeto->contacto->correo, '', 'campoCorreoPersona', array('title' => $textos->id('')));
        $pestana2b .= HTML::parrafo($textos->id('TELEFONO'), 'negrilla margenSuperior');
        $pestana2b .= HTML::campoTexto('datos[telefono]', 30, 50, $objeto->contacto->telefono, '', '', array('title' => $textos->id('')));

        $pestana2b .= HTML::parrafo($textos->id('CELULAR'), 'negrilla margenSuperior');
        $pestana2b .= HTML::campoTexto('datos[celular]', 30, 50, $objeto->contacto->celular, 'campoObligatorio', 'campoCelularPersona', array('title' => $textos->id('')));

        $pestana2b .= HTML::parrafo($textos->id('FAX'), 'negrilla margenSuperior');
        $pestana2b .= HTML::campoTexto('datos[fax]', 30, 50, $objeto->contacto->fax, '', '', array('title' => $textos->id('')));
        $pestana2b .= HTML::parrafo($textos->id('OBSERVACIONES'), 'negrilla margenSuperior');
        $pestana2b .= HTML::areaTexto('datos[observaciones_contacto]', 2, 40, $objeto->contacto->observacionesContacto);


        $contenedor1 = HTML::contenedor($pestana2a, 'contenedorIzquierdo');
        $contenedor2 = HTML::contenedor($pestana2b, 'contenedorDerecho');

        if (!empty($objeto->hayMasContactos)) {
            //crear los formularios con la info para las demas sedes
            $datosTablaMasContactos = array(
                HTML::parrafo($textos->id('NOMBRE'), 'centrado') => 'nombres',
                HTML::parrafo($textos->id('APELLIDO'), 'centrado') => 'apellidos',
                HTML::parrafo($textos->id('CELULAR'), 'centrado') => 'celular',
                HTML::parrafo($textos->id('EDITAR'), 'centrado') => 'botonEditar',
                HTML::parrafo($textos->id('ELIMINAR'), 'centrado') => 'botonEliminar'
            );

            $rutas = array(
                'ruta_consultar' => '/ajax/proveedores/consultarContacto',
                'ruta_modificar' => '/ajax/proveedores/modificarContacto',
                'ruta_eliminar' => '/ajax/proveedores/eliminarContacto'
            );

            $listaContactos = array();
            while ($fila = $sql->filaEnObjeto($objeto->listaContactos)) {
                $persona = new Persona($fila->idPersona);
//                $fila->id        = $persona->id;
                $fila->nombres = $persona->primerNombre . ' ' . $persona->segundoNombre;
                $fila->apellidos = $persona->primerApellido . ' ' . $persona->segundoApellido;
                $fila->celular = $persona->celular;
                $fila->botonEditar = HTML::contenedor('', 'editarRegistro');
                $fila->botonEliminar = HTML::contenedor('', 'eliminarRegistro');
                $listaContactos[] = $fila;
            }

            $idTabla = 'tablaEditarMasContactos';
            $estilosColumnas = array('ancho200px', 'ancho200px', 'ancho150px', 'columnaPequena50', 'columnaPequena50');
            $contenedorc = HTML::contenedor(Recursos::generarTablaRegistrosInterna($listaContactos, $datosTablaMasContactos, $rutas, $idTabla, $estilosColumnas), 'flotanteIzquierda margenDerecha');
        }

        $pestana2 = HTML::contenedor($contenedor1 . $contenedor2 . $contenedorc, 'pestana1');


        if (!empty($objeto->listaSedes)) {
            //crear los formularios con la info para las demas sedes
            $datosTablaMasSedes = array(
                HTML::parrafo($textos->id('NOMBRE'), 'centrado')        => 'nombre',
                HTML::parrafo($textos->id('CIUDAD'), 'centrado')        => 'ciudad',
                HTML::parrafo($textos->id('DIRECCION'), 'centrado')     => 'direccion',
                HTML::parrafo($textos->id('TELEFONO'), 'centrado')      => 'telefono',
                HTML::parrafo($textos->id('EDITAR'), 'centrado')        => 'botonEditar',
                HTML::parrafo($textos->id('ELIMINAR'), 'centrado')      => 'botonEliminar'
            );

            $rutas = array(
                'ruta_consultar' => '/ajax/proveedores/consultarSede',
                'ruta_modificar' => '/ajax/proveedores/modificarSede',
                'ruta_eliminar' => '/ajax/proveedores/eliminarSede'
            );

            $listaSedes = array();

            while ($fila = $sql->filaEnObjeto($objeto->listaSedes)) {
                $fila->botonEditar      = HTML::contenedor('', 'editarRegistro');
                $fila->botonEliminar    = HTML::contenedor('', 'eliminarRegistro');
                $listaSedes[] = $fila;
            }

            $idTabla = 'tablaEditarMasSedes';
            $estilosColumnas = array('ancho150px', 'ancho150px', 'ancho125px', 'ancho100px', 'columnaPequena50', 'columnaPequena50');
            $contenedorSedes = HTML::contenedor(Recursos::generarTablaRegistrosInterna($listaSedes, $datosTablaMasSedes, $rutas, $idTabla, $estilosColumnas), 'flotanteIzquierda margenDerecha');
        }

        $pestana3 = HTML::contenedor($contenedorSedes, 'pestana1');

        if (!empty($objeto->listaCuentas)) {
            //crear los formularios con la info para las demas sedes
            $datosTablaCuentas = array(
                HTML::parrafo($textos->id('BANCO'), 'centrado')             => 'banco',
                HTML::parrafo($textos->id('NUMERO_CUENTA'), 'centrado')     => 'numeroCuenta',
                HTML::parrafo($textos->id('TIPO_CUENTA'), 'centrado')       => 'tipoCuenta',
                HTML::parrafo($textos->id('EDITAR'), 'centrado')            => 'botonEditar',
                HTML::parrafo($textos->id('ELIMINAR'), 'centrado')          => 'botonEliminar'
            );

            $rutas = array(
                'ruta_modificar' => '/ajax/proveedores/modificarCuenta',
                'ruta_eliminar' => '/ajax/proveedores/eliminarCuenta'
            );

            $listaCuentas = array();
            while ($fila = $sql->filaEnObjeto($objeto->listaCuentas)) {
                $fila->tipoCuenta       = $textos->id('TIPO_CUENTA' . $fila->tipoCuenta);
                $fila->botonEditar      = HTML::contenedor('', 'editarRegistro');
                $fila->botonEliminar    = HTML::contenedor('', 'eliminarRegistro');
                $listaCuentas[] = $fila;
            }

            $idTabla = 'tablaEditarCuentas';
            $estilosColumnas = array('ancho200px', 'ancho200px', 'ancho200px', 'columnaPequena50', 'columnaPequena50');
            $contenedorCuentas = HTML::contenedor(Recursos::generarTablaRegistrosInterna($listaCuentas, $datosTablaCuentas, $rutas, $idTabla, $estilosColumnas), 'flotanteIzquierda margenDerecha');
        }
        $pestana4 = HTML::contenedor($contenedorCuentas, 'pestana1');
        
        $pestana5 = '';
        $listaRegimen = HTML::listaDesplegable('datos[regimen]', $arregloRegimen, $objeto->regimen);
        $pestana5 .= HTML::parrafo($textos->id('REGIMEN') , 'negrilla margenSuperior');
        $pestana5 .= $listaRegimen ;

        $pestana5 .= HTML::parrafo($textos->id('ACTIVIDAD_ECONOMICA'), 'negrilla margenSuperior');
        $pestana5 .= HTML::campoTexto('datos[actividad_economica]', 40, 255, $objeto->actividadEconomica->nombre, 'autocompletable campoObligatorio', 'campoActividadEconomica', array('title' => HTML::urlInterna('ACTIVIDADES_ECONOMICAS', 0, true, 'listar')), $textos->id('AYUDA_USO_AUTOCOMPLETAR'), HTML::urlInterna('ACTIVIDADES_ECONOMICAS', 0, true, 'add'), 'datos[id_actividad_economica]', $objeto->idActividadEconomica);        


//        $fraseAutoretenedor = HTML::frase($textos->id('AUTORETENEDOR'), 'negrilla margenIzquierda');
//        $checkAutoretenedor = HTML::campoChequeo('datos[autoretenedor]', $objeto->autoretenedor, '', 'checkAutoretenedor');
//        $fraseRetefuente = HTML::frase($textos->id('RETEFUENTE'), 'negrilla margenIzquierda');
//        $checkRetefuente = HTML::campoChequeo('datos[retefuente]', $objeto->retefuente, 'check-tributo', 'checkRetefuente');
//        $fraseReteica = HTML::frase($textos->id('RETEICA'), 'negrilla margenIzquierda');
//        $checkReteica = HTML::campoChequeo('datos[reteica]', $objeto->reteica, 'check-tributo', 'checkReteica');
//        $fraseRetecre = HTML::frase($textos->id('RETECREE'), 'negrilla margenIzquierda');
//        $checkRetecre = HTML::campoChequeo('datos[retecree]', $objeto->retecree, 'check-tributo', 'checkRetecre');        
//
//        $pestana5 .= HTML::parrafo($fraseAutoretenedor . $fraseRetefuente . $fraseReteica . $fraseRetecre, 'negrilla margenSuperior');
//        $pestana5 .= HTML::parrafo($checkAutoretenedor . $checkRetefuente . $checkReteica . $checkRetecre, '');           


        $pestanas = array(
            HTML::frase($textos->id('INFORMACION_PROVEEDOR'), 'letraBlanca')    => $pestana1,
            HTML::frase($textos->id('LISTA_CONTACTOS'), 'letraBlanca')          => $pestana2,
            HTML::frase($textos->id('LISTA_SEDES'), 'letraBlanca')              => $pestana3,
            HTML::frase($textos->id('INFORMACION_BANCARIA'), 'letraBlanca')     => $pestana4,
            HTML::frase($textos->id('INFORMACION_TRIBUTARIA'), 'letraBlanca')   => $pestana5,
        );

        $codigo .= HTML::pestanas2('pestanasModificar', $pestanas);

        $codigo .= HTML::frase(HTML::boton('chequeo', $textos->id('ACEPTAR'), 'directo', '', 'botonModificarProveedores', '', ''), 'margenSuperior');
        $codigo .= HTML::frase($textos->id('REGISTRO_MODIFICADO'), 'textoExitoso margenIzquierda', 'textoExitoso');
        //campo oculto donde guardare via javascript la cadena que contrandra las cuentas y sus numeros
        $codigo .= HTML::campoOculto('datos[cuentas_proveedor]', '', 'campoCadenaCuentasProveedor');
        $codigo = HTML::forma($destino, $codigo, 'P', true, 'formaEditarProveedor', '', 'formaEditarProveedor');


        $respuesta['generar']       = true;
        $respuesta['cargarJs']      = true;
        $respuesta['archivoJs']     = $configuracion['SERVIDOR']['media'] . $configuracion['RUTAS']['javascript'] . '/modulos/proveedores/funcionesEditarProveedor.js';
        $respuesta['titulo']        = HTML::parrafo($textos->id('MODIFICAR_ITEM'), 'letraBlanca negrilla subtitulo');
        $respuesta['destino']       = '#cuadroDialogo';
        $respuesta['ancho']         = 800;
        $respuesta['alto']          = 550;
        $respuesta['codigo']        = $codigo;
        
    } else {

        $respuesta['error'] = true;

        $existeProveedor    = $sql->existeItem('proveedores', 'nombre', $datos['nombre_comercial'], 'id != "' . $id . '"');
        $existeNit          = $sql->existeItem('proveedores', 'id_proveedor', $datos['id_proveedor'], 'id != "' . $id . '"');

        if ($existeProveedor) {
            $respuesta['mensaje'] = $textos->id('ERROR_PROVEEDOR_EXISTENTE');
            
        } elseif (empty($datos['id_proveedor'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_CODIGO_PROVEEDOR');
            
        } elseif (empty($datos['id_ciudad_sede'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_CIUDAD_SEDE');
            
        } elseif (empty($datos['direccion_sede'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_DIRECCION_SEDE');
            
        } elseif (empty($datos['telefono_sede'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_TELEFONO_SEDE');
            
        } elseif (empty($datos['documento_identidad'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_DOCUMENTO_CONTACTO');
            
        } elseif (empty($datos['primer_nombre'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_NOMBRE');
            
        } elseif (empty($datos['primer_apellido'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_APELLIDO');
            
        } elseif (empty($datos['actividad_economica'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_ACTIVIDAD_ECONOMICA');
            
        } elseif ($existeNit) {
            $respuesta['mensaje'] = $textos->id('ERROR_EXISTE_NIT_PROVEEDOR');
            
        } else {

            $idItem = $objeto->modificar($datos);
            if ($idItem) {
                /*                 * ************** Creo el nuevo item que se insertara via ajax *************** */
                $objeto = new Proveedor($id);

                if ($objeto->activo) {
                    $estado = HTML::frase($textos->id('ACTIVO'), 'activo');
                } else {
                    $estado = HTML::frase($textos->id('INACTIVO'), 'inactivo');
                }
                $celdas = array($objeto->idProveedor, $objeto->nombre, $objeto->contacto->primerNombre, $objeto->contacto->primerApellido, $objeto->contacto->celular, $objeto->callCenter, $estado);
                $celdas1 = HTML::crearFilaAModificar($celdas);

                $respuesta['error']             = false;
                $respuesta['accion']            = 'insertar';
                $respuesta['contenido']         = $celdas1;
                $respuesta['idContenedor']      = '#tr_' . $id;
                $respuesta['idDestino']         = '#tr_' . $id;

                if ($datos['dialogo'] == '') {
                    $respuesta['modificarFilaTabla']    = true;
                    
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

    if (!isset($id) || (isset($id) && !$sql->existeItem('proveedores', 'id', $id))) {
        $respuesta              = array();
        $respuesta['error']     = true;
        $respuesta['mensaje']   = $textos->id('NO_HA_SELECCIONADO_ITEM');

        Servidor::enviarJSON($respuesta);
        return NULL;
    }

    $objeto     = new Proveedor($id);
    $destino    = '/ajax' . $objeto->urlBase . '/delete';
    $respuesta  = array();

    if (!$confirmado) {
        $titulo     = HTML::frase($objeto->nombre, 'negrilla');
        $titulo1    = str_replace('%1', $titulo, $textos->id('CONFIRMAR_ELIMINACION'));
        $codigo     = HTML::campoOculto('procesar', 'true');
        $codigo     .= HTML::campoOculto('id', $id);
        $codigo     .= HTML::parrafo($titulo1);
        $codigo     .= HTML::parrafo(HTML::boton('chequeo', $textos->id('ACEPTAR'), '', 'botonOk', 'botonOk'), 'margenSuperior');
        $codigo     .= HTML::parrafo($textos->id('REGISTRO_ELIMINADO'), 'textoExitoso', 'textoExitoso');
        $codigo1    = HTML::forma($destino, $codigo);

        $respuesta['generar']       = true;
        $respuesta['codigo']        = $codigo1;
        $respuesta['destino']       = '#cuadroDialogo';
        $respuesta['titulo']        = HTML::parrafo($textos->id('ELIMINAR_ITEM'), 'letraBlanca negrilla subtitulo');
        $respuesta['ancho']         = 350;
        $respuesta['alto']          = 150;
        
    } else {
        
        $arreglo1 = array('facturas_compras', 'id_proveedor = "'.$id.'"', $textos->id('FACTURAS_COMPRA'));//arreglo del que sale la info a consultar
        
        $arregloIntegridad = array($arreglo1);//arreglo de arreglos para realizar las consultas de integridad referencial, (ver documentacion de metodo)
        
        $integridad = Recursos::verificarIntegridad($textos->id('PROVEEDOR'), $arregloIntegridad);  
        
        if ($integridad != '') {
            $respuesta['error']     = true;
            $respuesta['mensaje']   = $integridad;
            
        } else {        
            if ($objeto->eliminar()) {

                $respuesta['error']         = false;
                $respuesta['accion']        = 'insertar';
                $respuesta['idDestino']     = '#tr_' . $id;

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

    $data = explode('[', $data);
    $datos = $data[0];

    if (empty($datos)) {
        $respuesta['error']     = true;
        $respuesta['mensaje']   = $textos->id('ERROR_FALTA_CADENA_BUSQUEDA');
        
    } else if (!empty($datos) && strlen($datos) < 2) {
        $respuesta['error']     = true;
        $respuesta['mensaje']   = str_replace('%1', '2', $textos->id('ERROR_TAMAÑO_CADENA_BUSQUEDA'));
        
    } else {
        $item = '';
        $respuesta = array();
        $objeto = new Proveedor();
        $registros = $configuracion['GENERAL']['registrosPorPagina'];
        if (!empty($cantidadRegistros)) {
            $registros = (int) $cantidadRegistros;
        }
        $pagina = 1;
        $registroInicial = 0;


        $palabras = explode(' ', $datos);

        $condicionales = $data[1];

        if ($condicionales == '') {
            $condicion = '(p.nombre REGEXP "(' . implode("|", $palabras) . ')")';
        } else {
            //$condicion = str_replace(']', ''', $data[1]);
            $condicionales = explode('|', $condicionales);

            $condicion = '(';
            $tam = sizeof($condicionales) - 1;
            for ($i = 0; $i < $tam; $i++) {
                $condicion .= $condicionales[$i] . ' REGEXP "(' . implode("|", $palabras) . ')" ';
                if ($i != $tam - 1) {
                    $condicion .= ' OR ';
                }
            }
            $condicion .= ')';
        }

        $arregloItems = $objeto->listar($registroInicial, $registros, array('0'), $condicion, 'p.nombre');

        if ($objeto->registrosConsulta) {//si la consulta trajo registros
            $datosPaginacion = array($objeto->registrosConsulta, $registroInicial, $registros, $pagina, $objeto->registrosConsulta);
            $item .= $objeto->generarTabla($arregloItems, $datosPaginacion);
            $info = HTML::parrafo(str_replace('%1', $objeto->registrosConsulta, $textos->id('RESULTADOS_BUSQUEDA')), 'textoExitosoNotificaciones');
            
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

    $item = '';
    $respuesta = array();
    $objeto = new Proveedor();

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
                $condicion .= $condicionales[$i] . ' REGEXP "(' . implode("|", $palabras) . ')" ';
                if ($i != $tam - 1) {
                    $condicion .= ' OR ';
                }
            }
            $condicion .= ')';

            $consultaGlobal = $condicion;
        } else {
            $consultaGlobal = '(p.nombre REGEXP "(' . implode("|", $palabras) . ')")';
        }
    } else {
        $consultaGlobal = '';
    }

    if (empty($nombreOrden)) {
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
 * Funcion que devuelve una respuesta para verificar la existencia de un item via ajax
 * 
 * @global recurso $sql     = objeto global de interaccion con la BD
 * @param type $cadena 
 */
function verificarPersona($cadena) {
    global $sql;
    
    $respuesta  = array();

    $tablas     = array('personas');
    
    $columnas = array(
        'cedula'            => 'documento_identidad',
        'tipoDoc'           => 'id_tipo_documento',
        'primerNombre'      => 'primer_nombre',
        'segundoNombre'     => 'segundo_nombre',
        'primerApellido'    => 'primer_apellido',
        'segundoApellido'   => 'segundo_apellido',
        'celular'           => 'celular'
    );
    
    $consulta = $sql->seleccionar($tablas, $columnas, 'documento_identidad = "' . $cadena . '" ');

    if ($sql->filasDevueltas) {
        $persona = $sql->filaEnObjeto($consulta);
        
        $respuesta['cedula']            = $persona->cedula;
        $respuesta['tipoDoc']           = $persona->tipoDoc;
        $respuesta['primerNombre']      = $persona->primerNombre;
        $respuesta['segundoNombre']     = $persona->segundoNombre;
        $respuesta['primerApellido']    = $persona->primerApellido;
        $respuesta['segundoApellido']   = $persona->segundoApellido;
        $respuesta['celular']           = $persona->celular;
        
    } else {
        $respuesta['error'] = true;
        
    }

    Servidor::enviarJSON($respuesta);
    
}

/**
 * Función que muestra el formulario para adicionar una sede nueva para el proveedor
 *
 * @global recurso $textos  = objeto global de gestion de los textos de idioma
 * @global recurso $sql     = objeto global de interaccion con la BD
 * @param  array $datos    = arreglo con los datos provenientes del formulario
 */
function adicionarSede($id, $datos = array(), $tablaEditarVisible = NULL) {
    global $textos, $sql;
      

    $destino = '/ajax/proveedores/adicionarSede';
    $respuesta = array();

    if (empty($datos)) {
        
        if (empty($id) || (!$sql->existeItem('proveedores', 'id', $id))) {
            $respuesta              = array();
            $respuesta['error']     = true;
            $respuesta['mensaje']   = $textos->id('NO_HA_SELECCIONADO_ITEM');

            Servidor::enviarJSON($respuesta);
            return NULL;
        }         

        $codigo = HTML::campoOculto('procesar', 'true');
        $codigo .= HTML::campoOculto('datos[dialogo]', '', 'idDialogo');
        $codigo .= HTML::campoOculto('datos[id_proveedor]', $id, ''); //id del proveedor
        $codigo .= HTML::campoOculto('datos[tablaEditarVisible]', $tablaEditarVisible, 'tablaEditarVisible'); //determina si la respuesta va para la tabla de editar, es decir va con el icono eliminar y editar
        $codigo .= HTML::parrafo($textos->id('NOMBRE_SEDE'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[nombre]', 30, 50, '', 'campoObligatorio');
        $codigo .= HTML::parrafo($textos->id('CIUDAD_UBICACION'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[id_ciudad]', 35, 255, '', 'autocompletable campoObligatorio', 'ciudadSede', array('title' => HTML::urlInterna('CIUDADES', 0, true, 'listar')), $textos->id('SELECCIONE_CIUDAD_SEDE'));
        $codigo .= HTML::parrafo($textos->id('DIRECCION'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[direccion]', 30, 50, '', 'campoObligatorio', 'direccionSede', array('title' => $textos->id('')));
        $codigo .= HTML::parrafo($textos->id('TELEFONO'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[telefono]', 30, 50, '', 'campoObligatorio', 'telefonoSede', array('title' => $textos->id('')));
        $codigo .= HTML::parrafo($textos->id('CELULAR'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[celular]', 30, 50, '', 'campoObligatorio', 'telefonoSede', array('title' => $textos->id('')));        
        $codigo .= HTML::parrafo($textos->id('FAX'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[fax]', 30, 50, '', '', '', array('title' => $textos->id('')));
        $codigo .= HTML::parrafo(HTML::boton('chequeo', $textos->id('ACEPTAR'), 'botonOk', 'botonOk', 'botonOk'), 'margenSuperior');
        $codigo .= HTML::parrafo($textos->id('REGISTRO_AGREGADO'), 'textoExitoso', 'textoExitoso');
        $codigo1 = HTML::forma($destino, $codigo, 'P');

        $respuesta['generar']       = true;
        $respuesta['codigo']        = $codigo1;
        $respuesta['titulo']        = HTML::parrafo($textos->id('ADICIONAR_SEDE'), 'letraBlanca negrilla subtitulo');
        $respuesta['destino']       = '#cuadroDialogo';
        $respuesta['ancho']         = 350;
        $respuesta['alto']          = 380;
        
    } else {

        $respuesta['error'] = true;

        $existeNombre = $sql->existeItem('sedes_proveedor', 'nombre', $datos['nombre']);
        $existeCiudad = $sql->existeItem('lista_ciudades', 'cadena', $datos['id_ciudad']);

        if (empty($datos['nombre'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_NOMBRE_SEDE');
            
        } elseif ($existeNombre) {
            $respuesta['mensaje'] = $textos->id('ERROR_EXISTE_NOMBRE_SEDE');
            
        } elseif (empty($datos['id_ciudad'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_CIUDAD');
            
        } elseif (!$existeCiudad) {
            $respuesta['mensaje'] = $textos->id('ERROR_NO_EXISTE_CIUDAD');
            
        } elseif (empty($datos['telefono'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_TELEFONO');
            
        } elseif (empty($datos['direccion'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_DIRECCION');
            
        } else {

            $ciudad                 = $datos['id_ciudad'];
            $dialogo                = $datos['dialogo'];
            $tablaEditarVisible     = $datos['tablaEditarVisible'];
            
            unset($datos['dialogo']);
            unset($datos['tablaEditarVisible']);
            
            $datos['id_ciudad'] = $sql->obtenerValor('lista_ciudades', 'id', 'cadena = "' . $datos['id_ciudad'] . '"');
            
            $consulta   = $sql->insertar('sedes_proveedor', $datos);
            $idItem     = $sql->ultimoId;
            
            if ($consulta) {

                $idDestino      = ''; //determino el destino, según sea la tabla editar contactos o consultar contactos
                $idFila         = '';
                $idContenedor   = '';
                
                if ($tablaEditarVisible) {//si la tabla de editar contactos es visible
                    $botonEditar    = HTML::contenedor('', 'editarRegistro');
                    $botonEliminar  = HTML::contenedor('', 'eliminarRegistro');

                    $idDestino      = '#tablaEditarMasSedes';
                    $idContenedor   = '#tablaEditarMasSedesTr_';
                    $idFila         = 'tablaEditarMasSedesTr_';
                    $celdas         = array($datos['nombre'], $ciudad, $datos['direccion'], $datos['telefono'], $botonEditar, $botonEliminar);
                    
                } else {//si solo es visible la tabla consultar contactos
                    $idDestino      = '#tablaConsultarMasSedes';
                    $idContenedor   = '#tablaConsultarMasSedesTr_';
                    $idFila         = 'tablaConsultarMasSedesTr_';
                    $celdas         = array($datos['nombre'], $ciudad, $datos['direccion'], $datos['telefono'], $datos['fax']);
                    
                }

                $celdas = HTML::crearNuevaFilaDesdeModal($celdas, '', $idFila . $idItem);


                if ($dialogo == '') {
                    $respuesta['error']                 = false;
                    $respuesta['accion']                = 'insertar';
                    $respuesta['insertarNuevaFila']     = true;
                    
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
 * Función que carga el formulario para modificar una sede de las existentes
 *
 * @global recurso $textos  = objeto global de gestion de los textos de idioma
 * @global recurso $sql     = objeto global de interaccion con la BD
 * @param array $datos    = arreglo con los datos provenientes del formulario
 */
function modificarSede($id, $datos = array()) {
    global $textos, $sql;

    $proveedor = new Proveedor();
    $destino = '/ajax/proveedores/modificarSede';
    $respuesta = array();

    $objeto = $proveedor->cargarSedeProveedor($id);

    if (empty($datos)) {

        $codigo = HTML::campoOculto('procesar', 'true');
        $codigo .= HTML::campoOculto('datos[dialogo]', '', 'idDialogo');
        $codigo .= HTML::campoOculto('id', $id, ''); //id de la sede
        $codigo .= HTML::parrafo($textos->id('NOMBRE_SEDE'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[nombre]', 30, 50, $objeto->nombre, '');
        $codigo .= HTML::parrafo($textos->id('CIUDAD_UBICACION'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[id_ciudad]', 35, 255, $objeto->ciudad, 'autocompletable campoObligatorio', 'ciudadSede', array('title' => HTML::urlInterna('CIUDADES', 0, true, 'listar')), $textos->id('SELECCIONE_CIUDAD_SEDE'));
        $codigo .= HTML::parrafo($textos->id('DIRECCION'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[direccion]', 30, 50, $objeto->direccion, 'campoObligatorio', 'direccionSede', array('title' => $textos->id('')));
        $codigo .= HTML::parrafo($textos->id('TELEFONO'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[telefono]', 30, 50, $objeto->telefono, 'campoObligatorio', 'telefonoSede', array('title' => $textos->id('')));
        $codigo .= HTML::parrafo($textos->id('CELULAR'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[celular]', 30, 50, $objeto->celular, 'campoObligatorio', 'telefonoSede', array('title' => $textos->id('')));         
        $codigo .= HTML::parrafo($textos->id('FAX'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[fax]', 30, 50, $objeto->fax, '', '', array('title' => $textos->id('')));
        $codigo .= HTML::parrafo(HTML::boton('chequeo', $textos->id('ACEPTAR'), 'botonOk', 'botonOk', 'botonOk'), 'margenSuperior');
        $codigo .= HTML::parrafo($textos->id('REGISTRO_MODIFICADO'), 'textoExitoso', 'textoExitoso');
        $codigo1 = HTML::forma($destino, $codigo, 'P');

        $respuesta['generar']   = true;
        $respuesta['codigo']    = $codigo1;
        $respuesta['titulo']    = HTML::parrafo($textos->id('MODIFICAR_SEDE'), 'letraBlanca negrilla subtitulo');
        $respuesta['destino']   = '#cuadroDialogo';
        $respuesta['ancho']     = 350;
        $respuesta['alto']      = 380;
        
    } else {

        $respuesta['error'] = true;

        $existeCiudad = $sql->existeItem('lista_ciudades', 'cadena', $datos['id_ciudad']);
        $existeNombre = $sql->existeItem('sedes_proveedor', 'nombre', $datos['nombre'], 'id != "' . $id . '"');

        if (empty($datos['nombre'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_NOMBRE_SEDE');
            
        } elseif ($existeNombre) {
            $respuesta['mensaje'] = $textos->id('ERROR_EXISTE_NOMBRE_SEDE');
            
        } elseif (empty($datos['id_ciudad'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_CIUDAD');
            
        } elseif (!$existeCiudad) {
            $respuesta['mensaje'] = $textos->id('ERROR_NO_EXISTE_CIUDAD');
            
        } else {

            $ciudad             = $datos['id_ciudad']; //guardo en ciudad el nombre de la ciudad para agregarla via ajax sin problema
            $datos['id_ciudad'] = $sql->obtenerValor('lista_ciudades', 'id', 'cadena = "' . $datos['id_ciudad'] . '"');
            $dialogo            = $datos['dialogo']; //guardo en dialogo el valor del nombre de la ventana de dialogo en la que saco el form, prque debo quitar esta posicion
           
            unset($datos['dialogo']); //quito esta posicion del arreglo para que no me de conflicto
            
            $consulta = $sql->modificar('sedes_proveedor', $datos, 'id = "' . $id . '"');
            
            if ($consulta) {
                $botonEditar    = HTML::contenedor('', 'editarRegistro');
                $botonEliminar  = HTML::contenedor('', 'eliminarRegistro');
                $celdas         = array($datos['nombre'], $ciudad, $datos['direccion'], $datos['telefono'], $botonEditar, $botonEliminar);
                $celdas1        = HTML::crearFilaAModificar($celdas);

                $respuesta['error']                 = false;
                $respuesta['accion']                = 'insertar';
                $respuesta['contenido']             = $celdas1;
                $respuesta['idContenedor']          = '#tablaEditarMasSedesTr_' . $id;
                $respuesta['modificarFilaDialogo']  = true;
                $respuesta['idDestino']             = '#tablaEditarMasSedesTr_' . $id;
                $respuesta['ventanaDialogo']        = $dialogo;
                
            } else {
                $respuesta['mensaje'] = $textos->id('ERROR_DESCONOCIDO');
                
            }
            
        }
        
    }

    Servidor::enviarJSON($respuesta);
}

/**
 * Función que carga el formulario de confirmación de eliminación de una sede
 * 
 * @global recurso $textos  = objeto global de gestion de los textos de idioma
 * @param int $id identificador del recurso en la BD
 * @param bolean $confirmado determina si se confirma la orden 
 */
function eliminarSede($id, $confirmado, $dialogo) {
    global $textos, $sql;

    $proveedor = new Proveedor();
    $destino = '/ajax/proveedores/eliminarSede';
    $respuesta = array();

    $objeto = $proveedor->cargarSedeProveedor($id);

    if (!$confirmado) {
        $titulo = HTML::frase($objeto->nombre, 'negrilla');
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
        $respuesta['titulo']        = HTML::parrafo($textos->id('ELIMINAR_SEDE'), 'letraBlanca negrilla subtitulo');
        $respuesta['ancho']         = 350;
        $respuesta['alto']          = 150;
    } else {

        $consulta = $sql->eliminar('sedes_proveedor', 'id = "' . $id . '"');

        if ($consulta) {
            $respuesta['error']         = false;
            $respuesta['accion']        = 'insertar';
            $respuesta['idDestino']     = '#tablaEditarMasSedesTr_' . $id;
            
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

    Servidor::enviarJSON($respuesta);
}

/**
 * Función que carga el formulario para adicionar un nuevo contacto a un proveedor
 *
 * @global recurso $textos  = objeto global de gestion de los textos de idioma
 * @global recurso $sql     = objeto global de interaccion con la BD
 * @param array $datos    = arreglo con los datos provenientes del formulario
 */
function adicionarContacto($id, $datos = array(), $tablaEditarVisible = NULL) {
    global $textos, $sql, $configuracion;
     
    $destino    = '/ajax/proveedores/adicionarContacto';
    $respuesta  = array();

    if (empty($datos)) {
        
        if (empty($id) || (!$sql->existeItem('proveedores', 'id', $id))) {
            $respuesta              = array();
            $respuesta['error']     = true;
            $respuesta['mensaje']   = $textos->id('NO_HA_SELECCIONADO_ITEM');

            Servidor::enviarJSON($respuesta);
            return NULL;
        }          

        //seleccionar los tipos de documentos
        $tiposDocumentos = $sql->seleccionar(array('tipos_documento'), array('id', 'nombre'), 'id NOT IN (0)', 'id', 'nombre ASC');
        $tiposDoc = array();
        while ($tiposDocumento = $sql->filaEnObjeto($tiposDocumentos)) {
            $tiposDoc[$tiposDocumento->id] = $tiposDocumento->nombre;
        }
        $listaTipodocumento = HTML::listaDesplegable('datos[id_tipo_documento]', $tiposDoc, '', '', 'listaTipoDocumentoPersona');


        $codigo  = HTML::campoOculto('procesar', 'true');
        $codigo .= HTML::campoOculto('datos[dialogo]', '', 'idDialogo');
        $codigo .= HTML::campoOculto('datos[id_proveedor]', $id, ''); //id del proveedor
        $codigo .= HTML::campoOculto('datos[tablaEditarVisible]', $tablaEditarVisible, 'tablaEditarVisible'); //determina si la respuesta va para la tabla de editar, es decir va con el icono eliminar y editar


        $documento  = HTML::parrafo($textos->id('DOCUMENTO'), 'negrilla margenSuperior');
        $documento .= HTML::campoTexto('datos[documento_identidad]', 30, 50, '', 'campoObligatorio autocompletable', 'campoDocumentoPersona1', array('title' => HTML::urlInterna('INICIO', 0, true, 'listarPersonas')), $textos->id('AYUDA_USO_AUTOCOMPLETAR')) . HTML::frase('Persona ya registrada.', 'margenIzquierda letraVerde oculto', 'frasePersonaRegistrada');
        $email       = HTML::parrafo($textos->id('EMAIL'), 'negrilla margenSuperior');
        $email      .= HTML::campoTexto('datos[correo]', 30, 50, '', '', 'campoCorreoPersona', array('title' => $textos->id('')));

        $codigo1 = HTML::contenedorCampos($documento, $email);

        $tipoDoc = HTML::parrafo($textos->id('TIPO_DOCUMENTO'), 'negrilla margenSuperior');
        $tipoDoc .= $listaTipodocumento;
        $telefono = HTML::parrafo($textos->id('TELEFONO'), 'negrilla margenSuperior');
        $telefono .= HTML::campoTexto('datos[telefono]', 30, 50, '', '', 'campoTelefonoPersona', array('title' => $textos->id('')));

        $codigo1 .= HTML::contenedorCampos($tipoDoc, $telefono);

        $primerNom = HTML::parrafo($textos->id('PRIMER_NOMBRE'), 'negrilla margenSuperior');
        $primerNom .= HTML::campoTexto('datos[primer_nombre]', 30, 50, '', 'campoObligatorio', 'campoPrimerNombrePersona', array('title' => $textos->id('')));
        $celular = HTML::parrafo($textos->id('CELULAR'), 'negrilla margenSuperior');
        $celular .= HTML::campoTexto('datos[celular]', 30, 50, '', 'campoObligatorio', 'campoCelularPersona', array('title' => $textos->id('')));

        $codigo1 .= HTML::contenedorCampos($primerNom, $celular);

        $segundoNom = HTML::parrafo($textos->id('SEGUNDO_NOMBRE'), 'negrilla margenSuperior');
        $segundoNom .= HTML::campoTexto('datos[segundo_nombre]', 30, 50, '', '', 'campoSegundoNombrePersona', array('title' => $textos->id('')));
        $fax = HTML::parrafo($textos->id('FAX'), 'negrilla margenSuperior');
        $fax .= HTML::campoTexto('datos[fax]', 30, 50, '', '', 'campoFaxPersona', array('title' => $textos->id('')));

        $codigo1 .= HTML::contenedorCampos($segundoNom, $fax);

        $primerApe = HTML::parrafo($textos->id('PRIMER_APELLIDO'), 'negrilla margenSuperior', '');
        $primerApe .= HTML::campoTexto('datos[primer_apellido]', 30, 50, '', 'campoObligatorio', 'campoPrimerApellidoPersona');
        $observaciones = HTML::parrafo($textos->id('OBSERVACIONES'), 'negrilla margenSuperior');
        $observaciones .= HTML::areaTexto('datos[observaciones_contacto]', 2, 40, '');

        $codigo1 .= HTML::contenedorCampos($primerApe, $observaciones);

        $segundoApe = HTML::parrafo($textos->id('SEGUNDO_APELLIDO'), 'negrilla margenSuperior');
        $segundoApe .= HTML::campoTexto('datos[segundo_apellido]', 30, 50, '', '', 'campoSegundoApellidoPersona');

        $codigo1 .= HTML::contenedorCampos($segundoApe, '');


        $codigo .= $codigo1;

        $codigo .= HTML::parrafo(HTML::boton('chequeo', $textos->id('ACEPTAR'), 'botonOk margenSuperior', 'botonOk', 'botonOk'), 'margenSuperior');
        $codigo .= HTML::parrafo($textos->id('REGISTRO_AGREGADO'), 'textoExitoso', 'textoExitoso');
        $codigo_f = HTML::forma($destino, $codigo, 'P');

        $respuesta['generar']       = true;
        $respuesta['cargarJs']      = true;
        $respuesta['archivoJs']     = $configuracion['SERVIDOR']['media'] . $configuracion['RUTAS']['javascript'] . '/modulos/proveedores/funcionesAgregarContacto.js';
        $respuesta['codigo']        = $codigo_f;
        $respuesta['titulo']        = HTML::parrafo($textos->id('ADICIONAR_CONTACTO'), 'letraBlanca negrilla subtitulo');
        $respuesta['destino']       = '#cuadroDialogo';
        $respuesta['ancho']         = 650;
        $respuesta['alto']          = 450;

    } else {

        $respuesta['error'] = true;
        $idPersona          = $sql->obtenerValor('personas', 'id', 'documento_identidad = "' . $datos['documento_identidad'] . '"');
        $existeContacto     = $sql->existeItem('contactos_proveedor', 'id_persona', $idPersona, 'id_proveedor = "' . $datos['id_proveedor'] . '"');

        if (empty($datos['documento_identidad'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_DOCUMENTO_CONTACTO');

        } elseif (empty($datos['primer_nombre'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_NOMBRE');

        } elseif (empty($datos['primer_apellido'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_APELLIDO');

        } elseif ($existeContacto) {
            $respuesta['mensaje'] = $textos->id('ERROR_EXISTE_CONTACTO');

        } else {
            //asigno los valores a nuevas variables para despues eliminar esos valores del arreglo y enviarlo a la BD
            $idProveedor                = $datos['id_proveedor'];
            $dialogo                    = $datos['dialogo'];
            $observacionesContacto      = $datos['observaciones_contacto'];
            $tablaEditarVisible         = $datos['tablaEditarVisible'];
            
            unset($datos['dialogo']); //aqui los elimino del arreglo
            unset($datos['id_proveedor']);
            unset($datos['observaciones_contacto']);
            unset($datos['tablaEditarVisible']);

            if (empty($idPersona)) {
                $persona = new Persona();
                $idPersona = $persona->adicionar($datos);
                
            } else {
                $datosPersona = array(
                    'documento_identidad'   => $datos['documento_identidad'],
                    'id_tipo_documento'     => $datos['id_tipo_documento'],
                    'primer_nombre'         => $datos['primer_nombre'],
                    'segundo_nombre'        => $datos['segundo_nombre'],
                    'primer_apellido'       => $datos['primer_apellido'],
                    'segundo_apellido'      => $datos['segundo_apellido'],
                    'telefono'              => $datos['telefono'],
                    'celular'               => $datos['celular'],
                    'fax'                   => $datos['fax'],
                    'correo'                => $datos['correo'],
                );
                $sql->modificar('personas', $datosPersona, 'id = "' . $idPersona . '"');
                
            }
            
            $observacionesContacto = ($observacionesContacto != '') ? $observacionesContacto : 'Ninguna.';

            $datosContacto = array(
                'id_proveedor'      => $idProveedor,
                'id_persona'        => $idPersona,
                'observaciones'     => $observacionesContacto
            );

            $consulta = $sql->insertar('contactos_proveedor', $datosContacto);
            $idItem = $sql->ultimoId;
            if ($consulta) {

                $idDestino      = ''; //determino el destino, según sea la tabla editar contactos o consultar contactos
                $idContenedor   = '';
                $idFila         = '';
                
                //creo la variable telefono para concatenar los datos del celular, fax y telefono y mostrarlo via ajax
                $telefonos  = '';
                $telefonos .= 'Cel: '.$datos['celular'];
                $telefonos .= ($datos['telefono']) ? ' - Tel: '.$datos['telefono'] : '';
                $telefonos .= ($datos['fax']) ? ' - Fax: '.$datos['fax'] : '';                
                
                if ($tablaEditarVisible) {//si la tabla de editar contactos es visible
                    $botonEditar = HTML::contenedor('', 'editarRegistro');
                    $botonEliminar = HTML::contenedor('', 'eliminarRegistro');

                    $idDestino      = '#tablaEditarMasContactos';
                    $idContenedor   = '#tablaEditarMasContactosTr_';
                    $idFila         = 'tablaEditarMasContactosTr_';
                    $celdas         = array($datos['primer_nombre'].' '.$datos['segundo_nombre'], $datos['primer_apellido'].' '.$datos['segundo_apellido'], $datos['celular'], $botonEditar, $botonEliminar);
                    
                } else {//si solo es visible la tabla consultar contactos
                    $idDestino      = '#tablaConsultarMasContactos';
                    $idContenedor   = '#tablaConsultarMasContactosTr_';
                    $idFila         = 'tablaConsultarMasContactosTr_';
                    $celdas         = array($datos['primer_nombre'].' '.$datos['segundo_nombre'], $datos['primer_apellido'].' '.$datos['segundo_apellido'], $telefonos, $datos['correo'], $observacionesContacto);
                    
                }

                $celdas = HTML::crearNuevaFilaDesdeModal($celdas, '', $idFila . $idItem);

                if ($dialogo == '') {
                    $respuesta['error']                 = false;
                    $respuesta['accion']                = 'insertar';
                    $respuesta['insertarNuevaFila']     = true;
                    
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
 * Función que carga el formulario para modificar la información de un contacto
 *
 * @global recurso $textos  = objeto global de gestion de los textos de idioma
 * @global recurso $sql     = objeto global de interaccion con la BD
 * @param array $datos    = arreglo con los datos provenientes del formulario
 */
function modificarContacto($id, $datos = array()) {
    global $textos, $sql, $configuracion;

    $destino            = '/ajax/proveedores/modificarContacto';
    $respuesta          = array();
    $idPersona          = $sql->obtenerValor('contactos_proveedor', 'id_persona', 'id = "' . $id . '"');
    $observaciones      = $sql->obtenerValor('contactos_proveedor', 'observaciones', 'id = "' . $id . '"');
    $objeto             = new Persona($idPersona);

    if (empty($datos)) {
        //seleccionar los tipos de documentos
        $tiposDocumentos = $sql->seleccionar(array('tipos_documento'), array('id', 'nombre'), 'id NOT IN (0)', 'id', 'nombre ASC');
        $tiposDoc = array();
        
        while ($tiposDocumento = $sql->filaEnObjeto($tiposDocumentos)) {
            $tiposDoc[$tiposDocumento->id] = $tiposDocumento->nombre;
        }
        
        $listaTipodocumento = HTML::listaDesplegable('datos[id_tipo_documento]', $tiposDoc, $objeto->idTipoDocumento, '', 'listaTipoDocumentoPersona');

        $codigo = HTML::campoOculto('procesar', 'true');
        $codigo .= HTML::campoOculto('datos[dialogo]', '', 'idDialogo');
        $codigo .= HTML::campoOculto('id', $id, ''); //id del contacto

        $documento = HTML::parrafo($textos->id('DOCUMENTO'), 'negrilla margenSuperior');
        $documento .= HTML::campoTexto('datos[documento_identidad]', 30, 50, $objeto->documentoIdentidad, 'campoObligatorio autocompletable', 'campoDocumentoPersona1', array('title' => HTML::urlInterna('INICIO', 0, true, 'listarPersonas')), $textos->id('AYUDA_USO_AUTOCOMPLETAR')) . HTML::frase('Persona ya registrada.', 'margenIzquierda letraVerde oculto', 'frasePersonaRegistrada');
        $email = HTML::parrafo($textos->id('EMAIL'), 'negrilla margenSuperior');
        $email .= HTML::campoTexto('datos[correo]', 30, 50, $objeto->correo, '', 'campoCorreoPersona', array('title' => $textos->id('')));

        $codigo1 = HTML::contenedorCampos($documento, $email);

        $tipoDoc = HTML::parrafo($textos->id('TIPO_DOCUMENTO'), 'negrilla margenSuperior');
        $tipoDoc .= $listaTipodocumento;
        $telefono = HTML::parrafo($textos->id('TELEFONO'), 'negrilla margenSuperior');
        $telefono .= HTML::campoTexto('datos[telefono]', 30, 50, $objeto->telefono, '', 'campoTelefonoPersona', array('title' => $textos->id('')));

        $codigo1 .= HTML::contenedorCampos($tipoDoc, $telefono);

        $primerNom = HTML::parrafo($textos->id('PRIMER_NOMBRE'), 'negrilla margenSuperior');
        $primerNom .= HTML::campoTexto('datos[primer_nombre]', 30, 50, $objeto->primerNombre, 'campoObligatorio', 'campoPrimerNombrePersona', array('title' => $textos->id('')));
        $celular = HTML::parrafo($textos->id('CELULAR'), 'negrilla margenSuperior');
        $celular .= HTML::campoTexto('datos[celular]', 30, 50, $objeto->celular, 'campoObligatorio', 'campoCelularPersona', array('title' => $textos->id('')));

        $codigo1 .= HTML::contenedorCampos($primerNom, $celular);

        $segundoNom = HTML::parrafo($textos->id('SEGUNDO_NOMBRE'), 'negrilla margenSuperior');
        $segundoNom .= HTML::campoTexto('datos[segundo_nombre]', 30, 50, $objeto->segundoNombre, '', 'campoSegundoNombrePersona', array('title' => $textos->id('')));
        $fax = HTML::parrafo($textos->id('FAX'), 'negrilla margenSuperior');
        $fax .= HTML::campoTexto('datos[fax]', 30, 50, $objeto->fax, '', 'campoFaxPersona', array('title' => $textos->id('')));

        $codigo1 .= HTML::contenedorCampos($segundoNom, $fax);

        $primerApe = HTML::parrafo($textos->id('PRIMER_APELLIDO'), 'negrilla margenSuperior', '');
        $primerApe .= HTML::campoTexto('datos[primer_apellido]', 30, 50, $objeto->primerApellido, 'campoObligatorio', 'campoPrimerApellidoPersona');
        $observ = HTML::parrafo($textos->id('OBSERVACIONES'), 'negrilla margenSuperior');
        $observ .= HTML::areaTexto('datos[observaciones_contacto]', 2, 40, $observaciones);

        $codigo1 .= HTML::contenedorCampos($primerApe, $observ);

        $segundoApe = HTML::parrafo($textos->id('SEGUNDO_APELLIDO'), 'negrilla margenSuperior');
        $segundoApe .= HTML::campoTexto('datos[segundo_apellido]', 30, 50, $objeto->segundoApellido, '', 'campoSegundoApellidoPersona');

        $codigo1 .= HTML::contenedorCampos($segundoApe, '');

        $codigo .= $codigo1;

        $codigo .= HTML::parrafo(HTML::boton('chequeo', $textos->id('ACEPTAR'), 'botonOk', 'botonOk', 'botonOk'), 'margenSuperior');
        $codigo .= HTML::parrafo($textos->id('REGISTRO_MODIFICADO'), 'textoExitoso', 'textoExitoso');
        $codigo_f = HTML::forma($destino, $codigo, 'P');

        $respuesta['generar']       = true;
        $respuesta['cargarJs']      = true;
        $respuesta['archivoJs']     = $configuracion['SERVIDOR']['media'] . $configuracion['RUTAS']['javascript'] . '/modulos/proveedores/funcionesAgregarContacto.js';
        $respuesta['codigo']        = $codigo_f;
        $respuesta['titulo']        = HTML::parrafo($textos->id('MODIFICAR_CONTACTO'), 'letraBlanca negrilla subtitulo');
        $respuesta['destino']       = '#cuadroDialogo';
        $respuesta['ancho']         = 630;
        $respuesta['alto']          = 450;
        
    } else {
        
        $respuesta['error'] = true;

        if (empty($datos['primer_nombre'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_NOMBRE');
            
        } elseif (empty($datos['primer_apellido'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_APELLIDO');
            
        } elseif (empty($datos['celular'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_CELULAR');
            
        } else {
            //$sql->depurar = true;
            $dialogo = $datos['dialogo']; //guardo en dialogo el valor del nombre de la ventana de dialogo en la que saco el form, prque debo quitar esta posicion
            unset($datos['dialogo']);
            
            $idPersona = $sql->obtenerValor('contactos_proveedor', 'id_persona', 'id = "' . $id . '"');
            
            if (!empty($datos["observaciones_contacto"])) {                
                $datos["observaciones_contacto"] = ($datos["observaciones_contacto"] != '') ? $datos["observaciones_contacto"] : 'Ninguna.';                
                $datosObser = array('observaciones' => $datos["observaciones_contacto"]);
                $sql->modificar('contactos_proveedor', $datosObser, 'id = "' . $id . '"');
                unset($datos['observaciones_contacto']);
            }
            
            $consulta = $sql->modificar('personas', $datos, 'id = "' . $idPersona . '"');
            
            if ($consulta) {

                $botonEditar    = HTML::contenedor('', 'editarRegistro');
                $botonEliminar  = HTML::contenedor('', 'eliminarRegistro');
                $celdas         = array($datos['primer_nombre'].' '.$datos['segundo_nombre'], $datos['primer_apellido'].' '.$datos['segundo_apellido'], $datos['celular'], $botonEditar, $botonEliminar);
                $idContenedor   = '#tablaEditarMasContactosTr_' . $id;
                $idDestino      = '#tablaEditarMasContactosTr_' . $id;

                $celdas1 = HTML::crearFilaAModificar($celdas);

                $respuesta['error']                 = false;
                $respuesta['accion']                = 'insertar';
                $respuesta['contenido']             = $celdas1;
                $respuesta['idContenedor']          = $idContenedor;
                $respuesta['modificarFilaDialogo']  = true;
                $respuesta['idDestino']             = $idDestino;
                $respuesta['ventanaDialogo']        = $dialogo;
                
            } else {
                $respuesta['mensaje'] = $textos->id('ERROR_DESCONOCIDO');
                
            }
            
        }
        
    }

    Servidor::enviarJSON($respuesta);
}

/**
 * Función que carga el formulario para eliminar un contacto
 * 
 * @global recurso $textos  = objeto global de gestion de los textos de idioma
 * @param int $id identificador del recurso en la BD
 * @param bolean $confirmado determina si se confirma la orden 
 */
function eliminarContacto($id, $confirmado, $dialogo) {
    global $textos, $sql;

    $destino    = '/ajax/proveedores/eliminarContacto';
    $respuesta  = array();

    $objeto = new Persona($sql->obtenerValor('contactos_proveedor', 'id_persona', 'id = "' . $id . '"'));


    if (!$confirmado) {
        $titulo = HTML::frase($objeto->primerNombre . ' ' . $objeto->primerApellido, 'negrilla');
        $titulo1 = str_replace('%1', $titulo, $textos->id('CONFIRMAR_ELIMINACION'));
        $codigo = HTML::campoOculto('procesar', 'true');
        $codigo .= HTML::campoOculto('id', $id);
        $codigo .= HTML::campoOculto('dialogo', '', 'idDialogo');
        $codigo .= HTML::parrafo($titulo1);
        $codigo .= HTML::parrafo(HTML::boton('chequeo', $textos->id('ACEPTAR'), '', 'botonOk', 'botonOk'), 'margenSuperior');
        $codigo .= HTML::parrafo($textos->id('REGISTRO_ELIMINADO'), 'textoExitoso', 'textoExitoso');
        $codigo1 = HTML::forma($destino, $codigo);

        $respuesta['generar']   = true;
        $respuesta['codigo']    = $codigo1;
        $respuesta['destino']   = '#cuadroDialogo';
        $respuesta['titulo']    = HTML::parrafo($textos->id('ELIMINAR_CONTACTO'), 'letraBlanca negrilla subtitulo');
        $respuesta['ancho']     = 350;
        $respuesta['alto']      = 150;
        
    } else {

        $consulta = $sql->eliminar('contactos_proveedor', 'id = "' . $id . '"');

        if ($consulta) {

            $respuesta['error'] = false;
            $respuesta['accion'] = 'insertar';
            $respuesta['idDestino'] = '#tablaEditarMasContactosTr_' . $id;

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

    Servidor::enviarJSON($respuesta);
    
}

/**
 * Función que carga el formulario para adicionar cuentas bancarias a un proveedor
 * 
 * @global recurso $textos  = objeto global de gestion de los textos de idioma
 * @global recurso $sql     = objeto global de interaccion con la BD
 * @param array $datos    = arreglo con los datos provenientes del formulario
 */
function adicionarCuenta($id, $datos = array(), $tablaEditarVisible = NULL) {
    global $textos, $sql;
   

    $destino = '/ajax/proveedores/adicionarCuenta';
    $respuesta = array();

    if (empty($datos)) {
        
    
        if (empty($id) || (!$sql->existeItem('proveedores', 'id', $id))) {
            $respuesta              = array();
            $respuesta['error']     = true;
            $respuesta['mensaje']   = $textos->id('NO_HA_SELECCIONADO_ITEM');

            Servidor::enviarJSON($respuesta);
            return NULL;
        }         

        $codigo = HTML::campoOculto('procesar', 'true');
        $codigo .= HTML::campoOculto('datos[dialogo]', '', 'idDialogo');
        $codigo .= HTML::campoOculto('datos[id_proveedor]', $id, ''); //id del proveedor
        $codigo .= HTML::campoOculto('datos[tablaEditarVisible]', $tablaEditarVisible, 'tablaEditarVisible'); //determina si la respuesta va para la tabla de editar, es decir va con el icono eliminar y editar

        $arregloTipoCuenta = array(
            '1' => 'Ahorros',
            '2' => 'Corriente'
        );
        $listaTipoCuenta = HTML::listaDesplegable('datos[tipo_cuenta]', $arregloTipoCuenta, '', '', 'listaTipoCuenta');

        $codigo .= HTML::parrafo($textos->id('BANCO'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[id_banco]', 40, 255, '', 'autocompletable campoObligatorio', 'campoNombreBanco', array('title' => HTML::urlInterna('BANCOS', 0, true, 'listar')), $textos->id('AYUDA_USO_AUTOCOMPLETAR'), HTML::urlInterna('BANCOS', 0, true, 'add'));
        $codigo .= HTML::parrafo($textos->id('NUMERO_CUENTA'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[numero_cuenta]', 25, 50, '', 'campoObligatorio', 'campoNumeroCuenta');
        $codigo .= HTML::parrafo($textos->id('TIPO_CUENTA'), 'negrilla margenSuperior');
        $codigo .= HTML::frase($listaTipoCuenta, '');

        $codigo .= HTML::parrafo(HTML::boton('chequeo', $textos->id('ACEPTAR'), 'botonOk', 'botonOk', 'botonOk'), 'margenSuperior');
        $codigo .= HTML::parrafo($textos->id('REGISTRO_AGREGADO'), 'textoExitoso', 'textoExitoso');
        $codigo1 = HTML::forma($destino, $codigo, 'P');

        $respuesta['generar'] = true;
        $respuesta['codigo'] = $codigo1;
        $respuesta['titulo'] = HTML::parrafo($textos->id('ADICIONAR_CUENTA'), 'letraBlanca negrilla subtitulo');
        $respuesta['destino'] = '#cuadroDialogo';
        $respuesta['ancho'] = 450;
        $respuesta['alto'] = 380;
    } else {

        $respuesta['error'] = true;

        $existeBanco = $sql->existeItem('bancos', 'nombre', $datos['id_banco']);

        if (empty($datos['id_banco'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_BANCO');
        } elseif (!$existeBanco) {
            $respuesta['mensaje'] = $textos->id('ERROR_NO_EXISTE_NOMBRE_BANCO');
        } elseif (empty($datos['numero_cuenta'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_NUMERO_CUENTA');
        } else {

            $banco = $datos['id_banco'];
            $dialogo = $datos['dialogo'];
            $tablaEditarVisible = $datos['tablaEditarVisible'];
            unset($datos['dialogo']);
            unset($datos['tablaEditarVisible']);

            $datos['id_banco'] = $sql->obtenerValor('bancos', 'id', 'nombre = "' . $datos['id_banco'] . '"');

            $consulta = $sql->insertar('cuentas_proveedor', $datos);
            $idItem = $sql->ultimoId;

            if ($consulta) {

                $idDestino = ''; //determino el destino, según sea la tabla editar cuentas o consultar cuentas
                $idContenedor = '';
                $idFila = '';
                if ($tablaEditarVisible) {//si la tabla de editar cuentas es visible
                    $botonEditar = HTML::contenedor('', 'editarRegistro');
                    $botonEliminar = HTML::contenedor('', 'eliminarRegistro');

                    $idDestino = '#tablaEditarCuentas';
                    $idContenedor = '#tablaEditarCuentasTr_';
                    $idFila = 'tablaEditarCuentasTr_';
                    $celdas = array($banco, $datos['numero_cuenta'], $textos->id('TIPO_CUENTA' . $datos['tipo_cuenta']), $botonEditar, $botonEliminar);
                } else {//si solo es visible la tabla consultar cuentas
                    $idDestino = '#tablaConsultarCuentas';
                    $idContenedor = '#tablaConsultarCuentasTr_';
                    $idFila = 'tablaConsultarCuentasTr_';
                    $celdas = array($banco, $datos['numero_cuenta'], $textos->id('TIPO_CUENTA' . $datos['tipo_cuenta']));
                }


                $celdas = HTML::crearNuevaFilaDesdeModal($celdas, '', $idFila . $idItem);

                if ($dialogo == '') {
                    $respuesta['error'] = false;
                    $respuesta['accion'] = 'insertar';
                    $respuesta['insertarNuevaFila'] = true;
                } else {
                    $respuesta['error'] = false;
                    $respuesta['accion'] = 'insertar';
                    $respuesta['contenido'] = $celdas;
                    $respuesta['idContenedor'] = $idContenedor . $idItem;
                    $respuesta['insertarNuevaFilaDialogo'] = true;
                    $respuesta['idDestino'] = $idDestino;
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
 * Función que carga el formulario para modificar una cuenta bancaria del proveedor
 *
 * @global recurso $textos  = objeto global de gestion de los textos de idioma
 * @global recurso $sql     = objeto global de interaccion con la BD
 * @param array $datos    = arreglo con los datos provenientes del formulario
 */
function modificarCuenta($id, $datos = array(), $tablaEditarVisible = NULL) {
    global $textos, $sql;

    $destino    = '/ajax/proveedores/modificarCuenta';
    $respuesta  = array();

    $tablas     = array('cp' => 'cuentas_proveedor', 'b' => 'bancos');
    $columnas   = array('id' => 'cp.id', 'banco' => 'b.nombre', 'numeroCuenta' => 'cp.numero_cuenta', 'tipoCuenta' => 'cp.tipo_cuenta');
    $consulta   = $sql->seleccionar($tablas, $columnas, 'cp.id_banco = b.id AND cp.id = "' . $id . '"');
    $objeto     = $sql->filaEnObjeto($consulta);

    if (empty($datos)) {

        $codigo = HTML::campoOculto('procesar', 'true');
        $codigo .= HTML::campoOculto('id', $id, 'id');
        $codigo .= HTML::campoOculto('datos[dialogo]', '', 'idDialogo');


        $arregloTipoCuenta = array(
            '1' => 'Ahorros',
            '2' => 'Corriente'
        );
        $listaTipoCuenta = HTML::listaDesplegable('datos[tipo_cuenta]', $arregloTipoCuenta, $objeto->tipoCuenta, '', 'listaTipoCuenta');

        $codigo .= HTML::parrafo($textos->id('BANCO'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[id_banco]', 40, 255, $objeto->banco, 'autocompletable campoObligatorio', 'campoNombreBanco', array('title' => HTML::urlInterna('BANCOS', 0, true, 'listar')), $textos->id('AYUDA_USO_AUTOCOMPLETAR'), HTML::urlInterna('BANCOS', 0, true, 'add'));
        $codigo .= HTML::parrafo($textos->id('NUMERO_CUENTA'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[numero_cuenta]', 25, 50, $objeto->numeroCuenta, 'campoObligatorio', 'campoNumeroCuenta');
        $codigo .= HTML::parrafo($textos->id('TIPO_CUENTA'), 'negrilla margenSuperior');
        $codigo .= HTML::frase($listaTipoCuenta, '');

        $codigo .= HTML::parrafo(HTML::boton('chequeo', $textos->id('ACEPTAR'), 'botonOk', 'botonOk', 'botonOk'), 'margenSuperior');
        $codigo .= HTML::parrafo($textos->id('REGISTRO_MODIFICADO'), 'textoExitoso', 'textoExitoso');
        $codigo1 = HTML::forma($destino, $codigo, 'P');

        $respuesta['generar'] = true;
        $respuesta['codigo'] = $codigo1;
        $respuesta['titulo'] = HTML::parrafo($textos->id('MODIFICAR_CUENTA'), 'letraBlanca negrilla subtitulo');
        $respuesta['destino'] = '#cuadroDialogo';
        $respuesta['ancho'] = 450;
        $respuesta['alto'] = 380;
        
    } else {

        $respuesta['error'] = true;

        $existeBanco = $sql->existeItem('bancos', 'nombre', $datos['id_banco']);

        if (empty($datos['id_banco'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_BANCO');
        } elseif (!$existeBanco) {
            $respuesta['mensaje'] = $textos->id('ERROR_NO_EXISTE_NOMBRE_BANCO');
        } elseif (empty($datos['numero_cuenta'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_NUMERO_CUENTA');
        } else {

            $banco = $datos['id_banco'];

            $dialogo = $datos['dialogo'];

            unset($datos['dialogo']);

            $datos['id_banco'] = $sql->obtenerValor('bancos', 'id', 'nombre = "' . $datos['id_banco'] . '"');
            $consulta = $sql->modificar('cuentas_proveedor', $datos, 'id = "' . $id . '"');

            if ($consulta) {

                $botonEditar        = HTML::contenedor('', 'editarRegistro');
                $botonEliminar      = HTML::contenedor('', 'eliminarRegistro');
                $celdas             = array($banco, $datos['numero_cuenta'], $textos->id('TIPO_CUENTA' . $datos['tipo_cuenta']), $botonEditar, $botonEliminar);
                $celdas1            = HTML::crearFilaAModificar($celdas);
                $idDestino          = '#tablaEditarCuentasTr_' . $id;
                $idContenedor       = '#tablaEditarCuentasTr_' . $id;



                $respuesta['error']                     = false;
                $respuesta['accion']                    = 'insertar';
                $respuesta['contenido']                 = $celdas1;
                $respuesta['idContenedor']              = $idContenedor;
                $respuesta['idDestino']                 = $idDestino;
                $respuesta['modificarFilaDialogo']      = true;
                $respuesta['ventanaDialogo']            = $dialogo;
                
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

    $destino = '/ajax/proveedores/eliminarCuenta';
    $respuesta = array();

    $cuenta = $sql->obtenerValor('cuentas_proveedor', 'numero_cuenta', 'id = "' . $id . '"');

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

        $consulta = $sql->eliminar('cuentas_proveedor', 'id = "' . $id . '"');

        if ($consulta) {

            $respuesta['error'] = false;
            $respuesta['accion'] = 'insertar';
            $respuesta['idDestino'] = '#tablaEditarCuentasTr_' . $id;

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
    $consulta = $sql->seleccionar(array('proveedores'), array('id', 'id_proveedor', 'nombre'), '(nombre LIKE "%' . $cadena . '%" OR id_proveedor LIKE "%' . $cadena . '%") AND activo = "1" AND id NOT IN(0)', '', 'nombre ASC', 0, 20);

    while ($fila = $sql->filaEnObjeto($consulta)) {
        $respuesta1 = array();
        $respuesta1["label"] = $fila->id_proveedor . ' :: ' . $fila->nombre;
        $respuesta1["value"] = $fila->id;
        $respuesta1["nombre"] = $fila->nombre;
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


    $destino    = '/ajax/proveedores/eliminarVarios';
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

        $cadenaIds  = substr($cadenaItems, 0, -1);
        $arregloIds = explode(",", $cadenaIds);

        $eliminarVarios = true;
        
        foreach ($arregloIds as $val) {
            $objeto = new Proveedor($val);
            
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
 * Funcion que recibe el id de un proveedor, con este consulta su regimen
 * 
 * @global objeto $textos objeto global para la traducción de textos
 * @global objeto $sql objeto global de interacción con la BD
 * @param entero $idProveedor identificador único del proveedoro en la BD
 * 
 * @return int id del regimen del proveedor
 */
function getRegimen($idProveedor) {
    global  $sql;

    $respuesta = array();

    $regimen = $sql->obtenerValor("proveedores", 'regimen', 'id = "'.$idProveedor.'"');
    
    $respuesta['regimen']       = $regimen;

    Servidor::enviarJSON($respuesta);
}
