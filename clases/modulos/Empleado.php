<?php

/**
 *
 * @package     FOLCS
 * @subpackage  Empleados
 * @author      Pablo Andrés Vélez Vidal <pavelez8@misena.edu.co>
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2012 Genesys corporation
 * @version     0.2
 *
 * Clase encargada de gestionar la información del listado de los empleados del negocio. En este módulo se pueden
 * agregar, consultar, eliminar o modificar la información de los empleados del negocio, ademas se puede adjuntar un archivo
 * digital de la hoja de vida del empleado. Este modulo hará parte del modulo padre "Gestión Humana". Esta clase tiene el atributo
 * idPersona, relacionado con el modulo "Personas", es decir, un empleado es una persona.
 * 
 * tabla principal: empleados.
 * tablas relacionadas: tipos_de_empleados, cargos, personas.
 * 
 **/


class Empleado {

    /**
     * Código interno o identificador del país en la base de datos
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
     * Código interno o identificador del modulo
     * @var entero
     */
    public $idModulo;

    /**
     * id del tipo de empleado
     * @var cadena
     */
    public $idTipoEmpleado;
    
    /**
     * id del tipo de empleado
     * @var cadena
     */
    public $tipoEmpleado;    


     /**
     * identificador de la persona relacionada con el empleado
     * @var cadena
     */
    public $idPersona;
    
    /**
     * Objeto que representa la persona y su informacion
     * @var cadena
     */
    public $persona;

     /**
     * fecha incio de labores
     * @var cadena
     */
    public $fechaInicio;  
    
     /**
     * Fecha de finalizacion de labores del empleado
     * @var cadena
     */
    public $fechaFin;  
    
    
     /**
     * identificador del cargo desempeñado por el trabajador
     * @var cadena
     */
    public $idCargo;
    
    /**
     * nombre del cargo que tiene el empleado
     * @var cadena
     */
    public $cargo;    
    
     /**
     * identificador de la sede del empleado
     * @var cadena
     */
    public $idSede;
    
    /**
     * Nombre de la sede a la que pertenece el empleado
     * @var cadena
     */
    public $sede;    
   

     /**
     * Salario devengado por el empleado
     * @var cadena
     */
    public $salario;     
    
     /**
     * Salario devengado por el empleado
     * @var cadena
     */
    public $observaciones;       

    /**
     * Indicador del orden cronológio de la lista de empleados
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
     *
     * Inicializar el empleado
     *
     * @param entero $id Código interno o identificador de la sedeEmpresa en la base de datos
     *
     */
    public function __construct($id = NULL) {
        global $sql, $modulo;
        $this->urlBase  = '/'.$modulo->url;
        $this->url      = $modulo->url;
        $this->idModulo = $modulo->id;
       
        $this->registros = $sql->obtenerValor('empleados', 'COUNT(id)', 'id != "0"');
        //establecer el valor del campo predeterminado para organizar los listados
        $this->ordenInicial = 'fecha_inicio';

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

        if (isset($id) && $sql->existeItem('empleados', 'id', intval($id))) {

            $tablas = array(
                'e'  => 'empleados',
                's'  => 'sedes_empresa',
                'c'  => 'cargos',
                'te' => 'tipos_empleado'
            );

            $columnas = array(
                'id'                => 'e.id',
                'idTipoEmpleado'    => 'e.id_tipo_empleado',
                'tipoEmpleado'      => 'te.nombre',
                'idPersona'         => 'e.id_persona',
                'fechaInicio'       => 'e.fecha_inicio',
                'fechaFin'          => 'e.fecha_fin',
                'idCargo'           => 'e.id_cargo',
                'cargo'             => 'c.nombre',
                'idSede'            => 'e.id_sede',
                'sede'              => 's.nombre',
                'salario'           => 'e.salario',
                'activo'            => 'e.activo',
                'observaciones'     => 'e.observaciones'
            );

            $condicion = 'e.id_sede = s.id AND e.id_cargo = c.id AND e.id_tipo_empleado = te.id AND e.id = "'.$id.'"';

            $consulta = $sql->seleccionar($tablas, $columnas, $condicion);

            if ($sql->filasDevueltas) {
                $fila = $sql->filaEnObjeto($consulta);

                foreach ($fila as $propiedad => $valor) {
                    $this->$propiedad = $valor;
                }
                
                $this->persona = new Persona($this->idPersona);
                
                $this->url = $this->urlBase.'/'.$this->usuario;
            }
        }
    }




    /**
     *
     * Adicionar un empleado
     *
     * @param  arreglo $datos       Datos del empleado a adicionar
     * @return entero               Código interno o identificador del empleado en la base de datos (NULL si hubo error)
     *
     */
    public function adicionar($datos) {
        global $sql;      
        
        if(empty($datos)){
            return NULL;
        }
        
        $existe = $sql->existeItem('personas', 'documento_identidad', $datos['documento_identidad']);        
        if($existe){
            $idPersona = $sql->obtenerValor('personas', 'id', 'documento_identidad = "'.$datos['documento_identidad'].'"');
            $idCiudadDocumento  = $sql->obtenerValor('lista_ciudades', 'id', 'cadena = "'.$datos['id_ciudad_documento'].'"');     
            $idCiudadResidencia = $sql->obtenerValor('lista_ciudades', 'id', 'cadena = "'.$datos['id_ciudad_residencia'].'"');
            
            $datosPersona = array(
//                'documento_identidad'       => $datos['documento_identidad'],
//                'id_tipo_documento'         => $datos['id_tipo_documento'],
                'id_ciudad_documento'       => $idCiudadDocumento,
                'primer_nombre'             => $datos['primer_nombre'],
                'segundo_nombre'            => $datos['segundo_nombre'],
                'primer_apellido'           => $datos['primer_apellido'],
                'segundo_apellido'          => $datos['segundo_apellido'],
                'id_ciudad_residencia'      => $idCiudadResidencia,
                'direccion'                 => $datos['direccion'],
                'telefono'                  => $datos['telefono'],
                'celular'                   => $datos['celular'],
                'fax'                       => $datos['fax'],
                'correo'                    => $datos['correo']
            );
            $sql->modificar('personas', $datosPersona, 'id = "'.$idPersona.'"'); 
            
        }else{

            $idCiudadDocumento  = $sql->obtenerValor('lista_ciudades', 'id', 'cadena = "'.$datos['id_ciudad_documento'].'"');     
            $idCiudadResidencia = $sql->obtenerValor('lista_ciudades', 'id', 'cadena = "'.$datos['id_ciudad_residencia'].'"');

            $datosPersona = array(
                'documento_identidad'       => $datos['documento_identidad'],
                'id_tipo_documento'         => $datos['id_tipo_documento'],
                'id_ciudad_documento'       => $idCiudadDocumento,
                'primer_nombre'             => $datos['primer_nombre'],
                'segundo_nombre'            => $datos['segundo_nombre'],
                'primer_apellido'           => $datos['primer_apellido'],
                'id_ciudad_residencia'      => $idCiudadResidencia,
                'direccion'                 => $datos['direccion'],
                'telefono'                  => $datos['telefono'],
                'celular'                   => $datos['celular'],
                'fax'                       => $datos['fax'],
                'correo'                    => $datos['correo']
            );

            $consulta = $sql->insertar('personas', $datosPersona);
            $idPersona = $sql->ultimoId;
        }
        
        $datosEmpleado = array(
            'id_tipo_empleado'  => $sql->obtenerValor('tipos_empleado', 'id', 'nombre = "'.$datos['id_tipo_empleado'].'"'),
            'id_persona'        => $idPersona,
            'fecha_inicio'      => $datos['fecha_inicio'],
            'id_cargo'          => $sql->obtenerValor('cargos', 'id', 'nombre = "'.$datos['id_cargo'].'"'),
            'id_sede'           => $sql->obtenerValor('sedes_empresa', 'id', 'nombre = "'.$datos['id_sede'].'"'),
            'salario'           => $datos['salario'],
            'observaciones'     => $datos['observaciones']
        );
        
       if (isset($datos['activo'])) {
            $datosEmpleado['activo']       = '1';           

        } else {
            $datosEmpleado['activo']       = '0';
        }        

        $consulta = $sql->insertar('empleados', $datosEmpleado);
        $idEmpleado = $sql->ultimoId;

        if ($consulta) {
            return $idEmpleado;

        } else {
            return NULL;
        }
    }



    /**
     *
     * Modificar un Empleado
     *
     * @param  arreglo $datos       Datos del empleado a modificar
     * @return lógico               Indica si el procedimiento se pudo realizar correctamente o no
     *
     */
    public function modificar($datos) {
        global $sql;

        if (!isset($this->id) || empty($datos)) {
            return NULL;
        }
        
        $existe = $sql->existeItem('personas', 'documento_identidad', $datos['documento_identidad']);        
        if($existe){
            $idPersona = $sql->obtenerValor('personas', 'id', 'documento_identidad = "'.$datos['documento_identidad'].'"');
            $idCiudadDocumento  = $sql->obtenerValor('lista_ciudades', 'id', 'cadena = "'.$datos['id_ciudad_documento'].'"');     
            $idCiudadResidencia = $sql->obtenerValor('lista_ciudades', 'id', 'cadena = "'.$datos['id_ciudad_residencia'].'"');
            
            $datosPersona = array(
//                'documento_identidad'       => $datos['documento_identidad'],
                'id_tipo_documento'         => $datos['id_tipo_documento'],
                'id_ciudad_documento'       => $idCiudadDocumento,
                'primer_nombre'             => $datos['primer_nombre'],
                'segundo_nombre'            => $datos['segundo_nombre'],
                'primer_apellido'           => $datos['primer_apellido'],
                'segundo_apellido'          => $datos['segundo_apellido'],
                'id_ciudad_residencia'      => $idCiudadResidencia,
                'direccion'                 => $datos['direccion'],
                'telefono'                  => $datos['telefono'],
                'celular'                   => $datos['celular'],
                'fax'                       => $datos['fax'],
                'correo'                    => $datos['correo']
            );
            $sql->modificar('personas', $datosPersona, 'id = "'.$idPersona.'"'); 
            
        }else{

            $idCiudadDocumento  = $sql->obtenerValor('lista_ciudades', 'id', 'cadena = "'.$datos['id_ciudad_documento'].'"');     
            $idCiudadResidencia = $sql->obtenerValor('lista_ciudades', 'id', 'cadena = "'.$datos['id_ciudad_residencia'].'"');

            $datosPersona = array(
                'documento_identidad'       => $datos['documento_identidad'],
                'id_tipo_documento'         => $datos['id_tipo_documento'],
                'id_ciudad_documento'       => $idCiudadDocumento,
                'primer_nombre'             => $datos['primer_nombre'],
                'segundo_nombre'            => $datos['segundo_nombre'],
                'primer_apellido'           => $datos['primer_apellido'],
                'id_ciudad_residencia'      => $idCiudadResidencia,
                'direccion'                 => $datos['direccion'],
                'telefono'                  => $datos['telefono'],
                'celular'                   => $datos['celular'],
                'fax'                       => $datos['fax'],
                'correo'                    => $datos['correo']
                
            );

            $consulta = $sql->insertar('personas', $datosPersona);
            $idPersona = $sql->ultimoId;
        }
        
        $datosEmpleado = array(
            'id_tipo_empleado'  => $sql->obtenerValor('tipos_empleado', 'id', 'nombre = "'.$datos['id_tipo_empleado'].'"'),
            'id_persona'        => $idPersona,
            'fecha_inicio'      => $datos['fecha_inicio'],            
            'fecha_fin'         => $datos['fecha_fin'],
            'id_cargo'          => $sql->obtenerValor('cargos', 'id', 'nombre = "'.$datos['id_cargo'].'"'),
            'id_sede'           => $sql->obtenerValor('sedes_empresa', 'id', 'nombre = "'.$datos['id_sede'].'"'),
            'salario'           => $datos['salario'],
            'observaciones'     => $datos['observaciones']
        );
        
       if (isset($datos['activo'])) {
            $datosEmpleado['activo']       = '1';           

        } else {
            $datosEmpleado['activo']       = '0';
            $datosEmpleado['fecha_fin']    = date('Y-m-d');
        }        

        $consulta = $sql->modificar('empleados', $datosEmpleado, 'id = "'.$this->id.'"');

        if ($consulta) {
            return $this->id;

        } else {
            return NULL;
        }
    }



    /**
     *
     * Eliminar un empleado
     *
     * @param entero $id    Código interno o identificador del empleado  en la base de datos
     * @return lógico       Indica si el procedimiento se pudo realizar correctamente o no
     *
     */
    public function eliminar() {
        global $sql;

        if (!isset($this->id)) {
            return NULL;
        }

        $consulta = $sql->eliminar('empleados', 'id = "'.$this->id.'"');
        return $consulta;
    }





    /**
     *
     * Listar las empleados
     *
     * @param entero  $cantidad    Número de ciudadesa incluir en la lista (0 = todas las entradas)
     * @param arreglo $excepcion   Arreglo con los códigos internos o identificadores a omitir en la lista
     * @param cadena  $condicion   Condición adicional (SQL)
     * @return arreglo             Lista de empleados
     *
     */
    public function listar($inicio = 0, $cantidad = 0, $excepcion = NULL, $condicionGlobal = NULL, $orden = NULL) {
        global $sql, $textos;

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
            $condicion = 'e.id NOT IN ('.$excepcion.') AND ';
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
            'e'  => 'empleados',
            's'  => 'sedes_empresa',
            'c'  => 'cargos',
            'te' => 'tipos_empleado',
            'pe' => 'personas'
        );

        $columnas = array(
            'id'                => 'e.id',
            'idTipoEmpleado'    => 'e.id_tipo_empleado',
            'tipoEmpleado'      => 'te.nombre',
            'idPersona'         => 'e.id_persona',
            'fechaInicio'       => 'e.fecha_inicio',
            'fechaFin'          => 'e.fecha_fin',
            'idCargo'           => 'e.id_cargo',
            'cargo'             => 'c.nombre',
            'idSede'            => 'e.id_sede',
            'sede'              => 's.nombre',
            'contacto'          => 'CONCAT(pe.primer_nombre, " ", pe.segundo_nombre, " ", pe.primer_apellido, " ", pe.segundo_apellido)',
            'celular'           => 'pe.celular',
            'correo'            => 'pe.correo',
            'salario'           => 'e.salario',
            'activo'            => 'e.activo'
        );


        if (!empty($condicionGlobal)) {
            
            $condicion .= $condicionGlobal.' AND ';
        } 
        
        $condicion .= 'e.id_sede = s.id AND e.id_cargo = c.id AND e.id_tipo_empleado = te.id AND e.id_persona = pe.id';    
       

        if (is_null($this->registrosConsulta)) {
            $sql->seleccionar($tablas, $columnas, $condicion);
            $this->registrosConsulta = $sql->filasDevueltas;
        }

        $consulta = $sql->seleccionar($tablas, $columnas, $condicion, '', $orden, $inicio, $cantidad);

        $lista = array();
        if ($sql->filasDevueltas) {           
            while ($objeto = $sql->filaEnObjeto($consulta)) {
                $objeto->url           = $this->urlBase.'/'.$objeto->id;
                $objeto->estado =  ($objeto->activo) ? HTML::frase($textos->id('ACTIVO'), 'activo') : HTML::frase($textos->id('INACTIVO'), 'inactivo');

                $lista[]   = $objeto;
            }
        }

        return $lista;

    }
    
    
    public function generarTabla($arregloRegistros, $datosPaginacion = NULL){
        global $textos;
        //Declaracion de las columnas que se van a mostrar en la tabla
        $datosTabla = array(                      
            HTML::parrafo( $textos->id('TIPO_EMPLEADO')        ,  'centrado' ) => 'tipoEmpleado|te.nombre',
            HTML::parrafo( $textos->id('NOMBRES')              ,  'centrado' ) => 'contacto|pe.contacto',
            HTML::parrafo( $textos->id('SEDE')                 ,  'centrado' ) => 'sede|s.nombre',            
            HTML::parrafo( $textos->id('CELULAR')              ,  'centrado' ) => 'celular|pe.celular',
            HTML::parrafo( $textos->id('EMAIL')                ,  'centrado' ) => 'correo|pe.correo',
            HTML::parrafo( $textos->id('ESTADO')               ,  'centrado' ) => 'estado'
            
        );        
        //ruta a donde se mandara la accion del doble click
        $rutaPaginador = '/ajax'.$this->urlBase.'/move';
        
        return Recursos::generarTablaRegistros($arregloRegistros, $datosTabla, $rutaPaginador, $datosPaginacion).HTML::crearMenuBotonDerecho('EMPLEADOS');
        
    }    
    
    
    
}
?>
