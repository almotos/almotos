<?php

/**
 * @package     FOLCS
 * @subpackage  Cotizaciones
 * @author      Pablo Andrés Vélez Vidal <pavelez8@misena.edu.co>
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2012 Genesys Corporation.
 * @version     0.2
 * 
 * Cotizaciones que son generadas y entregadas a los clientes cuando se va a realizar una potencial venta de mercancia dentro del sistema. 
 * Clase encargada de gestionar la información del listado de cotizaciones almacenadas en el sistema. En este módulo se pueden
 * agregar, consultar, eliminar o modificar la información de las cotizaciones, e inclusive se puede generar una factura de venta a partir de una
 * cotizacion. Este modulo se relaciona con el modulo de ventas, ya que se puede generar una cotización desde allí, o se puede generar una factura de venta
 * desde una cotización ya realizada, así no se tendría que volver a generar la lista de articulos previamente cotizados por un cliente, esto solo con el fin
 * de optimizar procesos.
 * 
 * tablas principales: fom_cotizaciones, fom_articulos_cotizacion.
 * 
 * https://github.com/Craswer/PhpNetworkLprPrinter //para usar impresoras con php
 * */
class Cotizacion {

    /**
     * Código interno o identificador del registro
     * @var entero
     */
    public $id;

    /**
     * Código interno o identificador del registro
     * @var entero
     */
    public $idCotizacion;

    /**
     * URL relativa del módulo de cotizaciones
     * @var cadena
     */
    public $urlBase;

    /**
     * URL relativa de un cotizacion específico
     * @var cadena
     */
    public $url;

    /**
     * Código interno del modulo cotizacion
     * @var entero
     */
    public $idModulo;

    /**
     * Código interno del cliente al cual se le realiza la compra
     * @var entero
     */
    public $idCliente;

    /**
     * objeto cliente al cual se le realiza la compra
     * @var objeto
     */
    public $cliente;

    /**
     * fecha en que se realia  la cotizacion
     * @var cadena
     */
    public $fechaCotizacion;    

    /**
     * fecha en que se vence el plazo para pagar esta factura al proveedor
     * @var cadena
     */
    public $fechaVtoFactura;    
    
    /**
     * forma en que se paga la factura 1=contado, 2= credito
     * @var cadena
     */
    public $modoPago;    

    /**
     * Identificador del cotizacion que genera la cotizacion
     * @var entero
     */
    public $id_usuario;

    /**
     *  usuario que genera la cotizacion
     * @var entero
     */
    public $usuario;
        
    /**
     * Identificador de la caja donde se genera la cotizacion
     * @var entero
     */
    public $idCaja;

    /**
     * caja donde se genera la cotizacion
     * @var entero
     */
    public $caja;   

    /**
     * Identificador de la sede donde se genera la cotizacion
     * @var entero
     */
    public $idSede;

    /**
     * sede donde se genera la cotizacion
     * @var entero
     */
    public $sede;
    

    /**
     * determina el iva que tiene la cotizacion
     * @var entero
     */
    public $iva;

    /**
     * concepto 1 de precio de venta
     * @var cadena
     */
    public $concepto1;

    /**
     * precio 1 de precio de venta
     * @var entero
     */
    public $descuento1;

    /**
     * concepto 2 de precio de venta
     * @var cadena
     */
    public $concepto2;

    /**
     * precio 2 de precio de venta
     * @var entero
     */
    public $descuento2;
    
    /**
     * concepto 1 de precio de venta
     * @var cadena
     */
    public $fechaLimiteDcto1;

    /**
     * precio 1 de precio de venta
     * @var entero
     */
    public $porcentajeDcto1;

    /**
     * concepto 2 de precio de venta
     * @var cadena
     */
    public $fechaLimiteDcto2;

    /**
     * precio 2 de precio de venta
     * @var entero
     */
    public $porcentajeDcto2;    

    /**
     * el valor del flete de la mercancia
     * @var enum
     */
    public $valorFlete;
    
    /**
     * precio de venta del articulo
     * @var entero
     */
    public $subtotal;    

    /**
     * precio de venta del articulo
     * @var entero
     */
    public $total;

    /**
     * observaciones realizadas a la cotizacion
     * @var entero
     */
    public $observaciones;
    
    /**
     * Determina si la cotizacion se encuentra activa o no
     * @var entero
     */
    public $activo;    

    /**
     * listado de articulos que contiene la cotizacion
     * @var array
     */
    public $listaArticulos;

    /**
     * Indicador del cotizacion cronológio de la lista de registros
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
     * Inicializar los datos de una cotizacion
     * @param entero $id Código interno o identificador de la cotizacion
     */
    public function __construct($id = NULL) {
        global $sql, $modulo;
        
        $this->urlBase      = '/' . $modulo->url;
        $this->url          = $modulo->url;
        $this->idModulo     = $modulo->id;

        //Saber el numero de registros
        $this->registros        = $sql->obtenerValor('cotizaciones', 'COUNT(id)', 'id != "0"');
        //Saber el numero de registros activos
        $this->registrosActivos = $sql->obtenerValor('cotizaciones', 'COUNT(id)', 'activo = "1" AND id != "0"');
        //establecer el valor del campo predeterminado para organizar los listados
        $this->ordenInicial     = "fecha_cotizacion";          

        if (!empty($id)) {
            $this->cargar($id);
            
        }
        
    }

    /**
     * Cargar los datos de una unidad
     * @param entero $id Código interno o identificador de la unidad en la base de datos
     */
    public function cargar($id) {
        global $sql;

        if (isset($id) && $sql->existeItem('cotizaciones', 'id', intval($id))) {

            $tablas = array(
                'oc'    => 'cotizaciones',
                's'     => 'sedes_empresa',
                'c'     => 'clientes',
                'c1'    => 'cajas',
                'u'     => 'usuarios'
            );

            $columnas = array(
                'id'                    => 'oc.id',
                'idCotizacion'          => 'oc.id_cotizacion',
                'idCliente'             => 'oc.id_cliente',
                'fechaCotizacion'       => 'oc.fecha_cotizacion',
                'fechaVtoFactura'       => 'oc.fecha_vencimiento',
                'modoPago'              => 'oc.modo_pago',
                'idCaja'                => 'oc.id_caja',
                'caja'                  => 'c1.nombre',
                'idSede'                => 'c1.id_sede',
                'sede'                  => 's.nombre',
                'id_usuario'            => 'oc.id_usuario',
                'usuario'               => 'u.usuario',
                'subtotal'              => 'oc.subtotal',
                'iva'                   => 'oc.iva',
                'concepto1'             => 'oc.concepto1',
                'descuento1'            => 'oc.descuento1',
                'concepto2'             => 'oc.concepto2',
                'descuento2'            => 'oc.descuento2',
                'fechaLimiteDcto1'      => 'oc.fecha_limite_dcto_1',
                'porcentajeDcto1'       => 'oc.porcentaje_dcto_1',
                'fechaLimiteDcto2'      => 'oc.fecha_limite_dcto_2',
                'porcentajeDcto2'       => 'oc.porcentaje_dcto_2',                
                'valorFlete'            => 'oc.valor_flete',
                'total'                 => 'oc.total',
                'subtotal'              => 'oc.subtotal',
                'observaciones'         => 'oc.observaciones',
                'activo'                => 'oc.activo'
            );

            $condicion = 'oc.id_caja = c1.id AND c1.id_sede = s.id AND oc.id_usuario = u.id AND oc.id_cliente = c.id AND oc.id = "' . $id . '"';

            $consulta = $sql->seleccionar($tablas, $columnas, $condicion);

            if ($sql->filasDevueltas) {
                $fila = $sql->filaEnObjeto($consulta);

                foreach ($fila as $propiedad => $valor) {
                    $this->$propiedad = $valor;
                }

                $tablas1 = array(
                    'ao'    => 'articulos_cotizacion',
                    'a'     => 'articulos'
                );

                $columnas1 = array(
                    'id'            => 'ao.id',
                    'idCotizacion'  => 'ao.id_cotizacion',
                    'idArticulo'    => 'ao.id_articulo',
                    'articulo'      => 'a.nombre',
                    'cantidad'      => 'ao.cantidad',
                    'descuento'     => 'ao.descuento',
                    'iva'           => 'ao.iva',
                    'precio'        => 'ao.precio'
                );                

                $condicion1 = 'ao.id_articulo = a.id  AND ao.id_cotizacion = "' . $id . '"';

                $consulta1 = $sql->seleccionar($tablas1, $columnas1, $condicion1);

                if ($sql->filasDevueltas) {
                    while ($objeto = $sql->filaEnObjeto($consulta1)) {
                        $this->listaArticulos[] = $objeto;
                    }
                }
                
                $this->cliente = new Cliente($this->idCliente);
            }
        }
    }


    /**
     * Adicionar una cotizacion
     * 
     * @param  arreglo $datos       Datos de la cotizacion a adicionar
     * @return entero               Código interno o identificador de la cotizacion en la base de datos (NULL si hubo error)
     */
    public function adicionar($datos) {
        global $sql, $sesion_usuarioSesion;
        
        $datosCotizacion = array(
            'id_cliente'            => $datos['id_cliente'],
            'fecha_cotizacion'      => date("Y-m-d H:i:s"),
            'fecha_vencimiento'     => $datos['fecha_vto_factura'],
            'modo_pago'             => $datos['modo_pago'],
            'id_usuario'            => $sesion_usuarioSesion->id,
            'id_caja'               => $datos['id_caja'],
            'iva'                   => $datos['iva'],
            'concepto1'             => $datos['concepto1'],
            'descuento1'            => $datos['descuento1'],
            'concepto2'             => $datos['concepto2'],
            'descuento2'            => $datos['descuento2'],
            'fecha_limite_dcto_1'   => $datos['fecha_limite_dcto_1'],
            'porcentaje_dcto_1'     => $datos['porcentaje_dcto_1'],
            'fecha_limite_dcto_2'   => $datos['fecha_limite_dcto_2'],
            'porcentaje_dcto_2'     => $datos['porcentaje_dcto_2'],            
            'valor_flete'           => $datos['valor_flete'],
            'total'                 => $datos['total'],
            'subtotal'              => $datos['subtotal'],
            'observaciones'         => $datos['observaciones']
        );
        
        $datosCotizacion['activo'] = '1';
        
        $sql->iniciarTransaccion();

        $consulta = $sql->insertar('cotizaciones', $datosCotizacion);
        
        $idItem = $sql->ultimoId;


        if ($consulta) {
            //recibir toda la cadena y hacer el insertar en una sola consulta de la lista de articulos            
            $arreglo = explode('|', substr($datos['cadenaArticulosPrecios'], 0, -1));

            $valoresConsulta = '';
            
            foreach ($arreglo as $id => $valor) {
                $articulo = explode(';', $valor);
                $valoresConsulta .= '(' . $idItem . ', ' . $articulo[0] . ', ' . $articulo[1] . ', ' . $articulo[2] . ', ' . $articulo[3] . ', ' . $articulo[5] .' ),';
                
            }

            $valoresConsulta = substr($valoresConsulta, 0, -1);

            $sentencia = "INSERT INTO fom_articulos_cotizacion (id_cotizacion, id_articulo, cantidad, descuento, precio, iva) VALUES $valoresConsulta";

            $insertarListaArticulos = $sql->ejecutar($sentencia);

            if ($insertarListaArticulos) {
                //echo"aqui si entré";
                $idCotizacion = 'CO' . (int) $sesion_usuarioSesion->sede->id . '-' . $idItem;
                
                $datosModificar = array('id_cotizacion' => $idCotizacion);
                
                $modificacion = $sql->modificar('cotizaciones', $datosModificar, 'id = "' . $idItem . '"'); 
                
                if(!$modificacion){
                    $sql->cancelarTransaccion();
                    return false;                     
                    
                }
                
            } else {
                $sql->cancelarTransaccion();
                return false;                

            }

            $sql->finalizarTransaccion();
            return $idItem;
            
        } else {
            $sql->cancelarTransaccion();
            return NULL;
            
        }
        
    }


    /**
     * Modificar una cotizacion
     * 
     * @param  arreglo $datos       Datos del articulo a modificar
     * @return lógico               Indica si el procedimiento se pudo realizar correctamente o no
     */
    public function modificar($datos) {
        global $sql;

        if (!isset($this->id)) {
            return NULL;
        }

        $datosCotizacion = array(
            'id_cliente'            => $datos['id_cliente'],
            'fecha_cotizacion'      => $datos['fecha_cotizacion'],
            'fecha_vencimiento'     => $datos['fecha_vto_factura'],
            'modo_pago'             => $datos['modo_pago'],
            'id_usuario'            => $datos['id_usuario'],
            'id_caja'               => $datos['id_caja'],
            'iva'                   => $datos['iva'],
            'concepto1'             => $datos['concepto1'],
            'descuento1'            => $datos['descuento1'],
            'concepto2'             => $datos['concepto2'],
            'descuento2'            => $datos['descuento2'],
            'fecha_limite_dcto_1'   => $datos['fecha_limite_dcto_1'],
            'porcentaje_dcto_1'     => $datos['porcentaje_dcto_1'],
            'fecha_limite_dcto_2'   => $datos['fecha_limite_dcto_2'],
            'porcentaje_dcto_2'     => $datos['porcentaje_dcto_2'],            
            'valor_flete'           => $datos['valor_flete'],
            'total'                 => $datos['total'],
            'subtotal'              => $datos['subtotal'],
            'observaciones'         => $datos['observaciones']
        );
        
        if (isset($datos['activo'])) {
            $datosCotizacion['activo'] = '1';
            
        } else {
            $datosCotizacion['activo'] = '0';
            
        }        
        
        $sql->iniciarTransaccion();

        $consulta = $sql->modificar('cotizaciones', $datosCotizacion, 'id = "' . $this->id . '"');

        if ($consulta) {

            $eliminarArticulosCotizacion = $sql->eliminar('articulos_cotizacion', 'id_cotizacion = "' . $this->id . '"');

            if ($eliminarArticulosCotizacion) {
                //recibir toda la cadena y hacer el insertar en una sola consulta de la lista de articulos            
                $arreglo = explode('|', substr($datos['cadenaArticulosPrecios'], 0, -1));
                
                foreach ($arreglo as $id => $valor) {
                    $articulo = explode(';', $valor);
                    $valoresConsulta .= '(' . $this->id . ', ' . $articulo[0] . ', ' . $articulo[1] . ', ' . $articulo[2] . ', ' . $articulo[3] . ', ' . $articulo[5] . ' ),';
                }

                $valoresConsulta = substr($valoresConsulta, 0, -1);

                $sentencia = "INSERT INTO fom_articulos_cotizacion (id_cotizacion, id_articulo, cantidad, descuento, precio, iva) VALUES $valoresConsulta";

                $insertarListaArticulos = $sql->ejecutar($sentencia);
                
                if( !$insertarListaArticulos ){
                    $sql->cancelarTransaccion();
                    return false;
                    
                }
                
            } else {
                $sql->cancelarTransaccion();
                return false;                
            }

            $sql->finalizarTransaccion();
            return $this->id;
            
        } else {
            $sql->cancelarTransaccion();
            return false;
            
        }
        
    }

    /**
     *
     * Eliminar una cotizacion
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
        
        $sql->iniciarTransaccion();
        
        $query = $consulta = $sql->eliminar('cotizaciones', 'id = "' . $this->id . '"');
        
        if (!$query) {
            $sql->cancelarTransaccion();
            return false;
            
        } else {
            $consulta = $sql->eliminar('articulos_cotizacion', 'id_cotizacion = "' . $this->id . '"');
            
            if( !$consulta ){
                $sql->cancelarTransaccion();
                return false;

            }            
            
            $sql->finalizarTransaccion();
            return true;
            
        }
        
    }


    /**
     * Listar las ordenes
     * @param entero  $cantidad    Número de articulos a incluir en la lista (0 = todas las entradas)
     * @param arreglo $excepcion   Arreglo con los códigos internos o identificadores a omitir en la lista
     * @param cadena  $condicion   Condición adicional (SQL)
     * @return arreglo             Lista de articulos
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
            $condicion .= 'oc.id NOT IN (' . $excepcion . ')  AND ';
        }

        /*         * * Definir el orden de presentación de los datos ** */
        if (!isset($orden)) {
            $orden = 'fecha_cotizacion';
        }
        if ($this->listaAscendente) {
            $orden = $orden . ' ASC';
        } else {
            $orden = $orden . ' DESC';
        }

        $tablas = array(
            'oc'    => 'cotizaciones',
            's'     => 'sedes_empresa',
            'c'     => 'clientes',
            'c1'    => 'cajas',
            'u'     => 'usuarios'
        );

        $columnas = array(
            'id'                => 'oc.id',
            'idCliente'         => 'oc.id_cliente',
            'cliente'           => 'c.nombre',
            'fechaCotizacion'   => 'oc.fecha_cotizacion',
            'fechaVtoFactura'   => 'oc.fecha_vencimiento',
            'idCaja'            => 'oc.id_caja',
            'caja'              => 'c1.nombre',
            'idSede'            => 'c1.id_sede',
            'sede'              => 's.nombre',
            'id_usuario'        => 'oc.id_usuario',
            'usuario'           => 'u.usuario',
            'iva'               => 'oc.iva',
            'concepto1'         => 'oc.concepto1',
            'descuento1'        => 'oc.descuento1',
            'concepto2'         => 'oc.concepto2',
            'descuento2'        => 'oc.descuento2',
            'valorFlete'        => 'oc.valor_flete',
            'total'             => 'oc.total',
            'observaciones'     => 'oc.observaciones',
            'activo'            => 'oc.activo'
        );

        if (!empty($condicionGlobal)) {
            $condicion .= $condicionGlobal .' AND ';
        }

        $condicion .= 'oc.id_caja = c1.id AND c1.id_sede = s.id AND oc.id_usuario = u.id AND oc.id_cliente = c.id';

        if (is_null($this->registrosConsulta)) {
            $sql->seleccionar($tablas, $columnas, $condicion);
            $this->registrosConsulta = $sql->filasDevueltas;
        }
//        $sql->depurar = true;
        $consulta = $sql->seleccionar($tablas, $columnas, $condicion, 'oc.id', $orden, $inicio, $cantidad);

        if ($sql->filasDevueltas) {
            $lista = array();

            while ($objeto = $sql->filaEnObjeto($consulta)) {
                
                if ($objeto->activo) {
                    $objeto->estado = HTML::frase($textos->id("ACTIVO"), "activo");
                } else {
                    $objeto->estado = HTML::frase($textos->id("INACTIVO"), "inactivo");
                }                 
                
                $lista[] = $objeto;
            }
        }

        return $lista;
    }

    /**
     * Metodo que genera los datos que contendra la tabla principal del modulo
     * @global type $textos
     * @param type $arregloRegistros
     * @param type $datosPaginacion
     * @return type 
     */
    public function generarTabla($arregloRegistros, $datosPaginacion = NULL) {
        global $textos;
        //Declaracion de las columnas que se van a mostrar en la tabla
        $datosTabla = array(
            HTML::parrafo($textos->id('NUMERO_COTIZACION'), 'centrado') => 'id|oc.id',
            HTML::parrafo($textos->id('SEDE'), 'centrado') => 'sede|s.nombre',
            HTML::parrafo($textos->id('CLIENTE'), 'centrado') => 'cliente|c.nombre',
            HTML::parrafo($textos->id('USUARIO_CREADOR'), 'centrado') => 'usuario|u.usuario',
            HTML::parrafo($textos->id('FECHA_COTIZACION'), 'centrado') => 'fechaCotizacion|oc.fecha_cotizacion',
            HTML::parrafo($textos->id('ESTADO'), 'centrado') => 'estado'
        );
        //ruta del metodo paginador
        $rutaPaginador = '/ajax' . $this->urlBase . '/move';

        return Recursos::generarTablaRegistros($arregloRegistros, $datosTabla, $rutaPaginador, $datosPaginacion) . HTML::crearMenuBotonDerecho('COTIZACIONES');
    }

}
