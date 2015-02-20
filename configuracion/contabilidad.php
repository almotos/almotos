<?php

/**
 * Regimenes de las empresas
 */
$configuracion["REGIMENES"]["es"] = array(
                                        '1'     => 'Regimen simplificado',
                                        '2'     => 'Regimen Común',
                                        '3'     => 'Gran Contribuyente',
                                        '4'     => 'Gran Contribuyente autoretenedor',
                                        '5'     => 'Simplificado no residente',
                                        '6'     => 'No residente',
                                        '7'     => 'Empresa del estado',
                                        '8'     => 'No responsable',
                                    ); 

/**
 * Retenciones para compras
 */
$configuracion["RETENCIONES"]["es"] = array(
                                            '1'     => array(
                                                            'nombre'        => 'Retefuente',
                                                            'nombre_clave'  => 'retefuente',
                                                            'porcentaje'    => '3.5',//si no tiene porcentaje, el % esta ligado a actividad economica
                                                            'monto_minimo'  => '100%',
                                                            'id_cuenta'     => '236540',
                                                            ),
    
                                            '2'     => array(
                                                            'nombre'        => 'Reteica',
                                                            'nombre_clave'  => 'reteica',
                                                            'porcentaje'    => '5',
                                                            'monto_minimo'  => '27',//UVT
                                                            'id_cuenta'     => '236801',
                                                            ),
    
                                            '3'     => array(
                                                            'nombre'        => 'Reteiva',
                                                            'nombre_clave'  => 'reteiva',
                                                            'porcentaje'    => '100%',
                                                            'monto_minimo'  => '0',
                                                            'id_cuenta'     => '236701',
                                                            ),
    
                                            '4'     => array(
                                                            'nombre'         => 'Retecree',
                                                            'nombre_clave'   => 'retecree',
                                                            'campo_consulta' => 'porcentaje_retecree',
                                                            'monto_minimo'   => '100%',
                                                            'id_cuenta'      => '236570',
                                                            ),
    
                                            '5'     => array(
                                                            'nombre'        => 'Iva Teorico',
                                                            'nombre_clave'  => 'ivateorico',
                                                            'porcentaje'    => '15',
                                                            'monto_minimo'  => '27',
                                                            'id_cuenta2'    => '240802',
                                                            'id_cuenta'     => '236701',
                                                            ),
                                            );

/**
 * Retenciones para ventas 
 * el id de cuenta no declarante representa las cuentas de gastos en las cuales
 * se deben registrar las retenciones que se le realizan a un vendedor no declarante de renta
 */
$configuracion["RETENCIONES"]["VENTAS"]["es"] = array(
                                            '1'     => array(
                                                            'nombre'        => 'Retefuente',
                                                            'nombre_clave'  => 'retefuente',
                                                            'porcentaje'    => '3.5',//si no tiene porcentaje, el % esta ligado a actividad economica
                                                            'monto_minimo'  => '100%',
                                                            'id_cuenta'     => '135515',
                                                            'id_cuenta_no_declarante' => '111111',//cuenta de gastos
                                                            ),
    
                                            '2'     => array(
                                                            'nombre'        => 'Reteica',
                                                            'nombre_clave'  => 'reteica',
                                                            'porcentaje'    => '5',
                                                            'monto_minimo'  => '27',
                                                            'id_cuenta'     => '135518',
                                                            'id_cuenta_no_declarante' => '111111',
                                                            ),
    
                                            '3'     => array(
                                                            'nombre'        => 'Reteiva',
                                                            'nombre_clave'  => 'reteiva',
                                                            'porcentaje'    => '50',
                                                            'monto_minimo'  => '27',
                                                            'id_cuenta'     => '135517',
                                                            'id_cuenta_no_declarante' => '',
                                                            ),
    
                                            '4'     => array(
                                                            'nombre'         => 'Retecree',
                                                            'nombre_clave'   => 'retecree',
                                                            'campo_consulta' => 'porcentaje_retecree',
                                                            'monto_minimo'   => '100%',
                                                            'id_cuenta'      => '135519',
                                                            'id_cuenta_no_declarante' => '111111',
                                                            ),
    
                                            '5'     => array(
                                                            'nombre'        => 'Iva Teorico',
                                                            'nombre_clave'  => 'ivateorico',
                                                            'porcentaje'    => '15',
                                                            'monto_minimo'  => '27',
                                                            'id_cuenta2'    => '',
                                                            'id_cuenta'     => '',
                                                            'id_cuenta_no_declarante' => '',
                                                            ),
                                            );