<?php

/**
 *
 * @package     FOLCS
 * @subpackage  Base
 * @author      Pablo Andres Velez Vidal
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2012 Genesys
 * @version     0.1
 *
 **/

class Mensaje {

    /**
     * Código interno o identificador del mensaje en la base de datos
     * @var entero
     */
    public $id;

    /**
     * Código interno del remitente
     * @var entero
     */
    public $idRemitente;
    
    /**
     * URL relativa de un blog específica
     * @var cadena
     */
    public $url;
    
     /**
     * URL relativa del módulo de blogs
     * @var cadena
     */
    public $urlBase; 
    
    /**
     * Código interno o identificador del modulo
     * @var entero
     */
    public $idModulo;

     /**
     * Código interno del remitente
     * @var entero
     */
    public $idDestinatario;


    /**
     * Ruta de la foto del autor en miniatura
     * @var cadena
     */
    public $nombreRemitente;

    /**
     * Título del mensaje
     * @var cadena
     */
    public $titulo;

    /**
     * Contenido completo del mensaje
     * @var cadena
     */
    public $contenido;
    
     /**
     * Cantidad de Mensajes que tiene determinado usuario
     * @var cadena
     */
    public $cantidadMensajes;

    /**
     * Fecha de publicación del mensaje
     * @var fecha
     */
    public $fecha;

    /**
     * Estado de lectura del mensaje
     * @var lógico
     */
    public $leido;


    

    /**
     *
     * Inicializar el mensaje
     *
     * @param entero $id Código interno o identificador del mensaje en la base de datos
     *
     */
    public function __construct($id = NULL, $idUsuario = NULL) {
        $modulo         = new Modulo('MENSAJES');
        $this->urlBase  = '/'.$modulo->url;
        $this->url      = $modulo->url;
        $this->idModulo = $modulo->id;
        if(isset($idUsuario) && $idUsuario != ''){
            $sqlGlobal = new SqlGlobal();
            $this->cantidadMensajes = $sqlGlobal->obtenerValor('mensajes', 'COUNT(id)', 'id_usuario_destinatario = "' . $idUsuario. '"');
            unset($sqlGlobal);
        }       
        
        if (isset($id) && $id != '') {
            $this->cargar($id);
        }
        
    }

    /**
     *
     * Cargar los datos del mensaje
     *
     * @param entero $id Código interno o identificador del mensaje en la base de datos
     *
     */
    public function cargar($id) {
        $sqlGlobal = new SqlGlobal();

        if (isset($id) && $sqlGlobal->existeItem('mensajes', 'id', intval($id))) {

            $tablas = array(
                'm' => 'mensajes'
            );

            $columnas = array(
                'id'               => 'm.id',
                'idRemitente'      => 'm.id_usuario_remitente',
                'idDestinatario'   => 'm.id_usuario_destinatario',
                'nombreRemitente'  => 'm.nombre_remitente',
                'titulo'           => 'm.titulo',
                'contenido'        => 'm.contenido',
                'fecha'            => 'UNIX_TIMESTAMP(m.fecha)'
            );

            $condicion = ' m.id = "'.$id.'"';

            $consulta = $sqlGlobal->seleccionar($tablas, $columnas, $condicion);

            if ($sqlGlobal->filasDevueltas) {
                $fila = $sqlGlobal->filaEnObjeto($consulta);

                foreach ($fila as $propiedad => $valor) {
                    $this->$propiedad = $valor;
                }

            }
        }
    }

    /**
     *
     * Adicionar un mensaje
     *
     * @param  arreglo $datos       Datos del mensaje a adicionar
     * @return entero               Código interno o identificador del mensaje en la base de datos (NULL si hubo error)
     *
     */
    public function adicionar($datos) {
        global $sesion_usuarioSesion;
        $sqlGlobal = new SqlGlobal();
        
        $datos['id_usuario_remitente'] = $sesion_usuarioSesion->id;
        $datos['nombre_remitente']     = $sesion_usuarioSesion->usuario;
        $datos['fecha']                = date('Y-m-d G:i:s');
        $datos['leido']                = 0;

        $datos = array(
            'id_usuario_destinatario'    => $sesion_usuarioSesion->id,
            'titulo'        => $datos['titulo'],
            'contenido'     => $datos['contenido'],            
            'fecha'         => date('Y-m-d H:i:s'),
            'activo'        => '1'
        );

        $consulta   = $sqlGlobal->insertar('mensajes', $datos);
        $idConsulta = $sqlGlobal->ultimoId;

        if ($consulta) {
            return $idConsulta;

        } else {
            return NULL;
        }
    }

    /**
     *
     * Eliminar un mensaje
     *
     * @param entero $id    Código interno o identificador del mensaje en la base de datos
     * @return lógico       Indica si el procedimiento se pudo realizar correctamente o no
     *
     */
    public function eliminar() {
       $sqlGlobal = new SqlGlobal();
        if (!isset($this->id)) {
            return NULL;
        }

        $consulta = $sqlGlobal->eliminar('mensajes', 'id = "'.$this->id.'"');
        return $consulta;
    }

    /**
     *
     * Listar los mensajes de un registro en un módulo
     *
     * @param  cadena $modulo      Nombre
     * @param  entero $registro    Código interno o identificador del registro del módulo en la base de datos
     * @return arreglo             Lista de mensajes hechos al registro del módulo
     *
     */
    public function listar($inicio = 0, $cantidad = 0, $idUsuario = NULL) {        

        $sqlGlobal = new SqlGlobal();
        /*** Validar la fila inicial de la consulta ***/
        if (!is_int($inicio) || $inicio < 0) {
            $inicio = 0;
        }

        /*** Validar la cantidad de registros requeridos en la consulta ***/
        if (!is_int($cantidad) || $cantidad <= 0) {
            $cantidad = 0;
        }

        $tablas = array(
            'm' => 'mensajes',
        );

        $columnas = array(
            'id'                => 'm.id',
            'idRemitente'       => 'm.id_usuario_remitente',
            'nombreRemitente'   => 'm.nombre_remitente',
            'titulo'            => 'm.titulo',
            'contenido'         => 'm.contenido',
            'fecha'             => 'UNIX_TIMESTAMP(m.fecha)',
            'leido'             => 'm.leido'
        );
 
        //$sql->depurar = true;
        $condicion = 'm.id_usuario_destinatario = "'.$idUsuario.'"';

        $consulta = $sqlGlobal->seleccionar($tablas, $columnas, $condicion, '', 'fecha DESC', $inicio, $cantidad);

        $lista = array();
        
        if ($sqlGlobal->filasDevueltas) {            
            while ($mensaje = $sqlGlobal->filaEnObjeto($consulta)) {                
                $lista[] = $mensaje;
            }
        }

        return $lista;

    }
}
?>
