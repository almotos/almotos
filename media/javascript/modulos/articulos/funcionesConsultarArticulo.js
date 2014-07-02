function imprimirBarcode(obj){
    
    var id          = obj.attr('id_articulo');
    var cantidad    = $("#campoCantidadBarcode").val();
    var destino     = 'ajax/articulos/imprimirBarcode';
    
    $("#BoxOverlay").css("display","block");
    $("#indicadorEspera").css("display","block");

    $.ajax({
        type:"POST",
        url:destino,
        dataType:"json",
        data: {
            id: id,
            cantidad: cantidad
        },
        success:procesaRespuesta
    });
    
}



function consultarKardex(obj){
    var id              = obj.attr('id_articulo');
    var destino         = '/ajax/articulos/consultarKardex';
    var fechaInicio     = $("#fechaInicioKardex").val();
    var fechaFin        = $("#fechaFinKardex").val();
    
    if (fechaInicio == "" || fechaFin == "") {
        Sexy.alert("Debe seleccionar un rango de fechas para consultar el kardex");
        return;
    }
    
    var bodega = '0';
    //verificar si se quiere filtrar también por bodega
    if($("#filtrarKardexBodega").is(":checked")){
        bodega = obj.parents(".contenidoPestana").find("#selectorBodegas").val();
    }
    
    $("#BoxOverlay").css("display","block");
    $("#indicadorEspera").css("display","block");

    $.ajax({
        type:"POST",
        url:destino,
        dataType:"json",
        data: {
            id: id,
            fechaInicio: fechaInicio,
            fechaFin: fechaFin,
            idBodega : bodega
        },
        success:procesaRespuesta
    });
}

    /*
     *Codigos que agregan via ajax los options con las bodegas
     *segun la sede que se seleccione
     **/
    function seleccionarBodegas(obj){
        var sede = obj.val();        
        if(sede != ''){
            $.ajax({
                type:"POST",
                url:"/ajax/bodegas/escogerBodega",
                data: {
                    idSede : sede
                },
                dataType:"json",
                success:function (respuesta){
                    obj.parents("#contenedorSelectorBodegas").find("#selectorBodegas").html(respuesta.contenido);
                }

            });          
           
        }   
    }
    
    /**
     * función que muestra los campos para seleccionar la bodega para que el 
     * kardex sea filtrado por bodega tambien
     */
    function filtrarKardexBodega(obj){
        if(obj.is(":checked")){
            $("#contenedorSelectorBodegas").fadeIn("fast");
            
        } else {
            $("#contenedorSelectorBodegas").fadeOut("fast");
            
        }
        
        /**
         * Función que agrega el plugin chosen ver 
         **/
        $(".selectChosen").chosen({no_results_text: "Oops, sin resultados!"});      
        
    }
    
    
    /**
     * Funcion encargada de consultar via ajax cada vez que se clickea un titulo de las 
     * pestañas de la ventana modal "Consultar Articulos"
     */
    $(".titulosConsultarArticulo").bind("click", function(){
        var idPestana  = $(this).attr("id");
        var idArticulo = $("#idArticuloConsulta").val();
        var idDestino  = $(this).attr("href");
        
        $.ajax({
            type:"POST",
            url:"/ajax/articulos/seeMore",
            data: {
                pestana : idPestana,
                id      : idArticulo,
                destino : idDestino
            },
            dataType:"json",
            success:procesaRespuesta

        });          
    });
    
    
    /**
     * Funciones para cargar el grafico de compra y venta de articulos
     */
    $("#info-economica").bind("click", function(){

        var data = '';//inicializo los datos del grafico

        var idArticulo = $("#idArticuloConsulta").val();//capturo el id del articulo del cual voy a hacer la consulta

        $("#graficoKardexArticulo").addClass("cargando2");//agrego la clase de espera

        //var tipo = $("#graficoKardexArticulo").data("tipo-grafico");//capturo el tipo de grafico que voy a generar

        $.ajax({
            type:"POST",
            url:"/ajax/articulos/datosGrafico",
            data: {
                id          : idArticulo,
                tipo        : 'barras'
            },
            dataType:"json",
            success:function(respuesta){

                data = respuesta.datos;

                var largo       = data.datasets.length;
                //volver a entero los valores que vienen de la BD para poder ser leidos por el grafico
                for(var i = 0; i< largo; i++){
                    var largo1 = data.datasets[i].data.length;

                    for(var j = 0; j< largo1; j++){
                        data.datasets[i].data[j] = parseInt(data.datasets[i].data[j], 10);
                    }

                }                

                setTimeout(function(){
                    $("#graficoKardexArticulo").removeClass("cargando2");
                    var myChar = new Chart(document.getElementById("graficoKardexArticulo").getContext("2d")).Bar(data);                        
                }, 1000)


            },
            error: function(xhr){
                $("#graficoKardexArticulo").removeClass("cargando2");
                Sexy.alert("Hubo un error al procesar la petición, verifique que los datos sean validos");
            } 

        });          
        
    });
    
    
    
     
    
    
