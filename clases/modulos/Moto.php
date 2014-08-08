<?php

/**
 * @package     FOM
 * @subpackage  Motos 
 * @author      Pablo Andrés Vélez Vidal 
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2012 Genesys Soft.
 * @version     0.2
 * 
 * Clase encargada de gestionar la información del listado de motos existentes en el sistema.
 * Modulo utilizado en los modulos de compra, de venta y de articulos. En los modulos de compra y de venta
 * su función es cargar el catalogo perteneciente a cada moto. En el modulo de articulos es almacenar la 
 * relación articulo-motos. Pues un articulo sirve principalmente a una moto, pero a su vez puede servir a varias motos.
 * Este modulo tambien se relaciona con el modulo de "Marcas" ya que una moto pertenece a una marca.
 * */
class Moto {

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
     * Código interno o identificador de la marca
     * @var entero
     */
    public $idMarca;

    /**
     * Nombre de la marca
     * @var entero
     */
    public $marca;

    /**
     * Tabla principal a la que va relacionada el modulo
     * @var entero
     */
    public $tabla;

    /**
     * Nombre del item
     * @var entero
     */
    public $nombre;

    /**
     * Identificador de la imagen
     * @var entero
     */
    public $idImagen;

    /**
     * Ruta de la imagen de la marca
     * @var cadena
     */
    public $imagenMarca;

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
     * Número de registros de la lista
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
     * Inicializar una moto
     *
     * @param entero $id Código interno o identificador del moto en la base de datos
     *
     */
    public function __construct($id = NULL) {
        global $sql, $modulo;
        $this->urlBase              = '/' . $modulo->url;
        $this->url                  = $modulo->url;
        $this->idModulo             = $modulo->id;
        $this->tabla                = $modulo->tabla;
        //Saber el numero de registros
        $this->registros            = $sql->obtenerValor('motos', 'COUNT(id)', 'id != "0" AND id != "999"');
        //Saber el numero de registros activos
        $this->registrosActivos     = $sql->obtenerValor('motos', 'COUNT(id)', 'activo = "1"');
        //establecer el valor del campo predeterminado para organizar los listados
        $this->ordenInicial         = 'nombre';

        if (!empty($id)) {
            $this->cargar($id);
        }
    }

    /**
     *
     * Cargar los datos de un item
     *
     * @param entero $id Código interno o identificador del item en la base de datos
     *
     */
    public function cargar($id) {
        global $sql, $configuracion, $textos;

        if (!empty($id) && $sql->existeItem('motos', 'id', intval($id))) {

            $tablas = array(
                'm'     => 'motos',
                'ma'     => 'marcas',
                'i1'    => 'imagenes',
                'i'     => 'imagenes'
            );

            $columnas = array(
                'id'                => 'm.id',
                'nombre'            => 'm.nombre',
                'idMarca'           => 'm.id_marca',
                'marca'             => 'ma.nombre',
                'idImagen'          => 'm.id_imagen',
                'imagen'            => 'i.ruta',
                'imagenMarca'       => 'i1.ruta',
                'archivo'           => 'm.archivo',
                'activo'            => 'm.activo'
            );

            $condicion = 'm.id_marca = ma.id AND m.id_imagen = i.id AND ma.id_imagen = i1.id AND m.id = "' . $id . '"';

            $consulta = $sql->seleccionar($tablas, $columnas, $condicion);

            if ($sql->filasDevueltas) {
                $fila = $sql->filaEnObjeto($consulta);

                foreach ($fila as $propiedad => $valor) {
                    $this->$propiedad = $valor;
                }

                $this->url              = $this->urlBase . '/' . $this->id;
                $this->imagenPrincipal  = $configuracion['SERVIDOR']['media'] . $configuracion['RUTAS']['imagenesDinamicas'] . '/' . $this->imagen;
                $this->imagenMiniatura  = $configuracion['SERVIDOR']['media'] . $configuracion['RUTAS']['imagenesMiniaturas'] . '/' . $this->imagen;
                $this->imagenMarca      = $configuracion['SERVIDOR']['media'] . $configuracion['RUTAS']['imagenesMiniaturas'] . '/' . $this->imagenMarca;
                $this->rutaArchivo      = $configuracion['SERVIDOR']['media'] . $configuracion['RUTAS']['archivosCatalogos'] . $this->archivo;
                
                if($this->archivo == 'empty'){
                    $this->enlaceArchivo    = HTML::parrafo($textos->id("SIN_CATALOGO"), '', '');
                    
                } else {
                    $this->enlaceArchivo    = HTML::enlace($this->nombre, $this->rutaArchivo, 'estiloEnlace', '',  array('target' => '_blank'));
                    
                }
                                
            }
            
        }
        
    }

//Fin del metodo Cargar

    /**
     *
     * Adicionar una moto
     *
     * @param  arreglo $datos       Datos de la moto a adicionar
     * @return entero               Código interno o identificador del moto en la base de datos (NULL si hubo error)
     *
     */
    public function adicionar($datos) {
        global $sql, $archivo_imagen, $archivo_archivo, $configuracion;

        $datosItem = array();

        $idImagen = '0';

        $sql->iniciarTransaccion();
        
        if (isset($archivo_imagen) && !empty($archivo_imagen['tmp_name'])) {
            $imagen         = new Imagen();
            
            $datosImagen = array(
                'modulo'            => 'MOTOS',
                'idRegistro'        => '',
                'titulo'            => 'imagen_moto',
                'descripcion'       => 'imagen_moto'
            );

            $idImagen = $imagen->adicionar($datosImagen);
            
            if($idImagen === false){
                $sql->cancelarTransaccion();
                return false;
            } 
        }
        
        $recurso = 'empty';
        
        if (isset($archivo_archivo) && !empty($archivo_archivo['tmp_name'])) {
            $configuracionRuta  = $configuracion["RUTAS"]["media"] . "/" . $configuracion["RUTAS"]["archivosCatalogos"] ;
            $recurso            = Archivo::subirArchivoAlServidor($archivo_archivo, $configuracionRuta); 
        }
        
        if($recurso === false){
            $sql->cancelarTransaccion();
            return false;
        }         

        $datosItem['id_marca']      = $datos['id_marca'];
        $datosItem['nombre']        = $datos['nombre'];
        $datosItem['id_imagen']     = $idImagen;
        $datosItem['archivo']       = $recurso;

        $datosItem['activo'] =  (isset($datos['activo'])) ? '1' : '0';
        
        $consulta = $sql->insertar('motos', $datosItem);

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
     * Modificar una moto
     *
     * @param  arreglo $datos       Datos del moto a modificar
     * @return lógico               Indica si el procedimiento se pudo realizar correctamente o no
     *
     */
    public function modificar($datos) {
        global $sql, $archivo_imagen, $archivo_archivo, $configuracion;

        if (!isset($this->id)) {
            return NULL;
        }

        $datosItem = array();

        $idImagen = $this->idImagen;
        
        $sql->iniciarTransaccion();
        
        if (isset($archivo_imagen) && !empty($archivo_imagen['tmp_name'])) {

            $imagen         = new Imagen($this->idImagen);            
            $eliminarImagen = $imagen->eliminar();
            
            if($eliminarImagen === false){
                $sql->cancelarTransaccion();
                return false;
            }              
            
            $datosImagen = array(
                'modulo'        => 'MOTOS',
                'idRegistro'    => $this->id,
                'titulo'        => 'imagen_moto',
                'descripcion'   => 'imagen_moto'
            );

            $idImagen = $imagen->adicionar($datosImagen);
            
            if($idImagen === false){
                $sql->cancelarTransaccion();
                return false;
            } 
        }
        
        $recurso = $this->archivo;

        if (isset($archivo_archivo) && !empty($archivo_archivo['tmp_name'])) {
            
            if($this->archivo != 'empty'){
                
                $eliminarArchivo = Archivo::eliminarArchivoDelServidor(array($configuracion['RUTAS']['media'] .'/'. $configuracion['RUTAS']['archivosCatalogos'] . $this->archivo));      
                
//                if($eliminarArchivo === false){
//                    $sql->cancelarTransaccion();
//                    return false;
//                }  
                
            }
           
            
            $configuracionRuta  = $configuracion["RUTAS"]["media"] . "/" . $configuracion["RUTAS"]["archivosCatalogos"];
            $recurso            = Archivo::subirArchivoAlServidor($archivo_archivo, $configuracionRuta);
            
            if($recurso === false){
                $sql->cancelarTransaccion();
                return false;
            }             
            
        }   
                

        $datosItem['id_marca']      = $datos['id_marca'];
        $datosItem['nombre']        = $datos['nombre'];
        $datosItem['id_imagen']     = $idImagen;
        $datosItem['archivo']       = $recurso;

        $datosItem['activo'] =  (isset($datos['activo'])) ? '1' : '0';
        $consulta = $sql->modificar('motos', $datosItem, 'id = "' . $this->id . '"');


        if ($consulta) {
            $sql->finalizarTransaccion();
            return true;
            
        } else {
            $sql->cancelarTransaccion();
            return false;
            
        }
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
        global $sql, $configuracion, $textos;

        if (!isset($this->id)) {
            return false;
        }
        
        //arreglo que será devuelto como respuesta
        $respuestaEliminar = array(
            'respuesta' => false,
            'mensaje'   => $textos->id('ERROR_DESCONOCIDO'),
        );
        
        //hago la validacion de la integridad referencial
        $arreglo1 = array('articulos',    'id_moto = "'.$this->id.'"', $textos->id('ARTICULOS'));//arreglo del que sale la info a consultar
        $arreglo2 = array('articulo_moto',     'id_moto = "'.$this->id.'"', $textos->id('ARTICULOS'));
        
        $arregloIntegridad = array($arreglo1, $arreglo2);//arreglo de arreglos para realizar las consultas de integridad referencial, (ver documentacion de metodo)
        $integridad = Recursos::verificarIntegridad($textos->id('MOTO'), $arregloIntegridad);
             
        /**
         * si hay problemas con la integridad referencial, la variable integridad tiene como valor,
         * un texto diciendo que tabla contiene n cantidad de relaciones con esta
         */
        if ($integridad != "") {
            $respuestaEliminar['mensaje'] = $integridad;
            return $respuestaEliminar;
        }
              
        $sql->iniciarTransaccion();
        $consulta = $sql->eliminar('motos', 'id = "' . $this->id . '"');
        
        if ($consulta) {
            $imagen         = new Imagen($this->idImagen); 
            $eliminarImagen = $imagen->eliminar;
            
            if($eliminarImagen === false){
                $sql->cancelarTransaccion("Fallo en el archivo " . __FILE__ . " en la linea " .  __LINE__);
                return $respuestaEliminar;
            }            
            
            if($this->archivo != 'empty'){
                $rutaArchivo = $configuracion['RUTAS']['media'] .'/'. $configuracion['RUTAS']['archivosCatalogos'] . $this->archivo;
                
                if (is_file($rutaArchivo)) {
                    $eliminarCatalogo = Archivo::eliminarArchivoDelServidor(array($rutaArchivo));

                    if($eliminarCatalogo === false){
                        $sql->cancelarTransaccion("Fallo en el archivo " . __FILE__ . " en la linea " .  __LINE__);
                        return $respuestaEliminar;
                    }
                }
                
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
            $condicion .= 'm.id NOT IN (' . $excepcion . ') AND ';
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
            'm' => 'motos',
            'ma' => 'marcas',
            'i' => 'imagenes'
        );

        $columnas = array(
            'id'            => 'm.id',
            'nombre'        => 'm.nombre',
            'idMarca'       => 'm.id_marca',
            'marca'         => 'ma.nombre',
            'idImagen'      => 'm.id_imagen',
            'imagen'        => 'i.ruta',
            'archivo'       => 'm.archivo',
            'activo'        => 'm.activo'
        );

        if (!empty($condicionGlobal)) {
            $condicion .= $condicionGlobal . ' AND ';
        }

        $condicion .= ' m.id_marca = ma.id AND m.id_imagen = i.id';

        if (is_null($this->registrosConsulta)) {
            $sql->seleccionar($tablas, $columnas, $condicion);
            $this->registrosConsulta = $sql->filasDevueltas;
        }
        //$sql->depurar = true;
        $consulta = $sql->seleccionar($tablas, $columnas, $condicion, 'm.id', $orden, $inicio, $cantidad);
        //echo $sql->sentenciaSql;
        if ($sql->filasDevueltas) {
            $lista = array();

            while ($objeto = $sql->filaEnObjeto($consulta)) {
                $objeto->url                = $this->urlBase . '/' . $objeto->id;     
                $objeto->estado             =  ($objeto->activo) ? HTML::frase($textos->id('ACTIVO'), 'activo') :  HTML::frase($textos->id('INACTIVO'), 'inactivo');

                if($objeto->archivo == 'empty'){
                    $objeto->enlaceArchivo    = HTML::parrafo($textos->id("SIN_CATALOGO"), '', '');
                } else {
                    $objeto->enlaceArchivo      = HTML::enlace($objeto->nombre, $configuracion['SERVIDOR']['media'] . $configuracion['RUTAS']['archivosCatalogos'] . $objeto->archivo, 'estiloEnlace', '', array('target' => '_blank'));
                }                
                
                $lista[] = $objeto;
            }
        }

        return $lista;
    }



    public function generarTabla($arregloRegistros, $datosPaginacion = NULL) {
        global $textos;
        //Declaracion de las columnas que se van a mostrar en la tabla
        $datosTabla = array(
            HTML::parrafo($textos->id('CODIGO'), 'centrado')    => 'id|m.id',
            HTML::parrafo($textos->id('NOMBRE'), 'centrado')    => 'nombre|m.nombre',
            HTML::parrafo($textos->id('MARCA'), 'centrado')     => 'marca|ma.nombre',
            HTML::parrafo($textos->id('ARCHIVO'), 'centrado')   => 'enlaceArchivo|m.nombre',
            HTML::parrafo($textos->id('ESTADO'), 'centrado')    => 'estado'
        );
        //ruta a donde se mandara la accion del doble click
        $ruta = '/ajax' . $this->urlBase . '/move';


        return Recursos::generarTablaRegistros($arregloRegistros, $datosTabla, $ruta, $datosPaginacion) . HTML::crearMenuBotonDerecho('MOTOS');
    }

    /**
     *
     * Metodo que se encarga de mostrar los checkBoxes con las motos
     *
     * */
    public static function cargarListaMotosEditar($idArticulo = NULL, $idMotoPrincipal = NULL) {

        $motos = ArticuloMotos::cargarMotosAplicablesEdit($idArticulo, $idMotoPrincipal);
        
        $codigo  = HTML::contenedor($motos.HTML::frase('', 'spanListaMotos', 'spanListaMotos'), 'contenedorListaMotos', 'contenedorListaMotos', array());
        
        
        return $codigo;
    }

}
