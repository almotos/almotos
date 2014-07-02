<?php

/**
 *
 * @package     FOLCS
 * @subpackage  Tipos de Unidades 
 * @author      Pablo Andrés Vélez Vidal <pavelez@colomboamericano.edu.co>
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2012 Genesys Soft.
 * @version     0.2
 * 
 * Tipos de Unidades de medida utilizadas en el sistema
 **/

class TipoUnidad {


    /**
     * Código interno o identificador de la unidad en la base de datos
     * @var entero
     */
    public $id;

    /**
     * URL relativa del módulo de unidades
     * @var cadena
     */
    public $urlBase;
      
    /**
     * URL relativa del módulo de unidades
     * @var cadena
     */
    public $url;

     /**
     * Código interno o identificador del modulo
     * @var entero
     */
    public $idModulo;
 

    /**
     * Nombre de la unidad
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
     * Número de registros activos de la lista de foros
     * @var entero
     */
    public $registrosActivos = NULL;
    
    /**
     * Número de registros activos de la lista de foros
     * @var entero
     */
    public $registrosConsulta = NULL;    
    
    /**
     * Orden predeterminado para organizar los listados
     * @var entero
     */
    public $ordenInicial = NULL;       
    

    /**
     *
     * Inicializar de la unidad
     *
     * @param entero $id Código interno o identificador de la unidad en la base de datos
     *
     */
    public function __construct($id = NULL) {
        global $sql, $modulo;
        $this->urlBase   = '/'.$modulo->url;
        $this->url       = $modulo->url;
        $this->idModulo  = $modulo->id;
        //Saber el numero de registros
        $this->registros = $sql->obtenerValor('tipos_unidades', 'COUNT(id)', 'id != "0"');
        //Saber el numero de registros activos
        $this->registrosActivos = $sql->obtenerValor('tipos_unidades', 'COUNT(id)', 'activo = "1"');
        //establecer el valor del campo predeterminado para organizar los listados
        $this->ordenInicial = 'nombre';          

        if (isset($id)) {
            $this->cargar($id);        
        }
     }//Fin del metodo constructor




    /**
     *
     * Cargar los datos de un tipo de  unidad
     *
     * @param entero $id Código interno o identificador del tipo de unidad en la base de datos
     *
     */
    public function cargar($id) {
        global $sql;

        if (isset($id) && $sql->existeItem('tipos_unidades', 'id', intval($id))) {

            $tablas = array(
                'tu' => 'tipos_unidades'
            );

            $columnas = array(
                'id'       => 'tu.id',              
                'nombre'   => 'tu.nombre',
                'activo'   => 'tu.activo'
            );

            $condicion = 'tu.id = "'.$id.'"';

            $consulta = $sql->seleccionar($tablas, $columnas, $condicion);

            if ($sql->filasDevueltas) {
                $fila = $sql->filaEnObjeto($consulta);

                foreach ($fila as $propiedad => $valor) {
                    $this->$propiedad = $valor;
                }
                                
            }
        }
    }//Fin del metodo Cargar




    /**
     *
     * Adicionar un tipo de unidad
     *
     * @param  arreglo $datos       Datos del tipo de  unidad a adicionar
     * @return entero               Código interno o identificador del tipo unidad en la base de datos (NULL si hubo error)
     *
     */
    public function adicionar($datos) {
        global $sql;

        $datosTipoUnidad = array();
        $datosTipoUnidad['nombre']   = $datos['nombre'];

        if (isset($datos['activo'])) {
            $datosTipoUnidad['activo']   = '1';            

        } else {
            $datosTipoUnidad['activo']     = '0';
        }

        $consulta = $sql->insertar('tipos_unidades', $datosTipoUnidad);

        if ($consulta) {
             $idItem  =  $sql->ultimoId;
             return $idItem;

        } else {
            return NULL;
        }//fin del if($consulta)

    }//fin del metodo adicionar tipos de unidades




    /**
     * Modificar un tipo de unidad
     *
     * @param  arreglo $datos       Datos del tipo de unidad a modificar
     * @return lógico               Indica si el procedimiento se pudo realizar correctamente o no
     *
     */
 public function modificar($datos) {
        global $sql;

        if (!isset($this->id)) {
            return NULL;
        }
        $datosTipoUnidad = array();
        $datosTipoUnidad['nombre'] = $datos['nombre'];


       if (isset($datos['activo'])) {
            $datosTipoUnidad['activo']   = '1';            

        } else {
            $datosTipoUnidad['activo']     = '0';
        }
        //$sql->depurar = true;
        $consulta = $sql->modificar('tipos_unidades', $datosTipoUnidad, 'id = "'.$this->id.'"');


     if($consulta){
         return $this->id;  

     }else{
        return NULL;

     }//fin del if(consulta)

 }//fin del metodo Modificar




    /**
     *
     * Eliminar una unidad
     *
     * @param entero $id    Código interno o identificador de una unidad en la base de datos
     * @return lógico       Indica si el procedimiento se pudo realizar correctamente o no
     *
     */
    public function eliminar() {
        global $sql;
       
        if (!isset($this->id)) {
            return NULL;
        }

        $consulta = $sql->eliminar('tipos_unidades', "id = '".$this->id."'");
        if(!($consulta)){                  
            return false;
            
         }else{
           return true;

         }//fin del si funciono eliminar
  
        
    }//Fin del metodo eliminar Unidades



    

    /**
     *
     * Listar los tipos de unidades 
     *
     * @param entero  $cantidad    Número de tipos de unidades a incluir en la lista (0 = todas las entradas)
     * @param arreglo $excepcion   Arreglo con los códigos internos o identificadores a omitir en la lista
     * @param cadena  $condicion   Condición adicional (SQL)
     * @return arreglo             Lista de tipos de unidades
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
            $condicion .= 'tu.id NOT IN ('.$excepcion.')';
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
            'tu' => 'tipos_unidades'
        );

        $columnas = array(
            'id'                 => 'tu.id',
            'nombre'             => 'tu.nombre',                      
            'activo'             => 'tu.activo'
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
        
        $consulta = $sql->seleccionar($tablas, $columnas, $condicion, 'tu.id', $orden, $inicio, $cantidad);
//        echo $sql->sentenciaSql;
        if ($sql->filasDevueltas) {
            $lista = array();

            while ($objeto = $sql->filaEnObjeto($consulta)) {
                $objeto->url       = $this->urlBase.'/'.$objeto->id;
                $objeto->idModulo  = $this->idModulo;  
                if ($objeto->activo) {
                    $objeto->estado = HTML::frase($textos->id('ACTIVO'), 'activo');
                } else {
                    $objeto->estado = HTML::frase($textos->id('INACTIVO'), 'inactivo');
                }   
                $lista[]           = $objeto;
            }
        }

        return $lista;

    }//Fin del metodo de listar los tipos de unidades
    
    
        public function generarTabla($arregloRegistros, $datosPaginacion = NULL){
        global $textos;
        //Declaracion de las columnas que se van a mostrar en la tabla
        $datosTabla = array(
            HTML::parrafo( $textos->id('NOMBRE')        ,  'centrado' ) => 'nombre|tu.nombre',
            HTML::parrafo( $textos->id('ESTADO')        ,  'centrado' ) => 'estado'
        );        
        //ruta a donde se mandara la accion del doble click
        $ruta  = '/ajax'.$this->urlBase.'/move';
        
        $tabla       = Recursos::generarTablaRegistros($arregloRegistros, $datosTabla, $ruta, $datosPaginacion);
        $menuDerecho = HTML::crearMenuBotonDerecho('TIPOS_UNIDADES');
        
        return $tabla.$menuDerecho;        
        
    }


}