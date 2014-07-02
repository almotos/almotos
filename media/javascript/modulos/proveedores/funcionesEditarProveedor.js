/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
var contador = 0;
var ejecutar = "";
var proceder = "";

    
    $("#botonModificarProveedores").bind("click", function(e){//click en el boton del formulario
        e.preventDefault();
        var pestanas    = $("#pestanasModificar");
        var formulario  = $(this).parents("form");
        var idPestana = '';
        
        if(formulario.find("#nombreProveedor").val() == ""){
            formulario.find("#nombreProveedor").addClass("textFieldBordeRojo");
            formulario.find("#nombreProveedor").addClass("campo_obligatorio");
            formulario.find("#nombreProveedor").focus();
            idPestana = formulario.find("#nombreProveedor").parents('div.ui-tabs-panel').attr('id');
            pestanas.find(".ui-tabs-nav li a[href='#"+idPestana+"']").trigger("click"); 
            
        } else if(formulario.find("#idProveedor").val() == ""){
            formulario.find("#idProveedor").addClass("textFieldBordeRojo");
            formulario.find("#idProveedor").addClass("campo_obligatorio");
            idPestana = formulario.find("#idProveedor").parents('div.ui-tabs-panel').attr('id');
            pestanas.find(".ui-tabs-nav li a[href='#"+idPestana+"']").trigger("click"); 
            formulario.find("#idProveedor").focus();
            
        } else if(formulario.find("#ciudadSede").val() == ""){
            formulario.find("#ciudadSede").addClass("textFieldBordeRojo");
            formulario.find("#ciudadSede").addClass("campo_obligatorio");
            idPestana = formulario.find("#ciudadSede").parents('div.ui-tabs-panel').attr('id');
            pestanas.find(".ui-tabs-nav li a[href='#"+idPestana+"']").trigger("click"); 
            formulario.find("#ciudadSede").focus();
            
        } else if(formulario.find("#telefonoSede").val() == ""){
            formulario.find("#telefonoSede").addClass("textFieldBordeRojo");
            formulario.find("#telefonoSede").addClass("campo_obligatorio");
            idPestana = formulario.find("#telefonoSede").parents('div.ui-tabs-panel').attr('id');
            pestanas.find(".ui-tabs-nav li a[href='#"+idPestana+"']").trigger("click");  
            formulario.find("#telefonoSede").focus();        
            
        } else if(formulario.find("#direccionSede").val() == ""){
            formulario.find("#direccionSede").addClass("textFieldBordeRojo");
            formulario.find("#direccionSede").addClass("campo_obligatorio");
            idPestana = formulario.find("#direccionSede").parents('div.ui-tabs-panel').attr('id');
            pestanas.find(".ui-tabs-nav li a[href='#"+idPestana+"']").trigger("click"); 
            formulario.find("#direccionSede").focus();          
            
        } else if(formulario.find("#campoDocumentoPersona").val() == ""){
            formulario.find("#campoDocumentoPersona").addClass("textFieldBordeRojo");
            formulario.find("#campoDocumentoPersona").addClass("campo_obligatorio");
            idPestana = formulario.find("#campoDocumentoPersona").parents('div.ui-tabs-panel').attr('id');
            pestanas.find(".ui-tabs-nav li a[href='#"+idPestana+"']").trigger("click");      
            formulario.find("#campoDocumentoPersona").focus();      
            
        } else if(formulario.find("#campoPrimerNombrePersona").val() == ""){
            formulario.find("#campoPrimerNombrePersona").addClass("textFieldBordeRojo");
            formulario.find("#campoPrimerNombrePersona").addClass("campo_obligatorio");
            idPestana = formulario.find("#campoPrimerNombrePersona").parents('div.ui-tabs-panel').attr('id');
            pestanas.find(".ui-tabs-nav li a[href='#"+idPestana+"']").trigger("click");     
            formulario.find("#campoPrimerNombrePersona").focus();       
            
        } else if(formulario.find("#campoPrimerApellidoPersona").val() == ""){
            formulario.find("#campoPrimerApellidoPersona").addClass("textFieldBordeRojo");
            formulario.find("#campoPrimerApellidoPersona").addClass("campo_obligatorio");
            idPestana = formulario.find("#campoPrimerApellidoPersona").parents('div.ui-tabs-panel').attr('id');
            
            pestanas.find(".ui-tabs-nav li a[href='#"+idPestana+"']").trigger("click");   
            formulario.find("#campoPrimerApellidoPersona").focus();       
            
        } else if(formulario.find("#campoCelularPersona").val() == ""){
            formulario.find("#campoCelularPersona").addClass("textFieldBordeRojo");
            formulario.find("#campoCelularPersona").addClass("campo_obligatorio");
            
            idPestana = formulario.find("#campoCelularPersona").parents('div.ui-tabs-panel').attr('id');
            pestanas.find(".ui-tabs-nav li a[href='#"+idPestana+"']").trigger("click");
            formulario.find("#campoCelularPersona").focus();        
            
        } else if(formulario.find("#campoActividadEconomica").val() == ""){
            formulario.find("#campoActividadEconomica").addClass("textFieldBordeRojo");
            formulario.find("#campoActividadEconomica").addClass("campo_obligatorio");
  
            idPestana = formulario.find("#campoActividadEconomica").parents('div.ui-tabs-panel').attr('id');
            pestanas.find(".ui-tabs-nav li a[href='#"+idPestana+"']").trigger("click");
            formulario.find("#campoActividadEconomica").focus();       
            
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