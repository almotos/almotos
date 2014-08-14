<?php

/**
 *
 * @package     FOLCS
 * @subpackage  Lineas 
 * @author      Pablo Andrés Vélez Vidal 
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2012 Genesys Soft.
 * @version     0.2
 * 
 * Lineas utilizados en el sistema
 * 
 * */
class Linea {

    /**
     * Código interno o identificador de la unidad en la base de datos
     * @var entero
     */
    public $id;

    /**
     * URL relativa del módulo de unidades
     * @var cadena
     */
    public $urlBase;

    /**
     * URL relativa del módulo de unidades
     * @var cadena
     */
    public $url;

    /**
     * Código interno o identificador del modulo
     * @var entero
     */
    public $idModulo;

    /**
     * Tabla principal a la que va relacionada el modulo
     * @var entero
     */
    public $tabla;

    /**
     * Nombre de la unidad
     * @var entero
     */
    public $nombre;

    /**
     * Nombre de la unidad
     * @var entero
     */
    public $idImagen;

    /**
     * objeto imagen
     * @var cadena
     */
    public $imagen; 

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
     * Número de registros activos de la lista 
     * @var entero
     */
    public $registrosConsulta = 0;

    /**
     * Orden predeterminado para organizar los listados
     * @var entero
     */
    public $ordenInicial = NULL;

    /**
     * Inicializar una linea
     * @param entero $id Código interno o identificador del linea en la base de datos
     */
    public function __construct($id = NULL) {
        global $sql, $modulo;
        
        $this->urlBase      = '/' . $modulo->url;
        $this->url          = $modulo->url;
        $this->idModulo     = $modulo->id;
        $this->tabla        = $modulo->tabla;
        //Saber el numero de registros
        $this->registros        = $sql->obtenerValor('lineas', 'COUNT(id)', 'id != "0"');
        //Saber el numero de registros activos
        $this->registrosActivos = $sql->obtenerValor('lineas', 'COUNT(id)', 'activo = "1"');
        //establecer el valor del campo predeterminado para organizar los listados
        $this->ordenInicial     = 'nombre';

        if (!empty($id)) {
            $this->cargar($id);
        }
        
    }

    /**
     *
     * Cargar los datos de una linea
     *
     * @param entero $id Código interno o identificador de la linea en la base de datos
     *
     */
    public function cargar($id) {
        global $sql;

        if (!empty($id) && $sql->existeItem('lineas', 'id', intval($id))) {

            $tablas = array(
                'l' => 'lineas',
                'i' => 'imagenes'
            );

            $columnas = array(
                'id'        => 'l.id',
                'nombre'    => 'l.nombre',
                'idImagen'  => 'l.id_imagen',
                'imagen'    => 'i.ruta',
                'activo'    => 'l.activo'
            );

            $condicion = 'l.id_imagen = i.id AND l.id = "'.$id.'"';

            $consulta = $sql->seleccionar($tablas, $columnas, $condicion);

            if ($sql->filasDevueltas) {
                $fila = $sql->filaEnObjeto($consulta);

                foreach ($fila as $propiedad => $valor) {
                    $this->$propiedad = $valor;
                }

                $this->url = $this->urlBase . '/' . $this->id;
                
                $this->imagen = new Imagen($this->idImagen);
                
            }
            
        }
        
    }


    /**
     * Adicionar una linea
     * @param  arreglo $datos       Datos de la linea a adicionar
     * @return entero               Código interno o identificador del linea en la base de datos (NULL si hubo error)
     */
    public function adicionar($datos) {
        global $sql, $archivo_imagen;

        $datosItem = array();

        $idImagen = '0';
        
        $sql->iniciarTransaccion();

        if (isset($archivo_imagen) && !empty($archivo_imagen['tmp_name'])) {
            $imagen = new Imagen();
            
            $datosImagen = array(
                'titulo'        => 'imagen_linea'
            );

            $idImagen = $imagen->adicionar($datosImagen);
            
            if (!$idImagen) {
                $sql->cancelarTransaccion();
            }
            
        }

        $datosItem['nombre']    = $datos['nombre'];
        $datosItem['id_imagen'] = $idImagen;

        $datosItem['activo'] = (isset($datos['activo'])) ? '1' : '0';

        $consulta = $sql->insertar('lineas', $datosItem);

        if ($consulta) {
            $sql->finalizarTransaccion();
            $idItem = $sql->ultimoId;
            return $idItem;
            
        } else {
            $sql->cancelarTransaccion();
            return false;
            
        }
        
    }

    /**
     * Modificar una linea
     * @param  arreglo $datos       Datos del linea a modificar
     * @return lógico               Indica si el procedimiento se pudo realizar correctamente o no
     */
    public function modificar($datos) {
        global $sql, $archivo_imagen;

        if (!isset($this->id)) {
            return NULL;
        }

        $sql->iniciarTransaccion();
        
        $datosItem  = array();

        $idImagen   = $this->idImagen;

        if (isset($archivo_imagen) && !empty($archivo_imagen['tmp_name'])) {
            $imagen = new Imagen($this->idImagen);
            
            $elimina = $imagen->eliminar();

            if (!$elimina) {
                $sql->cancelarTransaccion();

            }
            
            $datosImagen = array(
                'titulo' => 'imagen_linea'
            );

            $idImagen = $imagen->adicionar($datosImagen);
            
            if (!$idImagen) {
                $sql->cancelarTransaccion();
            }
            
        }

        $datosItem['nombre']    = $datos['nombre'];
        $datosItem['id_imagen'] = $idImagen;

        $datosItem['activo'] =  (isset($datos['activo'])) ? '1' : '0';

        //$sql->depurar = true;
        $consulta = $sql->modificar('lineas', $datosItem, 'id = "' . $this->id . '"');

        if ($consulta) {
            $sql->finalizarTransaccion();
            return true;
            
        } else {
            $sql->cancelarTransaccion();
            return false;
            
        }
        
    }

    /**
     * Eliminar una linea
     * @param entero $id    Código interno o identificador de una linea en la base de datos
     * @return lógico       Indica si el procedimiento se pudo realizar correctamente o no
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
        $arreglo1           = array('articulos', 'id_linea = "'.$this->id.'"', $textos->id('ARTICULOS'));//arreglo del que sale la info a consultar
        $arregloIntegridad  = array($arreglo1);//arreglo de arreglos para realizar las consultas de integridad referencial, (ver documentacion de metodo)
        $integridad         = Recursos::verificarIntegridad($textos->id('LINEA'), $arregloIntegridad);  

        /**
         * si hay problemas con la integridad referencial, la variable integridad tiene como valor,
         * un texto diciendo que tabla contiene n cantidad de relaciones con esta
         */
        if ($integridad != "") {
            $respuestaEliminar['mensaje'] = $integridad;
            return $respuestaEliminar;
        }
              
        $sql->iniciarTransaccion();
        $consulta = $sql->eliminar('lineas', 'id = "' . $this->id . '"');
     
        if ($consulta) {
            $imagen         = new Imagen($this->idImagen); 
            $eliminarImagen = $imagen->eliminar;
            
            if($eliminarImagen === false){
                $sql->cancelarTransaccion("Fallo en el archivo " . __FILE__ . " en la linea " .  __LINE__);
                return $respuestaEliminar;
                
            }  
            
            $sql->finalizarTransaccion();
            //todo salió bien, se envia la respuesta positiva
            $respuestaEliminar['respuesta'] = true;
            return $respuestaEliminar;
            
        } else {
            $sql->cancelarTransaccion("Fallo en el archivo " . __FILE__ . " en la linea " .  __LINE__);
            return $respuestaEliminar;
            
        }
     
    }

    /**
     * Listar las lineas
     * 
     * @param entero  $cantidad    Número de lineas a incluir en la lista (0 = todas las entradas)
     * @param arreglo $excepcion   Arreglo con los códigos internos o identificadores a omitir en la lista
     * @param cadena  $condicion   Condición adicional (SQL)
     * @return arreglo             Lista de lineas
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
            $condicion .= 'l.id NOT IN ('.$excepcion.') AND ';
        }

        /*         * * Definir el orden de presentación de los datos ** */
        if (empty($orden)) {
            $orden = $this->ordenInicial;
        }
        
        if ($this->listaAscendente) {
            $orden = $orden .' ASC';
        } else {
            $orden = $orden .' DESC';
        }


        $tablas = array(
            'l' => 'lineas',
            'i' => 'imagenes'
        );

        $columnas = array(
            'id'        => 'l.id',
            'nombre'    => 'l.nombre',
            'idImagen'  => 'l.id_imagen',
            'imagen'    => 'i.ruta',
            'activo'    => 'l.activo'
        );

        if (!empty($condicionGlobal)) {
            $condicion .= $condicionGlobal . ' AND ';
        }

        $condicion .= 'l.id_imagen = i.id';


        if (empty($this->registrosConsulta)) {
            $sql->seleccionar($tablas, $columnas, $condicion);
            $this->registrosConsulta = $sql->filasDevueltas;
        }
//        $sql->depurar = true;
        $consulta = $sql->seleccionar($tablas, $columnas, $condicion, 'l.id', $orden, $inicio, $cantidad);
        
        if ($sql->filasDevueltas) {
            $lista = array();

            while ($objeto = $sql->filaEnObjeto($consulta)) {
                $objeto->estado = ($objeto->activo) ? HTML::frase($textos->id('ACTIVO'), 'activo'): $objeto->estado = HTML::frase($textos->id('INACTIVO'), 'inactivo');

                $lista[] = $objeto;
            }
        }

        return $lista;
    }


    public function generarTabla($arregloRegistros, $datosPaginacion = NULL) {
        global $textos;

        //Declaracion de las columnas que se van a mostrar en la tabla
        $datosTabla = array(
            HTML::parrafo($textos->id('CODIGO'), 'centrado') => 'id|l.id',
            HTML::parrafo($textos->id('NOMBRE'), 'centrado') => 'nombre|l.nombre',
            HTML::parrafo($textos->id('ESTADO'), 'centrado') => 'estado'
        );
        //ruta a donde se mandara la accion del doble click
        $ruta = '/ajax' . $this->urlBase . '/move';

        return Recursos::generarTablaRegistros($arregloRegistros, $datosTabla, $ruta, $datosPaginacion) . HTML::crearMenuBotonDerecho('LINEAS');
    }
    
    
  
    /**
     * Adicionar lineas a partir de un archivo excel
     *
     * @param  arreglo $datos       Datos de la linea a adicionar
     * @return entero               Código interno o identificador de la linea en la base de datos (NULL si hubo error)
     */
    public function adicionarMasivo($datos) {
        global $sql, $configuracion, $archivo_masivo;

        if (empty($archivo_masivo['tmp_name'])) {
            return false;
            
        } else {
            
            $validarFormato = Archivo::validarArchivo($archivo_masivo, array('xls'));
            
            if (!$validarFormato) {
                $configuracionRuta  = $configuracion['RUTAS']['media'] . "/" . $configuracion["RUTAS"]["documentos"];
                $recurso            = Archivo::subirArchivoAlServidor($archivo_masivo, $configuracionRuta);

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
                    
                }

                $valor1     = $data->val($row, $col);
                $respuesta  = array();

                while ($valor1 != null) {

                    if ($datos['inicial'] == 0) {
                        $respuesta[$col] = $valor1;
                        $col++;
                        
                    } else {
                        $datosLinea = array(
                            'activo' => '1'
                        );

                        foreach ($campos AS $nombre => $valor) {
                            $valor                  = $data->val($row, $valor);
                            $datosLinea[$nombre]    = $valor;
                            
                        }
                        $sql->depurar = true;
                        $sql->insertar('lineas', $datosLinea);

                        $row++;
                        $respuesta = true;
                    }
                    $valor1 = $data->val($row, $col);
                }
                
                Archivo::eliminarArchivoDelServidor($configuracionRuta . '/' . $recurso);
                
                return $respuesta;
                
            } else {
                return NULL;
                
            }
            
        }
        
    }    
 

}
