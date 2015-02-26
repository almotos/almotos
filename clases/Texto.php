<?php

/**
 * Texto.php Clase del núcleo del framework.
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
 * Clase encargada de la traducción de los textos. Esta clase se encarga 
 * por cada petición que se realize a un modulo particular de buscar 
 * básicamente dos archivos e incluirlos en el código PHP. Uno de estos es el archivo general de idioma
 * (idiomas/es/general.php) y el archivo de idioma del módulo, llamado igual que el módulo, asi por
 * ejemplo en el caso de usuarios se encontrará un archivo /idiomas/es/usuarios.php que contendrá la información
 * de todos los textos de la aplicación en un arreglo que funciona en el modelo "LLAVE" => "Valor", según esto, a lo
 * largo de la aplicación encontraremos los textos de la siguiente forma:
 * $textos->id('NOMBRES'); -> si nos encontramos en el módulo de usuarios esto significa que o bien tengo un indice
 * "NOMBRES" en el archivo usuarios.php, o bien tengo un indice "NOMBRES" en el archivo general.php. En caso de no 
 * existir dicho indice, aparecera directamente el valor suministrado al metodo id(), en este caso "NOMBRES".
 */
class Texto {

    /**
     * Indicador del estado de carga de los textos generales
     * @var lógico
     */
    public $generales;


    /**
     * Lista de módulos para los cuales ya se han cargado los textos
     * @var arreglo
     */
    public $modulos;

    /**
     *
     * Inicializar el objeto con el contenido de los textos para el módulo especificado
     *
     * @param cadena $modulo    Nombre único del módulo en la base de datos
     *
     */
    function __construct($modulo = NULL) {
        global $configuracion, $sesion_idioma, $textos;

        if (empty($textos)) {
            $textos = array();
        }

        if (!$this->generales) {
            $archivo = $configuracion['RUTAS']['idiomas'].'/'.$sesion_idioma.'/'.$configuracion['RUTAS']['archivoGeneral'].'.php';

            if (file_exists($archivo) && is_readable($archivo)) {
                require_once $archivo;
            }

            foreach ($textos as $llave => $texto) {
                $this->{$llave} = $texto;
            }

            $this->generales = true;
        }

        if (!$this->modulos[$modulo]) {
            if (!empty($modulo)) {
                $archivo = $configuracion['RUTAS']['idiomas'].'/'.$sesion_idioma.'/'.strtolower($modulo).'.php';

                if (file_exists($archivo) && is_readable($archivo)) {
                    require_once $archivo;
                }

                foreach ($textos as $llave => $texto) {
                    $this->{$llave} = $texto;
                }
            }

            $this->modulos[$modulo] = true;
        }
    }

    /**
     *
     * Devuelve el texto asociado a la llave indicada
     *
     * @param  cadena $llave    Llave asociada al texto que se debe mostrar
     * @return cadena
     *
     */
    function id($llave) {

        if (isset($this->{$llave})) {
            return $this->{$llave};

        } else {
            return $llave;
        }
    }
    
}
