$(document).ready(function(){
 
    /**
     * funcion para verificar si esta visible la tablaEditarMasContactos, en caso de estar visible
     * cambia el valor de un campo oculto, para que en el ajax se arme la respuesta como debe ser
     **/
    $("#botonAdicionarContactoProveedor").bind("click", function(e){
        
        if($("#tablaEditarMasContactos").is(":visible")){
            $(this).find("input[name=tablaEditarVisible]").val("1");
        } else {
            $(this).find("input[name=tablaEditarVisible]").val("0");
        }
        
    });
    
    
    /**
     * funcion para verificar si esta visible la tablaEditarMasSedes, en caso de estar visible
     * cambia el valor de un campo oculto, para que en el ajax se arme la respuesta como debe ser
     **/
    $("#botonAdicionarSedeProveedor").bind("click", function(e){
        
        if($("#tablaEditarMasSedes").is(":visible")){
            $(this).find("input[name=tablaEditarVisible]").val("1");
        } else {
            $(this).find("input[name=tablaEditarVisible]").val("0");
        }
        
    });    
    
    
    
    
    /**
     * funcion para verificar si esta visible la tablaEditarMasSedes, en caso de estar visible
     * cambia el valor de un campo oculto, para que en el ajax se arme la respuesta como debe ser
     **/
    $("#botonAdicionarCuentaProveedor").bind("click", function(e){
        
        if($("#tablaEditarCuentas").is(":visible")){
            $(this).find("input[name=tablaEditarVisible]").val("1");
        } else {
            $(this).find("input[name=tablaEditarVisible]").val("0");
        }
        
    });    
    
    
    /**
     * funcion que muestra o esconde los campos de nombre comercial y nit dependiendo si es persona juridica o natural
     **/
//    $("#listaTipoPersona").live("change",function(){
//        var valor = $("#listaTipoPersona option:selected").html();
//        
//        if(valor == "Juridica"){
//            $("#textoNombreProveedor").fadeIn("fast");
//            $("#nombreProveedor").fadeIn("fast");
//            $("#textoIdProveedor").fadeIn("fast");
//            $("#idProveedor").fadeIn("fast");
//        }else{
//            $("#textoNombreProveedor").fadeOut("fast");
//            $("#nombreProveedor").fadeOut("fast");
//            $("#textoIdProveedor").fadeOut("fast");
//            $("#idProveedor").fadeOut("fast");            
//            
//        }
//       
//    });        
    
     
});


    /**
    * Funcion que se encarga de verificar si se hace click en autoretenedor y 
    * desmarca los checks de retefuente y reteica
    */
    $("#checkAutoretenedor").live("click", function(){
        if(this.checked == true){
            $("#checkRetefuente").attr("checked", false);
            $("#checkReteica").attr("checked", false);
            $("#checkRetecre").attr("checked", false);
        }else{
            $("#checkRetefuente").attr("checked", true);
            $("#checkReteica").attr("checked", true);
            $("#checkRetecre").attr("checked", true);
        }
        
    });
    
    
    /**
    * Funcion que se encarga de verificar si se hace click en cualquiera de los tributos y 
    * desmarca el check de autoretenedor
    */
    $(".check-tributo").live("click", function(){
        if(this.checked == true){
            $("#checkAutoretenedor").attr("checked", false);
            
        }        
    });    