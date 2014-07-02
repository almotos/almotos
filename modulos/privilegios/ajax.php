<?php

/**
 *
 * @package     FOLCS
 * @subpackage  Privilegios
 * @author      Pablo Andrés Vélez Vidal <pavelez@genesyscorporation.com.co>
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2014 Genesys corporation.
 * @version     0.2
 *
 **/
global $url_accion, $forma_procesar, $forma_id;

if (isset($url_accion)) {
    switch ($url_accion) {
        case "add"      :   $datos = ($forma_procesar) ?  $forma_datos : array();
                            adicionarItem($datos);
                            break;

        case "see"      :   consultarItem($forma_id, $forma_sede);
                            break;

        case "edit"     :   $datos = ($forma_procesar) ?  $forma_datos : array();
                            modificarItem($forma_id, $forma_sede, $datos);
                            break;

        case "delete"   :   $confirmado = ($forma_procesar) ? true : false;
                            eliminarItem($forma_id, $confirmado);
                            break;

        case 'search'   :   buscarItem($forma_datos, $forma_cantidadRegistros);
                            break;

        case 'move'     :   paginador($forma_pagina, $forma_orden, $forma_nombreOrden, $forma_consultaGlobal, $forma_cantidadRegistros);
                            break;

        case 'cargarSedes': cargarSedesSinPrivilegios($forma_idUsuario);
                            break;

    }

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

    $objeto    = new Privilegios();
    $destino        = "/ajax".$objeto->urlBase."/add";   

    if (empty($datos)) {
        $usuarios = array();
        $consulta  = $sql->seleccionar(array("usuarios"), array("id", "usuario"), "id != 0", "", "usuario ASC");
        if($sql->filasDevueltas){
            while ($dato = $sql->filaEnObjeto($consulta)) {
                $usuarios[$dato->id] = $dato->usuario;
            }
        }
        
        $listaSedesEmpresa = HTML::listaDesplegable('datos[sede]', array(), '', '', 'listaSedesEmpresa');
    
        $codigo  = HTML::campoOculto("procesar", "true");
        $codigo .= HTML::parrafo($textos->id("USUARIO"), "negrilla margenSuperior");
        $codigo .= HTML::listaDesplegable("datos[usuario]", $usuarios, "", "", "listaUsuarios", "   ");
        $codigo .= HTML::parrafo($textos->id("SEDE"), "negrilla margenSuperior");
        $codigo .= HTML::parrafo($listaSedesEmpresa, 'margenSuperior');
        $codigo .= HTML::parrafo($textos->id("PRIVILEGIOS"), "negrilla margenSuperior");
        $codigo .= $objeto->listaPrivilegios(0, 0, "adicionar");
        $codigo .= HTML::parrafo(HTML::boton("chequeo", $textos->id("ACEPTAR")), "margenSuperior", "", "botonAgregarPrivilegios");
        $codigo  = HTML::forma($destino, $codigo);

        $respuesta["generar"]   = true;
        $respuesta["codigo"]    = $codigo;
        $respuesta['cargarJs']  = true;
        $respuesta['archivoJs'] = $configuracion['SERVIDOR']['media'] . $configuracion['RUTAS']['javascript'] . '/modulos/privilegios/funcionesAdicionarPrivilegios.js';
        $respuesta["destino"]   = "#cuadroDialogo";
        $respuesta["titulo"]    = HTML::parrafo($textos->id("ADICIONAR_PRIVILEGIOS"), "letraBlanca negrilla");
        $respuesta["ancho"]     = 600;
        $respuesta["alto"]      = 500;
        
    } else {
        $respuesta["error"]   = true;

        if (empty($datos["usuario"])) {
            $respuesta["mensaje"] = $textos->id("ERROR_FALTA_USUARIO");
            
        } else if (empty($datos["sede"])) {
            $respuesta["mensaje"] = $textos->id("ERROR_FALTA_SEDE");
            
        } else if (empty($datos["privilegios"])) {
            $respuesta["mensaje"] = $textos->id("ERROR_FALTA_PRIVILEGIOS");
            
        } else if ($sql->existeItem("permisos_modulos_usuarios", "id_usuario", $datos["usuario"], "id_sede = ".$datos["sede"])) {
            $respuesta["mensaje"] = $textos->id("ERROR_EXISTE_USUARIO");
            
        } else {
           if ($objeto->adicionar($datos)) {
                $respuesta["error"]   = false;
                $respuesta["accion"]  = "recargar";
                
            } else {
                $respuesta["mensaje"] = $textos->id("ERROR_DESCONOCIDO");
                
            }
            
        }
        
    }
    
    Servidor::enviarJSON($respuesta);
    
}

 /**
 * Funcion que muestra la ventana modal de consulta para un item
 * 
 * @global objeto $textos   = objeto global encargado de la traduccion de los textos     
 * @param int $id           = id del item a consultar 
 */
function consultarItem($id, $sede) {
    global $textos, $configuracion;
    
    $objeto    = new Privilegios($id);

    $codigo  = HTML::campoOculto("procesar", "true");
    $codigo .= HTML::parrafo($textos->id("USUARIO"), "negrilla margenSuperior");
    $codigo .= HTML::frase($objeto->usuario);
    $codigo .= HTML::parrafo($textos->id("NOMBRE"), "negrilla margenSuperior");
    $codigo .= HTML::frase($objeto->nombre);
    $codigo .= HTML::parrafo($textos->id("SEDE"), "negrilla margenSuperior");
    $codigo .= HTML::frase($objeto->sede);
    $codigo .= HTML::parrafo($textos->id("PRIVILEGIOS"), "negrilla margenSuperior");
    $codigo .= $objeto->listaPrivilegios($objeto->id, $objeto->id_sede, "consultar");

    $respuesta["generar"]   = true;
    $respuesta["codigo"]    = $codigo;
    $respuesta['cargarJs']  = true;
    $respuesta['archivoJs'] = $configuracion['SERVIDOR']['media'] . $configuracion['RUTAS']['javascript'] . '/modulos/privilegios/funcionesConsultarPrivilegios.js';
    $respuesta["destino"]   = "#cuadroDialogo";
    $respuesta["titulo"]    = HTML::parrafo($textos->id("CONSULTAR_PRIVILEGIOS"), "letraBlanca negrilla");
    $respuesta["ancho"]     = 600;
    $respuesta["alto"]      = 500;
    
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
function modificarItem($id, $sede, $datos = array()) {
    global $textos, $configuracion;

    $objeto    = new Privilegios($id);
    $destino        = "/ajax".$objeto->urlBase."/edit";
    $respuesta      = array();

    if (empty($datos)) {    
        $codigo  = HTML::campoOculto("procesar", "true");
        $codigo .= HTML::campoOculto("id", $id);
        $codigo .= HTML::parrafo($textos->id("USUARIO"), "negrilla margenSuperior");
        $codigo .= HTML::frase($objeto->usuario);
        $codigo .= HTML::parrafo($textos->id("NOMBRE"), "negrilla margenSuperior");
        $codigo .= HTML::frase($objeto->nombre);
        $codigo .= HTML::parrafo($textos->id("SEDE"), "negrilla margenSuperior");
        $codigo .= HTML::frase($objeto->sede);
        $codigo .= HTML::parrafo($textos->id("PRIVILEGIOS"), "negrilla margenSuperior");
        $codigo .= $objeto->listaPrivilegios($objeto->id, $objeto->id_sede, "modificar");
        $codigo .= HTML::parrafo(HTML::boton("chequeo", $textos->id("ACEPTAR")), "margenSuperior");
        $codigo  = HTML::forma($destino, $codigo);

        $respuesta["generar"]   = true;
        $respuesta["codigo"]    = $codigo;
        $respuesta['cargarJs']  = true;
        $respuesta['archivoJs'] = $configuracion['SERVIDOR']['media'] . $configuracion['RUTAS']['javascript'] . '/modulos/privilegios/funcionesEditarPrivilegios.js';
        $respuesta["destino"]   = "#cuadroDialogo";
        $respuesta["titulo"]    = HTML::parrafo($textos->id("MODIFICAR_ITEM"), "letraBlanca negrilla");
        $respuesta["ancho"]     = 600;
        $respuesta["alto"]      = 550;
        
    } else {
        $respuesta["error"]   = true;

        if (empty($datos["privilegios"])) {
            $respuesta["mensaje"] = $textos->id("ERROR_FALTA_PRIVILEGIOS");
            
        } else {
            if ($objeto->modificar($datos)) {
                $respuesta["error"]   = false;
                $respuesta["accion"]  = "recargar";
                
            } else {
                $respuesta["mensaje"] = $textos->id("ERROR_DESCONOCIDO");
                
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
function eliminarItem($id, $confirmado) {
    global $textos;

    $objeto    = new Privilegios($id);
    $destino = "/ajax".$objeto->urlBase."/delete";

    if (!$confirmado) {
        $nombre  = HTML::frase($objeto->nombre, "negrilla");
        $sede    = HTML::frase($objeto->sede, "negrilla");
        $nombre  = str_replace("%1", $nombre, $textos->id("CONFIRMAR_ELIMINACION_ITEM"));
        $nombre  = str_replace("%2", $sede, $nombre);
        $codigo  = HTML::campoOculto("procesar", "true");
        $codigo .= HTML::campoOculto("id", $id);
        $codigo .= HTML::parrafo($nombre);
        $codigo .= HTML::parrafo(HTML::boton("chequeo", $textos->id("ACEPTAR")), "margenSuperior");
        $codigo  = HTML::forma($destino, $codigo);

        $respuesta["generar"] = true;
        $respuesta["codigo"]  = $codigo;
        $respuesta["destino"] = "#cuadroDialogo";
        $respuesta["titulo"]  = HTML::parrafo($textos->id("ELIMINAR_ITEM"), "letraBlanca negrilla");
        $respuesta["ancho"]   = 350;
        $respuesta["alto"]    = 140;

    } else {
        if ($objeto->eliminar()) {
            $respuesta["error"]   = false;
            $respuesta["accion"]  = "recargar";
            
        } else {
            $respuesta["error"]   = true;
            $respuesta["mensaje"] = $textos->id("ERROR_DESCONOCIDO");
            
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
        $objeto         = new Privilegios();
        $registros      = $configuracion['GENERAL']['registrosPorPagina'];
        
        if (!empty($cantidadRegistros)) {
            $registros = (int) $cantidadRegistros;
            
        }        
        $pagina             = 1;
        $registroInicial    = 0;


        $palabras = explode(' ', $datos);

        $condicionales = $data[1];

        if ($condicionales == '') {
            $condicion = '(u.usuario REGEXP "(' . implode('|', $palabras) . ')")';
            
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

        $arregloItems = $objeto->listar($registroInicial, $registros, array('0'), $condicion, 'u.usuario');

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
    $objeto         = new Privilegios();

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
            $consultaGlobal = '(u.usuario REGEXP "(' . implode('|', $palabras) . ')")';
            
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
 * Funcion llamada cuando se selecciona un usuario desde el formulario de "adicionar privilegios" y esta
 * encargada de consultar las sedes donde el usuario ya tiene privilegios y removerlas del arreglo de sedes
 * de la empresa, para asi solo mostrar las sedes donde el usuario no tiene privilegios.
 * 
 * @param int $idUsuario identificador del usuario que se va a usar para la consulta
 */
function cargarSedesSinPrivilegios($idUsuario){
    global $sql;
    /**
     * Seleccionar las sedes donde el usuario ya tiene permisos y remover estas del selector de sedes
     */
    $sedesConPermisos = array();

    $query  = $sql->seleccionar(array("permisos_modulos_usuarios"), array("id_sede"), "id_usuario ='".$idUsuario."' ", "id_sede", "id_sede ASC");
    
    if($sql->filasDevueltas){
        while ($dato = $sql->filaEnObjeto($query)) {
            $sedesConPermisos[] = $dato->id_sede;
        }
    }
    if (!empty($sedesConPermisos)) {
        $condicion = 'id NOT IN (0, '.implode(",", $sedesConPermisos).')';
    } else {
        $condicion = 'id NOT IN (0)';
    }
    
    $sedesEmpresa = $sql->seleccionar(array('sedes_empresa'), array('id', 'nombre'), $condicion, 'id', 'nombre ASC');
    
    $sedes = array();
    
    while ($obj = $sql->filaEnObjeto($sedesEmpresa)) {
            $sedes[$obj->id] = $obj->nombre;
    }
    
    $respuesta['sedes'] = $sedes;

    Servidor::enviarJSON($respuesta);    
    
}