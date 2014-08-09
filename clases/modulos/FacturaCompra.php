<?php

/**
 * @package     FOLCS
 * @subpackage  Factura de compra
 * @author      Pablo Andrés Vélez Vidal <pavelez8@misena.edu.co>
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2012 Genesys Corporation.
 * @version     0.2
 * 
 * Descripción: modulo encargado de gestionar la información relacionada a todo el proceso de 
 * compras de mercancia para el negocio. En este módulo se pueden agregar, consultar, eliminar o 
 * modificar la información de cada una de las transacciones realizadas en dicho proceso. Este módulo 
 * esta directamente relacionado con el módulo de ordenes de compra.
 * 
 * dato: este módulo tiene la funcionalidad de guardar facturas temporales, esto significa que a medida 
 * que se va generando una factura se va guardando la información de la misma, esto significa que si por 
 * algun mótivo se cierra la factura en la que estamos trabajando, esta factura puede ser recuperada 
 * facilmente desde la pantalla principal del usuario que la estaba generando. 
 * (ver metodos guardarFacturaTemporalCompra y modificarFac...)
 * 
 * Modulo : compras.
 * 
 * tablas: facturas_compra y articulos_factura_compra.
 * 
 * integridad referencial: básicamente será imposible borrar una factura de compra (ver clase SQL),
 * lo más que se podrá hacer será inactivarla
 * 
 * https://github.com/Craswer/PhpNetworkLprPrinter //para usar impresoras con php
 * */
class FacturaCompra {

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
     * Inicializar los datos de una factura de compra
     * 
     * @param entero $id Código interno o identificador de la factura
     */
    public function __construct($id = NULL) {
        global $sql, $modulo;
        
        $this->urlBase          = '/' . $modulo->url;
        $this->url              = $modulo->url;
        $this->idModulo         = $modulo->id;

        //establecer el valor del campo predeterminado para organizar los listados
        $this->ordenInicial = "fc.fecha_factura";

        //Saber el numero de registros
        $this->registros        = $sql->obtenerValor('facturas_compras', 'COUNT(id)', 'id != "0"');
        //Saber el numero de registros activos
        $this->registrosActivos = $sql->obtenerValor('facturas_compras', 'COUNT(id)', 'id != "0"');

        if (isset($id)) {
            $this->cargar($id);
            
        }
        
    }

    /**
     * Cargar los datos de una factura
     * 
     * @param entero $id Código interno o identificador de la factura en la base de datos
     */
    public function cargar($id) {
        global $sql, $configuracion, $textos;

        if (isset($id) && $sql->existeItem('facturas_compras', 'id', intval($id))) {

            $tablas = array(
                'fc' => 'facturas_compras',
                'c1' => 'cajas',
                's'  => 'sedes_empresa',
                'p'  => 'proveedores',
                'u'  => 'usuarios'
            );

            $columnas = array(
                'id'                        => 'fc.id',
                'idFactura'                 => 'fc.id_factura',
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
                'subtotal'                  => 'fc.subtotal',
                'total'                     => 'fc.total',
                'retenciones'               => 'fc.retenciones',
                'estadoFactura'             => 'fc.estado_factura',
                'observaciones'             => 'fc.observaciones',
                'facturaDigital'            => 'fc.archivo',
            );

            $condicion = 'fc.id_caja = c1.id AND c1.id_sede = s.id  AND fc.id_usuario = u.id AND fc.id_proveedor = p.id AND fc.id = "' . $id . '"';

            $consulta = $sql->seleccionar($tablas, $columnas, $condicion);

            if ($sql->filasDevueltas) {
                $fila = $sql->filaEnObjeto($consulta);

                foreach ($fila as $propiedad => $valor) {
                    $this->$propiedad = $valor;
                }
                
                $this->proveedor = new Proveedor($this->idProveedor);

                if (!empty($this->facturaDigital)) {
                    $rutaArchivo            = $configuracion["RUTAS"]["media"] . "/" . $configuracion["RUTAS"]["archivos"] . "/facturas_compra/" . $this->facturaDigital;
                    $botonEliminarArchivo   = HTML::imagen($configuracion['RUTAS']['media'] . $configuracion['RUTAS']['imagenesEstilos'] . 'eliminarRojo.png', 'margenIzquierda cursorManito', 'eliminarFacturaDigital', array('ayuda' => $textos->id('ELIMINAR_FACTURA_DIGITAL'), 'idfactura' => $this->id));
                    $this->facturaDigital   = HTML::enlace($textos->id('FACTURA_DIGITAL_PROVEEDOR') . ' -> ' . $this->idFactura, $rutaArchivo, 'margenSuperiorDoble letraVerde negrilla', '', array('target' => '_blank')) . $botonEliminarArchivo;
                    $this->facturaDigital   = HTML::parrafo($this->facturaDigital, '');
                    $this->rutaFacturaDigital = $rutaArchivo;
                    
                } else {
                    $this->facturaDigital = 'No se ha subido la factura digital';
                    
                }

                /**
                 * Tablas y columnas para cargar los ARTICULOS relacionados a una factura 
                 */
                $tablas1 = array(
                    'af' => 'articulos_factura_compra',
                    'a'  => 'articulos'
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
                    'idBodega'      => 'af.id_bodega',
                    'idProveedor'   => 'af.id_proveedor',
                    'fecha'         => 'af.fecha',
                    'precioVenta'   => 'af.precio_venta',//PARA QUE?
                    'iva'           => 'a.iva',
                );

                $condicion1 = 'af.id_articulo = a.id  AND af.id_factura = "' . $id . '"';

                $consulta1 = $sql->seleccionar($tablas1, $columnas1, $condicion1);

                /**
                 * una vez se consulta se guarda la información en el arreglo listaArticulos, que contendra un objeto stdClass en cada una de sus posiciones
                 */
                if ($sql->filasDevueltas) {
                    while ($objeto = $sql->filaEnObjeto($consulta1)) {
                        $this->listaArticulos[] = $objeto;
                    }
                    
                }
                
                /**
                 * generar arreglo de retenciones llave -> valor, donde llave es el valor de la retencion y valor, el valor :)
                 */
                $arrRetenciones = explode('|', substr($this->retenciones, 0, -1));
                
                foreach ($arrRetenciones as $id => $valor) {
                    $retencion          = explode(';', $valor);
                    $nombreRetencion    = $configuracion["RETENCIONES"][$configuracion["GENERAL"]["idioma"]][$retencion[0]]["nombre"];
                    
                    $this->arregloRetenciones[$nombreRetencion] = $retencion[1];
                    
                }
                
            }
            
        }
        
    }

    /**
     *
     * Adicionar una factura
     *
     * @param  arreglo $datos       Datos de la factura a adicionar
     * @return entero               Código interno o identificador de la factura en la base de datos (NULL si hubo error)
     *
     */
    public function adicionar($datos) {
        global $sql, $sesion_usuarioSesion;
        
        //verificar si este mismo proveedor nos habia traido una factura antes con el mismo numero
        $idFacturaPrevia = $sql->obtenerValor('facturas_compras', 'id', 'id_proveedor = "'.$datos['id_proveedor'].'" AND num_factura_proveedor = "'.$datos['num_factura_proveedor'].'" ');
        
        if($idFacturaPrevia){
            $this->existeFacturaProveedor = $idFacturaPrevia;
            return true;
            
        }  
        
        $datosFactura = array(
            'id_proveedor'              => $datos['id_proveedor'],
            'num_factura_proveedor'     => $datos['num_factura_proveedor'],
            'fecha_factura'             => $datos['fecha_factura'] . ' ' . date('H:i:s'),
            'id_usuario'                => $sesion_usuarioSesion->id,
            'id_caja'                   => $datos['id_caja'],
            'subtotal'                  => $datos['subtotal'],
            'iva'                       => $datos['iva'],
            'concepto1'                 => $datos['concepto1'],
            'descuento1'                => $datos['descuento1'],
            'concepto2'                 => $datos['concepto2'],
            'descuento2'                => $datos['descuento2'],
            'valor_flete'               => $datos['valor_flete'],
            'total'                     => $datos['total'],
            'retenciones'               => $datos['retenciones'],
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
                        
        if(isset($datos['fecha_vto_factura'])){
            $datosFactura['fecha_vencimiento'] = $datos['fecha_vto_factura'];
        }

        $sql->iniciarTransaccion();
        
        $query      = $sql->insertar('facturas_compras', $datosFactura);
        $idItem     = $sql->ultimoId;

        if ($query) {
            //recibir toda la cadena y hacer el insertar en una sola consulta de la lista de articulos   
            //aqui se recibe una cadena que contiene el listado completo de todos los articulos que se van a ingresar
            //por una compra separados por pipe's
            $arreglo = explode('|', substr($datos['cadenaArticulosPrecios'], 0, -1));
            //cada posicion del areglo ahora contiene una cadena de datos separados por ";" el cual se encuentran
            // 0=> id_articulo, 1=> cantidad, 2=> descuento, 3=> precio unitario, 4=> bodega, 5=> precio de venta
            $inventario = new Inventario();
            $arti       = new Articulo();
            
            $valoresConsulta = '';

            foreach ($arreglo as $id => $valor) {
                $articulo   = explode(';', $valor);
                
                $valoresConsulta .= '("' . $idItem . '", "' . $articulo[0] . '", "' . $articulo[1] . '", "' . $articulo[2] . '", "' . $articulo[3] . '", "' . $articulo[4] . '", "' . $datos['id_proveedor'] . '", "'.$articulo[5].'" ),';
                
                //adicionar estos articulos comprados al inventario
                $sumarInventario = $inventario->adicionar($articulo[0], $articulo[1], $articulo[4], $idItem);
                
                if(!$sumarInventario){
                    $sql->cancelarTransaccion();

                }                 
                //generar registrp de datos para mostrar en el kardes
                $datosKardex = array(
                    'id_articulo'           => $articulo[0],
                    'fecha'                 => $datos['fecha_factura'] . ' ' . date('H:i:s'),
                    'concepto'              => 'compras',
                    'num_factura'           => $idItem,
                    'cantidad_compra'       => $articulo[1],
                    'val_unitario_compra'   => $articulo[3],
                    'val_total_compra'      => ($articulo[1] * $articulo[3]),
                    'cantidad_venta'        => '',
                    'val_unitario_venta'    => '',
                    'val_total_venta'       => '',
                    'cantidad_saldo'        => $articulo[1],
                    'total_saldo'           => ($articulo[1] * $articulo[3]),
                );

                $insertarEnKardex = Kardex::adicionar($datosKardex);

                if(!$insertarEnKardex){
                    $sql->cancelarTransaccion();

                }
                
                //aqui se le modifican precios al articulo como por ejempo el ultimo precio de compra y el precio de venta
                $arti->modificarInfoCompras($articulo[0], $articulo[3], $articulo[5]);
            
            }

            $valoresConsulta = substr($valoresConsulta, 0, -1);

            $sentencia = "INSERT INTO fom_articulos_factura_compra (id_factura, id_articulo, cantidad, descuento, precio, id_bodega, id_proveedor, precio_venta) VALUES $valoresConsulta";

            $insertarListaArticulos = $sql->ejecutar($sentencia);

            if ($insertarListaArticulos) {
                $idFactura      = 'FC' . (int) $sesion_usuarioSesion->sede->id . '-' . $idItem;
                $datosModificar = array('id_factura' => $idFactura);
                $modiFactura    = $sql->modificar('facturas_compras', $datosModificar, 'id = "' . $idItem . '"');
                
                if(!$modiFactura){
                    $sql->cancelarTransaccion();
                    
                }
                
            } else {
                $sql->cancelarTransaccion();
                $idItem = false;
                
            }           
            
            //añadir la notificacion de la factura a pagar
            if(!empty($datos['fecha_vto_factura']) && $datos["fecha_vto_factura"] != "0000-00-00"){
                //notificar que esta factura se va a vencer
                $datos = array(
                    'proveedor'         => $datos['id_proveedor'],
                    'fecha_vencimiento' => $datos['fecha_vto_factura'],
                );
                
                Evento::adicionarEventoVencimientoFC($datos);
                
            }
            
            //Ejecutar las transacciones contables
            $contabilidad = new ContabilidadCompras();
            
            $contabilidad->contabilizarFacturaCompra($idItem, $datosMediosPago);

            $sql->finalizarTransaccion();
            return $idItem;            
            
        } else {
            $sql->cancelarTransaccion();
            return false;
            
        }
        
    }

    /**
     *
     * Modificar una factura de compra.
     * 
     * Como se modifica una factura de compra si hubo errores??. Como se afecta el inventario??. La contabilidad.??
     *
     * @param  arreglo $datos       Datos del articulo a modificar
     * @return lógico               Indica si el procedimiento se pudo realizar correctamente o no
     *
     */
    public function modificar($datos) {
        global $sql, $sesion_usuarioSesion;

        if (!isset($this->id)) {
            return false;
        }

        $datosFactura = array(
            'id_proveedor'              => $datos['id_proveedor'],
            'num_factura_proveedor'     => $datos['num_factura_proveedor'],
            'fecha_factura'             => $datos['fecha_factura'] . ' ' . date('H:i:s'),
            'fecha_vencimiento'         => $datos['fecha_vto_factura'],
//            'modo_pago'                 => $datos['modo_pago'],
            'id_usuario'                => $sesion_usuarioSesion->id,
            'id_caja'                   => $datos['id_caja'],
            'subtotal'                  => $datos['subtotal'],
            'iva'                       => $datos['iva'],
//            'retecree'                  => $datos['retecree'],
            'concepto1'                 => $datos['concepto1'],
            'descuento1'                => $datos['descuento1'],
            'concepto2'                 => $datos['concepto2'],
            'descuento2'                => $datos['descuento2'],
            'valor_flete'               => $datos['valor_flete'],
            'total'                     => $datos['total'],
            'observaciones'             => $datos['observaciones']
        );

        $sql->iniciarTransaccion();

        $consulta = $sql->modificar('facturas_compras', $datosFactura, 'id = "' . $this->id . '"');

        if ($consulta) {
            //recibir toda la cadena y hacer el insertar en una sola consulta de la lista de articulos  
            $consulta = $sql->eliminar('articulos_factura_compra', 'id_factura = "' . $this->id . '"');
            
            if (!$consulta) {
                $sql->cancelarTransaccion();
                return false;
                
            }

            $arreglo = explode('|', substr($datos['cadenaArticulosPrecios'], 0, -1));

            
            foreach ($arreglo as $id => $valor) {
                $articulo = explode(';', $valor);
                $valoresConsulta .= '("' . $this->id . '", "' . $articulo[0] . '", "' . $articulo[1] . '", "' . $articulo[2] . '", "' . $articulo[3] . '", "' . $articulo[4] . '", "' . $datos['id_proveedor'] . '", "' . $articulo[5] . '"),';
            }

            $valoresConsulta = substr($valoresConsulta, 0, -1);

            $sentencia = "INSERT INTO fom_articulos_factura_compra (id_factura, id_articulo, cantidad, descuento, precio, id_bodega, id_proveedor, precio_venta) VALUES $valoresConsulta";

            $insertarListaArticulos = $sql->ejecutar($sentencia);

            if (!$insertarListaArticulos) {
                $sql->cancelarTransaccion();
                $idItem = false;
                
            }
            
            //añadir la notificacion de la factura a pagar
            if(!empty($datos['fecha_vto_factura'])){
                //notificar que esta factura se va a vencer
            }           
            
            //Ejecutar las transacciones contables
            //$contabilidad = new Contabilidad("FACTURAS_COMPRA");
            //$query = $contabilidad->contabilizarModificacionFacturaCompra($this);            

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
     * @return lógico       Indica si el procedimiento se pudo realizar correctamente o no
     *
     */
    public function eliminar() 
    {
        global $sql, $configuracion;

        if (!isset($this->id)) {
            return false;
        }
        
        $sql->iniciarTransaccion();
        
        $consulta = $sql->eliminar('facturas_compras', 'id = "' . $this->id . '"');

        if (!$consulta) {
            $sql->cancelarTransaccion();
            return false;
            
        } else {            
            $query = $sql->eliminar('articulos_factura_compra', 'id_factura = "' . $this->id . '"');
            
            if (!$query) {
                $sql->cancelarTransaccion();
                return false;
            
            }
            
            if ($this->facturaDigital) {
                $configuracionRuta = $configuracion['RUTAS']['media'] . '/' . $configuracion['RUTAS']['archivos'] . '/facturas_compra/' . $this->id;
                $eliminar = Archivo::eliminarArchivoDelServidor($configuracionRuta);
                
                if (!$eliminar) {
                    $sql->cancelarTransaccion();
                    return false;
            
                }
                
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
        
        $datos = array('activo' => '0');
        
        $consulta = $sql->modificar('facturas_compras', $datos, 'id = "' . $this->id . '"');

        if (!$consulta) {
            $sql->cancelarTransaccion();
            return false;
            
        } 
        
        $sql->finalizarTransaccion();
        return true;
        
    }    

    /**
     * Cargar al servidor y asociar a la factura una copia digital de la misma
     * 
     * @global objeto $sql objeto de interacción global con la BD
     * @global arreglo $configuracion
     * @param objeto $archivo archivo a ser cargado
     * @return boolean true or false dependiendo del exito de la operación
     */
    public function cargarFacturaDigital($archivo) {
        global $sql, $configuracion;
        
        if ($this->facturaDigital) {
            $this->eliminarFacturaDigital();
        }

        $validarFormato = Archivo::validarArchivo($archivo, array("doc", "docx", "pdf", "ppt", "pptx", "pps", "ppsx", "xls", "xlsx", "odt", "rtf", "txt", "ods", "odp", "jpg", "jpeg", "png"));

        if (!$validarFormato) {
            $configuracionRuta = $configuracion["RUTAS"]["media"] . "/" . $configuracion["RUTAS"]["archivos"] . "/facturas_compra/";
            
            $recurso = Archivo::subirArchivoAlServidor($archivo, $configuracionRuta);
            
            if ($recurso) {
                $datosFactura   = array('archivo' => $recurso);
                $consulta       = $sql->modificar('facturas_compras', $datosFactura, 'id = "' . $this->id . '"');
                
                if ($consulta) {
                    return true;
                    
                } else {
                    Archivo::eliminarArchivoDelServidor($configuracionRuta.$this->id);
                    return false;
                    
                }
            } else {
                return false;
                
            }
            
        } else {
            return false;
            
        }
        
    }

    /**
     * Metodo que se encarga de eliminar la factura digital
     * 
     * @global objeto $sql objeto global de interacción con la BD
     * @global array $configuracion arreglo global donde se almacenan los parametros de configuración
     * @return boolean true or false dependiendo del exito de la operación
     */
    public function eliminarFacturaDigital() {
        global $sql;

        $recurso = Archivo::eliminarArchivoDelServidor(array($this->rutaFacturaDigital));
        
        if ($recurso) {
            $datosFactura = array('archivo' => '');
            $consulta = $sql->modificar('facturas_compras', $datosFactura, 'id = "' . $this->id . '"');
            
            if ($consulta) {
                return true;
                
            } else {//espera tres sec y vuelve y lo intenta
                sleep(3);
                $sql->modificar('facturas_compras', $datosFactura, 'id = "' . $this->id . '"');
                return true;
                
            }
            
        } else {
            return false;
            
        }
    }
   

    /**
     *
     * Listar las facturas
     *
     * @param entero  $cantidad    Número de articulos a incluir en la lista (0 = todas las entradas)
     * @param arreglo $excepcion   Arreglo con los códigos internos o identificadores a omitir en la lista
     * @param cadena  $condicion   Condición adicional (SQL)
     * @return arreglo             Lista de articulos
     *
     */
    public function listar($inicio = 0, $cantidad = 0, $excepcion = NULL, $condicionGlobal = NULL, $orden = NULL) {
        global $sql, $textos;

        $condicion = '';
        
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
            $condicion .= 'fc.id NOT IN (' . $excepcion . ') AND ';
        }

        /*         * * Definir el orden de presentación de los datos ** */
        if (!isset($orden)) {
            $orden = 'fc.fecha_factura';
        }
        if ($this->listaAscendente) {
            $orden = $orden . ' ASC';
            
        } else {
            $orden = $orden . ' DESC';
            
        }

        $tablas = array(
            'fc' => 'facturas_compras',
            'c1' => 'cajas',
            's'  => 'sedes_empresa',
            'p'  => 'proveedores',
            'u'  => 'usuarios'
        );

        $columnas = array(
            'id'                => 'fc.id',
            'idFactura'         => 'fc.id_factura',
            'idProveedor'       => 'fc.id_proveedor',
            'proveedor'         => 'p.nombre',
            'numeroFactura'     => 'fc.num_factura_proveedor',
            'fechaFactura'      => 'fc.fecha_factura',
            'idCaja'            => 'fc.id_caja',
            'caja'              => 'c1.nombre',
            'idSede'            => 'c1.id_sede',
            'sede'              => 's.nombre',
            'id_usuario'        => 'fc.id_usuario',
            'usuario'           => 'u.usuario',
            'estadoFactura'     => 'fc.estado_factura',
            'activo'            => 'fc.activo',
        );



        if (!empty($condicionGlobal)) {
            $condicion .= $condicionGlobal . ' AND ';
        }

        $condicion .= 'fc.id_caja = c1.id AND c1.id_sede = s.id  AND fc.id_usuario = u.id AND fc.id_proveedor = p.id';

        if (is_null($this->registrosConsulta)) {
            $sql->seleccionar($tablas, $columnas, $condicion);
            $this->registrosConsulta = $sql->filasDevueltas;
        }

//        $sql->depurar = true;
        $consulta = $sql->seleccionar($tablas, $columnas, $condicion, 'fc.id', $orden, $inicio, $cantidad);

        if ($sql->filasDevueltas) {
            $lista = array();

            while ($objeto = $sql->filaEnObjeto($consulta)) {
                $objeto->activo =  ($objeto->activo) ? HTML::frase($textos->id("ACTIVO"), "activo") : HTML::frase($textos->id("INACTIVO"), "inactivo");

                $lista[] = $objeto;
            }
            
        }

        return $lista;
    }


    /**
     * Metodo que genera los datos que contendra la tabla principal del modulo
     * 
     * @global type $textos
     * @param type $arregloRegistros
     * @param type $datosPaginacion
     * @return type 
     */
    public function generarTabla($arregloRegistros, $datosPaginacion = NULL) {
        global $textos, $sesion_usuarioSesion;
        //Declaracion de las columnas que se van a mostrar en la tabla
        $datosTabla = array(
            HTML::parrafo($textos->id('NUMERO_DE_FACTURA'), 'centrado')     => 'id|fc.id',
            HTML::parrafo($textos->id('SEDE'), 'centrado')                  => 'sede|s.nombre',
            HTML::parrafo($textos->id('ID_PROVEEDOR'), 'centrado')          => 'idProveedor|fc.id_proveedor',             
            HTML::parrafo($textos->id('PROVEEDOR'), 'centrado')             => 'proveedor|p.nombre',
            HTML::parrafo($textos->id('NUM_FACT_PROVEEDOR'), 'centrado')    => 'numeroFactura|fc.num_factura_proveedor',
            HTML::parrafo($textos->id('USUARIO_CREADOR'), 'centrado')       => 'usuario|u.usuario',
            HTML::parrafo($textos->id('FECHA_FACTURA'), 'centrado')         => 'fechaFactura|fc.fecha_factura',
            HTML::parrafo($textos->id('ESTADO'), 'centrado')                => 'activo|fc.activo',
        );
        //ruta del metodo paginador
        $rutaPaginador = '/ajax' . $this->urlBase . '/move';
        
        $adicionarNotaCredito = '';

        $puedeAdicionarNotaCredito= Perfil::verificarPermisosBoton('botonAdicionarNotaCreditoCompra');
        
        if ($puedeAdicionarNotaCredito || $sesion_usuarioSesion->id == 0) {
            $adicionarNotaCredito1 = HTML::formaAjax($textos->id('ADICIONAR_NOTA_CREDITO'), 'contenedorMenuAdicionarNotaCredito', 'adicionarNotaCredito', '', '/ajax/facturas_compra/adicionarNotaCredito', array('id' => ''));
            $adicionarNotaCredito = HTML::contenedor($adicionarNotaCredito1, '', 'botonAdicionarNotaCreditoCompra');

        }    

        $adicionarNotaDebito = '';

        $puedeAdicionarNotaDebito= Perfil::verificarPermisosBoton('botonAdicionarNotaDebitoCompra');
        if ($puedeAdicionarNotaDebito || $sesion_usuarioSesion->id == 0) {
            $adicionarNotaDebito1 = HTML::formaAjax($textos->id('ADICIONAR_NOTA_DEBITO'), 'contenedorMenuAdicionarNotaDebito', 'adicionarNotaDebito', '', '/ajax/facturas_compra/adicionarNotaDebito', array('id' => ''));
            $adicionarNotaDebito = HTML::contenedor($adicionarNotaDebito1, '', 'botonAdicionarNotaDebitoCompra');

        }         

        $botonesExtras = array($adicionarNotaCredito, $adicionarNotaDebito);

        return Recursos::generarTablaRegistros($arregloRegistros, $datosTabla, $rutaPaginador, $datosPaginacion) . HTML::crearMenuBotonDerecho('FACTURAS_COMPRA', $botonesExtras);
    }

    /**
     * modificar la fecha de una factura
     * @param entero $id    Código interno o identificador de una unidad en la base de datos
     * @return lógico       Indica si el procedimiento se pudo realizar correctamente o no
     */
    public function modificarFechaFactura($id, $fechaFact) {
        global $sql;

        if (!isset($id) || !isset($fechaFact)) {
            return false;
        }

        $datos = array(
            'fecha_factura' => $fechaFact
        );

        $modificar = $sql->modificar('facturas_compras', $datos, 'id = "' . $id . '"');

        if ($modificar) {
            return true;
            
        } else {
            return false;
            
        }
    }
    
    
}
