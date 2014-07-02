/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


$(document).ready(function(){
    /**
 * Funcion encargada de enviar la informacion del articulo de carga masiva para armar en la
 * respuesta los campos de los selectores de el archivo
 */    
    $("#archivoMasivo").live("change", function(){
        $("#indicadorEspera").css("display","block");
        $("#BoxOverlay").css("display","block");
        
        formulario=$(this).parents("form");
        destino=$(formulario).attr("action");

        $(formulario).ajaxForm({
            dataType:"json"
        });
        $(formulario).ajaxSubmit({
            dataType:"json",
            success:procesaRespuestaInicial
        });

        return false;

    });
    
});