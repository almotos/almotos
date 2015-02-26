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

    $loader = new Twig_Loader_Filesystem(__DIR__.'/plantillas/');

    $twig = new Twig_Environment($loader, array(
        //'cache' => __DIR__.'/plantillas_c/',
    ));
    
    //listar las sedes
    $listaSedes = array();
    
    $consulta = $sql->seleccionar(array('sedes_empresa'), array('id', 'nombre'), 'id !="0"', '', 'nombre ASC');

    if ($sql->filasDevueltas) {
        while ($dato = $sql->filaEnObjeto($consulta)) {
            $listaSedes[$dato->id] = $dato->nombre;
        }
    }    
    
    //listar las cajas
    $listaCajas = array();//arreglo que almacenará el listado de cajas y será pasado como parametro al metodo HTML::listaDesplegable
    
    $consulta = $sql->seleccionar(array('cajas'), array('id', 'nombre'), 'id_sede = "' . $sesion_usuarioSesion->sede->id . '" AND id !="0"', '', 'nombre ASC');//consulto las cajas de la sede actual del usuario

    if ($sql->filasDevueltas) {
        while ($dato = $sql->filaEnObjeto($consulta)) {//recorro las respuesta, y voy guardandolas en el arreglo
            $listaCajas[$dato->id] = $dato->nombre;
        }
    }

    $idCajaPrincipal = $sql->obtenerValor('cajas', 'id', 'id_sede = "' . $sesion_usuarioSesion->sede->id . '" ANd principal = "1"');    
    
    $opciones   = array('cajas'         => $listaCajas,
                        'sedes'         => $listaSedes,
                        'cajaPrincipal' => $idCajaPrincipal
                        );
    
    $contenido = $twig->render('principal.html', $opciones);    

} else {
    $contenido .= HTML::contenedor($textos->id('SIN_PERMISOS_ACCESO_MODULO'), 'textoError');

}

Plantilla::$etiquetas['BLOQUE_CENTRAL'] = $contenido;
