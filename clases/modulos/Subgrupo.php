<?php

/**
 * @package     FOLCS
 * @subpackage  Subgrupos 
 * @author      Pablo Andrés Vélez Vidal 
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2012 Genesys Soft.
 * @version     0.2
 * 
 * SubGrupos utilizados en el sistema
 * */
class Subgrupo {

    /**
     * Código interno o identificador del Subgrupo en la base de datos
     * @var entero
     */
    public $id;

    /**
     * URL relativa del módulo de Subgrupos
     * @var cadena
     */
    public $urlBase;

    /**
     * URL relativa del módulo de Subgrupos
     * @var cadena
     */
    public $url;

    /**
     * Código interno o identificador del modulo
     * @var entero
     */
    public $idModulo;

    /**
     * Nombre del Subgrupo
     * @var entero
     */
    public $nombre;

    /**
     * Identificador del grupo al que pertenece
     * @var entero
     */
    public $idGrupo;

    /**
     * Nombre del grupo al que pertenece
     * @var entero
     */
    public $nombreGrupo;

    /**
     * Determina si el subgrupo esta activo
     * @var boleano
     */
    public $activo;

    /**
     * Indicador del orden cronológio de la lista de registros
     * @var lógico
     */
    public $listaAscendente = TRUE;

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
     * Número de registros activos de la lista de foros
     * @var entero
     */
    public $registrosConsulta = NULL;

    /**
     * Orden predeterminado para organizar los listados
     * @var entero
     */
    public $ordenInicial = NULL;

    /**
     * Inicializar un subgrupo
     * @param entero $id Código interno o identificador del subgrupo en la base de datos
     */
    public function __construct($id = NULL) {
        global $sql, $modulo;
        
        $this->urlBase          = '/' . $modulo->url;
        $this->url              = $modulo->url;
        $this->idModulo         = $modulo->id;
        
        //Saber el numero de registros
        $this->registros        = $sql->obtenerValor('subgrupos', 'COUNT(id)', 'id != "0"');
        //Saber el numero de registros activos
        $this->registrosActivos = $sql->obtenerValor('subgrupos', 'COUNT(id)', 'activo = "1"');
        //establecer el valor del campo predeterminado para organizar los listados
        $this->ordenInicial = 'nombre';

        if (isset($id)) {
            $this->cargar($id);
            
        }
        
    }

    /**
     * Cargar los datos de un subgrupo
     * @param entero $id Código interno o identificador del subgrupo en la base de datos
     */
    public function cargar($id) {
        global $sql;

        if (isset($id) && $sql->existeItem('subgrupos', 'id', intval($id))) {

            $tablas = array(
                'sg'    => 'subgrupos',
                'g'     => 'grupos'
            );

            $columnas = array(
                'id'            => 'sg.id',
                'nombre'        => 'sg.nombre',
                'idGrupo'       => 'sg.id_grupo',
                'nombreGrupo'   => 'g.nombre',
                'activo'        => 'sg.activo'
            );

            $condicion = 'sg.id_grupo = g.id AND sg.id = "' . $id . '"';
            
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
     * Adicionar un subgrupo
     * @param  arreglo $datos       Datos del subgrupo a adicionar
     * @return entero               Código interno o identificador del subgrupo en la base de datos (NULL si hubo error)
     */
    public function adicionar($datos) {
        global $sql;

        $datosItem = array();

        $datosItem['nombre']    = $datos['nombre'];
        $datosItem['id_grupo']  = $datos['id_grupo'];

        $datosItem['activo'] = (isset($datos['activo'])) ? '1' :  '0';

        $consulta = $sql->insertar('subgrupos', $datosItem);

        if ($consulta) {
            $idItem = $sql->ultimoId;
            return $idItem;
            
        } else {
            return false;
            
        }
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
                    
                    if ($datos['id'] != 0)
                        $campos['id'] = $datos['id'];   
                    
                    if ($datos['nombre'] != 0)
                        $campos['nombre'] = $datos['nombre'];
                    
                    if ($datos['id_grupo'] != 0)
                        $campos['id_grupo'] = $datos['id_grupo'];                      
                }

                $valor1    = $data->val($row, $col);
                $respuesta = array();

                while ($valor1 != null) {
                    if ($datos['inicial'] == 0) {
                        $respuesta[$col] = $valor1;
                        $col++;
                        
                    } else {
                        $datosArticulo = array(
                            'activo' => '1'
                        );

                        foreach ($campos AS $nombre => $valor) {

                            $valor = $data->val($row, $valor);
//                            if ($nombre == 'id_grupo') {
//                                $valor = $sql->obtenerValor('grupos', 'id', "nombre LIKE '%" . $valor . "%'");
//                            } 

                            $datosArticulo[$nombre] = $valor;
                        }

                        $sql->insertar('subgrupos', $datosArticulo);

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
     
    /**
     * Modificar un subgrupo
     * @param  arreglo $datos       Datos del subgrupo a modificar
     * @return lógico               Indica si el procedimiento se pudo realizar correctamente o no
     */
    public function modificar($datos) {
        global $sql;

        if (!isset($this->id)) {
            return false;
        }

        $datosItem = array();

        $datosItem['nombre']    = $datos['nombre'];
        $datosItem['id_grupo']  = $datos['id_grupo'];

        $datosItem['activo'] = (isset($datos['activo'])) ? '1' :  '0';
        
        $consulta = $sql->modificar('subgrupos', $datosItem, 'id = "' . $this->id . '"');


        if ($consulta) {
            return true;
            
        } else {
            return false;
            
        }
        
    }

    /**
     * Eliminar un subgrupo
     * @param entero $id    Código interno o identificador de un subgrupo en la base de datos
     * @return lógico       Indica si el procedimiento se pudo realizar correctamente o no
     */
    public function eliminar() {
        global $sql;

        if (!isset($this->id)) {
            return false;
        }

        $consulta = $sql->eliminar('subgrupos', 'id = "' . $this->id . '"');
        
        if (!$consulta) {
            return false;
            
        } else {
            return true;
            
        }
    }

    /**
     * Listar los subgrupos
     * @param entero  $cantidad    Número de grupos a incluir en la lista (0 = todas las entradas)
     * @param arreglo $excepcion   Arreglo con los códigos internos o identificadores a omitir en la lista
     * @param cadena  $condicion   Condición adicional (SQL)
     * @return arreglo             Lista de subgrupos
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
            $condicion .= 'sg.id NOT IN (' . $excepcion . ') AND ';
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
            'sg'    => 'subgrupos',
            'g'     => 'grupos'
        );

        $columnas = array(
            'id'            => 'sg.id',
            'nombre'        => 'sg.nombre',
            'idGrupo'       => 'sg.id_grupo',
            'nombreGrupo'   => 'g.nombre',
            'activo'        => 'sg.activo'
        );

        if (!empty($condicionGlobal)) {
            $condicion .= $condicionGlobal . ' AND ';
        }

        $condicion .= 'sg.id_grupo = g.id AND sg.id !=0';

        if (is_null($this->registrosConsulta)) {
            $sql->seleccionar($tablas, $columnas, $condicion);
            $this->registrosConsulta = $sql->filasDevueltas;
        }
        
        $consulta = $sql->seleccionar($tablas, $columnas, $condicion, 'sg.id', $orden, $inicio, $cantidad);
        
        $lista = array();
        
        if ($sql->filasDevueltas) {

            while ($objeto = $sql->filaEnObjeto($consulta)) {
                $objeto->estado = ($objeto->activo) ? HTML::frase($textos->id('ACTIVO'), 'activo') :  HTML::frase($textos->id('INACTIVO'), 'inactivo');
                
                $lista[] = $objeto;
                
            }
            
        }

        return $lista;
        
    }

    /**
     * Metodo que arma la grilla para mostrarse desde la pagina principal
     * @global type $textos
     * @param type $arregloRegistros
     * @param type $datosPaginacion
     * @return type 
     */
    public function generarTabla($arregloRegistros, $datosPaginacion = NULL) {
        global $textos;
        //Declaracion de las columnas que se van a mostrar en la tabla
        $datosTabla = array(
            HTML::parrafo($textos->id('CODIGO'), 'centrado') => 'id|sg.id',
            HTML::parrafo($textos->id('GRUPO'), 'centrado')  => 'nombreGrupo|g.nombre',
            HTML::parrafo($textos->id('NOMBRE'), 'centrado') => 'nombre|sg.nombre',
            HTML::parrafo($textos->id('ESTADO'), 'centrado') => 'estado'
        );
        //ruta a donde se mandara la accion del doble click
        $ruta = '/ajax' . $this->urlBase . '/move';

        return Recursos::generarTablaRegistros($arregloRegistros, $datosTabla, $ruta, $datosPaginacion) . HTML::crearMenuBotonDerecho('SUBGRUPOS');
    }

}
