<?php

/**
 *
 * @package     FOM
 * @subpackage  Dias Festivos
 * @author      Pablo Andrés Vélez Vidal <pavelez8@misena.edu.co>
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2012 Genesys Corporation.
 * @version     0.2
 * 
 * Clase encargada de gestionar la información de los días festivos existentes en el sistema. En este módulo se pueden
 * agregar, consultar, eliminar o modificar la información de los días festivos del año actual. Este módulo debe ser actualizado 
 * los primeros días del año para poder establecer una planeación de funcionamiento de algunos módulos, los cuales podrían depender
 * de que un día sea o no festivo.
 * 
 * tablas: dias_festivos.
 * 
 **/

class DiaFestivo{


    /**
     * Código interno o identificador del registro en la base de datos
     * @var entero
     */
    public $id;

    /**
     * URL relativa del módulo 
     * @var cadena
     */
    public $urlBase;
      
    /**
     * URL relativa del módulo 
     * @var cadena
     */
    public $url;

     /**
     * Código interno o identificador del modulo
     * @var entero
     */
    public $idModulo;
 
 
    /**
     * Fecha del dia festivo
     * @var date
     */
    public $fecha;

    /**
     * Descripcion del registro
     * @var entero
     */
    public $descripcion;

    
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
     * Inicializar del objeto
     *
     * @param entero $id Código interno o identificador del dia festivo en la base de datos
     *
     */
    public function __construct($id = NULL) {
        global $sql, $modulo;
        $this->urlBase   = '/'.$modulo->url;
        $this->url       = $modulo->url;
        $this->idModulo  = $modulo->id;
        //Saber el numero de registros
        $this->registros = $sql->obtenerValor('dias_festivos', 'COUNT(id)', 'id != "0"');
        //Saber el numero de registros activos
        $this->registrosActivos = $sql->obtenerValor('dias_festivos', 'COUNT(id)', 'activo = "1"');
        //establecer el valor del campo predeterminado para organizar los listados
        $this->ordenInicial = 'descripcion';        

        if (isset($id)) {
            $this->cargar($id);        
        }
     }//Fin del metodo constructor




    /**
     *
     * Cargar los datos de un dia festivo
     *
     * @param entero $id Código interno o identificador del tdia festivo en la base de datos
     *
     */
    public function cargar($id) {
        global $sql;

        if (isset($id) && $sql->existeItem('dias_festivos', 'id', intval($id))) {

            $tablas = array(
                'df' => 'dias_festivos'
            );

            $columnas = array(
                'id'            => 'df.id',
                'fecha'         => 'df.fecha',              
                'descripcion'   => 'df.descripcion',
                'activo'        => 'df.activo'
            );

            $condicion = 'df.id = "'.$id.'"';

            $consulta = $sql->seleccionar($tablas, $columnas, $condicion);

            if ($sql->filasDevueltas) {
                $fila = $sql->filaEnObjeto($consulta);

                foreach ($fila as $propiedad => $valor) {
                    $this->$propiedad = $valor;
                }

                $this->url       = $this->urlBase.'/'.$this->id;
                                
            }
        }
    }//Fin del metodo Cargar




    /**
     *
     * Adicionar un dia festivo
     *
     * @param  arreglo $datos       Datos del tipo dia festivo a adicionar
     * @return entero               Código interno o identificador del dia festivo en la base de datos (NULL si hubo error)
     *
     */
    public function adicionar($datos) {
        global $sql;

        $datosItem = array();
        $datosItem['fecha']         = $datos['fecha'];
        $datosItem['descripcion']   = $datos['descripcion'];

        if (isset($datos['activo'])) {
            $datosItem['activo']   = '1';            

        } else {
            $datosItem['activo']     = '0';
        }

        $consulta = $sql->insertar('dias_festivos', $datosItem);

        if ($consulta) {
             $idItem  =  $sql->ultimoId;
             return $idItem;

        } else {
            return NULL;
        }//fin del if($consulta)

    }//fin del metodo adicionar dias festivos




    /**
     * Modificar un dia festivo
     *
     * @param  arreglo $datos       Datos del dia festivo a modificar
     * @return lógico               Indica si el procedimiento se pudo realizar correctamente o no
     *
     */
 public function modificar($datos) {
        global $sql;

        if (!isset($this->id)) {
            return NULL;
        }
        $datosItem = array();

        $datosItem['fecha']         = $datos['fecha'];
        $datosItem['descripcion']   = $datos['descripcion'];

        if (isset($datos['activo'])) {
            $datosItem['activo']   = '1';            

        } else {
            $datosItem['activo']     = '0';
        }
        //$sql->depurar = true;
        $consulta = $sql->modificar('dias_festivos', $datosItem, 'id = "'.$this->id.'"');


     if($consulta){
         return $this->id;  

     }else{
        return NULL;

     }//fin del if(consulta)

 }//fin del metodo Modificar




    /**
     *
     * Eliminar un dia festivo
     *
     * @param entero $id    Código interno o identificador del dia festivo en la base de datos
     * @return lógico       Indica si el procedimiento se pudo realizar correctamente o no
     *
     */
    public function eliminar() {
        global $sql;
       
        if (!isset($this->id)) {
            return NULL;
        }

        $consulta = $sql->eliminar('dias_festivos', 'id = "'.$this->id.'"');
        if(!($consulta)){                  
            return false;
            
         }else{
           return true;

         }//fin del si funciono eliminar
  
        
    }//Fin del metodo eliminar Dias Festivos



    

    /**
     *
     * Listar los dias Festivos
     *
     * @param entero  $cantidad    Número de dias festivos a incluir en la lista (0 = todas las entradas)
     * @param arreglo $excepcion   Arreglo con los códigos internos o identificadores a omitir en la lista
     * @param cadena  $condicion   Condición adicional (SQL)
     * @return arreglo             Lista de  dias festivos
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
            $condicion .= 'df.id NOT IN ('.$excepcion.')';
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
            'df' => 'dias_festivos'
        );

        $columnas = array(
            'id'            => 'df.id',
            'fecha'         => 'df.fecha',
            'descripcion'   => 'df.descripcion',                      
            'activo'        => 'df.activo'
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
//        $sql->depurar = true;
        $consulta = $sql->seleccionar($tablas, $columnas, $condicion, 'df.id', $orden, $inicio, $cantidad);
        //echo $sql->sentenciaSql;
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

    }//Fin del metodo de listar 
    
    
        public function generarTabla($arregloRegistros, $datosPaginacion = NULL){
        global $textos;
        //Declaracion de las columnas que se van a mostrar en la tabla
        $datosTabla = array(
            HTML::parrafo( $textos->id('FECHA')         ,  'centrado' ) => 'fecha|df.fecha',
            HTML::parrafo( $textos->id('DESCRIPCION')   ,  'centrado' ) => 'descripcion|df.descripcion',
            HTML::parrafo( $textos->id('ESTADO')        ,  'centrado' ) => 'estado'
        );        
        //ruta a donde se mandara la accion del doble click
        $ruta = '/ajax'.$this->urlBase.'/move';
        
        return Recursos::generarTablaRegistros($arregloRegistros, $datosTabla, $ruta, $datosPaginacion).HTML::crearMenuBotonDerecho('DIAS_FESTIVOS');
        
    }


}