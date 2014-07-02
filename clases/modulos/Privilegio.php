<?php

/**
 *
 * @package     FOLCS
 * @subpackage  Privilegios
 * @author      Francisco J. Lozano c. <fjlozano@felinux.com.co>
 * @author      Julian A. Mondragón <jmondragon@felinux.com.co>
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2009 FELINUX LTDA
 * @version     0.1
 *
 **/

/**
 * 
 */
class Privilegios {

    /**
     * Código interno o identificador del usuario de los privilegios en la base de datos
     * @var entero
     */
    public $id;

    /**
     * Valor numérico que determina el orden o la posición del usuario en la base de datos
     * @var entero
     */
    public $orden;

    /**
     * URL relativa del módulo de privilegios
     * @var cadena
     */
    public $urlBase;

    /**
     * URL relativa de un usuario con privilegios específico
     * @var cadena
     */
    public $url;

    /**
     * Nombre del usuario
     * @var cadena
     */
    public $usuario;
    
    /* Nombre de la sede
     * @var cadena
     */
    public $sede;
    
    /* Id de la sede
     * @var int
     */
    public $id_sede;

    /**
     * Nombre real del usuario
     * @var cadena
     */
    public $nombre;
    
    /**
     * Orden predeterminado para organizar los listados
     * @var entero
     */
    public $ordenInicial = NULL;    

    /**
     * Indicador del orden cronológio de la lista de privilegios
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
    public $registrosConsulta = NULL;    

    /**
     *
     * Inicializar los privilegios
     *
     * @param entero $id Código interno o identificador de los privilegios en la base de datos
     *
     */
    public function __construct($id = NULL) {
        global $modulo, $sql;
        
        $this->urlBase              = "/".$modulo->url;
        $this->url                  = $modulo->url;
        $this->ordenInicial         = 'u.usuario';
        $sql->depurar = true;
        $this->registros = $sql->obtenerValor('permisos_modulos_usuarios', 'COUNT(DISTINCT(id_usuario))', 'id_usuario != 0');        
        
        if (isset($id)) {
            $this->cargar($id);
        }
    }

    /**
     *
     * Cargar los datos de los privilegios
     *
     * @param entero $id Código interno o identificador del perfil en la base de datos
     *
     */
    public function cargar($id) {
        global $sql;
        
        $datos   = explode('-', $id);
        $id      = $datos[0];
        $id_sede = $datos[1];
        
        if (isset($id) && $sql->existeItem("usuarios", "id", intval($id))) {

       	    $tablas = array(
            	"u"     => "usuarios",
            	"pmu"   => "permisos_modulos_usuarios",
	        "p"     => "personas",
      	        "s"     => "sedes_empresa",
            );

            $columnas = array(
                "id"        => "u.id",
                "id_sede"   => "s.id",
                "usuario"   => "u.usuario",
                "nombre"    => "REPLACE(TRIM(CONCAT(p.primer_nombre,' ',p.segundo_nombre,' ',p.primer_apellido,' ',p.segundo_apellido)),'  ', ' ')",
                "sede"      => "s.nombre"
            );

	    $condicion = "pmu.id_usuario = u.id AND u.id = '$id' AND pmu.id_sede = s.id AND s.id = '$id_sede' AND u.id_persona = p.id AND p.id != 0";
	    
	    $agrupar = "u.id";

            $consulta = $sql->seleccionar($tablas, $columnas, $condicion, $agrupar);

            if ($sql->filasDevueltas) {
                $fila = $sql->filaEnObjeto($consulta);
                foreach ($fila as $propiedad => $valor) {
                    $this->$propiedad = $valor;
                }
                $this->url = $this->urlBase."/".$this->usuario;
                
            }
        }
    }

    /**
     *
     * Adicionar los privilegios
     *
     * @param  arreglo $datos       Datos de los privilegios a adicionar
     * @return entero               Código interno o identificador del perfil en la base de datos (NULL si hubo error)
     *
     */
    public function adicionar($datos) {
        global $sql;

        foreach ($datos["privilegios"] as $privilegio) {
            $datos_privilegio = explode("|", $privilegio);
            if ($datos_privilegio[1] == 'M') {
                $consulta  = $sql->insertar("permisos_modulos_usuarios", array("id_modulo" => $datos_privilegio[0], "id_sede" => $datos["sede"], "id_usuario" => $datos["usuario"]));
            } else {
                $consulta  = $sql->insertar("permisos_componentes_usuarios", array("id_componente" => $datos_privilegio[0], "id_sede" => $datos["sede"], "id_usuario" => $datos["usuario"]));
            }
        }

        if ($consulta) {
            return true;
        } else {
            return NULL;
        }
    }

    /**
     *
     * Modificar los privilegios
     *
     * @param  arreglo $datos       Datos de los privilegios a modificar
     * @return lógico               Indica si el procedimiento se pudo realizar correctamente o no
     *
     */
    public function modificar($datos) {
        global $sql;

        if (!isset($this->id)) {
            return NULL;
        }

        $sql->eliminar("permisos_componentes_usuarios", "id_usuario = '".$this->id."' AND id_sede = '".$this->id_sede."'");
        $sql->eliminar("permisos_modulos_usuarios", "id_usuario = '".$this->id."' AND id_sede = '".$this->id_sede."'");

        foreach ($datos["privilegios"] as $privilegio) {
            $datos_privilegio = explode("|", $privilegio);
            if ($datos_privilegio[1] == 'M') {
                $consulta  = $sql->insertar("permisos_modulos_usuarios", array("id_modulo" => $datos_privilegio[0], "id_sede" => $this->id_sede, "id_usuario" => $this->id));
            } else {
                $consulta  = $sql->insertar("permisos_componentes_usuarios", array("id_componente" => $datos_privilegio[0], "id_sede" => $this->id_sede, "id_usuario" => $this->id));
            }
        }

        if ($consulta) {
            return true;
        } else {
            return NULL;
        }
    }

    /**
     *
     * Eliminar los privilegios
     *
     * @return lógico       Indica si el procedimiento se pudo realizar correctamente o no
     *
     */
    public function eliminar() {
        global $sql;

        if (!isset($this->id)) {
            return NULL;
        }
        
        $sql->iniciarTransaccion();

        $query1 = $sql->eliminar("permisos_componentes_usuarios", "id_usuario = '".$this->id."' AND id_sede = '".$this->id_sede."'");
        
        if (!$query1) {
            $sql->cancelarTransaccion();
            return false;
        }
        
        $query2 = $sql->eliminar("permisos_modulos_usuarios", "id_usuario = '".$this->id."' AND id_sede = '".$this->id_sede."'");
        
        if (!$query2) {
            $sql->cancelarTransaccion();
            return false;
        }
        
        $sql->finalizarTransaccion();
        
        return true;
        
    }

    /**
     *
     * Listar los usuarios que tiene privilegios
     *
     * @param entero  $cantidad    Número de usuarios a incluir en la lista (0 = todas las entradas)
     * @return arreglo             Lista de usuarios con privilegios
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
        
        /*         * * Validar que la excepción sea un arreglo y contenga elementos ** */
        if (isset($excepcion) && is_array($excepcion)) {
            $excepcion = implode(',', $excepcion);
            $condicion .= 'u.id NOT IN (' . $excepcion . ') AND ';
        }   
        
        if (!empty($condicionGlobal)) {
            $condicion .= $condicionGlobal . ' AND ';
        }        

        /*** Validar que la condición sea una cadena de texto ***/
        $condicion .= "pmu.id_usuario = u.id AND pmu.id_sede = s.id AND u.id_persona = p.id AND p.id != 0";

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
            "u"     => "usuarios",
            "pmu"   => "permisos_modulos_usuarios",
            "p"     => "personas",
            "s"     => "sedes_empresa",
        );

        $columnas = array(
            "id"                => "u.id",
            "id_sede"           => "s.id",
            "usuario"           => "u.usuario",
            "nombre"            => "p.primer_nombre",
            "apellido"          => "p.primer_apellido",
            "sede"              => "s.nombre"
        );

        $agrupar = "u.id, s.id";

        if (is_null($this->registrosConsulta)) {
            $sql->seleccionar($tablas, $columnas, $condicion, $agrupar);
            $this->registrosConsulta = $sql->filasDevueltas;
        }

        //$sql->depurar = true;
        $consulta = $sql->seleccionar($tablas, $columnas, $condicion, $agrupar, $orden, $inicio, $cantidad);
        if ($sql->filasDevueltas) {
            $lista = array();
            while ($privilegios = $sql->filaEnObjeto($consulta)) {
                $privilegios->url = $this->urlBase."/".$privilegios->id;
                $lista[] = $privilegios;
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
            HTML::parrafo($textos->id("USUARIO"),   "centrado") => "usuario|u.usuario",
            HTML::parrafo($textos->id("NOMBRE"),    "centrado") => "nombre|p.primer_nombre",
            HTML::parrafo($textos->id("APELLIDO"),  "centrado") => "apellido|p.primer_apellido",
            HTML::parrafo($textos->id("ID_SEDE"),  "centrado")  => "id_sede|s.id",
            HTML::parrafo($textos->id("SEDE"),  "centrado")     => "sede|s.nombre",
        );
        //ruta a donde se mandara la accion del doble click
        $ruta = "/ajax" . $this->urlBase . "/move";

        $tabla = Recursos::generarTablaRegistros($arregloRegistros, $datosTabla, $ruta, $datosPaginacion);
        $menuDerecho = HTML::crearMenuBotonDerecho("PRIVILEGIOS");

        return $tabla . $menuDerecho;
    }    
    

    /*** Genera lista HTML y crea otras opciones a partir de ella ***/
    public static function listaPrivilegios($usuario, $sede, $evento = "") {
        $arbol  = "";
        $arbol .= "<ul class=\"vistaArbol\">\n";
        $arbol .= self::arbolPrivilegios($evento, $usuario, $sede, $elemento = "");
        $arbol .= "</ul>\n";
        $arbol .= "<div class='imagen_cargando'></div>\n";
        return $arbol;
    }

    /*** Requerida por self::listaPrivilegios() ***/
    public static function arbolPrivilegios($evento, $usuario, $sede, $elemento) {
        global $arbol, $sql;

        $tablas         = array("modulos");
        $columnas       = array("id", "id_padre", "orden", "nombre_menu");
        $condicion      = "id = id_padre AND menu = '1' AND (tipo_menu = '0' OR global = '1')";
        $ordenamiento   = "orden ASC";
        $agrupamiento   = "id";

        if ($elemento == ""){
            $resultado = $sql->seleccionar($tablas, $columnas, $condicion, $agrupamiento, $ordenamiento);

            if ($sql->filasDevueltas) {
                while($datos = $sql->filaEnObjeto($resultado)) {
                    $item     = $datos->id;
                    $nombre   = $datos->nombre_menu;
                    $marcador = "";

                    if ($sql->existeItem("permisos_modulos_usuarios", "id_usuario", $usuario, "id_modulo = '$item' AND id_sede = '$sede'")) {
                        if ($evento == "consultar") {
                            $marcador = "checked disabled";
                        } else if ($evento == "modificar") {
                            $marcador = "checked";
                        }
                    } else {
                        if ($evento == "consultar") {
                            $marcador = "disabled";
                        } else if ($evento == "modificar") {
                            $marcador = "";
                        }
                    }

                    $valor  = $item."|M";
                    $tipo   = "<input type=\"checkbox\" value=\"$valor\" name=\"datos[privilegios][$item]\" $marcador />";
                    $arbol .= "<li>$tipo<label>".$nombre."</label>";

                    $tablas_componentes         = array("componentes_modulos");
                    $columnas_componentes       = array("id", "id_modulo", "nombre");
                    $condicion_componentes      = "id_modulo = $item";
                    $ordenamiento_componentes   = "nombre ASC";
                    $agrupamiento_componentes   = "";
                    $resultado_componentes      = $sql->seleccionar($tablas_componentes, $columnas_componentes, $condicion_componentes, $agrupamiento_componentes, $ordenamiento_componentes);
                    if ($sql->filasDevueltas) {
                        $arbol .= "<ul>\n";
                        while($datos_componentes = $sql->filaEnObjeto($resultado_componentes)) {
                            $modulo = $datos_componentes->id;
                            $nombre = $datos_componentes->nombre;
                            if ($sql->existeItem("permisos_componentes_usuarios", "id_usuario", $usuario, "id_componente = '$datos_componentes->id' AND id_sede = '$sede'")) {
                                if ($evento == "consultar") {
                                    $marcador = "checked disabled";
                                } else if ($evento == "modificar") {
                                    $marcador = "checked";
                                }
                            } else {
                                if ($evento == "consultar") {
                                    $marcador = "disabled";
                                } else if ($evento == "modificar") {
                                    $marcador = "";
                                }
                            }

                            $valor  = $modulo."|C";
                            $tipo   = "<input type=\"checkbox\" value=\"$valor\" name=\"datos[privilegios][$modulo]\" $marcador />";
                            $arbol .= "<li>$tipo<label>".$nombre."</label>";
                        }
                        $arbol .= "</ul>\n";
                    }

                    $condicion  = "id_padre = $item AND id != $item AND menu = '1'";
                    $resultado2 = $sql->seleccionar($tablas, $columnas, $condicion, $agrupamiento, $ordenamiento);
                    if ($sql->filasDevueltas) {
                        $arbol .= "<ul>\n";
                        self::arbolPrivilegios($evento, $usuario, $sede, $item);
                        $arbol .= "</ul>\n";
                    }
                    $arbol .= "</li>\n";
                }
            }
        } else {
            $condicion = "id_padre = $elemento AND id != $elemento AND menu = '1'";
            $resultado = $sql->seleccionar($tablas, $columnas, $condicion, $agrupamiento, $ordenamiento);

            if ($sql->filasDevueltas) {
                while($datos = $sql->filaEnObjeto($resultado)){
                    $item     = $datos->id;
                    $nombre   = $datos->nombre_menu;
                    $marcador = "";

                    if ($sql->existeItem("permisos_modulos_usuarios", "id_usuario", $usuario, "id_modulo = '$item' AND id_sede = '$sede'")) {
                        if ($evento == "consultar") {
                            $marcador = "checked disabled";
                        } else if ($evento == "modificar") {
                            $marcador = "checked";
                        }
                    } else {
                        if ($evento == "consultar") {
                            $marcador = "disabled";
                        } else if ($evento == "modificar") {
                            $marcador = "";
                        }
                    }

                    $valor  = $item."|M";
                    $tipo   = "<input type=\"checkbox\" value=\"$valor\" name=\"datos[privilegios][$item]\" $marcador />";
                    $arbol .="<li>$tipo<label>".$nombre."</label>";

                    $tablas_componentes         = array("componentes_modulos");
                    $columnas_componentes       = array("id", "id_modulo", "nombre");
                    $condicion_componentes      = "id_modulo = $item";
                    $ordenamiento_componentes   = "nombre ASC";
                    $agrupamiento_componentes   = "";
                    $resultado_componentes      = $sql->seleccionar($tablas_componentes, $columnas_componentes, $condicion_componentes, $agrupamiento_componentes, $ordenamiento_componentes);
                    if ($sql->filasDevueltas) {
                        $arbol .= "<ul>\n";
                        while($datos_componentes = $sql->filaEnObjeto($resultado_componentes)) {
                            $modulo = $datos_componentes->id;
                            $nombre = $datos_componentes->nombre;
                            if ($sql->existeItem("permisos_componentes_usuarios", "id_usuario", $usuario, "id_componente = '$datos_componentes->id' AND id_sede = '$sede'")) {
                                if ($evento == "consultar") {
                                    $marcador = "checked disabled";
                                } else if ($evento == "modificar") {
                                    $marcador = "checked";
                                }
                            } else {
                                if ($evento == "consultar") {
                                    $marcador = "disabled";
                                } else if ($evento == "modificar") {
                                    $marcador = "";
                                }
                            }

                            $valor  = $modulo."|C";
                            $tipo   = "<input type=\"checkbox\" value=\"$valor\" name=\"datos[privilegios][$modulo]\" $marcador />";
                            $arbol .= "<li>$tipo<label>".$nombre."</label>";
                        }
                        $arbol .= "</ul>\n";
                    }

                    $condicion  = "id_padre = $item AND id != $item AND menu = '1'";
                    $resultado2 = $sql->seleccionar($tablas, $columnas, $condicion, $agrupamiento, $ordenamiento);

                    if ($sql->filasDevueltas) {
                        $arbol .= "<ul>\n";
                        self::arbolPrivilegios($evento, $usuario, $sede, $item);
                        $arbol .= "</ul>\n";
                    }
                    $arbol .= "</li>\n";
                }
            }
        }
        return $arbol;
    }
}

