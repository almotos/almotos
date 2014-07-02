<?php


/**
 *
 * @package     FOLCS
 * @subpackage  Inventario del negocio
 * @author      Pablo Andrés Vélez Vidal <pavelez@colomboamericano.edu.co>
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2012 Genesys Corporation.
 * @version     0.2
 *
 * Clase encargada de gestionar la información del inventario de existencia de articulos en el en el sistema. En este módulo se puede
 * gestionar la información de existencias de articulos por bodega. esta clase realiza funciones como:
 * -ingresar mercancias en el sistema (tabla de inventarios = cantidad de un determinado articulo por bodega) normalmente por una compra
 * -descontar mercancias en el sistema (tabla de inventarios = cantidad de un determinado articulo por bodega) normalmente por una venta
 * -movimiento de mercancia: mover cantidades de un determinado articulo de una bodega a otra
 * 
 * */
class Inventario2
{

    /**
     * Código interno o identificador de la unidad en la base de datos
     * @var entero
     */
    public $id;
    
    /**
     * URL relativa del módulo de inventario
     * @var cadena
     */
    public $urlBase;

    /**
     * URL relativa del módulo de inventario
     * @var cadena
     */
    public $url;

    /**
     * identificador del Articulo
     * @var entero
     */
    public $idArticulo;

    /**
     * Objeto Articulo
     * @var objeto
     */
    public $articulo;
    
    /**
     * identificador dela bodega
     * @var entero
     */
    public $idBodega;

    /**
     * Objeto bodega
     * @var objeto
     */
    public $bodega;    

    /**
     * cantidad de existencias de un articulo especifico
     * @var entero
     */
    public $cantidadTotalArticulo;

    /**
     * cantidad de existencias de un articulo especifico en una bodega
     * @var entero
     */
    public $cantidadArticuloBodega;

    /**
     * Total de articulos en el inventario
     * @var cadena
     */
    public $totalInventario;

    /**
     * cantidad de existencias de un articulo especifico por cada bodega
     * @var array en una posicion la bodega, y en la otra la cantidad
     */
    public $cantidadesArticuloBodega;
    
    /**
     * Indicador del orden cronológio de la lista de registros
     * @var lógico
     */
    public $listaAscendente = true;
    
    /**
     * Número de registros de la lista
     * @var entero
     */
    public $registros = NULL;

    /**
     * Número de registros activos de la lista de articulos
     * @var entero
     */
    public $registrosActivos = NULL;

    /**
     * Número de registros activos de la lista de articulos
     * @var entero
     */
    public $registrosConsulta = NULL;

    /**
     * Orden predeterminado para organizar los listados
     * @var entero
     */
    public $ordenInicial = NULL; 
    
    

    /**
     * Inicializar la clase inventarios
     * @param entero $id Código interno o identificador de la unidad en la base de datos
     */
    public function __construct($id = NULL)
    {
        global $sql, $modulo;

        $this->urlBase          = '/' . $modulo->url;
        $this->url              = $modulo->url;
        $this->ordenInicial     = 'a.nombre';        

        if (isset($id)) {
            $this->idArticulo       = $id;
            $this->totalInventario  = $sql->obtenerValor("inventarios", "SUM(cantidad)", "id_articulo = '" . $id . "'");            
            $this->inventarioArticulo($id);
        }
        
    }

    /**
     * Cargar la cantidad existente de un determinado articulo
     * @param entero $id del articulo
     */
    public function cargarArticulo()
    {

        if (isset($this->idArticulo)) {
            $this->articulo = new Articulo($this->idArticulo);
            
        } else {
            return false;
            
        }
        
    }

    /**
     * Cargar la cantidad existente de un determinado articulo
     * @param entero $id del articulo
     */
    public function inventarioArticulo($id)
    {
        global $sql;

        if (isset($id) && $sql->existeItem('articulos', 'id', intval($id))) {
            $this->cantidadTotalArticulo = $sql->obtenerValor('inventarios', 'SUM(cantidad)', "id_articulo = '" . $id . "'");
            
        }
    }

    /**
     * Cargar la cantidad existente de un determinado articulo en una bodega especifica
     * @param entero $id del articulo
     * @param entero $id de la bodega
     */
    public function inventarioArticuloBodega($id, $bodega)
    {
        global $sql;

        if (isset($id) && $sql->existeItem("articulos", "id", intval($id)) && (isset($bodega) && $sql->existeItem("bodegas", "id", intval($bodega)))) {

            $this->cantidadArticuloBodega = $sql->obtenerValor("inventarios", "cantidad", $id, " id_bodega = '" . $bodega . "'");
        }
    }

    /**
     * Adicionar articulos al inventario
     * 
     * @global type $sql
     * @param type $idArticulo = identificador del articulo del que se van a ingresar existencias al inventario
     * @param type $cantidad   = cantidad que va a ser ingresada al inventario
     * @param type $bodega     = bodega a la cual se va a ingresar la mercancia
     * @param type $idItem     = identificador de la factura de compra, para en caso de que falle la insercion de los datos, esta factura sea eliminada del sistema
     * @return null
     */
    public function adicionar($idArticulo, $cantidad, $bodega, $idItem = NULL)
    {
        global $sql;

        $existeArticulo = $sql->existeItem("inventarios", "id_articulo", $idArticulo, " id_bodega = '" . $bodega . "'");

        if ($existeArticulo) {            
            $cantidadActual = $sql->obtenerValor("inventarios", "cantidad", "id_articulo = '" . $idArticulo . "' AND id_bodega = '" . $bodega . "'");

            $cantidad += $cantidadActual;

            $datos = array(
                "cantidad" => $cantidad
            );
            
            $consulta   = $sql->modificar("inventarios", $datos, "id_articulo = '" . $idArticulo . "' AND id_bodega = '" . $bodega . "'");
            
            $idItem1    = $idArticulo;
            
        } else {
            $datos = array(
                "id_articulo"   => $idArticulo,
                "cantidad"      => $cantidad,
                "id_bodega"     => $bodega
            );
            
            $consulta = $sql->insertar("inventarios", $datos);
            
            $idItem1 = $sql->ultimoId;
        }


        if ($consulta) {
            return $idItem1;
            
        } else { //si falla en algun punto la insercion de los datos al inventario, se devuelve false, y se elimina la factura de compra, pues no tiene sentido una factura de compra que no afecte correctamente el inventario
            return false;
            
        }
    }

    /**
     * Descontar articulos al inventario
     * 
     * @global resource $sql
     * 
     * @param int $idArticulo = identificador del articulo del que se van a descontar existencias al inventario
     * @param int $cantidad   = cantidad que va a ser descontada al inventario
     * @param int $bodega     = bodega a la cual se va a descontar la mercancia
     * @param type $idItem    = identificador de la factura de venta, para en caso de que falle la insercion de los datos, esta factura sea eliminada del sistema
     * 
     * @return $response   = si es correcto devuelve la palabra 'proseguir', en caso contrario, devuelve un texto que será mostrado en un mensaje de alerta
     */
    public function descontar($idArticulo, $cantidad, $bodega, $idItem = NULL)
    {
        global $sql, $sesion_configuracionGlobal, $textos;

        if (empty($idArticulo) || empty($cantidad) || empty($bodega)) {
            return NULL;
        }

        $response = 'proseguir';

        $existeArticulo = $sql->existeItem('articulos', 'id', $idArticulo); //verificar si existe el articulo en la tabla de articulos

        if ($existeArticulo) {//if exists
            $existeInventario = $sql->existeItem('inventarios', 'id_articulo', $idArticulo, " id_bodega = '" . $bodega . "'"); //si ya hay existencias del articulo en el inventario, osea, si ya hay un registro en la tabla articulo de este articulo en esta bodega

            if ($existeInventario) {//if exists
                $cantidadActual = $sql->obtenerValor('inventarios', 'cantidad', "id_articulo = '" . $idArticulo . "' AND id_bodega = '" . $bodega . "'"); //consulto la cantidad actual

                if (($cantidadActual < $cantidad) && !$sesion_configuracionGlobal->facturarNegativo) {//si la cantidad en existencias es menor a la que se desea retirar, y el sistema no permite retirar en negativo
                    $response = str_replace('%1', $cantidadActual, $sql->textos('SIN_EXISTENCIA_SUFICIENTE_INVENTARIOS'));
                    
                } else {//realizo el descuento de la tabla de inventarios
                    $nuevaCantidad = $cantidadActual - $cantidad;

                    $datos = array(
                        'cantidad' => $nuevaCantidad
                    );
                    $consulta = $sql->modificar('inventarios', $datos, "id_articulo = '" . $idArticulo . "' AND id_bodega = '" . $bodega . "'");
                    
                }
                
            } else { //no hay registro en la tabla inventarios
                if ($sesion_configuracionGlobal->facturarNegativo) {//se verifica que el sistema permita facturar en negativo
                    $cantidad = 0 - $cantidad; //y obviamente el primer registro ingresaria en negativo

                    $datos = array(
                        "id_articulo"   => $idArticulo,
                        "cantidad"      => $cantidad,
                        "id_bodega"     => $bodega
                    );
                    $consulta = $sql->insertar("inventarios", $datos);
                } else {
                    $response = $textos->id('SIN_EXISTENCIA_INVENTARIOS'); //si no permite facturar en negativo informaria al usuario con la respuesta de la funcion
                }
            }
            
        } else {
            if($idItem != NULL){
                $sql->eliminar('facturas_venta', 'id = "' . $idItem . '"');
            }
            
            return false;
        }

        return $response;
    }

    /**
     * Listar los articulos por cada una de las bodegas donde tiene existencias
     * @param entero  $idArticulo identificador del articulo el cual vamos a listar
     * @return arreglo             Lista de articulos
     */
    public function cantidadArticuloPorBodega($idArticulo = NULL)
    {
        global $sql;

        if (isset($idArticulo)) {
            $idArticulo = $idArticulo;
            
        } else if (!isset($idArticulo) && isset($this->idArticulo)) {
            $idArticulo = $this->idArticulo;
            
        } else {
            return false;
            
        }

        $tablas = array(
            'i' => 'inventarios',
            'b' => 'bodegas',
            's' => 'sedes_empresa',
            'a' => 'articulos'
        );

        $columnas = array(
            'id'            => 'a.id',
            'cantidad'      => 'i.cantidad',
            'idSede'        => 's.id',
            'sede'          => 's.nombre',
            'idBodega'      => 'i.id_bodega',
            'bodega'        => 'b.nombre',
            'articulo'      => 'a.nombre'
        );

        $condicion = 'i.id_bodega = b.id AND b.id_sede = s.id AND  i.id_articulo = a.id AND i.id_articulo = "' . $idArticulo . '" AND i.cantidad > 0';

//        $sql->depurar = true;
        $consulta = $sql->seleccionar($tablas, $columnas, $condicion);

        if ($sql->filasDevueltas) {

            while ($objeto = $sql->filaEnObjeto($consulta)) {
                $this->cantidadesArticuloBodega[] = $objeto;
            }
        }
    }

    /**
     * Funcion que se encarga de realizar el movimiento de mercancia entre bodegas de una sede
     * 
     * @global type $sql
     * @param int $idBodegaO = identificador de la bodega de origen
     * @param int $idBodegaD = identificador de la bodega destino
     * @param int $cantidad
     * @param mediumint $idArticulo
     * @return boolean 
     */
    public function moverMercanciaEntreBodegas($idBodegaO, $idBodegaD, $cantidad, $idArticulo)
    {
        global $sql, $sesion_usuarioSesion;

        if (empty($idArticulo) || empty($cantidad) || empty($idBodegaO) || empty($idBodegaD)) {
            return NULL;
        }

        $cantidadActualOrigen = $sql->obtenerValor('inventarios', 'cantidad', 'id_articulo = "' . $idArticulo . '" AND id_bodega = "' . $idBodegaO . '"');

        $totalOrigen = $cantidadActualOrigen - $cantidad;

        $datosOrigen = array('cantidad' => $totalOrigen);
        $sql->modificar('inventarios', $datosOrigen, 'id_articulo = "' . $idArticulo . '" AND id_bodega = "' . $idBodegaO . '"');


        $cantidadActualDestino = $sql->obtenerValor('inventarios', 'cantidad', 'id_articulo = "' . $idArticulo . '" AND id_bodega = "' . $idBodegaD . '"');

        if ($cantidadActualDestino == '') {

            $cantidadActualDestino = 0;
            $totalDestino = $cantidadActualDestino + $cantidad;
            $datosDestino = array(
                'id_articulo' => $idArticulo,
                'cantidad' => $totalDestino,
                'id_bodega' => $idBodegaD
            );
            $sql->insertar('inventarios', $datosDestino);
        }

        $totalDestino = $cantidadActualDestino + $cantidad;

        $datosDestino = array('cantidad' => $totalDestino);
        $query = $sql->modificar('inventarios', $datosDestino, 'id_articulo = "' . $idArticulo . '" AND id_bodega = "' . $idBodegaD . '"');

        if ($query) {
            $movimiento = new MovimientoMercancia();
            $datos = array(
                'id_articulo'           => $idArticulo,
                'cantidad'              => $cantidad,
                'id_bodega_origen'      => $idBodegaO,
                'id_bodega_destino'     => $idBodegaD,
                'id_usuario'            => $sesion_usuarioSesion->id
            );
            $movimiento->adicionar($datos);
        }

        return $query;
    }
    
    
    
    
    /**
     *
     * Listar los articulos 
     *
     * @param entero  $cantidad    = Número de articulos a incluir en la lista (0 = todas las entradas)
     * @param arreglo $excepcion   = Arreglo con los códigos internos o identificadores a omitir en la lista
     * @param objeto  $textos      = objeto global de traduccion de textos = objeto global de traduccion de textos
     * @param cadena  $condicion   = Condición adicional (SQL)
     * @return arreglo             Lista de articulos
     *
     */
    public function listar($inicio = 0, $cantidad = 0, $excepcion = NULL, $condicionGlobal = NULL, $orden = NULL) {
        global $sql, $textos, $configuracion, $sesion_configuracionGlobal;

        /*         * * Validar la fila inicial de la consulta ** */
        if (!is_int($inicio) || $inicio < 0) {
            $inicio = 0;
        }

        /*         * * Validar la cantidad de registros requeridos en la consulta ** */
        if (!is_int($cantidad) || $cantidad <= 0) {
            $cantidad = 0;
        }

        /*         * * Validar que la condición sea una cadena de texto ** */

        $condicion = '';


        /*         * * Validar que la excepción sea un arreglo y contenga elementos ** */
        if (isset($excepcion) && is_array($excepcion) && count($excepcion)) {
            $excepcion = implode(',', $excepcion);
            $condicion .= 'i.id NOT IN (' . $excepcion . ') AND ';
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
            'a'     => 'articulos',
            'i'     => 'inventarios',
            'b'     => 'bodegas'
        );

        $columnas = array(
            'id'                        => 'i.id',
            'idArticulo'                => 'a.id',
            'codigo_oem'                => 'a.codigo_oem',
            'plu_interno'               => 'a.plu_interno',
            'articulo'                  => 'a.nombre',
            'bodega'                    => 'b.nombre',
            'cantidadTotalArticulo'     => 'i.cantidad',
        );

        if (!empty($condicionGlobal)) {
            $condicion .= $condicionGlobal . ' AND ';
        }

        $condicion .= 'i.id_articulo = a.id AND i.id_bodega = b.id ';

        if (is_null($this->registrosConsulta)) {//Este dato se necesita para la info de la paginación
            $sql->seleccionar($tablas, $columnas, $condicion);
            $this->registrosConsulta = $sql->filasDevueltas;
        }

        //$sql->depurar = true;
        $consulta = $sql->seleccionar($tablas, $columnas, $condicion, 'i.id', $orden, $inicio, $cantidad);
        
        
        $idPrincipalArticulo = (string)$sesion_configuracionGlobal->idPrincipalArticulo;
        
        if ($sql->filasDevueltas) {
            $lista = array();

            while ($objeto = $sql->filaEnObjeto($consulta)) {
                $objeto->$idPrincipalArticulo = Recursos::completarCeros($objeto->$idPrincipalArticulo, 6);
                //$objeto->estado = ($objeto->activo) ? HTML::frase($textos->id('ACTIVO'), 'activo') : HTML::frase($textos->id('INACTIVO'), 'inactivo');
                if($objeto->referencia){
                    $objeto->nombre = $objeto->nombre.' :: ('.$objeto->referencia.')';
                } 
                
                $lista[] = $objeto;
            }
        }

        return $lista;
    }    
    
    /**
     * Metodo que genera los datos que contendra la tabla principal del modulo
     * 
     * @global objeto $textos = objeto global de traduccion de textos
     * @param type $arregloRegistros
     * @param type $datosPaginacion
     * @return type 
     */
    public function generarTabla($arregloRegistros, $datosPaginacion = NULL, $tablaModal = false) {
        global $textos, $sesion_configuracionGlobal, $sesion_usuarioSesion;
        
        $idPrincipalArticulo    = (string)$sesion_configuracionGlobal->idPrincipalArticulo;
        
        $idPrincipalArticulo1    = ($idPrincipalArticulo  == 'id') ?  'idArticulo' :  $idPrincipalArticulo;        
        
        $arrayIdArticulo        = array('id' => $textos->id('ID_AUTOMATICO'), 'codigo_oem' => $textos->id('CODIGO_OEM'), 'plu_interno' => $textos->id('PLU'));        
        
        $datosTabla = array(
            HTML::parrafo($arrayIdArticulo[$idPrincipalArticulo], 'centrado')   => ''.$idPrincipalArticulo1.'|a.'.$idPrincipalArticulo.'', //concateno el nombre del alias para usarlo al armar la tabla con el fila en objeto
            HTML::parrafo($textos->id('ARTICULO'), '')                          => 'articulo|a.nombre', //la busqueda, al armar la tabla dividira la cadena y usara el que necesite
            HTML::parrafo($textos->id('BODEGA'), '')                            => 'bodega|b.nombre',
            HTML::parrafo($textos->id('CANTIDAD'), '')                          => 'cantidadTotalArticulo|i.cantidad',
        );
        //ruta del metodo paginador
        $rutaPaginador = '/ajax' . $this->urlBase . '/move';
//
//        $moverMercancia = '';
//
//        $puedeMoverMercancia = Perfil::verificarPermisosBoton('botonMoverMercanciaBodega');
//        if ($puedeMoverMercancia || $sesion_usuarioSesion->id == 0) {
//            $moverMercancia1    = HTML::formaAjax($textos->id('MOVER_MERCANCIA'), 'contenedorMenuMoverMercanciaBodega', 'moverMercanciaBodega', '', '/ajax/articulos/moverMercancia', array('id' => ''));
//            $moverMercancia     = HTML::contenedor($moverMercancia1, '', 'botonMoverMercanciaBodega');
//            
//        }

//        $botonesExtras      = array($moverMercancia);
        $estilosColumnas    = array('columna1', 'descripcion-articulo', 'texto-alineado-izquierda', 'texto-alineado-izquierda', 'columna5', 'columna6', 'columna7', 'columna8');        
        $botonDerecho       = HTML::crearMenuBotonDerecho('INVENTARIOS', $botonesExtras);
              

        return Recursos::generarTablaRegistros($arregloRegistros, $datosTabla, $rutaPaginador, $datosPaginacion, $estilosColumnas, $tablaModal) . $botonDerecho;
        
    }
    
    
    /**
     *
     * Cargar los datos de un item
     *
     * @param entero $id Código interno o identificador del item en la base de datos
     *
     */
    public function cargarRegistroInventario($id) {
        global $sql, $configuracion, $textos;

        if (!empty($id) && $sql->existeItem('inventarios', 'id', intval($id))) {

             $tablas = array(
            'a'     => 'articulos',
            'i'     => 'inventarios',
            'b'     => 'bodegas'
        );

        $columnas = array(
            'id'                        => 'i.id',
            'idArticulo'                => 'a.id',
            'codigoOem'                 => 'a.codigo_oem',
            'plu_interno'               => 'a.plu_interno',
            'articulo'                  => 'a.nombre',
            'bodega'                    => 'b.nombre',
            'cantidadTotalArticulo'     => 'i.cantidad',
        );

            $condicion = 'i.id = "' . $id . '"AND i.id_articulo = a.id AND i.id_bodega = b.id ';

            $consulta = $sql->seleccionar($tablas, $columnas, $condicion);

            if ($sql->filasDevueltas) {
                $fila = $sql->filaEnObjeto($consulta);
                
                return $fila;
                                
            }
            
            return false;
        }
    }
    
    
/**
     * Adicionar registros a partir de un archivo excel
     *
     * @global recurso $sql         = Objeto global de interaccion con la BD
     * @global array $configuracion = arreglo global con la información de configuración no parametrizable
     * @global file $archivo_masivo = archivo de tipo xls (97-2003)
     * @param  arreglo $datos       Datos del articulo a adicionar
     * @return entero               Código interno o identificador del articulo en la base de datos (NULL si hubo error)
     */
    public function adicionarMasivo($datos) {
        global $sql, $configuracion, $archivo_masivo;

        if (empty($archivo_masivo['tmp_name'])) {
            return false;
            
        } else {
            $validarFormato = Archivo::validarArchivo($archivo_masivo, array('xls'));  
            
            if (!$validarFormato) {
                $configuracionRuta  = $configuracion['RUTAS']['media'] . "/" . $configuracion["RUTAS"]["documentos"];
                $recurso            = Archivo::subirArchivoAlServidor($archivo_masivo, $configuracionRuta);

                require_once $configuracion['RUTAS']['clases'] . '/excel_reader2.php';
                
                $data = new Spreadsheet_Excel_Reader($configuracionRuta . '/' . $recurso);

                $row = 1;
                $col = 1;

                if ($datos['inicial'] == 1) {
                    $row++;
                    $campos = array();
                    
                    if ($datos['id_articulo'] != 0)
                        $campos['id_articulo'] = $datos['id_articulo'];
                    
                    if ($datos['cantidad'] != 0)
                        $campos['cantidad'] = $datos['cantidad'];
                    
                    if ($datos['id_bodega'] != 0)
                        $campos['id_bodega'] = $datos['id_bodega'];
                    
                }

                $valor1    = $data->val($row, $col);
                $respuesta = array();

                while ($valor1 != null) {
                    if ($datos['inicial'] == 0) {
                        $respuesta[$col] = $valor1;
                        $col++;
                        
                    } else {
                        $datosCarga = array();

                        foreach ($campos AS $nombre => $valor) {
                            $valor = $data->val($row, $valor);
                            
                            if ($nombre == 'id_articulo' && !is_numeric($nombre)) {
                                $valor = $sql->obtenerValor('articulos', 'id', "nombre = '" . $valor . "'");
                                
                            } else if ($nombre == 'id_bodega' && !is_numeric($nombre)) {
                                $valor = $sql->obtenerValor('bodegas', 'id', "nombre = '" . $valor . "'");
                                
                            } 

                            $datosCarga[$nombre] = $valor;
                        }

                        $sql->insertar('inventarios', $datosCarga);

                        $row++;
                        $respuesta = true;
                    }
                    
                    $valor1 = $data->val($row, $col);
                }
                
                Archivo::eliminarArchivoDelServidor($configuracionRuta . '/' . $recurso);
                
                return $respuesta;
                
            } else {
                return false;
                
            }
            
        }
        
    }
      
    

}
