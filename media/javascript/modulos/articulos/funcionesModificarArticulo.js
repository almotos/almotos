/**
* Funcion que se encarga de agregar motos al listado de motos de aplicacion de un articulo 
**/
var campoMotos = $("#campoMotosAplicacion");

if(!campoMotos.hasClass("activado")){
    campoMotos.addClass("activado");                                     
    campoMotos.bind("autocompleteselect", function( event, ui) { 
        
        var agregar = true;
        
        $(".parrafoMotoAplicacion:visible").each(function(){
            var id = $(this).attr("id");
            
            if(id == ui.item.value){
                agregar = false;
            }
            
        });        
        
        if(agregar){
            //contenido que se va a agregar
            var contenido = "<li class='cursorMove'><p id = '"+ui.item.value+"' class='parrafoMotoAplicacion' >"+ui.item.label;
            contenido += "<span id='borrar_"+ui.item.value+"' class = 'borrarMotoAplicacion'>x</span></p></li>";
            //se agrega el contenido
            $("#listaMotosAplicacion").append(contenido);

            setTimeout(function(){
                $("#campoMotosAplicacion").val('');

                var listaMotos = "";//se recorren los parrafos con el listado de motos
                //y se agrega este valor a un campo 
                $(".parrafoMotoAplicacion:visible").each(function(i){
                    var id = $(this).attr("id");
                    listaMotos += id+"|";
                });

                $("#campoListaMotos").val(listaMotos);

            }, 100);            
            
        } else {
            Sexy.alert("Esta moto ya existe en el listado", {
                onComplete: function(){
                    $("#campoMotosAplicacion").val('').focus();
                }
            });
            
        } 

    });

}


/**
 * Función para el arrastrar y soltar de las motos de aplicacion
 */
$( "#listaMotosAplicacion" ).sortable({
    update: function() {
            var listaMotos = "";
            $(".parrafoMotoAplicacion:visible").each(function(i){
                var id = $(this).attr("id");
                listaMotos += id+"|";
            });
            $("#campoListaMotos").val("");
            $("#campoListaMotos").val(listaMotos);
    }
});