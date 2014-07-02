<?php

/**
 * Clase Archivo: encargada de interactuar con los archivos y el servidor
 * 
 * this is a new line
 *
 * @package     FOLCS
 * @subpackage  Base
 * @author      Pablo Andrés Vélez Vidal <pavelez@colomboamericano.edu.co>
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2011 Colombo-Americano Soft.
 * @version     0.2
 *
 * */
class Archivo {

    /**
     * Funcion encargada de subir un archivo al servidor.
     * 
     * @global array $configuracion         = arreglo global con datos de configuracion del sistema
     * @param recurso $archivo              = el archivo a ser cargado al servidor
     * @param string $configuracionRuta     = ruta de la carpeta en la que se debe guardar el archivo
     * @param array $identificador          = arreglo con datos de dimensiones para las imagenes (para poder realizar la redimension y crear la imagen miniatura)
     * @return string                       = ruta del archivo que se subio al servidor
     */
    public static function subirArchivoAlServidor($archivo, $configuracionRuta, $identificador = NULL) {
        global $configuracion;

        $formato        = strtolower(substr($archivo["name"], strrpos($archivo["name"], ".") + 1));
        $nombre         = substr(md5(uniqid(rand(), true)), 0, 8);
        $subcarpeta     = substr($nombre, 0, 2);
        $ruta           = $configuracionRuta . "/$subcarpeta/$nombre.$formato";


        while (file_exists($ruta)) {
            $nombre = substr(md5(uniqid(rand(), true)), 0, 8);
            $subcarpeta = substr($nombre, 0, 2);
            $ruta = $configuracionRuta . "/$subcarpeta/$nombre.$formato";
        }
        
        $rutaAdiciona = $subcarpeta . "/" . $nombre . "." . $formato;

        $ruta_carpeta = $configuracionRuta . "/$subcarpeta";

        if (!file_exists($ruta_carpeta)) {
            mkdir($configuracionRuta . "/$subcarpeta", 0777, true);
        }
        chmod($ruta_carpeta, 0777);


        $copiar = move_uploaded_file($archivo["tmp_name"], $ruta);
        
        if (!$copiar) {
            return false;
            
        } else {

            chmod($ruta, 0777);


            /**
             * $identificador es un arreglo con dimensiones para la redimension de las imagenes
             */
            if ($identificador != "" && is_array($identificador)) {
                $anchoMaximo    = $identificador[0];
                $altoMaximo     = $identificador[1];
                $anchoMinimo    = $identificador[2];
                $altoMinimo     = $identificador[3];
                $datos_imagen   = getimagesize($ruta);
                $ancho          = $datos_imagen[0];
                $alto           = $datos_imagen[1];



                if ($anchoMinimo != "" && $altoMinimo != "") {


                    $configuracionRutaMini = $configuracion["RUTAS"]["media"] . "/" . $configuracion["RUTAS"]["imagenesMiniaturas"];

                    $ruta_carpeta_mini = $configuracionRutaMini . "/$subcarpeta";
                    if (!file_exists($ruta_carpeta_mini)) {
                        mkdir($configuracionRutaMini . "/$subcarpeta", 0777, true);
                    }
                    chmod($ruta_carpeta_mini, 0777);


                    $nombreMini = $nombre; //nombre de la miniatura                
                    $rutaMini = $configuracionRutaMini . "/$subcarpeta/$nombreMini.$formato";

                    copy($ruta, $rutaMini);
                    chmod($rutaMini, 0777);


                    if ((($ancho / $alto) > ($anchoMinimo / $altoMinimo)) && ($ancho > $anchoMinimo)) {
                        $dimensiones_min[0] = $anchoMinimo;
                        $dimensiones_min[1] = ($anchoMinimo / $ancho) * $alto;
                    } elseif ($alto > $altoMinimo) {
                        $dimensiones_min[0] = ($altoMinimo / $alto) * $ancho;
                        $dimensiones_min[1] = $altoMinimo;
                    } else {
                        $dimensiones_min[0] = $anchoMinimo;
                        $dimensiones_min[1] = $altoMinimo;
                    }

                    $lienzo = imagecreatetruecolor($dimensiones_min[0], $dimensiones_min[1]);

                    switch ($formato) {
                        case "png" : $imagen = imagecreatefrompng($rutaMini);
                            $copia = imagecopyresampled($lienzo, $imagen, 0, 0, 0, 0, $dimensiones_min[0], $dimensiones_min[1], $ancho, $alto);
                            $guardar = imagepng($lienzo, $rutaMini);
                            break;


                        case "jpg" : $imagen = imagecreatefromjpeg($rutaMini);
                            $copia = imagecopyresampled($lienzo, $imagen, 0, 0, 0, 0, $dimensiones_min[0], $dimensiones_min[1], $ancho, $alto);
                            $guardar = imagejpeg($lienzo, $rutaMini);
                            break;

                        case "jpeg" : $imagen = imagecreatefromjpeg($rutaMini);
                            $copia = imagecopyresampled($lienzo, $imagen, 0, 0, 0, 0, $dimensiones_min[0], $dimensiones_min[1], $ancho, $alto);
                            $guardar = imagejpeg($lienzo, $rutaMini);
                            break;

                        case "gif" : $imagen = imagecreatefromgif($rutaMini);
                            $copia = imagecopyresampled($lienzo, $imagen, 0, 0, 0, 0, $dimensiones_min[0], $dimensiones_min[1], $ancho, $alto);
                            $guardar = imagegif($lienzo, $rutaMini);
                            break;
                    }

                    imagedestroy($lienzo);
                    imagedestroy($imagen);
                }

                if ((($ancho / $alto) > ($anchoMaximo / $altoMaximo)) && ($ancho > $anchoMaximo)) {
                    $dimensiones[0] = $anchoMaximo;
                    $dimensiones[1] = ($anchoMaximo / $ancho) * $alto;
                } elseif ($alto > $altoMaximo) {
                    $dimensiones[0] = ($altoMaximo / $alto) * $ancho;
                    $dimensiones[1] = $altoMaximo;
                } else {
                    $dimensiones[0] = $anchoMaximo;
                    $dimensiones[1] = $altoMaximo;
                }

                $lienzo = imagecreatetruecolor($dimensiones[0], $dimensiones[1]);

                switch ($formato) {
                    case "png" : $imagen = imagecreatefrompng($ruta);
                        $copia = imagecopyresampled($lienzo, $imagen, 0, 0, 0, 0, $dimensiones[0], $dimensiones[1], $ancho, $alto);
                        $guardar = imagepng($lienzo, $ruta);
                        break;

                    case "jpg" : $imagen = imagecreatefromjpeg($ruta);
                        $copia = imagecopyresampled($lienzo, $imagen, 0, 0, 0, 0, $dimensiones[0], $dimensiones[1], $ancho, $alto);
                        $guardar = imagejpeg($lienzo, $ruta);
                        break;

                    case "jpeg" : $imagen = imagecreatefromjpeg($ruta);
                        $copia = imagecopyresampled($lienzo, $imagen, 0, 0, 0, 0, $dimensiones[0], $dimensiones[1], $ancho, $alto);
                        $guardar = imagejpeg($lienzo, $ruta);
                        break;

                    case "gif" : $imagen = imagecreatefromgif($ruta);
                        $copia = imagecopyresampled($lienzo, $imagen, 0, 0, 0, 0, $dimensiones[0], $dimensiones[1], $ancho, $alto);
                        $guardar = imagegif($lienzo, $ruta);
                        break;
                }

                imagedestroy($lienzo);
                imagedestroy($imagen);
            }
        }

        return $rutaAdiciona;
    }


    /**
     * Función encargada de recibir un arreglo con las rutas RELATIVAS de lños archivos a eliminar
     * Nota: Debe ser la ruta relativa, el metodo unlink() no soporta URLs HTTP.
     *
     * @param string $ruta  = arreglo con las Rutas RELATIVAS de los archivos a eliminar
     * @return boolean      = true or false dependiendo de si se ejecuto con exito todas las intruscciones
     */
    public static function eliminarArchivoDelServidor($ruta) {
        
        if (!isset($ruta)) {
            return false;
        }

        $exito = true;
        
        if(is_array($ruta)){
            foreach ($ruta as $archivo) {
                $borrar = unlink($archivo);

                if ($borrar) {
                    $exito = true;

                } else {
                    $exito = false;

                }
            }            
            
        } else {
            $borrar = unlink($ruta);

            if ($borrar) {
                $exito = true;

            } else {
                $exito = false;

            }            
            
        }

        return $exito;
    }

    /**
     * Metodo que valida las extensiones de un archivo, devuelve true si tiene una extension valida
     * 
     * @global array $configuracion         = arreglo con datos de configuracion del sistema
     * @global recurso $archivo_imagen      = archivo tipo file
     * @param recurso $archivo              = el archivo a ser validado
     * @param type $extensiones             = arreglo con las extensiones que se van a validar
     * @return boolean                      = true o false segun la validacion
     */
    public static function validarArchivo($archivo, $extensiones) {

        if (!empty($archivo["name"])) {
            $existe = true;

            $extension_archivo = strtolower(substr($archivo["name"], (strrpos($archivo["name"], ".") - strlen($archivo["name"])) + 1));

            if (!empty($extensiones) && is_array($extensiones)) {
                foreach ($extensiones as $extension) {
                    if ($extension_archivo == $extension) {
                        $existe = false;
                    }
                }
            }
            return $existe;
        } else {
            return false;
        }
    }

//fin del metodo validar archivo  
}
