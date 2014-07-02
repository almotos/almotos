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
     * enlace al archivo digital que representa la nota
     * @var string ruta absoluta al archivo digital de la nota
     */
    public $rutaNotaDigital;    
    
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


            $sql->depurar = true;
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
        global $sql, $archivo_nota_digital;


        //almaceno en nuevas variables los datos que serán eliminados del arreglo
        $nuevasCantidades       = $datos['nueva_cantidad'];
        $datos['archivo']       = '';
        
        if($datos['inventario_modificado']){
            $datos['inventario_modificado'] = '1';
        }
        
        //elimino los datos del arreglo para que concuerde con los datos en la BD
        unset($datos['nueva_cantidad']);
        unset($datos['dialogo']);

        //verifico si se ha cargado un soporte digital
        if(isset($archivo_nota_digital) && !empty($archivo_nota_digital['tmp_name'])){//de ser cierto, lo guardo en el servidor
            $archivo_digital = $this->cargarNotaDigital($archivo_nota_digital, 'nota_credito_cliente');            
            $datos['archivo'] = $archivo_digital;//y agrego el campo para la insercion a la BD
            
        }      
        //inserto los datos en la tabla
        $sql->iniciarTransaccion();
        
        $insertarNota  = $sql->insertar('notas_credito_clientes', $datos);
        $idNotaCredito = $sql->ultimoId;
        
        if(!$insertarNota){//si falla la insercion de los datos en la tabla
            if($archivo_digital) {
                $this->eliminarNotaDigital('nota_credito_cliente', $archivo_digital);//elimino la nota del servidor
            }
            
            $sql->cancelarTransaccion();
            return false;
            
        }
        
        $inventario = new Inventario();
        
        if($datos['inventario_modificado']){ //si se marcó la opción de modificar de las cantidades del inventario
            
            foreach($nuevasCantidades as $key => $value){
                
                $arr_1 = explode('_', $key);
                //$idArticuloFactura  = $arr_1[0];//identificador del registro en la tabla articulo factura venta
                $cantidadActual     = $arr_1[0];
                $idArticulo         = $arr_1[1];
                $idBodega           = $arr_1[2];
                $nuevaCantidad      = $value;
                

                /**
                 * verificar cambios en cantidades para asi mismo modificar el inventario
                 * solo se modificarian datos en una nota credito cuando la nueva cantidad ingresada 
                 * sea menor a la cantidad existente en la factura, ya que una nota credito se puede 
                 * generar solo por exceso en la facturacion de parte del cliente al cliente
                 */
                 if ($nuevaCantidad < $cantidadActual){
                     
                    $descontar = $inventario->descontar($idArticulo, $nuevaCantidad, $idBodega);
                    
                    if($descontar){
   
                        $datos_amncc = array(
                                            "id_nota_credito_cliente" => $idNotaCredito,
                                            "id_articulo"               => $idArticulo,
                                            "cantidad_anterior"         => $cantidadActual,
                                            "cantidad_nueva"            => $nuevaCantidad
                                            );
                        
                        $query = $sql->insertar("articulos_modificados_ncc", $datos_amncc);
                        
                        if(!$query){
                            $sql->cancelarTransaccion();
                            return false;
                        }
                        
                    } else {
                        $sql->cancelarTransaccion();
                        return false;
                        
                    }
                    
                } else {
                    $aumentar = $inventario->adicionar($idArticulo, $nuevaCantidad, $idBodega);
                    
                    if($aumentar){
                           
                        $datos_amncc = array(
                                            "id_nota_credito_cliente" => $idNotaCredito,
                                            "id_articulo"               => $idArticulo,
                                            "cantidad_anterior"         => $cantidadActual,
                                            "cantidad_nueva"            => $nuevaCantidad
                                            );
                        
                        $query = $sql->insertar("articulos_modificados_ncc", $datos_amncc);
                        
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
            $consulta = $sql->eliminar('articulos_modificados_ncc', 'id_factura = "' . $this->id . '"');
            
            if ($this->facturaDigital) {
                $configuracionRuta = $configuracion['RUTAS']['media'] . '/' . $configuracion['RUTAS']['archivos'] . '/facturas_venta/' . $this->id;
                Archivo::eliminarArchivoDelServidor(array($configuracionRuta));
                
            }
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
     * Metodo que se encarga de guardar el archivo digital de la nota enviada por el cliente
     *
     * @global type $sql
     * @global type $configuracion
     * @param type $archivo = archivo digital
     * @param type $tipo = tipo de la nota, crédito o débito
     * @return boolean 
     */
    public function cargarNotaDigital($archivo, $tipo) {
        global $configuracion;

        $validarFormato = Archivo::validarArchivo($archivo, $configuracion['VALIDACIONES']['notas_credito']);

        if (!$validarFormato) {
            $configuracionRuta = $configuracion["RUTAS"]["media"] . "/" . $configuracion["RUTAS"]["archivos"] . '/'.$tipo.'/';
            $recurso = Archivo::subirArchivoAlServidor($archivo, $configuracionRuta);
            
            if ($recurso) {
                return $recurso;
                
            } else {
                return false;
                
            }
            
        } else {
            return false;
            
        }
        
    }

    /**
     * Metodo que se encarga de eliminar la nota digital
     * 
     * @global objeto $sql objeto global de interacción con la BD
     * @global array $configuracion arreglo global donde se almacenan los parametros de configuración
     * @return boolean true or false dependiendo del exito de la operación
     */
    public function eliminarNotaDigital() {
        global $sql;

        $recurso = Archivo::eliminarArchivoDelServidor(array($this->rutaNotaDigital));
        
        if ($recurso) {
            $datosFactura = array('archivo' => '');
            $consulta = $sql->modificar('notas_credito_clientes', $datosFactura, 'id = "' . $this->id . '"');
            
            if ($consulta) {
                return true;
                
            } else {//espera tres sec y vuelve y lo intenta
                sleep(3);
                $sql->modificar('notas_credito_clientes', $datosFactura, 'id = "' . $this->id . '"');
                return true;
                
            }
            
        } else {
            return false;
            
        }
        
    }   

    
}  
