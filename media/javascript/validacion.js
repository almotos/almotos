/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


$(document).ready(function(){
    
    /**
    *
    *Codigo para la validacion del formulario de editarUsuarios
    *ubicado en usuarios/ajax.php
    *
    **/
   
        $("#formaEditarUsuario").validate({
            rules:{
                sobrenombre:{
                    required: true
                }

            }

        });
    
    
})