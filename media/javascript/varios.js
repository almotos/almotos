var modulo = $("#nombreModulo");

$(document).ready(function(){   
    
    //add chosen plugin to the theme selector
    $("#selectTemas").chosen();
   
    /**
     * Funciones encargadas de agregar las 7 ayudas basicas del sistema
     **/
    agregarInformacionBasicaAyuda();
    
    $.ajaxSetup({
        cache:false
    });
    
    /**
     * Hacer el menu del boton derecho arrastrable
     **/
    $("#contenedorBotonDerecho").draggable();
    
    /**
     * Función que agrega el plugin chosen ver 
     **/
//    $(".selectChosen").chosen({no_results_text: "Oops, no se encontraron resultados!"});

    /**
     * Funcion encargada de ponerle el icono a los botones
     **/
    $("button").each(function(){
        icono=$(this).attr("title");
        $(this).button({
            icons:{
                primary:icono
            }
        });
        $(this).removeAttr("title");
    });

    /**
     * Agregar los tooltips a todo lo que cumpla con la condicion,
     * un objeto del DOM con tooltip será el que tenga un atributo ayuda,
     * y lo que será mostrado en el tooltip será el valor de este atributo
     **/
    $('*').tooltip({
        track: true,
        delay: 0,
        showURL: false
    });


    /**
     * Funcion que agrega la funcionalidad de AJAX a todos los botones del sistema
     * que no tengan la clase directo
     **/
    $("button").not(".directo").click(function(){
        $("#indicadorEspera").css("display","block");
        $("#BoxOverlay").css("display","block");
        
        formulario  =   $(this).parents("form");
        destino     =   $(formulario).attr("action");    
        validar     =   $(this).attr("validar");
                
        if(validar != "NoValidar"){
            validarFormulario(formulario); 
            
        } else {
            enviarFormulario(formulario);
            
        }

        return false;
        
    });







 /*
 * Superfish v1.4.8 - jQuery menu widget
 * Copyright (c) 2008 Joel Birch
 *
 * Dual licensed under the MIT and GPL licenses:
 *  http://www.opensource.org/licenses/mit-license.php
 *  http://www.gnu.org/licenses/gpl.html
 *
 * CHANGELOG: http://users.tpg.com.au/j_birch/plugins/superfish/changelog.txt
 */
    jQuery('ul.sf-menu').superfish({
        delay:         1200,
        speed:       'fast'
    }); //inicializar el menu
    


    /**
     *  Agrega la funcionalidad de envio de datos via AJAX a cualquier objeto del DOM
     *  que disponga de esta clase
     **/
    $(".enlaceAjax").click(function(){

        $("#indicadorEspera").css("display","block");
        $("#BoxOverlay").css("display","block");

        destino=$(this).attr("title");
        $.ajax({
            type:"POST",
            url:destino,
            dataType:"json",
            success:procesaRespuesta
        });
        return false;
    });
    /**
     *  Agrega la funcionalidad de envio de datos via AJAX a cualquier objeto del DOM
     *  que disponga de esta clase
     **/
    $(".enlaceAjaxRuta").click(function(){

        $("#indicadorEspera").css("display","block");
        $("#BoxOverlay").css("display","block");
        
        destino=$(this).attr("ruta");
        $.ajax({
            type:"POST",
            url:destino,
            dataType:"json",
            success:procesaRespuesta
        });
        return false;
    });

    /**
     * Le agrega el plugin autocomplete de jquery ui a cualquier campo de texto del
     * sistema que tenga la clase autocompletable
     **/
    $(".autocompletable").autocomplete({
        minLenght:1,
        source:$(this).attr("title"),
        select: function( event, ui ) {
            $( this ).val( ui.item.label );//el valor que se pone en el campo es el label, ejemplo en el campo aparece: "Cali" y en el campo oculto seguido, el id de la ciudad "4""
            $( this ).next("input[type='hidden']").val( ui.item.value );//cada campo autocompletable dispone de un campo oculto al lado que será donde se guarde el id del valor seleccionado

            return false;
        }
    });
  
    
    /**
     * Funcion que le cambia las caracteristicas del idioma al plugin datepicker 
     * osea, el que muestra los calendarios en los campos de texto
     **/
    jQuery(function($){
        $.datepicker.regional['es'] = {
            closeText: 'Cerrar',
            prevText: '<Ant',
            nextText: 'Sig>',
            currentText: 'Hoy',
            monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
            monthNamesShort: ['Ene','Feb','Mar','Abr', 'May','Jun','Jul','Ago','Sep', 'Oct','Nov','Dic'],
            dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
            dayNamesShort: ['Dom','Lun','Mar','Mié','Juv','Vie','Sáb'],
            dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sá'],
            weekHeader: 'Sm',
            dateFormat: 'dd/mm/yy',
            firstDay: 1,
            isRTL: false,
            showMonthAfterYear: false,
            yearSuffix: ''
        };
        $.datepicker.setDefaults($.datepicker.regional['es']);
    }); 


    /**
     * agrega un calendario con caracteristicas de una fecha reciente al campo de texto
     * que tenga esta clase
     **/
    $(".fechaReciente").datepicker({
        dateFormat:"yy-mm-dd",
        changeMonth:true,
        changeYear:true,
        showButtonPanel:true,
        minDate:0,
        maxDate:365
    });

    /**
     * agrega un calendario con caracteristicas de una fecha antigua al campo de texto
     * que tenga esta clase
     **/
    $(".fechaAntigua").datepicker({
        dateFormat:"yy-mm-dd",
        changeMonth:true,
        changeYear:true,
        showButtonPanel:true,
        yearRange:"c-100:c"
    });

    /**
     * Se le agrega la region a los campos datepicker
     **/
    $.datepicker.setDefaults( $.datepicker.regional[ "" ] );
    $( "#fechaAntigua" ).datepicker( $.datepicker.regional[ "es" ] );


    /**
     * Funcion que controla la funcionalidad de la cabecera, (flechita verde a la derecha
     * de la cabecera), esta se encarga de ocultar o mostrar la cabecera
     **/
    $("#cerrarCabecera").click(function(){
        if($("#cabecera").is(":visible")){
            $("#cabecera").slideUp("fast");
            $("#cerrarCabecera").removeClass("flechaArriba");
            $("#cerrarCabecera").addClass("flechaAbajo");
        }else{
            $("#cabecera").slideDown("fast");
            $("#cerrarCabecera").removeClass("flechaAbajo");
            $("#cerrarCabecera").addClass("flechaArriba");
        }
    });


    /**
     * Funcion que pone el focus en el campo usuario en el formulario de inicio de sesion
     **/
    $("#pestanasInicioSesion").find(".contenidoAcordeon").onShow(function(){
        $("#campoUsuario").focus();
    });


    /**
    * Estilos y efectos del campo buscador de los modulos
    **/
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


    /**
     * desabilitar la tecla enter de los campos de buscadores
     * por que? no me acuerdo, Verificar.
     **/
    $("#campoBuscador").bind("keypress", function(e){
        if(e.which == 13){
            return false;
        }  
    });
    

    /*
 *Codigo para agregar el resaltado a la primera fila de la tabla(despues de la fila cabecera)
 */
    //    $(".tablaRegistros tr:gt(0):first").addClass("fondoFilaRegistro");

    /*
    *Codigo para agregar la clase a la fila de la cabecera de la tabla
    */
    $(".tablaRegistros tr:first").addClass("noSeleccionable");

    


    /**
     * Agregar la opcion del modulo pretty photo a todo lo que coincida con el patron del selector.
     * en este caso es a todas las etiquetas <a> que tengan como valor prettyPhoto en el atributo rel
     **/
    $("a[rel^='prettyPhoto']").prettyPhoto(); 
  
    
    //a cada uno de los div de ayuda usuario de los modulos les agrego el boton cerrar
    $("#contenedorAyudaUsuario div").prepend("<p id =botonCerrarAyuda>X</p>");
  
    /**
     * se le agrega la duracion a la cookie que guarda la info de la preferencia de
     * las pestañas del usuario, es decir, si la ultima vez tenia visible una pestaña en particular
     * la siguiente vez que inice sesion veré a misma pestaña
     **/
    $(".pestanas").tabs({
        cookie:{
            expires:30
        }
    });
    /**
     * agrega las caracteristicas del plugin acordion de jquery ui
     **/
    $(".acordeon").accordion({
        autoHeight:false,
        collapsible:true,
        active:false
    });
    /**
     * agrega las caracteristicas del plugin acordion de jquery ui
     **/
    $("#cuadroDialogo").dialog({
        bgiframe:true,
        autoOpen:false,
        resizable:false,
        draggable:true,
        modal:true,
        width:600,
        height:400
    });

    $("#full").show();
});//fin del document.ready


/**
 * Funciones que se interpretan inmediatamente y no necesitan estar dentro del document ready
 **/
    /**
     *  Agrega la funcionalidad de envio de datos via AJAX a cualquier objeto del DOM
     *  que disponga de esta clase
     **/
    $(".enviarAjax").live("click", function(){//codigo para enviar via ajax un formulario o lo que sea
        $("#indicadorEspera").css("display","block");
        $("#BoxOverlay").css("display","block");
    
        formulario  =   $(this).parents("form");
        destino     =   $(formulario).attr("action");

        $(formulario).ajaxForm({
            dataType:   "json"
        });
        $(formulario).ajaxSubmit({
            dataType:   "json",
            success:procesaRespuesta
        });

        return false;

    });
    

    /**
     * Funcion que se encarga de capturar los datos en el lado del cliente cuando se esta realizando una busqueda
     * y enviarlos via AJAX al servidor, esto cuando se hace click en el boton buscador
     **/
    $("#botonBuscador").live("click", function(){
    
        var destino = $(this).attr("alt");  
        var cantReg = $("#campoNumeroRegistros").val(); 
        //    var checks = $(".checkPatronBusqueda:checked");
        var busqueda = $("#campoBuscador").val();
    
        if(busqueda == ""){
            Sexy.alert("Debes ingresar un texto para realizar la busqueda");
            return false;
        }
    
        //        var palabras = busqueda.split(" ");//si el patron de busqueda trae espacios en blanco, lo separo en un arreglo para despues unirlo por |
    
        var cadena = "";//cadena que almacenara cuales de los checkboxes de la tabla han sido seleccionados para el patron de busqueda
        // var hayCheck = false;
    
        $(".checkPatronBusqueda:checked").each(function(){
            cadena += $(this).attr("name")+"|";//voy concatenando el valor del checkbox, ej: su.nombre|a.nombre
        //     hayCheck = true;
        });    
    
        $("#condicionGlobal").val(busqueda+"["+cadena);//le doy valor al campo oculto condicionGlobal para tenerlo en cuenta en la paginacion
    
        var datos =  busqueda+"["+cadena;    
            
        $("#indicadorEspera").css("display","block");
        $("#BoxOverlay").css("display","block");

        $.ajax({
            type:"POST",
            url:destino,
            dataType:"json",
            data: {
                datos: datos,
                cantidadRegistros: cantReg
            },
            success:procesaRespuesta
        });
    
        return false;  
    
    });



    /**
     * Funcion que asigna el evento click sobre el boton "ok" del sexy alert, y
     * sobre este evento, si hay visible una ventana de dialogo, asigna el focus
     * al primer campo input que encuentre, sino, le pone el foco al campo buscador
     **/
    $("#BoxAlertBtnOk").live("click", function(){
        if($("#cuadroDialogo").is(":visible")){
            $("#cuadroDialogo").find("input:text:visible:first").focus();
        }else{
            $("#campoBuscador").focus();
        }
    
    });

    
    /**
     * Funcion que guarda que checks de busqueda estaban seleccionados al enviar la consulta de busqueda, 
     * asi cuando recarga la tabla sabe que campos estaban seleccionados, para si se realiza la paginacion, la realize
     * con las mismas condicones de busqueda
     **/
    $(".checkPatronBusqueda").live("click", function(){
        var cadena = '';
        $(".checkPatronBusqueda:checked").each(function(){
            cadena += "#"+$(this).attr("id")+"|";
        }); 
        
        $("#botonBuscador").attr("checks_busqueda", cadena);//le agrego un atributo con estos valores al boton de la busqueda
    });

    /**
     * A cualquier objeto del DOM que se le haya agregado esta clase (es la que marca que un campo
     * de un formulario era obligatorio y se trató de enviar el formulario sin llenarlo), al hacerle click
     * se encarga de remover la clase para que vuelva a tener una apariencia normal
     **/
    $(".textFieldBordeRojo").live("keypress click", function(){
        $(this).removeClass("textFieldBordeRojo");
        if($(this).has(".campo_obligatorio")){
            $(this).removeClass("campo_obligatorio");
        }
        if($(this).has(".autocompletable_obligatorio")){
            $(this).removeClass("autocompletable_obligatorio");
        }  
    });

    /**
     * A cualquier objeto del DOM que se le haya agregado esta clase (es la que marca que un campo
     * de un formulario era obligatorio y se trató de enviar el formulario sin llenarlo), al presionar cualquier tecla
     * se encarga de remover la clase para que vuelva a tener una apariencia normal
     **/
    $(".autocompletable_obligatorio").live("keypress click", function(){
        $(this).removeClass("campo_obligatorio");
        if($(this).has(".campo_obligatorio")){
            $(this).removeClass("campo_obligatorio");
        }
        if($(this).has(".autocompletable_obligatorio")){
            $(this).removeClass("autocompletable_obligatorio");
        } 
    });


    /**
     * Funcion encargada de restaurar la consulta de la tabla que se muestra en el sistema,
     * por ejemplo despues de una busqueda, con este boton (el azulito que esta al lado del campo de busqueda)
     * se volveria  a cargar la tabla con sus datos originales
     **/
    $("#botonRestaurarConsulta").live("click", function(){
        $("#condicionGlobal").val("");
        $("#contenedorNotificaciones").html("");
        $("#campoBuscador").val("");
        $("#campoNumeroRegistros").val("");
        var destino     = $(this).attr("alt"); 
        var ordenGlobal = $("#ordenGlobal").val();
        ordenGlobal     = ordenGlobal.split("|");
        ordenGlobal     = ordenGlobal[0];
    
        $("#indicadorEspera").css("display","block");
        $("#BoxOverlay").css("display","block");

        $.ajax({
            type:"POST",
            url:destino,
            dataType:"json",
            data: {orden : ordenGlobal},
            success:procesaRespuesta
        });
        
        

    });

    /*
     *Funcion para validar un rango de numeros en un campo de texto, este campo de texto debe tener:
     *1)la clase Solo numeros, la cual valida que solo ingresen numeros
     *2)la clase rangoNumeros
     *3) un atributo llamado rango, el cual tendrá como valores lo sig: rango="numMin-numMax"
     **/
    $(".rangoNumeros").live("keypress", function(e){
        //e.preventDefault();        
        var tecla = verificarTecla(e, "numeros");
        //alert(tecla);
        if(tecla != ""){
            if(tecla != 8){
                e.preventDefault();    
                var rango = $(this).attr("rango");
                
                var cantidad = String.fromCharCode(e.which);
                cantidad = $(this).val()+cantidad;                
                
                if (typeof rango == "undefined") {
                    $(this).val(cantidad);
                    return;
                }
                
                rango = rango.split("-");

                if(cantidad < parseInt(rango[0]) || cantidad > parseInt(rango[1])){
                    return;
                } else {
                    $(this).val(cantidad);    
                }
    
            }
        }
  
    });
    
    /**
     * Funcion Valor minimo, para validar que en un campo solo se pueda introducir un valor minimo. El campo debe tener:
     *1)la clase Solo numeros, la cual valida que solo ingresen numeros
     *2)la clase valorMinimo
     *3) un atributo llamado valorMinimo, el cual tendrá como valor el valor minimo de este campo
     **/
    $(".valorMinimo").live("keypress", function(e){
        //e.preventDefault();        
        var tecla = verificarTecla(e, "numeros");
        if(tecla != ""){
            if(tecla != 8){
                e.preventDefault();    
                var valMin = $(this).attr("valor_minimo");
            
            
                var cantidad = String.fromCharCode(e.which);
                cantidad = $(this).val()+cantidad;

                if(cantidad < parseInt(valMin) ){
                    return;
                } else {
                    $(this).val(cantidad);    
                }
    
            }
        }
  
    });
    
    
    /**
     * Funcion Valor maximo, para validar que en un campo solo se pueda introducir un valor maximo. El campo debe tener:
     *1)la clase Solo numeros, la cual valida que solo ingresen numeros
     *2)la clase valorMaximo
     *3) un atributo llamado valor_maximo, el cual tendrá como valor el valor maximo de este campo
     **/
    $(".valorMaximo").live("keypress", function(e){
        //e.preventDefault();        
        var tecla = verificarTecla(e, "numeros");
        if(tecla != ""){
            if(tecla != 8){
                e.preventDefault();    
                var valMax = $(this).attr("valor_maximo");

                var cantidad = String.fromCharCode(e.which);
                cantidad = $(this).val()+cantidad;

                if( cantidad > parseInt(valMax) ){
                    return;
                } else {
                    $(this).val(cantidad);    
                }
    
            }
        }
  
    });    
    

    /**
     * Funcion encargada de mostrar la columna izquierda de opciones existentes. La columna izquierda
     * es aque lla que se muestra y se esconde
     **/
    $("#mostrarColumnaIzquierda").live("click", function(e){        
        e.stopPropagation();
        
        if( $("#columnaIzquierda").hasClass("columnaIzdaCerrada") ){

            $("#columnaIzquierda").removeClass("columnaIzdaCerrada").addClass("columnaIzdaAbierta");
            $(this).removeClass("flechaDerecha").addClass("flechaIzquierda");
            
            
            $("#mostrarColumnaIzquierda").animate({
                left: '990px'
            }, 500, function() {
                // Animation complete.
                });             
            
            $("#columnaIzquierda").animate({
                left: '-18px'
            }, 500, function() {
                // Animation complete.
                });  
                
        } else {
            $("#columnaIzquierda").removeClass("columnaIzdaAbierta").addClass("columnaIzdaCerrada");
            $(this).removeClass("flechaIzquierda").addClass("flechaDerecha");            
            
            $("#mostrarColumnaIzquierda").animate({
                left: '5'
            }, 500, function() {
                // Animation complete.
                });            

            $("#columnaIzquierda").animate({
                left: '-1001'
            }, 500, function() {
                // Animation complete.
                });            
            
        }
  
    });
    

    /**
     * Codigo para marcar las filas de la tabla, en caso de luego querer borrarlas o modificarlas o en
     * el caso de el modulo de articulos, imprimir los codigos de barra. 
     * funcion: cada vez que se hace click sobre una fila de la tabla con id "tablaRegistros", a esa fila
     * se le agrega una clase "filaConsultada". si dicha fila ya tenia la clase, entonces se desmarca, o lo
     * que es lo mismo, se le remueve la clase "filaConsultada"
     * -- nos referimos a filas marcadas, las que tienen la clase filaConsultada
     **/
    $("#tablaRegistros tr:gt(0)").live("click", function(){
       
        var cantidadFilas = $('.filaConsultada').length;//verifico la cantidad de filas marcadas
       
        if($(this).hasClass("filaConsultada")){ //si la fila sobre la que hice click ya estaba marcada
            cantidadFilas -= 1; //como se va a desmarcar, mermo la cantidad en uno
            $(this).removeClass("filaConsultada");//y le quito la clase
            
        }else{
            $(this).addClass("filaConsultada");//le agrego la clase
            cantidadFilas += 1;//e incremento en uno la cantidad de filas marcadas
            
        }
        
        if(cantidadFilas > 0){ //si hay filas marcadas, se puede mostrar el boton eliminar masivo
            $(".botonEliminarMasivo").slideDown("fast");
            if($("#nombreModulo").val() == "Articulos"){ //si estamos en el modulo articulos
                $(".botonImprimirVariosBarcode").slideDown("fast");//se muestra el boton para imprimir los barcodes
            }
        } else { //si no hay filas marcadas
            $(".botonEliminarMasivo").slideUp("fast");//se oculta el boton eliminar masivo
            if($("#nombreModulo").val() == "Articulos"){//si estamos en articulos
                $(".botonImprimirVariosBarcode").slideUp("fast");//se oculta el boton "imprimir barcode""
            }            
        }
        
    });  


    /**
     * Función que se ejecuta cuando se hace doble click sobre una de las filas de la tabla de registros
     **/
    $("#tablaRegistros tr:gt(0)").live("dblclick", function(e){
        // evito que se ejecute el evento
        e.preventDefault();
        // conjunto de acciones a realizar
        var id    = $(this).attr("id");
        var ruta  = $("#tablaRegistros").attr("ruta_paginador");//por defecto la tabla tiene un atributo "ruta_paginador", la guardo en ruta
        var ruta1 =  ruta.split("/");//la divido para acceder a la ruta basica del modulo
        var ruta_consultar = ruta1[1]+"/"+ruta1[2]+"/see"//le concateno "see", que hace referencia a la llamada a la funcion consultar de cada uno de los modulos
        
        //el id en cada una de las filas tiene esta forma "tr_id" (mal diseño, muy duro de cambiar ahora), por eso lo dividimos y solo 
        //pasamos el valor del id. (De hecho no se corrije, porque es mucho trabajo, y el impacto que tiene sonbre el usuario final es CERO 0.0, y el
        //impacto sobre el desarrollador es mínimo CERO PUNTO CINCO 0.5)
        id = id.split("_");
        $.ajax({

            type:"POST",
            url:ruta_consultar,
            data: {
                id: id[1]
            },
            dataType:"json",
            success:procesaRespuesta
        });
        
      
    });
  

    /**
     * Función llamada cada vez que se hace click en alguno de los botones del paginador (next, prev, ultimo, primero)
     * cada uno de estos botones tienen una serie de atributos que se utilizaran para poder enviar una serie de parametros
     * al servidor via AJAX, y que este responda con la nueva tabla y los nuevos datos segun la posición del paginador
     **/
    $(".botonPaginacion").live("click", function(){
        $("#indicadorEspera").css("display","block");
        $("#BoxOverlay").css("display","block");
        
        var pag             = $(this).attr("pagina");//capturo la pagina en la cual se encuentra
        var cantReg         = $(this).attr("cantidad_registros");//capturo la cantidad maxima de registros que se van a mostrar
        var destino         = $("#tablaRegistros").attr("ruta_paginador");//el destino al cual se enviaran los datos
        var condicion       = $("#condicionGlobal").val();//si hay una condicion global para realizar la consulta (por ejemplo cuando se hace una busqueda y se pagina, se sigue paginando sobre los mismos datos de esa bsqueda)
        var ordenamiento    = $("#ordenGlobal").val();//si hay un ordenamiento global para realizar la busqueda
        
        var ord    = "";
        var nomOrd = "";
        
        if(ordenamiento != ""){ //el ordenamiento se almacena del tipo ej: descendente|a.nombre, donde despues del pipe representa 
            //la tabla (a.) y la columna (nombre) con las cuales se quiere ordenar la consulta
            ordenamiento = ordenamiento.split("|");
            ord    = ordenamiento[0];//seria ej: descendente
            nomOrd = ordenamiento[1];//sería ej: de la tabla Articulos (a. representa articulos) por el nombre
            
        }

        if(condicion == ""){ //si no hay condicion global
            condicion = '';
        }

        $.ajax({

            type:"POST",
            url:destino,
            data: {
                pagina            : pag, 
                orden             :ord, 
                nombreOrden       : nomOrd, 
                consultaGlobal    : condicion,
                cantidadRegistros : cantReg
            },
            dataType:"json",
            success:procesaRespuesta
        });
        
        $(".botonImprimirVariosBarcode").slideUp("fast");
        $("#botonBorrarMasivo").slideUp("fast");
        
    });
    
    /**
     * Codigo para guardar el codigo base de un barchar
     **/
   /* var barChartData = {
            labels : ["Enero","February","March","April","May","June","July"],
            datasets : [
                    {
                            fillColor : "rgba(220,220,220,0.5)",
                            strokeColor : "rgba(220,220,220,1)",
                            data : [65,59,90,81,56,55,40]
                    },
                    {
                            fillColor : "rgba(151,187,205,0.5)",
                            strokeColor : "rgba(151,187,205,1)",
                            data : [28,48,40,19,96,27,100]
                    }
            ]

    }
    
{"labels":["Enero","February","March","April","May","June","July"],"datasets":[{"fillColor":"rgba(220,220,220,0.5)","strokeColor":"rgba(220,220,220,1)","data":["65","59","90","81","56","55","40"]},{"fillColor":"rgba(151,187,205,0.5)","strokeColor":"rgba(151,187,205,1)","data":["28","48","40","19","96","27","100"]}]}    
    $("body").data("bar-chart-data", barChartData); */
  
  
    /**
     * Funciones "ascendente" y "descendente" llamada cada vez que se hace click sobre uno de los botones (flechitas arriba de cada columna) de ordenamiento
     * cada uno de estos botones tienen una serie de atributos que se utilizaran para poder enviar una serie de parametros
     * al servidor via AJAX, y que este responda con la nueva tabla y los nuevos datos segun el nuevo orden que se le acaba de enviar.
     * --mal diseño, deberia ser solo una función, que capture la clase del boton "ejemplo: .ordenamiento" y un atributo llamado "ej: orden" y con estos
     * valores envíe la petición
     **/
    $("#ascendente").live("click", function(){
        var pag     = $("#tablaRegistros").attr("pagina");
        var cantReg = $("#campoNumeroRegistros").val();
        var nomOrd  = $(this).parents("th").attr("nombreOrden");

        var ord = "ascendente";//darle valor al campo oculto orden global
        $("#ordenGlobal").val(ord+"|"+nomOrd);//capturo si hay una condicion global
        var condicion = $("#condicionGlobal").val();
        var destino    = $("#tablaRegistros").attr("ruta_paginador");

        $.ajax({

            type:"POST",
            url:destino,
            data: {
                pagina: pag, 
                orden:ord, 
                nombreOrden: nomOrd, 
                consultaGlobal : condicion,
                cantidadRegistros: cantReg
            },
            dataType:"json",
            success:procesaRespuesta
        });
    });
  
    $("#descendente").live("click", function(){
        var pag     = $("#tablaRegistros").attr("pagina");
        var cantReg = $("#campoNumeroRegistros").val();
        var nomOrd  = $(this).parents("th").attr("nombreOrden");

        var ord = "descendente";
        
        //darle valor al campo oculto orden global
        $("#ordenGlobal").val(ord+"|"+nomOrd);
        
        var condicion = $("#condicionGlobal").val();
        
        var destino    = $("#tablaRegistros").attr("ruta_paginador");

        $.ajax({
            type:"POST",
            url:destino,
            data: {
                pagina            : pag, 
                orden             : ord, 
                nombreOrden       : nomOrd, 
                consultaGlobal    : condicion,
                cantidadRegistros : cantReg
            },
            dataType:"json",
            success:procesaRespuesta
        });
    });
  
  /*
  * Metodo para mostrar y ocultar los botones del bloque principal
  */
    $(".contenidoBloque:has(#botonesInternos)").live("mouseover mouseleave", function(event){
        if(event.type == "mouseover"){
            $(this).find("#contenedorBotonesLista").show("fast");

        }else{
            $(this).find("#contenedorBotonesLista").hide("fast");

        }

    });

  /**
   * Metodo para mostrar y ocultar los botones de los listados
   */
    $("li:has(#botonesLista)").live("mouseover mouseleave", function(event){

        if(event.type == "mouseover"){
            $(this).find("#contenedorBotonesLista").show("fast");

        }else{
            $(this).find("#contenedorBotonesLista").hide("fast");

        }

    });


    /**
     * Funcion llamada cada vez que se escribe sobre un campo de texto con la clase "autocompletable"
     **/
    $(".autocompletable").live("keypress", function(){
        lista = $(this).attr("title");
        $(this).autocomplete({
            minLenght:1,
            source:lista,
            select: function( event, ui ) {//cuando se seleccione un valor
                $( this ).val( ui.item.label );//al campo con la clase autocompletable se le pone el valor ej: autocompletable = ciudades, 
                // si se selecciona Cali, esto sería lo que se pondría en el campo
                $( this ).next("input[type='hidden']").val( ui.item.value );//busca un campo de texto que debe estar 
                // justo seguido del campo autocompletable, y le pone como valor el ID de la ciudad, en este caso 4, es decir, id = 4 , nombre = Cali

                return false;
            }
        });
    });


     /**
      * funcion que verifica que una persona exista en el sistema, y en caso de no existir, avisa que se procedera al registro de una nueva persona
      **/
    $("#campoDocumentoPersona").live("blur", function(){
        var formulario = $( this).parents("form");
        var valor      = formulario.find("#campoDocumentoPersona").val();
    
        if(valor != ''){            
            $.ajax({
                type:"POST",
                url:"/ajax/inicio/verificarPersona",
                dataType:"json",
                data: {
                    datos: valor
                },
                success:function(respuesta){
                    if(respuesta.error){
                        formulario.find("#frasePersonaRegistrada").html('<span class="letraRoja">Se registrará a una nueva persona</span>');
                        formulario.find("#frasePersonaRegistrada").slideDown(300);
                    } else {
                        completarDatosPersona(valor, formulario);
                    }
                }
            });                                    
            
        }

    });

    /**
    * Metodo que le agrega la funcionalidad de envío de formularios a los nuevos botones que se agregan
    * en vivo a través de Javascript una vez ha sido cargado el DOM
    *
    **/
    $("#nuevoBoton").live("click", function(e){
        e.preventDefault();

        //        $("#indicadorEspera").css("display","block");
        $("#indicadorEspera").css("display","block");
        $("#BoxOverlay").css("display","block");
        
        formulario=$(this).parents("form");
        destino=$(formulario).attr("action");
        $(formulario).ajaxForm({
            dataType:"json"

        });
        $(formulario).ajaxSubmit({
            dataType:"json",
            success:procesaRespuesta

        });
        return false;

    });


    /*
    *Codigo para cambiar el tema css, interactura con el select
    *que carga los estilos en la cabecera izquierda de cada modulo
    */
    $("#selectTemas").live("change", function(){

        var tema = $("#selectTemas option:selected").html();

        $("#campoTema").val(tema);

        $("#indicadorEspera").css("display","block");
        $("#BoxOverlay").css("display","block");
        formulario=$(this).parents("form");
        destino=$(formulario).attr("action");
        $(formulario).ajaxForm({
            dataType:"json"

        });
        $(formulario).ajaxSubmit({
            dataType:"json",
            success:procesaRespuesta

        });
        return false;

    });

    //funcion que solo permite la entreda de datos numericos en un campo de texto con esta clase
    $(".soloNumeros").live("keydown", function(e){
        var tecla= document.all ? tecla = e.keyCode : tecla = e.which;
        return ((tecla > 95 && tecla < 106) || (tecla > 47 && tecla < 58) || tecla == 8 || tecla == 9  || tecla == 46 || tecla == 37 || tecla == 39 || tecla == 127);
    });
    
    //funcion que solo permite la entreda de datos numericos en un campo de texto con esta clase Y DE LA TECLA ENTER
    $(".soloNumerosEnter").live("keydown", function(e){
        var tecla= document.all ? tecla = e.keyCode : tecla = e.which;
        return ((tecla > 95 && tecla < 106) || (tecla > 47 && tecla < 58) || tecla == 8 || tecla == 9  || tecla == 46 || tecla == 37 || tecla == 39 || tecla == 127 || tecla == 13);
    });    


    $("#menuPrueba").live("click", function(e){
        e.preventDefault();

        $("#indicadorEspera").css("display","block");
        $("#BoxOverlay").css("display","block");
        formulario=$(this).parents("form");
        destino=$(formulario).attr("action");
        $(formulario).ajaxForm({
            dataType:"json"

        });
        $(formulario).ajaxSubmit({
            dataType:"json",
            success:procesaRespuesta

        });
        return false;

    });
    
    
    /**
    *Codigo para generar el evento de click en el boton derecho
    **/
    $("#tablaRegistros tr:gt(0)").live('contextmenu', function(e) {
        // evito que se ejecute el evento
        e.preventDefault();
        // conjunto de acciones a realizar
        $(".filaConsultada").removeClass("filaConsultada");
        $(this).addClass("filaConsultada");
        
        var id  = $(this).attr("id");
        id      = id.split("_");
        
        var x = parseInt(e.pageX) -145;
        var y = parseInt(e.pageY) -160; 
    
        $("#contenedorBotonDerecho").css("left", x+"px");
        $("#contenedorBotonDerecho").css("top", y+"px");
        
        $("#contenedorBotonDerecho").find("form").find("input[name='id']").val(id[1]);
        
        $("#contenedorBotonDerecho").slideDown("fast");
    });

    /**
    * Click para esconder el menu del boton derecho si se hace click fuera de el
    **/
    $("#full").live("click", function(){
        if($("#contenedorBotonDerecho").is(":visible")){
            $("#contenedorBotonDerecho").slideUp("slow");
        }
    });
    /**
    *Codigo para mover el menu del boton derecho hacia la izquierda cuando se elige una opcion
    **/
    $("#contenedorBotonDerecho").find(".enviarAjax").live("click", function(){
    
        if($("#contenedorBotonDerecho").is(":visible")){
            $("#contenedorBotonDerecho").animate({
                left: "50px"
            }, 1000);
        }
    });



    /**
    * Funcion encargada de dar accion a los botones editar y eliminar
    * de una tabla de registros de ventana modal
    **/
    $(".consultarRegistro").live("click", function(){
        var id = $(this).parents("tr").attr("id");
   
        id = id.split("_");
        id = Number(id[1]);
   
        var destino = $(this).parents("table").attr("ruta_consultar");
        $("#indicadorEspera").css("display","block");
        $("#BoxOverlay").css("display","block");
        
        $.ajax({        
            type:"POST",
            url:destino,
            data: {
                id: id
            },
            dataType:"json",
            success:procesaRespuesta
        }); 
   
    });
    
    $(".editarRegistro").live("click", function(){
        var id = $(this).parents("tr").attr("id");
   
        id = id.split("_");
        id = Number(id[1]);
   
        var destino = $(this).parents("table").attr("ruta_modificar");
        $("#indicadorEspera").css("display","block");
        $("#BoxOverlay").css("display","block");
        
        $.ajax({        
            type:"POST",
            url:destino,
            data: {
                id: id
            },
            dataType:"json",
            success:procesaRespuesta
        }); 
   
    });
    $(".eliminarRegistro").live("click", function(){
        var id = $(this).parents("tr").attr("id");
   
        id = id.split("_");
        id = Number(id[1]);
   
        var destino = $(this).parents("table").attr("ruta_eliminar");
        
        $("#indicadorEspera").css("display","block");
        $("#BoxOverlay").css("display","block");
        
        $.ajax({        
            type:"POST",
            url:destino,
            data: {
                id: id
            },
            dataType:"json",
            success:procesaRespuesta
        }); 
   
    });
  
    /**
    * Funcion que se encarga de marcar las todas filas de la tabla principal
    */ 
    $("#chkMarcarFilas").live("click", function(){    
        if($("#chkMarcarFilas").attr("checked") == 'checked'){
            $("#tablaRegistros tr").not(':first').addClass('filaConsultada');
            $(".botonEliminarMasivo").slideDown("fast");
        } else {
            $("#tablaRegistros tr").not(':first').removeClass('filaConsultada');
            $(".botonEliminarMasivo").slideUp("fast");
        }
   
    });

/**
 *Funcion que permite que al poner el focus sobre cualquier input y presionar la tecla enter
 *lanze un evento click sobre dicho input
 **/
    $("input").live("focus", function(e){
        $(this).bind("keydown", function(e){
            if(e.which == 13 ){
                $(this).trigger("click");
            } 
        });
    });



    /**
     * Cuando se hace click sobre el icono del fondo de las tabla que parece un libro
     * es para mostrar u ocultar la ayuda del modulo
     **/
    $("#contenedorImagenAyuda").live("click", function(){
        if($("#contenedorAyudaUsuario").is(":visible")){
            $("#contenedorAyudaUsuario").slideUp("slow");
            $("#BoxOverlay").css("display","none");
        }else{
            $("#BoxOverlay").css("display","block");
            $("#contenedorAyudaUsuario").slideDown("slow");
        }
    });




var cuentaRespuestas = 0;//machete

function procesaRespuesta(respuesta){
    basicasProcesaRespuesta(respuesta);

    if(respuesta.error){
        funcionesRespuestaError(respuesta);

    }else{
        if(respuesta.accion=="recargar"){
            funcionesRespuestaRecargar(respuesta);

        }else if(respuesta.accion == "insertar"){
            funcionesRespuestaInsertar(respuesta);

        }else if(respuesta.accion=="redireccionar"&&respuesta.destino!=""){
            funcionesRespuestaRedireccionar(respuesta);

        }else if(respuesta.generar){
            if(respuesta.destino){
               
                if( $(".ui-dialog").is(":visible") ){//si ya hay una ventana de dialogo en el dom
                    
                    var cuadros = $(".ui-dialog"); //guardo aqui las ventanas de dialogo que hayan
                    var num     = 0;//averiguo cuantas ventanas hay
                    num         = cuadros.length;//averiguo cuantas ventanas hay
                    var destino = "#cuadroDialogo"+num;
                    
                    //                    $("#base").append("<div id = '"+destino+"'></div>");
                    
                    $(destino).dialog({
                        autoOpen: true,
                        modal: true,
                        title: respuesta.titulo,
                        width: eval(respuesta.ancho),
                        height: eval(respuesta.alto),
                        close: function(){
                            var existeEditor = $(destino).find(".editor");
                            if(existeEditor.length != 0){
                                $(destino).find(".editor").ckeditorGet().destroy();
                            }
                            $(destino).dialog("destroy");
                            $(destino).html("");

                        }

                    });
                    $(destino).html(respuesta.codigo);
                    //agregue un campo oculto en todos los formularios ajax que se llama datos[dialogo]
                    //generalmente esta vacio, pero si la ventana que se abre es diferente a el cuadro de dialogo inicial
                    //le asigna un valor, el valor del cuadro de dialogo en el cual se va a abrir dicho formulario.
                    $(destino+" #idDialogo").val(destino);
                    
                    armarDomConJquery(destino);//funcion contenida en funciones .js
                    activarFuncionesCuadroDialogo(destino);
                    
                    return false;
                    
                }
                               

                if(respuesta.titulo){
                    $(respuesta.destino).dialog("option","title",respuesta.titulo);

                }
                if(respuesta.ancho){
                    $(respuesta.destino).dialog("option","width",eval(respuesta.ancho));
                    
                }
                if(respuesta.alto){
                    $(respuesta.destino).dialog("option","height",eval(respuesta.alto));

                }
                $(respuesta.destino).html(respuesta.codigo);
                
                
                
                armarDomConJquery(respuesta.destino);
                
                
            }     
            
            $(respuesta.destino).dialog({
                open:function(){
                    activarFuncionesCuadroDialogo(respuesta.destino);
                    //no se porque al crear una ventana de dialogo, y llamar a las funciones que arman la apariencia y funcionalidad de la ventana de dialogo, 
                    //se asignan varios triggers en caso de que hayan, como en este caso, es decir, si abro una ventana dos veces, la segunda vez me lanzara
                    //el trigger dos veces, por eso con cuentaRespuestas limito a que asigne estas propiedades solo una vez
                    if(cuentaRespuestas == 0){
                            
                        var isCtrl = false;
                        $(document).bind("keyup", function(e){
                            if(e.which == 17) isCtrl=false;
                        });
                            

                        $(document).bind("keydown", function(e){
                            if(e.which == 17) {
                                isCtrl=true;
                            }

                            if(e.which == 81 && isCtrl == true) {//Ctrl + Q -> cerrar la ventana de dialogo
                                e.preventDefault();
                                $(".ui-dialog").find(".ui-dialog-titlebar-close").trigger("click"); 
                                return false;
                            }


                            if(e.which == 39 && isCtrl == true){//Ctrl + flecha derecha
                                $(".ui-dialog .ui-tabs-selected").next().find("a").trigger("click");//attr("prueba", "jodase");

                            }

                            if(e.which == 37 && isCtrl == true){//Ctrl + flecha izquierda
                                $(".ui-dialog .ui-tabs-selected").prev().find("a").trigger("click");//attr("prueba", "jodase");
                            }        


                        });
                       
                    }
                    cuentaRespuestas ++;                    
 
                },
                close:function(){                    
                    var existeEditor = $(this).find(".editor");
                    if(existeEditor.length != 0){
                        $(this).find(".editor").ckeditorGet().destroy();
                    }
                    $(respuesta.destino).html("");
                }
            });
            $(respuesta.destino).dialog("open");

        }else if(respuesta.accion=="cerrar"&&respuesta.destino!=""){
            if(respuesta.mensaje){
                if(respuesta.textoExito){//si se va a mostrar el alert pero con el  icono de registro exitoso
                    Sexy.info(respuesta.mensaje);
                }else{
                    Sexy.alert(respuesta.mensaje);
                }

            }
            $(respuesta.destino).dialog("close");
            $(respuesta.destino).dialog("destroy");
            $(respuesta.destino).html("");

        }else if(respuesta.accion == "abrir_ubicacion" && respuesta.destino != ""){
            
            if(respuesta.info && respuesta.recargar){
                Sexy.info(respuesta.textoInfo, {
                    onComplete: function(){ 
                        //window.location.reload();
                        window.location = document.URL;
                    }
                });
                
            }else if(respuesta.info && respuesta.redireccionar){
                Sexy.info(respuesta.textoInfo, {
                    onComplete: function(){ 
                        window.location = respuesta.redireccionar;
                    }
                });
                
            } else if(respuesta.info && !respuesta.recargar){
                Sexy.info(respuesta.textoInfo);
                
            }else if(respuesta.alerta){
                Sexy.alert(respuesta.textoAlerta);
                
            }         
            
            if(respuesta.otro_archivo){
                setTimeout(function(){
                    window.open($("#rutaServidor").val()+respuesta.destino2, '_blank');
                }, 1000)
            }            
            
            window.open($("#rutaServidor").val()+respuesta.destino, '_blank');
            


            
        } else{
            if(respuesta.mensaje){
                if(respuesta.textoExito){//si se va a mostrar el alert pero con el  icono de registro exitoso
                    Sexy.info(respuesta.mensaje);
                }else{
                    Sexy.alert(respuesta.mensaje);
                }

            }
        }
    }

    return false;

}//fin de procesa respuesta

function basicasProcesaRespuesta(respuesta) {
    $("#indicadorEspera").css("display","none");
    $("#BoxOverlay").css("display","none");
    $("#BoxOverlayTransparente").css("display","none");
    
    if(respuesta.pruebas){
        Sexy.alert(respuesta.pruebas);

    }
    
    //verificar si hay checks marcados, de ser asi llamo a la funcion para volver a marcarlos
    if($("#tablaRegistros").is(":visible") && !respuesta.generar){
        restaurarValoresTablaRegistros();
    }
    
    if(respuesta.mostrarDatosArticulo){
        $("#contenedorInfoArticulo").html("");
        $("#contenedorInfoArticulo").html(respuesta.contenido);
    }
    
    if(respuesta.cargarJs){
        cargarJS(respuesta.archivoJs);
    }
}


function funcionesRespuestaError(respuesta) {
    if(respuesta.textoExito){//si se va a mostrar el alert pero con el  icono de registro exitoso
        Sexy.info(respuesta.mensaje);
        if(respuesta.recargarTablaRegistros){
            recargarTablaRegistros();
        }
    }else{
        Sexy.alert(respuesta.mensaje);
    }

    if(respuesta.objetivo == "activarSlider"){ 
        $("#sliderInicio").css("display", "block");
        $("#parrafoMensajeSlider").css("display", "block");              
    }
    
}

function funcionesRespuestaRecargar(respuesta) {
    if(respuesta.mensaje){
        if(respuesta.textoExito){//si se va a mostrar el alert pero con el  icono de registro exitoso
            Sexy.info(respuesta.mensaje, {
                onComplete: function(){ 
                    window.location.reload();
                }
            });
        }else{
            Sexy.alert(respuesta.mensaje, {
                onComplete: function(){ 
                    window.location.reload();
                }
            });
        }

    } else {
        window.location.reload();
    }
}

function funcionesRespuestaRedireccionar(respuesta){
    if(respuesta.mensaje){                
        if(respuesta.textoExito){//si se va a mostrar el alert pero con el  icono de registro exitoso
            Sexy.info(respuesta.mensaje, {
                onComplete: function(){ 
                    window.location.href=respuesta.destino; 
                }
            });
        }else{
            Sexy.alert(respuesta.mensaje, {
                onComplete: function(){ 
                    window.location.href=respuesta.destino; 
                }
            });
        }
    //                setTimeout(function(){
    //                    window.location.href=respuesta.destino; 
    //                }, 1500);

    }else{
        window.location.href=respuesta.destino;
    }
}

function funcionesRespuestaInsertar(respuesta) {
    /**
      * si la respuesta ajax requiere modificar un select en el DOM,
      * tambien se agrega la funcionalidad para modificar el plugin chosen
      */
     if (respuesta.idSelect && ($(respuesta.idSelect).is(":visible") || $(respuesta.idSelect+"_chzn").is(":visible"))) {
         var _datos = respuesta.idYNombre.split("|");

         var _id      = _datos[0];
         var _nombre  = _datos[1];

         var _option = "<option value="+_id+" selected>"+_nombre+"</option>";

         $(respuesta.idSelect).append(_option);

         //si el chosen esta visible
         if ($(respuesta.idSelect+"_chzn").is(":visible")) {
             $(respuesta.idSelect).trigger("liszt:updated");
         }

     }

     if(respuesta.destino  || respuesta.destinoInsertar){

         if(respuesta.insertarAjax){
             $(respuesta.destino).prepend(respuesta.contenido);
         }else{
             $(respuesta.destino).html(respuesta.contenido);
         }

         if(respuesta.removerTexto){//codigo para quitar los textos de advertencia de los buscadores
             setTimeout(function(){
                 $(respuesta.removerTexto).slideUp("slow");
             }, 2000)

         }

         /*--INSERTAR DATOS VIA AJAX---*/
         if(respuesta.insertarAjax){
             if(respuesta.idContenedor){
                 $(respuesta.idContenedor).css("display", "none");
                 $(respuesta.idContenedor).show(1500);
             }
             desaBtnYMostrarText();                

             cerrarDialogYHabiBtn();                        

         }

     }//fin de si se envio por ajax una variable destino o una destinoInsertar

     /*INSERTAR DATOS VIA AJAX*/
     if(respuesta.insertarAjax){
         if(respuesta.idContenedor){
             $(respuesta.idContenedor).css("display", "none");
             $(respuesta.idContenedor).show(1500);
         }

         if(respuesta.ventanaDialogo){
             desaBtnYMostrarTextDialog(respuesta.ventanaDialogo);
             setTimeout(function(){ 
                 cerrarDialogYHabiBtnDialog(respuesta.ventanaDialogo);
             }, 1200);                       
         } else {
             if(respuesta.mostrarNotificacionDinamica){
                 setTimeout(function(){ 
                     mostrarNotificacionDinamica('Registro agregado exitosamente', 'exitoso');
                     ocultarNotificacionDinamica();
                 }, 1200);  
             }                    
             desaBtnYMostrarText();                         
             cerrarDialogYHabiBtn();                    
         }

     }

     /**
      * Codigo para editar la informacion del usuario y que
      * haga una recarga de la informacion via ajax
      **/
     if(respuesta.modificarUsuarioAjax){
         desaBtnYMostrarText();
         $(respuesta.idContenedor).prev().hide(1500, function(){
             cerrarDialogYHabiBtn();
             $(respuesta.idContenedor).prev().remove();
             $(respuesta.idContenedor).parent("div").prepend(respuesta.contenido);
             $(respuesta.idContenedor).prev().css("display", "none");
             $(respuesta.idContenedor).prev().show("slow");

             return false;

         });

     }

     /*-- MODIFICAR EL ITEM DIRECTAMENTE DESDE LA LISTA --*/
     if(respuesta.modificarAjaxLista){
         desaBtnYMostrarText();
         $(respuesta.idContenedor).hide(1500, function(){
             $(respuesta.idContenedor).html("");
             $(respuesta.idContenedor).html(respuesta.contenido);
             $(respuesta.idContenedor).show("slow");
             cerrarDialogYHabiBtn();
             return false;
         });
     }

     /*-- ELIMINAR EL ITEM DIRECTAMENTE DESDE LA LISTA --*/
     if(respuesta.eliminarAjaxLista){
         desaBtnYMostrarText();//llamada a funcion, ver para detalles
         $(respuesta.idContenedor).hide(1500, function(){
             $(respuesta.idContenedor).html("");
             $(respuesta.idContenedor).remove();
             cerrarDialogYHabiBtn();
             return false;
         });
     }            

     /*Codigo para agregar una nueva Fila a la tabla de registros*/
     if(respuesta.insertarNuevaFila){
         mostrarNotificacionDinamica('Registro agregado exitosamente', 'exitoso');
         $(respuesta.idDestino).append(respuesta.contenido);             

         $("#cuadroDialogo").dialog("close");                
         ocultarNotificacionDinamica();

         $(respuesta.idContenedor).fadeIn("slow", function(){                    
             if($("#trSinRegistros").is(":visible")){
                 $("#trSinRegistros").fadeOut("slow");
             }
         });

     }

     /*Codigo para agregar una nueva Fila a la tabla de registros*/
     if(respuesta.insertarNuevaFilaDialogo && respuesta.ventanaDialogo){

         desaBtnYMostrarTextDialog(respuesta.ventanaDialogo);//llamada a funcion, consultar para más info.

         setTimeout(function(){
             $(respuesta.ventanaDialogo).dialog("close");                

         }, 1200);
         //Aqui hay que validar porque esta ingresando en otros modulos
         //si no se puede de una forma global y funcional hay que usar la variable modulo para saber donde se encuentra
         if(respuesta.idDestino == "#tablaRegistros" && $(respuesta.idDestino).is(":visible")){ //Si tabla es igual a tabla registros, y es visible 
             if(respuesta.modulo && respuesta.modulo != modulo){
                 //asi funciona no tocar
             } else {
                 $(respuesta.idDestino).append(respuesta.contenido);                    
                 setTimeout(function(){    
                     $(respuesta.idContenedor).fadeIn("slow", function(){
                         $(respuesta.ventanaDialogo+" #textoExitoso").fadeOut("100");
                         $(respuesta.ventanaDialogo+" #botonOk").removeAttr('disabled');
                     });  
                 }, 1200);                          
             }

         } else if(respuesta.idDestino != "#tablaRegistros" && $(respuesta.idDestino).is(":visible")){ //Si tabla es diferente a tabla registros, y es visible 
             $(respuesta.idDestino).append(respuesta.contenido);

             setTimeout(function(){    
                 $(respuesta.idContenedor).fadeIn("slow", function(){
                     $(respuesta.ventanaDialogo+" #textoExitoso").fadeOut("100");
                     $(respuesta.ventanaDialogo+" #botonOk").removeAttr('disabled');
                 });  
             }, 1200);     

         }

     }            

     /*Codigo para modificar una Fila de la tabla de registros*/
     if(respuesta.modificarFilaTabla){
         //verifico que la fila a editar se encuentre en el DOM en caso de haber sido editada desde el buscador
         var visible = $(respuesta.idContenedor).is (':visible');

         if(visible){
             mostrarNotificacionDinamica('Registro modificado exitosamente', 'exitoso');
             $("#cuadroDialogo").dialog("close"); 
             $(respuesta.idContenedor).fadeOut(350); 

             setTimeout(function(){
                 $(respuesta.idDestino).html(respuesta.contenido);
                 $(respuesta.idContenedor).fadeIn("fast");
             }, 350);      

             ocultarNotificacionDinamica();               

         }else{
             desaBtnYMostrarText();                    
             cerrarDialogYHabiBtn();                   

         }                 

     }//fin del metodo modificar fila tabla

     if(respuesta.modificarFilaDialogo && respuesta.ventanaDialogo){
         desaBtnYMostrarTextDialog(respuesta.ventanaDialogo);
         //verifico que la fila a editar se encuentre en el DOM en caso de haber sido editada desde el buscador
         visible = $(respuesta.idContenedor).is (':visible');

         if(visible){
             setTimeout(function(){    
                 $(respuesta.idContenedor).fadeOut("slow", function(){                        
                     $(respuesta.idDestino).html(respuesta.contenido);                            
                     cerrarDialogYHabiBtnDialog(respuesta.ventanaDialogo); 

                 });  
             }, 1200);

             setTimeout(function(){
                 $(respuesta.idContenedor).fadeIn("slow", function(){                                      
                     });
             }, 1200);                    


         }else{
             setTimeout(function(){ 
                 cerrarDialogYHabiBtnDialog(respuesta.ventanaDialogo);
             }, 1200);                   

         }                 

     }//fin del metodo modificar fila tabla            

     /**
      * Codigo para eliminar una fila de la tabla de registros cuando es la unica ventana de dialogo visible
      * por convencion, solo se debe poder eliminar la fila desde el mismo
      * modulo al cual pertenece, es decir, un articulo puede ser eliminado
      * si y solo si se esta viendo el listado general de articulos en el 
      * modulo de articulos
      **/
     if(respuesta.eliminarFilaTabla){

         mostrarNotificacionDinamica('Registro Eliminado exitosamente', 'exitoso');//como es la unica ventana de dialogo se llama a esta funcion (ver para detalles)          

         $("#cuadroDialogo").dialog("close"); 
         $("#contenedorBotonDerecho").slideUp("fast");

         ocultarNotificacionDinamica();//llamada a funcion, ver para detalles

         $(respuesta.idDestino).animate({
             backGroundColor:'#fff', 
             color:'#fff'
         }, 500, function(){            
             $(respuesta.idDestino).fadeOut(500, function(){
                 $(respuesta.idDestino).remove();                     
             });
         });

     }
     /**
      * Codigo para eliminar una fila de la tabla de registros cuando hay varias ventanas de dialogo visibles
      * por convencion, solo se debe poder eliminar la fila desde el mismo
      * modulo al cual pertenece, es decir, un articulo puede ser eliminado
      * si y solo si se esta viendo el listado general de articulos en el 
      * modulo de articulos *** excepcion "acciones" las acciones son vicibles desde el modulo "modulos"
      **/
     if(respuesta.eliminarFilaDialogo && respuesta.ventanaDialogo){
         desaBtnYMostrarTextDialog(respuesta.ventanaDialogo);//llamada a funcion, ver funcion para mas detalles
         $(respuesta.ventanaDialogo+" #full").trigger("click");
         
         setTimeout(function(){                    
             $(respuesta.ventanaDialogo).dialog("close");
             $("#contenedorBotonDerecho").slideUp("fast");
         }, 1000);

         var fila;

         if (respuesta.idDestino2) {
             fila = respuesta.idDestino2; 
         } else {
             fila =respuesta.idDestino;
         }

         $(fila).animate({
             backGroundColor:'#fff', 
             color:'#fff'
         }, 1000, function(){            
             $(fila).fadeOut("slow", function(){
                 $(respuesta.ventanaDialogo+" #textoExitoso").fadeOut("100");
                 $(respuesta.ventanaDialogo+" #botonOk").removeAttr('disabled');                        
                 $(fila).remove();                     
             });
         });
     }            

     /*Codigo para paginar de forma ajax la tabla de registros*/
     if(respuesta.paginarTabla){                
         if(respuesta.info){
             $("#contenedorNotificaciones").html(respuesta.info);
         }

         setTimeout(function(){    
             $(respuesta.idContenedor).fadeOut(5, function(){                        
                 $(respuesta.idDestino).html(respuesta.contenido);

             });  
         }, 10);

         setTimeout(function(){
             $(respuesta.idContenedor).fadeIn(7, function(){

                 });
         }, 15);                
     }

 if(respuesta.destino && respuesta.cargarFunciones){
     armarDomConJquery(respuesta.destino);//funcion contenida en funciones .js
     activarFuncionesCuadroDialogo(respuesta.destino);   
 }
        
}

/**
 * Funcion encargada de redondear un  numero decimal
 **/
function redondear(num, dec){
    num = parseFloat(num);
    dec = parseFloat(dec);
    dec = (!dec ? 2 : dec);
    return Math.round(num * Math.pow(10, dec)) / Math.pow(10, dec);
} 
   

/**
 * Funcion para validar formulario con campos obligatorios
 */
function validarFormulario(forma){
    
    var enviar = true;//enviar por defecto en true
    
    forma.find(".campoObligatorio").each(function(i){//recorro todos los campos obligatorios dentro del form
        var valor = $(this).val();
        
        if(valor == ""){//si alguno esta vacio
            $(this).addClass("textFieldBordeRojo campo_obligatorio");//le agrego las clases que indican error al campo de texto
            enviar = false;
        }
        
    }).promise().done(function(){//una vez ha terminado de recorrer todos los campos
        
        if(enviar){//valido el envio
            enviarFormulario(forma);
            
        } else{//si hubo campos vacios simplemente retiro las capas de proteccion
//            forma.find(".textFieldBordeRojo:first").focus();
            var campoError = forma.find(".textFieldBordeRojo:first");
            var pestana = campoError.parents('div.contenidoPestana');

            if(pestana){
                var idPestana   = pestana.attr("id");
                var pestanas    = campoError.parents('div.ui-tabs');                
                pestanas.find(".ui-tabs-nav li a[href='#"+idPestana+"']").trigger("click");  
                forma.find(".textFieldBordeRojo:first").focus();
                
            } else {
                forma.find(".textFieldBordeRojo:first").focus();
                
            }

            $("#indicadorEspera").css("display","none");
            $("#BoxOverlay").css("display","none");
            $("#BoxOverlayTransparente").css("display","none");   
            
        }
    });
    
    setTimeout(function(){
        $(".textFieldBordeRojo").removeClass("textFieldBordeRojo");
        $(".campo_obligatorio").removeClass("campo_obligatorio");
    }, 2500);
    
}


function enviarFormulario(formulario){
        $(formulario).ajaxForm({
            dataType:   "json"
        });
        $(formulario).ajaxSubmit({
            dataType:   "json",
            success:    procesaRespuesta
        });     
}

/**
* Funcion que se encarga de completar los datos de todos los campos de documento de persona en el sistema
* , es decir, cuaando un modulo requiera una persona, el campo documento debe validar que esa persona ya se
* encuentre registrada, y de ser asi debe traer los datos ya gregarlos en el formulario
**/
function completarDatosPersona(doc, forma){

        
    $.ajax({
        type:"POST",
        url:"/ajax/inicio/verificarPersona",
        dataType:"json",
        data: {
            datos: doc
        },
        success:function(respuesta){
            if(respuesta.error){
                forma.find("#frasePersonaRegistrada").slideUp(300);
                forma.find("#listaTipoDocumentoPersona").val("2");
                forma.find("#campoCiudadDocumentoPersona").val("");
                forma.find("#campoPrimerNombrePersona").val("");
                forma.find("#campoSegundoNombrePersona").val("");
                forma.find("#campoPrimerApellidoPersona").val("");                    
                forma.find("#campoSegundoApellidoPersona").val("");
                forma.find("#campoFechaNacimientoPersona").val("");
                forma.find("#campoCiudadResidenciaPersona").val("");
                forma.find("#campoDireccionPersona").val("");
                forma.find("#campoTelefonoPersona").val("");
                forma.find("#campoCelularPersona").val("");
                forma.find("#campoFaxPersona").val("");
                forma.find("#campoCorreoPersona").val("");
                forma.find("#campoSitioWebPersona").val("");
                forma.find("#campoGeneroPersona").val("");
                forma.find("#imagenMiniaturaPersona").val("");
                forma.find("#imagenNormalPersona").val("");
                forma.find("#campoObservacionesPersona").val("");
                forma.find("#campoActivoPersona").val("");
                return false;
                
            }else{
                forma.find("#frasePersonaRegistrada").html('<span class="letraVerde">Persona ya registrada</span>');
                forma.find("#frasePersonaRegistrada").slideDown(300);
                forma.find("#listaTipoDocumentoPersona").val(respuesta.tipoDoc);
                forma.find("#campoCiudadDocumentoPersona").val(respuesta.ciudadDocumento);
                forma.find("#campoPrimerNombrePersona").val(respuesta.primerNombre);
                forma.find("#campoSegundoNombrePersona").val(respuesta.segundoNombre);
                forma.find("#campoPrimerApellidoPersona").val(respuesta.primerApellido);                    
                forma.find("#campoSegundoApellidoPersona").val(respuesta.segundoApellido);
                forma.find("#campoFechaNacimientoPersona").val(respuesta.fechaNacimiento);
                forma.find("#campoCiudadResidenciaPersona").val(respuesta.ciudadResidencia);
                forma.find("#campoDireccionPersona").val(respuesta.direccion);
                forma.find("#campoTelefonoPersona").val(respuesta.telefono);
                forma.find("#campoCelularPersona").val(respuesta.celular);
                forma.find("#campoFaxPersona").val(respuesta.fax);
                forma.find("#campoCorreoPersona").val(respuesta.correo);
                forma.find("#campoSitioWebPersona").val(respuesta.sitioWeb);
                forma.find("#campoGeneroPersona").val(respuesta.genero);
                forma.find("#imagenMiniaturaPersona").val(respuesta.imagenMiniatura);
                forma.find("#imagenNormalPersona").val(respuesta.imagenNormal);
                forma.find("#campoObservacionesPersona").val(respuesta.observaciones);
                forma.find("#campoActivoPersona").val(respuesta.activo);
                    
                return respuesta.cedula;
            }
        }
    });
        

}

/**
 * Funcion que verifica si la tecla que se esta presionando generara un caracter imprimible
 * 
 * @param {type} evt
 * @returns 
 */
function isCharacterKeyPress(evt) {
    if (typeof evt.which == "undefined") {
        // This is IE, which only fires keypress events for printable keys
        return true;
    } else if (typeof evt.which == "number" && evt.which > 0) {
        // In other browsers except old versions of WebKit, evt.which is
        // only greater than zero if the keypress is a printable key.
        // We need to filter out backspace and ctrl/alt/meta key combinations
        return !evt.ctrlKey && !evt.metaKey && !evt.altKey && evt.which != 8 && evt.which != 46;
    }
    return false;
}

/**
 * Función que se encarga de verificar que la tecla que se presiono, cumple con determinado patrón
 * Ejemplo, si es un "numero" lo que se pasa como valor en el parametro llamado "patron", debe verificarse
 * que sea o un numero valido, o las flechas de navegacion, o la tecla borrar, etc.
 * 
 * parametros.
 * 
 * e= evento js capturado
 * patron = lo que se desa validar, ejemplo: "numeros"
 *
 **/
function verificarTecla(e, patron) {
    
    var tecla = (document.all) ? e.keyCode : e.which;   
    
    if (!isCharacterKeyPress(e)) {//si la tecla solamente tiene funcionalidad no debe agregar ningun valor, ejemplo las flechas, borrar, etc
        return tecla;
        
    } else {
        var valor = String.fromCharCode(tecla);
        //se esta verificando que la tecla a ser verificada es un numero
        if (patron == "numeros") {
            

        } else if (patron == "entero") {
            if (tecla == 46) {
                return;
                
            }
            
        }      
        
        if ( !( /^\d$/.test(valor) ) ) {
            e.stopPropagation();
            e.preventDefault();  
            e.returnValue   = false;
            e.cancelBubble  = true;

            return false;

        } else {
            return tecla;

        }        
        
    }

}

/**
 * Funcion encargada de mostrar los errores en los inputs junto con su notificacion
 * 
 * @param event e
 * @param object obj
 * @returns if valid the value
 */
function mostrarErrorCampo(obj){
    
    mostrarNotificacionDinamica("Ingrese valores correctos en este campo", "error");
    
    obj.val("");
    
    obj.addClass("textFieldBordeRojo");
    
    setTimeout(function(){
        obj.removeClass("textFieldBordeRojo");
        obj.focus();
    }, 2000);
    
    

}

/**
 * Funcion que se encarga de validar que un numero este bien escrito solo con sus dos puntos decimales
 * 
 * @returns {undefined}
 */
function verificarFormatoNumeroDecimal(obj) {
     var strString  = obj.val();
     var blnResult  = true;
     var varcount   = 0;

     for (i = 0; i < strString.length && blnResult == true; i++) {

         if (strString.charAt(i) == ".") {
             varcount++;
             if (varcount > 2) {
                 //alert("Please enter one decimal only");
                 blnResult = false;
             }
         }
     }
     
    if (!blnResult) {
        mostrarErrorCampo(obj);
    }
    
}


/**
 *funcion que verifica los parametros de formateo y de moneda y se encarga de dar el formato correcto a los datos a ser mostrados
 **/
function formatearDato(total){
     
//    var tipoMonedaColombiana = $("#tipoMonedaColombiana").val();
//    
//    if(tipoMonedaColombiana == "tradicional"){
//        total = Math.round(total);
//        return total;
//        
//    } else {
        var cantidadDecimales = $("#cantidadDecimales").val();
        var datos = total.toString().split(".");
        
        if(datos.length > 1){
            var primerDato  = datos[0];
            var segundoDato = datos[1];
            
            if(segundoDato.length > cantidadDecimales){
                segundoDato = segundoDato.substr(0, parseInt(cantidadDecimales));
            } 
        
            total = primerDato+"."+segundoDato;            
            
        }

        return total;
//    }
    
}

/*
 *Funcion para cargar archivos javascript via ajax
 **/
function cargarJS(nomArch) {
    //var d = new Date();
    var ele     = document.getElementById(nomArch);//codigo que determina
    var tagjs   = document.createElement("script");
    
    tagjs.setAttribute("type", "text/javascript");
    tagjs.setAttribute("id", nomArch); 
    //tagjs.setAttribute("src", "/js/"+nomarch+".js?rnd="+d.getTime());
    tagjs.setAttribute("src", nomArch);   
    
    if (ele == undefined) {//si ya se ha cargado el archivo para evitar que se carge varias veces
        document.getElementsByTagName("head")[0].appendChild(tagjs);
        
    } else {
        ele.parentNode.removeChild(ele);
        document.getElementsByTagName("head")[0].appendChild(tagjs);  
        
    }
    
}



/**
 * Funcion que se encarga de volver a marcar los checks de patrones de busqueda de las tablas despues de una 
 * busqueda, o de una paginacion
 */
function restaurarValoresTablaRegistros(){

    $("#tablaRegistros").onShow(function(){

        setTimeout(function(){
            
            /**
            * Hacer el menu del boton derecho arrastrable
            **/
            $("#contenedorBotonDerecho").draggable();              
            
            //Colocar nuevamente los checkbox que estabas seleccionados, otra vez seleccionados
            var checks = $("#botonBuscador").attr("checks_busqueda");
            if(checks){
                var largo = checks.length;
                checks = checks.substr(0, parseInt(largo) - 1)

                var arreglo = checks.split("|");

                for(var i in arreglo){
                    $(arreglo[i]).attr("checked", "checked");
                }                   
            }
            
            //agregar las ayudas del tooltip
            $('*').tooltip({
                track: true,
                delay: 0,
                showURL: false
            });            
            
            //marcar las palabras de las columnas de acuerdo al patron de busqueda            
            var patronBusqueda = $("#campoBuscador").val().toUpperCase();
            if(patronBusqueda != ""){
                $("#tablaRegistros tr td:contains('"+patronBusqueda+"')").each(function(){
                    var contenido = $(this).html();
                    if(contenido.toLowerCase().indexOf(('letraVerde' + '').toLowerCase()) == -1){
                        contenido = contenido.replace(patronBusqueda, "<span class='letraVerde negrilla subtitulo'>"+patronBusqueda+"</span>");
                        $(this).html(contenido);                        
                    }
                });
            }
            
            //cuando se recarga la página verificar si esta chequeado el marcar las filas y marcarlas
            if($("#chkMarcarFilas").is(":checked")){
                $("#tablaRegistros tr:gt(0)").addClass("filaConsultada");
            }            
            
            //agregar en los botones de paginacion el atributo registros por página
            var cantReg = $("#campoNumeroRegistros").val();
            $(".botonPaginacion").each(function(){ //Añadir a los botones de páginacion la cantidad de registros que se van a utilizar
                $(this).attr("cantidad_registros" , cantReg);
            });    
            
            //activar nuevamente la ayuda
            $(".contenedorImagenTeachme").bind("click", function(){
                introJs().start();
            });  
            
        }, 300);
        
    }); 
}

/**
 * Funcion que se encarga de recargar la tabla de registros de un determinado modulo
 */

function recargarTablaRegistros(){
    $("#botonRestaurarConsulta").trigger("click");
}


/**
 * Funcion que se encarga de "desabilitar el boton y mostrar el texto de que fue exitosa la transaccion"
 * esto cuando se muestra en la ventana de dialogo numero 1, sino en el cuadro de notificaciones del modulo
 **/
function desaBtnYMostrarText(){
    $("#botonOk").attr('disabled', 'disabled');
    $("#textoExitoso").fadeIn("1000");    
}

/**
 * función que se encarga de cerrar la ventana de dialogo y de habilitar el boton de ok
 * esto cuando se muestra en la ventana de dialogo numero 1, sino en el cuadro de notificaciones del modulo
 **/
function cerrarDialogYHabiBtn(){
    setTimeout(function(){
        $("#cuadroDialogo").dialog("close");
        $("#botonOk").removeAttr('disabled');
        $("#textoExitoso").fadeOut("100");                             
                        
        return false;
    }, 1000);
                    
   
}

/**
 * Funcion que se encarga de "desabilitar el boton y mostrar el texto de que fue exitosa la transaccion"
 * esto cuando se esta mostrandor en la ventana de dialogo (sobre todo cuando se llama a una ventana desde un modulo diferente al propio, o cuando hay varias ventanas de dialogo en pantalla
 * por ejemplo, si desde el modulo de compras de mercancia hago la llamada al metodo agregar articulo), sino en el cuadro de notificaciones del modulo
 *
 * @param objeto = objeto del DOm, seria la ventana de dialogo donde se esta mostrando la transaccion, asi se encuentra el boton y el texto en dicha ventana
 **/
function desaBtnYMostrarTextDialog(objeto){
    $(objeto+" #botonOk").attr('disabled', 'disabled');
    $(objeto+" #textoExitoso").fadeIn("1000");    
}

/**
 * Funcion que se encarga de "desabilitar el boton y mostrar el texto de que fue exitosa la transaccion"
 * esto cuando se esta mostrandor en la ventana de dialogo (sobre todo cuando se llama a una ventana desde un modulo diferente al propio,  o cuando hay varias ventanas de dialogo en pantalla
 * por ejemplo, si desde el modulo de compras de mercancia hago la llamada al metodo agregar articulo), sino en el cuadro de notificaciones del modulo
 *
 * @param objeto = objeto del DOm, seria la ventana de dialogo donde se esta mostrando la transaccion, asi se encuentra el boton y el texto en dicha ventana
 **/
function cerrarDialogYHabiBtnDialog(objeto){
    $(objeto).dialog("close");
    $(objeto+" #botonOk").removeAttr('disabled');
    $(objeto+" #textoExitoso").fadeOut("100");  
    $(objeto+" #sinRegistros").fadeOut("500");
                        
    return false;                    
   
}

/**
 * Funciones para mostrar y ocultar las notificaciones dinamicas que se muestran
 * deslizandose desde la derecha
 */
function mostrarNotificacionDinamica(texto, tipo){
    
    var claseNotificacion = 'textoExitosoNotificaciones';
    
    if(tipo == "exitoso") {
        claseNotificacion = 'textoExitosoNotificaciones';
        
    } else if (tipo == "advertencia") {
        claseNotificacion = 'textoAdvertenciaNotificaciones';
        
    } else if (tipo == "error") {
        claseNotificacion = 'textoErrorNotificaciones';
        
    } else if (tipo == "info") {
        claseNotificacion = 'textoInfoNotificaciones';
        
    }
    
    $("#contenedorNotificaciones").html("<p class='"+claseNotificacion+" notificacionDinamica' id='notificacionDinamica'>"+texto+"</p>");
    $("#notificacionDinamica").slideDown(700);     
    
    ocultarNotificacionDinamica();
    
}

function ocultarNotificacionDinamica(){
    setTimeout(function(){
        $("#notificacionDinamica").slideUp(700);
        setTimeout(function(){
            $("#contenedorNotificaciones").html('');
        }, 700);
    }, 5000); 
}



/**
* Funciones utilizadas por la carga masiva de articulos 
 */
function procesaRespuestaInicial(respuesta) {
    
    $("#indicadorEspera").css("display","none");
    $("#BoxOverlay").css("display","none");
    
    if (respuesta.error) {
        Sexy.alert(respuesta.mensaje);
    } else {

        if (respuesta.campos) {

            $('#inicial').val('1');
            var lista = '<option value=""> </option>';
            $.each(respuesta.campos, function(id, valor){
                lista = lista+'<option value="'+id+'">'+valor+'</option>';
            });
            $('.selectorCampo').html(lista);
        }
    }
}
function seleccionarCampo(campo) {

    var id_campo    = $(campo).attr('id');
    var valor       = $(campo).val();
    
    if (valor != 0) {
        $('.selectorCampo').each(function() {
            
            if ($(this).attr('id') != id_campo && $(this).val() == valor) {
                $(this).val('0');
            }
        });
    }
}

function agregarInformacionBasicaAyuda(){
    
    $(".chkMarcarFilas").attr("data-step", "1");
    $(".chkMarcarFilas").attr("data-intro", "Haga click aquí para seleccionar todas las filas de la tabla. Así podría hacer una eliminación masiva.");
    
    $("#campoNumeroRegistros").attr("data-step", "2"); 
    $("#campoNumeroRegistros").attr("data-intro", "Ingrese aquí el número de registros que desea ver en la tabla y presione la tecla enter.");
    
    $('.botonAccion').parents("div [id^='botonAdicionar']").attr("data-step", "3"); 
    $('.botonAccion').parents("div [id^='botonAdicionar']").attr("data-intro", "Click aquí para agregar un nuevo registro.");             
    
    $("#campoBuscador").attr("data-step", "4"); 
    $("#campoBuscador").attr("data-intro", "Aquí puedes realizar busquedas. Mínimo debes ingresar dos caracteres y hacer click en el botón buscar o presionar la tecla enter.");    
        
    $("#contenedorNotificaciones").attr("data-step", "5"); 
    $("#contenedorNotificaciones").attr("data-intro", "Aquí se mostrarán las diferentes notificaciones o alertas que genere el sistema.");    
        
    $("#checkPatronBusqueda1").attr("data-step", "6"); 
    $("#checkPatronBusqueda1").attr("data-intro", "Marca en estos campos de chequeo las columnas por las que desees filtrar una busqueda."); 
    
    $("#contenedorTablaRegistros  tbody").attr("data-step", "7"); 
    $("#contenedorTablaRegistros  tbody").attr("data-intro", "En esta tabla se listan los registros. Se muestran 10 por defecto, pero se pueden mostrar cuantas se deseen(ver paso 2. Máximo recomendable 500 registros).");       
               
    
    $("#contenedorTablaRegistros  tbody tr:eq(1)").attr("data-step", "7"); 
    $("#contenedorTablaRegistros  tbody tr:eq(1)").attr("data-intro", 
    "Haz click derecho sobre las filas de la tabla para desplegar el menú de opciones, por ejemplo consultar, editar, modificar, borrar, etc.");       
           
    $("#contenedorInferiorTabla").attr("data-step", "8"); 
    $("#contenedorInferiorTabla").attr("data-intro",
    "Aquí se encuentra: El paginador para desplazarse hacia adelante o hacia atras en el listado de registros. La información de la paginación y a la derecha encuentras el botón 'ayuda' para mostrar el manual de uso del módulo");  
    

    setTimeout(function(){
        $(".contenedorImagenTeachme").bind("click", function(){
            introJs().start();
        });        
    }, 250);

}



/**
 * Funcion encargada de enviar la informacion del articulo de carga masiva para armar en la
 * respuesta los campos de los selectores de el archivo
 */    
    $("#[name=masivo]").live("change", function(){
        $("#indicadorEspera").css("display","block");
        $("#BoxOverlay").css("display","block");
        
        formulario=$(this).parents("form");
        destino=$(formulario).attr("action");

        $(formulario).ajaxForm({
            dataType:"json"
        });
        $(formulario).ajaxSubmit({
            dataType:"json",
            success:procesaRespuestaInicial
        });

        return false;

    });
    
function notificador (titulo, mensaje, tipo) {
    $.pnotify({
        title: titulo,
        text: mensaje,
        type: tipo,
        styling: 'bootstrap'
    });    
       
}
    
    
function procesaRespuestaInicial(respuesta) {
    
    $("#indicadorEspera").css("display","none");
    $("#BoxOverlay").css("display","none");
    
    if (respuesta.error) {
        Sexy.alert(respuesta.mensaje);
    } else {

        if (respuesta.campos) {

            $('#inicial').val('1');
            var lista = '<option value=""> </option>';
            $.each(respuesta.campos, function(id, valor){
                lista = lista+'<option value="'+id+'">'+valor+'</option>';
            });
            $('.selectorCampo').html(lista);
        }
    }
}    

/*
 * Funcion para transformar valores de dinero, y le agrega la cantidad de decimales defiidos en el sistema
 */
function parseDouble(numero) {

    var valor = parseFloat(numero).toFixed(parseInt($("#cantidadDecimales").val()));
    
    return parseFloat(valor);
}