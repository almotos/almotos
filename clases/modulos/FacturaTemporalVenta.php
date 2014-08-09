<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Clase encargada de gestionar los datos de las facturas temporales de ventas, 
 * que son las facturas de ventas que se empiezan a crear, pero no se finalizan.
 *
 * @author pipe
 */
class FacturaTemporalVenta {

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
    public $listaAscendente = TRUE;
    
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
     * Cargar los datos de una unidad
     * @param entero $id Código interno o identificador de la unidad en la base de datos
     */
    public function cargarFacturaTemporal($id) {
        global $sql;

        if (isset($id) && $sql->existeItem('facturas_temporales_venta', 'id', intval($id))) {

            $tablas = array(
                'fv'    => 'facturas_temporales_venta',
                's'     => 'sedes_empresa',
                'c1'    => 'cajas',
                'c'     => 'clientes',
                'u'     => 'usuarios'
            );

            $columnas = array(
                'id'                            => 'fv.id',
                'idCliente'                     => 'fv.id_cliente',
                'cliente'                       => 'c.nombre',
                'fechaFactura'                  => 'fv.fecha_factura',
                'fechaVtoFactura'               => 'fv.fecha_vencimiento',
                'modoPago'                      => 'fv.modo_pago',
                'idCaja'                        => 'fv.id_caja',
                'caja'                          => 'c1.nombre',
                'idSede'                        => 'c1.id_sede',
                'sede'                          => 's.nombre',
                'id_usuario'                    => 'fv.id_usuario',
                'usuario'                       => 'u.usuario',
                'iva'                           => 'fv.iva',
                'concepto1'                     => 'fv.concepto1',
                'descuento1'                    => 'fv.descuento1',
                'concepto2'                     => 'fv.concepto2',
                'descuento2'                    => 'fv.descuento2',
                'fechaLimiteDcto1'              => 'fv.fecha_limite_dcto_1',
                'porcentajeDcto1'               => 'fv.porcentaje_dcto_1',
                'fechaLimiteDcto2'              => 'fv.fecha_limite_dcto_2',
                'porcentajeDcto2'               => 'fv.porcentaje_dcto_2',
                'valorFlete'                    => 'fv.valor_flete',
                'total'                         => 'fv.total',
                'subtotal'                      => 'fv.subtotal',
                'estadoFactura'                 => 'fv.estado_factura',
                'observaciones'                 => 'fv.observaciones'
            );

            $condicion = 'fv.id_caja = c1.id AND c1.id_sede = s.id  AND fv.id_usuario = u.id AND fv.id_cliente = c.id AND fv.id = "' . $id . '"';

            
            $consulta = $sql->seleccionar($tablas, $columnas, $condicion);

            if ($sql->filasDevueltas) {
                $fila = $sql->filaEnObjeto($consulta);

                foreach ($fila as $propiedad => $valor) {
                    $this->$propiedad = $valor;
                }

                $tablas1    =       array(
                                        'af' => 'articulos_factura_temporal_venta',
                                        'a' => 'articulos'
                                        );
                
                $columnas1  =       array(
                                        'id'                => 'af.id',
                                        'idFactura'         => 'af.id_factura_temporal',
                                        'idArticulo'        => 'af.id_articulo',
                                        'articulo'          => 'a.nombre',
                                        'plu_interno'       => 'a.plu_interno',
                                        'codigo_oem'        => 'a.codigo_oem',                    
                                        'cantidad'          => 'af.cantidad',
                                        'descuento'         => 'af.descuento',
                                        'precio'            => 'af.precio',
                                        'iva'               => 'af.iva'
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
     * Adicionar una factura
     * @param  arreglo $datos       Datos de la factura a adicionar
     * @return entero               Código interno o identificador de la factura en la base de datos (NULL si hubo error)
     */
    public function adicionarFacturaTemporal($datos) {
        global $sql, $sesion_usuarioSesion, $configuracion, $textos;

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

        $sql->iniciarTransaccion();
        
        $query = $sql->insertar('facturas_temporales_venta', $datosFactura);
        
        $idItem = $sql->ultimoId;

        //forma ajax que envia al modulo de notificaciones
        $boton = HTML::boton('lapiz', $textos->id('MODIFICAR_FACTURA'), 'directo', '', '', '', array());
        $botonFormaAjax = HTML::botonImagenAjax($boton, '', '', array(), $configuracion['SERVIDOR']['principal'] . 'ventas_mercancia', array('idFactTemp' => $idItem), '', array('target' => '_blank'));

        $cliente = $sql->obtenerValor("clientes", "nombre", "id = '".$datos['id_cliente']."'");
        
        
        $notificacion = str_replace('%1', $datos['fecha_factura'] . ' ' . date('H:i:s'), $textos->id("MENSAJE_FACTURA_TEMPORAL_VENTA"));
        $notificacion = str_replace('%2', $cliente, $notificacion) . ' ' . $botonFormaAjax;

        $idNoti = Servidor::notificar($datos['id_usuario'], addslashes($notificacion), array(), $this->idModulo, $idItem);
        //se actualiza la abla de facturas temporales, se pone el id de la notificacion que genera, para poder borrarla en caso que se inicie nuevamente la factura
        $datosModificar = array('id_notificacion' => $idNoti);
        
        $modificar = $sql->modificar('facturas_temporales_venta', $datosModificar, 'id = "' . $idItem . '"');

        if (!$modificar) {
            $sql->cancelarTransaccion();
            return false;
        }

        if ($query) {
            //recibir toda la cadena y hacer el insertar en una sola consulta de la lista de articulos            
            $arreglo = explode('|', substr($datos['cadenaArticulosPrecios'], 0, -1));

            
            foreach ($arreglo as $id => $valor) {
                $articulo = explode(';', $valor);
                $valoresConsulta .= '("' . $idItem . '", "' . $articulo[0] . '", "' . $articulo[1] . '", "' . $articulo[2] . '", "' . $articulo[3]. '", "' . $articulo[5] . '" ),';
            }

            $valoresConsulta = substr($valoresConsulta, 0, -1);


            $sentencia = "INSERT INTO fom_articulos_factura_temporal_venta (id_factura_temporal, id_articulo, cantidad, descuento, precio, iva) VALUES $valoresConsulta";

            $query = $sql->ejecutar($sentencia);
            
            if (!$query) {
                return false;
                $sql->cancelarTransaccion();       
                
            }

            $sql->finalizarTransaccion();  
            return $idItem;
            
        } else {
            return false;
            $sql->cancelarTransaccion();
            
        }
        
    }
    
    /**
     * Modificar factura temporal
     * 
     * @param  arreglo $datos       Datos del articulo a modificar
     * @return lógico               Indica si el procedimiento se pudo realizar correctamente o no
     */
    public function modificarFacturaTemporal($datos) {
        global $sql, $sesion_usuarioSesion;

        $idItem = $datos['id_factura_temporal'];
        
        if (!isset($idItem)) {
            return NULL;
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
        
        $sql->iniciarTransaccion();

        $consulta = $sql->modificar('facturas_temporales_venta', $datosFactura, 'id = "' . $datos['id_factura_temporal'] . '"');

        if ($consulta) {
            //recibir toda la cadena y hacer el insertar en una sola consulta de la lista de articulos  
            $eliminar = $sql->eliminar('articulos_factura_temporal_venta', 'id_factura_temporal = "' . $datos['id_factura_temporal'] . '"');

            if (!$eliminar) {
                $sql->cancelarTransaccion();
                return false;                
            }
            
            $arreglo = explode('|', substr($datos['cadenaArticulosPrecios'], 0, -1));

            
            foreach ($arreglo as $id => $valor) {
                $articulo = explode(';', $valor);
                $valoresConsulta .= '("' . $datos['id_factura_temporal'] . '", "' . $articulo[0] . '", "' . $articulo[1] . '", "' . $articulo[2] . '", "' . $articulo[3]. '", "' . $articulo[5] . '" ),';
            }

            $valoresConsulta = substr($valoresConsulta, 0, -1);


            $sentencia = "INSERT INTO fom_articulos_factura_temporal_venta (id_factura_temporal, id_articulo, cantidad, descuento, precio, iva) VALUES $valoresConsulta";

            $insertarListaArticulos = $sql->ejecutar($sentencia);

            if ($insertarListaArticulos) {
                //no se pa que? despues lo miro
                
            } else {
                $sql->cancelarTransaccion();
                return false;
                
            }

            $sql->finalizarTransaccion();
            return $idItem;
            
        } else {
            $sql->cancelarTransaccion();
            return false;
            
        }
        
    }

    
    /**
     * Eliminar una factura
     * @param entero $id    Código interno o identificador de una unidad en la base de datos
     * @return lógico       Indica si el procedimiento se pudo realizar correctamente o no
     * 
     * bandas, llantas, neumatiucos, balineras, radios, cauchos de campana
     */
    public static function eliminarFacturaTemporal($idFactTemp) {
        global $sql;  

        if (!isset($idFactTemp)) {
            return NULL;
        }

        $sql->iniciarTransaccion();

        $idNotificacion = $sql->obtenerValor('facturas_temporales_venta', 'id_notificacion', 'id = "' . $idFactTemp . '"');

        $eliminar = $sql->eliminar('facturas_temporales_venta', 'id = "' . $idFactTemp . '"');
        
        if (!$eliminar) {
            $sql->cancelarTransaccion();
            return false;
            
        } else {
            
            $eliminar1 = $sql->eliminar('notificaciones', 'id = "' . $idNotificacion . '"');
            
            if (!$eliminar1) {
                $sql->cancelarTransaccion();
                return false;
            }
            
            $eliminar2 = $sql->eliminar('articulos_factura_temporal_venta', 'id_factura_temporal = "' . $idFactTemp . '"');
                        
            if (!$eliminar2) {
                $sql->cancelarTransaccion();
                return false;
            }
            
            $sql->finalizarTransaccion();
            return true;
            
        }
    }


}
