<?php

/**
 * @package     FOLCS
 * @subpackage  Empresas
 * @author      Pablo Andrés Vélez Vidal <pavelez8@misena.edu.co>
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2012 Genesys corporation.
 * @version     0.2
 * */
global $url_accion, $forma_procesar, $forma_id, $forma_datos;


if (isset($url_accion)) {
    switch ($url_accion) {
        case "see"      :   cosultarItem($forma_id);
                            break;
                        
        case "edit"     :   ($forma_procesar) ? $datos = $forma_datos : array();
                            modificarItem($forma_id, $datos);
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

    if (!isset($id) || (isset($id) && !$sql->existeItem("empresas", "id", $id))) {
        $respuesta              = array();
        $respuesta["error"]     = true;
        $respuesta["mensaje"]   = $textos->id("NO_HA_SELECCIONADO_ITEM");

        Servidor::enviarJSON($respuesta);
        return NULL;
    }

    $objeto = new Empresa($id);
    $respuesta = array();

    $pestana1  = HTML::parrafo($textos->id("NIT"), "negrilla margenSuperior");
    $pestana1 .= HTML::parrafo($objeto->nit, "", "");
    $pestana1 .= HTML::parrafo($textos->id("DIRECCION_PRINCIPAL"), "negrilla margenSuperior");
    $pestana1 .= HTML::parrafo($objeto->direccionPrincipal, "", "");
    $pestana1 .= HTML::parrafo($textos->id("TELEFONO"), "negrilla margenSuperior");
    $pestana1 .= HTML::parrafo($objeto->telefono, "", "");
    $pestana1 .= HTML::parrafo($textos->id("EMAIL"), "negrilla margenSuperior");
    $pestana1 .= HTML::parrafo($objeto->email, "", "");
    $pestana1 .= HTML::parrafo($textos->id("PAGINA_WEB"), "negrilla margenSuperior");
    $pestana1 .= HTML::parrafo($objeto->paginaWeb, "", "");
    $pestana1 .= HTML::parrafo($textos->id("NOMBRE"), "negrilla margenSuperior");
    $pestana1 .= HTML::parrafo($objeto->nombre, "", "");
    $pestana1 .= HTML::parrafo($textos->id("NOMBRE_ORIGINAL"), "negrilla margenSuperior");
    $pestana1 .= HTML::parrafo($objeto->nombreOriginal, "", "");
    $pestana1 .= HTML::enlace(HTML::imagen($objeto->imagenMiniatura, 'imagenItem', ''), $objeto->imagenPrincipal, '', '', array('rel' => 'prettyPhoto[]'));
    
    
    $pestana2  = HTML::parrafo($textos->id("INGRESO_MERCANCIA"), "negrilla margenSuperior");
    $pestana2 .= HTML::parrafo($textos->id("INGRESO_MERCANCIA_" . $objeto->ingresoMercancia), "", "");
    $pestana2 .= HTML::parrafo($textos->id("REGIMEN"), "negrilla margenSuperior");
    $pestana2 .= HTML::parrafo($textos->id("REGIMEN_" . $objeto->regimen), "", "");
    $pestana2 .= HTML::parrafo($textos->id('ACTIVIDAD_ECONOMICA') , 'negrilla margenSuperior');
    $pestana2 .= HTML::parrafo($objeto->actividadEconomica->nombre, 'margenDerecha', ''); 
    $pestana2 .= HTML::parrafo($textos->id('BASE_RETEFUENTE') , 'negrilla margenSuperior');
    $pestana2 .= HTML::parrafo($objeto->baseRetefuente, 'margenDerecha', ''); 
//    $codigo .= HTML::parrafo($textos->id("RETIENE_FUENTE"), "negrilla margenSuperior");
//    $codigo .= HTML::parrafo($textos->id("RETIENE_FUENTE_" . $objeto->retieneFuente), "", "");
//    $codigo .= HTML::parrafo($textos->id("RETIENE_ICA"), "negrilla margenSuperior");
//    $codigo .= HTML::parrafo($textos->id("RETIENE_ICA_" . $objeto->retieneIca), "", "");
//    $codigo .= HTML::parrafo($textos->id("RETIENE_IVA"), "negrilla margenSuperior");
//    $codigo .= HTML::parrafo($textos->id("RETIENE_IVA_" . $objeto->retieneIva), "", "");
//    $codigo .= HTML::parrafo($textos->id("AUTORETENEDOR"), "negrilla margenSuperior");
//    $codigo .= HTML::parrafo($textos->id("AUTORETENEDOR_" . $objeto->autoretenedor), "", "");
//    $codigo .= HTML::parrafo($textos->id("GRANCONTRIBUYENTE"), "negrilla margenSuperior");
//    $codigo .= HTML::parrafo($textos->id("GRANCONTRIBUYENTE_" . $objeto->grancontribuyente), "", "");

    $pestanas = array(
        HTML::frase($textos->id('INFORMACION_EMPRESA'), 'letraBlanca')       => $pestana1,
        HTML::frase($textos->id('INFORMACION_TRIBUTARIA'), 'letraBlanca')    => $pestana2,

    );

        $codigo .= HTML::pestanas2('pestanasAgregar', $pestanas);  
    $respuesta["generar"]   = true;
    $respuesta["codigo"]    = $codigo;
    $respuesta["titulo"]    = HTML::parrafo($textos->id("CONSULTAR_ITEM"), "letraBlanca negrilla subtitulo");
    $respuesta["destino"]   = "#cuadroDialogo";
    $respuesta["ancho"]     = 550;
    $respuesta["alto"]      = 450;



    Servidor::enviarJSON($respuesta);
}

/**
 *
 * @global type $textos
 * @global type $sql
 * @param type $id
 * @param type $datos 
 */
function modificarItem($id, $datos = array()) {
    global $textos, $archivo_imagen, $configuracion, $modulo, $sesion_usuarioSesion;
    
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

    $objeto     = new Empresa($id);
    $destino    = "/ajax" . $objeto->urlBase . "/edit";
    $respuesta  = array();

    if (empty($datos)) {
        $arregloRegimen = $configuracion["REGIMENES"][$configuracion["GENERAL"]["idioma"]];        

        $listaRegimen = HTML::listaDesplegable("datos[regimen]", $arregloRegimen, $objeto->regimen, "", "listaRegimen", "", array("alt" => $textos->id("SELECCIONE_REGIMEN")));

        $codigo    = HTML::campoOculto("procesar", "true");
        $codigo   .= HTML::campoOculto("id", $id);
        $codigo   .= HTML::campoOculto("datos[dialogo]", "", "idDialogo");
        
        $pestana1  = HTML::parrafo($textos->id("NIT"), "negrilla margenSuperior");
        $pestana1 .= HTML::campoTexto("datos[nit]", 30, 255, $objeto->nit);
        $pestana1 .= HTML::parrafo($textos->id("DIRECCION_PRINCIPAL"), "negrilla margenSuperior");
        $pestana1 .= HTML::campoTexto("datos[direccion_principal]", 30, 255, $objeto->direccionPrincipal);
        $pestana1 .= HTML::parrafo($textos->id("TELEFONO"), "negrilla margenSuperior");
        $pestana1 .= HTML::campoTexto("datos[telefono]", 30, 255, $objeto->telefono);
        $pestana1 .= HTML::parrafo($textos->id("EMAIL"), "negrilla margenSuperior");
        $pestana1 .= HTML::campoTexto("datos[email]", 30, 255, $objeto->email);
        $pestana1 .= HTML::parrafo($textos->id("PAGINA_WEB"), "negrilla margenSuperior");
        $pestana1 .= HTML::campoTexto("datos[pagina_web]", 30, 255, $objeto->paginaWeb);
        $pestana1 .= HTML::parrafo($textos->id("NOMBRE"), "negrilla margenSuperior");
        $pestana1 .= HTML::campoTexto("datos[nombre]", 30, 255, $objeto->nombre);
        $pestana1 .= HTML::parrafo($textos->id("NOMBRE_ORIGINAL"), "negrilla margenSuperior");
        $pestana1 .= HTML::campoTexto("datos[nombre_original]", 30, 255, $objeto->nombreOriginal);
        $pestana1 .= HTML::parrafo($textos->id('IMAGEN'), 'negrilla margenSuperior');
        $pestana1 .= HTML::campoArchivo('imagen', 50, 255). HTML::imagen($objeto->imagenMiniatura, 'imagenMarca', 'margenIzquierda');          
        
        $pestana2  = HTML::parrafo($textos->id("REGIMEN"), "negrilla margenSuperior");
        $pestana2 .= $listaRegimen;
        
        $pestana2 .= HTML::parrafo($textos->id('ACTIVIDAD_ECONOMICA'), 'negrilla margenSuperior');
        $pestana2 .= HTML::campoTexto('datos[actividad_economica]', 40, 255, $objeto->actividadEconomica->nombre, 'autocompletable campoObligatorio', 'campoActividadEconomica', array('title' => HTML::urlInterna('ACTIVIDADES_ECONOMICAS', 0, true, 'listar')), $textos->id('AYUDA_USO_AUTOCOMPLETAR'), HTML::urlInterna('ACTIVIDADES_ECONOMICAS', 0, true, 'add'), 'datos[id_actividad_economica]', $objeto->actividadEconomica->id);        
     
        $pestana2 .= HTML::parrafo($textos->id('BASE_RETEFUENTE'), 'negrilla margenSuperior');
        $pestana2 .= HTML::campoTexto('datos[base_retefuente]', 35, 50, $objeto->baseRetefuente, 'campoObligatorio soloNumeros', '', array(), $textos->id("AYUDA_BASE_RETEFUENTE")); 
        
        $check1    = HTML::campoChequeo("datos[ingreso_mercancia]", $objeto->ingresoMercancia, "margenIzquierda", "checkReteica");
        $pestana2 .= HTML::parrafo($textos->id("INGRESO_MERCANCIA") . $check1, "negrilla margenSuperior");
        
        
        $pestanas = array(
            HTML::frase($textos->id('INFORMACION_EMPRESA'), 'letraBlanca')       => $pestana1,
            HTML::frase($textos->id('INFORMACION_TRIBUTARIA'), 'letraBlanca')    => $pestana2,

        );

        $codigo .= HTML::pestanas2('pestanasAgregar', $pestanas);        
        
        $codigo .= HTML::parrafo(HTML::boton("chequeo", $textos->id("ACEPTAR")), "margenSuperior");
        $codigo .= HTML::parrafo($textos->id("REGISTRO_MODIFICADO"), "textoExitoso", "textoExitoso");
        $codigo  = HTML::forma($destino, $codigo);

        $respuesta["generar"]   = true;
        $respuesta["codigo"]    = $codigo;
        $respuesta["destino"]   = "#cuadroDialogo";
        $respuesta["titulo"]    = HTML::parrafo($textos->id("MODIFICAR_ITEM"), "letraBlanca negrilla subtitulo");
        $respuesta["ancho"]     = 550;
        $respuesta["alto"]      = 550;
        
    } else {
        $respuesta["error"] = true;
        
        if (!empty($archivo_imagen['tmp_name'])) {
            $validarFormato = Recursos::validarArchivo($archivo_imagen, array('jpg', 'jpeg'));
        }        

        if (empty($datos["nombre"])) {
            $respuesta["mensaje"] = $textos->id("ERROR_FALTA_NOMBRE");
            
        } else if (empty($datos["nit"])) {
            $respuesta["mensaje"] = $textos->id("ERROR_FALTA_NIT");
            
        } elseif ($validarFormato) {
            $respuesta['mensaje'] = $textos->id('ERROR_FORMATO_IMAGEN');
            
        } else if (empty($datos["direccion_principal"])) {
            $respuesta["mensaje"] = $textos->id("ERROR_FALTA_DIRECCION_PRINCIPAL");
            
        } else if (empty($datos["telefono"])) {
            $respuesta["mensaje"] = $textos->id("ERROR_FALTA_DIRECCION_PRINCIPAL");
            
        } else if (!empty($datos["email"]) && !filter_var($datos["email"], FILTER_VALIDATE_EMAIL)) {
            $respuesta["mensaje"] = $textos->id("ERROR_TIPO_EMAIL");
            
        } else if (!empty($datos["pagina_web"]) && !filter_var("http://" . $datos["pagina_web"], FILTER_VALIDATE_URL)) {
            $respuesta["mensaje"] = $textos->id("ERROR_TIPO_PAGINA_WEB");
            
        } else {
            $idItem = $objeto->modificar($datos);
            
            if ($idItem) {
                /*                 * ************** Creo el nuevo item que se insertara via ajax *************** */
                $objeto = new Empresa($id);
                $celdas = array($objeto->nombre, $objeto->nit, $objeto->nombreOriginal);
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
