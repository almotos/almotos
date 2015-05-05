<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


/**
 * Description of Factory
 *
 * @author pablo
 */
class Factory
{
    
    public static function crearObjeto($clase, $params = array())
    {
        
        switch ($clase) {
            case "SqlGlobal":
                return new SqlGlobal();

                break;

            default:
                break;
        }
        
    }
}

?>
