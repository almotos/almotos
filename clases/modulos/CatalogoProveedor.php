<?php

/**
 * @package     FOM
 * @subpackage  Catalogos 
 * @author      Pablo Andrés Vélez Vidal 
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2012 Genesys Soft.
 * @version     0.2
 * 
 * Clase encargada de gestionar la información del listado de catalogos de las motos existentes en el sistema. Este modulo
 * es utilizado en los modulos de compras y ventas para consultar un catálogo de una moto determinada. También puede ser accedido
 * como un modulo independiente. En teoría una moto tendría un catálogo, y este catálogo podría ser en cualquier formato de archivo, 
 * por ejemplo .doc, .xls, .pdf, etc.
 * 
 * */
class CatalogoProveedor {

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
     * Nombre que se le pone al catalogo
     * @var string
     */
    public $nombre;

    /**
     * Código interno o identificador del proveedor
     * @var entero
     */
    public $idProveedor;

    /**
     * Objeto representativo de la clase proveedor
     * @var entero
     */
    public $proveedor;
       /**
     * Código interno o identificador del articulo
     * @var entero
     */
    public $idArticulo;

    /**
     * Objeto representativo de la clase articulo
     * @var entero
     */
    public $articulo;

    /**
     * Tabla principal a la que va relacionada el modulo
     * @var entero
     */
    public $tabla;

    /**
     * Identificador del archivo
     * @var entero
     */
    public $archivo;

    /**
     * ruta absoluta hacia el archivo
     * @var entero
     */
    public $rutaArchivo;

    /**
     * Codigo html <a>enlace</> que lleva directamente al archivo
     * @var entero
     */
    public $enlaceArchivo;

    /**
     * Determina si el registro se encuentra activo
     * @var entero
     */
    public $activo;

    /**
     * Indicador del orden cronológio de la lista de registros
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
     *
     * Inicializar un catalogo
     *
     * @param entero $id Código interno o identificador del catalogo en la base de datos
     *
     */
    public function __construct($id = NULL) {
        global $sql, $modulo;
        $this->urlBase = '/' . $modulo->url;
        $this->url = $modulo->url;
        $this->idModulo = $modulo->id;
        $this->tabla = $modulo->tabla;
        //Saber el numero de registros
        $this->registros = $sql->obtenerValor('catalogos', 'COUNT(id)', 'id != "0"');
        //Saber el numero de registros activos
        $this->registrosActivos = $sql->obtenerValor('catalogos', 'COUNT(id)', 'activo = "1"');
        //establecer el valor del campo predeterminado para organizar los listados
        $this->ordenInicial = 'fecha';

        if (!empty($id)) {
            $this->cargar($id);
        }
    }

//Fin del metodo constructor

    /**
     *
     * Cargar los datos de un item
     *
     * @param entero $id Código interno o identificador del item en la base de datos
     *
     */
    public function cargar($id) {
        global $sql, $configuracion;

        if (!empty($id) && $sql->existeItem('catalogos', 'id', intval($id))) {

            $tablas = array(
                'c' => 'catalogos',
            );

            $columnas = array(
                'id'             => 'c.id',
                'nombre'         => 'c.nombre',
                'idProveedor'    => 'c.id_proveedor',
                'idArticulo'     => 'c.id_articulo',
                'fecha'          => 'c.fecha',
                'archivo'        => 'c.archivo',
                'activo'         => 'c.activo'
            );

            $condicion = 'c.id = "' . $id . '"';
            $sql-> depurar =true;
            $consulta = $sql->seleccionar($tablas, $columnas, $condicion);

            if ($sql->filasDevueltas) {
                $fila = $sql->filaEnObjeto($consulta);

                foreach ($fila as $propiedad => $valor) {
                    $this->$propiedad = $valor;
                }

                $this->url = $this->urlBase . '/' . $this->id;

                $this->rutaArchivo = $configuracion['SERVIDOR']['media'] . $configuracion['RUTAS']['archivos'] . '/catalogos/' . $this->archivo;
                $this->enlaceArchivo = HTML::enlace($this->nombre, $this->rutaArchivo, 'estiloEnlace', '',  array('target' => '_blank'));
                $this->proveedor = new Proveedor($this->idProveedor);
                $this->articulo = new Articulo($this->idArticulo);
            }
        }
    }

    /**
     *
     * Adicionar un catalogo
     *
     * @param  arreglo $datos       Datos del catalogo a adicionar
     * @return entero               Código interno o identificador del catalogo en la base de datos (NULL si hubo error)
     *
     */
    public function adicionar($datos) {
        global $sql, $archivo_archivo, $configuracion;

        $datosItem = array();

        $configuracionRuta = $configuracion["RUTAS"]["media"] . "/" . $configuracion["RUTAS"]["archivos"] . "/catalogos/";
        $recurso = Archivo::subirArchivoAlServidor($archivo_archivo, $configuracionRuta);


        $datosItem['id_moto'] = $datos['id_moto'];
        $datosItem['nombre'] = $datos['nombre'];
        $datosItem['archivo'] = $recurso;

        if (isset($datos['activo'])) {
            $datosItem['activo'] = '1';
        } else {
            $datosItem['activo'] = '0';
        }

        $consulta = $sql->insertar('catalogos', $datosItem);

        if ($consulta) {
            $idItem = $sql->ultimoId;
            return $idItem;
        } else {
            return NULL;
        }//fin del if($consulta)
    }


    /**
     * Modificar un catalogo
     * @param  arreglo $datos       Datos del catalogo a modificar
     * @return lógico               Indica si el procedimiento se pudo realizar correctamente o no
     */
    public function modificar($datos) {
        global $sql, $archivo_archivo, $configuracion;

        if (!isset($this->id)) {
            return NULL;
        }

        $datosItem = array();

        $recurso = $this->archivo;

        if (isset($archivo_archivo) && !empty($archivo_archivo['tmp_name'])) {
            
            Archivo::eliminarArchivoDelServidor(array($this->rutaArchivo));
            $configuracionRuta = $configuracion["RUTAS"]["media"] . "/" . $configuracion["RUTAS"]["archivos"] . "/catalogos/";
            $recurso = Archivo::subirArchivoAlServidor($archivo_archivo, $configuracionRuta);
        }

        $datosItem['id_moto'] = $datos['id_moto'];
        $datosItem['nombre'] = $datos['nombre'];
        $datosItem['archivo'] = $recurso;

        if (isset($datos['activo'])) {
            $datosItem['activo'] = '1';
        } else {
            $datosItem['activo'] = '0';
        }

        $consulta = $sql->modificar('catalogos', $datosItem, 'id = "' . $this->id . '"');

        if ($consulta) {
            return 1;
        } else {
            return NULL;
        }//fin del if(consulta)
    }


    /**
     *
     * Eliminar una moto
     *
     * @param entero $id    Código interno o identificador de una moto en la base de datos
     * @return lógico       Indica si el procedimiento se pudo realizar correctamente o no
     *
     */
    public function eliminar() {
        global $sql;

        if (!isset($this->id)) {
            return NULL;
        }

        if (($consulta = $sql->eliminar('catalogos', 'id = "' . $this->id . '"'))) {
            if(!empty($this->archivo)){
                $ruta = array($this->rutaArchivo);
                Archivo::eliminarArchivoDelServidor($ruta);
            }
            
            return true;
        } else {
            return false;
        }//fin del si funciono eliminar
    }

//Fin del metodo eliminar Unidades

    /**
     *
     * Listar las motos
     *
     * @param entero  $cantidad    Número de motos a incluir en la lista (0 = todas las entradas)
     * @param arreglo $excepcion   Arreglo con los códigos internos o identificadores a omitir en la lista
     * @param cadena  $condicion   Condición adicional (SQL)
     * @return arreglo             Lista de motos
     *
     */
    public function listar($inicio = 0, $cantidad = 0, $excepcion = NULL, $condicionGlobal = NULL, $orden = NULL) {
        global $sql, $textos, $configuracion;

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
            $condicion .= 'c.id NOT IN (' . $excepcion . ') AND ';
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
            'c' => 'catalogos',
            'p' => 'proveedores',
            'a' => 'articulos'
        );

        $columnas = array(
            'articuloTest'       => 'a.nombre',
            'proveedor'      => 'p.nombre',
            'archivo'        => 'c.archivo',
            'activo'         => 'c.activo'
        );


        if (!empty($condicionGlobal)) {
            $condicion .= $condicionGlobal . ' AND ';
        }

        $condicion .= 'c.id_proveedor = p.id AND c.id_articulo = a.id';


        if (is_null($this->registrosConsulta)) {
            $sql->seleccionar($tablas, $columnas, $condicion);
            $this->registrosConsulta = $sql->filasDevueltas;
        }
        //$sql->depurar = true;
        $consulta = $sql->seleccionar($tablas, $columnas, $condicion, 'c.id', $orden, $inicio, $cantidad);
        //echo $sql->sentenciaSql;
        if ($sql->filasDevueltas) {
            $lista = array();

            while ($objeto = $sql->filaEnObjeto($consulta)) {
                $objeto->url = $this->urlBase . '/' . $objeto->id;
                $objeto->idModulo = $this->idModulo;

                $objeto->enlaceArchivo = HTML::enlace($objeto->nombre, $configuracion['SERVIDOR']['media'] . $configuracion['RUTAS']['archivos'] . '/catalogos/' . $objeto->archivo, 'estiloEnlace', '', array('target' => '_blank'));

                $objeto->estado = ($objeto->activo) ? HTML::frase($textos->id('ACTIVO'), 'activo') : $objeto->estado = HTML::frase($textos->id('INACTIVO'), 'inactivo');

                $lista[] = $objeto;
            }
        }

        return $lista;
    }

//Fin del metodo de listar 

    public function generarTabla($arregloRegistros, $datosPaginacion = NULL) {
        global $textos;
        //Declaracion de las columnas que se van a mostrar en la tabla
        $datosTabla = array(
            HTML::parrafo($textos->id('ARTICULO'), 'centrado')      => 'articulo|a.nombre',
            HTML::parrafo($textos->id('PROVEEDOR'), 'centrado')     => 'proveedor|p.nombre',
            HTML::parrafo($textos->id('ARCHIVO'), 'centrado')       => 'enlaceArchivo|c.nombre',
            HTML::parrafo($textos->id('ESTADO'), 'centrado')        => 'estado'
        );
        //ruta a donde se mandara la accion del doble click
        $ruta = '/ajax' . $this->urlBase . '/move';


        return Recursos::generarTablaRegistros($arregloRegistros, $datosTabla, $ruta, $datosPaginacion) . HTML::crearMenuBotonDerecho('CATALOGOS');
    }

}