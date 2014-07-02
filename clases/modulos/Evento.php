<?php

/**
 *
 * @package     FOLCS
 * @subpackage  Eventos o actividades que serán mostradas en el calendario de actividades
 * @author      Pablo A. Vélez <pavelez@genesyscorp.com.co>
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2013 Genesys corp.
 * @version     0.2
 *
 * */
class Evento {

    /**
     * Código interno o identificador del evento en la base de datos
     * @var entero
     */
    public $id;

    /**
     * URL relativa del módulo de eventos
     * @var cadena
     */
    public $urlBase;

    /**
     * URL relativa de un evento específica
     * @var cadena
     */
    public $url;

    /**
     * Código interno o identificador del usuario creador del evento en la base de datos
     * @var entero
     */
    public $idUsuario;

    /**
     * Nombre unico del usuario(nombre login) creador del evento
     * @var String
     */
    public $usuario;

    /**
     * Título del evento
     * @var cadena
     */
    public $titulo;

    /**
     * Descripcion completa del evento
     * @var cadena
     */
    public $descripcion;

    /**
     * Fecha de Inicio del evento
     * @var fecha
     */
    public $fechaInicio;

    /**
     * Hora de inicio del evento
     * @var fecha
     */
    public $horaInicio;

    /**
     * Fecha de finalizacion del evento
     * @var fecha
     */
    public $fechaFin;

    /**
     * Hora de fin del evento
     * @var fecha
     */
    public $horaFin;

    /**
     * Fecha de creación del Registro
     * @var fecha
     */
    public $fechaCreacion;

    /**
     * Indicador de disponibilidad del registro
     * @var lógico
     */
    public $activo;

    /**
     * Indicador del orden cronológio de la lista de eventos
     * @var lógico
     */
    public $listaAscendente = false;

    /**
     * Número de registros de la lista
     * @var entero
     */
    public $registros = NULL;

    /**
     * Número de registros activos de la lista
     * @var entero
     */
    public $registrosActivos = NULL;

    /**
     * Inicializar un evento
     * @param entero $id Código interno o identificador del evento en la base de datos
     *
     */
    public function __construct($id = NULL) {
        global $sql;

        $modulo = new Modulo('EVENTOS');
        
        $this->urlBase  = '/' . $modulo->url;
        $this->url      = $modulo->url;

        $this->registros        = $sql->obtenerValor('eventos', 'COUNT(id)', '');
        $this->registrosActivos = $sql->obtenerValor('eventos', 'COUNT(id)', 'activo = "1"');


        if (isset($id)) {
            $this->cargar($id);
        }
        
    }

    /**
     * Cargar los datos de un evento
     * @param entero $id Código interno o identificador del evento en la base de datos
     *
     */
    public function cargar($id) {
        global $sql;

        if (isset($id) && $sql->existeItem('eventos', 'id', intval($id))) {

            $tablas = array(
                'e' => 'eventos',
                'u' => 'usuarios'
            );

            $columnas = array(
                'id'                => 'e.id',
                'idUsuario'         => 'e.id_usuario',
                'usuario'           => 'u.usuario',
                'titulo'            => 'e.titulo',
                'descripcion'       => 'e.descripcion',
                'fechaInicio'       => 'e.fecha_inicio',
                'horaInicio'        => 'e.hora_inicio',
                'fechaFin'          => 'e.fecha_fin',
                'horaFin'           => 'e.hora_fin',
                'fechaCreacion'     => 'UNIX_TIMESTAMP(e.fecha_creacion)',
                'activo'            => 'e.activo',
            );

            $condicion = 'e.id_usuario = u.id AND e.id = "' . $id . '"';

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
     *
     * Adicionar un evento
     *
     * @param  arreglo $datos       Datos del evento a adicionar
     * @return entero               Código interno o identificador del evento en la base de datos (NULL si hubo error)
     *
     */
    public static function adicionar($datos) {
        global $sql, $sesion_usuarioSesion;

        $datosEvento = array(
            'id_usuario'        => $sesion_usuarioSesion->id,
            'titulo'            => $datos['titulo'],
            'descripcion'       => $datos['descripcion'],
            'fecha_inicio'      => $datos['fecha_inicio'],
            'hora_inicio'       => $datos['hora_inicio'],
            'fecha_fin'         => $datos['fecha_fin'],
            'hora_fin'          => $datos['hora_fin'],
        );

        
        if (isset($datos['activo'])) {
            $datosEvento['activo'] = '1';
            
        } else {
            $datosEvento['activo'] = '0';
            
        }

        $consulta = $sql->insertar('eventos', $datosEvento);
        
        $idItem = $sql->ultimoId;

        if ($consulta) {
            return $idItem;
            
        } else {
            return FALSE;
            
        }
        
    }

    /**
     *
     * Modificar un evento
     *
     * @param  arreglo $datos       Datos del evento a modificar
     * @return lógico               Indica si el procedimiento se pudo realizar correctamente o no
     *
     */
    public function modificar($datos) {
        global $sql, $sesion_usuarioSesion;

        if (!isset($this->id)) {
            return NULL;
        }
        
        $datosEvento = array(
            'id_usuario'        => $sesion_usuarioSesion->id,
            'titulo'            => $datos['titulo'],
            'descripcion'       => $datos['descripcion'],
            'fecha_inicio'      => $datos['fecha_inicio'],
            'hora_inicio'       => $datos['hora_inicio'],
            'fecha_fin'         => $datos['fecha_fin'],
            'hora_fin'          => $datos['hora_fin']
        );

        if (isset($datos['activo'])) {
            $datosEvento['activo'] = '1';
            
        } else {
            $datosEvento['activo'] = '0';
            
        }

        $consulta = $sql->modificar('eventos', $datosEvento, 'id = "' . $this->id . '"');

        if ($consulta) {
            return $consulta;
            
        } else {
            return FALSE;
            
        }
        
    }

    /**
     * Eliminar un evento
     * @param entero $id    Código interno o identificador del evento en la base de datos
     * @return lógico       Indica si el procedimiento se pudo realizar correctamente o no
     */
    public function eliminar() {
        global $sql;

        if (!isset($this->id)) {
            return NULL;
        }

        $consulta = $sql->eliminar('eventos', 'id = "' . $this->id . '"');

        if ($consulta) {
            return $consulta;
            
        } else {
            return false;
            
        }
        
    }

    /**
     * Listar los eventos
     * @param entero  $cantidad    Número de eventos a incluir en la lista (0 = todas las entradas)
     * @param arreglo $excepcion   Arreglo con los códigos internos o identificadores a omitir en la lista
     * @param cadena  $condicion   Condición adicional (SQL)
     * @return arreglo             Lista de eventos
     */
    public function listar($inicio = 0, $cantidad = 0, $excepcion = NULL, $condicionGlobal = NULL, $orden = NULL) {
        global $sql, $sesion_usuarioSesion;

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
            $condicion .= 'e.id NOT IN (' . $excepcion . ')';
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
            'e' => 'eventos',
            'u' => 'usuarios',
        );

        $columnas = array(
            'id'                => 'e.id',
            'idUsuario'         => 'e.id_usuario',
            'usuario'           => 'u.usuario',
            'titulo'            => 'e.titulo',
            'descripcion'       => 'e.descripcion',
            'fechaInicio'       => 'e.fecha_inicio',
            'horaInicio'        => 'e.hora_inicio',
            'fechaFin'          => 'e.fecha_fin',
            'horaFin'           => 'e.hora_fin',
            'fechaCreacion'     => 'UNIX_TIMESTAMP(e.fecha_creacion)',
            'activo'            => 'e.activo',
        );

        if (!empty($condicionGlobal)) {
            $condicion .= $condicionGlobal . ' AND ';
        }

        $condicion .= 'e.id_usuario = u.id AND u.id = "' . $sesion_usuarioSesion->id . '" ';

        $consulta = $sql->seleccionar($tablas, $columnas, $condicion, 'e.id', $orden, $inicio, $cantidad);

        $lista = array();
        
        if ($sql->filasDevueltas) {
            while ($evento = $sql->filaEnObjeto($consulta)) {
                $evento->url = $this->urlBase . '/' . $evento->id;
                $lista[] = $evento;
            }
            
        }

        return $lista;
        
    }

    /**
     * funcion que devuelve un arreglo con los identificadores de los eventos o actividades pertenecientes a un usuario
     * 
     * @global type $sql
     * @global type $sesion_usuarioSesion
     * @param type $inicio
     * @param type $fin
     * @return string 
     */
    public function cargarFechasEventos($inicio, $fin) {
        global $sql, $sesion_usuarioSesion;

        $tablas = array(
            'e' => 'eventos'
        );

        $condicion = 'UNIX_TIMESTAMP(fecha_inicio) BETWEEN "' . $inicio . '" AND "' . $fin . '" AND e.id_usuario = "' . $sesion_usuarioSesion->id . '"';

        $consulta = $sql->seleccionar($tablas, array('id'), $condicion);

        $arregloFinal = array();
        
        while ($evento = $sql->filaEnObjeto($consulta)) {
            $arreglo    = array();
            $event      = new self($evento->id);

            $arreglo["id"]          =   $event->id;
            $arreglo["title"]       =   $event->titulo;
            $arreglo["allDay"]      =   false;
            $arreglo["start"]       =   $event->fechaInicio . 'T' . $event->horaInicio . 'Z';
            $arreglo["url"]         =   '/ajax/eventos/see]'.$event->id;
            $arreglo["className"]   =   'enlaceAjaxEvento';

            $arregloFinal[] = $arreglo;
            
        }

        return $arregloFinal;
        
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
            HTML::parrafo($textos->id('TITULO'), 'centrado')            => 'callCenter|p.call_center',
            HTML::parrafo($textos->id('FECHA_INICIO'), 'centrado')      => 'nombre|p.nombre',
            HTML::parrafo($textos->id('HORA_INICIO'), 'centrado')       => 'nombreContacto|pe.primer_nombre',
            HTML::parrafo($textos->id('FECHA_FIN'), 'centrado')         => 'apellidoContacto|pe.primer_apellido',
            HTML::parrafo($textos->id('HORA_FIN'), 'centrado')          => 'celularContacto|pe.celular',
            HTML::parrafo($textos->id('ESTADO'), 'centrado')            => 'estado'
        );
        //ruta a donde se mandara la accion del doble click
        $ruta = '/ajax' . $this->urlBase . '/move';

        $tabla = Recursos::generarTablaRegistros($arregloRegistros, $datosTabla, $ruta, $datosPaginacion);
        $menuDerecho = HTML::crearMenuBotonDerecho('EVENTOS');

        return $tabla . $menuDerecho;
    }
    

    public static function adicionarEventoVencimientoFC($datos = array()){
        global $sesion_usuarioSesion, $textos;
        
        //seleccionar el listado de usuarios que deben ser notificados
        $listaUsuarios = $sesion_usuarioSesion->listaUsuariosNotificacionEventoFC();
        
        //por cada uno de los usuarios generar el evento a ser mostrado en el calendario
        foreach ($listaUsuarios as $idUsuario) {

            $datosEvento = array(
                'id_usuario'        => $idUsuario,
                'titulo'            => $textos->id("VENCIMIENTO_FACTURA_COMPRA"),
                'descripcion'       => str_replace("%1", $datos["proveedor"], $textos->id("DESCRIPCION_VTO_FC")),
                'fecha_inicio'      => $datos["fecha_vencimiento"],
                'activo'            => '1',
            );

            self::adicionar($datosEvento);
            
        }
        
    }

}
