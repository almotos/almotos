<?php

/**
 * @package     FOM
 * @subpackage  Cajas 
 * @author      Pablo Andrés Vélez Vidal 
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2012 Genesys Soft.
 * @version     0.2
 * 
 * Clase encargada de gestionar la información del listado de cajas existentes en el negocio para el proceso de compra y venta.
 * Modulo utilizado en los modulos de compra y de venta, mostrando allí un select con las cajas pertenecientes a la sede con la que inició sesión el usuario.
 * también utilizado en los modulos de administración contable para la generación de reportes.
 * */
class Caja {

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
     * Código interno o identificador de la sede
     * @var entero
     */
    public $idSede;

    /**
     * nombre de la sede
     * @var entero
     */
    public $sede;

    /**
     * Nombre del item
     * @var entero
     */
    public $nombre;

    /**
     * Determina si esta caja esta activa en el sistema o no
     * @var entero
     */
    public $activo;
    
    /**
     * Determina si esta es la caja principal de la sede
     * @var entero
     */
    public $principal;    

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
     * Número de registros activos de la lista de cajas
     * @var entero
     */
    public $registrosActivos = NULL;

    /**
     * Número de registros traidos en una consulta de la lista de cajas
     * @var entero
     */
    public $registrosConsulta = NULL;

    /**
     * Orden predeterminado para organizar los listados
     * @var entero
     */
    public $ordenInicial = NULL;

    /**
     * Inicializar una caja
     * @param entero $id Código interno o identificador del caja en la base de datos
     */
    public function __construct($id = NULL) {
        global $sql, $modulo;
        $this->urlBase              = '/' . $modulo->url;
        $this->url                  = $modulo->url;
        //Saber el numero de registros
        $this->registros            = $sql->obtenerValor('cajas', 'COUNT(id)', 'id != "0"');
        //Saber el numero de registros activos
        $this->registrosActivos     = $sql->obtenerValor('cajas', 'COUNT(id)', 'id != "0"');
        //establecer el valor del campo predeterminado para organizar los listados
        $this->ordenInicial         = 'c.nombre';

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

        if (isset($id) && $sql->existeItem('cajas', 'id', intval($id))) {

            $tablas = array(
                'c' => 'cajas',
                's' => 'sedes_empresa'
            );

            $columnas = array(
                'id'                => 'c.id',
                'nombre'            => 'c.nombre',
                'idSede'            => 'c.id_sede',
                'sede'              => 's.nombre',
                'activo'            => 'c.activo',
                'principal'         => 'c.principal'
            );

            $condicion = 'c.id_sede = s.id  AND c.id = "' . $id . '"';

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
     * Adicionar una caja
     * @param  arreglo $datos       Datos de la caja a adicionar
     * @return entero               Código interno o identificador del caja en la base de datos (NULL si hubo error)
     */
    public function adicionar($datos) {
        global $sql;

        $datosItem  = array(
                            'id_sede'   => $datos['id_sede'],
                            'nombre'    => $datos['nombre'],
                            'activo'    => ( isset($datos['activo']) ) ? '1' : '0'
                            );
        
        if (isset($datos['principal'])) {
            $datosItem['principal'] = '1';
            $datosPrincipal         = array('principal' => '0');
            
            $sql->modificar('cajas', $datosPrincipal, 'id_sede = "' . $datosItem['id_sede'] . '"');
            
        } else {
            $datosItem['principal'] = '0';
            
        }        

        $consulta = $sql->insertar('cajas', $datosItem);

        if ($consulta) {
            $idItem = $sql->ultimoId;
            return $idItem;
            
        } else {
            return false;
            
        }
        
    }

    /**
     * Modificar una caja
     * @param  arreglo $datos       Datos del caja a modificar
     * @return lógico               Indica si el procedimiento se pudo realizar correctamente o no
     */
    public function modificar($datos) {
        global $sql;

        if (!isset($this->id)) {
            return NULL;
        }

        $datosItem  =   array(
                            'id_sede'       => $datos['id_sede'],
                            'nombre'        => $datos['nombre'],
                            'activo'        => ( isset($datos['activo']) ) ? '1' : '0'
                            );
        
        if (isset($datos['principal'])) {
            $datosItem['principal'] = '1';
            $datosPrincipal = array('principal' => '0');
            
            $sql->modificar('cajas', $datosPrincipal, 'id_sede = "' . $datosItem['id_sede'] . '"');
            
        } else {
            $datosItem['principal'] = '0';
            
        }        

        $consulta = $sql->modificar('cajas', $datosItem, 'id = "' . $this->id . '"');


        if ($consulta) {
            return true;
            
        } else {
            return false;
            
        }
    }

    /**
     * Eliminar una caja
     * @param entero $id    Código interno o identificador de una caja en la base de datos
     * @return lógico       Indica si el procedimiento se pudo realizar correctamente o no
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
        $arreglo1 = array('cotizaciones',                   'id_caja = "'.$this->id.'"', $textos->id('COTIZACIONES'));//arreglo del que sale la info a consultar
        $arreglo2 = array('facturas_compras',               'id_caja = "'.$this->id.'"', $textos->id('FACTURAS_COMPRA'));
        $arreglo3 = array('facturas_venta',                 'id_caja = "'.$this->id.'"', $textos->id('FACTURAS_VENTA'));
        $arreglo4 = array('facturas_temporales_venta',      'id_caja = "'.$this->id.'"', $textos->id('FACTURAS_TEMPORALES_VENTA'));
        $arreglo5 = array('facturas_temporales_compra',     'id_caja = "'.$this->id.'"', $textos->id('FACTURAS_TEMPORALES_COMPRA'));
        $arreglo6 = array('ordenes_compra',                 'id_caja = "'.$this->id.'"', $textos->id('ORDENES_COMPRA'));
        
        $arregloIntegridad = array($arreglo1, $arreglo2, $arreglo3, $arreglo4, $arreglo5, $arreglo6);//arreglo de arreglos para realizar las consultas de integridad referencial, (ver documentacion de metodo)
        $integridad = Recursos::verificarIntegridad($textos->id('CAJA'), $arregloIntegridad);
        /**
         * si hay problemas con la integridad referencial, la variable integridad tiene como valor,
         * un texto diciendo que tabla contiene n cantidad de relaciones con esta
         */
        if ($integridad != "") {
            $respuestaEliminar['mensaje'] = $integridad;
            return $respuestaEliminar;
        }
              
        $sql->iniciarTransaccion();
        $consulta = $sql->eliminar('cajas', 'id = "' . $this->id . '"');
        
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
     * Listar las cajas
     * @param entero  $cantidad    Número de cajas a incluir en la lista (0 = todas las entradas)
     * @param arreglo $excepcion   Arreglo con los códigos internos o identificadores a omitir en la lista
     * @param cadena  $condicion   Condición adicional (SQL)
     * @return arreglo             Lista de cajas
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


        $tablas =   array(
                        'c' => 'cajas',
                        's' => 'sedes_empresa'
                        );

        $columnas = array(
                        'id'            => 'c.id',
                        'nombre'        => 'c.nombre',
                        'sede'          => 's.nombre',
                        'activo'        => 'c.activo',
                        'principal'     => 'c.principal'
                        );

        if (!empty($condicionGlobal)) {
            $condicion .= $condicionGlobal . ' AND ';
        }

        $condicion .= ' c.id_sede = s.id';



        if (is_null($this->registrosConsulta)) {
            $sql->seleccionar($tablas, $columnas, $condicion);
            $this->registrosConsulta = $sql->filasDevueltas;
            
        }
        $consulta = $sql->seleccionar($tablas, $columnas, $condicion, 'c.id', $orden, $inicio, $cantidad);
        
        if ($sql->filasDevueltas) {
            $lista = array();

            while ($objeto = $sql->filaEnObjeto($consulta)) {

                $objeto->estado = ($objeto->activo) ? HTML::frase($textos->id('ACTIVO'), 'activo') : HTML::frase($textos->id('INACTIVO'), 'inactivo');
                $objeto->categoria = ($objeto->principal) ? HTML::frase($textos->id('PRINCIPAL'), 'activo') : HTML::frase($textos->id('SECUNDARIA'), 'inactivo');

                $lista[] = $objeto;
                
            }
        }

        return $lista;
    }

    /**
     * Metodo encargado de generar la tabla con el listado de registros de las bodegas
     * @global type $textos
     * @param type $arregloRegistros
     * @param type $datosPaginacion
     * @return type
     */
    public function generarTabla($arregloRegistros, $datosPaginacion = NULL) {
        global $textos;
        //Declaracion de las columnas que se van a mostrar en la tabla
        $datosTabla = array(
            HTML::parrafo($textos->id('SEDE'), 'centrado')          => 'sede|s.nombre',
            HTML::parrafo($textos->id('NOMBRE'), 'centrado')        => 'nombre|c.nombre',
            HTML::parrafo($textos->id('ESTADO'), 'centrado')        => 'estado',
            HTML::parrafo($textos->id('CATEGORIA'), 'centrado')     => 'categoria'
        );
        //ruta a donde se mandara la accion del doble click
        $ruta = '/ajax' . $this->urlBase . '/move';


        return Recursos::generarTablaRegistros($arregloRegistros, $datosTabla, $ruta, $datosPaginacion) . HTML::crearMenuBotonDerecho('CAJAS');
    }

}
