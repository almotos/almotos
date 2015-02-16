<?php

/**
 * @package     FOLCS
 * @subpackage  Factura de venta
 * @author      Pablo Andrés Vélez Vidal <pavelez8@misena.edu.co>
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2012 Genesys Corporation.
 * @version     0.2
 * 
 * Facturas que son generadas cuando se realiza una venta de mercancia
 * dentro del sistema.
 * 
 * Modulo : ventas.
 * tablas: facturas_venta y articulos_factura_venta.
 * integridad referencial: 
 * 
 * https://github.com/Craswer/PhpNetworkLprPrinter //para usar impresoras con php
 * */
class FacturaVenta {

    /**
     * Código interno o identificador del registro
     * @var entero
     */
    public $id;

    /**
     * Código interno o identificador del registro
     * @var entero
     */
    public $idFactura;

    /**
     * URL relativa del módulo de usuarios
     * @var cadena
     */
    public $urlBase;

    /**
     * URL relativa de un usuario específico
     * @var cadena
     */
    public $url;

    /**
     * Código interno del modulo usuario
     * @var entero
     */
    public $idModulo;

    /**
     * Código interno del cliente al cual se le realiza la venta
     * @var entero
     */
    public $idCliente;
    
    /**
     * nit del cliente al cual se le realiza la venta
     * @var objeto
     */
    public $nitCliente;

    /**
     * cliente al cual se le realiza la venta
     * @var objeto
     */
    public $cliente;
      
    /**
     * objeto cliente al cual se le realiza la venta
     * @var objeto
     */
    public $objCliente;    

    /**
     * fecha en que se realia  la factura
     * @var cadena
     */
    public $fechaFactura;

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
     * Identificador del usuario que genera la factura
     * @var entero
     */
    public $id_usuario;

    /**
     *  usuario que genera la factura
     * @var entero
     */
    public $usuario;

    /**
     * Identificador de la sede donde se genera la factura
     * @var entero
     */
    public $id_sede;

    /**
     * sede donde se genera la factura
     * @var entero
     */
    public $sede;

    /**
     * subtotal de la factura
     * @var entero
     */
    public $subtotal;

    /**
     * determina el iva que tiene la factura
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
    public $total;

    /**
     * estado de la factura 1= abierta 2= cerrada
     * @var enum 1 o 2
     */
    public $estadoFactura;
    
    /**
     * Cadena con la informacion para las retenciones que llevara a cabo la clase Contabilidad
     * @var entero
     */
    public $retenciones;  
    
    /**
     * Valor total de las retenciones en la factura
     * @var entero
     */
    public $totalRetenciones = 0;     
    
    /**
     * arreglo (llave ->valor) con la informacion para las retenciones que llevara a cabo la clase Contabilidad
     * @var entero
     */
    public $arregloRetenciones = array();      

    /**
     * observaciones realizadas a la factura
     * @var entero
     */
    public $observaciones;

    /**
     * listado de articulos que contiene la factura
     * @var array
     */
    public $listaArticulos;

    /**
     * Indicador del orden cronológio de la lista de registros
     * @var lógico
     */
    public $listaAscendente = FALSE;
    
    /**
     * Se pone en true si al tratar de generar una factura, el id de dicha factura esta fuera del rango de la resolucion de la DIAN vigente
     * @var lógico
     */
    public $fueraResolucion = false;    
    
    /**
     * Se pone en true si al tratar de generar una factura, no se encuentra una resolucon activa para la sede donde se quiere facturar
     * @var lógico
     */
    public $sinResolucion = false;     

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
     * Número de registros activos de la lista de foros
     * @var entero
     */
    public $nuevoNumeroFactura = NULL;

    /**
     * Orden predeterminado para organizar los listados
     * @var entero
     */
    public $ordenInicial = NULL;

    /**
     * Inicializar los datos de una factura de venta
     * @param entero $id Código interno o identificador de la factura
     */
    public function __construct($id = NULL) {
        global $sql;
        $modulo             = new Modulo('FACTURAS_VENTA');
        $this->urlBase      = '/' . $modulo->url;
        $this->url          = $modulo->url;
        $this->idModulo     = $modulo->id;

        //establecer el valor del campo predeterminado para organizar los listados
        $this->ordenInicial = 'fv.fecha_factura';

        //Saber el numero de registros
        $this->registros = $sql->obtenerValor('facturas_venta', 'COUNT(id)', 'id != "0"');

        if (isset($id)) {
            $this->cargar($id);
        }
    }

    /**
     * Cargar los datos de una unidad
     * @param entero $id Código interno o identificador de la unidad en la base de datos
     */
    public function cargar($id) {
        global $sql, $configuracion;

        if (isset($id) && $sql->existeItem('facturas_venta', 'id', intval($id))) {

            $tablas = array(
                'fv'    => 'facturas_venta',
                'c1'    => 'cajas',
                's'     => 'sedes_empresa',
                'c'     => 'clientes',
                'u'     => 'usuarios'
            );

            $columnas = array(
                'id'                        => 'fv.id',
                'idFactura'                 => 'fv.id_factura',
                'idCliente'                 => 'fv.id_cliente',
                'nitCliente'                => 'c.id_cliente',
                'cliente'                   => 'c.nombre',
                'fechaFactura'              => 'fv.fecha_factura',
                'fechaVtoFactura'           => 'fv.fecha_vencimiento',
                'modoPago'                  => 'fv.modo_pago',
                'idCaja'                    => 'fv.id_caja',
                'caja'                      => 'c1.nombre',
                'idSede'                    => 'c1.id_sede',
                'sede'                      => 's.nombre',
                'id_usuario'                => 'fv.id_usuario',
                'usuario'                   => 'u.usuario',
                'iva'                       => 'fv.iva',
                'retenciones'               => 'fv.retenciones',
                'concepto1'                 => 'fv.concepto1',
                'descuento1'                => 'fv.descuento1',
                'concepto2'                 => 'fv.concepto2',
                'descuento2'                => 'fv.descuento2',
                'fechaLimiteDcto1'          => 'fv.fecha_limite_dcto_1',
                'porcentajeDcto1'           => 'fv.porcentaje_dcto_1',
                'fechaLimiteDcto2'          => 'fv.fecha_limite_dcto_2',
                'porcentajeDcto2'           => 'fv.porcentaje_dcto_2',
                'valorFlete'                => 'fv.valor_flete',
                'total'                     => 'fv.total',
                'subtotal'                  => 'fv.subtotal',
                'estadoFactura'             => 'fv.estado_factura',
                'observaciones'             => 'fv.observaciones',
                'activo'                    => 'fv.activo',
            );

            $condicion = 'fv.id_caja = c1.id AND c1.id_sede = s.id  AND fv.id_usuario = u.id AND fv.id_cliente = c.id AND fv.id = "' . $id . '"';

            
            $consulta = $sql->seleccionar($tablas, $columnas, $condicion);

            if ($sql->filasDevueltas) {
                $fila = $sql->filaEnObjeto($consulta);

                foreach ($fila as $propiedad => $valor) {
                    $this->$propiedad = $valor;
                }
                
                $this->objCliente = new Cliente($this->idCliente);

                $tablas1 = array(
                    'af'    => 'articulos_factura_venta',
                    'a'     => 'articulos'
                );
                $columnas1 = array(
                    'id'            => 'af.id',
                    'idFactura'     => 'af.id_factura',
                    'idArticulo'    => 'af.id_articulo',
                    'articulo'      => 'a.nombre',
                    'plu_interno'   => 'a.plu_interno',
                    'codigo_oem'    => 'a.codigo_oem',                    
                    'cantidad'      => 'af.cantidad',
                    'descuento'     => 'af.descuento',
                    'precio'        => 'af.precio',
                    'precioCompra'  => 'a.ultimo_precio_compra',
                    'iva'           => 'af.iva',
                    'idCliente'     => 'af.id_cliente',
                    'idBodega'      => 'af.id_bodega'
                );

                $condicion1 = 'af.id_articulo = a.id  AND af.id_factura = "' . $id . '"';
                
                $consulta1 = $sql->seleccionar($tablas1, $columnas1, $condicion1);

                if ($sql->filasDevueltas) {
                    while ($objeto = $sql->filaEnObjeto($consulta1)) {
                        $this->listaArticulos[] = $objeto;
                    }
                }
                
                /**
                 * generar arreglo de retenciones llave -> valor, donde llave es el valor de la retencion y valor, el valor :)
                 */
                if (!empty($this->retenciones)){
                    $arrRetenciones = explode('|', substr($this->retenciones, 0, -1));

                    foreach ($arrRetenciones as $id => $valor) {
                        $retencion          = explode(';', $valor);
                        $nombreRetencion    = $configuracion["RETENCIONES"]["VENTAS"][$configuracion["GENERAL"]["idioma"]][$retencion[0]]["nombre"];

                        $this->arregloRetenciones[$nombreRetencion] = $retencion[1];
                        $this->totalRetenciones += $retencion[1];

                    }   
                }             
                
            }
        }
    }
    
    /**
     * Adicionar una factura
     * @param  arreglo $datos       Datos de la factura a adicionar
     * @return entero               Código interno o identificador de la factura en la base de datos (NULL si hubo error)
     */
    public function adicionar($datos) {
        global $sql, $sesion_usuarioSesion, $textos, $sesion_configuracionGlobal, $textos;

        //verifico el regimen de la empresa para ver si usa o no usa resolucion
        $regimenEmpresa = $sesion_configuracionGlobal->empresa->regimen;
        
        //establecer el numero de factura
        $maxNumFactAct  = $sql->obtenerValor('facturas_venta', 'MAX(id_factura)', 'id != "0"');//el que debo consultar es el id_factura
        $idFactura      = $maxNumFactAct + 1;     
        
        if ($regimenEmpresa != "1"){
            //escojo la resolucion vigente para la sede del usuario que factura
            $idResolucion = $sql->obtenerValor('resoluciones', 'id', 'activo = "1" AND id_sede = "'.$sesion_usuarioSesion->sede->id.'"');

            if(empty($idResolucion)){//si no tiene resolución vigente devuelvo un error
                $nota                   = str_replace('%1', $sesion_usuarioSesion->sede->nombre, $textos->id('ERROR_SEDE_SIN_RESOLUCION'));
                $this->sinResolucion    = $nota;

                return false;  

            }

            $resolucion     = new Resolucion($idResolucion);

            $maxNumFactAct  = $sql->obtenerValor('facturas_venta', 'MAX(id_factura)', 'id != "0" AND id_resolucion = "'.$idResolucion.'"');//el que debo consultar es el id_factura
            //si hay que usar resolucion se usa esta misma para facturar
            if ($maxNumFactAct >= $resolucion->numeroFacturaInicial && $maxNumFactAct < $resolucion->numeroFacturaFinal) {//aqui verificar que este entre el rango de fechas
                $idFactura = $maxNumFactAct + 1;

            } else {
                $nota1  = str_replace('%1', $maxNumFactAct, $textos->id('ERROR_NUMERO_FUERA_RESOLUCION'));
                $nota2  = str_replace('%2', $resolucion->numero, $nota1);
                $nota3  = str_replace('%3', $resolucion->fechaResolucion, $nota2);
                $nota   = str_replace('%4', $resolucion->numeroFacturaInicial.' - '.$resolucion->numeroFacturaFinal, $nota3);

                $this->fueraResolucion = $nota;

                return false;

            }
        }
        
        //aqui verificar si ya esta para terminarse, o el numero de facturas, o por vencerse la fecha de la resolucion y notificar
        
        $datosFactura = array(
            'id_factura'                => $idFactura,
            'id_cliente'                => $datos['id_cliente'],
            'fecha_factura'             => $datos['fecha_factura'] . ' ' . date('H:i:s'),
            'fecha_vencimiento'         => $datos['fecha_vto_factura'],
            'modo_pago'                 => $datos['modo_pago'],
            'id_usuario'                => $datos['id_usuario'],
            'id_caja'                   => $datos['id_caja'],
            'iva'                       => $datos['iva'],
            'retenciones'               => $datos['retenciones'],
            'concepto1'                 => (!empty($datos['concepto1'])) ? $datos['concepto1'] : $textos->id("DESCUENTO_1"),
            'descuento1'                => $datos['descuento1'],
            'concepto2'                 => (!empty($datos['concepto2'])) ? $datos['concepto2'] : $textos->id("DESCUENTO_2"),
            'descuento2'                => $datos['descuento2'],
            'fecha_limite_dcto_1'       => $datos['fecha_limite_dcto_1'],
            'porcentaje_dcto_1'         => $datos['porcentaje_dcto_1'],
            'fecha_limite_dcto_2'       => $datos['fecha_limite_dcto_2'],
            'porcentaje_dcto_2'         => $datos['porcentaje_dcto_2'],
            'valor_flete'               => $datos['valor_flete'],
            'total'                     => $datos['total'],
            'subtotal'                  => $datos['subtotal'],
            'campo_efectivo'            => $datos['campo_efectivo'],
            'campo_tarjeta'             => $datos['campo_tarjeta'],
            'campo_cheque'              => $datos['campo_cheque'],
            'campo_credito'             => $datos['campo_credito'],            
            'observaciones'             => $datos['observaciones']
        );
        
        //generar el arreglo con los medios de pago y sus valores enviados desde el formulario
        $datosMediosPago = array(
            "efectivo"  => $datos['campo_efectivo'],
            "tarjeta"   => $datos['campo_tarjeta'],
            "cheque"    => $datos['campo_cheque'],
            "credito"   => $datos['campo_credito'],
        );        
        
        if ($regimenEmpresa != "1"){
            $datosFactura['id_resolucion'] = $idResolucion;
            
        }
        
        $sql->iniciarTransaccion();
        
        $query  = $sql->insertar('facturas_venta', $datosFactura);
        $idItem = $sql->ultimoId;

        if ($query) {
            //recibir toda la cadena y hacer el insertar en una sola consulta de la lista de articulos            
            $arreglo = explode('|', substr($datos['cadenaArticulosPrecios'], 0, -1));

            $inventario = new Inventario();
            
            $valoresConsulta = '';
            
            foreach ($arreglo as $id => $valor) {
                $articulo = explode(';', $valor);
                
                $valoresConsulta .= '("' . $idItem . '", "' . $articulo[0] . '", "' . $articulo[1] . '", "' . $articulo[2] . '", "' . $articulo[3] . '", "'.$datos['id_cliente'].'" , "' . $articulo[4].'" , "' . $articulo[5] . '"),';
                
                $descontarInventario =  $inventario->descontar($articulo[0], $articulo[1], $articulo[4], $idItem);
                
                if(!$descontarInventario){
                    $sql->cancelarTransaccion();

                }                 
                
                $datosKardex = array(
                    'id_articulo'           => $articulo[0],
                    'fecha'                 => $datos['fecha_factura'] . ' ' . date('H:i:s'),
                    'concepto'              => 'ventas',
                    'num_factura'           => $idItem,
                    'cantidad_compra'       => '',
                    'val_unitario_compra'   => '',
                    'val_total_compra'      => '',
                    'cantidad_venta'        => $articulo[1],
                    'val_unitario_venta'    => $articulo[3],
                    'val_total_venta'       => ($articulo[1] * $articulo[3]),
                    'cantidad_saldo'        => $articulo[1],
                    'total_saldo'           => ($articulo[1] * $articulo[3]),
                );

                $insertarEnKardex = Kardex::adicionar($datosKardex);

                if(!$insertarEnKardex){
                    $sql->cancelarTransaccion();

                }                
      
            }

            $valoresConsulta = substr($valoresConsulta, 0, -1);

            $sentencia = "INSERT INTO fom_articulos_factura_venta (id_factura, id_articulo, cantidad, descuento, precio, id_cliente, id_bodega, iva) VALUES $valoresConsulta";

            $insertarListaArticulos = $sql->ejecutar($sentencia);

            if ($insertarListaArticulos) {
                //parte contable
                //Ejecutar las transacciones contables
                $contabilidad = new ContabilidadVentas();

                $contabilidad->contabilizarFacturaVenta($idItem, $datosMediosPago);               
                
            } else {
                //$sql->eliminar('facturas_venta', 'id = "' . $idItem . '"');
                $sql->cancelarTransaccion();
                $idItem = false;
            }

            $sql->finalizarTransaccion();
            
            return $idItem;
            
        } else {
            $sql->cancelarTransaccion();
            return false;
            
        }//fin del if($consulta)
    }

    /**
     * Modificar una factura de venta
     * @param  arreglo $datos       Datos del articulo a modificar
     * @return lógico               Indica si el procedimiento se pudo realizar correctamente o no
     */
    public function modificar($datos) {
        global $sql, $sesion_usuarioSesion, $sesion_configuracionGlobal;

        if (!isset($this->id)) {
            return NULL;
        }
        
        //capturo el regimen de la empresa para ver si utiliza resolucion
        $regimenEmpresa = $sesion_configuracionGlobal->empresa->regimen;
        
        if ($regimenEmpresa != "1"){        
            $idResolucion = $sql->obtenerValor('resoluciones', 'id', 'activo = "1" AND id_sede = "'.$sesion_usuarioSesion->sede->id.'"');
        
        }

        $datosFactura = array(
            'id_cliente'                    => $datos['id_cliente'],
            'fecha_factura'                 => $datos['fecha_factura'] . ' ' . date('H:i:s'),
            'fecha_vencimiento'             => $datos['fecha_vto_factura'],
            'modo_pago'                     => $datos['modo_pago'],
            'id_usuario'                    => $datos['id_usuario'],
            'id_caja'                       => $datos['id_caja'],
            'iva'                           => $datos['iva'],
            'concepto1'                     => $datos['concepto1'],
            'descuento1'                    => $datos['descuento1'],
            'concepto2'                     => $datos['concepto2'],
            'descuento2'                    => $datos['descuento2'],
            'fecha_limite_dcto_1'           => $datos['fecha_limite_dcto_1'],
            'porcentaje_dcto_1'             => $datos['porcentaje_dcto_1'],
            'fecha_limite_dcto_2'           => $datos['fecha_limite_dcto_2'],
            'porcentaje_dcto_2'             => $datos['porcentaje_dcto_2'],
            'valor_flete'                   => $datos['valor_flete'],
            'total'                         => $datos['total'],
            'subtotal'                      => $datos['subtotal'],
            'observaciones'                 => $datos['observaciones']
        );
        
        if ($regimenEmpresa != "1"){
            $datosFactura['id_resolucion'] = $idResolucion;
            
        }
        
        $sql->iniciarTransaccion();
        
        $query = $sql->modificar('facturas_venta', $datosFactura, 'id = "' . $this->id . '"');

        if ($query) {
            //recibir toda la cadena y hacer el insertar en una sola consulta de la lista de articulos  
            $eliminar = $sql->eliminar('articulos_factura_venta', 'id_factura = "' . $this->id . '"');
            
            if (!$eliminar) {
                $sql->cancelarTransaccion();
                return false;
                
            }            

            $arreglo = explode('|', substr($datos['cadenaArticulosPrecios'], 0, -1));

            foreach ($arreglo as $id => $valor) {
                $articulo = explode(';', $valor);
                $valoresConsulta .= '("' . $this->id . '", "' . $articulo[0] . '", "' . $articulo[1] . '", "' . $articulo[2] . '", "' . $articulo[3] . '", "'.$datos['id_cliente'].'" ),';
            }

            $valoresConsulta = substr($valoresConsulta, 0, -1);


            $sentencia = "INSERT INTO fom_articulos_factura_venta (id_factura, id_articulo, cantidad, descuento, precio, id_cliente) VALUES $valoresConsulta";

            $insertarListaArticulos = $sql->ejecutar($sentencia);

            if ($insertarListaArticulos) {
                
                
            } else {
                //$sql->eliminar('facturas_venta', 'id = "' . $this->id. '"');
                $sql->cancelarTransaccion();
                $idItem = false;
                
            }

            $sql->finalizarTransaccion();
            
            return $idItem;
            
        } else {
            $sql->cancelarTransaccion();
            return NULL;
            
        }//fin del if($consulta)
        
    }

    /**
     *
     * Eliminar una factura
     *
     * @return lógico       Indica si el procedimiento se pudo realizar correctamente o no
     *
     */
    public function eliminar() 
    {
        global $sql;

        if (!isset($this->id)) {
            return false;
        }
        
        $sql->iniciarTransaccion();
        
        $consulta = $sql->eliminar('facturas_venta', 'id = "' . $this->id . '"');

        if (!$consulta) {
            $sql->cancelarTransaccion();
            return false;
            
        } else {            
            $query = $sql->eliminar('articulos_factura_venta', 'id_factura = "' . $this->id . '"');
            
            if (!$query) {
                $sql->cancelarTransaccion();
                return false;
            
            }
            
            $sql->finalizarTransaccion();
            return true;
            
        }
        
    }
    
    
    /**
     *
     * Inactivar una factura
     *
     * @return lógico       Indica si el procedimiento se pudo realizar correctamente o no
     *
     */
    public function inactivar() 
    {
        global $sql;

        if (!isset($this->id)) {
            return false;
        }
        
        $sql->iniciarTransaccion();
        
        $activo = $sql->obtenerValor('facturas_venta', 'activo', 'id = "' . $this->id . '"');
        
        $valor = ($activo == "1") ? "0" : "1";
        
        $datos = array('activo' => $valor);
        
        $consulta = $sql->modificar('facturas_venta', $datos, 'id = "' . $this->id . '"');

        if (!$consulta) {
            $sql->cancelarTransaccion();
            return false;
            
        } 
        
        $sql->finalizarTransaccion();
        return true;
        
    }     

    /**
     * Listar las facturas
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

        /*         * * Definir el orden de presentación de los datos ** */
        if (!isset($orden)) {
            $orden = 'fv.fecha_factura';
        }
        
        if ($this->listaAscendente) {
            $orden = $orden . ' ASC';
        } else {
            $orden = $orden . ' DESC';
        }

        $tablas = array(
            'fv'        => 'facturas_venta',
        );

        $columnas = array(
            'id'                        => 'fv.id',
            'idFactura'                 => 'fv.id_factura',
            'idCliente'                 => 'fv.id_cliente',
            'nitCliente'                => 'c.id_cliente',
            'cliente'                   => 'c.nombre',
            'fechaFactura'              => 'fv.fecha_factura',
            'modoPago'                  => 'fv.modo_pago',
            'idCaja'                    => 'fv.id_caja',
            'caja'                      => 'c1.nombre',
            'idSede'                    => 'c1.id_sede',
            'sede'                      => 's.nombre',
            'id_usuario'                => 'fv.id_usuario',
            'usuario'                   => 'u.usuario',
            'iva'                       => 'fv.iva',
            'concepto1'                 => 'fv.concepto1',
            'descuento1'                => 'fv.descuento1',
            'concepto2'                 => 'fv.concepto2',
            'descuento2'                => 'fv.descuento2',
            'valorFlete'                => 'fv.valor_flete',
            'total'                     => 'fv.total',
            'estadoFactura'             => 'fv.estado_factura',
            'observaciones'             => 'fv.observaciones',
            'activo'                    => 'fv.activo',
        );

        $condicion .=   ' LEFT JOIN fom_cajas c1 ON fv.id_caja = c1.id'. 
                        ' LEFT JOIN fom_sedes_empresa s ON c1.id_sede = s.id'.
                        ' LEFT JOIN fom_usuarios u ON fv.id_usuario = u.id'.
                        ' LEFT JOIN fom_clientes c ON fv.id_cliente = c.id';
        
        $where = '';
        
        /*         * * Validar que la excepción sea un arreglo y contenga elementos ** */
        if (isset($excepcion) && is_array($excepcion) && count($excepcion)) {
            $where = ' WHERE ';
            $excepcion = implode(',', $excepcion);
            $condicion .= ' '.$where.' fv.id NOT IN (' . $excepcion . ')';
        }
        
        if (!empty($condicionGlobal)) {
            $cond = (empty($where)) ? ' WHERE ' : ' AND ';
            $condicion .= $cond .$condicionGlobal;
        }

        if (is_null($this->registrosConsulta)) {
            $sql->seleccionar($tablas, $columnas, $condicion, "", "", NULL, NULL, FALSE);
            $this->registrosConsulta = $sql->filasDevueltas;
        }
        
        $consulta = $sql->seleccionar($tablas, $columnas, $condicion, 'fv.id', $orden, $inicio, $cantidad, FALSE);

        if ($sql->filasDevueltas) {
            $lista = array();

            while ($objeto = $sql->filaEnObjeto($consulta)) {

                $objeto->activo = ($objeto->activo) ? HTML::frase($textos->id('ACTIVO'), 'activo') : HTML::frase($textos->id('INACTIVO'), 'inactivo');

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
        global $textos, $sesion_usuarioSesion;
        //Declaracion de las columnas que se van a mostrar en la tabla
        $datosTabla = array(
            HTML::parrafo($textos->id('NUMERO_DE_FACTURA'), 'centrado') => 'id|fv.id',
            HTML::parrafo($textos->id('SEDE'), 'centrado')              => 'sede|s.nombre',
            HTML::parrafo($textos->id('ID_CLIENTE'), 'centrado')        => 'nitCliente|c.id_cliente',            
            HTML::parrafo($textos->id('CLIENTE'), 'centrado')           => 'cliente|c.nombre',
            HTML::parrafo($textos->id('USUARIO_CREADOR'), 'centrado')   => 'usuario|u.usuario',
            HTML::parrafo($textos->id('FECHA_FACTURA'), 'centrado')     => 'fechaFactura|fv.fecha_factura',
            HTML::parrafo($textos->id('ACTIVO'), 'centrado')            => 'activo|fv.activo',
        );
        //ruta del metodo paginador
        $rutaPaginador = '/ajax' . $this->urlBase . '/move';
        
        $adicionarNotaCredito = '';

        $puedeAdicionarNotaCredito= Perfil::verificarPermisosBoton('botonAdicionarNotaCreditoVenta');
        
        if ($puedeAdicionarNotaCredito || $sesion_usuarioSesion->id == 0) {
            $adicionarNotaCredito1 = HTML::formaAjax($textos->id('ADICIONAR_NOTA_CREDITO'), 'contenedorMenuAdicionarNotaCredito', 'adicionarNotaCredito', '', '/ajax/facturas_venta/adicionarNotaCredito', array('id' => ''));
            $adicionarNotaCredito = HTML::contenedor($adicionarNotaCredito1, '', 'botonAdicionarNotaCreditoVenta');
            
        }    
        
        $adicionarNotaDebito = '';

        $puedeAdicionarNotaDebito= Perfil::verificarPermisosBoton('botonAdicionarNotaDebitoVenta');
        if ($puedeAdicionarNotaDebito || $sesion_usuarioSesion->id == 0) {
            $adicionarNotaDebito1 = HTML::formaAjax($textos->id('ADICIONAR_NOTA_DEBITO'), 'contenedorMenuAdicionarNotaDebito', 'adicionarNotaDebito', '', '/ajax/facturas_venta/adicionarNotaDebito', array('id' => ''));
            $adicionarNotaDebito = HTML::contenedor($adicionarNotaDebito1, '', 'botonAdicionarNotaDebitoVenta');
            
        }        
        
        $botonesExtras = array($adicionarNotaCredito, $adicionarNotaDebito);        

        return Recursos::generarTablaRegistros($arregloRegistros, $datosTabla, $rutaPaginador, $datosPaginacion) . HTML::crearMenuBotonDerecho('FACTURAS_VENTA', $botonesExtras);
    }

    /**
     * modificar la fecha de una factura
     * @param entero $id    Código interno o identificador de una unidad en la base de datos
     * @return lógico       Indica si el procedimiento se pudo realizar correctamente o no
     */
    public function modificarFechaFactura($id, $fechaFact) {
        global $sql;


        if (!isset($id) || !isset($fechaFact)) {
            return NULL;
        }

        $datos = array(
            'fecha_factura' => $fechaFact
        );

        $modificar = $sql->modificar('facturas_venta', $datos, 'id = "' . $id . '"');

        if ($modificar) {
            return TRUE;
            
        } else {
            return FALSE;
            
        }//fin del si funciono eliminar
    }
    
    
    /**
     * Metodo que retorna la sumatoria del total de la compra del listado de
     * articulos vendidos en una factura
     */
    public function getCostoDeVenta(){
        $costoDeVenta = 0;
        
        if (!empty($this->listaArticulos) && is_array($this->listaArticulos)) {
            foreach ($this->listaArticulos as $articulo) {
                $costoDeVenta += $articulo->precioCompra;
            }
        }
        
        return (int)$costoDeVenta;
    }

}
