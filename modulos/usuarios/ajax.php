<?php

/**
 *
 * @package     FOM
 * @subpackage  Usuarios
 * @author      Pablo Andres Velez Vidal <pavelez@genesyscorp.com.co>
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2013 GENESYS
 * @version     0.1
 * 
 * Modificado el 20-01-12
 *
 * */

if (isset($url_accion)) {
    
    switch ($url_accion) {
        case 'validate'             :   validarUsuario($forma_usuario, $forma_contrasena, $forma_datos);
                                        break;
        
        case 'logout'               :   cerrarSesion();
                                        break;
        
        case 'add'                  :   $datos = ($forma_procesar) ? $forma_datos : array();
                                        adicionarItem($datos);
                                        break;
        
        case 'see'                  :   cosultarItem($forma_id);
                                        break;
        
        case 'edit'                 :   $datos = ($forma_procesar) ? $forma_datos : array();
                                        modificarItem($forma_id, $datos);
                                        break;
        
        case 'delete'               :   $confirmado = ($forma_procesar) ? true : false;
                                        eliminarItem($forma_id, $confirmado);
                                        break;
                                    
        case 'listar'               :   listarItems($url_cadena);
                                        break;                                    
        
        case 'search'               :   buscarItem($forma_datos, $forma_cantidadRegistros);
                                        break;
                                    
        case 'move'                 :   paginador($forma_pagina, $forma_orden, $forma_nombreOrden, $forma_consultaGlobal, $forma_cantidadRegistros);
                                        break;
        
        case 'verificarUsuario'     :   verificarUsuario($forma_usuario);
                                        break;
        
        case 'eliminarVarios'       :   $confirmado = ($forma_procesar) ? true : false;
                                        eliminarVarios($confirmado, $forma_cantidad, $forma_cadenaItems);
                                        break;
        
    }
    
}

/**
 *
 * @global type $textos
 * @global type $sql
 * @global type $sesion_usuarioSesion
 * @param type $id
 * @param type $datos 
 */
function cosultarItem($id) {
    global $textos;

    $objeto     = new Usuario($id);
    $respuesta  = array();

    $pestana1 = HTML::campoOculto('id', $id);

    $ciudadResidencia = $objeto->persona->ciudadResidencia . ', ' . $objeto->persona->estadoResidencia . ', ' . $objeto->persona->paisResidencia;

    $pestana1a = HTML::parrafo($textos->id('TIPO_DOCUMENTO'), 'negrilla margenSuperior');
    $pestana1a .= HTML::parrafo($objeto->persona->tipoDocumento, '');
    $pestana1b = HTML::parrafo($textos->id('DOCUMENTO'), 'negrilla margenSuperior');
    $pestana1b .= HTML::parrafo($objeto->persona->documentoIdentidad, '');
    $pestana1a .= HTML::parrafo($textos->id('CIUDAD_EXPEDICION_DOCUMENTO'), 'negrilla margenSuperior');
    $pestana1a .= HTML::parrafo($objeto->persona->ciudadDocumento, '');
    $pestana1b .= HTML::parrafo($textos->id('GENERO'), 'negrilla margenSuperior');
    $pestana1b .= HTML::parrafo($objeto->persona->genero, '');
    $pestana1a .= HTML::parrafo($textos->id('PRIMER_NOMBRE'), 'negrilla margenSuperior');
    $pestana1a .= HTML::parrafo($objeto->persona->primerNombre, '', '');
    $pestana1b .= HTML::parrafo($textos->id('SEGUNDO_NOMBRE'), 'negrilla margenSuperior');
    $pestana1b .= HTML::parrafo($objeto->persona->segundoNombre, '');
    $pestana1a .= HTML::parrafo($textos->id('PRIMER_APELLIDO'), 'negrilla margenSuperior');
    $pestana1a .= HTML::parrafo($objeto->persona->primerApellido, '');
    $pestana1b .= HTML::parrafo($textos->id('SEGUNDO_APELLIDO'), 'negrilla margenSuperior');
    $pestana1b .= HTML::parrafo($objeto->persona->segundoApellido, '');     
    $pestana1a .= HTML::parrafo($textos->id('OBSERVACIONES'), 'negrilla margenSuperior');
    $pestana1a .= HTML::parrafo($objeto->observaciones, ' margenSuperior');
    $pestana1b .= HTML::parrafo($textos->id('FECHA_NACIMIENTO'), 'negrilla margenSuperior');
    $pestana1b .= HTML::parrafo($objeto->persona->fechaNacimiento, '');
    $pestana1b .= HTML::parrafo($textos->id('FOTOGRAFIA_USUARIO'), 'negrilla margenSuperior');
    $pestana1b .= HTML::enlace(HTML::imagen($objeto->persona->imagenMiniatura, 'imagenItem', ''), $objeto->persona->imagenPrincipal, '', '', array('rel' => 'prettyPhoto[]'));


    $contenedor1 = HTML::contenedor($pestana1a, 'contenedorIzquierdo');
    $contenedor2 = HTML::contenedor($pestana1b, 'contenedorDerecho');

    $pestana1 .= HTML::contenedor($contenedor1 . $contenedor2, 'pestana1');


    $pestana2 = HTML::parrafo($textos->id('CIUDAD_RESIDENCIA'), 'negrilla margenSuperior');
    $pestana2 .= HTML::parrafo($ciudadResidencia, '');
    $pestana2 .= HTML::parrafo($textos->id('DIRECCION'), 'negrilla margenSuperior');
    $pestana2 .= HTML::parrafo($objeto->persona->direccion, '');
    $pestana2 .= HTML::parrafo($textos->id('TELEFONO'), 'negrilla margenSuperior');
    $pestana2 .= HTML::parrafo($objeto->persona->telefono, '');
    $pestana2 .= HTML::parrafo($textos->id('CELULAR'), 'negrilla margenSuperior');
    $pestana2 .= HTML::parrafo($objeto->persona->celular, '');
    $pestana2 .= HTML::parrafo($textos->id('FAX'), 'negrilla margenSuperior');
    $pestana2 .= HTML::parrafo($objeto->persona->fax, '');

    $pestana3  = HTML::parrafo($textos->id('TIPO_USUARIO'), 'negrilla margenSuperior');
    $pestana3 .= HTML::parrafo($objeto->tipo, '');
    $pestana3 .= HTML::parrafo($textos->id('CORREO'), 'negrilla margenSuperior');
    $pestana3 .= HTML::parrafo($objeto->persona->correo, '');
    $pestana3 .= HTML::parrafo($textos->id('USUARIO'), 'negrilla margenSuperior');
    $pestana3 .= HTML::parrafo($objeto->usuario, '');
    $pestana3 .= HTML::parrafo($textos->id('MAXIMO_DESCUENTO_AUTORIZADO'), 'negrilla margenSuperior');
    $pestana3 .= HTML::parrafo($objeto->dctoMaximo.'%', '');
    $pestana3 .= HTML::parrafo($textos->id('PORCENTAJE_GANANCIA'), 'negrilla margenSuperior');
    $pestana3 .= HTML::parrafo($objeto->porcentajeGanancia.'%', '');    
    $pestana3 .= HTML::parrafo($textos->id('ESTADO'), 'negrilla margenSuperior');
    $activo    = ($objeto->activo) ?  HTML::frase($textos->id('ACTIVO'), 'activo') : HTML::frase($textos->id('INACTIVO'), 'inactivo');
    $pestana3 .= HTML::parrafo($activo, '', '');
    $pestana3 .= HTML::parrafo($textos->id('VENDEDOR')."?", 'negrilla margenSuperior');
    $vendedor  = ($objeto->vendedor) ?  HTML::frase($textos->id('SI'), 'activo') : HTML::frase($textos->id('NO'), 'inactivo');    
    $pestana3 .= HTML::parrafo($vendedor, '', '');
    $pestana3 .= HTML::parrafo($textos->id('NOTIFICACION_VTO_FC')."?", 'negrilla margenSuperior');
    $notifiFC  = ($objeto->notificacionVtoFC) ?  HTML::frase($textos->id('SI'), 'activo') : HTML::frase($textos->id('NO'), 'inactivo');    
    $pestana3 .= HTML::parrafo($notifiFC, '', '');    
    $pestana3 .= HTML::parrafo($textos->id('NOTIFICACION_VTO_FV')."?", 'negrilla margenSuperior');
    $notifiFV  = ($objeto->notificacionVtoFV) ?  HTML::frase($textos->id('SI'), 'activo') : HTML::frase($textos->id('NO'), 'inactivo');    
    $pestana3 .= HTML::parrafo($notifiFV, '', '');      

    $pestanas = array(
        HTML::frase($textos->id('INFORMACION_PERSONAL'), 'letraBlanca') => $pestana1,
        HTML::frase($textos->id('UBICACION'), 'letraBlanca')            => $pestana2,
        HTML::frase($textos->id('INFORMACION_USUARIO'), 'letraBlanca')  => $pestana3,
    );

    $codigo = HTML::pestanas2('', $pestanas);

    $respuesta['generar']       = true;
    $respuesta['titulo']        = HTML::parrafo($textos->id('CONSULTAR_ITEM'), 'letraBlanca negrilla subtitulo');
    $respuesta['destino']       = '#cuadroDialogo';
    $respuesta['ancho']         = 780;
    $respuesta['alto']          = 530;
    $respuesta['codigo']        = $codigo;

    Servidor::enviarJSON($respuesta);
    
}

/**
 *
 * @global type $textos
 * @global type $sql
 * @global type $sesion_usuarioSesion
 * @param type $datos 
 */
function adicionarItem($datos = array()) {
    global $textos, $sql, $sesion_usuarioSesion;

    $objeto = new Usuario();
    $destino = '/ajax' . $objeto->urlBase . '/add';
    $respuesta = array();

    if (empty($datos)) {

        $pestana1  = HTML::campoOculto('procesar', 'true');
        $pestana1 .= HTML::campoOculto('datos[dialogo]', '', 'idDialogo');
        //seleccionar los tipos de documentos
        $tiposDocumentos = $sql->seleccionar(array('tipos_documento'), array('id', 'nombre'), 'id NOT IN (0)', 'id', 'nombre ASC');
        
        $tiposDoc = array();
        
        while ($tiposDocumento = $sql->filaEnObjeto($tiposDocumentos)) {
            $tiposDoc[$tiposDocumento->id] = $tiposDocumento->nombre;
            
        }
        
        $listaDesplegable = HTML::listaDesplegable('datos[id_tipo_documento]', $tiposDoc, '', 'campoObligatorio');
        //seleccionar los tipos de usuarios
        $tiposUsuarios = $sql->seleccionar(array('perfiles'), array('id', 'nombre'), 'id NOT IN (0)', 'id', 'nombre ASC');
        
        while ($tiposUsuario = $sql->filaEnObjeto($tiposUsuarios)) {
            $tiposUser[$tiposUsuario->id] = $tiposUsuario->nombre;
            
        }

        $pestana1a  = HTML::parrafo($textos->id('TIPO_DOCUMENTO'), 'negrilla margenSuperior');
        $pestana1a .= $listaDesplegable;
        $pestana1b = HTML::parrafo($textos->id('DOCUMENTO'), 'negrilla margenSuperior');
        $pestana1b .= HTML::campoTexto('datos[documento_identidad]', 25, 50, '', 'autocompletable campoObligatorio', 'campoDocumentoPersona', array('title' => HTML::urlInterna('INICIO', 0, true, 'listarPersonas')), $textos->id('AYUDA_USO_AUTOCOMPLETAR')) . HTML::frase('Persona ya registrada.', 'margenIzquierda letraVerde oculto', 'frasePersonaRegistrada');
        $pestana1a .= HTML::parrafo($textos->id('CIUDAD_EXPEDICION_DOCUMENTO'), 'negrilla margenSuperior');
        $pestana1a .= HTML::campoTexto('datos[id_ciudad_documento]', 30, 255, '', 'autocompletable campoObligatorio', 'campoCiudadDocumentoPersona', array('title' => '/ajax/ciudades/listarCiudades'), $textos->id('SELECCIONE_CIUDAD_EXPEDICION_DOCUMENTO'), '/ajax/ciudades/add');
        $pestana1b .= HTML::parrafo($textos->id('GENERO'), 'negrilla margenSuperior');
        $pestana1b .= HTML::listaDesplegable('datos[genero]', array('M' => $textos->id('GENERO_M'), 'F' => $textos->id('GENERO_F')), '', '', 'campoGeneroPersona', '', array('ayuda' => $textos->id('SELECCIONE_GENERO')));

        $pestana1a .= HTML::parrafo($textos->id('PRIMER_NOMBRE'), 'negrilla margenSuperior');
        $pestana1a .= HTML::campoTexto('datos[primer_nombre]', 30, 50, '', 'campoObligatorio', 'campoPrimerNombrePersona', array('title' => $textos->id('')));
        $pestana1b .= HTML::parrafo($textos->id('SEGUNDO_NOMBRE'), 'negrilla margenSuperior');
        $pestana1b .= HTML::campoTexto('datos[segundo_nombre]', 30, 50, '', '', 'campoSegundoNombrePersona', array('title' => $textos->id('')));
        $pestana1a .= HTML::parrafo($textos->id('PRIMER_APELLIDO'), 'negrilla margenSuperior');
        $pestana1a .= HTML::campoTexto('datos[primer_apellido]', 30, 50, '', 'campoObligatorio', 'campoPrimerApellidoPersona');
        $pestana1b .= HTML::parrafo($textos->id('SEGUNDO_APELLIDO'), 'negrilla margenSuperior');
        $pestana1b .= HTML::campoTexto('datos[segundo_apellido]', 30, 50, '', '', 'campoSegundoApellidoPersona');
        $pestana1a .= HTML::parrafo($textos->id('TIPO_USUARIO'), 'negrilla margenSuperior');
        $pestana1a .= HTML::listaDesplegable('datos[id_tipo]', $tiposUser, '', '', '', '', array('alt' => $textos->id('SELECCIONE_TIPO_USUARIO')));

        $pestana1a .= HTML::parrafo($textos->id('OBSERVACIONES'), 'negrilla margenSuperior');
        $pestana1a .= HTML::areaTexto('datos[observaciones]', 5, 40, '', '', '');
        $pestana1b .= HTML::parrafo($textos->id('FECHA_NACIMIENTO'), 'negrilla margenSuperior');
        $pestana1b .= HTML::campoTexto('datos[fecha_nacimiento]', 12, 12, '', 'fechaAntigua', 'campoFechaNacimientoPersona', array('ayuda' => $textos->id('SELECCIONE_FECHA_NACIMIENTO')));
        $pestana1b .= HTML::parrafo($textos->id('FOTOGRAFIA_USUARIO'), 'negrilla margenSuperior');
        $pestana1b .= HTML::campoArchivo('imagen', 40, 255, '');

        $contenedor1 = HTML::contenedor($pestana1a, 'contenedorIzquierdo');
        $contenedor2 = HTML::contenedor($pestana1b, 'contenedorDerecho');

        $pestana1 .= HTML::contenedor($contenedor1 . $contenedor2, 'pestana1');

        $pestana2 = HTML::parrafo($textos->id('CIUDAD_RESIDENCIA'), 'negrilla margenSuperior');
        $pestana2 .= HTML::campoTexto('datos[id_ciudad_residencia]', 50, 255, '', 'autocompletable campoObligatorio', 'campoCiudadResidenciaPersona', array('title' => '/ajax/ciudades/listarCiudades'), $textos->id('SELECCIONE_CIUDAD_RESIDENCIA'), '/ajax/ciudades/add');
        $pestana2 .= HTML::parrafo($textos->id('DIRECCION'), 'negrilla margenSuperior');
        $pestana2 .= HTML::campoTexto('datos[direccion]', 30, 50, '', 'campoObligatorio', 'campoDireccionPersona', array('title' => $textos->id('')));
        $pestana2 .= HTML::parrafo($textos->id('TELEFONO'), 'negrilla margenSuperior');
        $pestana2 .= HTML::campoTexto('datos[telefono]', 30, 50, '', 'campoObligatorio', 'campoTelefonoPersona', array('title' => $textos->id('')));
        $pestana2 .= HTML::parrafo($textos->id('CELULAR'), 'negrilla margenSuperior');
        $pestana2 .= HTML::campoTexto('datos[celular]', 30, 50, '', '', 'campoCelularPersona', array('title' => $textos->id('')));
        $pestana2 .= HTML::parrafo($textos->id('FAX'), 'negrilla margenSuperior');
        $pestana2 .= HTML::campoTexto('datos[fax]', 30, 50, '', '', 'campoFaxPersona', array('title' => $textos->id('')));

        $pestana3 = HTML::parrafo($textos->id('CORREO'), 'negrilla margenSuperior');
        $pestana3 .= HTML::campoTexto('datos[correo]', 30, 50, '', '', 'campoCorreoPersona');
        $pestana3 .= HTML::parrafo($textos->id('USUARIO'), 'negrilla margenSuperior');
        $pestana3 .= HTML::campoTexto('datos[usuario]', 20, 30, '', 'campoObligatorio', 'campoAgregarUsuario') . HTML::frase('Ya hay alguien registrado con ese usuario', 'oculto textoError medioMargenIzquierda', 'textoExisteUsuario');
        $pestana3 .= HTML::parrafo($textos->id('CONTRASENA'), 'negrilla margenSuperior');
        $pestana3 .= HTML::campoClave('datos[contrasena1]', 10, 12, '', 'campoObligatorio', 'contrasena1', array('ayuda' => $textos->id('INGRESE_SU_CONTRASENA')));
        $pestana3 .= HTML::parrafo($textos->id('CONFIRMAR_CONTRASENA'), 'negrilla margenSuperior');
        $pestana3 .= HTML::campoClave('datos[contrasena2]', 10, 12, '', 'campoObligatorio', 'contrasena1', array('ayuda' => $textos->id('REPITA_CONTRASENA')));
        
        $pestana3 .= HTML::parrafo($textos->id('MAXIMO_DESCUENTO_AUTORIZADO'), 'negrilla margenSuperior');
        $pestana3 .= HTML::campoTexto('datos[dcto_maximo]', 3, 2, '', 'campoObligatorio rangoNumeros', '', array('rango' => '1-99'), $textos->id('AYUDA_MAXIMO_DESCUENTO_AUTORIZADO'));        
        $pestana3 .= HTML::parrafo($textos->id('PORCENTAJE_GANANCIA'), 'negrilla margenSuperior');
        $pestana3 .= HTML::campoTexto('datos[porcentaje_ganancia]', 3, 2, '', 'campoObligatorio rangoNumeros', '', array('rango' => '1-99'), $textos->id('AYUDA_PORCENTAJE_GANANCIA'));        

        //Codigo para mostrar la lista desplegable que muestra los perfiles de usuario para poder modificarlo//
        if (isset($sesion_usuarioSesion) && ($sesion_usuarioSesion->idTipo == 0 )) {
            $pestana3 .= HTML::parrafo(HTML::campoChequeo('datos[activo]', '') . $textos->id('ACTIVO'), 'margenSuperior');
            $pestana3 .= HTML::parrafo(HTML::campoChequeo('datos[vendedor]', '') . $textos->id('VENDEDOR').'?', 'margenSuperior');
            $pestana3 .= HTML::parrafo(HTML::campoChequeo('datos[notificacion_vto_fc]', '') . $textos->id('NOTIFICACION_VTO_FC').'?', 'margenSuperior');
            $pestana3 .= HTML::parrafo(HTML::campoChequeo('datos[notificacion_vto_fv]', '') . $textos->id('NOTIFICACION_VTO_FV').'?', 'margenSuperior');
            
        }//Fin del if(es el admin?)

        $pestanas = array(
            HTML::frase($textos->id('INFORMACION_PERSONAL'), 'letraBlanca') => $pestana1,
            HTML::frase($textos->id('UBICACION'), 'letraBlanca')            => $pestana2,
            HTML::frase($textos->id('INFORMACION_USUARIO'), 'letraBlanca')  => $pestana3,
        );

        $codigo .= HTML::pestanas2('', $pestanas);

        $codigo .= HTML::parrafo(HTML::boton('chequeo', $textos->id('ACEPTAR'), 'directo', '', 'botonFormaEditarUsuarios', '', '') . HTML::frase('     ' . $textos->id('REGISTRO_AGREGADO'), 'textoExitoso', 'textoExitoso'), 'margenSuperior');
        $codigo = HTML::forma($destino, $codigo, 'P', true, 'formaEditarUsuario', '', 'formaEditarUsuario');

        $respuesta['generar'] = true;

        $respuesta['titulo']    = HTML::parrafo($textos->id('ADICIONAR_ITEM'), 'letraBlanca negrilla subtitulo');
        $respuesta['destino']   = '#cuadroDialogo';
        $respuesta['ancho']     = 780;
        $respuesta['alto']      = 530;
        $respuesta['codigo']    = $codigo;
        
    } else {

        $respuesta['error'] = true;

        $existeUsuario              = $sql->existeItem('usuarios', 'usuario', $datos['usuario']);
        $existeCiudadDocumento      = $sql->existeItem('lista_ciudades', 'cadena', $datos['id_ciudad_documento']);
        $existeCiudadResidencia     = $sql->existeItem('lista_ciudades', 'cadena', $datos['id_ciudad_residencia']);        

        if (empty($datos['documento_identidad'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_DOCUMENTO');
            
        } elseif (empty($datos['id_ciudad_documento'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_CIUDAD_DOCUMENTO');
            
        } elseif (empty($datos['primer_nombre'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_NOMBRE');
            
        } elseif (!$existeCiudadDocumento) {
            $respuesta['mensaje'] = $textos->id('ERROR_DATO_CIUDAD_DOCUMENTO');
            
        } elseif (empty($datos['primer_apellido'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_APELLIDO');
            
        } elseif (empty($datos['primer_nombre'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_NOMBRE');
            
        } elseif (empty($datos['id_ciudad_residencia'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_CIUDAD_RESIDENCIA');
            
        }  elseif (!$existeCiudadResidencia) {
            $respuesta['mensaje'] = $textos->id('ERROR_DATO_CIUDAD_RESIDENCIA');
            
        } elseif (empty($datos['direccion'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_DIRECCION');
            
        } elseif (empty($datos['telefono']) && empty($datos['celular'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_TELEFONOS');
            
        } elseif (empty($datos['usuario'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_USUARIO');
            
        } elseif ($existeUsuario) {
            $respuesta['mensaje'] = $textos->id('ERROR_USUARIO_EXISTENTE');
            
        } elseif (strlen($datos['usuario']) < 4) {
            $respuesta['mensaje'] = $textos->id('ERROR_USUARIO_CORTO');
            
        } elseif (empty($datos['contrasena1'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_CONTRASENA_REQUERIDA');
            
        } elseif (empty($datos['contrasena2'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_CONTRASENA2_REQUERIDA');
            
        } elseif ($datos['contrasena1'] != $datos['contrasena2']) {
            $respuesta['mensaje'] = $textos->id('ERROR_CONTRASENAS_DIFERENTES');
            
        } else {
            $idItem = $objeto->adicionar($datos);
            
            if ($idItem) {
                /**************** Creo el nuevo item que se insertara via ajax ****************/
                $objeto = new Usuario($idItem);

                if ($objeto->activo) {
                    $estado = HTML::frase($textos->id('ACTIVO'), 'activo');
                    
                } else {
                    $estado = HTML::frase($textos->id('INACTIVO'), 'inactivo');
                    
                }
                
                $celdas         = array($objeto->tipo, $objeto->persona->primerNombre, $objeto->persona->primerApellido, $objeto->usuario, $estado);
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
 *
 * @global type $textos
 * @global type $sql
 * @global type $sesion_ipUsuario
 * @param type $usuario
 * @param type $contrasena
 * @param type $datos 
 */
function validarUsuario($usuario, $contrasena, $datos) {
    global $textos;

    $respuesta = array();
    $respuesta['error'] = true;
    $respuesta['accion'] = 'insertar';


    if (empty($usuario)) {
        $respuesta['mensaje'] = $textos->id('ERROR_USUARIO_REQUERIDO');
    } elseif (empty($contrasena)) {
        $respuesta['mensaje'] = $textos->id('ERROR_CONTRASENA_REQUERIDA');
    } else {
        $idExistente = Usuario::validar($usuario, $contrasena);

        if (is_null($idExistente)) {
            $respuesta['mensaje'] = $textos->id('ERROR_USUARIO_INVALIDO');

        } else {
            $usuarioSesion = new Usuario($usuario);
            $usuarioSesion->setSede($datos['sede']);
            Sesion::registrar('usuarioSesion', $usuarioSesion);
            
            //en caso que se determine la configuracion por sede, aqui habria que pasarle el id de la sede
            $configuracionGlobal = new Configuracion('1');
            Sesion::registrar('configuracionGlobal', $configuracionGlobal);            

            $respuesta['error']   = NULL;
            $respuesta['accion']  = 'redireccionar';
            $respuesta['destino'] = $usuarioSesion->url;
        }
    }

    Servidor::enviarJSON($respuesta);
}


/**
 *
 * @global type $textos
 * @global type $sql
 * @global type $sesion_usuarioSesion
 * @param type $id
 * @param type $datos 
 */
function modificarItem($id, $datos = array()) {
    global $textos, $sql, $sesion_usuarioSesion;

    $objeto     = new Usuario($id);
    $destino    = '/ajax' . $objeto->urlBase . '/edit';
    $respuesta  = array();

    if (empty($datos)) {

        $pestana1 = HTML::campoOculto('procesar', 'true');
        $pestana1 .= HTML::campoOculto('id', $id);
        $pestana1 .= HTML::campoOculto('datos[dialogo]', '', 'idDialogo');

        //seleccionar los tipos de documentos
        $tiposDocumentos = $sql->seleccionar(array('tipos_documento'), array('id', 'nombre'), 'id NOT IN (0)', 'id', 'nombre ASC');
        $tiposDoc = array();
        
        while ($tiposDocumento = $sql->filaEnObjeto($tiposDocumentos)) {
            $tiposDoc[$tiposDocumento->id] = $tiposDocumento->nombre;
        }
        
        $listaDesplegable = HTML::listaDesplegable('datos[id_tipo_documento]', $tiposDoc, $objeto->persona->idTipoDocumento, 'campoObligatorio');
        //seleccionar los tipos de usuarios
        $tiposUsuarios = $sql->seleccionar(array('perfiles'), array('id', 'nombre'), 'id NOT IN (0)', 'id', 'nombre ASC');
        
        while ($tiposUsuario = $sql->filaEnObjeto($tiposUsuarios)) {
            $tiposUser[$tiposUsuario->id] = $tiposUsuario->nombre;
        }

        $ciudadDocumento = $sql->obtenerValor('lista_ciudades', 'cadena', 'id = "' . $objeto->persona->idCiudadDocumento . '"');
        $ciudadResidencia = $objeto->persona->ciudadResidencia . ', ' . $objeto->persona->estadoResidencia . ', ' . $objeto->persona->paisResidencia;

        $pestana1a  = HTML::parrafo($textos->id('TIPO_DOCUMENTO'), 'negrilla margenSuperior');
        $pestana1a .= $listaDesplegable;
        $pestana1b  = HTML::parrafo($textos->id('DOCUMENTO'), 'negrilla margenSuperior');
        $pestana1b .= HTML::campoTexto('datos[documento_identidad]', 25, 50, $objeto->persona->documentoIdentidad, 'autocompletable campoObligatorio', 'campoDocumentoPersona', array('title' => HTML::urlInterna('INICIO', 0, true, 'listarPersonas')), $textos->id('AYUDA_USO_AUTOCOMPLETAR')) . HTML::frase('Persona ya registrada.', 'margenIzquierda letraVerde oculto', 'frasePersonaRegistrada');
        $pestana1a .= HTML::parrafo($textos->id('CIUDAD_EXPEDICION_DOCUMENTO'), 'negrilla margenSuperior');
        $pestana1a .= HTML::campoTexto('datos[id_ciudad_documento]', 30, 255, $ciudadDocumento, 'autocompletable campoObligatorio', 'campoCiudadDocumentoPersona', array('title' => '/ajax/ciudades/listarCiudades'), $textos->id('SELECCIONE_CIUDAD_EXPEDICION_DOCUMENTO'), '/ajax/ciudades/add');
        $pestana1b .= HTML::parrafo($textos->id('GENERO'), 'negrilla margenSuperior');
        $pestana1b .= HTML::listaDesplegable('datos[genero]', array('M' => $textos->id('GENERO_M'), 'F' => $textos->id('GENERO_F')), $objeto->persona->genero, '', '', '', array('alt' => $textos->id('SELECCIONE_GENERO')));

        $pestana1a .= HTML::parrafo($textos->id('PRIMER_NOMBRE'), 'negrilla margenSuperior');
        $pestana1a .= HTML::campoTexto('datos[primer_nombre]', 30, 50, $objeto->persona->primerNombre, 'campoObligatorio', 'campoPrimerNombrePersona', array('title' => $textos->id('')));
        $pestana1b .= HTML::parrafo($textos->id('SEGUNDO_NOMBRE'), 'negrilla margenSuperior');
        $pestana1b .= HTML::campoTexto('datos[segundo_nombre]', 30, 50, $objeto->persona->segundoNombre, '', 'campoSegundoNombrePersona', array('title' => $textos->id('')));
        $pestana1a .= HTML::parrafo($textos->id('PRIMER_APELLIDO'), 'negrilla margenSuperior');
        $pestana1a .= HTML::campoTexto('datos[primer_apellido]', 30, 50, $objeto->persona->primerApellido, 'campoObligatorio', 'campoPrimerApellidoPersona');
        $pestana1b .= HTML::parrafo($textos->id('SEGUNDO_APELLIDO'), 'negrilla margenSuperior');
        $pestana1b .= HTML::campoTexto('datos[segundo_apellido]', 30, 50, $objeto->persona->segundoApellido, '', 'campoSegundoApellidoPersona');
        $pestana1a .= HTML::parrafo($textos->id('TIPO_USUARIO'), 'negrilla margenSuperior');
        $pestana1a .= HTML::listaDesplegable('datos[id_tipo]', $tiposUser, $objeto->idTipo, '', '', '', array('alt' => $textos->id('SELECCIONE_TIPO_USUARIO')));

        $pestana1a .= HTML::parrafo($textos->id('OBSERVACIONES'), 'negrilla margenSuperior');
        $pestana1a .= HTML::areaTexto('datos[observaciones]', 5, 40, $objeto->observaciones);
        $pestana1b .= HTML::parrafo($textos->id('FECHA_NACIMIENTO'), 'negrilla margenSuperior');
        $pestana1b .= HTML::campoTexto('datos[fecha_nacimiento]', 12, 12, $objeto->persona->fechaNacimiento, 'fechaAntigua', 'campoFechaNacimientoPersona', array('alt' => $textos->id('SELECCIONE_FECHA_NACIMIENTO')));
        $pestana1b .= HTML::parrafo($textos->id('FOTOGRAFIA_USUARIO'), 'negrilla margenSuperior');
        $pestana1b .= HTML::campoArchivo('imagen', 40, 255, $objeto->persona->descripcion);


        $contenedor1 = HTML::contenedor($pestana1a, 'contenedorIzquierdo');
        $contenedor2 = HTML::contenedor($pestana1b, 'contenedorDerecho');

        $pestana1 .= HTML::contenedor($contenedor1 . $contenedor2, 'pestana1');

        $pestana2 = HTML::parrafo($textos->id('CIUDAD_RESIDENCIA'), 'negrilla margenSuperior');
        $pestana2 .= HTML::campoTexto('datos[id_ciudad_residencia]', 50, 255, $ciudadResidencia, 'autocompletable campoObligatorio', 'campoCiudadResidenciaPersona', array('title' => '/ajax/ciudades/listarCiudades'), $textos->id('SELECCIONE_CIUDAD_RESIDENCIA'), '/ajax/ciudades/add');
        $pestana2 .= HTML::parrafo($textos->id('DIRECCION'), 'negrilla margenSuperior');
        $pestana2 .= HTML::campoTexto('datos[direccion]', 30, 50, $objeto->persona->direccion, 'campoObligatorio', 'campoDireccionPersona', array('title' => $textos->id('')));
        $pestana2 .= HTML::parrafo($textos->id('TELEFONO'), 'negrilla margenSuperior');
        $pestana2 .= HTML::campoTexto('datos[telefono]', 30, 50, $objeto->persona->telefono, 'campoObligatorio', 'campoTelefonoPersona', array('title' => $textos->id('')));
        $pestana2 .= HTML::parrafo($textos->id('CELULAR'), 'negrilla margenSuperior');
        $pestana2 .= HTML::campoTexto('datos[celular]', 30, 50, $objeto->persona->celular, '', 'campoCelularPersona', array('title' => $textos->id('')));
        $pestana2 .= HTML::parrafo($textos->id('FAX'), 'negrilla margenSuperior');
        $pestana2 .= HTML::campoTexto('datos[fax]', 30, 50, $objeto->persona->fax, '', 'campoFaxPersona', array('title' => $textos->id('')));


        $pestana3 = HTML::parrafo($textos->id('CORREO'), 'negrilla margenSuperior');
        $pestana3 .= HTML::campoTexto('datos[correo]', 30, 50, $objeto->persona->correo, '', 'campoCorreoPersona');
        $pestana3 .= HTML::parrafo($textos->id('USUARIO'), 'negrilla margenSuperior');
        $pestana3 .= HTML::campoTexto('datos[usuario]', 20, 30, $objeto->usuario, 'campoObligatorio', 'campoAgregarUsuario') . HTML::frase('Ya hay alguien registrado con ese usuario', 'oculto textoError medioMargenIzquierda', 'textoExisteUsuario');
        $pestana3 .= HTML::parrafo($textos->id('CONTRASENA'), 'negrilla margenSuperior');
        $pestana3 .= HTML::campoClave('datos[contrasena1]', 10, 12, '', 'campoObligatorio', 'contrasena1', array('alt' => $textos->id('INGRESE_SU_CONTRASENA')));
        $pestana3 .= HTML::parrafo($textos->id('CONFIRMAR_CONTRASENA'), 'negrilla margenSuperior');
        $pestana3 .= HTML::campoClave('datos[contrasena2]', 10, 12, '', 'campoObligatorio', 'contrasena1', array('alt' => $textos->id('REPITA_CONTRASENA')));
        $pestana3 .= HTML::parrafo($textos->id('MAXIMO_DESCUENTO_AUTORIZADO'), 'negrilla margenSuperior');
        $pestana3 .= HTML::campoTexto('datos[dcto_maximo]', 3, 2, $objeto->dctoMaximo, 'campoObligatorio rangoNumeros', '', array('alt' => $textos->id('AYUDA_MAXIMO_DESCUENTO_AUTORIZADO'), 'rango' => '1-99'));           
        $pestana3 .= HTML::parrafo($textos->id('PORCENTAJE_GANANCIA'), 'negrilla margenSuperior');
        $pestana3 .= HTML::campoTexto('datos[porcentaje_ganancia]', 3, 2, $objeto->porcentajeGanancia, 'campoObligatorio rangoNumeros', '', array('rango' => '1-99'), $textos->id('AYUDA_PORCENTAJE_GANANCIA'));        

        //Codigo para mostrar la lista desplegable que muestra los perfiles de usuario para poder modificarlo//
        if (isset($sesion_usuarioSesion) && ($sesion_usuarioSesion->idTipo == 0 )) {
            $pestana3 .= HTML::parrafo(HTML::campoChequeo('datos[activo]', $objeto->activo) . $textos->id('ACTIVO'), 'margenSuperior');
            $pestana3 .= HTML::parrafo(HTML::campoChequeo('datos[vendedor]', $objeto->vendedor) . $textos->id('VENDEDOR').'?', 'margenSuperior');
            $pestana3 .= HTML::parrafo(HTML::campoChequeo('datos[notificacion_vto_fc]', $objeto->notificacionVtoFC) . $textos->id('NOTIFICACION_VTO_FC').'?', 'margenSuperior');
            $pestana3 .= HTML::parrafo(HTML::campoChequeo('datos[notificacion_vto_fv]', $objeto->notificacionVtoFV) . $textos->id('NOTIFICACION_VTO_FV').'?', 'margenSuperior');

        }//Fin del if(es el admin?)

        $pestanas = array(
            HTML::frase($textos->id('INFORMACION_PERSONAL'), 'letraBlanca') => $pestana1,
            HTML::frase($textos->id('UBICACION'), 'letraBlanca')            => $pestana2,
            HTML::frase($textos->id('INFORMACION_USUARIO'), 'letraBlanca')  => $pestana3,
        );

        $codigo .= HTML::pestanas2('', $pestanas);

        $codigo .= HTML::parrafo(HTML::boton('chequeo', $textos->id('ACEPTAR'), 'directo', '', 'botonFormaEditarUsuarios', '', '') . HTML::frase('     ' . $textos->id('REGISTRO_AGREGADO'), 'textoExitoso', 'textoExitoso'), 'margenSuperior');
        $codigo = HTML::forma($destino, $codigo, 'P', true, 'formaEditarUsuario', '', 'formaEditarUsuario');

        $respuesta['generar']   = true;
        $respuesta['titulo']    = HTML::parrafo($textos->id('MODIFICAR_ITEM'), 'letraBlanca negrilla subtitulo');
        $respuesta['destino']   = '#cuadroDialogo';
        $respuesta['ancho']     = 780;
        $respuesta['alto']      = 530;
        $respuesta['codigo']    = $codigo;

    } else {
        $respuesta['error'] = true;

        $existeUsuario = $sql->existeItem('usuarios', 'usuario', $datos['usuario'], 'id != "' . $id . '"');
        
        $existeCiudadDocumento      = $sql->existeItem('lista_ciudades', 'cadena', $datos['id_ciudad_documento']);
        $existeCiudadResidencia     = $sql->existeItem('lista_ciudades', 'cadena', $datos['id_ciudad_residencia']);           

        if (empty($datos['documento_identidad'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_DOCUMENTO');
            
        } elseif (empty($datos['id_ciudad_documento'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_CIUDAD_DOCUMENTO');
            
        } elseif (!$existeCiudadDocumento) {
            $respuesta['mensaje'] = $textos->id('ERROR_DATO_CIUDAD_DOCUMENTO');
            
        } elseif (empty($datos['primer_nombre'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_NOMBRE');
            
        } elseif (empty($datos['primer_apellido'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_APELLIDO');
            
        } elseif (empty($datos['primer_nombre'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_NOMBRE');
            
        } elseif (empty($datos['id_ciudad_residencia'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_CIUDAD_RESIDENCIA');
            
        } elseif (!$existeCiudadResidencia) {
            $respuesta['mensaje'] = $textos->id('ERROR_DATO_CIUDAD_RESIDENCIA');
            
        } elseif (empty($datos['direccion'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_DIRECCION');
            
        } elseif (empty($datos['telefono']) && empty($datos['celular'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_TELEFONOS');
            
        } elseif (empty($datos['usuario'])) {
            $respuesta['mensaje'] = $textos->id('ERROR_FALTA_USUARIO');
            
        } elseif ($existeUsuario) {
            $respuesta['mensaje'] = $textos->id('ERROR_USUARIO_EXISTENTE');
            
        } elseif (strlen($datos['usuario']) < 4) {
            $respuesta['mensaje'] = $textos->id('ERROR_USUARIO_CORTO');
            
        } elseif (!empty($datos['contrasena1']) && !empty($datos['contrasena2']) && $datos['contrasena1'] != $datos['contrasena2']) {
            $respuesta['mensaje'] = $textos->id('ERROR_CONTRASENAS_DIFERENTES');
            
        } else {
            $modificar = $objeto->modificar($datos);
            
            if ($modificar) {
                /*                 * ************** Creo el nuevo item que se insertara via ajax *************** */
                $objeto = new Usuario($id);

                $estado =  ($objeto->activo) ? HTML::frase($textos->id('ACTIVO'), 'activo') : HTML::frase($textos->id('INACTIVO'), 'inactivo');
               
                $celdas = array($objeto->tipo, $objeto->persona->primerNombre, $objeto->persona->primerApellido, $objeto->usuario, $estado);
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
 * Función con doble comportamiento. La primera llamada (con el parametro $confirmado vacio)
 * muestra el formulario de confirmación de eliminación del registro. El destino de este formulario es esta 
 * misma función, pero una vez viene desde el formulario con el parametro $confirmado en "true"
 * se encarga de validar la información y llamar al metodo modificar estado del objeto.
 * 
 * nota: no se debe eliminar ningun usuario del sistema, solo inactivar.
 * 
 * @global recurso $textos  = objeto global de gestion de los textos de idioma
 * @global recurso $sql     = objeto global de interaccion con la BD
 * @param int $id           = id del registro a modificar
 * @param array $datos      = arreglo con la informacion a adicionar
 */
function eliminarItem($id, $confirmado, $dialogo) {
    global $textos;

    $objeto     = new Usuario($id);
    $destino    = '/ajax' . $objeto->urlBase . '/delete';
    $respuesta  = array();

    if (!$confirmado) {
        $titulo  = HTML::frase($objeto->persona->nombreCompleto, 'negrilla');
        $titulo1 = str_replace('%1', $titulo, $textos->id('CONFIRMAR_INACTIVACION'));
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

        if ($objeto->inactivar()) {
               /*                 * ************** Creo el nuevo item que se insertara via ajax *************** */
                $objeto     = new Usuario($id);

                $estado     = ($objeto->activo) ? HTML::frase($textos->id('ACTIVO'), 'activo') : HTML::frase($textos->id('INACTIVO'), 'inactivo');

                $celdas         = array($objeto->tipo, $objeto->persona->primerNombre, $objeto->persona->primerApellido, $objeto->usuario, $estado);
                $celdas1    = HTML::crearFilaAModificar($celdas);

                $respuesta['error']         = false;
                $respuesta['accion']        = 'insertar';
                $respuesta['contenido']     = $celdas1;
                $respuesta['idContenedor']  = '#tr_' . $id;
                $respuesta['idDestino']     = '#tr_' . $id;

                if ($dialogo == '') {
                    $respuesta['modificarFilaTabla'] = true;
                    
                } else {
                    $respuesta['modificarFilaDialogo']  = true;
                    $respuesta['ventanaDialogo']        = $dialogo;
                }
            
        } else {
            $respuesta['mensaje'] = $textos->id('ERROR_DESCONOCIDO');
            
        }
        
    }

    Servidor::enviarJSON($respuesta);
    
}

/**
 * Funcion que termina la sesion de un usuario
 */
function cerrarSesion() {
    Sesion::terminar();
    
    $respuesta = array();
    
    $respuesta['error'] = NULL;
    $respuesta['accion'] = 'redireccionar';
    $respuesta['destino'] = '/';
    Servidor::enviarJSON($respuesta);
}

/**
 *
 * @global type $sql
 * @param type $usuario
 * @return null 
 */
function verificarUsuario($usuario) {
    global $sql;

    if (empty($usuario)) {
        return NULL;
    }

    $existe = $sql->existeItem('usuarios', 'usuario', $usuario);

    if ($existe) {
        $existeUsuario = true;
    } else {
        $existeUsuario = false;
    }
    $respuesta = array();
    $respuesta['accion'] = 'verificarUsuario';
    $respuesta['existeUsuario'] = $existeUsuario;

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
        $objeto = new Usuario();
        $registros = $configuracion['GENERAL']['registrosPorPagina'];
        if (!empty($cantidadRegistros)) {
            $registros = (int) $cantidadRegistros;
        }         
        $pagina = 1;
        $registroInicial = 0;


        $palabras = explode(' ', $datos);

        $condicionales = $data[1];

        if ($condicionales == '') {
            $condicion = '(p.primer_nombre REGEXP "(' . implode('|', $palabras) . ')")';
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

        $arregloItems = $objeto->listar($registroInicial, $registros, array('0'), $condicion, 'p.primer_nombre');

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
    $objeto = new Usuario();

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
            $consultaGlobal = '(p.primer_nombre REGEXP "(' . implode('|', $palabras) . ')" OR u.usuario REGEXP "(' . implode('|', $palabras) . ')")';
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
    $consulta = $sql->seleccionar(array('usuarios'), array('id', 'usuario', 'dcto_maximo'), '(usuario LIKE "%' . $cadena . '%" OR id LIKE "%' . $cadena . '%") AND activo = "1" AND id NOT IN(0)', '', 'usuario ASC', 0, 20);

    while ($fila = $sql->filaEnObjeto($consulta)) {
        $respuesta1 = array();
        $respuesta1["label"]            = $fila->id . ' :: ' . $fila->usuario;
        $respuesta1["value"]            = $fila->id;
        $respuesta1["nombre"]           = $fila->usuario;
        $respuesta1["dcto_maximo"]      = $fila->dcto_maximo;
        
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
    global $textos;

    $destino = '/ajax/usuarios/eliminarVarios';
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
            $objeto = new Usuario($val);
            $eliminarVarios = $objeto->eliminar();
        }

        if ($eliminarVarios) {

            $respuesta['error'] = false;
            $respuesta['textoExito'] = true;
            $respuesta['mensaje'] = $textos->id('ITEMS_ELIMINADOS_CORRECTAMENTE');
            $respuesta['accion'] = 'recargar';
            
        } else {
            $respuesta['mensaje'] = $textos->id('ERROR_DESCONOCIDO');
            
        }
        
    }

    Servidor::enviarJSON($respuesta);
}
