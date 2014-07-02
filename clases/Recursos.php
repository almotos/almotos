<?php

/**
 *
 * @package     FOLCS
 * @subpackage  Base
 * @author      Pablo Andrés Vélez Vidal <pavelez@colomboamericano.edu.co>
 * @author      Julian A. Mondragón <jmondragon@felinux.com.co>
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2011 Colombo-Americano Soft.
 * @version     0.2
 * 
 * Clase Recursos: clase compuesta principalmente por metodos estaticos los cuales son utilizados a lo largo de la aplicación
 * para diversas funciones, entre ellas validación de información, pero principalmente para el renderizado de bloques de código
 * que serán reutilizados a todo lo largo de la aplicación, por ejemplo generar el bloque de codigo multimedia o de archivos.
 *
 * */
class Recursos {

    /**
     *
     * Funcion que arma y devuelve un bloque de codigo HTML con el listado
     * de documentos que pertenecen a un determinado item de un determinado modulo
     * 
     * @global type $sql
     * @global type $textos
     * @global type $configuracion
     * @global type $sesion_usuarioSesion
     * @param type $modulo
     * @param type $registro
     * @param type $propietario
     * @return type bloque de codigo html con el listado de documentos
     */
    static function bloqueArchivos($modulo, $registro, $propietario) {
        global $sql, $textos, $sesion_usuarioSesion;

        if (!isset($modulo) && !isset($registro) && !$sql->existeItem("modulos", "nombre", $modulo)) {
            return NULL;
        }
        $moduloActual = new Modulo($modulo);
        if (isset($sesion_usuarioSesion) && $sesion_usuarioSesion->id == $propietario) {

            $bloqueArchivos = HTML::campoOculto("idModulo", $moduloActual->id);
            $bloqueArchivos .= HTML::campoOculto("idRegistro", $registro);
            $bloqueArchivos .= HTML::boton("documentoNuevo", $textos->id("ADICIONAR_ARCHIVO"), "flotanteDerecha margenInferior");
            $bloqueArchivos = HTML::forma(HTML::urlInterna("DOCUMENTOS", "", true, "addDocument"), $bloqueArchivos);
        } elseif (isset($sesion_usuarioSesion) && $sesion_usuarioSesion->id != $propietario) {
            $bloqueArchivos = HTML::parrafo($textos->id("ERROR_ARCHIVO_PROPIETARIO"), "margenInferior");
        } else {
            $bloqueArchivos = HTML::parrafo($textos->id("ERROR_ARCHIVO_SESION"), "margenInferior");
        }


        $archivos = new Documento();
        $listaArchivos = array();
        $botonEliminar = "";

        if ($archivos->contar($modulo, $registro)) {


            foreach ($archivos->listar(0, 4, $modulo, $registro) as $archivo) {

                if (isset($sesion_usuarioSesion) && $sesion_usuarioSesion->id == $propietario) {
                    $botonEliminar1 = HTML::botonAjax("basura", "ELIMINAR", HTML::urlInterna("DOCUMENTOS", "", true, "deleteDocument"), array("id" => $archivo->id));
                    $botonEliminar = HTML::contenedor(HTML::contenedor($botonEliminar1, "contenedorBotonesLista", "contenedorBotonesLista"), "botonesLista flotanteDerecha", "botonesLista");
                }
                $contenidoArchivo = $botonEliminar;
                $contenidoArchivo .= HTML::enlace(HTML::imagen($archivo->icono, "flotanteIzquierda  margenDerecha miniaturaListaUltimos5"), $archivo->enlace);
                $contenidoArchivo .= HTML::parrafo(HTML::enlace($archivo->titulo, $archivo->enlace));
                $contenidoArchivo2 = HTML::parrafo($archivo->descripcion);
                $contenidoArchivo2 .= HTML::parrafo(HTML::frase($textos->id("ENLACE") . ": ", "negrilla") . $archivo->enlace, "margenSuperior");
                $contenidoArchivo .= HTML::contenedor($contenidoArchivo2, "contenedorGrisLargo");

                $listaArchivos[] = HTML::contenedor($contenidoArchivo, "contenedorListaDocumentos", "contenedorDocumento" . $archivo->id);
            }//fin del foreach

            if (sizeof($listaArchivos) >= 4) {
                $listaArchivos[] .= HTML::enlace($textos->id("VER_MAS") . HTML::icono("circuloFlechaDerecha"), HTML::urlInterna("DOCUMENTOS", $moduloActual->url . "/" . $registro), "flotanteCentro margenSuperior") . "</BR></BR>";
            }
        } else {
            $listaArchivos[] = HTML::frase(HTML::parrafo($textos->id("SIN_ARCHIVOS"), "sinRegistros", "sinRegistros"), "margenInferior");
        }


        $bloqueArchivos .= HTML::lista($listaArchivos, "listaVertical bordeSuperiorLista", "botonesOcultos", "listaDocumentos");

        return $bloqueArchivos;
    }
    

    /**
     * Metodo estatico que se encarga de mostrar cuantos registros totales existen en la consulta
     * y tambien muestra al usuario cuantos registros de cuantos esta viendo. Recibe  parametros
     * de tipo numerico
     * 
     * @global type $textos
     * @param type $totalRegistros
     * @param type $registroInicial
     * @param type $registroPorPagina
     * @param type $pagina
     * @param type $totalPaginas
     * @return type
     */
    public static function contarPaginacion($totalRegistros, $registroInicial, $registroPorPagina, $pagina, $totalPaginas) {
        global $textos;

        $registroMaximo = $registroInicial + $registroPorPagina;

        if ($pagina == $totalPaginas) {

            //codigo para reemplazar los valores que aparecen con el %1 con la variable que se le pasa, y en el texto que se le pasa.
            $texto1 = str_replace("%1", ($registroInicial + 1), $textos->id("PAGINACION"));
            $texto2 = str_replace("%2", $totalRegistros, $texto1);
            $texto = str_replace("%3", $totalRegistros, $texto2);
        } else {

            $texto1 = str_replace("%1", ($registroInicial + 1), $textos->id("PAGINACION"));
            $texto2 = str_replace("%2", $registroMaximo, $texto1);
            $texto = str_replace("%3", $totalRegistros, $texto2);
        }//fin del if


        $response = HTML::parrafo($texto, "negrilla");
        return $response;
    }

    /**
     * Escribir errores en un archivo txt
     * @param type $texto
     */
    public static function escribirTxt($texto) {

        $fecha = date("d/m/y H:i:s");
        $fp = fopen("errores.txt", "w");
        fwrite($fp, "Fecha: $fecha -> \n Variable: $texto  " . PHP_EOL);
        fclose($fp);
    }

    /**
     * Validar archivo, recibe un archivo, y un arreglo de extensiones,
     * compara la extension del archivo con cada una de las posiciones del arreglo
     * y devuelve true or false dependiendo de si existe o no el formato del archivo
     * en el arreglo de extensiones
     * 
     * @param type $archivo
     * @param type $extensiones
     * @return boolean
     */
    public function validarArchivo($archivo, $extensiones) {
        if (!empty($archivo["name"])) {
            $existe = true;
            $extension_archivo = strtolower(substr($archivo["name"], (strrpos($archivo["name"], ".") - strlen($archivo["name"])) + 1));
            if (!empty($extensiones) && is_array($extensiones)) {
                foreach ($extensiones as $extension) {
                    if ($extension_archivo == $extension) {
                        $existe = false;
                    }
                }
            }
            return $existe;
        } else {
            return true;
        }
    }
    
    /**
     * 
     * @global type $textos
     * @param type $datosTabla
     * @param type $rutaPaginador
     * @return string
     */
    static function generarTablaSinRegistros($datosTabla, $rutaPaginador, $estilosColumnas = NULL, $idTabla = '') {
        global $textos;
        $columnas = array();
        $celdas = array();
        
        $idTablaRegistros = 'tablaRegistros';
        if($idTabla){
            $idTablaRegistros = $idTabla;
        }

        $item = '<table border="2" cellspacing="1" cellpadding="3" pagina="1" ruta_paginador="' . $rutaPaginador . '" class="tablaRegistros" id="'.$idTablaRegistros.'">';
        $item .= '';
        foreach ($datosTabla as $columna => $celda) {//recorro el arreglo
            $columnas[] = $columna; //agrego a cada uno su valor correspondiente
            $celdas[] = $celda;
        }
        
        if (!empty($columnas)) {
            $item   .= "     <tr class='cabeceraTabla noSeleccionable'>\n";
            $contador  = 0;
 
                foreach ($columnas as $id => $columna) {
                    $item .= "     <th";
 
                    if (!empty($id) && is_string($id)) {
                        $item .= " id=\"$id\"";
                    }

                    $check = '';
                    $organizadores = '';
                    $columnaPequena = 'columnaPequena';
                    
                    if (!empty($celdas) && is_array($celdas)) {//aqui recibo una cadena que trae el nombre del objeto y el nombre para hacer la consulta
                        
                        $data = explode('|', $celdas[$contador]);                        
                        $item .= " nombreOrden=\"".$data[0]."\"";//en la posicion 0 traigo el nombre del objeto ej: nombreGrupo
                        //if($data[0] != "estado" &&  $data[0] != "imagen"){
                            $check  = HTML::campoChequeo($data[1], false, 'checkPatronBusqueda', 'checkPatronBusqueda'.($contador+1));//en la posicion 1 traigo el nombre para la consulta
                            $organizadores = "<div id='ascendente' ayuda='".$textos->id('AYUDA_ASCENDENTE')."'></div> <div id ='descendente' ayuda='".$textos->id('AYUDA_DESCENDENTE')."'></div>";
                            $columnaPequena = "";
                        //}
                        
                    }
                    
                    if (!empty($estilosColumnas) && is_array($estilosColumnas)) {
                        $item .= " class=\"columnaTabla $columnaPequena ".$estilosColumnas[$contador]."\"";
                    }                    
 
                    $item .= '>';                    
                    $item .= "$organizadores $columna  $check</th>";
                    $contador++;
                }
            $item   .= "  </tr>";
        }        
        
        $item .= '<tr id="trSinRegistros"><td colspan = '.sizeof($columnas).'>'.HTML::parrafo($textos->id("MODULO_SIN_REGISTROS"), 'textoAdvertencia centrado').'</td></tr>';
        
        $item .= '</tbody></table>';
        $item .=    '<div class="contenedorInferiorTabla" id="contenedorInferiorTabla">
                        <div class="contenedorImagenAyuda" id="contenedorImagenAyuda">
                            <input type="hidden" id="textoAyudaModulo" value="' . $textos->id("AYUDA_MODULO") . '" name="textoAyudaModulo">
                            <p>
                            '.$textos->id("AYUDA").'<br>
                            </p>
                        </div>
                    </div>';
        return $item;
    }

    /**
     * Metodo llamado por los metodos generar tabla de las clases, el cual se encarga de generar el codigo html de 
     * una tabla incluyendo la informacion que le fue suministrada en los parametros. 
     * Este metodo es llamado en los modulos  introducidos recientemente y genera la grilla con 
     * el paginador, el buscador, los botones de ordenamiento por columnas, el boton derecho de opciones y el icono
     *  para la informacion de ayuda. Se debe de tener en cuenta que cuando se llama este metodo pasandole los parametros
     * adecuados el genera la tabla pero sin ninguna funcionalidad (busqueda, ordenamiento, paginacion), el codigo para
     * estas funcionalidades se debe agregar en el archivo ajax del modulo, la ventaja es que de un modulo a otro estos
     * metodos son casi exactamente iguales, asi que solo seria copiar, pegar y reemplazar algunos alias de tablas y 
     * nombres de clases.
     * 
     * @global type $sesion_usuarioSesion
     * @global type $textos
     * @global type $modulo
     * @param type $arregloItems
     * @param type $datosTabla
     * @param type $rutaPaginador
     * @param type $datosPaginacion
     * @return type 
     */
    static function generarTablaRegistros($arregloItems, $datosTabla, $rutaPaginador, $datosPaginacion = NULL, $estilosColumnas = NULL, $tablaModal = false) {
        global $sesion_usuarioSesion, $textos, $modulo;

        if (empty($arregloItems) || !is_array($arregloItems)) {
            $item = Recursos::generarTablaSinRegistros($datosTabla, $rutaPaginador, $estilosColumnas = NULL);
            return $item;
        }

        $fila       = 0;
        $columnas   = array(); //columnas que va a tener la tabla
        $celdas     = array(); //celdas que va a tener la tabla  

        $ids            = array(); //identificador de cada uno de los registros
        $arregloCeldas  = array();
        $item           = ""; //codigo html final a devolver por el metodo    

        if (!empty($datosTabla)/* && is_array($datosTabla) */) {//verifico que llegue un arreglo con los nombres de las columnas y con que celdas(posiciones del objeto en el array devuelto por el listar) se van a recorrer
            foreach ($datosTabla as $columna => $celda) {//recorro el arreglo
                $columnas[] = $columna; //agrego a cada uno su valor correspondiente            
                $celdas[] = $celda;
            }

            foreach ($arregloItems as $elemento) {//recorro el arreglo de registros que me envian
                if ($elemento->id != 0) {
                    $fila++;

                    if ((isset($sesion_usuarioSesion) && ($sesion_usuarioSesion->idTipo == 0) ) || (isset($sesion_usuarioSesion) && Perfil::verificarPermisosModulo($modulo->id))) {

                        $filas   = array(); //filas que va a aparecer en la tabla
                        $atributos = array(); //atributos que se van a poner en la fila

                        foreach ($celdas as $registro) {//armo las celdas que se van a pasar a la tabla para ser generada
                            $registro = explode("|", $registro); //en celdas viene el nombre usado en el objeto, y el nombre del mismo usado para la consulta ej: nombreItem | i.nombre

                            $filas[] = $elemento->$registro[0]; //por eso accedo a la posicion 0 que es donde viene el nombre del objeto
                            
                            if($registro[0] != "completo" && $registro[0] != "codigoPais" && $registro[0] != "activo"){//si no es ningun registro que contenga imagenes
                                $atributos[] = $elemento->$registro[0];                                                //porque causan conflicto al renderizar la tabla
                            }
                        }
                        
                        $arregloCeldas[$fila] = $filas;
                        $ids[] = 'tr_' . $elemento->id;
                        
                    }
                    
                }
                
            }
            
        }

        $paginador = "";
        
        if (!empty($datosPaginacion)/* && is_array($datosPaginacion) */) {
            //$datosPaginacion =                  0=>totalRegistrosActivos  1=>registroInicial   2=>registros         3=>pagina
            $paginador = Recursos::mostrarPaginadorPeque($datosPaginacion[0], $datosPaginacion[1], $datosPaginacion[2], $datosPaginacion[3]);
            $pag = $datosPaginacion[3];
        }


        $opciones = array('cellpadding' => '3', 'border' => '2', 'cellspacing' => '1', 'ruta_paginador' => $rutaPaginador, 'pagina' => $pag);
        $item .= HTML::tablaGrilla($columnas, $arregloCeldas, 'tablaRegistros', 'tablaRegistros', $estilosColumnas, 'filasTabla', $opciones, $ids, $celdas, $atributos);

        if(!$tablaModal){
            $ayuda = HTML::cargarIconoAyuda($textos->id('AYUDA_MODULO')); //con esto se carga el icono que contiene los datos para poder mostrar la ayuda en el modulo
            $enseñame = HTML::contenedor('', 'contenedorImagenTeachme', '', array('ayuda' => 'Enseñame!!!'));
            
        }

        $item .= HTML::contenedor($enseñame . $ayuda . $paginador, 'contenedorInferiorTabla', 'contenedorInferiorTabla');

        $item1 = HTML::contenedor($item, 'contenedorTablaRegistros', 'contenedorTablaRegistros');


        return $item1;
    }

    /**
     * @global type $textos
     * @param type $totalRegistrosActivos
     * @param type $registroInicial
     * @param type $registros
     * @param type $pagina
     * @param type $totalPaginas
     * @return type 
     */
    public static function mostrarPaginadorPeque($totalRegistrosActivos, $registroInicial, $registros, $pagina, $totalPaginas = NULL) {
        global $textos;

        $botonPrimera = $botonUltima = $botonAnterior = $botonSiguiente = $infoPaginacion = '';
        if ($totalRegistrosActivos > $registros) {
            $totalPaginas = ceil($totalRegistrosActivos / $registros);

            if ($pagina > 1) {
                $botonPrimera = ''; //HTML::campoOculto('pagina', 1);
                $botonPrimera .= HTML::contenedor($textos->id('PRIMERA_PAGINA'), 'botonPrimeraPagina botonPaginacion', 'botonPrimeraPagina', array('pagina' => (1)));

                $botonAnterior = ''; //HTML::campoOculto('pagina', $pagina-1);
                $botonAnterior .= HTML::contenedor($textos->id('PAGINA_ANTERIOR'), 'botonAtrasPagina botonPaginacion', 'botonAtrasPagina', array('pagina' => ($pagina - 1)));
            }

            if ($pagina < $totalPaginas) {
                $botonSiguiente = ''; //HTML::campoOculto('pagina', $pagina+1);
                $botonSiguiente .= HTML::contenedor($textos->id('PAGINA_SIGUIENTE'), 'botonSiguientePagina botonPaginacion', 'botonSiguientePagina', array('pagina' => ($pagina + 1)));

                $botonUltima = ''; //HTML::campoOculto('pagina', $totalPaginas);
                $botonUltima .= HTML::contenedor($textos->id('ULTIMA_PAGINA'), 'botonUltimaPagina botonPaginacion', 'botonUltimaPagina', array('pagina' => ($totalPaginas)));
            }

            $infoPaginacion = self::contarPaginacion($totalRegistrosActivos, $registroInicial, $registros, $pagina, $totalPaginas);
        }

        $paginador = HTML::contenedor($botonPrimera . $botonAnterior . $botonSiguiente . $botonUltima, 'paginadorTabla', 'paginadorTabla');
        $paginador .= HTML::contenedor($infoPaginacion, 'informacionPaginacion');

        return $paginador;
    }



    /**
     * Funcion para verificar que una tabla no tenga registros asociados a otras tablas,
     * recibe como parametro un arreglo de arreglos. Cada uno de estos arreglos contiene los siguientes parametros
     *
     * @global type $sql
     * @param type $nombreItem = el nombre del "tipo de item" que se trata de borrar, ejemplo: Banco* 
     * @param type array $arregloTablas Arreglo de arreglos, cada posicion contiene los sig 3 datos
     * 
     * @param type $tabla = tabla con la cual se va a verificar la integridad referencial
     * @param type condicion = condicion con el identificador del item que se desea eliminar para hacer la consulta y hallar las relaciones
     * @param type $nombreItemRelacionado = el nombre del item (tabla de la BD) relacionado, ejemplo: Cuentas de Proveedor
     * 
     * @return string = cadena con la informacion que será mostrado en un sexy alert box, indicando con que tablas tiene relacion
     * y que por tal motivo este registro no puede ser eliminado. Ejemplo: No puedes eliminar esta ciudad
     * porque tiene 25 empleados, 14 proveedores y 40 clientes relacionados a el.
     */
    public static function verificarIntegridad($nombreItem, $arregloTablas) {
        global $sql;
        
        if(empty($arregloTablas)){
            return NULL;
        }

        $contadorRelacion       = 0;//contador que se encarga de verificar si este item tiene relacion con alguna de las tablas que se consulta
        $textoIntegridad        = '';//en caso de tener alguna relacion, esta variable almacenará el texto a devolver al usuario
        $consultaIntegridad     = array();//arreglo que almacenará los datos de qué tabla tiene cuantos registros relacionados
        
        
        foreach($arregloTablas as $arregloItem){            
            
            $cantidad = $sql->obtenerValor($arregloItem[0], 'COUNT(id)', $arregloItem[1]);
            if($cantidad > 0){
                $contadorRelacion ++;
                $consultaIntegridad[$arregloItem[2]] = $cantidad;
            }

            
        }
        

        if ($contadorRelacion > 0) {
            
            $textoIntegridad = 'No puede eliminar este <b>'.$nombreItem.'</b> porque tiene <br>';
            
            foreach ($consultaIntegridad as $nombre => $valor) {
                $textoIntegridad .= '<br> -' . $valor . ' '. $nombre . ',<br>';
            }
            
            $textoIntegridad .= '<br> asociados a el. <br>';
            $textoIntegridad .= '<br>*Elimine primero esos registros, y despues podrá eliminar este.   <br>';

        } 
        
        return $textoIntegridad;

    }

    /**
     *
     * @global type $sesion_usuarioSesion
     * @global type $textos
     * @global type $modulo
     * @param type $arregloItems
     * @param type $datosTabla
     * @param type $rutaPaginador
     * @param type $datosPaginacion
     * @return type 
     */
    static function generarTablaRegistrosInterna($arregloItems, $datosTabla, $rutas, $idTabla, $estilosColumnas, $claseTabla = '') {
        
//        if (empty($arregloItems) || !is_array($arregloItems)) {
//            $item = Recursos::generarTablaSinRegistros($datosTabla, $rutaPaginador, $estilosColumnas = NULL);
//            return $item;
//        }        
        
        $fila       = 0;
        $columnas   = array(); //columnas que va a tener la tabla
        $celdas     = array(); //celdas que va a tener la tabla  

        $ids            = array(); //identificador de cada uno de los registros
        $arregloCeldas  = array();
        $item           = ""; //codigo html final a devolver por el metodo  

        if (empty($claseTabla)) {
            $claseTabla = 'tablaRegistros';
        }

        if (isset($datosTabla) && is_array($datosTabla)) {//verifico que llegue un arreglo con los nombres de las columnas y con que celdas(posiciones del objeto en el array devuelto por el listar) se van a recorrer
            foreach ($datosTabla as $columna => $celda) {//recorro el arreglo
                $columnas[] = $columna; //agrego a cada uno su valor correspondiente            
                $celdas[] = $celda;
            }

            foreach ($arregloItems as $elemento) {//recorro el arreglo de registros que me envian
                if ($elemento->id != 0) {
                    $fila++;

                    $filas = array(); //filas que va a aparecer en la tabla

                    foreach ($celdas as $registro) {//armo las celdas que se van a pasar a la tabla para ser generada
                        $registro = explode('|', $registro); //en celdas viene el nombre usado en el objeto, y el nombre del mismo usado para la consulta ej: nombreItem | i.nombre
                        $filas[] = HTML::parrafo($elemento->$registro[0], 'centrado'); //por eso accedo a la posicion 0 que es donde viene el nombre del objeto
                      
                        
                    }
                    
                    $arregloCeldas[$fila] = $filas;
                    $ids[] = $idTabla . 'Tr_' . (int) $elemento->id;
                }
            }//fin del foreach
//        print_r($arregloItems);
        }//fin del if(isset($datosTabla) && is_array($datosTabla))
//    $estilosColumnas = array('ancho25porCiento', 'ancho25porCiento', 'ancho25porCiento', 'ancho25porCiento');
        $opciones = array('cellpadding' => '3', 'border' => '2', 'cellspacing' => '1');
        if (!empty($rutas) && is_array($rutas)) {
            foreach ($rutas as $ruta => $valor) {
                $opciones[$ruta] = $valor;
            }
        }
        $item .= HTML::tablaGrillaInterna($columnas, $arregloCeldas, $claseTabla, $idTabla, $estilosColumnas, 'filasTablaInterna', $opciones, $ids, $celdas);


        $item .= HTML::contenedor('', 'contenedorInferiorTablaInterna', 'contenedorInferiorTablaInterna');

        $item1 = HTML::contenedor($item, 'contenedorTablaRegistros', 'contenedorTablaRegistros');


        return $item1;
    }

    /**
     * 
     *
     * @global type $sesion_usuarioSesion
     * @global type $textos
     * @global type $modulo
     * @param type $arregloItems
     * @param type $datosTabla
     * @param type $rutaPaginador
     * @param type $datosPaginacion
     * @return type 
     */
    static function generarTablaLista($arregloItems, $datosTabla, $rutas, $idTabla, $estilosColumnas, $claseTabla) {

        $fila = 0;
        $columnas = array(); //columnas que va a tener la tabla
        $celdas = array(); //celdas que va a tener la tabla  

        $ids = array(); //identificador de cada uno de los registros
        $arregloCeldas = array(); //arreglo que en cada posicion contiene un arreglo con los datos de cada una de las filas
//    $arregloArticulos = array();//arreglo que en cada posicion contiene un arreglo con los datos de cada una de las filas
        $item = ""; //codigo html final a devolver por el metodo  

        if (empty($claseTabla)) {
            $claseTabla = 'tablaRegistros';
        }

        if (isset($datosTabla) && is_array($datosTabla)) {//verifico que llegue un arreglo con los nombres de las columnas y con que celdas(posiciones del objeto en el array devuelto por el listar) se van a recorrer
            foreach ($datosTabla as $columna => $celda) {//recorro el arreglo
                $columnas[] = $columna; //agrego a cada uno su valor correspondiente            
                $celdas[] = $celda;
            }

            foreach ($arregloItems as $elemento) {//recorro el arreglo de registros que me envian
                if ($elemento->id != 0) {
                    $fila++;

                    $filas = array(); //filas que va a aparecer en la tabla
//                    $datosAtributosArticulo = array();//arreglo donde se guardan los valores sin formatear, para que este como atributos y poderlos capturar con el javascript
                    foreach ($celdas as $registro) {//armo las celdas que se van a pasar a la tabla para ser generada
                        $registro = explode("|", $registro); //en celdas viene el nombre usado en el objeto, y el nombre del mismo usado para la consulta ej: nombreItem | i.nombre
                        $filas[] = $elemento->$registro[0]; //por eso accedo a la posicion 0 que es donde viene el nombre del objeto
//                        $datosAtributosArticulo[] = $elemento->$registro[0];
                    }

                    $arregloCeldas[$fila] = $filas;
//                    $arregloArticulos[$fila] = $datosAtributosArticulo;
                    $ids[] = $idTabla . "Tr_" . (int) $elemento->id;
                }
            }//fin del foreach
//        print_r($arregloItems);
        }//fin del if(isset($datosTabla) && is_array($datosTabla))
//    $estilosColumnas = array('ancho25porCiento', 'ancho25porCiento', 'ancho25porCiento', 'ancho25porCiento');
        $opciones = array('cellpadding' => '3', 'border' => '2', 'cellspacing' => '1');
        if (!empty($rutas) && is_array($rutas)) {
            foreach ($rutas as $ruta => $valor) {
                $opciones[$ruta] = $valor;
            }
        }
        $item .= HTML::tablaGrillaListaArticulos($columnas, $arregloCeldas, $claseTabla, $idTabla, $estilosColumnas, 'filasTablaInterna', $opciones, $ids, $celdas);


        $item .= HTML::contenedor('', 'contenedorInferiorTablaInterna', 'contenedorInferiorTablaInterna');

        $item1 = HTML::contenedor($item, 'contenedorTablaRegistros', 'contenedorTablaRegistros');


        return $item1;
    }

    /**
     * Funcion que se encarga de dar formato a un numero segun sea su tipo,
     * se usará generalmente para mostrar datos monetarios, teniendo en cuenta los centavos
     * @global type $sql
     * @param type $numero
     * @param type $cola
     * @param type $cantidadDecimales 
     */
    static function formatearNumero($numero, $cola = NULL, $cantidadDecimales = NULL, $tipo = '') {
        global $sesion_configuracionGlobal;

        if ($cola === '$') {
            if ($tipo == 'entero') {
                $numero = ($numero == '') ? '0' : round($numero);                
                
            } else {
                $numero = ($numero == '') ? '0' : number_format($numero, 2, '.', '');
                
            }
            
        } else {

            if (empty($cantidadDecimales) && $cantidadDecimales !== '0') {
                $cantidadDecimales = $sesion_configuracionGlobal->cantidadDecimales;
            }
            $numero = ($numero == '') ? '0,00' : number_format($numero, $cantidadDecimales, '.', '') . ' '.$cola;
        }
        
        return $numero;
    }

    /**
     * Recibe un numero, y el largo, sgun el largo del numero, completa con ceros a la izquierda el largo que recibe
     * ejemplo: recibe 23 (numero) y 7 (largo), asi que completaria con 5 ceros a la izquierda 0000023 y retornaria este numero
     *
     * @param type $numero
     * @param type $largo
     * @return type 
     */
    static function completarCeros($numero, $largo) {
        $numero = (int) $numero;
        $largo = (int) $largo;

        $llenado = '';

        if (strlen($numero) < $largo) {
            $llenado = str_repeat('0', $largo - strlen($numero));
        }
        return $llenado . $numero;
    }
    
    /**
     * Recibe un string, recibe el caracter con el cual lo va a completar, y recibe el largo de caracteres que debe de tener
     *
     * @param string $string la cadena a completar
     * @param string $caracter el caracter con el cual se va a completar
     * @return int $largo el largo que debe de tener la cadena
     * @return int $posicion la posicion donde se debe llenar los caracteres 1->antes de la cadena, 2->despues de la cadena
     */
    static function completarCaracteres($string, $caracter, $largo, $posicion) {
        $string     = (string) $string;
        $caracter   = (string) $caracter;
        $largo      = (int) $largo;

        $llenado = '';

        if (strlen($string) < $largo) {
            $llenado = str_repeat($caracter, $largo - strlen($string));
        }
        
        $valorARetornar = ($posicion == '1') ? $llenado . " " . $string :  $string . " " . $llenado;
        
        return $valorARetornar;
    }    
    
    
    /**
     * Verifica si el parametro introducido es un numero entero o no
     * @param type $text
     * @return type 
     */
    static function verificarEntero($text){
        return preg_match('/^-?[0-9]+$/', (string)$text) ? true : false;
    }
    
     

    /**
     * Captura la ip del cliente, sin importar que se encuentre con un proxy delante
     * @return type
     */
    public static function getRealIP() {

        if ($_SERVER['HTTP_X_FORWARDED_FOR'] != '') {
            $client_ip =
                    (!empty($_SERVER['REMOTE_ADDR']) ) ?
                    $_SERVER['REMOTE_ADDR'] :
                    ( (!empty($_ENV['REMOTE_ADDR']) ) ?
                            $_ENV['REMOTE_ADDR'] :
                            'unknown' );

            // los proxys van añadiendo al final de esta cabecera
            // las direcciones ip que van 'ocultando'. Para localizar la ip real
            // del usuario se comienza a mirar por el principio hasta encontrar 
            // una dirección ip que no sea del rango privado. En caso de no 
            // encontrarse ninguna se toma como valor el REMOTE_ADDR

            $entries = split('[, ]', $_SERVER['HTTP_X_FORWARDED_FOR']);

            reset($entries);
            while (list(, $entry) = each($entries)) {
                $entry = trim($entry);
                if (preg_match('/^([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)/', $entry, $ip_list)) {
                    // http://www.faqs.org/rfcs/rfc1918.html
                    $private_ip = array(
                        '/^0\./',
                        '/^127\.0\.0\.1/',
                        '/^192\.168\..*/',
                        '/^172\.((1[6-9])|(2[0-9])|(3[0-1]))\..*/',
                        '/^10\..*/');

                    $found_ip = preg_replace($private_ip, $client_ip, $ip_list[1]);

                    if ($client_ip != $found_ip) {
                        $client_ip = $found_ip;
                        break;
                    }
                }
            }
        } else {
            $client_ip =
                    (!empty($_SERVER['REMOTE_ADDR']) ) ?
                    $_SERVER['REMOTE_ADDR'] :
                    ( (!empty($_ENV['REMOTE_ADDR']) ) ?
                            $_ENV['REMOTE_ADDR'] :
                            'unknown' );
        }

        return $client_ip;
    }
    
    
    /**
     * Función que simula array_map() pero lo hace de manera recursiva
     *
     * @param string $fn función a aplicar a los objetos
     * @param arreglo $arr arreglo al que se le va a aplicar la función
     * @return arreglo arreglo formateado por la función recibida como parametro
     */
    public static function array_map_recursive($fn, $arr) {
        $rarr = array();
        
        foreach ($arr as $k => $v) {
            $rarr[$k] = is_array($v)
                ? self::array_map_recursive($fn, $v)
                : $fn($v); // or call_user_func($fn, $v)
        }
        
        return $rarr;
        
    }   
    
    
    /**
     * Funcion para validar formatos de fecha (DATETIME) validos
     */
    function verificarDate($data) {
        if (date('Y-m-d', strtotime($data)) == $data) {
            return true;
            
        } else {
            return false;
            
        }
        
    }    
    

}
