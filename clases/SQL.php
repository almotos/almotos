<?php

/**
 * Clase encargada de gestionar todo el proceso de conecxion con una base de datos.
 * 
 * @package     FOLCS
 * @subpackage  Base
 * @author      Francisco J. Lozano B. <fjlozano@felinux.com.co>
 * @author      Pablo Andrés Véelez. <pavelez@genesyscorp.com.co>
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2013 Genesys corporation
 * @version     0.2
 *
 * */

/**
 * Clase encargada de gestionar todo el proceso de conecxion con una base de datos.
 * A fecha 06/2013 Utiliza una base de datos MySql a traves del plugin conector MySqlLi.
 * Esta clase perfectamente puede cambiarse para hacer que la aplicacion trabaje 
 * con cualquier base de datos.
 */
class SQL {

    /**
     * Nombre o dirección IP del servidor de bases de datos MySQL
     * @var cadena
     */
    public $servidor;

    /**
     * Nombre de usuario para la conexión al servidor de bases de datos MySQL
     * @var cadena
     */
    public $usuario;

    /**
     * Contraseña del usuario para la conexión al servidor de bases de datos MySQL
     * @var cadena
     */
    public $contrasena;

    /**
     * Nombre de la base datos para la conexión al servidor de bases de datos MySQL
     * @var cadena
     */
    public $baseDatos;

    /**
     * Prefijo para las tablas y vistas del proyecto en la base de datos MySQL
     * @var cadena
     */
    public $prefijo;

    /**
     * Gestor de la conexión a la base de datos MySQL
     * @var recurso
     */
    public $conexion;

    /**
     * Objeto resultado devuelto al ejecutar un query
     * @var recurso
     */
    public $resultado;

    /**
     * Objeto resultado devuelto al ejecutar un query
     * @var recurso
     */
    public $prefijoDepuracion = '';

    /**
     * Número asignado para el último registro adicionado mediante incremento automático
     * @var entero
     */
    public $ultimoId;

    /**
     * Número de filas devueltas por una consulta
     * @var entero
     */
    public $filasDevueltas;

    /**
     * Número de filas afectadas por una consulta
     * @var recurso
     */
    public $filasAfectadas;

    /**
     * Número de consultas realizadas en cada página generada
     * @var entero
     */
    public $consultas;

    /**
     * Tiempo total empleado para las consultas realizadas (en segundos)
     * @var flotante
     */
    public $tiempo;

    /**
     * Tiempo total empleado para las consultas realizadas (en segundos)
     * @var flotante
     */
    public $sentenciaSql;

    /**
     * Depurar las consultas realizadas en la base de datos MySQL mediante los archivos de registro (logs)
     * @var lógico
     */
    public $depurar = false;
    
    /**
     * Variable que determina si un dato que va a la BD debe ser filtrado, por defecto se pone en true y si un modulo
     * determinado lo requiere, se pone en false antes del insert o del update, y al finalizar el inser o update, se debe
     * poner nuevamente en true.
     * @var lógico
     */
    public $filtrarDatos = true;    

    /**
     * Registro de error que determina si una operacion fue exitosa
     * @var lógico
     */
    public $error = false;

    /**
     * Determina si algunos parametros de la sentencia deben ser guardados en la bitacora
     * @var lógico
     */
    public $guardarBitacora = true;

    /**
     *
     * Inicializar la clase estableciendo una conexión con el servidor de bases de datos MySQL
     *
     * @param cadena $servidor      Nombre o dirección IP del servidor de bases de datos MySQL
     * @param cadena $usuario       Nombre de usuario para la conexión al servidor de bases de datos MySQL
     * @param cadena $contrasena    Contraseña del usuario para la conexión al servidor de bases de datos MySQL
     * @param cadena $nombre        Nombre de la base datos para la conexión al servidor de bases de datos MySQL
     * @return                      recurso
     *
     */
    function __construct($servidor = '', $usuario = '', $contrasena = '', $nombre = '') {
        global $configuracion;

        if (empty($servidor) && empty($usuario) && empty($contrasena) && empty($nombre)) {
            $this->servidor         = $configuracion['BASEDATOS']['servidor'];
            $this->usuario          = $configuracion['BASEDATOS']['usuario'];
            $this->contrasena       = $configuracion['BASEDATOS']['contraseña'];
            $this->baseDatos        = $configuracion['BASEDATOS']['nombre'];
            $this->prefijo          = $configuracion['BASEDATOS']['prefijo'];
            
        } else {
            $this->servidor         = $servidor;
            $this->usuario          = $usuario;
            $this->contrasena       = $contrasena;
            $this->baseDatos        = $nombre;
            $this->prefijo          = 'fom_';
            
        }

        $this->conectar();
    }

    /**
     *
     * Establecer una conexión con el servidor de bases de datos MySQL
     *
     * @param cadena $servidor      Nombre o dirección IP del servidor de bases de datos MySQL
     * @param cadena $usuario       Nombre de usuario para la conexión al servidor de bases de datos MySQL
     * @param cadena $contrasena    Contraseña del usuario para la conexión al servidor de bases de datos MySQL
     * @param cadena $nombre        Nombre de la base datos para la conexión al servidor de bases de datos MySQL
     * @return                      recurso
     *
     */
    public function conectar() {
        $this->conexion = new mysqli($this->servidor, $this->usuario, $this->contrasena, $this->baseDatos);
        if ($this->conexion->connect_errno) {
            echo 'Fallo al conectar a MySQL: (' . $this->conexion->connect_errno . ') ' . $this->conexion->connect_error;
        }
    }

    /**
     *
     * Finalizar una conexión con el servidor de bases de datos MySQL
     *
     * @param recurso $conexion     Gestor de la conexión a la base de datos MySQL
     * @return                      lógico
     *
     */
    public function desconectar($conexion = "") {

        if (empty($conexion)) {
            $cierre = $this->conexion->close();
            
        } else {
            $cierre = mysqli_close($conexion);
            
        }
    }

    /**
     *
     * Ejecutar una consulta en el servidor de bases de datos MySQL
     *
     * @param cadena $consulta      Instrucción SQL a ejecutar
     * @return                      recurso
     *
     */
    public function ejecutar($consulta) {
        global $modulo, $sesion_usuarioSesion;

        $this->consultas++;
        $this->filasDevueltas   = NULL;
        $this->filasAfectadas   = NULL;
        $horaInicio             = microtime(true);
        $this->resultado        = $this->conexion->query($consulta);
        $this->sentenciaSql     = $consulta;
        $horaFinalizacion       = microtime(true);
        $this->tiempo           += round($horaFinalizacion - $horaInicio, 4);

        if ((!empty($this->conexion->error)) || $this->depurar) {
            openlog('FOLCS', LOG_PID, LOG_LOCAL0);
            $log = syslog(LOG_DEBUG, $modulo->nombre . ' :: ' . $this->prefijoDepuracion . ' ' . $consulta);

            if (!empty($this->conexion->error)) {
                $log = syslog(LOG_DEBUG, $modulo->nombre . ' :: ' . $this->conexion->error);
            }

            $this->depurar = false;
        }

        if (preg_match("/^(SELECT|SHOW)/", $consulta) && !$this->conexion->errno) {
            $this->filasDevueltas = $this->resultado->num_rows;
        } else {
            $this->filasAfectadas = $this->conexion->affected_rows;

            //Funciones para guardar registro de actividades en la bitacora
//            if ($this->guardarBitacora) {
//                $tipo = '';
//                if (isset($sesion_usuarioSesion) && !empty($sesion_usuarioSesion->usuario)) {
//                    $username = $sesion_usuarioSesion->usuario;
//                } else {
//                    $username = 'sin sesion';
//                }
            if (preg_match("/INSERT/", $consulta)) {
//                    $tipo = 'INSERT';
                $this->ultimoId = $this->conexion->insert_id;
            }

//                else if (preg_match("/DELETE/", $consulta)) {
//                    $tipo = 'DELETE';
//                } else if (preg_match("/UPDATE/", $consulta)) {
//                    $tipo = 'UPDATE';
//                }
//
//                $sentencia = "INSERT INTO folcs_bitacora (usuario, ip, tipo, consulta, fecha, modulo) VALUES ('$username', '" . Recursos::getRealIP() . "', '$tipo', '" . addslashes($consulta) . "', '" . date('Y-m-d H:i:s') . "', '$modulo->nombre')";
//                $this->conexion->query($sentencia);
//            }
            //se pone nuevamente en true, poque pudo haber sido puesto en false en algun metodo de alguna clase
            $this->guardarBitacora      = true;
            $this->prefijoDepuracion    = '';
        }

        return $this->resultado;
    }

    /**
     * Escribir en un log personal a traves del objeto sql
     * 
     * @param type $texto 
     */
    public static function escribirTxt($texto) {
        $fecha = date("d/m/y H:i:s");
        $fp = fopen("errores.txt", "w");
        fwrite($fp, "Fecha: $fecha -> \n Variable: $texto  " . PHP_EOL);
        fclose($fp);
    }

    /**
     *
     * Convertir el recurso resultante de una consulta en un objeto
     *
     * @param recurso $resultado    Recurso resultante de una consulta
     * @return                      objeto
     *
     */
    public function filaEnObjeto($resultado = NULL) {
//        if (empty($resultado)) {
//            $fila = $this->resultado->fetch_object();
//        } else {
            $fila = mysqli_fetch_object($resultado);
//        }

        return $fila;
    }

    /**
     *
     * Convertir el recurso resultante de una consulta en un arreglo
     *
     * @param recurso $resultado    Recurso resultante de una consulta
     * @return                      arreglo
     *
     */
    public function filaEnArreglo($resultado = NULL) {
        if (empty($resultado)) {
            $fila = $this->resultado->fetch_array();
        } else {
            $fila = mysqli_fetch_array($resultado);
        }
        return $fila;
    }

    /**
     *
     * Convertir el recurso resultante de una consulta en un arreglo ASOCIATIVO
     *
     * @param recurso $resultado    Recurso resultante de una consulta
     * @return                      arreglo
     *
     */
    public function filaEnArregloAsoc($resultado = NULL) {
        if (empty($resultado)) {
            $fila = $this->resultado->fetch_assoc();
        } else {
            $fila = mysqli_fetch_assoc($resultado);
        }
        return $fila;
    }
    
    /**
     *
     * Inicializar una transaccion en la BD
     * 
     * @return  void
     */
    public function iniciarTransaccion() {
        $this->ejecutar("START TRANSACTION");
    }    
    
    /**
     *
     * finalizar una transaccion en la BD
     * 
     * @return  void
     */
    public function finalizarTransaccion() {
        $this->ejecutar("COMMIT");
    }   
    
    /**
     *
     * cancelar una transaccion en la BD
     * 
     * @return  void
     */
    public function cancelarTransaccion($textoError = "") {
        syslog(LOG_DEBUG, $textoError);
        $this->ejecutar("ROLLBACK");
        return false;
    }    

    /**
     * Seleccionar datos de una o varias tablas del servidor de bases de datos MySQL
     * 
     * @param string $tablas  tablas a las que se realiuzar la consulta (FROM $TABLAS)
     * @param string $columnas columnas que se van a consultar (SELECT $columnas)
     * @param string $condicion condicion sql de la consulta (WHERE)
     * @param string $agrupamiento agrupamiento de la consulta (GROUP BY)
     * @param string $ordenamiento (ORDER BY)
     * @param int $filaInicial (LIMIT $filaInicial, $numeroFilas)
     * @param int $numeroFilas
     * 
     * @return recurso
     */
    public function seleccionar($tablas, $columnas, $condicion = "", $agrupamiento = "", $ordenamiento = "", $filaInicial = NULL, $numeroFilas = NULL, $where = true) {
        $listaColumnas  = array();
        $listaTablas    = array();
        $limite         = "";

        if (!is_array($tablas)){
            $tablas = array($tablas);

        }
        
        if (!is_array($columnas)){
            $columnas = array($columnas);

        }

        foreach ($columnas as $alias => $columna) {

            if (preg_match("/(^[a-zA-z]+[a-zA-Z0-9]*)/", $alias)) {
                $alias = " AS $alias";
            } else {
                $alias = '';
            }

            $listaColumnas[] = $columna . $alias;
        }

        $columnas = implode(', ', $listaColumnas);

        foreach ($tablas as $alias => $tabla) {

            if (preg_match("/(^[a-zA-z]+[a-zA-Z0-9]*)/", $alias)) {
                $alias = ' AS ' . $alias;
            } else {
                $alias = '';
            }

            $tabla = $this->prefijo . $tabla;
            $listaTablas[] = $tabla . $alias;
        }

        if (!empty($condicion)) {
            $cond = ($where) ? ' WHERE ' : '';
            $condicion = $cond . $condicion;
        }

        if (!empty($agrupamiento)) {
            $agrupamiento = ' GROUP BY ' . $agrupamiento;
        }

        if (!empty($ordenamiento)) {
            $ordenamiento = ' ORDER BY ' . $ordenamiento;
        }

        if (is_int($numeroFilas) && $numeroFilas > 0) {
            $limite = ' LIMIT ';

            if (is_int($filaInicial) && $filaInicial >= 0) {
                $limite .= "$filaInicial, ";
            }

            $limite .= $numeroFilas;
        }

        $tablas = implode(', ', $listaTablas);
        $sentencia = 'SELECT ' . $columnas . ' FROM ' . $tablas . $condicion . $agrupamiento . $ordenamiento . $limite;

        $this->sentenciaSql = $sentencia;

        return $this->ejecutar($sentencia);
    }

    /**
     * Insertar datos en una tabla de la BD
     *
     * @param string $tabla tabla en la cual se van a ingresar los datos
     * @param array $datos arreglo de datos a ser ingresados (key = nombre de la columna en la BD, value = valor a ser insertado)
     * @return boolean true or false dependiendo del exito de la operación 
     */
    public function insertar($tabla, $datos) {
        $tabla = $this->prefijo . $tabla;

        if (is_array($datos) && count($datos) > 0) {

            foreach ($datos as $campo => $valor) {

                if ($valor != '') {
                    $campos[] = $campo;

                    if (Variable::contieneUTF8($valor)) {
                        $valor = Variable::codificarCadena($valor);
                    }
                    if ($this->filtrarDatos) {
                        $valores[] = '"' . Variable::filtrarTagsInseguros($valor) . '"';
                        
                    } else {
                        $valores[] = "'$valor'";
                        
                    }
                    
                }
            }

            $campos = implode(",", $campos);
            $valores = implode(",", $valores);
            $sentencia = "INSERT INTO $tabla ($campos) VALUES ($valores)";
        }

        $resultado = $this->ejecutar($sentencia);

        return $resultado;
    }

    /**
     * Reemplazar datos existentes en la tabla o insertarlos si no existen
     *
     * @param string $tabla tabla en la cual se van a insertar los datos
     * @param array $datos datos a ser insertados
     * @return boolean true or false dependiendo del exito de la operación
     */
    public function reemplazar($tabla, $datos) {

        $tabla = $this->prefijo . $tabla;

        if (is_array($datos) && count($datos) > 0) {

            foreach ($datos as $campo => $valor) {
                $campos[] = $campo;

                if (Variable::contieneUTF8($valor)) {
                    $valor = Variable::codificarCadena($valor);
                }

                $valores[] = "'$valor'";
            }

            $campos = implode(", ", $campos);
            $valores = implode(", ", $valores);
            $sentencia = "REPLACE INTO $tabla ($campos) VALUES ($valores)";
        }

        $resultado = $this->ejecutar($sentencia);

        return $resultado;
    }

    /**
     * Modificar datos existentes en la tabla de acuerdo con una condición
     * 
     * @param string $tabla tabla en la cual se van a modificar los datos
     * @param array $datos datos a ser utilizados para realizar la modificación de los datos
     * @param string $condicion condición que determina que registros van a ser modificados
     * @return boolean true or false dependiendo del exito de la operación 
     */
    public function modificar($tabla, $datos, $condicion) {
        $tabla = $this->prefijo . $tabla;

        if (is_array($datos) && count($datos) > 0) {
            $campos = array();

            foreach ($datos as $campo => $valor) {

                if ($valor != "") {

                    if (Variable::contieneUTF8($valor)) {
                        $valor = Variable::codificarCadena($valor);
                    }

                    if ($this->filtrarDatos) {
                        $valores[] = "$campo= '" . Variable::filtrarTagsInseguros($valor) . "'";
                        $campos["$campo"] = '"' . Variable::filtrarTagsInseguros($valor) . '"';
                    } else {
                        $valores[] = "$campo='$valor'";
                        $campos["$campo"] = "'$valor'";
                    }
                } else {
                    $valores[] = "$campo=NULL";
                    $campos["$campo"] = "NULL";
                }
            }

            $valores = implode(", ", $valores);
            $sentencia = "UPDATE $tabla SET $valores WHERE $condicion";
        }

        $resultado = $this->ejecutar($sentencia);

        return $resultado;
    }

    /**
     * Eliminar datos de una tabla que coincidan con una condición
     *
     * @param string $tabla tabla en la que se va a eliminar la información
     * @param string $condicion condición a ser utilizada para realizar la eliminación de la información
     * @return boolean true or false dependiendo del exito de la operación
     */
    public function eliminar($tabla, $condicion) {
        
        if ($tabla == "facturas_compras" || $tabla == "facturas_venta") {
            return false;
        }
        
        $tabla = $this->prefijo . $tabla;
        $sentencia = "DELETE FROM $tabla WHERE $condicion";

        $resultado = $this->ejecutar($sentencia);

        return $resultado;
    }

    /**
     * Verificar si un registro con un valor específico existe en una tabla
     *
     * @param string $tabla tabla en la cual se va realizar la consulta
     * @param string $columna columna utilizada para realizar la comparación de la existencia del registro
     * @param string $valor valor a ser verificada su existencia en la tabla y en la columna pasadas como parametro
     * @param string $condicionExtra cadena que representa una condición extra a ser utilizada en la consulta
     * @return boolean true or false dependiendo del exito de la operación
     */
    public function existeItem($tabla, $columna, $valor, $condicionExtra = "") {
        $tablas = array($tabla);
        $columnas = array($columna);
        $condicion = "$columna = '$valor'";

        if (!empty($condicionExtra)) {
            $condicion .= " AND $condicionExtra";
        }

        $this->seleccionar($tablas, $columnas, $condicion);

        if ($this->filasDevueltas) {
            return TRUE;
            
        } else {
            return FALSE;
            
        }
    }

    /**
     * Obtener el valor de un campo en una tabla cuyo registro (único) coincida con una condición dada
     * 
     * @param string $tabla  tabla a la que se va a realizar la consulta
     * @param string $columna columna de la condicion
     * @param string $condicion condicion para realizar la consulta
     * @return boolean true or false dependiendo del exito de la operación
     */
    public function obtenerValor($tabla, $columna, $condicion) {
        $tablas = array($tabla);
        $columnas = array($columna);
        //$this->depurar = true;
        $consulta = $this->seleccionar($tablas, $columnas, $condicion);

        if ($this->filasDevueltas == 1) {
            $datos = $this->filaEnObjeto($consulta);
            $valor = $datos->$columna;
            return $valor;
            
        } else {
            return FALSE;
            
        }
    }


}
