<?php

/**
 *
 * @package     FOLCS
 * @subpackage  Periodos Contables *
 * @author      Pablo Andres Velez Vidal <pavelez@colomboamericano.edu.co>
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2012 Genesys Corporation.
 * @version     0.2
 * 
 * Periodos de control para contabilidad del negocio.
 *
 **/

class PeriodoContable {
    /**
     * Codigo interno o identificador del periodo contable en la base de datos
     * @var entero
     */
    public $id;

    /**
     * URL relativa del modulo de periodos contables
     * @var cadena
     */
    public $urlBase;
      
    /**
     * URL relativa del modulo de periodos contables
     * @var cadena
     */
    public $url;

     /**
     * Codigo identificador del periodo contable
     * @var cadena
     */
    public $codigo;

    /**
     * Nombre del periodo contable
     * @var entero
     */
    public $nombre;

    /**
     * Fecha inicial del periodo contable
     * @var fecha
     */
    public $fechaInicial;

    /**
     *  Fecha final del periodo contable
     * @var fecha
     */
    public $fechaFinal;
    
    /**
    * Estado del periodo contable
    * @var entero
    */
    public $activo;
    
     /**
     * Indicador del orden cronologico de la lista de registros
     * @var logico
     */
    public $listaAscendente = TRUE;

    /**
     * Numero de registros de la lista
     * @var entero
     */
    public $registros = NULL;

    /**
     * Numero de registros activos de la lista de foros
     * @var entero
     */
    public $registrosActivos = NULL;

    /**
     * Numero de registros activos de la lista de foros
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
     * Inicializar el periodo contable
     *
     * @param entero $id Codigo interno o identificador del periodo contable en la base de datos
     */
    public function __construct($id = NULL) {
        global $sql, $modulo;
        
        $this->urlBase  = '/'.$modulo->url;
        $this->url      = $modulo->url;
        $this->idModulo = $modulo->id;
        
        //Saber el numero de registros
        $this->registros            = $sql->obtenerValor('periodos_contables', 'COUNT(id)', 'id != "0"');
        //Saber el numero de registros activos
        $this->registrosActivos     = $sql->obtenerValor('periodos_contables', 'COUNT(id)', 'activo = "1" AND id != "0"');
        //establecer el valor del campo predeterminado para organizar los listados
        $this->ordenInicial         = 'fecha_inicial';          
       
        if (isset($id)) {
            $this->cargar($id);        
        }
        
     }
 
     /**
     *
     * Cargar los datos de un periodo contable
     *
     * @param entero $id Codigo interno o identificador del periodo contable en la base de datos
     */
    public function cargar($id) {
        global $sql;

        if (isset($id) && $sql->existeItem('periodos_contables', 'id', intval($id))) {

            $tablas = array(
                'pc'  => 'periodos_contables',
             
            );

            $columnas = array(
                'id'                 => 'pc.id',
                'codigo'             => 'pc.codigo',
                'nombre'             => 'pc.nombre',
                'fechaInicial'       => 'pc.fecha_inicial',
                'fechaFinal'         => 'pc.fecha_final',
                'activo'             => 'pc.activo'
               
            );

            $condicion = 'pc.id = "'.$id.'"';

            $consulta = $sql->seleccionar($tablas, $columnas, $condicion);

            if ($sql->filasDevueltas) {
                $fila = $sql->filaEnObjeto($consulta);

                foreach ($fila as $propiedad => $valor) {
                    $this->$propiedad = $valor;
                }
                                
            }
        }
    }

    /**
     *
     * Adicionar un periodo contable
     *
     * @param  arreglo $datos       Datos del periodo contable a adicionar
     * @return entero               Codigo interno o identificador del periodo contable en la base de datos (NULL si hubo error)
     *
     */
    public function adicionar($datos) {
        global $sql;
        
        $datos['activo'] = (isset($datos['activo'])) ? '1' : '0';

        $consulta = $sql->insertar('periodos_contables', $datos);

        if ($consulta) {
             $idItem   =  $sql->ultimoId;
             return $idItem;

        } else {
            return false;
            
        }

    }

    /**
    *
    * Modificar un periodo contable
    *
    * @param  arreglo $datos       Datos del periodo contable a modificar
    * @return logico               Indica si el procedimiento se pudo realizar correctamente o no
    *
    */
    public function modificar($datos) {
        global $sql;
        
        if (!isset($this->id)) {
            return false;
        }
        
        $datos['activo'] = (isset($datos['activo'])) ? '1' : '0';

        $consulta = $sql->modificar("periodos_contables", $datos, "id = '".$this->id."'");
        
        return $consulta;
        
    }

    /**
     *
     * Eliminar un periodo contable
     *
     * @param entero $id    Codigo interno o identificador de un periodo contable en la base de datos
     * @return logico       Indica si el procedimiento se pudo realizar correctamente o no
     *
     */
    public function eliminar() {
        global $sql;

        if (!isset($this->id)) {
            return false;
        }
        
        $consulta = $sql->eliminar("periodos_contables", "id = '".$this->id."'");
        
        return $consulta;
        
    }
    
    /**
     *
     * Listar las unidades 
     *
     * @param entero  $cantidad    Numero de periodos contables a incluir en la lista (0 = todas las entradas)
     * @param arreglo $excepcion   Arreglo con los codigos internos o identificadores a omitir en la lista
     * @param cadena  $condicion   Condicion adicional (SQL)
     * @return arreglo             Lista de unidades
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

        /*** Validar que la condicion sea una cadena de texto ***/
        if (!is_string($condicionGlobal)) {
            $condicion = '';
        }

        /*** Validar que la excepcion sea un arreglo y contenga elementos ***/
        if (isset($excepcion) && is_array($excepcion) && count($excepcion)) {
            $excepcion = implode(',', $excepcion);
            $condicion .= 'pc.id NOT IN ('.$excepcion.')';
        }

        /*** Definir el orden de presentacion de los datos ***/
        if(!isset($orden)){
            $orden = $this->ordenInicial;
        }
        if ($this->listaAscendente) {
            $orden = $orden.' ASC';

        } else {
            $orden = $orden.' DESC';
        }
    
        $tablas = array(
            'pc'  => 'periodos_contables',
        );

        $columnas = array(
            'id'                 => 'pc.id',
            'codigo'             => 'pc.codigo',
            'nombre'             => 'pc.nombre',
            'fechaInicial'       => 'pc.fecha_inicial',
            'fechaFinal'         => 'pc.fecha_final',
            'estado'             => 'pc.activo',
        );

        if (!empty($condicionGlobal)) {
            if ($condicion != '') {
                $condicion .= ' AND ';
                
            }
            
            $condicion .= $condicionGlobal;
            
        }
            
        if (is_null($this->registrosConsulta)) {
            $sql->seleccionar($tablas, $columnas, $condicion);
            $this->registrosConsulta = $sql->filasDevueltas;
        }
        
        $consulta = $sql->seleccionar($tablas, $columnas, $condicion, 'pc.id', $orden, $inicio, $cantidad);

        if ($sql->filasDevueltas) {
            $lista = array();

            while ($objeto = $sql->filaEnObjeto($consulta)) {                
                $objeto->estado = ($objeto->estado) ? HTML::frase($textos->id('ACTIVO'), 'activo') : HTML::frase($textos->id('INACTIVO'), 'inactivo');                 
                   
                $lista[]           = $objeto;
            }
        }

        return $lista;

    }
    
    
    /**
     * Metodo que genera los datos que contendra la tabla principal del modulo
     * 
     * @global objeto $textos = objeto global de traduccion de textos
     * @param array $arregloRegistros matriz con la info a ser mostrada en la tabla
     * @param array $datosPaginacion arreglo con la informacion para la paginacion
     * @return string cadena HTML con la tabla (<table>) generada 
     */
    public function generarTabla($arregloRegistros, $datosPaginacion = NULL){
        global $textos;
        //Declaracion de las columnas que se van a mostrar en la tabla
        $datosTabla = array(
            HTML::parrafo( $textos->id('CODIGO')        ,  'centrado' ) => 'codigo|pc.codigo',            
            HTML::parrafo( $textos->id('NOMBRE')        ,  'centrado' ) => 'nombre|pc.nombre',
            HTML::parrafo( $textos->id('FECHA_INICIAL') ,  'centrado' ) => 'fechaInicial|pc.fecha_inicial',
            HTML::parrafo( $textos->id('FECHA_FINAL')   ,  'centrado' ) => 'fechaFinal|pc.fecha_final',
            HTML::parrafo( $textos->id('ACTIVO')        ,  'centrado' ) => 'estado|pc.activo'      
        );        
        //ruta del metodo paginador
        $ruta  = '/ajax'.$this->urlBase.'/move';
        
        $tabla       = Recursos::generarTablaRegistros($arregloRegistros, $datosTabla, $ruta, $datosPaginacion);
        $menuDerecho = HTML::crearMenuBotonDerecho('PERIODOS_CONTABLES');
        
        return $tabla.$menuDerecho;
    }

}