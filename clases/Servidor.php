<?php
/**
 * Servidor.php, archivo que forma parte del núcleo del framework.
 *
 * @package     FOLCS
 * @subpackage  Base
 * @author      Francisco J. Lozano B. <fjlozano@felinux.com.co>
 * @author      Pablo Andrés Vélez Vidal <pavelez@genesyscorp.com.co>
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2009 FELINUX LTDA
 * @version     0.1
 *
 **/

/**
 * Clase Servidor. Forma parte del nucleo del framework. Es utilizada para la gestión 
 * del comportamiento del servidor web. En esta clase se gestionan comportamientos como 
 * la gestión de los arreglos globales ($_SESSION, $_POST, $_GET, $_FILES), envío de correos 
 * y notificaciones.
 * También se utiliza para la comunicación asincronica entre el cliente y el servidor
 *
 **/
class Servidor {

    /**
     * Representa un objeto de esta clase para ser devuelto por el metodo iniciar
     * si ya existe en memoria
     * @var objeto 
     */
    private static $instancia = NULL;

    /**
     * se captura la ip del cliente que realiza la petición y se almacena en esta variable
     * @var string 
     */
    public static $cliente;
    
    /**
     * se captura el proxy utilizado por el cliente que realiza la petición y se almacena en esta variable
     * @var string 
     */
    public static $proxy;

    /**
     * Nombre completo para mostrar como remitente del correo electrónico
     * @var cadena
     */
    private static $nombreRemitenteCorreo;

    /**
     * Dirección para mostrar como remitente del correo electrónico
     * @var cadena
     */
    private static $direccionRemitenteCorreo;

    /**
     * Metodo constructor
     * 
     * Metodo constructor, en este caso no se asignan valores al objeto directamente 
     * durante la instanciación, pero se deja declarado el metodo siguiendo ciertas practicas
     * de programación que recomiendan siempre declarar el metodo constructor
     * 
     */
    private function __construct() {}

    /**
     * Función que devuelve una instancia de esta clase
     *
     * Devuelve una instancia de esta clase, de ya existir en memoria devuelve la
     * instancia existente.
     *
     * @return                  objeto  instancia de esta misma clase
     */
    public static function iniciar() {

        if (self::$instancia == NULL) {
            self::$instancia = new Servidor;
        }

        return self::$instancia;
    }

    /**
     * Función encargada de leer y reescribir los arreglos superglobales
     * 
     * Función encargada de leer y reescribir los arreglos superglobales y reescribir
     * variables globales por cada una de sus posiciones con valor. Ejemplo: 
     * la posicion del arreglo superglobal $_SESSION['id_usuario'] = '12' sera reescrita y
     * generaría una variable global $sesion_id_usuario = '12'.
     * 
     * @return  variables globales reescritas apartir de los arreglos super globales
     */
    public static function exportarVariables() {

        if (empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            self::$cliente = $_SERVER["REMOTE_ADDR"];
            self::$proxy = "";
        } else {
            self::$cliente = $_SERVER["HTTP_X_FORWARDED_FOR"];
            self::$proxy   = $_SERVER["REMOTE_ADDR"];
        }

        if (isset($_POST)) {

            foreach ($_POST as $variable => $valor) {

                if (!get_magic_quotes_gpc()) {

                    if (!is_array($valor)) {
                        $valor = addslashes(Variable::codificarCadena($valor));

                    } else {
                        $valor = Variable::codificarArreglo($valor);
                    }
                }

                $nombre  = "forma_$variable";
                global $$nombre;
                $$nombre = $valor;
            }
        }

        if (isset($_GET)) {

            foreach ($_GET as $variable => $valor) {

                if (!get_magic_quotes_gpc()) {

                    if (!is_array($valor)) {
                        $valor = addslashes(Variable::codificarCadena($valor));

                    } else {
                        $valor = Variable::codificarArreglo($valor);
                    }
                }

                $nombre  = "url_$variable";
                global $$nombre;
                $$nombre = $valor;
            }
        }     
        
        if (isset($_FILES)) {

            foreach ($_FILES as $variable => $valor) {

                if (!get_magic_quotes_gpc()) {

                    if (!is_array($valor)) {
                        $valor = addslashes(Variable::codificarCadena($valor));

                    } else {
                        $valor = Variable::codificarArreglo($valor);
                    }
                }

                $nombre  = "archivo_$variable";
                global $$nombre;
                $$nombre = $valor;
            }
        }

        if (isset($_COOKIES)) {

            foreach ($_COOKIES as $variable => $valor) {

                if (!get_magic_quotes_gpc()) {

                    if (!is_array($valor)) {
                        $valor = addslashes(Variable::codificarCadena($valor));

                    } else {
                        $valor = Variable::codificarArreglo($valor);
                    }
                }

                $nombre  = "cookie_$variable";
                global $$nombre;
                $$nombre = $valor;
            }
        }
    }


    /**
     * Enviar contenido JSON al cliente desde el servidor
     * 
     * Función encargada de codificar una cadena o arreglo de cadenas para enviar 
     * en formato JSON desde el servidor al cliente.
     * 
     * @param string $datos puede ser una cadena o un arreglo de cadenas a ser codificados
     * en formato JSON
     */
    public static function enviarJSON($datos) {

        if (is_array($datos)) {
            foreach($datos as $id => $value){
                if(is_array($value)){
                    $datos[$id] = Recursos::array_map_recursive("utf8_encode", $datos[$id]);
                    
                }else{
                    $datos[$id] = utf8_encode($datos[$id]);
                    
                }
                
            }            

        } else {
            $datos = utf8_encode($datos);
        }

        header("Content-type: application/json");
        echo @json_encode($datos);
        
        
    }

    /**
     * Enviar contenido HTML desde el servidor al cliente
     * 
     * Codificar una cadena o arreglo de cadenas para enviar en formato HTML, 
     * esta función hace una llamada al metodo generarCodigo de la clase Plantilla
     * e imprime el contenido de su variable estatica $contenido.
     */
    public static function enviarHTML() {      
        Plantilla::generarCodigo();
        echo Plantilla::$contenido;
    }

    /**
     * Función encargada de enviar un mensaje por correo electrónico.
     * 
     * Función encargada de enviar un mensaje por correo electrónico a los usuarios 
     * del sistema.
     * 
     * @global array $configuracion arreglo global de configuración no parametrizable
     * @param string $destino correo de destino del envio de correo
     * @param string $asunto asunto del correo
     * @param string $contenido contenido del correo
     * @param string $nombre nombre del destinatario
     * @return boolean true or false dependiendo del exito de la operación
     */
    public static function enviarCorreo($destino, $asunto, $contenido, $nombre = NULL) {
        global $configuracion;

        $envio = NULL;

        self::$nombreRemitenteCorreo    = $configuracion["SERVIDOR"]["nombreRemitente"];
        self::$direccionRemitenteCorreo = $configuracion["SERVIDOR"]["correoRemitente"];

        if (isset($destino) && filter_var($destino, FILTER_VALIDATE_EMAIL) && isset($asunto) && isset($contenido)) {

            if (isset($nombre)) {
                $destino = trim($nombre)." <".$destino.">\r\n";
            }

            $cabecera .= "MIME-Version: 1.0\r\n";
            $cabecera .= "Content-type: text/html; charset=".$configuracion["SERVIDOR"]["codificacion"]."\r\n";
            $cabecera  = "From: ".self::$nombreRemitenteCorreo." <".self::$direccionRemitenteCorreo.">\r\n";
            $cabecera .= "To: $destino\r\n";
            $envio     = mail("", trim($asunto), $contenido, $cabecera, "-f".self::$direccionRemitenteCorreo);
        }

        return $envio;
    }

    /**
     * Función encargada de generar notificaciones
     * 
     * Función encargada de generar notificaciones para un usuario del sistema, estas son 
     * las notificaciones que aparecen en la pagina principal del usuario
     * 
     * @global object $sql objeto global de interacción con la BD
     * @param int $usuario identificador del usuario al que se le va a hacer la notificación
     * @param string $mensaje mensaje de la notificación
     * @param array $variables arreglo de valores a ser reemplazados en el mensaje de la notificación
     * @param int $idModulo identificador del módulo de donde proviene la notificación
     * @param int $idRegistro identificador del registro con el que esta asociada la notificación
     * @return int|boolean en caso de exito devuelve el ultimo id insertado, si falla devuelve false
     */
    public static function notificar($usuario, $mensaje, $variables = array(), $idModulo = NULL, $idRegistro = NULL) {
        global $sql;

        foreach ($variables as $variable => $valor) {
            $mensaje = preg_replace("/$variable/", $valor, $mensaje);
        }

        $datos = array(
            "id_usuario"  => $usuario,
            "fecha"       => date("Y-m-d H:i:s"),
            "contenido"   => $mensaje,
            "leido"       => "0",
            'id_modulo'   => $idModulo,
            'id_registro' => $idRegistro
        );
        
        $sql->insertar("notificaciones", $datos);

        return $sql->ultimoId;
    }

}
