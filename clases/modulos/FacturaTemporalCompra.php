<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FacturaTemporalCompra
 * 
 * Todo: Migrar toda la relacion existente de la clase factura de compra a la 
 * clase factura temporal compra
 *
 * @author pipe
 */
class FacturaTemporalCompra {


    /**
     * Código interno o identificador del registro
     * @var entero
     */
    public $id;

    /**
     * Código interno o identificador de la factura con la sede y el tipo concatenado ej: FC-5-3234 = (el 5 sería la sede)
     * @var entero
     */
    public $idFactura;

    /**
     * URL relativa del módulo de facturas de compra
     * @var cadena
     */
    public $urlBase; //revisar

    /**
     * URL relativa 
     * @var cadena
     */
    public $url; //revisar

    /**
     * Código interno del modulo 
     * @var entero
     */
    public $idModulo;

    /**
     * Código interno del proveedor al cual se le realiza la compra
     * @var entero
     */
    public $idProveedor;

    /**
     * objeto proveedor al cual se le realiza la compra
     * @var objeto
     */
    public $proveedor;

    /**
     * numero de factura provisto por el proveedor
     * @var entero
     */
    public $numeroFacturaProveedor;

    /**
     * fecha en que se realiza  la factura
     * @var cadena
     */
    public $fechaFactura;

    /**
     * fecha en que se vence el plazo para pagar esta factura al proveedor
     * @var cadena
     */
    public $fechaVtoFactura;

    /**
     * forma en que se paga la factura
     * @var cadena
     */
    public $modoPago;

    /**
     * Identificador del usuario que genera la factura
     * @var entero
     */
    public $id_usuario;

    /**
     *  objeto usuario que genera la factura
     * @var entero
     */
    public $usuario;
        
    /**
     * Identificador de la caja donde se genera la cotizacion
     * @var entero
     */
    public $idCaja;

    /**
     * objeto caja donde se genera la cotizacion
     * @var entero
     */
    public $caja;       

    /**
     * Identificador de la sede donde se genera la factura
     * @var entero
     */
    public $idSede;

    /**
     * objeto sede donde se genera la factura
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
    
//    /**
//     * determina el retecree que tiene la factura
//     * @var entero
//     */
//    public $retecree;    

    /**
     * concepto 1 de l descuento realizado en esta factura
     * @var cadena
     */
    public $concepto1;

    /**
     * valor del descuento 1 en %
     * @var entero
     */
    public $descuento1;

    /**
     * concepto 1 de l descuento realizado en esta factura
     * @var cadena
     */
    public $concepto2;

    /**
     * valor del descuento 1 en %
     * @var entero
     */
    public $descuento2;

    /**
     * el valor del flete de la mercancia
     * @var enum
     */
    public $valorFlete;

    /**
     * precio total de la factura
     * @var entero
     */
    public $total;
    
    /**
     * Cadena con la informacion para las retenciones que llevara a cabo la clase Contabilidad
     * @var entero
     */
    public $retenciones;  
    
    /**
     * arreglo (llave ->valor) con la informacion para las retenciones que llevara a cabo la clase Contabilidad
     * @var entero
     */
    public $arregloRetenciones = array();      

    /**
     * estado de la factura 1= abierta 2= cerrada
     * @var enum 1 o 2
     */
    public $estadoFactura;

    /**
     * observaciones realizadas a la factura
     * @var entero
     */
    public $observaciones;

    /**
     * archivo digital que representa la factura de venta del proveedor (ya sea porque el proveedor la envio digital, o se escaneo el medio fisico)
     * @var entero
     */
    public $facturaDigital;
    
    /**
     * ruta relativa de la factura digital
     */
    public $rutaFacturaDigital;

    /**
     * listado de articulos que contiene la factura (arreglño con todo el listado de articulos)
     * @var array
     */
    public $listaArticulos;

    /**
     * Indicador del orden cronológio de la lista de registros
     * @var lógico
     */
    public $listaAscendente = FALSE;
    
    /**
     * Atributo que determina si a determinado proveedor ya se le habia realizado una compra con ese mismo número de factura (usado para validaciones)
     * @var lógico
     */
    public $existeFacturaProveedor = false;    

    /**
     * Número de registros de la lista
     * @var entero
     */
    public $registros = NULL;

    /**
     * Número de registros activos de la lista 
     * @var entero
     */
    public $registrosActivos = NULL;

    /**
     * Número de registros activos de la lista 
     * @var entero
     */
    public $registrosConsulta = NULL;

    /**
     * Número de registros activos de la lista //revisar
     * @var entero
     */
    public $nuevoNumeroFactura = NULL;

    /**
     * Orden predeterminado para organizar los listados
     * @var entero
     */
    public $ordenInicial = NULL;

    
    /**
     * Cargar los datos de una factura temporal que significa una factura de 
     * compra no finalizada.
     * 
     * @param entero $id Código interno o identificador de la factura en la tabla facturas temporales
     */
    public function cargarFacturaTemporal($id) {
        global $sql;

        if (isset($id) && $sql->existeItem('facturas_temporales_compra', 'id', intval($id))) {

            $tablas = array(
                'fc' => 'facturas_temporales_compra',
                'c1' => 'cajas',
                's'  => 'sedes_empresa',
                'p'  => 'proveedores',
                'u'  => 'usuarios'
            );

            $columnas = array(
                'id'                        => 'fc.id',
                'idProveedor'               => 'fc.id_proveedor',
                //'proveedor'                 => 'p.nombre',
                'numeroFacturaProveedor'    => 'fc.num_factura_proveedor',
                'fechaFactura'              => 'fc.fecha_factura',
                'fechaVtoFactura'           => 'fc.fecha_vencimiento',
//                'modoPago'                  => 'fc.modo_pago',
                'idCaja'                    => 'fc.id_caja',
                'caja'                      => 'c1.nombre',
                'idSede'                    => 'c1.id_sede',
                'sede'                      => 's.nombre',
                'id_usuario'                => 'fc.id_usuario',
                'usuario'                   => 'u.usuario',
                'iva'                       => 'fc.iva',
//                'retecree'                  => 'fc.retecree',
                'concepto1'                 => 'fc.concepto1',
                'descuento1'                => 'fc.descuento1',
                'concepto2'                 => 'fc.concepto2',
                'descuento2'                => 'fc.descuento2',
                'valorFlete'                => 'fc.valor_flete',
                'total'                     => 'fc.total',
                'subtotal'                  => 'fc.subtotal',
                //'retenciones'               => 'fc.retenciones',
                'estadoFactura'             => 'fc.estado_factura',
                'observaciones'             => 'fc.observaciones'
            );

            $condicion = 'fc.id_caja = c1.id AND c1.id_sede = s.id  AND fc.id_usuario = u.id AND fc.id_proveedor = p.id AND fc.id = "' . $id . '"';

            $consulta = $sql->seleccionar($tablas, $columnas, $condicion);

            if ($sql->filasDevueltas) {
                $fila = $sql->filaEnObjeto($consulta);

                foreach ($fila as $propiedad => $valor) {
                    $this->$propiedad = $valor;
                }

                $this->proveedor = new Proveedor($this->idProveedor);

                $tablas1 = array(
                    'af' => 'articulos_factura_temporal_compra',
                    'a'  => 'articulos'
                );
                $columnas1 = array(
                    'id'            => 'af.id',
                    'idFactura'     => 'af.id_factura_temporal',
                    'idArticulo'    => 'af.id_articulo',
                    'articulo'      => 'a.nombre',
                    'plu_interno'   => 'a.plu_interno',
                    'codigo_oem'    => 'a.codigo_oem',                    
                    'cantidad'      => 'af.cantidad',
                    'descuento'     => 'af.descuento',
                    'precio'        => 'af.precio',
                    'idBodega'      => 'af.id_bodega',
                    'precioVenta'   => 'af.precio_venta',
                    'iva'           => 'a.iva',
                );

                $condicion1 = 'af.id_articulo = a.id  AND af.id_factura_temporal = "' . $id . '"';
  
                $consulta1 = $sql->seleccionar($tablas1, $columnas1, $condicion1);

                if ($sql->filasDevueltas) {
                    while ($objeto = $sql->filaEnObjeto($consulta1)) {
                        $this->listaArticulos[] = $objeto;
                        
                    }
                }
                
            }
            
        }
        
    }

    /**
     *
     * Adicionar una factura temporal de compra
     *
     * @param  arreglo $datos       Datos de la factura a adicionar
     * @return entero               Código interno o identificador de la factura en la base de datos (NULL si hubo error)
     *
     */
    public function adicionarFacturaTemporal($datos) {
        global $sql, $sesion_usuarioSesion, $configuracion, $textos;

        $datosFactura = array(
            'id_proveedor'          => $datos['id_proveedor'],
            'num_factura_proveedor' => $datos['num_factura_proveedor'],
            'fecha_factura'         => $datos['fecha_factura'] . ' ' . date('H:i:s'),
            'fecha_vencimiento'     => $datos['fecha_vto_factura'],
//            'modo_pago'             => $datos['modo_pago'],
            'id_usuario'            => $sesion_usuarioSesion->id,
            'id_caja'               => $datos['id_caja'],
            'subtotal'              => $datos['subtotal'],
            'iva'                   => $datos['iva'],
//            'retecree'              => $datos['retecree'],
            'concepto1'             => $datos['concepto1'],
            'descuento1'            => $datos['descuento1'],
            'concepto2'             => $datos['concepto2'],
            'descuento2'            => $datos['descuento2'],
            'valor_flete'           => $datos['valor_flete'],
            'total'                 => $datos['total'],
            'observaciones'         => $datos['observaciones']
        );
        
        $sql->iniciarTransaccion();

        $consulta = $sql->insertar('facturas_temporales_compra', $datosFactura);
        $idItem = $sql->ultimoId;

        //forma ajax que envia al modulo de notificaciones
        $boton = HTML::boton('lapiz', $textos->id('MODIFICAR_FACTURA'), 'directo', '', '', '', array());
        
        
        //no se por que demonios lo plantee de esta forma, pero segur que tiene sentido
        $botonFormaAjax = HTML::botonImagenAjax($boton, '', '', array(), $configuracion['SERVIDOR']['principal'] . 'compras_mercancia', array('idFactTemp' => $idItem), '', array('target' => '_blank'));

        $proveedor = $sql->obtenerValor("proveedores", "nombre", "id = '".$datos['id_proveedor']."'");
        
        $notificacion  = str_replace('%1', $datos['fecha_factura'] . ' ' . date('H:i:s'), $textos->id('MENSAJE_FACTURA_TEMPORAL_COMPRA'));
        $notificacion  = str_replace('%2', $proveedor, $notificacion). ' ' . $botonFormaAjax;

        $idNoti = Servidor::notificar($sesion_usuarioSesion->id, addslashes($notificacion), array(), $this->idModulo, $idItem);
        //se actualiza la tabla de facturas temporales, se pone el id de la notificacion que genera, para poder borrarla en caso que se inicie nuevamente la factura
        $datosModificar = array('id_notificacion' => $idNoti);
        
        $modificar = $sql->modificar('facturas_temporales_compra', $datosModificar, 'id = "' . $idItem . '"');
        
        if (!$modificar) {
            $sql->cancelarTransaccion();
            return false;
        }


        if ($consulta) {
            
            if($datos['cadenaArticulosPrecios'] != ''){//verifico que la cadena que trae la lista con los datos de los articulos para la compra(id_articulo, precio, bodega, etc) no este vacia
                //recibir toda la cadena y hacer el insertar en una sola consulta de la lista de articulos            
                $arreglo = explode('|', substr($datos['cadenaArticulosPrecios'], 0, -1));


                foreach ($arreglo as $id => $valor) {
                    $articulo = explode(';', $valor);
                    $valoresConsulta .= '("' . $idItem . '", "' . $articulo[0] . '", "' . $articulo[1] . '", "' . $articulo[2] . '", "' . $articulo[3] . '" , "' . $articulo[4] . '" , "' . $articulo[5] . '"),';

                }

                $valoresConsulta = substr($valoresConsulta, 0, -1);


                $sentencia = "INSERT INTO fom_articulos_factura_temporal_compra (id_factura_temporal, id_articulo, cantidad, descuento, precio, id_bodega, precio_venta) VALUES $valoresConsulta";
                //$sql->depurar = true;
                $query = $sql->ejecutar($sentencia);  
                
                if (!$query) {
                    $sql->cancelarTransaccion();
                    return false;
                }                
                
            }

            $sql->finalizarTransaccion();
            return $idItem;
            
        } else {
            $sql->cancelarTransaccion();
            return false;
            
        }//fin del if($consulta)
    }


    /**
     *
     * Modificar factura temporal
     *
     * @param  arreglo $datos       Datos del articulo a modificar
     * @return lógico               Indica si el procedimiento se pudo realizar correctamente o no
     *
     */
    public function modificarFacturaTemporal($datos) {
        global $sql, $sesion_usuarioSesion;

        $idItem = $datos['id_factura_temporal'];
        if (!isset($idItem)) {
            return false;
        }

        $datosFactura = array(
            'id_proveedor'                  => $datos['id_proveedor'],
            'num_factura_proveedor'         => $datos['num_factura_proveedor'],
            'fecha_factura'                 => $datos['fecha_factura'] . ' ' . date('H:i:s'),
            'fecha_vencimiento'             => $datos['fecha_vto_factura'],
//            'modo_pago'                     => $datos['modo_pago'],
            'id_usuario'                    => $sesion_usuarioSesion->id,
            'id_caja'                       => $datos['id_caja'],
            'iva'                           => $datos['iva'],
//            'retecree'                      => $datos['retecree'],
            'subtotal'                      => $datos['subtotal'],
            'concepto1'                     => $datos['concepto1'],
            'descuento1'                    => $datos['descuento1'],
            'concepto2'                     => $datos['concepto2'],
            'descuento2'                    => $datos['descuento2'],
            'valor_flete'                   => $datos['valor_flete'],
            'total'                         => $datos['total'],
            'observaciones'                 => $datos['observaciones']
        );

        $sql->iniciarTransaccion();

        $consulta = $sql->modificar('facturas_temporales_compra', $datosFactura, 'id = "' . $datos['id_factura_temporal'] . '"');

        if ($consulta) {
            //primero elimino todos los articulos existentes en la tabla
            
            $consulta = $sql->eliminar('articulos_factura_temporal_compra', 'id_factura_temporal = "' . $datos['id_factura_temporal'] . '"');
            
            if (!$consulta) {
                $sql->cancelarTransaccion();
                return false;
            }
            
            if ($datos['cadenaArticulosPrecios'] != '') {
                //recibir toda la cadena y hacer el insertar en una sola consulta de la lista de articulos  
                $arreglo = explode('|', substr($datos['cadenaArticulosPrecios'], 0, -1));


                foreach ($arreglo as $id => $valor) {
                    $articulo = explode(';', $valor);
                    $valoresConsulta .= '("' . $datos['id_factura_temporal'] . '", "' . $articulo[0] . '", "' . $articulo[1] . '", "' . $articulo[2] . '", "' . $articulo[3] . '", "' . $articulo[4] . '", "' . $articulo[5] . '" ),';

                }

                $valoresConsulta = substr($valoresConsulta, 0, -1);


                $sentencia = "INSERT INTO fom_articulos_factura_temporal_compra (id_factura_temporal, id_articulo, cantidad, descuento, precio, id_bodega, precio_venta) VALUES $valoresConsulta";
                //$sql->depurar = true;
                $insertarListaArticulos = $sql->ejecutar($sentencia);

                if (!$insertarListaArticulos) {
                    $sql->cancelarTransaccion();
                    $idItem = false;
                    
                }   
                
            }
            
            $sql->finalizarTransaccion();
            return $idItem;
            
        } else {
            $sql->cancelarTransaccion();
            return false;
            
        }
        
    }

    
    /**
     *
     * Eliminar una factura
     *
     * @param entero $id    Código interno o identificador de una unidad en la base de datos
     * @return lógico       Indica si el procedimiento se pudo realizar correctamente o no
     *
     */
    public static function eliminarFacturaTemporal($idFactTemp) {
        global $sql;

        if (!isset($idFactTemp)) {
            return false;
        }

        $idNotificacion = $sql->obtenerValor('facturas_temporales_compra', 'id_notificacion', 'id = "' . $idFactTemp . '"');
        
        $sql->iniciarTransaccion();
        
        $query = $sql->eliminar('facturas_temporales_compra', 'id = "' . $idFactTemp . '"');

        if (!$query) {
            $sql->cancelarTransaccion();
            return false;
            
        } else {
            $query = $sql->eliminar('notificaciones', 'id = "' . $idNotificacion . '"'); 
            
            if (!$query) {
                $sql->cancelarTransaccion();
                return false;
            }
            
            $query = $sql->eliminar('articulos_factura_temporal_compra', 'id_factura_temporal = "' . $idFactTemp . '"');
            
            if (!$query) {
                $sql->cancelarTransaccion();
                return false;
            }
            
            $sql->finalizarTransaccion();
            return true;
            
        }
        
    }
    
    
}
