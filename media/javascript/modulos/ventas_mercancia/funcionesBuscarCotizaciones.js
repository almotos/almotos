/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$("#textoBusquedaAvanzada").bind("click", function(){
    if($("#contenedorBusquedaAvanzada").is(":visible")){
        $("#contenedorBusquedaAvanzada").slideUp("fast");
        $("#textoBusquedaAvanzada").html("Busqueda avanzada >>");
        $("#contenedorBusquedaRapida").slideDown("fast");
    } else {
        $("#contenedorBusquedaAvanzada").slideDown("fast");
        $("#textoBusquedaAvanzada").html("Busqueda rápida >>");
        $("#contenedorBusquedaRapida").slideUp("fast");
    }
});


$("#campoIdCotizacion").bind( "autocompleteselect", function( event, ui) {      

    $("#campoIdCotizacion").val('');
    $("#campoIdCotizacion").val(ui.item.nombre);
    $("#ocultoIdCotizacion").val(ui.item.value);
    
    setTimeout(function(){
        formulario = $("#campoIdCotizacion").parents("form");
        formulario.submit();
    }, 75);
           
});   