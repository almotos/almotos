

$(document).ready(function(){
    
    $(".borrarMotoAplicacion").live("click", function(){
        var parrafo = $(this).parent("p");
        parrafo.slideUp("fast", function(){
            parrafo.parent("p").remove();
            
            var listaMotos = "";
            $(".parrafoMotoAplicacion:visible").each(function(i){
                var id = $(this).attr("id");
                listaMotos += id+"|";
            });
            $("#campoListaMotos").val("");
            $("#campoListaMotos").val(listaMotos);
        });

        $("#campoMotosAplicacion").focus();

        
    });
    


    
    
    
$("#chkMarcarFilas").bind("click", function(){
    
    if($("#chkMarcarFilas").attr("checked") == 'checked'){
        $("#tablaRegistros tr").not(':first').addClass('filaConsultada');
        $(".botonImprimirVariosBarcode").slideDown("fast");
    } else {
        $("#tablaRegistros tr").not(':first').removeClass('filaConsultada');
        $(".botonImprimirVariosBarcode").slideUp("fast");
    }
   
});    
    
    
    
    /*
     *Codigo para el boton eliminar varios registros al tiempo
     **/

    $("#botonImprimirVariosBarcode").live("click", function(e){
        e.preventDefault();
         
        var cantidad    = 0;
        var cadenaItems = '';
        
        $(".filaConsultada").each(function(){
            cantidad++;
            var id = $(this).attr("id");
            id = id.split('_');
            id = id[1];
            cadenaItems += id+",";
        });
        
        if(cantidad == 0){
            Sexy.alert("No ha seleccionado ningún articulo");
            return;
        }
         
        var ruta = $("#botonImprimirVariosBarcode").attr("ruta");
         
        $.ajax({
            type:"POST",
            url:ruta,
            dataType:"json",
            data: {
                cantidad: cantidad, 
                cadenaItems: cadenaItems
            },
            success:procesaRespuesta
        });
         
    })    
    
    
    

    
        
});

$("#gravado_iva").live("click", function(){
    if($(this).is(":checked")){
        $(this).next().removeClass("oculto");
        $(this).next().next().removeClass("oculto");
        
    } else {
        $(this).next().addClass("oculto");
        $(this).next().next().addClass("oculto");
        
    }
});




function seleccionarCampo(campo) {

    var id_campo    = $(campo).attr('id');
    var valor       = $(campo).val();
    
    if (valor != 0) {
        $('.selectorCampo').each(function() {
            
            if ($(this).attr('id') != id_campo && $(this).val() == valor) {

                $(this).val('0');
            }
        });
    }
}

















