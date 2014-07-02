<?php

/**
 * Sesion.php, clase del núcleo del framework
 *
 * @package     FOLCS
 * @subpackage  Base
 * @author      Francisco J. Lozano B. <fjlozano@felinux.com.co>
 * @author      Julian A. Mondragón <jmondragon@felinux.com.co>
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2009 FELINUX LTDA
 * @version     0.1
 *
 **/

/**
 * Clase Sesión, encargada como su nombre lo indica de la gestión de las sesiones
 * en la aplicación. A través de sus metodos se pueden crear, consultar, modificar
 * y destruir sesiones PHP dentro del framework. Como se puede apreciar a lo largo de
 * la aplicación, las variables super globales son leidas y reescritas, lo mismo sucede 
 * con el arreglo super global $_SESSION, todas y cada una de sus posiciones son leidas
 * y reescritas, así, $_SESSION['nombreUsuario'] sera reescrita a $sesion_nombreUsuario.
 * Segun lo dicho, los valores de las sesiones pueden ser accedidos a través de variables
 * globales normales y su nombre siempre iniciara por $sesion_.
 * 
 * Nota: para utilizar una variable de sesión en cualquier función, dicha variable debe ser
 * previamente declarada como global, por ejemplo:
 * 
 * Situación real: En el framework por defecto cuando un usuario se loguea se crea una variable
 * de sesión llamada usuarioSesion que contiene un objeto de la clase usuario con toda la información 
 * del usuario que inicio la sesión. Esto significa que en el arreglo super global existe la posición
 * $_SESSION['usuarioSesion'], y dentro del framework existe la variable global $sesion_usuarioSesion.
 * 
 * Según lo anterior, si dentro de una función necesito almacenar el usuario que realiza alguna acción
 * usaria por ejemplo:
 * 
 * function almacenarRegistro($registro){
 *  global $sesion_usuarioSesion;
 * 
 *  //código que inserta el registro y usa "$sesion_usuarioSesion->id" para almacenar que usuario realizo la
 * // acción
 * 
 * }
 */
class Sesion {
    
    /**
     *
     * @var int identificador de la sesión 
     */
    public  static $id;


    /**
     * Iniciar la sesión, como su nombre lo indica, se encarga de iniciar la sesión y 
     * tambien se establece los límites de vida de la misma
     * 
     * @global string $nombre
     */
    public static function iniciar() {
        if (self::$id == "") {
            ini_set("session.cookie_lifetime",108000); 
            ini_set("session.gc_maxlifetime", 108000);
            session_start();
            
        }

        self::$id = session_id();

        foreach ($_SESSION as $variable => $valor) {
            $nombre  = "sesion_".$variable;
            global $$nombre;
            $$nombre = $valor;
        }
        
    }

    /**
     * Finalizar la sesión
     */
    public static function terminar() {
        self::destruir(self::$id);
        
    }

    /**
     * Registrar una variable en la sesión. Recibe dos parametros, uno es el
     * nombre que va a tener la variable y la otra es su valor, asi, por ejemplo
     * si quiero almacenar una variable llamada nombre con valor Pablo, lo haría
     * así: Sesion::registrar("nombre", "Pablo"). con esto ya tendría el texto
     * "Pablo" en $sesion_nombre;
     * 
     * @global string $variable el nombre de la posición
     * @global string|object|array $valor el valor a ser almacenado en la sesión
     * @param object $variable el valor en el super global $_SESSION y en la variable global $sesion_XXXx
     */
    public static function registrar($variable, $valor = "") {
        global $$variable;

        if (isset($valor)) {
            $$variable = $valor;
        }

        $nombre = "sesion_".$variable;

        if (isset($$variable)) {
            global $$nombre;

            $$nombre               = $$variable;
            $_SESSION["$variable"] = $$variable;
        }
        
    }



    /*** Eliminar una variable de sesión ***/
    public static function borrar($variable) {
        $nombre = "sesion_".$variable;

        global $$nombre;

        if (isset($$nombre)) {
            unset($$nombre);
            unset($_SESSION["$variable"]);
            
        }
        
    }

    /*** Escribir los datos de una sesión ***/
    public static function escribir($id, $contenido) {
        global $sesion_usuarioSesion;

        if (isset($sesion_usuarioSesion) && is_object($sesion_usuarioSesion)) {
//            $actualizaUsuario = self::$sql->modificar("sesiones", array("id_usuario" => $sesion_usuarioSesion->id, "disponible" => "1"), "id = '$id'");

        } elseif (isset($_SESSION["usuarioSesion"])) {
            $usuario          = $_SESSION["usuarioSesion"];
//            $actualizaUsuario = self::$sql->modificar("sesiones", array("id_usuario" => $usuario->id, "disponible" => "1"), "id = '$id'");
        }

        return $resultado;
    }

    /*** Destruir una sesión ***/
    public static function destruir($id) {

        foreach ($_SESSION as $variable => $valor) {
            unset($_SESSION[$variable]);
        }

        unset($_SESSION);

        return true;
        
    }


}
