<?php

/**
 * Rabo de gato, sidelitis cartica
 * @package     FOM
 * @subpackage  Clientes del negocio
 * @author      Pablo Andrés Vélez Vidal <pavelez8@misena.edu.co>
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2012 Genesys Corporation.
 * @version     0.2
 * 
 * Un cliente tiene 1..* sedes y tiene 1..* contactos
 * tablas principales: clientes, sedes_cliente, contactos_cliente
 * dato: la tabla contacto cliente almacena 2 campos llaves, el id_cliente, y el id_persona.
 * asi que para traer los datos de un contacto, se debe consultar a la persona que esta relacionada con este
 * contacto
 * 
 * */
class Cliente {

    /**
     * Código interno o identificador del cliente en la base de datos
     * @var entero
     */
    public $id;

    /**
     * URL relativa del módulo de clientes
     * @var cadena
     */
    public $urlBase;

    /**
     * URL relativa de un cliente específico
     * @var cadena
     */
    public $url;

    /**
     * Código interno del modulo cliente
     * @var entero
     */
    public $idModulo;

    /**
     * Código interno o identificador del tipo de cliente en la base de datos
     * @var entero
     */
    public $idCliente;

    /**
     * Nombre del cliente
     * @var objeto
     */
    public $nombre;
    
    /**
     * Razon social del cliente
     * @var objeto
     */
    public $razonSocial;    

    /**
     * Código interno o identificador en la base de datos de la persona con la cual está relacionada el cliente
     * @var entero
     */
    public $idContacto;

    /**
     * Representación (objeto) de la persona con la cual está relacionada el cliente (contacto oficial)
     * @var objeto
     */
    public $contacto;

    /**
     * Código interno o identificador en la base de datos de la sede con la cual está relacionada el cliente
     * @var entero
     */
    public $idSede;

    /**
     * Representación (objeto) de la sede con la cual está relacionada el cliente (sede oficial)
     * @var objeto
     */
    public $sede;

    /**
     * regimen del cliente
     * @var boolean
     */
    public $tipoPersona;

    /**
     * regimen del cliente
     * @var boolean
     */
    public $regimen;
    
    /**
     * id la actividad economica de la dian a la que se dedica el cliente
     * @var int 
     */
    public $idActividadEconomica;    
    
    /**
     * objeto actividad economica de la dian a la que se dedica el cliente
     * @var int 
     */
    public $actividadEconomica;      

    /**
     * maximo cupo de credito asignado a un cliente
     * @var int 
     */
    public $maxCupoCredito;    
       
    /**
     * Call center del cliente
     * @var boolean
     */
    public $callCenter;

    /**
     * id del usuario que crea el cliente
     * @var boolean
     */
    public $idUsuarioCreador;

    /**
     * id del usuario que crea el cliente
     * @var boolean
     */
    public $usuarioCreador;

    /**
     * Fecha de registro de la persona en el sistema
     * @var boolean
     */
    public $fechaCreacion;

    /**
     * Fecha de registro de la persona en el sistema
     * @var boolean
     */
    public $observaciones;

    /**
     * Indicador del orden del listado de clientes
     * @var lógico
     */
    public $listaAscendente = TRUE;

    /**
     * Número de registros de la lista
     * @var entero
     */
    public $registros = NULL;

    /**
     * Número de registros de la lista
     * @var entero
     */
    public $registrosActivos = NULL;

    /**
     * Determina si un cliente se encuentra activo en el sistema o no
     * @var entero
     */
    public $activo;

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
     * sedes -> arreglo de objetos que contiene la informacion de las sedes de un cliente
     * @var entero
     */
    public $listaSedes = array();

    /**
     * contactos -> Determina si un cliente tiene mas sedes aparte de la sede principal
     * @var entero
     */
    public $hayMasSedes;

    /**
     * contactos -> arreglo de objetos que contiene la informacion de los contactos de un cliente
     * @var entero
     */
    public $listaContactos = array();

    /**
     * contactos -> Determina si el cliente tiene mas contactos aparte del contacto principal
     * @var entero
     */
    public $hayMasContactos;



    /**
     *
     * Inicializar el cliente
     *
     * @param entero $id Código interno o identificador del cliente en la base de datos
     *
     */
    public function __construct($id = NULL) {
        global $sql, $modulo;
        $this->urlBase      = '/' . $modulo->url;
        $this->url          = $modulo->url;
        $this->idModulo     = $modulo->id;
        $cliente = '';
        if (is_string($id) && isset($id) && $sql->existeItem('clientes', 'id_cliente', $id)) {
            $cliente = $sql->obtenerValor('clientes', 'id', 'id_cliente = "' . $id . '"');
        } elseif (is_numeric($id)) {
            $cliente = $id;
        }

        $this->registros            = $sql->obtenerValor('clientes', 'COUNT(id)', 'id != "0"');
        $this->registrosActivos     = $sql->obtenerValor('clientes', 'COUNT(id)', 'id != "0" AND activo = "1"');
        //establecer el valor del campo predeterminado para organizar los listados
        $this->ordenInicial         = 'nombre';


        if (!empty($cliente)) {
            $this->cargar($cliente);
        }
    }

    /**
     *
     * Cargar los datos del cliente
     *
     * @param entero $id Código interno o identificador del cliente en la base de datos
     *
     */
    public function cargar($id) {
        global $sql;

        if (isset($id) && $sql->existeItem('clientes', 'id', intval($id))) {
            $this->id = $id;

            $tablas = array(
                'p'     => 'clientes',
                'u'     => 'usuarios'
            );

            $columnas = array(
                'idCliente'             => 'p.id_cliente',
                'nombre'                => 'p.nombre',
                'razonSocial'           => 'p.razon_social',
                'regimen'               => 'p.regimen',
                'idActividadEconomica'  => 'p.id_actividad_economica',
                'tipoPersona'           => 'p.tipo_persona',
                'callCenter'            => 'p.call_center',
                'idUsuarioCreador'      => 'p.id_usuario_crea',
                'maxCupoCredito'        => 'p.max_cupo_credito',
                'usuarioCreador'        => 'u.usuario',
                'fechaCreacion'         => 'p.fecha_creacion',
                'activo'                => 'p.activo',
                'observaciones'         => 'p.observaciones',
            );

            $condicion = 'p.id_usuario_crea = u.id AND p.id = "' . $id . '"';
            
            $consulta = $sql->seleccionar($tablas, $columnas, $condicion);

            if ($sql->filasDevueltas) {
                $fila = $sql->filaEnObjeto($consulta);
                //asignar los valores de la consulta a los atributos del objeto 
                foreach ($fila as $propiedad => $valor) {
                    $this->$propiedad = $valor;
                }
                
                $this->actividadEconomica = new ActividadEconomica($this->idActividadEconomica);

                //obtener la info del contacto principal
                
                $objetoContacto = $sql->filaEnObjeto($sql->seleccionar(array('contactos_cliente'), array('id', 'id_cliente', 'id_persona', 'observaciones', 'principal'), 'id_cliente = "' . $id . '" AND principal = "1"'));
                $this->idContacto = $objetoContacto->id;
                $this->contacto = new Persona($objetoContacto->id_persona);
                $this->contacto->observacionesContacto = $objetoContacto->observaciones;

                //obtener la info de la sede principal
                $tablas = array('s' => 'sedes_cliente', 'c' => 'lista_ciudades');
                $columnas = array('id' => 's.id', 'nombre' => 's.nombre', 'id_ciudad' => 's.id_ciudad', 'idCiudad' => 'c.id', 'nombreCiudad' => 'c.cadena', 'direccion' => 's.direccion', 'telefono' => 's.telefono', 'celular' => 's.celular', 'fax' => 's.fax');
                $this->sede = $sql->filaEnObjeto($sql->seleccionar($tablas, $columnas, 's.id_ciudad = c.id AND s.id_cliente = "' . $id . '" AND principal = "1"'));

                //verificar si el cliente tiene mas contactos aparte del principal, en caso de que tenga, hacer la consulta y armar la lista de objetos
                $this->listaContactos = $sql->seleccionar(array('cp' => 'contactos_cliente'), array('id' => 'id', 'idPersona' => 'id_persona', 'observaciones' => 'observaciones'), 'cp.id_cliente = "' . $id . '" AND cp.principal = "0"');
                $this->hayMasContactos = $sql->filasDevueltas;

                //verificar si el cliente tiene mas sedes aparte de la principal, en caso de que tenga, hacer la consulta y armar la lista de objetos
                $tablas1 = array('s' => 'sedes_cliente', 'c' => 'lista_ciudades');
                $columnas1 = array('id' => 's.id', 'nombre' => 's.nombre', 'ciudad' => 'c.cadena', 'direccion' => 's.direccion', 'telefono' => 's.telefono', 'celular' => 's.celular', 's.fax');
                $this->listaSedes = $sql->seleccionar($tablas1, $columnas1, 's.id_ciudad = c.id AND s.id_cliente = "' . $id . '" AND s.principal = "0"');
                $this->hayMasSedes = $sql->filasDevueltas;

            }//fin del filas devueltas
        }
    }

    /**
     * Registrar un cliente con los datos básicos
     * @param  arreglo $datos       Datos del cliente a registrar
     * @return entero               Código interno o identificador del cliente en la base de datos (NULL si hubo error)
     */
    public function adicionar($datos) {
        global $sql, $sesion_usuarioSesion;

        $datosCliente = array(
            'tipo_persona'              => $datos['tipo_persona'],
            'regimen'                   => $datos['regimen'],
            'id_actividad_economica'    => $datos['id_actividad_economica'],
            'razon_social'              => $datos['razon_social'],
            'max_cupo_credito'          => $datos['max_cupo_credito'],
            'call_center'               => $datos['call_center'],
            'id_usuario_crea'           => $sesion_usuarioSesion->id,
            'fecha_creacion'            => date('Y-m-d H:i:s'),
            'observaciones'             => $datos['observaciones']
        );
        
        if ($datos['tipo_persona'] == '1') {
            $datosCliente['nombre']           = (!empty($datos['nombre_comercial'])) ? $datos['nombre_comercial'] : $datos['primer_nombre'] . ' ' . $datos['segundo_nombre'] . ' ' . $datos['primer_apellido'] . ' ' . $datos['segundo_apellido'];
            $datosCliente['id_cliente']     = (!empty($datos['id_cliente'])) ? $datos['id_cliente'] : $datos['documento_identidad'];
        } else {
            $datosCliente['nombre']           = $datos['nombre_comercial'];
            $datosCliente['id_cliente']     = $datos['id_cliente'];
        }        

        $datosCliente['activo']       = (isset($datos['activo'])) ? '1' : '0';  


        //iniciar transaccion
        $sql->iniciarTransaccion();
        $consulta = $sql->insertar('clientes', $datosCliente);

        if ($consulta) {//si se pudo insertar el cliente
            $idCliente = $sql->ultimoId;

            if ($datos['nombre_sede'] == '') {
                $datos['nombre_sede'] = 'Sede Principal';
            }

            $datosSede = array(
                'id_cliente'      => $idCliente,
                'nombre'            => $datos['nombre_sede'],
                'id_ciudad'         => $datos['id_ciudad_sede'],
                'direccion'         => $datos['direccion_sede'],
                'telefono'          => $datos['telefono_sede'],
                'celular'           => $datos['celular_sede'],
                'fax'               => $datos['fax_sede'],
                'principal'         => '1'
            );

            $consulta = $sql->insertar('sedes_cliente', $datosSede);

            if ($consulta) {//si se logro insertar la sede del cliente
                $existe = $sql->existeItem('personas', 'documento_identidad', $datos['documento_identidad']);
                if ($existe) {
                    $idPersona = $sql->obtenerValor('personas', 'id', 'documento_identidad = "' . $datos['documento_identidad'] . '"');
                    $datosPersona = array(
                        'documento_identidad'   => $datos['documento_identidad'],
                        'id_tipo_documento'     => $datos['id_tipo_documento'],
                        'primer_nombre'         => $datos['primer_nombre'],
                        'segundo_nombre'        => $datos['segundo_nombre'],
                        'primer_apellido'       => $datos['primer_apellido'],
                        'segundo_apellido'      => $datos['segundo_apellido'],
                        'telefono'              => $datos['telefono'],
                        'celular'               => $datos['celular'],
                        'fax'                   => $datos['fax'],
                        'correo'                => $datos['correo'],
                    );
                    
                    $sql->modificar('personas', $datosPersona, 'id = "' . $idPersona . '"');
                    
                } else {

                    $datosPersona = array(
                        'documento_identidad'       => $datos['documento_identidad'],
                        'id_tipo_documento'         => $datos['id_tipo_documento'],
                        'primer_nombre'             => $datos['primer_nombre'],
                        'segundo_nombre'            => $datos['segundo_nombre'],
                        'primer_apellido'           => $datos['primer_apellido'],
                        'segundo_apellido'          => $datos['segundo_apellido'],
                        'telefono'                  => $datos['telefono'],
                        'celular'                   => $datos['celular'],
                        'fax'                       => $datos['fax'],
                        'correo'                    => $datos['correo'],
                    );

                    $consulta = $sql->insertar('personas', $datosPersona);
                    $idPersona = $sql->ultimoId;
                }

                $datosContacto = array(
                    'id_cliente'      => $idCliente,
                    'id_persona'        => $idPersona,
                    'observaciones'     => $datos['observaciones_contacto'],
                    'principal'         => '1'
                );
                $consulta = $sql->insertar('contactos_cliente', $datosContacto);

                if ($consulta) {//si se inserto el contacto del cliente, la sede y el cliente                    
                    //finalizar la transaccion
                    $sql->finalizarTransaccion();                   
                    return $idCliente;
                    
                } else {
                    $sql->error = 'Error insertando contacto de cliente';
                    $sql->cancelarTransaccion();
                    return false;
                    
                }
                
            } else {
                $sql->error = 'Error insertando sede de cliente, eliminando el registro del cliente...';
                $sql->cancelarTransaccion();
                return false;
                
            }
            
        } else {
            $sql->error = 'Error insertando cliente';
            $sql->cancelarTransaccion();
            return false;
          
        }
        
    }

    /**
     * Modificar la informaci{on básica de un cliente
     * 
     * @param  arreglo $datos       Datos del cliente a modificar
     * @return entero               Código interno o identificador del cliente en la base de datos (NULL si hubo error)
     */
    public function modificar($datos) {
        global $sql;

        if (!isset($this->id)) {
            return NULL;
        }

        $datosCliente = array(
            'tipo_persona'              => $datos['tipo_persona'],
            'regimen'                   => $datos['regimen'],
            'id_actividad_economica'    => $datos['id_actividad_economica'],
            'razon_social'              => $datos['razon_social'],
            'call_center'               => $datos['call_center'],
            'max_cupo_credito'          => $datos['max_cupo_credito'],
            'observaciones'             => $datos['observaciones']
        );

        if ($datos['tipo_persona'] == '1') {//persona juridica
            $datosCliente['nombre']           = (!empty($datos['nombre_comercial'])) ? $datos['nombre_comercial'] : $datos['primer_nombre'] . ' ' . $datos['segundo_nombre'] . ' ' . $datos['primer_apellido'] . ' ' . $datos['segundo_apellido'];
            $datosCliente['id_cliente']     = (!empty($datos['id_cliente'])) ? $datos['id_cliente'] : $datos['documento_identidad'];
        } else {
            $datosCliente['nombre']           = $datos['nombre_comercial'];
            $datosCliente['id_cliente']     = $datos['id_cliente'];
        }     

        $datosCliente['activo']       = (isset($datos['activo'])) ? '1' : '0';     

        $sql->iniciarTransaccion();
        $consulta = $sql->modificar('clientes', $datosCliente, 'id = "' . $this->id . '"');

        if ($consulta) {//si se pudo insertar el cliente
            $idCliente = $this->id;

            if ($datos['nombre_sede'] == '') {
                $datos['nombre_sede'] = 'Sede Principal';
            }

            $datosSede = array(
                'id_cliente'          => $idCliente,
                'nombre'                => $datos['nombre_sede'],
                'id_ciudad'             => $datos['id_ciudad_sede'],
                'direccion'             => $datos['direccion_sede'],
                'telefono'              => $datos['telefono_sede'],
                'celular'               => $datos['celular_sede'],
                'fax'                   => $datos['fax_sede'],
                'principal'             => '1'
            );

            $consulta = $sql->modificar('sedes_cliente', $datosSede, 'id = "' . $this->sede->id . '"');

            if ($consulta) {//si se logro modificar la sede del cliente
                $existe = $sql->existeItem('personas', 'documento_identidad', $datos['documento_identidad']);
                
                if ($existe) {
                    $idPersona = $sql->obtenerValor('personas', 'id', 'documento_identidad = "' . $datos['documento_identidad'] . '"');
                    $datosPersona = array(
                        'primer_nombre'         => $datos['primer_nombre'],
                        'segundo_nombre'        => $datos['segundo_nombre'],
                        'primer_apellido'       => $datos['primer_apellido'],
                        'segundo_apellido'      => $datos['segundo_apellido'],
                        'correo'                => $datos['correo'],
                        'fax'                   => $datos['fax'],
                        'telefono'              => $datos['telefono'],
                        'celular'               => $datos['celular']
                    );

                    $consulta = $sql->modificar('personas', $datosPersona, 'id = "' . $idPersona . '"');
                    
                } else {
                    $datosPersona = array(
                        'documento_identidad'       => $datos['documento_identidad'],
                        'id_tipo_documento'         => $datos['id_tipo_documento'],
                        'primer_nombre'             => $datos['primer_nombre'],
                        'segundo_nombre'            => $datos['segundo_nombre'],
                        'primer_apellido'           => $datos['primer_apellido'],
                        'segundo_apellido'          => $datos['segundo_apellido'],
                        'celular'                   => $datos['celular']
                    );

                    $consulta = $sql->insertar('personas', $datosPersona);
                    $idPersona = $sql->ultimoId;
                }

                $datosContacto = array(
                    'id_cliente'      => $idCliente,
                    'id_persona'        => $idPersona,
                    'observaciones'     => $datos['observaciones_contacto'],
                    'principal'         => '1'
                );

                $consulta = $sql->modificar('contactos_cliente', $datosContacto, 'id = "' . $this->idContacto . '"');


                if (!$consulta) {//si se inserto el contacto del cliente, la sede y el cliente
                    $sql->cancelarTransaccion();
                    $sql->error = 'Error modificando contacto de cliente';
                    return false;
                }
                
                $sql->finalizarTransaccion();
                return $idCliente;
                
            } else {
                $sql->cancelarTransaccion();
                $sql->error = 'Error modificando sede de cliente...';
                return false;
                
            }
            
        } else {
            $sql->cancelarTransaccion();
            $sql->error = 'Error modificando el cliente';
            return false;
            
        }
        
    }

    /**
     *
     * Eliminar un cliente
     *
     * @param entero $id    Código interno o identificador del usuario en la base de datos
     * @return lógico       Indica si el procedimiento se pudo realizar correctamente o no
     *
     */
    public function eliminar() {
        global $sql;

        if (!isset($this->id)) {
            return NULL;
        }

        $sql->iniciarTransaccion();
        
        $eliminaSedes           = $sql->eliminar('sedes_cliente', 'id_cliente = "' . $this->id . '"');
        if (!$eliminaSedes){
            $sql->cancelarTransaccion();
            $sql->error = 'Error eliminando sedes';
            return false;            
        }
        
        $eliminaContactos       = $sql->eliminar('contactos_cliente', 'id_cliente = "' . $this->id . '"');
        if (!$eliminaContactos){
            $sql->cancelarTransaccion();
            $sql->error = 'Error eliminando contactos';
            return false;            
        }    
        
        $eliminaCliente = $sql->eliminar('clientes', 'id = "' . $this->id . '"');
        if (!$eliminaCliente){
            $sql->cancelarTransaccion();
            $sql->error = 'Error eliminando cliente';
            return false;            
        }           

        $sql->finalizarTransaccion();
        return true;
    }

    /**
     *
     * Listar los Clientes
     *
     * @param entero  $cantidad    Número de items a incluir en la lista (0 = todas las entradas)
     * @param arreglo $excepcion   Arreglo con los códigos internos o identificadores a omitir en la lista
     * @param cadena  $condicion   Condición adicional (SQL)
     * @return arreglo             Lista de usuarios
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

        /*         * *Validar que la excepción sea un arreglo y contenga elementos ** */
        if (isset($excepcion) && is_array($excepcion) && count($excepcion) && sizeof($excepcion) > 0) {
            $excepcion = implode(',', $excepcion);
            $condicion = 'p.id NOT IN (' . $excepcion . ') AND ';
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
            'p'     => 'clientes',
            'c'     => 'contactos_cliente',
            'pe'    => 'personas'
        );

        $columnas = array(
            'id'                    => 'p.id',
            'idCliente'             => 'p.id_cliente',
            'nombre'                => 'p.nombre',
            'nombreContacto'        => 'pe.primer_nombre',
            'apellidoContacto'      => 'pe.primer_apellido',
            'celularContacto'       => 'pe.celular',
            'regimen'               => 'p.regimen',
            'callCenter'            => 'p.call_center',
            'idUsuarioCreador'      => 'p.id_usuario_crea',
            'activo'                => 'p.activo'
        );

        if (!empty($condicionGlobal)) {
            $condicion .= $condicionGlobal . ' AND ';
        }

        $condicion .= 'c.id_cliente = p.id AND c.principal = "1"  AND  c.id_persona = pe.id';

        if (is_null($this->registrosConsulta)) {
            $sql->seleccionar($tablas, $columnas, $condicion);
            $this->registrosConsulta = $sql->filasDevueltas;
        }

        $consulta = $sql->seleccionar($tablas, $columnas, $condicion, '', $orden, $inicio, $cantidad);

        $lista = array();

        if ($sql->filasDevueltas) {

            while ($cliente = $sql->filaEnObjeto($consulta)) {
                $cliente->estado = ($cliente->activo) ? HTML::frase($textos->id('ACTIVO'), 'activo') : HTML::frase($textos->id('INACTIVO'), 'inactivo');

                $lista[] = $cliente;
            }
        }

        return $lista;
    }

    /**
     *
     * @global recurso =  $textos objeto global de textos
     * @global objeto $sesion_usuarioSesion = objeto tipo usuario gurdado en sesion
     * @param array $arregloRegistros = arreglo con los registros que se van a mostrar en la tabla
     * @param array $datosPaginacion = arreglo con la informaci{on para usarse en la paginacion, como por ejemplo, pagina inical, total registros, etc
     * @return string = codigo HTML con la tabla de la lista de clientes, tambien devuelve el codigo HTML del boton derecho 
     */
    public function generarTabla($arregloRegistros, $datosPaginacion = NULL) {
        global $textos, $sesion_usuarioSesion;
        //Declaracion de las columnas que se van a mostrar en la tabla
        $datosTabla = array(
            HTML::parrafo($textos->id('NIT'), 'centrado')                       => 'idCliente|p.id_cliente',
            HTML::parrafo($textos->id('NOMBRE_CLIENTE'), 'centrado')          => 'nombre|p.nombre',
            HTML::parrafo($textos->id('NOMBRE_CONTACTO'), 'centrado')           => 'nombreContacto|pe.primer_nombre',
            HTML::parrafo($textos->id('APELLIDO_CONTACTO'), 'centrado')         => 'apellidoContacto|pe.primer_apellido',
            HTML::parrafo($textos->id('CELULAR_CONTACTO'), 'centrado')          => 'celularContacto|pe.celular',
            HTML::parrafo($textos->id('CALL_CENTER'), 'centrado')               => 'callCenter|p.call_center',
            HTML::parrafo($textos->id('ESTADO'), 'centrado')                    => 'estado'
        );
        //ruta a donde se mandara la accion del doble click
        $ruta = '/ajax' . $this->urlBase . '/move';

        $adicionarSede = '';

        $puedeAdicionarSede = Perfil::verificarPermisosBoton('botonAdicionarSedeCliente');
        if ($puedeAdicionarSede || $sesion_usuarioSesion->id == 0) {
            $adicionarSede1 = HTML::formaAjax($textos->id('ADICIONAR_SEDE'), 'contenedorMenuAdicionarSede', 'adicionarSedeCliente', '', '/ajax/clientes/adicionarSede', array('id' => '', 'tablaEditarVisible' => '0'));
            $adicionarSede = HTML::contenedor($adicionarSede1, '', 'botonAdicionarSedeCliente');
        }

        $adicionarContacto = '';

        $puedeAdicionarContacto = Perfil::verificarPermisosBoton('botonAdicionarContactoCliente');
        if ($puedeAdicionarContacto || $sesion_usuarioSesion->id == 0) {
            $adicionarContacto1 = HTML::formaAjax($textos->id('ADICIONAR_CONTACTO'), 'contenedorMenuAdicionarPersona', 'adicionarContactoCliente', '', '/ajax/clientes/adicionarContacto', array('id' => '', 'tablaEditarVisible' => '0'));
            $adicionarContacto  = HTML::contenedor($adicionarContacto1, '', 'botonAdicionarContactoCliente');
        }

        $botonesExtras = array($adicionarSede, $adicionarContacto);

        $estilosColumnas    = array('texto-alineado-izquierda', 'descripcion-cliente', 'nombre-contacto texto-alineado-izquierda',  'apellido-contacto texto-alineado-izquierda'); 
        $tabla              = Recursos::generarTablaRegistros($arregloRegistros, $datosTabla, $ruta, $datosPaginacion, $estilosColumnas);
        $menuDerecho        = HTML::crearMenuBotonDerecho('CLIENTES', $botonesExtras);

        return $tabla . $menuDerecho;
    }


    
    /**
     * Metodo encargado de crear un objeto (degenerado) sede consultando en la BD
     *
     * @global recurso $sql objeto global sql
     * @param entero $id identificador de la sede
     * @return objeto degenerado sede (solo repositorio de información) 
     */
    public function cargarSedeCliente($id) {
        global $sql;
        if (empty($id)) {
            return NULL;
        }

        //cargar los datos de la sede en un objeto, el cual será devuelto por el metodo
        $tablas = array('s' => 'sedes_cliente', 'c' => 'lista_ciudades');
        $columnas = array('id' => 's.id', 'nombre' => 's.nombre', 'idCiudad' => 'c.id', 'ciudad' => 'c.cadena', 'direccion' => 's.direccion', 'telefono' => 's.telefono', 'celular' => 's.celular', 's.fax');
        $listaSedes = $sql->seleccionar($tablas, $columnas, 's.id_ciudad = c.id AND s.id = "' . $id . '"');
        $objetoSede = $sql->filaEnObjeto($listaSedes);

        return $objetoSede;
    }

}
