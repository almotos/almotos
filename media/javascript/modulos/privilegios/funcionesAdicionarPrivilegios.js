var arbol = $(".vistaArbol");

if(arbol.length){
    arbol.checkboxTree ({
        onCheck: {
            ancestors: 'check',
            descendants: 'uncheck'
        },

        onUncheck: {
            node: 'collapse',
            ancestors: 'check',
            descendants: 'uncheck'
        },

        initializeChecked: 'expanded',
        initializeUnchecked: 'collapsed'
    });
            
}

//efecto de que los permisos carguen ocultos mientras el plugin checkbox tree arma el DOM
setTimeout(function(){
    $(".imagen_cargando").fadeOut("fast", function(){
        arbol.fadeIn("fast");
    });
}, 1800);

/**
 * Funcion encargada de cargar las sedes (en las cuales el usuario seleccionado no tiene permisos) 
 * cada vez que se selecciona un usuario
 */
$("#listaUsuarios").on("change", function(){
   var idUsuario = $(this).val(); 
   var destino   = "/ajax/privilegios/cargarSedes";
   var options   = "";
   
   if (idUsuario != ""){
       $.ajax({
           type : "POST",
           data : {idUsuario : idUsuario},
            url:destino,
            dataType:"json",
            success: function(respuesta){
               for(var sede in respuesta.sedes){
                   options += "<option value='"+sede+"'>"+respuesta.sedes[sede]+"</option>";
               }
               
               $("#listaSedesEmpresa").html(options);
                                             
            }
            
       });
       
   } else {
       $("#listaSedesEmpresa").html(options);
       
   }

});
