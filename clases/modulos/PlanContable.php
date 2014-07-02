<?php

/**
 *
 * @package     FOLCS
 * @subpackage  Plan contable
 * @author      Julian Mondragón <bugshoo@gmail.com>
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2013 almotos
 * @version     0.1
 *
 **/
class PlanContable {

    /**
     * Código interno o identificador del país en la base de datos
     * @var entero
     */
    public $id;

    /**
     * URL relativa del módulo de plan contable
     * @var cadena
     */
    public $urlBase;

    /**
     * URL relativa de una cuenta específica
     * @var cadena
     */
    public $url;
    
     /**
     * Código interno o identificador del modulo
     * @var entero
     */
    public $idModulo;
    
    /**
     * Código de la cuenta en el plan contable
     * @var cadena
     */
    public $codigoContable;
    
    /**
     * Descripcion
     * @var cadena
     */
    public $descripcion;
    
    /**
     * Nombre de la cuenta
     * @var cadena
     */
    public $nombre;    
    
    /**
     * Id de la cuenta padre, si aplica
     * @var cadena
     */
    public $idCuentaPadre;
    
    /**
     * Nombre de la cuenta padre, si aplica
     * @var cadena
     */
    public $cuentaPadre;
    
    /**
     * Nivel de la cuenta dentro de la estructura
     * @var cadena
     */
    public $nivel;
    
    /**
     * Naturaleza de la cuenta
     * @var cadena
     */
    public $naturaleza;
    
    /**
     * Clase de cuenta
     * @var cadena
     */
    public $clase;
    
    /**
     * Tipo de cuenta
     * @var cadena
     */
    public $tipo;
    
    /**
     * Id del Anexo contable asignado
     * @var cadena
     */
    public $idAnexoContable;
    
    /**
     * Nombre del Anexo contable asignado
     * @var cadena
     */
    public $anexoContable;
    
    /**
     * Cuenta de impuestos o gravamenes 1
     * @var cadena
     */
    public $idTasa1;
    
    /**
     * Nombre de la Cuenta de impuestos o gravamenes 1
     * @var cadena
     */
    public $tasa1;
    
    /**
     * Cuenta de impuestos o gravamenes 2
     * @var cadena
     */
    public $idTasa2;
    
    /**
     * Nombre de la Cuenta de impuestos o gravamenes 2
     * @var cadena
     */
    public $tasa2;
    
    /**
     * Concepto asignado por DIAN para medios magneticos
     * @var cadena
     */
    public $idConceptoDIAN;
    
    /**
     * Nombre del Concepto asignado por DIAN para medios magneticos
     * @var cadena
     */
    public $conceptoDIAN;
    
    /**
     * Cuenta de retencione para las cuales se requiere expedir el certificado a terceros, 1->No aplica, 2-> Retención en la fuente 3-> industria y comercio (ica), 4-> Retención de iva'
     * @var cadena
     */
    public $tipoCertificado;
    
    /**
     * Afecta flujo de efectivo 1->No afecta flujo 2->Caja 3->Bancos
     * @var cadena
     */
    public $flujoEfectivo;
    
    /**
     * Indicador del orden de la lista de cuentas
     * @var lógico
     */
    public $listaAscendente = true;

    /**
     * Número de registros de la lista
     * @var entero
     */
    public $registros = NULL;

        /**
     * Número de registros activos de la lista de cuentas
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
     * Inicializar el plan de cuentas
     *
     * @param entero $id Código interno o identificador de la cuenta en la base de datos
     *
     */
    public function __construct($id = NULL) {
        global $sql;

        $modulo                 = new Modulo("PLAN_CONTABLE");
        $this->urlBase          = "/".$modulo->url;
        $this->url              = $modulo->url;
        $this->idModulo         = $modulo->id;
        //Saber el numero de registros
        $this->registros        = $sql->obtenerValor("plan_contable", "COUNT(id)", "id != '0'");  
        //establecer el valor del campo predeterminado para organizar los listados
        $this->ordenInicial     = "codigo_contable";           

        if (isset($id)) {
            $this->cargar($id);
            
        }
        
    }

    /**
     *
     * Cargar los datos de una cuenta
     *
     * @param entero $id Código interno o identificador del país en la base de datos
     *
     */
    public function cargar($id) {
        global $sql;

        if (isset($id) && $sql->existeItem("plan_contable", "id", intval($id))) {

            $tablas = array(
                "plan_contable"
            );

            $columnas = array(
                "id",
                "codigoContable"    => "codigo_contable",
                "descripcion",
                "nombre",
                "idCuentaPadre"     => "id_cuenta_padre",
                "nivel",
                "naturaleza"        => "naturaleza_cuenta",
                "clase"             => "clase_cuenta",
                "tipo"              => "tipo_cuenta",
                "idAnexoContable"   => "id_anexo_contable",
                "idTasa1"           => "id_tasa_aplicar_1",
                "idTasa2"           => "id_tasa_aplicar_2",
                "idConceptoDIAN"    => "id_concepto_DIAN",
                "tipoCertificado"   => "tipo_certificado",
                "flujoEfectivo"     => "flujo_efectivo"
            );

            $condicion = "id = '$id'";

            $sql->depurar = true;
            $consulta = $sql->seleccionar($tablas, $columnas, $condicion);

            if ($sql->filasDevueltas) {
                $fila = $sql->filaEnObjeto($consulta);

                foreach ($fila as $propiedad => $valor) {
                    $this->$propiedad = $valor;
                }
                
                $this->cuentaPadre        = new PlanContable($this->idCuentaPadre);
//                $this->anexoContable    = $sql->obtenerValor('anexos_contables', 'nombre', "id = '$this->idAnexoContable'");
//                $this->tasa1            = $sql->obtenerValor('tasas', 'nombre', "id = '$this->idTasa1'");
//                $this->tasa2            = $sql->obtenerValor('tasas', 'nombre', "id = '$this->idTasa2'");
//                $this->conceptoDIAN     = $sql->obtenerValor('conceptos_DIAN', 'nombre', "id = '$this->idConceptoDIAN'");

                $this->url = $this->urlBase."/".$this->id;
            }
            
        }
        
    }

    /**
     *
     * Adicionar una cuenta
     *
     * @param  arreglo $datos       Datos de la cuenta a adicionar
     * @return entero               Código interno o identificador de la cuenta en la base de datos (NULL si hubo error)
     *
     */
    public function adicionar($datos) {
        global $sql;

        $consulta = $sql->insertar("plan_contable", $datos);

        if ($consulta) {
            return $sql->ultimoId;

        } else {
            return NULL;
            
        }
        
    }
    
    /**
     * Adicionar subgrupos a partir de un archivo excel
     *
     * @param  arreglo $datos       Datos del subgrupo a adicionar
     * @return entero               Código interno o identificador del subgrupo en la base de datos (NULL si hubo error)
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
                    
                    if ($datos['codigo'] != 0)
                        $campos['codigo'] = $datos['codigo'];   
                    
                    if ($datos['nombre'] != 0)
                        $campos['nombre'] = $datos['nombre'];
                    
                    if ($datos['clasificacion'] != 0)
                        $campos['clasificacion'] = $datos['clasificacion'];                      
                }

                $valor1    = $data->val($row, $col);
                $respuesta = array();
                
                $test = array();

                while ($valor1 != null) {
                    if ($datos['inicial'] == 0) {
                        $respuesta[$col] = $valor1;
                        $col++;
                        
                    } else {
                        $datosInsert = array();
                        
                        foreach ($campos AS $nombre => $valor) {

                            $valor = $data->val($row, $valor);
                            
                            if ($nombre == 'codigo') {
                                $datosInsert['codigo_contable'] = $valor;
                                
                                $test[] = $valor;
                                
                                //convertir el codigo en un arreglo para las siguientes operaciones
                                $arrCodigo = str_split($valor);                                
                                //1)saber la naturaleza de la cuenta (capturar el primer digito)
                                $naturaleza = "D";                                
                                if (in_array($arrCodigo[0], array("2", "3", "4", "9"))) {//clases de cuenta de naturaleza credito
                                    $naturaleza = "C";
                                } 
                                $datosInsert['naturaleza_cuenta'] = $naturaleza;
                                
                                //2)saber cual es la cuenta padre
                                $tamCod = sizeof($arrCodigo);  
                                
                                $idPadre        = "";
                                $strCodPadre    = "";
                                 
                                
                                if ($tamCod == "1") {
                                    $idPadre = "0";
                                    
                                } else if ($tamCod == "2") {
                                    $strCodPadre = array_shift($arrCodigo);
                                    
                                } else {
                                    $arrCodPadre = array_slice($arrCodigo, 0, ($tamCod - 2));
                                    $strCodPadre = implode("", $arrCodPadre);                                    
                                    
                                }
                                
                                if ($idPadre !== "0") {
                                    $idPadre = $sql->obtenerValor("plan_contable", "id", "codigo_contable = '". $strCodPadre ."'");
                                }
                                
                                $datosInsert['id_cuenta_padre'] = $idPadre;
                                           
                                
                            } 
                            
                            if ($nombre == 'nombre') {
                                $datosInsert["nombre"] = $valor;
                                $datosInsert["descripcion"] = $valor;
                            }    
                            
                            if ($nombre == 'clasificacion') {
                                
                                $clasificacion = "";
                                
                                $valor = strtoupper($valor);
                                
                                switch ($valor) {
                                    case "CLASE"        :  $clasificacion = "K"; break;
                                    case "GRUPO"        :  $clasificacion = "G"; break;
                                    case "CUENTA"       :  $clasificacion = "C"; break;
                                    case "SUBCUENTA"    :  $clasificacion = "S"; break;
                                    case "AUXILIAR"     :  $clasificacion = "A"; break;
                                    default             :  $clasificacion = "K";
                                    
                                }
                                $datosInsert["clasificacion"] = $clasificacion;
                            }                            
                            
                        }
                        
                       $datosInsert['activo'] = '1';

                        $sql->insertar('plan_contable', $datosInsert);

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

    /**
     *
     * Modificar una cuenta
     *
     * @param  arreglo $datos       Datos de la cuenta a modificar
     * @return lógico               Indica si el procedimiento se pudo realizar correctamente o no
     *
     */
    public function modificar($datos) {
        global $sql;

        if (!isset($this->id)) {
            return false;
        }

        $consulta = $sql->modificar("plan_contable", $datos, "id = '".$this->id."'");
        
        return $consulta;
        
    }

    /**
     *
     * Eliminar una cuenta
     *
     * @param entero $id    Código interno o identificador de la cuenta en la base de datos
     * @return lógico       Indica si el procedimiento se pudo realizar correctamente o no
     *
     */
    public function eliminar() {
        global $sql;

        if (!isset($this->id)) {
            return false;
        }
        
        $consulta = $sql->eliminar("plan_contable", "id = '".$this->id."'");
        
        return $consulta;
        
    }

    /**
     *
     * Listar las cuentas
     *
     * @param entero  $cantidad    Número de cuentas a incluir en la lista (0 = todas las entradas)
     * @param arreglo $excepcion   Arreglo con los códigos internos o identificadores a omitir en la lista
     * @param cadena  $condicion   Condición adicional (SQL)
     * @return arreglo             Lista de cuentas
     *
     */
    public function listar($inicio = 0, $cantidad = 0, $excepcion = NULL, $condicionGlobal = NULL, $orden = NULL) {
        global $sql;

        /*** Validar la fila inicial de la consulta ***/
        if (!is_int($inicio) || $inicio < 0) {
            $inicio = 0;
        }

        /*** Validar la cantidad de registros requeridos en la consulta ***/
        if (!is_int($cantidad) || $cantidad <= 0) {
            $cantidad = 0;
        }

        /*** Validar que la condición sea una cadena de texto ***/
        $condicion = "";

        /*** Validar que la excepción sea un arreglo y contenga elementos ***/
        if (isset($excepcion) && is_array($excepcion) && count($excepcion)) {
            $excepcion = implode(",", $excepcion);
            $condicion .= "id NOT IN ($excepcion) ";
        }

        /*** Definir el orden de presentación de los datos ***/
        if(!isset($orden)){
            $orden = $this->ordenInicial;
        }
        if ($this->listaAscendente) {
            $orden = "$orden ASC";

        } else {
            $orden = "$orden DESC";
            
        }

        $tablas = array(
            "plan_contable",
        );

        $columnas = array(
            "id",
            "codigoContable"    => "codigo_contable",
            "descripcion",
            "naturaleza"        => "naturaleza_cuenta",
            "clasificacion"
        );
        
        if (!empty($condicionGlobal)) {
            if($condicion != ""){
                $condicion .= " AND ";
            }
            
            $condicion .= $condicionGlobal;
            
        }        
        
        if (is_null($this->registrosConsulta)) {
            $sql->seleccionar($tablas, $columnas, $condicion);
            $this->registrosConsulta = $sql->filasDevueltas;
            
        }

        $consulta = $sql->seleccionar($tablas, $columnas, $condicion, "", $orden, $inicio, $cantidad);

        $lista = array();
        
        if ($sql->filasDevueltas) {           

            while ($objeto = $sql->filaEnObjeto($consulta)) {                            
                $lista[]   = $objeto;
                
            }
            
        }

        return $lista;

    }
    
    
    public function generarTabla ($arregloRegistros, $datosPaginacion = NULL) {
        global $textos;
        //Declaracion de las columnas que se van a mostrar en la tabla
        $datosTabla = array(
            HTML::parrafo( $textos->id("CODIGO_CONTABLE")       ,  "centrado" ) => "codigoContable|codigo_contable",
            HTML::parrafo( $textos->id("DESCRIPCION")           ,  "centrado" ) => "descripcion|descripcion",
            HTML::parrafo( $textos->id("NATURALEZA_CUENTA")     ,  "centrado" ) => "naturaleza|naturaleza_cuenta",        
            HTML::parrafo( $textos->id("CLASIFICACION_CUENTA")  ,  "centrado" ) => "clasificacion|clasificacion"
        );        
        //ruta a donde se mandara la accion del doble click
        $ruta = "/ajax".$this->urlBase."/move";
        
        return Recursos::generarTablaRegistros($arregloRegistros, $datosTabla, $ruta, $datosPaginacion).HTML::crearMenuBotonDerecho("PLAN_CONTABLE");
        
    }    
    
}
