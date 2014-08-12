<?php

/**
 * @package     FOLCS
 * @subpackage  Grupos 
 * @author      Pablo Andrés Vélez Vidal 
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2012 Genesys Soft.
 * @version     0.2
 * 
 * Grupos en los cuales se categorizan o dividen los subgrupos del sistema.
 * 
 * Clase encargada de gestionar la información del listado de grupos existentes en el sistema. En este módulo se pueden
 * agregar, consultar, eliminar o modificar la información de los grupos. Este modulo se relaciona con el modulo de subgrupos
 * ya que engloba estos registros, es decir, un Grupo tiene 1 o muchos Subgrupos. Esto se utiliza normalmente para categorizar
 * los registros de los articulos.
 * 
 * tabla principal: grupos.
 * 
 * */
class Grupo {

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
     * Tipo de grupo, inventario o miscelanea
     * @var entero
     */
    public $tipo;

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
     * Inicializar un grupo
     * @param entero $id Código interno o identificador del grupo en la base de datos
     */
    public function __construct($id = NULL) {
        global $sql, $modulo;
        $this->urlBase = '/' . $modulo->url;
        $this->url = $modulo->url;
        $this->idModulo = $modulo->id;
        $this->tabla = $modulo->tabla;
        //Saber el numero de registros
        $this->registros = $sql->obtenerValor('grupos', 'COUNT(id)', 'id != "0" ');
        //Saber el numero de registros activos
        $this->registrosActivos = $sql->obtenerValor('grupos', 'COUNT(id)', 'activo = "1" ');
        //establecer el valor del campo predeterminado para organizar los listados
        $this->ordenInicial = 'nombre';

        if (isset($id)) {
            $this->cargar($id);
        }
    }


    /**
     * Cargar los datos de un grupo
     * @param entero $id Código interno o identificador del grupo en la base de datos
     */
    public function cargar($id) {
        global $sql;

        if (isset($id) && $sql->existeItem('grupos', 'id', intval($id))) {

            $tablas = array(
                'g' => 'grupos'
            );

            $columnas = array(
                'id' => 'g.id',
                'nombre' => 'g.nombre',
                'tipo' => 'g.tipo',
                'activo' => 'g.activo'
            );

            $condicion = 'g.id = "' . $id . '"';

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
     * Adicionar un grupo
     * @param  arreglo $datos       Datos del grupo a adicionar
     * @return entero               Código interno o identificador del grupo en la base de datos (NULL si hubo error)
     */
    public function adicionar($datos) {
        global $sql;

        $datosItem = array();

        $datosItem['nombre'] = $datos['nombre'];
        $datosItem['tipo'] = $datos['tipo'];

        if (isset($datos['activo'])) {
            $datosItem['activo'] = '1';
        } else {
            $datosItem['activo'] = '0';
        }

        $consulta = $sql->insertar('grupos', $datosItem);

        if ($consulta) {
            $idItem = $sql->ultimoId;
            return $idItem;
        } else {
            return NULL;
        }//fin del if($consulta)
    }


    /**
     * Modificar un grupo
     * @param  arreglo $datos       Datos del grupo a modificar
     * @return lógico               Indica si el procedimiento se pudo realizar correctamente o no
     */
    public function modificar($datos) {
        global $sql;

        if (!isset($this->id)) {
            return NULL;
        }

        $datosItem = array();

        $datosItem['nombre'] = $datos['nombre'];
        $datosItem['tipo'] = $datos['tipo'];

        if (isset($datos['activo'])) {
            $datosItem['activo'] = '1';
        } else {
            $datosItem['activo'] = '0';
        }
        //$sql->depurar = true;
        $consulta = $sql->modificar('grupos', $datosItem, 'id = "' . $this->id . '" ');


        if ($consulta) {
            return 1;
        } else {
            return NULL;
        }//fin del if(consulta)
    }


    /**
     * Eliminar un grupo
     * @param entero $id    Código interno o identificador de un grupo en la base de datos
     * @return lógico       Indica si el procedimiento se pudo realizar correctamente o no
     */
    public function eliminar() {
        global $sql, $configuracion, $textos;

        //arreglo que será devuelto como respuesta
        $respuestaEliminar = array(
            'respuesta' => false,
            'mensaje'   => $textos->id('ERROR_DESCONOCIDO'),
        );
        
        if (!isset($this->id)) {
            return $respuestaEliminar;
        }
        
        //hago la validacion de la integridad referencial
        $arreglo1          = array('subgrupos', 'id_grupo = "'.$this->id.'"', $textos->id('SUBGRUPOS'));//arreglo del que sale la info a consultar
        $arregloIntegridad = array($arreglo1);//arreglo de arreglos para realizar las consultas de integridad referencial, (ver documentacion de metodo)
        $integridad        = Recursos::verificarIntegridad($textos->id('GRUPO'), $arregloIntegridad);

        /**
         * si hay problemas con la integridad referencial, la variable integridad tiene como valor,
         * un texto diciendo que tabla contiene n cantidad de relaciones con esta
         */
        if ($integridad != "") {
            $respuestaEliminar['mensaje'] = $integridad;
            return $respuestaEliminar;
        }
              
        $sql->iniciarTransaccion();
        $consulta = $sql->eliminar('grupos', 'id = "' . $this->id . '"');
        
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
     * Listar los grupos
     * @param entero  $cantidad    Número de grupos a incluir en la lista (0 = todas las entradas)
     * @param arreglo $excepcion   Arreglo con los códigos internos o identificadores a omitir en la lista
     * @param cadena  $condicion   Condición adicional (SQL)
     * @return arreglo             Lista de grupos
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
            $condicion .= 'g.id NOT IN (' . $excepcion . ')';
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
            'g' => 'grupos'
        );

        $columnas = array(
            'id' => 'g.id',
            'nombre' => 'g.nombre',
            'tipo' => 'g.tipo',
            'activo' => 'g.activo'
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
        //$sql->depurar = true;
        $consulta = $sql->seleccionar($tablas, $columnas, $condicion, 'g.id', $orden, $inicio, $cantidad);
        
        if ($sql->filasDevueltas) {
            $lista = array();

            while ($objeto = $sql->filaEnObjeto($consulta)) {
                $objeto->url = $this->urlBase . '/' . $objeto->id;
                $objeto->idModulo = $this->idModulo;
                if ($objeto->activo) {
                    $objeto->estado = HTML::frase($textos->id('ACTIVO'), 'activo');
                } else {
                    $objeto->estado = HTML::frase($textos->id('INACTIVO'), 'inactivo');
                }
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
            HTML::parrafo($textos->id('CODIGO'), 'centrado') => 'id|g.id',
            HTML::parrafo($textos->id('NOMBRE'), 'centrado') => 'nombre|g.nombre',
            HTML::parrafo($textos->id('TIPO'), 'centrado') => 'tipo|g.tipo',
            HTML::parrafo($textos->id('ESTADO'), 'centrado') => 'estado'
        );
        //ruta a donde se mandara la accion del doble click
        $ruta = '/ajax' . $this->urlBase . '/move';

        return Recursos::generarTablaRegistros($arregloRegistros, $datosTabla, $ruta, $datosPaginacion) . HTML::crearMenuBotonDerecho('GRUPOS');
    }

}