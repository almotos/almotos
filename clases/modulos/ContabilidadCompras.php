<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once("Contabilidad.php");
/**
 * Description of ContabilidadCompras
 *
 * @author pablo
 */
class ContabilidadCompras extends Contabilidad
{
    public $idFacturaCompra;
    
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Metodo encargado de generar los asientos contables para cada una de las cuentas afectadas durante la compra
     * 
     * @global array $configuracion arreglo global con la iformacion de configuracion del sistema
     * @param int $idItem identificador unico de la factura
     * @param array $camposPago arreglo que contiene la informacion sobre los campos de pago, ejemplo
     *                          efectivo => 100, credito => 50, cheque => 30, tarjeta => 20.
     * @return boolean
     */
    public function contabilizarFacturaCompra($idItem, $camposMediosPago = array()) 
    {
        $concepto    = 'Compra de mercancia';
        //tipo de comprobante 1 = factura de compra
        $comprobante = '1';
        
        $factura            = new FacturaCompra($idItem);
        $infoFactura        = array(
                                    "id"            => $factura->id,
                                    "retenciones"   => $factura->retenciones,
                                    "iva"           => $factura->iva,
                                    "fechaFactura"  => $factura->fechaFactura,
                                    "total"         => $factura->total
                                    );
        
        $this->generarAsientosContablesFC($camposMediosPago, $concepto, $comprobante, $infoFactura);
                 
    }

    public function generarAsientosContablesFC($camposMediosPago = array(), $concepto = 'Compra de mercancia', $comprobante= '1', $infoFactura = array())
    {
        global $configuracion;
        //objetos necesarios
        $asientoContable    = new AsientoContable();        
        
        //crear el arreglo con las retenciones a partir de la cadena guardada en la BD
        //del tipo idRetencion;valor|idRetencion;valor|
        $arregloRetenciones = array();
        $arreglo            = array();
        //el ultimo pipe es retirado del string y se crea un arreglo dividiendo la cadena por pipe
        if (isset($infoFactura['retenciones'])) {
            $arreglo = explode('|', substr($infoFactura['retenciones'], 0, -1));
        }
        
        $totalRetenciones = 0;
        //recorrer el arreglo para generar los valores
        if (count($arreglo) > 0) {
            foreach ($arreglo as $id => $valor) {
                $retencion              = explode(';', $valor);
                $arregloRetenciones[]   = array("id" => $retencion[0], "valor" => $retencion[1]);
                //aqui hay que tener cuidado con el iva teorico
                $totalRetenciones += $retencion[1];

            }
        }
        
        //armar el arreglo de arreglos para guardar los asientos contables
        $asientosContables = array();
        
        //iva de la compra
        $_iva = (!empty($infoFactura['iva']) && $infoFactura['iva'] > 0) ? $infoFactura['iva'] : "0";
        //afectar cuenta "mercancias no fabricadas por la empresa"
        $datosTotal = array();
        $datosTotal['id_cuenta']         = '143501';
        $datosTotal['comprobante']       = $comprobante;
        $datosTotal['num_comprobante']   = $infoFactura['id'];
        $datosTotal['fecha']             = $infoFactura['fechaFactura'];
        $datosTotal['concepto']          = $concepto;
        $datosTotal['credito']           = '';
        $datosTotal['debito']            = ($infoFactura['total'] - $_iva);
        $asientosContables[]             = $datosTotal;   
                
        //afectar cuentas contables dependiendo de los medios de pago
        if ($camposMediosPago["efectivo"] != "") {
            //afectar caja general
            $datosTotal = array();
            $datosTotal['id_cuenta']         = '110505';
            $datosTotal['comprobante']       = $comprobante;
            $datosTotal['num_comprobante']   = $infoFactura['id'];
            $datosTotal['fecha']             = $infoFactura['fechaFactura'];
            $datosTotal['concepto']          = $concepto;
            $datosTotal['credito']           = $camposMediosPago["efectivo"];
            $datosTotal['debito']            = '';
            $asientosContables[]             = $datosTotal;
        }
        
        if ($camposMediosPago["tarjeta"] != "") {
            //afectar cuentas bancos "moneda nacional"
            $datosTotal = array();
            $datosTotal['id_cuenta']         = '111005';
            $datosTotal['comprobante']       = $comprobante;
            $datosTotal['num_comprobante']   = $infoFactura['id'];
            $datosTotal['fecha']             = $infoFactura['fechaFactura'];
            $datosTotal['concepto']          = $concepto;
            $datosTotal['credito']           = $camposMediosPago["tarjeta"];
            $datosTotal['debito']            = '';
            $asientosContables[]             = $datosTotal;
        }
        if ($camposMediosPago["cheque"] != "") {
            //afectar cuentas bancos "moneda nacional"
            $datosTotal = array();
            $datosTotal['id_cuenta']         = '111005';
            $datosTotal['comprobante']       = $comprobante;
            $datosTotal['num_comprobante']   = $infoFactura['id'];
            $datosTotal['fecha']             = $infoFactura['fechaFactura'];
            $datosTotal['concepto']          = $concepto;
            $datosTotal['credito']           = $camposMediosPago["cheque"];
            $datosTotal['debito']            = '';
            $asientosContables[]             = $datosTotal;
        }
        if ($camposMediosPago["credito"] != "") {
            //afectar cuentas por pagar "proveedores nacionales"
            $datosTotal = array();
            $datosTotal['id_cuenta']         = '220501';
            $datosTotal['comprobante']       = $comprobante;
            $datosTotal['num_comprobante']   = $infoFactura['id'];
            $datosTotal['fecha']             = $infoFactura['fechaFactura'];
            $datosTotal['concepto']          = $concepto;
            $datosTotal['credito']           = $camposMediosPago["credito"];
            $datosTotal['debito']            = '';
            $asientosContables[]             = $datosTotal;
        }                      
        
        //iva
        if (isset($infoFactura['iva']) && $infoFactura['iva'] > 0) {
            $datosIva = array();
            $datosIva['id_cuenta']         = '240805';
            $datosIva['comprobante']       = $comprobante;
            $datosIva['num_comprobante']   = $infoFactura['id'];
            $datosIva['fecha']             = $infoFactura['fechaFactura'];
            $datosIva['concepto']          = $concepto;
            $datosIva['credito']           = '';
            $datosIva['debito']            = $infoFactura['iva'];    
            $asientosContables[]           = $datosIva;       
        }
        
        /**
         * generar los registros contables para cada una de las retenciones
         */
        foreach ($arregloRetenciones as $value) {
            $retencion = $configuracion["RETENCIONES"][$configuracion["GENERAL"]["idioma"]][$value["id"]];
            //aqui llega solo los impuestos que se retuvieron segun los regimenes del proveedor y comprador
            $datosRetencion = array();
            $datosRetencion['id_cuenta']         = $retencion["id_cuenta"];
            $datosRetencion['comprobante']       = $comprobante;
            $datosRetencion['num_comprobante']   = $infoFactura['id'];
            $datosRetencion['fecha']             = $infoFactura['fechaFactura'];
            $datosRetencion['concepto']          = $concepto;
            $datosRetencion['credito']           = $value["valor"];
            $datosRetencion['debito']            = '';    
            $asientosContables[]                 = $datosRetencion;  
            
            //si el impuesto es el iva teorico, se debe afectar tambien el iva descontable
            if ($value["id"] == "5") {
                $datosRetencion = array();
                $datosRetencion['id_cuenta']         = $retencion["id_cuenta2"];
                $datosRetencion['comprobante']       = $comprobante;
                $datosRetencion['num_comprobante']   = $infoFactura['id'];
                $datosRetencion['fecha']             = $infoFactura['fechaFactura'];
                $datosRetencion['concepto']          = $concepto;
                $datosRetencion['credito']           = '';
                $datosRetencion['debito']            = $value["valor"];    
                $asientosContables[]                 = $datosRetencion;  
            }
            
        }        
        
        //agregar cada uno de los asientos contables
        foreach ($asientosContables as $asiento) {
            $asientoContable->adicionar($asiento);
        }
        
        return true;   
    }
    
    /**
     * Organizar los asientos contables para las notas de credito de un proveedor
     * e insertarlos en la base de datos
     * @param type $montoNota
     * @param type $ivaNota
     */
    public function contabilizarNCP($idNota)
    {
        $nota               = new NotaCreditoProveedor($idNota);
        $asientoContable    = new AsientoContable();        
        
        //asientos xontables
        $asientosContables = array();
        //valores de variables
        $comprobante        = '3';//tipo de comprobante 3-> NC Proveedor,
        $concepto           = 'Descuentos o menores valores en precios - compras';
        
//        $contabilidad = new Contabilidad();
//        $retenciones = $contabilidad->generarCamposRetenciones($nota->factura->idProveedor, $nota->montoNota, $nota->ivaNota);

        //afectar cuenta "mercancias no fabricadas por la empresa"
        $datosTotal = array();
        $datosTotal['id_cuenta']         = '143501';
        $datosTotal['comprobante']       = $comprobante;
        $datosTotal['num_comprobante']   = $idNota;
        $datosTotal['fecha']             = date("Y-m-d H:i:s");
        $datosTotal['concepto']          = $concepto;
        $datosTotal['credito']           = $nota->montoNota;
        $datosTotal['debito']            = '';
        $asientosContables[]             = $datosTotal; 
        
        //afectar cuenta "mercancias no fabricadas por la empresa"
        $datosTotal = array();
        $datosTotal['id_cuenta']         = '220501';
        $datosTotal['comprobante']       = $comprobante;
        $datosTotal['num_comprobante']   = $idNota;
        $datosTotal['fecha']             = date("Y-m-d H:i:s");
        $datosTotal['concepto']          = $concepto;
        $datosTotal['credito']           = '';
        $datosTotal['debito']            = $nota->totalNota;
        $asientosContables[]             = $datosTotal;  
        
        //afectar cuenta "mercancias no fabricadas por la empresa"
        $datosTotal = array();
        $datosTotal['id_cuenta']         = '240801';
        $datosTotal['comprobante']       = $comprobante;
        $datosTotal['num_comprobante']   = $idNota;
        $datosTotal['fecha']             = date("Y-m-d H:i:s");
        $datosTotal['concepto']          = $concepto;
        $datosTotal['credito']           = $nota->ivaNota;
        $datosTotal['debito']            = '';
        $asientosContables[]             = $datosTotal;    
        
        //agregar cada uno de los asientos contables
        foreach ($asientosContables as $asiento) {
            $asientoContable->adicionar($asiento);
        }        

        return true;        
        
    }

    /**
     * Organizar los asientos contables para las notas de debito de un proveedor
     * e insertarlos en la base de datos
     * @param type $montoNota
     * @param type $ivaNota
     */
    public function contabilizarNDP($idNota)
    {
        $nota               = new NotaDebitoProveedor($idNota);
        $asientoContable    = new AsientoContable();        
        
        //asientos xontables
        $asientosContables = array();
        //valores de variables
        $comprobante        = '4';//tipo de comprobante 3-> NC Proveedor,
        $concepto           = 'Mayores valores en precios - compras';
        
//        $contabilidad = new Contabilidad();
//        $retenciones = $contabilidad->generarCamposRetenciones($nota->factura->idProveedor, $nota->montoNota, $nota->ivaNota);

        //afectar cuenta "mercancias no fabricadas por la empresa"
        $datosTotal = array();
        $datosTotal['id_cuenta']         = '143501';
        $datosTotal['comprobante']       = $comprobante;
        $datosTotal['num_comprobante']   = $idNota;
        $datosTotal['fecha']             = date("Y-m-d H:i:s");
        $datosTotal['concepto']          = $concepto;
        $datosTotal['credito']           = '';
        $datosTotal['debito']            = $nota->montoNota;
        $asientosContables[]             = $datosTotal; 
        
        //afectar cuenta "mercancias no fabricadas por la empresa"
        $datosTotal = array();
        $datosTotal['id_cuenta']         = '220501';
        $datosTotal['comprobante']       = $comprobante;
        $datosTotal['num_comprobante']   = $idNota;
        $datosTotal['fecha']             = date("Y-m-d H:i:s");
        $datosTotal['concepto']          = $concepto;
        $datosTotal['credito']           = $nota->totalNota;
        $datosTotal['debito']            = '';
        $asientosContables[]             = $datosTotal;  
        
        //afectar cuenta "mercancias no fabricadas por la empresa"
        $datosTotal = array();
        $datosTotal['id_cuenta']         = '240801';
        $datosTotal['comprobante']       = $comprobante;
        $datosTotal['num_comprobante']   = $idNota;
        $datosTotal['fecha']             = date("Y-m-d H:i:s");
        $datosTotal['concepto']          = $concepto;
        $datosTotal['credito']           = '';
        $datosTotal['debito']            = $nota->ivaNota;
        $asientosContables[]             = $datosTotal;    
        
        //agregar cada uno de los asientos contables
        foreach ($asientosContables as $asiento) {
            $asientoContable->adicionar($asiento);
        }        

        return true;        
        
    }    
    
}

