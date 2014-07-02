$(document).ready(function(){
    /**
     * funcion para verificar si esta visible la tablaEditarMasContactos, en caso de estar visible
     * cambia el valor de un campo oculto, para que en el ajax se arme la respuesta como debe ser
     **/
    $("#botonAdicionarContactoCliente").bind("click", function(e){
        
        if($("#tablaEditarMasContactos").is(":visible")){
            $(this).find("input[name=tablaEditarVisible]").val("1");
        } else {
            $(this).find("input[name=tablaEditarVisible]").val("0");
        }
        
    });
    
    /**
     * funcion para verificar si esta visible la tablaEditarMasSedes, en caso de estar visible
     * cambia el valor de un campo oculto, para que en el ajax se arme la respuesta como debe ser
     **/
    $("#botonAdicionarSedeCliente").bind("click", function(e){
        
        if($("#tablaEditarMasSedes").is(":visible")){
            $(this).find("input[name=tablaEditarVisible]").val("1");
        } else {
            $(this).find("input[name=tablaEditarVisible]").val("0");
        }
        
    });       
    
     
});

