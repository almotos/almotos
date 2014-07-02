<?php

/**
 *
 * @package     FOLCS
 * @subpackage  Empresas
 * @author      Pablo Andrés Vélez Vidal <pavelez8@misena.edu.co>
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2012 Genesys corporation.
 * @version     0.2
 *
 * Clase encargada de gestionar los datos de configuración de la empresa que gestiona el sistema. En este módulo se pueden
 * parametrizar todos los datos correspondientes a la empresa que esta utilizando el sistema, por ejemplo el NIT, la direccion,
 * el correo, la página web, el logo de la empresa, etc. Estos datos son utilizados en algunos modulos del sistema como por 
 * ejemplo en la facturación donde aparece la información de la empresa.
 * 
 * tabla principal: empresas.
 * 
 * */
class Empresa {

    /**
     * Código interno o identificador de la empresa en la base de datos
     * @var entero
     */
    public $id;

    /**
     * URL relativa del módulo de la empresa
     * @var cadena
     */
    public $urlBase;

    /**
     * URL relativa de una empresa específica
     * @var cadena
     */
    public $url;

    /**
     * Nombre de la empresa
     * @var cadena
     */
    public $nit;

    /**
     * Nombre de la empresa
     * @var cadena
     */
    public $direccionPrincipal;

    /**
     * Nombre de la empresa
     * @var cadena
     */
    public $telefono;

    /**
     * Nombre de la empresa
     * @var cadena
     */
    public $email;

    /**
     * Nombre de la empresa
     * @var cadena
     */
    public $paginaWeb;

    /**
     * Nombre de la empresa
     * @var cadena
     */
    public $nombre;

    /**
     * indica el nombre con el que se encuentra registrada la empresa ante las autoridades competentes
     * @var cadena
     */
    public $nombreOriginal;

    /**
     * indica el regimen de la empresa (comun, o simplificado)
     * @var cadena
     */
    public $regimen;
    
    /**
     * id la actividad economica de la dian a la que se dedica la empresa
     * @var int 
     */
    public $idActividadEconomica;    
    
    /**
     * objeto actividad economica de la dian a la que se dedica la empresa
     * @var int 
     */
    public $actividadEconomica;   
    
     /**
     * valor que representa el monto base para aplicar la retefuente sobre las compras
     * @var int 
     */
    public $baseRetefuente;  

    /**
     * indica si la empresa retiene impuestos sobre la fuente
     * @var cadena
     */
    public $retienefuente;

    /**
     * indica si la empresa retiene impuestos de ica
     * @var cadena
     */
    public $retieneIca;

    /**
     * indica si la empresa retiene impuestos sobre el iva
     * @var cadena
     */
    public $retieneIva;

    /**
     * indica si la empresa retiene sus propios impuestos
     * @var cadena
     */
    public $autoretenedor;

    /**
     * indica si la empresa es catalogada como grancontribuyente
     * @var cadena
     */
    public $grancontribuyente;

    /**
     * indica si la empresa realiza un ingreso de mercancia cada vez que realiza una compra
     * @var cadenana 
     */
    public $ingresoMercancia;
    
   /**
     * Identificador en la tabla imagenes
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
     * indica si la empresa se encuentra activa
     * @var cadena
     */
    public $activo;

    /**
     * Indicador del orden cronológio de la lista de empresas
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
    public $registrosConsulta = NULL;

    /**
     * Orden predeterminado para organizar los listados
     * @var entero
     */
    public $ordenInicial = NULL;

    /**
     *
     * Inicializar la Empresa
     *
     * @param entero $id Código interno o identificador de la empresa en la base de datos
     *
     */
    public function __construct($id = NULL) {
        global $sql, $modulo;
        $this->urlBase = '/' . $modulo->url;
        $this->url = $modulo->url;

        $this->registros = $sql->obtenerValor('empresas', 'COUNT(id)', 'id != "0"');
        //establecer el valor del campo predeterminado para organizar los listados
        $this->ordenInicial = 'nombre';

        if (!empty($id)) {
            $this->cargar($id);
        }
    }

    /**
     *
     * Cargar los datos de una empresa
     *
     * @param entero $id Código interno o identificador de la empresa en la base de datos
     *
     */
    public function cargar($id) {
        global $sql, $configuracion;

        if (isset($id) && $sql->existeItem('empresas', 'id', intval($id))) {

            $tablas = array(
                'e' => 'empresas',
                'i' => 'imagenes'
            );

            $columnas = array(
                'id'                    => 'e.id',
                'nit'                   => 'e.nit',
                'direccionPrincipal'    => 'e.direccion_principal',
                'telefono'              => 'e.telefono',
                'email'                 => 'e.email',
                'paginaWeb'             => 'e.pagina_web',
                'nombre'                => 'e.nombre',
                'nombreOriginal'        => 'e.nombre_original',
                'regimen'               => 'e.regimen',
                'idActividadEconomica'  => 'e.id_actividad_economica',
                'baseRetefuente'        => 'e.base_retefuente',
                'retieneFuente'         => 'e.retiene_fuente',
                'retieneIca'            => 'e.retiene_ica',
                'retieneIva'            => 'e.retiene_iva',
                'autoretenedor'         => 'e.autoretenedor',
                'grancontribuyente'     => 'e.grancontribuyente',
                'ingresoMercancia'      => 'e.ingreso_mercancia',
                'idImagen'              => 'e.id_imagen',
                'imagen'                => 'i.ruta',
            );

            $condicion = 'e.id_imagen = i.id AND e.id = "'.$id.'"';

            $consulta = $sql->seleccionar($tablas, $columnas, $condicion);

            if ($sql->filasDevueltas) {
                $fila = $sql->filaEnObjeto($consulta);

                foreach ($fila as $propiedad => $valor) {
                    $this->$propiedad = $valor;
                }
                
                $this->actividadEconomica = new ActividadEconomica($this->idActividadEconomica);
                
                $this->imagenPrincipal = $configuracion['SERVIDOR']['media'] . $configuracion['RUTAS']['imagenesDinamicas'] . '/' . $this->imagen;
                $this->imagenMiniatura = $configuracion['SERVIDOR']['media'] . $configuracion['RUTAS']['imagenesMiniaturas'] . '/' . $this->imagen;                
                
            }
            
        }
        
    }

    /**
     *
     * Modificar una empresa
     *
     * @param  arreglo $datos       Datos de la empresa a modificar
     * @return lógico               Indica si el procedimiento se pudo realizar correctamente o no
     *
     */
    public function modificar($datos) {
        global $sql, $archivo_imagen;

        if (!isset($this->id)) {
            return NULL;
        }
                
        $idImagen = $this->idImagen;

        if (isset($archivo_imagen) && !empty($archivo_imagen['tmp_name'])) {

            $imagen         = new Imagen($this->idImagen);            
            $eliminarImagen = $imagen->eliminar();
            
            if($eliminarImagen === false){
                $sql->cancelarTransaccion();
                return false;
            }              
            
            $datosImagen = array(
                'modulo'        => 'EMPRESAS',
                'idRegistro'    => $this->id,
                'titulo'        => 'logo_empresa',
                'descripcion'   => 'logo_empresa'
            );

            $idImagen = $imagen->adicionar($datosImagen);
            
            if($idImagen === false){
                $sql->cancelarTransaccion();
                return false;
            } 
        }

        $datosEmpresa = array(
            'nit'                       => $datos['nit'],
            'direccion_principal'       => $datos['direccion_principal'],
            'telefono'                  => $datos['telefono'],
            'email'                     => $datos['email'],
            'pagina_web'                => $datos['pagina_web'],
            'nombre'                    => $datos['nombre'],
            'nombre_original'           => $datos['nombre_original'],
            'regimen'                   => $datos['regimen'],
            'id_actividad_economica'    => $datos['id_actividad_economica'],
            'base_retefuente'           => $datos['base_retefuente']
        );
        
        $datosEmpresa['id_imagen'] = $idImagen;         

        if (isset($datos['activo'])) {
            $datosEmpresa['activo'] = '1';
        } else {
            $datosEmpresa['activo'] = '0';
        }

        if (isset($datos['retiene_fuente'])) {
            $datosEmpresa['retiene_fuente'] = '1';
        } else {
            $datosEmpresa['retiene_fuente'] = '0';
        }

        if (isset($datos['retiene_ica'])) {
            $datosEmpresa['retiene_ica'] = '1';
        } else {
            $datosEmpresa['retiene_ica'] = '0';
        }

        if (isset($datos['retiene_iva'])) {
            $datosEmpresa['retiene_iva'] = '1';
        } else {
            $datosEmpresa['retiene_iva'] = '0';
        }

        if (isset($datos['grancontribuyente'])) {
            $datosEmpresa['grancontribuyente'] = '1';
        } else {
            $datosEmpresa['grancontribuyente'] = '0';
        }

        if (isset($datos['autoretenedor'])) {
            $datosEmpresa['autoretenedor'] = '1';
        } else {
            $datosEmpresa['autoretenedor'] = '0';
        }

        if (isset($datos['ingreso_mercancia'])) {
            $datosEmpresa['ingreso_mercancia'] = '1';
        } else {
            $datosEmpresa['ingreso_mercancia'] = '0';
        }

        $consulta = $sql->modificar('empresas', $datosEmpresa, 'id = "' . $this->id . '"');
        
        return $consulta;
        
    }

    /**
     *
     * Listar las empresas
     *
     * @param entero  $cantidad    Número de ciudadesa incluir en la lista (0 = todas las entradas)
     * @param arreglo $excepcion   Arreglo con los códigos internos o identificadores a omitir en la lista
     * @param cadena  $condicion   Condición adicional (SQL)
     * @return arreglo             Lista de empresas
     *
     */
    public function listar($inicio = 0, $cantidad = 0, $excepcion = NULL, $condicionGlobal = NULL, $orden = NULL) {
        global $sql;

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
            $condicion = 'e.id NOT IN ('.$excepcion.') ';
        }


        /*         * * Definir el orden de presentación de los datos ** */
        if (!isset($orden)) {
            $orden = $this->ordenInicial;
        }
        if ($this->listaAscendente) {
            $orden = $orden.' ASC';
        } else {
            $orden = $orden.' DESC';
        }


        $tablas = array(
            'e' => 'empresas'
        );

        $columnas = array(
            'id'                => 'e.id',
            'nit'               => 'e.nit',
            'nombre'            => 'e.nombre',
            'nombreOriginal'    => 'e.nombre_original'
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

        $consulta = $sql->seleccionar($tablas, $columnas, $condicion, '', $orden, $inicio, $cantidad);

        if ($sql->filasDevueltas) {
            $lista = array();

            while ($empresa = $sql->filaEnObjeto($consulta)) {
                $lista[] = $empresa;
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
            HTML::parrafo($textos->id('NOMBRE')         , 'centrado')   => 'nombre|e.nombre',
            HTML::parrafo($textos->id('NIT')            , 'centrado')   => 'nit|e.nit',
            HTML::parrafo($textos->id('NOMBRE_ORIGINAL'), 'centrado')   => 'nombreOriginal|e.nombre_original'
        );
        //ruta a donde se mandara la accion del doble click
        $rutaPaginador = '/ajax' . $this->urlBase . '/move';

        return Recursos::generarTablaRegistros($arregloRegistros, $datosTabla, $rutaPaginador, $datosPaginacion) . HTML::crearMenuBotonDerecho('EMPRESAS', '', array('borrar' => true));
    }

}

