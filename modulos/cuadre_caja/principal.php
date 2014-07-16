<?php

/**
 *
 * @package     FOM
 * @subpackage  Cuadre de caja
 * @author      Pablo Andrés Vélez Vidal <pavelez8@misena.edu.co>
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2014 GENESYS Corporation.
 * @version     0.2
 *
 * */

global $sesion_usuarioSesion, $modulo, $configuracion, $textos, $sql, $sesion_configuracionGlobal;

$tituloBloque   = $textos->id('MODULO_ACTUAL');
$codigo         = '';

/**
 * Verifico que se haya iniciado una sesion y que tenga permisos para ver el modulo
 * */
if ((isset($sesion_usuarioSesion) && Perfil::verificarPermisosModulo($modulo->id)) || isset($sesion_usuarioSesion) && $sesion_usuarioSesion->idTipo == 0) {

    $fechaInicial  = HTML::parrafo($textos->id('FECHA_INICIAL'), 'margenSuperior negrilla');
    $fechaInicial .= HTML::campoTexto("datos[fecha_inicial]", 12, 12, '', "fechaAntigua", "fechaInicioCuadre", array("alt" => $textos->id("SELECCIONE_FECHA_INICIAL")));
    $fechaFinal    = HTML::parrafo($textos->id('FECHA_FINAL'), 'margenSuperior negrilla');
    $fechaFinal   .= HTML::campoTexto("datos[fecha_final]", 12, 12, '', "fechaAntigua", "fechaFinCuadre", array("alt" => $textos->id("SELECCIONE_FECHA_FINAL")));

    $listaSedes = array();

    $consulta = $sql->seleccionar(array('sedes_empresa'), array('id', 'nombre'), 'id !="0"', '', 'nombre ASC');

    if ($sql->filasDevueltas) {
        while ($dato = $sql->filaEnObjeto($consulta)) {
            $listaSedes[$dato->id] = $dato->nombre;
        }
    }   

    $selectorSedes = HTML::listaDesplegable('datos[sede]', $listaSedes, $sesion_usuarioSesion->sede->id, 'selectChosen', 'selectorSedes', '', array("onchange" => "seleccionarCajas($(this))"), '');    

    $listaCajas = array();//arreglo que almacenará el listado de cajas y será pasado como parametro al metodo HTML::listaDesplegable
    $consulta = $sql->seleccionar(array('cajas'), array('id', 'nombre'), 'id_sede = "' . $sesion_usuarioSesion->sede->id . '" AND id !="0"', '', 'nombre ASC');//consulto las cajas de la sede actual del usuario

    if ($sql->filasDevueltas) {
        while ($dato = $sql->filaEnObjeto($consulta)) {//recorro las respuesta, y voy guardandolas en el arreglo
            $listaCajas[$dato->id] = $dato->nombre;
        }
    }

    $idCajaPrincipal = $sql->obtenerValor('cajas', 'id', 'id_sede = "' . $sesion_usuarioSesion->sede->id . '" ANd principal = "1"');    

    $selectorCajas = HTML::listaDesplegable('datos[caja]', $listaCajas, $idCajaPrincipal, 'margenIzquierdaDoble ', 'selectorCajas', '', array(), '');

    $checkFiltroCaja = HTML::campoChequeo("datos[filtrar_todas_cajas]", false, '', 'filtrarTodasCajas', array('onclick' => 'filtrarTodasCajas($(this))'));
    
    $parrafo1  = HTML::frase($textos->id("USAR_TODAS_LAS_CAJAS"), "claseTextoReporte");
    $parrafo2  = HTML::frase($textos->id("USAR_CAJA_PARTICULAR").$checkFiltroCaja, "claseTextoReporte");
    
    $filtrarPorCaja .= HTML::contenedor($parrafo1.$parrafo2, 'checkFiltroCaja');

    //filtros predeterminados: se agrega el texto y un selector para "últimos X Tiempo"
    $selectorUltimos = HTML::frase($textos->id("SELECCIONE_ULTIMOS"), "claseTextoReporte", "idTextoUltimos");
    
    $arregloTiempos = array(
                        ""               => $textos->id("SELECCIONAR"),
                        "dia"            => $textos->id("DIA"),
                        "semana"         => $textos->id("SEMANA"),
                        "mes"            => $textos->id("MES"),
                        "tres_meses"     => $textos->id("TRES_MESES"),
                        "seis_meses"     => $textos->id("SEIS_MESES"),
                        "año"            => $textos->id("ANYO")
                    );
    
    $selectorUltimos .= HTML::listaDesplegable("selectorTiempos", $arregloTiempos, "", "claseSelectorTiempos", "idSelectorTiempos");
    
    $check = HTML::campoChequeo("rango_personalizado", false, "rangoPersonalizado", 'rangoPersonalizado', array('onclick' => 'mostrarContenedorRangoFechas($(this))'));
    
    $selectorUltimos .= HTML::parrafo($textos->id("RANGO_FECHA_PERSONALIZADO").$check, "textoRangoPersonalizado negrita claseTextoReporte", "textoRangoPersonalizado");
    
    
    $codigo .= HTML::contenedor($selectorUltimos, "claseContenedorUltimos", "idContenedorUltimos");
    
    //formulario para generar el reporte en un rango de fechas parametrizado, por defecto esta oculto
    $codigo .= HTML::contenedorCampos($fechaInicial, $fechaFinal, 'contenedorCuadreCaja oculto', 'contenedorRangoFechas');
    
    //check para usar todas las cajas en la consulta
    
    $codigo .= $filtrarPorCaja;
    
    //codigo que muestra los selectores de sedes y cajas
    $sedesAndCajas = HTML::contenedorCampos($selectorSedes, $selectorCajas, 'contenedorSelectorCajas oculto', 'contenedorSelectorCajas');
    
    $codigo .= $sedesAndCajas;
    
    $boton1 = HTML::boton('chequeo', $textos->id('GENERAR_CONSULTA'), 'directo margenSuperiorDoble', '', 'botonConsultarCuadreCaja', '', array('validar' => 'NoValidar', 'onclick' => 'consultarCuadreCaja($(this))'));
    
    $codigo .= HTML::contenedor($boton1, 'wrapperBotonGenerar');

    
    $codigo = HTML::contenedor($codigo, "wrapper", "wrapper");
    
    $contenido .= HTML::bloque('bloqueContenidoPrincipal', $tituloBloque, $codigo, '', 'overflowVisible');

} else {
    $contenido .= HTML::contenedor($textos->id('SIN_PERMISOS_ACCESO_MODULO'), 'textoError');

}

Plantilla::$etiquetas['BLOQUE_CENTRAL'] = $contenido;
