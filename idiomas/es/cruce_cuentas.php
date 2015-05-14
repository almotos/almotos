<?php

/**
 *
 * @package     FOLCS
 * @subpackage  Textos tipos de compra
 * @author      Pablo Andrés Vélez Vidal. <pavelez@genesyscorp.com.co>
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   Copyright (c) 2013 Genesys corporation
 * @version     0.1
 *
 **/

$textos = array(
    /*Basicos*/
    'MODULO_ACTUAL'                             => 'Operaciones de Negocio - Cruzar cuentas con operacion',
    'AGREGAR_CUENTAS_AFECTADAS'                 => 'Agregar cuenta contable',
    'ADICIONAR_ITEM'                            => 'Adicionar',
    'MODIFICAR_ITEM'                            => 'Modificar',
    'ELIMINAR_ITEM'                             => 'Eliminar',
    'CONSULTAR_ITEM'                            => 'Consultar',
    'BUSCAR_ITEM'                               => 'Buscar',    
    
    /*Campos y labels*/
    'NOMBRE'                                    => 'Nombre',
    'ACTIVO'                                    => 'Activo',
    'INACTIVO'                                  => 'Inactivo',
    'TIPO'                                      => 'Tipo',
    'CATEGORIA'                                 => 'Categoria o Modulo',
    'DESCRIPCION'                               => 'Descripción',
    'ID'                                        => 'Id Auto',
    'TIPO_1'                                    => 'Crédito',
    'TIPO_2'                                    => 'Contado',
    'TIPO_3'                                    => 'Mixto',
    'CATEGORIA_1'                               => 'Compras',
    'CATEGORIA_2'                               => 'Ventas',  
    'CATEGORIA_3'                               => 'Ambos Modulos', 
    'INFO_TIPO_COMPRA'                          => 'Info tipo compra',
    'LISTA_CUENTAS'                             => 'Lista cuentas',
    'TIPO_CUENTA_1'                             => 'Crédito',
    'TIPO_CUENTA_2'                             => 'Débito',
    'EDITAR'                                    => 'Editar',
    'ADICIONAR_CUENTA_CONTABLE'                 => 'Adicionar cuenta contable',
    'TIPO_CUENTA'                               => 'Tipo cuenta (Forma en que se afectará la cuenta)',
    'CUENTA_CONTABLE'                           => 'Cuenta contable',
    'ELIMINAR_CUENTA'                           => 'Eliminar cuenta',
    'CODIGO_CONTABLE'                           => 'Código contable',
    'INFO_OPERACION_NEGOCIO'                    => 'Info. Operación negocio',
    'CODIGO'                                    => 'Codigo',
    'BASE_MIN_TOTAL_PESOS'                      => 'Base min. total UVT &nbsp;<i>(Valor UVT: $ <span id="valorUvt">%1</span>)  &nbsp;&nbsp; Total Pesos: <span id="totalPesosUvt">$0</span></i>',
    'PORCENTAJE_DEL_TOTAL'                      => 'Porcentaje del total.',
    'BASE_TOTAL_PESOS'                          => 'Min. UVT ',
    'BASE_TOTAL_PORCENTAJE'                     => 'Min. % ',
    'PORCENTAJE_DEL_TOTAL'                      => '% del total',
    'SIN_CUENTAS_ASOCIADAS'                     => 'Esta operacion de negocio no tiene cuentas asociadas. Haga click en <u><i>"Agregar cuenta contable"</u></i> y agregue las cuentas que desea que sean afectadas.',
    
    
    /*Errores*/
    'ERROR_FALTA_CODIGO'                        => 'Error falta codigo',
    'ERROR_FALTA_NOMBRE'                        => 'Error falta nombre',
    'ERROR_FALTA_TIPO'                          => 'Error falta tipo', 
    'ERROR_FALTA_CUENTA'                        => 'Error, falta cuenta',
    'ERROR_NO_EXISTE_CUENTA'                    => 'Error, no existe cuenta',
    'ERROR_CUENTA_EXISTENTE'                    => 'Error, cuenta ya agregada a este cruce de cuentas',
    'ERROR_FALTA_BASE_MININA'                   => 'Error, debe seleccionar o la base minima en pesos o en %',
    
    /*Ayuda*/
    'SELECCIONA_EL_TIPO'                        => 'Selecciona el tipo de grupo',
    'AYUDA_BASE_MIN_TOTAL_PESOS'                => 'Base minima del total de la factura<br> en UVT para que esta cuenta<br> sea afectada.',
    'AYUDA_BASE_MIN_TOTAL_PORCENTAJE'           => 'Porcentaje del valor total que<br> se va a guardar en esta<br> cuenta.',    
    'AYUDA_SELECCIONAR_TIPO'                    => 'Selecciona el cruce de cuentas:<br>-Crédito<br>-Contado<br>-Mixto(Parte a crédito parte a contado)',
    'AYUDA_SELECCIONAR_CATEGORIA'               => 'Selecciona la categoria o módulo:<br>-Compras<br>-Ventas<br>-Ambos Modulos<br>Al cual va a pertenecer este cruce de cuentas',
    'AYUDA_MODULO'                              => '<p>*Para agregar un Item Click en el Boton Agregar o Ctrl+A </p><p>*Para buscar un Item Click en el Boton buscar o Ctrl+B </p><p>*Para Consultar - Editar - Eliminar un Item haga Click Derecho sobre la fila en que aparece.</p>*Para cerrar esta ventana presione Ctrl+H, o haga click en cualquier lugar <br>'
);
