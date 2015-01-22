/*
* Codigos que agregan via ajax los options con las bodegas
* segun la sede que se seleccione
**/
$("#selectorSedes").live("change", function(){
    var sede = $("#selectorSedes").val();
    if(sede != ''){
        $.ajax({
            type:"POST",
            url:"/ajax/ventas_mercancia/escogerBodega",
            data: {
                idSede : sede
            },
            dataType:"json",
            success:function (respuesta){
                $("#selectorBodegas").html(respuesta.contenido);
            }

        });          
           
    }   
});
    
/**
     * Funciones que se ejecutan cuando se selecciona una bodega de origen
     * para poner el valor del identificador de la bodega en el campo oculto idBodegaOrigen,
     * tambien pone el nombre de la bodega en el span bodegaOrigen
     */
$(".checkBodegaOrigen").click(function(){    
    $(".checkBodegaOrigen").removeAttr('checked');
    $(this).attr('checked', 'checked');
    
    $('.parrafoSeleccionado').removeClass('parrafoSeleccionado');
    $(this).parents('p').addClass('parrafoSeleccionado');
    
    $("#idBodegaOrigen").val($(this).attr('id'));
    $("#bodegaOrigen").html($(this).attr('bodega'));
    
    $("#campoCantidadAMover").attr("rango", "1-"+$(this).attr("cantidad"));
    $("#textoCantidadAMover").html("Ingrese la cantidad que desea mover, entre 1 y "+$(this).attr("cantidad"));
    
});

/**
 *  Funciones utilizadas cuando se hace click en el boton
 *  restaurar valores iniciales
 */

$("#botonRestaurarValores").click(function(e){
    e.preventDefault();
    
    $(".checkBodegaOrigen").removeAttr('checked');
    $('.parrafoSeleccionado').removeClass('parrafoSeleccionado');
    
    $("#idBodegaOrigen").val('');
    $("#bodegaOrigen").html('Selecciona una bodega de origen');
    $("#cantidadMercancia").val('');
    
    $("#campoCantidadAMover").val("");
    $("#textoCantidadAMover").html("Ingrese la cantidad que desea mover");   
    
    $("#selectorSedes").val('0');
    $("#selectorBodegas").empty();
    
});

/**
 *  Funciones utilizadas cuando se hace click en el boton
 *  aceptar
 */
function realizarMovimiento(event, obj){
    event.preventDefault();
    
    var padre = obj.parents("div.ui-dialog-content");
    
    var bodegaOrigen        = padre.find("#idBodegaOrigen").val();
    var bodegaDestino       = padre.find("#selectorBodegas").val();
    var nombreBodegaDestino = padre.find("#selectorSedes option:selected").html()+" :: "+padre.find("#selectorBodegas option:selected").html();
    var nombreBodegaOrigen  = padre.find("input.checkBodegaOrigen:checked").attr("bodega");
    var cantidadMercancia   = padre.find("#campoCantidadAMover").val();
    var idArticulo          = padre.find("#idArticulo").val();
    var dialogo             = padre.find("#idDialogo").val();
    
    if(bodegaDestino == null || bodegaDestino == ''){
        Sexy.alert("Debes seleccionar una bodega de destino");
        return;
        
    } else if(bodegaOrigen == '') {
        Sexy.alert("Debes seleccionar una bodega de origen");
        return;   
        
    }  else if(bodegaOrigen == bodegaDestino) {
        Sexy.alert("La bodega de destino debe ser diferente a la bodega de origen");
        return;   
        
    } else if(cantidadMercancia == ''){
        Sexy.alert("Debes ingresar una cantidad de items");
        return;   
        
    } else {
        Sexy.confirm("<p class=margin5>Esta seguro que desea mover <span class=subtitulo negrilla>"+cantidadMercancia+"</span> items de la bodega <span class=subtitulo>--"+nombreBodegaOrigen+"</span> a la bodega <span class=subtitulo>--"+nombreBodegaDestino+"--</span>? </p>", {
            onComplete: function(returnvalue){ 
                if(returnvalue){
                    
                    $.ajax({
                        type:"POST",
                        url:"/ajax/articulos/accionMoverMercancia",
                        data: {
                            bodegaO : bodegaOrigen,
                            bodegaD : bodegaDestino,
                            cantidad : cantidadMercancia,
                            idArticulo : idArticulo
                        },
                        dataType:"json",
                        success:function (){
                            Sexy.info('Mercancia movida correctamente');
                            if(dialogo == ''){//si la variable dialogo viene vacia, es porque estuvo abieta la primera ventana de dialogo
                                dialogo = '#cuadroDialogo';
                            }
                            $(dialogo).dialog("close");
                        }

                    });  
                    
                } else {
                    return;
                    
                }
            

            }
        });  
        
    }
    
}

$("#imagenPregunta").css("cursor", "pointer");
$("#imagenPregunta").bind("click", function(){
    introJs().start();
});