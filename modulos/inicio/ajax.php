<?php

/**
 *
 * @package     FOM
 * @subpackage  Inicio
 * @author      Pablo Andrés Vélez Vidal <pavelez8@misena.edu.co>
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2012 Genesys Corporation
 * @version     0.2
 * 
 * Modificado el 20-01-12
 *
 * */
global $url_accion, $forma_procesar, $forma_id, $forma_datos, $url_cadena, $url_cadena, $forma_tema, $forma_columna, $forma_valor, $forma_tabla;

if (isset($url_accion)) {
    switch ($url_accion) {

        case "borrarNotificacion" : ($forma_procesar) ? $confirmado = true : $confirmado = false;
            eliminarNotificacion($forma_id, $confirmado);
            break;
        case "addImage" : ($forma_procesar) ? $datos = $forma_datos : $datos = array();
            adicionarImagen($datos);
            break;
        case "deleteImage" : ($forma_procesar) ? $confirmado = true : $confirmado = false;
            eliminarImagen($forma_id, $confirmado);
            break;
        case "listUsers" : listarUsuarios($url_cadena);
            break;
        case "listCities" : listarCiudades($url_cadena);
            break;
        case "cambiarApariencia" : cambiarApariencia($forma_tema);
            break;
        case "verificar" : verificarItem($forma_tabla, $forma_columna, $forma_valor);
            break;
        case "verificarPersona" : verificarPersona($forma_datos);
            break;
        case "listarPersonas" : listarPersonas($url_cadena);
            break;
        case "buscarModulo" : buscarModulo();
            break;
        case "listarModulos" : listarModulos($url_cadena);
            break;
        case 'cargarFechasEventos' : cargarFechasEventos($url_start, $url_end);
            break;
    }
}

/**
 *
 * @global type $textos
 * @global type $configuracion
 * @global type $archivo_recurso
 * @global type $forma_idRegistro
 * @global type $forma_modulo
 * @param type $datos 
 */
function adicionarImagen($datos = array()) {
    global $textos, $configuracion, $archivo_recurso, $forma_idRegistro, $forma_modulo;

    $moduloInicio = new Modulo("INICIO");
    $destino = "/ajax/" . $moduloInicio->url . "/addImage";
    $respuesta = array();

    if (empty($datos)) {
        $codigo = HTML::campoOculto("procesar", "true");
        //$codigo .= HTML::campoOculto("datos[idModulo]", $forma_idModulo);
        $codigo .= HTML::campoOculto("datos[modulo]", $forma_modulo);
        $codigo .= HTML::campoOculto("datos[idRegistro]", $forma_idRegistro);
        $codigo .= HTML::parrafo($textos->id("TITULO"), "negrilla margenSuperior");
        $codigo .= HTML::campoTexto("datos[titulo]", 50, 255);
        $codigo .= HTML::parrafo($textos->id("DESCRIPCION"), "negrilla margenSuperior");
        $codigo .= HTML::campoTexto("datos[descripcion]", 50, 255);
        $codigo .= HTML::parrafo($textos->id("ARCHIVO"), "negrilla margenSuperior");
        $codigo .= HTML::campoArchivo("recurso", 50, 255);
        $codigo .= HTML::parrafo(HTML::boton("chequeo", $textos->id("ACEPTAR"), "botonOk", "botonOk"), "margenSuperior");
        $codigo .= HTML::parrafo($textos->id("REGISTRO_AGREGADO"), "textoExitoso", "textoExitoso");
        $codigo = HTML::forma($destino, $codigo, "P", true);

        $respuesta["generar"] = true;
        $respuesta["codigo"] = $codigo;
        $respuesta["titulo"] = HTML::contenedor(HTML::frase(HTML::parrafo($textos->id("ADICIONAR_IMAGEN"), "letraNegra negrilla"), "bloqueTitulo-IS"), "encabezadoBloque-IS");
        $respuesta["destino"] = "#cuadroDialogo";
        $respuesta["ancho"] = 350;
        $respuesta["alto"] = 270;
    } else {
        $respuesta["error"] = true;

        if (!empty($archivo_recurso["tmp_name"])) {
            $validarFormato = Archivo::validarArchivo($archivo_recurso, array("jpg", "png", "gif", "jpeg"));
//            $area  = getimagesize($archivo_recurso["tmp_name"]);
        }

        if (empty($datos["titulo"])) {
            $respuesta["mensaje"] = $textos->id("ERROR_FALTA_TITULO");
        } elseif (empty($datos["descripcion"])) {
            $respuesta["mensaje"] = $textos->id("ERROR_FALTA_DESCRIPCION");
        } elseif (empty($archivo_recurso["tmp_name"])) {
            $respuesta["mensaje"] = $textos->id("ERROR_FALTA_ARCHIVO");
        } elseif ($archivo_recurso["size"] > $configuracion["DIMENSIONES"]["maximoPesoArchivo"]) {
            $respuesta["mensaje"] = $textos->id("ERROR_PESO_ARCHIVO");
        } elseif ($validarFormato) {
            $respuesta["mensaje"] = $textos->id("ERROR_FORMATO_IMAGEN");
        } else {
            $recurso = new Imagen();
            $idImagen = $recurso->adicionar($datos);
            if ($idImagen) {

                $respuesta["error"] = false;
                $respuesta["accion"] = "recargar";
            } else {
                $respuesta["mensaje"] = $textos->id("ERROR_DESCONOCIDO");
            }
        }
    }

    Servidor::enviarJSON($respuesta);
}

/**
 *
 * @global type $textos
 * @global type $sql
 * @param type $id
 * @param type $confirmado 
 */
function eliminarImagen($id, $confirmado) {
    global $textos;

    $imagen = new Imagen($id);
    $moduloInicio = new Modulo("INICIO");
    $destino = "/ajax/" . $moduloInicio->url . "/deleteImage";
    $respuesta = array();

    if (!$confirmado) {
        $nombre = HTML::frase($imagen->descripcion, "negrilla");
        $nombre = preg_replace("/\%1/", $nombre, $textos->id("CONFIRMAR_ELIMINACION_IMAGEN"));
        $codigo = HTML::campoOculto("procesar", "true");
        $codigo .= HTML::campoOculto("id", $id);
        $codigo .= HTML::parrafo($nombre);
        $codigo .= HTML::parrafo(HTML::boton("chequeo", $textos->id("ACEPTAR"), "botonOk", "botonOk", "botonOk"), "margenSuperior");
        $codigo .= HTML::parrafo($textos->id("REGISTRO_ELIMINADO"), "textoExitoso", "textoExitoso");
        $codigo = HTML::forma($destino, $codigo);

        $respuesta["generar"] = true;
        $respuesta["codigo"] = $codigo;
        $respuesta["destino"] = "#cuadroDialogo";
        $respuesta["titulo"] = HTML::contenedor(HTML::frase(HTML::parrafo($textos->id("ELIMINAR_IMAGEN"), "letraNegra negrilla"), "bloqueTitulo-IS"), "encabezadoBloque-IS");
        $respuesta["ancho"] = 350;
        $respuesta["alto"] = 150;
    } else {

        if ($imagen->eliminar()) {
            $respuesta["error"] = false;
            $respuesta["accion"] = "insertar";
            $respuesta["idContenedor"] = "#contenedorImagen" . $id;
            $respuesta["eliminarAjaxLista"] = true;
        } else {
            $respuesta["mensaje"] = $textos->id("ERROR_DESCONOCIDO");
        }
    }

    Servidor::enviarJSON($respuesta);
}

/* * ****************************** NOTIFICACIONES  ******************************************** */

/**
 *
 * Funcion que muestra la ventana modal con el formulario para la confirmación y eliminacion de
 * las Notificaciones del usuario haciendo uso de Ajax
 * @global type $textos
 * @global type $sql
 * @param type $id
 * @param type $confirmado 
 */
function eliminarNotificacion($id, $confirmado) {
    global $textos, $sql;

    $destino = "/ajax/inicio/borrarNotificacion";
    $respuesta = array();

    if (!$confirmado) {
        $nombre = HTML::frase($comentario->autor, "negrilla");
        $nombre = str_replace("%1", $nombre, $textos->id("CONFIRMAR_ELIMINACION_NOTIFICACION"));
        $codigo = HTML::campoOculto("procesar", "true");
        $codigo .= HTML::campoOculto("id", $id);
        $codigo .= HTML::parrafo($nombre);
        $codigo .= HTML::parrafo(HTML::boton("chequeo", $textos->id("ACEPTAR"), "botonOk", "botonOk", "botonOk"), "margenSuperior");
        $codigo .= HTML::parrafo($textos->id("REGISTRO_ELIMINADO"), "textoExitoso", "textoExitoso");
        $codigo = HTML::forma($destino, $codigo);

        $respuesta["generar"] = true;
        $respuesta["codigo"] = $codigo;
        $respuesta["destino"] = "#cuadroDialogo";
        $respuesta["titulo"] = HTML::parrafo($textos->id("ELIMINAR_NOTIFICACION"), "letraBlanca negrilla subtitulo");
        $respuesta["ancho"] = 350;
        $respuesta["alto"] = 170;
    } else {
        //Seleccionar los datos del modulo y del registro pertenecientes a la notificacion
        $notificacion = $sql->filaEnObjeto($sql->seleccionar(array('notificaciones'), array('id_modulo', 'id_registro'), 'id = "' . $id . '"'));
        //selecciono el identificador del modulo de facturas de compra para hacer el comparativo
        $facturaCompra = $sql->obtenerValor('modulos', 'id', 'nombre = "FACTURAS_COMPRA"');



        if ($notificacion->id_modulo == $facturaCompra) {
            $sql->eliminar('facturas_temporales_compra', 'id = "' . $notificacion->id_registro . '"');
            $sql->eliminar('articulos_factura_temporal_compra', 'id_factura_temporal = "' . $notificacion->id_registro . '"');
        }
        $consulta = $sql->eliminar("notificaciones", "id = '" . $id . "'");

        if ($consulta) {
            $respuesta["error"] = false;
            $respuesta["accion"] = "insertar";
            $respuesta["idContenedor"] = "#contenedorNotificacion" . $id;
            $respuesta["eliminarAjaxLista"] = true;
        } else {
            $respuesta["mensaje"] = $textos->id("ERROR_DESCONOCIDO");
        }
    }

    Servidor::enviarJSON($respuesta);
}

/* * ****************************** FIN notificaciones  ******************************************** */

/**
 *
 * @global type $sql
 * @param type $cadena 
 */
function listarUsuarios($cadena) {
    global $sql;
    $respuesta = array();
    $consulta = $sql->seleccionar(array("lista_usuarios"), array("nombre"), "nombre LIKE '%$cadena%'", "", "nombre ASC", 0, 20);

    while ($fila = $sql->filaEnObjeto($consulta)) {
        $respuesta[] = $fila->nombre;
    }

    Servidor::enviarJSON($respuesta);
}

/**
 *
 * @global type $sql
 * @param type $cadena 
 */
function listarCiudades($cadena) {
    global $sql;
    $respuesta = array();

    $consulta = $sql->seleccionar(array("lista_ciudades"), array("cadena"), "nombre LIKE '%$cadena%'", "", "cadena ASC", 0, 20);

    while ($fila = $sql->filaEnObjeto($consulta)) {
        $respuesta[] = $fila->cadena;
    }

    Servidor::enviarJSON($respuesta);
}

/**
 *
 * @global type $sql
 * @param type $cadena 
 */
//function listarPersonas($cadena) {
//    global $sql;
//    $respuesta    = array();
//
//    $consulta  = $sql->seleccionar(array("personas"), array("cadena" => "CONCAT(documento_identidad, '| ', primer_nombre, ' ', primer_apellido, ' ', segundo_apellido)"), "documento_identidad LIKE '%$cadena%'", "", "cadena ASC", 0, 20);
//
//    while ($fila = $sql->filaEnObjeto($consulta)) {
//        $respuesta[] = $fila->cadena;
//    }
//
//    Servidor::enviarJSON($respuesta);
//}

function listarPersonas($cadena) {
    global $sql;
    $respuesta = array();

    $consulta = $sql->seleccionar(array("personas"), array("documento" => "documento_identidad", "nombre" => "CONCAT( primer_nombre, ' ', primer_apellido, ' ', segundo_apellido)"), "documento_identidad LIKE '%$cadena%'", "", "primer_apellido ASC", 0, 20);

    while ($fila = $sql->filaEnObjeto($consulta)) {
        $respuesta1 = array();
        $respuesta1["value"] = $fila->documento;
        $respuesta1["label"] = $fila->nombre;
        $respuesta[] = $respuesta1;
    }

    Servidor::enviarJSON($respuesta);
}

/**
 * Funcion encargada de registrar el tema de estilos css selecionado
 * 
 * @global type $sesion_tema variable de sesion que contiene el valor del tema actual
 * @param type  $nuevoTema   variable que contiene el nuevo valor del tema a almacenar en la variable de sesion
 */
function cambiarApariencia($nuevoTema) {
    global $sesion_tema;


    Sesion::borrar($sesion_tema);
    Sesion::registrar("tema", $nuevoTema);

    $respuesta              = array();
    $respuesta["error"]     = NULL;
    $respuesta["accion"]    = "recargar";

    Servidor::enviarJSON($respuesta);
}

/**
 * Funcion que devuelve una respuesta para verificar la existencia de un item via ajax
 * @global type $sql
 * @param type $cadena 
 */
function verificarItem($tabla, $columna, $valor) {
    global $sql;
    $respuesta = array();
    $consulta = $sql->existeItem($tabla, $columna, $valor);

    $respuesta["existeItem"] = $consulta;

    Servidor::enviarJSON($respuesta);
}

/**
 * Funcion que devuelve una respuesta para verificar la existencia de un item via ajax
 * @global type $sql
 * @param type $cadena 
 */
function verificarPersona($cadena) {
    global $sql, $configuracion;
    $respuesta = array();

    $tablas = array("personas");
    $columnas = array("cedula" => "documento_identidad",
        "tipoDoc" => "id_tipo_documento",
        "ciudadDoc" => "id_ciudad_documento",
        "primerNombre" => "primer_nombre",
        "segundoNombre" => "segundo_nombre",
        "primerApellido" => "primer_apellido",
        "segundoApellido" => "segundo_apellido",
        "fechaNacimiento" => "fecha_nacimiento",
        "ciudadResidencia" => "id_ciudad_residencia",
        "direccion" => "direccion",
        "telefono" => "telefono",
        "celular" => "celular",
        "fax" => "fax",
        "correo" => "correo",
        "sitioWeb" => "sitio_web",
        "genero" => "genero",
        "idImagen" => "id_imagen",
        "observaciones" => "observaciones",
        "activo" => "activo"
    );
    $consulta = $sql->seleccionar($tablas, $columnas, "documento_identidad = '" . $cadena . "'");

    if ($sql->filasDevueltas) {
        $persona = $sql->filaEnObjeto($consulta);

        $persona->ciudadDocumento = $sql->obtenerValor("lista_ciudades", "cadena", "id = '" . $persona->ciudadDoc . "'");
        $persona->ciudadResidencia = $sql->obtenerValor("lista_ciudades", "cadena", "id = '" . $persona->ciudadResidencia . "'");

        $ruta = $sql->obtenerValor("imagenes", "ruta", "id = '" . $persona->idImagen . "'");

        $persona->imagenMiniatura = $configuracion["SERVIDOR"]["media"] . $configuracion["RUTAS"]["imagenesMiniaturas"] . "/" . $ruta;
        $persona->imagenNormal = $configuracion["SERVIDOR"]["media"] . $configuracion["RUTAS"]["imagenesNormales"] . "/" . $ruta;

        $respuesta["cedula"] = $persona->cedula;
        $respuesta["tipoDoc"] = $persona->tipoDoc;
        $respuesta["ciudadDocumento"] = $persona->ciudadDocumento;
        $respuesta["primerNombre"] = $persona->primerNombre;
        $respuesta["segundoNombre"] = $persona->segundoNombre;
        $respuesta["primerApellido"] = $persona->primerApellido;
        $respuesta["segundoApellido"] = $persona->segundoApellido;
        $respuesta["fechaNacimiento"] = $persona->fechaNacimiento;
        $respuesta["ciudadResidencia"] = $persona->ciudadResidencia;
        $respuesta["direccion"] = $persona->direccion;
        $respuesta["telefono"] = $persona->telefono;
        $respuesta["celular"] = $persona->celular;
        $respuesta["fax"] = $persona->fax;
        $respuesta["correo"] = $persona->correo;
        $respuesta["sitioWeb"] = $persona->sitioWeb;
        $respuesta["genero"] = $persona->genero;
        $respuesta["imagenMiniatura"] = $persona->imagenMiniatura;
        $respuesta["imagenNormal"] = $persona->imagenNormal;
        $respuesta["observaciones"] = $persona->observaciones;
        $respuesta["activo"] = $persona->activo;
    } else {
        $respuesta["error"] = true;
    }

    Servidor::enviarJSON($respuesta);
}

/**
 *
 * @global type $textos
 * @global type $sql
 * @global type $archivo_imagen
 * @param type $datos 
 * Funcion que abre la ventana modal que autocompleta segun escriba el usuario,
 * lo que autocompleta es los nombres de los modulos, teniendo en cuenta los permisos del usuario,
 * para visualizar dichos modulos, una vez el usuario selecciona un modulo, sobre el evento
 * on select se llama la funcion que se encarga de redireccionar al usuario hacia dicho modulo
 */
function buscarModulo() {
    global $textos;


    $respuesta = array();

    $codigo .= HTML::parrafo($textos->id('ESCRIBA_NOMBRE_MODULO'), 'negrilla margenSuperior');
    $codigo .= HTML::campoTexto('datos[texto_busqueda]', 40, 100, '', 'autocompletable campoObligatorio', 'campoTextoBusquedaModulos', array('title' => '/ajax/inicio/listarModulos'), $textos->id('AYUDA_USO_AUTOCOMPLETAR'));
    $codigo .= HTML::campoOculto('datos[dialogo]', '', 'idDialogo');
    $codigo = HTML::forma($destino, $codigo, 'P', true);

    $respuesta['generar'] = true;
    $respuesta['codigo'] = $codigo;
    $respuesta['titulo'] = HTML::parrafo($textos->id('BUSCAR_MODULOS'), 'letraBlanca negrilla subtitulo');
    $respuesta['destino'] = '#cuadroDialogo';
    $respuesta['ancho'] = 450;
    $respuesta['alto'] = 200;



    Servidor::enviarJSON($respuesta);
}

/**
 * Funcion que se encarga de listar los modulos existentes dependiendo tambien
 * de los permisos que tenga el usuario sobre los modulos.
 * -usado en el autocompletar para ir a algun modulo particular
 * @global type $sql
 * @param type $cadena 
 */
function listarModulos($cadena) {
    global $sql, $sesion_usuarioSesion;
    $respuesta = array();

    if (isset($sesion_usuarioSesion) && $sesion_usuarioSesion->id != 0) {
        $tablas = array(
            'm' => 'modulos'
        );
        $columnas = array(
            'id' => 'm.id',
            'nombreMenu' => 'm.nombre_menu',
            'url' => 'm.url'
        );
        $condicion = 'm.nombre_menu LIKE "%' . $cadena . '%" AND m.url != "" AND m.id IN (SELECT id_modulo from fom_permisos_modulos_usuarios where id_usuario = "' . $sesion_usuarioSesion->id . '")';

        $consulta = $sql->seleccionar($tablas, $columnas, $condicion, "", "m.nombre_menu ASC", 0, 20);

        while ($fila = $sql->filaEnObjeto($consulta)) {
            $respuesta1 = array();
            $respuesta1['value'] = $fila->url;
            $respuesta1['label'] = $fila->nombreMenu;
            $respuesta[] = $respuesta1;
        }
    } else {
        $tablas = array(
            'm' => 'modulos'
        );
        $columnas = array(
            'id' => 'm.id',
            'nombreMenu' => 'm.nombre_menu',
            'url' => 'm.url'
        );
        $condicion = 'm.nombre_menu LIKE "%' . $cadena . '%" AND m.url != "" AND m.menu = "1" ';

        $consulta = $sql->seleccionar($tablas, $columnas, $condicion, "", "m.nombre_menu ASC", 0, 20);

        while ($fila = $sql->filaEnObjeto($consulta)) {
            $respuesta1 = array();
            $respuesta1['value'] = $fila->url;
            $respuesta1['label'] = $fila->nombreMenu;
            $respuesta[] = $respuesta1;
        }
    }

    Servidor::enviarJSON($respuesta);
}


/**
 * @global type $sql
 * @global type $sesion_usuarioSesion
 * @param type $mes 
 */
function cargarFechasEventos($inicio, $fin){

    $objeto = new Evento();
    $arregloFinal = $objeto->cargarFechasEventos($inicio, $fin);
    Servidor::enviarJSON($arregloFinal);   
    
}


?>