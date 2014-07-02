/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */



function armarDomConJquery(destino){
        
    $(destino+" .pestanas").tabs();

    $(destino).find(".pestanas").tabs({
        cookie:{
            expires:30
        }
    });
                                
    $(destino+" .fechaReciente").datepicker({
        dateFormat:"yy-mm-dd",
        changeMonth:true,
        changeYear:true,
        showButtonPanel:true,
        minDate:0,
        maxDate:365
    });
    
    
    $(destino+" .fechaAntigua").datepicker({
        dateFormat:"yy-mm-dd",
        changeMonth:true,
        changeYear:true,
        showButtonPanel:true,
        yearRange:"c-100:c"

    });
    
    
    $(destino+" button").each(function(){
        icono=$(this).attr("title");
        $(this).button({
            icons:{
                primary:icono
            }
        });

    });
    
    
    $(destino+" button").not(".directo").click(function(){
        //        $("#indicadorEspera").css("display","block");
        $("#BoxOverlayTransparente").css("display","block");
        
        formulario  =   $(this).parents("form");
        destino     =   $(formulario).attr("action");    
        validar     =   $(this).attr("validar");
                
        if(validar != "NoValidar"){
            validarFormulario(formulario); 
            
        } else {
            enviarFormulario(formulario);
            
        }

        return false;
        
    });
                
                
    $(destino+" .enlaceAjax").click(function(){
        $("#indicadorEspera").css("display","block");
        $("#BoxOverlay").css("display","block");
        destino=$(this).attr("title");
        $.ajax({
            type:"POST",
            url:destino,
            dataType:"json",
            success:procesaRespuesta
        });
        return false;
    });
                


    $('*').tooltip({
        track: true,
        delay: 0,
        showURL: false
    });


    $(destino+" a[rel^='prettyPhoto']").prettyPhoto(); 

    $(destino+" .autocompletable").each(function(){
        lista=$(this).attr("title");
        $(this).autocomplete({
            minLenght:1,
            source:lista,
            select: function( event, ui ) {
                $( this ).val( ui.item.label );
                $( this ).next("input[type='hidden']").val( ui.item.value );

                return false;
            }
            
        });
    });


    $(destino+" .soloNumeros").bind("keydown", function(e){
        var tecla= document.all ? tecla = e.keyCode : tecla = e.which;
        return ((tecla > 95 && tecla < 106) || (tecla > 47 && tecla < 58) || tecla == 8 || tecla == 37 || tecla == 39 || tecla == 127);
    });
    
    
    $(destino).find(".editor").ckeditor();
    $("#botonFormaEditarUsuarios").click(function(){//click en el boton del formulario de editar usuarios
        if ($("#formaEditarUsuario").valid()) {//si el formulario es valido, envio los datos via Ajax
            $("#indicadorEspera").css("display","block");
            $("#BoxOverlay").css("display","block");
            formulario=$(this).parents("form");
            destino=$(formulario).attr("action");
            $(formulario).ajaxForm();
            $(formulario).ajaxSubmit({
                dataType:"json",
                success:function(respuesta,estado){
                    procesaRespuesta(respuesta);
                }
            });
            return false;
        }
        return false;


    });    
    
  
    
    
}


function activarFuncionesCuadroDialogo(destino){
    
    var modulo = $("#nombreModulo").val();
    
    /**
     * Función que agrega el plugin chosen ver 
     **/
//    $(".selectChosen").chosen({no_results_text: "Oops, no se encontraron resultados!"});  
  
             
    /*Codigo para agregar el resaltado a la primera fila de la tabla(despues de la fila cabecera)
     */
    $(".tablaListarItems tr:gt(0):first").addClass("filaTablaSeleccionarItem");    
    $(".tablaListarItems tr").not("tr:first").hover(function(){
        $(this).addClass("filaTablaSeleccionarItem")
    }, function(){
        $(this).removeClass("filaTablaSeleccionarItem")
    });
    $(".tablaListarItems tr").not("tr:first").click(function(){
        if($(this).hasClass("filaTablaItemSeleccionada")){
            $(this).removeClass("filaTablaItemSeleccionada");
        }else{
            $(this).addClass("filaTablaItemSeleccionada");
        }
    });           
    
                             
    $("#cuadroDialogo").onShow(function(){
        $("#cuadroDialogo").find("input:text:visible:first").focus();

    });   
    
    $(".listaPestanas").find("li").find("a").click(function(){
        $(".contenidoPestana").find("input:visible:first").focus();
    });     
    
    //funciones del codigo de barras
    if(modulo == "Articulos" || modulo == "compras_mercancia" || modulo == "ventas_mercancia"){    
        $("#contenedorCodigoBarras").addClass("cargando2");
        setTimeout(function(){
            $("#contenedorCodigoBarras").removeClass("cargando2");
            var codigo = $(destino+" #idCodigoBarras").val();

            $(destino+" #contenedorCodigoBarras").barcode(codigo, "code39" ,{
                barWidth:2, 
                barHeight:60
            });             
        }, 1000);
   
    }    
                                    
    $(destino+" .fechaAntigua").datepicker({
        dateFormat:"yy-mm-dd",
        changeMonth:true,
        changeYear:true,
        showButtonPanel:true,
        yearRange:"c-100:c"

    });
    
    if(modulo == "Usuarios"){
        $(destino+' .selectorHora').timepicker({
            currentText: "Ahora",
            closeText:   "Seleccionar",
            timeText: "Hora",
            hourText: "Hora",
            minuteText: "Minuto",
            timeOnlyTitle: "Selecciona Hora"
        });        
        
    }
    

    
    /*Funcion que es lanzada sobre el evento selected del plugin autocomplete sobre el
     *campo de documento de identidad de una persona del sistema*/
    $("#campoDocumentoPersona").bind("autocompleteselect", function( event, ui) {
        var doc = ui.item.value;
        var formulario = $(this).parents("form");
        completarDatosPersona( doc, formulario);
        setTimeout(function(){
            $("#campoDocumentoPersona").val(doc);
        }, 300);
        
    });    
    
    /*Funcion que es lanzada sobre el evento selected del plugin autocomplete sobre el
     *campo de documento de identidad de una persona del sistema*/
    $("#campoTextoBusquedaModulos").bind( "autocompleteselect", function( event, ui) {
        var ruta = ui.item.value;
        var rutaServidor = $("#rutaServidor").val();
        window.open(rutaServidor+ruta, '_blank');
        $(this).parents(".ui-dialog").find(".ui-dialog-titlebar-close").trigger("click");
    });        
    
  
    
    
    
    $(destino).find("input").bind("keypress", function(e){
        if(e.which == 13){
            return false;
        }  
        return true;
    });
    
    //agrega el icono de obligatorio en los inputs que lo sean  
    var rutaServidor = $("#rutaServidor").val();
    $(destino+" .campoObligatorio").prev("p").prepend("<img src='"+rutaServidor+"media/estilos/imagenes/obligatorio3.gif' style='cursor:help; margin-right:5px;' ayuda='Campo Obligatorio'/>");
    $(destino+" .campoObligatorio2").parents("p").prepend("<img src='"+rutaServidor+"media/estilos/imagenes/obligatorio3.gif' style='cursor:help; margin-right:5px;' ayuda='Campo Obligatorio'/>");
  
    setTimeout(function(){
      
        $('*').tooltip({
            track: true,
            delay: 0,
            showURL: false
        });      
      
    }, 500);
    
    
}


