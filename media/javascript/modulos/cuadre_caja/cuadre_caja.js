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
    
var Router = Backbone.Router.extend({
    routes: {
        "": "cuadre_caja"
    }
});

var router = new Router();

router.on('route:cuadre_caja', function(){
    $("#idSelectorTiempos").chosen();
    console.log("que maricada, en serio que necesito aprender rapido la mecanografia");
});
    
    
var Consulta = Backbone.Model.extend({
    defaults: {
        ultimos: '',
        rango_personalizado: false,
        fecha_inicial: '',
        fecha_final: '',
        todas_cajas: true,
        caja: ''
        
    },
});


var ParametrosConsulta = Backbone.View.extend({
    el: "#wrapper",
    events: {
        "click #rangoPersonalizado" : "mostrarContenedorRangoFechas",
        "click #filtrarTodasCajas"  : "filtrarTodasCajas",
        "change #selectorSedes"     : "actualizarSelectorCaja"
    },
    
    initialize: function(){
        this.model = new Consulta();
        this.model.on('change', this.prepararCargaAjax);
    },
            
    render: function() {
        return this;
    },
            
    prepararCargaAjax: function(){
        console.log("prepararCargaAjax");
    },
            
    /**
     * función que muestra los campos para seleccionar la bodega para que el 
     * kardex sea filtrado por bodega tambien
     */
    mostrarContenedorRangoFechas: function(event){
        var obj = $(event.target);
        
        if(obj.is(":checked")){
            $("#contenedorRangoFechas").fadeIn("fast");
            
        } else {
            $("#contenedorRangoFechas").fadeOut("fast");
            
        }     
        
    },
            
    /**
     * función que muestra los campos para seleccionar la bodega para que el 
     * kardex sea filtrado por bodega tambien
     */
     filtrarTodasCajas: function(event){
        var obj = $(event.target);
        
        var contenedorSelectorCajas = $("#contenedorSelectorCajas");
        
        if(obj.is(":checked")){
            contenedorSelectorCajas.fadeIn("fast");
            
        } else {
            contenedorSelectorCajas.fadeOut("fast");
            
        }
        
        /**
         * Función que agrega el plugin chosen ver 
         **/
        contenedorSelectorCajas.find("select").chosen({no_results_text: "Oops, sin resultados!"});      
        
    },
            
    /*
     *Codigos que agregan via ajax los options con las cajas
     *segun la sede que se seleccione
     **/
     actualizarSelectorCaja: function(event){
        var obj = $(event.target);
        
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
                    var selector = obj.parents("#contenedorSelectorCajas").find("#selectorCajas");
                        selector.html(respuesta.contenido);
                        selector.trigger("chosen:updated");
                }

            });          
           
        }   
    }
            
});


var Reporte = Backbone.View.extend({
    el: "#contenedorRespuesta",
    events: {
        
    },
    
    initialize: function() {
        
        this.render();
    },
            
    render: function(){
        var plantilla = "Esto debe ir dentro del div contenedor Respuesta";
        
        $(this.el).html(plantilla);
        
        return this;
        
    }
});


var formulario = new ParametrosConsulta();

var vista = new Reporte();

Backbone.history.start();
