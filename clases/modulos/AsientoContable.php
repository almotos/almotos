<?php

/**
 * @package     FOM
 * @subpackage  Bodegas 
 * @author      Pablo Andrés Vélez Vidal 
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2012 Genesys Soft.
 * @version     0.2
 * 
 * Clase encargada de gestionar la información del listado de asientos contables existentes en el negocio para el almacenamiento de los articulos.
 * Modulo utilizado en la compra y venta de mercancias para la gestión del inventario del negocio. Determinando asientos contables según las sedes.
 * Tiene relacion con el modulo "Sedes empresa" ya que una sede puede tener una o muchas asientos contables, y el stock de articulos se almacena en
 * dichas asientos contables.
 * */
class AsientoContable {

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
     * Código interno o identificador de la sede a la que pertenece el asiento contable
     * @var entero
     */
    public $idCuenta;

    /**
     * Objeto sede
     * @var entero
     */
    public $cuenta;

    /**
     * Nombre del item
     * @var entero
     */
    public $comprobante;

    /**
     * Descripción de la ubicación  del item
     * @var cadena
     */
    public $numeroComprobante;

    /**
     * Determina si esta es el asiento contable principal de la sede
     * @var entero
     */
    public $fecha;
    
    /**
     * Determina si esta es el asiento contable principal de la sede
     * @var entero
     */
    public $concepto;
    
    /**
     * Determina si esta es el asiento contable principal de la sede
     * @var entero
     */
    public $credito;
    
    /**
     * Determina si esta es el asiento contable principal de la sede
     * @var entero
     */
    public $debito;

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
     * Número de registros activos de la lista de asientos contables
     * @var entero
     */
    public $registrosActivos = NULL;

    /**
     * Número de registros extraidos en una consulta
     * @var entero
     */
    public $registrosConsulta = NULL;

    /**
     * Orden predeterminado para organizar los listados
     * @var entero
     */
    public $ordenInicial = NULL;

    /**
     * Inicializar una asiento contable
     * @param entero $id Código interno o identificador del asiento contable en la base de datos
     */
    public function __construct($id = NULL) {
        global $sql, $modulo;

        $this->urlBase              = '/' . $modulo->url;
        $this->url                  = $modulo->url;
        $this->idModulo             = $modulo->id;
        //Saber el numero de registros
        $this->registros            = $sql->obtenerValor('asientos_contables', 'COUNT(id)', 'id != "0" ');
        //Saber el numero de registros activos
        $this->registrosActivos     = $sql->obtenerValor('asientos_contables', 'COUNT(id)', 'id != "0"');
        //establecer el valor del campo predeterminado para organizar los listados
        $this->ordenInicial         = 'ac.nombre';

        if (isset($id)) {
            $this->cargar($id);
        }
    }

    /**
     * Cargar los datos de un item
     * 
     * @param entero $id Código interno o identificador del item en la base de datos
     */
    public function cargar($id) {
        global $sql;

        if (isset($id) && $sql->existeItem('asientos_contables', 'id', intval($id))) {

            $tablas = array(
                'ac' => 'asientos_contables',
               
            );

            $columnas = array(
                'id'                => 'ac.id',
                'idCuenta'          => 'ac.id_cuenta',
                'comprobante'       => 'ac.comprobante',
                'numeroComprobante' => 'ac.num_comprobante',
                'fecha'             => 'ac.fecha',
                'conceptol'         => 'ac.concepto',
                'credito'           => 'ac.credito',
                'debito'            => 'ac.debito',
            );

            $condicion = 'ac.id = "' . $id . '"';

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
     * Cargar los datos de un item
     * 
     * @param entero $id Código interno o identificador del item en la base de datos
     */
    public function cargarRegistroContable($tipoComprobante, $idComprobante) {
        global $sql;

        $tablas = array(
            'ac' => 'asientos_contables'

        );

        $columnas = array(
            'id'                => 'ac.id',
            'idCuenta'          => 'ac.id_cuenta',
            'comprobante'       => 'ac.comprobante',
            'numeroComprobante' => 'ac.num_comprobante',
            'fecha'             => 'ac.fecha',
            'concepto'          => 'ac.concepto',
            'credito'           => 'ac.credito',
            'debito'            => 'ac.debito',

        );

        $condicion = 'ac.comprobante = "' . $tipoComprobante . '" AND num_comprobante = "'.$idComprobante.'"';

        $consulta = $sql->seleccionar($tablas, $columnas, $condicion);

        $asientos = array();
        
        while ($objeto = $sql->filaEnObjeto($consulta)) {
            $asientos[] = $objeto;
        }
        
        return $asientos;

    }    

    /**
     * Adicionar un asiento contable
     * 
     * @param  arreglo $datos       Datos del asiento contable a adicionar
     * @return entero               Código interno o identificador del asiento contable en la base de datos (NULL si hubo error)
     */
    public function adicionar($datos) {
        global $sql;

        $datosItem = array();
        
        $datosItem['id_cuenta']         = $datos['id_cuenta'];
        $datosItem['comprobante']       = $datos['comprobante'];
        $datosItem['num_comprobante']   = $datos['num_comprobante'];
        $datosItem['fecha']             = $datos['fecha'];
        $datosItem['concepto']          = $datos['concepto'];
        $datosItem['credito']           = $datos['credito'];
        $datosItem['debito']            = $datos['debito'];
        

        $sql->iniciarTransaccion();  

        $consulta = $sql->insertar('asientos_contables', $datosItem);

        if ($consulta) {
            $idItem = $sql->ultimoId;
            $sql->finalizarTransaccion();
            return $idItem;
            
        } else {
            $sql->cancelarTransaccion();
            return false;
            
        }
        
    }

    /**
     * Modificar la información correspondiente a una asiento contable
     * 
     * @param  arreglo $datos       Datos del asiento contable a modificar
     * @return lógico               Indica si el procedimiento se pudo realizar correctamente o no
     */
    public function modificar($datos) {
        global $sql;

        if (!isset($this->id)) {
            return false;
        }

        $datosItem = array();


        $datosItem['id_cuenta']         = $datos['id_cuenta'];
        $datosItem['comprobante']       = $datos['comprobante'];
        $datosItem['num_comprobante']   = $datos['num_comprobante'];
        $datosItem['fecha']             = $datos['fecha'];
        $datosItem['concepto']          = $datos['concepto'];
        $datosItem['credito']           = $datos['credito'];
        $datosItem['debito']            = $datos['debito'];

        $sql->iniciarTransaccion();
        
        $consulta = $sql->modificar('asientos_contables', $datosItem, 'id = "' . $this->id . '"');


        if ($consulta) {
            $sql->finalizarTransaccion();
            return true;
            
        } else {
            $sql->cancelarTransaccion();
            return false;
            
        }
    }

    /**
     * Eliminar una asiento contable
     * 
     * @param entero $id    Código interno o identificador de una asiento contable en la base de datos
     * @return lógico       Indica si el procedimiento se pudo realizar correctamente o no
     */
    public function eliminar() {
        global $sql;

        if (!isset($this->id)) {
            return false;
        }

        $consulta = $consulta = $sql->eliminar('asientos_contables', 'id = "' . $this->id . '"');
        
        if ($consulta) {
            return true;
            
        } else {
            return false;
            
        }
    }

    /**
     * Listar las asientos contables
     * 
     * @param entero  $cantidad    Número de asientos contables a incluir en la lista (0 = todas las entradas)
     * @param arreglo $excepcion   Arreglo con los códigos internos o identificadores a omitir en la lista
     * @param cadena  $condicion   Condición adicional (SQL)
     * @return arreglo             Lista de asientos contables
     */
    public function listar($inicio = 0, $cantidad = 0, $excepcion = NULL, $condicionGlobal = NULL, $orden = NULL) {
        global $sql;

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
        if (isset($excepcion) && is_array($excepcion)) {
            $excepcion = implode(',', $excepcion);
            $condicion .= 'ac.id NOT IN (' . $excepcion . ') AND ';
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
            'ac' => 'asientos_contables',

        );

        $columnas = array(
            'id'                => 'ac.id',
            'idCuenta'          => 'ac.id_cuenta',
            'cuenta'            => 'ac.cuenta',
            'comprobante'       => 'ac.comprobante',
            'numeroComprobante' => 'ac.num_comprobante',
            'fecha'             => 'ac.fecha',
            'conceptol'         => 'ac.concepto',
            'credito'           => 'ac.credito',
            'debito'            => 'ac.debito',


        );

        if (!empty($condicionGlobal)) {
            $condicion .= $condicionGlobal . ' AND ';
        }

        $condicion .= ' ac.id != "0"';

        if (is_null($this->registrosConsulta)) {
            $sql->seleccionar($tablas, $columnas, $condicion);
            $this->registrosConsulta = $sql->filasDevueltas;
        }
        
        $consulta = $sql->seleccionar($tablas, $columnas, $condicion, 'ac.id', $orden, $inicio, $cantidad);
        
        if ($sql->filasDevueltas) {
            $lista = array();

            while ($objeto = $sql->filaEnObjeto($consulta)) {            
                $lista[] = $objeto;
            }
        }

        return $lista;
    }

    /**
     * Metodo encargado de generar la tabla con el listado de registros de las asientos contables
     * 
     * @global recurso $textos          = objeto global encargado de la traduccion de los textos
     * @param array $arregloRegistros   = arreglo con los registros a mostrar en la tabla
     * @param array $datosPaginacion    = datos con los arreglos para la paginacion
     * @return text                     = texto html con la tabla y el menu del boton derecho
     */
    public function generarTabla($arregloRegistros, $datosPaginacion = NULL) {
        global $textos;
        //Declaracion de las columnas que se van a mostrar en la tabla
        $datosTabla = array(
            HTML::parrafo($textos->id('ID_CUENTA'), 'centrado')      => 'sede|s.nombre',
            HTML::parrafo($textos->id('TIPO_COMPROBANTE'), 'centrado')    => 'nombre|b.nombre',
            HTML::parrafo($textos->id('COMPROBANTE'), 'centrado')    => 'nombre|b.nombre',
            HTML::parrafo($textos->id('CREDITO'), 'centrado')    => 'estado',
            HTML::parrafo($textos->id('DEBITO'), 'centrado')    => 'estado'
        );
        //ruta a donde se mandara la accion del doble click
        $ruta = '/ajax' . $this->urlBase . '/move';


        return Recursos::generarTablaRegistros($arregloRegistros, $datosTabla, $ruta, $datosPaginacion) . HTML::crearMenuBotonDerecho('ASIENTOS_CONTABLES');
    }
    
    
    public function generarTablaRegistroContable($tipoComprobante, $numComprobante){
        global $textos;
        
        $listaAsientos = $this->cargarRegistroContable($tipoComprobante, $numComprobante);  

        $totalCredito = 0;
        $totalDebito  = 0;

        foreach ($listaAsientos as &$lista) {
            //$lista->cuenta = $sql->obtenerValor("plan_contable", "nombre", "codigo_contable = '".$lista->idCuenta."'");
            $totalCredito += $lista->credito;
            $totalDebito  += $lista->debito;
        }    

        //objeto que se va a agregar al final del listado para mostrar las sumatorias de los debitos y los creditos
        $objTotal = new stdClass();
        $objTotal->id           = "999";
        $objTotal->idCuenta     = "";
        $objTotal->comprobante  = "";
        $objTotal->numeroComprobante  = "";
        $objTotal->fecha        = "";
        $objTotal->concepto     = "";
        $objTotal->credito      = "___________";
        $objTotal->debito       = "___________";   

        $listaAsientos[] = $objTotal;

        //objeto que se va a agregar al final del listado para mostrar las sumatorias de los debitos y los creditos
        $objTotal = new stdClass();
        $objTotal->id           = "999";
        $objTotal->idCuenta     = "";
        $objTotal->comprobante  = "";
        $objTotal->numeroComprobante  = "";
        $objTotal->fecha        = "";
        $objTotal->concepto     = HTML::frase($textos->id("TOTALES"), "negrilla");
        $objTotal->credito      = $totalCredito;
        $objTotal->debito       = $totalDebito;   

        $listaAsientos[] = $objTotal;   

        $datosTabla = array(
            HTML::parrafo($textos->id('CUENTA'), 'centrado')        => 'idCuenta',
            HTML::parrafo($textos->id('FECHA'), 'centrado')         => 'fecha',
            HTML::parrafo($textos->id('CONCEPTO'), 'centrado')      => 'concepto',
            HTML::parrafo($textos->id('CREDITO'), 'centrado')       => 'credito',
            HTML::parrafo($textos->id('DEBITO'), 'centrado')        => 'debito',
        );    

        $rutas = array();    

        $idTabla                   = 'tablaListaRegistroContable';
        $clasesColumnas            = array('centrado', 'centrado', 'centrado', 'centrado', 'centrado');

        $tablaListaRegistroContable     = Recursos::generarTablaRegistrosInterna($listaAsientos, $datosTabla, $rutas, $idTabla, $clasesColumnas);
        
        return $tablaListaRegistroContable;
    }

}
