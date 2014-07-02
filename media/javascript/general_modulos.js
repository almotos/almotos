/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


$(document).ready(function(){
    
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
        
//        console.log(e.which);
//        $("#calc").find(".calc-text").html(e.which)
     
        
        /* eventos de la calculadora*/
            
        if($("#calc").is(":visible")){      
            e.preventDefault();
            if(e.which == 49 || e.which == 97){
                $("#calc").find('.calc-button-wrapper:has(div:contains("1"))').trigger("click");
            } else if(e.which == 50 || e.which == 98){
                $("#calc").find('.calc-button-wrapper:has(div:contains("2"))').trigger("click");
            } else if(e.which == 51 || e.which == 99){
                $("#calc").find('.calc-button-wrapper:has(div:contains("3"))').trigger("click");
            } else if(e.which == 52 || e.which == 100){
                $("#calc").find('.calc-button-wrapper:has(div:contains("4"))').trigger("click");
            } else if(e.which == 53 || e.which == 101){
                $("#calc").find('.calc-button-wrapper:has(div:contains("5"))').trigger("click");
            } else if(e.which == 54 || e.which == 102){
                $("#calc").find('.calc-button-wrapper:has(div:contains("6"))').trigger("click");
            } else if(e.which == 55 || e.which == 103){
                $("#calc").find('.calc-button-wrapper:has(div:contains("7"))').trigger("click");
            } else if(e.which == 56 || e.which == 104){
                $("#calc").find('.calc-button-wrapper:has(div:contains("8"))').trigger("click");
            } else if(e.which == 57 || e.which == 105){
                $("#calc").find('.calc-button-wrapper:has(div:contains("9"))').trigger("click");
            } else if(e.which == 48 || e.which == 96){
                $("#calc").find('.calc-button-wrapper:has(div:contains("0"))').trigger("click");
            }  
                
            if( (shift == true && e.which == 56 ) || (shift == true && e.which == 191 ) || e.which == 61|| e.which == 109 ||  e.which == 107 ||  e.which == 19 || e.which == 111 || e.which == 106){ // * ,  +,  - , 4/
                e.preventDefault();
                   
                if(e.which == 56 || e.which == 106){
                    $("#calc").find('.calc-button-wrapper:has(div:contains("*"))').trigger("click");
                } else if(e.which == 61 || e.which == 107){
                    $("#calc").find('.calc-button-wrapper:has(div:contains("+"))').trigger("click");
                } else if(e.which == 109 || e.which == 19){
                    $("#calc").find('.calc-button-wrapper:has(div:contains("-"))').trigger("click");
                } else if(e.which == 191 || e.which == 111){
                    $("#calc").find('.calc-button-wrapper:has(div:contains("/"))').trigger("click");
                }
     
            } 
            
            if(e.which == 46 || e.which == 27 || e.which == 8){ //
                $("#calc").find('.calc-button-wrapper').find('div:contains("C")').trigger("click");
                
            }            
                
            if(e.which == 61 || e.which == 13){ //
                $("#calc").find('.calc-button-wrapper').find('div:contains("=")').trigger("click");
                
            }
                
                
        }


        if(e.which == 65 && isCtrl == true) {//Ctrl + A 
            $("#botonAdicionar"+modulo).find(".botonTextoIcono").trigger("click");
            return false;
        }
        
        if(e.which == 66 && isCtrl == true) {//Ctrl + B
            $("#botonBuscar"+modulo).find(".campoBuscador").focus();
            return false;
        }
        
        if(e.which == 113 && isCtrl == true) {//Ctrl + F2 ->mostrar calculadora
            if($("#calc").is(":visible")){
                $('#calc').hide();
            } else {
                $('#calc').show();
            }
                   
            return false;
        }

        
        //Codigo que abre la ventana modal para buscar modulos
        if(e.which == 112 && isCtrl == true) {//Ctrl + F1
            buscarModulo();
        }        
        
        
        if(e.which == 40 && isCtrl == true) {//Ctrl + flecha abajo  
            if($("#contenedorBotonDerecho").is(":visible")){
                
                var botonMarcado = $(".itemMenuBotonDerechoHover").next("div");
                if(botonMarcado.length <= 0){
                    $(".itemMenuBotonDerechoHover").removeClass("itemMenuBotonDerechoHover");
                    $("#contenedorBotonDerecho div:first").addClass("itemMenuBotonDerechoHover");
                } else {
                    $(".itemMenuBotonDerechoHover").removeClass("itemMenuBotonDerechoHover");
                    botonMarcado.addClass("itemMenuBotonDerechoHover");
            
                }            


            } else {                
                
                var sig = $(".fondoFilaRegistro").next("tr:not(.noSeleccionable)");
                if(sig.length <= 0){
                    $(".fondoFilaRegistro").removeClass("fondoFilaRegistro");
                    $("#tablaRegistros tr:first").next().addClass("fondoFilaRegistro");
                } else {
                    $(".fondoFilaRegistro").removeClass("fondoFilaRegistro");
                    sig.addClass("fondoFilaRegistro");                   
                    
                }   
                
            }
            return false;

        }
        
        if(e.which == 38 && isCtrl == true) {//Ctrl + flecha arriba
            if($("#contenedorBotonDerecho").is(":visible")){
                
                var botonMarcado2 = $(".itemMenuBotonDerechoHover").prev("div");
                if(botonMarcado2.length <= 0){
                    $(".itemMenuBotonDerechoHover").removeClass("itemMenuBotonDerechoHover");
                    $("#contenedorBotonDerecho div:last").addClass("itemMenuBotonDerechoHover");
                } else {
                    $(".itemMenuBotonDerechoHover").removeClass("itemMenuBotonDerechoHover");
                    botonMarcado2.addClass("itemMenuBotonDerechoHover");                   
                    
                }            
                                    
            }else{
                var pre = $(".fondoFilaRegistro").prev("tr:not(.noSeleccionable)");
                if(pre.length <= 0){
                    $(".fondoFilaRegistro").removeClass("fondoFilaRegistro");
                    $("#tablaRegistros tr:last").addClass("fondoFilaRegistro");
                } else {
                    $(".fondoFilaRegistro").removeClass("fondoFilaRegistro");
                    pre.addClass("fondoFilaRegistro")                   
                    
                }            
                
            }
            return false;  
        }
        
        
        
        
        
        if((!$(".ui-dialog").is(":visible") ) && e.which == 37 && isCtrl == true) {//Ctrl + flecha izquierda
            var atrPag = $(".botonAtrasPagina");
            if(atrPag.length > 0){
                $(".botonAtrasPagina").trigger("click");                
            }
            return false;
        }
        
        
        if( (!$(".ui-dialog").is(":visible") ) && e.which == 39 && isCtrl == true) {//Ctrl + flecha derecha
            var sigPag = $(".botonSiguientePagina");
            if(sigPag.length > 0){
                $(".botonSiguientePagina").trigger("click");                
            }
            return false;
        }

        if(e.which == 68 && isCtrl == true) {//codigo para el Ctrl + D abrir el menu del boton derecho
            
            if($("#contenedorBotonDerecho").is(":visible")){
                $("#contenedorBotonDerecho").slideUp("fast");
                return false;
            }else{
                var fila = $(".fondoFilaRegistro");
                var id = fila.attr("id");
                id = id.split("_");

                var pos = fila.position();
                var x = parseInt(pos.left) +200;
                var y = parseInt(pos.top); 

                $("#contenedorBotonDerecho").css("left", x+"px");
                $("#contenedorBotonDerecho").css("top", y+"px");

                $("#contenedorBotonDerecho").find("form").find("input[name='id']").val(id[1]);

                $("#contenedorBotonDerecho").slideDown("fast");
                return false;
            }
        }
        
        if(e.which == 72 && isCtrl == true) {//Ctrl + H         
            if($("#contenedorAyudaUsuario").is(":visible")){
                $("#contenedorAyudaUsuario").slideUp("slow");
                $("#BoxOverlay").css("display","none");
            }else{
                $("#BoxOverlay").css("display","block");
                $("#contenedorAyudaUsuario").slideDown("slow");
            }

            return false;
        }
        
        if($("#contenedorBotonDerecho").is(":visible") && e.which == 13 && isCtrl == true){
            $(".itemMenuBotonDerechoHover").find(".enviarAjax").trigger("click");
            
        }else if($("#botonBuscador").is(":visible") && e.which == 13  && estanBuscando == true){//CTRL + Enter
            $("#botonBuscador").trigger("click");
            
        } else if($(".ui-dialog button").is(":visible") && e.which == 13 && isCtrl == true){
            $(".ui-dialog button").trigger("click");
            
        }
        

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

        
        if($(".ui-dialog").not(":visible") && e.which == 82 && isCtrl == true){//CTRL + R
            e.preventDefault();
            $("#botonRestaurarConsulta").trigger("click");
        }
        
        
        //        if( (!$("#cuadroDialogo").is(":visible") ) && e.which == 39 && shift == true) {//Shift + flecha derecha
        //            
        //            var filaMenu = $(".sfHover");
        //            if(filaMenu.length <= 0){
        //                $("#mainMenu li:first").addClass("sfHover");
        //                $("#mainMenu li:first").trigger("mouseover");
        //            }
        //            var menuSig = $(".sfHover").next("li");
        //            if(menuSig.length > 0){
        //                $(".sfHover").removeClass("sfHover");
        //                menuSig.addClass("sfHover")
        //            }
        //            return false;
        //        }




        
        if( (!$("#cuadroDialogo").is(":visible") ) && e.which == 77 && shift == true) {//Shift + M
            $("#cerrarCabecera").trigger("click");
            
        }
        
        
    });
    
    
   
    $("#botonCerrarAyuda").click(function(){
        if($("#contenedorAyudaUsuario").is(":visible")){
            $("#contenedorAyudaUsuario").slideUp("fast");
            $("#BoxOverlay").slideUp("slow");
        }
    });
    
    
    
    
    /*
         *Codigo para el boton eliminar varios registros al tiempo
         **/

    $("#botonBorrarMasivo").live("click", function(e){
        e.preventDefault();
         
        var cantidad    = 0;
        var cadenaItems = '';
        
        $(".filaConsultada").each(function(){
            cantidad++;
            var id = $(this).attr("id");
            id = id.split('_');
            id = id[1];
            cadenaItems += id+",";
        });
        
        if(cantidad == 0){
            Sexy.alert("No ha seleccionado ningún articulo");
            return;
        }        
         
        var ruta = $("#botonBorrarMasivo").attr("ruta");
         
        $.ajax({
            type:"POST",
            url:ruta,
            dataType:"json",
            data: {
                cantidad: cantidad, 
                cadenaItems: cadenaItems
            },
            success:procesaRespuesta
        });
         
    });
    
    
    //codigo para agregar la clase a todos los item del menu boton derecho
    $("#contenedorBotonDerecho").children("div").addClass("itemMenuBotonDerecho");
    
    $.fn.calculator.hide = function(calc) {
        calc.slideUp("fast");
    };
		
    $('#calc').calculator({
        movable:true,
        resizable:true, 
        width:350,
        height:350,
        defaultOpen:false
    });
    
    $('#mostrarCalc').click(function(){
        if($("#calc").is(":visible")){
            $('#calc').hide();
        } else {
            $('#calc').show();
        }
    });
    
    
});

/**
 * Función encargada de mostrar la ventana para la busqueda de Modulos en el sistema
 * @returns boolean
 */
function buscarModulo(){
    $.ajax({
        type:"POST",
        url:"/ajax/inicio/buscarModulo",
        dataType:"json",
        success:procesaRespuesta

    });
    return false;    
}