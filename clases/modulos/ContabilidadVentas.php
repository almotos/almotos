<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ContabilidadVentas
 *
 * @author pablo
 */
class ContabilidadVentas extends Contabilidad
{
    public $idFacturaVenta;
    
    public function __construct()
    {
        
    }
    
    /**
     * Metodo encargado de generar los asientos contables para cada una de las cuentas afectadas durante la venta
     * 
     * @global array $configuracion arreglo global con la iformacion de configuracion del sistema
     * @param int $idItem identificador unico de la factura
     * @param array $camposPago arreglo que contiene la informacion sobre los campos de pago, ejemplo
     *                          efectivo => 100, credito => 50, cheque => 30, tarjeta => 20.
     * @return boolean
     */
    public function contabilizarFacturaVenta($idItem, $camposMediosPago = array()) 
    {
        $this->ventaNormal($idItem, $camposMediosPago);
        
//        
//        $regimenEmpresa     = (string)$this->empresa->regimen;
//        $regimenProveedor   = (string)$factura->proveedor->regimen;
//        
//        //si regimen simplificado le venta a regimen simplificado
//        if ($regimenEmpresa == $regimenProveedor && $regimenProveedor == "1") {
//            
//        }    
//        
//        //si regimen simplificado le venta a regimen comun
//        if ($regimenEmpresa == "1" && $regimenProveedor == "2") {
//            
//        }            
//        
//        //si regimen comun le venta a regimen comun
//        if ($regimenEmpresa == $regimenProveedor && $regimenProveedor == "2") {
//            
//        }
//        
//        //si regimen comun le venta a regimen simplificado
//        if ($regimenEmpresa == "1" && $regimenProveedor == "2") {
//            $this->ventaRegimenComunARegimenSimplificado($idItem, $camposMediosPago);
//        }           
    }
    
    
    
    public function ventaNormal($idItem, $camposMediosPago = array())
    {
        global $configuracion;
        //objetos necesarios
        $asientoContable    = new AsientoContable();        
        $factura            = new FacturaVenta($idItem);
        //valores de variables
        $comprobante        = '2';//tipo de comprobante 2 = factura de venta
        $concepto           = 'Venta de mercancia';

        //crear el arreglo con las retenciones a partir de la cadena guardada en la BD
        //del tipo idRetencion;valor|idRetencion;valor|
        $arregloRetenciones = array();
        $totalRetenciones   = 0;
        
        if (!empty($factura->retenciones)) {
            //el ultimo pipe es retirado del string y se crea un arreglo dividiendo la cadena por pipe
            $arreglo = explode('|', substr($factura->retenciones, 0, -1));

            //recorrer el arreglo para generar los valores
            foreach ($arreglo as $id => $valor) {
                $retencion              = explode(';', $valor);
                $arregloRetenciones[]   = array("id" => $retencion[0], "valor" => $retencion[1]);
                //aqui hay que tener cuidado con el iva teorico
                $totalRetenciones += $retencion[1];

            }
        }
        
        //armar el arreglo de arreglos para guardar los asientos contables
        $asientosContables = array();
        
        //afectar cuenta "mercancias no fabricadas por la empresa"
        $datosTotal = array();
        $datosTotal['id_cuenta']         = '413501';
        $datosTotal['comprobante']       = $comprobante;
        $datosTotal['num_comprobante']   = $factura->id;
        $datosTotal['fecha']             = $factura->fechaFactura;
        $datosTotal['concepto']          = $concepto;
        $datosTotal['credito']           = ($factura-> iva) ? ($factura->total - $factura->iva) : $factura->total;
        $datosTotal['debito']            = '';
        $asientosContables[]             = $datosTotal;        
        
        //afectar cuentas contables dependiendo de los medios de pago
        if ($camposMediosPago["efectivo"] != "") {
            //afectar caja general
            $datosTotal = array();
            $datosTotal['id_cuenta']         = '110505';
            $datosTotal['comprobante']       = $comprobante;
            $datosTotal['num_comprobante']   = $factura->id;
            $datosTotal['fecha']             = $factura->fechaFactura;
            $datosTotal['concepto']          = $concepto;
            $datosTotal['credito']           = '';
            $datosTotal['debito']            = $camposMediosPago["efectivo"];
            $asientosContables[]             = $datosTotal;
        }
        if ($camposMediosPago["tarjeta"] != "") {
            //afectar cuentas bancos "moneda nacional"
            $datosTotal = array();
            $datosTotal['id_cuenta']         = '111005';
            $datosTotal['comprobante']       = $comprobante;
            $datosTotal['num_comprobante']   = $factura->id;
            $datosTotal['fecha']             = $factura->fechaFactura;
            $datosTotal['concepto']          = $concepto;
            $datosTotal['credito']           = '';
            $datosTotal['debito']            = $camposMediosPago["tarjeta"];
            $asientosContables[]             = $datosTotal;
        }
        if ($camposMediosPago["cheque"] != "") {
            //afectar cuentas bancos "moneda nacional"
            $datosTotal = array();
            $datosTotal['id_cuenta']         = '111005';
            $datosTotal['comprobante']       = $comprobante;
            $datosTotal['num_comprobante']   = $factura->id;
            $datosTotal['fecha']             = $factura->fechaFactura;
            $datosTotal['concepto']          = $concepto;
            $datosTotal['credito']           = '';
            $datosTotal['debito']            = $camposMediosPago["cheque"];
            $asientosContables[]             = $datosTotal;
        }
        if ($camposMediosPago["credito"] != "") {
            //afectar cuentas por pagar "clientes nacionales"
            $datosTotal = array();
            $datosTotal['id_cuenta']         = '130505';
            $datosTotal['comprobante']       = $comprobante;
            $datosTotal['num_comprobante']   = $factura->id;
            $datosTotal['fecha']             = $factura->fechaFactura;
            $datosTotal['concepto']          = $concepto;
            $datosTotal['credito']           = '';
            $datosTotal['debito']            = $camposMediosPago["credito"];
            $asientosContables[]             = $datosTotal;
        }                      
        
        //iva
        if ($factura->iva > 0) {
            $datosIva = array();
            $datosIva['id_cuenta']         = '240805';
            $datosIva['comprobante']       = $comprobante;
            $datosIva['num_comprobante']   = $factura->id;
            $datosIva['fecha']             = $factura->fechaFactura;
            $datosIva['concepto']          = $concepto;
            $datosIva['credito']           = $factura->iva;
            $datosIva['debito']            = '';    
            $asientosContables[]           = $datosIva;       
        }
        
        /**
         * Contabilidad del costo de venta
         */
        $datosTotal = array();
        $datosTotal['id_cuenta']         = '613501';
        $datosTotal['comprobante']       = $comprobante;
        $datosTotal['num_comprobante']   = $factura->id;
        $datosTotal['fecha']             = $factura->fechaFactura;
        $datosTotal['concepto']          = $concepto;
        $datosTotal['credito']           = '';
        $datosTotal['debito']            = $factura->getCostoDeVenta();
        $asientosContables[]             = $datosTotal;  
        
        $datosTotal = array();
        $datosTotal['id_cuenta']         = '140501';
        $datosTotal['comprobante']       = $comprobante;
        $datosTotal['num_comprobante']   = $factura->id;
        $datosTotal['fecha']             = $factura->fechaFactura;
        $datosTotal['concepto']          = $concepto;
        $datosTotal['credito']           = '';
        $datosTotal['debito']            = $factura->getCostoDeVenta();
        $asientosContables[]             = $datosTotal;        
        
        
        /**
         * generar los registros contables para cada una de las retenciones
         */
        foreach ($arregloRetenciones as $value) {            
            //si el impuesto no es el iva teorico
            if ($value["id"] != "5") {
                $retencion = $configuracion["RETENCIONES"]["VENTAS"][$configuracion["GENERAL"]["idioma"]][$value["id"]];
                //aqui llega solo los impuestos que se retuvieron segun los regimenes del proveedor y vendedor
                $datosRetencion = array();
                $datosRetencion['id_cuenta']         = $retencion["id_cuenta"];
                $datosRetencion['comprobante']       = $comprobante;
                $datosRetencion['num_comprobante']   = $factura->id;
                $datosRetencion['fecha']             = $factura->fechaFactura;
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
    
    
}

