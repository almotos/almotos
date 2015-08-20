var contadorArticuloCompra = 0;
  
$(document).ready(function(){

    //ayuda a chosen selects
    setTimeout(function(){
        
        //ADICIONAR EL PASOS DEL TUTORIAL DINAMICAMENTE, YA QUE ERA MUCHO MAS COMPLEJO (O IMPOSIBLE, EJEMPLO EN LOS SELECT CON CLASE ".CHOSEN") 
        //HACERLO DESDE EL PHP
        $("#selectorProveedores_chzn").next().attr("data-step", "2");
        $("#selectorProveedores_chzn").next().attr("data-intro", "Agregar Proveedor: En caso de que el proveedor no se encuentre registrado \n\
        en el sistema, lo puedes registrar desde aquí.");        

        //Selector de proveedores
        $("#selectorProveedores_chzn").attr("data-step", "1");
        $("#selectorProveedores_chzn").attr("data-intro", "Seleccione el proveedor al que se le va a realizar la compra."); 
        
        //selector bodegas
        $("#selectorBodegas_chzn").attr("data-step", "5");
        $("#selectorBodegas_chzn").attr("data-intro", "Bodegas: seleccione la bodega general a la cual va a ingresar esta mercancia.\n\
         Recuerda que desde el listado de articulos existe la opción de cambiar la bodega a la cual ingresará un determinado articulo."); 
        
        //selector cajas
        $("#selectorCaja_chzn").attr("data-step", "6");
        $("#selectorCaja_chzn").attr("data-intro", "Cajas: seleccione la caja en la cual va a realizar esta compra.");  
        
        //campo agregar artiulos
        $("#articuloFactura").next().next().attr("data-step", "11");
        $("#articuloFactura").next().next().attr("data-intro", "Agregar articulo: Si el articulo a ser comprado no existe\n\
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
            if(modulo == "Compras_mercancia"){
                agregarVariosArticulos(modulo);
            }
            return false;
        }
        if(ev.which == 13 && isCtrl == true) {//Ctrl + Enter
            if(modulo == "Compras_mercancia"){ 
                $("#botonFinalizarFactura").trigger("click");
            }
            return false;
        }    
        
        if(ev.which == 73 && isCtrl == true) {//Ctrl + I //imprimir factura pdf
            
            if(modulo == "Compras_mercancia"){
                
                $("#btnImprimirFacturaPdf").trigger("click");
            }
            
            return false;
            
        }     
        
        if(ev.which == 80 && isCtrl == true) {//Ctrl + P //imprimir factura pos
            if(modulo == "Compras_mercancia"){
                
                $("#btnImprimirFacturaPos").trigger("click");
            }
            return false;
        }   
        
        if(ev.which == 79 && isCtrl == true) {//Ctrl + O //Generar orden de compra
            if(modulo == "Compras_mercancia"){
                
                $("#btnGenerarOrdenCompra").trigger("click");
            }
            return false;
        }     
        
        if(ev.which == 81 && isCtrl == true) {//Ctrl + Q //Cancelar acción Factura
            if(modulo == "Compras_mercancia"){
                
                $("#botonCancelarAccionFactura").trigger("click");
            }
            return false;
        }          
        
        if(ev.which == 113 && isCtrl == false) {//Tecla F2  Buscar factura
            buscarFactura(modulo);
            return false;
        }
        
        if($("#tablaRegistros").is(":visible") ){
            
            if($("#tablaRegistros").is(":visible") && ev.which == 38){//Ctrl + flecha arriba 
                ev.preventDefault();
                filaSeleccionada = false;
                var filaMarcada1 = $(".filaTablaSeleccionarItem");
                if(filaMarcada1.length <= 0){
                    $("#tablaArticulosCompra tr:last").addClass("filaTablaSeleccionarItem");
                }
                
                var pre = filaMarcada1.prev("tr:not(.noSeleccionable)");
                if(pre.length > 0){
                    filaMarcada1.removeClass("filaTablaSeleccionarItem");
                    pre.addClass("filaTablaSeleccionarItem");                   
                    
                }
                return false;
            }
            
            if($("#tablaRegistros").is(":visible") && ev.which == 40) {//Ctrl + flecha abajo      aqui tendria que verificar si esta marcada y desmarcarla 
                ev.preventDefault();
                filaSeleccionada = false;
                var filaMarcada = $(".filaTablaSeleccionarItem");
                if(filaMarcada.length <= 0){
                    $("#tablaArticulosCompra tr:first").addClass("filaTablaSeleccionarItem");
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
        
        }//fin de si tabla articulos compra es visible

        
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
         * Funcion que se encarga de agregar los articulos seleccionados para la compra cuando
         * se llama a la tabla que muestra varios articulos y se seleccionan varios
         *
         **/
         $("#imagenAdicionarVariosArticulos").live("click", function(){
                $("#BoxOverlayTransparente").css("display","block");

                var bodega = $("#idBodegaGeneral").val();
                
                var descuento = $("#campoDescuentoListadoArticulos").val();
                if(descuento != ''){
                    descuento = parseDouble(descuento);
                }
                
                var ganancia_venta = $("#campoGananciaListadoArticulos").val();
                
                if(ganancia_venta != ''){
                    ganancia_venta  = parseDouble(ganancia_venta);
                    
                } else {
                    ganancia_venta = 0;
                    
                }
                
                var porcPredGanancia = $("#porcPredGanancia").val();
                
                porcPredGanancia = parseDouble(porcPredGanancia);                
                    
                    var fila = "";
                    
                    $(".filaConsultada").each(function(i){//se recorren todas las filas marcadas para armar el listado de articulos

                        //en la tabla que muestra los articulos existen unos atributos que almacenan la
                        //informacion relevante del articulo para poder generar con esta la fila del
                        //articulo
                        var articulo = $(this).attr("atributo_1");//aqui se van capturando ;D    
                            articulo = articulo.split(":");
                            articulo = articulo[0];
                            
                        var cantidad = $(this).find(".campo-cantidad-articulo").val();
                              
                        var precio   = $(this).attr("atributo_4");
                        
                        precio  = (precio != '') ? parseDouble(precio) : precio  = 0;
                        
                        var id       = $(this).attr("id");
                            id       = id.split("_");
                            id       = id[1];
                        
                        id = parseInt(id, 10);
                        
                        var iva = $(this).attr("atributo_5");
                        
//                        var valPredIva = (iva != 0) ? iva : $("#valorIva").val();
                        var valPredIva = iva;
                            valPredIva = parseInt(valPredIva); 
   
                        var subtotal = precio;
                       
                        if(descuento != ''){
                            subtotal =  precio * ( descuento / 100 );
                            subtotal =  parseDouble(precio - subtotal);
                        }    
                                              
                        //validar si el proveedor va a vender con iva
                        if ($("#regimenProveedor").val() != 1) {
                            subtotal += (subtotal * (valPredIva / 100));
            
                        }
                        
                        subtotal = subtotal * cantidad;
                     
                        var precioVenta = 0;                        
                            
                        if(precio != '' && ganancia_venta != ''){
                            var cobroIva  = precio * ( valPredIva / 100 ); 
                            var precioIva = parseDouble (precio + cobroIva);
                            var ganancia  = precioIva * ( ganancia_venta / 100 ); 
                            
                            //hack
                            precioVenta = parseDouble(precioIva + ganancia);
                            //precioVenta = parseDouble(precio + ganancia);
                            
                        } else if(precio != '' && ganancia_venta == ''){
                            var cobroIva = precio * ( valPredIva / 100 ); 
                            
                            precioVenta = parseDouble(precio + cobroIva);
                            
                        } else {
                            precioVenta = 0;
                            
                        } 

                        //se verifica que el articulo que se quiere ingresar no se
                        //encuentra ya en el listado  //creo que se puede remplazar por el largo de $(".filaArticuloCompra").has('[cod=id]')
                        var existeEnListado = false;
                        var idFila = '';
                        
                        $(".filaArticuloCompra").each(function(){
                            idFila = $(this).attr("cod");
                            
                            if(idFila == id){
                                existeEnListado = true;
                            }
                            
                        });
                        
                        if(!existeEnListado){
                            contadorArticuloCompra ++;                            
    
    
                            var datos = new Array();//arreglo con la informacion del articulo para armar la fila
                            datos['contadorArticuloCompra'] = contadorArticuloCompra;
                            datos['id']                     = parseInt(id, 10);
                            datos['precio']                 = parseDouble(precio);
                            datos['precioVenta']            = parseDouble(precioVenta);
                            datos['bodega']                 = bodega;
                            datos['subtotal']               = parseDouble(subtotal);
                            datos['articulo']               = parseInt(id, 10)+"::"+articulo;
                            datos['descuento']              = descuento;
                            datos['cantidad']               = cantidad;
                            datos['ganancia_venta']         = parseDouble(ganancia_venta);   
                            datos['iva']                    = parseInt(valPredIva, 10);

                            fila += generarFilaArticulo(datos);//funcion que retorna la fila ya armada
                        } 
                        
                        
                    });  

                    $("#tablaListaArticulosFactura").find("#thead").after(fila);;

                    armarCadenaDatosArticulos();
                    //settimeout
                    setTimeout(function(){
                        calcularTotalFactura();
                    //guardarFacturaTemporal();
                    }, 250);                

                    $(".ui-dialog").find(".ui-dialog-titlebar-close").trigger("click"); 
                    $("#articuloFactura").focus();
                    $("#BoxOverlayTransparente").css("display","none");
                    return false;            

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
  
    //codigo que asigna el descuento general a todos los items
    $("#campoDescuentoListadoArticulos").keyup(function(){
        $(".descuentoGeneralArticuloCompra").val("");
        var descuento = $("#campoDescuentoListadoArticulos").val();        
        $(".descuentoGeneralArticuloCompra").val(descuento);
        
    });
    
    //codigo que asigna el porcentaje de ganancia de la venta del articulo
    $("#campoGananciaListadoArticulos").keyup(function(){
        $(".porcentajeGanancia").val(descuento);
        var descuento = $("#campoGananciaListadoArticulos").val();
        $(".porcentajeGanancia").val(descuento);
        
    });    
  
    $("#fraseMasDescuento").click(function(){
        if($(".dctoOculto").is(":visible")){
            $(".dctoOculto").addClass("oculto");
            $(this).html("+ dcto");
            
        }else{
            $(".dctoOculto").removeClass("oculto");
            $(this).html("- dcto");
            
        }
        
    });
       
    /* funciones para el autocompletable del articulo que se lleva tambien el id de la bodega */

    $(".imagenEliminarItemFila").live("click", function(){
        $(this).parents("tr.filaArticuloCompra").fadeOut("fast");
        $(this).parents("tr.filaArticuloCompra").remove();   
        
        setTimeout(function(){
            calcularTotalFactura();
        }, 250);
        
    });
    
    //codigo que pone la clase en la tabla de articulos cuando se pasa el raton por una fila
    $("#tablaArticulosCompra tr").not(":first").live({
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
    $("#tablaArticulosCompra tbody tr").not(":first").live("click", function(){
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
        $(this).parents(".ui-dialog").find(".ui-dialog-titlebar-close").trigger("click"); 
        return false;
    });
     
    //codigo para llamar al metodo que muestra la ventana de dialogo de busqueda de facturas
    $("#tablaBusquedaFacturas").live("click", function(e){ 
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
        $(this).parents(".ui-dialog").find(".ui-dialog-titlebar-close").trigger("click"); 
        return false;
    });   
      
    //codigo para consultar un articulo
    $(".imagenConsultarItemFila").live("click", function(){
        var id = $(this).parents("tr.filaArticuloCompra").attr("cod");
        
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
                    var destino     = "/ajax/articulos/listarArticulosCompra?extra="+idBodega+"&term="+id;

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
    
    
    
    //codigo para deterner la funcion por defecto de la tecla enter sobre un formulario
    $("#formaCompraMercancias").find("input").on("keypress", function(e){
        var tecla = (document.all) ? e.keyCode : e.which; 
        if (tecla == "13") {
            e.preventDefault();
            return false;
        }
    });

    //Funcion que se encarga de mostrar los datos del articulo en el modulo de ventas
    //cada vez que se hace focus sobre un articulo de la lista del autocomplete

    //Funcion que se encarga de mostrar los datos del articulo en el modulo de ventas
    //cada vez que se hace focus sobre un articulo de la lista del autocomplete

    $("#articuloFactura").bind("autocompletefocus", function(event, ui){
        $(this).val(ui.item.label);
        var bodega = $("#idBodegaGeneral").val();
        var precio  = "<p class='letraBlanca'><span class=masGrande2>Ultimo Precio Compra:</span> <span class=masGrande5>"+ui.item.value+"</span></p>";
        precio += "<p class='letraBlanca '><span class=masGrande1>Cant. Bodega "+parseInt(bodega, 10)+":</span> <span class=masGrande2>"+ui.item.cant+"</span></p>";
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
    
    //codigo para mover un articulo que se ingrese en una compra a una bodega
    $(".imagenMoverItemBodega").live("click", function(){
        var id = $(this).parents("tr.filaArticuloCompra").attr("id");
        
        $.ajax({
            type:"POST",
            url:"/ajax/compras_mercancia/moverArticuloBodega",
            dataType:"json",
            data: {
                id: id
            },
            success:procesaRespuesta
        });
        
    });      
    
    /**
     * Funcion que cambia el valor base del campo retecree haciendo una peticion ajax
     * enviando el id del proveedor, consultando su actividad economica y asi traer el valor del retecree
     */
    $("#selectorProveedores").on("change", function(){
        var idProveedor = $(this).val();
        
        $.ajax({
            type: 'POST',
            url: '/ajax/proveedores/getRegimen',
            dataType: 'json',
            data: {
                idProveedor: idProveedor
            },
            success: function(respuesta){                
                if(respuesta.regimen != 1){
                    $(".campoIva").show("fast");
                    
                } else {
                    $(".campoIva").hide("fast");
                    
                }
                $("#regimenProveedor").val(respuesta.regimen);
                //si se cambia de proveedor se deben eliminar todos los articulos de la lista
                resetFactura();
            }
        });
        
    });
    
    
    /*Funcion que es lanzada sobre el evento selected del plugin autocomplete sobre el
     *campo de seleccionar articulos en una factura, esto para el precio de venta*/
    $("#articuloFactura").bind("autocompleteselect", function( event, ui) { 
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
        
//    /**
//     * funcion encargada de asignar el valor del id del proveedor al campo oculto y de poner solo el nombre del proveedor el el campo
//     * ya que cuando se lista, este mismo se muestra con el nit del proveedor concatenado
//     **/
//    $("#campoIdProveedor").bind( "autocompleteselect", function( event, ui) {      
//
//        $("#campoOcultoIdProveedor").val(parseDouble(ui.item.value, 10));
//        setTimeout(function(){
//            $("#campoIdProveedor").val('');
//            $("#campoIdProveedor").val(ui.item.nombre);
//        }, 75);
//           
//    });      
    
    $("#imagenBuscarFactura").live("click", function(){
        buscarFactura(modulo);
    });
    
    $("#imagenAgregarVariosArticulos").live("click", function(){
//        agregarVariosArticulos(modulo);
        $("#indicadorEspera").css("display","block");
        $("#BoxOverlay").css("display","block");
        $.ajax({
            type:"POST",
            url:"/ajax/articulos/verTodos",
            dataType:"json",
            success:procesaRespuesta

        });
    });    
    
    /* Funcion que se encarga de abrir el formulario para buscar los catalogos*/
    $("#imagenBuscarCatalogo").live("click", function(){
        $("#indicadorEspera").css("display","block");
        $("#BoxOverlay").css("display","block");
        $.ajax({
            type:"POST",
            url:"/ajax/motos/buscarCatalogos",
            dataType:"json",
            success:procesaRespuesta
    
        });    
    });
    
    $("#imagenCargarOrden").live("click", function(){
        $("#indicadorEspera").css("display","block");
        $("#BoxOverlay").css("display","block");
        $.ajax({
            type:"POST",
            url:"/ajax/compras_mercancia/buscarOrdenCompra",
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
            $(".campoFechaVtoFact").slideDown("fast"); 
            
        } else {
            $(".campoFechaVtoFact").slideUp("fast");
            
        }

    });     

    /*
     * Codigo que le da el valor a el campo oculto id bodega
     * cada vez que se selecciona una bodega
     **/
    $("#selectorBodegas").live("change", function(){
        var bodega = $("#selectorBodegas").val();   
        $("#idBodegaGeneral").val(bodega);
        
        $(".filaArticuloCompra").each(function(){
            $(this).attr("bodega", bodega)
        });
        
        var titleCampo = $("#articuloFactura").attr("title").split("?");
        
        var ruta = titleCampo[0]+"?extra="+bodega;
        
        $("#articuloFactura").attr("title", ruta)
        
        armarCadenaDatosArticulos();

    });
       
    /*
     *Codigo que ejecuta las funciones cuando se selecciona mover a un articulo a otra bodega
     **/
    $("#botonMoverArticuloBodega").live("click", function(e){
        e.preventDefault();
        var fila = $("#idFilaArticuloAMover").val();
        var idBodega = $("#selectorBodegas2").val();
        
        
        if(idBodega == null){
            Sexy.alert("Debes seleccionar una bodega");
            
        } else {
            
            $("#"+fila).attr("bodega", parseInt(idBodega, 10));
            
            $("#"+fila).find(".idBodegaCompra").html(parseInt(idBodega, 10));
            Sexy.info("Articulo movido exitosamente", {
                onComplete: function(){                    
                    $("#botonMoverArticuloBodega").parents('.ui-dialog').find(".ui-dialog-titlebar-close").trigger("click");
                    armarCadenaDatosArticulos();
            
                }
            });
            guardarFacturaTemporal();
        }

    });
      
    //Si se escribe en el campo numero de factura de proveedor es porque es una compra legitima
    //esta precacucion por si primero se envia vacio el campo por error, y luego se corrige el tema
    $("#campoNumeroFacturaProveedor").blur(function(){
        if($("#campoNumeroFacturaProveedor").val() != ''){
            $("#esOrdenDeCompra").val("");//pongo el campo que determina si es una orden de compra vacio
        }        
    });    
            
    /**
    * FUNCIONES QUE CALCULAN EL TOTAL DE LA FACTURA A MEDIDA QUE SE VAN ESCRIBIENDO VALORES
    */    
    
    /**
     *Funciones para calcular el valor cuando se aplique el PORCENTAJE DE DESCUENTO GENERAL general
     **/
    $("#campoDescuentoListadoArticulos").live("keypress", function(e){verificarTecla(e, "entero");}).live("keyup change", function(e){
            var proceder = true;
            //para aplicar un descuento cada fila de articulo debe tener una cantidad, minimo uno
            //y debe tener un precio unitario, de lo contrario, sobre que se aplicaría descuento
            
            $(".cantidadArticuloCompra").each(function(){//verifico que no hayan cantuidades vacias recorriendo cada fila
                if( $(this).val() === ""  ||  $(this).val() === "0" ||  $(this).val() === 0 ){
                    proceder = false;
                    $(this).addClass("textFieldBordeRojo");
                }
            });
            
            $(".precioUnitarioArticuloCompra").each(function(){//verifico que no hayan precios vacios
                if( $(this).val() === ""  ||  $(this).val() === "0" ||  $(this).val() === 0 ){
                    proceder = false;
                    $(this).addClass("textFieldBordeRojo");
                }
            });            
            
            if(proceder){ //si todo salio bien, se cumplen las condiciones para aplicar el descuento
                $(".descuentoGeneralArticuloCompra").each(function(){ //recorro cada una de las filas a traves del objeto DOM campoDcto
                    var obj = $(this);//capturo el objDOM campo dcot
                    calcularSubtotalCampoDescuento(obj, e, true);//llamo a la funcion encargada de calcular este subtotal
                });
                
            } else { //si hay algún campo cant o precio vacio
                Sexy.alert("No puede haber campos cantidad o precio de unitario vacios", { //se avisa al usuario con un alert
                    onComplete: function(){ //una vez hace click en ok
                    
                        $(".descuentoGeneralArticuloCompra").val(''); //se ponen todos los campos de las filas de Dcto en vacio
                            
                        $("#campoDescuentoListadoArticulos").val('');// el campo dcto genarl a vacio
                        $(".textFieldBordeRojo:first").focus();//y se pone el foco en el primer campo que se encuentre con la clase txtField BordeRojo
            
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
     *funcion que se encarga de ir calculando el subtotal a medida que se va ingresando el PORCENTAJE DE DESCUENTO a cada uno de los articulo
     **/
    $(".descuentoGeneralArticuloCompra").live("keypress", function(e){verificarTecla(e, "entero");}).live("keyup change", function(e){   
        var obj       = $(this);
        var proceder  = true;
        var padre     = obj.parents("tr.filaArticuloCompra"); //capturo el objDOM padre <p> para asi poder acceder a los hermanos del objeto campoDcto
        var precio    = padre.find(".precioUnitarioArticuloCompra"); //capturo el precio
        var cantidad  = padre.find(".cantidadArticuloCompra");//capturo el objDOM cantidad


        if(cantidad.val() === 0 || cantidad.val() === '' || cantidad.val() === '0'){//nuevamente se verifica que no hayan campos cantidad vacios
            cantidad.addClass("textFieldBordeRojo"); //de haber un campo cant vacio, se pone la clase de error     
            proceder = false; //proceder se pone el false
            setTimeout(function(){ //para que tome bien los cambios mientras procesa, se pone un tiempo de espera de 50 milisegundos
                obj.val(""); //se pone el campo dcto en vacio
                
            }, 50);

            //return;
            
        } else if(precio.val() === 0 || precio.val() === '' || precio.val() === '0'){//nuevamente se verifica que no hayan campos cantidad vacios
            precio.addClass("textFieldBordeRojo"); //de haber un campo cant vacio, se pone la clase de error   
            proceder = false;
            setTimeout(function(){
                obj.val("");
                
            }, 50);

            //return;
            
        }         
        
        if (proceder) {
            calcularSubtotalCampoDescuento(obj, e, false);
            
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

    /**
     *Funciones para calcular el valor de la venta cuando se aplique el PORCENTAJE DE GANANCIA general
     **/
    $("#campoGananciaListadoArticulos").live("keypress", function(e){verificarTecla(e, "entero");}).live("keyup change", function(e){        
    
            var proceder = true;//determina si se harán los calculos, por defecto es true=si, si hay algun problema, este valor será modificado
            $(".precioUnitarioArticuloCompra").each(function(){//verificar que las fila de los articulos tengan un precio de compra, para poder asi calcular el precio de venta
                if( $(this).val() === ""  ||  $(this).val() === "0" ||  $(this).val() === 0 ){
                    proceder = false;//si hay alguna fila sin precio unitario, se cancela el flujo
                    $(this).addClass("textFieldBordeRojo");//y se le agrega la clase de error
                }
            });
            
            if(proceder){//si no hubo problemas
                $(".porcentajeGanancia").each(function(){//recorremos todas las filas de articulos
                    var obj = $(this);
                    calcularGananciaArticulo(obj, e, true);//llamamos a esta funcion
                });
                
            } else { //si hubo problemas
                Sexy.alert("No puede haber campos de precio unitario vacios", {//se notifica del error al usuario
                    onComplete: function(){ //cuando haga click en ok
                    
                        $(".porcentajeGanancia").val('');//los campos porcentaje de ganancia en blanco
                        $("#campoGananciaListadoArticulos").val('');//el campo porcentaje de ganancia general en blanco
                        $(".textFieldBordeRojo:first").focus();//y se pone el foco en el primer campo que se encuentre con la clase txtField BordeRojo
            
                    }
                });

                return;
   
            }   

    }).live("blur", function(e) {
        var valor = $(this).val();
        
        if (isNaN(valor) && !isFinite(valor)) {
            mostrarErrorCampo($(this));
        }
        
    });        
       
    /**
     *funcion que se encarga de ir calculando el valor de venta del articulo a medida que se va ingresando el PORCENTAJE DE GANANCIA a cada uno de los articulo
     **/
    $(".porcentajeGanancia").live("keypress", function(e){verificarTecla(e, "entero");}).live("keyup change", function(e){        
        var obj          = $(this);  
        var padre        = obj.parents("tr.filaArticuloCompra");//capturo el objDOM <p> padre
        var precio       = padre.find(".precioUnitarioArticuloCompra");//capturo el precio unitario
        var precio_venta = padre.find(".precioVentaArticulo");//capturo el objDOM precio de venta


        if(precio.val() === 0 || precio.val() === "" || precio.val() === "0"){//si no tiene precio unitario

            Sexy.alert("No puede haber campos de precio unitario vacios", {//se notifica del error al usuario
                    onComplete: function(){ //cuando haga click en ok                    
                            precio.addClass("textFieldBordeRojo"); //de haber un campo cant vacio, se pone la clase de error 
                            obj.val(""); //se pone el campo dcto en vacio  
                            precio_venta.val("");//se pone el precio de venta vacio
            
                    }
                });            


            return;
        } else {
            calcularGananciaArticulo(obj, e, false);
            
        }

    }).live("blur", function(e) {
        var valor = $(this).val();
        
        if (isNaN(valor) && !isFinite(valor)) {
            mostrarErrorCampo($(this));
        }
        
    });      
    
    
    //Funciones para calcular el % de la ganancia cuando se vaya ingresando la cantidad de dinero que se quiere ganar
    $(".precioVentaArticulo").live("keypress", function(e){verificarTecla(e, "numeros");}).live("keyup change", function(e){        
        var obj          = $(this);  
        var padre        = obj.parents("tr.filaArticuloCompra");//capturo el objDOM <p> padre
        var precio       = padre.find(".precioUnitarioArticuloCompra");//capturo el precio unitario
        var porc_ganancia = padre.find(".campoPorcentajeGanancia");//capturo el objDOM porcentaje de ganancia de la fila


        if(precio.val() === 0 || precio.val() === "" || precio.val() === "0"){//si no tiene precio unitario
            precio.addClass("textFieldBordeRojo"); //de haber un campo cant vacio, se pone la clase de error 
            
            Sexy.alert("No puede haber campos de precio unitario vacios", {//se notifica del error al usuario
                    onComplete: function(){ //cuando haga click en ok                    
                            
                            precio.focus();
                            obj.val(""); //se pone el campo dcto en vacio  
                            porc_ganancia.val("");//se pone el precio de venta vacio
            
                    }
                });            


            return;
        } else {
            calcularGananciaArticuloPorcentaje(obj, e, false);
            
        }        
    }).live("blur", function(e) {
        var valor = $(this).val();
        
        if (isNaN(valor) && !isFinite(valor)) {
            mostrarErrorCampo($(this));
        }
        
    });  
    
    
    
    
    //funcion que se encarga de ir calculando el subtotal a medida que se va ingresando la cantidad de articulos
    $(".cantidadArticuloCompra").live("keypress", function(e){verificarTecla(e, "entero");}).live("keyup change", function(e){
        
        var padre   = $(this).parents("tr.filaArticuloCompra");//capturo el objeto parrafo padre, donde se encuentran los demas campos que hacen parte del art
        
        var precio  = padre.find(".precioUnitarioArticuloCompra").val();//capturo el precio unitario de ese articulo en especial    
        
        if (precio != '') {precio = parseDouble(precio);} //casting a entero
        
        var campoSubtotal = padre.find(".subtotalArticuloCompra");//capturo el subtotal
        
        var descuento     = padre.find(".descuentoGeneralArticuloCompra").val();//capturo el descuento que tenga el articulo
        
        var iva = padre.attr("iva");
        
        if (descuento != '') {descuento = parseDouble(descuento);}//casting a entero
        
        var cantidad    = 0;
        var subtotal    = 0;

        cantidad = $(this).val();
        if(cantidad == ''){cantidad = 0;}

        cantidad = parseInt(cantidad);

        padre.attr("cantidad", cantidad);

        if(descuento === '' || descuento === 0 || descuento === "0"){
            subtotal = cantidad * precio;

        } else{
            precio = precio - ((precio * descuento) / 100);
            subtotal = cantidad * precio;

        }
        
        //validar si el proveedor va a vender con iva
        if ($("#regimenProveedor").val() != 1) {
            subtotal += (subtotal * (iva / 100));
        }       

        campoSubtotal.val(subtotal); 
        
        padre.attr("subtotal", parseDouble(subtotal));

        calcularTotalFactura();

    }).live("blur", function(e) {
        var valor = $(this).val();
        
        if (isNaN(valor) && !isFinite(valor)) {
            mostrarErrorCampo($(this));
        } else {
            verificarFormatoNumeroDecimal($(this));
        }
        
    });  
    

    /**
     *funcion que se encarga de ir calculando el subtotal a medida que se va ingresando el precio del articulo
     **/
    $(".precioUnitarioArticuloCompra").live("keypress", function(e){verificarTecla(e, "numeros");}).live("keyup change", function(e){

        var padre    = $(this).parents("tr.filaArticuloCompra");//capturar el padre <tr>
        
        var cantidad = padre.find(".cantidadArticuloCompra").val();
        
        if (cantidad != '') {cantidad = parseInt(cantidad);}
        
        var campoSubtotal = padre.find(".subtotalArticuloCompra");
        
        var descuento     = padre.find(".descuentoGeneralArticuloCompra").val();
        
        if (descuento != '') {descuento = parseDouble(descuento);}
        
        var porcenGanan  = padre.find(".campoPorcentajeGanancia").val();
        if (porcenGanan != '') {porcenGanan = parseDouble(porcenGanan);}  
   
        //var campoPrecioVenta = padre.find(".campoPrecioVentaArticulo");
      
        var precio      = 0;
        var subtotal    = 0;
        //var precioVenta = 0;
        
        //var valPredIva = $("#valorIva").val();
        var iva = padre.attr("iva");
        

        precio = $(this).val();
        
        if(precio == ''){precio = 0;}
        
        precio = parseDouble(precio);

        padre.attr("precio", parseDouble(precio));

        if(descuento === '' || descuento === 0 || descuento === '0'){//si no hay valores en el campo porcentaje de descuento
            subtotal = cantidad * precio;

        } else{//pero si hay valor en el campo porcentaje de ganancia 
            subtotal = precio - ((precio * descuento) / 100);//se hace el calculo aplicando el porcentaje
            subtotal = cantidad * subtotal;

        }
        
        //validar si el proveedor va a vender con iva
        if ($("#regimenProveedor").val() != 1) {
            subtotal += (subtotal * (iva / 100));
        } 

        campoSubtotal.val(subtotal); 
        
        padre.attr("subtotal", parseDouble(subtotal));

        calcularTotalFactura();

    }).live("blur", function(e) {
        var valor = $(this).val();
        
        if (isNaN(valor) && !isFinite(valor)) {
            mostrarErrorCampo($(this));
        } else {
            verificarFormatoNumeroDecimal($(this));
        }
        
    });   
    
    /**
     * Funciones que se encargan de bloquear el boton derecho de los campos de texto
     **/
        $("input").live('contextmenu', function(e) {
        // evito que se ejecute el evento
            e.preventDefault();
        });    
    
    
    //Funcion que se encarga de calcular el total cuando se escribe en el valor del flete
    $("#campoValorFlete").live("keypress", function(e){verificarTecla(e, "numeros");}).live("keyup change", function(e){

        var total       = 0;
        var subtotal    = 0;
        
        $(".filaArticuloCompra").each(function(){
            subtotal = subtotal + parseDouble($(this).attr("subtotal"));
        });
        
        subtotal = parseDouble(subtotal);

        //pongo valor en el <span> subtotal
        $("#campo_subtotal").html("<span class='prefijo_numero'>$</span>"+subtotal);
        $("#subtotal").val(subtotal);         
        
        var flete = 0;        
        flete = $(this).val();//+flete;
        if(flete == ''){flete = 0;}        
    
        total = parseDouble(subtotal) + parseDouble(flete);
               

        if(total > 0){
            var descuento1 = $("#campoDescuento1").val();
            if(descuento1 != ''){descuento1 = parseDouble(descuento1);}
            
            var descuento2 = $("#campoDescuento2").val();
            if(descuento2 != ''){descuento2 = parseDouble(descuento2);}

            sumarIva();                       

            total = aplicarDescuento(descuento1, total);    

            total = aplicarDescuento(descuento2, total); 
 
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
    
    /**
     * documentado porque el campo esta como readonly, entonces no es necesaria esta funcionalidad, si los
     * requerimientos cambias, se desdocumenta
     */
//    $("#campoIva").live("keypress", function(e){verificarTecla(e, "numeros");}).live("keyup change", function(e){
//
//        var total       = 0;
//        var subtotal    = 0;
//
//        subtotal = parseDouble($("#oculto_iva").val());
//
//        total = subtotal;
//
//        if (total > 0) {
//            var iva = $(this).val();
//            if (iva === '') { iva = 0; }
//
//            total = total + parseDouble(iva); 
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
//    }).live("blur", function(e) {
//        var valor = $(this).val();
//        
//        if (isNaN(valor) && !isFinite(valor)) {
//            mostrarErrorCampo($(this));
//        }
//        
//    });      
    
    
    // funcion que se encarga de ir calculando el total si se escribe en el campo id= descuento1
    $("#campoDescuento1").live("keypress", function(e){verificarTecla(e, "numeros");}).live("keyup change", function(e){
        var total       = 0;
        var subtotal    = 0;
        
        $(".filaArticuloCompra").each(function(){
            subtotal = subtotal + parseDouble($(this).attr("subtotal"));
        });
        
        subtotal = parseDouble(subtotal);
    
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
            
            //pongo valor en el <span> subtotal , uso la variable total pero en realidad aun es el subtotal :)
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
    
        
    // funcion que se encarga de ir calculando el total si se escribe en el campo id= descuento2
    $("#campoDescuento2").live("keypress", function(e){verificarTecla(e, "numeros");}).live("keyup change", function(e){
        
        var total = 0;
        var subtotal = 0;
        $(".filaArticuloCompra").each(function(){
            subtotal = subtotal + parseDouble($(this).attr("subtotal"));
        });
        
        subtotal = parseDouble(subtotal);

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
 

//Todos los campos autocompletables deben tener un atributo llamado verificacion, que contendrá la ruta de verificacion
//del item en la BD, tambien habrá un metodo para los campos autocompletables (esta por definirse si en el on blur, o en el onselect)
//en este evento, se captura la ruta de verificacion y se hace la consulta y sus respectivos cambios en caso de no ser valido

function cerrarAyuda(){
    $("#BoxOverlay").css("display","none");
    $("#contenedorAyudaUsuario").slideUp("slow");
    
}

function agregarVariosArticulos(){
    $("#indicadorEspera").css("display","block");
    $("#BoxOverlay").css("display","block");
    $.ajax({
        type:"POST",
        url:"/ajax/articulos/buscarArticulos",
        dataType:"json",
        success:procesaRespuesta
    
    });
}


function buscarFactura(modulo){
    if(modulo == "Compras_mercancia"){
        $("#indicadorEspera").css("display","block");
        $("#BoxOverlay").css("display","block");
        $.ajax({
            type:"POST",
            url:"/ajax/compras_mercancia/buscarFactura",
            dataType:"json",
            success:procesaRespuesta
    
        });
    }
}




// FINALIZAR LA FACTURA DE COMPRA
$("#botonFinalizarFactura").bind("click", function(e){
    e.preventDefault();
    var guardarFactura = true;
    //bloquear el boton finalizar factura (para que solo se muestre la ventana modal una vez)
    $boton = $(this);
    $boton.attr("disabled", "disabled");

    var cantArticulosCompra = $(".filaArticuloCompra");
    
    
    var total = $("#totalFactura").val();
      
    if(cantArticulosCompra.length <= 0){
        Sexy.alert("Debes al menos ingresar un articulo para generar una compra");
        $boton.removeAttr("disabled");
        return;        
        
    } else if (total <= 0) {
        /**
        * Verificar que la factura tenga vaores adecuados
        **/
        Sexy.alert("El total de la factura debe de ser mayor a 0 para poder facturar");   
        $boton.removeAttr("disabled");
        return; 
    }

    //validar que el campo numero de factura del proveedor no este vacio    
    var numFacPro = $("#campoNumeroFacturaProveedor");

    if(numFacPro.val() == ''){
        numFacPro.focus();
        numFacPro.addClass('textFieldBordeRojo campo_obligatorio');
        $("#esOrdenDeCompra").val("esOrdenDeCompra");
        setTimeout(function(){
            $(".textFieldBordeRojo").removeClass("textFieldBordeRojo");
            $(".campo_obligatorio").removeClass("campo_obligatorio");
        }, 3000);
        
    //guardarFactura = false;
    }    

    setTimeout(function(){
        if(guardarFactura){
            
            $("#indicadorEspera").css("display","block");
            $("#BoxOverlay").css("display","block");
    
            formulario=$("#botonFinalizarFactura").parents("form");
            destino=$(formulario).attr("action");    


            $(formulario).ajaxForm({
                dataType:"json"
            });
            $(formulario).ajaxSubmit({
                dataType:"json",
                success:procesaRespuesta
            });
            
        }
    }, 300);
    
    setTimeout(function(){
        $boton.removeAttr("disabled");
    },2000);
    
    return false;
    
});



/**
 * Función que se encarga de ir guardando la factura que esta siendo generada.
 * una vez hace la primera llamada y ha guardado una factura temporal, con cada una
 * de las siguientes llamadas, esta factura guardada es modificada
 */
function guardarFacturaTemporal(){    
    
    var idFacturaTemporal = $("#idFacturaTemporal").val();//capturo el id de la factura temporal (campo oculto en el formulario)
    
    var ruta = 'guardarFacturaTemporal';//la primera intencion será guardar la factura temporal
    
    if(idFacturaTemporal != ''){//sucede que cuando se guarda la primera vez la factura temporal, el procesa respuesta pone el $sql->ultimoId como valor a la variable idFacturaTemporal
        //esto quiere decir que ya se agrego la factura temporal, y que ahora debe ser modificada, tambien se tendrá en cuenta este id para eliminar la factura temporal
        ruta = 'modificarFacturaTemporal';
    }
    
    setTimeout(function(){

    
        formulario=$("#botonFinalizarFactura").parents("form");
        destino= '/ajax/compras_mercancia/'+ruta;    


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
 * ya sea el gral, o un descuento a una fila en particular
 * obj = objDOM campo de descuento de una fila de articulos
 * e   = evento js que se captura
 * borrarCampoDesGen = determina si esta funcion es llamada cuando se aplica un dcto a una fila particular, o
 * en el campo dcto general. true si es desde el dcto general.
 */
function calcularSubtotalCampoDescuento(obj, e, borrarCampoDesGen){
    
    var padre  = obj.parents("tr.filaArticuloCompra"); //capturo el objDOM padre <p> para asi poder acceder a los hermanos del objeto campoDcto
    
    var precio = padre.find(".precioUnitarioArticuloCompra").val(); //capturo el precio
    
    if (precio == '') {precio = 0;} else {precio = parseDouble(precio);} //casting a entero
    
    var campoSubtotal = padre.find(".subtotalArticuloCompra");//capturo el objDOM campo del subtotal
    
    var cantidad      = padre.find(".cantidadArticuloCompra");//capturo el objDOM cantidad
    
    var iva = padre.attr("iva");
  
    var descuento = 0;
    
    var subtotal  = 0;

    descuento = obj.val();//capturo el descuento
    
    if(descuento == ''){descuento = 0;}
    
    descuento = parseDouble(descuento);//casting a entero            

    padre.attr("descuento", descuento);//al padre, osea a la etiqueta <p> dentro de la cual se encuentra el campo, pongale el atributo "descuento" con el valor del dcto

    if(cantidad.val() === '' || cantidad.val() === 0 || cantidad.val() === '0'){
        subtotal = 0;
        
    } else{
        precio = precio - ((precio * descuento) / 100);
        //subtotal_con_descuentos = valor resultante de aplicar el descuento al precio unitario
        padre.attr("subtotal_con_descuentos", parseDouble(precio));
        subtotal = cantidad.val() * precio;
        
    }
    
    //validar si el proveedor va a vender con iva
    if ($("#regimenProveedor").val() != 1) {
        subtotal += (subtotal * (iva / 100));
    }    

    campoSubtotal.val(parseDouble(subtotal));   
    
    padre.attr("subtotal", parseDouble(subtotal));

    calcularTotalFactura();
        
}

/**
 * Funcion que se encarga de calcular el valor de la ganancia de un articulo de una fila cuando se coloca un porcentaje de ganancia
 * borrarCampoGanGen = booleano que determina si hay que borrar el campo de ganancia general
 */
function calcularGananciaArticulo(obj, e, borrarCampoGanGen){
    
    var padre           = obj.parents("tr.filaArticuloCompra");//capturo el objDOM <p> padre
    
    var precio          = padre.attr("precio");//capturo el precio unitario (el precio base sobre el que se esta trabajando)
    
    var iva             = parseDouble( padre.attr("iva") );
    
    var $precioVenta    = padre.find(".precioVentaArticulo");//capturo el objDOM precio de venta
    
    
 
    if(precio == ''){precio = 0;}
    precio = parseDouble(precio);
    
    var precioVenta        = 0;
    
    //calculo el porcentaje de ganancia
    var porcentajeGanancia = 0;
    porcentajeGanancia     = obj.val();

    if(porcentajeGanancia == ''){porcentajeGanancia = 0;}
    porcentajeGanancia = parseDouble(porcentajeGanancia);

    //var valPredIva = (iva != 0) ? iva : $("#valorIva").val();
    var valPredIva = iva;
        valPredIva = parseInt(valPredIva);   
        
    var subtotal = precio;
    
    //validar si el proveedor va a vender con iva
    if ($("#regimenProveedor").val() != 1) {
        subtotal += (subtotal * (iva / 100));
    }     

    if(subtotal != '' && porcentajeGanancia != ''){
        var ganancia = subtotal * ( porcentajeGanancia / 100 ); 
        precioVenta = parseDouble(subtotal +  ganancia);

    } else if(subtotal != '' && porcentajeGanancia == ''){
        precioVenta = parseDouble(subtotal);

    } else {
        precioVenta = 0;

    }         

    $precioVenta.val(parseDouble(precioVenta));

    if(borrarCampoGanGen){
        $(".campoGananciaListadoArticulos").val("");
    }
   
    padre.attr("precio_venta", parseDouble(precioVenta));
    armarCadenaDatosArticulos();
    guardarFacturaTemporal();
    
}


//Calcular ganancia en porcentaje de la venta del articulo
//cuando se tipea en el campo valor venta, esta funcion se encarga de capturar el precio unitario
//y segun lo introducido calcula el porcentaje de ganancia
function calcularGananciaArticuloPorcentaje(obj, e, borrarCampoGanGen){
    
    var padre               = obj.parents("tr.filaArticuloCompra");
    
    var $porcentajeGanancia = padre.find(".porcentajeGanancia");
    
    var iva                 = parseDouble( padre.attr("iva") );
    
    var subtotal            = padre.attr("subtotal");
    
    if (subtotal == '') {subtotal = 0;}
    
    subtotal = parseDouble(subtotal);    
 
    var precioVenta        = 0;
    var porcentajeGanancia = 0;    

    //var valPredIva = (iva != 0) ? iva : $("#valorIva").val();
    var valPredIva = iva;
        valPredIva = parseInt(valPredIva); 

    precioVenta = obj.val();

    if(precioVenta == ''){precioVenta = 0;}

    precioVenta = parseDouble(precioVenta);    

    if(parseDouble(precioVenta) < 0){
        return;

    } else if(subtotal == 0){
        porcentajeGanancia = 0;
        
    } else{
        //calcular el porcentaje de ganancia
        porcentajeGanancia =  precioVenta - subtotal ;
        porcentajeGanancia =  porcentajeGanancia * 100 ;
        porcentajeGanancia =  porcentajeGanancia / subtotal ;

    }

    $porcentajeGanancia.val("");
    if(borrarCampoGanGen){
        $(".campoGananciaListadoArticulos").val("");
    }
        
    if(porcentajeGanancia > 0){
        $porcentajeGanancia.val(parseDouble(porcentajeGanancia));
    }

    padre.attr("precio_venta", parseDouble(precioVenta));
    
}


/**
 * Funcion que calcula el total de la factura cuando hago algun cambio sobre un campo del listado de articulos
 */
function calcularTotalFactura(){
    
    var total    = 0;
    var subtotal = 0;
    
    $(".filaArticuloCompra").each(function(){//va recorriendo cada fila de articulo y sumando el valor del subtotal
        subtotal = subtotal + parseDouble($(this).attr("subtotal"));
    });
    
    subtotal = parseDouble(subtotal);
    
    //pongo valor en el <span> subtotal
    $("#campo_subtotal").html("<span class='prefijo_numero'>$</span>"+subtotal);
    $("#subtotal").val(subtotal);
    
    total = subtotal;
    
    //colocar el valor al iva
    if ($("#campoIva").is(":visible")) {
        sumarIva();
        
    }


    //basicamente se verifica que si se este vendiendo algo
    if(total > 0){
        var flete = $("#campoValorFlete").val();
        total = sumarFlete(flete, total);   
    
        sumarIva(total);
    
        var descuento1 = $("#campoDescuento1").val();
        total = aplicarDescuento(descuento1, total);
    
        var descuento2 = $("#campoDescuento2").val();
        total = aplicarDescuento(descuento2, total);        
        
    } else {//si no se esta vendiendo nada el total será cero
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
    if(flete != 0 && flete != "0" && flete != ""){
        total = total + parseDouble(flete);

    }
    return total;
}

//funcion que se encarga de sumar el valor del iva al total de la factura
function sumarIva(){
    //colocar el valor al iva
    var valIva = 0;

    if ($("#campoIva").is(":visible")) {
        $("tr.filaArticuloCompra").each(function(){
           var _iva   = parseInt($(this).attr("iva"));
           //queda pendiente si se saca es el subtotal (ya con descuentos) o el precio
           var precioTemp = ($(this).attr("subtotal_con_descuentos") != 0) ? $(this).attr("subtotal_con_descuentos") : $(this).attr("precio");
           var precio = parseDouble(precioTemp);
           
           var cantidad = parseDouble($(this).attr("cantidad"));
     
           //Cambio para no mostrar el iva incluido en el precio del arituclo
           //var _ivaArticulo = precio - (precio / (1 + (_iva/100)) );
         
           var _ivaArticulo = cantidad * (precio * (_iva/100));
                      
           valIva += _ivaArticulo;
         
           //valIva = parseDouble(valIva) * cantidad;
        
        });
        
    }

    $("#campoIva").val(valIva);
    
}

function sumarPrecioVenta(precio, porcenGanan) {
    var precioVenta = precio; //+ (precio* (valPredIva / 100));

    if (porcenGanan !== '' || porcenGanan !== 0 || porcenGanan !== '0') {//si  hay porcentaje de ganancia               
        precioVenta = precioVenta + ( (porcenGanan * precio) / 100);//se hace el calculo aplicando el porcentaje

    }  
    
    return precioVenta;
}

////funcion que se encarga de sumar el valor del iva al total de la factura
//function sumarRetecree(retecree, total){
//    //09/08/2013 se documenta este codigo, se decide agregar el iva de la compra direcatmente en pesos
////    if(iva != 0 && iva != "0" && iva != ""){
////        total = total + ( (total * iva) / 100);
////    } 
//    return total + retecree;
//}

//funcion que se encarga de aplicar descuento sobre el total de la factura
function aplicarDescuento(descuento, total){
    if(descuento != 0 && descuento != "0" && descuento != ""){
        total = total - ( (total * descuento) / 100);
    }
    return total;
}

function armarCadenaDatosArticulos(){
    
    var cadenaDatosArticulos = '';
    
    $(".filaArticuloCompra").each(function(){//va recorriendo cada fila de articulo y sumando el valor del subtotal
        
        var id = $(this).attr("cod");
            id = parseInt(id,10);
            
        var cantidad = $(this).attr("cantidad");
        if (cantidad == '') cantidad = 0;
        
        var descuento = $(this).attr("descuento");
        if (descuento == '') descuento = 0;
        
        var precio = $(this).attr("precio");
        
        var bodega = $(this).attr("bodega");
        bodega = parseInt(bodega,10);
        
        var precio_venta = $(this).attr("precio_venta");        
        
        cadenaDatosArticulos += id+";"+cantidad+";"+descuento+";"+precio+";"+bodega+";"+precio_venta+";"+"|";
        
    });
      
    $("#cadenaArticulosPrecios").val(cadenaDatosArticulos);
}

/**
 * Funcion encargada de armar el html para agregar una fila a la lista de articulos
 * 
 * @param array datos contiene toda la informacion del articulo
 * @param boolean disabledPrecioVenta si TRUE agrega un identificador a la fila, que inhabilita los campos precio de venta y % ganancia
 * @returns {String} codigo html que representa una fila de la tabla del listado de articulos
 */
function generarFilaArticulo(datos, disabledPrecioVenta){
    
            //funciones y validaciones que se dan si un articulo ya existe en el listado
            var changeId = '';
            
            if (disabledPrecioVenta) { 
                datos['precioVenta'] = 0; 
                datos['ganancia_venta'] = 0; 
                changeId = '_no';//se agrega esto a la clase (de los campos ganancia venta y precio venta) por si se agrega de nuevo valores a la ganacia general no afecte a estos campos
            }
            
            var cantidad = 1;
            if(datos["cantidad"]) {
                cantidad = datos["cantidad"];
            }
    
            var fila =  "";
                fila += "<tr id='fila_"+datos['contadorArticuloCompra']+"' cod='"+datos['id']+"' iva='"+datos['iva']+"'  cantidad = '1' descuento = '0' precio_venta = '"+datos['precioVenta']+"' bodega = '"+datos['bodega']+"' precio='"+datos['precio']+"'  subtotal = '"+datos['subtotal']+"' subtotal_con_descuentos='0' class='filaArticuloCompra'>";
                    fila += "   <td><input type='text' disabled  id='articuloFactura_"+datos['contadorArticuloCompra']+"' value='"+datos['articulo']+"' ayuda='"+datos['articulo']+"' class='campoDescripcionArticulo medioMargenSuperior' maxlength='255' size='50' name='' /></td>";
                    fila += "   <td><input class=' soloNumeros cantidadArticuloCompra campoCantidadArticulo valorMinimo' size='3' valor_minimo = '1' value = '"+cantidad+"'/></td>";
                    fila += "   <td><input class=' soloNumeros descuentoGeneralArticuloCompra campoDescuentoArticulo campoPorcentaje rangoNumeros' rango='1-99' value = '"+datos['descuento']+"' size='3' maxlength='2' /></td>";
                    fila += "   <td><input class=' precioUnitarioArticuloCompra campoPrecioUnitario campoDinero' size='8' value = '"+datos['precio']+"' /></td>";
                    fila += "   <td><input disabled class=' subtotalArticuloCompra campoSubtotalArticulo campoDinero' size='8' value = '"+datos['subtotal']+"'/></td>";
                    fila += "   <td><img src='media/estilos/imagenes/eliminar.png' class='imagenEliminarItemFila' ayuda='Eliminar este articulo<br>del listado' id='imagenEliminarItemFila"+datos['contadorArticuloCompra']+"' /></td>";
                    fila += "   <td><img src='media/estilos/imagenes/consultar.png' class='imagenConsultarItemFila' ayuda='Consultar articulo' id='imagenConsultarItemFila"+datos['contadorArticuloCompra']+"' /></td>";
                    fila += "   <td><input "+disabledPrecioVenta+" class=' porcentajeGanancia"+changeId+" campoPorcentajeGanancia campoPorcentaje' size='3' value = '"+datos['ganancia_venta']+"' maxlength='3' /></td>";
                    fila += "   <td><input "+disabledPrecioVenta+" class=' precioVentaArticulo"+changeId+"  campoPrecioVentaArticulo campoDinero' size='8' value = '"+datos['precioVenta']+"' /></td>";            
                    fila += "   <td><img src='media/estilos/imagenes/mover.png' class='imagenMoverItemBodega' ayuda='Mover a bodega diferente' id='imagenMoverItemBodega"+datos['contadorArticuloCompra']+"' /></td>";
                    fila += "   <td><span class='txtBodegaCompra'>Bodega <span class = 'idBodegaCompra'> "+parseInt(datos['bodega'],10)+"</span></span></td>";
                fila += "</tr>";
            
            return fila;
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
        $("#articuloFactura").val("");
        
        if($("#contenedorInfoArticulo").is(":visible")){
            $("#contenedorInfoArticulo").slideUp("fast");
        }        
        
        if (typeof(_obj) == "undefined") {
            $("#BoxOverlayTransparente").css("display","none");
            return;
        }
        
        var articulo = _obj.label;//nombre del articulo, viene en la respuesta
        
        var bodega = $("#idBodegaGeneral").val();
        
        var id          = _obj.id;//id del articulo
        
        var iva         = _obj.iva;//iva del articulo        
        
        var precio = _obj.value;//precio del articulo
        if(precio !== '' || precio !== '0' || precio !== 0 && precio !== null){             
            precio = parseDouble(precio);
            
        } else {
            precio = 0;
            
        }  

        var descuento = $("#campoDescuentoListadoArticulos").val();//capturo el campo del descuento general
            
        var subtotal = precio;//por base el subtotal es el mismo precio
        
        if(descuento != ''){//si hay algún descuento
            descuento     = parseDouble(descuento);
            var subtotal1 = ( descuento * precio ) / 100;//aplico dicho descuento
            subtotal      = precio - subtotal1;
        } 
                      
        //validar si el proveedor va a vender con iva
        if ($("#regimenProveedor").val() != 1) {
            subtotal += (subtotal * (iva / 100));
        } 

        var ganancia_venta = $("#campoGananciaListadoArticulos").val();//capturo el valor del campo % total ganancia factura
        if(ganancia_venta != ''){
            ganancia_venta = parseDouble(ganancia_venta);
        }

        var precioVenta = 0;//por base el precio de venta es 0
        
        //var valPredIva = (iva != 0) ? iva : $("#valorIva").val();
        var valPredIva =  iva;
        
        //hack para quitar el iva de los calculos de precio de venta
        var valPredIva2 = 0;
        
        if (subtotal != '' && subtotal != '0' && subtotal != 0 && ganancia_venta != '') {//si el articulo tiene subtotal, y se ha introducido un % de ganancia sobre el articulo
            precioVenta = ( subtotal + ( (subtotal *  ganancia_venta) / 100)) + ( (subtotal *  valPredIva2) / 100) ;//aplico ese porcentaje de ganancia sobre el subtotal de venta
            
        } else if (subtotal != '' && subtotal != '0' && subtotal != 0 && ganancia_venta == '') {//si no hay porcentaje de ganancia escrito, se toma el predeterminado
            var porcPredGanancia = $("#porcPredGanancia").val();
            porcPredGanancia     = parseDouble(porcPredGanancia);            
            precioVenta          = (subtotal + ( (subtotal *  porcPredGanancia) / 100) ) + ( (subtotal *  valPredIva2) / 100);
            
        } else {
            precioVenta = '0';//sino este sigue siendo cero, esto para no modificar ese valor en la BD, pues antes de introducirlo, se verificará que no sea 0
            
        }     
                      
        var existeEnListado = false;//asi se verifica que el articulo no exista en el listado
        var idFila = '';
        $(".filaArticuloCompra").each(function(){//recorro cada articulo del listado en busca que ya se encuentre
            
            idFila = $(this).attr("cod");
            
            if(idFila == id){//si el ide de una de las filas es igual al id del articulo que esta a punto de ser introducido
                existeEnListado = true;//determina que esta en el listado
                
            }
            
        });
        
        var disabledPrecioVenta = '';
            
        if(existeEnListado){//detiene la ejecucion y muestra una alerta
            
            disabledPrecioVenta = 'disabled=disabled';//
            
            Sexy.confirm("<p class=margin5>El articulo que deseas agregar ya se encuentra en el listado. Esta seguro de querer agregar otro?</span> </p>", {
                onComplete: function(returnvalue){ 
                    if(!returnvalue){
                        $("#BoxOverlayTransparente").css("display","none"); 
                        $("#articuloFactura").val("");
                        $("#articuloFactura").focus();                          
                        return;
                        
                    } else {
                        contadorArticuloCompra ++;

                        var datos = new Array();//arreglo con la informacion del articulo para armar la fila

                        datos['contadorArticuloCompra'] = contadorArticuloCompra;
                        datos['id']                     = parseInt(id, 10);
                        datos['precio']                 = parseDouble(precio);
                        datos['precioVenta']            = parseDouble(precioVenta);
                        datos['bodega']                 = bodega;
                        datos['subtotal']               = parseDouble(subtotal);
                        datos['articulo']               = articulo;
                        datos['descuento']              = descuento;
                        datos['ganancia_venta']         = parseDouble(ganancia_venta);  
                        datos['iva']                    = parseInt(valPredIva, 10);

                        var fila =  generarFilaArticulo(datos, disabledPrecioVenta);//llamada al metodo que retorna la fila ya armada

                        $("#tablaListaArticulosFactura").find("#thead").after(fila);//aqui concatena el codigo al DOM

                        armarCadenaDatosArticulos();//llama a la funcion que se encarga de armar la cadena de datos de articulos
                        //esta cadena lleva todos los datos a ser guardados en la tabla articulos_factura_compra, *ver documentacion de funcion para mas detalles

                        setTimeout(function(){//espero un tiempo prudente a que se ejecute todo
                            calcularTotalFactura();//y calculo el total de la factura
                            $("#articuloFactura").val("");
                            $("#articuloFactura").focus();
                        }, 250);    

                        $("#BoxOverlayTransparente").css("display","none");     

                        $('*').tooltip({//activo el tooltip paralos elementos que recien que se añaden
                            track: true,
                            delay: 0,
                            showURL: false
                        });
                        
                        $("#articuloFactura").val("");
                        $("#articuloFactura").focus();                          

                        return false;                           
                        
                    }
   
                }
            });  
                      
        } else {
            contadorArticuloCompra ++;

            var datos = new Array();//arreglo con la informacion del articulo para armar la fila

            datos['contadorArticuloCompra'] = contadorArticuloCompra;
            datos['id']                     = parseInt(id, 10);
            datos['precio']                 = parseDouble(precio);
            datos['precioVenta']            = parseDouble(precioVenta);
            datos['bodega']                 = bodega;
            datos['subtotal']               = parseDouble(subtotal);
            datos['articulo']               = articulo;
            datos['descuento']              = descuento;
            datos['ganancia_venta']         = parseDouble(ganancia_venta);  
            datos['iva']                    = parseInt(valPredIva, 10);

            var fila =  generarFilaArticulo(datos, '');//llamada al metodo que retorna la fila ya armada

            $("#tablaListaArticulosFactura").find("#thead").after(fila);//aqui concatena el codigo al DOM

            armarCadenaDatosArticulos();//llama a la funcion que se encarga de armar la cadena de datos de articulos
            //esta cadena lleva todos los datos a ser guardados en la tabla articulos_factura_compra, *ver documentacion de funcion para mas detalles

            setTimeout(function(){//espero un tiempo prudente a que se ejecute todo
                calcularTotalFactura();//y calculo el total de la factura
                $("#articuloFactura").val("");
                $("#articuloFactura").focus();
            }, 250);    

            $("#BoxOverlayTransparente").css("display","none");     

            $('*').tooltip({//activo el tooltip paralos elementos que recien que se añaden
                track: true,
                delay: 0,
                showURL: false
            });

            return false;              
            
        }     
}

/**
 * Funcion que se encarga de resetear la factura
 * @returns {undefined}
 */
function resetFactura(){
        $(".filaArticuloCompra").remove();
        
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