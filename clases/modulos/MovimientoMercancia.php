<?php

/**
 * @package     FOM
 * @subpackage  Movimientos Mercancia 
 * @author      Pablo Andrés Vélez Vidal 
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2012 Genesys Soft.
 * @version     0.2
 * 
 * Movimientos de mercancia que se realizan sobre el inventario de existencias de articulos
 * */
class MovimientoMercancia {

    /**
     * Código interno o identificador del registro
     * @var entero
     */
    public $id;

    /**
     * URL relativa del módulo de movimientos
     * @var cadena
     */
    public $urlBase;

    /**
     * URL relativa del modulo
     * @var cadena
     */
    public $url;

    /**
     * Identificador del articulo sobre el cual se realiza el movimiento
     * @var cadena
     */
    public $idArticulo;    
    
    /**
     * Nombre del articulo
     * @var cadena
     */
    public $articulo;
    
    /**
     * Cantidad de articulos movidos
     * @var cadena
     */
    public $cantidad;    

    /**
     * Identificador de la bodega de donde sale el articulo
     * @var cadena
     */
    public $idBodegaOrigen;
    
    /**
     * Nombre de la bodega de donde sale el articulo
     * @var cadena
     */
    public $bodegaOrigen;    

    /**
     * Identificador de la bodega a la cual se mueve el articulo
     * @var cadena
     */
    public $idBodegaDestino;
    
    /**
     * Nombre de la bodega de destino
     * @var cadena
     */
    public $bodegaDestino;      
    
    /**
     * Identificador del usuario que realiza la transaccion
     * @var cadena
     */
    public $idUsuario;
    
    /**
     * Usuario que realiza la transaccion
     * @var cadena
     */
    public $usuario;  
    
    /**
     * Fecha en que se realiza la transaccion
     * @var cadena
     */
    public $fechaMovimiento;      

    /**
     * Indicador del orden cronológio de la lista de acciones
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
     * Inicializar una bodega
     * @param entero $id Código interno o identificador del bodega en la base de datos
     */
    public function __construct($id = NULL) {
        global $sql;

        $this->urlBase              = '/movimientos_mercancia';
        $this->url                  = 'movimientos_mercancia';
        //Saber el numero de registros
        $this->registros            = $sql->obtenerValor('movimientos_mercancia', 'COUNT(id)', 'id != "0"');
        //Saber el numero de registros activos
        $this->registrosActivos     = $sql->obtenerValor('movimientos_mercancia', 'COUNT(id)', 'id != "0"');
        //establecer el valor del campo predeterminado para organizar los listados
        $this->ordenInicial         = 'a.nombre';

        if (isset($id)) {
            $this->cargar($id);
            
        }
        
    }

    /**
     * Cargar los datos de un item
     * @param entero $id Código interno o identificador del item en la base de datos
     */
    public function cargar($id) {
        global $sql;

        if (isset($id) && $sql->existeItem('movimientos_mercancia', 'id', intval($id))) {

            $tablas = array(
                'mm'    => 'movimientos_mercancia',
                'b1'    => 'bodegas',
                'b2'    => 'bodegas',
                'u'     => 'usuarios',
                'a'     => 'articulos'
            );

            $columnas = array(
                'id'                => 'mm.id',
                'idArticulo'        => 'mm.id_articulo',
                'articulo'          => 'a.nombre',
                'cantidad'          => 'mm.cantidad',
                'idBodegaOrigen'    => 'mm.id_bodega_origen',
                'bodegaOrigen'      => 'b1.nombre',
                'idBodegaDestino'   => 'mm.id_bodega_destino',
                'bodegaDestino'     => 'b2.nombre',
                'idUsuario'         => 'mm.id_usuario',
                'usuario'           => 'u.usuario',
                'fechaMovimiento'   => 'mm.fecha',
            );

            $condicion = 'mm.id_articulo = a.id  AND mm.id_usuario = u.id AND mm.id_bodega_origen  = b1.id AND mm.id_bodega_destino = b2.id AND  mm.id = "'.$id.'"';

            $consulta = $sql->seleccionar($tablas, $columnas, $condicion);

            if ($sql->filasDevueltas) {
                $fila = $sql->filaEnObjeto($consulta);

                foreach ($fila as $propiedad => $valor) {
                    $this->$propiedad = $valor;
                }

            }
            
        }
        
    }

    /**
     * Adicionar un registro de movimiento de mercancias
     * @param  arreglo $datos       Datos de la registro a adicionar
     * @return entero               Código interno o identificador del registro en la base de datos (NULL si hubo error)
     */
    public function adicionar($datos) {
        global $sql;

        $datosItem = array();

        $datosItem['id_articulo']           = $datos['id_articulo'];
        $datosItem['cantidad']              = $datos['cantidad'];
        $datosItem['id_bodega_origen']      = $datos['id_bodega_origen'];
        $datosItem['id_bodega_destino']     = $datos['id_bodega_destino'];
        $datosItem['id_usuario']            = $datos['id_usuario'];


        $consulta = $sql->insertar('movimientos_mercancia', $datosItem);

        if ($consulta) {
            $idItem = $sql->ultimoId;
            return $idItem;
            
        } else {
            return NULL;
            
        }
        
    }

    /**
     * Listar las movimientos_mercancia
     * @param entero  $cantidad    Número de movimientos_mercancia a incluir en la lista (0 = todas las entradas)
     * @param arreglo $excepcion   Arreglo con los códigos internos o identificadores a omitir en la lista
     * @param cadena  $condicion   Condición adicional (SQL)
     * @return arreglo             Lista de movimientos_mercancia
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

        /*         * * Validar que la condición sea una cadena de texto ** */
        if (!is_string($condicionGlobal)) {
            $condicion = '';
        }

        /*         * * Validar que la excepción sea un arreglo y contenga elementos ** */
        if (isset($excepcion) && is_array($excepcion)) {
            $excepcion = implode(',', $excepcion);
            $condicion .= 'mm.id NOT IN (' . $excepcion . ') AND ';
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
                'mm'        => 'movimientos_mercancia',
                'b1'        => 'bodegas',
                'b2'        => 'bodegas',
                'u'         => 'usuarios',
                'a'         => 'articulos'
            );

            $columnas = array(
                'id'                => 'mm.id',
                'idArticulo'        => 'mm.id_articulo',
                'articulo'          => 'a.nombre',
                'cantidad'          => 'mm.cantidad',
                'idBodegaOrigen'    => 'mm.id_bodega_origen',
                'bodegaOrigen'      => 'b1.nombre',
                'idBodegaDestino'   => 'mm.id_bodega_destino',
                'bodegaDestino'     => 'b2.nombre',
                'idUsuario'         => 'mm.id_usuario',
                'usuario'           => 'u.usuario',
                'fechaMovimiento'   => 'mm.fecha',
            );

            

        if (!empty($condicionGlobal)) {
            $condicion .= $condicionGlobal . ' AND ';
        }

        $condicion .= 'mm.id_articulo = a.id  AND mm.id_usuario = u.id AND mm.id_bodega_origen  = b1.id AND mm.id_bodega_destino = b2.id';



        if (is_null($this->registrosConsulta)) {
            $sql->seleccionar($tablas, $columnas, $condicion);
            $this->registrosConsulta = $sql->filasDevueltas;
        }

        $consulta = $sql->seleccionar($tablas, $columnas, $condicion, 'mm.id', $orden, $inicio, $cantidad);
        
        if ($sql->filasDevueltas) {
            $lista = array();

            while ($objeto = $sql->filaEnObjeto($consulta)) {
                $lista[] = $objeto;
            }
        }

        return $lista;
        
    }

    public function generarTabla($arregloRegistros, $datosPaginacion = NULL) {
        global $textos;
        //Declaracion de las columnas que se van a mostrar en la tabla
        $datosTabla = array(
            HTML::parrafo($textos->id('ARTICULO'), 'centrado') => 'articulo|a.nombre',
            HTML::parrafo($textos->id('BODEGA_ORIGEN'), 'centrado') => 'bodegaOrigen|b1.nombre',
            HTML::parrafo($textos->id('BODEGA_DESTINO'), 'centrado') => 'bodegaDestino|b2.nombre',
            HTML::parrafo($textos->id('FECHA_MOVIMIENTO'), 'centrado') => 'fechaMovimiento|mm.fecha',
            HTML::parrafo($textos->id('USUARIO'), 'centrado') => 'usuario|u.usuario'
        );
        //ruta a donde se mandara la accion del doble click
        $ruta = '/ajax' . $this->urlBase . '/move';


        return Recursos::generarTablaRegistros($arregloRegistros, $datosTabla, $ruta, $datosPaginacion) . HTML::crearMenuBotonDerecho('MOVIMIENTOS_MERCANCIA', array(), array("editar" => true, "borrar" => true));
    }
    

}
