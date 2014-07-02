<?php

/**
 *
 * @package     FOLCS
 * @subpackage  Base
 * @author      Francisco J. Lozano B. <fjlozano@felinux.com.co>
 * @author      Julian A. Mondragón <jmondragon@felinux.com.co>
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2009 FELINUX LTDA
 * @version     0.1
 *
 * */

/**
 * Clase encargada de gestionar la informacion sobre el modulo en particular sobre el que se
 * esta realizando la solicitud, es decir, es en esta clase donde se consulta la BD, en particular
 * se consulta la tabla modulo, la cual contiene toda la informacion de configuracion y de 
 * opciones para los modulos existentes en el sistema. esta clase consulta esa tabla para hacer
 * la carga de datos en el objeto modulo con la informacion obtenida desde la BD del registro en particular.
 * notese que se puede consultar a un modulo por cualquiera de sus ids, ya sea el id autonumerico, o la cadena
 * de texto,que debe de ser unica y en mayusculas que representa a el nombre, es decir, si estamos en el modulo
 * de bitacora, el id que se pasa al constructor de esta clase seria entonces la palabra BITACORA. este funcionamiento
 * permite cierta agilidad a la hora de crear un nuevo objeto de la clase modulo, ya que siempre es mas facil acordarse
 * del nombre, que del id numerico autoincremental que se encuentra en la base de datos.
 */
class Modulo {

    /**
     * Código interno o identificador del módulo en la base de datos
     * @var entero
     */
    public $id;

    /**
     * Clase a la cual pertenece el módulo
     * - 1: Configuración del sitio
     * - 2: Configuración personal
     * - 3: Uso global
     * - 4: e-learning
     * @var entero
     */
    public $clase;

    /**
     * nOMBRE DEL MODULO
     * @var cadena
     */
    public $nombre;

    /**
     * Texto que identifica un registro especíco del módulo a cargar o enlazar en una URL (Ej: 'news' en http://servidor/news/123)
     * @var cadena
     */
    public $url;

    /**
     * Carpeta en la que residen los archivos propios del módulo
     * @var cadena
     */
    public $carpeta;

    /**
     * El módulo aparece en los menús o listas de componentes
     * @var lógico
     */
    public $visible;

    /**
     * El módulo puede ser cargado sin verificar permisos
     * @var lógico
     */
    public $global;

    /**
     * Tabla principal con la que se relaciona el módulo
     * @var cadena
     */
    public $tabla;

    
    /**
     * Descripción y ayuda del modulo
     * @var cadena
     */
    public $documentacion;    
    
    /**
     * Inicializar el módulo especificado consultando en la bd el identificador segun el nombre
     * @param cadena $modulo Nombre único del módulo en la base de datos
     */
    public function __construct($modulo) {
        global $sql, $configuracion, $textos;
//        $sql = new SQL();

        /*         * * Hacer globales las variables procedentes de formularios y/o peticiones ** */
//        foreach ($GLOBALS as $variable => $valor) {
//
//            if (is_string($variable) && preg_match("/(^sesion_|^forma_|^url_|^cookies_|^archivo_)/", $variable)) {
//                global $$variable;
//            }
//        }

        $columnas = array(
            'id'            => 'id',
            'clase'         => 'clase',
            'orden'         => 'orden',
            'nombre'        => 'nombre',
            'url'           => 'url',
            'carpeta'       => 'carpeta',
            'visible'       => 'visible',
            'global'        => 'global',
            'tabla'         => 'tabla_principal',
            'documentacion' => 'documentacion',
            'validar'       => 'valida_usuario'
        );

        $consulta = $sql->seleccionar(array('modulos'), $columnas, 'BINARY nombre = "' . $modulo . '"');

        if ($sql->filasDevueltas) {

            $fila = $sql->filaEnObjeto($consulta);

            foreach ($fila as $propiedad => $valor) {
                $this->$propiedad = $valor;
            }

            $this->carpeta = $configuracion['RUTAS']['modulos'] . '/' . $this->carpeta;


            if (empty($textos)) {
                $textos = new Texto($modulo);
            }
        } else {

            if (empty($textos)) {
                $textos = new Texto();
            }
        }
    }

    /**
     * metodo llamado desde el archivo index php y que tiene dos funciones bastante importantes. 
     * la primera es hacer globales todas las variables que fueron reescritas desde los arreglos
     * globales $_GET, $_POST, $_FILES, $_SESION, $_COOKIE, para que puedan ser accedidas desde cua
     * lquier lugar de la aplicacion. y la segunda funcion importante es determinar si la pericion
     * que se esta realizando es una peticion ajax o una peticion normal, y asi determinar que archivo
     * de la carpeta del modulo incluir.
     * @global type $sql
     * @global type $peticionAJAX
     * @global type $configuracion
     * @global type $textos
     * @global type $parametros
     * @global type $sesion_usuarioSesion
     * @global type $variable
     */
    public function procesar() {
        global $sql, $peticionAJAX, $configuracion, $textos, $parametros, $sesion_usuarioSesion;

        /*         * * Hacer globales las variables procedentes de formularios y/o peticiones ** */
        foreach ($GLOBALS as $variable => $valor) {

            if (is_string($variable) && preg_match("/(^sesion_|forma_|^url_|^cookies_|^archivo_)/", $variable)) {
                global $$variable;
            }
        }

        if ($peticionAJAX) {
            /*             * * Cargar archivo manejador de peticiones AJAX ** */
            $archivo = $this->carpeta . '/' . $configuracion['MODULOS']['ajax'];
        } else {
            /*             * * Cargar archivo manejador de peticiones comúnes ** */
            $archivo = $this->carpeta . '/' . $configuracion['MODULOS']['principal'];
        }


//        if (file_exists($archivo) && is_readable($archivo)) {
            require_once $archivo;
//        }
    }

}

?>