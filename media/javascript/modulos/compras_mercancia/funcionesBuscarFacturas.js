$("#tablaListarFacturas tr:gt(0)").bind('contextmenu', function(e) {
    // evito que se ejecute el evento
    e.preventDefault();
    
    $(".filaConsultada").removeClass("filaConsultada");
    $(this).addClass("filaConsultada");
    
    var idFactura = $(this).attr("atributo_0");
    $.ajax({
        type:"POST",
        url:"/ajax/facturas_compra/see",
        dataType:"json",
        data: {
            id: idFactura
        },
        success:procesaRespuesta
    });
});