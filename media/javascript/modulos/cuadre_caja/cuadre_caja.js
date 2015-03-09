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
        rango_personalizado: 0,
        fecha_inicial: '',
        fecha_final: '',
        todas_cajas: 1,
        caja: [],
        tipo: 'compras',
        filtro_usuarios: 0,
        usuarios: [],
        tipo_reporte: "lista_facturas",
        fields: ['idFactura','tercero','fechaFactura','total']
    }
});

var ParametrosConsulta = Backbone.View.extend({
    el: "#wrapper",
    events: {
        "click #rangoPersonalizado"         : "mostrarContenedorRangoFechas",
        "change #idSelectorTiempos"         : "setTiempos",
        "change #fechaInicioCuadre"         : "setFechaInicio",
        "change #fechaFinCuadre"            : "setFechaFin",        
        //filtros por caja
        "click #filtrarTodasCajas"          : "filtrarTodasCajas",
        "change #selectorSedes"             : "actualizarSelectorCaja",
        "change #selectorCajas"             : "setCaja",
        //filtro por tipo
        "change .selectorTipo"              : "setTipo",//determina si es de compras o ventas
        "change .selectorTipoReporte"       : "setTipoReporte",//si es el listado de facturas, el sumarizado, el promedio, etc.
        //filtros por usuarios
        "change #selectorUsuarios"          : "setUsuario",
        "click #filtroUsuarios"             : "filtrarUsuarios",
        //escoger que campos consultar
        "click #filtroFields"               : "filtrarFields",
        "change #selectorFields"            : "setFields",
        //boton consultar
        "click #botonConsultarCuadreCaja"   : "consultarCuadreCaja",
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
            
    setUsuario: function(event){
        var obj = $(event.target);
        this.model.set('usuarios', obj.val());
    },       
            
    setFields: function(event){
        var obj = $(event.target);
        this.model.set('fields', obj.val());
    },              
            
    setTiempos: function(event){
        var obj = $(event.target);
        this.model.set('ultimos', obj.val());
    },     

    setTipo: function(event){
        var obj = $(event.target);
        this.model.set('tipo', obj.val());
    },   
            
    setTipoReporte: function(event){
        var obj = $(event.target);
        this.model.set('tipo_reporte', obj.val());

        if(obj.val() == "lista_facturas"){
            $("#wrapContenedorFields").fadeIn("fast");

        } else {
            $("#wrapContenedorFields").fadeOut("fast");      

        }        
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
            this.model.set('rango_personalizado', 1);
            this.model.set('fecha_inicial', fechaI.val() );
            this.model.set('fecha_final', fechaF.val() );

        } else {
            $("#contenedorRangoFechas").fadeOut("fast");
            this.model.set('rango_personalizado', 0);
            this.model.set('fecha_inicial', '' );
            this.model.set('fecha_final', '' );         

        }

    },
            
    filtrarTodasCajas: function(event){
        var obj = $(event.target);
        
        var contenedorSelectorCajas = $("#contenedorSelectorCajas");
        var caja = contenedorSelectorCajas.find("#selectorCajas");
        
        if(obj.is(":checked")){
            contenedorSelectorCajas.fadeIn("fast");
            this.model.set('todas_cajas' , 0);
            this.model.set('caja' , caja.val());
            
        } else {
            contenedorSelectorCajas.fadeOut("fast");
            this.model.set('todas_cajas' , 1);
            this.model.set('caja' , []);
        }
        /**
         * Función que agrega el plugin chosen ver 
         **/
        contenedorSelectorCajas.find("select").chosen({no_results_text: "Oops, sin resultados!"});      

    },
            
    filtrarUsuarios: function(event){
        var obj = $(event.target);
        
        var contenedorUsuarios = $("#contenedorUsuarios");
        var usuarios = contenedorUsuarios.find("#selectorUsuarios");
        
        if(obj.is(":checked")){
            contenedorUsuarios.fadeIn("fast");
            this.model.set('filtro_usuarios' , 1);
            this.model.set('usuarios' , usuarios.val());
            
        } else {
            contenedorUsuarios.fadeOut("fast");
            this.model.set('filtro_usuarios' , 0);
            this.model.set('usuarios' , []);
        }
        /**
         * Función que agrega el plugin chosen ver 
         **/
        contenedorUsuarios.find("select").chosen({no_results_text: "Oops, sin resultados!"});      

    },      
            
    filtrarFields: function(event){
        var obj = $(event.target);
        
        var contenedorFields = $("#contenedorFields");
        var fields = contenedorFields.find("#selectorFields");
        
        if(obj.is(":checked")){
            contenedorFields.fadeIn("fast");
            this.model.set('fields' , fields.val());
            
        } else {
            contenedorFields.fadeOut("fast");
        }
        /**
         * Función que agrega el plugin chosen ver 
         **/
        contenedorFields.find("select").chosen({no_results_text: "Oops, sin resultados!"});     

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
