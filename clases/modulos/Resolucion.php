<?php

/**
 *
 * @package     FOLM
 * @subpackage  Modulos 
 * @author      Pablo Andrés Vélez Vidal 
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2012 Genesys Soft.
 * @version     0.2
 * 
 * Resoluciones utilizados en el sistema otorgadas por la dian para la facturación
 * */
class Resolucion {

    /**
     * Código interno o identificador de la resolución en la base de datos
     * @var entero
     */
    public $id;

    /**
     * URL relativa del módulo de resoluciónes
     * @var cadena
     */
    public $urlBase;

    /**
     * URL relativa del módulo de unidades
     * @var cadena
     */
    public $url;

    /**
     * Código interno o identificador del modulo
     * @var entero
     */
    public $idModulo;

    /**
     * Tabla principal a la que va relacionada el modulo
     * @var entero
     */
    public $tabla;

    /**
     * Identificador de la sede a la cual aplicará esta resolución
     * @var cadena
     */
    public $idSede;

    /**
     * Objeto que representa la sede
     * @var entero
     */
    public $sede;

    /**
     * Prefijo posible de las facturas con esta resolución
     * @var cadena
     */
    public $prefijo;

    /**
     * Numero de la resolución
     * @var entero
     */
    public $numero;

    /**
     * Fecha en la que se expide la resolución
     * @var entero
     */
    public $fechaResolucion;

    /**
     * Numero de factura con la que inicia esta resolucion
     * @var entero
     */
    public $numeroFacturaInicial;

    /**
     * Numero de factura con la que termina esta resolucion
     * se agrega cuando se pasa a una nueva resolucion
     * @var entero
     */
    public $numeroFacturaFinal;

    /**
     * Fecha en la que inicia esta resolucion
     * @var entero
     */
    public $fechaInicial;

    /**
     * Fecha en la que termina esta resolucion
     * @var entero
     */
    public $fechafinal;

    /**
     * Numero de facturas antes de la cual el sistema debe avisar que esta proxima a acabarse la resolucion
     * @var entero
     */
    public $numeroFacturasAlerta;

    /**
     * Identificador de l factura que se utiliza para parametrizar el sistema y que facture desde un número dado
     * @var entero
     */
    public $idFacturaRetoma;

    /**
     * Numero de factura en el cual venia facturando el proceso anterior, para que el sistema inicie con este de nuevo, esto en caso de ser la primera resolución que se introduce en el sistema para una sede en particular
     * @var entero
     */
    public $numeroRetomaFacturacion;

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
     * Número de registros activos 
     * @var entero
     */
    public $registrosActivos = NULL;

    /**
     * Número de registros activos 
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
     * Inicializar una resolucion
     *
     * @param entero $id Código interno o identificador del grupo en la base de datos
     *
     */
    public function __construct($id = NULL) {
        global $sql, $modulo;
        $this->urlBase          = '/' . $modulo->url;
        $this->url              = $modulo->url;
        $this->idModulo         = $modulo->id;
        $this->tabla            = $modulo->tabla;
        //Saber el numero de registros
        $this->registros        = $sql->obtenerValor('resoluciones', 'COUNT(id)', 'id != "0"');
        //Saber el numero de registros activos
        $this->registrosActivos = $sql->obtenerValor('resoluciones', 'COUNT(id)', 'activo = "1"');
        //establecer el valor del campo predeterminado para organizar los listados
        $this->ordenInicial     = 'numero';

        if (isset($id)) {
            $this->cargar($id);
            
        }
        
    }

    /**
     *
     * Cargar los datos de una resolucion
     *
     * @param entero $id Código interno o identificador del grupo en la base de datos
     *
     */
    public function cargar($id) {
        global $sql;

        if (isset($id) && $sql->existeItem('resoluciones', 'id', intval($id))) {

            $tablas = array(
                'r' => 'resoluciones'
            );

            $columnas = array(
                'id'                        => 'r.id',
                'idSede'                    => 'r.id_sede',
                'prefijo'                   => 'r.prefijo',
                'numero'                    => 'r.numero',
                'fechaResolucion'           => 'r.fecha_resolucion',
                'numeroFacturaInicial'      => 'r.num_factura_inicio',
                'numeroFacturaFinal'        => 'r.num_factura_final',
                'fechaInicial'              => 'r.fecha_inicio',
                'fechaFinal'                => 'r.fecha_final',
                'numeroFacturasAlerta'      => 'r.numero_facturas_alerta',
                'idFacturaRetoma'           => 'r.id_factura_retoma',
                'activo'                    => 'r.activo'
                
            );

            $condicion = 'r.id = "' . $id . '"';
            
            $consulta = $sql->seleccionar($tablas, $columnas, $condicion);

            if ($sql->filasDevueltas) {
                $fila = $sql->filaEnObjeto($consulta);

                foreach ($fila as $propiedad => $valor) {
                    $this->$propiedad = $valor;
                }

                if (!empty($this->idFacturaRetoma)) {//si esta resolución retomó la facturación desde un número especifico
                    $this->numeroRetomaFacturacion = $sql->obtenerValor('facturas_venta', 'id_factura', 'id = "' . $this->idFacturaRetoma . '"'); //lo cargo al atributo del objeto
                }

                $this->sede = new SedeEmpresa($this->idSede);
            }
            
        }
        
    }

    /**
     * Adicionar una resolucion
     * 
     * @global type $sql
     * @param type $datos
     * @return null 
     */
    public function adicionar($datos) {
        global $sql, $sesion_usuarioSesion, $textos;

        $datos_item = array(
            'id_sede'                   => $datos['id_sede'],
            'prefijo'                   => $datos['prefijo'],
            'numero'                    => $datos['numero'],
            'fecha_resolucion'          => $datos['fecha_resolucion'],
            'num_factura_inicio'        => $datos['num_factura_inicio'],
            'num_factura_final'         => $datos['num_factura_final'],
            'numero_facturas_alerta'    => $datos['numero_facturas_alerta'],
            'fecha_inicio'              => $datos['fecha_inicio'],
        );


        if (isset($datos['activo'])) {
            $datos_item['activo'] = '1';
            
        } else {
            $datos_item['activo'] = '0';
            
        }

        $resolucionActiva = $sql->existeItem('resoluciones', 'activo', '1', 'id_sede = "' . $datos['id_sede'] . '"');
        
        $sql->iniciarTransaccion();
        
        if ($resolucionActiva) {
            $datosRes = array('activo' => '0', 'fecha_final' => date('Y, m, d'));
            $query = $sql->modificar('resoluciones', $datosRes, 'activo = "1"');
            
            if (!$query) {
                $sql->cancelarTransaccion();
                return false;
            }
            
        }

        $consulta = $sql->insertar('resoluciones', $datos_item);

        if ($consulta) {
            $id_item = $sql->ultimoId;
            /**
             * Si se va a retomar la resolucion desde un numero ya iniciado
             * se agrega una factura de venta al sistema, que hace las veces
             * de una factura de configuracion
             */
            if (!empty($datos['numero_retoma_facturacion'])) {
                
                /**
                 *ya que la sede se relaciona con la caja, capturamos la caja principal 
                 * haciendo uso de la sede 
                 */
                $idCaja = $sql->obtenerValor('cajas', 'id', 'principal = "1" AND id_sede = "'.$datos['id_sede'].'"');

                $datos_retoma_resolucion = array(
                    'id_factura'        => $datos['numero_retoma_facturacion'],
                    'id_resolucion'     => $id_item,
                    'id_cliente'        => '0',
                    'fecha_factura'     => date('Y-m-d'),
                    'id_usuario'        => $sesion_usuarioSesion->id,
                    'id_caja'           => $idCaja,
                    'estado_factura'    => '2',
                    'observaciones'     => str_replace('%1', $datos['numero'], $textos->id('FACTURA_PARAMETRIZACION_RETOMA_FACTURACION_RESOLUCION')),
                );

                $insert = $sql->insertar('facturas_venta', $datos_retoma_resolucion); 
                
                if (!$insert) {
                    $sql->cancelarTransaccion();
                    return false;
                }                
                
                //actualizo la resolucion con el id factura de parametrizacion
                $datos_id_factura = array(
                    'id_factura_retoma' => $sql->ultimoId
                );
                
                $update = $sql->modificar('resoluciones', $datos_id_factura, 'id = "' . $id_item . '"');
                
                if (!$update) {
                    $sql->cancelarTransaccion();
                    return false;
                }                 
                
            }

            $sql->finalizarTransaccion();
            return $id_item;
            
        } else {
            $sql->cancelarTransaccion();
            return false;
            
        }
        
    }

    /**
     * Modificar una resolucion
     *
     * @param  arreglo $datos       Datos de la resolucion a modificar
     * @return lógico               Indica si el procedimiento se pudo realizar correctamente o no
     *
     */
    public function modificar($datos) {
        global $sql, $sesion_usuarioSesion, $textos;

        if (!isset($this->id)) {
            return NULL;
        }

        $datos_item = array(
            'id_sede'                   => $datos['id_sede'],
            'prefijo'                   => $datos['prefijo'],
            'numero'                    => $datos['numero'],
            'fecha_resolucion'          => $datos['fecha_resolucion'],
            'num_factura_inicio'        => $datos['num_factura_inicio'],
            'num_factura_final'         => $datos['num_factura_final'],
            'numero_facturas_alerta'    => $datos['numero_facturas_alerta'],
            'fecha_inicio'              => $datos['fecha_inicio'],
            'fecha_final'               => $datos['fecha_final'],
        );
        
        $sql->iniciarTransaccion();

        if (isset($datos['activo'])) {
            $datos_item['activo'] = '1';
            
            $datosRes = array('activo' => '0', 'fecha_final' => date('Y, m, d'));
            
            $update = $sql->modificar('resoluciones', $datosRes, 'activo = "1" AND id_sede = "'.$datos['id_sede'].'"');
            
            if (!$update) {
                $sql->cancelarTransaccion();
                return false;
            }             
            
        } else {
            $datos_item['activo'] = '0';
            
        }

        //$sql->depurar = true;
        $consulta = $sql->modificar('resoluciones', $datos_item, 'id = "' . $this->id . '"');

        if ($consulta) {
            
            if (!empty($datos['numero_retoma_facturacion'])) {
                
                $idCaja = $sql->obtenerValor('cajas', 'id', 'principal = "1" AND id_sede = "'.$datos['id_sede'].'"');
                
                $datos_retoma_resolucion = array(
                    'id_factura'        => $datos['numero_retoma_facturacion'],
                    'id_resolucion'     => $this->id,
                    'id_cliente'        => '0',
                    'fecha_factura'     => date('Y-m-d'),
                    'id_usuario'        => $sesion_usuarioSesion->id,
                    'id_caja'           => $idCaja,
                    'estado_factura'    => '2',
                    'observaciones'     => str_replace('%1', $datos['numero'], $textos->id('FACTURA_PARAMETRIZACION_RETOMA_FACTURACION_RESOLUCION')),
                );        
                
                $idRetoma = '';
                
                if (!empty($this->idFacturaRetoma)) {
                    $update3  = $sql->modificar('facturas_venta', $datos_retoma_resolucion, 'id = "'.$this->idFacturaRetoma.'"');

                    if (!$update3) {
                        $sql->cancelarTransaccion();
                        return false;
                    }       
                    
                    $idRetoma = $this->idFacturaRetoma;
                    
                } else {

                    $insert = $sql->insertar('facturas_venta', $datos_retoma_resolucion); 

                    if (!$insert) {
                        $sql->cancelarTransaccion();
                        return false;
                    }     
                    
                    $idRetoma = $sql->ultimoId;
                    
                }
                
                //actualizo la resolucion con el id factura de parametrizacion
                $datos_id_factura = array(
                    'id_factura_retoma' => $idRetoma
                );
                
                $update = $sql->modificar('resoluciones', $datos_id_factura, 'id = "' . $this->id . '"');
                
                if (!$update) {
                    $sql->cancelarTransaccion();
                    return false;
                }                 
                
            }     

             $sql->finalizarTransaccion();
            return true;
        
        } else {
             $sql->cancelarTransaccion();
            return false;
            
        }
        
    }

    /**
     *
     * Eliminar una resolucion (realmente solo se puede inactivar por integridad de informacion nada debe ser eliminado)
     *
     * @param entero $id    Código interno o identificador de una resolucion en la base de datos
     * @return lógico       Indica si el procedimiento se pudo realizar correctamente o no
     *
     */
    public function eliminar() {
        global $sql, $configuracion, $textos;
        
        //arreglo que será devuelto como respuesta
        $respuestaEliminar = array(
            'respuesta' => false,
            'mensaje'   => $textos->id('ERROR_DESCONOCIDO'),
        );
        
        if (!isset($this->id)) {
            return $respuestaEliminar;
        }

        $sql->iniciarTransaccion();
        $consulta = $sql->eliminar('resoluciones', 'id = "'.$this->id.'"');
        
        if (!($consulta)) {
            $sql->cancelarTransaccion("Fallo en el archivo " . __FILE__ . " en la linea " .  __LINE__);
            return $respuestaEliminar;
            
        } else {
            $sql->finalizarTransaccion();
            //todo salió bien, se envia la respuesta positiva
            $respuestaEliminar['respuesta'] = true;
            return $respuestaEliminar;
            
        }//fin del si funciono eliminar
        
    }//Fin del metodo eliminar objeto
    
   /* public function eliminar() {
        global $sql;

        if (!isset($this->id)) {
            return NULL;
        }

        $datosRes = array('activo' => '0', 'fecha_final' => date('Y, m, d'));

        $update = $sql->modificar('resoluciones', $datosRes, 'id = "'.$this->id.'"');

        if (!$update) {
            $sql->cancelarTransaccion();
            return false;
        }   

        return true;

    }*/

    /**
     *
     * Listar las resoluciones
     *
     * @param entero  $cantidad    Número de resoluciones a incluir en la lista (0 = todas las entradas)
     * @param arreglo $excepcion   Arreglo con los códigos internos o identificadores a omitir en la lista
     * @param cadena  $condicion   Condición adicional (SQL)
     * @return arreglo             Lista de resoluciones
     *
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
            $condicion = 'r.id NOT IN (' . $excepcion . ') AND';
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
            'r' => 'resoluciones',
            'se' => 'sedes_empresa'
        );

        $columnas = array(
            'id'                    => 'r.id',
            'idSede'                => 'r.id_sede',
            'sede'                  => 'se.nombre',
            'prefijo'               => 'r.prefijo',
            'numero'                => 'r.numero',
            'fechaResolucion'       => 'r.fecha_resolucion',
            'numeroFacturaInicial'  => 'r.num_factura_inicio',
            'numeroFacturaFinal'    => 'r.num_factura_final',
            'fechaInicial'          => 'r.fecha_inicio',
            'fechaFinal'            => 'r.fecha_final',
            'activo'                => 'r.activo'
        );

        if (!empty($condicionGlobal)) {
            $condicion .= $condicionGlobal . ' AND ';
        }

        $condicion .= ' r.id_sede = se.id';


        if (is_null($this->registrosConsulta)) {
            $sql->seleccionar($tablas, $columnas, $condicion);
            $this->registrosConsulta = $sql->filasDevueltas;
        }
        //$sql->depurar = true;
        $consulta = $sql->seleccionar($tablas, $columnas, $condicion, 'r.id', $orden, $inicio, $cantidad);

        if ($sql->filasDevueltas) {
            $lista = array();

            while ($objeto = $sql->filaEnObjeto($consulta)) {
                $objeto->estado = ($objeto->activo) ? HTML::frase($textos->id('ACTIVO'), 'activo') : HTML::frase($textos->id('INACTIVO'), 'inactivo');

                $lista[] = $objeto;
            }
        }

        return $lista;
    }

    /**
     * 
     * @global type $textos
     * @param type $arregloRegistros
     * @param type $datosPaginacion
     * @return type
     */
    public function generarTabla($arregloRegistros, $datosPaginacion = NULL) {
        global $textos;
        //Declaracion de las columnas que se van a mostrar en la tabla
        $datosTabla = array(
            HTML::parrafo($textos->id('SEDE_RESOLUCION'), 'centrado')       => 'sede|s.nombre',
            HTML::parrafo($textos->id('PREFIJO'), 'centrado')               => 'prefijo|r.prefijo',
            HTML::parrafo($textos->id('NUMERO'), 'centrado')                => 'numero|r.numero',
            HTML::parrafo($textos->id('FECHA_RESOLUCION'), 'centrado')      => 'fechaResolucion|r.fecha_resolucion',
            HTML::parrafo($textos->id('FACTURA_INICIAL'), 'centrado')       => 'numeroFacturaInicial|r.num_factura_inicio',
            HTML::parrafo($textos->id('FACTURA_FINAL'), 'centrado')         => 'numeroFacturaFinal|r.num_factura_final',
            HTML::parrafo($textos->id('FECHA_INICIAL'), 'centrado')         => 'fechaInicial|r.fecha_final',
            HTML::parrafo($textos->id('FECHA_FINAL'), 'centrado')           => 'fechaFinal|r.fecha_final',
            HTML::parrafo($textos->id('ESTADO'), 'centrado')                => 'estado'
        );
        //ruta a donde se mandara la accion del doble click
        $ruta = '/ajax' . $this->urlBase . '/move';

        $estilosColumnas = array('columna1', 'columna2', 'columna3', 'columna4', 'columna5', 'columna6', 'columna7', 'columna8', 'columna9');

        return Recursos::generarTablaRegistros($arregloRegistros, $datosTabla, $ruta, $datosPaginacion, $estilosColumnas) . HTML::crearMenuBotonDerecho('RESOLUCIONES');
    }

}
