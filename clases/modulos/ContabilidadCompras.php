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
        $this->compraNormal($idItem, $camposMediosPago);
                 
    }
    
    
    
    public function compraNormal($idItem, $camposMediosPago = array())
    {
        global $configuracion;
        //objetos necesarios
        $asientoContable    = new AsientoContable();        
        $factura            = new FacturaCompra($idItem);
        //valores de variables
        $comprobante        = '1';//tipo de comprobante 1 = factura de compra
        $concepto           = 'Compra de mercancia';

        //crear el arreglo con las retenciones a partir de la cadena guardada en la BD
        //del tipo idRetencion;valor|idRetencion;valor|
        $arregloRetenciones = array();
        //el ultimo pipe es retirado del string y se crea un arreglo dividiendo la cadena por pipe
        $arreglo = explode('|', substr($factura->retenciones, 0, -1));
        
        $totalRetenciones = 0;
        //recorrer el arreglo para generar los valores
        foreach ($arreglo as $id => $valor) {
            $retencion              = explode(';', $valor);
            $arregloRetenciones[]   = array("id" => $retencion[0], "valor" => $retencion[1]);
            //aqui hay que tener cuidado con el iva teorico
            $totalRetenciones += $retencion[1];
            
        }
        
        //armar el arreglo de arreglos para guardar los asientos contables
        $asientosContables = array();
        
        //afectar cuenta "mercancias no fabricadas por la empresa"
        $datosTotal = array();
        $datosTotal['id_cuenta']         = '143501';
        $datosTotal['comprobante']       = $comprobante;
        $datosTotal['num_comprobante']   = $factura->id;
        $datosTotal['fecha']             = $factura->fechaFactura;
        $datosTotal['concepto']          = $concepto;
        $datosTotal['credito']           = '';
        $datosTotal['debito']            = $factura->total;
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
            $datosTotal['credito']           = $camposMediosPago["efectivo"];
            $datosTotal['debito']            = '';
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
            $datosTotal['credito']           = $camposMediosPago["tarjeta"];
            $datosTotal['debito']            = '';
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
            $datosTotal['credito']           = $camposMediosPago["cheque"];
            $datosTotal['debito']            = '';
            $asientosContables[]             = $datosTotal;
        }
        if ($camposMediosPago["credito"] != "") {
            //afectar cuentas por pagar "proveedores nacionales"
            $datosTotal = array();
            $datosTotal['id_cuenta']         = '220501';
            $datosTotal['comprobante']       = $comprobante;
            $datosTotal['num_comprobante']   = $factura->id;
            $datosTotal['fecha']             = $factura->fechaFactura;
            $datosTotal['concepto']          = $concepto;
            $datosTotal['credito']           = $camposMediosPago["credito"];
            $datosTotal['debito']            = '';
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
            $datosIva['credito']           = '';
            $datosIva['debito']            = $factura->iva;    
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
            $datosRetencion['num_comprobante']   = $factura->id;
            $datosRetencion['fecha']             = $factura->fechaFactura;
            $datosRetencion['concepto']          = $concepto;
            $datosRetencion['credito']           = $value["valor"];
            $datosRetencion['debito']            = '';    
            $asientosContables[]                 = $datosRetencion;  
            
            //si el impuesto es el iva teorico, se debe afectar tambien el iva descontable
            if ($value["id"] == "5") {
                $datosRetencion = array();
                $datosRetencion['id_cuenta']         = $retencion["id_cuenta2"];
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

