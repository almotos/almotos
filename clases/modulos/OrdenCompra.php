<?php

/**
 * @package     FOLCS
 * @subpackage  Orden de compra
 * @author      Pablo Andrés Vélez Vidal <pavelez8@misena.edu.co>
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2012 Genesys Corporation.
 * @version     0.2
 * 
 * Ordenes que son generadas cuando se va a realizar compra de mercancia
 * dentro del sistema. Podria ser una especie de cotizaciones que haga el sistema.
 * 
 * */
class OrdenCompra {

    /**
     * Código interno o identificador del registro
     * @var entero
     */
    public $id;

    /**
     * Código interno o identificador del registro
     * @var entero
     */
    public $idOrden;

    /**
     * URL relativa del módulo de ordenes de compra
     * @var cadena
     */
    public $urlBase;

    /**
     * URL relativa de un orden de compra específico
     * @var cadena
     */
    public $url;

    /**
     * Código interno del modulo orden de compra
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
     * fecha en que se realia  la orden
     * @var cadena
     */
    public $fechaOrden;

    /**
     * Identificador del orden de compra que genera la orden
     * @var entero
     */
    public $id_usuario;

    /**
     *  usuario que genera la orden
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
     * Identificador de la sede donde se genera la orden
     * @var entero
     */
    public $idSede;

    /**
     * sede donde se genera la orden
     * @var entero
     */
    public $sede;

    /**
     * determina el iva que tiene la orden
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
     * el valor del flete de la mercancia
     * @var enum
     */
    public $valorFlete;
    
    /**
     * subtotal de la orden de compra
     * @var entero
     */
    public $subtotal;    

    /**
     * total de la orden de compra
     * @var entero
     */
    public $total;

    /**
     * observaciones realizadas a la orden
     * @var entero
     */
    public $observaciones;
    
    /**
     * Determina si la orden se encuentra activa o no
     * @var entero
     */
    public $activo;    

    /**
     * listado de articulos que contiene la orden
     * @var array
     */
    public $listaArticulos;

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
     * Inicializar los datos de una orden de compra
     * 
     * @param entero $id Código interno o identificador de la orden
     */
    public function __construct($id = NULL) {
        global $sql, $modulo;
        
        $this->urlBase      = '/' . $modulo->url;
        $this->url          = $modulo->url;
        $this->idModulo     = $modulo->id;

        //Saber el numero de registros
        $this->registros            = $sql->obtenerValor('ordenes_compra', 'COUNT(id)', 'id != "0"');
        //Saber el numero de registros activos
        $this->registrosActivos     = $sql->obtenerValor('ordenes_compra', 'COUNT(id)', 'activo = "1" AND id != "0"');
        //establecer el valor del campo predeterminado para organizar los listados
        $this->ordenInicial         = "fecha_orden";          

        if (isset($id)) {
            $this->cargar($id);
        }
        
    }

    /**
     * Cargar los datos de una orden de compra
     * 
     * @param entero $id Código interno o identificador de la orden de compra en la base de datos
     */
    public function cargar($id) {
        global $sql;

        if (isset($id) && $sql->existeItem('ordenes_compra', 'id', intval($id))) {

            $tablas = array(
                'oc' => 'ordenes_compra',
                'c1' => 'cajas',
                's'  => 'sedes_empresa',
                'p'  => 'proveedores',
                'u'  => 'usuarios'
            );

            $columnas = array(
                'id'                => 'oc.id',
                'idOrden'           => 'oc.id_orden',
                'idProveedor'       => 'oc.id_proveedor',
                'fechaOrden'        => 'oc.fecha_orden',
                'idCaja'            => 'oc.id_caja',
                'caja'              => 'c1.nombre', 
                'sede'              => 's.nombre',
                'id_usuario'        => 'oc.id_usuario',
                'usuario'           => 'u.usuario',
                'subtotal'          => 'oc.subtotal',
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

            $condicion = 'oc.id_caja = c1.id AND c1.id_sede = s.id AND oc.id_usuario = u.id AND oc.id_proveedor = p.id AND oc.id = "' . $id . '"';

            //$sql->depurar = true;
            $consulta = $sql->seleccionar($tablas, $columnas, $condicion);

            if ($sql->filasDevueltas) {
                $fila = $sql->filaEnObjeto($consulta);

                foreach ($fila as $propiedad => $valor) {
                    $this->$propiedad = $valor;
                    
                }

                $tablas1 = array(
                    'ao' => 'articulos_orden_compra',
                    'a'  => 'articulos'
                );

                $columnas1 = array(
                    'id'            => 'ao.id',
                    'idOrden'       => 'ao.id_orden',
                    'idArticulo'    => 'ao.id_articulo',
                    'articulo'      => 'a.nombre',
                    'plu_interno'   => 'a.plu_interno',
                    'codigo_oem'    => 'a.codigo_oem',
                    'cantidad'      => 'ao.cantidad',
                    'descuento'     => 'ao.descuento',
                    'precio'        => 'ao.precio',
                    'idBodega'      => 'ao.id_bodega',
                    'idProveedor'   => 'ao.id_proveedor',
                    'fecha'         => 'ao.fecha',
                    'iva'           => 'a.iva',
                    'precioVenta'   => 'ao.precio_venta',//PARA QUE?
                );                

                $condicion1 = 'ao.id_articulo = a.id  AND ao.id_orden = "' . $id . '"';

                $consulta1 = $sql->seleccionar($tablas1, $columnas1, $condicion1);

                if ($sql->filasDevueltas) {
                    while ($objeto = $sql->filaEnObjeto($consulta1)) {
                        $this->listaArticulos[] = $objeto;
                    }
                    
                }
                
                $this->proveedor = new Proveedor($this->idProveedor);
                
            }
            
        }
        
    }


    /**
     *
     * Adicionar una orden
     *
     * @param  arreglo $datos       Datos de la orden a adicionar
     * @return entero               Código interno o identificador de la orden en la base de datos (NULL si hubo error)
     *
     */
    public function adicionar($datos) {
        global $sql, $sesion_usuarioSesion;

        $datosOrden = array(
            'id_proveedor'      => $datos['id_proveedor'],
            'fecha_orden'       => date("Y-m-d H:i:s"),
            'id_usuario'        => $sesion_usuarioSesion->id,
            'id_caja'           => $datos['id_caja'],
            'subtotal'          => $datos['subtotal'],
            'iva'               => $datos['iva'],
            'concepto1'         => $datos['concepto1'],
            'descuento1'        => $datos['descuento1'],
            'concepto2'         => $datos['concepto2'],
            'descuento2'        => $datos['descuento2'],
            'valor_flete'       => $datos['valor_flete'],
            'total'             => $datos['total'],
            'observaciones'     => $datos['observaciones']
        );
        
        $datosOrden['activo'] = '1';
        
        $sql->iniciarTransaccion();

        $consulta = $sql->insertar('ordenes_compra', $datosOrden);
        $idItem = $sql->ultimoId;

        if ($consulta) {
            //recibir toda la cadena y hacer el insertar en una sola consulta de la lista de articulos            
            $arreglo = explode('|', substr($datos['cadenaArticulosPrecios'], 0, -1));

            foreach ($arreglo as $id => $valor) {
                $articulo = explode(';', $valor);
                $valoresConsulta .= '("' . $idItem . '", "' . $articulo[0] . '", "' . $articulo[1] . '", "' . $articulo[2] . '", "' . $articulo[3] . '", "' . $articulo[4] . '", "' . $datos['id_proveedor'] . '", "'.$articulo[5].'" ),';
            }

            $valoresConsulta = substr($valoresConsulta, 0, -1);

            $sentencia = "INSERT INTO fom_articulos_orden_compra (id_orden, id_articulo, cantidad, descuento, precio, id_bodega, id_proveedor, precio_venta) VALUES $valoresConsulta";

            $insertarListaArticulos = $sql->ejecutar($sentencia);

            if ($insertarListaArticulos) {
                $idOrden        = 'OC' . (int) $sesion_usuarioSesion->sede->id . '-' . $idItem;
                $datosModificar = array('id_orden' => $idOrden);
                
                $query = $sql->modificar('ordenes_compra', $datosModificar, 'id = "' . $idItem . '"');  
                
                if (!$query) {
                    $sql->cancelarTransaccion();
                    return false;
                }
                
                
            } else {
                $sql->cancelarTransaccion();
                $idItem = false;                

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
     * Modificar unarticulo
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

        $datosOrden = array(
            'id_proveedor'      => $datos['id_proveedor'],
            'fecha_orden'       => date("Y-m-d H:i:s"),
            'id_usuario'        => $sesion_usuarioSesion->id,
            'id_caja'           => $datos['id_caja'],
            'subtotal'          => $datos['subtotal'],
            'iva'               => $datos['iva'],
            'concepto1'         => $datos['concepto1'],
            'descuento1'        => $datos['descuento1'],
            'concepto2'         => $datos['concepto2'],
            'descuento2'        => $datos['descuento2'],
            'valor_flete'       => $datos['valor_flete'],
            'total'             => $datos['total'],
            'observaciones'     => $datos['observaciones']
        );
        
        $datosOrden['activo'] = (isset($datos['activo'])) ? '1' : '0';  
        
        $sql->iniciarTransaccion();

        $consulta = $sql->modificar('ordenes_compra', $datosOrden, 'id = "' . $this->id . '"');

        if ($consulta) {

            $eliminarArticulosOrden = $sql->eliminar('articulos_orden_compra', 'id_orden = "' . $this->id . '"');

            if ($eliminarArticulosOrden) {
                //recibir toda la cadena y hacer el insertar en una sola consulta de la lista de articulos            
                $arreglo = explode('|', substr($datos['cadenaArticulosPrecios'], 0, -1));

                $valoresConsulta = '';
                
                foreach ($arreglo as $id => $valor) {
                    $articulo = explode(';', $valor);
                    $valoresConsulta .= '("' . $this->id . '", "' . $articulo[0] . '", "' . $articulo[1] . '", "' . $articulo[2] . '", "' . $articulo[3] . '", "' . $articulo[4] . '", "' . $datos['id_proveedor'] . '", "'.$articulo[5].'" ),';
                }

                $valoresConsulta = substr($valoresConsulta, 0, -1);


                $sentencia = "INSERT INTO fom_articulos_orden_compra (id_orden, id_articulo, cantidad, descuento, precio, id_bodega, id_proveedor, precio_venta) VALUES $valoresConsulta";

                $insertarListaArticulos = $sql->ejecutar($sentencia);
                
                if( !$insertarListaArticulos ){
                    $sql->cancelarTransaccion();
                    $this->id = false;
                    
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
     * Eliminar una orden de compra
     *
     * @param entero $id    Código interno o identificador de una unidad en la base de datos
     * @return lógico       Indica si el procedimiento se pudo realizar correctamente o no
     *
     */
    public function eliminar() {
        global $sql;

        if (!isset($this->id)) {
            return false;
        }
        
        $sql->iniciarTransaccion();
        
        $query = $sql->eliminar('ordenes_compra', 'id = "' . $this->id . '"');

        if (!$query) {
            $sql->cancelarTransaccion();
            return false;
            
        } else {
            $query = $sql->eliminar('articulos_orden_compra', 'id_orden = "' . $this->id . '"');
            
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
     * Listar las ordenes de compra
     *
     * @param entero  $cantidad    Número de articulos a incluir en la lista (0 = todas las entradas)
     * @param arreglo $excepcion   Arreglo con los códigos internos o identificadores a omitir en la lista
     * @param cadena  $condicion   Condición adicional (SQL)
     * @return arreglo             Lista de articulos
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
            $condicion .= 'oc.id NOT IN (' . $excepcion . ')  AND ';
        }

        /*         * * Definir el orden de presentación de los datos ** */
        if (!isset($orden)) {
            $orden = 'fecha_orden';
        }
        if ($this->listaAscendente) {
            $orden = $orden . ' ASC';
            
        } else {
            $orden = $orden . ' DESC';
            
        }

        $tablas = array(
            'oc'    => 'ordenes_compra',
            's'     => 'sedes_empresa',
            'c'     => 'cajas',
            'p'     => 'proveedores',
            'u'     => 'usuarios'
        );

        $columnas = array(
            'id'                => 'oc.id',
            'idProveedor'       => 'oc.id_proveedor',
            'proveedor'         => 'p.nombre',
            'fechaOrden'        => 'oc.fecha_orden',
            'idCaja'            => 'oc.id_caja',
            'sede'              => 's.nombre',
            'usuario'           => 'u.usuario',
            'activo'            => 'oc.activo'
        );

        if (!empty($condicionGlobal)) {
            $condicion .= $condicionGlobal .' AND ';
        }

        $condicion .= 'oc.id_caja = c.id AND c.id_sede = s.id AND oc.id_usuario = u.id AND oc.id_proveedor = p.id';

        if (is_null($this->registrosConsulta)) {
            $sql->seleccionar($tablas, $columnas, $condicion);
            $this->registrosConsulta = $sql->filasDevueltas;
            
        }

        $consulta = $sql->seleccionar($tablas, $columnas, $condicion, 'oc.id', $orden, $inicio, $cantidad);

        if ($sql->filasDevueltas) {
            $lista = array();

            while ($objeto = $sql->filaEnObjeto($consulta)) {                
                $objeto->estado =  ($objeto->activo) ? HTML::frase($textos->id("ACTIVO"), "activo") : HTML::frase($textos->id("INACTIVO"), "inactivo");                 
                
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
        global $textos;
        //Declaracion de las columnas que se van a mostrar en la tabla
        $datosTabla = array(
            HTML::parrafo($textos->id('NUMERO_ORDEN'), 'centrado')      => 'id|oc.id',
            HTML::parrafo($textos->id('SEDE'), 'centrado')              => 'sede|s.nombre',
            HTML::parrafo($textos->id('PROVEEDOR'), 'centrado')         => 'proveedor|p.nombre',
            HTML::parrafo($textos->id('USUARIO_CREADOR'), 'centrado')   => 'usuario|u.usuario',
            HTML::parrafo($textos->id('FECHA_ORDEN'), 'centrado')       => 'fechaOrden|oc.fecha_orden',
            HTML::parrafo($textos->id('ESTADO'), 'centrado')            => 'estado'
        );
        //ruta del metodo paginador
        $rutaPaginador = '/ajax' . $this->urlBase . '/move';

        return Recursos::generarTablaRegistros($arregloRegistros, $datosTabla, $rutaPaginador, $datosPaginacion) . HTML::crearMenuBotonDerecho('ORDENES_COMPRA');
    }

}
