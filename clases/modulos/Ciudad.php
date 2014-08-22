<?php

/**
 *
 * @package     FOLCS
 * @subpackage  Ciudades
 * @author      Pablo Andr�s V�lez Vidal <pavelez@colomboamericano.edu.co>
 * @author      Julian A. Mondrag�n <jmondragon@felinux.com.co>
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2011 Colombo-Americano Soft.
 * @version     0.2
 * 
 * Clase encargada de gestionar la informaci�n del listado de ciudades existentes en el sistema. En este m�dulo se pueden
 * agregar, consultar, eliminar o modificar la informaci�n de las ciudades. Este m�dulo es utilizado por diversos modulos
 * en el sistema, como por ejemplo "USUARIOS" para relacionar la ciudad de residencia del usuario, o "SEDES PROVEEDOR" para
 * identificar la ciudad de la sede, y siempre mantener la ingridad referencial.
 *
 **/


class Ciudad {

    /**
     * C�digo interno o identificador del pa�s en la base de datos
     * @var entero
     */
    public $id;


    /**
     * URL relativa del m�dulo de la ciudad
     * @var cadena
     */
    public $urlBase;


    /**
     * URL relativa de una ciudad espec�fica
     * @var cadena
     */
    public $url;


    /**
     * Nombre de la ciudad
     * @var cadena
     */
    public $nombre;


    /**
     * id del Estado
     * @var cadena
     */
    public $idEstado;


     /**
     * nombre del Estado
     * @var cadena
     */
    public $Estado;
    
    /**
     * id del Estado
     * @var cadena
     */
    public $idPais;


     /**
     * nombre del pais y su bandera
     * @var cadena
     */
    public $pais;   
    
     /**
     * solo el nombre del pais
     * @var cadena
     */
    public $paisSolo;       
    
    
    
     /**
     * nombre del Estado
     * @var cadena
     */
    public $codigo;      


    /**
     * Indicador del orden cronol�gio de la lista de ciudades
     * @var l�gico
     */
    public $listaAscendente = TRUE;


    /**
     * N�mero de registros de la lista
     * @var entero
     */
    public $registros = NULL;
    
    /**
     * N�mero de registros activos de la lista de foros
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
     * Inicializar la Ciudad
     *
     * @param entero $id C�digo interno o identificador de la ciudad en la base de datos
     *
     */
    public function __construct($id = NULL) {
        global $sql, $modulo;
        $this->urlBase = '/'.$modulo->url;
        $this->url     = $modulo->url;
       
        $this->registros = $sql->obtenerValor('ciudades', 'COUNT(id)', 'id != "0"');
        //establecer el valor del campo predeterminado para organizar los listados
        $this->ordenInicial = 'nombre';

        if (isset($id)) {
            $this->cargar($id);
        }
    }





    /**
     *
     * Cargar los datos de una ciudad
     *
     * @param entero $id C�digo interno o identificador de la ciudad en la base de datos
     *
     */
    public function cargar($id) {
        global $sql, $configuracion;

        if (isset($id) && $sql->existeItem('ciudades', 'id', intval($id))) {

            $tablas = array(
                'c' => 'ciudades',
                'e' => 'estados',
                'p' => 'paises'
            );

            $columnas = array(
                'id'       => 'c.id',
                'idEstado' => 'c.id_estado',
                'nombre'   => 'c.nombre',
                'Estado'   => 'e.nombre',
                'idPais'   => 'e.id_pais',
                'pais'     => 'p.nombre',
                'codigo'   => 'p.codigo_iso'
            );

            $condicion = 'c.id_estado = e.id AND e.id_pais = p.id AND c.id = "'.$id.'"';

            $consulta = $sql->seleccionar($tablas, $columnas, $condicion);

            if ($sql->filasDevueltas) {
                $fila = $sql->filaEnObjeto($consulta);

                foreach ($fila as $propiedad => $valor) {
                    $this->$propiedad = $valor;
                }
                
                $this->paisSolo = $this->pais;
                $this->pais = HTML::imagen($configuracion['SERVIDOR']['media'] . $configuracion['RUTAS']['iconosBanderas'] . '/' . strtolower($this->codigo) . '.png', 'miniaturaBanderas margenDerecha').$this->pais;
                $this->url = $this->urlBase.'/'.$this->usuario;
            }
        }
    }




    /**
     *
     * Adicionar una ciudad
     *
     * @param  arreglo $datos       Datos de la ciudad a adicionar
     * @return entero               C�digo interno o identificador de la ciudad en la base de datos (NULL si hubo error)
     *
     */
    public function adicionar($datos) {
        global $sql;
        
        $idEstado = $sql->obtenerValor('lista_estados', 'id', 'cadena = "'.utf8_decode($datos['id_estado']) .'"');
        
        $datosCiudad = array(
            'nombre'    => $datos['nombre'],
            'id_estado' => $idEstado
        );

        $consulta = $sql->insertar('ciudades', $datosCiudad);

        if ($consulta) {
            return $sql->ultimoId;

        } else {
            return NULL;
        }
    }



    /**
     *
     * Modificar una ciudad
     *
     * @param  arreglo $datos       Datos de la ciudad a modificar
     * @return l�gico               Indica si el procedimiento se pudo realizar correctamente o no
     *
     */
    public function modificar($datos) {
        global $sql;

        if (!isset($this->id)) {
            return NULL;
        }
        
        $idEstado = $sql->obtenerValor('lista_estados', 'id', 'cadena = "'.utf8_decode($datos['id_estado']).'"');
        
        $datosCiudad = array(
            'nombre'    => $datos['nombre'],
            'id_estado' => $idEstado
        );
        

        $consulta = $sql->modificar('ciudades', $datosCiudad, 'id = "'.$this->id.'"');
        return $consulta;
    }




    /**
     *
     * Eliminar una ciudad
     *
     * @param entero $id    C�digo interno o identificador de la ciudad en la base de datos
     * @return l�gico       Indica si el procedimiento se pudo realizar correctamente o no
     *
     */
  
    public function eliminar() {
        global $sql, $textos;

        //arreglo que ser� devuelto como respuesta
        $respuestaEliminar = array(
            'respuesta' => false,
            'mensaje'   => $textos->id('ERROR_DESCONOCIDO'),
        );
        
        if (!isset($this->id)) {
            return $respuestaEliminar;
        }
         
        //hago la validacion de la integridad referencial
        $arreglo1 = array('localidades',      'id_ciudad            = "'.$this->id.'"', $textos->id('LOCALIDADES'));//arreglo del que sale la info a consultar
        $arreglo2 = array('personas',         'id_ciudad_residencia = "'.$this->id.'"', $textos->id('PERSONAS'));//arreglo del que sale la info a consultar
        $arreglo3 = array('personas',         'id_ciudad_documento  = "'.$this->id.'"', $textos->id('PERSONAS'));//arreglo del que sale la info a consultar
        $arreglo4 = array('sedes_cliente',    'id_ciudad            = "'.$this->id.'"', $textos->id('SEDES_CLIENTE'));//arreglo del que sale la info a consultar
        $arreglo5 = array('sedes_empresa',    'id_ciudad            = "'.$this->id.'"', $textos->id('SEDES_EMPRESA'));//arreglo del que sale la info a consultar
        $arreglo6 = array('sedes_proveedor',  'id_ciudad            = "'.$this->id.'"', $textos->id('SEDES_PROVEEDOR'));//arreglo del que sale la info a consultar
        
        $arregloIntegridad  = array($arreglo1, $arreglo2, $arreglo3, $arreglo4, $arreglo5, $arreglo6);//arreglo de arreglos para realizar las consultas de integridad referencial, (ver documentacion de metodo)
        $integridad         = Recursos::verificarIntegridad($textos->id('CIUDAD'), $arregloIntegridad);

        /**
         * si hay problemas con la integridad referencial, la variable integridad tiene como valor,
         * un texto diciendo que tabla contiene n cantidad de relaciones con esta
         */
        if ($integridad != "") {
            $respuestaEliminar['mensaje'] = $integridad;
            return $respuestaEliminar;
            
        }
              
        $sql->iniciarTransaccion();
        $consulta = $sql->eliminar('ciudades', "id = '".$this->id."'");
        
        if (!($consulta)) {
            $sql->cancelarTransaccion("Fallo en el archivo " . __FILE__ . " en la linea " .  __LINE__);
            return $respuestaEliminar;
            
        } else {
            $sql->finalizarTransaccion();
            //todo sali� bien, se envia la respuesta positiva
            $respuestaEliminar['respuesta'] = true;
            return $respuestaEliminar;
        }
        
    }
    /**
     *
     * Listar las ciudades
     *
     * @param entero  $cantidad    N�mero de ciudadesa incluir en la lista (0 = todas las entradas)
     * @param arreglo $excepcion   Arreglo con los c�digos internos o identificadores a omitir en la lista
     * @param cadena  $condicion   Condici�n adicional (SQL)
     * @return arreglo             Lista de ciudades
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

        /*** Validar que la condici�n sea una cadena de texto ***/
        if (!is_string($condicionGlobal)) {
            $condicion = '';
        }

        /*** Validar que la excepci�n sea un arreglo y contenga elementos ***/
        if (isset($excepcion) && is_array($excepcion) && count($excepcion)) {
            $excepcion = implode(',', $excepcion);
            $condicion .= 'c.id NOT IN ('.$excepcion.') AND ';
        }

        /*** Definir el orden de presentaci�n de los datos ***/
        /*** Definir el orden de presentaci�n de los datos ***/
        if(!isset($orden)){
            $orden = $this->ordenInicial;
        }
        if ($this->listaAscendente) {
            $orden = $orden.' ASC';

        } else {
            $orden = $orden.' DESC';
        }

        $tablas = array(
            'c' => 'ciudades',
            'e' => 'estados',
            'p' => 'paises'
        );

        $columnas = array(
            'id'       => 'c.id',
            'idEstado' => 'c.id_estado',
            'nombre'   => 'c.nombre',
            'Estado'   => 'e.nombre',
            'idPais'   => 'e.id_pais',
            'pais'     => 'p.nombre',
            'codigo'   => 'p.codigo_iso'
        );

            
        
         
        if (!empty($condicionGlobal)) {
            
            $condicion .= $condicionGlobal.' AND ';
        } 
        
        $condicion .= 'c.id_estado = e.id AND e.id_pais = p.id';
       

        if (is_null($this->registrosConsulta)) {
            $sql->seleccionar($tablas, $columnas, $condicion);
            $this->registrosConsulta = $sql->filasDevueltas;
        }
        //$sql->depurar = true;
        $consulta = $sql->seleccionar($tablas, $columnas, $condicion, '', $orden, $inicio, $cantidad);

        if ($sql->filasDevueltas) {
            $lista = array();

            while ($ciudad = $sql->filaEnObjeto($consulta)) {
                $ciudad->url = $this->urlBase.'/'.$ciudad->id;
                $ciudad->pais = HTML::imagen($configuracion['SERVIDOR']['media'] . $configuracion['RUTAS']['iconosBanderas'] . '/' . strtolower($ciudad->codigo) . '.png', 'miniaturaBanderas margenDerechaTriple').$ciudad->pais;
                $lista[]   = $ciudad;
            }
        }

        return $lista;

    }
    
    
    public function generarTabla($arregloRegistros, $datosPaginacion = NULL){
        global $textos;
        //Declaracion de las columnas que se van a mostrar en la tabla
        $datosTabla = array(                      
            HTML::parrafo( $textos->id('NOMBRE')                ,  'centrado' ) => 'nombre|c.nombre',
            HTML::parrafo( $textos->id('ESTADO')                ,  'centrado' ) => 'Estado|e.nombre',
            HTML::parrafo( $textos->id('PAIS')                  ,  'centrado' ) => 'pais|p.nombre'
        );        
        //ruta a donde se mandara la accion del doble click
        $rutaPaginador = '/ajax'.$this->urlBase.'/move';
        
        return Recursos::generarTablaRegistros($arregloRegistros, $datosTabla, $rutaPaginador, $datosPaginacion).HTML::crearMenuBotonDerecho('CIUDADES');
        
    }    
    
    
    
}
