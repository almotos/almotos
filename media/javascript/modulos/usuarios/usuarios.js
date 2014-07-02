/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


$(document).ready(function(){
    
    $("a[rel^='prettyPhoto']").prettyPhoto(); 
    
    

    $("#full").click(function(){
        $("#contenedorAyudaUsuario").slideUp("slow");
    });

        
    $("#campoAgregarUsuario").live("keyup", function(){
        destino= "/ajax/usuarios/verificarUsuario";
        var usuario = $(this).val();
        $.ajax({
            type:"POST",
            url:destino,
            data: {
                usuario: usuario
            },
            dataType:"json",
            success:procesaRespuestaUsuarios
        });
        return false;
    });
    
    
    
    
    //Calendario	
    $('#eventsCalendar').fullCalendar({
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,agendaWeek,agendaDay'
        },
        editable: true,
        events: '/ajax/inicio/cargarFechasEventos',
        dayNames:['Domingo', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado'],
        dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab'],
        monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
        monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
        buttonText: {
            today:    'Hoy',
            month:    'Mes',
            week:     'Semana',
            day:      'dia'
        }
        
    //        ,    
    //        eventMouseover: function(event, jsEvent, view) {
    //
    //            alert('mouseover on the slot: ' + event.title);
    //
    //
    //        },
    //        eventMouseout: function(event, jsEvent, view) {
    //
    //            alert('mouseout on the entire day: ' + event.url);
    //
    //
    //        }
    });     
    
  
    
    $(".enlaceAjaxEvento").live("click", function(){
        
        var destino  = $(this).attr("href");
        
        var destino1 = destino.split(']');
        
        destino = destino1[0];
        var id  = destino1[1];
        
        $.ajax({
            type:"POST",
            url:destino,
            dataType:"json",
            data:{
                id : id
            },
            success:procesaRespuesta
        });
        return false;        
    });
    
    
    
    setTimeout(function(){
        $("#eventsCalendar").fadeIn("slow");
        $("#eventsCalendar").fullCalendar("render");
        
    }, 1000); 
    
    
    
    //Evento doble click sobre un dia del calendario
    $(".fc-day").live("dblclick", function(){
        
        $("#BoxOverlayTransparente").css("display","block");
        
        var fecha   = $(this).attr("data-date");
        var destino = '/ajax/eventos/add';
        
        $.ajax({
            type:"POST",
            url:destino,
            dataType:"json",
            data:{
                fecha : fecha
            },
            success:procesaRespuesta
        });
        return false;         
        
    });   
    


    $("a[href=#barraPersonal_4]").click(function(){
        $("#eventsCalendar").fullCalendar("render");    
    });
    
    
    
       
    
});


function procesaRespuestaUsuarios(respuesta){
    
    if(respuesta.accion == "verificarUsuario"){
        if(respuesta.existeUsuario){
            $("#textoExisteUsuario").removeClass("oculto");
        }else{
            $("#textoExisteUsuario").addClass("oculto");
        }       
    }

    
    
}