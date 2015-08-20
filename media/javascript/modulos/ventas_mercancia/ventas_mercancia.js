var contadorArticuloVenta = 0;
 
$(document).ready(function(){
    
    
    //ayuda a chosen selects
    setTimeout(function(){
        
        //ADICIONAR EL PASOS DEL TUTORIAL DINAMICAMENTE, YA QUE ERA MUCHO MAS COMPLEJO (O IMPOSIBLE, EJEMPLO EN LOS SELECT CON CLASE ".CHOSEN") 
        //HACERLO DESDE EL PHP
        $("#selectorCliente_chzn").next().attr("data-step", "2");
        $("#selectorCliente_chzn").next().attr("data-intro", "Agregar Cliente: En caso de que el cliente no se encuentre registrado \n\
        en el sistema, lo puedes registrar desde aquí.");
        
        //SELECTOR DE USUARIO VENDEDOR
        $("#selectorUsuario_chzn").attr("data-step", "0");
        $("#selectorUsuario_chzn").attr("data-intro", "Seleccione el usuario vendedor que va a realizar la venta.");         

        //Selector de proveedores
        $("#selectorCliente_chzn").attr("data-step", "1");
        $("#selectorCliente_chzn").attr("data-intro", "Seleccione el cliente al que se le va a realizar la venta."); 
        
        //selector bodegas
        $("#selectorBodegas_chzn").attr("data-step", "6");
        $("#selectorBodegas_chzn").attr("data-intro", "Bodegas: seleccione la bodega general desde la cual va a salir esta mercancia.\n\
         Recuerda que desde el listado de articulos existe la opción de cambiar la bodega a la cual ingresará un determinado articulo."); 
        
        //selector cajas
        $("#selectorCaja_chzn").attr("data-step", "7");
        $("#selectorCaja_chzn").attr("data-intro", "Cajas: seleccione la caja en la cual va a realizar esta venta.");  
        
        //campo agregar artiulos
        $("#articuloFactura").next().next().attr("data-step", "11");
        $("#articuloFactura").next().next().attr("data-intro", "Agregar articulo: Si el articulo a ser vendido no existe\n\
        en el sistema, lo puedes ingresar desde este mismo formulario haciendo click aquí.");
        
    }, 1000);    
    
    
    $("#articuloFactura").focus();
    var modulo = $("#nombreModulo").val();
    var isCtrl = false;
    var shift  = false;

    $(document).bind("keyup", function(ev){
        if(ev.which == 17) isCtrl=false;
        
        if(ev.shiftKey==1) shift=false;
    });


    $(document).bind("keydown", function(ev){
        if(ev.which == 17) {
            isCtrl=true;
        }
        if(ev.shiftKey==1) shift=true;



        if(ev.which == 112 && isCtrl == false) {//Tecla F1 Buscar articulos
            if(modulo == "Ventas_mercancia"){
                agregarVariosArticulos(modulo);
            }
            return false;
        }
        
        if(ev.which == 13 && isCtrl == true) {//Ctrl + Enter
            if(modulo == "Ventas_mercancia"){
                alert("control + enter");
                $("#botonFinalizarFactura").trigger("click");
            }
            return false;
        }    
        
        if(ev.which == 73 && isCtrl == true) {//Ctrl + I //imprimir factura pdf
            if(modulo == "Ventas_mercancia"){
                
                $("#btnImprimirFacturaPdf").trigger("click");
            }
            return false;
        }     
        
        if(ev.which == 80 && isCtrl == true) {//Ctrl + P //imprimir factura pos
            if(modulo == "Ventas_mercancia"){
                
                $("#btnImprimirFacturaPos").trigger("click");
            }
            return false;
        }   
        
        if(ev.which == 79 && isCtrl == true) {//Ctrl + O //Generar orden de venta
            if(modulo == "Ventas_mercancia"){
                
                $("#btnGenerarOrdenVenta").trigger("click");
            }
            return false;
        }     
        
        if(ev.which == 81 && isCtrl == true) {//Ctrl + Q //Cancelar acción Factura
            if(modulo == "Ventas_mercancia"){
                
                $("#botonCancelarAccionFactura").trigger("click");
            }
            return false;
        }          
        
        
        if(ev.which == 113 && isCtrl == false) {//Tecla F2  Buscar factura
            buscarFactura(modulo);
            return false;
        }
        
        if($("#tablaArticulosVenta").is(":visible") ){
            
            if($("#tablaArticulosVenta").is(":visible") && ev.which == 38){//Ctrl + flecha arriba 
                ev.preventDefault();
                filaSeleccionada = false;
                var filaMarcada1 = $(".filaTablaSeleccionarItem");
                if(filaMarcada1.length <= 0){
                    $("#tablaArticulosVenta tr:last").addClass("filaTablaSeleccionarItem");
                }
                
                var pre = filaMarcada1.prev("tr:not(.noSeleccionable)");
                if(pre.length > 0){
                    filaMarcada1.removeClass("filaTablaSeleccionarItem");
                    pre.addClass("filaTablaSeleccionarItem");                   
                    
                }
                return false;
            }
            
            if($("#tablaArticulosVenta").is(":visible") && ev.which == 40) {//Ctrl + flecha abajo      aqui tendria que verificar si esta marcada y desmarcarla 
                ev.preventDefault();
                filaSeleccionada = false;
                var filaMarcada = $(".filaTablaSeleccionarItem");
                if(filaMarcada.length <= 0){
                    $("#tablaArticulosVenta tr:first").addClass("filaTablaSeleccionarItem");
                }            
                
                var sig = $(".filaTablaSeleccionarItem").next("tr");
                if(sig.length > 0){
                    $(".filaTablaSeleccionarItem").removeClass("filaTablaSeleccionarItem");
                    sig.addClass("filaTablaSeleccionarItem");                    
                }
                return false;
            }
            
            //codigo para marcar las filas con F5
            var filasResaltadas = $(".filaTablaSeleccionarItem");
            filasResaltadas = filasResaltadas.length;
            
            if(ev.which == 116 && filasResaltadas){
                ev.preventDefault();
                $(".filaTablaSeleccionarItem").addClass("filaTablaItemSeleccionada");
            }
            
            if(ev.which == 117 && filasResaltadas){
                ev.preventDefault();
                $(".filaTablaSeleccionarItem").removeClass("filaTablaItemSeleccionada");
            }
            
            
            
            if(ev.which == 120) {//Tecla Fp  Buscar articulo
                var id = $(".filaTablaSeleccionarItem").attr("atributo_0");

                $.ajax({
                    type:"POST",
                    url:"/ajax/articulos/see",
                    dataType:"json",
                    data: {
                        id: id
                    },
                    success:procesaRespuesta
                });

                return false;
            }

        
        }//fin de si tabla articulos venta es visible

        
    });//fin del keydown
    
    
            /**
            * Agrega la funcionalidad de cerrar la ventana de dialogo en el cual se muestra
            * sin realizar ningun tipo de accion contra el servidor
            **/
            $(".botonCancelar").live("click", function(e){
                e.preventDefault();
                $(this).parents(".ui-dialog").find(".ui-dialog-titlebar-close").trigger("click");
            });    
    
            /**
             * Esta funcion es lanzada cuando se hace click en el boton "adicionar varios articulos"
             * desde la tabla de articulos que se muestra en la ventana modal.
             * Su funcion es recorrer las filas de la tabla con la clase "filaConsultada" y armar
             * un articulo para agregarlo en el listado de venta.
             **/
            $("#imagenAdicionarVariosArticulos").live("click", function(){
                
                var bodega      = $("#idBodegaGeneral").val();
                
                var descuento = $("#campoDescuentoListadoArticulos").val();              
                
                $("#BoxOverlayTransparente").css("display","block");
                
                var fila = "";
                
                $(".filaConsultada").each(function(i){

                    //en la tabla que muestra los articulos existen unos atributos que almacenan la
                    //informacion relevante del articulo para poder generar con esta la fila del
                    //articulo
                    var articulo = $(this).attr("atributo_1");//aqui se van capturando ;D                
                    var precio   = $(this).attr("atributo_4");
                    
                    var cantidad = $(this).find(".campo-cantidad-articulo").val();

                    var id       = $(this).attr("atributo_0");

                    var iva      = $(this).attr("atributo_5");

                    if(precio == ""){
                        precio = "0";
                    }
                    precio       = parseDouble(precio);

                    var subtotal = precio;

                    if(descuento != ''){
                        descuento   = parseDouble(descuento);
                        subtotal    = ( descuento * precio ) / 100;
                        subtotal    = precio - subtotal;
                    } else {
                        descuento = '0';
                    } 
                    
                    subtotal *= cantidad;
                        
                    var noExisteEnListado   = true;
                    var idFila              = '';
                    /**
                     * recorro todas las filas para verificar que el articulo no se encuentre ya en el listado de articulos
                     **/
                    $(".filaArticuloVenta").each(function(){
                        idFila = $(this).attr("cod");

                        if(idFila == id){
                            noExisteEnListado = false;//en caso de encontrarse declaro la variable false
                        }

                    });

                    if(noExisteEnListado){ //                       
                        contadorArticuloVenta ++;  

                        var datos = new Array();//arreglo con la informacion del articulo para armar la fila
                        datos['contadorArticuloVenta']  = contadorArticuloVenta;
                        datos['id']                     = parseInt(id, 10);
                        datos['iva']                    = parseDouble(iva);
                        datos['precio']                 = parseDouble(precio);
                        datos['bodega']                 = bodega;
                        datos['cantidad']               = cantidad;
                        datos['subtotal']               = parseDouble(subtotal);
                        datos['articulo']               = parseInt(id, 10)+"::"+articulo;
                        datos['descuento']              = descuento;                           

                        fila += generarFilaArticulo(datos);//funcion que retorna la fila ya armada                            

                    }
                    
                });  

                $("#tablaListaArticulosFactura").find("#thead").after(fila);

                armarCadenaDatosArticulos();
                
                //settimeout
                setTimeout(function(){
                    calcularTotalFactura();
                    
                }, 250);                

                $(".ui-dialog").find(".ui-dialog-titlebar-close").trigger("click"); 
                
                $("#articuloFactura").focus();
                
                $("#BoxOverlayTransparente").css("display","none");
                
                return false;            

                

            });    
    

    $("#selectorCliente").on("change", function(){
        //si se cambia de cliente se deben eliminar todos los articulos de la lista
        resetFactura();

        
    });            
            
    
    
    //Codigo que se encarga de verificar que se escriban las letras correctas en el cmpo selector del patron
    //de busqueda, y a su vez, determina a que modulo se debe ir para listar
    $("#campoTextoBusquedaArticulos").live("focus", function(ev){
        var patron = $("#campoPatronBusquedaArticulos").val().toUpperCase();
        if(patron != "S" && patron != "L"){
            $("#campoPatronBusquedaArticulos").val("");
            $("#campoPatronBusquedaArticulos").focus();
        }else{
            if(patron == "S"){
                $(this).attr("title", "/ajax/subgrupos/listar");
            } else if(patron == "L"){
                $(this).attr("title", "/ajax/lineas/listar");
            }
        }
    });
    
    //Funcion que se encarga de mostrar los campos ocultos de busqueda por marca
    //tambien agrega valor al campo oculto de buscar por marca
    $("#textoAgregarCampoMarca").live("click", function(){
        if($("#contenedorBusquedaMarca").is(":visible")){
            $("#identificadorOculto").val("");
            $("#contenedorBusquedaMarca").fadeOut("fast");
        }else{
            $("#contenedorBusquedaMarca").fadeIn("fast");
            $("#campoTextoBusquedaArticulosMarca").focus();
        }

    });
    
 
    
    
    $("#fraseMasDescuento").click(function(){
        if($(".dctoOculto").is(":visible")){
            $(".dctoOculto").addClass("oculto");
        }else{
            $(".dctoOculto").removeClass("oculto");
        }
    });
     
     
    $(".imagenEliminarItemFila").live("click", function(){
        $(this).parents("tr").fadeOut("fast");
        $(this).parents("tr").remove();   
        
//        var valorCampo = "";
//        
//        $(".filaArticuloVenta").each(function(e){
//            var cod = $(this).attr("cod");
//            
//            var precio = $(this).attr("precio");
//            
//            valorCampo += parseInt(cod)+"-"+precio+"|";
//            
//        });
//        
//        $("#cadenaArticulosPrecios").val(valorCampo);
        
        setTimeout(function(){
            calcularTotalFactura();
        }, 250);
               
    });
    
    //codigo que pone la clase en la tabla de articulos cuando se pasa el raton por una fila
    $("#tablaArticulosVenta tr").not(":first").live({
        mouseenter:
        function(){
            $(this).addClass("filaTablaSeleccionarItem");
        },
        mouseleave:
        function(){
            $(this).removeClass("filaTablaSeleccionarItem");   
        }
    }
    );
      
    //codigo que agrega o quita la clase de seleccion sobre una fila al hacer click  
    $("#tablaArticulosVenta tbody tr").not(":first").live("click", function(){
        if($(this).hasClass("filaTablaItemSeleccionada")){
            $(this).removeClass("filaTablaItemSeleccionada");
        }else{
            $(this).addClass("filaTablaItemSeleccionada");
        }
    }
    );    

    //codigo para abrir la ventana que muestra el formulario para buscar listado de articulos
    $("#mostrarTablaArticulos").live("click", function(e){ 
        e.preventDefault();

        //        $("#indicadorEspera").css("display","block");
        $("#BoxOverlayTransparente").css("display","block");

        formulario=$(this).parents("form");
        destino=$(formulario).attr("action");

        $(formulario).ajaxForm({
            dataType:"json"
        });
        $(formulario).ajaxSubmit({
            dataType:"json",
            success:procesaRespuesta
        });
        $(this).parents(".ui-dialog").find(".ui-dialog-titlebar-close").trigger("click"); 
        return false;
    });
   
   
    //codigo para llamar al metodo que muestra la ventana de dialogo de busqueda de facturas
    $("#tablaBusquedaFacturas").live("click", function(e){ 
        e.preventDefault();

        //        $("#indicadorEspera").css("display","block");
        $("#BoxOverlayTransparente").css("display","block");

        formulario=$(this).parents("form");
        destino=$(formulario).attr("action");

        $(formulario).ajaxForm({
            dataType:"json"
        });
        $(formulario).ajaxSubmit({
            dataType:"json",
            success:procesaRespuesta
        });
        $(this).parents(".ui-dialog").find(".ui-dialog-titlebar-close").trigger("click"); 
        return false;
    });   
   
   
    //codigo para consultar un articulo
    $(".imagenConsultarItemFila").live("click", function(){
        var id = $(this).parents("tr").attr("cod");
        
        $.ajax({
            type:"POST",
            url:"/ajax/articulos/see",
            dataType:"json",
            data: {
                id: id
            },
            success:procesaRespuesta
        });
        
    });
    
    

    //Funcion que se encarga de mostrar los datos del articulo en el modulo de ventas
    //cada vez que se hace focus sobre un articulo de la lista del autocomplete

    $("#articuloFactura").bind("autocompletefocus", function(event, ui){
        var bodega = $("#idBodegaGeneral").val();
        var precio  = "<p class='letraBlanca  '><span class=masGrande2>Precio venta:</span> <span class=masGrande5>"+ui.item.value+"</span></p>";
        precio += "<p class='letraBlanca  '><span class=masGrande1>Cant. bodega "+parseInt(bodega, 10)+":</span> <span class=masGrande2>"+ui.item.cant+"</span></p>";
        precio += "<p class='letraBlanca '><span class=masGrande1>Cant. Total:</span> <span class=masGrande1>"+ui.item.cant_total+"</span></p>";
        if($("#contenedorInfoArticulo").is(":visible")){
            $("#contenedorInfoArticulo").html(precio);
        } else {
            $("#contenedorInfoArticulo").slideDown("fast");
            $("#contenedorInfoArticulo").html(precio);
        }
    });
    
    $("#articuloFactura").bind("blur", function(){
        if($("#contenedorInfoArticulo").is(":visible")){
            $("#contenedorInfoArticulo").slideUp("fast");
        }
    });
     
    //Evento enter sobre el campo de agregar articulos
    //Utilizado por el lector del codigo de barras
    $("#articuloFactura").on("keyup", function(e){
        e.preventDefault();
        var tecla = (document.all) ? e.keyCode : e.which; 
        
        if (tecla == "13") {
            
            var that = $(this);
            setTimeout(function(){

                if (that.attr("autocompletable") !== "true") {
                    var id          = that.val();

                    if (id == "") {
                        return;
                    }

                    var idBodega    = $("#selectorBodegas").val();
                    var destino     = "/ajax/articulos/listarArticulosVenta?extra="+idBodega+"&term="+id;

                    $.ajax({
                        type:"POST",
                        url: destino,
                        dataType:"json",
                        data: {},
                        success:function(data) {
                            setTimeout(function(){
                                $("ul.ui-autocomplete").css("display", "none"); 
                            },250);   

                            if (data.id == ""){
                                return false;
                            }

                            agregarItemListaArticulo(data[0]);

                        }
                    });
                }

            }, 75);

        }
    });
    
    
    /**
     * Funciones que se encargan de bloquear el boton derecho de los campos de texto
     **/
        $("input").live('contextmenu', function(e) {
        // evito que se ejecute el evento
            e.preventDefault();
        });
    
    
 
    /**
     * funciones que se encargan de verificar el maximo descuento autorizado a otorgar por un usuario e particular
     **/    
//    $("#campoDescuentoListadoArticulos").live("keypress", function(e){verificarTecla(e, "numeros");}).live("keyup change", function(e){
//
//        
//    });
 
    $(".descuentoGeneralArticuloVenta").live("keyup", function(){
        
        var dcto = $(this).val();
        
        
        if(dcto != '') {dcto = parseDouble(dcto);}
        
        var maxDcto = $("#dctoMaximo").val();
        
        if(maxDcto != '') {maxDcto = parseDouble(maxDcto);}
        
        var fila = $(this).parents(".filaArticuloVenta").attr("id");
        
        if(dcto > maxDcto){
            
            
            var precioU = $("#"+fila).find(".precioUnitarioArticuloVenta").val();
            if(precioU != ''){ 
                precioU = parseDouble(precioU);
                var dctoPesos = "% equivalente a "+ ( (precioU * dcto) / 100 )+"$ ";
                
            }
            

            var txtDcto = dcto+dctoPesos;
            
            $("#txtValDcto").html(txtDcto);
            $("#idDcto").val(dcto);
            $("#idFila").val(fila);
            $("#BoxOverlayTransparente").css("display","block");
            $("#contenedorValidarDescuento").fadeIn("fast");

        }        
        
    });    
    
    
  
    
    
    
    //funcion que cierra el cuadro de dialogo, y se encarga de borrar el valor del descuento que se intentaba aplicar
    $(".cerrarContenedorValidarDcto").bind("click", function(){
        
        var fila = $("#contenedorValidarDescuento").find("#idFila");//capturo el valor del id de la fila, para saber si es una fila en particular en la que se aplico el dcto, o fue en el campo de dcto genral
        
        if(fila.val() == "campoDescuentoListadoArticulos"){//aqui se habria tratado de aplicar el dcto sobre el campo general de dcto
            
            $("#campoDescuentoListadoArticulos").val("");//pongo el campo gral de dcto vacio
            
            $(".descuentoGeneralArticuloVenta").each(function(){//recorro todas las filas, para poner el campo dcto vacio y acomodar los atributos de la fila
                
                $(this).val("");//pongo el campo vacio
                
                var padre       = $(this).parents("tr.filaArticuloVenta"); //capturo la fila padre
                
                var precioBase  = padre.attr("precio_base");//capturo el campo precio base
                       
                var cantidad    = padre.find(".cantidadArticuloVenta");//capturo el campo precioU 

                var _subtotal   = parseDouble(precioBase * cantidad.val());

                var subtotal    = padre.find(".subtotalArticuloVenta");//capturo el campo subtotal
                
                subtotal.val(_subtotal);//le pongo al subtotal nuevamente el precioU
                
                padre.attr("subtotal", _subtotal);//modifico nuevamente el atributo subtotal de la fila
                
                
            }).promise().done(function(){
                Sexy.alert("El descuento no puede realizarse");
                
                reestablecerValoresIva();
            });
            
        } else {
           
            var obj = $("#"+fila.val());   
            
            obj.find(".descuentoGeneralArticuloVenta").val("");
            
            var precioBase = obj.attr("precio_base");//capturo el campo precioU de acuerdo con el precio base, para normalizarlo
            
            var subtotal    = obj.find(".subtotalArticuloVenta");//capturo el campo subtotal
            var precioU     = obj.find(".precioUnitarioArticuloVenta");//capturo el campo subtotal
            var cantidad    = obj.find(".cantidadArticuloVenta");//capturo el campo subtotal            
            
            precioU.val(precioBase);//le pongo al precio unitario nuevamente el precio base
            
            var _subtotal = parseDouble(precioBase * cantidad.val());
            
            subtotal.val(_subtotal);//le pongo al subtotal nuevamente el precio base            
            
            obj.attr("subtotal", _subtotal);//modifico nuevamente el atributo subtotal de la fila  
            
            obj.attr("precio", precioBase);//modifico nuevamente el atributo precio de la fila 
            
            Sexy.alert("El descuento no puede realizarse", {
                onComplete: function(){
                    reestablecerValoresIva(); 
                }
            });
 
        }
        
        $("#contenedorValidarDescuento").slideToggle("fast");
        $("#BoxOverlayTransparente").css("display","none");
        
        setTimeout(function(){
            calcularTotalFactura();
        }, 250);
        
    });    
    
    /**
     * Funcion ajax que se encarga de validar que las credenciales introducidas para autorizar
     * un descuento sean validas
     */
    $("#btnValidarDcto").live("click", function(e){
        e.preventDefault();
        
        if( $("#campoValidarDctoUsuario").val() == '' ||  $("#campoValidarDctoPassword").val() == ''){
            $("#campoValidarDctoUsuario").focus();
            
        } else {
            
            $("#BoxOverlayTransparente").css("display","block");
            
            var datos = {};
            var $contenedorValidarDescuento = $("#contenedorValidarDescuento");
            
            datos["usuario"]        = $contenedorValidarDescuento.find("#campoValidarDctoUsuario").val();
            datos["contrasena"]     = $contenedorValidarDescuento.find("#campoValidarDctoPassword").val();
            datos["dcto_maximo"]    = $contenedorValidarDescuento.find("#idDcto").val();
            datos["fila"]           = $contenedorValidarDescuento.find("#idFila").val();
            
            $.ajax({
                type:"POST",
                url:'/ajax/ventas_mercancia/validarPermisoDcto',
                data: { datos : datos},
                dataType:"json",
                success:procesaRespuestaDcto

            });            
            
            return false;              
            
        }
        
    });
    
    /**
     * Funcion encargada de validar la respuesta ajax que llega del servidor cada vez
     * que se trata de dar un descuento superior al autorizado y se introducen las 
     * credenciales de alguien que supuestamente esta autorizado a otorgasr dicho descuento.
     * Dependiendo de la respuesta ajax, se autoriza o se niega el descuento. Ver el metodo 
     * PHP en el servidor para mas info.
     * 
     **/
    function procesaRespuestaDcto(respuesta){
        
        var fila = respuesta.fila;//capturo el valor del id de la fila, para saber si es una fila en particular en la que se aplico el dcto, o fue en el campo de dcto genral
                
        if(respuesta.autorizar){
  
            $("#textoInfoDcto").html("Descuento autorizado").removeClass("letraRoja").addClass("letraVerde").fadeIn("fast");  
        
            if(fila == "#campoDescuentoListadoArticulos"){//aqui se habria tratado de aplicar el dcto sobre el campo general de dcto y se autoriza, se aumenta el dcto del usuario en esta factura a ese limite
                $("#dctoMaximo").val(respuesta.dcto);
            }            
            
            setTimeout(function(){
                $("#textoInfoDcto").html("");
                $("#BoxOverlayTransparente").css("display","none");
                $("#textoInfoDcto").addClass("oculto");
                $("#contenedorValidarDescuento").slideToggle("fast");
            }, 1500);
            
        } else {
            
//            $("#textoInfoDcto").html("Descuento NO autorizado").removeClass("letraVerde").addClass("letraRoja").fadeIn("fast");

            if(fila == "#campoDescuentoListadoArticulos"){//aqui se habria tratado de aplicar el dcto sobre el campo general de dcto
            
                $("#campoDescuentoListadoArticulos").val("");//pongo el campo gral de dcto vacio
            
                $(".descuentoGeneralArticuloVenta").each(function(){//recorro todas las filas, para poner el campo dcto vacio y acomodar los atributos de la fila
                
                    $(this).val("");//pongo el campo vacio
                    
                    var padre       = $(this).parents("tr.filaArticuloVenta"); //capturo la fila padre
                    
                    var precioBase   = padre.attr("precio_base");//capturo el campo precioU
                    
                    var cantidad    = padre.find(".cantidadArticuloVenta");//capturo el campo subtotal
                    
                    var subtotal    = padre.find(".subtotalArticuloVenta");//capturo el campo subtotal
                    
                    var _subtotal = parseDouble(cantidad.val() * precioBase);
                
                    subtotal.val(_subtotal);//le pongo al subtotal nuevamente el precioU
                    
                    padre.attr("subtotal", _subtotal);//modifico nuevamente el atributo subtotal de la fila
                
                }).promise().done(function(){
//                    Sexy.alert("El descuento no puede realizarse");
                });
            
            } else {
           
                var obj = $(fila); 
                
                obj.find(".descuentoGeneralArticuloVenta").val("");
                
                var precioBase   = obj.attr("precio_base");//capturo el campo precioU
                
                var cantidad    = obj.find(".cantidadArticuloVenta");//capturo el campo subtotal
                
                var subtotal    = obj.find(".subtotalArticuloVenta");//capturo el campo subtotal
                
                var _subtotal = parseDouble(cantidad.val() * precioBase);
                
                subtotal.val(_subtotal);//le pongo al subtotal nuevamente el precioU
                
                obj.attr("subtotal", _subtotal);//modifico nuevamente el atributo subtotal de la fil            

            
            }
            
        
            $("#contenedorValidarDescuento").slideToggle("fast");
            $("#BoxOverlayTransparente").css("display","none");
            $("#textoInfoDcto").addClass("oculto");
        
            setTimeout(function(){
                Sexy.alert("El usuario no tiene autorización para aplicar este descuento", {
                    onComplete: function(){
                        reestablecerValoresIva();
                    }
                });
                calcularTotalFactura();
            }, 250);       
            
        }
        
        $("#campoValidarDctoUsuario").val("");
        $("#campoValidarDctoPassword").val("");
        
    }
    /* fin de las funciones del calculo del descuento */   
    
    /*Funcion que es lanzada sobre el evento selected del plugin autocomplete sobre el
     *campo de seleccionar articulos en una factura, esto para el precio de venta*/
    $("#articuloFactura").bind( "autocompleteselect", function( event, ui) {  
        /**
         * Hubo un conflicto con el plugin de jquery y el lector del codigo de barras. El conflicto
         * se daba porque para agregar el item con el lector se capturaba el evento con la tecla enter,
         * y el autocomplete de jquery utiliza la tecla enter, asi que para solucionar esto, en este metodo
         * agregamos un atributo llamado autocompletable al campo de articuloFactura para saber si el evento 
         * keypress se lanza usando el plugin autocomplete. Asi mismo en el codigo encargado de capturar el
         * evento keypress lanzado por el codigo de barras se agregó un delay y un condicional para saber
         * si el evento venia del autocomplete o del lector.
         */
        $(this).attr("autocompletable", "true");
        
        agregarItemListaArticulo(ui.item);
        
        var that = $(this);
        
        setTimeout(function(){
            that.attr("autocompletable", "");
        }, 200);
        
    });   
        
    $("#campoIdCliente").bind( "autocompleteselect", function( event, ui) {      

        $("#campoOcultoIdCliente").val(parseInt(ui.item.value, 10));
        setTimeout(function(){
            $("#campoIdCliente").val('');
            $("#campoIdCliente").val(ui.item.nombre);
            $("#campoNumeroFacturaCliente").focus();
        }, 75);
           
    });      
    
    $("#imagenBuscarFactura").live("click", function(){
        buscarFactura(modulo);
    });
    
    $("#imagenAgregarVariosArticulos").live("click", function(){
//        agregarVariosArticulos(modulo);
        $("#BoxOverlayTransparente").css("display","block");
        $.ajax({
            type:"POST",
            url:"/ajax/articulos/verTodos",
            dataType:"json",
            success:procesaRespuesta

        });
    });    
    
    //abrir el formulario para buscar un catalogo
    $("#imagenBuscarCatalogo").live("click", function(){
        //        $("#indicadorEspera").css("display","block");
        $("#BoxOverlayTransparente").css("display","block");
        $.ajax({
            type:"POST",
            url:"/ajax/motos/buscarCatalogos",
            dataType:"json",
            success:procesaRespuesta
    
        });    
    });
    
    //abrir el formulario para buscar una cotizacion y cargarla
    $("#imagenCargarCotizacion").live("click", function(){
        //        $("#indicadorEspera").css("display","block");
        $("#BoxOverlayTransparente").css("display","block");
        $.ajax({
            type:"POST",
            url:"/ajax/ventas_mercancia/buscarCotizacion",
            dataType:"json",
            success:procesaRespuesta
    
        });    
    });    
   
    /*
     *Codigos que agregan via ajax los options con las bodegas
     *segun la sede que se seleccione
     **/
    $("#selectorSedes").live("change", function(){
        var sede = $("#selectorSedes").val();
        if(sede != ''){
            $.ajax({
                type:"POST",
                url:"/ajax/bodegas/escogerBodega",
                data: {
                    idSede : sede
                },
                dataType:"json",
                success:function (respuesta){
                    $("#selectorBodegas").html(respuesta.contenido);
                }

            });          
           
        }   
    });
    
    $("#selectorSedes2").live("change", function(){
        var sede = $("#selectorSedes2").val();
        if(sede != ''){
            $.ajax({
                type:"POST",
                url:"/ajax/bodegas/escogerBodega",
                data: {
                    idSede : sede
                },
                dataType:"json",
                success:function (respuesta){
                    $("#selectorBodegas2").html(respuesta.contenido);
                }

            });          
           
        }   
    });    
    
    /*
     * Codigo que le da el valor a el campo oculto id bodega
     * cada vez que se selecciona una bodega
     **/
    $("#selectorBodegas").live("change", function(){
        var bodega = $("#selectorBodegas").val();   
        $("#idBodegaGeneral").val(bodega);
        
        $(".filaArticuloVenta").each(function(){
            $(this).attr("bodega", bodega)
        });
        
        var ruta = $("#articuloFactura").attr("title")+"?extra="+bodega;
        
        $("#articuloFactura").attr("title", ruta)        
        
        armarCadenaDatosArticulos();

    });
       
    /**
     * Codigo para mostrar u ocultar los campos de fecha de vencimiento de factura
     **/
    $("#listaMedioPago").live("change", function(){
        //capturo el valor seleccionado en los select del tipo de pago (este seria el pajar)
        var haystack = $("#listaMedioPago option:selected").html();
        //declaro los valores a buscar en el pajar (serian las agujas)
        haystack = haystack.replace('é', 'e');//elimino las é para el mejor funcionamiento de la comparacion

        var needles = new Array('credito', 'credi');
        var needlesLength = needles.length;//el largo del arreglo
        
        var coincide = false;
        //recorro cada una de las agujas
        for(var i = 0; i< needlesLength; i++){
            coincide = (haystack.toLowerCase().indexOf(needles[i].toLowerCase()) !== -1) ? true : false;//y verifico si la encuentro en el pajar
        }
        
        //indexOf doest work in IE 8
        if(coincide){
            $(".campoFechaVtoFact").fadeIn("slow"); 
            $("#textoDctoExtra").fadeIn("slow");         
            
        } else {
            $(".campoFechaVtoFact").fadeOut("slow");
            $("#textoDctoExtra").fadeOut("slow");
        }
    });   
    
    /**
     * Codigo para al hacer click al texto + dcto por pronto pago se muestre el formulario para agregar los dctos
     **/
    $("#textoDctoExtra").live("click", function(){
        var visible = $(".contenedorDctoExtra").is(":visible");
        if(visible){ 
            $(".contenedorDctoExtra").slideUp("fast");         
        } else {
            $(".contenedorDctoExtra").slideDown("fast");
        }
    });     
    
    /* Funcion que cierra el contenedor de Dcto Extra   */
    $(".cerrarContenedorDctoExtra").bind("click", function(){
        $(".contenedorDctoExtra").slideToggle("fast");
    });
        
        
        
    /**
    * FUNCIONES QUE CALCULAN EL TOTAL DE LA FACTURA A MEDIDA QUE SE VAN ESCRIBIENDO VALORES
    */    


    //funcion que se encarga de ir calculando el subtotal a medida que se va ingresando la cantidad de articulos
    $(".cantidadArticuloVenta").live("keypress", function(e){verificarTecla(e, "numeros");}).live("keyup change", function(e){
        
        var padre   = $(this).parents(".filaArticuloVenta");//capturo el objeto parrafo padre, donde se encuentran los demas campos que hacen parte del art
        
        var precio  = padre.find(".precioUnitarioArticuloVenta").val();//capturo el precio unitario de ese articulo en especial       
        
        if (precio != '') {precio = parseDouble(precio);} //casting a entero
        
        var campoSubtotal = padre.find(".subtotalArticuloVenta");//capturo el subtotal
        
        var descuento     = padre.find(".descuentoGeneralArticuloVenta").val();//capturo el descuento que tenga el articulo
        
        if (descuento != '') {descuento = parseDouble(descuento);}//casting a entero
        
        var ivaArticulo   = padre.find(".ivaArticuloVenta");

        var ivaTotalArticulo   = padre.find(".ivaTotalArticuloVenta");        
        
        var cantidad    = 0;
        var subtotal    = 0;

        cantidad = $(this).val();
        
        if(cantidad == ''){cantidad = 0;}
        
        cantidad = parseDouble(cantidad);
        
        padre.attr("cantidad", cantidad);

        if(descuento === '' || descuento === 0 || descuento === "0"){
            subtotal = cantidad * precio;
            
        } else{
            precio = precio - ((precio * descuento) / 100);
            subtotal = cantidad * precio;
            
        }
        
        subtotal = parseDouble(subtotal);

        campoSubtotal.val(subtotal); 
        
        padre.attr("subtotal", subtotal);
        
        //agregar en pesos el total del iva
        ivaTotalArticulo.val( parseDouble( (subtotal * ivaArticulo.val() / 100) ) );        

        calcularTotalFactura();

    });      
    
    
    //Funciones para calcular el valor cuando se aplique el descuento general
   $("#campoDescuentoListadoArticulos").live("keypress", function(e){verificarTecla(e, "numeros");}).live("keyup change", function(e){

        var dcto = $(this).val();
        
        if(dcto != '') {dcto = parseDouble(dcto);}
        
        var maxDcto = $("#dctoMaximo").val();
        
        if(maxDcto != '') {maxDcto = parseDouble(maxDcto);}
        
 

        $(".descuentoGeneralArticuloVenta").val("");//se ponen todos los campos de descuento en vacio

        $(".descuentoGeneralArticuloVenta").val(dcto);//se les pone el valor de dicho descuento

        var proceder = true;

        $(".cantidadArticuloVenta").each(function(){//recorro los campo cantidad del listado de articulos
            if( $(this).val() === ""  ||  $(this).val() === "0" ||  $(this).val() === 0 ){//verifico que no haya ninguna cantidad vacia
                proceder = false;//si la hay, declaro proceder en false
                $(this).addClass("textFieldBordeRojo");//y pongo el campo vacio con un textfield rojo
            }
        });

        $(".precioUnitarioArticuloVenta").each(function(){//verifico que no hayan precios vacios
            if( $(this).val() === ""  ||  $(this).val() === "0" ||  $(this).val() === 0 ){
                proceder = false;
                $(this).addClass("textFieldBordeRojo");
            }
        });                  

        if(proceder){
            $(".descuentoGeneralArticuloVenta").each(function(){
                var obj = $(this);
                calcularSubtotalCampoDescuento(obj);

            }).promise().done(function (dcto, maxDcto){
                
                        var dcto = $("#campoDescuentoListadoArticulos").val();

                        if(dcto != '') {dcto = parseDouble(dcto);}

                        var maxDcto = $("#dctoMaximo").val();

                        if(maxDcto != '') {maxDcto = parseDouble(maxDcto);}                    

                        if(dcto > maxDcto){

                            var txtDcto = dcto+"% ";

                            $("#txtValDcto").html(txtDcto);
                            $("#idDcto").val(dcto);
                            $("#idFila").val("campoDescuentoListadoArticulos");

                            $("#BoxOverlayTransparente").css("display","block");
                            $("#contenedorValidarDescuento").fadeIn("fast");

                        }   

                        return true;
                            
            });

        } else {
            Sexy.alert("No puede haber campos cantidad o precio de unitario vacios", {
                onComplete: function(){ 

                    $(".descuentoGeneralArticuloVenta").val('');                       
                    $("#campoDescuentoListadoArticulos").val('');
                    $(".textFieldBordeRojo:first").focus();

                }
            });


            return;

        }               


    }).live("blur", function(e) {
        var valor = $(this).val();
        
        if (isNaN(valor) && !isFinite(valor)) {
            mostrarErrorCampo($(this));
        } else {
            verificarFormatoNumeroDecimal($(this));
        }
        
    });     
    

    
    /**
     *funcion que se encarga de ir calculando el subtotal a medida que se va ingresando el descuento a cada uno de los articulo
     */
    $(".descuentoGeneralArticuloVenta").live("keypress", function(e){verificarTecla(e, "numeros");}).live("keyup change", function(e){     
        var obj       = $(this);
        var proceder  = true;
        var padre     = obj.parents("tr.filaArticuloVenta"); //capturo el objDOM padre <p> para asi poder acceder a los hermanos del objeto campoDcto
        var precio    = padre.find(".precioUnitarioArticuloVenta"); //capturo el precio
        var cantidad  = padre.find(".cantidadArticuloVenta");//capturo el objDOM cantidad


        if(cantidad.val() === 0 || cantidad.val() === '' || cantidad.val() === '0'){//nuevamente se verifica que no hayan campos cantidad vacios
            cantidad.addClass("textFieldBordeRojo"); //de haber un campo cant vacio, se pone la clase de error     
            proceder = false; //proceder se pone el false
            setTimeout(function(){ //para que tome bien los cambios mientras procesa, se pone un tiempo de espera de 50 milisegundos
                obj.val(""); //se pone el campo dcto en vacio
                
            }, 50);

            //return;
            
        } 
        if(precio.val() === 0 || precio.val() === '' || precio.val() === '0'){//nuevamente se verifica que no hayan campos cantidad vacios
            precio.addClass("textFieldBordeRojo"); //de haber un campo cant vacio, se pone la clase de error   
            proceder = false;
            setTimeout(function(){
                obj.val("");
                
            }, 50);

            //return;
            
        }         
        
        if (proceder) {
            calcularSubtotalCampoDescuento(obj);
            
        } else {
            Sexy.alert("No puede haber campos cantidad o precio unitario vacios", { //se avisa al usuario con un alert
                onComplete: function(){ //una vez hace click en ok
                     obj.val('');//se ponen todos los campos de las filas de Dcto en vacio
                    $(".textFieldBordeRojo:first").focus();//y se pone el foco en el primer campo que se encuentre con la clase txtField BordeRojo

                }
            });            
            
            
       }

    }).live("blur", function(e) {
        var valor = $(this).val();
        
        if (isNaN(valor) && !isFinite(valor)) {
            mostrarErrorCampo($(this));
        }
        
    });    
    
    //codigo para deterner la funcion por defecto de la tecla enter sobre un formulario
    $("#formaVentasMercancias").find("input").on("keypress", function(e){
        var tecla = (document.all) ? e.keyCode : e.which; 
        if (tecla == "13") {
            e.preventDefault();
            return false;
        }
    });


    //funcion que se encarga de ir calculando el subtotal a medida que se va ingresando el precio del articulo
    $(".precioUnitarioArticuloVenta").live("blur",  function(e){
        
        var padre       = $(this).parents(".filaArticuloVenta");//capturar el padre <tr>        
        
        var descuento   = padre.find(".descuentoGeneralArticuloVenta");
        
        var precio      = $(this).val();

        if(precio == ''){precio = 0;}
        
        var precioBase = padre.attr("precio_base");
            precioBase = parseDouble(precioBase);
        
        var dineroDcto = precioBase - precio;

        //como se va a realizar una division, me aseguro que el precio base nunca sea cero
        if (precioBase <= 0) {
            precioBase = 1;
        }
        
        var porcentajeDcto = parseDouble( ( dineroDcto * 100) / precioBase ); 
        
        if (precioBase > precio) {
            descuento.val(porcentajeDcto);        
            validarMaximoDescuentoAutorizado(descuento);
            
        } 
        
    });
    
    //funcion que se encarga de ir calculando el subtotal a medida que se va ingresando el precio del articulo
    $(".precioUnitarioArticuloVenta").live("keypress", function(e){verificarTecla(e, "numeros");}).live("keyup change", function(e){
        
        var padre       = $(this).parents(".filaArticuloVenta");//capturar el padre <tr>        
        
        var descuento   = padre.find(".descuentoGeneralArticuloVenta");
        
        var precio      = $(this).val();

        if(precio == ''){precio = 0;}
        

        var cantidad = padre.find(".cantidadArticuloVenta").val();

        if (cantidad != '') {cantidad = parseInt(cantidad);}

        var campoSubtotal = padre.find(".subtotalArticuloVenta");

        if (descuento.val() != '') {
            descuento = parseDouble(descuento.val());

        } else {
            descuento = 0;

        }

        var ivaArticulo   = padre.find(".ivaArticuloVenta");

        var ivaTotalArticulo   = padre.find(".ivaTotalArticuloVenta");        


        var subtotal    = 0;

        precio = parseDouble(precio);

        padre.attr("precio", precio);

        if(descuento === '' || descuento === 0 || descuento === '0'){//si no hay valores en el campo porcentaje de descuento
            subtotal = cantidad * precio;

        } else{//pero si hay valor en el campo porcentaje de ganancia 
            subtotal = precio - ((precio * descuento) / 100);//se hace el calculo aplicando el porcentaje
            subtotal = cantidad * subtotal;

        }

        subtotal = parseDouble(subtotal);

        campoSubtotal.val(subtotal); //asigno el precio al subtotal

        padre.attr("subtotal", subtotal); //le pongo al <tr> padre el subtotal, pues es recorriendo todas las filas <tr> y tomando este atributo con lo que se calcula el total

        //agregar en pesos el total del iva
        ivaTotalArticulo.val( parseDouble( (subtotal * ivaArticulo.val() / 100) ) );

        calcularTotalFactura();            
        
    }).live("blur", function(e) {
        var valor = $(this).val();
        
        if (isNaN(valor) && !isFinite(valor)) {
            mostrarErrorCampo($(this));
        } else {
            verificarFormatoNumeroDecimal($(this));
        }
        
    });     
    
    //Funcion que se encarga de calcular el total cuando se escribe en el valor del flete
    $("#campoValorFlete").live("keypress", function(e){verificarTecla(e, "numeros");}).live("keyup change", function(e){
        var total       = 0;
        var subtotal    = 0;
        
        $(".filaArticuloVenta").each(function(){
            subtotal = subtotal + parseDouble($(this).attr("subtotal"));
            
        });
    
        total = parseDouble(subtotal);

        if(total > 0){
            var descuento1 = $("#campoDescuento1").val();
            
            if(descuento1 != ''){descuento1 = parseDouble(descuento1);}
            
            var descuento2 = $("#campoDescuento2").val();
            
            if(descuento2 != ''){descuento2 = parseDouble(descuento2);}
            
            var flete = 0;

            flete = $(this).val();//+flete;
            
            if(flete == ''){
                flete = 0;
                
            } else {
                flete = parseDouble(flete);
            }

            total = sumarFlete(flete, total);   
            
            //aqui podria ser necesario meter el sumar iva en caso de que los descuentos generales
            //apliquen a la factura total una vez sumado el iva

            total = aplicarDescuento(descuento1, total);    

            total = aplicarDescuento(descuento2, total); 
            
            total = parseDouble(total); 
            
            //pongo valor en el <span> subtotal
            $("#campo_subtotal").html("<span class='prefijo_numero'>$</span>"+total);
            
            $("#subtotal").val(total);            
            
            //aqui estariamos sumando el iva despues de aplicar los descuentos sobre el total
            
            sumarIva(total);              
 
        } else {
            total = "0";
            
        }
        
        total = parseDouble(total);
        
        $("#campoTotal").html("<span class='prefijo_numero'>$</span>"+total);
        
        $("#totalFactura").val(total);
        
        guardarFacturaTemporal();
         
    }).live("blur", function(e) {
        var valor = $(this).val();
        
        if (isNaN(valor) && !isFinite(valor)) {
            mostrarErrorCampo($(this));
        }
        
    });  
    
    
//    $("#campoIva").live("keypress", function(e){verificarTecla(e, "numeros");}).live("keyup change", function(e){
//
//        var total = 0;
//        var subtotal = 0;
//        $(".filaArticuloVenta").each(function(){
//            subtotal = subtotal + parseDouble($(this).attr("subtotal"));
//        });
//    
//        total = subtotal;
//
//        if(total > 0){
//            var descuento1 = $("#campoDescuento1").val();
//            
//            if(descuento1 != ''){descuento1 = parseDouble(descuento1);}
//            
//            var descuento2 = $("#campoDescuento2").val();
//            
//            if(descuento2 != ''){descuento2 = parseDouble(descuento2);}
//            
//            var flete = $("#campoValorFlete").val();
//            
//            if(flete != ''){flete = parseDouble(flete);}
//            
//            var iva = 0;
//
//            total = sumarFlete(flete, total);  
//            
//            total = aplicarDescuento(descuento1, total);
//
//            total = aplicarDescuento(descuento2, total); 
//            
//            //pongo valor en el <span> subtotal
//            $("#campo_subtotal").html("<span class='prefijo_numero'>$</span>"+total);
//            $("#subtotal").val(total);              
//            
//            iva = $(this).val();//+iva;
//            
//            if(iva == ''){iva = 0;}
//            
//            iva = parseDouble(iva);
//
//            total = sumarIva( total);            
//
//        } else {
//            total = "0";
//        }
//        
//        total = parseDouble(total);
//        
//        $("#campoTotal").html("<span class='prefijo_numero'>$</span>"+total);
//        
//        $("#totalFactura").val(total);
//        
//        guardarFacturaTemporal();
//         
//    });    
    
    
    // funcion que se encarga de ir calculando el total si se escribe en el campo id= descuento1
    $("#campoDescuento1").live("keypress", function(e){verificarTecla(e, "numeros");}).live("keyup change", function(e){

        var total       = 0;
        var subtotal    = 0;
        
        $(".filaArticuloVenta").each(function(){
            subtotal = subtotal + parseDouble($(this).attr("subtotal"));
        });
    
        total = subtotal;

        if(total > 0){
            var descuento1 = 0;
            
            var descuento2 = $("#campoDescuento2").val();
            
            if(descuento2 != ''){descuento2 = parseDouble(descuento2);}
            
            var flete = $("#campoValorFlete").val();
            
            if(flete != ''){flete = parseDouble(flete);}
            
            total = sumarFlete(flete, total);   
            
            descuento1 = $(this).val();
            
            if(descuento1 == ''){descuento1 = 0;}
            
            descuento1 = parseDouble(descuento1);

            total = aplicarDescuento(descuento1, total);

            total = aplicarDescuento(descuento2, total);     
            
            total = parseDouble(total);
            
            //pongo valor en el <span> subtotal
            //$("#campo_subtotal").html("<span class='prefijo_numero'>$</span>"+total);
            //$("#subtotal").val(total);             
      
            sumarIva(total);  //estamos es sumando el iva despues de aplicarle los descuentos a la factura               

        } else {
            total = "0";
            
        }
        
        total = parseDouble(total);
        
        $("#campoTotal").html("<span class='prefijo_numero'>$</span>"+total);
        
        $("#totalFactura").val(total);
        
        guardarFacturaTemporal();
         
    }).live("blur", function(e) {
        var valor = $(this).val();
        
        if (isNaN(valor) && !isFinite(valor)) {
            mostrarErrorCampo($(this));
        }
        
    });  
    
        
    // funcion que se encarga de ir calculando el total si se escribe en el campo id= descuento1
    $("#campoDescuento2").live("keypress", function(e){verificarTecla(e, "numeros");}).live("keyup change", function(e){
        
        var total       = 0;
        var subtotal    = 0;
        
        $(".filaArticuloVenta").each(function(){
            subtotal = subtotal + parseDouble($(this).attr("subtotal"));
            
        });
    
        total = subtotal;
        
        if(total > 0){
            var descuento1 = $("#campoDescuento1").val();
            
            if(descuento1 != ''){descuento1 = parseDouble(descuento1);}
            
            var descuento2 = 0;
            
            var flete = $("#campoValorFlete").val();
            
            if(flete != ''){flete = parseDouble(flete);}

            total = sumarFlete(flete, total);   
            
            total = aplicarDescuento(descuento1, total);
            
            descuento2 = $(this).val();//+descuento2;  
            
            if(descuento2 == ''){descuento2 = 0;}
            
            descuento2 = parseDouble(descuento2);

            total = aplicarDescuento(descuento2, total);
            
            total = parseDouble(total);
            
            //pongo valor en el <span> subtotal
            //$("#campo_subtotal").html("<span class='prefijo_numero'>$</span>"+total);
            //$("#subtotal").val(total);      
            
            
            sumarIva(total);              

        } else {
            total = "0";
            
        }
        
        total = parseDouble(total);
        
        $("#campoTotal").html("<span class='prefijo_numero'>$</span>"+total);
        
        $("#totalFactura").val(total);
        
        guardarFacturaTemporal();
         
    }).live("blur", function(e) {
        var valor = $(this).val();
        
        if (isNaN(valor) && !isFinite(valor)) {
            mostrarErrorCampo($(this));
        }
        
    });  
    
       
    $("#imagenEliminarTodosArticulos").click(function(){
        resetFactura();
        
    });        
    
    /**
     * Función que agrega el plugin chosen ver 
     **/
    $(".selectChosen").chosen({no_results_text: "Oops, sin resultados!"});     

     
});//fin del document ready
 
 
 
 
//Funcion que se encarga de agregar un articulo a la lista de venta, cada vez que se selecciona un
//articulo del campo Articulo del formulario, y se hace click en el boton agregar



//Todos los campos autocompletables deben tener un atributo llamado verificacion, que contendrá la ruta de verificacion
//del item en la BD, tambien habrá un metodo para los campos autocompletables (esta por definirse si en el on blur, o en el onselect)
//en este evento, se captura la ruta de verificacion y se hace la consulta y sus respectivos cambios en caso de no ser valido



function cerrarAyuda(){
    $("#BoxOverlay").css("display","none");
    $("#contenedorAyudaUsuario").slideUp("slow");
    
}

//function agregarVariosArticulos(){
//    //    $("#indicadorEspera").css("display","block");
//
//}


function buscarFactura(modulo){
    if(modulo == "Ventas_mercancia"){
        //        $("#indicadorEspera").css("display","block");
        $("#BoxOverlayTransparente").css("display","block");
        $.ajax({
            type:"POST",
            url:"/ajax/ventas_mercancia/buscarFactura",
            dataType:"json",
            success:procesaRespuesta
    
        });
    }
}


// FINALIZAR LA FACTURA DE VENTA
$("#botonFinalizarFactura").bind("click", function(e){
    e.preventDefault();
    //bloquear el boton finalizar factura (para que solo se muestre la ventana modal una vez)
    $boton = $(this);
    $boton.attr("disabled", "disabled"); 

    var cantArticulosVenta = $(".filaArticuloVenta");
    
    var total = $("#totalFactura").val();
        
    if(cantArticulosVenta.length <= 0){
       /**
        * Verificar que hayan articulos en la factura
        **/
        Sexy.alert("Debes al menos ingresar un articulo para generar una venta");
        $boton.removeAttr("disabled");
        return;        
    } else if (total <= 0) {
        /**
        * Verificar que la factura tenga vaores adecuados
        **/
        Sexy.alert("El total de la factura debe de ser mayor a 0 para poder facturar");     
        $boton.removeAttr("disabled");
        return;
        
    } else{
        /**
        * Si no se cumplen ninguna de las condiciones superiore, se envia la factura para ser facturada
        **/
        $("#BoxOverlayTransparente").css("display","block");

        formulario  =   $("#botonFinalizarFactura").parents("form");
        destino     =   $(formulario).attr("action");    

        $(formulario).ajaxForm({
            dataType:"json"
        });
        $(formulario).ajaxSubmit({
            dataType:"json",
            success:procesaRespuesta
        });
        
        setTimeout(function(){
            $boton.removeAttr("disabled");
        },2000);
    
        return false;        
    }    
 
});









function guardarFacturaTemporal(){
    
    
    var idFacturaTemporal = $("#idFacturaTemporal").val();//capturo el id de la factura temporal (campo oculto en el formulario)
    
    var ruta = 'guardarFacturaTemporal';//la primera intencion será guardar la factura temporal
    
    if(idFacturaTemporal != ''){//sucede que cuando se guarda la primera vez la factura temporal, el procesa respuesta pone el $sql->ultimoId como valor a la variable idFacturaTemporal
        //esto quiere decir que ya se agrego la factura temporal, y que ahora debe ser modificada, tambien se tendrá en cuenta este id para eliminar la factura temporal
        ruta = 'modificarFacturaTemporal';
    }
    
    setTimeout(function(){

    
        formulario=$("#botonFinalizarFactura").parents("form");
        destino= '/ajax/ventas_mercancia/'+ruta;    


        $(formulario).ajaxForm({
            dataType:"json",
            url: destino
        });
        $(formulario).ajaxSubmit({
            dataType:"json",
            url: destino,
            success:function(respuesta){
                $("#idFacturaTemporal").val(respuesta.codigo);
            }
        });
        return false;

    }, 250);
    
    
}



/**
 * Funcion que se encarga de calcular el subtotal de una fila cuando se coloca un descuento
 */
function calcularSubtotalCampoDescuento(obj){//proceder = boleano que determina si debe llamar a la funcion de validar maximo descuento
    
//    if (!proceder) {
//        proceder = validarMaximoDescuentoAutorizado(obj);
//    }
    
//    if (proceder) {
        var padre  = obj.parents(".filaArticuloVenta"); //capturo el objDOM padre <tr> para asi poder acceder a los hermanos del objeto campoDcto

        var precio = padre.find(".precioUnitarioArticuloVenta").val(); //capturo el precio

        if (precio == '') {precio = 0; } else {precio = parseDouble(precio); } //casting a entero

        var campoSubtotal = padre.find(".subtotalArticuloVenta");//capturo el objDOM campo del subtotal

        var cantidad      = padre.find(".cantidadArticuloVenta");//capturo el objDOM cantidad

        var ivaArticulo   = padre.find(".ivaArticuloVenta");

        var ivaTotalArticulo   = padre.find(".ivaTotalArticuloVenta");

        var descuento = 0;
        var subtotal  = 0;

        descuento = obj.val();//capturo el descuento

        if(descuento == ''){descuento = 0;}

        descuento = parseDouble(descuento);//casting a entero            

        padre.attr("descuento", descuento);//al padre, osea a la etiqueta <tr> dentro de la cual se encuentra el campo, pongale el atributo "descuento" con el valor del dcto

        if(cantidad.val() === '' || cantidad.val() === 0 || cantidad.val() === '0'){
            subtotal = 0;

        } else{
            precio = precio - ((precio * descuento) / 100);
            subtotal = cantidad.val() * precio;

        }

        subtotal = parseDouble(subtotal);

        campoSubtotal.val(subtotal);  

        padre.attr("subtotal", subtotal);

        //agregar en pesos el total del iva
        ivaTotalArticulo.val( parseDouble( (subtotal * ivaArticulo.val() / 100) ) );

        calcularTotalFactura();     
        
        
        validarMaximoDescuentoAutorizado(obj);
//    }
  
    
}




//Funcion que calcula el total de la factura cuando hago algun cambio sobre un campo del listado de articulos
function calcularTotalFactura(){
    
    var total       = 0;
    var subtotal    = 0;
    
    $(".filaArticuloVenta").each(function(){//va recorriendo cada fila de articulo y sumando el valor del subtotal
        subtotal = subtotal + parseDouble($(this).attr("subtotal"));
        
    });
    
    total = parseDouble(subtotal);

    if(total > 0){
        
        var flete = $("#campoValorFlete").val();
        
        total = sumarFlete(flete, total);   
    
        var descuento1 = $("#campoDescuento1").val();
        
        if(descuento1 == ''){descuento1 = 0;}
        
        descuento1 = parseDouble(descuento1);        
        
        total = aplicarDescuento(descuento1, total);
    
        var descuento2 = $("#campoDescuento2").val();
        
        if(descuento2 == ''){descuento2 = 0;}
        
        descuento2 = parseDouble(descuento2);   
        
        total = aplicarDescuento(descuento2, total);
        
        total = parseDouble(total);
        
        //pongo valor en el <span> subtotal
        $("#campo_subtotal").html("<span class='prefijo_numero'>$</span>"+total);
        $("#subtotal").val(total);         
        
        sumarIva(total);        
        
        
    } else {
        total = "0";
        
    }
    
   
    total = parseDouble(total);
        
    $("#campoTotal").html("<span class='prefijo_numero'>$</span>"+total);
    
    $("#totalFactura").val(total);
    
    armarCadenaDatosArticulos();
    
    guardarFacturaTemporal();
}

//Funcion que se encarga de sumar el valor del flete al total de la factura
function sumarFlete(flete, total){
    
    //determina si se va a cobrar iva por el flete y su valor
    //var porcentajeIvaFlete = $("#porcentajeIvaFleteVentas").val();
    
    if(flete != 0 && flete != "0" && flete != ""){
        total = total + parseDouble(flete);

    }
    
    return total;
    
}

//funcion que se encarga de sumar el valor del iva al total de la factura
function sumarIva(){
    
    if ($("#regimenEmpresa").val() != "1"){
        var totalIva = 0;

        $(".filaArticuloVenta").each(function(){//va recorriendo cada fila de articulo y sumando el valor del subtotal
            var iva = $(this).find(".ivaTotalArticuloVenta").val();

            totalIva += parseDouble(iva);

        });

        totalIva = parseDouble(totalIva);    

       //var valorFlete = parseDouble( $("#campoValorFlete").val() );

        //if(valorFlete){
            //var porcentajeIvaFlete = $("#porcentajeIvaFleteVentas").val();
            //como el iva va incluido se saca del valor total del flete segun su porcentaje
            //var totalIvaFlete = valorFlete / (1 + ((porcentajeIvaFlete/ 100)));
                
            //totalIva = totalIva + totalIvaFlete;
       // }     

        $("#campoIva").val(parseDouble(totalIva));
        $("#campoOcultoIva").val(parseDouble(totalIva));
        
    }
    
}

//funcion que se encarga de aplicar descuento sobre el total de la factura
function aplicarDescuento(descuento, total){
    if(descuento != 0 && descuento != "0" && descuento != ""){
        total = total - ( (total * descuento) / 100);
    }
    return total;
}



function armarCadenaDatosArticulos(){
    
    var cadenaDatosArticulos = '';
    
    $(".filaArticuloVenta").each(function(){//va recorriendo cada fila de articulo y sumando el valor del subtotal
        
        var id = $(this).attr("cod");
        
        id = parseInt(id,10);
        
        var cantidad = $(this).attr("cantidad");
        if(cantidad == '') cantidad = 0;
        
        var descuento = $(this).attr("descuento");
        if(descuento == '') descuento = 0;
        
        var precio = $(this).attr("precio");
        
        var bodega = $(this).attr("bodega");
        
        bodega = parseInt(bodega,10);
        
        var iva = $(this).attr("iva");
        
        iva = parseDouble(iva);        
        
        cadenaDatosArticulos += id+";"+cantidad+";"+descuento+";"+precio+";"+bodega+";"+iva+"|";
        
    });
      
    $("#cadenaArticulosPrecios").val(cadenaDatosArticulos);
}


/**
 * Funcion encargada de generar cada una de las filas con la informacion del articulo
 * que se va agregando en el listado de articulos de una factura de venta
 * 
 * @param {type} datos
 * @returns {String}
 */
function generarFilaArticulo(datos){
    
    var ivaTotal = 0;
    
    if (datos['iva'] > 0) {
        ivaTotal = parseDouble( ( datos['precio'] * datos['iva'] ) / 100 );
    }
    
    var cantidad = 1;
    if(datos["cantidad"]) {
        cantidad = datos["cantidad"];
    }    
    
    //verificar si hay un descuento establecido, y agregar los articulos con ese descuento
    
    var fila = "";
        fila +=  "<tr id='fila_"+datos['contadorArticuloVenta']+"' cod = '"+datos['id']+"' bodega = '"+datos['bodega']+"' iva = '"+datos['iva']+"' descuento = '"+datos['descuento']+"' cantidad = '1' precio='"+datos['precio']+"' precio_base='"+datos['precio']+"' subtotal = '"+datos['subtotal']+"' class='filaArticuloVenta'>";
            fila +=  "<td><input type='text' disabled id='articuloFactura_"+datos['contadorArticuloVenta']+"' value='"+datos['articulo']+"' class='campoDescripcionArticulo margenIzquierda medioMargenSuperior' maxlength='255' size='50' name=''></td>";
            fila +=  "<td><input class=' soloNumeros cantidadArticuloVenta ' size='3' value = '"+cantidad+"' /></td>";
            fila +=  "<td><input class=' soloNumeros descuentoGeneralArticuloVenta  rangoNumeros campoPorcentaje' value = '"+datos['descuento']+"' rango='1-99' size='3' /></td>";
            fila +=  "<td><input class=' soloNumeros precioUnitarioArticuloVenta  campoDinero' size='10' value = '"+datos['precio']+"' /></td>";
            
            if ($("#regimenEmpresa").val() != "1"){
                fila +=  "<td><input class=' soloNumeros ivaArticuloVenta campoDisabledConColor campoPorcentaje' disabled='disabled' size='5' value = '"+datos['iva']+"' /></td>";
                fila +=  "<td><input class=' soloNumeros ivaTotalArticuloVenta campoDisabledConColor campoDinero' disabled='disabled' size='10' value = '"+ivaTotal+"' /></td>";
            }
    
            fila +=  "<td><input disabled class=' subtotalArticuloVenta campoDisabledConColor  campoDinero' size='10' value = '"+datos['subtotal']+"' /></td>";
            fila +=  "<td><img src='media/estilos/imagenes/eliminar.png' ayuda='Eliminar este articulo<br>del listado' class='imagenEliminarItemFila cursorManito margenIzquierdaDoble' id='imagenEliminarItemFila"+datos['contadorArticuloVenta']+"' /></td>";
            fila +=  "<td><img src='media/estilos/imagenes/consultar.png' ayuda='Consultar articulo' class='imagenConsultarItemFila cursorManito margenIzquierdaDoble' id='imagenConsultarItemFila"+datos['contadorArticuloVenta']+"' /></td>"; 
        fila +=  "</tr>";
        
    return fila;

}


/**
 * Funcion que se encarga e verificar que un descuento asignado (ya sea en el campo descuento
 * o en el campo precio unitario) no supere el maximo permitido por un usuario en particular
 * 
 * valor = el valor del descuneto
 * tipo  = determina si es porcentaje o en pesos (si es en pesos se hace el calculo para pasarlo a porcentaje)
 */
function validarMaximoDescuentoAutorizado(obj) {
    
    var valDcto     = obj.val();  
    
    var padre       = obj.parents(".filaArticuloVenta");
    
    var precioUnitario      = padre.find(".precioUnitarioArticuloVenta").val();  
    precioUnitario          = parseDouble(precioUnitario);
    
    var precioBase = padre.attr("precio_base");

    var diferenciaDinero = parseDouble( ( valDcto / 100) * precioBase );
    
    //capturo el maximo descuento autorizado
    var maxDcto = $("#dctoMaximo").val();

    if(maxDcto != '') {maxDcto = parseDouble(maxDcto); }    
    
    //capturo el maximo descuento para este campo en particular
    var maxDctoCampo = $(this).attr("max-dcto");
    
    if (maxDctoCampo && valDcto <= maxDctoCampo) { //si ya se ha autorizado un descuento para este campo en particular y el que se quiere aplicar es menor
        return true;
        
    } else if (valDcto > maxDcto) {
        
        var textoDcto = valDcto+"% equivalente a "+diferenciaDinero+"$ ";

        $("#txtValDcto").html(textoDcto);
        $("#idDcto").val(valDcto);
        $("#idFila").val(padre.attr("id"));
        $("#BoxOverlayTransparente").css("display","block");
        $("#contenedorValidarDescuento").fadeIn("fast");        
        
        return;

    }
    
    return true;
    
}

/**
 * Funcion que se encarga de recorrer todas las filas
 * y calcular el precio del iva de acuerdo al valor del subtotal del articulo
 */
function reestablecerValoresIva(){
    
    var _ivaTotal = 0;
    
    
    $(".filaArticuloVenta").each(function(){//va recorriendo cada fila de articulo y sumando el valor del subtotal

        var _ivaArticulo         = 0;        
            _ivaArticulo         = $(this).find(".ivaArticuloVenta").val();

        if (_ivaArticulo != '') {
            _ivaArticulo = parseDouble(_ivaArticulo);
        }

        var $ivaTotalArticulo    = $(this).find(".ivaTotalArticuloVenta");

        var _subtotal            = $(this).attr("subtotal");

        var _ivaFila = _subtotal * (_ivaArticulo / 100);

        if(_ivaFila != '') {
            _ivaFila = parseDouble(_ivaFila);
        }

         $ivaTotalArticulo.val(_ivaFila);

         _ivaTotal += _ivaFila;        

    });        

    
    var _valorFlete  = $("#campoValorFlete").val();
    
    var _ivaGeneral  = $("#porcentajeIvaFleteVentas").val();
    
    var _ivaFlete = _valorFlete * (_ivaGeneral / 100);
    
    _ivaTotal += _ivaFlete;
    
    _ivaTotal = parseDouble(_ivaTotal);
    
    $("#campoIva").val(_ivaTotal);    
    
}


/**
 * funcion encargada de poner en blanco la factura
 */
function resetFactura(){
    $(".filaArticuloVenta").remove();
        
    $("#cadenaArticulosPrecios").val("");

    $("#campoTotal").html("<span class='prefijo_numero'>$</span>"+"0");
    $("#totalFactura").val('');

    $("#campo_subtotal").html("<span class='prefijo_numero'>$</span>"+"0");  
    $("#subtotal").val('');

    $("#campoValorFlete").val("");

    $("#campoIva").val("");

    $("#campoConcepto1").val("");

    $("#campoDescuento1").val("");

    $("#campoConcepto2").val("");

    $("#campoDescuento2").val("");       

    $("#campoObservaciones").val("");

    guardarFacturaTemporal();    
}


/**
 * Funcion encargada de crear el objeto "articulo" para ser agregado a la lista
 * 
 * @param object _obj   objeto que contiene toda la informacion del articulo, i.e=
 *                      Object { id="9291", iva="17", label="14224 :: +ESPEJO AKT EVO II # 10 JUE (M)", value="1500"}
 * @returns false para terminar la ejecucion
 */
function agregarItemListaArticulo(_obj) {
    $("#BoxOverlayTransparente").css("display","block");
        
        if($("#contenedorInfoArticulo").is(":visible")){
            $("#contenedorInfoArticulo").slideUp("fast");
            
        }      
        
        if (typeof(_obj) == "undefined") {
            $("#BoxOverlayTransparente").css("display","none");
            return;
        }
        
        var descuento = $("#campoDescuentoListadoArticulos").val();
        
        var bodega      = $("#idBodegaGeneral").val();           
        var articulo    = _obj.label;
        var precio      = parseDouble(_obj.value);
        var id          = parseInt(_obj.id, 10);
        var iva         = parseDouble(_obj.iva);
               
        var subtotal    = precio;
        
        if(descuento != ''){
            descuento   = parseDouble(descuento);
            
            subtotal    = ( descuento * precio ) / 100;
            subtotal    = precio - subtotal;
            
        } else {
            descuento = 0;
            
        }         
            
        var existeEnListado = false;
        
        var idFila = '';
        
        $(".filaArticuloVenta").each(function(){
            idFila = $(this).attr("cod");
            
            if(idFila == id){
                existeEnListado = true;
            }
            
        });
            
        if(existeEnListado){
            Sexy.confirm("<p class=margin5>El articulo que deseas agregar ya se encuentra en el listado. Esta seguro de querer agregar otro?</span> </p>", {
                onComplete: function(returnvalue){ 
                    if(!returnvalue){
                        $("#BoxOverlayTransparente").css("display","none"); 
                        $("#articuloFactura").val("");
                        $("#articuloFactura").focus();                          
                        return;
                    } else {
                        
                        contadorArticuloVenta ++;

                        var datos = new Array();//arreglo con la informacion del articulo para armar la fila

                        datos['contadorArticuloVenta']  = contadorArticuloVenta;
                        datos['id']                     = parseInt(id, 10);
                        datos['iva']                    = iva;
                        datos['precio']                 = parseDouble(precio);
                        datos['subtotal']               = parseDouble(subtotal);
                        datos['bodega']                 = parseInt(bodega, 10);
                        datos['articulo']               = articulo;
                        datos['descuento']              = descuento;                           

                        $("#tablaListaArticulosFactura").find("#thead").after(generarFilaArticulo(datos));
                        armarCadenaDatosArticulos();

                        setTimeout(function(){
                            calcularTotalFactura();
                            $("#articuloFactura").val("");
                            $("#articuloFactura").focus();
                        }, 250);    

                        $("#BoxOverlayTransparente").css("display","none");      

                        $('*').tooltip({
                            track: true,
                            delay: 0,
                            showURL: false
                        });

                        return false;                        
                        
                    }
                        
                }
            });
            
            $("#BoxOverlayTransparente").css("display","none");     
            
        } else {
            contadorArticuloVenta ++;
            
            var datos = new Array();//arreglo con la informacion del articulo para armar la fila
            
            datos['contadorArticuloVenta']  = contadorArticuloVenta;
            datos['id']                     = parseInt(id, 10);
            datos['iva']                    = iva;
            datos['precio']                 = parseDouble(precio);
            datos['subtotal']               = parseDouble(subtotal);
            datos['bodega']                 = parseInt(bodega, 10);
            datos['articulo']               = articulo;
            datos['descuento']              = descuento;                           

            $("#tablaListaArticulosFactura").find("#thead").after(generarFilaArticulo(datos));
            armarCadenaDatosArticulos();

            setTimeout(function(){
                calcularTotalFactura();
                $("#articuloFactura").val("");
                $("#articuloFactura").focus();
            }, 250);    
  
            $("#BoxOverlayTransparente").css("display","none");      
            
            $('*').tooltip({
                track: true,
                delay: 0,
                showURL: false
            });

            return false;            
            
        }
}
