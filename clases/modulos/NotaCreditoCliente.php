<?php

/**
 * @package     FOM
 * @subpackage  Notas crédito de clientes
 * @author      Pablo Andrés Vélez Vidal <pavelez8@misena.edu.co>
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2012 Genesys Corporation.
 * @version     0.2
 * 
 * Notas credito que son generadas para el modulo
 * venta de mercancia.
 * 
 * Modulo : ventas.
 * tablas: notas_credito_cliente y articulos_modificados_ncc
 * integridad referencial: 
 *
 * */
class NotaCreditoCliente {

    /**
     * Código interno o identificador del registro
     * @var entero
     */
    public $id;

    /**
     * Código de la factura a la que va relacionada esta nota
     * @var entero
     */
    public $idFactura;
    
    /**
     * Representacion del objeto factura
     * @var objeto Representacion del objeto factura (@see Facturaventa)
     */
    public $factura;    

    /**
     * Código interno del modulo usuario
     * @var entero
     */
    public $idModulo;

    /**
     * Monto de la nota
     * @var objeto
     */
    public $montoNota;

    /**
     * iva de la nota
     * @var entero
     */
    public $ivaNota;
    
    /**
     * total de la nota
     * @var entero
     */
    public $totalNota;    

    /**
     * fecha en que se realia  la nota
     * @var cadena
     */
    public $fechaNota;

    /**
     * observaciones realizadas a la nota
     * @var entero
     */
    public $conceptoNota;  
    
    /**
     * determina si se modificaron las cantidades de los articulos en la factura de venta
     * @var boleano 
     */
    public $inventarioModificado;    

    /**
     * listado de articulos que fueron modificados
     * @var array
     */
    public $listaArticulos;

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
        global $modulo;
        
        $this->idModulo = $modulo->id;


        if (isset($id)) {
            $this->cargar($id);
        }
    }

    /**
     * Cargar los datos de una nota
     * 
     * @param entero $id Código interno o identificador de la nota en la base de datos
     */
    public function cargar($id) {
        global $sql;

        if (isset($id) && $sql->existeItem('notas_credito_clientes', 'id', intval($id))) {

            $tablas = array(
                'ncc' => 'notas_credito_clientes'
            );

            $columnas = array(
                'id'                        => 'ncc.id',
                'idFactura'                 => 'ncc.id_factura',
                'montoNota'                 => 'ncc.monto_nota',
                'ivaNota'                   => 'ncc.iva_nota',
                'conceptoNota'              => 'ncc.concepto_nota',
                'fechaNota'                 => 'ncc.fecha_nota',
                'inventarioModificado'      => 'ncc.inventario_modificado',
                'totalNota'                 => 'SUM(ncc.monto_nota + ncc.iva_nota)',
            );

            $condicion = 'ncc.id = "' . $id . '"';

            $consulta = $sql->seleccionar($tablas, $columnas, $condicion);

            if ($sql->filasDevueltas) {
                $fila = $sql->filaEnObjeto($consulta);

                foreach ($fila as $propiedad => $valor) {
                    $this->$propiedad = $valor;
                }

                if($this->inventarioModificado){
                    /**
                    * Tablas y columnas para cargar los archivos relacionados a una factura 
                    */
                    $tablas1 = array(
                        'af' => 'articulos_modificados_ncc'
                    );

                    $columnas1 = array(
                        'id'                => 'af.id',
                        'idNotaCredito'     => 'af.id_nota_credito_cliente',
                        'idArticulo'        => 'af.id_articulo',
                        'cantidadAnterior'  => 'af.cantidad_anterior',
                        'cantidadNueva'     => 'af.cantidad_nueva',
                    );

                    $condicion1 = 'af.id_nota_credito_cliente = "' . $id . '"';

                    $consulta1 = $sql->seleccionar($tablas1, $columnas1, $condicion1);

                    /**
                    * una vez se consulta se guarda la información en el arreglo listaArticulos, que contendra un objeto stdClass en cada una de sus posiciones
                    */
                    if ($sql->filasDevueltas) {
                        while ($objeto = $sql->filaEnObjeto($consulta1)) {
                            $objeto->articulo = new Articulo($objeto->idArticulo);
                            $this->listaArticulos[] = $objeto;
                            
                        }
                        
                    }
                    
                }
                
            }
            
        }
        
    }

    /**
     * Metodo encargado de agregar a la factura de este objeto una nota de credito expedida por el cliente
     * para ser contabilizada. recibe parametro con datos como el total, el iva y el concepto. También permite
     * cargarle la copia de la nota digital, en caso de que el cliente envie la nota digital, o si es fisica,
     * sea escaneada.
     * 
     * @param type $datos
     * @param type $nuevas_cantidades 
     */
    public function adicionar($datos){
        global $sql;
        
        //almaceno en nuevas variables los datos que serán eliminados del arreglo
        $nuevasCantidades       = $datos['nueva_cantidad'];
        $datos['archivo']       = '';
        
        if($datos['inventario_modificado']){
            $datos['inventario_modificado'] = '1';
        }
        
        //elimino los datos del arreglo para que concuerde con los datos en la BD
        unset($datos['nueva_cantidad']);
        unset($datos['dialogo']);
    
        //inserto los datos en la tabla
        $sql->iniciarTransaccion();
        
        $insertarNota  = $sql->insertar('notas_credito_clientes', $datos);
        $idNotaCredito = $sql->ultimoId;
        
        if(!$insertarNota){//si falla la insercion de los datos en la tabla
            $sql->cancelarTransaccion();
            return false;
            
        }
        
        if($datos['inventario_modificado']){ //si se marcó la opción de modificar de las cantidades del inventario
            $inventario = new Inventario();
            
            foreach($nuevasCantidades as $key => $value){
                
                $arr_1 = explode('_', $key);
                $cantidadActual     = $arr_1[0];
                $idArticulo         = $arr_1[1];
                $idBodega           = $arr_1[2];
                $idArticuloFactura  = $arr_1[3];
                $nuevaCantidad      = $value;
                
                if ($cantidadActual == $nuevaCantidad) {
                    continue;
                }
                
                $queryInv = FALSE;
                
                 if ($nuevaCantidad < $cantidadActual){
                     $cantidadAModificar = $cantidadActual - $nuevaCantidad;
                     $queryInv = $inventario->adicionar($idArticulo, $cantidadAModificar, $idBodega);

                } else {
                    //Revisar si en una nota credito hay la posibilidad que la cantidad del articulo sea mayor a la existente
                    $cantidadAModificar = $nuevaCantidad - $cantidadActual;
                    $queryInv = $inventario->descontar($idArticulo, $cantidadAModificar, $idBodega);    
                    
                }
                
                if($queryInv){
                    $datos_amncp = array(
                                        "id_nota_credito_cliente"    => $idNotaCredito,
                                        "id_articulo_factura_venta"  => $idArticuloFactura,
                                        "id_articulo"                => $idArticulo,
                                        "cantidad_anterior"          => $cantidadActual,
                                        "cantidad_nueva"             => $nuevaCantidad,
                                        "fecha"                      => date("Y-m-d H:i:s"),
                                        );

                    $query = $sql->insertar("articulos_modificados_ncc", $datos_amncp);

                    if(!$query){
                        $sql->cancelarTransaccion();
                        return false;
                    }

                } else {
                    $sql->cancelarTransaccion();
                    return false;

                }              
                
            }
        }
        
        $contabilidadVentas = new ContabilidadVentas();
        
        $contabilizarNCC = $contabilidadVentas->contabilizarNCC($idNotaCredito);
        
        if (!$contabilizarNCC) {
            $sql->cancelarTransaccion();
            return false;
        }
        
        $sql->finalizarTransaccion();
        return true;
        
    }

    /**
     *
     * Eliminar una nota
     *
     * @param entero $id    Código interno o identificador de una nota en la base de datos
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
        
        $consulta = $sql->eliminar('notas_credito_clientes', 'id = "' . $this->id . '"');

        if ($consulta) {            
            $consulta = $sql->eliminar('articulos_modificados_ncc', 'id_nota_credito_cliente = "' . $this->id . '"');

            $sql->finalizarTransaccion();
            return true;
            
        } else {
            $sql->cancelarTransaccion();
            return false;
            
        }
        
    }
    
    
    /**
     * Cargar los datos de una nota
     * @param entero $id Código interno o identificador de la nota en la base de datos
     */
    public function listar($idFactura) {
        global $sql;
        
        if (isset($idFactura) && $sql->existeItem('facturas_venta', 'id', intval($idFactura))) {
            
            $tablas = array(
                'ncc'   => 'notas_credito_clientes'
            );

            $columnas = array(
                'id'                        => 'ncc.id', 
                'idFactura'                 => 'ncc.id_factura',                
                'conceptoNota'              => 'ncc.concepto_nota',
                'montoNota'                 => 'ncc.monto_nota',
                'ivaNota'                   => 'ncc.iva_nota',
                'fechaNota'                 => 'ncc.fecha_nota',
            );

            $condicion = 'ncc.id_factura = "' . $idFactura . '"';

            $consulta = $sql->seleccionar($tablas, $columnas, $condicion);

            if ($sql->filasDevueltas) {
                
                $listaNotas = array();
                
                while ($objeto = $sql->filaEnObjeto($consulta)) {
                    if (strlen($objeto->conceptoNota) > 45) {
                        $objeto->conceptoNota = substr($objeto->conceptoNota, 0, 44) . '.';
                    }     
                    
                    $objeto->totalNota = $objeto->ivaNota + $objeto->montoNota;
                    $objeto->totalNota = '$ '.Recursos::formatearNumero( $objeto->totalNota, '$');
                    
                    $listaNotas[] = $objeto;
                }
                
                return $listaNotas;
 
            }
            
            
        }
    }
    
    /**
     * Verifica si un articulo fue modificado en una nota credito previa y devuelve la cantidad
     * real del articulo despues de aplicar dicha nota.
     * 
     * @global type $sql
     * @param type $idArticuloFactura = id del registro en la tabla articulos_factura_venta
     * @return int|boolean devuelve la cantidad actual del articulo o FALSE si no hay una nota credito previa
     */
    public static function verificarNotaPrevia($idArticuloFactura) {
        global $sql;

        $datos = array();

        $tabla          = "articulos_modificados_ndc";
        $columna        = array("cantidad_nueva", "fecha");
        $condicion      = "id_articulo_factura_compra = '".$idArticuloFactura."'";
        $orden          = "id DESC";

        $consulta = $sql->seleccionar($tabla, $columna , $condicion, "", $orden, 0, 1);

        if ($sql->filasDevueltas == 1) {
            $datos[] = $sql->filaEnObjeto($consulta);

        }      

        $tabla1          = "articulos_modificados_ncc";
        $columna1        = array("cantidad_nueva", "fecha");
        $condicion1      = "id_articulo_factura_compra = '".$idArticuloFactura."'";
        $orden1          = "id DESC";

        $consulta1 = $sql->seleccionar($tabla1, $columna1 , $condicion1, "", $orden1, 0, 1);    

        if ($sql->filasDevueltas == 1) {
            $datos[] = $sql->filaEnObjeto($consulta1);

        }        
        
        if (empty($datos[0]) && empty($datos[1])) {
            return false;
            
        } else if (!empty($datos[0]) && empty($datos[1])) {
            return $datos[0]->cantidad_nueva;
            
        } else if (empty($datos[0]) && !empty($datos[1])) {
            return $datos[1]->cantidad_nueva;
            
        } else {
            if (strtotime($datos[0]->fecha) > strtotime($datos[1]->fecha)) {
                return $datos[0]->cantidad_nueva;
            } else {
                return $datos[1]->cantidad_nueva;
            }
        }

    }    
    
}  
