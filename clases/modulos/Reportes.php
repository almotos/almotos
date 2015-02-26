<?php

/**
 * Clase encargada de generar la informacion de los Reportes
 *
 * @author pablo
 */
class Reportes
{
    public function __construct()
    {
        
    }
    
    public function consultarCuadreCaja($datos)
    {
        global $modulo;
        
        $caja                   = $datos['caja'];
        $fechaFinal             = $datos['fecha_final'];
        $fechaInicial           = $datos['fecha_inicial'];
        $rangoPersonalizado     = $datos['rango_personalizado'];
        $tipo                   = $datos['tipo'];
        $todasCajas             = $datos['todas_cajas'];
        $ultimos                = $datos['ultimos'];
        
        unset($datos);        
        
        if (!empty($tipo) && $tipo == 'ventas') {
            $factura = new FacturaVenta();
            $prefijo = "fv";

        } else {
            $factura = new FacturaCompra();
            $prefijo = "fc";

        }
        
        if ($rangoPersonalizado == "false" && !empty($ultimos)) {
            $valorFechas = array(
                'dia'           => '-1 day',
                'semana'        => '-7 day',
                'mes'           => '-30 day',
                'tres_meses'    => '-90 day',
                'seis_meses'    => '-180 day',
                'anyo'          => '-360 day'
            );
            
            $fechaInicial = date('Y-m-d H:i:s', strtotime($valorFechas[$ultimos]));
            $fechaFinal   = date('Y-m-d H:i:s');
        }
        
        $condicionGlobal = " {$prefijo}.fecha_factura BETWEEN '{$fechaInicial}' AND '{$fechaFinal}' ";
        
        if ($todasCajas == "false" && !empty($caja) && is_array($caja)) {
            $cajas = implode(",", $caja);
            $condicionGlobal .= " AND {$prefijo}.id_caja IN ({$cajas}) ";
        } 
        
        
        $datosConsulta = $factura->listar(0, 0, "", $condicionGlobal, "{$prefijo}.fecha_factura");
        
        $filas = array();
        
        if (!empty($datosConsulta) && is_array($datosConsulta)) {
            foreach ($datosConsulta as $factura) {
                $fila = array();
                $fila["id"]                       = $factura->id;
                $fila["atributos"]                = "atributo_0={$factura->id}";
                $fila["columnas"][0]              = array();
                $fila["columnas"][0]["id"]        = "id_columna";
                $fila["columnas"][0]["valor"]     = $factura->idFactura;
                $fila["columnas"][1]              = array();
                $fila["columnas"][1]["id"]        = "id_columna";
                $fila["columnas"][1]["valor"]     = $factura->proveedor;    
                
                $filas[] = $fila;
            }
        }
        
        $datos = array(
            "basicosTabla" => array(
                                    "pagina"            => "1",
                                    "rutaPaginador"     => "ajax/{$modulo->url}/move",
                                    "mostrarTeachme"    => false,
                                    "mostrarAyuda"      => false 
                            ),
            "cabeceras" => array(
                                    array(
                                        "id"            => "cabecera_1",
                                        "valor"         => "Id Factura",
                                        "name"          => "id_factura",
                                        "nombreOrden"   => "idFactura|{$prefijo}.id_factura"
                                    ),
                                    array(
                                        "id"            => "cabecera_2",
                                        "valor"         => "Proveedor",
                                        "name"          => "proveedor",
                                        "nombreOrden"   => "proveedor|p.nombre"
                                    )                

                           ),
//            "filas" => array(
//                                array(
//                                    "id" => "id fila 1",
//                                    "atributos" => "atributo_1=1 atributo2=2",
//                                    "columnas" => array(
//                                        array(
//                                            "id" => "id_col_1",
//                                            "valor" => "valor 1"
//                                        ),
//                                        array(
//                                            "id" => "id_col_2",
//                                            "valor" => "valor 2"
//                                        )                                        
//                                    )
//                                ),
//                                array(
//                                    "id" => "id fila 2",
//                                    "atributos" => "atributo_4=4 atributo5=5",
//                                    "columnas" => array(
//                                        array(
//                                            "id" => "id_col_4",
//                                            "valor" => "valor 4"
//                                        ),
//                                        array(
//                                            "id" => "id_col_5",
//                                            "valor" => "valor 5"
//                                        )                                        
//                                    )
//                                )                
//                
//            ),
                                       
            "paginacion" => array(
                                    "mostrarPaginacion" => true,
                                    "datosPaginacion"   => "Registro 1 a su madre",
                                    "cantidadRegistros" => "10",
                                    "botonPrimeraPagina" => array(
                                        "pagina" => "1"
                                    ),
                                    "botonAtrasPagina" => array(
                                        "pagina" => "2"
                                    ),
                                    "botonSiguientePagina" => array(
                                        "pagina" => "4"
                                    ),
                                    "botonUltimaPagina" => array(
                                        "pagina" => "10"
                                    )
            )
        );
                                        
        $datos['filas'] = $filas;
        
        return json_encode($datos);


    }
    
}
