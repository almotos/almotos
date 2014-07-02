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


$("#campoIdOrden").bind( "autocompleteselect", function( event, ui) {      

    $("#campoIdOrden").val('');
    $("#campoIdOrden").val(ui.item.nombre);
    $("#ocultoIdOrden").val(ui.item.value);
    
    setTimeout(function(){
        formulario = $("#campoIdOrden").parents("form");
        formulario.submit();
    }, 75);
           
});   