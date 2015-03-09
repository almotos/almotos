<?php

/**
 *
 * @package     FOLCS
 * @subpackage  Marcas 
 * @author      Pablo Andrés Vélez Vidal <pavelez@colomboamericano.edu.co>
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2012 Genesys Soft.
 * @version     0.2
 * 
 * Marcas comerciales para ser utilizadas en el sistema, utilizado por los módulos
 * de articulos y de motos.
 **/

class Marca {

    /**
     * Código interno o identificador de la marca en la base de datos
     * @var entero
     */
    public $id;

    /**
     * URL relativa del módulo de marcas
     * @var cadena
     */
    public $urlBase;
      
    /**
     * URL relativa del módulo de marcas
     * @var cadena
     */
    public $url;

     /**
     * Código interno o identificador del modulo
     * @var entero
     */
    public $idModulo;

    /**
     * Nombre de la marca
     * @var entero
     */
    public $nombre;

    
    /**
     * Número de registros de la lista
     * @var entero
     */
    public $activo;

     /**
     * Indicador del orden cronológio de la lista de registros
     * @var lógico
     */
    public $listaAscendente = TRUE;
    
    /**
     * Número de registros de la lista
     * @var entero
     */
    public $registros = NULL;

    /**
     * Número de registros activos de la lista de marcas
     * @var entero
     */
    public $registrosActivos = NULL;
    
    /**
     * Número de registros activos de la lista de marcas
     * @var entero
     */
    public $registrosConsulta = NULL;    
    
    /**
     * Nombre de la unidad
     * @var entero
     */
    public $idImagen;

    /**
     * objeto imagen
     * @var cadena
     */
    public $imagen;  
    
    /**
     * Orden predeterminado para organizar los listados
     * @var entero
     */
    public $ordenInicial = NULL;       

    /**
     *
     * Inicializar de la marca
     *
     * @param entero $id Código interno o identificador de la marca en la base de datos
     *
     */
    public function __construct($id = NULL) {
        global $sql, $modulo;
        
        $this->urlBase   = '/'.$modulo->url;
        $this->url       = $modulo->url;
        $this->idModulo  = $modulo->id;
        
        //Saber el numero de registros
        $this->registros            = $sql->obtenerValor('marcas', 'COUNT(id)', 'id != "0"');
        //Saber el numero de registros activos
        $this->registrosActivos     = $sql->obtenerValor('marcas', 'COUNT(id)', 'activo = "1"');
        //establecer el valor del campo predeterminado para organizar los listados
        $this->ordenInicial         = 'nombre';        

        if (isset($id)) {
            $this->cargar($id);        
        }
        
     }

    /**
     *
     * Cargar los datos de un tipo de  marca
     *
     * @param entero $id Código interno o identificador del tipo de marca en la base de datos
     *
     */
    public function cargar($id) {
        global $sql;

        if (isset($id) && $sql->existeItem('marcas', 'id', intval($id))) {

            $tablas = array(
                'm' => 'marcas'
            );

            $columnas = array(
                'id'       => 'm.id',              
                'nombre'   => 'm.nombre',
                'idImagen' => 'm.id_imagen',
                'activo'   => 'm.activo'
            );

            $condicion = 'm.id = '.$id.'';

            $consulta = $sql->seleccionar($tablas, $columnas, $condicion);

            if ($sql->filasDevueltas) {
                $fila = $sql->filaEnObjeto($consulta);

                foreach ($fila as $propiedad => $valor) {
                    $this->$propiedad = $valor;
                }

                $this->url       = $this->urlBase.'/'.$this->id;
                                
            }
            
            $this->imagen = new Imagen($this->idImagen);
            
        }
        
    }

    /**
     *
     * Adicionar un tipo de marca
     *
     * @param  arreglo $datos       Datos del tipo de  marca a adicionar
     * @return entero               Código interno o identificador del tipo marca en la base de datos (NULL si hubo error)
     *
     */
    public function adicionar($datos) {
        global $sql, $archivo_imagen;
        
        $sql->iniciarTransaccion();

        $datos['activo']   =(isset($datos['activo'])) ? '1' : '0';
        
        $idImagen = '0';

        if (isset($archivo_imagen) && !empty($archivo_imagen['tmp_name'])) {
            $imagen = new Imagen();
            
            $datosImagen = array(
                'titulo' => 'imagen_marca',
            );

            $idImagen = $imagen->adicionar($datosImagen);
            
            if (!$idImagen) {
                $sql->cancelarTransaccion();
            }            
        }

        $datos['id_imagen'] = $idImagen;        

        $consulta = $sql->insertar('marcas', $datos);

        if ($consulta) {
            $sql->finalizarTransaccion();
            
            $idItem  =  $sql->ultimoId;
            return $idItem;

        } else {
            $sql->cancelarTransaccion();
            return false;
            
        }

    }

    /**
     * Modificar un tipo de marca
     *
     * @param  arreglo $datos       Datos del tipo de marca a modificar
     * @return lógico               Indica si el procedimiento se pudo realizar correctamente o no
     *
     */
 public function modificar($datos) {
    global $sql, $archivo_imagen;

    if (!isset($this->id)) {
        return false;
    }
    
    $sql->iniciarTransaccion();

    $datos['activo']   =  (isset($datos['activo'])) ? '1' : '0';

    $idImagen   = $this->idImagen;

    if (isset($archivo_imagen) && !empty($archivo_imagen['tmp_name'])) {
        $imagen = new Imagen($this->idImagen);
        
        $elimina = $imagen->eliminar();
        
        if (!$elimina) {
            $sql->cancelarTransaccion();
            
        }
        
        $datosImagen = array(
            'titulo' => 'imagen_marca',
        );

        $idImagen = $imagen->adicionar($datosImagen);
        
        if (!$idImagen) {
            $sql->cancelarTransaccion();
        }        
        
    }

    $datos['id_imagen'] = $idImagen;    
    
    $consulta = $sql->modificar('marcas', $datos, 'id = "'.$this->id.'"');


     if($consulta){
         $sql->finalizarTransaccion();
         return $this->id;  

     }else{
        $sql->cancelarTransaccion();
        return false;

     }

 }

    /**
     *
     * Eliminar una marca
     *
     * @param entero $id    Código interno o identificador de una marca en la base de datos
     * @return lógico       Indica si el procedimiento se pudo realizar correctamente o no
     *
     */
     public function eliminar() {
        global $sql, $textos;
        
        //arreglo que será devuelto como respuesta
        $respuestaEliminar = array(
            'respuesta' => false,
            'mensaje'   => $textos->id('ERROR_DESCONOCIDO'),
        );
        
        if (!isset($this->id)) {
            return $respuestaEliminar;
        }
         
        //hago la validacion de la integridad referencial.
        $arreglo1           = array('articulos', 'id_marca = "'.$this->id.'"', $textos->id('ARTICULOS'));//arreglo del que sale la info a consultar
        $arreglo2           = array('motos',     'id_marca = "'.$this->id.'"', $textos->id('MOTOS'));
        
        $arregloIntegridad  = array($arreglo1, $arreglo2);//arreglo de arreglos para realizar las consultas de integridad referencial, (ver documentacion de metodo)
        $integridad         = Recursos::verificarIntegridad($textos->id('MARCA'), $arregloIntegridad);  
        
        /**
        * si hay problemas con la integridad referencial, la variable integridad tiene como valor,
        * un texto diciendo que tabla contiene n cantidad de relaciones con esta
        */
        if ($integridad != "") {
            $respuestaEliminar['mensaje'] = $integridad;
            return $respuestaEliminar;
            
        }

        $sql->iniciarTransaccion();
        $consulta = $sql->eliminar('marcas', 'id = "' . $this->id . '"');
     
         if ($consulta) {
            $imagen         = new Imagen($this->idImagen); 
            $eliminarImagen = $imagen->eliminar;
            
            if($eliminarImagen === false){
                $sql->cancelarTransaccion("Fallo en el archivo " . __FILE__ . " en la linea " .  __LINE__);
                return $respuestaEliminar;
                
            }            
            $sql->finalizarTransaccion();
            //todo salió bien, se envia la respuesta positiva
            $respuestaEliminar['respuesta'] = true;
            return $respuestaEliminar;
            
        } else {
            $sql->cancelarTransaccion("Fallo en el archivo " . __FILE__ . " en la linea " .  __LINE__);
            return $respuestaEliminar;
            
        }
     
    }

    /**
     *
     * Listar los tipos de marcas 
     *
     * @param entero  $cantidad    Número de tipos de marcas a incluir en la lista (0 = todas las entradas)
     * @param arreglo $excepcion   Arreglo con los códigos internos o identificadores a omitir en la lista
     * @param cadena  $condicion   Condición adicional (SQL)
     * @return arreglo             Lista de tipos de marcas
     *
     */
    public function listar($inicio = 0, $cantidad = 0, $excepcion = NULL, $condicionGlobal = NULL, $orden = NULL) {
        global $sql, $textos;

        /*** Validar la fila inicial de la consulta ***/
        if (!is_int($inicio) || $inicio < 0) {
            $inicio = 0;
        }

        /*** Validar la cantidad de registros requeridos en la consulta ***/
        if (!is_int($cantidad) || $cantidad <= 0) {
            $cantidad = 0;
        }

        /*** Validar que la condición sea una cadena de texto ***/
        if (!is_string($condicionGlobal)) {
            $condicion = '';
        }

        /*** Validar que la excepción sea un arreglo y contenga elementos ***/
        if (isset($excepcion) && is_array($excepcion) && count($excepcion)) {
            $excepcion = implode(',', $excepcion);
            $condicion .= 'm.id NOT IN ('.$excepcion.')';
        }

        /*** Definir el orden de presentación de los datos ***/
        if(!isset($orden)){
            $orden = $this->ordenInicial;
        }
        if ($this->listaAscendente) {
            $orden = $orden.' ASC';

        } else {
            $orden = $orden.' DESC';
        }
    

        $tablas = array(
            'm' => 'marcas'
        );

        $columnas = array(
            'id'                 => 'm.id',
            'nombre'             => 'm.nombre',                      
            'activo'             => 'm.activo'
        );

        if (!empty($condicionGlobal)) {
            if($condicion != ''){
                $condicion .= ' AND ';
            }
            $condicion .= $condicionGlobal;
        }   

        if (is_null($this->registrosConsulta)) {
            $sql->seleccionar($tablas, $columnas, $condicion);
            $this->registrosConsulta = $sql->filasDevueltas;
        }
        
        $consulta = $sql->seleccionar($tablas, $columnas, $condicion, 'm.id', $orden, $inicio, $cantidad);

        if ($sql->filasDevueltas) {
            $lista = array();

            while ($objeto = $sql->filaEnObjeto($consulta)) {
                $objeto->estado =  ($objeto->activo) ? HTML::frase($textos->id('ACTIVO'), 'activo') : HTML::frase($textos->id('INACTIVO'), 'inactivo');
                
                $lista[]           = $objeto;
            }
        }

        return $lista;

    }
    
    /**
     *
     * @global type $textos
     * @param type $arregloRegistros
     * @param type $datosPaginacion
     * @return type 
     */
    public function generarTabla($arregloRegistros, $datosPaginacion = NULL){
        global $textos;
        //Declaracion de las columnas que se van a mostrar en la tabla
        $datosTabla = array(
            HTML::parrafo( $textos->id('CODIGO')        ,  'centrado' ) => 'id|m.id',
            HTML::parrafo( $textos->id('NOMBRE')        ,  'centrado' ) => 'nombre|m.nombre',
            HTML::parrafo( $textos->id('ESTADO')        ,  'centrado' ) => 'estado'
        );        
        //ruta a donde se mandara la accion del doble click
        $ruta = '/ajax'.$this->urlBase.'/move';
        
        return Recursos::generarTablaRegistros($arregloRegistros, $datosTabla, $ruta, $datosPaginacion).HTML::crearMenuBotonDerecho('MARCAS');
        
    }

}
