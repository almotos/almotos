<?php

/**
 * @package     FOM
 * @subpackage  Bodegas 
 * @author      Pablo Andrés Vélez Vidal 
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2012 Genesys Soft.
 * @version     0.2
 * 
 * Clase encargada de gestionar la información del listado de bodegas existentes en el negocio para el almacenamiento de los articulos.
 * Modulo utilizado en la compra y venta de mercancias para la gestión del inventario del negocio. Determinando bodegas según las sedes.
 * Tiene relacion con el modulo "Sedes empresa" ya que una sede puede tener una o muchas bodegas, y el stock de articulos se almacena en
 * dichas bodegas.
 * */
class Bodega {

    /**
     * Código interno o identificador del item en la base de datos
     * @var entero
     */
    public $id;

    /**
     * URL relativa del módulo 
     * @var cadena
     */
    public $urlBase;

    /**
     * URL relativa del módulo 
     * @var cadena
     */
    public $url;

    /**
     * Código interno o identificador del modulo
     * @var entero
     */
    public $idModulo;

    /**
     * Código interno o identificador de la sede a la que pertenece la bodega
     * @var entero
     */
    public $idSede;

    /**
     * Objeto sede
     * @var entero
     */
    public $sede;

    /**
     * Nombre del item
     * @var entero
     */
    public $nombre;

    /**
     * Descripción de la ubicación  del item
     * @var cadena
     */
    public $ubicacion;

    /**
     * Determina si esta es la bodega principal de la sede
     * @var entero
     */
    public $principal;

    /**
     * Indicador del orden  de la lista de registros
     * @var lógico
     */
    public $listaAscendente = true;

    /**
     * Número de registros de la lista
     * @var entero
     */
    public $registros = NULL;

    /**
     * Número de registros activos de la lista de bodegas
     * @var entero
     */
    public $registrosActivos = NULL;

    /**
     * Número de registros extraidos en una consulta
     * @var entero
     */
    public $registrosConsulta = NULL;

    /**
     * Orden predeterminado para organizar los listados
     * @var entero
     */
    public $ordenInicial = NULL;

    /**
     * Inicializar una bodega
     * @param entero $id Código interno o identificador del bodega en la base de datos
     */
    public function __construct($id = NULL) {
        global $sql, $modulo;

        $this->urlBase              = '/' . $modulo->url;
        $this->url                  = $modulo->url;
        $this->idModulo             = $modulo->id;
        //Saber el numero de registros
        $this->registros            = $sql->obtenerValor('bodegas', 'COUNT(id)', 'id != "0" AND id != "999"');
        //Saber el numero de registros activos
        $this->registrosActivos     = $sql->obtenerValor('bodegas', 'COUNT(id)', 'id != "0"');
        //establecer el valor del campo predeterminado para organizar los listados
        $this->ordenInicial         = 'b.nombre';

        if (isset($id)) {
            $this->cargar($id);
        }
    }

    /**
     * Cargar los datos de un item
     * 
     * @param entero $id Código interno o identificador del item en la base de datos
     */
    public function cargar($id) {
        global $sql;

        if (isset($id) && $sql->existeItem('bodegas', 'id', intval($id))) {

            $tablas = array(
                'b' => 'bodegas',
                's' => 'sedes_empresa'
            );

            $columnas = array(
                'id'                => 'b.id',
                'nombre'            => 'b.nombre',
                'idSede'            => 'b.id_sede',
                'sede'              => 's.nombre',
                'ubicacion'         => 'b.ubicacion',
                'principal'         => 'b.principal'
            );

            $condicion = 'b.id_sede = s.id  AND b.id = "' . $id . '"';

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
     * Adicionar una bodega
     * 
     * @param  arreglo $datos       Datos de la bodega a adicionar
     * @return entero               Código interno o identificador del bodega en la base de datos (NULL si hubo error)
     */
    public function adicionar($datos) {
        global $sql;

        $datosItem = array();

        $datosItem['id_sede']   = $datos['id_sede'];
        $datosItem['nombre']    = $datos['nombre'];
        $datosItem['ubicacion'] = $datos['ubicacion'];

        $sql->iniciarTransaccion();
        
        if (isset($datos['principal'])) {
            $datosItem['principal'] = '1';
            $datosPrincipal         = array('principal' => '0');
            
            $modificar = $sql->modificar('bodegas', $datosPrincipal, 'id_sede = "' . $datosItem['id_sede'] . '"');
            
            if(!$modificar){
                $sql->cancelarTransaccion();
            }
            
        } else {
            $datosItem['principal'] = '0';
            
        }

        $consulta = $sql->insertar('bodegas', $datosItem);

        if ($consulta) {
            $idItem = $sql->ultimoId;
            $sql->finalizarTransaccion();
            return $idItem;
            
        } else {
            $sql->cancelarTransaccion();
            return false;
            
        }
        
    }

    /**
     * Modificar la información correspondiente a una bodega
     * 
     * @param  arreglo $datos       Datos del bodega a modificar
     * @return lógico               Indica si el procedimiento se pudo realizar correctamente o no
     */
    public function modificar($datos) {
        global $sql;

        if (!isset($this->id)) {
            return false;
        }

        $datosItem = array();


        $datosItem['id_sede']       = $datos['id_sede'];
        $datosItem['nombre']        = $datos['nombre'];
        $datosItem['ubicacion']     = $datos['ubicacion'];

        $sql->iniciarTransaccion();
        
        if (isset($datos['principal'])) {
            $datosItem['principal'] = '1';
            $datosPrincipal = array('principal' => '0');
            
            $modificar = $sql->modificar('bodegas', $datosPrincipal, 'id_sede = "' . $datosItem['id_sede'] . '"');
            
            if(!$modificar){
                $sql->cancelarTransaccion();
            }            
            
        } else {
            $datosItem['principal'] = '0';
            
        }

        $consulta = $sql->modificar('bodegas', $datosItem, 'id = "' . $this->id . '"');


        if ($consulta) {
            $sql->finalizarTransaccion();
            return true;
            
        } else {
            $sql->cancelarTransaccion();
            return false;
            
        }
    }

    /**
     * Eliminar una bodega
     * 
     * @param entero $id    Código interno o identificador de una bodega en la base de datos
     * @return lógico       Indica si el procedimiento se pudo realizar correctamente o no
     */
    public function eliminar() {
        global $sql;

        if (!isset($this->id)) {
            return false;
        }

        $consulta = $consulta = $sql->eliminar('bodegas', 'id = "' . $this->id . '"');
        
        if ($consulta) {
            return true;
            
        } else {
            return false;
            
        }
    }

    /**
     * Listar las bodegas
     * 
     * @param entero  $cantidad    Número de bodegas a incluir en la lista (0 = todas las entradas)
     * @param arreglo $excepcion   Arreglo con los códigos internos o identificadores a omitir en la lista
     * @param cadena  $condicion   Condición adicional (SQL)
     * @return arreglo             Lista de bodegas
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

        /*         * * Validar que la excepción sea un arreglo y contenga elementos ** */
        if (isset($excepcion) && is_array($excepcion)) {
            $excepcion = implode(',', $excepcion);
            $condicion .= 'b.id NOT IN (' . $excepcion . ') AND ';
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
            'b' => 'bodegas',
            's' => 'sedes_empresa'
        );

        $columnas = array(
            'id'            => 'b.id',
            'nombre'        => 'b.nombre',
            'sede'          => 's.nombre',
            'principal'     => 'b.principal'
        );

        if (!empty($condicionGlobal)) {
            $condicion .= $condicionGlobal . ' AND ';
        }

        $condicion .= ' b.id_sede = s.id';



        if (is_null($this->registrosConsulta)) {
            $sql->seleccionar($tablas, $columnas, $condicion);
            $this->registrosConsulta = $sql->filasDevueltas;
        }
        
        $consulta = $sql->seleccionar($tablas, $columnas, $condicion, 'b.id', $orden, $inicio, $cantidad);
        
        if ($sql->filasDevueltas) {
            $lista = array();

            while ($objeto = $sql->filaEnObjeto($consulta)) {            
                $objeto->estado = ($objeto->principal) ? HTML::frase($textos->id('PRINCIPAL'), 'activo') : HTML::frase($textos->id('SECUNDARIA'), 'inactivo');
                
                $lista[] = $objeto;
            }
        }

        return $lista;
    }

    /**
     * Metodo encargado de generar la tabla con el listado de registros de las bodegas
     * 
     * @global recurso $textos          = objeto global encargado de la traduccion de los textos
     * @param array $arregloRegistros   = arreglo con los registros a mostrar en la tabla
     * @param array $datosPaginacion    = datos con los arreglos para la paginacion
     * @return text                     = texto html con la tabla y el menu del boton derecho
     */
    public function generarTabla($arregloRegistros, $datosPaginacion = NULL) {
        global $textos;
        //Declaracion de las columnas que se van a mostrar en la tabla
        $datosTabla = array(
            HTML::parrafo($textos->id('SEDE'), 'centrado')      => 'sede|s.nombre',
            HTML::parrafo($textos->id('NOMBRE'), 'centrado')    => 'nombre|b.nombre',
            HTML::parrafo($textos->id('ESTADO'), 'centrado')    => 'estado'
        );
        //ruta a donde se mandara la accion del doble click
        $ruta = '/ajax' . $this->urlBase . '/move';


        return Recursos::generarTablaRegistros($arregloRegistros, $datosTabla, $ruta, $datosPaginacion) . HTML::crearMenuBotonDerecho('BODEGAS');
    }

}
