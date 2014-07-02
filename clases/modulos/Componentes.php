<?php

/**
 *
 * @package     FOLCS
 * @subpackage  Componentes
 * @author      Francisco J. Lozano c. <fjlozano@felinux.com.co>
 * @author      Julian A. Mondragón <jmondragon@felinux.com.co>
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2009 FELINUX LTDA
 * @version     0.1
 *
 * Clase encargada de gestionar la información de los modulos que componen el sistema. En este módulo se pueden
 * agregar, consultar, eliminar o modificar la información de los modulos. Se puede determinar si el modulo va a ser
 * un modulo de control (por ejemplo, un modulo que va a contener otros varios modulos adentro como "procesos" o "datos configuracion"), o
 * un modulo funcional (por ejemplo "Articulos" o "Catálogos").
 * 
 * */
class Componentes {

    /**
     * Código interno o identificador del usuario de los privilegios en la base de datos
     * @var entero
     */
    public $id;

    /**
     * URL relativa del módulo de componentes
     * @var cadena
     */
    public $urlBase;

    /**
     * URL relativa del componentes específico
     * @var cadena
     */
    public $url;

    /**
     * Nombre del componente
     * @var cadena
     */
    public $nombre;

    /**
     * Nombre del componente nque aparece en el menu
     * @var cadena
     */
    public $nombreMenu;

    /**
     * identificador del padre del componente
     * @var cadena
     */
    public $idPadre;

    /**
     * Nombre del padre del componente
     * @var cadena
     */
    public $padre;
    
    /**
     * Documentación breve del funcioamiento del modulo
     * @var cadena
     */
    public $documentacion;    
    
    /**
     * Arreglo que contiene las acciones o "botones" existentes en el módulo
     * 
     * @var arraeglo
     */
    public $listaAcciones = array();
    
    /**
     * Numero (entero) que representa la cantidad de acciones (botones) que toiene el módulo
     * 
     * @var entero
     */
    public $numeroAcciones;    

    /**
     * Indicador del orden cronológio de la lista de componentes
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
     * Inicializar los componentes
     *
     * @param entero $id Código interno o identificador de los componentes en la base de datos
     *
     */
    public function __construct($id = NULL) {
        global $sql, $modulo;
        $this->urlBase          = '/' . $modulo->url;
        $this->url              = $modulo->url;
        //Saber el numero de registros
        $this->registros        = $sql->obtenerValor('modulos', 'COUNT(id)', 'id != "0"');
        //Saber el numero de registros activos
        $this->registrosActivos = $sql->obtenerValor('modulos', 'COUNT(id)', 'id != "0"');
        //establecer el valor del campo predeterminado para organizar los listados
        $this->ordenInicial     = 'nombre';

        if (isset($id)) {
            $this->cargar($id);
            
        }
        
    }

    /**
     *
     * Cargar los datos de los componentes
     *
     * @param entero $id Código interno o identificador del perfil en la base de datos
     *
     */
    public function cargar($id) {
        global $sql;

        if (isset($id) && $sql->existeItem('modulos', 'id', intval($id))) {

            $tablas = array(
                'm1' => 'modulos',
                'm2' => 'modulos'
            );

            $columnas = array(
                'id'            => 'm1.id',
                'nombre'        => 'm1.nombre',
                'nombreMenu'    => 'm1.nombre_menu',
                'idPadre'       => 'm1.id_padre',
                'padre'         => 'm2.nombre_menu',
                'orden'         => 'm1.orden',
                'documentacion' => 'm1.documentacion'
            );


            $condicion .= 'm1.id_padre = m2.id AND m1.id = "' . $id . '"';

            $consulta = $sql->seleccionar($tablas, $columnas, $condicion);

            if ($sql->filasDevueltas) {
                $fila = $sql->filaEnObjeto($consulta);
                foreach ($fila as $propiedad => $valor) {
                    $this->$propiedad = $valor;
                }
                
                //verificar si el proveedor tiene cuentas bancarias, en caso de que tenga, hacer la consulta y armar la lista de objetos
                $tablas2    = array('cp' => 'componentes_modulos', 'm' => 'modulos');
                $columnas2  = array('id' => 'cp.id', 'idModulo' => 'cp.id_modulo', 'modulo' => 'm.nombre', 'nombreAccion' => 'cp.componente', 'nombreAccionMenu' => 'cp.nombre', 'activo' => 'cp.activo');
                
                $this->listaAcciones = $sql->seleccionar($tablas2, $columnas2, 'cp.id_modulo = m.id AND cp.id_modulo = "' . $id . '"');
                $this->numeroAcciones = $sql->filasDevueltas;
                
                
                
            }
            
        }
        
    }

    /**
     *
     * Adicionar el componentes en la tabla de modulos
     *
     * @param  arreglo $datos       Datos de los componentes a adicionar
     * @return entero               Código interno o identificador del perfil en la base de datos (NULL si hubo error)
     *
     */
    public function adicionar($datos) {
        global $sql;

        $datos_insertar = array(
            'id_padre'          => $datos['componente_padre'],
            'menu'              => $datos['mostrar_menu'],
            'tipo_menu'         => $datos['tipo_menu'],
            'nombre_menu'       => $datos['nombre_menu'],
            'clase'             => $datos['clase'],
            'orden'             => $datos['orden'],
            'nombre'            => $datos['nombre'],
            'url'               => $datos['url'],
            'carpeta'           => $datos['carpeta'],
            'visible'           => $datos['visible'],
            'global'            => $datos['global'],
            'tabla_principal'   => $datos['tabla_principal'],
            'valida_usuario'    => $datos['valida_usuario'],
            'documentacion'     => $datos['documentacion'],
        );
        //$sql->depurar = true;
        $consulta = $sql->insertar('modulos', $datos_insertar);
        
        $id_item = $sql->ultimoId;

        if ($consulta) {
            if ($datos['componente_padre'] == 0) {
                $id_modulo = $sql->ultimoId;
                $consulta = $sql->modificar('modulos', array('id_padre' => $id_modulo), 'id = "' . $id_modulo . '"');
                
            }
            
            return $id_item;
            
        } else {
            return NULL;
            
        }
        
    }

    /**
     *
     * Modificar los datos del componente
     *
     * @param  arreglo $datos       Datos del componente a modificar
     * @return lógico               Indica si el procedimiento se pudo realizar correctamente o no
     *
     */
    public function modificar($datos) {
        global $sql;

        if (!isset($this->id)) {
            return NULL;
        }

        $datos_modificar = array(
            'id_padre'          => $datos['componente_padre'],
            'menu'              => $datos['mostrar_menu'],
            'tipo_menu'         => $datos['tipo_menu'],
            'nombre_menu'       => $datos['nombre_menu'],
            'clase'             => $datos['clase'],
            'orden'             => $datos['orden'],
            'nombre'            => $datos['nombre'],
            'url'               => $datos['url'],
            'carpeta'           => $datos['carpeta'],
            'visible'           => $datos['visible'],
            'global'            => $datos['global'],
            'tabla_principal'   => $datos['tabla_principal'],
            'valida_usuario'    => $datos['valida_usuario'],
            'documentacion'     => $datos['documentacion'],
        );

        $sql->iniciarTransaccion();
        
        $consulta = $sql->modificar('modulos', $datos_modificar, 'id = "' . $this->id . '"');

        if ($consulta) {
            
            if (!empty($datos['acciones_modulo'])) {//si se van a insertar acciones en el modulo
                //se recibe una cadna separada por caracteres especiales y se separa volviendola un arreglo
                $cadenaAcciones = explode('[', $datos['acciones_modulo']);
                $largo = sizeof($cadenaAcciones) - 1;

                $consultaSql = 'INSERT INTO fom_componentes_modulos (id ,id_modulo , componente , nombre , activo) VALUES ';
                //se recorre el arreglo, para nuevamente separar cada posicion que es tambien una cadena por otro caracter especial
                //para consultar el id del banco e ir armando la consulta sql 
                for ($i = 0; $i < $largo; $i++) {
                    $cuentas = explode('|', $cadenaAcciones[$i]);
                    $consultaSql .= '(NULL, "' . $this->id . '", "' . $cuentas[0] . '", "' . $cuentas[1] . '", "1")';
                    if ($i != $largo - 1) {
                        $consultaSql .= ', ';
                    }
                }

                $consultaSql .= ';';

                $consulta = $sql->ejecutar($consultaSql);

                if (!$consulta) {
                    $sql->cancelarTransaccion();
                    $sql->error = 'Error insertando las acciones al modulo';
                    return false;

                } else {
                    //finalizar la transaccion
                    $sql->finalizarTransaccion();                   
                    return $this->id ;

                }
            }
            
            $sql->finalizarTransaccion(); 
            return $this->id;
            
        } else {
            $sql->cancelarTransaccion();
            return NULL;
            
        }
        
    }

    /**
     *
     * Eliminar el componente
     *
     * @return lógico Indica si el procedimiento se pudo realizar correctamente o no
     *
     */
    public function eliminar() {
        global $sql;

        if (!isset($this->id)) {
            return NULL;
        }

        $consulta = $sql->eliminar('modulos', 'id = "' . $this->id . '"');
        return $consulta;
    }

    /**
     *
     * Listar los componentes de la tabla modulos
     *
     * @param entero  $cantidad Número de usuarios a incluir en la lista (0 = todas las entradas)
     * @return arreglo Listar los componentes de la tabla modulos
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

        /*         * *Validar que la excepción sea un arreglo y contenga elementos ** */
        if (isset($excepcion) && is_array($excepcion) && count($excepcion) && sizeof($excepcion) > 0) {
            $excepcion = implode(',', $excepcion);
            $condicion .= 'u.id NOT IN ('.$excepcion.')';
        }
        
        /*** Validar que la condición sea una cadena de texto ***/
        if (!is_string($condicionGlobal)) {
            $condicion = '';
        }        
        

        /*         * * Validar que la condición sea una cadena de texto ** */
        $condicion = 'm1.id != 0 AND';

        /*         * * Definir el orden de presentación de los datos ** */
        if (!isset($orden)) {
            $orden = 'm1.nombre';
        }
        if ($this->listaAscendente) {
            $orden = $orden.' ASC';
        } else {
            $orden = $orden.' DESC';
        }

        $tablas = array(
            'm1' => 'modulos',
            'm2' => 'modulos'
        );

        $columnas = array(
            'id'            => 'm1.id',
            'nombre'        => 'm1.nombre',
            'nombreMenu'    => 'm1.nombre_menu',
            'idPadre'       => 'm1.id_padre',
            'padre'         => 'm2.nombre_menu',
            'orden'         => 'm1.orden'
        );

        $agrupar = '';
        
        if (!empty($condicionGlobal)) {
            $condicion .= $condicionGlobal . ' AND ';
        }        

        $condicion .= ' m1.id_padre = m2.id';


        if (is_null($this->registrosConsulta)) {
            $sql->seleccionar($tablas, $columnas, $condicion);
            $this->registrosConsulta = $sql->filasDevueltas;
        }
        //$sql->depurar = true;
        $consulta = $sql->seleccionar($tablas, $columnas, $condicion, $agrupar, $orden, $inicio, $cantidad);
        if ($sql->filasDevueltas) {

            $lista = array();
            while ($componentes = $sql->filaEnObjeto($consulta)) {
                $componentes->url = $this->urlBase . '/' . $componentes->id;
                $lista[] = $componentes;
            }
        }
        return $lista;
    }

    /**
     *
     * @global type $textos
     * @global type $sesion_usuarioSesion
     * @param type $arregloRegistros
     * @param type $datosPaginacion
     * @return type 
     */
    public function generarTabla($arregloRegistros, $datosPaginacion = NULL) {
        global $textos;
        //Declaracion de las columnas que se van a mostrar en la tabla
        $datosTabla = array(
            HTML::parrafo($textos->id('NOMBRE'), 'centrado')    => 'nombre|m1.nombre',
            HTML::parrafo($textos->id('NOM_MENU'), 'centrado')  => 'nombreMenu|m1.nombre_menu',
            HTML::parrafo($textos->id('PADRE'), 'centrado')     => 'padre|m2.nombre',
            HTML::parrafo($textos->id('ORDEN'), 'centrado')     => 'orden|m1.orden'
        );
        //ruta a donde se mandara la accion del doble click
        $ruta = '/ajax' . $this->urlBase . '/move';


        $tabla = Recursos::generarTablaRegistros($arregloRegistros, $datosTabla, $ruta, $datosPaginacion);
        $menuDerecho = HTML::crearMenuBotonDerecho('COMPONENTES');

        return $tabla . $menuDerecho;
    }

}
