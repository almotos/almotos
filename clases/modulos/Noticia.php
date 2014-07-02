<?php

/**
 *
 * @package     FOLCS
 * @subpackage  Noticias
 * @author      Pablo A. Vélez <pavelez8@misena.edu.co>
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2012 Genesys
 * @version     0.2
 * 
 * Hay que pensar en dos tablas, visitas_noticia y visitas_anuncio
 * para llevar un registro de quien y cuantas veces vistan una determinada noticia o anuncio
 *
 * */
class Noticia {

    /**
     * Código interno o identificador de la noticia en la base de datos
     * @var entero
     */
    public $id;

    /**
     * URL relativa del módulo de noticias
     * @var cadena
     */
    public $urlBase;

    /**
     * URL relativa de una noticia específica
     * @var cadena
     */
    public $url;

    /**
     * Código interno o identificador del usuario creador de la noticia en la base de datos
     * @var entero
     */
    public $idAutor;

    /**
     * Código interno o identificador del modulo
     * @var entero
     */
    public $idModulo;

    /**
     * Sobrenombre o apodo del usuario creador de la noticia
     * @var cadena
     */
    public $autor;

    /**
     * Título de la noticia
     * @var cadena
     */
    public $titulo;

    /**
     * Resumen corto de la noticia
     * @var cadena
     */
    public $resumen;

    /**
     * Contenido completo de la noticia
     * @var cadena
     */
    public $contenido;

    /**
     * Código interno o identificador en la base de datos de la imagen relacionada con la noticia
     * @var entero
     */
    public $idImagen;

    /**
     * Ruta de la imagen de la noticia en tamaño normal
     * @var cadena
     */
    public $imagenPrincipal;

    /**
     * Ruta de la imagen de la noticia en miniatura
     * @var cadena
     */
    public $imagenMiniatura;

    /**
     * Fecha de publicación de la noticia
     * @var fecha
     */
    public $fechaPublicacion;
    
    /**
     * Indicador de disponibilidad del registro
     * @var lógico
     */
    public $activo;

    /**
     * Indicador del orden cronológio de la lista de noticias
     * @var lógico
     */
    public $listaAscendente = TRUE;

    /**
     * Número de registros de la lista
     * @var entero
     */
    public $registros = NULL;

    /**
     * Número de registros de la lista
     * @var entero
     */
    public $registrosActivos = NULL;

    /**
     * Inicializar la noticia
     * @param entero $id Código interno o identificador de la noticia en la base de datos
     */
    public function __construct($id = NULL) {
        $sqlGlobal = new SqlGlobal();

        $modulo = new Modulo("NOTICIAS");
        $this->urlBase  = "/" . $modulo->url;
        $this->url      = $modulo->url;
        $this->idModulo = $modulo->id;


        $this->registros = $sqlGlobal->obtenerValor("noticias", "COUNT(id)", "");
        

        $this->registrosActivos =  $sqlGlobal->obtenerValor("noticias", "COUNT(id)", "activo = '1'");
        

        if (isset($id)) {
            $this->cargar($id);
        }
    }

    /**
     * Cargar los datos de una noticia
     * @param entero $id Código interno o identificador de la noticia en la base de datos
     */
    public function cargar($id) {
        global  $configuracion;
        $sqlGlobal = new SqlGlobal();

        if (isset($id) && $sqlGlobal->existeItem("noticias", "id", intval($id))) {

            $tablas = array(
                "n" => "noticias",
                "i" => "imagenes"
            );

            $columnas = array(
                "id"                => "n.id",
                "idAutor"           => "n.id_usuario",
                "autor"             => "n.autor",
                "idImagen"          => "n.id_imagen",
                "imagen"            => "i.ruta",
                "resumen"           => "n.resumen",
                "titulo"            => "n.titulo",
                "contenido"         => "n.contenido",
                "fechaPublicacion"  => "UNIX_TIMESTAMP(n.fecha_publicacion)",
                "activo"            => "n.activo"
            );

            $condicion = "n.id_imagen = i.id AND n.id = '$id'";

            $consulta = $sqlGlobal->seleccionar($tablas, $columnas, $condicion);

            if ($sqlGlobal->filasDevueltas) {
                $fila = $sqlGlobal->filaEnObjeto($consulta);

                foreach ($fila as $propiedad => $valor) {
                    $this->$propiedad = $valor;
                }

                $this->url = $this->urlBase . "/" . $this->id;
                $this->imagenPrincipal = $configuracion["SERVIDOR_GLOBAL"]["media"] . $configuracion["RUTAS"]["imagenesDinamicas"] . "/" . $this->imagen;
                $this->imagenMiniatura = $configuracion["SERVIDOR_GLOBAL"]["media"] . $configuracion["RUTAS"]["imagenesMiniaturas"] . "/" . $this->imagen;
                //sumar una visita a la noticia
               // $this->sumarVisita();
            }
        }
    }

    /**
     * Adicionar una noticia
     * @param  arreglo $datos       Datos de la noticia a adicionar
     * @return entero               Código interno o identificador de la noticia en la base de datos (NULL si hubo error)
     */
    public function adicionar($datos) {
        global $sql, $archivo_imagen;
//        $sqlGlobal = new SqlGlobal();
        
        $idImagen = "0";

        if (isset($archivo_imagen) && !empty($archivo_imagen["tmp_name"])) {
           
            $imagen = new Imagen();

            $datosImagen = array(
                "titulo"        => "imagen Noticia: " . $datos["titulo"],
                "descripcion"   => "imagen de noticia",
                "modulo"        => "NOTICIAS",
                "idRegistro"    => ""
            );
            $idImagen = $imagen->adicionar($datosImagen);
        }

        $datosNoticia = array(

            "codigo"            => $datos["titulo"],
            "nombre"            => $datos["resumen"],
            "id_imagen"         => $idImagen
        );

        if (isset($datos["activo"])) {
            $datosNoticia["activo"] = "1";

        } else {
            $datosNoticia["activo"] = "0";

        }
        $sql->filtrarDatos = FALSE;
        $consulta = $sql->insertar("lineas", $datosNoticia);
        $sql->filtrarDatos = TRUE;

        $idNoticia= $sql->ultimoId;
        if ($consulta) {
            return $idNoticia;
            
        } else {
            return FALSE;
        }
    }

    /**
     * Modificar una noticia
     * @param  arreglo $datos       Datos de la noticia a modificar
     * @return lógico               Indica si el procedimiento se pudo realizar correctamente o no
     */
    public function modificar($datos) {
        global  $archivo_imagen;
        $sqlGlobal = new SqlGlobal();

        if (!isset($this->id)) {
            return NULL;
        }

        $idImagen = $this->idImagen;

       if (isset($archivo_imagen) && !empty($archivo_imagen["tmp_name"])) {
            $imagen = new Imagen($this->idImagen);
            $imagen->eliminar();

            $datosImagen = array(
                "titulo"        => "imagen Noticia: " . $datos["titulo"],
                "descripcion"   => "imagen de noticia",
                "modulo"        => "NOTICIAS",
                "idRegistro"    => ""
            );
            $idImagen = $imagen->adicionar($datosImagen);
        }

        if (isset($datos["activo"])) {
            $datosNoticia["activo"] = "1";
            $datosNoticia["fecha_publicacion"] = date("Y-m-d H:i:s");
        } else {
            $datosNoticia["activo"] = "0";
            $datosNoticia["fecha_publicacion"] = NULL;
        }



        $datosNoticia = array(
            "orden"             => $orden,
            "titulo"            => $datos["titulo"],
            "resumen"           => $datos["resumen"],
            "contenido"         => $datos["contenido"],
            "id_menu"           => $datos["menu"],
            "id_usuario"        => $sesion_usuarioSesion->id,
            "autor"             => $sesion_usuarioSesion->nombre,
            "id_imagen"         => $idImagen
        );


        $consulta = $sqlGlobal->modificar("noticias", $datosNoticia, "id = '" . $this->id . "'");

        if ($consulta) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    /**
     * Eliminar una noticia
     * @param entero $id    Código interno o identificador de la noticia en la base de datos
     * @return lógico       Indica si el procedimiento se pudo realizar correctamente o no
    */
    public function eliminar() {
        $sqlGlobal = new SqlGlobal();
        
        if (!isset($this->id)) {
            return NULL;
        }
        $imagen = new Imagen($this->idImagen);
        $imagen->eliminar();
        //$sql->depurar = true;
        $consulta = $sqlGlobal->eliminar("noticias", "id = '" . $this->id . "'");

        if ($consulta) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Listar las noticias
     * @param entero  $cantidad    Número de noticias a incluir en la lista (0 = todas las entradas)
     * @param arreglo $excepcion   Arreglo con los códigos internos o identificadores a omitir en la lista
     * @param cadena  $condicion   Condición adicional (SQL)
     * @return arreglo             Lista de noticias
     */
    public function listar($inicio = 0, $cantidad = 0, $excepcion = NULL, $condicion = NULL) {
        global $configuracion;
        $sqlGlobal = new SqlGlobal();

        /* ** Validar la fila inicial de la consulta ** */
        if (!is_int($inicio) || $inicio < 0) {
            $inicio = 0;
        }

        /* ** Validar la cantidad de registros requeridos en la consulta ** */
        if (!is_int($cantidad) || $cantidad <= 0) {
            $cantidad = 0;
        }

        /* ** Validar que la condición sea una cadena de texto ** */
        if (!is_string($condicion)) {
            $condicion = "";
        }

        /* ** Validar que la excepción sea un arreglo y contenga elementos ** */
        if (isset($excepcion) && is_array($excepcion) && count($excepcion)) {
            $excepcion = implode(",", $excepcion);
            $condicion .= "n.id NOT IN ($excepcion)";
        }

        /* ** Definir el orden de presentación de los datos ** */
        if ($this->listaAscendente) {
            $orden = "n.fecha_publicacion DESC";
        } else {
            $orden = "n.fecha_publicacion DESC";
        }

        $tablas = array(
            "n" => "noticias",
            "i" => "imagenes"
        );

        $columnas = array(
            "id"                => "n.id",
            "idAutor"           => "n.id_usuario",
            "autor"             => "n.autor",
            "idImagen"          => "n.id_imagen",
            "imagen"            => "i.ruta",
            "resumen"           => "n.resumen",
            "titulo"            => "n.titulo",
            "contenido"         => "n.contenido",
            "fechaPublicacion"  => "UNIX_TIMESTAMP(n.fecha_publicacion)",
            "activo"            => "n.activo"
        );

        if (!empty($condicion)) {
            $condicion .= " AND ";
        }

        $condicion .= "n.id_imagen = i.id";


        if (is_null($this->registros)) {
            $sqlGlobal->seleccionar($tablas, $columnas, $condicion);
            $this->registros = $sqlGlobal->filasDevueltas;
        }
        //$sql->depurar = true;
        $consulta = $sqlGlobal->seleccionar($tablas, $columnas, $condicion, "n.id", $orden, $inicio, $cantidad);

         $lista = array();
        if ($sqlGlobal->filasDevueltas) {          

            while ($noticia = $sqlGlobal->filaEnObjeto($consulta)) {
                $noticia->url = $this->urlBase . "/" . $noticia->id;
                $noticia->imagenPrincipal = $configuracion["SERVIDOR_GLOBAL"]["media"] . $configuracion["RUTAS"]["imagenesDinamicas"] . "/" . $noticia->imagen;
                $noticia->imagenMiniatura = $configuracion["SERVIDOR_GLOBAL"]["media"] . $configuracion["RUTAS"]["imagenesMiniaturas"] . "/" . $noticia->imagen;
                $lista[] = $noticia;
            }
        }

        return $lista;
    }

//fin del metodo listar

    /**
     * @global type $sql object -> objeto sql para interacciones con la BD
     * @return type boolean ->     verdadero si se realizo la actividad sin problema
     */
    public function sumarVisita() {
        global $sqlGlobal;

        if (!isset($this->id)) {
            return NULL;
        }

        $numVisitas = $sqlGlobal->obtenerValor("noticias", "visitas", "id = '" . $this->id . "'");
        
        $datosNoticia["visitas"] = $numVisitas + 1;
        
        $sumVisita = $sqlGlobal->modificar("noticias", $datosNoticia, "id = '" . $this->id . "'");

        if ($sumVisita) {
            return true;
        } else {
            return false;
        }
        
    }

//fin del metodo sumar visita
}

//fin de la clase Noticias
?>