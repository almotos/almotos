<?php

/**
 * Rabo de gato, sidelitis cartica
 * @package     FOM
 * @subpackage  Proveedores del negocio
 * @author      Pablo Andrés Vélez Vidal <pavelez8@misena.edu.co>
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2012 Genesys Corporation.
 * @version     0.2
 * 
 * Un proveedor tiene 1..* sedes y tiene 1..* contactos
 * tablas principales: proveedores, sedes_proveedor, contacto_proveedor y cuentas_proveedor.
 * dato: la tabla contacto proveedor almacena 2 campos llaves, el id_proveedor, y el id_persona.
 * asi que para traer los datos de un contacto, se debe consultar a la persona que esta relacionada con este
 * contacto
 * 
 * */
class Proveedor {

    /**
     * Código interno o identificador del proveedor en la base de datos
     * @var entero
     */
    public $id;

    /**
     * URL relativa del módulo de proveedores
     * @var cadena
     */
    public $urlBase;

    /**
     * URL relativa de un proveedor específico
     * @var cadena
     */
    public $url;

    /**
     * Código interno del modulo proveedor
     * @var entero
     */
    public $idModulo;

    /**
     * Código interno o identificador del tipo de proveedor en la base de datos
     * @var entero
     */
    public $idProveedor;

    /**
     * Nombre del proveedor
     * @var objeto
     */
    public $nombre;
    
    /**
     * Razon social del proveedor
     * @var objeto
     */
    public $razonSocial;    

    /**
     * Código interno o identificador en la base de datos de la persona con la cual está relacionada el proveedor
     * @var entero
     */
    public $idContacto;

    /**
     * Representación (objeto) de la persona con la cual está relacionada el proveedor (contacto oficial)
     * @var objeto
     */
    public $contacto;

    /**
     * Código interno o identificador en la base de datos de la sede con la cual está relacionada el proveedor
     * @var entero
     */
    public $idSede;

    /**
     * Representación (objeto) de la sede con la cual está relacionada el proveedor (sede oficial)
     * @var objeto
     */
    public $sede;

    /**
     * tipo de persona del proveedor
     * @var boolean
     */
    public $tipoPersona;

    /**
     * regimen del proveeedor
     * @var boolean
     */
    public $regimen;

    /**
     * si el proveedor es autoretenedor
     * @var boolean
     */
    public $autoretenedor;

    /**
     * si el proveedor retiene fuente o no
     * @var boolean
     */
    public $retefuente;

    /**
     * si el proveedor retiene ica o no
     * @var boolean
     */
    public $reteica;
    
    /**
     * impuesto retecree asociado a la actividad economica
     * @var boolean
     */
    public $retecree;    
    
    /**
     * id la actividad economica de la dian a la que se dedica el proveedor
     * @var int 
     */
    public $idActividadEconomica;    
    
    /**
     * objeto actividad economica de la dian a la que se dedica el proveedor
     * @var int 
     */
    public $actividadEconomica;      

    /**
     * Call center del proveedor
     * @var boolean
     */
    public $callCenter;

    /**
     * id del usuario que crea el proveedor
     * @var boolean
     */
    public $idUsuarioCreador;

    /**
     * id del usuario que crea el proveedor
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
     * Indicador del orden del listado de proveedores
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
     * Determina si un proveedor se encuentra activo en el sistema o no
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
     * sedes -> arreglo de objetos que contiene la informacion de las sedes de un proveedor
     * @var entero
     */
    public $listaSedes = array();

    /**
     * contactos -> Determina si un proveedor tiene mas sedes aparte de la sede principal
     * @var entero
     */
    public $hayMasSedes;

    /**
     * contactos -> arreglo de objetos que contiene la informacion de los contactos de un proveedor
     * @var entero
     */
    public $listaContactos = array();

    /**
     * contactos -> Determina si el proveedor tiene mas contactos aparte del contacto principal
     * @var entero
     */
    public $hayMasContactos;

    /**
     * contactos -> arreglo de objetos que contiene la informacion de las cuentas de un proveedor
     * @var entero
     */
    public $listaCuentas = array();

    /**
     * contactos -> Determina si el proveedor tiene cuentas bancarias registradas
     * @var entero
     */
    public $tieneCuentasBancarias;
    
    /**
     * ** 16/06 = Casi deprecated, se sigue manejando igual solo el cree se amarra a la actividad economica
     * Ya tengo la tabla impuestos, que va a ser con la que muestre los checkbos
     * de la pestaña informacion tributaria. Necesitaria crear la tabla "proveedor_impuesto"
     * donde se almacenara que impuestos tiene determinado proveedor. Cuando se vaya a consultar
     * se debe verificar primero si es autoretenedor, en caso contrario, si se debe consultar que impuestos tiene
     * este determinado proveedor. 
     */

    /**
     *
     * Inicializar el proveedor
     *
     * @param entero $id Código interno o identificador del proveedor en la base de datos
     *
     */
    public function __construct($id = NULL) {
        global $sql, $modulo;
        $this->urlBase      = '/' . $modulo->url;
        $this->url          = $modulo->url;
        $this->idModulo     = $modulo->id;
        $proveedor = '';
        if (is_string($id) && isset($id) && $sql->existeItem('proveedores', 'id_proveedor', $id)) {
            $proveedor = $sql->obtenerValor('proveedores', 'id', 'id_proveedor = "' . $id . '"');
        } elseif (is_numeric($id)) {
            $proveedor = $id;
        }

        $this->registros            = $sql->obtenerValor('proveedores', 'COUNT(id)', 'id != "0"');
        $this->registrosActivos     = $sql->obtenerValor('proveedores', 'COUNT(id)', 'id != "0" AND activo = "1"');
        //establecer el valor del campo predeterminado para organizar los listados
        $this->ordenInicial         = 'nombre';


        if (!empty($proveedor)) {
            $this->cargar($proveedor);
        }
    }

    /**
     *
     * Cargar los datos del proveedor
     *
     * @param entero $id Código interno o identificador del proveedor en la base de datos
     *
     */
    public function cargar($id) {
        global $sql;

        if (isset($id) && $sql->existeItem('proveedores', 'id', intval($id))) {
            $this->id = $id;

            $tablas = array(
                'p'     => 'proveedores',
                'u'     => 'usuarios',
                'ae'    => 'actividades_economicas'
            );

            $columnas = array(
                'idProveedor'           => 'p.id_proveedor',
                'nombre'                => 'p.nombre',
                'razonSocial'           => 'p.razon_social',
                'regimen'               => 'p.regimen',
                'tipoPersona'           => 'p.tipo_persona',
                'autoretenedor'         => 'p.autoretenedor',
                'retefuente'            => 'p.retefuente',
                'reteica'               => 'p.reteica',
                'retecree'              => 'p.retecree',
                'callCenter'            => 'p.call_center',
                'idUsuarioCreador'      => 'p.id_usuario_crea',
                'idActividadEconomica'  => 'p.id_actividad_economica',
                'usuarioCreador'        => 'u.usuario',
                'fechaCreacion'         => 'p.fecha_creacion',
                'activo'                => 'p.activo',
                'observaciones'         => 'p.observaciones',
            );

            $condicion = 'p.id_actividad_economica = ae.id AND p.id_usuario_crea = u.id AND p.id = "' . $id . '"';
            
            $consulta = $sql->seleccionar($tablas, $columnas, $condicion);

            if ($sql->filasDevueltas) {
                $fila = $sql->filaEnObjeto($consulta);
                //asignar los valores de la consulta a los atributos del objeto 
                foreach ($fila as $propiedad => $valor) {
                    $this->$propiedad = $valor;
                }

                $this->actividadEconomica = new ActividadEconomica($this->idActividadEconomica);
                //obtener la info del contacto principal
                
                $objetoContacto = $sql->filaEnObjeto($sql->seleccionar(array('contactos_proveedor'), array('id', 'id_proveedor', 'id_persona', 'observaciones', 'principal'), 'id_proveedor = "' . $id . '" AND principal = "1"'));
                $this->idContacto = $objetoContacto->id;
                $this->contacto = new Persona($objetoContacto->id_persona);
                $this->contacto->observacionesContacto = $objetoContacto->observaciones;

                //obtener la info de la sede principal
                $tablas = array('s' => 'sedes_proveedor', 'c' => 'lista_ciudades');
                $columnas = array('id' => 's.id', 'nombre' => 's.nombre', 'id_ciudad' => 's.id_ciudad', 'idCiudad' => 'c.id', 'nombreCiudad' => 'c.cadena', 'direccion' => 's.direccion', 'telefono' => 's.telefono', 'celular' => 's.celular', 'fax' => 's.fax');
                $this->sede = $sql->filaEnObjeto($sql->seleccionar($tablas, $columnas, 's.id_ciudad = c.id AND s.id_proveedor = "' . $id . '" AND principal = "1"'));

                //verificar si el proveedor tiene mas contactos aparte del principal, en caso de que tenga, hacer la consulta y armar la lista de objetos
                $this->listaContactos = $sql->seleccionar(array('cp' => 'contactos_proveedor'), array('id' => 'id', 'idPersona' => 'id_persona', 'observaciones' => 'observaciones'), 'cp.id_proveedor = "' . $id . '" AND cp.principal = "0"');
                $this->hayMasContactos = $sql->filasDevueltas;

                //verificar si el proveedor tiene mas sedes aparte de la principal, en caso de que tenga, hacer la consulta y armar la lista de objetos
                $tablas1 = array('s' => 'sedes_proveedor', 'c' => 'lista_ciudades');
                $columnas1 = array('id' => 's.id', 'nombre' => 's.nombre', 'ciudad' => 'c.cadena', 'direccion' => 's.direccion', 'telefono' => 's.telefono', 'celular' => 's.celular', 's.fax');
                $this->listaSedes = $sql->seleccionar($tablas1, $columnas1, 's.id_ciudad = c.id AND s.id_proveedor = "' . $id . '" AND s.principal = "0"');
                $this->hayMasSedes = $sql->filasDevueltas;

                //verificar si el proveedor tiene cuentas bancarias, en caso de que tenga, hacer la consulta y armar la lista de objetos
                $tablas2 = array('cp' => 'cuentas_proveedor', 'b' => 'bancos');
                $columnas2 = array('id' => 'cp.id', 'banco' => 'b.nombre', 'numeroCuenta' => 'cp.numero_cuenta', 'tipoCuenta' => 'cp.tipo_cuenta');
                $this->listaCuentas = $sql->seleccionar($tablas2, $columnas2, 'cp.id_banco = b.id AND cp.id_proveedor = "' . $id . '"');
                $this->tieneCuentasBancarias = $sql->filasDevueltas;
            }//fin del filas devueltas
        }
    }

    /**
     * Registrar un proveedor con los datos básicos
     * @param  arreglo $datos       Datos del proveedor a registrar
     * @return entero               Código interno o identificador del proveedor en la base de datos (NULL si hubo error)
     */
    public function adicionar($datos) {
        global $sql, $sesion_usuarioSesion;

        $datosProveedor = array(
            'tipo_persona'              => $datos['tipo_persona'],
            'regimen'                   => $datos['regimen'],
            'razon_social'              => $datos['razon_social'],
            'id_actividad_economica'    => $datos['id_actividad_economica'],
            'call_center'               => $datos['call_center'],
            'id_usuario_crea'           => $sesion_usuarioSesion->id,
            'fecha_creacion'            => date('Y-m-d H:i:s'),
            'observaciones'             => $datos['observaciones']
        );
        
        if ($datos['tipo_persona'] == '1') {
            $datosProveedor['nombre']           = (!empty($datos['nombre_comercial'])) ? $datos['nombre_comercial'] : $datos['primer_nombre'] . ' ' . $datos['segundo_nombre'] . ' ' . $datos['primer_apellido'] . ' ' . $datos['segundo_apellido'];
            $datosProveedor['id_proveedor']     = (!empty($datos['id_proveedor'])) ? $datos['id_proveedor'] : $datos['documento_identidad'];
        } else {
            $datosProveedor['nombre']           = $datos['nombre_comercial'];
            $datosProveedor['id_proveedor']     = $datos['id_proveedor'];
        }        

        $datosProveedor['activo']       = (isset($datos['activo'])) ? '1' : '0';  

        if (isset($datos['autoretenedor'])) {
            $datosProveedor['autoretenedor']    = '1';
            $datosProveedor['retefuente']       = '0';
            $datosProveedor['reteica']          = '0';
            $datosProveedor['retecree']          = '0';
            
        } else {
            $datosProveedor['autoretenedor'] = '0';
            $datosProveedor['retefuente']   = (isset($datos['retefuente'])) ? '1' : '0';
            $datosProveedor['reteica']      = (isset($datos['reteica'])) ? '1' : '0';
            $datosProveedor['retecree']      =  (isset($datos['retecree'])) ? '1' : '0';                
            
        }

        //iniciar transaccion
        $sql->iniciarTransaccion();
        $consulta = $sql->insertar('proveedores', $datosProveedor);

        if ($consulta) {//si se pudo insertar el proveedor
            $idProveedor = $sql->ultimoId;

            if ($datos['nombre_sede'] == '') {
                $datos['nombre_sede'] = 'Sede Principal';
            }

            $datosSede = array(
                'id_proveedor'      => $idProveedor,
                'nombre'            => $datos['nombre_sede'],
                'id_ciudad'         => $datos['id_ciudad_sede'],
                'direccion'         => $datos['direccion_sede'],
                'telefono'          => $datos['telefono_sede'],
                'celular'           => $datos['celular_sede'],
                'fax'               => $datos['fax_sede'],
                'principal'         => '1'
            );

            $consulta = $sql->insertar('sedes_proveedor', $datosSede);

            if ($consulta) {//si se logro insertar la sede del proveedor
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
                    'id_proveedor'      => $idProveedor,
                    'id_persona'        => $idPersona,
                    'observaciones'     => $datos['observaciones_contacto'],
                    'principal'         => '1'
                );
                $consulta = $sql->insertar('contactos_proveedor', $datosContacto);

                if ($consulta) {//si se inserto el contacto del proveedor, la sede y el proveedor
                    if (!empty($datos['cuentas_proveedor'])) {//si se van a insertar cuentas del proveedor
                        //se recibe una cadna separada por caracteres especiales y se separa volviendola un arreglo
                        $cadenaCuentas = explode('[', $datos['cuentas_proveedor']);
                        $largo = sizeof($cadenaCuentas) - 1;

                        $consultaSql = 'INSERT INTO fom_cuentas_proveedor (id ,id_banco , id_proveedor ,numero_cuenta ,tipo_cuenta) VALUES ';
                        //se recorre el arreglo, para nuevamente separar cada posicion que es tambien una cadena por otro caracter especial
                        //para consultar el id del banco e ir armando la consulta sql 
                        for ($i = 0; $i < $largo; $i++) {
                            $cuentas = explode('|', $cadenaCuentas[$i]);
                            $consultaSql .= '(NULL, "' . $cuentas[0] . '", "' . $idProveedor . '", "' . $cuentas[1] . '", "' . $cuentas[2] . '")';
                            if ($i != $largo - 1) {
                                $consultaSql .= ', ';
                            }
                        }
                        
                        $consultaSql .= ';';

                        $consulta = $sql->ejecutar($consultaSql);

                        if (!$consulta) {
                            $sql->cancelarTransaccion();
                            $sql->error = 'Error insertando las cuentas del proveedor';
                            return false;
                            
                        } else {
                            //finalizar la transaccion
                            $sql->finalizarTransaccion();                   
                            return $idProveedor;
                            
                        }
                    }
                    
                    //finalizar la transaccion
                    $sql->finalizarTransaccion();                   
                    return $idProveedor;
                    
                } else {
                    $sql->error = 'Error insertando contacto de proveedor';
                    $sql->cancelarTransaccion();
                    return false;
                    
                }
                
            } else {
                $sql->error = 'Error insertando sede de proveedor, eliminando el registro del proveedor...';
                $sql->cancelarTransaccion();
                return false;
                
            }
            
        } else {
            $sql->error = 'Error insertando proveedor';
            $sql->cancelarTransaccion();
            return false;
          
        }
        
    }

    /**
     * Modificar la informaci{on básica de un proveedor
     * 
     * @param  arreglo $datos       Datos del proveedor a modificar
     * @return entero               Código interno o identificador del proveedor en la base de datos (NULL si hubo error)
     */
    public function modificar($datos) {
        global $sql;

        if (!isset($this->id)) {
            return NULL;
        }

        $datosProveedor = array(
            'tipo_persona'              => $datos['tipo_persona'],
            'regimen'                   => $datos['regimen'],
            'razon_social'              => $datos['razon_social'],
            'call_center'               => $datos['call_center'],
            'id_actividad_economica'    => $datos['id_actividad_economica'],
            'observaciones'             => $datos['observaciones']
        );

        if ($datos['tipo_persona'] == '1') {//persona juridica
            $datosProveedor['nombre']           = (!empty($datos['nombre_comercial'])) ? $datos['nombre_comercial'] : $datos['primer_nombre'] . ' ' . $datos['segundo_nombre'] . ' ' . $datos['primer_apellido'] . ' ' . $datos['segundo_apellido'];
            $datosProveedor['id_proveedor']     = (!empty($datos['id_proveedor'])) ? $datos['id_proveedor'] : $datos['documento_identidad'];
        } else {
            $datosProveedor['nombre']           = $datos['nombre_comercial'];
            $datosProveedor['id_proveedor']     = $datos['id_proveedor'];
        }     

        $datosProveedor['activo']       = (isset($datos['activo'])) ? '1' : '0';     

        if (isset($datos['autoretenedor'])) {
            $datosProveedor['autoretenedor']    = '1';
            $datosProveedor['retefuente']       = '0';
            $datosProveedor['reteica']          = '0';
            $datosProveedor['retecree']          = '0';
            
        } else {
            $datosProveedor['autoretenedor'] = '0';
            $datosProveedor['retefuente']   = (isset($datos['retefuente'])) ? '1' : '0';
            $datosProveedor['reteica']      = (isset($datos['reteica'])) ? '1' : '0';
            $datosProveedor['retecree']      =  (isset($datos['retecree'])) ? '1' : '0';            
            
        }
        $sql->iniciarTransaccion();
        $consulta = $sql->modificar('proveedores', $datosProveedor, 'id = "' . $this->id . '"');

        if ($consulta) {//si se pudo insertar el proveedor
            $idProveedor = $this->id;

            if ($datos['nombre_sede'] == '') {
                $datos['nombre_sede'] = 'Sede Principal';
            }

            $datosSede = array(
                'id_proveedor'          => $idProveedor,
                'nombre'                => $datos['nombre_sede'],
                'id_ciudad'             => $datos['id_ciudad_sede'],
                'direccion'             => $datos['direccion_sede'],
                'telefono'              => $datos['telefono_sede'],
                'celular'               => $datos['celular_sede'],
                'fax'                   => $datos['fax_sede'],
                'principal'             => '1'
            );

            $consulta = $sql->modificar('sedes_proveedor', $datosSede, 'id = "' . $this->sede->id . '"');

            if ($consulta) {//si se logro modificar la sede del proveedor
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
                    'id_proveedor'      => $idProveedor,
                    'id_persona'        => $idPersona,
                    'observaciones'     => $datos['observaciones_contacto'],
                    'principal'         => '1'
                );

                $consulta = $sql->modificar('contactos_proveedor', $datosContacto, 'id = "' . $this->idContacto . '"');


                if (!$consulta) {//si se inserto el contacto del proveedor, la sede y el proveedor
                    $sql->cancelarTransaccion();
                    $sql->error = 'Error modificando contacto de proveedor';
                    return false;
                }
                
                $sql->finalizarTransaccion();
                return $idProveedor;
                
            } else {
                $sql->cancelarTransaccion();
                $sql->error = 'Error modificando sede de proveedor...';
                return false;
                
            }
            
        } else {
            $sql->cancelarTransaccion();
            $sql->error = 'Error modificando el proveedor';
            return false;
            
        }
        
    }

    /**
     *
     * Eliminar un proveedor
     *
     * @param entero $id    Código interno o identificador del usuario en la base de datos
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
        $arreglo1 = array('facturas_compras', 'id_proveedor = "'.$this->id.'"', $textos->id('FACTURAS_COMPRA'));//arreglo del que sale la info a consultar
        $arreglo2 = array('ordenes_compra', 'id_proveedor = "'.$this->id.'"', $textos->id('ORDENES_COMPRA'));//arreglo del que sale la info a consultar
        $arregloIntegridad = array($arreglo1, $arreglo2);//arreglo de arreglos para realizar las consultas de integridad referencial, (ver documentacion de metodo)
        $integridad = Recursos::verificarIntegridad($textos->id('PROVEEDOR'), $arregloIntegridad); 

        /**
         * si hay problemas con la integridad referencial, la variable integridad tiene como valor,
         * un texto diciendo que tabla contiene n cantidad de relaciones con esta
        */
        if ($integridad != "") {
            $respuestaEliminar['mensaje'] = $integridad;
            return $respuestaEliminar;
        }

        $sql->iniciarTransaccion();
        
        $eliminaSedes = $sql->eliminar('sedes_proveedor', 'id_proveedor = "' . $this->id . '"');
        
        if (!$eliminaSedes){
            $sql->cancelarTransaccion("Fallo en el archivo " . __FILE__ . " en la linea " .  __LINE__);
            //$sql->error = 'Error eliminando sedes';
            return $respuestaEliminar;            
        }
        
        $eliminaContactos       = $sql->eliminar('contactos_proveedor', 'id_proveedor = "' . $this->id . '"');
        
        if (!$eliminaContactos){
            $sql->cancelarTransaccion("Fallo en el archivo " . __FILE__ . " en la linea " .  __LINE__);
            //$sql->error = 'Error eliminando contactos';
            return $respuestaEliminar;            
        }   
        
        $eliminaCuentas         = $sql->eliminar('cuentas_proveedor', 'id_proveedor = "' . $this->id . '"');
        
        if (!$eliminaCuentas){
            $sql->cancelarTransaccion("Fallo en el archivo " . __FILE__ . " en la linea " .  __LINE__);
            //$sql->error = 'Error eliminando cuentas';
            return $respuestaEliminar;            
        } 
        
        //Aqui se eliminan las facturas temporales que este proveedor tenga amarradas
        $idsFacturasTemporales = $sql->seleccionar(array('facturas_temporales_compra'), 
                                                   array('id'), 
                                                   'id_proveedor = "'.$this->id.'"');
        //si el proveedor tenia facturas temporales de compra
        if ($sql->filasDevueltas) {

            while ($factTemp = $sql->filaEnObjeto($idsFacturasTemporales)) {
                FacturaTemporalCompra::eliminarFacturaTemporal($factTemp->id);
            }
        }        
        
        
        $eliminaProveedor = $sql->eliminar('proveedores', 'id = "' . $this->id . '"');
        
        if (!$eliminaProveedor){
            $sql->cancelarTransaccion("Fallo en el archivo " . __FILE__ . " en la linea " .  __LINE__);
            //$sql->error = 'Error eliminando proveedor';
            return $respuestaEliminar;            

        } else {
           $sql->finalizarTransaccion();
           $respuestaEliminar['respuesta'] = true;
           return $respuestaEliminar;

       }  
    }        
         

        //Fin del metodo eliminar proveedor 
       

    /**
     *
     * Listar los Proveedores
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
            'p'     => 'proveedores',
            'c'     => 'contactos_proveedor',
            'pe'    => 'personas'
        );

        $columnas = array(
            'id'                    => 'p.id',
            'idProveedor'           => 'p.id_proveedor',
            'nombre'                => 'p.nombre',
            'nombreContacto'        => 'pe.primer_nombre',
            'apellidoContacto'      => 'pe.primer_apellido',
            'celularContacto'       => 'pe.celular',
            'regimen'               => 'p.regimen',
            'autoretenedor'         => 'p.autoretenedor',
            'callCenter'            => 'p.call_center',
            'idUsuarioCreador'      => 'p.id_usuario_crea',
            'activo'                => 'p.activo'
        );

        if (!empty($condicionGlobal)) {
            $condicion .= $condicionGlobal . ' AND ';
        }

        $condicion .= 'c.id_proveedor = p.id AND c.principal = "1"  AND  c.id_persona = pe.id';

        if (is_null($this->registrosConsulta)) {
            $sql->seleccionar($tablas, $columnas, $condicion);
            $this->registrosConsulta = $sql->filasDevueltas;
        }
//        $sql->depurar = true;
        $consulta = $sql->seleccionar($tablas, $columnas, $condicion, '', $orden, $inicio, $cantidad);

        $lista = array();

        if ($sql->filasDevueltas) {

            while ($proveedor = $sql->filaEnObjeto($consulta)) {
                $proveedor->estado = ($proveedor->activo) ? HTML::frase($textos->id('ACTIVO'), 'activo') : HTML::frase($textos->id('INACTIVO'), 'inactivo');

                $lista[] = $proveedor;
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
     * @return string = codigo HTML con la tabla de la lista de proveedores, tambien devuelve el codigo HTML del boton derecho 
     */
    public function generarTabla($arregloRegistros, $datosPaginacion = NULL) {
        global $textos, $sesion_usuarioSesion;
        //Declaracion de las columnas que se van a mostrar en la tabla
        $datosTabla = array(
            HTML::parrafo($textos->id('NIT'), 'centrado')                       => 'idProveedor|p.id_proveedor',
            HTML::parrafo($textos->id('NOMBRE_PROVEEDOR'), 'centrado')          => 'nombre|p.nombre',
            HTML::parrafo($textos->id('NOMBRE_CONTACTO'), 'centrado')           => 'nombreContacto|pe.primer_nombre',
            HTML::parrafo($textos->id('APELLIDO_CONTACTO'), 'centrado')         => 'apellidoContacto|pe.primer_apellido',
            HTML::parrafo($textos->id('CELULAR_CONTACTO'), 'centrado')          => 'celularContacto|pe.celular',
            HTML::parrafo($textos->id('CALL_CENTER'), 'centrado')               => 'callCenter|p.call_center',
            HTML::parrafo($textos->id('ESTADO'), 'centrado')                    => 'estado'
        );
        //ruta a donde se mandara la accion del doble click
        $ruta = '/ajax' . $this->urlBase . '/move';

        $adicionarSede = '';

        $puedeAdicionarSede = Perfil::verificarPermisosBoton('botonAdicionarSedeProveedor');
        if ($puedeAdicionarSede || $sesion_usuarioSesion->id == 0) {
            $adicionarSede1 = HTML::formaAjax($textos->id('ADICIONAR_SEDE'), 'contenedorMenuAdicionarSede', 'adicionarSedeProveedor', '', '/ajax/proveedores/adicionarSede', array('id' => '', 'tablaEditarVisible' => '0'));
            $adicionarSede = HTML::contenedor($adicionarSede1, '', 'botonAdicionarSedeProveedor');
        }

        $adicionarContacto = '';

        $puedeAdicionarContacto = Perfil::verificarPermisosBoton('botonAdicionarContactoProveedor');
        if ($puedeAdicionarContacto || $sesion_usuarioSesion->id == 0) {
            $adicionarContacto1 = HTML::formaAjax($textos->id('ADICIONAR_CONTACTO'), 'contenedorMenuAdicionarPersona', 'adicionarContactoProveedor', '', '/ajax/proveedores/adicionarContacto', array('id' => '', 'tablaEditarVisible' => '0'));
            $adicionarContacto  = HTML::contenedor($adicionarContacto1, '', 'botonAdicionarContactoProveedor');
        }

        $adicionarCuenta = '';

        $puedeAdicionarCuenta = Perfil::verificarPermisosBoton('botonAdicionarCuentaProveedor');
        if ($puedeAdicionarCuenta || $sesion_usuarioSesion->id == 0) {
            $adicionarCuenta1 = HTML::formaAjax($textos->id('ADICIONAR_CUENTA'), 'contenedorMenuAdicionarCuenta', 'adicionarCuentaProveedor', '', '/ajax/proveedores/adicionarCuenta', array('id' => '', 'tablaEditarVisible' => '0'));
            $adicionarCuenta = HTML::contenedor($adicionarCuenta1, '', 'botonAdicionarCuentaProveedor');
        }

        $botonesExtras = array($adicionarSede, $adicionarContacto, $adicionarCuenta);

        $estilosColumnas    = array('texto-alineado-izquierda', 'descripcion-proveedor', 'nombre-contacto texto-alineado-izquierda',  'apellido-contacto texto-alineado-izquierda'); 
        $tabla              = Recursos::generarTablaRegistros($arregloRegistros, $datosTabla, $ruta, $datosPaginacion, $estilosColumnas);
        $menuDerecho        = HTML::crearMenuBotonDerecho('PROVEEDORES', $botonesExtras);

        return $tabla . $menuDerecho;
    }


    
    /**
     * Metodo encargado de crear un objeto (degenerado) sede consultando en la BD
     *
     * @global recurso $sql objeto global sql
     * @param entero $id identificador de la sede
     * @return objeto degenerado sede (solo repositorio de información) 
     */
    public function cargarSedeProveedor($id) {
        global $sql;
        if (empty($id)) {
            return NULL;
        }

        //cargar los datos de la sede en un objeto, el cual será devuelto por el metodo
        $tablas = array('s' => 'sedes_proveedor', 'c' => 'lista_ciudades');
        $columnas = array('id' => 's.id', 'nombre' => 's.nombre', 'idCiudad' => 'c.id', 'ciudad' => 'c.cadena', 'direccion' => 's.direccion', 'telefono' => 's.telefono', 'celular' => 's.celular', 's.fax');
        $listaSedes = $sql->seleccionar($tablas, $columnas, 's.id_ciudad = c.id AND s.id = "' . $id . '"');
        $objetoSede = $sql->filaEnObjeto($listaSedes);

        return $objetoSede;
    }

}
