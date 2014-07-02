<?php

/**
 *
 * @package     FOLCS
 * @subpackage  Permisos Item
 * @author      Pablo Andrés Vélez Vidal <pavelez8@misena.edu.co>
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2012 Genesys
 * @version     0.1
 *
 **/

class Permisos{

    public function verificarPermisos($idModulo, $componente) {
        global $sesion_usuarioSesion, $sql;

        if ($sesion_usuarioSesion->id != 0) {
            $tablas    = array('pcu' => 'permisos_componentes_usuarios', 'cm' => 'componentes_modulos');
            $columnas  = array('cm.id');
            $condicion = "cm.id_modulo = '$idModulo' AND cm.componente = '$componente' AND cm.id = pcu.id_componente AND pcu.id_usuario = '$sesion_usuarioSesion->id'";
            
            $sql->seleccionar($tablas, $columnas, $condicion);
            if ($sql->filasDevueltas) {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }//fin del metodo verificarPermisos
}//fin de la clase permisos