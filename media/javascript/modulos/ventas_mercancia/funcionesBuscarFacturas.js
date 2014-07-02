$("#tablaListarFacturas tr:gt(0)").bind('contextmenu', function(e) {
    // evito que se ejecute el evento
    e.preventDefault();
    
    $(".filaConsultada").removeClass("filaConsultada");
    $(this).addClass("filaConsultada");
    
    var idFactura = $(this).attr("id");
        idFactura = idFactura.split("_")[1];
        
    $.ajax({
        type:"POST",
        url:"/ajax/facturas_venta/see",
        dataType:"json",
        data: {
            id: idFactura
        },
        success:procesaRespuesta
    });
});