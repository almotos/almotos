<?php

/**
 *
 * @package     FOM
 * @subpackage  Gondolas 
 * @author      Pablo Andrés Vélez Vidal 
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2012 Genesys Soft.
 * @version     0.2
 * 
 * Gondolas existentes en el negocio para el almacenamiento de los articulos.
 * 
 * Modulo: gondolas.
 * Tabla principal: gondolas.
 * modulo relacionado: se relaciona con bodegas. pues una bodega puede tener 1..* gondolas.
 * Uso: Será utilizado sobre todo en el modulo de articulos para darle una ubicacion 'espacial'
 * a cada uno de los articulos, seleccionando en que bodega se encuentra, y en que gondola. a su vez
 * como cada gondola almacena la informacion de cuantos lados tiene, y cuantas bandejas tiene, cuando se
 * escoge una gondola, te muestra los lados y las bandejas para escoger.
 * Modulos que dependen referencialmente:
 * -articulos: pues un articulo puede estar ubicado en una gondola, pero siempre se debe dejar una gondola base. * 
 *
 **/

class Gondola {

    

    /**
     * Código interno o identificador del item en la base de datos
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
     * Código interno o identificador de la bodega en la que se ubica la gondola
     * @var entero
     */
    public $idBodega;    
    
     /**
     * Nombre de la bodega
     * @var entero
     */
    public $bodega;  
    
     /**
     * Nombre de la bodega
     * @var entero
     */
    public $sede;     
    
    /**
     * Tabla principal a la que va relacionada el modulo
     * @var entero
     */
    public $tabla;
 
    
     /**
     * Nombre del item
     * @var entero
     */
    public $nombre;
 
    /**
     * lados que tiene la bodega para almacenar
     * @var cadena
     */
    public $lados;
    
    /**
     * lados que tiene la bodega para almacenar
     * @var cadena
     */
    public $bandejas;    


       
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
     * Inicializar una gondola
     *
     * @param entero $id Código interno o identificador del gondola en la base de datos
     *
     */
    public function __construct($id = NULL) {
        global $sql;

        $modulo          = new Modulo('GONDOLAS');
        $this->urlBase   = '/'.$modulo->url;
        $this->url       = $modulo->url;
        $this->idModulo  = $modulo->id;
        $this->tabla     = $modulo->tabla;
        //Saber el numero de registros
        $this->registros = $sql->obtenerValor('gondolas', 'COUNT(id)', 'id != "0" AND id != "999"');
        //Saber el numero de registros activos
        $this->registrosActivos = $sql->obtenerValor('gondolas', 'COUNT(id)', 'activo = "1"');
        //establecer el valor del campo predeterminado para organizar los listados
        $this->ordenInicial = 'g.nombre';        

        if (isset($id)) {
            $this->cargar($id);        
        }
     }//Fin del metodo constructor




    /**
     *
     * Cargar los datos de un item
     *
     * @param entero $id Código interno o identificador del item en la base de datos
     *
     */
    public function cargar($id) {
        global $sql;

        if (isset($id) && $sql->existeItem('gondolas', 'id', intval($id))) {

            $tablas = array(
                'g'  => 'gondolas',
                'b'  => 'bodegas',
                'se' => 'sedes_empresa'
            );

            $columnas = array(
                'id'        => 'g.id',             
                'nombre'    => 'g.nombre',
                'bodega'    => 'b.nombre',
                'sede'      => 'se.nombre',
                'lados'     => 'g.lados',
                'bandejas'  => 'g.bandejas',
                'activo'    => 'g.activo' 
            );

            $condicion = 'g.id_bodega = b.id AND b.id_sede = se.id AND g.id = "'.$id.'"';

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
     * Adicionar una gondola
     *
     * @param  arreglo $datos       Datos de la gondola a adicionar
     * @return entero               Código interno o identificador del gondola en la base de datos (NULL si hubo error)
     *
     */
    public function adicionar($datos) {
        global $sql;

        $datosItem = array();
        
     
        $datosItem['id_bodega']     = $sql->obtenerValor('bodegas', 'id', 'nombre = "'.$datos['id_bodega'].'"');        
        $datosItem['nombre']        = $datos['nombre'];
        $datosItem['lados']         = $datos['lados'];
        $datosItem['bandejas']      = $datos['bandejas'];
        
        if (isset($datos['activo'])) {
            $datosItem['activo']   = '1';            

        } else {
            $datosItem['activo']   = '0';
        }

        $consulta = $sql->insertar('gondolas', $datosItem);

        if ($consulta) {
             $idItem  =  $sql->ultimoId;
             return $idItem;

        } else {
            return NULL;
        }//fin del if($consulta)

    }//fin del metodo adicionar gondolas




    /**
     * Modificar una gondola
     *
     * @param  arreglo $datos       Datos de la gondola a modificar
     * @return lógico               Indica si el procedimiento se pudo realizar correctamente o no
     *
     */
 public function modificar($datos) {
        global $sql;

        if (!isset($this->id)) {
            return NULL;
        }
        
        $datosItem = array();
        
        
        $datosItem['id_bodega']     = $sql->obtenerValor('bodegas', 'id', 'nombre = "'.$datos['id_bodega'].'"');        
        $datosItem['nombre']        = $datos['nombre'];
        $datosItem['lados']         = $datos['lados'];
        $datosItem['bandejas']      = $datos['bandejas'];
        
        if (isset($datos['activo'])) {
            $datosItem['activo']   = '1';            

        } else {
            $datosItem['activo']   = '0';
        }

        //$sql->depurar = true;
        $consulta = $sql->modificar('gondolas', $datosItem, 'id = "'.$this->id.'"');


     if($consulta){
         return 1;  

     }else{
        return NULL;

     }//fin del if(consulta)

 }//fin del metodo Modificar




    /**
     *
     * Eliminar una gondola
     *
     * @param entero $id    Código interno o identificador de una gondola en la base de datos
     * @return lógico       Indica si el procedimiento se pudo realizar correctamente o no
     *
     */
    public function eliminar() {
        global $sql;
       
        if (!isset($this->id)) {
            return NULL;
        }

        if(($consulta = $sql->eliminar('gondolas', 'id = "'.$this->id.'"'))){ 
            
            return true;
            
         }else{
           return false;

         }//fin del si funciono eliminar
  
        
    }//Fin del metodo eliminar Unidades



    

    /**
     *
     * Listar las gondolas
     *
     * @param entero  $cantidad    Número de gondolas a incluir en la lista (0 = todas las entradas)
     * @param arreglo $excepcion   Arreglo con los códigos internos o identificadores a omitir en la lista
     * @param cadena  $condicion   Condición adicional (SQL)
     * @return arreglo             Lista de gondolas
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
        if (isset($excepcion) && is_array($excepcion)) {
            $excepcion = implode(',', $excepcion);
            $condicion .= 'g.id NOT IN ('.$excepcion.') AND ';
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
            'g'  => 'gondolas',
            'b'  => 'bodegas',
            'se' => 'sedes_empresa'
        );

        $columnas = array(
            'id'        => 'g.id',             
            'nombre'    => 'g.nombre',
            'bodega'    => 'b.nombre',
            'sede'      => 'se.nombre',
            'lados'     => 'g.lados',
            'bandejas'  => 'g.bandejas',
            'activo'    => 'g.activo' 
        );
        
        if (!empty($condicionGlobal)) {
            $condicion .= $condicionGlobal.' AND ';
        }        

        $condicion .= ' g.id_bodega = b.id AND b.id_sede = se.id';

      

        if (is_null($this->registrosConsulta)) {
            $sql->seleccionar($tablas, $columnas, $condicion);
            $this->registrosConsulta = $sql->filasDevueltas;
        }
//        $sql->depurar = true;
        $consulta = $sql->seleccionar($tablas, $columnas, $condicion, 'g.id', $orden, $inicio, $cantidad);
        //echo $sql->sentenciaSql;
        if ($sql->filasDevueltas) {
            $lista = array();

            while ($objeto = $sql->filaEnObjeto($consulta)) {
                $objeto->estado =  ($objeto->activo) ? HTML::frase($textos->id('ACTIVO'), 'activo') : HTML::frase($textos->id('INACTIVO'), 'inactivo');
              
                $lista[]           = $objeto;
            }
        }

        return $lista;

    }//Fin del metodo de listar 
    
    
    public function generarTabla($arregloRegistros, $datosPaginacion = NULL){
        global $textos;
        //Declaracion de las columnas que se van a mostrar en la tabla
        $datosTabla = array(
            HTML::parrafo( $textos->id('SEDE')        ,  'centrado' ) => 'sede|se.nombre',
            HTML::parrafo( $textos->id('BODEGA')      ,  'centrado' ) => 'bodega|b.nombre',
            HTML::parrafo( $textos->id('NOMBRE')      ,  'centrado' ) => 'nombre|g.nombre',
            HTML::parrafo( $textos->id('LADOS')       ,  'centrado' ) => 'lados|g.lados',
            HTML::parrafo( $textos->id('BANDEJAS')    ,  'centrado' ) => 'bandejas|g.bandejas',
            HTML::parrafo( $textos->id('ESTADO')      ,  'centrado' ) => 'estado'
        );        
        //ruta a donde se mandara la accion del doble click
        $ruta = '/ajax'.$this->urlBase.'/move';

        
        return Recursos::generarTablaRegistros($arregloRegistros, $datosTabla, $ruta, $datosPaginacion).HTML::crearMenuBotonDerecho('GONDOLAS');
        
    }
    
 
}