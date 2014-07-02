<?php

/**
 *
 * @package     FOLCS
 * @subpackage  Paginas
 * @author      Francisco J. Lozano B. <fjlozano@felinux.com.co>
 * @author      Julian A. Mondragón <jmondragon@felinux.com.co>
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2009 FELINUX LTDA
 * @version     0.1
 *
 **/

    $contenido    = "";
    $contenido     .= HTML::contenedor(HTML::contenedor($textos->id("AYUDA_MODULO"), 'ui-corner-all'), 'ui-widget-shadow ui-corner-all oculto', 'contenedorAyudaUsuario');
    global $sesion_usuarioSesion;
    
    if (isset($url_ruta)) {
        $pagina = new Pagina($url_ruta);

        if (isset($pagina->id)) {
            Plantilla::$etiquetas["TITULO_PAGINA"] .= " :: ".$pagina->titulo;
            Plantilla::$etiquetas["DESCRIPCION"]    = $pagina->titulo;
            $contenidoPagina     = HTML::contenedor($pagina->contenido, "justificado");
            $contenidoPagina    .= HTML::contenedor(HTML::botonesCompartir(), "botonesCompartir");
            $contenido            = HTML::bloque("pagina_".$pagina->id, $pagina->titulo, $contenidoPagina);
            $contenido           .= HTML::bloque("bloqueComentariosNoticia", $textos->id("COMENTARIOS"), Recursos::bloqueComentarios("PAGINAS", $pagina->id, $pagina->idAutor));
        }

    } else {
        
        $pagina       = new Pagina();
        $tituloBloque = $textos->id("MODULO_ACTUAL");
        $listaItems   = array();
        
        $cantidadPaginas = $pagina->listar();
        $cantidadPaginas = sizeof($cantidadPaginas);


        /**
        *
        * Formulario para adicionar un nuevo elemento
        *
        **/
        if (isset($sesion_usuarioSesion) /*&& Permisos::verificarPermisos()*/ ) {
            if($cantidadPaginas < 5){
               if (Permisos::verificarPermisos(7,"ADICIONAR")) {
                   $contenido .= HTML::contenedor(HTML::botonAdicionarItem($pagina->urlBase, $textos->id("ADICIONAR_PAGINA")), "derecha margenInferior");
               }
            }else{
               $contenido .= HTML::parrafo($textos->id("SOLO_PUEDES_TENER_MAXIMO_5_PAGINAS"));
            }
        }

        $fila = 0;

        foreach ($pagina->listar(0, 0, array(0)) as $elemento) {
            $fila++;
            $item   = "";
            $celdas = array();

            if (isset($sesion_usuarioSesion)) {
                $botones = "";

                if ($fila > 1) {
                    if (Permisos::verificarPermisos(7,"SUBIR")) {
                        $botones .= HTML::botonSubirItem($elemento->id, $pagina->urlBase);
                    }
                }

                if ($fila < $pagina->registros) {
                    if (Permisos::verificarPermisos(7,"BAJAR")) {
                        $botones .= HTML::botonBajarItem($elemento->id, $pagina->urlBase);
                    }
                }

                if (Permisos::verificarPermisos(7,"MODIFICAR")) {
                    $botones .= HTML::botonModificarItem($elemento->id, $pagina->urlBase);
                }

                if (Permisos::verificarPermisos(7,"ELIMINAR")) {
                    $botones .= HTML::botonEliminarItem($elemento->id, $pagina->urlBase);
                }
                $item    .= HTML::contenedor($botones, "botonesLista", "botonesLista");
            }

            $item .= HTML::parrafo($textos->id("TITULO"), "negrilla");
            $item .= HTML::parrafo(HTML::enlace($elemento->titulo, $elemento->url), "negrilla");

            if ($elemento->activo) {
                $estado = HTML::parrafo($textos->id("ACTIVO"));

            } else {
                $estado = HTML::parrafo($textos->id("INACTIVO"));
            }


            $celdas[0][]  = HTML::parrafo($textos->id("AUTOR"), "negrilla").HTML::parrafo($elemento->autor);
            $celdas[0][]  = HTML::parrafo($textos->id("ESTADO"), "negrilla").HTML::parrafo($estado);
            $celdas[0][]  = HTML::parrafo($textos->id("FECHA_CREACION"), "negrilla").HTML::parrafo(date("D, d M Y h:i:s A", $elemento->fechaCreacion));
            $celdas[1][]  = HTML::parrafo($textos->id("FECHA_PUBLICACION"), "negrilla").HTML::parrafo(date("D, d M Y h:i:s A", $elemento->fechaPublicacion));
            $celdas[1][]  = HTML::parrafo($textos->id("FECHA_ACTUALIZACION"), "negrilla").HTML::parrafo(date("D, d M Y h:i:s A", $elemento->fechaActualizacion));
            $item        .= HTML::tabla(array(), $celdas, "tablaCompleta2");
            $listaItems[] = $item;
        }

        $contenido .= HTML::lista($listaItems, "listaVertical bordeSuperiorLista", "botonesOcultos");
        $contenido  = HTML::bloque("bloquePaginas", $tituloBloque, $contenido);
        
     }    
    Plantilla::$etiquetas["BLOQUE_CENTRAL"] = $contenido;
    

?>