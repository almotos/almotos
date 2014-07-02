<?php

/**
 * @package     FOM
 * @subpackage  Tipos Venta 
 * @author      Pablo Andrés Vélez Vidal 
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2012 Genesys Soft.
 * @version     0.2
 * 
 * Tipos de Venta (venta/venta) utilizados para organizar la parte de facturacion contable
 * 
 * Clase encargada de gestionar la información del listado de tipos de venta existentes en el sistema. En este módulo se pueden
 * agregar, consultar, eliminar o modificar la información de los tipos de venta. Este modulo se relaciona con el modulo de TBD...
 * 
 * Aqui se crean por ejemplo los tipos de venta o venta, ejemplo: un tipo de venta sería "Venta a crédito y parte de cheque al contado", esto quiere decir 
 * que en este tipo de transacción una parte de la venta se hará a un cliente a crédito, y el resto ingresará por un cheque.
 * 
 * tabla principal: tipos_venta.
 * 
 * */
class TipoVenta {

    /**
     * Código interno o identificador de la unidad en la base de datos
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
     * Nombre del tipo de venta
     * @var entero
     */
    public $nombre;
    
    /**
     * Descripción del tipo de venta
     * @var entero
     */
    public $descripcion;    

    /**
     * Tipo de venta (1=> credito, 2=> contado, 3=>mixto)
     * @var entero
     */
    public $tipo;  

    /**
     * Determina si el registro esta activo
     * @var entero
     */
    public $activo;
    
    /**
     * Determina si este es el tipo de venta principal
     * @var entero
     */
    public $principal;      

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
     * Número de registros activos de la lista 
     * @var entero
     */
    public $registrosActivos = NULL;

    /**
     * Número de registros activos de la lista 
     * @var entero
     */
    public $registrosConsulta = NULL;
    
    /**
     * Arreglo con el listado de cuentas que se afectan directamente por la partida doble
     * @var lógico
     */
    public $listaCuentas = array();     

    /**
     * Orden predeterminado para organizar los listados
     * @var entero
     */
    public $ordenInicial = NULL;

    /**
     * Inicializar un tipo_venta
     * @param entero $id Código interno o identificador del tipo_venta en la base de datos
     */
    public function __construct($id = NULL) {
        global $sql, $modulo;
        
        $this->urlBase      = '/' . $modulo->url;
        $this->url          = $modulo->url;
        $this->idModulo     = $modulo->id;
        //Saber el numero de registros
        $this->registros        = $sql->obtenerValor('tipos_venta', 'COUNT(id)', 'id != "0" ');
        //Saber el numero de registros activos
        $this->registrosActivos = $sql->obtenerValor('tipos_venta', 'COUNT(id)', 'activo = "1" AND id != "0" ');
        //establecer el valor del campo predeterminado para organizar los listados
        $this->ordenInicial     = 'nombre';

        if (isset($id)) {
            $this->cargar($id);
        }
    }


    /**
     * Cargar los datos de un tipo_venta
     * @param entero $id Código interno o identificador del tipo_venta en la base de datos
     */
    public function cargar($id) {
        global $sql;

        if (isset($id) && $sql->existeItem('tipos_venta', 'id', intval($id))) {

            $tablas = array(
                'tc' => 'tipos_venta'
            );

            $columnas = array(
                'id'            => 'tc.id',
                'nombre'        => 'tc.nombre',
                'tipo'          => 'tc.tipo',
                'activo'        => 'tc.activo',
                'descripcion'   => 'tc.descripcion',
                'principal'     => 'tc.principal'
            );

            $condicion = 'tc.id = "' . $id . '"';

            $consulta = $sql->seleccionar($tablas, $columnas, $condicion);

            if ($sql->filasDevueltas) {
                $fila = $sql->filaEnObjeto($consulta);

                foreach ($fila as $propiedad => $valor) {
                    $this->$propiedad = $valor;
                }

                $this->url = $this->urlBase . '/' . $this->id;
                
                //verificar si el proveedor tiene cuentas bancarias, en caso de que tenga, hacer la consulta y armar la lista de objetos
                $tablas2        = array('cp' => 'cuentas_tipo_venta', 'pc' => 'plan_contable');
                $columnas2      = array('id' => 'cp.id', 'cuenta' => 'pc.nombre', 'codigoCuenta' => 'pc.codigo_contable', 'tipoCuenta' => 'cp.tipo');
                $sql->depurar   = true;
                $this->listaCuentas = $sql->seleccionar($tablas2, $columnas2, 'cp.id_cuenta = pc.id AND cp.id_tipo_venta = "' . $id . '"');                
                                
                
            }
        }
    }


    /**
     * Adicionar un tipo_venta
     * @param  arreglo $datos       Datos del tipo_venta a adicionar
     * @return entero               Código interno o identificador del tipo_venta en la base de datos (NULL si hubo error)
     */
    public function adicionar($datos) {
        global $sql;

        $datosItem = array();

        $datosItem['nombre']        = $datos['nombre'];
        $datosItem['descripcion']   = $datos['descripcion'];
        $datosItem['tipo']          = $datos['tipo'];

        if (isset($datos['activo'])) {
            $datosItem['activo'] = '1';
        } else {
            $datosItem['activo'] = '0';
        }
        
        $sql->iniciarTransaccion();
        
        if (isset($datos['principal'])) {
            $datosItem['principal'] = '1';
            $datosPrincipal         = array('principal' => '0');
            
            $modificar = $sql->modificar('tipos_venta', $datosPrincipal, 'principal = "1"');
            
            if(!$modificar){
                $sql->cancelarTransaccion();
            }
            
        } else {
            $datosItem['principal'] = '0';
            
        }            

        $consulta = $sql->insertar('tipos_venta', $datosItem);

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
     * Modificar un tipo_venta
     * @param  arreglo $datos       Datos del tipo_venta a modificar
     * @return lógico               Indica si el procedimiento se pudo realizar correctamente o no
     */
    public function modificar($datos) {
        global $sql;

        if (!isset($this->id)) {
            return NULL;
        }

        $datosItem = array();

        $datosItem['nombre']        = $datos['nombre'];
        $datosItem['descripcion']   = $datos['descripcion'];
        $datosItem['tipo']          = $datos['tipo'];

        if (isset($datos['activo'])) {
            $datosItem['activo'] = '1';
            
        } else {
            $datosItem['activo'] = '0';
            
        }
        
        $sql->iniciarTransaccion();
        
        if (isset($datos['principal'])) {
            $datosItem['principal'] = '1';
            $datosPrincipal         = array('principal' => '0');
            
            $modificar = $sql->modificar('tipos_venta', $datosPrincipal, 'principal = "1"');
            
            if(!$modificar){
                $sql->cancelarTransaccion();
            }            
            
        } else {
            $datosItem['principal'] = '0';
            
        }         
        
        $consulta = $sql->modificar('tipos_venta', $datosItem, 'id = "' . $this->id . '" ');
        
        if ($consulta) {
            $sql->finalizarTransaccion();
            return true;
            
        } else {
            $sql->cancelarTransaccion();
            return false;
        };

    }


    /**
     * Eliminar un tipo_venta
     * @param entero $id    Código interno o identificador de un tipo_venta en la base de datos
     * @return lógico       Indica si el procedimiento se pudo realizar correctamente o no
     */
    public function eliminar() {
        global $sql;

        if (!isset($this->id)) {
            return NULL;
        }

        $consulta = $sql->eliminar('tipos_venta', 'id = "' . $this->id . '"');
        
        return ($consulta) ? true : false;
    }

    /**
     * Listar los tipos_venta
     * @param entero  $cantidad    Número de tipos_venta a incluir en la lista (0 = todas las entradas)
     * @param arreglo $excepcion   Arreglo con los códigos internos o identificadores a omitir en la lista
     * @param cadena  $condicion   Condición adicional (SQL)
     * @return arreglo             Lista de tipos_venta
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
        if (isset($excepcion) && is_array($excepcion) && count($excepcion)) {
            $excepcion = implode(',', $excepcion);
            $condicion .= 'tc.id NOT IN (' . $excepcion . ')';
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
            'tc' => 'tipos_venta'
        );

        $columnas = array(
            'id'        => 'tc.id',
            'nombre'    => 'tc.nombre',
            'tipo'      => 'tc.tipo',
            'activo'    => 'tc.activo',
            'principal' => 'tc.principal'
        );

        if (!empty($condicionGlobal)) {
            if ($condicion != '') {
                $condicion .= ' AND ';
            }
            $condicion .= $condicionGlobal;
        }

        if (is_null($this->registrosConsulta)) {
            $sql->seleccionar($tablas, $columnas, $condicion);
            $this->registrosConsulta = $sql->filasDevueltas;
        }
        //$sql->depurar = true;
        $consulta = $sql->seleccionar($tablas, $columnas, $condicion, 'tc.id', $orden, $inicio, $cantidad);
        
        if ($sql->filasDevueltas) {
            $lista = array();

            while ($objeto = $sql->filaEnObjeto($consulta)) {
                $objeto->url        = $this->urlBase . '/' . $objeto->id;
                $objeto->idModulo   = $this->idModulo;
                
                $varEstado = '';
                $varEstado = ($objeto->activo) ? 'ACTIVO' : 'INACTIVO';                        
                $objeto->estado = HTML::frase($textos->id((string)$varEstado), strtolower($varEstado));
                
                $objeto->tipo = $textos->id('TIPO_'.$objeto->tipo);
                
                $objeto->principal1 = ($objeto->principal) ? HTML::frase($textos->id('PRINCIPAL'), 'activo') : HTML::frase($textos->id('SECUNDARIA'), 'inactivo');                
                
                
                $lista[] = $objeto;
            }
        }

        return $lista;
    }


    /**
     * Metodo que arma la grilla para mostrarse desde la pagina principal
     * @global type $textos
     * @param type $arregloRegistros
     * @param type $datosPaginacion
     * @return type 
     */
    public function generarTabla($arregloRegistros, $datosPaginacion = NULL) {
        global $textos, $sesion_usuarioSesion;
        //Declaracion de las columnas que se van a mostrar en la tabla
        $datosTabla = array(
            HTML::parrafo($textos->id('NOMBRE'), 'centrado')        => 'nombre|tc.nombre',
            HTML::parrafo($textos->id('TIPO'), 'centrado')          => 'tipo|tc.tipo',
            HTML::parrafo($textos->id('ESTADO'), 'centrado')        => 'estado',
            HTML::parrafo($textos->id('PRINCIPAL'), 'centrado')     => 'principal1'
        );
        //ruta a donde se mandara la accion del doble click
        $ruta = '/ajax' . $this->urlBase . '/move';
        
        $agregarCuentaAfectada = '';

        $puedeAgregarCuentaAfectada = Perfil::verificarPermisosBoton('botonAgregarCuentaAfectadaVenta');
        if ($puedeAgregarCuentaAfectada || $sesion_usuarioSesion->id == 0) {
            $agregarCuentaAfectada1    = HTML::formaAjax($textos->id('AGREGAR_CUENTAS_AFECTADAS'), 'contenedorMenuCuentas', 'agregarCuentaAfectada', '', '/ajax/tipos_venta/agregarCuentas', array('id' => ''));
            $agregarCuentaAfectada     = HTML::contenedor($agregarCuentaAfectada1, '', 'botonAgregarCuentaAfectadaVenta');
        }

        $botonesExtras = array($agregarCuentaAfectada);            

        return Recursos::generarTablaRegistros($arregloRegistros, $datosTabla, $ruta, $datosPaginacion) . HTML::crearMenuBotonDerecho('TIPOS_VENTA', $botonesExtras);
    }

}
