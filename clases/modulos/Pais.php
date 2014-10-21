<?php

/**
 *
 * @package     FOLCS
 * @subpackage  Paises
 * @author      Francisco J. Lozano c. <fjlozano@felinux.com.co>
 * @author      Julian A. Mondragón <jmondragon@felinux.com.co>
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2009 FELINUX LTDA
 * @version     0.1
 *
 **/

/**
 * Clase Pais: clase encargada de gestionar la informacion de los registros sobre los paises almacenados en el sistema.
 * es una clase que contiene en su mayoria metodos crud para la gestion con la bd, ademas de esto tiene algunos metodos
 * para el renderizado de informacion, como por ejemplo el metodo generar tabla.Esta clase mantiene una relacion directa
 * con las clases paises y ciudades, ya que una ciudad pertenece a un estado y un estado pertenece a un pais.
 */
class Pais {

    /**
     * Código interno o identificador del país en la base de datos
     * @var entero
     */
    public $id;

    /**
     * URL relativa del módulo de paises
     * @var cadena
     */
    public $urlBase;

    /**
     * URL relativa de un pais específico
     * @var cadena
     */
    public $url;
    
     /**
     * Código interno o identificador del modulo
     * @var entero
     */
    public $idModulo;
    
    /**
     * Nombre del país
     * @var cadena
     */
    public $nombre;

    /**
     * Código ISO del país
     * @var cadena
     */
    public $codigo;
    
    /**
     * Código ISO del país
     * @var cadena
     */
    public $bandera;    
    
    /**
     * Código comercial del pais
     * @var cadena
     */
    public $codigoCo;    

    /**
     * Indicador del orden cronológio de la lista de paises
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
     * Inicializar el pais
     *
     * @param entero $id Código interno o identificador del país en la base de datos
     *
     */
    public function __construct($id = NULL) {
        global $sql, $modulo;
        $this->urlBase = '/'.$modulo->url;
        $this->url     = $modulo->url;
        $this->idModulo  = $modulo->id;
        //Saber el numero de registros
        $this->registros = $sql->obtenerValor('paises', 'COUNT(id)', 'id != "0"');  
        //establecer el valor del campo predeterminado para organizar los listados
        $this->ordenInicial = 'nombre';           

        if (!empty($id)) {
            $this->cargar($id);
        }
    }

    /**
     *
     * Cargar los datos de un pais
     *
     * @param entero $id Código interno o identificador del país en la base de datos
     *
     */
    public function cargar($id) {
        global $sql, $configuracion;

        if (isset($id) && $sql->existeItem('paises', 'id', intval($id))) {

            $tablas = array(
                'p' => 'paises'
            );

            $columnas = array(
                'id'        => 'p.id',
                'nombre'    => 'p.nombre',
                'codigo'    => 'p.codigo_iso',
                'codigoCo'  => 'p.codigo_comercial'
            );

            $condicion = 'p.id = "'.$id.'"';

            $consulta = $sql->seleccionar($tablas, $columnas, $condicion);

            if ($sql->filasDevueltas) {
                $fila = $sql->filaEnObjeto($consulta);

                foreach ($fila as $propiedad => $valor) {
                    $this->$propiedad = $valor;
                }

                $this->url = $this->urlBase.'/'.$this->usuario;
                $this->bandera = HTML::imagen($configuracion['SERVIDOR']['media'] . $configuracion['RUTAS']['iconosBanderas'] . '/' . strtolower($this->codigo) . '.png', 'miniaturaBanderas');
            }
        }
    }

    /**
     *
     * Adicionar un pais
     *
     * @param  arreglo $datos       Datos del país a adicionar
     * @return entero               Código interno o identificador del país en la base de datos (NULL si hubo error)
     *
     */
    public function adicionar($datos) {
        global $sql;

        unset($datos['dialogo']);
        $consulta = $sql->insertar('paises', $datos);

        if ($consulta) {
            return $sql->ultimoId;

        } else {
            return NULL;
        }
    }

    /**
     *
     * Modificar un pais
     *
     * @param  arreglo $datos       Datos del país a modificar
     * @return lógico               Indica si el procedimiento se pudo realizar correctamente o no
     *
     */
    public function modificar($datos) {
        global $sql;

        if (!isset($this->id)) {
            return NULL;
        }
        unset($datos['dialogo']);
        $consulta = $sql->modificar('paises', $datos, 'id = "'.$this->id.'"');
        return $consulta;
    }

    /**
     *
     * Eliminar un pais
     *
     * @param entero $id    Código interno o identificador del país en la base de datos
     * @return lógico       Indica si el procedimiento se pudo realizar correctamente o no
     *
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
         
        //hago la validacion de la integridad referencial
        $arreglo1 = array('estados',       'id_pais  = "'.$this->id.'"', $textos->id('ESTADOS'));//arreglo del que sale la info a consultar
        //$arreglo2 = array('articulos',     'id_pais  = "'.$this->id.'"', $textos->id('ARTICULOS'));//arreglo del que sale la info a consultar
       
        $arregloIntegridad  = array($arreglo1);//arreglo de arreglos para realizar las consultas de integridad referencial, (ver documentacion de metodo)
        $integridad         = Recursos::verificarIntegridad($textos->id('PAIS'), $arregloIntegridad);

        /**
         * si hay problemas con la integridad referencial, la variable integridad tiene como valor,
         * un texto diciendo que tabla contiene n cantidad de relaciones con esta
         */
        if ($integridad != "") {
            $respuestaEliminar['mensaje'] = $integridad;
            return $respuestaEliminar;
            
        }
              
        $sql->iniciarTransaccion();
        $consulta = $sql->eliminar('paises', 'id = "'.$this->id.'"');
        
        if (!($consulta)) {
            $sql->cancelarTransaccion("Fallo en el archivo " . __FILE__ . " en la linea " .  __LINE__);
            return $respuestaEliminar;
            
        } else {
            $sql->finalizarTransaccion();
            //todo salió bien, se envia la respuesta positiva
            $respuestaEliminar['respuesta'] = true;
            return $respuestaEliminar;
            
        }
        
    }

    /**
     *
     * Listar los paises
     *
     * @param entero  $cantidad    Número de paises a incluir en la lista (0 = todas las entradas)
     * @param arreglo $excepcion   Arreglo con los códigos internos o identificadores a omitir en la lista
     * @param cadena  $condicion   Condición adicional (SQL)
     * @return arreglo             Lista de paises
     *
     */
    public function listar($inicio = 0, $cantidad = 0, $excepcion = NULL, $condicionGlobal = NULL, $orden = NULL) {
        global $sql, $configuracion;

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
            $condicion .= 'p.id NOT IN ('.$excepcion.')';
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
            'p' => 'paises',
        );

        $columnas = array(
            'id'        => 'p.id',
            'nombre'    => 'p.nombre',
            'codigo'    => 'p.codigo_iso',
            'codigoCo'  => 'p.codigo_comercial'
        );
        
        if (!empty($condicionGlobal)) {
            if($condicion != ''){
                $condicion .= ' AND ';
            }
            $condicion .= $condicionGlobal;
        }        

        if (is_null($this->registrosConsulta)) {
            $sql->seleccionar($tablas, $columnas, $condicion);
            $this->registrosConsulta = $sql->filasDevueltas;
        }

        $consulta = $sql->seleccionar($tablas, $columnas, $condicion, '', $orden, $inicio, $cantidad);

        $lista = array();
        if ($sql->filasDevueltas) {           

            while ($objeto = $sql->filaEnObjeto($consulta)) {
                $objeto->url = $this->urlBase.'/'.$objeto->id;
                $objeto->idModulo  = $this->idModulo;                
                $objeto->imagen = HTML::imagen($configuracion['SERVIDOR']['media'] . $configuracion['RUTAS']['iconosBanderas'] . '/' . strtolower($objeto->codigo) . '.png', 'miniaturaBanderas');
                                
                $lista[]   = $objeto;
            }
        }

        return $lista;

    }
    
    
    public function generarTabla($arregloRegistros, $datosPaginacion = NULL){
        global $textos;
        //Declaracion de las columnas que se van a mostrar en la tabla
        $datosTabla = array(
            HTML::parrafo( $textos->id('NOMBRE')            ,  'centrado' ) => 'nombre|p.nombre',
            HTML::parrafo( $textos->id('CODIGO')            ,  'centrado' ) => 'codigo|p.codigo_iso',
            HTML::parrafo( $textos->id('CODIGO_COMERCIAL')  ,  'centrado' ) => 'codigoCo|p.codigo_comercial',        
            HTML::parrafo( $textos->id('BANDERA')           ,  'centrado' ) => 'imagen'
        );        
        //ruta a donde se mandara la accion del doble click
        $ruta = '/ajax'.$this->urlBase.'/move';
        
        return Recursos::generarTablaRegistros($arregloRegistros, $datosTabla, $ruta, $datosPaginacion).HTML::crearMenuBotonDerecho('PAISES');
        
    }    
    
    
    
}

?>