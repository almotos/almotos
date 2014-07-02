<?php

/**
 * @package     FOM
 * @subpackage  Movimientos Mercancia 
 * @author      Pablo Andrés Vélez Vidal 
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2012 Genesys Soft.
 * @version     0.2
 * 
 * Movimientos de mercancia que se realizan sobre el inventario de existencias de articulos
 * */
class Kardex {

    /**
     * Código interno o identificador del registro
     * @var entero
     */
    public $id;

    /**
     * URL relativa del módulo de movimientos
     * @var cadena
     */
    public $urlBase;

    /**
     * URL relativa del modulo
     * @var cadena
     */
    public $url;

    /**
     * Identificador del articulo sobre el cual se realiza el movimiento
     * @var cadena
     */
    public $idArticulo;    
    
    /**
     * Nombre del articulo
     * @var cadena
     */
    public $articulo;
    
    /**
     * Fecha en la que se realiz{o este registro en el kardex
     * @var cadena
     */
    public $fecha;     
    
    /**
     * Concepto del registro = compras, ventas, devolucion_compras, devolucion_ventas
     * @var cadena
     */
    public $concepto;    
    
    /**
     * Numero de la factura compra/venta o nota credito/debito
     * @var cadena
     */
    public $numFactura;      
    
    /**
     * Cantidad de articulos comprados
     * @var cadena
     */
    public $cantidadCompra;    

    /**
     * Valor unitario de articulos comprados
     * @var cadena
     */
    public $valUnitarioCompra;      
    
    /**
     * Valor total de los articulos comprados
     * @var cadena
     */
    public $valTotalCompra;     
    
    /**
     * Cantidad de articulos Vendidos
     * @var cadena
     */
    public $cantidadVenta;    

    /**
     * Valor unitario de articulos Vendidos
     * @var cadena
     */
    public $valUnitarioVenta;      
    
    /**
     * Valor total de los articulos Vendidos
     * @var cadena
     */
    public $valTotalVenta;    
    
    /**
     * Cantidad de articulos total
     * @var cadena
     */
    public $cantidadSaldo;    

    /**
     * Valor unitario de articulos en general usando el promedio ponderado
     * @var cadena
     */
    public $valUnitarioSaldo;      
    
    /**
     * Valor total del saldo
     * @var cadena
     */
    public $valTotalSaldo;        
    
    /**
     * Indicador del orden cronológio de la lista de acciones
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
     * Inicializar una bodega
     * @param entero $id Código interno o identificador del bodega en la base de datos
     */
    public function __construct($id = NULL) {
        global $sql;

        $this->urlBase  = '/kardex';
        $this->url      = 'kardex';
        //Saber el numero de registros
        $this->registros = $sql->obtenerValor('kardex', 'COUNT(id)', 'id != "0"');
        //Saber el numero de registros activos
        $this->registrosActivos = $sql->obtenerValor('kardex', 'COUNT(id)', 'id != "0"');
        //establecer el valor del campo predeterminado para organizar los listados
        $this->ordenInicial = 'k.id';

        if (isset($id)) {
            $this->cargar($id);
        }
    }

    /**
     * Cargar los datos de un item
     * @param entero $id Código interno o identificador del item en la base de datos
     */
    public function cargar($id) {
        global $sql;

        if (isset($id) && $sql->existeItem('kardex', 'id', intval($id))) {

            $tablas = array(
                'k' => 'kardex'
            );

            $columnas = array(
                'id'                    => 'k.id',
                'idArticulo'            => 'k.id_articulo',
                'fecha'                 => 'k.fecha',
                'concepto'              => 'k.concepto',
                'numFactura'            => 'k.num_factura',
                'cantidadCompra'        => 'k.cantidad_compra',
                'valUnitarioCompra'     => 'k.val_unitario_compra',
                'valTotalCompra'        => 'k.val_total_compra',
                'cantidadVenta'         => 'k.cantidad_venta',
                'valUnitarioVenta'      => 'k.val_unitario_venta',
                'valTotalVenta'         => 'k.val_total_venta',
                'cantidad_saldo'        => 'k.cantidad_saldo',
                'valUnitarioSaldo'      => 'k.val_unitario_saldo',
                'totalSaldo'            => 'k.total_saldo',
            );

            $condicion = 'k.id = "'.$id.'"';

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
     * Adicionar un registro de movimiento de mercancias y asea compras, ventas o devoluciones de las mismas en
     * la tabla de kardex.
     * 
     * @param  arreglo $datos       Datos de la registro a adicionar
     * @return entero               Código interno o identificador del registro en la base de datos (NULL si hubo error)
     */
    public static function adicionar($datos) {
        global $sql;
        
        //ultimo id de registro de la tabla de kardex para el articulo a ser procesado
        $ultimoId = $sql->obtenerValor("kardex", "max(id)", 'id_articulo = "'.$datos["id_articulo"].'"');        
        
        //En estos calculos hay que tener en cuenta cuando sea devolucion en compras o ventas
        $totalCantidadSaldo = $sql->obtenerValor("kardex", "cantidad_saldo", 'id= "'.$ultimoId.'"');
        $totalDineroSaldo   = $sql->obtenerValor("kardex", "total_saldo", 'id= "'.$ultimoId.'"');
        
        //verificar el concepto de el registro en kardex y adecuar los datos en consecuencia
        switch ($datos['concepto']) {
            case 'compras'  : 
                                $cantidadSaldo = $totalCantidadSaldo + $datos['cantidad_saldo'];        
                                $totalSaldo    = $totalDineroSaldo + $datos['total_saldo'];        
                                $valUnitarioSaldo = $totalSaldo / ( ($cantidadSaldo <= 0) ? 1 : $cantidadSaldo );                

                                 break;

            case 'ventas'   : 
                                $cantidadSaldo = $totalCantidadSaldo - $datos['cantidad_saldo'];        
                                $totalSaldo    = $totalDineroSaldo - $datos['total_saldo'];        
                                $valUnitarioSaldo = $totalSaldo / ( ($cantidadSaldo <= 0) ? 1 : $cantidadSaldo );                

                                 break;                

            case 'devolucion_compras' : 
                                $cantidadSaldo = $totalCantidadSaldo - $datos['cantidad_saldo'];        
                                $totalSaldo    = $totalDineroSaldo - $datos['total_saldo'];        
                                $valUnitarioSaldo = $totalSaldo / ( ($cantidadSaldo <= 0) ? 1 : $cantidadSaldo );                

                                 break;   
                             
            case 'devolucion_ventas' : 
                                $cantidadSaldo = $totalCantidadSaldo + $datos['cantidad_saldo'];        
                                $totalSaldo    = $totalDineroSaldo + $datos['total_saldo'];        
                                $valUnitarioSaldo = $totalSaldo / ( ($cantidadSaldo <= 0) ? 1 : $cantidadSaldo );                

                                 break;                              

        }       
        
        $datosItem = array(
            'id_articulo'           => $datos['id_articulo'],
            'fecha'                 => $datos['fecha'],
            'concepto'              => $datos['concepto'],//compras, ventas, devolucion_compras, devolucion_ventas
            'num_factura'           => $datos['num_factura'],
            'cantidad_compra'       => $datos['cantidad_compra'],
            'val_unitario_compra'   => $datos['val_unitario_compra'],
            'val_total_compra'      => $datos['val_total_compra'],
            'cantidad_venta'        => $datos['cantidad_venta'],
            'val_unitario_venta'    => $datos['val_unitario_venta'],
            'val_total_venta'       => $datos['val_total_venta'],
            'cantidad_saldo'        => $cantidadSaldo,
            'val_unitario_saldo'    => $valUnitarioSaldo,
            'total_saldo'           => $totalSaldo,
        );

        $consulta = $sql->insertar('kardex', $datosItem);

        if ($consulta) {
            $idItem = $sql->ultimoId;
            return $idItem;
            
        } else {
            return false;
            
        }
        
    }

    /**
     * Listar las movimientos_mercancia
     * @param entero  $cantidad    Número de movimientos_mercancia a incluir en la lista (0 = todas las entradas)
     * @param arreglo $excepcion   Arreglo con los códigos internos o identificadores a omitir en la lista
     * @param cadena  $condicion   Condición adicional (SQL)
     * @return arreglo             Lista de movimientos_mercancia
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
            $condicion .= 'k.id NOT IN (' . $excepcion . ') AND ';
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
            'k' => 'kardex',
            'a' => 'articulos'
        );

        $columnas = array(
            'id'                    => 'k.id',
            'idArticulo'            => 'k.id_articulo',
            'articulo'              => 'a.nombre',
            'pluInterno'            => 'a.plu_interno',
            'fecha'                 => 'k.fecha',
            'concepto'              => 'k.concepto',
            'numFactura'            => 'k.num_factura',
            'cantidadCompra'        => 'k.cantidad_compra',
            'valUnitarioCompra'     => 'k.val_unitario_compra',
            'valTotalCompra'        => 'k.val_total_compra',
            'cantidadVenta'         => 'k.cantidad_venta',
            'valUnitarioVenta'      => 'k.val_unitario_venta',
            'valTotalVenta'         => 'k.val_total_venta',
            'cantidadSaldo'         => 'k.cantidad_saldo',
            'valUnitarioSaldo'      => 'k.val_unitario_saldo',
            'valTotalSaldo'         => 'k.total_saldo',
        );

        if (!empty($condicionGlobal)) {
            $condicion .= $condicionGlobal . ' AND ';
        }

        $condicion .= 'k.id_articulo = a.id';

        if (is_null($this->registrosConsulta)) {
            $sql->seleccionar($tablas, $columnas, $condicion);
            $this->registrosConsulta = $sql->filasDevueltas;
            
        }

        $sql->depurar = true;
        $consulta = $sql->seleccionar($tablas, $columnas, $condicion, 'k.id', $orden, $inicio, $cantidad);
        
        if ($sql->filasDevueltas) {
            $lista = array();

            while ($objeto = $sql->filaEnObjeto($consulta)) {
                $objeto->idArticulo = (int)$objeto->idArticulo;
                $objeto->numFactura = (int)$objeto->numFactura;
                
                $objeto->valUnitarioCompra  = '$ '.$objeto->valUnitarioCompra;
                $objeto->valTotalCompra     = '$ '.$objeto->valTotalCompra;
                $objeto->valUnitarioVenta   = '$ '.$objeto->valUnitarioVenta;
                $objeto->valTotalVenta      = '$ '.$objeto->valTotalVenta;
                $objeto->valUnitarioSaldo   = '$ '.$objeto->valUnitarioSaldo;
                $objeto->valTotalSaldo      = '$ '.$objeto->valTotalSaldo;                
                
                $lista[] = $objeto;
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
    public function generarTabla($arregloRegistros, $datosPaginacion = NULL) {
        global $textos, $sesion_configuracionGlobal;

        $idPrincipalArticulo    = (string)$sesion_configuracionGlobal->idPrincipalArticulo;
        
   
        $arrayIdArticulo        = array('id' => $textos->id('ID_AUTOMATICO'), 'plu_interno' => $textos->id('PLU'));
        
        if ($idPrincipalArticulo == "id") {
            $busquedaIdArticulo = "k.id_articulo";
            $idPrincipalArticulo1 = 'idArticulo';
            
        } else {
            $busquedaIdArticulo = "a.plu_interno";
            $idPrincipalArticulo1 = 'pluInterno';
        }

        $datosTabla = array(
            HTML::parrafo($arrayIdArticulo[$idPrincipalArticulo], 'centrado')   => ''.$idPrincipalArticulo1.'|'.$busquedaIdArticulo.'', //concateno el nombre del alias para usarlo al armar la tabla con el fila en objeto
            HTML::parrafo($textos->id('NOMBRE'), 'centrado')                    => 'articulo|a.nombre',
            HTML::parrafo($textos->id('FECHA'), 'centrado')                     => 'fecha|K.fecha',
            HTML::parrafo($textos->id('CONCEPTO'), 'centrado')                  => 'concepto|K.concepto',
            HTML::parrafo($textos->id('NUM_FACTURA'), 'centrado')               => 'numFactura|K.num_factura',
            HTML::parrafo($textos->id('CANTIDAD_COMPRA'), 'centrado')           => 'cantidadCompra|K.cantidad_compra',
            HTML::parrafo($textos->id('VAL_UNITARIO_COMPRA'), 'centrado')       => 'valUnitarioCompra|K.val_unitario_compra',
            HTML::parrafo($textos->id('VAL_TOTAL_COMPRA'), 'centrado')          => 'valTotalCompra|K.val_total_compra',
            HTML::parrafo($textos->id('CANTIDAD_VENTA'), 'centrado')            => 'cantidadVenta|K.cantidad_venta',
            HTML::parrafo($textos->id('VAL_UNITARIO_VENTA'), 'centrado')        => 'valUnitarioVenta|K.val_unitario_venta',
            HTML::parrafo($textos->id('VAL_TOTAL_VENTA'), 'centrado')           => 'valTotalVenta|K.val_total_venta',   
            HTML::parrafo($textos->id('CANTIDAD_SALDO'), 'centrado')            => 'cantidadSaldo|K.cantidad_saldo',
            HTML::parrafo($textos->id('VAL_UNITARIO_SALDO'), 'centrado')        => 'valUnitarioSaldo|K.val_unitario_saldo',
            HTML::parrafo($textos->id('VAL_TOTAL_SALDO'), 'centrado')           => 'valTotalSaldo|K.val_total_saldo',            
        );
        //ruta a donde se mandara la accion del doble click
        $ruta = '/ajax' . $this->urlBase . '/move';


        return Recursos::generarTablaRegistros($arregloRegistros, $datosTabla, $ruta, $datosPaginacion) . HTML::crearMenuBotonDerecho('KARDEX', array(), array("editar" => true, "borrar" => true));
    }

}
