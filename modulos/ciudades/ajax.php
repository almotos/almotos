<?php

/**
 *
 * @package     FOLCS
 * @subpackage  Ciudades
 * @author      Pablo Andres Velez Vidal <pavelez@genesyscorp.com.co>
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2013 GENESYS
 * @version     0.1
 *
 * */

if (isset($url_accion)) {
    switch ($url_accion) {
        case "add"              :   $datos = ($forma_procesar) ? $forma_datos : array();
                                    adicionarItem($datos);
                                    break;
        
        case "see"              :   cosultarItem($forma_id);
                                    break;
        
        case "edit"             :   $datos = ($forma_procesar) ? $forma_datos : array();
                                    modificarItem($forma_id, $datos);
                                    break;
        
        case "delete"           :   $confirmado = ($forma_procesar) ? true : false;
                                    eliminarItem($forma_id, $confirmado, $forma_dialogo);
                                    break;

        case "search"           :   buscarItem($forma_datos, $forma_cantidadRegistros);
                                    break;
        
        case "move"             :   paginador($forma_pagina, $forma_orden, $forma_nombreOrden, $forma_consultaGlobal, $forma_cantidadRegistros);
                                    break;
        
        case "listar"           :   listarItems($url_cadena);
                                    break;
        
        case "listarCiudades"   :   listarCiudades($url_cadena);
                                    break;
        
        case "listarEstados"    :   listarEstados($url_cadena);
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

    if (!isset($id) || (isset($id) && !$sql->existeItem("ciudades", "id", $id))) {
        $respuesta = array();
        
        $respuesta["error"]     = true;
        $respuesta["mensaje"]   = $textos->id("NO_HA_SELECCIONADO_ITEM");

        Servidor::enviarJSON($respuesta);
        return NULL;
        
    }

    $objeto = new Ciudad($id);
    $respuesta = array();

    $codigo  = HTML::parrafo($textos->id("NOMBRE"), "negrilla margenSuperior");
    $codigo .= HTML::parrafo($objeto->nombre, "", "");
    $codigo .= HTML::parrafo($textos->id("ESTADO_DPTO"), "negrilla margenSuperior");
    $codigo .= HTML::parrafo($objeto->Estado, "", "");
    $codigo .= HTML::parrafo($textos->id("PAIS"), "negrilla margenSuperior");
    $codigo .= HTML::parrafo($objeto->pais, "", "");


    $respuesta["generar"]   = true;
    $respuesta["codigo"]    = $codigo;
    $respuesta["titulo"]    = HTML::parrafo($textos->id("CONSULTAR_ITEM"), "letraBlanca negrilla subtitulo");
    $respuesta["destino"]   = "#cuadroDialogo";
    $respuesta["ancho"]     = 450;
    $respuesta["alto"]      = 300;



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

    $objeto = new Ciudad();
    $destino = "/ajax" . $objeto->urlBase . "/add";
    $respuesta = array();

    if (empty($datos)) {
        $codigo  = HTML::campoOculto("procesar", "true");
        $codigo .= HTML::campoOculto("datos[dialogo]", "", "idDialogo");
        $codigo .= HTML::parrafo($textos->id("NOMBRE"), "negrilla margenSuperior");
        $codigo .= HTML::campoTexto("datos[nombre]", 30, 255);
        $codigo .= HTML::parrafo($textos->id("ESTADO"), "negrilla margenSuperior");
        $codigo .= HTML::campoTexto("datos[id_estado]", 50, 255, "", "autocompletable", "", array("title" => HTML::urlInterna("CIUDADES", 0, true, "listarEstados")));
        $codigo .= HTML::parrafo(HTML::boton("chequeo", $textos->id("ACEPTAR")), "margenSuperior");
        $codigo .= HTML::parrafo($textos->id("REGISTRO_AGREGADO"), "textoExitoso", "textoExitoso");
        $codigo  = HTML::forma($destino, $codigo);

        $respuesta["generar"]   = true;
        $respuesta["codigo"]    = $codigo;
        $respuesta["destino"]   = "#cuadroDialogo";
        $respuesta["titulo"]    = HTML::parrafo($textos->id("ADICIONAR_ITEM"), "letraBlanca negrilla subtitulo");
        $respuesta["ancho"]     = 450;
        $respuesta["alto"]      = 300;
        
    } else {
        $respuesta["error"] = true;
        $idEstado = $sql->obtenerValor("lista_estados", "id", "cadena = '" . utf8_decode($datos["id_estado"]) . "'");

        if (empty($datos["nombre"])) {
            $respuesta["mensaje"] = $textos->id("ERROR_FALTA_NOMBRE");
            
        } elseif (empty($datos["id_estado"])) {
            $respuesta["mensaje"] = $textos->id("ERROR_FALTA_ESTADO");
            
        } elseif ($sql->existeItem("ciudades", "nombre", utf8_decode($datos["nombre"]), "id_estado = '" . $idEstado . "'")) {
            $respuesta["mensaje"] = $textos->id("ERROR_EXISTE_NOMBRE");
            
        } elseif (!$sql->existeItem("lista_estados", "cadena", utf8_decode($datos["id_estado"]))) {
            $respuesta["mensaje"] = $textos->id("ERROR_NO_EXISTE_ESTADO");
            
        } else {

            $idItem = $objeto->adicionar($datos);
            if ($idItem) {
                /*                 * ************** Creo el nuevo item que se insertara via ajax *************** */
                $objeto = new Ciudad($idItem);

                $celdas = array($objeto->nombre, $objeto->Estado, $objeto->pais);
                $claseFila = "";
                $idFila = $idItem;
                $celdas = HTML::crearNuevaFila($celdas, $claseFila, $idFila);

                if ($datos["dialogo"] == "") {
                    $respuesta["error"] = false;
                    $respuesta["accion"] = "insertar";
                    $respuesta["contenido"] = $celdas;
                    $respuesta["idContenedor"] = "#tr_" . $idItem;
                    $respuesta["insertarNuevaFila"] = true;
                    $respuesta["idDestino"] = "#tablaRegistros";
                    
                } else {
                    $respuesta["error"] = false;
                    $respuesta["accion"] = "insertar";
                    $respuesta["contenido"] = $celdas;
                    $respuesta["idContenedor"] = "#tr_" . $idItem;
                    $respuesta["insertarNuevaFilaDialogo"] = true;
                    $respuesta["idDestino"] = "#tablaRegistros";
                    $respuesta["ventanaDialogo"] = $datos["dialogo"];
                    
                }
            } else {
                $respuesta["mensaje"] = $textos->id("ERROR_DESCONOCIDO");
                
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

    $objeto = new Ciudad($id);
    $destino = "/ajax" . $objeto->urlBase . "/edit";
    $respuesta = array();

    if (empty($datos)) {
        $codigo  = HTML::campoOculto("procesar", "true");
        $codigo .= HTML::campoOculto("id", $id);
        $codigo .= HTML::campoOculto("datos[dialogo]", "", "idDialogo");
        $codigo .= HTML::parrafo($textos->id("NOMBRE"), "negrilla margenSuperior");
        $codigo .= HTML::campoTexto("datos[nombre]", 30, 255, $objeto->nombre);
        $codigo .= HTML::parrafo($textos->id("ESTADO"), "negrilla margenSuperior");
        $codigo .= HTML::campoTexto("datos[id_estado]", 30, 255, $objeto->Estado . " - " . $objeto->paisSolo, "autocompletable", "", array("title" => HTML::urlInterna("CIUDADES", 0, true, "listarEstados")));
        $codigo .= HTML::parrafo(HTML::boton("chequeo", $textos->id("ACEPTAR")), "margenSuperior");
        $codigo .= HTML::parrafo($textos->id("REGISTRO_AGREGADO"), "textoExitoso", "textoExitoso");
        $codigo  = HTML::forma($destino, $codigo);

        $respuesta["generar"]   = true;
        $respuesta["codigo"]    = $codigo;
        $respuesta["destino"]   = "#cuadroDialogo";
        $respuesta["titulo"]    = HTML::parrafo($textos->id("MODIFICAR_ITEM"), "letraBlanca negrilla subtitulo");
        $respuesta["ancho"]     = 450;
        $respuesta["alto"]      = 300;
    } else {
        $respuesta["error"] = true;
        $idEstado = $sql->obtenerValor("lista_estados", "id", "cadena = '" . utf8_decode($datos["id_estado"]) . "'");
        
        if (empty($datos["nombre"])) {
            $respuesta["mensaje"] = $textos->id("ERROR_FALTA_NOMBRE");
            
        } elseif (empty($datos["id_estado"])) {
            $respuesta["mensaje"] = $textos->id("ERROR_FALTA_ESTADO");
            
        } elseif ($sql->existeItem("ciudades", "nombre", utf8_decode($datos["nombre"]), "id != '" . $objeto->id . "' AND id_estado = '" . $idEstado . "'")) {
            $respuesta["mensaje"] = $textos->id("ERROR_EXISTE_NOMBRE");
            
        } elseif (!$sql->existeItem("lista_estados", "cadena", utf8_decode($datos["id_estado"]))) {
            $respuesta["mensaje"] = $textos->id("ERROR_NO_EXISTE_ESTADO");
            
        } else {
            $idItem = $objeto->modificar($datos);
            if ($idItem) {
                /*                 * ************** Creo el nuevo item que se insertara via ajax *************** */
                $objeto = new Ciudad($id);
                $celdas = array($objeto->nombre, $objeto->Estado, $objeto->pais);
                $celdas = HTML::crearFilaAModificar($celdas);

                if ($datos["dialogo"] == "") {
                    $respuesta["error"]                 = false;
                    $respuesta["accion"]                = "insertar";
                    $respuesta["contenido"]             = $celdas;
                    $respuesta["idContenedor"]          = "#tr_" . $id;
                    $respuesta["modificarFilaTabla"]    = true;
                    $respuesta["idDestino"]             = "#tr_" . $id;
                    
                } else {
                    $respuesta["error"]                 = false;
                    $respuesta["accion"]                = "insertar";
                    $respuesta["contenido"]             = $celdas;
                    $respuesta["idContenedor"]          = "#tr_" . $id;
                    $respuesta["modificarFilaDialogo"]  = true;
                    $respuesta["idDestino"]             = "#tr_" . $id;
                    $respuesta["ventanaDialogo"]        = $datos["dialogo"];
                    
                }
                
            } else {
                $respuesta["mensaje"] = $textos->id("ERROR_DESCONOCIDO");
                
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
    global $textos;

    $objeto = new Ciudad($id);
    $destino = "/ajax" . $objeto->urlBase . "/delete";
    $respuesta = array();

    if (!$confirmado) {
        $nombre     = HTML::frase($objeto->nombre, "negrilla");
        $nombre     = preg_replace("/\%1/", $nombre, $textos->id("CONFIRMAR_ELIMINACION"));
        $codigo     = HTML::campoOculto("procesar", "true");
        $codigo    .= HTML::campoOculto("id", $id);
        $codigo    .= HTML::campoOculto("dialogo", "", "idDialogo");
        $codigo    .= HTML::parrafo($nombre);
        $codigo    .= HTML::parrafo(HTML::boton("chequeo", $textos->id("ACEPTAR")), "margenSuperior");
        $codigo    .= HTML::parrafo($textos->id("REGISTRO_ELIMINADO"), "textoExitoso", "textoExitoso");
        $codigo     = HTML::forma($destino, $codigo);

        $respuesta["generar"]   = true;
        $respuesta["codigo"]    = $codigo;
        $respuesta["destino"]   = "#cuadroDialogo";
        $respuesta["titulo"]    = HTML::parrafo($textos->id("ELIMINAR_ITEM"), "letraBlanca negrilla subtitulo");
        $respuesta["ancho"]     = 350;
        $respuesta["alto"]      = 150;
        
    } else {

        if ($objeto->eliminar()) {
            if ($dialogo == "") {
                $respuesta["error"] = false;
                $respuesta["accion"] = "insertar";
                $respuesta["idDestino"] = "#tr_" . $id;
                $respuesta["eliminarFilaTabla"] = true;
                
            } else {
                $respuesta["error"] = false;
                $respuesta["accion"] = "insertar";
                $respuesta["idDestino"] = "#tr_" . $id;
                $respuesta["eliminarFilaDialogo"] = true;
                $respuesta["ventanaDialogo"] = $dialogo;
                
            }
            
        } else {
            $respuesta["mensaje"] = $textos->id("ERROR_DESCONOCIDO");
            
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
 * @global objeto $textos               = objeto global que gestiona los textos a traducir
 * @global arreglo $configuracion       = arreglo global de configuracion
 * @param arreglo $data                 = arreglo con los parametros de busqueda
 * @param int $cantidadRegistros        = cantidad de registros aincluir por busqueda
 */
function buscarItem($data, $cantidadRegistros = NULL) {
    global $textos, $configuracion;

    $data   = explode("[", $data);
    $datos  = $data[0];

    if (empty($datos)) {
        $respuesta["error"] = true;
        $respuesta["mensaje"] = $textos->id("ERROR_FALTA_CADENA_BUSQUEDA");
        
    } else if (!empty($datos) && strlen($datos) < 2) {
        $respuesta["error"] = true;
        $respuesta["mensaje"] = str_replace("%1", "2", $textos->id("ERROR_TAMA�O_CADENA_BUSQUEDA"));
        
    } else {
        $item = "";
        $respuesta = array();
        $objeto = new Ciudad();
        $registros = $configuracion["GENERAL"]["registrosPorPagina"];
        
        if(!empty($cantidadRegistros)){
            $registros = (int)$cantidadRegistros;
            
        }          
        $pagina             = 1;
        $registroInicial    = 0;


        $palabras = explode(" ", $datos);

        $condicionales = $data[1];

        if ($condicionales == "") {
            $condicion = "(c.nombre REGEXP '(" . implode("|", $palabras) . ")')";
            
        } else {
            //$condicion = str_replace("]", "'", $data[1]);
            $condicionales = explode("|", $condicionales);

            $condicion  = "(";
            $tam        = sizeof($condicionales) - 1;
            for ($i = 0; $i < $tam; $i++) {
                $condicion .= $condicionales[$i] . " REGEXP '(" . implode("|", $palabras) . ")' ";
                
                if ($i != $tam - 1) {
                    $condicion .= " OR ";
                    
                }
                
            }
            
            $condicion .= ")";
            
        }

        $arregloItems = $objeto->listar($registroInicial, $registros, array("0"), $condicion, "c.nombre");

        if ($objeto->registrosConsulta) {//si la consulta trajo registros
            $datosPaginacion = array($objeto->registrosConsulta, $registroInicial, $registros, $pagina, $objeto->registrosConsulta);
            $item .= $objeto->generarTabla($arregloItems, $datosPaginacion);
            $info  = HTML::parrafo("Tu busqueda trajo " . $objeto->registrosConsulta . " resultados", "textoExitosoNotificaciones");
            
        } else {
            $datosPaginacion = 0;
            $item .= $objeto->generarTabla($textos->id("NO_HAY_REGISTROS"), $datosPaginacion);
            $info = HTML::parrafo("Tu busqueda no trajo resultados, por favor intenta otra busqueda", "textoErrorNotificaciones");
            
        }
        
        $respuesta["error"]         = false;
        $respuesta["accion"]        = "insertar";
        $respuesta["contenido"]     = $item;
        $respuesta["idContenedor"]  = "#tablaRegistros";
        $respuesta["idDestino"]     = "#contenedorTablaRegistros";
        $respuesta["paginarTabla"]  = true;
        $respuesta["info"]          = $info;
        
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

    $item = "";
    $respuesta = array();
    $objeto = new Ciudad();

    $registros = $configuracion["GENERAL"]["registrosPorPagina"];
    if(!empty($cantidadRegistros)){
        $registros = (int)$cantidadRegistros;
        
    }    

    if (isset($pagina)) {
        $pagina = $pagina;
        
    } else {
        $pagina = 1;
        
    }

    if (isset($consultaGlobal) && $consultaGlobal != "") {

        $data       = explode("[", $consultaGlobal);
        $datos      = $data[0];
        $palabras   = explode(" ", $datos);

        if ($data[1] != "") {
            $condicionales = explode("|", $data[1]);

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
        $consultaGlobal = "";
        
    }

    if (!isset($nombreOrden)) {
        $nombreOrden = $objeto->ordenInicial;
        
    }


    if (isset($orden) && $orden == "ascendente") {//ordenamiento
        $objeto->listaAscendente = true;
        
    } else {
        $objeto->listaAscendente = false;
        
    }

    if (isset($nombreOrden) && $nombreOrden == "estado") {//ordenamiento
        $nombreOrden = "activo";
        
    }

    $registroInicial = ($pagina - 1) * $registros;


    $arregloItems = $objeto->listar($registroInicial, $registros, array("0"), $consultaGlobal, $nombreOrden);

    if ($objeto->registrosConsulta) {//si la consulta trajo registros
        $datosPaginacion = array($objeto->registrosConsulta, $registroInicial, $registros, $pagina);
        $item .= $objeto->generarTabla($arregloItems, $datosPaginacion);
        
    }

    $respuesta["error"]         = false;
    $respuesta["accion"]        = "insertar";
    $respuesta["contenido"]     = $item;
    $respuesta["idContenedor"]  = "#tablaRegistros";
    $respuesta["idDestino"]     = "#contenedorTablaRegistros";
    $respuesta["paginarTabla"]  = true;

    Servidor::enviarJSON($respuesta);
}

/**
 * Funcion que devuelve la respuesta para el autocompletar
 * 
 * @global type $sql
 * @param type $cadena 
 */
function listarItems($cadena) {
    global $sql;

    $respuesta = array();
    $consulta  = $sql->seleccionar(array("lista_ciudades"), array("cadena"), "nombre LIKE '$cadena%'", "", "cadena ASC", 0, 20);

    while ($fila = $sql->filaEnObjeto($consulta)) {
        $respuesta[] = $fila->cadena;
        
    }

    Servidor::enviarJSON($respuesta);
}

/**
 * Funcion que devuelve la respuesta para el autocompletar
 * 
 * @global recurso $sql = objeto global de interaccion con la BD
 * @param string $cadena  = cadena de busqueda
 */
function listarCiudades($cadena) {
    global $sql;

    $respuesta = array();
    $consulta  = $sql->seleccionar(array("lista_ciudades"), array("id", "cadena"), "nombre LIKE '$cadena%'", "", "cadena ASC", 0, 20);

    while ($fila = $sql->filaEnObjeto($consulta)) {
        $respuesta1 = array();
        $respuesta1['label']    = $fila->cadena;
        $respuesta1['value']    = $fila->id;
        $respuesta[]            = $respuesta1;
    }

    Servidor::enviarJSON($respuesta);
}


/**
 *
 * @global type $sql
 * @param type $cadena 
 */
function listarEstados($cadena) {
    global $sql;

    $respuesta = array();
    $consulta  = $sql->seleccionar(array("lista_estados"), array("cadena"), "cadena LIKE '$cadena%'", "", "cadena ASC", 0, 20);

    while ($fila = $sql->filaEnObjeto($consulta)) {
        $respuesta[] = $fila->cadena;
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


    $destino = '/ajax/ciudades/eliminarVarios';
    $respuesta = array();

    if (!$confirmado) {
        $titulo  = HTML::frase($cantidad, 'negrilla');
        $titulo  = str_replace('%1', $titulo, $textos->id('CONFIRMAR_ELIMINACION_VARIOS'));
        $codigo  = HTML::campoOculto('procesar', 'true');
        $codigo .= HTML::campoOculto('cadenaItems', $cadenaItems, 'cadenaItems');
        $codigo .= HTML::parrafo($titulo);
        $codigo .= HTML::parrafo(HTML::boton('chequeo', $textos->id('ACEPTAR'), '', 'botonOk', 'botonOk'), 'margenSuperior');
        $codigo .= HTML::parrafo($textos->id('REGISTRO_ELIMINADO'), 'textoExitoso', 'textoExitoso');
        $codigo  = HTML::forma($destino, $codigo);

        $respuesta['generar']   = true;
        $respuesta['codigo']    = $codigo;
        $respuesta['destino']   = '#cuadroDialogo';
        $respuesta['titulo']    = HTML::parrafo($textos->id('ELIMINAR_VARIOS_REGISTROS'), 'letraBlanca negrilla subtitulo');
        $respuesta['ancho']     = 350;
        $respuesta['alto']      = 150;
        
    } else {

        $cadenaIds  = substr($cadenaItems, 0, -1);
        $arregloIds = explode(",", $cadenaIds);

        $eliminarVarios = true;
        foreach ($arregloIds as $val) {
            $objeto = new Ciudad($val);
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
