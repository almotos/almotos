/**
 * Cuando unas funciones javascript se agregan en vivo no se puede usar la funcion live, solo 
 * la funcion bind
 **/

    $("#botonModificarClientes").bind("click", function(e){//click en el boton del formulario
        e.preventDefault();
        var pestanas    = $("#pestanasModificar");
        var formulario  = $(this).parents("form");
        
        if(formulario.find("#nombreCliente").val() == ""){
            formulario.find("#nombreCliente").addClass("textFieldBordeRojo");
            formulario.find("#nombreCliente").addClass("campo_obligatorio");
            formulario.find("#nombreCliente").focus();
            pestanas.find(".ui-tabs-nav li a:first").trigger("click");
            
        } else if(formulario.find("#idCliente").val() == ""){
            formulario.find("#idCliente").addClass("textFieldBordeRojo");
            formulario.find("#idCliente").addClass("campo_obligatorio");
            pestanas.find(".ui-tabs-nav li a:first").trigger("click");  
            formulario.find("#idCliente").focus();
            
        } else if(formulario.find("#ciudadSede").val() == ""){
            formulario.find("#ciudadSede").addClass("textFieldBordeRojo");
            formulario.find("#ciudadSede").addClass("campo_obligatorio");
            pestanas.find(".ui-tabs-nav li a:first").trigger("click");
            formulario.find("#ciudadSede").focus();
            
        } else if(formulario.find("#telefonoSede").val() == ""){
            formulario.find("#telefonoSede").addClass("textFieldBordeRojo");
            formulario.find("#telefonoSede").addClass("campo_obligatorio");
            pestanas.find(".ui-tabs-nav li a:first").trigger("click");   
            formulario.find("#telefonoSede").focus();        
            
        } else if(formulario.find("#direccionSede").val() == ""){
            formulario.find("#direccionSede").addClass("textFieldBordeRojo");
            formulario.find("#direccionSede").addClass("campo_obligatorio");
            pestanas.find(".ui-tabs-nav li a:first").trigger("click");  
            formulario.find("#direccionSede").focus();          
            
        } else if(formulario.find("#campoDocumentoPersona").val() == ""){
            formulario.find("#campoDocumentoPersona").addClass("textFieldBordeRojo");
            formulario.find("#campoDocumentoPersona").addClass("campo_obligatorio");
            pestanas.find(".ui-tabs-nav li a:eq(1)").trigger("click");      
            formulario.find("#campoDocumentoPersona").focus();      
            
        } else if(formulario.find("#campoPrimerNombrePersona").val() == ""){
            formulario.find("#campoPrimerNombrePersona").addClass("textFieldBordeRojo");
            formulario.find("#campoPrimerNombrePersona").addClass("campo_obligatorio");
            pestanas.find(".ui-tabs-nav li a:eq(1)").trigger("click");      
            formulario.find("#campoPrimerNombrePersona").focus();       
            
        } else if(formulario.find("#campoPrimerApellidoPersona").val() == ""){
            formulario.find("#campoPrimerApellidoPersona").addClass("textFieldBordeRojo");
            formulario.find("#campoPrimerApellidoPersona").addClass("campo_obligatorio");
            pestanas.find(".ui-tabs-nav li a:eq(1)").trigger("click");     
            formulario.find("#campoPrimerApellidoPersona").focus();       
            
        } else if(formulario.find("#campoCelularPersona").val() == ""){
            formulario.find("#campoCelularPersona").addClass("textFieldBordeRojo");
            formulario.find("#campoCelularPersona").addClass("campo_obligatorio");
            pestanas.find(".ui-tabs-nav li a:eq(1)").trigger("click");     
            formulario.find("#campoCelularPersona").focus();       
            
        } else{
            
            $.ajax({
                type:"POST",
                url:"/ajax/inicio/verificar",
                dataType:"json",
                data: {
                    tabla: "lista_ciudades", columna: "cadena", valor: $("#ciudadSede").val()
                },
                success:function(respuesta){
                    if(!respuesta.existeItem){
                        $("#ciudadSede").val("");
                        $("#ciudadSede").addClass("textFieldBordeRojo");
                        $("#ciudadSede").addClass("autocompletable_obligatorio");
                        $(".ui-tabs-nav li a:first").trigger("click");
                        $("#ciudadSede").focus();
                        return false;
                    }else{

                        setTimeout(function(){
                                $("#indicadorEspera").css("display","block");
                                $("#BoxOverlay").css("display","block");
                                
                                destino = $(formulario).attr("action");
                                $(formulario).ajaxForm();
                                $(formulario).ajaxSubmit({
                                    dataType:"json",
                                    success:function(respuesta){
                                        procesaRespuesta(respuesta);
                                    }
                                });
                        }, 200);            

                        return true;
                    }

                }
            });

            
        }
        
            
    });