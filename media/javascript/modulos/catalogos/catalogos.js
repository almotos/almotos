
$(document).ready(function(){
    
    $('#textoBuscarMoto').live('click', function(){
        $("#contenedorBusquedaMoto").slideDown('fast');
        $("#contenedorBusquedaLinea").slideUp('fast');
    });
    
    $('#textoBuscarLinea').live('click', function(){
        $("#contenedorBusquedaLinea").slideDown('fast');
        $("#contenedorBusquedaMoto").slideUp('fast');        
    });    
    
    
});//fin del document ready