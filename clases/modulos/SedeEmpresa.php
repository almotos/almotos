<?php

/**
 *
 * @package     FOLCS
 * @subpackage  Sedes de Empresa
 * @author      Pablo Andrés Vélez Vidal <pavelez8@misena.edu.co>
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2012 Genesys corporation
 * @version     0.2
 *
 **/


class SedeEmpresa {

    /**
     * Código interno o identificador de la sede en la base de datos
     * @var entero
     */
    public $id;


    /**
     * URL relativa del módulo de la sedeEmpresa
     * @var cadena
     */
    public $urlBase;


    /**
     * URL relativa de una sedeEmpresa específica
     * @var cadena
     */
    public $url;


    /**
     * Nombre de la  sede
     * @var cadena
     */
    public $nombre;


    /**
     * id de la sedeEmpresa donde esta ubicada la sede
     * @var cadena
     */
    public $idCiudad;


     /**
     * nombre de la ciudad donde esta ubicada la sede
     * @var cadena
     */
    public $ciudad;
    
     /**
     * direccion donde esta ubicada la sede
     * @var cadena
     */
    public $direccion;    
    
    /**
     * Telefono principal de la sede
     * @var cadena
     */
    public $celular;


     /**
     * Telefono secundario de la sede
     * @var cadena
     */
    public $telefono;   
    
     /**
     * Fax de la sede
     * @var cadena
     */
    public $fax;       
    

     /**
     * Email de la sede
     * @var cadena
     */
    public $email;     
      
    
     /**
     * en el momento en el que se crea la sede, si no se escoge un valor, se pone la fecha actual
     * @var cadena
     */
    public $fechaApertura;  
    
     /**
     * Fecha de cierre de la sede, en el momento en el que se desactive la sede, se guarda un dato en esta variable
     * @var cadena
     */
    public $fechaCierre;    

    /**
     * Indicador del orden cronológio de la lista de sedes_empresa
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
    public $registrosConsulta = NULL;    
    
    /**
     * Orden predeterminado para organizar los listados
     * @var entero
     */
    public $ordenInicial = NULL;       

    /**
     * Objeto empresa
     * @var entero
     */
    public $empresa = NULL; 

    /**
     *
     * Inicializar la Ciudad
     *
     * @param entero $id Código interno o identificador de la sedeEmpresa en la base de datos
     *
     */
    public function __construct($id = NULL) {
        global $sql, $modulo;
        $this->urlBase = '/'.$modulo->url;
        $this->url     = $modulo->url;
       
        $this->registros = $sql->obtenerValor('sedes_empresa', 'COUNT(id)', 'id != "0"');
        //establecer el valor del campo predeterminado para organizar los listados
        $this->ordenInicial = 'nombre';

        if (isset($id)) {
            $this->cargar($id);
        }
    }

    /**
     *
     * Cargar los datos de una sede de empresa
     *
     * @param entero $id Código interno o identificador de la sede de empresa en la base de datos
     *
     */
    public function cargar($id) {
        global $sql;

        if (isset($id) && $sql->existeItem('sedes_empresa', 'id', intval($id))) {

            $tablas = array(
                's'  => 'sedes_empresa',
            );

            $columnas = array(
                'id'                => 's.id',
                'nombre'            => 's.nombre',
                'idCiudad'          => 's.id_ciudad',
                'ciudad'            => 'c.cadena',
                'direccion'         => 's.direccion',
                'celular'           => 's.celular',
                'telefono'          => 's.telefono',
                'fax'               => 's.fax',
                'email'             => 's.email',
                'fechaApertura'     => 's.fecha_apertura',
                'fechaCierre'       => 's.fecha_cierre',
                'activo'            => 's.activo'
            );

            $condicion = ' LEFT JOIN fom_lista_ciudades c ON s.id_ciudad = c.id WHERE s.id = "'.$id.'"';
            
            $consulta = $sql->seleccionar($tablas, $columnas, $condicion, "", "", NULL, NULL, FALSE);

            if ($sql->filasDevueltas) {
                $fila = $sql->filaEnObjeto($consulta);

                foreach ($fila as $propiedad => $valor) {
                    $this->$propiedad = $valor;
                }
                
            }
        }
    }

    /**
     *
     * Adicionar una sedeEmpresa
     *
     * @param  arreglo $datos       Datos de la sedeEmpresa a adicionar
     * @return entero               Código interno o identificador de la sedeEmpresa en la base de datos (NULL si hubo error)
     *
     */
    public function adicionar($datos) {
        global $sql;
        
        
        $datosSedes = array(
            'nombre'            => $datos['nombre'],
            'id_ciudad'         => $sql->obtenerValor('lista_ciudades', 'id', 'cadena = "'.$datos['id_ciudad'].'"'),
            'direccion'         => $datos['direccion'],
            'celular'           => $datos['celular'],
            'telefono'          => $datos['telefono'],
            'fax'               => $datos['fax'],
            'email'             => $datos['email'],
            'fecha_apertura'    => $datos['fecha_apertura']
        );
        
       if (isset($datos['activo'])) {
            $datosSedes['activo']       = '1';
            $datosSedes['fecha_cierre'] = NULL;            

        } else {
            $datosSedes['activo']       = '0';
            $datosSedes['fecha_cierre'] = date('Y-m-d');
        }        

        $consulta = $sql->insertar('sedes_empresa', $datosSedes);

        if ($consulta) {
            return $sql->ultimoId;

        } else {
            return NULL;
        }
    }

    /**
     *
     * Modificar una sedeEmpresa
     *
     * @param  arreglo $datos       Datos de la sedeEmpresa a modificar
     * @return lógico               Indica si el procedimiento se pudo realizar correctamente o no
     *
     */
    public function modificar($datos) {
        global $sql;

        if (!isset($this->id)) {
            return NULL;
        }
        
        $datosSedes = array(
            'nombre'            => $datos['nombre'],
            'id_ciudad'         => $sql->obtenerValor('lista_ciudades', 'id', 'cadena = "'.$datos['id_ciudad'].'"'),
            'direccion'         => $datos['direccion'],
            'celular'           => $datos['celular'],
            'telefono'          => $datos['telefono'],
            'fax'               => $datos['fax'],
            'email'             => $datos['email'],
            'fecha_apertura'    => $datos['fecha_apertura']
        );
        
       if (isset($datos['activo'])) {
            $datosSedes['activo']       = '1';
            $datosSedes['fecha_cierre'] = NULL;            

        } else {
            $datosSedes['activo']       = '0';
            $datosSedes['fecha_cierre'] = date('Y-m-d');
        }   

        $consulta = $sql->modificar('sedes_empresa', $datosSedes, 'id = "'.$this->id.'"');
        return $consulta;
    }

    /**
     *
     * Eliminar una sede empresa
     *
     * @param entero $id    Código interno o identificador de la sede empresa en la base de datos
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
        $arreglo1 = array('permisos_componentes_usuarios',   'id_sede     = "'.$this->id.'"', $textos->id('PERMISOS_COMPONENTES_USUARIOS'));//arreglo del que sale la info a consultar
        $arreglo2 = array('permisos_modulos_usuarios',       'id_sede     = "'.$this->id.'"', $textos->id('PERMISOS_MODULOS_USUARIOS'));//arreglo del que sale la info a consultar
        $arreglo3 = array('resoluciones',                    'id_sede     = "'.$this->id.'"', $textos->id('RESOLUCIONES'));//arreglo del que sale la info a consultar
        $arreglo4 = array('bodegas',                         'id_sede     = "'.$this->id.'"', $textos->id('BODEGAS'));//arreglo del que sale la info a consultar
        $arreglo5 = array('empleados',                       'id_sede     = "'.$this->id.'"', $textos->id('EMPLEADOS'));//arreglo del que sale la info a consultar
        $arreglo6 = array('cajas',                           'id_sede     = "'.$this->id.'"', $textos->id('CAJAS'));//arreglo del que sale la info a consultar

        $arregloIntegridad  = array($arreglo1, $arreglo2, $arreglo3, $arreglo4, $arreglo5, $arreglo6);//arreglo de arreglos para realizar las consultas de integridad referencial, (ver documentacion de metodo)
        $integridad         = Recursos::verificarIntegridad($textos->id('SEDES_EMPRESA'), $arregloIntegridad);

        /**
         * si hay problemas con la integridad referencial, la variable integridad tiene como valor,
         * un texto diciendo que tabla contiene n cantidad de relaciones con esta
         */
        if ($integridad != "") {
            $respuestaEliminar['mensaje'] = $integridad;
            return $respuestaEliminar;
            
        }
              
        $sql->iniciarTransaccion();
        $consulta = $sql->eliminar('sedes_empresa', 'id = "'.$this->id.'"');
        
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
     * Listar las sedes_empresa
     *
     * @param entero  $cantidad    Número de ciudadesa incluir en la lista (0 = todas las entradas)
     * @param arreglo $excepcion   Arreglo con los códigos internos o identificadores a omitir en la lista
     * @param cadena  $condicion   Condición adicional (SQL)
     * @return arreglo             Lista de sedes_empresa
     *
     */
    public function listar($inicio = 0, $cantidad = 0, $excepcion = NULL, $condicionGlobal = NULL, $orden = NULL) {
        global $sql;

        /*** Validar la fila inicial de la consulta ***/
        if (!is_int($inicio) || $inicio < 0) {
            $inicio = 0;
        }

        /*** Validar la cantidad de registros requeridos en la consulta ***/
        if (!is_int($cantidad) || $cantidad <= 0) {
            $cantidad = 0;
        }

        /*** Validar que la condición sea una cadena de texto ***/
        if (!is_string($condicionGlobal)) {
            $condicion = '';
        }

        /*** Validar que la excepción sea un arreglo y contenga elementos ***/
        if (isset($excepcion) && is_array($excepcion) && count($excepcion)) {
            $excepcion = implode(',', $excepcion);
            $condicion = 's.id NOT IN ('.$excepcion.') AND ';
        }

        /*** Definir el orden de presentación de los datos ***/
        if(!isset($orden)){
            $orden = $this->ordenInicial;
        }
        if ($this->listaAscendente) {
            $orden = $orden.' ASC';

        } else {
            $orden = $orden.' DESC';
        }

        $tablas = array(
            's'  => 'sedes_empresa',
            'c'  => 'lista_ciudades',
        );

        $columnas = array(
            'id'                => 's.id',
            'nombre'            => 's.nombre',
            'idCiudad'          => 's.id_ciudad',
            'ciudad'            => 'c.cadena',
            'direccion'         => 's.direccion',
            'celular'           => 's.celular',
            'telefono'          => 's.telefono',
            'fax'               => 's.fax',
            'email'             => 's.email',
            'fechaApertura'     => 's.fecha_apertura',
            'fechaCierre'       => 's.fecha_cierre',
            'activo'            => 's.activo'
        );
         
        if (!empty($condicionGlobal)) {
            
            $condicion .= $condicionGlobal.' AND ';
        } 
        
        $condicion .= 's.id_ciudad = c.id';     
       
        if (is_null($this->registrosConsulta)) {
            $sql->seleccionar($tablas, $columnas, $condicion);
            $this->registrosConsulta = $sql->filasDevueltas;
        }

        $consulta = $sql->seleccionar($tablas, $columnas, $condicion, '', $orden, $inicio, $cantidad);

        if ($sql->filasDevueltas) {
            $lista = array();

            while ($sedeEmpresa = $sql->filaEnObjeto($consulta)) {
                $sedeEmpresa->url           = $this->urlBase.'/'.$sedeEmpresa->id;
                $sedeEmpresa->direccion     = $sedeEmpresa->direccion.', '.$sedeEmpresa->ciudad;
                $lista[]   = $sedeEmpresa;
            }
        }

        return $lista;

    }
    
    public function generarTabla($arregloRegistros, $datosPaginacion = NULL){
        global $textos;
        //Declaracion de las columnas que se van a mostrar en la tabla
        $datosTabla = array(                      
            HTML::parrafo( $textos->id('NOMBRE')     ,  'centrado' ) => 'nombre|s.nombre',
            HTML::parrafo( $textos->id('DIRECCION')  ,  'centrado' ) => 'direccion|s.direccion',
            HTML::parrafo( $textos->id('TELEFONO')   ,  'centrado' ) => 'telefono|s.telefono',
            HTML::parrafo( $textos->id('CELULAR')    ,  'centrado' ) => 'celular|s.celular',
            HTML::parrafo( $textos->id('EMAIL')      ,  'centrado' ) => 'email|s.email'
            
        );        
        //ruta a donde se mandara la accion del doble click
        $rutaPaginador = '/ajax'.$this->urlBase.'/move';
        
        return Recursos::generarTablaRegistros($arregloRegistros, $datosTabla, $rutaPaginador, $datosPaginacion).HTML::crearMenuBotonDerecho('SEDES_EMPRESA');
        
    }    
  
}
