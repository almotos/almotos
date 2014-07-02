<?php

/**
 *
 * Clase que gestiona la informacion de las personas en el sistema
 * 
 * @package     FOM
 * @subpackage  Base
 * @author      Pablo Andres Velez Vidal
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2012 Genesys
 * @version     0.1
 *
 * */

/**
 * Clase que gestiona la informacion de las personas en el sistema. por persona estamos hablando de la entidad u objeto persona,
 * es decir, una persona o un objeto persona es el equivalente a cargar un objeto php con toda la informacion de un registro de la 
 * tabla personas, es decir, una vez obtenida toda la informacion de este registro, ya sea usando su identificador numerico, o lo mas
 * probable el documento de identidad de la persona, el objeto dispondra como atributos de instancia todos y cada uno de los valores 
 * almacenados en ese registro.
 * Esta clase es una clase que solo gestiona metodos crud para la interaccion con la BD, y su funcionamiento esta directamente relacionado
 * con la clase Usuario.
 */
class Persona {

    /**
     * Código interno o identificador de la persona en la base de datos
     * @var entero
     */
    public $id;

    /**
     * Código identificador de la persona ya sea cedula o tarjeta de identidad
     * @var entero
     */
    public $documentoIdentidad;

    /**
     * Código interno que relaciona que tipo de documento tiene la persona
     * @var entero
     */
    public $idTipoDocumento;

    /**
     * nombre del tipo de documento que tiene la persona
     * @var entero
     */
    public $tipoDocumento;

    /**
     * Código interno o identificador de la ciudad donde fue expedido el docuemnto de la persona
     * @var entero
     */
    public $idCiudadDocumento;

    /**
     * Ciudad donde fue expedido el docuemnto de la persona
     * @var entero
     */
    public $ciudadDocumento;

    /**
     * primer nombre de la persona
     * @var cadena
     */
    public $primerNombre;

    /**
     * segundo nombre de la persona
     * @var cadena
     */
    public $segundoNombre;

    /**
     * Primer apellido de la persona
     * @var cadena
     */
    public $primerApellido;

    /**
     * Segundo apellido de la persona
     * @var cadena
     */
    public $segundoApellido;

    /**
     * Nombre completo de la persona
     * @var entero
     */
    public $nombreCompleto;//*


    /**
     * Fecha de nacimiento de la persona
     * @var fecha
     */
    public $fechaNacimiento;

    /**
     * Código interno o identificador en la base de datos de la ciudad de residencia de la persona
     * @var entero
     */
    public $idCiudadResidencia;

    /**
     * Nombre de la ciudad de residencia de la persona
     * @var cadena
     */
    public $ciudadResidencia;

    /**
     * Código interno o identificador en la base de datos del estado de residencia de la persona
     * @var entero
     */
    public $idEstadoResidencia;

    /**
     * Nombre del estado de residencia de la persona
     * @var cadena
     */
    public $estadoResidencia;

    /**
     * Código interno o identificador en la base de datos de la persona de residencia de la persona
     * @var entero
     */
    public $idPaisResidencia;

    /**
     * Nombre de la persona de residencia de la persona
     * @var cadena
     */
    public $paisResidencia;

    /**
     * Direccion de la persona
     * @var cadena
     */
    public $direccion;

    /**
     * Telefono de la personad
     * @var cadena
     */
    public $telefono;

    /**
     * Descripción corta de su personalidad
     * @var cadena
     */
    public $celular;

    /**
     * Descripción corta de su personalidad
     * @var cadena
     */
    public $fax;

    /**
     * Dirección de correo electrónico de la persona
     * @var cadena
     */
    public $correo;

    /**
     * Género de la persona ('M' o 'F')
     * @var caracter
     */
    public $idGenero;


    /**
     * Código interno o identificador en la base de datos de la imagen de la persona
     * @var entero
     */
    public $idImagen;

    /**
     * Ruta de la imagen de la persona en tamaño normal
     * @var cadena
     */
    public $imagenPrincipal;

    /**
     * Ruta de la imagen de la persona en miniatura
     * @var cadena
     */
    public $imagenMiniatura;

    /**
     * Observaciones de la persona -(o del registro en general)
     * @var cadena
     */
    public $observaciones;
    
    /**
     * Codigo Iso del Pais al cual Pertenece el usuario
     * @var cadena
     */
    public $codigoIsoPais;
    
    /**
     * Indica si el registro de la persona se encuentra activa o no
     * @var enum '0', '1'
     */
    public $activo;

    
    /**
     *
     * Inicializar la persona
     *
     * @param entero $id Código interno o identificador de la persona en la base de datos
     *
     */
    public function __construct($id = NULL) {
        global $sql;

        //Saber el numero de registros        
        $this->registros = $sql->obtenerValor('personas', 'COUNT(id)', 'id != 0');
        //Saber el numero de registros activos        
        $this->registrosActivos = $sql->obtenerValor('personas', 'COUNT(id)', 'activo = "1"');

        if (isset($id)) {
            $this->cargar($id);
        }
        
    }

    /**
     *
     * Cargar los datos de la persona
     *
     * @param entero $id Código interno o identificador de la persona en la base de datos
     *
     */
    public function cargar($id = NULL) {
        global $sql, $configuracion, $textos;

        if (isset($id) && $sql->existeItem('personas', 'id', intval($id))) {
            
            $this->id = $id;

            $tablas = array(
                'p'  => 'personas',
                'c1' => 'ciudades',
                'c2' => 'lista_ciudades',
                'e1' => 'estados',
                'p1' => 'paises',
                'td' => 'tipos_documento',
                'i'  => 'imagenes'
            );

            $columnas = array(
                'documentoIdentidad'    => 'p.documento_identidad',
                'idTipoDocumento'       => 'p.id_tipo_documento',
                'tipoDocumento'         => 'td.nombre',
                'idCiudadDocumento'     => 'c2.id',
                'ciudadDocumento'       => 'c2.cadena',
                'primerNombre'          => 'p.primer_nombre',
                'segundoNombre'         => 'p.segundo_nombre',
                'primerApellido'        => 'p.primer_apellido',
                'segundoApellido'       => 'p.segundo_apellido',
                'fechaNacimiento'       => 'p.fecha_nacimiento',
                'idCiudadResidencia'    => 'p.id_ciudad_residencia',
                'ciudadResidencia'      => 'c1.nombre',
                'idEstadoResidencia'    => 'c1.id_estado',
                'estadoResidencia'      => 'e1.nombre',
                'idPaisResidencia'      => 'e1.id_pais',
                'paisResidencia'        => 'p1.nombre',
                'codigoIsoPais'         => 'p1.codigo_iso',
                'direccion'             => 'p.direccion',
                'telefono'              => 'p.telefono',
                'celular'               => 'p.celular',
                'fax'                   => 'p.fax',
                'correo'                => 'p.correo',
                'sitioWeb'              => 'p.sitio_web',
                'idGenero'              => 'p.genero',
                'idImagen'              => 'p.id_imagen',
                'imagen'                => 'i.ruta',
                'observaciones'         => 'p.observaciones',
                'activo'                => 'p.activo'
            );

            $condicion = 'p.id_tipo_documento = td.id AND p.id_ciudad_documento = c2.id AND p.id_ciudad_residencia = c1.id AND c1.id_estado = e1.id AND e1.id_pais = p1.id AND p.id_imagen = i.id AND p.id = "'.$id.'"';
            //$sql->depurar = true;
            $consulta = $sql->seleccionar($tablas, $columnas, $condicion);

            if ($sql->filasDevueltas) {
                $datos = $sql->filaEnObjeto($consulta);

                foreach ($datos as $propiedad => $valor) {
                    $this->$propiedad = $valor;
                }

                $this->nombreCompleto    = $this->primerNombre. ' ' . $this->segundoNombre. ' ' . $this->primerApellido. ' ' . $this->segundoApellido;
                $this->genero            = $textos->id('GENERO_' . $this->idGenero);
                $this->imagenPrincipal  = $configuracion['SERVIDOR']['media'] . $configuracion['RUTAS']['imagenesDinamicas'] . '/' . $this->imagen;
                $this->imagenMiniatura  = $configuracion['SERVIDOR']['media'] . $configuracion['RUTAS']['imagenesMiniaturas'] . '/' . $this->imagen;
            }
            
        }
        
    }

    /**
     *
     * Adicionar una persona
     *
     * @param  arreglo $datos       Datos de la persona a adicionar
     * @return entero               Código interno o identificador de la persona en la base de datos (NULL si hubo error)
     *
     */
    public function adicionar($datos) {
        global $sql;

        $consulta = $sql->insertar('personas', $datos);

        if ($consulta) {
            return $sql->ultimoId;
            
        } else {
            return NULL;
            
        }
        
    }

    /**
     *
     * Modificar una persona
     *
     * @param  arreglo $datos       Datos de la persona a modificar
     * @return lógico               Indica si el procedimiento se pudo realizar correctamente o no
     *
     */
    public function modificar($datos) {
        global $sql;

        if (!isset($this->id)) {
            return NULL;
        }

        $consulta = $sql->modificar('personas', $datos, "id = '" . $this->id . "'");
        
        return $consulta;
    }

    /**
     *
     * Eliminar una persona
     *
     * @param entero $id    Código interno o identificador de la persona en la base de datos
     * @return lógico       Indica si el procedimiento se pudo realizar correctamente o no
     *
     */
    public function eliminar() {
        global $sql;

        if (!isset($this->id)) {
            return NULL;
        }

        $consulta = $sql->eliminar('personas', "id = '" . $this->id . "'");
        return $consulta;
        
    }

}
