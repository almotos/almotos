<?php

/**
 *
 * @package     FOLCS
 * @subpackage  Base
 * @author      Pablo A. Vélez Vidal. <pavelez@colomboamericano.edu.co>
 * @author      Julian A. Mondragón <jmondragon@felinux.com.co>
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2011 CENTRO CULTURAL COLOMBO AMERICANO
 * @version     0.2
 *
 **/

class Imagen {

    /**
     * Código interno o identificador del archivo en la base de datos
     * @var entero
     */
    public $id;

    /**
     * URL relativa de un archivo específico
     * @var cadena
     */
    public $url;

    /**
     * Código interno o identificador del módulo al cual pertenece el archivo en la base de datos
     * @var entero
     */
    public $idModulo;

//    /**
//     * Código interno o identificador del registro del módulo al cual pertenece el archivo en la base de datos
//     * @var entero
//     */
//    public $idRegistro;
//
//    /**
//     * Código interno o identificador del usuario creador del archivo en la base de datos
//     * @var entero
//     */
//    public $idAutor;
//
//    /**
//     * Nombre de usuario (login) del usuario creador del archivo
//     * @var cadena
//     */
//    public $usuarioAutor;
//
//    /**
//     * Sobrenombre o apodo del usuario creador del archivo
//     * @var cadena
//     */
//    public $autor;
//
//    /**
//     * Ruta de la foto del autor en miniatura
//     * @var cadena
//     */
//    public $fotoAutor;
//    
//    
//     /**
//     * identificador del modulo al cual pertenece la imagen
//     * @var cadena
//     */
//    public $moduloImagen;
//
//    /**
//     * Título de la imagen
//     * @var cadena
//     */
//    public $titulo;
//
//    /**
//     * Descripción corta del archivo
//     * @var cadena
//     */
//    public $descripcion;
    
    /**
     * Ruta relativa de la imagen en tamaño normal en tamaño normal
     * @var cadena
     */
    public $imagenPrincipal;

    /**
     * Ruta de la imagen de la noticia en miniatura
     * @var cadena
     */
    public $imagenMiniatura;    

    /**
     * Indicador del estado del archivo
     * @var lógico
     */
    public $activo;

    /**
     * Indicador del orden cronológio de la lista de temas
     * @var lógico
     */
    public $listaAscendente = false;

    /**
     * Número de registros de la lista
     * @var entero
     */
    public $registros = NULL;
    
    /**
     * Ruta relativa a la imagen
     * @var entero
     */
    public $ruta;
    
    
   /**
     * Ruta absoluta a la imagen
     * @var entero
     */
    public $enlace;
    
    /**
     *
     * Inicializar el archivo
     *
     * @param entero $id Código interno o identificador del archivo en la base de datos
     *
     */
    public function __construct($id = NULL) {
                
        $modulo         = new Modulo("IMAGENES");
        $this->urlBase  = "/".$modulo->url;
        $this->url      = $modulo->url;
        $this->idModulo = $modulo->id;

        if ( isset($id) ) {
            $this->cargar($id);
        }

    }

    /**
     *
     * Cargar los datos del archivo
     *
     * @param entero $id Código interno o identificador del archivo en la base de datos
     *
     */
    public function cargar($id) {
        global $sql, $configuracion;

        if (isset($id) && $sql->existeItem("imagenes", "id", intval($id))) {

            $tablas = array(
                "i" => "imagenes"
            );

            $columnas = array(
                "id"            => "i.id",
                "titulo"        => "i.titulo",
                "ruta"          => "i.ruta"
            );

            $condicion = "i.id = '$id'";

            $consulta = $sql->seleccionar($tablas, $columnas, $condicion);

            if ($sql->filasDevueltas) {
                $fila = $sql->filaEnObjeto($consulta);

                foreach ($fila as $propiedad => $valor) {
                    $this->$propiedad = $valor;
                }

                $this->url       = $this->urlBase."/".$archivo->id;
                
                $this->imagenPrincipal = $configuracion["RUTAS"]["media"].$configuracion["RUTAS"]["imagenesDinamicas"]."/".$this->ruta;
                $this->imagenMiniatura = $configuracion["RUTAS"]["media"].$configuracion["RUTAS"]["imagenesMiniaturas"]."/".$this->ruta;

            }
            
        }
        
    }

    /**
     *
     * Metodo que se encarga de agregar una Imagen y usa el metodo subirArchivoAlServidor para subirla al servidor,
     * ingresa los datos de esta imagen a la tabla imagenes en la BD, y dependiendo del modulo.
     *
     * @param  arreglo $datos       Datos del archivo a adicionar
     * @return entero               Código interno o identificador del archivo en la base de datos (NULL si hubo error)
     *
     */
   public function adicionar($datos, $archivoImagen = NULL/*, $servidor =NULL*/) {
        global $sql, $configuracion, $archivo_imagen;

        if(isset($archivoImagen) && !empty($archivoImagen['tmp_name'])){
            $archivo = $archivoImagen;
            
        } else {
            $archivo = $archivo_imagen;
            
        }
        
        if (empty($archivo["tmp_name"])) {
            return false;
        }
        
        $titulo      = $datos["titulo"];

        $ruta        = $configuracion["RUTAS"]["media"]."/".$configuracion["RUTAS"]["imagenesDinamicas"];
        
        $area    = getimagesize($archivo["tmp_name"]);
        $ancho   = $area[0];
        $alto    = $area[1];
        
        while($ancho > 800 || $alto > 600){            
            $ancho = ($ancho * 90) / 100;
            $alto  = ($alto  * 90) / 100;
            
        }
        
        $dimensiones = array($ancho, $alto, 90, 90);
        //$dimensiones = $configuracion["DIMENSIONES"]["".$modulo.""];
        
        $recurso   = Archivo::subirArchivoAlServidor($archivo, $ruta, $dimensiones );
        
        
         if($recurso){
             
                $datosRecurso = array(
                    "titulo"      => $titulo,
                    "fecha"       => date("Y-m-d H:i:s"),
                    "ruta"        => $recurso
                );
                
                $consulta = $sql->insertar("imagenes", $datosRecurso);
                //echo $sql->sentenciaSql;
                $idImagen = $sql->ultimoId;

                if ($consulta){
                    return $idImagen;        

                } else {
                    return false;
                }
        
         }else{
             return false;
             
         }
        
    }

    /**
     *
     * Eliminar una imágen
     *
     * @param entero $id    Código interno o identificador del archivo en la base de datos
     * @return lógico       Indica si el procedimiento se pudo realizar correctamente o no
     *
     */
    public function eliminar() {
        global $sql, $configuracion, $sesion_usuarioSesion;

        if (!isset($this->id)) {
            return false;
        }
        
        $sql->iniciarTransaccion();
  
        $ruta      = $configuracion["RUTAS"]["media"]."/".$configuracion["RUTAS"]["imagenesDinamicas"]."/".$this->ruta;
        $miniatura = $configuracion["RUTAS"]["media"]."/".$configuracion["RUTAS"]["imagenesMiniaturas"]."/".$this->ruta;
       
        if($this->id != 0){ //si no es ninguna de las imagenes base
            
            if( Archivo::eliminarArchivoDelServidor( array($ruta, $miniatura) ) ){
                                
                //verifico que la imagen que se este eliminando sea la que tiene el usuario de imagen de perfil
                //en caso de que sea asi, se le pone como id de imagen la magen predeterminada = 0...cero
                if(isset($sesion_usuarioSesion) && $sesion_usuarioSesion->persona->idImagen == $this->id){
                    $datos = array(
                        "id_imagen" => "0"
                    );
                    $consulta = $sql->modificar("personas", $datos, "id = '".$sesion_usuarioSesion->id."'");
                    
                    if(!$consulta){
                        $sql->cancelarTransaccion();
                        return false;
                    }
                    
                }

                $consulta = $sql->eliminar("imagenes", "id = '".$this->id."'");

                if($consulta){
                    $sql->finalizarTransaccion();
                    return true;
                    
                } else {
                    $sql->cancelarTransaccion();
                    return false;
                    
                }

            }else{
                return false;
            }  
            
        } else {
            return true;
            
        }
                
    } 
    
    
   /**
    * Metodo usado para devolver solo la ruta de la imagen
    * cargada al servidor para ser almacenada en la BD
    * 
    * @global type $archivo_imagen
    * @global type $configuracion
    * @param type $archivoImagen
    * @param string $ruta
    * @return null 
    */
    public function cargarImagen($archivoImagen = NULL, $ruta = NULL){
        global  $archivo_imagen, $configuracion;

        if(isset($archivoImagen) && !empty($archivoImagen['tmp_name'])){
            $archivo = $archivoImagen;
        } else {
            $archivo = $archivo_imagen;
        }
        
        if (empty($archivo["tmp_name"])) {
            return NULL;
        }
        
        if (empty($ruta)) {
            $ruta        = $configuracion["RUTAS"]["media"]."/".$configuracion["RUTAS"]["imagenesDinamicas"];
        }
        
        
        $area    = getimagesize($archivo["tmp_name"]);
        $ancho   = $area[0];
        $alto    = $area[1];
        
        while($ancho > 800 || $alto > 600){            
            $ancho = ($ancho * 90) / 100;
            $alto  = ($alto  * 90) / 100;
            
        }
        
        $dimensiones = array($ancho, $alto, 90, 90);
        
        $recurso   = Archivo::subirArchivoAlServidor($archivo, $ruta, $dimensiones ); 
        
        return $recurso;
    }    
    
}
