/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

function consultarCuadreCaja(obj){
    var destino         = '/ajax/cuadre_caja/consultarCuadreCaja';
    var fechaInicio     = $("#fechaInicioCuadre").val();
    var fechaFin        = $("#fechaFinCuadre").val();
    
    if (fechaInicio == "" || fechaFin == "") {
        Sexy.alert("Debe seleccionar un rango de fechas para consultar el cuadre de caja");
        return;
    }
    
    var caja = '0';
    //verificar si se quiere filtrar también por caja
    if ($("#filtrarTodasCajas").is(":checked")) {
        caja = obj.parents(".contenedorCuadreCaja").find("#selectorCajas").val();
    }
    
    $("#BoxOverlay").css("display","block");
    $("#indicadorEspera").css("display","block");

    $.ajax({
        type:"POST",
        url:destino,
        dataType:"json",
        data: {
            fechaInicio: fechaInicio,
            fechaFin: fechaFin,
            idCaja : caja
        },
        success:procesaRespuesta
    });
}

    /*
     *Codigos que agregan via ajax los options con las cajas
     *segun la sede que se seleccione
     **/
    function seleccionarCajas(obj){
        var sede = obj.val();        
        if(sede != ''){
            $.ajax({
                type:"POST",
                url:"/ajax/cajas/escogerCaja",
                data: {
                    idSede : sede
                },
                dataType:"json",
                success:function (respuesta){
                    obj.parents("#contenedorSelectorCajas").find("#selectorCajas").html(respuesta.contenido);
                }

            });          
           
        }   
    }
    
    /**
     * función que muestra los campos para seleccionar la bodega para que el 
     * kardex sea filtrado por bodega tambien
     */
    function filtrarTodasCajas(obj){
        if(obj.is(":checked")){
            $("#contenedorSelectorCajas").fadeIn("fast");
            
        } else {
            $("#contenedorSelectorCajas").fadeOut("fast");
            
        }
        
        /**
         * Función que agrega el plugin chosen ver 
         **/
        $(".selectChosen").chosen({no_results_text: "Oops, sin resultados!"});      
        
    }
    
    
    /**
     * función que muestra los campos para seleccionar la bodega para que el 
     * kardex sea filtrado por bodega tambien
     */
    function mostrarContenedorRangoFechas(obj){
        if(obj.is(":checked")){
            $("#contenedorRangoFechas").fadeIn("fast");
            
        } else {
            $("#contenedorRangoFechas").fadeOut("fast");
            
        }
        
        /**
         * Función que agrega el plugin chosen ver 
         **/
        $("#contenedorRangoFechas .selectChosen").chosen({no_results_text: "Oops, sin resultados!"});      
        
    }    

