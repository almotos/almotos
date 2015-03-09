
 
$(document).ready(function(){
    //codigo para consultar un articulo
    $("#eliminarFacturaDigital").live("click", function(){
        var id = $(this).attr("idfactura");
        $(this).parents("p").html('Factura digital eliminada');
        $.ajax({
            type:"POST",
            url:"/ajax/facturas_compra/eliminarFacturaDigital",
            dataType:"json",
            data: {
                id: id
            },
            success:procesaRespuesta
        });
        
    });
    
    //codigo para consultar un articulo
    $("#eliminarNotaCreditoDigital").live("click", function(){
        var id = $(this).attr("idnota");
        
        $(this).parents("p").html('Nota digital eliminada');
        
        $.ajax({
            type:"POST",
            url:"/ajax/facturas_compra/eliminarNotaCreditoDigital",
            dataType:"json",
            data: {
                id: id
            },
            success:procesaRespuesta
        });
        
    });    
    
    
    //codigo para consultar un articulo
    $("#eliminarNotaDebitoDigital").live("click", function(){
        var id = $(this).attr("idnota");
        
        $(this).parents("p").html('Nota digital eliminada');
        
        $.ajax({
            type:"POST",
            url:"/ajax/facturas_compra/eliminarNotaDebitoDigital",
            dataType:"json",
            data: {
                id: id
            },
            success:procesaRespuesta
        });
        
    });     
    
});