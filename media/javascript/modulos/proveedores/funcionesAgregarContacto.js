/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


    /*Funcion que es lanzada sobre el evento selected del plugin autocomplete sobre el
     *campo de documento de identidad de una persona del sistema*/
    $("#campoDocumentoPersona1").bind("autocompleteselect", function( event, ui) {
        var doc = ui.item.value;
        var formulario = $(this).parents("form");
        completarDatosPersona( doc, formulario);
        setTimeout(function(){
            $("#campoDocumentoPersona1").val(doc);
        }, 300);
        
    });    
    
