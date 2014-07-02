<?php
/**
 *
 * @package     FOM
 * @subpackage  Articulo Motos
 * @author      Pablo Andrés Vélez Vidal <pavelez8@misena.edu.co>
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2012 Genesys Corporation.
 * @version     0.2
 * 
 * Módulo encargado de almacenar los registros de las motos a las cuales aplica un determinado articulo. En el módulo articulos
 * al crear o modificar un articulo se puede indicar a que motos tiene aplicabilidad. Este es el módulo encargado de gestionar 
 * dicha información. ejemplo: el Articulo con id: 001257 Llanta 80-100 pistera aplica a diversas motos tales como AKT 125 evolution,
 * AKT 150, Honda Hero, etc.
 * 
 * Esta clase es solo un repositorio de funciones, no tiene atributos ni un metodo constructor. * 
 *
 * */
class ArticuloMotos {
    

    /**
     * Cargar en la variable de tipo array motos, las motos a las cuales aplica determinado articulo
     * 
     * @global type $sql
     * @param type $id
     * @return type
     */
    public static function cargarMotos($id) {
        global $sql;

        $motos      = array();
        $tabla      = array('articulo_moto');
        $condicion  = 'id_articulo = "' . $id . '"';

        $consulta = $sql->seleccionar($tabla, array('id_articulo', 'id_moto'), $condicion);

        while ($moto = $sql->filaEnObjeto($consulta)) {
            $motos[] = $moto->id_moto;
            
        }

        return $motos;
    }


    /**
     * Metodo Insertar moto-articulo, para ingresar las motos sobre las que tiene aplicacion determinado articulo
     * 
     * @global objeto $sql objeto global de gestón con la BD.
     * @param entero $idArticulo identificador del articulo
     * @param arreglo $datosMotos arreglo con los id de las motos a las cuales aplica el determinado articulo
     * @return boolean  true o false dependiendo del exito de la transacción
     */
    public function insertarMotosAplicacion($idArticulo, $datosMotos) {
        global $sql;

        $tamano = sizeof($datosMotos);
        $ultimo = $tamano - 1; //saber cual será la ultima posicion del arreglo
        
        $sql->iniciarTransaccion();

        if (!empty($datosMotos)) {//verifico si el articulo se aplica a varias motos
            $valor = '';
            $count = 0;
            
            foreach ($datosMotos as $valor) {
                $count++;
                $values .= '(' . $idArticulo . ', ' . $valor . ')';
                
                if ($count <= $ultimo) {
                    $values .= ",";
                    
                }
                
            }

            $sentencia = "INSERT INTO fom_articulo_moto (id_articulo, id_moto) VALUES " . $values;

            $query = $sql->ejecutar($sentencia);
            
            if(!$query){
                $sql->cancelarTransaccion();
            }            
            
        } else {
            $sentencia = "INSERT INTO fom_articulo_moto (id_articulo, id_moto) VALUES (" . $idArticulo . ", 999)";

            $query = $sql->ejecutar($sentencia);
            
            if(!$query){
                $sql->cancelarTransaccion();
            }              
            
        }

        $sql->finalizarTransaccion();
        return true;
        
    }

    /**
     * Metodo modificarMotosAplicacion--> ingresa en la tabla articulo_moto y modifica a que motos
     * aplica determinado articulo
     * 
     * @global objeto $sql objeto global de gestón con la BD.
     * @param entero $idArticulo identificador del articulo
     * @param arreglo $datosMotos arreglo con los id de las motos a las cuales aplica el determinado articulo
     * @return boolean  true o false dependiendo del exito de la transacción
     */
    public function modificarMotosAplicacion($idArticulo, $datosMotos) {
        global $sql;

        if (!($this->eliminarMotoAplicacion($idArticulo))) {
            return false;
            
        } else {
            $tamano = sizeof($datosMotos);
            $ultimo = $tamano - 1; //saber cual será la ultima posicion del arreglo
            
            $sql->iniciarTransaccion();

            if (!empty($datosMotos)) {//verifico si el articulo se aplica a varias motos
                $valor = '';
                $count = 0;
                
                foreach ($datosMotos as $valor) {
                    $count++;
                    $values .= '(' . $idArticulo . ', ' . $valor . ')';
                    if ($count <= $ultimo) {
                        $values .= ",";
                    }
                }//fin del foreach

                $sentencia = "INSERT INTO fom_articulo_moto (id_articulo, id_moto) VALUES " . $values;

                $query = $sql->ejecutar($sentencia);
                
                if(!$query){
                    $sql->cancelarTransaccion();
                }                
                
            } else {//si viene publico se comparte con el perfil 99
                $sentencia = "INSERT INTO fom_articulo_moto (id_articulo, id_moto) VALUES (" . $idArticulo . ", 999)";
                $query = $sql->ejecutar($sentencia);
                
                if(!$query){
                    $sql->cancelarTransaccion();
                }                
                
            }
            
        }

        $sql->finalizarTransaccion();
        return true;
    }


    /**
     * Metodo Eliminar Es llamado cuando se requiere modificar las motos a las cuales aplica determinado articulo
     * 
     * @global objeto $sql objeto global de gestón con la BD.
     * @param entero $idArticulo identificador del articulo
     * @return boolean  true o false dependiendo del exito de la transacción
     */
    public function eliminarMotoAplicacion($idArticulo) {
        global $sql;

        $condicion = "id_articulo = '" . $idArticulo . "'";

        $borrar = $sql->eliminar("articulo_moto", $condicion);

        if ($borrar) {
            return true;
            
        } else {
            return false;
            
        }   
    }



    /**
     * Cargar en la variable de tipo array motos, las motos a las cuales aplica determinado articulo
     * 
     * @global objeto $sql objeto global de gestón con la BD.
     * @param entero $id identificador del articulo
     * @return boolean  true o false dependiendo del exito de la transacción
     */
    public static function cargarMotosAplicables($id) {
        global $sql;

        $motos  = array();
        $tablas = array(
            'am'    => 'articulo_moto',
            'm'     => 'motos'
        );

        $columnas = array(
            'idArticulo'    => 'am.id_articulo',
            'idMoto'        => 'am.id_moto',
            'moto'          => 'm.nombre'
        );

        $condicion = 'am.id_moto = m.id AND am.id_articulo = "' . $id . '"';
        //$sql->depurar = true;
        $consulta = $sql->seleccionar($tablas, $columnas, $condicion);

        while ($moto = $sql->filaEnObjeto($consulta)) {
            $motos[] = $moto->moto;
        }

        $codigo1 = HTML::lista($motos, '');
        $codigo = HTML::contenedor($codigo1, 'contenedorAplicacionMotos');

        return $codigo;
    }
    
    
    /**
     * Metodo que carga el formulario con las motos a las cuales aplica un articulo
     * para ser editados
     * 
     * @global objeto $sql objeto global de gestón con la BD.
     * @param entero $idArticulo identificador del articulo
     * @param entero $idMotoPrincipal identificador de la moto principal a la cual aplica el articulo
     * @return boolean  true o false dependiendo del exito de la transacción
     */
    public static function cargarMotosAplicablesEdit($idArticulo, $idMotoPrincipal) {
        global $sql;

        $tablas = array(
            'am'    => 'articulo_moto',
            'm'     => 'motos'
        );

        $columnas = array(
            'idArticulo'    => 'am.id_articulo',
            'idMoto'        => 'am.id_moto',
            'moto'          => 'm.nombre'
        );

        $condicion = 'am.id_moto = m.id AND am.id_articulo = "' . $idArticulo . '"';
        //$sql->depurar = true;
        $consulta = $sql->seleccionar($tablas, $columnas, $condicion);
        
        $codigo     = array();
        $listaIds   = '';
        
        $claseMotoPrincipal = '';
        
        while ($moto = $sql->filaEnObjeto($consulta)) {
            if($moto->idMoto == $idMotoPrincipal){
                $claseMotoPrincipal = ' letraVerde';
                
            }else{
                $claseMotoPrincipal = '';
                
            }
            
            $codigo[]   = HTML::parrafo($moto->moto.HTML::frase('x', 'borrarMotoAplicacion', 'borrar_'.$moto->idMoto), 'parrafoMotoAplicacion'.$claseMotoPrincipal, $moto->idMoto) ;
            $listaIds  .= $moto->idMoto.'|';
            
        }
        
        $codigo = HTML::lista($codigo, 'listaOrdenable listaVertical', 'cursorMove liMotoAplicacion', 'listaMotosAplicacion');

        $codigo .= HTML::campoOculto('datos[listaMotos]', $listaIds, 'campoListaMotos');

        return $codigo;
    }

}
