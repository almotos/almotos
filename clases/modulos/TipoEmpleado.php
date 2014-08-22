<?php

/**
 *
 * @package FOM
 * @subpackage Tipos de Empleados
 * @author Pablo Andrés Vélez Vidal <pavelez8@misena.edu.co>
 * @license http://www.gnu.org/licenses/gpl.txt
 * @copyright Copyright (c) 2012 Genesys corporation.
 * @version 0.2
 *
 * Tipos de Empleaods utilizados en el sistema, pensando en un futuro pequeño modulo
 * de recursos humanos
 * */

class TipoEmpleado {

    /**
     * Código interno o identificador del tipo de empleado en la base de datos
     * @var entero
     */
    public $id;

    /**
     * URL relativa del módulo de tipos empleado
     * @var cadena
     */
    public $urlBase;

    /**
     * URL relativa del módulo de tipos empleado
     * @var cadena
     */
    public $url;

    /**
     * Código interno o identificador del modulo
     * @var entero
     */
    public $idModulo;

    /**
     * Nombre de la TipoEmpleado
     * @var entero
     */
    public $nombre;

    /**
     * Número de registros de la lista
     * @var entero
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
     * Número de registros activos de la lista de foros
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
     *
     * Inicializar de la TipoEmpleado
     *
     * @param entero $id Código interno o identificador de la TipoEmpleado en la base de datos
     *
     */
    public function __construct($id = NULL) {
        global $sql, $modulo;
        $this->urlBase = '/' . $modulo->url;
        $this->url = $modulo->url;
        $this->idModulo = $modulo->id;
        //Saber el numero de registros
        $this->registros = $sql->obtenerValor('tipos_empleado', 'COUNT(id)', 'id != "0"');
        //Saber el numero de registros activos
        $this->registrosActivos = $sql->obtenerValor('tipos_empleado', 'COUNT(id)', 'activo = "1"');
        //establecer el valor del campo predeterminado para organizar los listados
        $this->ordenInicial = 'nombre';

        if (isset($id)) {
            $this->cargar($id);
        }
    }

//Fin del metodo constructor

    /**
     *
     * Cargar los datos de un tipo de TipoEmpleado
     *
     * @param entero $id Código interno o identificador del tipo de TipoEmpleado en la base de datos
     *
     */
    public function cargar($id) {
        global $sql;

        if (isset($id) && $sql->existeItem('tipos_empleado', 'id', intval($id))) {

            $tablas = array(
                'te' => 'tipos_empleado'
            );

            $columnas = array(
                'id' => 'te.id',
                'nombre' => 'te.nombre',
                'activo' => 'te.activo'
            );

            $condicion = 'te.id = "'.$id.'"';

            $consulta = $sql->seleccionar($tablas, $columnas, $condicion);

            if ($sql->filasDevueltas) {
                $fila = $sql->filaEnObjeto($consulta);

                foreach ($fila as $propiedad => $valor) {
                    $this->$propiedad = $valor;
                }
            }
        }
    }

//Fin del metodo Cargar

    /**
     *
     * Adicionar un tipo de Empleado
     *
     * @param arreglo $datos Datos del tipo de Empleado a adicionar
     * @return entero Código interno o identificador del tipo Empleado en la base de datos (NULL si hubo error)
     *
     */
    public function adicionar($datos) {
        global $sql;

        $datosTipoEmpleado = array();

        $datosTipoEmpleado['nombre'] = $datos['nombre'];

        if (isset($datos['activo'])) {
            $datosTipoEmpleado['activo'] = '1';
        } else {
            $datosTipoEmpleado['activo'] = '0';
        }

        $consulta = $sql->insertar('tipos_empleado', $datosTipoEmpleado);

        if ($consulta) {
            $idItem = $sql->ultimoId;
            return $idItem;
        } else {
            return NULL;
        }//fin del if($consulta)
    }

//fin del metodo adicionar tipos de Empleadoes

    /**
     * Modificar un tipo de Empleado
     *
     * @param arreglo $datos Datos del tipo de Empleado a modificar
     * @return lógico Indica si el procedimiento se pudo realizar correctamente o no
     *
     */
    public function modificar($datos) {
        global $sql;

        if (!isset($this->id)) {
            return NULL;
        }
        $datosTipoEmpleado = array();

        $datosTipoEmpleado['nombre'] = $datos['nombre'];

        if (isset($datos['activo'])) {
            $datosTipoEmpleado['activo'] = '1';
        } else {
            $datosTipoEmpleado['activo'] = '0';
        }
        //$sql->depurar = true;
        $consulta = $sql->modificar('tipos_empleado', $datosTipoEmpleado, 'id = "' . $this->id . '"');


        if ($consulta) {
            return $this->id;
        } else {
            return NULL;
        }//fin del if(consulta)
    }

//fin del metodo Modificar

    /**
     *
     * Eliminar un tipo de Empleado
     *
     * @param entero $id Código interno o identificador de una Empleado en la base de datos
     * @return lógico Indica si el procedimiento se pudo realizar correctamente o no
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
        $arreglo1 = array('empleados', 'id_tipo_empleado = "'.$this->id.'"', $textos->id('EMPLEADOS'));//arreglo del que sale la info a consultar
        $arregloIntegridad = array($arreglo1);//arreglo de arreglos para realizar las consultas de integridad referencial, (ver documentacion de metodo)
        $integridad = Recursos::verificarIntegridad($textos->id('TIPO_DE_EMPLEADO'), $arregloIntegridad);
        /**
         * si hay problemas con la integridad referencial, la variable integridad tiene como valor,
         * un texto diciendo que tabla contiene n cantidad de relaciones con esta
         */
        if ($integridad != "") {
            $respuestaEliminar['mensaje'] = $integridad;
            return $respuestaEliminar;
            
        }
              
        $sql->iniciarTransaccion();
        $consulta = $sql->eliminar('tipos_empleado', 'id = "' . $this->id . '"');
        
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
     * Listar los tipos de empleado
     *
     * @param entero $cantidad Número de tipos de empleado a incluir en la lista (0 = todas las entradas)
     * @param arreglo $excepcion Arreglo con los códigos internos o identificadores a omitir en la lista
     * @param cadena $condicion Condición adicional (SQL)
     * @return arreglo Lista de tipos de empleado
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
            $condicion = 'te.id NOT IN ('.$excepcion.')';
        }

        /*         * * Definir el orden de presentación de los datos ** */
        if (!isset($orden)) {
            $orden = $this->ordenInicial;
        }
        if ($this->listaAscendente) {
            $orden = $orden.' ASC';
        } else {
            $orden = $orden.' DESC';
        }


        $tablas = array(
            'te' => 'tipos_empleado'
        );

        $columnas = array(
            'id' => 'te.id',
            'nombre' => 'te.nombre',
            'activo' => 'te.activo'
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

        $consulta = $sql->seleccionar($tablas, $columnas, $condicion, 'te.id', $orden, $inicio, $cantidad);
        // echo $sql->sentenciaSql;
        if ($sql->filasDevueltas) {
            $lista = array();

            while ($objeto = $sql->filaEnObjeto($consulta)) {
                $objeto->url = $this->urlBase . '/' . $objeto->id;
                $objeto->idModulo = $this->idModulo;
                $objeto->estado = ($objeto->activo) ? HTML::frase($textos->id('ACTIVO'), 'activo') : HTML::frase($textos->id('INACTIVO'), 'inactivo');
                    
                $lista[] = $objeto;
            }
        }

        return $lista;
    }

/**
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
            HTML::parrafo($textos->id('NOMBRE'), 'centrado') => 'nombre|te.nombre',
            HTML::parrafo($textos->id('ESTADO'), 'centrado') => 'estado'
        );
        //ruta a donde se mandara la accion del doble click
        $ruta = '/ajax' . $this->urlBase . '/move';

        $tabla = Recursos::generarTablaRegistros($arregloRegistros, $datosTabla, $ruta, $datosPaginacion);
        $menuDerecho = HTML::crearMenuBotonDerecho('TIPOS_EMPLEADO');

        return $tabla . $menuDerecho;
    }

}