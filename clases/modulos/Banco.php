<?php

/**
 *
 * @package     FOLCS
 * @subpackage  bancos 
 * @author      Pablo Andrés Vélez Vidal <pavelez@genesyscorp.com.co>
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2012 Genesys Soft.
 * @version     0.2
 * 
 * Clase encargada de gestionar los registros de los bancos utilizadas en el sistema. Es utilizada normalmente
 * en el modulo de proveedores para las cuentas de los proveedores, y en la administración contable para gestionar
 * la información de las cuentas BANCOS. * 
 *
 * */
class Banco {

    /**
     * Código interno o identificador del banco en la base de datos
     * @var entero
     */
    public $id;

    /**
     * URL relativa del módulo de bancos
     * @var cadena
     */
    public $urlBase;

    /**
     * URL relativa del módulo de bancos
     * @var cadena
     */
    public $url;

    /**
     * Código interno o identificador del modulo
     * @var entero
     */
    public $idModulo;

    /**
     * Nombre del banco
     * @var entero
     */
    public $nombre;

    /**
     * Determina si el registro se encuentra activo o no
     * @var entero
     */
    public $activo;

    /**
     * Indicador del orden  de la lista de registros
     * @var lógico
     */
    public $listaAscendente = true;

    /**
     * Número de registros de la lista
     * @var entero
     */
    public $registros = NULL;

    /**
     * Número de registros activos de la lista de bancos
     * @var entero
     */
    public $registrosActivos = NULL;

    /**
     * Número de registros en una consulta para la lista de bancos
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
     * Inicializar un objeto de la clase BANCO
     *
     * @param entero $id Código interno o identificador del banco en la base de datos
     *
     */
    public function __construct($id = NULL) {
        global $sql, $modulo;
        
        $this->urlBase              = '/' . $modulo->url;
        $this->url                  = $modulo->url;
        $this->idModulo             = $modulo->id;
        //Saber el numero de registros
        $this->registros            = $sql->obtenerValor('bancos', 'COUNT(id)', 'id != "0"');
        //Saber el numero de registros activos
        $this->registrosActivos     = $sql->obtenerValor('bancos', 'COUNT(id)', 'activo = "1"');
        //establecer el valor del campo predeterminado para organizar los listados
        $this->ordenInicial         = 'nombre';

        if (isset($id)) {
            $this->cargar($id);
            
        }
        
    }


    /**
     *
     * Cargar los datos de un banco
     *
     * @param entero $id Código interno o identificador del banco en la base de datos
     */
    public function cargar($id) {
        global $sql;

        if (isset($id) && $sql->existeItem('bancos', 'id', intval($id))) {

            $tablas = array(
                'b' => 'bancos'
            );

            $columnas = array(
                'id'        => 'b.id',
                'nombre'    => 'b.nombre',
                'activo'    => 'b.activo'
            );

            $condicion = 'b.id = "' . $id . '"';

            $consulta = $sql->seleccionar($tablas, $columnas, $condicion);

            if ($sql->filasDevueltas) {
                $fila = $sql->filaEnObjeto($consulta);

                foreach ($fila as $propiedad => $valor) {
                    $this->$propiedad = $valor;
                    
                }

                $this->url = $this->urlBase . '/' . $this->id;
                
            }
            
        }
        
    }


    /**
     *
     * Adicionar un banco
     *
     * @param  arreglo $datos       Datos del banco a adicionar
     * @return entero               Código interno o identificador del banco ingresado en la base de datos (NULL si hubo error)
     */
    public function adicionar($datos) {
        global $sql;

        $datosItem = array();
        $datosItem['nombre'] = $datos['nombre'];

        $datosItem['activo'] = (isset($datos['activo'])) ? '1' : '0';

        $consulta = $sql->insertar('bancos', $datosItem);

        if ($consulta) {
            $idItem = $sql->ultimoId;
            return $idItem;
            
        } else {
            return false;
            
        }
    }


    /**
     * Modificar la informacion en la BD de un banco
     *
     * @param  arreglo $datos       Datos del banco a modificar
     * @return lógico               Indica si el procedimiento se pudo realizar correctamente o no
     */
    public function modificar($datos) {
        global $sql;

        if (!isset($this->id)) {
            return false;
        }
        
        $datosItem = array();

        $datosItem['nombre'] = $datos['nombre'];

        $datosItem['activo'] = (isset($datos['activo'])) ? '1' : '0';

        $consulta = $sql->modificar('bancos', $datosItem, 'id = "' . $this->id . '"');

        if ($consulta) {
            return $this->id;
            
        } else {
            return false;
            
        }
        
    }


    /**
     *
     * Eliminar el registro de un banco en la BD
     *
     * @param entero $id    Código interno o identificador del banco en la base de datos
     * @return lógico       Indica si el procedimiento se pudo realizar correctamente o no
     *
     */
    public function eliminar() {
        global $sql;

        if (!isset($this->id)) {
            return false;
        }

        $consulta = $sql->eliminar('bancos', "id = '" . $this->id . "'");
        
        if (!($consulta)) {
            return false;
            
        } else {
            return true;
            
        }
        
    }


    /**
     *
     * Listar los bancos 
     *
     * @param entero  $cantidad    Número de bancos a incluir en la lista (0 = todas las entradas)
     * @param arreglo $excepcion   Arreglo con los códigos internos o identificadores a omitir en la lista
     * @param cadena  $condicion   Condición adicional (SQL)
     * @return arreglo             Lista de bancos
     *
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
            $condicion .= 'b.id NOT IN (' . $excepcion . ')';
            
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
            'b' => 'bancos'
        );

        $columnas = array(
            'id'        => 'b.id',
            'nombre'    => 'b.nombre',
            'activo'    => 'b.activo'
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

        $consulta = $sql->seleccionar($tablas, $columnas, $condicion, 'b.id', $orden, $inicio, $cantidad);
        
        if ($sql->filasDevueltas) {
            $lista = array();

            while ($objeto = $sql->filaEnObjeto($consulta)) {
                $objeto->url        = $this->urlBase . '/' . $objeto->id;
                $objeto->idModulo   = $this->idModulo;
                
                $objeto->estado =  ($objeto->activo) ?  HTML::frase($textos->id('ACTIVO'), 'activo') : HTML::frase($textos->id('INACTIVO'), 'inactivo');

                $lista[] = $objeto;
                
            }
            
        }

        return $lista;
        
    }


    /**
     * Metodo encargado de generar la tabla con el listado de registros de los bancos
     * 
     * @global objeto $textos           = objeto global que contiene los textos traducidos
     * @param array $arregloRegistros   = contiene el arreglo de registros a ser incluidos en la tabla
     * @param array $datosPaginacion    = contiene los datos de la paginacion (pagina actual, total registros por pagina, etc.)
     * @return type
     */
    public function generarTabla($arregloRegistros, $datosPaginacion = NULL) {
        global $textos;
        //Declaracion de las columnas que se van a mostrar en la tabla
        $datosTabla = array(
            HTML::parrafo($textos->id('NOMBRE'), 'centrado') => 'nombre|b.nombre',
            HTML::parrafo($textos->id('ESTADO'), 'centrado') => 'estado'
            
        );
        //ruta a donde se mandara la accion del doble click
        $ruta = '/ajax' . $this->urlBase . '/move';

        $tabla          = Recursos::generarTablaRegistros($arregloRegistros, $datosTabla, $ruta, $datosPaginacion);
        $menuDerecho    = HTML::crearMenuBotonDerecho('BANCOS');

        return $tabla . $menuDerecho;
    }

}
