/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

function mostrarFactor(obj){
    
    if(obj.val() != 'principal'){
        $("#contenedorFactorConversion").removeClass("oculto");
    } else {
        $("#contenedorFactorConversion").addClass("oculto");
    }
    
}
