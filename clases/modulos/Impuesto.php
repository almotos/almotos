<?php

/**
 * @package     FOLCS
 * @subpackage  Impuestos 
 * @author      Pablo Andrés Vélez Vidal 
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2013 Genesys Soft.
 * @version     0.2
 * 
 * Impuestos utilizados para la parte tributaria del sistema.
 * 
 * Clase encargada de gestionar la información del listado de impuestos existentes en el sistema. En este módulo se pueden
 * agregar, consultar, eliminar o modificar la información de los impuestos. Este modulo se relaciona con el modulo de ActividadImpuesto
 * ya que engloba estos registros, es decir, una Actividad tiene 0 o muchos impuestos.
 * 
 * tabla principal: impuestos.
 * 
 * */
class Impuesto {

    /**
     * Código interno o identificador del impuestoen la base de datos
     * @var entero
     */
    public $id;

    /**
     * URL relativa del módulo de impuestos
     * @var cadena
     */
    public $urlBase;

    /**
     * Código interno o identificador del modulo
     * @var entero
     */
    public $idModulo;

    /**
     * Nombre del impuesto
     * @var entero
     */
    public $nombre;

    /**
     * Determina si este impuesto se aplica a clientes
     * @var entero
     */
    public $aplicaClientes;
    
    /**
     * Determina si este impuesto se aplica a clientes
     * @var entero
     */
    public $aplicaProveedores;    

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
     * Número de registros activos 
     * @var entero
     */
    public $registrosActivos = NULL;

    /**
     * Número de registros en una consulta
     * @var entero
     */
    public $registrosConsulta = NULL;

    /**
     * Orden predeterminado para organizar los listados
     * @var entero
     */
    public $ordenInicial = NULL;

    /**
     * Inicializar un impuesto
     * @param entero $id Código interno o identificador del impuesto en la base de datos
     */
    public function __construct($id = NULL) {
        global $sql, $modulo;
        $this->urlBase              = '/' . $modulo->url;
        $this->idModulo             = $modulo->id;
        //Saber el numero de registros
        $this->registros            = $sql->obtenerValor('impuestos', 'COUNT(id)', 'id != "0" ');
        //Saber el numero de registros activos
        $this->registrosActivos     = $sql->obtenerValor('impuestos', 'COUNT(id)', 'activo = "1" ');
        //establecer el valor del campo predeterminado para organizar los listados
        $this->ordenInicial         = 'nombre';

        if (isset($id)) {
            $this->cargar($id);
        }
    }


    /**
     * Cargar los datos de un impuesto
     * @param entero $id Código interno o identificador del impuesto en la base de datos
     */
    public function cargar($id) {
        global $sql;

        if (isset($id) && $sql->existeItem('impuestos', 'id', intval($id))) {

            $tablas = array(
                'i' => 'impuestos'
            );

            $columnas = array(
                'id'                    => 'i.id',
                'nombre'                => 'i.nombre',
                'aplicaClientes'        => 'i.aplica_clientes',
                'aplicaProveedores'     => 'i.aplica_proveedores',
                'activo'                => 'i.activo'
            );

            $condicion = 'i.id = "' . $id . '"';

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
     * Adicionar un impuesto
     * @param  arreglo $datos       Datos del impuesto a adicionar
     * @return entero               Código interno o identificador del impuesto en la base de datos (NULL si hubo error)
     */
    public function adicionar($datos) {
        global $sql;

        $datosItem = array();

        $datosItem['nombre']                = $datos['nombre'];
        $datosItem['aplica_clientes']       =  (isset($datos['aplica_clientes'])) ? '1' : '0';
        $datosItem['aplica_proveedores']    =  (isset($datos['aplica_proveedores'])) ? '1' : '0';
        $datosItem['activo']                =  (isset($datos['activo'])) ? '1' : '0';


        $consulta = $sql->insertar('impuestos', $datosItem);

        if ($consulta) {
            $idItem = $sql->ultimoId;
            return $idItem;
            
        } else {
            return false;
            
        }//fin del if($consulta)
    }


    /**
     * Modificar un impuesto
     * @param  arreglo $datos       Datos del impuesto a modificar
     * @return lógico               Indica si el procedimiento se pudo realizar correctamente o no
     */
    public function modificar($datos) {
        global $sql;

        if (!isset($this->id)) {
            return NULL;
        }

        $datosItem = array();

        $datosItem['nombre']                = $datos['nombre'];
        $datosItem['aplica_clientes']       =  (isset($datos['aplica_clientes'])) ? '1' : '0';
        $datosItem['aplica_proveedores']    =  (isset($datos['aplica_proveedores'])) ? '1' : '0';
        $datosItem['activo']                =  (isset($datos['activo'])) ? '1' : '0';
        //$sql->depurar = true;
        $consulta = $sql->modificar('impuestos', $datosItem, 'id = "' . $this->id . '" ');


        if ($consulta) {
            return true;
            
        } else {
            return false;
            
        }//fin del if(consulta)
    }


    /**
     * Eliminar un impuesto
     * @param entero $id    Código interno o identificador de un impuesto en la base de datos
     * @return lógico       Indica si el procedimiento se pudo realizar correctamente o no
     */
    public function eliminar() {
        global $sql;

        if (!isset($this->id)) {
            return NULL;
        }
        
        $consulta = $sql->eliminar('impuestos', 'id = "' . $this->id . '"');

        if ($consulta) {
            return true;
            
        } else {
            return false;
            
        }//fin del si funciono eliminar
    }

    /**
     * Listar los impuestos
     * @param entero  $cantidad    Número de impuestos a incluir en la lista (0 = todas las entradas)
     * @param arreglo $excepcion   Arreglo con los códigos internos o identificadores a omitir en la lista
     * @param cadena  $condicion   Condición adicional (SQL)
     * @return arreglo             Lista de impuestos
     */
    public function listar($inicio = 0, $cantidad = 0, $excepcion = NULL, $condicionGlobal = NULL, $orden = NULL) {
        global $sql, $textos;

        /*         * * Validar la fila inicial de la consulta ** */
        if (!is_int($inicio) || $inicio < 0) {
            $inicio = 0;
        }

        /*         * * Validar la cantidad de registros requeridos en la consulta ** */
        if (!is_int($cantidad) || $cantidad <= 0) {
            $cantidad = 0;
        }

        /*         * * Validar que la condición sea una cadena de texto ** */
        if (!is_string($condicionGlobal)) {
            $condicion = '';
        }

        /*         * * Validar que la excepción sea un arreglo y contenga elementos ** */
        if (isset($excepcion) && is_array($excepcion) && count($excepcion)) {
            $excepcion = implode(',', $excepcion);
            $condicion .= 'i.id NOT IN (' . $excepcion . ')';
        }

        /*         * * Definir el orden de presentación de los datos ** */
        if (!isset($orden)) {
            $orden = $this->ordenInicial;
        }
        if ($this->listaAscendente) {
            $orden = $orden . ' ASC';
        } else {
            $orden = $orden . ' DESC';
        }


        $tablas = array(
            'i' => 'impuestos'
        );

        $columnas = array(
            'id'                    => 'i.id',
            'nombre'                => 'i.nombre',
            'aplicaClientes'        => 'i.aplica_clientes',
            'aplicaProveedores'     => 'i.aplica_proveedores',
            'activo'                => 'i.activo'
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
        //$sql->depurar = true;
        $consulta = $sql->seleccionar($tablas, $columnas, $condicion, 'i.id', $orden, $inicio, $cantidad);
        
        if ($sql->filasDevueltas) {
            $lista = array();

            while ($objeto = $sql->filaEnObjeto($consulta)) {
                $objeto->estado             = ($objeto->activo) ? HTML::frase($textos->id('ACTIVO'), 'activo') : HTML::frase($textos->id('INACTIVO'), 'inactivo');
                $objeto->aplicaProveedores  = ($objeto->aplicaProveedores) ? HTML::frase($textos->id('SI'), 'activo') : HTML::frase($textos->id('NO'), 'inactivo');
                $objeto->aplicaClientes     = ($objeto->aplicaClientes) ? HTML::frase($textos->id('SI'), 'activo') : HTML::frase($textos->id('NO'), 'inactivo');
                $lista[]                    = $objeto;
                
            }
        }

        return $lista;
    }


    /**
     * Metodo que arma la grilla para mostrarse desde la pagina principal
     * @global type $textos
     * @param type $arregloRegistros
     * @param type $datosPaginacion
     * @return type 
     */
    public function generarTabla($arregloRegistros, $datosPaginacion = NULL) {
        global $textos;
        //Declaracion de las columnas que se van a mostrar en la tabla
        $datosTabla = array(
            HTML::parrafo($textos->id('NOMBRE'), 'centrado')                => 'nombre|i.nombre',
            HTML::parrafo($textos->id('APLICA_PROVEEDORES'), 'centrado')    => 'aplicaProveedores|i.aplica_proveedores',
            HTML::parrafo($textos->id('APLICA_CLIENTES'), 'centrado')       => 'aplicaClientes|i.aplica_clientes',            
            HTML::parrafo($textos->id('ESTADO'), 'centrado')                => 'estado'
        );
        //ruta a donde se mandara la accion del doble click
        $ruta = '/ajax' . $this->urlBase . '/move';

        return Recursos::generarTablaRegistros($arregloRegistros, $datosTabla, $ruta, $datosPaginacion) . HTML::crearMenuBotonDerecho('IMPUESTOS');
    }

}