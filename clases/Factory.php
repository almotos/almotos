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
        
        if (empty($clase)) {
            return FALSE;
        }
        
        if (empty($params)) {
            $params = NULL;
        }
        
        
        
        return new $clase($params);
        
//        switch ($clase) {
//            case "SqlGlobal":
//                return new $clase();
//
//                break;
//
//            default:
//                break;
//        }
        
    }
}

?>
