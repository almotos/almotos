/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


$(document).ready(function(){
    
    /**
    *Codigo para generar el evento de click en el boton derecho,
    *cuando se hace click se agrega al menu derecho el campo sede
    *para consultar la sede
    **/
    $("#tablaRegistros tr:gt(0)").live('contextmenu', function(e) {
        // evito que se ejecute el evento
        e.preventDefault();   
        
        var id  = $(this).attr("id");
        id      = id.split("_");
        
        var idSede = $(this).attr("atributo_3");
        
        $("#contenedorBotonDerecho").find("form").find("input[name='id']").val(id[1]+"-"+idSede);

    });     
    
});