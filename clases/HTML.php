<?php

/**
 * Html.php Clase del núcleo del framework para la generación de código HTML.
 *
 * @package     FOLCS
 * @subpackage  Base
 * @author      Francisco J. Lozano B. <fjlozano@felinux.com.co>
 * @author      Pablo Andrés Vélez Vidal <pavelez@genesyscorp.com.co>
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2009 FELINUX LTDA
 * @version     0.1
 *
 **/

/**
 * Clase que funciona como una capa de abstracción entre  código HTML y el modelo
 * orientado a objetos utilizado por el framework. 
 * 
 * Esta clase es solamente un repositorio
 * de funciones orientadas a la generación de código HTML apartir de llamadas a funciones PHP.
 * 
 * dicho esto tomemos como ejemplo lo siguiente:
 * 1)Situación: se requiere generar un parrafo con el texto "hola marte", la clase "parrafo-prueba"
 * el id= "id-parrafo" y con el evento onclick = "alert(hola marte)" y un atrubuto personalizado ayuda="esta es la ayuda".
 * 
 * para hacer esto, se escribiría el siguiente código PHP con esta clase de la siguiente forma:
 * 
 * $parrafo = HTML::parrafo("hola marte", "parrafo-prueba", "id-parrafo", array("onclick" => "alert(hola marte)", "ayuda" => "esta es la ayuda"));
 * 
 * según esto, lo anterior sería equivalente a :
 * 
 * $parafo = '<p class="parrafo-prueba" id="id-parrafo" ayuda="esta es la ayuda" onclick="alert(hola marte)"> 
 *      Hola Marte
 * </p>';
 * 
 * Para mas detalles, leer con calma la clase y prestar atención a la documentación de cada uno de sus metodos.
 */
class HTML {

    /**
     * Generar código HTML con botón y formulario para ejecutar un comando via AJAX
     * 
     * @global objeto $textos objeto global para la traduccion de textos
     * @param cadena $icono nombre del icono a ser traducido desde el arreglo global de configuraciones en la posicion "botones"
     * @param string $texto texto a ser mostrado por este elemento
     * @param string $destino URI relativa del destino "action" del form
     * @param array $datos arreglo con los datos a ser usados por este método
     * @param string $clase clase css utilizada por este objeto del DOM
     * @param string $id identificador de este objeto en el DOM
     * @return string|NULL código HTML a ser interpretado por el cliente web | NULL código HTML para ser mostrado e interpretado por el cliente web
     */
    static function botonAjax($icono, $texto, $destino, $datos = array(), $clase = NULL, $id = NULL, $opciones = array()) {
        global $textos;
        
        $opciones2 = $opciones + array('validar' => 'NoValidar');

        $codigo  = HTML::boton($icono, $textos->id($texto), $clase, '', $id, '', $opciones2);

        foreach ($datos as $nombre => $valor) {
            $codigo .= HTML::campoOculto($nombre, $valor);
        }
        $cod  = HTML::forma($destino, $codigo);

        return $cod;
    }

    /**
     * Generar código HTML con botón y formulario para ejecutar un comando via AJAX
     * 
     * @param string $contenido contenido a ser incluido en la llamada a este método
     * @param string $clase clase css utilizada por este objeto del DOM
     * @param string $id identificador de este objeto en el DOM
     * @param array $opciones arreglo de opciones o atributos a ser añadidas a el contenedor
     * @param string $destino URI relativa del destino "action" del form
     * @param array $datos arreglo con los datos a ser usados por este método
     * @param string $id identificador de este objeto en el DOMForma
     * @param array $opcionesForma arreglo de opciones o atributos a ser añadidas a el formulario
     * @return string|NULL código HTML a ser interpretado por el cliente web | NULL código HTML para ser mostrado e interpretado por el cliente web
     */
    static function botonImagenAjax($contenido, $clase, $id, $opciones, $destino, $datos = array(), $idForma = NULL, $opcionesForma = array()) {

        $codigo  = HTML::contenedor($contenido, $clase, $id, $opciones);

        foreach ($datos as $nombre => $valor) {
            $codigo .= HTML::campoOculto($nombre, $valor);
        }
        $codigo .= '';
        $cod  = HTML::forma($destino, $codigo, '', '', '', $opcionesForma, $idForma);

        return $cod;
    }
    
    /**
     * Generar código HTML con botón y formulario para ejecutar un comando via AJAX con cualquier tipo de contenido, ya sea texto, imagen o ambos
     * 
     * @param string $contenido contenido a ser incluido en la llamada a este método
     * @param string $clase clase css utilizada por este objeto del DOM
     * @param string $id identificador de este objeto en el DOM
     * @param array $opciones arreglo de opciones o atributos a ser añadidas a este item
     * @param string $destino URI relativa del destino "action" del form
     * @param array $datos arreglo con los datos a ser usados por este método
     * @param string $id identificador de este objeto en el DOMForma
     * @return string|NULL código HTML a ser interpretado por el cliente web | NULL código HTML para ser mostrado e interpretado por el cliente web
     */
    static function formaAjax($contenido, $clase, $id, $opciones, $destino, $datos = array(), $idForma = NULL) {
        
        $codigo  = HTML::contenedor(HTML::contenedor($contenido, $clase, $id, $opciones), 'enviarAjax' , '');
        foreach ($datos as $nombre => $valor) {
            $codigo .= HTML::campoOculto($nombre, $valor);
        }
        $codigo .= '';//HTML::campoOculto('idQuemado', '', 'idQuemado');
        $cod  = HTML::forma($destino, $codigo, '', '', '','', $idForma);

        return $cod;
    }

    /**
     * Generar código HTML con botón y formulario para adicionar un item
     * 
     * @param string $url URL relativa del destino del form
     * @param type $titulo
     * @return string|NULL código HTML a ser interpretado por el cliente web | NULL código HTML para ser mostrado e interpretado por el cliente web
     */
    static function botonAdicionarItem($url, $titulo) {

        $url     = preg_replace('|^\/|', '', $url);
        $codigo  = HTML::boton('masGrueso', $titulo, 'botonAccion');
        $cod  = HTML::forma('/ajax/'.$url.'/add', $codigo);

        return $cod;
    }

    /**
     * Generar código HTML con botón y formulario para consultar un item
     * 
     * @global objeto $textos objeto global para la traduccion de textos
     * @param string $id identificador de este objeto en el DOM
     * @param string $url URL relativa del destino del form
     * @param boolean $visible determina si estará visible o no este objeto DOM
     * @return string|NULL código HTML a ser interpretado por el cliente web | NULL código HTML para ser mostrado e interpretado por el cliente web
     */
    static function botonConsultarItem($id, $url, $visible = NULL) {
        global $textos;

        $url     = preg_replace('|^\/|', '', $url);
        $codigo  = HTML::boton('lapiz', $textos->id('CONSULTAR'));
        $codigo .= HTML::campoOculto('id', $id);
        $cod  = HTML::forma('/ajax/'.$url.'/see', $codigo);
         if(!isset($visible)){
            $cod  = HTML::contenedor($cod, 'contenedorBotonesLista', 'contenedorBotonesLista');
         }

        return $cod;
    }

    /**
     * Generar código HTML con botón y formulario para modificar un item
     * 
     * @global objeto $textos objeto global para la traduccion de textos
     * @param string $id identificador de este objeto en el DOM
     * @param string $url URL relativa del destino del form
     * @param boolean $visible determina si estará visible o no este objeto DOM
     * @return string|NULL código HTML a ser interpretado por el cliente web | NULL código HTML para ser mostrado e interpretado por el cliente web
     */
    static function botonModificarItem($id, $url, $visible = NULL) {
        global $textos;

        $url     = preg_replace('|^\/|', '', $url);
        $codigo  = HTML::boton('lapiz', $textos->id('MODIFICAR'), 'botonAccion');
        $codigo .= HTML::campoOculto('id', $id);
        $cod  = HTML::forma('/ajax/'.$url.'/edit', $codigo);
         if(!isset($visible)){
            $cod  = HTML::contenedor($cod, 'contenedorBotonesLista', 'contenedorBotonesLista');
         }

        return $cod;
    }
    
    /**
     * Generar código HTML con botón y formulario para eliminar un item desde el listado principal haciendo uso de ajax
     * 
     * @global objeto $textos objeto global para la traduccion de textos
     * @param string $id identificador de este objeto en el DOM
     * @param string $url URL relativa del destino del form
     * @param boolean $visible determina si estará visible o no este objeto DOM
     * @return string|NULL código HTML a ser interpretado por el cliente web | NULL código HTML para ser mostrado e interpretado por el cliente web
     */
    static function botonModificarItemAjax($id, $url, $visible = NULL) {
        global $textos;

        $url     = preg_replace('|^\/|', '', $url);
        $codigo  = HTML::boton('lapiz', $textos->id('MODIFICAR'));
        $codigo .= HTML::campoOculto('id', $id);
        $cod  = HTML::forma('/ajax/'.$url.'/editRegister', $codigo);
        if(!isset($visible)){
            $cod  = HTML::contenedor($cod, 'contenedorBotonesLista', 'contenedorBotonesLista');
        }

        return $cod;
    }

    /**
     * Generar código HTML con botón y formulario para eliminar un item
     * 
     * @global objeto $textos objeto global para la traduccion de textos
     * @param string $id identificador de este objeto en el DOM
     * @param string $url URL relativa del destino del form
     * @param boolean $visible determina si estará visible o no este objeto DOM
     * @return string|NULL código HTML a ser interpretado por el cliente web | NULL código HTML para ser mostrado e interpretado por el cliente web
     */
    static function botonEliminarItem($id, $url, $visible = NULL) {
        global $textos;

        $url     = preg_replace('|^\/|', '', $url);
        $codigo  = HTML::boton('basura', $textos->id('ELIMINAR'), 'botonAccion');
        $codigo .= HTML::campoOculto('id', $id);
        $cod  = HTML::forma('/ajax/'.$url.'/delete', $codigo);
        if(!isset($visible)){
            $cod  = HTML::contenedor($cod, 'contenedorBotonesLista', 'contenedorBotonesLista');
        }
        return $cod;
    }

    /**
     * Generar código HTML con botón y formulario para eliminar un item desde el listado principal haciendo uso de ajax
     * 
     * @global objeto $textos objeto global para la traduccion de textos
     * @param string $id identificador de este objeto en el DOM
     * @param string $url URL relativa del destino del form
     * @param boolean $visible determina si estará visible o no este objeto DOM
     * @return string|NULL código HTML a ser interpretado por el cliente web | NULL código HTML para ser mostrado e interpretado por el cliente web
     */
    static function botonEliminarItemAjax($id, $url, $visible = NULL) {
        global $textos;

        $url     = preg_replace('|^\/|', '', $url);
        $codigo  = HTML::boton('basura', $textos->id('ELIMINAR'), '', '', 'nuevoBoton');
        $codigo .= HTML::campoOculto('id', $id);
        $cod  = HTML::forma('/ajax/'.$url.'/deleteRegister', $codigo); 
        if(!isset($visible)){
            $cod  = HTML::contenedor($cod, 'contenedorBotonesLista', 'contenedorBotonesLista');
        }
        return $cod;
    }

    /**
     * Generar código HTML con botón y formulario para aprobar un item
     * 
     * @global objeto $textos objeto global para la traduccion de textos
     * @param string $id identificador de este objeto en el DOM
     * @param string $url URL relativa del destino del form
     * @return string|NULL código HTML a ser interpretado por el cliente web | NULL código HTML para ser mostrado e interpretado por el cliente web
     */
    static function botonAprobarItem($id, $url) {
        global $textos;
        $url     = preg_replace('|^\/|', '', $url);
        $codigo  = HTML::boton('chequeo', $textos->id('APROBAR'));
        $codigo .= HTML::campoOculto('id', $id);
        $cod  = HTML::forma('/ajax/'.$url.'/approve', $codigo);
        

        return $cod;
    }

    /**
     * Generar código HTML con botón y formulario para subir un item un nivel
     * 
     * @global objeto $textos objeto global para la traduccion de textos
     * @param string $id identificador de este objeto en el DOM
     * @param string $url URL relativa del destino del form
     * @return string|NULL código HTML a ser interpretado por el cliente web | NULL código HTML para ser mostrado e interpretado por el cliente web
     */
    static function botonSubirItem($id, $url) {
        global $textos;

        $url     = preg_replace('|^\/|', '', $url);
        $codigo  = HTML::boton('flechaGruesaArriba', $textos->id('SUBIR'));
        $codigo .= HTML::campoOculto('id', $id);
        $codigo1  = HTML::forma('/ajax/'.$url.'/up', $codigo);
        $cod  = HTML::contenedor($codigo1, 'contenedorBotonesLista', 'contenedorBotonesLista');

        return $cod;
    }

    /**
     * Generar código HTML con botón y formulario para bajar un item un nivel
     * 
     * @global objeto $textos objeto global para la traduccion de textos
     * @param string $id identificador de este objeto en el DOM
     * @param string $url URL relativa del destino del form
     * @return string|NULL código HTML a ser interpretado por el cliente web | NULL código HTML para ser mostrado e interpretado por el cliente web
     */
    static function botonBajarItem($id, $url) {
        global $textos;

        $url     = preg_replace('|^\/|', '', $url);
        $codigo  = HTML::boton('flechaGruesaAbajo', $textos->id('BAJAR'));
        $codigo .= HTML::campoOculto('id', $id);
        $codigo1  = HTML::forma('/ajax/'.$url.'/down', $codigo);
        $cod  = HTML::contenedor($codigo1, 'contenedorBotonesLista', 'contenedorBotonesLista');

        return $cod;
    }

    /**
     * Generar código HTML para insertar un icono en línea en un <span> 
     * 
     * @global array $configuracion arreglo global de configuraciones del sistema
     * @param string $icono cadena a ser traducida por el arreglo global de configuraciones en a posicion "ICONOS"
     * @return string|NULL código HTML a ser interpretado por el cliente web
     */
    static function icono($icono) {
        global $configuracion;

        if (array_key_exists($icono, $configuracion['ICONOS'])) {
            $icono   = 'ui-icon-'.$configuracion['ICONOS'][$icono];
        }
        $codigo = '<span class=\'ui-icon '.$icono.' icono\' style=\'display: inline-block;\'></span>';

        return $codigo;
    }
    
    /**
     * Genera código HTML con la imágen de interrogación (ayuda) que va junto a un objeto del dom
     * 
     * @global array $configuracion arreglo global de configuraciones del sistema
     * @param string $texto texto a ser mostrado por este elemento
     * @return string|NULL código HTML a ser interpretado por el cliente web | NULL código HTML para ser mostrado e interpretado por el cliente web
     */
    static function ayuda($texto, $claseExtra = '') {
        global $configuracion;
        
        $ruta       = $configuracion['SERVIDOR']['media'] . $configuracion['RUTAS']['imagenesEstilos'] . 'ayuda.png';
        $clase      = 'imagenAyudaTooltip '.$claseExtra;
        $id         = 'imagenAyuda';
        $opciones   = array('ayuda' => $texto);
        $codigo     = HTML::imagen($ruta, $clase, $id, $opciones);

        return $codigo;
    }
    
    

    /**
     * Generar código HTML para mostrar el simbolo + de adicionar un item al lado de un campo autocompletable
     * 
     * @global array $configuracion arreglo global de configuraciones del sistema
     * @global objeto $sesion_usuarioSesion objeto global que contiene toda la información del usuario logueado
     * @param string $url URL relativa del destino del form
     * @return string|NULL código HTML a ser interpretado por el cliente web | NULL código HTML para ser mostrado e interpretado por el cliente web
     */
    static function formaAdicionar($url) {
        global $configuracion, $sesion_usuarioSesion;
        
        $mod1           = explode('/', $url);
        $mod            = strtoupper($mod1[2]);
        $puedeAgregar   = Perfil::verificarPermisosAdicion($mod);
        $codigo         = '';
        
        if ((isset($sesion_usuarioSesion) && $puedeAgregar) || isset($sesion_usuarioSesion) && $sesion_usuarioSesion->idTipo == 0) {
            $ruta       = $configuracion['SERVIDOR']['media'] . $configuracion['RUTAS']['imagenesEstilos'] . 'add.png';
            $clase      = 'imagenAdicionar enlaceAjax';
            $id         = 'imagenAdicionar';
            $opciones   = array('ayuda' => 'Click para adicionar', 'title' => $url);
            $codigo     = HTML::imagen($ruta, $clase, $id, $opciones);
        }
        return $codigo;
    }  
     
    /**
     * Generar código HTML para resaltar una frase con <span>
     * 
     * @param string $contenido contenido a ser incluido en la llamada a este método
     * @param string $clase clase css utilizada por este objeto del DOM
     * @param string $id identificador de este objeto en el DOM
     * @param array $opciones arreglo de opciones o atributos a ser añadidas a este item
     * @return string|NULL código HTML a ser interpretado por el cliente web
     */
    static function frase($contenido, $clase = NULL, $id = NULL, $opciones = NULL) {
        $codigo = '     <span';

        if (!empty($clase) ) {
            $codigo .= ' class="'.$clase.'"';
        }

        if (!empty($id) ) {
            $codigo .= ' id="'.$id.'"';
        }

        if (!empty($opciones) ) {

            foreach ($opciones as $atributo => $valor) {
                $codigo .=  $atributo.' = "'.$valor.'"';
            }
        }

        $codigo .= '>';

        if ($contenido !== '' && $contenido !== NULL) {
            $codigo .= $contenido;
        }

        $codigo .= '</span>';

        return $codigo;
    }

    /**
     * Generar código HTML para un contenedor (div)
     * 
     * @param string $contenido contenido a ser incluido en la llamada a este método
     * @param string $clase clase css utilizada por este objeto del DOM
     * @param string $id identificador de este objeto en el DOM
     * @param array $opciones arreglo de opciones o atributos a ser añadidas a este item
     * @return string|NULL código HTML a ser interpretado por el cliente web
     */
    static function contenedor($contenido = NULL, $clase = NULL, $id = NULL, $opciones = NULL) {
        $codigo = '     <div';

        if (!empty($id)) {
            $codigo .= ' id="'.$id.'"';
        }

        if (!empty($clase)) {
            $codigo .= ' class="'.$clase.'"';
        }

        if (!empty($opciones) && is_array($opciones)) {

            foreach ($opciones as $atributo => $valor) {
                $codigo .=  $atributo.' = "'.$valor.'"';
            }
        }

        $codigo .= '>';

        if ($contenido !== '' && $contenido !== NULL) {
            $codigo .=      $contenido;
        }

        $codigo .= '     </div>';

        return $codigo;
    }
    
    /**
     * Generar código HTML para un contenedor canvas de HTML5, muy importante
     * pasar siempre en el arreglo de opciones el ancho (width) y el alto (height)
     * 
     * @param string $contenido contenido a ser incluido en la llamada a este método
     * @param string $clase clase css utilizada por este objeto del DOM
     * @param string $id identificador de este objeto en el DOM
     * @param array $opciones arreglo de opciones o atributos a ser añadidas a este item
     * @return string|NULL código HTML a ser interpretado por el cliente web
     */
    static function canvas($contenido = NULL, $clase = NULL, $id = NULL, $opciones = NULL) {
        $codigo = '     <canvas';

        if (!empty($id)) {
            $codigo .= ' id="'.$id.'"';
        }

        if (!empty($clase)) {
            $codigo .= ' class="'.$clase.'"';
        }

        if (!empty($opciones) && is_array($opciones)) {

            foreach ($opciones as $atributo => $valor) {
                $codigo .=  $atributo.' = "'.$valor.'"';
            }
        }

        $codigo .= '>';

        if ($contenido !== '' && $contenido !== NULL) {
            $codigo .=      $contenido;
        }

        $codigo .= '     </canvas>';

        return $codigo;
        
    }    
    
    

    /**
     * Generar código HTML para insertar un enlace
     * 
     * @global array $configuracion arreglo global de configuraciones del sistema
     * @param string $texto texto a ser mostrado por este elemento
     * @param string $destino URI relativa del destino "action" del form
     * @param string $clase clase css utilizada por este objeto del DOM
     * @param string $id identificador de este objeto en el DOM
     * @param array $opciones arreglo de opciones o atributos a ser añadidas a este item
     * @return string|NULL código HTML a ser interpretado por el cliente web
     */
    static function enlace($texto, $destino = NULL, $clase = NULL, $id = NULL, $opciones = NULL) {
        global $configuracion;

        if (empty($destino)) {
            $destino = $texto;
        }

        $codigo = '     <a href="'.$destino.'"';

        if (!empty($clase)) {
            $codigo .= ' class="'.$clase.'"';
        }

        if (!empty($id)) {
            $codigo .= ' id="'.$id.'"';
        }

        $servidor = addslashes($configuracion['SERVIDOR']['principal']);

        if (preg_match("|^(https?\:\/\/)|", $destino) && !preg_match("|(^".$servidor.")|", $destino)) {
            $codigo .= ' target = "_blank"';
        }

        if (!empty($opciones) && is_array($opciones)) {

            foreach ($opciones as $atributo => $valor) {
                $codigo .=  ' '.$atributo.' = "'.$valor.'" ';
            }
        }

        $codigo .= '>';

        if (!empty($texto)) {
            $codigo .= $texto;
        }

        $codigo .= '</a>';

        return $codigo;
    }

    /**
     * Generar código HTML para insertar un párrafo
     * 
     * @param string $contenido contenido a ser incluido en la llamada a este método
     * @param string $clase clase css utilizada por este objeto del DOM
     * @param string $id identificador de este objeto en el DOM
     * @param array $opciones arreglo de opciones o atributos a ser añadidas a este item = array() -> arreglo de opciones para agregar atributos al parrafo
     * @return string|NULL código HTML a ser interpretado por el cliente web
     */
    static function parrafo($contenido = NULL, $clase = NULL, $id = NULL, $opciones = NULL) {
        $codigo = '     <p';

        if (!empty($clase)) {
            $codigo .= ' class="'.$clase.'"';
        }

        if (!empty($id)) {
            $codigo .= ' id="'.$id.'"';
        }

        if (!empty($opciones) && is_array($opciones)) {

            foreach ($opciones as $atributo => $valor) {
                $codigo .=  $atributo.' = "'.$valor.'"';
            }
        }

        $codigo .= '>';

        if ($contenido !== '' && $contenido !== NULL) {
            $codigo .=  $contenido;
        }

        $codigo .= '     </p>';

        return $codigo;
    }

    /**
     * Generar código HTML para insertar una lista
     * 
     * @param string $contenido contenido a ser incluido en la llamada a este método
     * @param string $clase clase css utilizada por este objeto del DOMLista
     * @param string $clase clase css utilizada por este objeto del DOMItems
     * @param string $id identificador de este objeto en el DOM
     * @param array $opciones arreglo de opciones o atributos a ser añadidas a este item
     * @return null|string
     */
    static function lista($contenido = NULL, $claseLista = NULL, $claseItems = NULL, $id = NULL, $opciones = NULL) {

        if (!is_array($contenido) || !count($contenido)) {
            return NULL;
        }

        $codigo = '     <ul';

        if (!empty($claseLista)) {
            $codigo .= ' class="'.$claseLista.'"';
        }

        if (!empty($id)) {
            $codigo .= ' id="'.$id.'"';
        }

        if (!empty($opciones) && is_array($opciones)) {

            foreach ($opciones as $atributo => $valor) {
                $codigo .=  $atributo.' = "'.$valor.'"';
            }
        }

        $codigo .= '>';

        foreach ($contenido as $item) {
            $codigo .= '      <li';

            if (!empty($claseItems)) {
                $codigo .= ' class = "'.$claseItems.'"';
            }

            $codigo .= '>'.$item.'</li>';
        }

        $codigo .= '     </ul>';

        return $codigo;
    }

    /**
     * Generar código HTML para insertar una imagen
     * 
     * @param string $ruta ruta de la imágen ya sea relativa o absoluta
     * @param string $clase clase css utilizada por este objeto del DOM
     * @param string $id identificador de este objeto en el DOM
     * @param array $opciones arreglo de opciones o atributos a ser añadidas a este item
     * @return string|NULL código HTML a ser interpretado por el cliente web
     */
    static function imagen($ruta, $clase = NULL, $id = NULL, $opciones = NULL) {
        $codigo = '     <img src="'.$ruta.'"';

        if (!empty($clase)) {
            $codigo .= ' class="'.$clase.'"';
        }

        if (!empty($id)) {
            $codigo .= ' id="'.$id.'"';
        }

        if (!empty($opciones) && is_array($opciones)) {

            if (!array_key_exists('alt', $opciones)) {
                $codigo .= ' alt=""';
            }

            foreach ($opciones as $atributo => $valor) {
                $codigo .=  $atributo.' = "'.$valor.'"';
            }

        } else {
            $codigo .= ' alt=""';
        }

        $codigo .= ' />';

        return $codigo;
    }

    /**
     * Generar código HTML para insertar un bloque
     * 
     * @param string $id identificador de este objeto en el DOM
     * @param string $titulo texto a ser mostrado en la cabecera del bloque
     * @param string $contenido contenido a ser incluido en la llamada a este método
     * @param string $clase clase css utilizada por este objeto del DOMTitulo
     * @param string $clase clase css utilizada por este objeto del DOMContenido
     * @return string|NULL código HTML a ser interpretado por el cliente web
     */
    static function bloque($id, $titulo, $contenido, $claseTitulo = NULL, $claseContenido = NULL) {
        $codigo  = '<div id="'.$id.'" class="bloque ui-widget ui-corner-all">';     
        $codigo .= '<div class="encabezadoBloque '.$claseTitulo.' "><span class ="bloqueTitulo spanEncabezadoBloque">'.$titulo.'</span></div>';
        $codigo .= "<div class=\"contenidoBloque $claseContenido\">";
        $codigo .= $contenido;
        $codigo .= '</div>';
        $codigo .= '</div>';
        
        return $codigo;
    }

    /**
     * Generar código HTML para insertar el bloque de las noticias
     * 
     * @param string $id identificador de este objeto en el DOM
     * @param string $titulo exto a ser mostrado en la cabecera del bloque
     * @param string $contenido contenido a ser incluido en la llamada a este método
     * @param string $clase clase css utilizada por este objeto del DOMContenido
     * @return string|NULL código HTML a ser interpretado por el cliente web
     */
    static function bloqueNoticias($id, $titulo, $contenido, $claseContenido = NULL) {
        $codigo  = '<div id="'.$id.'" class="bloque ui-widget ui-corner-all">';
        $codigo .= '<div class="tituloNoticias">'.$titulo.'</div>';
        $codigo .= '<div class="bloqueResumenNoticias '.$claseContenido.'">';
        $codigo .= $contenido;
        $codigo .= '</div>';
        $codigo .= '</div>';
        return $codigo;
    }

    /**
     * Función que genera el código HTML para un formulario
     *
     * @global array $configuracion arreglo global de configuraciones del sistema
     * @param string $destino URI relativa del destino "action" del form
     * @param string $contenido contenido a ser incluido en la llamada a este método
     * @param string $metodo P o G, determina si el metodo es POST o GET
     * @param boolean $incluyeArchivos determina si el formulario va a incluir archivos o no
     * @param string $id identificador de este objeto en el DOM
     * @param array $opciones arreglo de opciones o atributos a ser añadidas a este item
     * @param string $name nombre del formulario
     * @return string|NULL código HTML a ser interpretado por el cliente web 
     */
    static function forma($destino, $contenido, $metodo = 'P', $incluyeArchivos = false, $id = NULL, $opciones = NULL, $name = NULL) {
        global $configuracion;

        $codigo = '<form action="'.$destino.'"';

        if (!empty($id) ) {
            $codigo .= ' id="'.$id.'"';
        }

         if (!empty($name) ) {
            $codigo .= ' name="'.$name.'"';
        }
              

        if (strtoupper($metodo) == 'P') {
            $codigo .= ' method="post"';

        } elseif (strtoupper($metodo) == 'G') {
            $codigo .= ' method="get"';

        } else {
            $codigo .= ' method="post"';
        }

        if ($incluyeArchivos) {
            $codigo .= ' enctype="multipart/form-data"';
        }

        if (!empty($opciones) && is_array($opciones)) {

            foreach ($opciones as $atributo => $valor) {
                $codigo .=  $atributo.' = "'.$valor.'"';
            }
        }

        $codigo .= '>';
        $codigo .= '<fieldset>';

        if ($incluyeArchivos) {
            $codigo .= '     <input type="hidden" name="MAX_FILE_SIZE" value="'.$configuracion["DIMENSIONES"]["maximoPesoArchivo"].'" />';
        }

        $codigo .= $contenido;
        $codigo .= '</fieldset>';
        $codigo .= '</form>';
        
        return $codigo;
        
    }

    /**
     * Generar código HTML para campo de captura de texto de una línea
     * 
     * @param string $nombre nomnbre del campo de texto
     * @param int $longitud largo maximo del campo de texto
     * @param int $limite limite de caracteres a introducir en este campo de texto
     * @param string $valorInicial valor por defecto
     * @param string $clase clase css
     * @param string $id id unico del objeto del DOM
     * @param array $opciones arreglo de opciones que se le pasan al objeto del DOM
     * @param string $ayuda texto que se mostrará en el tooltip
     * @param string $adicionar representa la URL del metodo adicionar del modulo que pertenece al autocompletable 
     * @param string $idCampoOculto representa al int identificador autonumerico en caso de que sea autocompletable
     * @param string $valorCampoOculto valor por defecto del campo oculto
     * @return string|NULL código HTML a ser interpretado por el cliente web texto HTML que representa el campo de texto 
     */
    static function campoTexto($nombre, $longitud, $limite = NULL, $valorInicial = NULL, $clase = NULL, $id = NULL, $opciones = NULL, $ayuda = NULL, $adicionar = NULL, $idCampoOculto = NULL, $valorCampoOculto = '') {
        $codigo = '     <input type="text" name="'.$nombre.'" size="'.$longitud.'"';

        if (!empty($limite) && is_int($limite)) {
            $codigo .= ' maxlength="'.$limite.'"';
        }

        if (!empty($valorInicial)) {
            $codigo .= ' value="'.$valorInicial.'"';
        }

        if (!empty($clase)) {
            $codigo .= ' class="'.$clase.'"';
        }

        if (!empty($id)) {
            $codigo .= ' id="'.$id.'"';
        }

        if (!empty($opciones) && is_array($opciones)) {

            foreach ($opciones as $atributo => $valor) {
                $codigo .=  $atributo.' = "'.$valor.'"';
            }
        } 
        
        $codigo .= ' />';
        
         if (!empty($idCampoOculto)) {
             $codigo .= HTML::campoOculto($idCampoOculto, $valorCampoOculto, '', array());
         }
        
         if (!empty($ayuda)) {
             $codigo .= HTML::ayuda($ayuda);
         }
         if (!empty($adicionar)) {
             $codigo .= HTML::formaAdicionar($adicionar);
         }         
        
        return $codigo;
    }

    /**
     * Generar código HTML para campo de texto oculto
     * 
     * @param string $nombre nombre del campo
     * @param string $valorInicial valor inicial a ser mostrado
     * @param string $id identificador de este objeto en el DOM
     * @param array $opciones arreglo de opciones o atributos a ser añadidas a este item
     * @param string $clase clase css utilizada por este objeto del DOM
     * @return string|NULL código HTML a ser interpretado por el cliente web
     */
    static function campoOculto($nombre, $valorInicial = NULL, $id = NULL, $opciones = NULL, $clase = NULL) {
        $codigo = '     <input type="hidden" name="'.$nombre.'" value="'.$valorInicial.'"';

        if (!empty($id)) {
            $codigo .= ' id="'.$id.'"';
        }
        
        if (!empty($clase)) {
            $codigo .= ' class="'.$clase.'"';
        }        

        if (!empty($opciones) && is_array($opciones)) {

            foreach ($opciones as $atributo => $valor) {
                $codigo .=  $atributo.' = "'.$valor.'"';
            }
        }

        $codigo .= ' />';
        return $codigo;
    }

    /**
     * Función que devuelve un campo para la carga de archivos desde el computador cliente
     * 
     * @param string $nombre nombre del campo
     * @param string $valorInicial texto que aparecerá en el campo
     * @param string $id identificador del objeto del DOM
     * @param array $opciones arreglo de opciones o atributos a ser añadidas a este item arreglo de opciones para ser puestos como atributos del campo
     * @param string $ayuda texto que sera mostrado en el tooltip de ayuda
     * @return string|NULL código HTML a ser interpretado por el cliente web código HTML del campo 
     */
    static function campoArchivo($nombre, $valorInicial = NULL, $id = NULL, $opciones = NULL, $ayuda = NULL) {
        $codigo = '     <input type="file" name="'.$nombre.'" value="'.$valorInicial.'"';

        if (!empty($id)) {
            $codigo .= ' id="'.$id.'"';
        }
               

        if (!empty($opciones) && is_array($opciones)) {

            foreach ($opciones as $atributo => $valor) {
                $codigo .=  $atributo.' = "'.$valor.'"';
            }
        }

        $codigo .= ' />';
        
         if (!empty($ayuda)) {
             $codigo .= HTML::ayuda($ayuda);
         }         
        return $codigo;
    }

    /**
     * Generar código HTML para campo de chequeo (checkbox)
     * 
     * @param string $nombre nombre del campo de chequeo
     * @param boolean $chequeado determina se aparecerá checkeado o no
     * @param string $clase clase que tendrá el campo
     * @param string $id identificador del campo de chequeo
     * @param array $opciones arreglo de opciones para ser puestos como atributos del campo
     * @param string $etiqueta texto que aparecerá previo al campo de chequeo
     * @return string|NULL código HTML a ser interpretado por el cliente web | NULL código HTML para ser mostrado e interpretado por el cliente web
     */
    static function campoChequeo($nombre, $chequeado = false, $clase = NULL, $id = NULL, $opciones = NULL, $etiqueta = NULL) {
        
        if(isset($etiqueta)){
            $etiqueta = HTML::frase($etiqueta, 'medioMargenSuperior');
        }
        $codigo = '     <input type="checkbox" name="'.$nombre.'"';

        if (!empty($id)) {
            $codigo .= ' id="'.$id.'"';
        }

        if (!empty($clase)) {
            $codigo .= ' class="'.$clase.'"';
        }

        if ($chequeado) {
            $codigo .= ' checked="true"';
        }

        if (!empty($opciones) && is_array($opciones)) {

            foreach ($opciones as $atributo => $valor) {
                $codigo .=  $atributo.' = "'.$valor.'"';
            }
        }

        $codigo .= ' />';
        return $etiqueta.$codigo;
    }

    /**
     * Generar código HTML para un Radio Button
     * 
     * @param string $nombre nombre del campo
     * @param boolean $chequeado determina si aparecerá checkeado o no
     * @param string $clase clase css utilizada por este objeto del DOM
     * @param string $valor valor a ser enviado por este campo
     * @param array $opciones arreglo de opciones o atributos a ser añadidas a este item
     * @param string $etiqueta texto que aparece previo a este campo
     * @return string|NULL código HTML a ser interpretado por el cliente web | NULL código HTML para ser mostrado e interpretado por el cliente web
     */
    static function radioBoton($nombre, $chequeado = NULL, $clase = NULL, $valor = NULL, $opciones = NULL, $etiqueta = NULL) {
        
        if(isset($etiqueta)){
            $etiqueta = HTML::frase($etiqueta, 'medioMargenSuperior');
        }
        $codigo = '     <input type="radio" name="'.$nombre.'"';

        if (!empty($valor)) {
            $codigo .= ' value="'.$valor.'"';
        }

        if (!empty($clase)) {
            $codigo .= ' class="'.$clase.'"';
        }

        if ($chequeado) {
            $codigo .= ' checked ';
        }

        if (!empty($opciones) && is_array($opciones)) {

            foreach ($opciones as $atributo => $valor) {
                $codigo .=  $atributo.' = "'.$valor.'"';
            }
        }

        $codigo .= ' />';
        return $etiqueta.$codigo;
    }

    /**
     * Generar código HTML para campo de captura de texto de múltiples línea
     * 
     * @param string $nombre nombre del campo
     * @param int $filas número de filas a contener por este campo
     * @param int $columnas número de columnas a contener por este campo
     * @param string $valorInicial valor inicial a ser mostrado
     * @param string $clase clase css utilizada por este objeto del DOM
     * @param string $id identificador de este objeto en el DOM
     * @param array $opciones arreglo de opciones o atributos a ser añadidas a este item
     * @return string|NULL código HTML a ser interpretado por el cliente web | NULL código HTML para ser mostrado e interpretado por el cliente web
     */
    static function areaTexto($nombre, $filas, $columnas, $valorInicial = NULL, $clase = NULL, $id = NULL, $opciones = NULL, $ayuda = '') {
        $codigo = '     <textarea name="'.$nombre.'" rows="'.$filas.'" cols="'.$columnas.'"';

        if (!empty($clase)) {
            $codigo .= ' class="'.$clase.'"';
        }

        if (!empty($id) ) {
            $codigo .= ' id="'.$id.'"';
        }

        if (!empty($opciones) && is_array($opciones)) {

            foreach ($opciones as $atributo => $valor) {
                $codigo .=  $atributo.' ="'.$valor.'"';
            }
        }

        $codigo .= '>'.$valorInicial.'</textarea>';
         if (!empty($ayuda)) {
             $codigo .= HTML::ayuda($ayuda);
         }
        return $codigo;
    }

    /**
     * Generar código HTML para presentar nombres de los campos (etiquetas)
     * 
     * @param string $texto texto a ser mostrado por este elemento
     * @param string $clase clase css utilizada por este objeto del DOM
     * @param string $id identificador de este objeto en el DOM
     * @param array $opciones arreglo de opciones o atributos a ser añadidas a este item
     * @return string|NULL código HTML a ser interpretado por el cliente web | NULL código HTML para ser mostrado e interpretado por el cliente web
     */
    static function etiqueta($texto, $clase = NULL, $id = NULL, $opciones = NULL) {
        $codigo = '     <span';

        if (!empty($clase)) {
            $codigo .= ' class="etiqueta '.$clase.'"';

        } else {
            $codigo .= ' class="etiqueta"';
        }

        if (!empty($id)) {
            $codigo .= ' id="'.$id.'"';
        }

        if (!empty($opciones) && is_array($opciones)) {

            foreach ($opciones as $atributo => $valor) {
                $codigo .=  $atributo.' = "'.$valor.'"';
            }
        }

        $codigo .= '>'.$texto.':</span>';

        return $codigo;
    }

    /**
     * Generar código HTML para campo de captura de palabra clave
     * 
     * @param string $nombre nombre del campo
     * @param int $longitud longitud del campo
     * @param int $limite máximo número de caracteres que pueden ser introducidos
     * @param string $valorInicial valor inicial a ser mostrado
     * @param string $clase clase css utilizada por este objeto del DOM
     * @param string $id identificador de este objeto en el DOM
     * @param array $opciones arreglo de opciones o atributos a ser añadidas a este item
     * @return string|NULL código HTML a ser interpretado por el cliente web
     */
    static function campoClave($nombre, $longitud, $limite = NULL, $valorInicial = NULL, $clase = NULL, $id = NULL, $opciones = NULL) {
        $codigo = '     <input type="password" name="'.$nombre.'" size="'.$longitud.'"';

        if (!empty($limite) && is_int($limite)) {
            $codigo .= ' maxlength="'.$limite.'"';
        }

        if (!empty($valorInicial)) {
            $codigo .= ' value="'.$valorInicial.'"';
        }

        if (!empty($clase)) {
            $codigo .= ' class="'.$clase.'"';
        }

        if (!empty($id)) {
            $codigo .= ' id="'.$id.'"';
        }

        if (!empty($opciones) && is_array($opciones)) {

            foreach ($opciones as $atributo => $valor) {
                $codigo .=  $atributo. ' = "'.$valor.'"';
            }
        }

        $codigo .= ' />';
        return $codigo;
    }

    /**
     * Generar lista desplegable <select>
     * 
     * @param string $nombre nombre del campo
     * @param string $contenido contenido a ser incluido en la llamada a este método
     * @param string $valorInicial valor inicial a ser mostrado
     * @param string $clase clase css utilizada por este objeto del DOM
     * @param string $id identificador de este objeto en el DOM
     * @param string $primerItem determina cual será el valor del primer item en la lista
     * @param array $opciones arreglo de opciones o atributos a ser añadidas a este item
     * @param string $ayuda texto a ser mostrado como tooltip de ayuda
     * @return string|NULL código HTML a ser interpretado por el cliente web | NULL código HTML para ser mostrado e interpretado por el cliente web
     */
    static function listaDesplegable($nombre, $contenido, $valorInicial = NULL, $clase = NULL, $id = NULL, $primerItem = NULL, $opciones = NULL, $ayuda = NULL, $adicionar = NULL) {

        $codigo = '     <select name="'.$nombre.'"';

        if (!empty($id)) {
            $codigo .= ' id="'.$id.'"';
        }

        if (!empty($clase)) {
            $codigo .= ' class="'.$clase.'"';
        }

        if (!empty($opciones) && is_array($opciones)) {

            foreach ($opciones as $atributo => $valor) {
                $codigo .=  $atributo.' ="'.$valor.'"';
            }
        }

        $codigo .= '>';

        if (!empty($primerItem)) {
            $codigo .= '     <option>'.$primerItem.'</option>';
        }
            
        if (is_array($contenido)) {

            foreach ($contenido as $valor => $texto) {

                if ($valor == $valorInicial) {
                    $elegido = 'selected';
                } else {
                    $elegido = '';
                }

                $codigo .= '  <option '.$elegido.' value="'.$valor.'">'.$texto.'</option>';
            }
        }

        $codigo .= '     </select>';
        
         if (!empty($adicionar)) {
             $codigo .= HTML::formaAdicionar($adicionar);
         }         
        
         if (!empty($ayuda)) {
            $codigo .= HTML::ayuda($ayuda);
        }

        return $codigo;
    }

    /**
     * Generar código HTML de un botón <button>
     *
     * @global array $configuracion arreglo global de configuraciones del sistema
     * @param string $icono cadena a ser traducida por el arreglo global de configuraciones en a posicion "ICONOS"
     * @param string $texto texto a ser mostrado por este elemento
     * @param string $clase clase css utilizada por este objeto del DOM
     * @param string $nombre nombre del campo
     * @param string $id identificador de este objeto en el DOM
     * @param string $accion determina la accion a ser utilizada por este item
     * @param array $opciones arreglo de opciones o atributos a ser añadidas a este item
     * @return string|NULL código HTML a ser interpretado por el cliente web 
     */
    static function boton($icono = NULL, $texto = NULL, $clase = NULL, $nombre = NULL, $id = NULL, $accion = NULL, $opciones = NULL) {
        global $configuracion;

        $codigo = '     <button ';

        if (empty($texto)) {
            $claseBoton = 'botonIcono';

        } else {

            if (empty($icono)) {
                $claseBoton = 'botonTexto';

            } else {
                $claseBoton = 'botonTextoIcono';
            }
        }

        if (!empty($nombre)) {
            $codigo .= ' name="'.$nombre.'"';
        }

        if (!empty($id)) {
            $codigo .= ' id="'.$id.'"';
        }

        if (!empty($accion)) {
            $codigo .= ' onclick="'.$accion.'"';
        }

        if (!empty($clase)) {
            $codigo .= ' class="'.$claseBoton .' '. $clase.'"';

        } else {
            $codigo .= ' class="'.$claseBoton.'" ';
        }

        if (!empty($opciones) && is_array($opciones)) {

            foreach ($opciones as $atributo => $valor) {
                $codigo .=  $atributo. ' = "'.$valor.'"';
            }
        }

        if (!empty($icono)) {

            if (array_key_exists($icono, $configuracion['ICONOS'])) {
                $icono   = 'ui-icon-'.$configuracion['ICONOS'][$icono];
                $codigo .= ' title='.$icono.'';
            }
        }

        $codigo .= '>';

        if (!empty($texto)) {
            $codigo .= $texto;
        }

        $codigo .= '</button>';

        return $codigo;
    }

    /**
     * Generar código HTML para visualizar un botón
     * 
     * @param string $contenido contenido a ser incluido en la llamada a este método
     * @param string $clase clase css utilizada por este objeto del DOM
     * @param string $nombre nombre del campo
     * @param string $id identificador de este objeto en el DOM
     * @param string $accion determina la accion a ser utilizada por este item
     * @param array $opciones arreglo de opciones o atributos a ser añadidas a este item
     * @return string|NULL código HTML a ser interpretado por el cliente web
     */
    static function botonPersonalizado($contenido, $clase = NULL, $nombre = NULL, $id = NULL, $accion = NULL, $opciones = NULL) {

        $codigo = '     <button ';

        if (!empty($nombre)) {
            $codigo .= ' name="'.$nombre.'"';
        }

        if (!empty($id)) {
            $codigo .= ' id="'.$id.'"';
        }

        if (!empty($accion)) {
            $codigo .= ' onclick="'.$accion.'"';
        }

        if (!empty($clase) ) {
            $codigo .= ' class="'. $clase.'"';

        }

        if (!empty($opciones) && is_array($opciones)) {

            foreach ($opciones as $atributo => $valor) {
                $codigo .=  $atributo .' = "'.$valor.'"';
            }
        }

        $codigo .= '>';

        if (!empty($contenido)) {
            $codigo .= $contenido;
        }

        $codigo .= '</button>';

        return $codigo;
    }    

    /**
     * Generar código HTML para visualizar un botón solamente con una imagen
     * 
     * @param string $ruta ruta de la imágen
     * @param string $title texto del botón
     * @return string|NULL código HTML a ser interpretado por el cliente web 
     */
    static function botonImagen($ruta, $title = '') {

        $codigo = '     <button ';

        $codigo .= ' onclick="submit"';
      
        $codigo .= ' title="'.$title.'"';   

        $codigo .= ' class=""';       

        $codigo .= '>';

        $codigo .= HTML::imagen("$ruta");
        
        $codigo .= '</button>';

        return $codigo;
    }
    
 


    /**
     * Generar código HTML para insertar juego de pestañas de altura variable
     * 
     * @param string $id identificador de este objeto en el DOM
     * @param array $pestanas código HTML con las pestañas a ser cargadas
     * @return string|NULL código HTML a ser interpretado por el cliente web 
     */
    static function pestanas($id, $pestanas) {
        $codigo  = '     <div id="'.$id.'" class="pestanas margenInferior">';

        if (is_array($pestanas)) {
            $contador   = 0;
            $titulos    = '';
            $contenidos = '';

            foreach ($pestanas as $titulo => $contenido) {
                $contador++;
                $titulos    .= '      <li><a href=\'#'.$id.'_'.$contador.'\'>'.$titulo.'</a></li>';
                $contenidos .= '      <div id=\''.$id.'_'.$contador.'\' class=\'contenidoPestana\'>';
                $contenidos .=       $contenido;
                $contenidos .= '      </div>';
            }

            $codigo .= '     <ul class=\'listaPestanas\'>';
            $codigo .= $titulos;
            $codigo .= '     </ul>';
            $codigo .= $contenidos;
        }

        $codigo .= '     </div>';

        return $codigo;
    }
    
    /**
     * Generar código HTML para insertar juego de pestañas de altura variable
     * 
     * @param string $id identificador de este objeto en el DOM
     * @param array $pestanas arreglo con el texto de las pestañas a incluir
     * @param string $clase clase css utilizada por este objeto del DOM
     * @param string $claseTitulos clase css utilizada por los titulos de las pestañas
     * @param string $idTitulos identificador de cada uno de los titulos
     * @return string|NULL código HTML a ser interpretado por el cliente web 
     */
    static function pestanas2($id, $pestanas, $clase = '', $claseTitulos = '', $idTitulos = array()) {
        
        $codigo  = '     <div id="'.$id.'" class="pestanas margenInferior '.$clase.' ">';

        if (is_array($pestanas)) {
            $contador   = 0;
            $contador2  = 0;
            $titulos    = '';
            $contenidos = '';
            
            foreach ($pestanas as $titulo => $contenido) {
                $contador++;
                
                $idTitulo2 = '';
                
                if (isset($idTitulos[$contador2])) {
                    $idTitulo2 = $idTitulos[$contador2];
                }
                
                $titulos    .= '      <li><a href=\'#'.$id.'_'.$contador.'\' class="'.$claseTitulos.'" id="'.$idTitulo2.'">'.$titulo.'</a></li>';
                $contenidos .= '      <div id=\''.$id.'_'.$contador.'\' class=\'contenidoPestana\'>';
                $contenidos .= $contenido;
                $contenidos .= '      </div>';
                $contador2++;
                
            }

            $codigo .= '     <ul class=\'listaPestanas\'>';
            $codigo .= $titulos;
            $codigo .= '</ul>';
            //$codigo .= '     ';
            $codigo .= $contenidos;
        }
 
        $codigo .= '     </div>';

        return $codigo;
    }

    /**
     * Función para generar un objeto acordeon jquery-ui en el DOM
     * 
     * @param array $titulos arreglotitulos de las partes
     * @param string $contenido contenido a ser incluido en la llamada a este métodos
     * @param string $id identificador de este objeto en el DOM
     * @param string $clase clase css utilizada por este objeto del DOMContenedor
     * @param array $opciones arreglo de opciones o atributos a ser añadidas a este item
     * @return string|NULL código HTML a ser interpretado por el cliente web
     */
    static function acordeon($titulos, $contenidos, $id = NULL, $claseContenedor = NULL, $opciones = NULL) {

        $codigo  = '     <div ';

        if (!empty($claseContenedor)) {
            $codigo .= ' class="acordeon '.$claseContenedor.'"';

        } else {
            $codigo .= ' class="acordeon"';
        }

        if (!empty($id)) {
            $codigo .= ' id="'.$id.'"';
        }

        if (!empty($opciones) && is_array($opciones)) {

            foreach ($opciones as $atributo => $valor) {
                $codigo .=  $atributo . '="'.$valor.'"';
            }
        }

        $codigo .= '>';

        for ($i = 0; $i < count($titulos); $i++) {
            
            $codigo2 = '      <h4';

            $codigo2 .= '><a href=\'#\'>'.$titulos[$i].'';
            $codigo2 .= '<div class = \'borde\'></div></a></h4>';
            $codigo  .= '<div class =\'acordion\'>'.$codigo2.'</div>';
            
            $codigo  .= '      <div class=\'contenidoAcordeon\'>';
            $codigo  .= '      '.$contenidos[$i];
            $codigo  .= '      </div>';
        }

        $codigo .= '     </div>';  

        $codigo .= '';
        return $codigo;
    }

    /**
     * Funcion que recibe código HTML de dos contenedores <div> y los ubica
     * a la derecha e izquierda respectivamente, utilizado por ejemplo en
     * formularios donde se desea tener un campo alineado a la derecha y el otro 
     * a la izquierda.
     *
     * @param string $campo1 código HTML para guardar en el div derecho
     * @param string $campo2 código HTML para guardar en el div derecho
     * @param string $claseContenedor clase del contenedor padre
     * @param string $idContenedor clase del contenedor padre
     * @return cadena código HTML que devuelve un <div> con otros dos <div> ubicados simetricamente a la derecha e izquierda 
     */
    static function contenedorCampos($campo1, $campo2, $claseContenedor = '', $idContenedor = ''){
        $codigo = '';
        $contenedorIzquierdo = HTML::contenedor($campo1, 'ancho50por100');
        $contenedorDerecho   = HTML::contenedor($campo2, 'ancho50por100');
        $codigo .= HTML::contenedor($contenedorIzquierdo.$contenedorDerecho, 'contenedorCampos '.$claseContenedor, $idContenedor);
        
        return $codigo;
    }

    /**
     * Función que devuelve el código HTML de una tabla (<table></table>)
     *
     * @param arreglo $columnas columnas que va a formar la tabla
     * @param arreglo $filas arreglo de arreglos para crear las filas de la tabla
     * @param cadena $clase clase css de la tabla
     * @param cadena $id id de la tabla
     * @param arreglo $claseColumnas clases css que se van a aplicar a las columnas de la tabla
     * @param arreglo $claseFilas clases css que se van a aplicar a las filas de la tabla
     * @param arreglo $opciones arreglo de opciones para agregar a la tabla (ej: array('onclick' => 'funcionOnclick()'))
     * @param arreglo $idFila arreglo que contiene identificadores unico para cada uno de los <tr> de la tabla
     * @return cadena código HTML de la tabla y su contenido
     */
    static function tabla($columnas, $filas, $clase = NULL, $id = NULL, $claseColumnas = array(), $claseFilas = array(), $opciones = array(), $idFila = NULL, $opcionesFilas = array()) {
        $codigo = '     <table ';

        if (!empty($id)) {
            $codigo .= ' id="'.$id.'"';
        }

        if (!empty($clase) ) {
            $codigo .= ' class="'.$clase.'"';
        }
        if (!empty($opciones) && is_array($opciones)) {

            foreach ($opciones as $atributo => $valor) {
                $codigo .=  $atributo.' = "'.$valor.'"';
            }
        }

        $codigo   .= '>';

        if (!empty($columnas)) {
            $codigo   .= '     <tr id="thead">';
            $contador  = 0;

                foreach ($columnas as $id => $columna) {
                    $codigo .= '     <th';

                    if (!empty($id)) {
                        $codigo .= ' id="'.$id.'"';
                    }

                    if (!empty($claseColumnas) && is_array($claseColumnas)) {
                        if (isset($claseColumnas[$contador])) {
                            $codigo .= ' class="'.$claseColumnas[$contador].'"';
                        }
                        
                    }

                    $codigo .= '>';
                    $codigo .= $columna.'</th>';
                    $contador++;
                }
            $codigo   .= '     </tr>';
        }

        if (!empty($filas)) {
            $contador1  = 0;
            foreach ($filas as $fila => $celdas) {
                $codigo   .= '     <tr';
                
                if (!empty($idFila) && is_array($idFila)) {
                        $codigo .= ' id="'.$idFila[$contador1].'"';
                }
                
                //agrego las opciones a cada fila (por ejemplo los atributos de los articulos en la tabla del listado de articulos de facturacion)
                if (!empty($opcionesFilas)) {
                    
                    if (isset($opcionesFilas[$contador1]) && is_array($opcionesFilas[$contador1])) {
                        
                        foreach ($opcionesFilas[$contador1] as $key => $val) {
                            $codigo .= ' '.$key.' = "'.$val.'" ';

                        }    
                        
                    }
  
                }
                
                $codigo .= '>';
                $contador  = 0;

                foreach ($celdas as $id => $celda) {
                    $codigo .= '     <td';

                    if (!empty($id)) {
                        $codigo .= ' id="'.$id.'"';
                    }

                    if (!empty($claseFilas) && is_array($claseFilas)) {
                        $codigo .= ' class="'.$claseFilas[$contador].'"';
                    }

                    $codigo .= '>';
                    $codigo .= $celda.'</td>';
                    $contador++;
                }

                $codigo .= '     </tr>';
                $contador1 ++;
            }
            
        }

        $codigo .= '     </table>';
        
        return $codigo;
        
    }

    /**
     * Función que devuelve un <div> con una clase sombra, muestra en el DOM una sombra
     * 
     * @return string|NULL código HTML a ser interpretado por el cliente web codigo HTML de un <div> con una clase sombra, muestra en el DOM una sombra
     */
    static function sombra() {
        return '     <div class="sombra"></div>';
    }
    
    /**
     * Función que devuelve un <div> con una clase sombra, muestra en el DOM una sombra
     * 
     * @return string|NULL código HTML a ser interpretado por el cliente web codigo HTML de un <div> con una clase sombra, muestra en el DOM una sombra
     */
    static function divisionHorizontal() {
        return '     <div class="divisionHorizontal"></div>';
    }    

    /**
     * Función que devuelve el código HTML de una tabla (<table></table>) utilizado en el principal de 
     * la mayoria de los modulos. contiene las opciones de paginación, click derecho, etc.
     *
     * @param arreglo $columnas columnas que va a formar la tabla
     * @param arreglo $filas arreglo de arreglos para crear las filas de la tabla
     * @param cadena $clase clase css de la tabla
     * @param cadena $id id de la tabla
     * @param arreglo $claseColumnas clases css que se van a aplicar a las columnas de la tabla
     * @param arreglo $claseFilas clases css que se van a aplicar a las filas de la tabla
     * @param arreglo $opciones arreglo de opciones para agregar a la tabla (ej: array('onclick' => 'funcionOnclick()'))
     * @param arreglo $idFila arreglo que contiene identificadores unico para cada uno de los <tr> de la tabla
     * @param arreglo $celdas revisar que se envia en este parametro
     * @param arreglo $atributos arreglo que contiene información especifica para una determinada tabla, se coloca en los <tr> en forma de atributos 
     * @return cadena código HTML de la tabla y su contenido
     */    
   static function tablaGrilla($columnas, $filas, $clase = NULL, $id = NULL, $claseColumnas = NULL, $claseFilas = NULL, $opciones = NULL, $idFila = NULL, $celdas = NULL, $atributos = array()) {
        global $textos;
        
       $codigo = '     <table ';
 
        if (!empty($id)) {
            $codigo .= ' id="'.$id.'"';
        }
 
        if (!empty($clase)) {
            $codigo .= ' class="'.$clase.'"';
        }
        if (!empty($opciones) && is_array($opciones)) {
 
            foreach ($opciones as $atributo => $valor) {
                $codigo .=  $atributo .' = "'.$valor.'"';
            }
        }
 
        $codigo   .= " cell-padding='5'>";
 
        if (!empty($columnas)) {
            $codigo   .= '     <tr class="cabeceraTabla noSeleccionable">';
            $contador  = 0;
 
                foreach ($columnas as $id => $columna) {
                    $codigo .= '     <th';
 
                    if (!empty($id)) {
                        $codigo .= ' id="'.$id.'"';
                    }

                    $check          = '';
                    $organizadores  = '';
                    
                    if (!empty($celdas) && is_array($celdas)) {//aqui recibo una cadena que trae el nombre del objeto y el nombre para hacer la consulta
                        
                        $data = explode("|", $celdas[$contador]);                        
                        $codigo .= ' nombreOrden="'.$data[0].'"';//en la posicion 0 traigo el nombre del objeto ej: nombreGrupo
                            $check  = HTML::campoChequeo($data[1], false, 'checkPatronBusqueda', 'checkPatronBusqueda'.($contador+1));//en la posicion 1 traigo el nombre para la consulta
                            $organizadores = '<div id="ascendente" ayuda="'.$textos->id('AYUDA_ASCENDENTE').'"></div> <div id ="descendente" ayuda="'.$textos->id('AYUDA_DESCENDENTE').'"></div>';
                     
                        
                    }
                    
                    if (!empty($claseColumnas) && is_array($claseColumnas)) {
                        $codigo .= ' class="columnaTabla '. $claseColumnas[$contador].'"';
                    } else {
                        $codigo .= ' class="columnaTabla "';
                    }                   
 
                    $codigo .= '>';                    
                    $codigo .= $organizadores .$columna . $check.'</th>';
                    $contador++;
                }
            $codigo   .= '  </tr>';
        }
 
        if (!empty($filas)) {
            $contador1  = 0;
            foreach ($filas as $fila => $celdas) {
                $codigo   .= '     <tr';
                if (!empty($idFila) && is_array($idFila)) {
                        $codigo .= ' id="'.$idFila[$contador1].'"';
                }
                if (!empty($claseFilas)) {
                       $codigo .= ' class="'.$claseFilas.'"';
                }
                
                if(!empty($atributos)){//si se esta solicitando la info de la tabla modal (posible cambiar de true a valor del modulo desde que se llama y hacerle los cambios necesarios)
                    //aqui le meto al tr todos los valores de la fila como atributos
                    $atri = "";
                    $contador7 = 0;
                    foreach ($celdas as $id => $valor) {
                        if(!stristr($valor, "<") && !stristr($valor, "\"")){
                           $atri .= ' atributo_'.$contador7.' = "'.str_replace('$ ', '', $valor).'"';//se reemplaza el simbolo pesos de los campos de precio
                           $contador7++;                            
                        }

                    }
                    $codigo .= $atri;                    
                    
                }                

                $codigo .= '>';
                $contador  = 0;
                
                foreach ($celdas as $id => $celda) {
                    $codigo .= '     <td class="'.$claseColumnas[$contador].'"';
 
                    if (!empty($id)) {
                        $codigo .= ' id="'.$id.'"';
                    }
 
                    $codigo .= ' >';
                    
                    $codigo .= $celda.'</td>';
                        
                    
                    $contador++;
                }
 
                $codigo .= '     </tr>';
                $contador1 ++;
            }
        }
 
        $codigo .= '     </table>';
        return $codigo;
    }

    /**
     * Función que devuelve el código HTML de una tabla (<table></table>) utilizado en algunas ventanas modales.
     *
     * @param arreglo $columnas columnas que va a formar la tabla
     * @param arreglo $filas arreglo de arreglos para crear las filas de la tabla
     * @param cadena  $clase clase css de la tabla
     * @param cadena  $id id de la tabla
     * @param arreglo $claseColumnas clases css que se van a aplicar a las columnas de la tabla
     * @param arreglo $claseFilas clases css que se van a aplicar a las filas de la tabla
     * @param arreglo $opciones arreglo de opciones para agregar a la tabla (ej: array('onclick' => 'funcionOnclick()'))
     * @param arreglo $idFila arreglo que contiene identificadores unico para cada uno de los <tr> de la tabla
     * @param arreglo $celdas revisar que se envia en este parametro
     * @return cadena código HTML de la tabla y su contenido
     */  
   static function tablaGrillaInterna($columnas, $filas, $clase = NULL, $id = NULL, $claseColumnas = NULL, $claseFilas = NULL, $opciones = NULL, $idFila = NULL, $celdas = NULL) {
        $codigo = '     <table ';
 
        if (!empty($id)) {
            $codigo .= ' id="'.$id.'"';
        }
 
        if (!empty($clase)) {
            $codigo .= ' class="'.$clase.'"';
        }
        if (!empty($opciones) && is_array($opciones)) {
 
            foreach ($opciones as $atributo => $valor) {
                $codigo .=  $atributo. ' = "'.$valor.'"';
            }
        }
 
        $codigo   .= '>';
 
        if (!empty($columnas)) {
            $codigo   .= '     <tr class="cabeceraTabla noSeleccionable">';
            $contador  = 0;
 
                foreach ($columnas as $id => $columna) {
                    $codigo .= '     <th';
 
                    if (!empty($id) ) {
                        $codigo .= ' id="'.$id.'"';
                    }
                    
                    if (!empty($claseColumnas) && is_array($claseColumnas)) {
                        if (isset($claseColumnas[$contador])) {
                            $codigo .= ' class=" columnaTabla '.$claseColumnas[$contador].'" ';
                        }
                        
                    }                    
 
                    $codigo .= '>';                    
                    $codigo .=  $columna  .'</th>';
                    $contador++;
                }
                
            $codigo   .= '  </tr>';
            
        }
 
        if (!empty($filas)) {
            $contador1  = 0;
            
            foreach ($filas as $fila => $celdas) {
                $codigo   .= '     <tr';
                
                if (!empty($idFila) && is_array($idFila)) {
                        $codigo .= ' id="'.$idFila[$contador1].'"';
                }
                if (!empty($claseFilas)) {
                       $codigo .= ' class="'.$claseFilas.'"';
                }             
                
                $codigo .= '>';
                $contador  = 0;
 
                foreach ($celdas as $id => $celda) {
                    $codigo .= '     <td';
 
                    if (!empty($id)) {
                        $codigo .= ' id="'.$id.'"';
                    }

                    $codigo .= '>';
                    $codigo .= $celda.'</td>';
                    $contador++;
                }
 
                $codigo .= '     </tr>';
                $contador1 ++;
            }
        }
 
        $codigo .= '     </table>';
        
        return $codigo;
        
    }    

    /**
     * Tabla que se encarga de poner todos los valores de cada fila como atributos eln la fila
     * 
     * @param type $columnas
     * @param type $filas
     * @param string $clase clase css utilizada por este objeto del DOM
     * @param string $id identificador de este objeto en el DOM
     * @param string $clase clase css utilizada por este objeto del DOMColumnas
     * @param string $clase clase css utilizada por este objeto del DOMFilas
     * @param array $opciones arreglo de opciones o atributos a ser añadidas a este item
     * @param string $id identificador de este objeto en el DOMFila
     * @param type $celdas
     * @return string|NULL código HTML a ser interpretado por el cliente web 
     */
   static function tablaGrillaListaArticulos($columnas, $filas, $clase = NULL, $id = NULL, $claseColumnas = NULL, $claseFilas = NULL, $opciones = NULL, $idFila = NULL, $celdas = NULL) {
        $codigo = '     <table ';
 
        if (!empty($id) ) {
            $codigo .= ' id="'.$id.'"';
        }
 
        if (!empty($clase)) {
            $codigo .= ' class="'.$clase.'"';
        }
        if (!empty($opciones) && is_array($opciones)) {
 
            foreach ($opciones as $atributo => $valor) {
                $codigo .=  $atributo .' = "'.$valor.'"';
            }
        }
 
        $codigo   .= '>';
 
        if (!empty($columnas)) {
            $codigo   .= '     <tr class="cabeceraTabla noSeleccionable">';
            $contador  = 0;
 
                foreach ($columnas as $id => $columna) {
                    $codigo .= '     <th';
 
                    if (!empty($id)) {
                        $codigo .= ' id="'.$id.'"';
                    }
                    
                    if (!empty($claseColumnas) && is_array($claseColumnas)) {
                        $codigo .= ' class="columnaTabla '.$claseColumnas[$contador].'"';
                    }                    
 
                    $codigo .= '>';                    
                    $codigo .=  $columna  .'</th>';
                    $contador++;
                }
            $codigo   .= '  </tr>';
        }
 
        if (!empty($filas)) {
            $contador1  = 0;
            foreach ($filas as $fila => $celdas) {
                $codigo   .= '     <tr';
                if (!empty($idFila) && is_array($idFila)) {
                        $codigo .= ' id="'.$idFila[$contador1].'"';
                }
                if (!empty($claseFilas)) {
                       $codigo .= ' class="'.$claseFilas.'"';
                }
                //aqui le meto al tr todos los valores de la fila como atributos
                $atributos = "";
                $contador7 = 0;
                foreach ($celdas as $id => $valor) {
                    $atributos .= ' atributo_'.$contador7.' = "'.str_replace('$ ', '', $valor).'"' ;
                    $contador7++;
                }
                $codigo .= $atributos;
          
                $codigo .= '>';
                $contador  = 0;
 
                foreach ($celdas as $id => $celda) {
                    $codigo .= '     <td';
 
                    if (!empty($id)) {
                        $codigo .= ' id="'.$id.'"';
                    }

                    
                    $codigo .= '>';
                    $codigo .= HTML::parrafo($celda, 'centrado').'</td>';
                    $contador++;
                }
 
                $codigo .= '     </tr>';
                $contador1 ++;
            }
        }
 
        $codigo .= '     </table>';
        return $codigo;
    }    

    /**
     * Generar código HTML para insertar un enlace hacia un elemento especifico
     * 
     * @param Modulo $modulo
     * @param type $registro
     * @param type $ajax
     * @param string $accion determina la accion a ser utilizada por este item
     * @param type $categoria
     * @return null 
     */
    static function urlInterna($modulo, $registro = '', $ajax = false, $accion = '', $categoria = '') {

        if (empty($modulo)) {
            return NULL;
        }

        $modulo = new Modulo($modulo);

        if (empty($registro) && empty($ajax) && !empty($categoria)) {
            return '/'.$modulo->url.'/category/'.$categoria;
        }

        if (empty($registro) && empty($ajax)) {
            return '/'.$modulo->url;
        }
        

        if ($registro) {
            return '/'.$modulo->url.'/'.$registro;
        }

        if ($ajax && $accion) {
            return '/ajax/'.$modulo->url.'/'.$accion;
        }

    }
    
    /**
     * Función que muestra el icono con el signo "?" y el tooltip al lado de los campos
     * 
     * @global objeto $textos objeto global para la traduccion de textos
     * @param string $texto texto a ser mostrado por este elemento
     * @return string|NULL código HTML a ser interpretado por el cliente web | NULL código HTML para ser mostrado e interpretado por el cliente web
     */
    static function cargarIconoAyuda($texto){   
        global $textos;
        $codigo  = '';
        $codigo .= HTML::campoOculto('textoAyudaModulo', $texto, 'textoAyudaModulo');
        $codigo .= HTML::parrafo($textos->id('AYUDA'));
        $cod  = HTML::contenedor($codigo , 'contenedorImagenAyuda', 'contenedorImagenAyuda');
        
        return $cod;
    }
    
    /**
     * Función que generá una nueva fila a ser incluida en una tabla de registros
     * cuando se carga desde una ventana modal
     * 
     * @param type $arregloDatos
     * @param string $clase clase css utilizada por este objeto del DOM
     * @param string $id identificador de este objeto en el DOM
     * @return null|string
     */
    static function crearNuevaFilaDesdeModal($arregloDatos, $clase = NULL, $id = NULL){
        if(!isset($arregloDatos) || !is_array($arregloDatos)){
            return NULL;
        }
        
        $codigo  = '';
        $codigo .= '<tr class="'.$clase.' oculto filasTabla" id='.$id.' >'; 
        
        foreach ($arregloDatos as $valor){
            $codigo .= '<td class="centrado">'. $valor .'</td>';
        }
        
        $codigo .= '</tr>';
        
        return $codigo;
        
    }    
    
    /**
     * Función que generá una nueva fila a ser incluida en una tabla de registros
     * 
     * @param type $arregloDatos
     * @param string $clase clase css utilizada por este objeto del DOM
     * @param string $id identificador de este objeto en el DOM
     * @return null|string
     */
    static function crearNuevaFila($arregloDatos, $clase = NULL, $id = NULL){
        if(!isset($arregloDatos) || !is_array($arregloDatos)){
            return NULL;
        }
        
        $codigo  = '';
        $codigo .= '<tr class="'.$clase.' oculto filasTabla" id="tr_'.$id.'" >'; 
        
        foreach ($arregloDatos as $valor){
            $codigo .= '<td class="centrado"> '.$valor.' </td>';
        }
        
        $codigo .= '</tr>';
        
        return $codigo;
        
    }
    
    /**
     * Función que generá una nueva fila a ser incluida en una tabla de registros
     * despues de la modificación de un registro
     * 
     * @param type $arregloDatos
     * @return null|string
     */
    static function crearFilaAModificar($arregloDatos){
        if(!isset($arregloDatos) || !is_array($arregloDatos)){
            return NULL;
        }
        
        $codigo  = '';
        
        foreach ($arregloDatos as $valor){
            $codigo .= '<td class="centrado"> '.$valor.' </td>';
        }       
        
        return $codigo;
        
    }
    
    /**
    * Función que se encarga de generar el código HTML el cual renderiza el menú que esta oculto, y que se muestra al hacer click derecho sobre una fila de la tabla de registros
    * el valor del campo oculto id es puesto en vivo por el javascript, es decir, cuando se hace click, se captura el id de la fila sobre la que se hizo click y se agrega al valor del campo
    * @global objeto $textos objeto global para la traduccion de textos = arreglo global con los textos  que usa el sistema
    * @param string $modulo = nombre del modulo sobre el cual se va a generar el menú, para generar así los nombres de los componentes del menú
    * @param array $botones = arreglo con el codigo HTML de los nuevos botones que se van a generar en este menú
    * @param array $excluidos = arreglo de dos posiciones ("editar" , "borrar") con dos posibles valores (true or false) ejemplo: si viene $excluidos['borrar'], el boton de borrar no aparecerá en el menú
    * @return string|NULL código HTML a ser interpretado por el cliente web  bloque de codigo HTML 
    */
    static function crearMenuBotonDerecho($modulo, $botones= NULL, $excluidos = NULL){
        global $textos;
        
        $objeto = new Modulo($modulo);
        
        $codigo  = $consultar = $editar = $borrar = '';
        $ruta    = '/ajax/'.$objeto->url;
        $datos   = array('id' => '');
        
        
        //Verificacion de permisos sobre el boton
        $puedeEditar  = Perfil::verificarPermisosBoton('botonEditar'.ucwords(strtolower($objeto->nombre)));            
        $puedeBorrar  = Perfil::verificarPermisosBoton('botonBorrar'.ucwords(strtolower($objeto->nombre)));  
      
        $codigo .= '<div id="contenedorBotonDerecho" class="oculto">';       
       
        $consultar = HTML::formaAjax($textos->id('CONSULTAR'), 'contenedorMenuConsultar', 'consultar'.ucwords(strtolower($objeto->nombre)), '', $ruta.'/see', $datos);
        $consultar1 = HTML::contenedor($consultar, '', 'botonConsultar'.ucwords(strtolower($objeto->nombre)));
                

        if($puedeEditar && empty($excluidos['editar'])){
            $editar    = HTML::formaAjax($textos->id('MODIFICAR'), 'contenedorMenuEditar botonAccion', 'editar'.ucwords(strtolower($objeto->nombre)), '', $ruta.'/edit', $datos);
            $editar1    = HTML::contenedor($editar, '', 'botonEditar'.ucwords(strtolower($objeto->nombre)));
        }
        
        if($puedeBorrar && empty($excluidos['borrar'])){ 
            $borrar    = HTML::formaAjax($textos->id('ELIMINAR'), 'contenedorMenuEliminar botonAccion', 'eliminar'.ucwords(strtolower($objeto->nombre)), '', $ruta.'/delete', $datos);
            $borrar1    = HTML::contenedor($borrar, '', 'botonBorrar'.ucwords(strtolower($objeto->nombre)));
        }
        
        $codigo   .= $consultar1.$editar1.$borrar1;
        
        if(isset($botones) && is_array($botones)){            
            foreach($botones as $boton){
                $codigo .= $boton;
            }
        }        
        
        $codigo   .=  '</div>';
        
        return $codigo;
        
    }

    /**
     * Generar código HTML con botón y formulario para la carga masiva de articulos
     * 
     * @param string $url URL relativa del destino del form
     * @param string $titulo titulo del botón
     * @return string|NULL código HTML a ser interpretado por el cliente web | NULL código HTML para ser mostrado e interpretado por el cliente web 
     */
    static function botonCargarMasivo($url, $titulo) {

        $url     = preg_replace("|^\/|", "", $url);
        $codigo  = HTML::boton('masGrueso', $titulo, 'botonAccion', '', '', '', array( "data-step" => "3", "data-intro" => "Click aquí para cargar el archivo(xls) de ingreso masivo de registros"));
        $cod  = HTML::forma('/ajax/'.$url.'/addMassive', $codigo);

        return $cod;
    }    
    

}
