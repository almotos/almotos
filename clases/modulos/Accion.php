<?php

/**
 *
 * @package     FOM
 * @subpackage  Acciones
 * @author      Pablo Andrés Vélez Vidal <pavelez@genesyscorp.com.co>
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2012 Genesys Corporation.
 * @version     0.2
 * 
 * Módulo encargado de la gestión de las acciones o "botones" de cada uno de los módulos, es decir, todos y cada uno de los botones
 * que se encuentran en los módulos deben ser registrados en este modulo para que estos registros posteriormente sean utilizados cuando
 * se otorgue privilegios a un determinado usuario sobre un determinado modulo. Ejemplo: en el módulo "Países" existen los botones: "Adicionar,
 * Consultar, Modificar y Eliminar", cada uno de estos botones deben estar registrados en el módulo "Acciones" ingresando 1= a que  módulo pertenece,
 * 2= el nombre de la acción, 3= el nombre de la acción para el menú de privilegios.
 *
 * */
class Accion {

    /**
     * Código interno o identificador del usuario de los privilegios en la base de datos
     * @var entero
     */
    public $id;

    /**
     * URL relativa del módulo de acciones
     * @var cadena
     */
    public $urlBase;

    /**
     * URL relativa del acciones específico
     * @var cadena
     */
    public $url;

    /**
     * Nombre de la acción
     * @var cadena
     */
    public $nombre;

    /**
     * Nombre de la acción
     * @var cadena
     */
    public $nombreMenu;

    /**
     * Nombre de la acción
     * @var cadena
     */
    public $nombreModulo;

    /**
     * Indicador del orden cronológio de la lista de acciones
     * @var lógico
     */
    public $listaAscendente = true;

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
     * Inicializar las acciones
     * @param entero $id Código interno o identificador de las acciones en la base de datos
     */
    public function __construct($id = NULL) {
        global $sql;

        $this->urlBase  = '/acciones';
        $this->url      = 'acciones';

        //Saber el numero de registros
        $this->registros        = $sql->obtenerValor('componentes_modulos', 'COUNT(id)', 'id != "0"');
        //Saber el numero de registros activos
        $this->registrosActivos = $this->registros;
        //establecer el valor del campo predeterminado para organizar los listados
        $this->ordenInicial     = 'm.nombre';

        if (isset($id)) {
            $this->cargar($id);
        }
    }

    /**
     * Cargar los datos de las acciones
     * @param entero $id Código interno o identificador del perfil en la base de datos
     */
    public function cargar($id) {
        global $sql;

        if (isset($id) && $sql->existeItem('componentes_modulos', 'id', intval($id))) {

            $tablas = array(
                'cm'    => 'componentes_modulos',
                'm'     => 'modulos',
            );

            $columnas = array(
                'id'            => 'cm.id',
                'nombreModulo'  => 'm.nombre',
                'nombre'        => 'cm.componente',
                'nombreMenu'    => 'cm.nombre'
            );

            $condicion .= 'cm.id != 0 AND cm.id_modulo = m.id AND cm.id = "' . $id . '"';

            $consulta = $sql->seleccionar($tablas, $columnas, $condicion);

            if ($sql->filasDevueltas) {
                $fila = $sql->filaEnObjeto($consulta);
                foreach ($fila as $propiedad => $valor) {
                    $this->$propiedad = $valor;
                    
                }
                
            }
            
        }
        
    }

    /**
     * Adicionar la acción en la tabla de componentes modulos
     * @param  arreglo $datos       Datos de la acción a adicionar
     * @return entero               Código interno o identificador del perfil en la base de datos (NULL si hubo error)
     */
    public function adicionar($datos) {
        global $sql;

        $datos_insertar = array(
            'id_modulo'     => $datos['componente'],
            'componente'    => $datos['nombre'],
            'nombre'        => $datos['nombre_menu']
        );

        $consulta = $sql->insertar('componentes_modulos', $datos_insertar);

        if ($consulta) {
            $idItem = $sql->ultimoId;
            return $idItem;
            
        } else {
            return NULL;
            
        }
        
    }

    /**
     * Modificar los datos de la acción
     * @param  arreglo $datos       Datos de la acción a modificar
     * @return lógico               Indica si el procedimiento se pudo realizar correctamente o no
     */
    public function modificar($datos) {
        global $sql;

        if (!isset($this->id)) {
            return NULL;
        }

        $datos_modificar = array(
            'id_modulo'     => $datos['componente'],
            'componente'    => $datos['nombre'],
            'nombre'        => $datos['nombre_menu']
        );

        $consulta = $sql->modificar('componentes_modulos', $datos_modificar, 'id = "' . $this->id . '"');

        if ($consulta) {
            return true;
            
        } else {
            return NULL;
            
        }
        
    }

    /**
     * Eliminar la accion
     * @return lógico Indica si el procedimiento se pudo realizar correctamente o no
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
        $arreglo1          = array('permisos_componentes_perfiles',   'id_componente = "'.$this->id.'"', $textos->id('PERMISOS_COMPONENTES_PERFILES'));//arreglo del que sale la info a consultar
        $arreglo2          = array('permisos_componentes_usuarios',   'id_componente = "'.$this->id.'"', $textos->id('PERMISOS_COMPONENTES_USUARIOS'));//arreglo del que sale la info a consultar
        $arregloIntegridad = array($arreglo1, $arreglo2);//arreglo de arreglos para realizar las consultas de integridad referencial, (ver documentacion de metodo)
        $integridad        = Recursos::verificarIntegridad($textos->id('ACCION'), $arregloIntegridad);

        /**
         * si hay problemas con la integridad referencial, la variable integridad tiene como valor,
         * un texto diciendo que tabla contiene n cantidad de relaciones con esta
         */
        if ($integridad != "") {
            $respuestaEliminar['mensaje'] = $integridad;
            return $respuestaEliminar;
        }
              
        $sql->iniciarTransaccion();
        $consulta = $sql->eliminar('componentes_modulos', 'id = "' . $this->id . '"');
        
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
     * Listar las acciones de la tabla componentes modulos
     *
     * @param entero  $cantidad Número de usuarios a incluir en la lista (0 = todas las entradas)
     * @return arreglo Listar las acciones de la tabla componentes modulos
     *
     */
    public function listar($inicio = 0, $cantidad = 0, $excepcion = NULL, $condicionGlobal = NULL, $orden = NULL) {
        global $sql;

        /*         * * Validar la fila inicial de la consulta ** */
        if (!is_int($inicio) || $inicio < 0) {
            $inicio = 0;
        }

        /*         * * Validar la cantidad de registros requeridos en la consulta ** */
        if (!is_int($cantidad) || $cantidad <= 0) {
            $cantidad = 0;
        }

        /*         * * Validar que la excepción sea un arreglo y contenga elementos ** */
        if (isset($excepcion) && is_array($excepcion) && count($excepcion)) {
            $excepcion = implode(',', $excepcion);
            $condicion .= 'cm.id NOT IN (' . $excepcion . ') AND ';
        }

        /*         * * Definir el orden de presentación de los datos ** */
        if (!isset($orden)) {
            $orden = $this->ordenInicial;
        }
        
        if ($this->listaAscendente) {
            $orden = $orden . ' DESC';
            
        } else {
            $orden = $orden . ' ASC';
            
        }

        $tablas = array(
            'cm'    => 'componentes_modulos',
            'm'     => 'modulos',
        );

        $columnas = array(
            'id'            => 'cm.id',
            'modulo'        => 'm.nombre',
            'componente'    => 'cm.componente',
            'nombreMenu'    => 'cm.nombre'
        );

        if (!empty($condicionGlobal)) {
            $condicion .= $condicionGlobal . ' AND ';
        }

        $condicion .= 'cm.id != 0 AND cm.id_modulo = m.id';

        if (is_null($this->registrosConsulta)) {
            $sql->seleccionar($tablas, $columnas, $condicion);
            $this->registrosConsulta = $sql->filasDevueltas;
        }

        $agrupamiento = '';

        //$sql->depurar = true;
        $consulta = $sql->seleccionar($tablas, $columnas, $condicion, $agrupamiento, $orden, $inicio, $cantidad);
        
        if ($sql->filasDevueltas) {
            $lista = array();
            while ($acciones = $sql->filaEnObjeto($consulta)) {
                $lista[] = $acciones;
                
            }
            
        }
        return $lista;
        
    }

    /**
     * Metodo que genera los datos que contendra la tabla principal del modulo
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
            HTML::parrafo($textos->id('NOMBRE_MODULO'), 'centrado')            => 'modulo|m.nombre',
            HTML::parrafo($textos->id('NOMBRE_BOTON'), 'centrado')      => 'componente|cm.componente',
            HTML::parrafo($textos->id('NOMBRE_BOTON_MENU'), 'centrado') => 'nombreMenu|cm.nombre',
        );
        //ruta del metodo paginador
        $rutaPaginador = '/ajax' . $this->urlBase . '/move';

        return Recursos::generarTablaRegistros($arregloRegistros, $datosTabla, $rutaPaginador, $datosPaginacion) . HTML::crearMenuBotonDerecho('ACCIONES');
    }

}
