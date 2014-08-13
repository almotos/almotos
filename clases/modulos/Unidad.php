<?php

/**
 *
 * @package     FOLCS
 * @subpackage  Unidades *
 * @author      Pablo Andrés Vélez Vidal <pavelez@colomboamericano.edu.co>
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2012 Genesys Corporation.
 * @version     0.2
 * 
 * Unidades de medida utilizadas en el sistema
 *
 **/

class Unidad {

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
     * Código interno del tipo de unidad
     * @var entero
     */
    public $idTipoUnidad;
     
    /**
     * Código interno del tipo de unidad
     * @var entero
     */
    public $tipoUnidad;
 
    /**
     * Codigo en letras de la unidad de medida
     * @var cadena
     */
    public $codigo;

    /**
     * Nombre de la unidad
     * @var entero
     */
    public $nombre;

    /**
     * Factor de conversion refrnte a otra unidad
     * @var fecha
     */
    public $factorConversion;

    /**
     * Código interno del tipo de unidad
     * @var entero
     */
    public $idUnidadPrincipal;
    
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
     */
    public function __construct($id = NULL) {
        global $sql, $modulo;
        
        $this->urlBase  = '/'.$modulo->url;
        $this->url      = $modulo->url;
        $this->idModulo = $modulo->id;
        
        //Saber el numero de registros
        $this->registros            = $sql->obtenerValor('unidades', 'COUNT(id)', 'id != "0"');
        //Saber el numero de registros activos
        $this->registrosActivos     = $sql->obtenerValor('unidades', 'COUNT(id)', 'activo = "1" AND id != "0"');
        //establecer el valor del campo predeterminado para organizar los listados
        $this->ordenInicial         = 'nombre';          
       
        if (isset($id)) {
            $this->cargar($id);        
        }
        
     }




    /**
     *
     * Cargar los datos de una unidad
     *
     * @param entero $id Código interno o identificador de la unidad en la base de datos
     */
    public function cargar($id) {
        global $sql;

        if (isset($id) && $sql->existeItem('unidades', 'id', intval($id))) {

            $tablas = array(
                'u'  => 'unidades',
                'tu' => 'tipos_unidades',
                'u1' => 'unidades'
            );

            $columnas = array(
                'id'                 => 'u.id',
                'codigo'             => 'u.codigo',
                'nombre'             => 'u.nombre',
                'factorConversion'   => 'u.factor_conversion',
                'idUnidadPrincipal'  => 'u.id_unidad_principal',
                'unidadPrincipal'    => 'u1.nombre',
                'idTipoUnidad'       => 'tu.id',
                'tipoUnidad'         => 'tu.nombre',              
                'activo'             => 'u.activo'
            );

            $condicion = 'u.id_tipo_unidad = tu.id AND u.id_unidad_principal = u1.id AND u.id = "'.$id.'"';

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
     * Adicionar una unidad
     *
     * @param  arreglo $datos       Datos de la unidad a adicionar
     * @return entero               Código interno o identificador de la unidad en la base de datos (NULL si hubo error)
     *
     */
    public function adicionar($datos) {
        global $sql;

        $datosUnidad = array();

        $datosUnidad['id_tipo_unidad']      = $datos['id_tipo_unidad'];
        $datosUnidad['codigo']              = $datos['codigo'];
        $datosUnidad['nombre']              = $datos['nombre'];
        $datosUnidad['factor_conversion']   = $datos['factor_conversion'];
        $datosUnidad['id_unidad_principal'] = $datos['id_unidad_principal'];

        $datosUnidad['activo']   = (isset($datos['activo'])) ? '1': '0'; 

        $consulta = $sql->insertar('unidades', $datosUnidad);

        if ($consulta) {
             $idItem          =  $sql->ultimoId;
             return $idItem;

        } else {
            return false;
            
        }

    }

    /**
    *
    * Modificar una unidad
    *
    * @param  arreglo $datos       Datos de la unidad a modificar
    * @return lógico               Indica si el procedimiento se pudo realizar correctamente o no
    *
    */
 public function modificar($datos) {
        global $sql;

        if (!isset($this->id)) {
            return NULL;
        }
        $datosUnidad = array();

        $datosUnidad['id_tipo_unidad']      = $datos['id_tipo_unidad'];
        $datosUnidad['codigo']              = $datos['codigo'];
        $datosUnidad['nombre']              = $datos['nombre'];
        $datosUnidad['factor_conversion']   = $datos['factor_conversion'];
        $datosUnidad['id_unidad_principal'] = $datos['id_unidad_principal'];

        $datosUnidad['activo']   = (isset($datos['activo'])) ? '1': '0'; 

        $consulta = $sql->modificar('unidades', $datosUnidad, 'id = "'.$this->id.'"');

        if($consulta){
            return true;  

        }else{
            return false;

        }

  }
  
    /**
     *
     * Eliminar una unidad
     *
     * @param entero $id    Código interno o identificador de una unidad en la base de datos
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
         
        //hago la validacion de la integridad referencial
        $arreglo1 = array('articulos',  'id_unidad = "'.$this->id.'"', $textos->id('ARTICULOS'));//arreglo del que sale la info a consultar
        $arregloIntegridad  = array($arreglo1);//arreglo de arreglos para realizar las consultas de integridad referencial, (ver documentacion de metodo)
        $integridad         = Recursos::verificarIntegridad($textos->id('UNIDAD'), $arregloIntegridad);

        /**
         * si hay problemas con la integridad referencial, la variable integridad tiene como valor,
         * un texto diciendo que tabla contiene n cantidad de relaciones con esta
         */
        if ($integridad != "") {
            $respuestaEliminar['mensaje'] = $integridad;
            return $respuestaEliminar;
        }
              
        $sql->iniciarTransaccion();
        $consulta = $sql->eliminar('unidades', "id = '".$this->id."'");
        
        if (!($consulta)) {
            return $respuestaEliminar;
            
        } else {
            $sql->finalizarTransaccion();
            //todo salió bien, se envia la respuesta positiva
            $respuestaEliminar['respuesta'] = true;
            return $respuestaEliminar;
        }
        
    }
    
    /**
     *
     * Listar las unidades 
     *
     * @param entero  $cantidad    Número de unidades a incluir en la lista (0 = todas las entradas)
     * @param arreglo $excepcion   Arreglo con los códigos internos o identificadores a omitir en la lista
     * @param cadena  $condicion   Condición adicional (SQL)
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

        /*** Validar que la condición sea una cadena de texto ***/
        if (!is_string($condicionGlobal)) {
            $condicion = '';
        }

        /*** Validar que la excepción sea un arreglo y contenga elementos ***/
        if (isset($excepcion) && is_array($excepcion) && count($excepcion)) {
            $excepcion = implode(',', $excepcion);
            $condicion .= 'u.id NOT IN ('.$excepcion.') AND ';
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
            'u'  => 'unidades',
            'tu' => 'tipos_unidades'
        );

        $columnas = array(
            'id'                 => 'u.id',
            'codigo'             => 'u.codigo',
            'nombre'             => 'u.nombre',
            'factorConversion'   => 'u.factor_conversion',
            'idUnidadPrincipal'  => 'u.id_unidad_principal',
            'idTipoUnidad'       => 'tu.id',
            'tipoUnidad'         => 'tu.nombre',              
            'activo'             => 'u.activo'
        );

        if (!empty($condicionGlobal)) {
            $condicion .= $condicionGlobal.' AND ';
        }  
      

        $condicion .= 'u.id_tipo_unidad = tu.id';      

        if (is_null($this->registrosConsulta)) {
            $sql->seleccionar($tablas, $columnas, $condicion);
            $this->registrosConsulta = $sql->filasDevueltas;
        }

        $consulta = $sql->seleccionar($tablas, $columnas, $condicion, 'u.id', $orden, $inicio, $cantidad);

        if ($sql->filasDevueltas) {
            $lista = array();

            while ($objeto = $sql->filaEnObjeto($consulta)) {                
                $objeto->estado = ($objeto->activo) ? HTML::frase($textos->id('ACTIVO'), 'activo') : HTML::frase($textos->id('INACTIVO'), 'inactivo');                 
                   
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
     * @param array $datosPaginacion arreglo con la información para la paginacion
     * @return string cadena HTML con la tabla (<table>) generada 
     */
    public function generarTabla($arregloRegistros, $datosPaginacion = NULL){
        global $textos;
        //Declaracion de las columnas que se van a mostrar en la tabla
        $datosTabla = array(
            HTML::parrafo( $textos->id('TIPO_UNIDAD')   ,  'centrado' ) => 'tipoUnidad|tu.nombre',
            HTML::parrafo( $textos->id('CODIGO')        ,  'centrado' ) => 'codigo|u.codigo',            
            HTML::parrafo( $textos->id('NOMBRE')        ,  'centrado' ) => 'nombre|u.nombre',
            HTML::parrafo( $textos->id('ESTADO')        ,  'centrado' ) => 'estado'
        );        
        //ruta del metodo paginador
        $ruta  = '/ajax'.$this->urlBase.'/move';
        
        $tabla       = Recursos::generarTablaRegistros($arregloRegistros, $datosTabla, $ruta, $datosPaginacion);
        $menuDerecho = HTML::crearMenuBotonDerecho('UNIDADES');
        
        return $tabla.$menuDerecho;
    }

}