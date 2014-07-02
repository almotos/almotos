
    var modulo = $("#nombreModulo").val();
    var isCtrl = false;
    var shift  = false;
    var estanBuscando = false;//variable que determina si el campo del buscador de los modulos tiene el foco
    var seleccionarCantReg = false;//variable que determina si el campo de seleccionar la cantidad de registros tiene el foco
    
    $(document).bind("keyup", function(e){
        if(e.which == 17) isCtrl=false;
        
        if(e.shiftKey==1) shift=false;
    });
    
    
    $("#campoBuscador").focus(function(){//con el evento focus pongo la variable estanBuscando en true
        estanBuscando = true;
    });
    $("#campoBuscador").blur(function(){//y aqui en false
        estanBuscando = false;
    });   
    
    $("#campoNumeroRegistros").focus(function(){//con el evento focus pongo la variable seleccionarCantReg en true
        seleccionarCantReg = true;
    });
    $("#campoNumeroRegistros").blur(function(){//y aqui en false
        seleccionarCantReg = false;
    });     




    $(document).bind("keydown", function(e){
        
        if(e.which == 17) {
            isCtrl=true;
        }
        if(e.shiftKey==1) shift = true;
        
        
        /**
             * Funciones que se ejecutan cuando escribo en el campo de seleccionar
             * la cantidad de registros y presiono ENTER
             **/
        if(e.which == 13  && seleccionarCantReg == true){//CTRL + Enter
            var cantReg = $("#campoNumeroRegistros").val();
            var destino = $("#campoNumeroRegistros").attr("ruta"); 
    
            $("#BoxOverlay").css("display","block");

            //si hay una condicion global para realizar la busqueda
            var condicion = $("#condicionGlobal").val();
            //si hay un ordenamiento global para realizar la busqueda
            var ordenamiento = $("#ordenGlobal").val();
        
            var ord    = "";
            var nomOrd = "";
        
            if(ordenamiento != ""){
                ordenamiento = ordenamiento.split("|");
                ord = ordenamiento[0];
                nomOrd = ordenamiento[1];
            
            }

            if(condicion == ""){
                condicion = '';
            }

            $.ajax({

                type:"POST",
                url:destino,
                data: {
                    orden:ord, 
                    nombreOrden: nomOrd, 
                    consultaGlobal : condicion,
                    cantidadRegistros: cantReg
                },
                dataType:"json",
                success:procesaRespuesta
            });
            
            $(".botonPaginacion").each(function(){ //Añadir a los botones de páginacion la cantidad de registros que se van a utilizar
                $(this).attr("cantidad_registros" , cantReg);
            });
        }             
        
});

    /**
    * Estilos y efectos del campo buscador de los modulos
    **/
    $("#campoNumeroRegistros").focus();
    $("#campoBuscador").addClass("fondoBuscador");

    $("#campoBuscador").focus(function(){
        $("#campoBuscador").removeClass("fondoBuscador");
        $("#botonBuscador").slideDown("fast");
        $("#campoBuscador").addClass("negrita");
    });

    $("#campoBuscador").blur(function(){
        setTimeout(function(){
            var texto = $("#campoBuscador").val();
            if(texto == ""){
                $("#campoBuscador").addClass("fondoBuscador");
            }       
            $("#botonBuscador").slideUp("fast");
            $("#campoBuscador").removeClass("negrita");
        }, 100);

    });
    

    
    $("#campoBuscador").bind("keyup", function(e){
        
        $ventana = $(this).parents(".ui-dialog");
        
        if(e.which == 13 && $ventana){
            $("#botonBuscador").trigger("click");
            return false;
        }  
    });   
    
    $(".ui-dialog #contenedorTablaRegistros #tablaRegistros .cabeceraTabla .cantidad").html('Cantidad');
    
//    //call the addFields function
//    //agregarCamposCantidad();
//    
//    $(".ui-dialog #contenedorTablaRegistros").bind("click", function(){
//        //agregarCamposCantidad();
//    });
//    
//    /**
//     * codigo para agregar la columna cantidad en la tabla de registros
//     */
//    function agregarCamposCantidad() {
//        $cabeceraCantidad = $('<th class="columnaTabla th-cantidad-articulo" id="cantidad-articulo"><p class="centrado">Cantidad</p></th>');
//
//        $(".ui-dialog #contenedorTablaRegistros #tablaRegistros .cabeceraTabla").append($cabeceraCantidad);
//
//        $filas = $(".ui-dialog #contenedorTablaRegistros #tablaRegistros .filasTabla");
//
//        $.each($filas, function(){
//            $fila = $(this);
//            $fila.append('<td class="td-cantidad-articulo"><input type="text" class="campo-cantidad-articulo" value="2" maxlength="20" size="5"></td>');
//        });
//    }
