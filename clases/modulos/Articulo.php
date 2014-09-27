<?php

/**
 *
 * @package     FOM
 * @subpackage  Articulos para la venta
 * @author      Pablo Andrés Vélez Vidal <pavelez@genesyscorp.com.co>
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2012 Genesys Corporation.
 * @version     0.2
 *
 * Módulo encargado de gestionar la información de los artículos que se encuentran registrados en el sistema. A través de este módulo 
 * se gestionan actividades como el ingreso de nuevos articulos, la modificación de los articulos existentes en el sistema, la inactivación
 * de los articulos, conocer el stock de un determinado articulo por bodega, realizar movimientos de mercancia entre bodegas y las funciones de
 * imprimir códigos de barra para los articulos.
 * 
 * */
class Articulo {

    /**
     * Código interno o identificador de la unidad en la base de datos
     * @var entero
     */
    public $id;

    /**
     * URL relativa del módulo de articulo
     * @var cadena
     */
    public $urlBase;

    /**
     * URL relativa del módulo de aticulo
     * @var cadena
     */
    public $url;

    /**
     * Código interno o identificador del modulo
     * @var entero
     */
    public $idModulo;

    /**
     * Código interno de la moto principal a la que sirve el articulo
     * @var entero
     */
    public $idMoto;

    /**
     * Moto principal para la que viene determinada dicho articulo
     * @var entero
     */
    public $moto;

    /**
     * Código interno de la moto principal a la que sirve el articulo
     * @var entero
     */
    public $idLinea;

    /**
     * Moto principal para la que viene determinada dicho articulo
     * @var entero
     */
    public $linea;

    /**
     * Código OEM referente alcodigo oem universal
     * @var entero
     */
    public $codigo_oem;

    /**
     * Código interno del subgrupo al cual pertenece
     * @var entero
     */
    public $idSubgrupo;

    /**
     * nombre del subgrupo
     * @var entero
     */
    public $subgrupo;

    /**
     * codigo que viene en el catalogo
     * @var cadena
     */
    public $referencia;

    /**
     * codigo interno
     * @var cadena
     */
    public $plu_interno;

    /**
     * Nombre del articulo
     * @var entero
     */
    public $nombre;

    /**
     * identificador de la unidad de medida del articulo
     * @var entero
     */
    public $idUnidad;

    /**
     * Nombre de la unidad de medida del articulo
     * @var entero
     */
    public $unidad;

    /**
     * Nacionalidad de proveniencia del articulo
     * @var entero
     */
    public $idPais;

    /**
     * Codigo corto del articulo
     * @var cadena
     */
    public $codigoPais;

    /**
     * Codigo corto del articulo
     * @var cadena
     */
    public $pais;

    /**
     * identificador de la marca del articulo
     * @var entero
     */
    public $idMarca;

    /**
     * Nombre de la marca del articulo
     * @var entero
     */
    public $marca;

    /**
     * Modelo del articulo
     * @var entero
     */
    public $modelo;

    /**
     * concepto 1 de precio de venta
     * @var entero
     */
    public $concepto1;

    /**
     * precio 1 de precio de venta
     * @var entero
     */
    public $precio1;

    /**
     * concepto 2 de precio de venta
     * @var entero
     */
    public $concepto2;

    /**
     * precio 2 de precio de venta
     * @var entero
     */
    public $precio2;

    /**
     * concepto 3 de precio de venta
     * @var entero
     */
    public $concepto3;

    /**
     * precio 3 de precio de venta
     * @var entero
     */
    public $precio3;

    /**
     * concepto 4 de precio de venta
     * @var entero
     */
    public $concepto4;

    /**
     * precio 4 de precio de venta
     * @var entero
     */
    public $precio4;

    /**
     * medidas del articulo en string, se ingrsan por formulario como
     * largo, ancho y alto, se concatenan y se vuelven String en la BD
     * @var entero
     */
    public $largo;

    /**
     * medidas del articulo en string, se ingrsan por formulario como
     * largo, ancho y alto, se concatenan y se vuelven String en la BD
     * @var entero
     */
    public $ancho;

    /**
     * medidas del articulo en string, se ingrsan por formulario como
     * largo, ancho y alto, se concatenan y se vuelven String en la BD
     * @var entero
     */
    public $alto;

    /**
     * medidas del articulo en string, se ingrsan por formulario como
     * largo, ancho y alto, se concatenan y se vuelven String en la BD
     * @var entero
     */
    public $otraMedida;

    /**
     * Medidas del articulo
     * @var entero
     */
    public $aplicacionExtra;

    /**
     * Fecha registro
     * @var fecha
     */
    public $fechaRegistro;

    /**
     * Número de registros de la lista
     * @var entero
     */
    public $activo;

    /**
     * Cantidad de este articulo en el inventario
     * @var entero
     */
    public $cantidadInventario;

    /**
     * Arreglo con los ids y nombres de las motos a las cuales sirve este articulo
     * @var entero
     */
    public $motosAplicacion = NULL;

    /**
     * Ultimo precio al que se compro el articulo
     * @var entero
     */
    public $ultimoPrecioCompra = 0;
       
    /**
     * Precio promedio teniendo en cuenta el numero de dias para realizar el precio promedio del articulo
     * @var entero
     */
    public $precioPromedioCompra = 0;    

    /**
     * Ruta de la imagen en tamaño normal
     * @var cadena
     */
    public $imagenPrincipal;

    /**
     * Ruta de la imagen  en miniatura
     * @var cadena
     */
    public $imagenMiniatura;

    /**
     * Ruta de la imagen en tamaño normal
     * @var cadena
     */
    public $imagenPrincipal2;

    /**
     * Ruta de la imagen  en miniatura
     * @var cadena
     */
    public $imagenMiniatura2;
    
    /**
     * Iva, impuesto del valor añadido
     * @var cadena
     */
    public $iva;
    
    /**
     * Determina si el atticulo esta grabado con iva
     * @var enum
     */
    public $gravadoIva; 
    
    /**
     * Minima cantidad de este articulo que se debe mantener en stock
     * @var cadena
     */
    public $stockMinimo; 
    
    /**
     * Maxima cantidad de este articulo que se debe mantener en stock
     * @var cadena
     */
    public $stockMaximo;     
      
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
     * Metodo constructor 
     * 
     * @global recurso $sql         = Objeto global de interaccion con la BD
     * @global type $modulo
     * @param  entero $id Código interno o identificador de la unidad en la base de datos
     */
    public function __construct($id = NULL) {
        global $sql, $modulo;

        $this->urlBase          = '/' . $modulo->url;
        $this->url              = $modulo->url;
        $this->idModulo         = $modulo->id;
        //Saber el numero de registros
        $this->registros        = $sql->obtenerValor('articulos', 'COUNT(id)', 'id != "0"');
        //Saber el numero de registros activos
        $this->registrosActivos = $sql->obtenerValor('articulos', 'COUNT(id)', 'activo = "1" AND id != "0" ');
        //establecer el valor del campo predeterminado para organizar los listados
        $this->ordenInicial     = 'a.nombre';


        if (isset($id)) {
            $this->cargar($id);
            $inventario = new Inventario($id);
            $this->cantidadTotalInventario = $inventario->cantidadArticulo;
            
        }
        
    }

//Fin del metodo constructor
//Habra que pensar que tan necesaria es la ubicacion, y segun esto, se deberá hacer una tabla
//gondola - articulo, por si el mismo articulo puede estar en varias bodegas, en distintas sedes

    /**
     * Cargar un objeto con los datos del articulo
     *
     * @global recurso $sql         = Objeto global de interaccion con la BD
     * @global type $configuracion
     * @global type $sesion_configuracionGlobal
     * @param entero $id Código interno o identificador de la unidad en la base de datos
     */
    public function cargar($id) {
        global $sql, $configuracion, $sesion_configuracionGlobal;

        if (isset($id) && $sql->existeItem('articulos', 'id', intval($id))) {

            $tablas = array(
                'a'     => 'articulos',
                'su'    => 'subgrupos',
                'g'     => 'grupos',
                'm'     => 'motos',
                'l'     => 'lineas',
                'ma'    => 'marcas',
                'u'     => 'unidades',
                'p'     => 'paises'
            );

            $columnas = array(
                'id'                    => 'a.id',
                'nombre'                => 'a.nombre',
                'idMoto'                => 'a.id_moto',
                'moto'                  => 'm.nombre',
                'idLinea'               => 'a.id_linea',
                'linea'                 => 'l.nombre',
                'idGrupo'               => 'su.id_grupo',
                'grupo'                 => 'g.nombre',
                'idSubgrupo'            => 'a.id_subgrupo',
                'subgrupo'              => 'su.nombre',
                'codigoOem'             => 'a.codigo_oem',
                'referencia'            => 'a.referencia',
                'plu_interno'           => 'a.plu_interno',
                'nombre'                => 'a.nombre',
                'idUnidad'              => 'a.id_unidad',
                'unidad'                => 'u.nombre',
                'idPais'                => 'a.id_pais',
                'codigo'                => 'p.codigo_iso',
                'codigoPais'            => 'p.codigo_comercial',
                'pais'                  => 'p.nombre',
                'idMarca'               => 'a.id_marca',
                'marca'                 => 'ma.nombre',
                'modelo'                => 'a.modelo',
                'imagen'                => 'a.id_imagen',
                'imagen2'               => 'a.id_imagen2',
                'concepto1'             => 'a.concepto1',
                'precio1'               => 'a.precio1',
                'concepto2'             => 'a.concepto2',
                'precio2'               => 'a.precio2',
                'concepto3'             => 'a.concepto3',
                'precio3'               => 'a.precio3',
                'concepto4'             => 'a.concepto4',
                'precio4'               => 'a.precio4',
                'largo'                 => 'a.largo',
                'ancho'                 => 'a.ancho',
                'alto'                  => 'a.alto',
                'otraMedida'            => 'a.otra_medida',
                'aplicacionExtra'       => 'a.aplicacion_extra',
                'fechaRegistro'         => 'a.fecha_registro',
                'activo'                => 'a.activo',
                'gravadoIva'            => 'a.gravado_iva',
                'iva'                   => 'a.iva',
                'stockMinimo'           => 'a.stock_minimo',
                'stockMaximo'           => 'a.stock_maximo',
                'ultimoPrecioCompra'    => 'a.ultimo_precio_compra',
                'completo'              => 'IF((a.nombre!="" AND a.codigo_oem!="" AND a.id_subgrupo!=0 AND a.referencia!="" AND a.id_unidad!=0 AND a.id_pais!=0 AND a.concepto1!="" AND a.precio1!=""), "1", "0")'
            );

            $condicion = 'a.id_subgrupo = su.id AND su.id_grupo = g.id AND a.id_unidad = u.id AND a.id_marca = ma.id AND a.id_pais = p.id AND a.id_moto = m.id AND a.id_linea = l.id AND a.id = "' . $id . '"';

            $consulta = $sql->seleccionar($tablas, $columnas, $condicion);

            if ($sql->filasDevueltas) {
                $fila = $sql->filaEnObjeto($consulta);

                foreach ($fila as $propiedad => $valor) {
                    $this->$propiedad = $valor;
                }

                $this->imagenPrincipal  = $configuracion['RUTAS']['media'] . $configuracion['RUTAS']['imagenesDinamicas'] . '/' . $this->imagen;
                $this->imagenMiniatura  = $configuracion['RUTAS']['media'] . $configuracion['RUTAS']['imagenesMiniaturas'] . '/' . $this->imagen;

                $this->imagenPrincipal2 = $configuracion['RUTAS']['media'] . $configuracion['RUTAS']['imagenesDinamicas'] . '/' . $this->imagen2;
                $this->imagenMiniatura2 = $configuracion['RUTAS']['media'] . $configuracion['RUTAS']['imagenesMiniaturas'] . '/' . $this->imagen2;

                $this->codigoPais = $this->codigoPais . HTML::imagen($configuracion['SERVIDOR']['media'] . $configuracion['RUTAS']['iconosBanderas'] . '/' . strtolower($this->codigo) . '.png', 'miniaturaBanderas');

                $this->precioPromedioCompra = $sql->obtenerValor('articulos_factura_compra', 'AVG(precio)', 'id_articulo = "'.$id.'" AND  ( fecha BETWEEN  DATE_SUB(NOW(),INTERVAL '.$sesion_configuracionGlobal->diasPromedioPonderado.' DAY) AND NOW()  )');
                $this->precioPromedioCompra = Recursos::formatearNumero($this->precioPromedioCompra, '$', $sesion_configuracionGlobal->cantidadDecimales);
                
                $this->url = $this->urlBase . '/' . $this->id;
            }
            
        }
        
    }

    /**
     * Metodo encargado de adicionar la info de un articulo a la BD
     *
     * @global recurso $sql         = Objeto global de interaccion con la BD    objeto global de interaccion con la BD
     * @global type $archivo_imagen archivo imagen 1
     * @global objeto $textos   objeto global de traduccion de textos
     * @global type $archivo_imagen2 archivo de imágen 2
     * @global type $sesion_configuracionGlobal objeto global que contiene la configuración del sistema
     * @param type $datos       Datos del articulo a adicionar
     * @return boolean          Código interno o identificador del articulo en la base de datos (NULL si hubo error)
     */
    public function adicionar($datos) {
        global $sql, $archivo_imagen, $textos, $archivo_imagen2, $sesion_configuracionGlobal;

        $idImagen       = '';
        $idImagen2      = '';
        $imagen         = new Imagen();
        
        if (!empty($archivo_imagen['tmp_name'])) {
            $idImagen = $imagen->cargarImagen($archivo_imagen);
        }

        if (!empty($archivo_imagen2['tmp_name'])) {
            $idImagen2 = $imagen->cargarImagen($archivo_imagen2);
        }

        if (empty($datos['concepto1'])) {
            $datos['concepto1'] = $textos->id('PRECIO_VENTA1');
        }
        
        if (empty($datos['concepto2'])) {
            $datos['concepto2'] = $textos->id('PRECIO_VENTA2');
        }
        
        if (empty($datos['concepto3'])) {
            $datos['concepto3'] = $textos->id('PRECIO_VENTA3');
        }
        
        if (empty($datos['concepto4'])) {
            $datos['concepto4'] = $textos->id('PRECIO_VENTA4');
        }

        if (!empty($datos['listaMotos'])) {
            $datos['listaMotos']    = substr($datos['listaMotos'], 0, -1);
            $datosListaMotos        = explode("|", $datos['listaMotos']);
            $idMoto                 = $datosListaMotos[0]; //funcion end, devuelve la ultima posicion de un arreglo conocido
            
        } else {
            $datosListaMotos = array();
            $idMoto = '1';
            
        }

        $datosArticulo = array(
            'id_moto'           => $idMoto,
            'id_subgrupo'       => $datos['id_subgrupo'],
            'id_linea'          => $datos['id_linea'],
            'codigo_oem'        => $datos['codigo_oem'],
            'referencia'        => $datos['referencia'],
            'plu_interno'       => $datos['plu_interno'],
            'nombre'            => $datos['nombre'],
            'id_unidad'         => $datos['id_unidad'],
            'id_pais'           => $datos['id_pais'],
            'id_marca'          => $datos['id_marca'],
            'modelo'            => $datos['modelo'],
            'concepto1'         => $datos['concepto1'],
            'precio1'           => $datos['precio1'],
            'concepto2'         => $datos['concepto2'],
            'precio2'           => $datos['precio2'],
            'concepto3'         => $datos['concepto3'],
            'precio3'           => $datos['precio3'],
            'concepto4'         => $datos['concepto4'],
            'precio4'           => $datos['precio4'],
            'largo'             => $datos['largo'],
            'ancho'             => $datos['ancho'],
            'alto'              => $datos['alto'],
            'otra_medida'       => $datos['otra_medida'],
            'aplicacion_extra'  => $datos['aplicacion_extra'],
            'stock_minimo'      => $datos['stock_minimo'],
            'stock_maximo'      => $datos['stock_maximo'],
        );

        $datosArticulo['activo'] = (isset($datos['activo'])) ? '1': '0';
        
        if(isset($datos['gravado_iva'])){
            $datosArticulo['iva'] = (!empty($datos['iva'])) ? $datos['iva'] : $sesion_configuracionGlobal->ivaGeneral;
            $datosArticulo['gravado_iva'] = '1';
            
        } else {
            $datosArticulo['iva']           = '0';
            $datosArticulo['gravado_iva']   = '0';
            
        }

        $datosArticulo['fecha_registro']    = date('Y-m-d');

        $datosArticulo['id_imagen']         = $idImagen;
        $datosArticulo['id_imagen2']        = $idImagen2;
        
        $sql->iniciarTransaccion();

        $consulta   = $sql->insertar('articulos', $datosArticulo);
        $idItem     = $sql->ultimoId;

        if ($consulta) {            
            if (empty($datos['plu_interno'])) {
                $datosPlu = array(//Esta consulta recupera el identificador autonumerico del articulo y lo pone como el codigo PLU del mismo
                    'plu_interno' => $idItem
                );

                $modificarPlu = $sql->modificar('articulos', $datosPlu, 'id = "' . $idItem . '"');
                
                if(!$modificarPlu){
                    $sql->cancelarTransaccion();
                }
            }

            $aplicacionMoto = new ArticuloMotos();
            $aplicacionMoto->insertarMotosAplicacion($idItem, $datosListaMotos);

            $sql->finalizarTransaccion();
            return $idItem;
            
        } else {
            $sql->cancelarTransaccion();
            
        }
        
    }

    /**
     * Función para modificar la información de un articulo en la BD
     *
     * @global recurso $sql         = Objeto global de interaccion con la BD                        Objeto global de interaccion con la BD
     * @global type $archivo_imagen             archivo de imagen
     * @global objeto $textos                   objeto global de traduccion de textos
     * @global type $archivo_imagen2            archivo de imagen 2
     * @global type $sesion_configuracionGlobal objeto global que contiene la configuración del sistema
     * @param  arreglo $datos               = Datos del articulo a modificar
     * @return null|boolean                 = Indica si el procedimiento se pudo realizar correctamente o no
     */
    public function modificar($datos) {
        global $sql, $archivo_imagen, $textos, $archivo_imagen2, $sesion_configuracionGlobal;

        if (!isset($this->id)) {
            return NULL;
        }

        $idImagen   = $this->imagen;
        $idImagen2  = $this->imagen2;
        
        $imagen     = new Imagen();
        
        if (isset($archivo_imagen) && !empty($archivo_imagen['tmp_name'])) {
            if ($this->imagen != '00/00000001.png') {
                Archivo::eliminarArchivoDelServidor(array($this->imagenPrincipal, $this->imagenMiniatura));

            }
            $idImagen = $imagen->cargarImagen($archivo_imagen);
        }

        if (isset($archivo_imagen2) && !empty($archivo_imagen2['tmp_name'])) {
            if ($this->imagen2 != '00/00000001.png') {
                Archivo::eliminarArchivoDelServidor(array($this->imagenPrincipal2, $this->imagenMiniatura2));

            }
            $idImagen2 = $imagen->cargarImagen($archivo_imagen2);
        }

        if (empty($datos['concepto1'])) {
            $datos['concepto1'] = $textos->id('PRECIO_VENTA1');
        }
        if (empty($datos['concepto2'])) {
            $datos['concepto2'] = $textos->id('PRECIO_VENTA2');
        }
        if (empty($datos['concepto3'])) {
            $datos['concepto3'] = $textos->id('PRECIO_VENTA3');
        }
        if (empty($datos['concepto4'])) {
            $datos['concepto4'] = $textos->id('PRECIO_VENTA4');
        }

        if (!empty($datos['listaMotos'])) {
            $datos['listaMotos']    = substr($datos['listaMotos'], 0, -1);
            $datosListaMotos        = explode("|", $datos['listaMotos']);
            $idMoto                 = $datosListaMotos[0]; //funcion end, devuelve la ultima posicion de un arreglo conocido,
            
        } else {
            $datosListaMotos    = array();
            $idMoto             = $this->idMoto;
            
        }

        $datosArticulo = array(
            'id_moto'           => $idMoto,
            'id_subgrupo'       => $datos['id_subgrupo'],
            'id_linea'          => $datos['id_linea'],            
            'codigo_oem'        => $datos['codigo_oem'],
            'referencia'        => $datos['referencia'],
            'plu_interno'       => $datos['plu_interno'],
            'nombre'            => $datos['nombre'],
            'id_unidad'         => $datos['id_unidad'],
            'id_pais'           => $datos['id_pais'],
            'id_marca'          => $datos['id_marca'],
            'modelo'            => $datos['modelo'],
            'concepto1'         => $datos['concepto1'],
            'precio1'           => $datos['precio1'],
            'concepto2'         => $datos['concepto2'],
            'precio2'           => $datos['precio2'],
            'concepto3'         => $datos['concepto3'],
            'precio3'           => $datos['precio3'],
            'concepto4'         => $datos['concepto4'],
            'precio4'           => $datos['precio4'],
            'largo'             => $datos['largo'],
            'ancho'             => $datos['ancho'],
            'alto'              => $datos['alto'],
            'otra_medida'       => $datos['otra_medida'],
            'aplicacion_extra'  => $datos['aplicacion_extra'],
            'stock_minimo'      => $datos['stock_minimo'],
            'stock_maximo'      => $datos['stock_maximo'],
        );

        $datosArticulo['activo'] = (isset($datos['activo'])) ? '1' : '0';
        
        if(isset($datos['gravado_iva'])){
            $datosArticulo['iva']           = (!empty($datos['iva'])) ? $datos['iva'] : $sesion_configuracionGlobal->ivaGeneral;
            $datosArticulo['gravado_iva']   = '1';
            
        } else {
            $datosArticulo['iva']           = '0';
            $datosArticulo['gravado_iva']   = '0';
            
        }

        $datosArticulo['id_imagen']     = $idImagen;
        $datosArticulo['id_imagen2']    = $idImagen2;

        $sql->iniciarTransaccion();
        
        $consulta = $sql->modificar('articulos', $datosArticulo, 'id = "' . $this->id . '" ');


        if ($consulta) {
            $aplicacionMoto = new ArticuloMotos();
            $aplicacionMoto->modificarMotosAplicacion($this->id, $datosListaMotos);

            $sql->finalizarTransaccion();
            return $this->id;
            
        } else {
            $sql->cancelarTransaccion();
            return false;
            
        }
    }

    /**
     * Eliminar un articulo
     * 
     * @param entero $id    = Código interno o identificador de una unidad en la base de datos
     * @global recurso $sql         = Objeto global de interaccion con la BD    = Objeto global de interaccion con la BD
     * @return lógico       = Indica si el procedimiento se pudo realizar correctamente o no
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
        
        $arreglo1   = array('articulos_factura_compra',             'id_articulo = "'.$this->id.'"', $textos->id('FACTURAS_COMPRA'));//arreglo del que sale la info a consultar
        $arreglo2   = array('articulos_factura_venta',              'id_articulo = "'.$this->id.'"', $textos->id('FACTURAS_VENTA'));
        $arreglo3   = array('articulos_factura_temporal_compra',    'id_articulo = "'.$this->id.'"', $textos->id('FACTURAS_TEMPORALES_COMPRA'));
        $arreglo4   = array('articulos_factura_temporal_venta',     'id_articulo = "'.$this->id.'"', $textos->id('FACTURAS_TEMPORALES_VENTA'));
        $arreglo5   = array('articulos_cotizacion',                 'id_articulo = "'.$this->id.'"', $textos->id('COTIZACIONES'));
        $arreglo6   = array('articulos_orden_compra',               'id_articulo = "'.$this->id.'"', $textos->id('ORDENES_COMPRA'));
        $arreglo7   = array('articulos_modificados_ncp',            'id_articulo = "'.$this->id.'"', $textos->id('ARTICULO_NOTA_CREDITO_P'));
        $arreglo8   = array('articulos_modificados_ndp',            'id_articulo = "'.$this->id.'"', $textos->id('ARTICULO_NOTA_DEBITO_P'));
        $arreglo9   = array('articulos_modificados_ncc',            'id_articulo = "'.$this->id.'"', $textos->id('ARTICULO_NOTA_CREDITO_C'));
        $arreglo10  = array('articulos_modificados_ndc',            'id_articulo = "'.$this->id.'"', $textos->id('ARTICULO_NOTA_DEBITO_C')); 
        $arreglo11  = array('inventarios',                          'id_articulo = "'.$this->id.'"', $textos->id('ARTICULO_INVENTARIO'));
        $arreglo12  = array('movimientos_mercancia',                'id_articulo = "'.$this->id.'"', $textos->id('MOVIMIENTOS_MERCANCIA_ARTICULO'));
        
        $arregloIntegridad  = array($arreglo1, $arreglo2, $arreglo3, $arreglo4, $arreglo5, $arreglo6, $arreglo7,
                                    $arreglo8, $arreglo9, $arreglo10, $arreglo11, $arreglo12);//arreglo de arreglos para realizar las consultas de integridad referencial, (ver documentacion de metodo)
        
        $integridad = Recursos::verificarIntegridad($textos->id('ARTICULO'), $arregloIntegridad);  
        
        /**
         * si hay problemas con la integridad referencial, la variable integridad tiene como valor,
         * un texto diciendo que tabla contiene n cantidad de relaciones con esta
         */
        if ($integridad != "") {
            $respuestaEliminar['mensaje'] = $integridad;
            return $respuestaEliminar;
        }        

        $sql->iniciarTransaccion();    
        $consulta = $sql->eliminar('articulos', 'id = "' . $this->id . '" ');
        
        if ($consulta) {
            if ($this->imagen != '00/00000001.png') {
                Archivo::eliminarArchivoDelServidor(array($this->imagenPrincipal, $this->imagenMiniatura));
 
            }
            
            if ($this->imagen2 != '00/00000001.png') {
                Archivo::eliminarArchivoDelServidor(array($this->imagenPrincipal2, $this->imagenMiniatura2));
            }
            
            $aplicacionMoto = new ArticuloMotos();
            $query = $aplicacionMoto->eliminarMotoAplicacion($this->id);
            
            if(!$query){
                $sql->cancelarTransaccion("Fallo en el archivo " . __FILE__ . " en la linea " .  __LINE__);
                return $respuestaEliminar;
            }
            
            $sql->finalizarTransaccion();
            //todo salió bien, se envia la respuesta positiva
            $respuestaEliminar['respuesta'] = true;
            return $respuestaEliminar;
            
        } else {
           $sql->cancelarTransaccion("Fallo en el archivo " . __FILE__ . " en la linea " .  __LINE__);
           return $respuestaEliminar;
            
        }
        
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
            $condicion .= 'a.id NOT IN (' . $excepcion . ') AND ';
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
            'su'    => 'subgrupos',
            'l'     => 'lineas',
            'p'     => 'paises'
        );

        $columnas = array(
            'id'                => 'a.id',
            'nombre'            => 'a.nombre',
            'idLinea'           => 'a.id_linea',
            'linea'             => 'l.nombre',
            'idSubgrupo'        => 'a.id_subgrupo',
            'subgrupo'          => 'su.nombre',
            'referencia'        => 'a.referencia',
            'plu_interno'       => 'a.plu_interno',
            'nombre'            => 'a.nombre',
            'idPais'            => 'a.id_pais',
            'codigo'            => 'p.codigo_iso',
            'codigoPais'        => 'p.codigo_comercial',
            'pais'              => 'p.nombre',
            'precio1'           => 'a.precio1',
            'iva'               => 'a.iva',
            'ultimoPrecioCompra'   => 'a.ultimo_precio_compra',
            'completo'          => 'IF((a.nombre!="" AND a.id_subgrupo!=0 AND a.referencia!="" AND a.id_unidad!=0 AND a.id_pais!=0 AND a.concepto1!="" AND a.precio1!=""), "1", "0")'
        );

        if (!empty($condicionGlobal)) {
            $condicion .= $condicionGlobal . ' AND ';
        }

        $condicion .= 'a.id_subgrupo = su.id AND a.id_pais = p.id AND a.id_linea = l.id ';

        if (is_null($this->registrosConsulta)) {//Este dato se necesita para la info de la paginación
            $sql->seleccionar($tablas, $columnas, $condicion);
            $this->registrosConsulta = $sql->filasDevueltas;
        }

        //$sql->depurar = true;
        $consulta = $sql->seleccionar($tablas, $columnas, $condicion, 'a.id', $orden, $inicio, $cantidad);
        
        //$idPrincipalArticulo = (string)$sesion_configuracionGlobal->idPrincipalArticulo;
        
        if ($sql->filasDevueltas) {
            $lista = array();

            while ($objeto = $sql->filaEnObjeto($consulta)) {
                //$objeto->$idPrincipalArticulo = Recursos::completarCeros($objeto->$idPrincipalArticulo, 6);
                //$objeto->estado = ($objeto->activo) ? HTML::frase($textos->id('ACTIVO'), 'activo') : HTML::frase($textos->id('INACTIVO'), 'inactivo');
                if($objeto->referencia){
                    $objeto->nombre = $objeto->nombre.' :: ('.$objeto->referencia.')';
                } 
                
                if($objeto->ultimoPrecioCompra){
                    $objeto->ultimoPrecioCompra = '$ '.$objeto->ultimoPrecioCompra;
                }                  
                
                $objeto->precio1    = '$ '.$objeto->precio1;
                $indicador_completo = ($objeto->completo == '1') ? 'activo' : 'inactivo';
                $objeto->completo = HTML::frase($textos->id('COMPLETO_' . $objeto->completo), $indicador_completo);
                
                $objeto->campoCantidad = HTML::campoTexto("campo-cantidad-articulo", 5, 20, "1", "campo-cantidad-articulo", "campoCantidadArticulo");

                $objeto->codigoPais = $objeto->codigoPais . HTML::imagen($configuracion['SERVIDOR']['media'] . $configuracion['RUTAS']['iconosBanderas'] . '/' . strtolower($objeto->codigo) . '.png', 'miniaturaBanderas');

                $lista[] = $objeto;
            }
        }

        return $lista;
    }

    /**
     * Metodo que genera los datos que contendra la tabla principal del modulo
     * 
     * @global objeto $textos = objeto global de traduccion de textos
     * @param array $arregloRegistros matriz con la info a ser mostrada en la tabla
     * @param array $datosPaginacion arreglo con la información para la paginacion
     * @return string cadena HTML con la tabla (<table>) generada 
     */
    public function generarTabla($arregloRegistros, $datosPaginacion = NULL, $tablaModal = false, $mostrarBotonDerecho = true) {
        global $textos, $sesion_configuracionGlobal, $sesion_usuarioSesion;
        
        $idPrincipalArticulo    = (string)$sesion_configuracionGlobal->idPrincipalArticulo;
        $arrayIdArticulo        = array('id' => $textos->id('ID_AUTOMATICO'), 'codigo_oem' => $textos->id('CODIGO_OEM'), 'plu_interno' => $textos->id('PLU'));
        
        $datosTabla = array(
            HTML::parrafo($arrayIdArticulo[$idPrincipalArticulo], 'centrado')   => ''.$idPrincipalArticulo.'|a.'.$idPrincipalArticulo.'', //concateno el nombre del alias para usarlo al armar la tabla con el fila en objeto
            HTML::parrafo($textos->id('NOMBRE'), 'centrado')                    => 'nombre|a.nombre',
            HTML::parrafo($textos->id('LINEA'), '')                             => 'linea|l.nombre', //la busqueda, al armar la tabla dividira la cadena y usara el que necesite
            HTML::parrafo($textos->id('SUBGRUPO'), '')                          => 'subgrupo|su.nombre',
            HTML::parrafo($textos->id('PAIS'), '')                              => 'codigoPais|p.nombre',
            HTML::parrafo($textos->id('PRECIO_VENTA'), '')                      => 'precio1|a.precio1',            
            HTML::parrafo($textos->id('ULTIMO_PRECIO_COMPRA'), '')              => 'ultimoPrecioCompra|a.ultimo_precio_compra',
            HTML::parrafo($textos->id('COMPLETO'), '')                          => 'completo',
        );        

        //ruta del metodo paginador
        $rutaPaginador = '/ajax' . $this->urlBase . '/move';

        $moverMercancia = '';

        $puedeMoverMercancia = Perfil::verificarPermisosBoton('botonMoverMercanciaBodega');
        if ($puedeMoverMercancia || $sesion_usuarioSesion->id == 0) {
            $moverMercancia1    = HTML::formaAjax($textos->id('MOVER_MERCANCIA'), 'contenedorMenuMoverMercanciaBodega', 'moverMercanciaBodega', '', '/ajax/articulos/moverMercancia', array('id' => ''));
            $moverMercancia     = HTML::contenedor($moverMercancia1, '', 'botonMoverMercanciaBodega');
            
        }

        $botonesExtras      = array($moverMercancia);
        $estilosColumnas    = array('columna1 id', 'columna2 descripcion-articulo', 'columna3 texto-alineado-izquierda', 'columna4 texto-alineado-izquierda', 'columna5 pais', 'columna6', 'columna7 completo', 'columna8 iva', ' columna9');   
        
        //si el regimen es diferente al simplificado muestro el iva en los articulos
        if ($sesion_configuracionGlobal->empresa->regimen != "1"){
            $datosTabla[HTML::parrafo($textos->id('IVA'), '')] = 'iva|a.iva';             

        } else {//si es simplificado remuevo el estilo del iva del arreglo de estilos columnas
            $estilosColumnas = array_diff($estilosColumnas, array('columna8 iva'));
            
        }
        
        $botonDerecho = '';
        
        if ($mostrarBotonDerecho) {
            $botonDerecho       = HTML::crearMenuBotonDerecho('ARTICULOS', $botonesExtras);
        }

        return Recursos::generarTablaRegistros($arregloRegistros, $datosTabla, $rutaPaginador, $datosPaginacion, $estilosColumnas, $tablaModal) . $botonDerecho;
        
    }
    
    /**
     * Metodo que genera los datos que contendra la tabla principal del modulo
     * 
     * @global objeto $textos = objeto global de traduccion de textos
     * @param array $arregloRegistros matriz con la info a ser mostrada en la tabla
     * @param array $datosPaginacion arreglo con la información para la paginacion
     * @return string cadena HTML con la tabla (<table>) generada 
     */
    public function generarTablaModal($arregloRegistros, $datosPaginacion = NULL, $tablaModal = false) {
        global $textos, $sesion_configuracionGlobal;
        
        $idPrincipalArticulo    = (string)$sesion_configuracionGlobal->idPrincipalArticulo;
        $arrayIdArticulo        = array('id' => $textos->id('ID_AUTOMATICO'), 'codigo_oem' => $textos->id('CODIGO_OEM'), 'plu_interno' => $textos->id('PLU'));
        
        $datosTabla = array(
            HTML::parrafo($arrayIdArticulo[$idPrincipalArticulo], 'centrado')   => ''.$idPrincipalArticulo.'|a.'.$idPrincipalArticulo.'', //concateno el nombre del alias para usarlo al armar la tabla con el fila en objeto
            HTML::parrafo($textos->id('NOMBRE'), 'centrado')                    => 'nombre|a.nombre',
            HTML::parrafo($textos->id('LINEA'), '')                             => 'linea|l.nombre', //la busqueda, al armar la tabla dividira la cadena y usara el que necesite
            HTML::parrafo($textos->id('PRECIO_VENTA'), '')                      => 'precio1|a.precio1',            
            HTML::parrafo($textos->id('ULTIMO_PRECIO_COMPRA'), '')              => 'ultimoPrecioCompra|a.ultimo_precio_compra',
            HTML::parrafo($textos->id('CANTIDAD'), '')                          => 'campoCantidad',
        );        
        
        //si el regimen es diferente al simplificado muestro el iva en los articulos
        if ($sesion_configuracionGlobal->empresa->regimen != "1"){
            $datosTabla[HTML::parrafo($textos->id('IVA'), '')] = 'iva|a.iva';             

        } else {//si es simplificado remuevo el estilo del iva del arreglo de estilos columnas
            $estilosColumnas = array_diff($estilosColumnas, array('columna7 iva'));
            
        }        

        //ruta del metodo paginador
        $rutaPaginador = '/ajax' . $this->urlBase . '/moveModal';

        $estilosColumnas    = array('columna1 id', 'columna2 descripcion-articulo', 'columna3 texto-alineado-izquierda', 'columna4 texto-alineado-izquierda', 'columna5 ', 'columna6');   

        return Recursos::generarTablaRegistros($arregloRegistros, $datosTabla, $rutaPaginador, $datosPaginacion, $estilosColumnas, $tablaModal);
        
    }    
    
    

    /**
     * Metodo que genera los datos que contendra la tabla principal del modulo
     * 
     * @global objeto $textos = objeto global de traduccion de textos = objeto global de traduccion de textos
     * @param type $arregloRegistros
     * @param type $datosPaginacion
     * @return type 
     */
    public function generarTablaReducida($arregloRegistros, $datosPaginacion = NULL) {
        global $textos, $sesion_configuracionGlobal;
        
        $idPrincipalArticulo    = (string)$sesion_configuracionGlobal->idPrincipalArticulo;
        $arrayIdArticulo        = array('id' => $textos->id('ID_AUTOMATICO'), 'codigo_oem' => $textos->id('CODIGO_OEM'), 'plu_interno' => $textos->id('PLU'));
        
        $datosTabla = array(
            HTML::parrafo($arrayIdArticulo[$idPrincipalArticulo], 'centrado')   => ''.$idPrincipalArticulo.'|a.'.$idPrincipalArticulo.'', //concateno el nombre del alias para usarlo al armar la tabla con el fila en objeto
            HTML::parrafo($textos->id('NOMBRE'), 'centrado')                    => 'nombre|a.nombre',
            HTML::parrafo($textos->id('LINEA'), 'centrado')                     => 'linea|l.nombre', //la busqueda, al armar la tabla dividira la cadena y usara el que necesite
            HTML::parrafo($textos->id('SUBGRUPO'), 'centrado')                  => 'subgrupo|su.nombre',
            HTML::parrafo($textos->id('NACIONALIDAD'), 'centrado')              => 'codigoPais|p.nombre'
        );
    
        $rutaPaginador = '/ajax' . $this->urlBase . '/moveNew';

        return Recursos::generarTablaRegistros($arregloRegistros, $datosTabla, $rutaPaginador, $datosPaginacion);
        
    }
    
    /**
     * Metodo llamado por la clase factura de compra. Cada vez que se realiza una compra, por cada uno de los articulos de la compra, se deberá modificar
     * su información referente a precio de compra y a precio de venta. Estos son datos para tener en cuenta en la inteligencia del negocio.
     * 
     * @global recurso $sql         = Objeto global de interaccion con la BD         
     * @param int $id               = id del articulo a ser modificado
     * @param string $precioCompra  = dato del precio de compra
     * @param int $precioVenta      = dato que determina el nuevo precio de venta
     * @param type $porMayor        = determina si una venta se esta realiando al por mayor, o es una venta de mostrador
     * @return boolean              = determina si la operación se realizó con éxito
     */
    public function modificarInfoCompras($id, $precioCompra, $precioVenta, $porMayor = NULL){
        global $sql;
        
        $ultimoPrecioCompra = $sql->obtenerValor("articulos", "ultimo_precio_compra", "id = '".$id."'");
        
        if ($precioVenta <= 0) {//si no viene precio de venta no se modifica nada
            return true;
            
        } else if ($ultimoPrecioCompra > $precioCompra) {//si el precio de compra anterior es mayor al actual no se modifica
            $precioCompra = $ultimoPrecioCompra;
            
        }
        
        $datos = array(
            'ultimo_precio_compra' => $precioCompra            
        );
        
        ($porMayor) ? $datos['precio2'] = $precioVenta : $datos['precio1'] = $precioVenta;
        
        $sql->iniciarTransaccion();
        
        $modificar = $sql->modificar('articulos', $datos, 'id = "'.$id.'"');
        
        if (!$modificar) {
            $sql->cancelarTransaccion();
            
        }
        
        $sql->finalizarTransaccion();
        
        return true;       
        
    }
    
    /**
     * Función consultar kardex. Es llamada desde el metodo consultar articulo,
     * se puede ver al hacer click en la pestaña "Kardex" existente en la ventana
     * modal de consultar Articulo. Muestra el kardex de un articulo en un periodo
     * de tiempo determinado por las dos fechas que se reciben como parametro, tambien se puede
     * consultar el kardex de un articulo por bodega.
     *
     * @global recurso $sql         = Objeto global de interaccion con la BD
     * @param string $fechaInicio   = fecha de inicio para retringir la consulta del kardex
     * @param string $fechaFin      = fecha de fin para retringir la consulta del kardex
     */
    public function consultarKardex($fechaInicio, $fechaFin, $idBodega){
        global $sql;
        
        $tablasAFC = array(
            "afc"   => "articulos_factura_compra",
            "p"     => "proveedores",
            "b"     => "bodegas",
            "s"     => "sedes_empresa"
        );
        
        $columnasAFC = array(
            "id"        => "afc.id",
            "fecha"     => "afc.fecha",
            "cantidad"  => "afc.cantidad",
            "bodega"    => "b.nombre",
            "sede"      => "s.nombre",
            "precioC"   => "afc.precio",
            "proveedor" => "p.nombre"

        );
        
        $condicionBodegaC = '';
        $condicionBodegaV = '';
        
        if(!empty($idBodega) && $idBodega != "0"){
            $condicionBodegaC = " AND afc.id_bodega = '".$idBodega."'";
            $condicionBodegaV = " AND afv.id_bodega = '".$idBodega."'";
        }
        
        $condicionAFC = "afc.id_proveedor = p.id AND afc.id_bodega = b.id ".$condicionBodegaC." AND b.id_sede = s.id AND afc.id_articulo = '".$this->id."' AND afc.fecha BETWEEN '".$fechaInicio."' AND '".$fechaFin."' ";
        
        $sql->depurar = true;
        $consultaAFC = $sql->seleccionar($tablasAFC, $columnasAFC, $condicionAFC);
        
        $arrayAFC       = array();
        $totalComprados = 0;
        $totalCompra    = 0;        
        
        if ($sql->filasDevueltas) {           
            while ($objeto = $sql->filaEnObjeto($consultaAFC)) {
                $objeto->venta      = "";
                $objeto->cliente    = "";
                $totalComprados    += $objeto->cantidad;
                $totalCompra       += $objeto->precioC;
                
                $arrayAFC[]         = $objeto;
            }
            
        }        
        
        $firstValAfc = new stdClass();
        $firstValAfc->id            = '1';
        $firstValAfc->fecha         = "";
        $firstValAfc->cantidad      = HTML::frase('comprados: '.$totalComprados, 'subtitulo negrilla');        
        $firstValAfc->bodega        = "";   
        $firstValAfc->sede          = "";
        $firstValAfc->cliente       = HTML::frase('Total:', 'subtitulo negrilla'); 
        $firstValAfc->precioC       = "";  
        $firstValAfc->precioV       = HTML::frase('$'.Recursos::formatearNumero($totalCompra, '$'), 'subtitulo negrilla');
        
        
        array_push($arrayAFC, $firstValAfc);
        
//        print_r($arrayAFC);
        
        $tablasAFV = array(
            "afv"   => "articulos_factura_venta",
            "c"     => "clientes",
            "b"     => "bodegas",
            "s"     => "sedes_empresa"
        );
        
        $columnasAFV = array(
            "id"        => "afv.id",
            "fecha"     => "afv.fecha",
            "cantidad"  => "afv.cantidad",
            "bodega"    => "b.nombre",
            "sede"      => "s.nombre",
            "precioV"   => "afv.precio",
            "cliente"   => "c.nombre"

        );     
        
        $condicionAFV = "afv.id_cliente = c.id AND afv.id_bodega = b.id ".$condicionBodegaV." AND b.id_sede = s.id AND afv.id_articulo = '".$this->id."' AND afv.fecha BETWEEN '".$fechaInicio."' AND '".$fechaFin."' ";
        
        //$sql->depurar = true;
        $consultaAFV = $sql->seleccionar($tablasAFV, $columnasAFV, $condicionAFV);   
        
        $arrayAFV       = array();
        $totalVendidos  = 0;
        $totalVenta     = 0;        
        
        if ($sql->filasDevueltas) {            
            while ($objeto = $sql->filaEnObjeto($consultaAFV)) {
                $objeto->compra     = "";
                $objeto->proveedor  = "";
                $totalVendidos     += $objeto->cantidad;
                $totalVenta        += $objeto->precioV;
                
                $arrayAFV[]         = $objeto;
            }
            
        }
        
        $firstValAfv                = new stdClass();
        $firstValAfv->id            = '1';
        $firstValAfv->fecha         = "";
        $firstValAfv->cantidad      = HTML::frase('vendidos: '.$totalVendidos, 'subtitulo negrilla');        
        $firstValAfv->bodega        = "";   
        $firstValAfv->sede          = "";
        $firstValAfv->cliente       = HTML::frase('Total:', 'subtitulo negrilla');
        $firstValAfv->precioC        = "";   
        $firstValAfv->precioV       = HTML::frase('$'.Recursos::formatearNumero($totalVenta, '$'), 'subtitulo negrilla');
        
        
        array_push($arrayAFV, $firstValAfv);        
        
        $consulta = array_merge($arrayAFV, $arrayAFC);   

        return $consulta;        
        
    }
    
    /**
     * Función encargada de generar la información para alimentar un gráfico de barras
     */
    public function datosGraficoBarras(){
        global $sql;
        
        $respuesta = array();
        
        $respuesta['labels'] = array(
                        date('m-y', strtotime('-6 month')) ,
                        date('m-y', strtotime('-5 month')) ,
                        date('m-y', strtotime('-4 month')) ,
                        date('m-y', strtotime('-3 month')) ,
                        date('m-y', strtotime('-2 month')) ,
                        date('m-y', strtotime('-1 month')) ,            
                        );
        
        $datos = array(
                        date('Y-m-d', strtotime('-6 month')) ,
                        date('Y-m-d', strtotime('-5 month')) ,
                        date('Y-m-d', strtotime('-4 month')) ,
                        date('Y-m-d', strtotime('-3 month')) ,
                        date('Y-m-d', strtotime('-2 month')) ,
                        date('Y-m-d', strtotime('-1 month')) ,            
                        );        

        $arrayAfc      = array();//aqui se guardaran las cantidas compradas
        
        $arrayAfv      = array(); //aqui se guardaran las cantidas vendidas

        foreach($datos as $fecha){
            
            $fechaInicio    = $fecha;
            $fechaFin       = date("Y-m-d", strtotime('+1 month', strtotime($fechaInicio)));
            
            $tablasAfc = array(
                "afc"   => "articulos_factura_compra"
            );

            $columnasAfc = array(
                "cantidad"  => "SUM(afc.cantidad)"

            );     

            $condicionAfc = "afc.id_articulo = '".$this->id."' AND afc.fecha BETWEEN '".$fechaInicio."' AND '".$fechaFin."' ";     
            
            //$sql->depurar = true;
            $consultaAfc = $sql->seleccionar($tablasAfc, $columnasAfc, $condicionAfc);        
            
            if ($sql->filasDevueltas) {            
                $objeto = $sql->filaEnObjeto($consultaAfc);    

                $arrayAfc[] = ($objeto->cantidad != '') ? $objeto->cantidad : '0';
                
            } 
            
            //consulta articulos vendidos
            
            $tablasAfv = array(
                "afv"   => "articulos_factura_venta"
            );

            $columnasAfv = array(
                "cantidad"  => "SUM(afv.cantidad)"

            );     

            $condicionAfv = "afv.id_articulo = '".$this->id."' AND afv.fecha BETWEEN '".$fechaInicio."' AND '".$fechaFin."' ";     
            
            //$sql->depurar = true;
            $consultaAfv = $sql->seleccionar($tablasAfv, $columnasAfv, $condicionAfv);        
            
            if ($sql->filasDevueltas) {            
                $objeto = $sql->filaEnObjeto($consultaAfv);    

                $arrayAfv[] = ($objeto->cantidad != '') ? $objeto->cantidad : '0';
                
            }              

            
        }       
        
        $respuesta['datos'] = array(
                            array(
                                    'fillColor' => "#0080FF",
                                    'strokeColor' => "rgba(220,220,220,1)",
                                    'data' => $arrayAfc           
                            ),
                            array(
                                    'fillColor' => "#A4A4A4",
                                    'strokeColor' => "rgba(151,187,205,1)",
                                    'data' => $arrayAfv           
                            )
                        );
        
        
        return $respuesta;
        
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
                $configuracionRuta = $configuracion['RUTAS']['media'] . "/" . $configuracion["RUTAS"]["documentos"];
                $recurso = Archivo::subirArchivoAlServidor($archivo_masivo, $configuracionRuta);

                require_once $configuracion['RUTAS']['clases'] . '/excel_reader2.php';
                $data = new Spreadsheet_Excel_Reader($configuracionRuta . '/' . $recurso);

                $row = 1;
                $col = 1;

                if ($datos['inicial'] == 1) {
                    $row++;
                    $campos = array();
                    
                    if ($datos['plu_interno'] != 0)
                        $campos['plu_interno'] = $datos['plu_interno'];
                    
                    if ($datos['nombre'] != 0)
                        $campos['nombre'] = $datos['nombre'];
                    
                    if ($datos['codigo_oem'] != 0)
                        $campos['referencia'] = $datos['codigo_oem'];
                    
                    if ($datos['id_linea'] != 0)
                        $campos['id_linea'] = $datos['id_linea'];   
                    
                    if ($datos['id_subgrupo'] != 0)
                        $campos['id_subgrupo'] = $datos['id_subgrupo'];
                    
                    if ($datos['id_pais'] != 0)
                        $campos['id_pais'] = $datos['id_pais'];
                    
                    if ($datos['id_unidad'] != 0)
                        $campos['id_unidad'] = $datos['id_unidad'];
                    
                    if ($datos['precio1'] != 0)
                        $campos['precio1'] = $datos['precio1'];
                }

                $valor1    = $data->val($row, $col);
                $respuesta = array();

                while ($valor1 != null) {
                    if ($datos['inicial'] == 0) {
                        $respuesta[$col] = $valor1;
                        $col++;
                        
                    } else {

                        $datosArticulo = array(
                            'activo'            => '1',
                            'fecha_registro'    => date('Y-m-d')
                        );

                        foreach ($campos AS $nombre => $valor) {
                            $valor = $data->val($row, $valor);
                            
                            if ($nombre == 'id_subgrupo') {
                                //$valor = $valor; //$sql->obtenerValor('subgrupos', 'id', "nombre LIKE '%" . $valor . "%'");
                                
                            } elseif ($nombre == 'id_pais') {
                                $valor = $sql->obtenerValor('paises', 'id', "codigo_comercial LIKE '%" . $valor . "%'");
                                
                            } elseif ($nombre == 'nombre') {
                                $valor = str_replace('¥', 'Ñ', $valor);
                                //$valor = $valor;$sql->obtenerValor('lineas', 'id', "nombre LIKE '%" . $valor . "%'");
                                
                            } elseif ($nombre == 'id_unidad') {
                                $valor = '01';//$sql->obtenerValor('unidades', 'id', "codigo LIKE '%" . $valor . "%'");
                                
                            } elseif ($nombre == 'precio1') {
                                $datosArticulo['concepto1'] = 'Concepto base';
                                $precio1                    = str_replace(',', '', $valor);
                                $precio1                    = substr($valor, 0, -3);
                                $datosArticulo['precio1']   = $precio1;
                            }

                            $datosArticulo[$nombre] = $valor;
                        }

                        $sql->insertar('articulos', $datosArticulo);

                        $row++;
                        $respuesta = true;
                    }
                    
                    $valor1 = $data->val($row, $col);
                }
                
                Archivo::eliminarArchivoDelServidor(array($configuracionRuta . '/' . $recurso));
                
                return $respuesta;
                
            } else {
                return false;
                
            }
            
        }
        
    }
    
    
}
