<?php

/**
 *
 * @package     FOLCS
 * @subpackage  Perfil
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2009 FELINUX LTDA
 * @version     0.1
 *
 **/

class Perfil {

    /**
     * Código interno o identificador del perfil de los privilegios en la base de datos
     * @var entero
     */
    public $id;

    /**
     * Valor numérico que determina el orden o la posición del perfil en la base de datos
     * @var entero
     */
    public $orden;

    /**
     * URL relativa del módulo de privilegios
     * @var cadena
     */
    public $urlBase;

    /**
     * URL relativa de un perfil
     * @var cadena
     */
    public $url;

    /**
     * Nombre del perfil
     * @var cadena
     */
    public $nombre;
    
    /**
     * Nombre del perfil
     * @var cadena
     */
    public $visibilidad;    

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
     * Inicializar los privilegios
     *
     * @param entero $id Código interno o identificador de los privilegios en la base de datos
     *
     */
    public function __construct($id = NULL) {
        global $modulo, $sql;
        
        $this->urlBase              = '/' . $modulo->url;
        $this->url                  = $modulo->url;
        $this->idModulo             = $modulo->id;
        $this->tabla                = $modulo->tabla;
        //Saber el numero de registros
        $this->registros            = $sql->obtenerValor('perfiles', 'COUNT(id)', 'id != "0"');
        //Saber el numero de registros activos
        $this->registrosActivos     = $sql->obtenerValor('perfiles', 'COUNT(id)', 'activo = "1"');
        //establecer el valor del campo predeterminado para organizar los listados
        $this->ordenInicial         = 'nombre';

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
        
        if (isset($id) && $sql->existeItem("perfiles", "id", intval($id))) {

       	    $tablas = array(
            	"p"     => "perfiles"
            );

            $columnas = array(
                "id"        => "p.id",
                "nombre"    => "p.nombre",
                "activo"    => "p.activo"
            );

	    $condicion = "p.id = '$id'";
	    
	    $agrupar = "p.id";

            //$sql->depurar = true;
            $consulta = $sql->seleccionar($tablas, $columnas, $condicion, $agrupar);

            if ($sql->filasDevueltas) {
                $fila = $sql->filaEnObjeto($consulta);
                foreach ($fila as $propiedad => $valor) {
                    $this->$propiedad = $valor;
                }
                
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
        
        //primero agregar el perfil
        $datosItem = array();
        
        $datosItem['nombre'] = $datos['nombre'];

        if (isset($datos['activo'])) {
            $datosItem['activo'] = '1';
        } else {
            $datosItem['activo'] = '0';
        }

        $query = $sql->insertar('perfiles', $datosItem);   
        
        $idPerfil = $sql->ultimoId;
        
        if ($query) {
            foreach ($datos["privilegios"] as $privilegio) {
                $datos_privilegio = explode("|", $privilegio);
                
                if ($datos_privilegio[1] == 'M') {
                    $consulta  = $sql->insertar("permisos_modulos_perfiles", array("id_modulo" => $datos_privilegio[0], "id_perfil" => $idPerfil));
                } else {
                    $consulta  = $sql->insertar("permisos_componentes_perfiles", array("id_componente" => $datos_privilegio[0], "id_perfil" => $idPerfil));
                }
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
        
        //primero agregar el perfil
        $datosItem = array();
        
        $datosItem['nombre'] = $datos['nombre'];

        if (isset($datos['activo'])) {
            $datosItem['activo'] = '1';
            
        } else {
            $datosItem['activo'] = '0';
            
        }

        $query = $sql->modificar('perfiles', $datosItem, "id = '".$this->id."'");
        
        if ($query){
            $sql->eliminar("permisos_componentes_perfiles", "id_perfil = '".$this->id."' ");
            $sql->eliminar("permisos_modulos_perfiles", "id_perfil = '".$this->id."'");

            foreach ($datos["privilegios"] as $privilegio) {
                $datos_privilegio = explode("|", $privilegio);

                if ($datos_privilegio[1] == 'M') {
                    $consulta  = $sql->insertar("permisos_modulos_perfiles", array("id_modulo" => $datos_privilegio[0],  "id_perfil" => $this->id));
                } else {
                    $consulta  = $sql->insertar("permisos_componentes_perfiles", array("id_componente" => $datos_privilegio[0], "id_perfil" => $this->id));
                }
                
            }

            if ($consulta) {
                return true;

            } else {
                return NULL;

            }

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

        $query1 = $sql->eliminar("permisos_componentes_perfiles", "id_perfil = '".$this->id."' ");

        if (!$query1){
            $sql->cancelarTransaccion();
            return false;

        }

        $query2 = $sql->eliminar("permisos_modulos_perfiles", "id_perfil = '".$this->id."'");

        if (!$query2){
            $sql->cancelarTransaccion();
            return false;

        } 

        $consulta = $sql->eliminar("perfiles", "id = '".$this->id."'");
        
        if (!$consulta){
            $sql->cancelarTransaccion();
            return false;
            
        }
        
        $sql->finalizarTransaccion();

        return $consulta;

    }

    /**
     *
     * Listar los usuarios que tiene privilegios
     *
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
            $condicion .= 'p.id NOT IN (' . $excepcion . ') AND ';
        }

        /*** Definir el orden de presentación de los datos ***/
        if ($this->listaAscendente) {
            $orden = "p.nombre DESC";
        } else {
            $orden = "p.nombre ASC";
        }

        $tablas = array(
            "p"     => "perfiles"
        );

        $columnas = array(
            "id"        => "p.id",
            "nombre"    => "p.nombre",
        );
 
        if (!empty($condicionGlobal)) {
            $condicion .= $condicionGlobal . ' AND ';
        }  

        $agrupar = "p.id";

        $condicion .= "p.id != 0";

        if (is_null($this->registrosConsulta)) {
            $sql->seleccionar($tablas, $columnas, $condicion, $agrupar);
            $this->registrosConsulta = $sql->filasDevueltas;
        }

        $consulta = $sql->seleccionar($tablas, $columnas, '', $agrupar, $orden, $inicio, $cantidad);

        if ($sql->filasDevueltas) {

            $lista = array();

            while ($privilegios = $sql->filaEnObjeto($consulta)) {
                $privilegios->url = $this->urlBase."/".$privilegios->id;
                $lista[] = $privilegios;

            }

        }

        return $lista;

    }


    public function generarTabla($arregloRegistros, $datosPaginacion = NULL) {
        global $textos;
        //Declaracion de las columnas que se van a mostrar en la tabla
        $datosTabla = array(
            HTML::parrafo($textos->id('CODIGO'), 'centrado')    => 'id|p.id',
            HTML::parrafo($textos->id('NOMBRE'), 'centrado')    => 'nombre|p.nombre',
        );
        //ruta a donde se mandara la accion del doble click
        $ruta = '/ajax' . $this->urlBase . '/move';


        return Recursos::generarTablaRegistros($arregloRegistros, $datosTabla, $ruta, $datosPaginacion) . HTML::crearMenuBotonDerecho('PERFILES');
    }    

    /*** Genera lista HTML y crea otras opciones a partir de ella ***/
    public static function listaPrivilegios($perfil, $evento = "") {
        $arbol  = "";
        $arbol .= "<ul class=\"vistaArbol\">\n";
        $arbol .= self::arbolPrivilegios($evento, $perfil, $elemento = "");
        $arbol .= "</ul>\n";
        $arbol .= "<div class='imagen_cargando'></div>\n";
        return $arbol;
    }

    /*** Requerida por self::listaPrivilegios() ***/
    public static function arbolPrivilegios($evento, $perfil, $elemento) {
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
                    
                    if ($sql->existeItem("permisos_modulos_perfiles", "id_perfil", $perfil, "id_modulo = '$item'")) {
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
                            
                            if ($sql->existeItem("permisos_componentes_perfiles", "id_perfil", $perfil, "id_componente = '$datos_componentes->id'")) {
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
                        self::arbolPrivilegios($evento, $perfil,  $item);
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

                    if ($sql->existeItem("permisos_modulos_perfiles", "id_perfil", $perfil, "id_modulo = '$item'")) {
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
                            if ($sql->existeItem("permisos_componentes_perfiles", "id_perfil", $perfil, "id_componente = '$datos_componentes->id'")) {
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
                        self::arbolPrivilegios($evento, $perfil,  $item);
                        $arbol .= "</ul>\n";
                    }
                    $arbol .= "</li>\n";
                }
            }
        }
        return $arbol;
    }
    

    public static function verificarPermisosAdicion($modulo){
        global $sql, $sesion_usuarioSesion;

        if(!isset($sesion_usuarioSesion)){
            return NULL;
        }
        //verificar primero si puede adicionar
        $idComponente  = $sql->obtenerValor("componentes_modulos", "id", "componente = 'botonAdicionar".ucwords(strtolower($modulo))."'");
        //obtener el valor de un campo, en este caso id_componente para verificar que el usuario actual si tiene permisos sobre este componente
        $puedeAgregar  = $sql->obtenerValor("permisos_componentes_usuarios", "id_componente", "id_componente = '".$idComponente."' AND id_usuario = '".$sesion_usuarioSesion->id."'");
        
        if($puedeAgregar){
            return true;
        }else{
            return false;
        }

    }//fin del metodo verfificar permiso adicion    
    
    

    public static function verificarPermisosModificacion($modulo){
        global $sql, $sesion_usuarioSesion;

        if(!isset($sesion_usuarioSesion)){
            return NULL;
        }
        
        $idComponente = $sql->obtenerValor("componentes_modulos", "id", "componente = 'botonEditar".ucwords(strtolower($modulo))."'");
        //obtener el valor de un campo, en este caso id_componente para verificar que el usuario actual si tiene permisos sobre este componente
        $puedeEditar  = $sql->obtenerValor("permisos_componentes_usuarios", "id_componente", "id_componente = '".$idComponente."' AND id_usuario = '".$sesion_usuarioSesion->id."'");        

        if($puedeEditar){
            return true;
        }else{
            return false;
        }

    }//fin del metodo verfificar permiso modificacion
    
    
    
    
    /**
*
*Metodo para verificar los permisos para añadir contenido del "tipo de usuario" del usuario que ha iniciado la sesion
*sobre determinado modulo.
*
**/



    public static function verificarPermisosEliminacion($modulo){
        global $sql, $sesion_usuarioSesion;

        if(!isset($sesion_usuarioSesion)){
            return NULL;
        }

        $idComponente = $sql->obtenerValor("componentes_modulos", "id", "componente = 'botonBorrar".ucwords(strtolower($modulo))."'");
        $puedeBorrar  = $sql->obtenerValor("permisos_componentes_usuarios", "id_componente", "id_componente = '".$idComponente."' AND id_usuario = '".$sesion_usuarioSesion->id."'");
      
        if($puedeBorrar){
            return true;
        }else{
            return false;
        }

    }//fin del metodo verfificar permiso eliminacion
    
    
    public static function verificarPermisosBoton($boton){
        global $sql, $sesion_usuarioSesion;

        if(!isset($sesion_usuarioSesion)){
            return NULL;
        }

        $idComponente = $sql->obtenerValor("componentes_modulos", "id", "componente = '".$boton."'");
        $tienePermiso = $sql->obtenerValor("permisos_componentes_usuarios", "id_componente", "id_componente = '".$idComponente."' AND id_usuario = '".$sesion_usuarioSesion->id."'");
      
        if($tienePermiso || $sesion_usuarioSesion->id == 0){
            return true;
        }else{
            return false;
        }

    }//fin del metodo verfificar permiso eliminacion    
    


  /**
   *
   * @global type $sql
   * @global type $configuracion
   * @global type $sesion_usuarioSesion
   * @param type $idModulo
   * @return null 
   */  
    public static function verificarPermisosModulo($idModulo){
        global $sql, $sesion_usuarioSesion;

        if(!isset($sesion_usuarioSesion)){
            return NULL;
        }

        $perfil    = $sesion_usuarioSesion->id;
        $condicion = "id_usuario = '".$perfil."' AND id_modulo = '".$idModulo."'";
        $permiso   = $sql->obtenerValor("permisos_modulos_usuarios", "id_usuario", $condicion);
        if($permiso){
            return true;
        }else{
            return false;
        }
        

    }
    
    /**
     * Funcion encargada
     * @param type $idUsuario
     */
    public function modificarPrivilegiosUsuario($idUsuario){
        global $sql, $sesion_usuarioSesion;
        
        $sede = $sesion_usuarioSesion->sede->id;
        
        $sql->iniciarTransaccion();

        $query1 = $sql->eliminar("permisos_componentes_usuarios", "id_usuario = '".$idUsuario."' AND id_sede = '".$sede."'");
        
        if (!$query1) {
            $sql->cancelarTransaccion();
            return false;
        }
        
        $query2 = $sql->eliminar("permisos_modulos_usuarios", "id_usuario = '".$idUsuario."' AND id_sede = '".$sede."'");
        
        if (!$query2) {
            $sql->cancelarTransaccion();
            return false;
        }
        
        $sql->finalizarTransaccion();        
        

        return $this->agregarPrivilegiosUsuario($idUsuario);
        
    }    
    
    /**
     * Funcion encargada
     * @param type $idUsuario
     */
    public function agregarPrivilegiosUsuario($idUsuario){
        global $sql, $sesion_usuarioSesion;
        
        //seleccionar los permisos-modulos por perfil y adicionarlos a los permisos-modulos del usuario
        $query1 = $sql->seleccionar("permisos_modulos_perfiles", "id_modulo", "id_perfil = '".$this->id."'");
        
        $permisosModulos = array();
        
        if($sql->filasDevueltas){
            while ($dato = $sql->filaEnObjeto($query1)) {
                $permisosModulos[] = $dato->id_modulo;
            }
        }
        
        //seleccionar los permisos-componentes-modulo por perfil y adicionarlos a los permisos-componentes-modulos del usuario
        $query2 = $sql->seleccionar("permisos_componentes_perfiles", "id_componente", "id_perfil = '".$this->id."'");
        
        $permisosComponentes = array();
        
        if($sql->filasDevueltas){
            while ($dato = $sql->filaEnObjeto($query2)) {
                $permisosComponentes[] = $dato->id_componente;
            }
        }
        
        //insertar los permisos-modulos-usuario
        if (!empty($permisosModulos)){
            $valoresConsulta1 = '';

            foreach ($permisosModulos as $value) {
                $valoresConsulta1 .= '("' . $value . '", "' . $sesion_usuarioSesion->sede->id . '", "' . $idUsuario . '"),';
            }

            $valoresConsulta1 = substr($valoresConsulta1, 0, -1);

            $sentencia1 = "INSERT INTO fom_permisos_modulos_usuarios (id_modulo, id_sede, id_usuario) VALUES $valoresConsulta1";

            $insertar1 = $sql->ejecutar($sentencia1);

            if (!$insertar1){
                return false;
            }            
        }
        
        //insertar los permisos-componentes-usuario
        if (!empty($permisosComponentes)){
            $valoresConsulta2 = '';

            foreach ($permisosComponentes as $value) {
                $valoresConsulta2 .= '("' . $value . '", "' . $sesion_usuarioSesion->sede->id . '", "' . $idUsuario . '"),';
            }

            $valoresConsulta2 = substr($valoresConsulta2, 0, -1);

            $sentencia2 = "INSERT INTO fom_permisos_componentes_usuarios (id_componente, id_sede, id_usuario) VALUES $valoresConsulta2";

            $insertar2 = $sql->ejecutar($sentencia2);

            if (!$insertar2){
                return false;
            }             
        }
        
        return true;        
        
    }    
    
}
