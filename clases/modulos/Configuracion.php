<?php

/**
 *
 * @package     FOLCS
 * @subpackage  Configuraciones
 * @author      Pablo Andrés Vélez Vidal <pavelez8@misena.edu.co>
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2012 Genesys corporation.
 * @version     0.2
 * 
 * Clase encargada de gestionar la información de la configuracion del sistema. En este módulo se pueden parametrizar
 * los datos de configuración del sistema, como por ejemplo: "permitir que los vendedores modifiquen el precio en la venta: S / N",
 * "Que tipo de costeo se utilizara: 1=LIFO, 2=FIFO, 3=PROMEDIO", "Digitar el año en que se comienza el saldo de inventarios:_____", etc, etc.
 * Estos datos son usados a lo largo de los distintos modulos del sistema, y dependiendo de los mismos sera el funcionamiento del sistema.
 *
 * */
class Configuracion {

    /**
     * Código interno o identificador de la configuracion en la base de datos
     * @var entero
     */
    public $id;

    /**
     * URL relativa del módulo de la configuracion
     * @var cadena
     */
    public $urlBase;

    /**
     * URL relativa de una configuracion específica
     * @var cadena
     */
    public $url;

    /**
     * Nombre de la configuracion
     * @var cadena
     */
    public $cantidadDecimales;

    /**
     * Nombre de la configuracion
     * @var cadena
     */
    public $tipoMoneda;
    
    /**
     * Nota que aparece en las facturas de venta
     * @var cadena
     */
    public $notaFactura;    
    
    /**
     * Dato que determina con que dato se va a imprimir el código de barras, ej: plu_interno (predeterminado), referencia, id, etc.
     * @var cadena
     */
    public $datoCodigoBarra;      
    
    /**
     * Dato que determina el IVA que se tomara como base siepre en los campos de textos que vayan a contener el IVA
     * @var cadena
     */
    public $ivaGeneral;   
    
    /**
     * Objeto que represnta la informacion de la empresa
     * @var objeto
     */
    public $empresa;     
    
    /**
     * Dato que determina el IVA que se tomara como base siepre en los campos de textos que vayan a contener el IVA
     * @var cadena
     */
    public $porcPredGanancia;     
    
    /**
     * Dato que determina el numero de días a ser utilizado para realizar el calculo del valor promedio de un articulo
     * @var cadena
     */
    public $diasPromedioPonderado;  
    
    
    /**
     * Dato que determina que campo será el predeterminado para reportes y consultas de un articulo, se escoge entre id (autonumerico), plu_interno, codigo_oem
     * @var cadena
     */
    public $idPrincipalArticulo;     
    
    /**
     * Dato que determina si el sistema permite facturar en negativo
     * @var cadena
     */
    public $facturarNegativo;    

    /**
     * Valor del uvt para el año en curso
     * @var cadena
     */
    public $valorUvt;     
    
    /**
     * Indicador del orden cronológio de la lista de configuraciones
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
    public $registrosConsulta = NULL;

    /**
     * Orden predeterminado para organizar los listados
     * @var entero
     */
    public $ordenInicial = NULL;

    /**
     *
     * Inicializar la Configuracion
     *
     * @param entero $id Código interno o identificador de la configuracion en la base de datos
     *
     */
    public function __construct($id = NULL) {
        global $sql, $modulo;
        $this->urlBase          = '/' . $modulo->url;
        $this->url              = $modulo->url;

        $this->registros        = $sql->obtenerValor('configuraciones', 'COUNT(id)', 'id != "0"');
        //establecer el valor del campo predeterminado para organizar los listados
        $this->ordenInicial     = 'id';

        if (isset($id)) {
            $this->cargar($id);
            
        }
        
    }

    /**
     *
     * Cargar los datos de una configuracion
     *
     * @param entero $id Código interno o identificador de la configuracion en la base de datos
     *
     */
    public function cargar($id) {
        global $sql;

        if (isset($id) && $sql->existeItem('configuraciones', 'id', intval($id))) {

            $tablas = array(
                'c' => 'configuraciones'
            );

            $columnas = array(
                'id'                    => 'c.id',
                'cantidadDecimales'     => 'c.cantidad_decimales',
                'tipoMoneda'            => 'c.tipo_moneda',
                'notaFactura'           => 'c.nota_factura',
                'datoCodigoBarra'       => 'c.dato_codigo_barra',
                'ivaGeneral'            => 'c.iva_general',
                'porcPredGanancia'      => 'c.porc_pred_ganancia',
                'diasPromedioPonderado' => 'c.dias_promedio_ponderado',
                'idPrincipalArticulo'   => 'c.id_principal_articulo',
                'facturarNegativo'      => 'c.facturar_negativo',
                'valorUvt'              => 'c.valor_uvt',
            );

            $condicion = 'c.id = "'.$id.'"';

            
            $consulta = $sql->seleccionar($tablas, $columnas, $condicion);

            if ($sql->filasDevueltas) {
                $fila = $sql->filaEnObjeto($consulta);

                foreach ($fila as $propiedad => $valor) {
                    $this->$propiedad = $valor;
                }

                $this->url = $this->urlBase . '/' . $this->usuario;
                $this->empresa = new Empresa(1);
            }
        }
    }

    /**
     *
     * Modificar una configuracion
     *
     * @param  arreglo $datos       Datos de la configuracion a modificar
     * @return lógico               Indica si el procedimiento se pudo realizar correctamente o no
     *
     */
    public function modificar($datos) {
        global $sql;

        if (!isset($this->id)) {
            return NULL;
        }

        $datosConfig= array(
            'cantidad_decimales'        => $datos['cantidad_decimales'],
            'tipo_moneda'               => $datos['tipo_moneda'],
            'nota_factura'              => $datos['nota_factura'],
            'dato_codigo_barra'         => $datos['dato_codigo_barra'],
            'iva_general'               => $datos['iva_general'],
            'porc_pred_ganancia'        => $datos['porc_pred_ganancia'],
            'dias_promedio_ponderado'   => $datos['dias_promedio_ponderado'],
            'id_principal_articulo'     => $datos['id_principal_articulo'],
            'facturar_negativo'         => '0',
            'valor_uvt'                 => $datos['valor_uvt'],
        );
        
        if($datos['facturar_negativo']){
            $datosConfig['facturar_negativo'] = '1';
        }

        //$sql->depurar = true;
        $consulta = $sql->modificar('configuraciones', $datosConfig, 'id = "' . $this->id . '"');
        return $consulta;
    }

    /**
     *
     * Listar las configuraciones
     *
     * @param entero  $cantidad    Número de ciudadesa incluir en la lista (0 = todas las entradas)
     * @param arreglo $excepcion   Arreglo con los códigos internos o identificadores a omitir en la lista
     * @param cadena  $condicion   Condición adicional (SQL)
     * @return arreglo             Lista de configuraciones
     *
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
        if (isset($excepcion) && is_array($excepcion) && count($excepcion)) {
            $excepcion = implode(',', $excepcion);
            $condicion = 'c.id NOT IN ('.$excepcion.') ';
        }


        /*         * * Definir el orden de presentación de los datos ** */
        if (!isset($orden)) {
            $orden = $this->ordenInicial;
        }
        if ($this->listaAscendente) {
            $orden = $orden.' ASC';
        } else {
            $orden = $orden.' DESC';
        }


        $tablas = array(
            'c' => 'configuraciones'
        );

        $columnas = array(
            'id' => 'c.id',
            'cantidadDecimales' => 'c.cantidad_decimales',
            'tipoMoneda' => 'c.tipo_moneda'
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

        $consulta = $sql->seleccionar($tablas, $columnas, $condicion, '', $orden, $inicio, $cantidad);

        if ($sql->filasDevueltas) {
            $lista = array();

            while ($_configuracion = $sql->filaEnObjeto($consulta)) {
                $lista[] = $_configuracion;
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
    public function generarTabla($arregloRegistros, $datosPaginacion = NULL) {
        global $textos;
        //Declaracion de las columnas que se van a mostrar en la tabla
        $datosTabla = array(
            HTML::parrafo($textos->id('CANTIDAD_DECIMALES'), 'centrado') => 'cantidadDecimales|c.cantidad_decimales',
            HTML::parrafo($textos->id('TIPO_MONEDA'), 'centrado') => 'tipoMoneda|c.tipo_moneda'
        );
        //ruta a donde se mandara la accion del doble click
        $rutaPaginador = '/ajax' . $this->urlBase . '/move';

        return Recursos::generarTablaRegistros($arregloRegistros, $datosTabla, $rutaPaginador, $datosPaginacion) . HTML::crearMenuBotonDerecho('CONFIGURACIONES', '', array('borrar' => true));
    }

}