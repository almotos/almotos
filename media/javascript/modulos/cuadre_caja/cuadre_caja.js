/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

var Router = Backbone.Router.extend({
    routes: {
        "": "cuadre_caja"
    }
});

var router = new Router();

router.on('route:cuadre_caja', function(){
    $("#idSelectorTiempos").chosen();
});
    
    
var Consulta = Backbone.Model.extend({
    defaults: {
        ultimos: 'dia',
        rango_personalizado: false,
        fecha_inicial: '',
        fecha_final: '',
        todas_cajas: true,
        caja: '',
        tipo: 'compras',
        
    },
});

var ParametrosConsulta = Backbone.View.extend({
    el: "#wrapper",
    events: {
        "click #rangoPersonalizado"         : "mostrarContenedorRangoFechas",
        "click #filtrarTodasCajas"          : "filtrarTodasCajas",
        "change #selectorSedes"             : "actualizarSelectorCaja",
        "change #idSelectorTiempos"         : "setTiempos",
        "change #selectorCajas"             : "setCaja",
        "click #botonConsultarCuadreCaja"   : "consultarCuadreCaja",
        "change #fechaInicioCuadre"         : "setFechaInicio",
        "change #fechaFinCuadre"            : "setFechaFin",
        "change .selectorTipo"              : "setTipo"
    },
    
    initialize: function(){
        this.model = new Consulta();
    },
            
    render: function() {
        return this;
    },       
            
    setFechaInicio: function(event){
        var obj = $(event.target);
        this.model.set('fecha_inicial', obj.val() );
    },
            
    setFechaFin: function(event){
        var obj = $(event.target);
        this.model.set('fecha_final',obj.val() );
    },       
            
    setCaja: function(event){
        var obj = $(event.target);
        this.model.set('caja', obj.val());
    },       
            
    setTiempos: function(event){
        var obj = $(event.target);
        this.model.set('ultimos', obj.val());
    },     

    setTipo: function(event){
        var obj = $(event.target);
        this.model.set('tipo', obj.val());
    },            

    /**
     * función que muestra los campos para seleccionar la bodega para que el 
     * kardex sea filtrado por bodega tambien
     */
    mostrarContenedorRangoFechas: function(event){
        var obj = $(event.target);

        var contenedorRangoFechas = $("#contenedorRangoFechas");
        var fechaI = contenedorRangoFechas.find("#fechaInicioCuadre");     
        var fechaF = contenedorRangoFechas.find("#fechaFinCuadre"); 

        if(obj.is(":checked")){
            $("#contenedorRangoFechas").fadeIn("fast");
            this.model.set('rango_personalizado', true);
            this.model.set('fecha_inicial', fechaI.val() );
            this.model.set('fecha_final', fechaF.val() );

        } else {
            $("#contenedorRangoFechas").fadeOut("fast");
            this.model.set('rango_personalizado', false);
            this.model.set('fecha_inicial', '' );
            this.model.set('fecha_final', '' );         

        }

    },

    /**
     * función que muestra los campos para seleccionar la bodega para que el 
     * kardex sea filtrado por bodega tambien
     */
     filtrarTodasCajas: function(event){
        var obj = $(event.target);
        
        var contenedorSelectorCajas = $("#contenedorSelectorCajas");
        var caja = contenedorSelectorCajas.find("#selectorCajas");
        
        if(obj.is(":checked")){
            contenedorSelectorCajas.fadeIn("fast");
            this.model.set('todas_cajas' , false);
            this.model.set('caja' , caja.val());
            
        } else {
            contenedorSelectorCajas.fadeOut("fast");
            this.model.set('todas_cajas' , true);
            this.model.set('caja' , "");
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
                        selector.trigger("liszt:updated");
                }

            });          
           
        }   
    },
    
    consultarCuadreCaja : function (){
        var destino                 = '/ajax/cuadre_caja/consultarCuadreCaja';

        $("#BoxOverlay").css("display","block");
        $("#indicadorEspera").css("display","block");

        $.ajax({
            type:"POST",
            url:destino,
            dataType:"json",
            data: {
                datos: this.model.attributes
            },
            success:this.renderDatos
        });

    },
            
    renderDatos: function(respuesta){
        basicasProcesaRespuesta(respuesta);
        
        var opcionesTabla = JSON.parse(respuesta.contenido);
        
        var tabla = renderTemplate('tabla', opcionesTabla);
        
        $(respuesta.destino).html(tabla);
        
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
