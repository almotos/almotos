/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$("#chkModInventario").bind("click", function(){
    var contenedor = $("#contenedorListaArticulosNota");
    if(contenedor.is(":visible")){
        contenedor.slideUp("fast");
    }else{
        contenedor.slideDown("fast");
    }
});


$("#campoMontoNota").bind("keyup", function(){
    var ivaNota     = $("#campoIvaNota").val();
    ivaNota = (ivaNota == '') ? 0 : parseDouble(ivaNota);
    var montoNota   = $(this).val();
    montoNota = (montoNota == '') ? 0 : parseDouble(montoNota);

    $("#campoTotalNota").val(montoNota + ivaNota);
    
});

$("#campoIvaNota").bind("keyup", function(){    
    var ivaNota     = $(this).val();
    ivaNota = (ivaNota == '') ? 0 : parseDouble(ivaNota);
    var montoNota   = $("#campoMontoNota").val();
    montoNota = (montoNota == '') ? 0 : parseDouble(montoNota);

    $("#campoTotalNota").val(montoNota + ivaNota);
    
});