<?php

/**
 *
 * @package     FOM
 * @subpackage  Contabilidad
 * @author      Pablo Andrés Vélez Vidal <pavelez@genesyscorp.com.co>
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2013 Genesys Corporation.
 * @version     0.2
 * 
 * Clase del modulo contable en general. Por contable en general nos referimos a
 * la primera capa de abstracción de la contabilidad de la empresa, habran mas clases,
 * por ejemplo una proxima será la clase Asiento contable, SoporteContable, DocumentoContable etc.
 *
 * */

class Contabilidad {

    /**
     * Objeto del modulo desde el cual se invoca esta clase
     * @var boolean
     */
    public $objeto;

    /**
     * Objeto que representa la info de la empresa
     * @var boolean
     */
    public $empresa;    

    /**
     *
     * Inicializar la clase
     *
     * @param string clase
     * @param string $id Description
     *
     */
    public function __construct($clase = NULL, $id = NULL) {
        global $sesion_configuracionGlobal;
        
        $this->objeto = (isset($id) && isset($clase)) ? new $clase($id) : (isset($clase)) ? new $clase() : "";
        $this->empresa = $sesion_configuracionGlobal->empresa;

    }

    /**
     * Devuelve el arreglo con las retenciones a ser ejecutadas por una compra
     * 
     *  Explicacion regimenes
     * 
     *      '1'     => 'Regimen simplificado',
     *      '2'     => 'Regimen Común',
     *      '3'     => 'Gran Contribuyente',
     *      '4'     => 'Gran Contribuyente autoretenedor',
     *      '5'     => 'Simplificado no residente',
     *      '6'     => 'No residente',
     *      '7'     => 'Empresa del estado',
     *      '8'     => 'No responsable' 
     * 
     *  Explicacion impuestos a retener
     * 
     *      '1'     => 'retefuente',
     *      '2'     => 'reteica',
     *      '3'     => 'reteiva',
     *      '4'     => 'retecree',
     *      '5'     => 'ivateorico',
     * 
     * "primera posicion" le compra a la "segunda posicion", se obtiene un arreglo
     * con los ids de los impuestos a retener, por ejemplo:
     * Regimen comun le compra a regimen simplificado, debe retener: 
     * 
     *      '1'     => 'retefuente',
     *      '2'     => 'reteica',
     *      '5'     => 'ivateorico',
     */
    public function getArregloRetenciones(){
//        $retenciones = array();
//        
//        $retenciones["1"]       = array();
//        $retenciones["1"]["1"]  = array();
//        $retenciones["1"]["2"]  = array();
//        $retenciones["1"]["3"]  = array();
//        $retenciones["1"]["4"]  = array("4");
//        $retenciones["2"]["1"]  = array("1", "2", "5");
//        $retenciones["2"]["2"]  = array("1", "4");
//        $retenciones["2"]["3"]  = array("1", "4");
//        $retenciones["2"]["4"]  = array();
//        $retenciones["3"]["1"]  = array("1", "3", "5");
//        $retenciones["3"]["2"]  = array("1", "2", "3", "4");
//        $retenciones["3"]["3"]  = array("1", "4");
//        $retenciones["3"]["4"]  = array();
//        $retenciones["4"]["1"]  = array("1", "2", "5");
//        $retenciones["4"]["2"]  = array("1", "2", "3", "4");
//        $retenciones["4"]["3"]  = array("1", "4");
//        $retenciones["4"]["4"]  = array();  

        //Se quita el retecree de la contabilidad, arriba se deja como estuvo en el 2013
        $retenciones = array();
        
        $retenciones["1"]       = array();
        $retenciones["1"]["1"]  = array();
        $retenciones["1"]["2"]  = array();
        $retenciones["1"]["3"]  = array();
        $retenciones["1"]["4"]  = array("4");
        $retenciones["2"]["1"]  = array("1", "2", "5");
        $retenciones["2"]["2"]  = array("1");
        $retenciones["2"]["3"]  = array("1");
        $retenciones["2"]["4"]  = array();
        $retenciones["3"]["1"]  = array("1", "3", "5");
        $retenciones["3"]["2"]  = array("1", "2", "3");
        $retenciones["3"]["3"]  = array("1");
        $retenciones["3"]["4"]  = array();
        $retenciones["4"]["1"]  = array("1", "2", "5");
        $retenciones["4"]["2"]  = array("1", "2", "3");
        $retenciones["4"]["3"]  = array("1");
        $retenciones["4"]["4"]  = array();         
        
        return $retenciones;
    }
  
    /**
     * Metodo encargado de calcular que retenciones se van a realizar a un determinado proveedor
     */
    public function mostrarRetencionesCompras($idProveedor){
        
        $proveedor = new Proveedor($idProveedor);
        
        $regimenEmpresa     = (string)$this->empresa->regimen;
        $regimenProveedor   = (string)$proveedor->regimen;
        
        $arregloRetenciones = $this->getArregloRetenciones();
        
        $retenciones = $arregloRetenciones[$regimenEmpresa][$regimenProveedor];
        
        return  $retenciones;    
        
    }
    
    /**
     * Metodo encargado de calcular que retenciones se van a realizar a un determinado proveedor
     */
    public function mostrarRetencionesVentas($idCliente){
        
        $cliente = new Cliente($idCliente);
        
        $regimenEmpresa     = (string)$this->empresa->regimen;
        $regimenCliente   = (string)$cliente->regimen;
        
        $arregloRetenciones = $this->getArregloRetenciones();
        
        $retenciones = $arregloRetenciones[$regimenCliente][$regimenEmpresa];
        
        return  $retenciones;    
        
    }    
    
    /**
     * Metodo encargado de retornar los campos de retenciones con los valores adecuados segun el total y
     * tambien devuelve el total de las retenciones y el total a pagar
     */
    public function generarCamposRetenciones($idTercero, $total, $totalIva, $operacion = "compras") {
        global $configuracion, $sql, $textos, $sesion_configuracionGlobal;
        //obtener el arreglo con las retenciones a aplicar segun los regimenes del comprador y del vendedor
        $retenciones = array();
        
        if ($operacion == "compras") {
            $retenciones = $this->mostrarRetencionesCompras($idTercero);
            
        } else if ($operacion == "ventas") {
            $retenciones = $this->mostrarRetencionesVentas($idTercero);
            
        }
        
        $respuesta = array();
        
        $camposRetencion    = '';
        $totalRetenciones   = 0;
        $totalAPagar        = 0;
        $datosRetenciones   = "";
        //recorrer estas retenciones para generar los campos
        foreach ($retenciones as $key) {
            $retencion = array();
            //capturar cada una de las retenciones llamando al arreglo ubicado en /configuracion/contabilidad.php
            if ($operacion == "compras"){
                $retencion = $configuracion["RETENCIONES"][$configuracion["GENERAL"]["idioma"]][$key];
                
            } else if ($operacion == "ventas") {
                $retencion = $configuracion["RETENCIONES"]["VENTAS"][$configuracion["GENERAL"]["idioma"]][$key];
            }
            
            
            if ($retencion["monto_minimo"] == '100%' || ( $total > ($retencion["monto_minimo"] * $sesion_configuracionGlobal->valorUvt)))  {
                $porcentajeRetencion = 1;
                //si esta establecido el porcentaje de retencion se utiliza
                if ($retencion["porcentaje"]) {
                    $porcentajeRetencion = $retencion["porcentaje"];

                } else if($retencion["campo_consulta"]){//si no, se consulta la columna de la tabla actividades economicas
                    $idActividad         = $sql->obtenerValor( ($operacion == "compras") ? "proveedores" : "clientes", "id_actividad_economica", "id = '".$idTercero."'");
                    $porcentajeRetencion = $sql->obtenerValor("actividades_economicas", $retencion["campo_consulta"], "id = '".$idActividad."'");

                }
                
                $opciones = array();
                
                
                //se calcula el valor de la retencion
                $valorRetencion = $total * ($porcentajeRetencion / 100);
                
                //se formatea el nombre de la retencion, solo con intenciones de presentacion en la UI ;)
                $nombreRetencion = Recursos::completarCaracteres($retencion["nombre"], ".", 25, "2");                 
              
                //si las retenciones son diferentes al "iva teorico o asumido" deben descontarse del valor a pagar
                if ($key != "5"){
                    //si el impuesto es el reteiva, este es el 50% del iva que nos facturaron
                    if ($key == "3"){
                        $valorRetencion = $totalIva / 2;
                    }
                    //se suma el valor al total de retenciones
                    $totalRetenciones   += $valorRetencion;
                    //se genera el campo a ser retenido
                    $camposRetencion .= HTML::frase($nombreRetencion, 'negrilla margenSuperior margenIzquierda');
                    $camposRetencion .= HTML::campoTexto($retencion["nombre_clave"], 20, 255, $valorRetencion, 'margenIzquierda campoDinero camposRetenciones', $key, $opciones); 
                    $camposRetencion .= HTML::parrafo('', 'margenSuperior');                     
                    
                } else {
                    //pendiente de verificacion si es el 15% o el 50% del iva que hubiera sido retenido
                    $valorRetencion = ( $total * (16 / 100) ) / 2;
                    //se genera el campo a ser retenido
                    $camposRetencion .= HTML::frase($nombreRetencion, 'negrilla margenSuperior margenIzquierda');
                    $camposRetencion .= HTML::frase("$ ".$valorRetencion); 
                    $camposRetencion .= HTML::campoOculto($retencion["nombre_clave"], $valorRetencion, 'campoOcultoIvaTeorico');
                    $camposRetencion .= HTML::parrafo('', 'margenSuperior');                    
                    
                }
                
                //se llena con valores el arreglo datos retenciones
                $datosRetenciones .= $key.";".$valorRetencion."|";                  
 
            }
            
        }
        
        //Set el "boton" para actualizar las retenciones
        if ($camposRetencion != '') {
            $camposRetencion .= HTML::parrafo($textos->id("ACTUALIZAR_RETENCIONES"), 'margenIzquierda margenSuperior estiloEnlace subtitulo', 'actualizarValoresRetenciones');
        }
        
        $totalAPagar += $total - $totalRetenciones;
        
        $respuesta["campos_retencion"]  = $camposRetencion;
        $respuesta["total_retenciones"] = $totalRetenciones;
        $respuesta["datos_retenciones"] = $datosRetenciones;
        $respuesta["total_a_pagar"]     = $totalAPagar;
        
        return $respuesta;
        
    }
    
}
