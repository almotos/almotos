    /*Funcion que es lanzada sobre el evento selected del plugin autocomplete sobre el
     *campo de seleccionar articulos en una factura, esto para el precio de venta*/
    $("#campoTextoBusquedaCatalogos").bind("autocompleteselect", function( event, ui) {      
        var item    = ui.item.value;
        
        $("#indicadorEspera").css("display","block");
        
        $.ajax({
            type:"POST",
            url:"/ajax/motos/cargarCatalogos",
            dataType:"json",
            data: {
                idMoto: item
            },
            success: function(respuesta){
                procesaRespuesta(respuesta);
                
            }
        });
        
        $(this).parents("div.ui-dialog").find(".ui-dialog-titlebar-close").trigger("click");
        
    });  
         