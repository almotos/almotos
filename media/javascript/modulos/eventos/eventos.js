$(document).ready(function(){

    //Calendario	
    $('#eventsCalendar').fullCalendar({
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,agendaWeek,agendaDay'
        },
        editable: true,
        events: '/ajax/home/cargarFechasEventos'
    });        
    
    
    
    setTimeout(function(){
        $("#eventsCalendar").fadeIn("slow");
        $("#eventsCalendar").fullCalendar("render");
        
    }, 1000);
    
        
        
});



    
