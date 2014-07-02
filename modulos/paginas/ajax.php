<?php

/**
 *
 * @package     FOLCS
 * @subpackage  Menus
 * @author      Pablo Andres Velez Vidal <pavelez@genesyscorp.com.co>
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2013 GENESYS
 * @version     0.1
 *
 * */
global $url_accion, $forma_datos, $forma_procesar, $forma_id, $forma_cantidad, $forma_cadenaItems;
if (isset($url_accion)) {
    switch ($url_accion) {
        case "add" : $datos = ($forma_procesar) ? $forma_datos : array();
            adicionarPagina($datos);
            break;
        case "edit" : $datos = ($forma_procesar) ? $forma_datos : array();
            modificarPagina($forma_id, $datos);
            break;
        case "delete" : $confirmado = ($forma_procesar) ? true : false;
            eliminarPagina($forma_id, $confirmado);
            break;
        case "up" : $confirmado = ($forma_procesar) ? true : false;
            subirPagina($forma_id, $confirmado);
            break;
        case "down" : $confirmado = ($forma_procesar) ? true : false;
            bajarPagina($forma_id, $confirmado);
            break;
        case 'eliminarVarios' : $confirmado = ($forma_procesar) ? true : false;
            eliminarVarios($confirmado, $forma_cantidad, $forma_cadenaItems);
            break;
    }
}

/**
 * Funcion que se encarga de agregar o modificar las paginas 
 * que se encuentran en la  pagina de inicio de la aplicacion,
 * se pueden agregar maximo 6 paginas.
 * 
 * @global type $textos
 * @global type $sql
 * @param type $datos 
 */
function adicionarPagina($datos = array()) {
    global $textos, $sql;

    $pagina = new Pagina();
    $destino = "/ajax" . $pagina->urlBase . "/add";
    $respuesta = "";

    if (empty($datos)) {
        $codigo = HTML::campoOculto("procesar", "true");
        $codigo .= HTML::parrafo($textos->id("TITULO"), "negrilla margenSuperior");
        $codigo .= HTML::campoTexto("datos[titulo]", 25, 25);
        $codigo .= HTML::parrafo($textos->id("CONTENIDO"), "negrilla margenSuperior");
        $codigo .= HTML::areaTexto("datos[contenido]", 10, 60, "", "editor");
        $codigo .= HTML::parrafo(HTML::campoChequeo("datos[activo]", true) . $textos->id("ACTIVO"), "margenSuperior");
        $codigo .= HTML::parrafo(HTML::boton("chequeo", $textos->id("ACEPTAR")), "margenSuperior");
        $codigo = HTML::forma($destino, $codigo);

        $respuesta["generar"] = true;
        $respuesta["codigo"] = $codigo;
        $respuesta["destino"] = "#cuadroDialogo";
        $respuesta["titulo"] = $textos->id("ADICIONAR_PAGINA");
        $respuesta["ancho"] = 700;
        $respuesta["alto"] = 500;
    } else {
        $respuesta["error"] = true;

        if (empty($datos["titulo"])) {
            $respuesta["mensaje"] = $textos->id("ERROR_FALTA_TITULO");
        } elseif ($sql->existeItem("paginas", "titulo", $datos["titulo"])) {
            $respuesta["mensaje"] = $textos->id("ERROR_EXISTE_TITULO");
        } elseif (empty($datos["contenido"])) {
            $respuesta["mensaje"] = $textos->id("ERROR_FALTA_CONTENIDO");
        } else {

            if ($pagina->adicionar($datos)) {
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
 * Funcion que se encarga de modificar una de las paginas creadas
 * @global type $textos
 * @global type $sql
 * @param type $id
 * @param type $datos 
 */
function modificarPagina($id, $datos = array()) {
    global $textos, $sql;

    $pagina = new Pagina($id);
    $destino = "/ajax" . $pagina->urlBase . "/edit";
    $respuesta = "";

    if (empty($datos)) {
        $codigo = HTML::campoOculto("procesar", "true");
        $codigo .= HTML::campoOculto("id", $id);
        $codigo .= HTML::parrafo($textos->id("TITULO"), "negrilla margenSuperior");
        $codigo .= HTML::campoTexto("datos[titulo]", 25, 25, $pagina->titulo);
        $codigo .= HTML::parrafo($textos->id("CONTENIDO"), "negrilla margenSuperior");
        $codigo .= HTML::areaTexto("datos[contenido]", 10, 60, $pagina->contenido, "editor");
        $codigo .= HTML::parrafo(HTML::campoChequeo("datos[activo]", $pagina->activo) . $textos->id("ACTIVO"), "margenSuperior");
        $codigo .= HTML::parrafo(HTML::boton("chequeo", $textos->id("ACEPTAR")), "margenSuperior");
        $codigo = HTML::forma($destino, $codigo);

        $respuesta["generar"] = true;
        $respuesta["codigo"] = $codigo;
        $respuesta["destino"] = "#cuadroDialogo";
        $respuesta["titulo"] = $textos->id("MODIFICAR_PAGINA");
        $respuesta["ancho"] = 700;
        $respuesta["alto"] = 500;
    } else {
        $respuesta["error"] = true;

        if (empty($datos["titulo"])) {
            $respuesta["mensaje"] = $textos->id("ERROR_EXISTE_TITULO");
        } elseif ($datos["titulo"] != $pagina->titulo && $sql->existeItem("paginas", "titulo", $datos["titulo"])) {
            $respuesta["mensaje"] = $textos->id("ERROR_EXISTE_TITULO");
        } elseif (empty($datos["contenido"])) {
            $respuesta["mensaje"] = $textos->id("ERROR_FALTA_CONTENIDO");
        } else {

            if ($pagina->modificar($datos)) {
                $respuesta["error"] = false;
                //$respuesta["mensaje"] = $textos->id("PAGINA_MODIFICADA");
                $respuesta["accion"] = "recargar";
            } else {
                $respuesta["mensaje"] = $textos->id("ERROR_DESCONOCIDO");
            }
        }
    }

    Servidor::enviarJSON($respuesta);
}

/**
 * Funcion que elimina una pagina de las existentes
 * @global type $textos
 * @global type $sql
 * @param type $id
 * @param type $confirmado 
 */
function eliminarPagina($id, $confirmado) {
    global $textos;

    $pagina = new Pagina($id);
    $destino = "/ajax" . $pagina->urlBase . "/delete";
    $respuesta = "";

    if (!$confirmado) {
        $titulo = HTML::frase($pagina->nombre, "negrilla");
        $titulo = preg_replace("/\%1/", $titulo, $textos->id("CONFIRMAR_ELIMINACION"));
        $codigo = HTML::campoOculto("procesar", "true");
        $codigo .= HTML::campoOculto("id", $id);
        $codigo .= HTML::parrafo($titulo);
        $codigo .= HTML::parrafo(HTML::boton("chequeo", $textos->id("ACEPTAR")), "margenSuperior");
        $codigo = HTML::forma($destino, $codigo);

        $respuesta["generar"] = true;
        $respuesta["codigo"] = $codigo;
        $respuesta["destino"] = "#cuadroDialogo";
        $respuesta["titulo"] = $textos->id("ELIMINAR_PAGINA");
        $respuesta["ancho"] = 200;
        $respuesta["alto"] = 120;
    } else {
        if ($pagina->eliminar()) {
            $respuesta["error"] = false;
            //$respuesta["mensaje"] = $textos->id("PAGINA_ELIMINADA");
            $respuesta["accion"] = "recargar";
        } else {
            $respuesta["mensaje"] = $textos->id("ERROR_DESCONOCIDO");
        }
    }

    Servidor::enviarJSON($respuesta);
}

/**
 * Subir el orden "la posicion" de una pagina
 * @global type $textos
 * @global type $sql
 * @param type $id
 * @param type $confirmado 
 */
function subirPagina($id, $confirmado) {
    global $textos;

    $pagina = new Pagina($id);
    $destino = "/ajax" . $pagina->urlBase . "/up";
    $respuesta = "";

    if (!$confirmado) {
        $titulo = HTML::frase($pagina->titulo, "negrilla");
        $titulo = preg_replace("/\%1/", $titulo, $textos->id("CONFIRMAR_MODIFICACION"));
        $codigo = HTML::campoOculto("procesar", "true");
        $codigo .= HTML::campoOculto("id", $id);
        $codigo .= HTML::parrafo($titulo);
        $codigo .= HTML::parrafo(HTML::boton("chequeo", $textos->id("ACEPTAR")), "margenSuperior");
        $codigo = HTML::forma($destino, $codigo);

        $respuesta["generar"] = true;
        $respuesta["codigo"] = $codigo;
        $respuesta["destino"] = "#cuadroDialogo";
        $respuesta["titulo"] = $textos->id("MODIFICAR_PAGINA");
        $respuesta["ancho"] = 200;
        $respuesta["alto"] = 120;
    } else {

        if ($pagina->subir()) {
            $respuesta["error"] = false;
            //$respuesta["mensaje"] = $textos->id("PAGINA_MODIFICADA");
            $respuesta["accion"] = "recargar";
        } else {
            $respuesta["error"] = true;
            $respuesta["mensaje"] = $textos->id("ERROR_DESCONOCIDO");
        }
    }

    Servidor::enviarJSON($respuesta);
}

/**
 * Bajar el orden "la posicion" de una pagina
 * @global type $textos
 * @global type $sql
 * @param type $id
 * @param type $confirmado 
 * 
 * 
 * 
 * CeCuCoAm2009
 * 
 */
function bajarPagina($id, $confirmado) {
    global $textos;

    $pagina = new Pagina($id);
    $destino = "/ajax" . $pagina->urlBase . "/down";
    $respuesta = "";

    if (!$confirmado) {
        $titulo = HTML::frase($pagina->titulo, "negrilla");
        $titulo = preg_replace("/\%1/", $titulo, $textos->id("CONFIRMAR_MODIFICACION"));
        $codigo = HTML::campoOculto("procesar", "true");
        $codigo .= HTML::campoOculto("id", $id);
        $codigo .= HTML::parrafo($titulo);
        $codigo .= HTML::parrafo(HTML::boton("chequeo", $textos->id("ACEPTAR")), "margenSuperior");
        $codigo = HTML::forma($destino, $codigo);

        $respuesta["generar"] = true;
        $respuesta["codigo"] = $codigo;
        $respuesta["destino"] = "#cuadroDialogo";
        $respuesta["titulo"] = $textos->id("MODIFICAR_PAGINA");
        $respuesta["ancho"] = 200;
        $respuesta["alto"] = 120;
    } else {

        if ($pagina->bajar()) {
            $respuesta["error"] = false;
            ;
            $respuesta["accion"] = "recargar";
        } else {
            $respuesta["error"] = true;
            $respuesta["mensaje"] = $textos->id("ERROR_DESCONOCIDO");
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
    global $textos;


    $destino = '/ajax/paginas/eliminarVarios';
    $respuesta = array();

    if (!$confirmado) {
        $titulo = HTML::frase($cantidad, 'negrilla');
        $titulo = str_replace('%1', $titulo, $textos->id('CONFIRMAR_ELIMINACION_VARIOS'));
        $codigo = HTML::campoOculto('procesar', 'true');
        $codigo .= HTML::campoOculto('cadenaItems', $cadenaItems, 'cadenaItems');
        $codigo .= HTML::parrafo($titulo);
        $codigo .= HTML::parrafo(HTML::boton('chequeo', $textos->id('ACEPTAR'), '', 'botonOk', 'botonOk'), 'margenSuperior');
        $codigo .= HTML::parrafo($textos->id('REGISTRO_ELIMINADO'), 'textoExitoso', 'textoExitoso');
        $codigo = HTML::forma($destino, $codigo);

        $respuesta['generar'] = true;
        $respuesta['codigo'] = $codigo;
        $respuesta['destino'] = '#cuadroDialogo';
        $respuesta['titulo'] = HTML::parrafo($textos->id('ELIMINAR_VARIOS_REGISTROS'), 'letraBlanca negrilla subtitulo');
        $respuesta['ancho'] = 350;
        $respuesta['alto'] = 150;
    } else {

        $cadenaIds = substr($cadenaItems, 0, -1);
        $arregloIds = explode(",", $cadenaIds);

        $eliminarVarios = true;
        foreach ($arregloIds as $val) {
            $objeto = new Pagina($val);
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

?>