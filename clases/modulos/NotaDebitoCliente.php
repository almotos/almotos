<?php

/**
 * @package     FOM
 * @subpackage  Notas débito de clientes
 * @author      Pablo Andrés Vélez Vidal <pavelez8@misena.edu.co>
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2012 Genesys Corporation.
 * @version     0.2
 * 
 * Notas debito que son generadas para el modulo
 * venta de mercancia.
 * 
 * Modulo : ventas.
 * tablas: notas_debito_cliente y articulos_modificados_ndc
 * integridad referencial: 
 *
 * */
class NotaDebitoCliente {

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

        if (isset($id) && $sql->existeItem('notas_debito_clientes', 'id', intval($id))) {

            $tablas = array(
                'ndc' => 'notas_debito_clientes'
            );

            $columnas = array(
                'id'                        => 'ndc.id',
                'idFactura'                 => 'ndc.id_factura',
                'montoNota'                 => 'ndc.monto_nota',
                'ivaNota'                   => 'ndc.iva_nota',
                'conceptoNota'              => 'ndc.concepto_nota',
                'fechaNota'                 => 'ndc.fecha_nota',
                'inventarioModificado'      => 'ndc.inventario_modificado',
                'totalNota'                 => 'SUM(ndc.monto_nota + ndc.iva_nota)',
            );

            $condicion = 'ndp.id = "' . $id . '"';


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
                        'af' => 'articulos_modificados_ndc'
                    );

                    $columnas1 = array(
                        'id'                => 'af.id',
                        'idNotaDebito'      => 'af.id_nota_debito_cliente',
                        'idArticulo'        => 'af.id_articulo',
                        'cantidadAnterior'  => 'af.cantidad_anterior',
                        'cantidadNueva'     => 'af.cantidad_nueva',
                    );

                    $condicion1 = 'af.id_nota_debito_cliente = "' . $id . '"';

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
     * Metodo encargado de agregar a la factura de este objeto una nota de debito expedida por el cliente
     * para ser contabilizada. recibe parametro con datos como el total, el iva y el concepto. 
     * 
     * @param type $datos
     * @param type $nuevas_cantidades 
     */
    public function adicionar($datos){
        global $sql;

        //almaceno en nuevas variables los datos que serán eliminados del arreglo
        $nuevasCantidades       = $datos['nueva_cantidad'];
        
        if($datos['inventario_modificado']){
            $datos['inventario_modificado'] = '1';
        }
        
        //elimino los datos del arreglo para que concuerde con los datos en la BD
        unset($datos['nueva_cantidad']);
        unset($datos['dialogo']);
    
        //inserto los datos en la tabla
        $sql->iniciarTransaccion();
        
        $insertarNota  = $sql->insertar('notas_debito_clientes', $datos);
        $idNotaDebito = $sql->ultimoId;
        
        if(!$insertarNota){//si falla la insercion de los datos en la tabla            
            $sql->cancelarTransaccion();
            return false;
            
        }
        
        $inventario = new Inventario();
        
        if($datos['inventario_modificado']){ //si se marcó la opción de modificar de las cantidades del inventario
            
            foreach($nuevasCantidades as $key => $value){
                
                $arr_1 = explode('_', $key);
                $cantidadActual     = $arr_1[0];
                $idArticulo         = $arr_1[1];
                $idBodega           = $arr_1[2];
                $idArticuloFactura  = $arr_1[3];
                $nuevaCantidad      = $value;
                
                /**
                 * verificar cambios en cantidades para asi mismo modificar el inventario
                 * solo se modificarian datos en una nota debito cuando la nueva cantidad ingresada 
                 * sea menor a la cantidad existente en la factura, ya que una nota debito se puede 
                 * generar solo por exceso en la facturacion de parte del proveedor al cliente
                 */
                $queryInv = FALSE;
                
                 if ($nuevaCantidad < $cantidadActual){
                    $cantidadAModificar = $cantidadActual - $nuevaCantidad;
                    $queryInv = $inventario->adicionar($idArticulo, $cantidadAModificar, $idBodega);
                    
                } else {
                    $cantidadAModificar = $nuevaCantidad - $cantidadActual;                    
                    $queryInv = $inventario->descontar($idArticulo, $cantidadAModificar, $idBodega);
                }
                
                if($queryInv){
                    $datos_amndp = array(
                                        "id_nota_debito_cliente"     => $idNotaDebito,
                                        "id_articulo_factura_venta" => $idArticuloFactura,
                                        "id_articulo"                => $idArticulo,
                                        "cantidad_anterior"          => $cantidadActual,
                                        "cantidad_nueva"             => $nuevaCantidad
                                        );

                    $query = $sql->insertar("articulos_modificados_ndc", $datos_amndp);

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
        
        $contabilizarNDC = $contabilidadVentas->contabilizarNDC($idNotaDebito);
        
        if (!$contabilizarNDC) {
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
        global $sql;

        if (!isset($this->id)) {
            return false;
        }
        
        $sql->iniciarTransaccion();
        
        $consulta = $sql->eliminar('notas_debito_clientes', 'id = "' . $this->id . '"');

        if ($consulta) {            
            $consulta = $sql->eliminar('articulos_modificados_ndc', 'id_nota_debito_cliente = "' . $this->id . '"');
            
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
                'ndp'   => 'notas_debito_clientes'
            );

            $columnas = array(
                'id'                        => 'ndp.id', 
                'idFactura'                 => 'ndp.id_factura',                
                'conceptoNota'              => 'ndp.concepto_nota',
                'montoNota'                 => 'ndp.monto_nota',
                'ivaNota'                   => 'ndp.iva_nota',
                'fechaNota'                 => 'ndp.fecha_nota',
            );

            $condicion = 'ndp.id_factura = "' . $idFactura . '"';

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
     * Verifica si un articulo fue modificado en una nota debito previa y devuelve la cantidad
     * real del articulo despues de aplicar dicha nota.
     * 
     * @global type $sql
     * @param type $idArticuloFactura = id del registro en la tabla articulos_factura_venta
     * @return int|boolean devuelve la cantidad actual del articulo o FALSE si no hay una nota debito previa
     */
    public static function verificarNotaPrevia($idArticuloFactura) {
        global $sql;
        
        $tabla          = "articulos_modificados_ndc";
        $columna        = "cantidad_nueva";
        $condicion      = "id_articulo_factura_venta = '".$idArticuloFactura."'";
        $orden          = "id DESC";
        
        $consulta = $sql->seleccionar($tabla, $columna , $condicion, "", $orden, 0, 1);
        
        if ($sql->filasDevueltas == 1) {
            $datos = $sql->filaEnObjeto($consulta);
            $valor = $datos->$columna;
            return $valor;
            
        } else {
            return FALSE;
            
        }        
     
    } 
    
}  
