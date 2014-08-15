<?php

/**
 *
 * @package     FOM
 * @subpackage  Actividades economicas realizadas en colombia
 * @author      Pablo Andrés Vélez Vidal <pavelez8@misena.edu.co>
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2012 Genesys Corporation.
 * @version     0.2
 * 
 * Módulo encargado de almacenar los registros con el listado de las actividades economicas realizadas en Colombia.
 * Puede ser utilizado en el módulo de proveedores, para identificarlos o sectorizarlos por la actividad economica que realizan,
 * y de esta forma se aplicar{an las retenciones tributarias debidamente.
 * 
 *
 * */
class ActividadEconomica {

    /**
     * Código interno o identificador del registro en la base de datos
     * @var entero
     */
    public $id;

    /**
     * URL relativa del módulo 
     * @var cadena
     */
    public $urlBase;

    /**
     * URL relativa del módulo 
     * @var cadena
     */
    public $url;

    /**
     * Código interno o identificador del modulo
     * @var entero
     */
    public $idModulo;

    /**
     * Codigo utilizado por la Dian para un determinado documento
     * @var cadena
     */
    public $codigoDian;

    /**
     * Nombre de la actividad economica
     * @var entero
     */
    public $nombre;
    
    /**
     * Valor del porcentaje del impuesto Retecree para una actividad economica en particular
     * @var entero
     */
    public $porcentajeRetecree;    
      
    /**
     * Número de registros de la lista
     * @var entero
     */
    public $activo;

    /**
     * Indicador del orden cronológio de la lista de registros
     * @var lógico
     */
    public $listaAscendente = true;

    /**
     * Número de registros de la lista
     * @var entero
     */
    public $registros = NULL;

    /**
     * Número de registros activos de la lista 
     * @var entero
     */
    public $registrosActivos = NULL;

    /**
     * Número de registros extraidos en una consulta
     * @var entero
     */
    public $registrosConsulta = NULL;

    /**
     * Orden predeterminado para organizar los listados
     * @var entero
     */
    public $ordenInicial = NULL;

    /**
     *
     * Inicializar del objeto
     *
     * @param entero $id Código interno o identificador de la actividad economica en la base de datos
     *
     */
    public function __construct($id = NULL) {
        global $sql, $modulo;
        $this->urlBase              = '/' . $modulo->url;
        $this->url                  = $modulo->url;
        $this->idModulo             = $modulo->id;
        //Saber el numero de registros
        $this->registros            = $sql->obtenerValor('actividades_economicas', 'COUNT(id)', 'id != "0"');
        //Saber el numero de registros activos
        $this->registrosActivos     = $sql->obtenerValor('actividades_economicas', 'COUNT(id)', 'activo = "1"');
        //establecer el valor del campo predeterminado para organizar los listados
        $this->ordenInicial         = 'nombre';

        if (isset($id)) {
            $this->cargar($id);
        }
    }


    /**
     *
     * Cargar los datos de una actividad economica
     *
     * @param entero $id Código interno o identificador de la actividad economica en la base de datos
     *
     */
    public function cargar($id) {
        global $sql;

        if (isset($id) && $sql->existeItem('actividades_economicas', 'id', intval($id))) {

            $tablas = array(
                'ae' => 'actividades_economicas'
            );

            $columnas = array(
                'id'                    => 'ae.id',
                'codigoDian'            => 'ae.codigo_dian',
                'nombre'                => 'ae.nombre',
                'porcentajeRetecree'    => 'ae.porcentaje_retecree',
                'activo'                => 'ae.activo'
            );

            $condicion = 'ae.id = "' . $id . '"';
            
            if (is_null($this->registrosConsulta)) {
                $sql->seleccionar($tablas, $columnas, $condicion);
                $this->registrosConsulta = $sql->filasDevueltas;
            }               

            $consulta = $sql->seleccionar($tablas, $columnas, $condicion);          

            if ($sql->filasDevueltas) {
                $fila = $sql->filaEnObjeto($consulta);

                foreach ($fila as $propiedad => $valor) {
                    $this->$propiedad = $valor;
                }

                $this->url = $this->urlBase . '/' . $this->id;
            }
        }
    }


    /**
     *
     * Adicionar una actividad economica
     *
     * @param  arreglo $datos       Datos del tipo de  unidad a adicionar
     * @return entero               Código interno o identificador del tipo unidad en la base de datos (NULL si hubo error)
     *
     */
    public function adicionar($datos) {
        global $sql;

        $datosItem = array();
        $datosItem['codigo_dian']           = $datos['codigo_dian'];
        $datosItem['nombre']                = $datos['nombre'];
        $datosItem['porcentaje_retecree']   = $datos['porcentaje_retecree'];

        $datosItem['activo'] = (isset($datos['activo'])) ?'1':'0';

        $consulta = $sql->insertar('actividades_economicas', $datosItem);

        if ($consulta) {
            $idItem = $sql->ultimoId;
            return $idItem;
            
        } else {
            return false;
            
        }
    }


    /**
     * Modificar una actividad economica
     *
     * @param  arreglo $datos       Datos del tipo de documento a modificar
     * @return lógico               Indica si el procedimiento se pudo realizar correctamente o no
     *
     */
    public function modificar($datos) {
        global $sql;

        if (!isset($this->id)) {
            return false;
        }
        $datosItem = array();

        $datosItem['codigo_dian']           = $datos['codigo_dian'];
        $datosItem['nombre']                = $datos['nombre'];
        $datosItem['porcentaje_retecree']   = $datos['porcentaje_retecree'];
        $datosItem['activo']                = (isset($datos['activo'])) ?'1':'0';
        
        $consulta = $sql->modificar('actividades_economicas', $datosItem, 'id = "' . $this->id . '"');


        if ($consulta) {
            return $this->id;
            
        } else {
            return false;
            
        }
    }


    /**
     *
     * Eliminar una actividad economica
     *
     * @param entero $id    Código interno o identificador de una unidad en la base de datos
     * @return lógico       Indica si el procedimiento se pudo realizar correctamente o no
     *
     */
    public function eliminar() {
        global $sql, $textos;
        //arreglo que será devuelto como respuesta
        $respuestaEliminar = array(
            'respuesta' => false,
            'mensaje'   => $textos->id('ERROR_DESCONOCIDO'),
        );
        
        if (!isset($this->id)) {
            return $respuestaEliminar;
        }
        
        //hago la validacion de la integridad referencial
        $arreglo1 = array('proveedores',  'id_actividad_economica = "'.$this->id.'"', $textos->id('PROVEEDORES'));//arreglo del que sale la info a consultar
        $arreglo2 = array('clientes',     'id_actividad_economica = "'.$this->id.'"', $textos->id('CLIENTES'));//arreglo del que sale la info a consultar
        $arreglo3 = array('empresas',     'id_actividad_economica = "'.$this->id.'"', $textos->id('EMPRESAS'));//arreglo del que sale la info a consultar
 
        $arregloIntegridad  = array($arreglo1, $arreglo2, $arreglo3);//arreglo de arreglos para realizar las consultas de integridad referencial, (ver documentacion de metodo)
        $integridad         = Recursos::verificarIntegridad($textos->id('ACTIVIDAD_ECONOMICA'), $arregloIntegridad);
        /**
         * si hay problemas con la integridad referencial, la variable integridad tiene como valor,
         * un texto diciendo que tabla contiene n cantidad de relaciones con esta
         */
        if ($integridad != "") {
            $respuestaEliminar['mensaje'] = $integridad;
            return $respuestaEliminar;
        }
              
        $sql->iniciarTransaccion();
        $consulta = $sql->eliminar('actividades_economicas', 'id = "' . $this->id . '"');
        
        if (!($consulta)) {
            $sql->cancelarTransaccion("Fallo en el archivo " . __FILE__ . " en la linea " .  __LINE__);
            return $respuestaEliminar;
            
        } else {
            $sql->finalizarTransaccion();
            //todo salió bien, se envia la respuesta positiva
            $respuestaEliminar['respuesta'] = true;
            return $respuestaEliminar;
        }
    }


    /**
     *
     * Listar las actividades economicas registradas en el sistema
     *
     * @param entero  $cantidad    Número de tipos de documento a incluir en la lista (0 = todas las entradas)
     * @param arreglo $excepcion   Arreglo con los códigos internos o identificadores a omitir en la lista
     * @param cadena  $condicion   Condición adicional (SQL)
     * @return arreglo             Lista de tipos de documento
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
            $condicion .= 'ae.id NOT IN (' . $excepcion . ')';
        }

        /*         * * Definir el orden de presentación de los datos ** */
        if (!isset($orden)) {
            $orden = $this->ordenInicial;
        }
        if ($this->listaAscendente) {
            $orden = $orden . ' ASC';
        } else {
            $orden = $orden . ' DESC';
        }


        $tablas = array(
            'ae' => 'actividades_economicas'
        );

        $columnas = array(
            'id'                    => 'ae.id',
            'codigoDian'            => 'ae.codigo_dian',
            'nombre'                => 'ae.nombre',
            'porcentajeRetecree'    => 'ae.porcentaje_retecree',
            'activo'                => 'ae.activo'
        );


        if (!empty($condicionGlobal)) {
            if ($condicion != '') {
                $condicion .= ' AND ';
            }
            $condicion .= $condicionGlobal;
        }

        if (is_null($this->registrosConsulta)) {
            $sql->seleccionar($tablas, $columnas, $condicion);
            $this->registrosConsulta = $sql->filasDevueltas;
            
        }
        $consulta = $sql->seleccionar($tablas, $columnas, $condicion, 'ae.id', $orden, $inicio, $cantidad);
        if ($sql->filasDevueltas) {
            $lista = array();

            while ($objeto = $sql->filaEnObjeto($consulta)) {
                $objeto->estado = ($objeto->activo) ? HTML::frase($textos->id('ACTIVO'), 'activo') : HTML::frase($textos->id('INACTIVO'), 'inactivo');
       
                $lista[] = $objeto;
            }
            
        }

        return $lista;
    }


    /**
     * Método encargado de generar la tabla de registros principal
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
            HTML::parrafo($textos->id('CODIGO_DIAN'), 'centrado')   => 'codigoDian|ae.codigo_dian',
            HTML::parrafo($textos->id('NOMBRE'), 'centrado')        => 'nombre|ae.nombre',
            HTML::parrafo($textos->id('RETECREE'), 'centrado')      => 'porcentajeRetecree|ae.porcentaje_retecree',
            HTML::parrafo($textos->id('ESTADO'), 'centrado')        => 'estado'
        );
        //ruta a donde se mandara la accion del doble click
        $ruta = '/ajax' . $this->urlBase . '/move';

        $estilosColumnas    = array('columna1', 'columna2', 'columna3', 'columna4');
        $tabla              = Recursos::generarTablaRegistros($arregloRegistros, $datosTabla, $ruta, $datosPaginacion, $estilosColumnas);
        $menuDerecho        = HTML::crearMenuBotonDerecho('ACTIVIDADES_ECONOMICAS');

        return $tabla . $menuDerecho;
    }
    
    
     /**
     * Adicionar subgrupos a partir de un archivo excel
     *
     * @param  arreglo $datos       Datos del subgrupo a adicionar
     * @return entero               Código interno o identificador del subgrupo en la base de datos (NULL si hubo error)
     */
    public function adicionarMasivo($datos) {
        global $sql, $configuracion, $archivo_masivo;

        if (empty($archivo_masivo['tmp_name'])) {
            return false;
            
        } else {
            $validarFormato = Archivo::validarArchivo($archivo_masivo, array('xls'));
            
            if (!$validarFormato) {
                $configuracionRuta = $configuracion['RUTAS']['media'] . "/" . $configuracion["RUTAS"]["documentos"];
                $recurso = Archivo::subirArchivoAlServidor($archivo_masivo, $configuracionRuta);


                require_once $configuracion['RUTAS']['clases'] . '/excel_reader2.php';
                $data = new Spreadsheet_Excel_Reader($configuracionRuta . '/' . $recurso);

                $row = 1;
                $col = 1;

                if ($datos['inicial'] == 1) {

                    $row++;
                    $campos = array();
                    
                    if ($datos['codigo_dian'] != 0)
                        $campos['codigo_dian'] = $datos['codigo_dian'];   
                    
                    if ($datos['nombre'] != 0)
                        $campos['nombre'] = $datos['nombre'];
                    
                    if ($datos['porcentaje_retecree'] != 0)
                        $campos['porcentaje_retecree'] = $datos['porcentaje_retecree'];                      
                }

                $valor1    = $data->val($row, $col);
                $respuesta = array();
                
                $test = array();

                while ($valor1 != null) {
                    if ($datos['inicial'] == 0) {
                        $respuesta[$col] = $valor1;
                        $col++;
                        
                    } else {
                        $datosInsert = array();
                        
                        foreach ($campos AS $nombre => $valor) {

                            $valor = $data->val($row, $valor);
                            
                            if ($nombre == 'codigo_dian') {
                                $datosInsert['codigo_dian'] = $valor;
                                
                            } 
                            
                            if ($nombre == 'nombre') {
                                $datosInsert["nombre"] = $valor;
                            }    
                            
                            if ($nombre == 'porcentaje_retecree') {
                                $porcentaje = str_replace("%", "", $valor);                                
                                $datosInsert["porcentaje_retecree"] = $porcentaje;
                                
                            }                            
                            
                        }
                        
                       $datosInsert['activo'] = '1';

                        $sql->insertar('actividades_economicas', $datosInsert);

                        $row++;
                        $respuesta = true;
                        
                    }
                    
                    $valor1 = $data->val($row, $col);
                    
                }
                
                Archivo::eliminarArchivoDelServidor($configuracionRuta . '/' . $recurso);        
                
                return $respuesta;
                
            } else {
                return false;
                
            }
            
        }
        
    }    
    
    

}



