<?php

/**
 *
 * @package     FOM
 * @subpackage  Usuarios del sistema
 * @author      Pablo Andrés Vélez Vidal <pavelez8@misena.edu.co>
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2012 Genesys Corporation.
 * @version     0.2
 *
 * */

/**
 * Clase que gestiona la informacion de los usuarios en el sistema. Los usuarios representan la informacion de
 * las personas que podran hacer uso del sistema, es decir, un usuario es una persona que tiene un registro en
 * la base de datos, especificamente en la tabla usuarios. Esta informacion almacena entre otras cosas un nombre de usuario
 * y una contraseña, lo cual permite a la  persona dueña del usuario iniciar sesion en el sistema ingresando sus credenciales,
 * las cuales son contrastadas contra la informacion almacenada en la base de datos. Esta clase es de las mas importantes en todo
 * el sistema, pues es en una instancia de esta clase en donde se almacena la informacion del usuario logeado, y con esto el sistema
 * sabe cual es la informacion de la persona que esta utilizando el sistema en el momento, y tambien puede permitir o bloquear el acceso
 * a informacion. Mas especificamente esta instancia de la clase usuario es almacenada en una variable de sesion llamada 
 * $sesion_usuarioSesion, esta variable es creada justo en el momento en que un usuario inicia sesion en el sistema y persiste durante
 * toda la sesion del usuario hasta que haga click en "cerrar sesion" o pase un tiempo determinado en las opciones del php ini (86400 sec)
 * sin realizar actividades en el sistema. Esta clase tiene una relacion directa con la clase Persona, pues como se meciono anteriormente
 * cada usuario es una persona, asi que esta clase dispone de un atributo llamado persona, el cual representa una instancia de la clase Persona
 * y dicho objeto contiene toda la informacion de la persona dueña del usuario.
 * 
 * Esta clase contiene metodos de gestion basica de informacion con la base de datos, pero tambien contiene metodos complejos de validacion
 * de informacion ytambien de renderizado de bloques de codigo especificas para el usuario que tiene la sesion activa.
 */
class Usuario {

    /**
     * Código interno o identificador del usuario en la base de datos
     * @var entero
     */
    public $id;

    /**
     * URL relativa del módulo de usuarios
     * @var cadena
     */
    public $urlBase;

    /**
     * URL relativa de un usuario específico
     * @var cadena
     */
    public $url;

    /**
     * Código interno o identificador del tipo de usuario en la base de datos
     * @var entero
     */
    public $idTipo;

    /**
     * Nombre del tipo de usuario
     * @var cadena
     */
    public $tipo;

    /**
     * Nombre de usuario para el inicio de sesión
     * @var cadena
     */
    public $usuario;

    /**
     * Código interno o identificador en la base de datos de la persona con la cual está relacionada el usuario
     * @var entero
     */
    public $idPersona;

    /**
     * Código interno del modulo usuario
     * @var entero
     */
    public $idModulo;

    /**
     * Representación (objeto) de la persona con la cual está relacionada el usuario
     * @var objeto
     */
    public $persona;

    /**
     * Fecha de registro de la persona en el sistema
     * @var boolean
     */
    public $fechaRegistro;

    /**
     * Determina la sede con la cual inició sesión el usuario
     * @var entero
     */
    public $sede;

    /**
     * Indicador del orden cronológio de la lista de usuarios
     * @var lógico
     */
    public $listaAscendente = true;

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
     * Determina si un usuario se encuentra activo en el sistema o no
     * @var entero
     */
    public $activo;
    
    /**
     * Determina si un usuario se encuentra habilittado para realizar ventas en el sistema
     * @var entero
     */
    public $vendedor;  
    
    /**
     * Determina si un usuario debe ser notificado ante el vencimiento de una factura de compra
     * @var entero
     */
    public $notificacionVtoFC;      

    /**
     * Determina si un usuario debe ser notificado ante el vencimiento de una factura de venta
     * @var entero
     */
    public $notificacionVtoFV;       
    
    /**
     * Determina si el usuario se ha bloqeuado por exceso de intentos de acceso
     * @var entero
     */
    public $bloqueado;

    /**
     * Determina el descuento maximo que el usuario esta autorizado a otorgar
     * @var entero
     */
    public $dctoMaximo;
    
    /**
     * Determina el porcentaje de ganancia de un vendedor respecto al total de sus ventas
     * @var entero
     */
    public $porcentajeGanancia;    
    
    /**
     * Observaciones del usuario
     * @var entero
     */
    public $observaciones;    

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
     * Inicializar el usuario
     *
     * @param entero $id Código interno o identificador del usuario en la base de datos
     *
     */
    public function __construct($id = NULL) {
        global $sql, $modulo;
        
        $this->urlBase      = "/" . $modulo->url;
        $this->url          = $modulo->url;
        $this->idModulo     = $modulo->id;
        
        $usuario = "";
        
        if (is_string($id) && isset($id) && $sql->existeItem("usuarios", "usuario", $id)) {
            $usuario = $sql->obtenerValor("usuarios", "id", "usuario = '$id'");
            
        } elseif (is_numeric($id)) {
            $usuario = $id;
            
        }

        $this->registros        = $sql->obtenerValor("usuarios", "COUNT(id)", "id != '0'");
        $this->registrosActivos = $sql->obtenerValor("usuarios", "COUNT(id)", "id != '0' AND activo = '1'");
        //establecer el valor del campo predeterminado para organizar los listados
        $this->ordenInicial = "usuario";
        
        if (!empty($usuario)) {
            $this->cargar($usuario);
            
        }
        
    }

    /**
     * Cargar los datos del usuario
     * @param entero $id Código interno o identificador del usuario en la base de datos
     */
    public function cargar($id = NULL) {
        global $sql;

        if (isset($id) && $sql->existeItem("usuarios", "id", intval($id))) {
            $this->id = $id;

            $tablas = array(
                "u" => "usuarios",
                "t" => "perfiles"
            );

            $columnas = array(
                "idTipo"            => "u.id_tipo",
                "tipo"              => "t.nombre",
                "usuario"           => "u.usuario",
                "idPersona"         => "u.id_persona",
                "fechaRegistro"     => "u.fecha_registro",
                "activo"            => "u.activo",
                "vendedor"          => "u.vendedor",
                "notificacionVtoFC" => "u.notificacion_vto_fc",
                "notificacionVtoFV" => "u.notificacion_vto_fv",
                'dctoMaximo'        => 'u.dcto_maximo',
                'observaciones'     => 'u.observaciones',
                'porcentajeGanancia' => 'u.porcentaje_ganancia',
            );

            $condicion = "u.id_tipo = t.id AND u.id = '$id'";
            //$sql->depurar = true;
            $consulta = $sql->seleccionar($tablas, $columnas, $condicion);

            if ($sql->filasDevueltas) {
                $fila = $sql->filaEnObjeto($consulta);

                foreach ($fila as $propiedad => $valor) {
                    $this->$propiedad = $valor;
                }

                $this->url = $this->urlBase . "/" . $this->usuario;
                $this->persona = new Persona($this->idPersona);
            }
        }
    }
    
    /**
     * Funcion encargada de agregar a un objeto usuario el atributo sede
     * @global resource $sql
     * @param entero $idSede = identificador de la sede
     * @return void 
     */
    public function setSede($idSede) {
        global $sql;

        if (!isset($this->id) || !$sql->existeItem('sedes_empresa', 'id', $idSede)) {
            return NULL;
        }

        $this->sede = new SedeEmpresa($idSede);
       
    }

    /**
     *
     * Validar un usuario
     *
     * @param  cadena $usuario      Nombre de acceso del usuario a validar
     * @param  cadena $contrasena   Contraseña del usuario a validar
     * @return entero               Código interno o identificador del usuario en la base de datos (-1 si el usuario está inactivo, NULL si hubo error)
     *
     */
    public function validar($usuario, $contrasena) {
        global $sql;

        if (is_string($usuario) && !preg_match("/[^a-z]/", $usuario) && is_string($contrasena) && !preg_match("/[^a-zA-Z0-9]/", $contrasena)) {
            $consulta = $sql->seleccionar(array("usuarios"), array("id", "activo", "bloqueado"), "usuario='$usuario' AND contrasena='$contrasena'");

            if ($sql->filasDevueltas) {
                $datos = $sql->filaEnObjeto($consulta);
                /*                 * ******** Verifico si el usuario esta bloqueado y lo desbloqueo porque coinciden el usuario y la contraseña**************** */
                if ($datos->bloqueado) {
                    $datosUser["bloqueado"] = '0';
                    $consulta = $sql->modificar("usuarios", $datosUser, "usuario = '" . $usuario . "'");
                }
                
                if ($datos->activo) {
                    return $datos->id;
                } else {
                    return -1;
                }
                
            }
            
        }

        return NULL;
    }

    /**
     *
     * Validar si un usuario que trata de ingresar al sistema esta bloqueado
     *
     * @param  cadena $usuario      Nombre de acceso del usuario a validar
     * @param  cadena $contrasena   Contraseña del usuario a validar
     * @return entero               Código interno o identificador del usuario en la base de datos (-1 si el usuario está inactivo, NULL si hubo error)
     *
     */
    public function validarUsuarioBloqueado($usuario) {
        global $sql;

        if (is_string($usuario) && !preg_match("/[^a-z]/", $usuario)) {
            $consulta = $sql->seleccionar(array("usuarios"), array("bloqueado"), "usuario='$usuario'");

            if ($sql->filasDevueltas) {
                $datos = $sql->filaEnObjeto($consulta);

                if ($datos->bloqueado) {
                    return true;
                } else {
                    return false;
                }
            }
        }

        return NULL;
    }

    /**
     *
     * Registrar un usuario con los datos básicos
     *
     * @param  arreglo $datos       Datos del usuario a registrar
     * @return entero               Código interno o identificador del usuario en la base de datos (NULL si hubo error)
     *
     */
    public function adicionar($datos) {
        global $sql, $archivo_imagen;

        $datosPersona["activo"] = (isset($datos["activo"])) ? "1" : "0";

        $datos["id_ciudad_documento"]  = $sql->obtenerValor("lista_ciudades", "id", "cadena = '" . $datos["id_ciudad_documento"] . "'");
        $datos["id_ciudad_residencia"] = $sql->obtenerValor("lista_ciudades", "id", "cadena = '" . $datos["id_ciudad_residencia"] . "'");
        
        $existe = $sql->existeItem('personas', 'documento_identidad', $datos['documento_identidad']);
        
        //iniciar transaccion
        $sql->iniciarTransaccion();
        
        $datosPersona = array(
            "id_tipo_documento"     => $datos["id_tipo_documento"],
            "id_ciudad_documento"   => $datos["id_ciudad_documento"],
            "primer_nombre"         => $datos["primer_nombre"],
            "segundo_nombre"        => $datos["segundo_nombre"],
            "primer_apellido"       => $datos["primer_apellido"],
            "segundo_apellido"      => $datos["segundo_apellido"],
            "fecha_nacimiento"      => $datos["fecha_nacimiento"],
            "id_ciudad_residencia"  => $datos["id_ciudad_residencia"],
            "direccion"             => $datos["direccion"],
            "telefono"              => $datos["telefono"],
            "celular"               => $datos["celular"],
            "fax"                   => $datos["fax"],
            "correo"                => $datos["correo"],
            "genero"                => $datos["genero"],
            "id_imagen"             => 0
        );        

       if (isset($archivo_imagen) && !empty($archivo_imagen["tmp_name"])) {
            $objImagen = new Imagen();
            
            $datosImagen = array(
                "idRegistro"    => "0",
                "modulo"        => "5",
                "titulo"        => "imagen de Perfil",
                "descripcion"   => "imagen del perfil"
            );
            
            $idImagen = $objImagen->adicionar($datosImagen);
            
            $datosPersona['id_imagen'] = $idImagen;
            
        }          
        
        if ($existe) {
            $idPersona = $sql->obtenerValor('personas', 'id', 'documento_identidad = "' . $datos['documento_identidad'] . '"');
            
            $query = $sql->modificar('personas', $datosPersona, 'id = "' . $idPersona . '"');
            
            if (!$query) {
                $sql->cancelarTransaccion();
                return false;                
            }

        } else {
            
            $datosPersona["documento_identidad"] = $datos["documento_identidad"];

            $query = $sql->insertar('personas', $datosPersona);
            
            if (!$query) {
                $sql->cancelarTransaccion();
                return false;                
            }            
            
            $idPersona = $sql->ultimoId;
        }        
        
        $persona = new Persona($idPersona);

        $datosUsuario = array(
            "usuario"               => $datos["usuario"],
            "id_tipo"               => $datos["id_tipo"],
            "id_persona"            => $persona->id,
            "contrasena"            => $datos["contrasena1"],
            "fecha_registro"        => date("Y-m-d H:i:s"),
            "activo"                => "1",
            "dcto_maximo"           => $datos["dcto_maximo"],
            "porcentaje_ganancia"   => $datos["porcentaje_ganancia"],
            "observaciones"         => $datos["observaciones"]
        );
        
        $datosUsuario["vendedor"] = (isset($datos["vendedor"])) ? "1" : "0";
        
        $datosUsuario["notificacion_vto_fc"] = (isset($datos["notificacion_vto_fc"])) ? "1" : "0";
        $datosUsuario["notificacion_vto_fv"] = (isset($datos["notificacion_vto_fv"])) ? "1" : "0";
               

        $consulta = $sql->insertar("usuarios", $datosUsuario);

        $idItem = $sql->ultimoId;

        if ($consulta) {
            //insertar los privilegios al usuario que se acaba de crear con base
            $perfil = new Perfil($datos["id_tipo"]);
            $adicionarPrivilegios = $perfil->agregarPrivilegiosUsuario($idItem);

            if ($adicionarPrivilegios){
                $sql->finalizarTransaccion();            
                return $idItem;
                
            } else {
                $sql->cancelarTransaccion();
                return false;                
            }                 
            
        } else {
            $sql->cancelarTransaccion();
            return false;                            
            
        }
    
    }

    /**
     *
     * Modificar la información en la BD de un usuario
     *
     * @param  arreglo $datos       Datos del usuario a modificar
     * @return lógico               Indica si el procedimiento se pudo realizar correctamente o no
     *
     */
    public function modificar($datos) {
        global $sql, $archivo_imagen;

        if (!isset($this->id)) {
            return NULL;
        }

        if (isset($datos["activo"])) {
            $datosPersona["activo"] = "1";
        } else {
            $datosPersona["activo"] = "0";
        }
        
        $existe = $sql->existeItem('personas', 'documento_identidad', $datos['documento_identidad']);
        
        $datos["id_ciudad_documento"]  = $sql->obtenerValor("lista_ciudades", "id", "cadena = '" . $datos["id_ciudad_documento"] . "'");
        $datos["id_ciudad_residencia"] = $sql->obtenerValor("lista_ciudades", "id", "cadena = '" . $datos["id_ciudad_residencia"] . "'");
                
        
        $datosPersona = array(
            "id_tipo_documento"     => $datos["id_tipo_documento"],
            "id_ciudad_documento"   => $datos["id_ciudad_documento"],
            "primer_nombre"         => $datos["primer_nombre"],
            "segundo_nombre"        => $datos["segundo_nombre"],
            "primer_apellido"       => $datos["primer_apellido"],
            "segundo_apellido"      => $datos["segundo_apellido"],
            "fecha_nacimiento"      => $datos["fecha_nacimiento"],
            "id_ciudad_residencia"  => $datos["id_ciudad_residencia"],
            "direccion"             => $datos["direccion"],
            "telefono"              => $datos["telefono"],
            "celular"               => $datos["celular"],
            "fax"                   => $datos["fax"],
            "correo"                => $datos["correo"],
            "genero"                => $datos["genero"],
        );        
        
        //iniciar transaccion
        $sql->iniciarTransaccion();
        
        if (isset($archivo_imagen) && !empty($archivo_imagen["tmp_name"])) {
            $objImagen = new Imagen();
            
            $datosImagen = array(
                "idRegistro"    => "0",
                "modulo"        => "5",
                "titulo"        => "imagen de Perfil",
                "descripcion"   => "imagen del perfil"
            );
            
            $idImagen = $objImagen->adicionar($datosImagen);
            
            $datosPersona['id_imagen'] = $idImagen;
            
        }        
        
        if ($existe) {
            $idPersona = $sql->obtenerValor('personas', 'id', 'documento_identidad = "' . $datos['documento_identidad'] . '"');
            
            $query = $sql->modificar('personas', $datosPersona, 'id = "' . $idPersona . '"');
            
            if (!$query) {
                $sql->cancelarTransaccion();
                return false;                
            }

        } else {
            $datosPersona["documento_identidad"] = $datos["documento_identidad"];
            
            $query = $sql->insertar('personas', $datosPersona);
            
            if (!$query) {
                $sql->cancelarTransaccion();
                return false;                
            }            
            
            $idPersona = $sql->ultimoId;
        }        
        
        $datosUsuario = array();
        
        $datosUsuario["vendedor"]   = (isset($datos["vendedor"])) ? "1" : "0";
        $datosUsuario["activo"]     = (isset($datos["activo"])) ? "1" : "0";
        
        $datosUsuario["notificacion_vto_fc"] = (isset($datos["notificacion_vto_fc"])) ? "1" : "0";
        $datosUsuario["notificacion_vto_fv"] = (isset($datos["notificacion_vto_fv"])) ? "1" : "0";             

        $datosUsuario["id_tipo"]                = $datos["id_tipo"];
        $datosUsuario["id_persona"]             = $idPersona;
        $datosUsuario["usuario"]                = $datos["usuario"];
        $datosUsuario["dcto_maximo"]            = $datos["dcto_maximo"];
        $datosUsuario["porcentaje_ganancia"]    = $datos["porcentaje_ganancia"];
        $datosUsuario["observaciones"]          = $datos["observaciones"];

        if (!empty($datos["contrasena1"])) {
            $datosUsuario["contrasena"] = $datos["contrasena1"];
        }
        
        //verificar si el tipo de usuario es diferente al que habia antes
        if ($this->idTipo != $datos["id_tipo"]){
            //modificar los privilegios al usuario que se acaba de crear con base
            $perfil = new Perfil($datos["id_tipo"]);
            $modificarPrivilegios = $perfil->modificarPrivilegiosUsuario($this->id);

            if (!$modificarPrivilegios){
                $sql->cancelarTransaccion();
                return false;
            }

        }

        $consulta = $sql->modificar("usuarios", $datosUsuario, "id = '" . $this->id . "'");

        if (!$consulta) {
            $sql->cancelarTransaccion();
            return false;                
        }           

        $sql->finalizarTransaccion();
        return $consulta;
    }

    /**
     *
     * Eliminar un usuario
     *
     * @param entero $id    Código interno o identificador del usuario en la base de datos
     * @return lógico       Indica si el procedimiento se pudo realizar correctamente o no
     *
     */
    /*public function eliminar() {
        global $sql;

        if (!isset($this->id)) {
            return NULL;
        }

        //eliminar cada uno de los Documentos pertenecientes a este usuario en caso de que los tenga
        $tablas     = array("documentos");
        $columnas   = array("id" => "id");
        $condicion  = "id_usuario = " . $this->id . "";
        $consulta   = $sql->seleccionar($tablas, $columnas, $condicion);
        
        $sql->iniciarTransaccion();

        //eliminar permisos del usuario sobre modulos y componentes
        $query1 = $sql->eliminar("permisos_componentes_usuarios", "id_usuario = '" . $this->id . "'");
        $query2 = $sql->eliminar("permisos_modulos_usuarios", "id_usuario = '" . $this->id . "'");
        
        //eliminar notificaciones del usuario
        $query3 = $sql->eliminar("notificaciones", "id_usuario = '" . $this->id . "'");

        //eliminar eventos del usuario
        $query4 = $sql->eliminar("eventos", "id_usuario = '" . $this->id . "'");        
        
        if (!$query1 || !$query2 || !$query3 || !$query4) {
            $sql->cancelarTransaccion();
            return false;  
            
        } else {
            $query = $sql->eliminar("usuarios", "id = '" . $this->id . "'");
            
            if (!$query){
                $sql->cancelarTransaccion();
                return false;
                
            }
            
            if ($sql->filasDevueltas) {
                while ($docs = $sql->filaEnObjeto($consulta)) {
                    $doc = new Documento($docs->id);
                    $doc->eliminar();
                    
                }
                
            }
            
        }               
        
        $sql->finalizarTransaccion();
        
        return true;
        
    }*/
    
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
        
        $arreglo1   = array('personas',                      'id_usuario = "'.$this->id.'"', $textos->id('PERSONAS'));//arreglo del que sale la info a consultar
        $arreglo2   = array('facturas_compras',              'id_usuario = "'.$this->id.'"', $textos->id('FACTURAS_COMPRAS'));
        $arreglo3   = array('facturas_temporales_compra',    'id_usuario = "'.$this->id.'"', $textos->id('FACTURAS_TEMPORALES_COMPRA'));
        $arreglo4   = array('facturas_temporales_venta',     'id_usuario = "'.$this->id.'"', $textos->id('FACTURAS_TEMPORALES_VENTA'));
        $arreglo5   = array('facturas_venta',                'id_usuario = "'.$this->id.'"', $textos->id('FACTURAS_VENTA'));
        $arreglo6   = array('ordenes_compra',                'id_usuario = "'.$this->id.'"', $textos->id('ORDENES_COMPRA'));
        $arreglo7   = array('paginas',                       'id_usuario = "'.$this->id.'"', $textos->id('PAGINAS'));
        $arreglo8   = array('proveedores',                   'id_usuario = "'.$this->id.'"', $textos->id('PROVEEDORES'));
        $arreglo9   = array('clientes',                      'id_usuario = "'.$this->id.'"', $textos->id('CLIENTES'));
        $arreglo10  = array('cotizaciones',                  'id_usuario = "'.$this->id.'"', $textos->id('COTIZACIONES')); 
                
        $arregloIntegridad  = array($arreglo1, $arreglo2, $arreglo3, $arreglo4, $arreglo5, $arreglo6, $arreglo7,
                                    $arreglo8, $arreglo9, $arreglo10);//arreglo de arreglos para realizar las consultas de integridad referencial, (ver documentacion de metodo)
        
        $integridad = Recursos::verificarIntegridad($textos->id('ARTICULO'), $arregloIntegridad);  
        
        /**
         * si hay problemas con la integridad referencial, la variable integridad tiene como valor,
         * un texto diciendo que tabla contiene n cantidad de relaciones con esta
         */
        if ($integridad != "") {
            $respuestaEliminar['mensaje'] = $integridad;
            return $respuestaEliminar;
        }        

        //eliminar cada uno de los Documentos pertenecientes a este usuario en caso de que los tenga
        $tablas     = array("documentos");
        $columnas   = array("id" => "id");
        $condicion  = "id_usuario = " . $this->id . "";
        $consulta   = $sql->seleccionar($tablas, $columnas, $condicion);
        
        $sql->iniciarTransaccion();

        //eliminar permisos del usuario sobre modulos y componentes
        $query1 = $sql->eliminar("permisos_componentes_usuarios", "id_usuario = '" . $this->id . "'");
        $query2 = $sql->eliminar("permisos_modulos_usuarios", "id_usuario = '" . $this->id . "'");
        
        //eliminar notificaciones del usuario
        $query3 = $sql->eliminar("notificaciones", "id_usuario = '" . $this->id . "'");

        //eliminar eventos del usuario
        $query4 = $sql->eliminar("eventos", "id_usuario = '" . $this->id . "'");        
        
        if (!$query1 || !$query2 || !$query3 || !$query4) {
            $sql->cancelarTransaccion("Fallo en el archivo " . __FILE__ . " en la linea " .  __LINE__);
            return $respuestaEliminar;  
            
        } else {
            $query = $sql->eliminar("usuarios", "id = '" . $this->id . "'");
            
            if (!$query){
                $sql->cancelarTransaccion("Fallo en el archivo " . __FILE__ . " en la linea " .  __LINE__);
                return $respuestaEliminar;
                
            }
            
            if ($sql->filasDevueltas) {
                while ($docs = $sql->filaEnObjeto($consulta)) {
                    $doc = new Documento($docs->id);
                    $doc->eliminar();
                    
                }
                
            }
            
        }               
        
        $sql->finalizarTransaccion();
        
        return $respuestaEliminar;
        
    }
    
    /**
     *
     * Inactivar un usuario
     *
     * @return lógico       Indica si el procedimiento se pudo realizar correctamente o no
     *
     */
    public function inactivar() 
    {
        global $sql;

        if (!isset($this->id)) {
            return false;
        }
        
        $sql->iniciarTransaccion();
        
        $datos = array('activo' => '0');
        
        $consulta = $sql->modificar('usuarios', $datos, 'id = "' . $this->id . '"');

        if (!$consulta) {
            $sql->cancelarTransaccion();
            return false;
            
        } 
        
        $sql->finalizarTransaccion();
        return true;
        
    }      

    /**
     *
     * Listar los usuarios
     *
     * @param entero  $cantidad    Número de usuarios a incluir en la lista (0 = todas las entradas)
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
            $condicion = "";
        }

        /*         * *Validar que la excepción sea un arreglo y contenga elementos ** */
        if (isset($excepcion) && is_array($excepcion) && count($excepcion) && sizeof($excepcion) > 0) {
            $excepcion = implode(",", $excepcion);
            $condicion .= "u.id NOT IN ($excepcion) AND ";
        }

        /*         * * Definir el orden de presentación de los datos ** */
        if (!isset($orden)) {
            $orden = $this->ordenInicial;
        }
        if ($this->listaAscendente) {
            $orden = "$orden ASC";
        } else {
            $orden = "$orden DESC";
        }

        $tablas = array(
            "u" => "usuarios",
            "t" => "perfiles",
            "p" => "personas"
        );

        $columnas = array(
            "id"                    => "u.id",
            "idTipo"                => "u.id_tipo",
            "tipo"                  => "t.nombre",
            "idPersona"             => "u.id_persona",
            "nombre"                => "p.primer_nombre",
            "apellido"              => "p.primer_apellido",
            "usuario"               => "u.usuario",
            "idPersona"             => "u.id_persona",
            "fechaRegistro"         => "UNIX_TIMESTAMP(u.fecha_registro)",
            "activo"                => "u.activo",
            "vendedor"              => "u.vendedor",
            
        );

        if (!empty($condicionGlobal)) {
            $condicion .= $condicionGlobal . " AND ";
        }

        $condicion .= "u.id_tipo = t.id AND u.id_persona = p.id AND u.id != '0'";

        if (is_null($this->registrosConsulta)) {
            $sql->seleccionar($tablas, $columnas, $condicion);
            $this->registrosConsulta = $sql->filasDevueltas;
        }

        $consulta = $sql->seleccionar($tablas, $columnas, $condicion, "", $orden, $inicio, $cantidad);

        $lista = array();

        if ($sql->filasDevueltas) {

            while ($usuario = $sql->filaEnObjeto($consulta)) {
                $usuario->url       = $this->urlBase . "/" . $usuario->usuario;
                $usuario->urlBase   = $this->urlBase;
                $usuario->persona   = new Persona($usuario->idPersona);
                $usuario->estado    = ($usuario->activo) ? HTML::frase($textos->id("ACTIVO"), "activo") : HTML::frase($textos->id("INACTIVO"), "inactivo");

                $lista[] = $usuario;
            }
        }

        return $lista;
    }

    /**
     * Metodo que cuenta cuantos mensajes nuevos tiene el usuario
     * @global type $sql
     * @return entero = cantidad de nuevos mensajes 
     */
    public function contarNuevosMensajes() {
        global $sql;

        if (!isset($this->id)) {
            return NULL;
        }

        $consulta = $sql->filaEnObjeto($sql->seleccionar(array("mensajes"), array("cantidad" => "COUNT(id)"), "id_usuario_destinatario = " . $this->id . " AND leido = '0'"));
        //Recursos::escribirTxt($sql->sentenciaSql);
        $cantidad = $consulta->cantidad;

        return $cantidad;
    }


    /**
     * Metodo que devuelve en codigo html una imagen que representa los nuevos mensajes y la cantidad
     * @global type $sql
     * @global type $textos
     * @global type $configuracion
     * @return cadena = codigo html con la imagen que representa los nuevos mensajes y la cantidad 
     */
    public function mostrarNuevosMensajes() {

        if (!isset($this->id)) {
            return NULL;
        }

        $cantidad = self::contarNuevosMensajes();

        if ($cantidad > 0) {
            $codigo = HTML::contenedor(HTML::frase($cantidad, "cantidadNuevosMensajes"), "contenedorNuevosMensajes");
            
        } else {
            $codigo = HTML::contenedor(HTML::frase("  ", "cantidadNuevosMensajes"), "contenedorSinMensajes");
            
        }

        return $codigo;
    }



    /**
     * Metodo que cuenta cuantas notificaciones nuevas tiene el usuario
     * @global type $sql
     * @return entero = cantidad de nuevas notificaciones 
     */
    public function contarNuevasNotificaciones() {
        global $sql;

        if (!isset($this->id)) {
            return NULL;
        }
        $consulta = $sql->obtenerValor("notificaciones", "COUNT(id)", "id_usuario = " . $this->id . " AND leido = '0'");

        $cantidad = $consulta;

        return $cantidad;
    }


    /**
     * Metodo que devuelve en codigo html una imagen representando las 
     * nuevas notificaciones y la cantidad de ellas
     * @global type $sql
     * @global type $textos
     * @global type $configuracion
     * @return cadena = codigo html con la imagen que representa las notificaciones y la cantidad de nuevas notificaciones
     */
    public function mostrarNuevasNotificaciones() {
        //global $sql;

        if (!isset($this->id)) {
            return NULL;
        }

        $cantidad = self::contarNuevasNotificaciones();

        if ($cantidad > 0) {
            
            $codigo = HTML::contenedor(HTML::frase($cantidad, "cantidadNuevasNotificaciones"), "contenedorNuevasNotificaciones");
        } else {
            $codigo = HTML::contenedor(HTML::frase("  ", "cantidadNuevasNotificaciones"), "contenedorSinNotificaciones");
            
        }


        return $codigo;
    }


    /**
     * Funcion que termina la sesion de un usuario
     */
    public static function cerrarSesion() {
        Sesion::terminar();
        $respuesta              = array();
        $respuesta["error"]     = NULL;
        $respuesta["accion"]    = "redireccionar";
        $respuesta["destino"]   = "/";
        
        Servidor::enviarJSON($respuesta);
    }

    /**
     * 
     * @global type $textos
     * @param type $arregloRegistros
     * @param type $datosPaginacion
     * @return type
     */
    public function generarTabla($arregloRegistros, $datosPaginacion = NULL) 
    {
        global $textos;
        //Declaracion de las columnas que se van a mostrar en la tabla
        $datosTabla = array(
            HTML::parrafo($textos->id("TIPO"),      "centrado") => "tipo|t.nombre",
            HTML::parrafo($textos->id("NOMBRE"),    "centrado") => "nombre|p.primer_nombre",
            HTML::parrafo($textos->id("APELLIDO"),  "centrado") => "apellido|p.primer_apellido",
            HTML::parrafo($textos->id("USUARIO"),   "centrado") => "usuario|u.usuario",
            HTML::parrafo($textos->id("ESTADO"),    "centrado") => "estado"
        );
        //ruta a donde se mandara la accion del doble click
        $ruta = "/ajax" . $this->urlBase . "/move";

        $tabla = Recursos::generarTablaRegistros($arregloRegistros, $datosTabla, $ruta, $datosPaginacion);
        $menuDerecho = HTML::crearMenuBotonDerecho("USUARIOS");

        return $tabla . $menuDerecho;
    }
    
    /**
     * 
     * @global resource $sql
     * @return type
     */
    public function listaUsuariosNotificacionEventoFC()
    {
        global $sql;
        
        $query = $sql->seleccionar("usuarios", "id", "notificacion_vto_fc = '1'");
        
        $lista = array();

        if ($sql->filasDevueltas) {

            while ($usuario = $sql->filaEnObjeto($query)) {                
                $lista[] = $usuario->id;
            }
        }
        
        return $lista;

    }

}
