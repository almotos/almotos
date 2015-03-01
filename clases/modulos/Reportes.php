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
        
        if (!empty($datos['tipo']) && $datos['tipo'] == 'ventas') {
            $factura = new FacturaVenta();
            $prefijo = "fv";
            $tercero = "cliente";

        } else {
            $factura = new FacturaCompra();
            $prefijo = "fc";
            $tercero = "proveedor";

        }
        
        if ($datos['tipo_reporte'] == "lista_facturas") {
            $response = $this->listaFacturas($factura, $prefijo, $tercero, $datos);
            
        } else if ($datos['tipo_reporte'] == "total_sumarizado") {
            $response = $this->sumarizadoTotal($factura, $prefijo, $tercero, $datos);
        }
        
        $respuesta = array(
            "basicosTabla" => array(
                                    "pagina"            => "1",
                                    "rutaPaginador"     => "ajax/{$modulo->url}/move",
                                    "mostrarTeachme"    => false,
                                    "mostrarAyuda"      => false 
                            ),    
            "paginacion" => array(
                                    "mostrarPaginacion" => false,
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
                                        
        $respuesta['filas']         = $response['filas'];
        $respuesta['cabeceras']     = $response['cabeceras'];
        
        return json_encode($respuesta);


    }
    
    
    public function listaFacturas($factura, $prefijo, $tercero, $datos) 
    {

        $respuesta = array();
        
        $datosConsulta = $this->datosConsulta($datos, $factura, $prefijo);
        
        //generar las filas de la tabla
        $filas      = array();
        
        if (!empty($datosConsulta) && is_array($datosConsulta)) {
            foreach ($datosConsulta as $factura) {
                $fila = array();
                $fila["id"]                       = $factura->id;
                $fila["atributos"]                = "atributo_0={$factura->id}";
                
                $i = 0;
                foreach ($datos['fields'] as $value) {
                    $value = ($value == "tercero") ? $tercero : $value;
                    $value = str_replace("_", "", $value);
                    //generar las columnas de cada fila
                    $fila["columnas"][$i]              = array();
                    $fila["columnas"][$i]["id"]        = "id_columna";
                    $fila["columnas"][$i]["valor"]     = $factura->$value;
                    $i++;
                }
                
                $filas[] = $fila;
            }
        }
        
        $respuesta['filas'] = $filas;
        
        //generar las cabeceras de la tabla de acuerdo a los fields que el usuario quiere consultar
        $cabeceras = array();
        
        if (!empty($datos['fields']) && is_array($datos['fields'])) {
            $j = 0;
            
            foreach ($datos['fields'] as $value) {
                $value = ($value == "tercero") ? $tercero : $value;
                //cada field viene con un formato (nombre_Campo)
                $valorObj = str_replace("_", "", $value);//este valor se usa para sacar los valores de un objeto (formato= nombreCampo)
                $valorBd  = strtolower($value);//este valor se usa para la query en la BD (formato= nombre_campo)
                $nombre   = ucwords(str_replace("_", " ", $value));//valor para mostrar en la tabla
                
                //generar las columnas de cada fila
                $cabeceras[$j]                   = array();
                $cabeceras[$j]["id"]             = "cabecera_1";
                $cabeceras[$j]["valor"]          = $nombre;
                $cabeceras[$j]["name"]           = $valorObj;
                $cabeceras[$j]["nombreOrden"]    = "{$valorObj}|{$valorBd}.";
                
                $j++;
            }  
            
        }
        
        $respuesta['cabeceras'] = $cabeceras;
        
        return $respuesta;
        
    }
    
    public function sumarizadoTotal($factura, $prefijo, $tercero, $datos) 
    {

        $respuesta = array();
        
        $datosConsulta = $this->datosConsulta($datos, $factura, $prefijo);
        
        //generar las filas de la tabla
        $filas   = array();
        $total   = 0;
        if (!empty($datosConsulta) && is_array($datosConsulta)) {
            foreach ($datosConsulta as $factura) {
                $total += $factura->total;
            }
        }
        
        $fila = array();
        $fila["id"]                       = 'id';
        $fila["atributos"]                = "atributo_0=id";
        $fila["columnas"][0]              = array();
        $fila["columnas"][0]["id"]        = "id_columna";
        $fila["columnas"][0]["valor"]     = "Total ".  ucwords($datos['tipo']);
        $fila["columnas"][1]              = array();
        $fila["columnas"][1]["id"]        = "id_columna";
        $fila["columnas"][1]["valor"]     = $total;        


        $filas[] = $fila;        
        
        $respuesta['filas'] = $filas;
        
        //generar las cabeceras de la tabla de acuerdo a los fields que el usuario quiere consultar
        $cabeceras = array();

        //generar las columnas de cada fila
        $cabeceras[0]                   = array();
        $cabeceras[0]["id"]             = "cabecera_1";
        $cabeceras[0]["valor"]          = 'Total Factura';
        $cabeceras[0]["name"]           = 'totalFactura';
        $cabeceras[0]["nombreOrden"]    = "totalFactura";
        $cabeceras[0]["colspan"]        = "2";

        $respuesta['cabeceras'] = $cabeceras;
        
        return $respuesta;
        
    }    
    
    public function datosConsulta($datos, $factura, $prefijo) 
    {
        if ($datos['rango_personalizado'] == false && !empty($datos['ultimos'])) {
            $valorFechas = array(
                'dia'           => '-1 day',
                'semana'        => '-7 day',
                'mes'           => '-30 day',
                'tres_meses'    => '-90 day',
                'seis_meses'    => '-180 day',
                'anyo'          => '-360 day'
            );
            
            $fechaInicial = date('Y-m-d H:i:s', strtotime($valorFechas[$datos['ultimos']]));
            $fechaFinal   = date('Y-m-d H:i:s');
        }
        
        $condicionGlobal = " {$prefijo}.fecha_factura BETWEEN '{$fechaInicial}' AND '{$fechaFinal}' ";
        
        //filtrar por cajas
        if ($datos['todas_cajas'] == false && !empty($datos['caja']) && is_array($datos['caja'])) {
            $cajas = implode(",", $datos['caja']);
            $condicionGlobal .= " AND {$prefijo}.id_caja IN ({$cajas}) ";
        } 
        
        //filtrar por usuarios
        if ($datos['filtro_usuarios'] == true && !empty($datos['usuarios']) && is_array($datos['usuarios'])) {
            $usuarios = implode(",", $datos['usuarios']);
            $condicionGlobal .= " AND {$prefijo}.id_usuario IN ({$usuarios}) ";
        }         
        
        $datosConsulta = $factura->listar(0, 0, "", $condicionGlobal, "{$prefijo}.fecha_factura");
        
        return $datosConsulta;
        
    }
    
}
