<?php

/**
 *
 * @package     FOM
 * @subpackage  Empleados
 * @author      Pablo Andres Velez Vidal <pavelez@genesyscorp.com.co>
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2013 GENESYS
 * @version     0.1
 * 
 * Modificado el 20-01-12
 *
 * */
global $url_accion, $forma_procesar, $forma_id, $forma_cantidadRegistros, $forma_datos, $forma_pagina, $forma_orden, $forma_nombreOrden, $forma_dialogo, $forma_consultaGlobal, $forma_cantidad, $forma_cadenaItems;

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

    if (!isset($id) || (isset($id) && !$sql->existeItem('empleados', 'id', $id))) {
        $respuesta              = array();
        $respuesta['error']     = true;
        $respuesta['mensaje']   = $textos->id('NO_HA_SELECCIONADO_ITEM');

        Servidor::enviarJSON($respuesta);
        return NULL;
    }

    $objeto = new Empleado($id);
    $respuesta  = array();
    $codigo     = '';

    $pestana1           = HTML::campoOculto('id', $id);
    $pestana1          .= HTML::campoOculto('datos[dialogo]', '', 'idDialogo');
    $tipoDocumento      = $objeto->persona->tipoDocumento;
    $ciudadDocumento    = '';
    
    if (!empty($objeto->persona->ciudadDocumento)) {
        $ciudadDocumento = ' ' . $textos->id('DE') . ' ' . $objeto->persona->ciudadDocumento;
    }


    $pestana1a .= HTML::parrafo(HTML::frase($textos->id('TIPO_EMPLEADO'), 'negrilla ', ''), 'negrilla margenSuperior');
    $pestana1a .= HTML::frase($textos->id($objeto->tipoEmpleado), 'margenDerecha', '');
    $pestana1a .= HTML::parrafo(HTML::frase($textos->id('CARGO'), 'negrilla ', ''), 'negrilla margenSuperior');
    $pestana1a .= HTML::frase($textos->id($objeto->cargo), 'margenDerecha', '');
    $pestana1a .= HTML::parrafo(HTML::frase($textos->id('SEDE'), 'negrilla ', ''), 'negrilla margenSuperior');
    $pestana1a .= HTML::frase($textos->id($objeto->sede), 'margenDerecha', '');
    $pestana1a .= HTML::parrafo(HTML::frase($textos->id('SALARIO'), 'negrilla ', ''), 'negrilla margenSuperior');
    $pestana1a .= HTML::frase($textos->id($objeto->salario), 'margenDerecha', '');
    $pestana1a .= HTML::parrafo(HTML::frase($textos->id('FECHA_INICIO'), 'negrilla ', ''), 'negrilla margenSuperior');
    $pestana1a .= HTML::frase($textos->id($objeto->fechaInicio), 'margenDerecha', '');
    
    if (!empty($objeto->fechaFin)) {
        $pestana1a .= HTML::parrafo(HTML::frase($textos->id('FECHA_FIN'), 'negrilla ', ''), 'negrilla margenSuperior');
        $pestana1a .= HTML::frase($textos->id($objeto->fechaFin), 'margenDerecha', '');
        
    }
    
    $pestana1b .= HTML::parrafo($textos->id('CONTACTO_PRINCIPAL'), 'negrilla margenSuperior margenInferior subtitulo letraVerde');
    $pestana1b .= HTML::parrafo($textos->id('DOCUMENTO'), 'negrilla margenSuperior');
    $pestana1b .= $tipoDocumento . ': ' . $objeto->persona->documentoIdentidad . $ciudadDocumento;
    $pestana1b .= HTML::parrafo($textos->id('NOMBRES'), 'negrilla margenSuperior');
    $pestana1b .= $objeto->persona->primerNombre . ' ' . $objeto->persona->segundoNombre;
    ;
    $pestana1b .= HTML::parrafo($textos->id('APELLIDOS'), 'negrilla margenSuperior', '');
    $pestana1b .= $objeto->persona->primerApellido . ' ' . $objeto->persona->segundoApellido;
    $pestana1b .= HTML::parrafo($textos->id('CELULAR'), 'negrilla margenSuperior');
    $pestana1b .= $objeto->persona->celular;
    if (!empty($objeto->persona->correo)) {
        $pestana1b .= HTML::parrafo($textos->id('EMAIL'), 'negrilla margenSuperior');
        $pestana1b .= $objeto->persona->correo;
    }
    if (!empty($objeto->persona->ciudadResidencia)) {
        $pestana1b .= HTML::parrafo($textos->id('CIUDAD_RESIDENCIA'), 'negrilla margenSuperior');
        $pestana1b .= $objeto->persona->ciudadResidencia;
    }
    if (!empty($objeto->persona->fax)) {
        $pestana1b .= HTML::parrafo($textos->id('FAX'), 'negrilla margenSuperior');
        $pestana1b .= $objeto->persona->fax;
    }
    if (!empty($objeto->persona->telefono)) {
        $pestana1b .= HTML::parrafo($textos->id('TELEFONO'), 'negrilla margenSuperior');
        $pestana1b .= $objeto->persona->telefono;
    }
    if (!empty($objeto->persona->direccion)) {
        $pestana1b .= HTML::parrafo($textos->id('DIRECCION'), 'negrilla margenSuperior');
        $pestana1b .= $objeto->persona->direccion;
    }
    if (!empty($objeto->observaciones)) {
        $pestana1b .= HTML::parrafo($textos->id('OBSERVACIONES'), 'negrilla margenSuperior');
        $pestana1b .= $objeto->observaciones;
    }

    $contenedora = HTML::contenedor($pestana1a, 'contenedorIzquierdo');
    $contenedorb = HTML::contenedor($pestana1b, 'contenedorDerecho');

    $codigo .= HTML::contenedor($contenedora . $contenedorb, 'altura400px overflowAuto');


    $respuesta['generar']       = true;
    $respuesta['titulo']        = HTML::parrafo($textos->id('CONSULTAR_ITEM'), 'letraBlanca negrilla subtitulo');
    $respuesta['destino']       = '#cuadroDialogo';
    $respuesta['ancho']         = 800;
    $respuesta['alto']          = 500;
    $respuesta['codigo']        = $codigo;



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

    $objeto = new Empleado();
    $destino = '/ajax' . $objeto->urlBase . '/add';
    $respuesta = array();
    $codigo = '';

    if (empty($datos)) {

        $pestana1       = HTML::campoOculto('procesar', 'true');
        $pestana1      .= HTML::campoOculto('datos[dialogo]', '', 'idDialogo');
        //seleccionar los tipos de documentos
        $tiposDocumentos    = $sql->seleccionar(array('tipos_documento'), array('id', 'nombre'), 'id NOT IN (0)', 'id', 'nombre ASC');
        $tiposDoc           = array();
        while ($tiposDocumento = $sql->filaEnObjeto($tiposDocumentos)) {
            $tiposDoc[$tiposDocumento->id] = $tiposDocumento->nombre;
        }

        $listaTipodocumento = HTML::listaDesplegable('datos[id_tipo_documento]', $tiposDoc, '', '', 'listaTipoDocumentoContactoEmpleado');

        $pestana1a  = HTML::parrafo(HTML::frase($textos->id('TIPO_EMPLEADO'), 'negrilla', ''), 'negrilla margenSuperior');
        $pestana1a .= HTML::campoTexto('datos[id_tipo_empleado]', 40, 255, '', 'autocompletable campoObligatorio', '', array('title' => HTML::urlInterna('TIPOS_EMPLEADO', 0, true, 'listar')), $textos->id('AYUDA_USO_AUTOCOMPLETAR'), HTML::urlInterna('TIPOS_EMPLEADO', 0, true, 'add'));
        $pestana1a .= HTML::parrafo(HTML::frase($textos->id('CARGO'), 'negrilla', ''), 'negrilla margenSuperior');
        $pestana1a .= HTML::campoTexto('datos[id_cargo]', 40, 255, '', 'autocompletable campoObligatorio', '', array('title' => HTML::urlInterna('CARGOS', 0, true, 'listar')), $textos->id('AYUDA_USO_AUTOCOMPLETAR'), HTML::urlInterna('CARGOS', 0, true, 'add'));
        $pestana1a .= HTML::parrafo(HTML::frase($textos->id('SEDE'), 'negrilla', ''), 'negrilla margenSuperior');
        $pestana1a .= HTML::campoTexto('datos[id_sede]', 40, 255, '', 'autocompletable campoObligatorio', '', array('title' => HTML::urlInterna('SEDES_EMPRESA', 0, true, 'listar')), $textos->id('AYUDA_USO_AUTOCOMPLETAR'), HTML::urlInterna('SEDES_EMPRESA', 0, true, 'add'));
        $pestana1a .= HTML::parrafo($textos->id('SALARIO'), 'negrilla margenSuperior');
        $pestana1a .= HTML::campoTexto('datos[salario]', 30, 50, '', '', 'campoSalario', array('title' => $textos->id('')));
        $pestana1a .= HTML::parrafo($textos->id('FECHA_INICIO'), 'negrilla margenSuperior');
        $pestana1a .= HTML::campoTexto('datos[fecha_inicio]', 12, 12, '', 'fechaAntigua campoObligatorio', '', array('alt' => $textos->id('SELECCIONE_FECHA_INICIO')));
        $pestana1a .= HTML::parrafo($textos->id('DOCUMENTO'), 'negrilla margenSuperior');
        $pestana1a .= HTML::campoTexto('datos[documento_identidad]', 25, 50, '', 'campoObligatorio autocompletable', 'campoDocumentoPersona', array('title' => HTML::urlInterna('INICIO', 0, true, 'listarPersonas')), $textos->id('AYUDA_USO_AUTOCOMPLETAR')) . HTML::frase('Persona ya registrada.', 'margenIzquierda letraVerde oculto', 'frasePersonaRegistrada');
        $pestana1a .= HTML::parrafo($textos->id('TIPO_DOCUMENTO'), 'negrilla margenSuperior');
        $pestana1a .= $listaTipodocumento;
        $pestana1a .= HTML::parrafo($textos->id('CIUDAD_EXPEDICION_DOCUMENTO'), 'negrilla margenSuperior');
        $pestana1a .= HTML::campoTexto('datos[id_ciudad_documento]', 35, 255, '', 'autocompletable', 'campoCiudadDocumentoPersona', array('title' => HTML::urlInterna('CIUDADES', 0, true, 'listar')), $textos->id('SELECCIONE_CIUDAD_EXPEDICION_DOCUMENTO'));
        $pestana1a .= HTML::parrafo($textos->id('PRIMER_NOMBRE'), 'negrilla margenSuperior');
        $pestana1a .= HTML::campoTexto('datos[primer_nombre]', 30, 50, '', 'campoObligatorio', 'campoPrimerNombrePersona', array('title' => $textos->id('')));

        $pestana1b .= HTML::parrafo($textos->id('SEGUNDO_NOMBRE'), 'negrilla margenSuperior');
        $pestana1b .= HTML::campoTexto('datos[segundo_nombre]', 30, 50, '', '', 'campoSegundoNombrePersona', array('title' => $textos->id('')));
        $pestana1b .= HTML::parrafo($textos->id('PRIMER_APELLIDO'), 'negrilla margenSuperior', '');
        $pestana1b .= HTML::campoTexto('datos[primer_apellido]', 30, 50, '', 'campoObligatorio', 'campoPrimerApellidoPersona');
        $pestana1b .= HTML::parrafo($textos->id('SEGUNDO_APELLIDO'), 'negrilla margenSuperior');
        $pestana1b .= HTML::campoTexto('datos[segundo_apellido]', 30, 50, '', '', 'campoSegundoApellidoPersona');
        $pestana1b .= HTML::parrafo($textos->id('DIRECCION'), 'negrilla margenSuperior');
        $pestana1b .= HTML::campoTexto('datos[direccion]', 30, 50, '', '', 'campodireccionPersona', array('title' => $textos->id('')));
        $pestana1b .= HTML::parrafo($textos->id('EMAIL'), 'negrilla margenSuperior');
        $pestana1b .= HTML::campoTexto('datos[correo]', 30, 50, '', '', 'campoCorreoPersona', array('title' => $textos->id('')));
        $pestana1b .= HTML::parrafo($textos->id('TELEFONO'), 'negrilla margenSuperior');
        $pestana1b .= HTML::campoTexto('datos[telefono]', 30, 50, '', '', 'campoTelefonoPersona', array('title' => $textos->id('')));
        $pestana1b .= HTML::parrafo($textos->id('CELULAR'), 'negrilla margenSuperior');
        $pestana1b .= HTML::campoTexto('datos[celular]', 30, 50, '', 'campoObligatorio', 'campoCelularPersona', array('title' => $textos->id('')));
        $pestana1b .= HTML::parrafo($textos->id('FAX'), 'negrilla margenSuperior');
        $pestana1b .= HTML::campoTexto('datos[fax]', 30, 50, '', '', 'campoFaxPersona', array('title' => $textos->id('')));
        $pestana1b .= HTML::parrafo($textos->id('ACTIVO'), 'negrilla margenSuperior');
        $pestana1b .= HTML::parrafo(HTML::campoChequeo('datos[activo]', true), '');


        $contenedora = HTML::contenedor($pestana1a, 'contenedorIzquierdo');
        $contenedorb = HTML::contenedor($pestana1b, 'contenedorDerecho');


        $pestana1 .= HTML::contenedor($contenedora . $contenedorb, 'altura450px');


        $pestana2 .= HTML::parrafo($textos->id('OBSERVACIONES'), 'negrilla margenSuperior');
        $pestana2 .= HTML::areaTexto('datos[observaciones]', 7, 80, '');

        $pestana2 = HTML::contenedor($pestana2, 'pestana1');


        $pestanas = array(
            HTML::frase($textos->id('INFORMACION_EMPLEADO'), 'letraBlanca') => $pestana1,
            HTML::frase($textos->id('OBSERVACIONES'), 'letraBlanca') => $pestana2,
        );

        $codigo .= HTML::pestanas2('pestanasAgregar', $pestanas);

        $codigo .= HTML::parrafo(HTML::boton('chequeo', $textos->id('ACEPTAR'), '', '', '', '', '') . HTML::frase($textos->id('REGISTRO_AGREGADO'), 'textoExitoso margenIzquierdaDoble', 'textoExitoso'), 'margenSuperior');


        $codigo = HTML::forma($destino, $codigo, 'P', true, '', '', '');



        $respuesta['generar']   = true;

        $respuesta['titulo']    = HTML::parrafo($textos->id('ADICIONAR_ITEM'), 'letraBlanca negrilla subtitulo');
        $respuesta['destino']   = '#cuadroDialogo';
        $respuesta['ancho']     = 800;
        $respuesta['alto']      = 600;
        $respuesta['codigo']    = $codigo;
    } else {

        $respuesta['error'] = true;

        $existeCiudadDocumento      = $sql->existeItem('lista_ciudades', 'cadena', $datos['id_ciudad_documento']);
        $existetipoEmpleado         = $sql->existeItem('tipos_empleado', 'nombre', $datos['id_tipo_empleado']);
        $existeSede                 = $sql->existeItem('sedes_empresa', 'nombre', $datos['id_sede']);
        $existeCargo                = $sql->existeItem('cargos', 'id', $datos['id_cargo']);
        
        $idPersona                  = $sql->obtenerValor('personas', 'id', 'documento_identidad = "'. $datos['documento_identidad'] .'"'); 
        $existeEmpleado             = ($idPersona == "") ? FALSE : $sql->existeItem('empleados', 'id_persona', $idPersona);

        if (empty($datos['id_tipo_empleado'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_TIPO_EMPLEADO');
            
        } elseif (!$existetipoEmpleado) {
            $respuesta['mensaje'] = $textos->id('ERROR_EXISTENCIA_TIPO_EMPLEADO');
            
        } elseif (empty($datos['id_sede'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_SEDE_EMPRESA');
            
        } elseif (!$existeSede) {
            $respuesta['mensaje'] = $textos->id('ERROR_EXISTENCIA_SEDE_EMPRESA');
            
        } elseif (empty($datos['id_cargo'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_CARGO_EMPLEADO');
            
        } elseif (!$existeCargo) {
            $respuesta['mensaje'] = $textos->id('ERROR_EXISTENCIA_CARGO_EMPLEADO');
            
        } elseif (!empty($datos['id_ciudad_documento']) && !$existeCiudadDocumento) {
            $respuesta['mensaje'] = $textos->id('ERROR_EXISTENCIA_CIUDAD_DOCUMENTO');
            
        } elseif (empty($datos['telefono']) && empty($datos['celular'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_TELEFONO');
            
        } elseif (empty($datos['documento_identidad'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_DOCUMENTO_CONTACTO');
            
        } elseif ($existeEmpleado) {
            $respuesta['mensaje'] = $textos->id('ERROR_EXISTENCIA_DOCUMENTO_EMPLEADO');
 
        } elseif (empty($datos['primer_nombre'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_NOMBRE');
            
        } elseif (empty($datos['primer_apellido'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_APELLIDO');
            
        } else {

            $idItem = $objeto->adicionar($datos);
            if ($idItem) {
                /*                 * ************** Creo el nuevo item que se insertara via ajax *************** */
                $objeto = new Empleado($idItem);

                $estado = ($objeto->activo) ? HTML::frase($textos->id('ACTIVO'), 'activo') : HTML::frase($textos->id('INACTIVO'), 'inactivo');
                  
                $celdas     = array($objeto->tipoEmpleado, $objeto->persona->nombreCompleto, $objeto->sede, $objeto->persona->celular, $objeto->persona->correo, $estado);
                $claseFila  = '';
                $idFila     = $idItem;
                $celdas1    = HTML::crearNuevaFila($celdas, $claseFila, $idFila);
                
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
 * Funcion modificar
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

    if (!isset($id) || (isset($id) && !$sql->existeItem('empleados', 'id', $id))) {
        $respuesta              = array();
        $respuesta['error']     = true;
        $respuesta['mensaje']   = $textos->id('NO_HA_SELECCIONADO_ITEM');

        Servidor::enviarJSON($respuesta);
        return NULL;
    }

    $objeto     = new Empleado($id);
    $destino    = '/ajax' . $objeto->urlBase . '/edit';
    $respuesta  = array();
    $codigo     = '';

    if (empty($datos)) {

        $pestana1    = HTML::campoOculto('procesar', 'true');
        $pestana1   .= HTML::campoOculto('id', $id);
        $pestana1   .= HTML::campoOculto('datos[dialogo]', '', 'idDialogo');
        //seleccionar los tipos de documentos
        $tiposDocumentos    = $sql->seleccionar(array('tipos_documento'), array('id', 'nombre'), 'id NOT IN (0)', 'id', 'nombre ASC');
        $tiposDoc           = array();
        while ($tiposDocumento = $sql->filaEnObjeto($tiposDocumentos)) {
            $tiposDoc[$tiposDocumento->id] = $tiposDocumento->nombre;
            
        }

        $listaTipodocumento = HTML::listaDesplegable('datos[id_tipo_documento]', $tiposDoc, $objeto->persona->idTipoDocumento, '', 'listaTipoDocumentoContactoEmpleado');
        $ciudadDocumento    = $sql->obtenerValor('lista_ciudades', 'cadena', 'id = "' . $objeto->persona->idCiudadDocumento . '"');

        $pestana1a  = HTML::parrafo(HTML::frase($textos->id('TIPO_EMPLEADO'), 'negrilla', ''), 'negrilla margenSuperior');
        $pestana1a .= HTML::campoTexto('datos[id_tipo_empleado]', 40, 255, $objeto->tipoEmpleado, 'autocompletable campoObligatorio', '', array('title' => HTML::urlInterna('TIPOS_EMPLEADO', 0, true, 'listar')), $textos->id('AYUDA_USO_AUTOCOMPLETAR'), HTML::urlInterna('TIPOS_EMPLEADO', 0, true, 'add'));
        $pestana1a .= HTML::parrafo(HTML::frase($textos->id('CARGO'), 'negrilla', ''), 'negrilla margenSuperior');
        $pestana1a .= HTML::campoTexto('datos[id_cargo]', 40, 255, $objeto->cargo, 'autocompletable campoObligatorio', '', array('title' => HTML::urlInterna('CARGOS', 0, true, 'listar')), $textos->id('AYUDA_USO_AUTOCOMPLETAR'), HTML::urlInterna('CARGOS', 0, true, 'add'));
        $pestana1a .= HTML::parrafo(HTML::frase($textos->id('SEDE'), 'negrilla', ''), 'negrilla margenSuperior');
        $pestana1a .= HTML::campoTexto('datos[id_sede]', 40, 255, $objeto->sede, 'autocompletable campoObligatorio', '', array('title' => HTML::urlInterna('SEDES_EMPRESA', 0, true, 'listar')), $textos->id('AYUDA_USO_AUTOCOMPLETAR'), HTML::urlInterna('SEDES_EMPRESA', 0, true, 'add'));
        $pestana1a .= HTML::parrafo($textos->id('SALARIO'), 'negrilla margenSuperior');
        $pestana1a .= HTML::campoTexto('datos[salario]', 30, 50, $objeto->salario, '', 'campoSalario', array('title' => $textos->id('')));
        $pestana1a .= HTML::parrafo($textos->id('FECHA_INICIO'), 'negrilla margenSuperior');
        $pestana1a .= HTML::campoTexto('datos[fecha_inicio]', 12, 12, $objeto->fechaInicio, 'fechaAntigua campoObligatorio', '', array('alt' => $textos->id('SELECCIONE_FECHA_INICIO')));
        $pestana1a .= HTML::parrafo($textos->id('FECHA_FIN'), 'negrilla margenSuperior');
        $pestana1a .= HTML::campoTexto('datos[fecha_fin]', 12, 12, $objeto->fechaFin, 'fechaAntigua campoObligatorio', '', array('alt' => $textos->id('SELECCIONE_FECHA_INICIO')));
        $pestana1a .= HTML::parrafo($textos->id('DOCUMENTO'), 'negrilla margenSuperior');
        $pestana1a .= HTML::campoTexto('datos[documento_identidad]', 25, 50, $objeto->persona->documentoIdentidad, 'campoObligatorio autocompletable', 'campoDocumentoPersona', array('title' => HTML::urlInterna('INICIO', 0, true, 'listarPersonas')), $textos->id('AYUDA_USO_AUTOCOMPLETAR')) . HTML::frase('Persona ya registrada.', 'margenIzquierda letraVerde oculto', 'frasePersonaRegistrada');
        $pestana1a .= HTML::parrafo($textos->id('TIPO_DOCUMENTO'), 'negrilla margenSuperior');
        $pestana1a .= $listaTipodocumento;
        $pestana1a .= HTML::parrafo($textos->id('CIUDAD_EXPEDICION_DOCUMENTO'), 'negrilla margenSuperior');
        $pestana1a .= HTML::campoTexto('datos[id_ciudad_documento]', 35, 255, $ciudadDocumento, 'autocompletable', 'campoCiudadDocumentoPersona', array('title' => HTML::urlInterna('CIUDADES', 0, true, 'listar')), $textos->id('SELECCIONE_CIUDAD_EXPEDICION_DOCUMENTO'));

        $pestana1b .= HTML::parrafo($textos->id('PRIMER_NOMBRE'), 'negrilla margenSuperior');
        $pestana1b .= HTML::campoTexto('datos[primer_nombre]', 30, 50, $objeto->persona->primerNombre, 'campoObligatorio', 'campoPrimerNombrePersona', array('title' => $textos->id('')));
        $pestana1b .= HTML::parrafo($textos->id('SEGUNDO_NOMBRE'), 'negrilla margenSuperior');
        $pestana1b .= HTML::campoTexto('datos[segundo_nombre]', 30, 50, $objeto->persona->segundoNombre, '', 'campoSegundoNombrePersona', array('title' => $textos->id('')));
        $pestana1b .= HTML::parrafo($textos->id('PRIMER_APELLIDO'), 'negrilla margenSuperior', '');
        $pestana1b .= HTML::campoTexto('datos[primer_apellido]', 30, 50, $objeto->persona->primerApellido, 'campoObligatorio', 'campoPrimerApellidoPersona');
        $pestana1b .= HTML::parrafo($textos->id('SEGUNDO_APELLIDO'), 'negrilla margenSuperior');
        $pestana1b .= HTML::campoTexto('datos[segundo_apellido]', 30, 50, $objeto->persona->segundoApellido, '', 'campoSegundoApellidoPersona');
        $pestana1b .= HTML::parrafo($textos->id('DIRECCION'), 'negrilla margenSuperior');
        $pestana1b .= HTML::campoTexto('datos[direccion]', 30, 50, $objeto->persona->direccion, '', 'campodireccionPersona', array('title' => $textos->id('')));
        $pestana1b .= HTML::parrafo($textos->id('EMAIL'), 'negrilla margenSuperior');
        $pestana1b .= HTML::campoTexto('datos[correo]', 30, 50, $objeto->persona->correo, '', 'campoCorreoPersona', array('title' => $textos->id('')));
        $pestana1b .= HTML::parrafo($textos->id('TELEFONO'), 'negrilla margenSuperior');
        $pestana1b .= HTML::campoTexto('datos[telefono]', 30, 50, $objeto->persona->telefono, '', 'campoTelefonoPersona', array('title' => $textos->id('')));
        $pestana1b .= HTML::parrafo($textos->id('CELULAR'), 'negrilla margenSuperior');
        $pestana1b .= HTML::campoTexto('datos[celular]', 30, 50, $objeto->persona->celular, 'campoObligatorio', 'campoCelularPersona', array('title' => $textos->id('')));
        $pestana1b .= HTML::parrafo($textos->id('FAX'), 'negrilla margenSuperior');
        $pestana1b .= HTML::campoTexto('datos[fax]', 30, 50, $objeto->persona->fax, '', 'campoFaxPersona', array('title' => $textos->id('')));
        $pestana1b .= HTML::parrafo($textos->id('ACTIVO') . HTML::frase(HTML::campoChequeo('datos[activo]', $objeto->activo), ' margenIzquierda'), 'negrilla margenSuperior');



        $contenedora = HTML::contenedor($pestana1a, 'contenedorIzquierdo');
        $contenedorb = HTML::contenedor($pestana1b, 'contenedorDerecho');


        $pestana1 .= HTML::contenedor($contenedora . $contenedorb, 'altura450px');


        $pestana2 .= HTML::parrafo($textos->id('OBSERVACIONES'), 'negrilla margenSuperior');
        $pestana2 .= HTML::areaTexto('datos[observaciones]', 7, 80, $objeto->observaciones);

        $pestana2 = HTML::contenedor($pestana2, 'pestana1');


        $pestanas = array(
            HTML::frase($textos->id('INFORMACION_EMPLEADO'), 'letraBlanca') => $pestana1,
            HTML::frase($textos->id('OBSERVACIONES'), 'letraBlanca') => $pestana2,
        );

        $codigo .= HTML::pestanas2('pestanasAgregar', $pestanas);

        $codigo .= HTML::parrafo(HTML::boton('chequeo', $textos->id('ACEPTAR'), '', '', 'botonModificarEmpleados', '', '') . HTML::frase($textos->id('REGISTRO_MODIFICADO'), 'textoExitoso margenIzquierdaDoble', 'textoExitoso'), 'margenSuperior');

        $codigo = HTML::forma($destino, $codigo, 'P', true, '', '', '');


        $respuesta['generar']   = true;
        $respuesta['titulo']    = HTML::parrafo($textos->id('MODIFICAR_ITEM'), 'letraBlanca negrilla subtitulo');
        $respuesta['destino']   = '#cuadroDialogo';
        $respuesta['ancho']     = 800;
        $respuesta['alto']      = 600;
        $respuesta['codigo']    = $codigo;
    } else {

        $respuesta['error'] = true;


        $existeCiudadDocumento = $sql->existeItem('lista_ciudades', 'cadena', $datos['id_ciudad_documento']);
        $existetipoEmpleado = $sql->existeItem('tipos_empleado', 'nombre', $datos['id_tipo_empleado']);
        $existeSede = $sql->existeItem('sedes_empresa', 'nombre', $datos['id_sede']);
        $existeCargo = $sql->existeItem('cargos', 'id', $datos['id_cargo']);

        if (empty($datos['id_tipo_empleado'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_TIPO_EMPLEADO');
            
        } elseif (!$existetipoEmpleado) {
            $respuesta['mensaje'] = $textos->id('ERROR_EXISTENCIA_TIPO_EMPLEADO');
            
        } elseif (empty($datos['id_sede'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_SEDE');
            
        } elseif (!$existeSede) {
            $respuesta['mensaje'] = $textos->id('ERROR_EXISTENCIA_SEDE');
            
        } elseif (empty($datos['id_cargo'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_CARGO');
            
        } elseif (!$existeCargo) {
            $respuesta['mensaje'] = $textos->id('ERROR_EXISTENCIA_CARGO');
            
        } elseif (!empty($datos['id_ciudad_documento']) && !$existeCiudadDocumento) {
            $respuesta['mensaje'] = $textos->id('ERROR_EXISTENCIA_CIUDAD_DOCUMENTO');
            
        } elseif (empty($datos['telefono']) && empty($datos['celular'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_TELEFONO');
            
        } elseif (empty($datos['documento_identidad'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_DOCUMENTO_CONTACTO');
            
        } elseif (empty($datos['primer_nombre'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_NOMBRE');
            
        } elseif (empty($datos['primer_apellido'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_APELLIDO');
            
        } else {

            $idItem = $objeto->modificar($datos);
            
            if ($idItem) {
                /*                 * ************** Creo el nuevo item que se insertara via ajax *************** */
                $objeto = new Empleado($id);

                $estado =  ($objeto->activo) ? HTML::frase($textos->id('ACTIVO'), 'activo') : HTML::frase($textos->id('INACTIVO'), 'inactivo');
       
                $celdas = array($objeto->tipoEmpleado, $objeto->persona->nombreCompleto, $objeto->sede, $objeto->persona->celular, $objeto->persona->correo, $estado);
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
 * @global type $textos
 * @param type $id
 * @param type $confirmado 
 */
function eliminarItem($id, $confirmado, $dialogo) {
    global $textos, $sql, $modulo, $sesion_usuarioSesion;
    
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

    if (!isset($id) || (isset($id) && !$sql->existeItem('empleados', 'id', $id))) {
        $respuesta              = array();
        $respuesta['error']     = true;
        $respuesta['mensaje']   = $textos->id('NO_HA_SELECCIONADO_ITEM');

        Servidor::enviarJSON($respuesta);
        return NULL;
    }

    $objeto     = new Empleado($id);
    $destino    = '/ajax' . $objeto->urlBase . '/delete';
    $respuesta  = array();

    if (!$confirmado) {
        $titulo     = HTML::frase($objeto->persona->nombreCompleto, 'negrilla');
        $titulo1    = str_replace('%1', $titulo, $textos->id('CONFIRMAR_ELIMINACION'));
        $codigo     = HTML::campoOculto('procesar', 'true');
        $codigo    .= HTML::campoOculto('id', $id);
        $codigo    .= HTML::parrafo($titulo1);
        $codigo    .= HTML::parrafo(HTML::boton('chequeo', $textos->id('ACEPTAR'), '', 'botonOk', 'botonOk'), 'margenSuperior');
        $codigo    .= HTML::parrafo($textos->id('REGISTRO_ELIMINADO'), 'textoExitoso', 'textoExitoso');
        $codigo1    = HTML::forma($destino, $codigo);

        $respuesta['generar']   = true;
        $respuesta['codigo']    = $codigo1;
        $respuesta['destino']   = '#cuadroDialogo';
        $respuesta['titulo']    = HTML::parrafo($textos->id('ELIMINAR_ITEM'), 'letraBlanca negrilla subtitulo');
        $respuesta['ancho']     = 350;
        $respuesta['alto']      = 150;
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
        $item = '';
        $respuesta = array();
        $objeto = new Empleado();
        $registros = $configuracion['GENERAL']['registrosPorPagina'];
        
        if (!empty($cantidadRegistros)) {
            $registros = (int) $cantidadRegistros;
            
        }        
        
        $pagina             = 1;
        $registroInicial    = 0;


        $palabras = explode(' ', $datos);

        $condicionales = $data[1];

        if ($condicionales == '') {
            $condicion = '(cl.nombre REGEXP "(' . implode('|', $palabras) . ')")';
            
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

        $arregloItems = $objeto->listar($registroInicial, $registros, array('0'), $condicion, 'cl.nombre');

        if ($objeto->registrosConsulta) {//si la consulta trajo registros
            $datosPaginacion = array($objeto->registrosConsulta, $registroInicial, $registros, $pagina, $objeto->registrosConsulta);
            $item           .= $objeto->generarTabla($arregloItems, $datosPaginacion);
            $info            = HTML::parrafo('Tu busqueda trajo ' . $objeto->registrosConsulta . ' resultados', 'textoExitosoNotificaciones');
            
        } else {
            $datosPaginacion = 0;
            $item           .= $objeto->generarTabla($textos->id('NO_HAY_REGISTROS'), $datosPaginacion);
            $info            = HTML::parrafo('Tu busqueda no trajo resultados, por favor intenta otra busqueda', 'textoErrorNotificaciones');
            
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

/*
 * Funcion que se encarga de recargar la tabla de datos paginando
 */

function paginador($pagina, $orden = NULL, $nombreOrden = NULL, $consultaGlobal = NULL, $cantidadRegistros = NULL) {
    global $configuracion;

    $item = '';
    $respuesta = array();
    $objeto = new Empleado();

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
            $consultaGlobal = '(cl.nombre REGEXP "(' . implode('|', $palabras) . ')")';
            
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

    $respuesta['error']         = false;
    $respuesta['accion']        = 'insertar';
    $respuesta['contenido']     = $item;
    $respuesta['idContenedor']  = '#tablaRegistros';
    $respuesta['idDestino']     = '#contenedorTablaRegistros';
    $respuesta['paginarTabla']  = true;

    Servidor::enviarJSON($respuesta);
}

/**
 *
 * @global type $textos
 * @global type $sql
 * @param type $datos 
 */
function adicionarContacto($id, $datos = array()) {
    global $textos, $sql;

    $destino = '/ajax/empleados/adicionarContacto';
    $respuesta = array();

    if (empty($datos)) {

        //seleccionar los tipos de documentos
        $tiposDocumentos = $sql->seleccionar(array('tipos_documento'), array('id', 'nombre'), 'id NOT IN (0)', 'id', 'nombre ASC');
        $tiposDoc = array();
        while ($tiposDocumento = $sql->filaEnObjeto($tiposDocumentos)) {
            $tiposDoc[$tiposDocumento->id] = $tiposDocumento->nombre;
        }
        $listaTipodocumento = HTML::listaDesplegable('datos[id_tipo_documento]', $tiposDoc, '', '', 'listaTipoDocumentoPersona');


        $codigo  = HTML::campoOculto('procesar', 'true');
        $codigo .= HTML::campoOculto('datos[dialogo]', '', 'idDialogo');
        $codigo .= HTML::campoOculto('datos[id_empleado]', $id, ''); //id del empleado
        $codigo .= HTML::parrafo($textos->id('DOCUMENTO'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[documento_identidad]', 30, 50, '', 'campoObligatorio autocompletable', 'campoDocumentoPersona', array('title' => HTML::urlInterna('INICIO', 0, true, 'listarPersonas')), $textos->id('AYUDA_USO_AUTOCOMPLETAR')) . HTML::frase('Persona ya registrada.', 'margenIzquierda letraVerde oculto', 'frasePersonaRegistrada');
        $codigo .= HTML::parrafo($textos->id('TIPO_DOCUMENTO'), 'negrilla margenSuperior');
        $codigo .= $listaTipodocumento;
        $codigo .= HTML::parrafo($textos->id('PRIMER_NOMBRE'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[primer_nombre]', 30, 50, '', 'campoObligatorio', 'campoPrimerNombrePersona', array('title' => $textos->id('')));
        $codigo .= HTML::parrafo($textos->id('SEGUNDO_NOMBRE'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[segundo_nombre]', 30, 50, '', '', 'campoSegundoNombrePersona', array('title' => $textos->id('')));
        $codigo .= HTML::parrafo($textos->id('PRIMER_APELLIDO'), 'negrilla margenSuperior', '');
        $codigo .= HTML::campoTexto('datos[primer_apellido]', 30, 50, '', 'campoObligatorio', 'campoPrimerApellidoPersona');
        $codigo .= HTML::parrafo($textos->id('SEGUNDO_APELLIDO'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[segundo_apellido]', 30, 50, '', '', 'campoSegundoApellidoPersona');
        $codigo .= HTML::parrafo($textos->id('EMAIL'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[correo]', 30, 50, '', '', 'campoCorreoPersona', array('title' => $textos->id('')));
        $codigo .= HTML::parrafo($textos->id('TELEFONO'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[telefono]', 30, 50, '', '', 'campoTelefonoPersona', array('title' => $textos->id('')));
        $codigo .= HTML::parrafo($textos->id('CELULAR'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[celular]', 30, 50, '', 'campoObligatorio', 'campoCelularPersona', array('title' => $textos->id('')));
        $codigo .= HTML::parrafo($textos->id('FAX'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[fax]', 30, 50, '', '', 'campoFaxPersona', array('title' => $textos->id('')));
        $codigo .= HTML::parrafo(HTML::boton('chequeo', $textos->id('ACEPTAR'), 'botonOk', 'botonOk', 'botonOk'), 'margenSuperior');
        $codigo .= HTML::parrafo($textos->id('REGISTRO_AGREGADO'), 'textoExitoso', 'textoExitoso');
        $codigo1 = HTML::forma($destino, $codigo, 'P');

        $respuesta['generar']   = true;
        $respuesta['codigo']    = $codigo1;
        $respuesta['titulo']    = HTML::parrafo($textos->id('ADICIONAR_CONTACTO'), 'letraBlanca negrilla subtitulo');
        $respuesta['destino']   = '#cuadroDialogo';
        $respuesta['ancho']     = 450;
        $respuesta['alto']      = 590;
    } else {

        $respuesta['error'] = true;
        $idPersona          = $sql->obtenerValor('personas', 'id', 'documento_identidad = "' . $datos['documento_identidad'] . '"');
        $existeContacto     = $sql->existeItem('contactos_empleado', 'id_persona', $idPersona, 'id_empleado = "' . $datos['id_empleado'] . '"');

        if (empty($datos['documento_identidad'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_DOCUMENTO_CONTACTO');
            
        } elseif (empty($datos['primer_nombre'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_NOMBRE');
            
        } elseif (empty($datos['primer_apellido'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_APELLIDO');
            
        } elseif (empty($datos['celular'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_CELULAR');
            
        } elseif ($existeContacto) {
            $respuesta['mensaje'] = $textos->id('ERROR_EXISTE_CONTACTO');
            
        } else {
            $idEmpleado = $datos['id_empleado'];
            $dialogo    = $datos['dialogo'];
            
            unset($datos['dialogo']);
            unset($datos['id_empleado']);

            if (empty($idPersona)) {
                $persona = new Persona();
                $idPersona = $persona->adicionar($datos);
                
            } else {
                $idPersona = $idPersona;
                
            }

            $datosContacto = array(
                'id_empleado' => $idEmpleado,
                'id_persona' => $idPersona
            );
//            $sql->depurar = true;

            $consulta = $sql->insertar('contactos_empleado', $datosContacto);
            
            $idItem = $sql->ultimoId;
            
            if ($consulta) {
                $botonEditar    = HTML::contenedor('', 'editarRegistro');
                $botonEliminar  = HTML::contenedor('', 'eliminarRegistro');
                
                $nombreCompletoPersona = $datos['primer_nombre'] . ' ' . $datos['segundo_nombre'] . ' ' . $datos['primer_apellido'] . ' ' . $datos['segundo_apellido'];
                
                $celdas = array($nombreCompletoPersona, $datos['celular'], $datos['correo'], $botonEditar, $botonEliminar);
                $celdas1 = HTML::crearNuevaFilaDesdeModal($celdas, '', 'tablaEditarMasContactosTr_' . $idItem);
                

                    $respuesta['error'] = false;
                    $respuesta['accion'] = 'insertar';                

                if ($dialogo == '') {
                    $respuesta['insertarNuevaFila'] = true;
                    
                } else {
                    $respuesta['contenido']                 = $celdas1;
                    $respuesta['idContenedor']              = '#tablaEditarMasContactosTr_' . $idItem;
                    $respuesta['insertarNuevaFilaDialogo']  = true;
                    $respuesta['idDestino']                 = '#tablaEditarMasContactos';
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
 *
 * @global type $textos
 * @global type $sql
 * @param type $datos 
 */
function modificarContacto($id, $datos = array()) {
    global $textos, $sql;

    $destino        = '/ajax/empleados/modificarContacto';
    $respuesta      = array();
    $idPersona      = $sql->obtenerValor('contactos_empleado', 'id_persona', 'id = "' . $id . '"');
    $objeto         = new Persona($idPersona);

    if (empty($datos)) {
        //seleccionar los tipos de documentos
        $tiposDocumentos = $sql->seleccionar(array('tipos_documento'), array('id', 'nombre'), 'id NOT IN (0)', 'id', 'nombre ASC');
        $tiposDoc = array();
        
        while ($tiposDocumento = $sql->filaEnObjeto($tiposDocumentos)) {
            $tiposDoc[$tiposDocumento->id] = $tiposDocumento->nombre;
        }
        
        $listaTipodocumento = HTML::listaDesplegable('datos[id_tipo_documento]', $tiposDoc, $objeto->idTipoDocumento, '', 'listaTipoDocumentoContactoEmpleado');

        $codigo  = HTML::campoOculto('procesar', 'true');
        $codigo .= HTML::campoOculto('datos[dialogo]', '', 'idDialogo');
        $codigo .= HTML::campoOculto('id', $id, ''); //id del persona
        $codigo .= HTML::parrafo($textos->id('DOCUMENTO'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[documento_identidad]', 30, 50, $objeto->documentoIdentidad, 'campoObligatorio autocompletable', 'campoDocumentoPersona', array('title' => HTML::urlInterna('INICIO', 0, true, 'listarPersonas')), $textos->id('AYUDA_USO_AUTOCOMPLETAR')) . HTML::frase('Persona ya registrada.', 'margenIzquierda letraVerde oculto', 'frasePersonaRegistrada');
        $codigo .= HTML::parrafo($textos->id('TIPO_DOCUMENTO'), 'negrilla margenSuperior');
        $codigo .= $listaTipodocumento;
        $codigo .= HTML::parrafo($textos->id('PRIMER_NOMBRE'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[primer_nombre]', 30, 50, $objeto->primerNombre, 'campoObligatorio', 'campoPrimerNombrePersona', array('title' => $textos->id('')));
        $codigo .= HTML::parrafo($textos->id('SEGUNDO_NOMBRE'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[segundo_nombre]', 30, 50, $objeto->segundoNombre, '', 'campoSegundoNombrePersona', array('title' => $textos->id('')));
        $codigo .= HTML::parrafo($textos->id('PRIMER_APELLIDO'), 'negrilla margenSuperior', '');
        $codigo .= HTML::campoTexto('datos[primer_apellido]', 30, 50, $objeto->primerApellido, 'campoObligatorio', 'campoPrimerApellidoPersona');
        $codigo .= HTML::parrafo($textos->id('SEGUNDO_APELLIDO'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[segundo_apellido]', 30, 50, $objeto->segundoApellido, '', 'campoSegundoApellidoPersona');
        $codigo .= HTML::parrafo($textos->id('EMAIL'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[correo]', 30, 50, $objeto->correo, '', 'campoCorreoPersona', array('title' => $textos->id('')));
        $codigo .= HTML::parrafo($textos->id('TELEFONO'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[telefono]', 30, 50, $objeto->telefono, '', 'campoTelefonoPersona', array('title' => $textos->id('')));
        $codigo .= HTML::parrafo($textos->id('CELULAR'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[celular]', 30, 50, $objeto->celular, 'campoObligatorio', 'campoCelularPersona', array('title' => $textos->id('')));
        $codigo .= HTML::parrafo($textos->id('FAX'), 'negrilla margenSuperior');
        $codigo .= HTML::campoTexto('datos[fax]', 30, 50, $objeto->fax, '', 'campoFaxPersona', array('title' => $textos->id('')));
        $codigo .= HTML::parrafo(HTML::boton('chequeo', $textos->id('ACEPTAR'), 'botonOk', 'botonOk', 'botonOk'), 'margenSuperior');
        $codigo .= HTML::parrafo($textos->id('REGISTRO_MODIFICADO'), 'textoExitoso', 'textoExitoso');
        $codigo1 = HTML::forma($destino, $codigo, 'P');

        $respuesta['generar']   = true;
        $respuesta['codigo']    = $codigo1;
        $respuesta['titulo']    = HTML::parrafo($textos->id('MODIFICAR_CONTACTO'), 'letraBlanca negrilla subtitulo');
        $respuesta['destino']   = '#cuadroDialogo';
        $respuesta['ancho']     = 450;
        $respuesta['alto']      = 590;
        
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
            
            $idPersona = $sql->obtenerValor('contactos_empleado', 'id_persona', 'id = "' . $id . '"');
            
            $consulta = $sql->modificar('personas', $datos, 'id = "'.$idPersona.'"');
            
            if ($consulta) {
                
                $botonEditar    = HTML::contenedor('', 'editarRegistro');
                $botonEliminar  = HTML::contenedor('', 'eliminarRegistro');
                
                $nombreCompletoPersona = $datos['primer_nombre'] . ' ' . $datos['segundo_nombre'] . ' ' . $datos['primer_apellido'] . ' ' . $datos['segundo_apellido'];
                
                $celdas = array($nombreCompletoPersona, $datos['celular'], $datos['correo'], $botonEditar, $botonEliminar);
                
                $celdas1 = HTML::crearFilaAModificar($celdas);

                $respuesta['error']                 = false;
                $respuesta['accion']                = 'insertar';
                $respuesta['contenido']             = $celdas1;
                $respuesta['idContenedor']          = '#tablaEditarMasContactosTr_' . $id;
                $respuesta['modificarFilaDialogo']  = true;
                $respuesta['idDestino']             = '#tablaEditarMasContactosTr_' . $id;
                $respuesta['ventanaDialogo']        = $dialogo;
                
            } else {
                $respuesta['mensaje'] = $textos->id('ERROR_DESCONOCIDO');
                
            }
            
        }
        
    }

    Servidor::enviarJSON($respuesta);
}
 

/**
 * @global type $textos
 * @param type $id
 * @param type $confirmado 
 */
function eliminarContacto($id, $confirmado, $dialogo) {
    global $textos, $sql;

    $destino    = '/ajax/empleados/eliminarContacto';
    $respuesta  = array();

    $objeto = new Persona($sql->obtenerValor('contactos_empleado', 'id_persona', 'id = "' . $id . '"'));

    if (!$confirmado) {
        
        $titulo  = HTML::frase($objeto->primerNombre . ' ' . $objeto->primerApellido, 'negrilla');
        $titulo1 = str_replace('%1', $titulo, $textos->id('CONFIRMAR_ELIMINACION'));
        $codigo  = HTML::campoOculto('procesar', 'true');
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

        $consulta = $sql->eliminar('contactos_empleado', 'id = "' . $id . '"');

        if ($consulta) {
                $respuesta['error']     = false;
                $respuesta['accion']    = 'insertar';
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
    $puedeEliminarMasivo = Perfil::verificarPermisosBoton('botonEliminarMasivoEmpleados', $modulo->nombre);
    
    if(!$puedeEliminarMasivo && $sesion_usuarioSesion->id != 0) {
        $respuesta            = array();
        $respuesta['error']   = true;
        $respuesta['mensaje'] = $textos->id('ACCESO_DENEGADO');
        
        Servidor::enviarJSON($respuesta);
        return FALSE;
        
    }

    $destino    = '/ajax/empleados/eliminarVarios';
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
            $objeto         = new Empleado($val);
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
