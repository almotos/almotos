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

$("#formaAdicionarArticulos").find("button#botonOk").bind("click", function(e){
    e.preventDefault();

    $("#indicadorEspera").css("display","block");
    $("#BoxOverlay").css("display","block");

    formulario  =   $(this).parents("form");
    destino     =   $(formulario).attr("action");    
    validar     =   $(this).attr("validar");

    if(validar == "NoValidar"){//modificado
        validarFormularioArticulos(formulario); 

    } else {
        enviarFormularioArticulos(formulario);

    }
    
});





function validarFormularioArticulos(forma){
    
    var enviar = true;//enviar por defecto en true
    
    forma.find(".campoObligatorio").each(function(i){//recorro todos los campos obligatorios dentro del form
        var valor = $(this).val();
        
        if(valor == ""){//si alguno esta vacio
            $(this).addClass("textFieldBordeRojo campo_obligatorio");//le agrego las clases que indican error al campo de texto
            enviar = false;
        }
        
    }).promise().done(function(){//una vez ha terminado de recorrer todos los campos
        
        if(enviar){//valido el envio
            enviarFormularioArticulos(forma);
            
        } else{//si hubo campos vacios simplemente retiro las capas de proteccion
//            forma.find(".textFieldBordeRojo:first").focus();
            var campoError = forma.find(".textFieldBordeRojo:first");
            var pestana = campoError.parents('div.contenidoPestana');

            if(pestana){
                var idPestana   = pestana.attr("id");
                var pestanas    = campoError.parents('div.ui-tabs');                
                pestanas.find(".ui-tabs-nav li a[href='#"+idPestana+"']").trigger("click");  
                forma.find(".textFieldBordeRojo:first").focus();
                
            } else {
                forma.find(".textFieldBordeRojo:first").focus();
                
            }

            $("#indicadorEspera").css("display","none");
            $("#BoxOverlay").css("display","none");
            $("#BoxOverlayTransparente").css("display","none");   
            
        }
    });
    
    setTimeout(function(){
        $(".textFieldBordeRojo").removeClass("textFieldBordeRojo");
        $(".campo_obligatorio").removeClass("campo_obligatorio");
    }, 2500);
    
}


function enviarFormularioArticulos(formulario){
        $(formulario).ajaxForm({
            dataType:   "json"
        });
        $(formulario).ajaxSubmit({
            dataType:   "json",
            success:    procesaRespuestaArticulos
        });     
}


function procesaRespuestaArticulos(respuesta){
    var modulo = $("#nombreModulo").val();
    basicasProcesaRespuesta(respuesta);
    
    if(respuesta.error){
        funcionesRespuestaError(respuesta);

    }else{        
        switch(modulo){
            case "Articulos":
                respuestaInsertarArticulos(respuesta);
                break;
                
            case "Compras_mercancia":
                respuestaInsertarCompras(respuesta);
                break;
                
            case "Ventas_mercancia":
                respuestaInsertarCompras(respuesta);
                break;
                
        }
        
    }
}

function respuestaInsertarCompras(respuesta) {
    mostrarNotificacionDinamica('Registro agregado exitosamente', 'exitoso');
    
    var _obj = { id:respuesta.contenido.id, iva:respuesta.contenido.iva, label:respuesta.contenido.nombre, value:respuesta.contenido.precioVenta};
    
    agregarItemListaArticulo(_obj);
    
    $("#cuadroDialogo").dialog("close");                
    ocultarNotificacionDinamica();
}

function respuestaInsertarVentas(respuesta) {
    mostrarNotificacionDinamica('Registro agregado exitosamente', 'exitoso');
    
    var _obj = { id:respuesta.contenido.id, iva:respuesta.contenido.iva, label:respuesta.contenido.nombre, value:respuesta.contenido.precioVenta};
    
    agregarItemListaArticulo(_obj);
    
    $("#cuadroDialogo").dialog("close");                
    ocultarNotificacionDinamica();
}


function respuestaInsertarArticulos(respuesta) {
    
    var plantillaFila = "<tr class='oculto filasTabla' id='tr_<%= obj.id %>' ><td class='centrado'> <%= obj.id %> </td><td class='centrado'> <%= obj.nombre %> </td> <td class='centrado'> <%= obj.linea %> </td><td class='centrado'> <%= obj.subgrupo %> </td><td class='centrado'> <%= obj.codigoPais %></td><td class='centrado'> $<%= obj.precioVenta %> </td><td class='centrado'> $<%= obj.precioCompra %> </td><td class='centrado'> <%= obj.completo %> </td><td class='centrado'> <%= obj.campoCantidad %></td></tr>";
    
    var obj = respuesta.contenido;
    
    var contenido = _.template(plantillaFila, obj);

    mostrarNotificacionDinamica('Registro agregado exitosamente', 'exitoso');
    $(respuesta.idDestino).append(contenido);             

    $("#cuadroDialogo").dialog("close");                
    ocultarNotificacionDinamica();

    $(respuesta.idContenedor).fadeIn("slow", function(){                    
        if($("#trSinRegistros").is(":visible")){
            $("#trSinRegistros").fadeOut("slow");
        }
    });
}

