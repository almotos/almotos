/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


    /**
     *  funcion llamada cuando se va a imprimir una factura, ya sea por pos o impresora normal
     * @returns {undefined}
     */
    $(".btnImpresionFactura").off("click").click(function(e){
        e.preventDefault();
        
        var $form = $(this).parents("form");
        
        var efectivo    = '<input type="hidden" value="'+$("#campoEfectivo").val()+'" name="datos[campo_efectivo]">';
        var tarjeta     = '<input type="hidden" value="'+$("#campoTarjeta").val()+'" name="datos[campo_tarjeta]">';
        var cheque      = '<input type="hidden" value="'+$("#campoCheque").val()+'" name="datos[campo_cheque]">';
        var credito     = '<input type="hidden" value="'+$("#campoCredito").val()+'" name="datos[campo_credito]">';
        
        var campos  =   $(efectivo + tarjeta+ cheque+ credito);
        
        $form.append(campos);
        
        enviarFormulario($form);
        
        $(".btnImpresionFactura").parents(".ui-dialog").find(".ui-dialog-titlebar-close").trigger("click");
        
    }); 
    
    
    //funciones para habilitar los campos de medios de pago
    $("#checkEfectivo").live("click",function(){
        if ($(this).is(":checked")) {
            $("#campoEfectivo").removeAttr("disabled");
            
        } else {            
            $("#campoEfectivo").attr("disabled", "disabled");
            
        }


    });   
    
    
    $("#checkTarjeta").live("click",function(){
        if ($(this).is(":checked")) {
            $("#campoTarjeta").removeAttr("disabled");
            
        } else {            
            $("#campoTarjeta").attr("disabled", "disabled");
            
        }


    }); 
    
    $("#checkCheque").live("click",function(){
        if ($(this).is(":checked")) {
            $("#campoCheque").removeAttr("disabled");
            
        } else {            
            $("#campoCheque").attr("disabled", "disabled");
            
        }
       

    }); 
    
    $("#checkCredito").live("click",function(){
        if ($(this).is(":checked")) {
            $("#campoCredito").removeAttr("disabled");
            $(".campoFechaVtoFact").removeClass("oculto");
            
        } else {            
            $("#campoCredito").attr("disabled", "disabled");
            $(".campoFechaVtoFact").addClass("oculto");
            
        }
       

    });    
    
    
    $("#campoTotalPagoCliente").live("keyup", function(){

        var total = $("#campoTotalFinFactura").val();
            total = parseDouble(total);
            
        var totalPago = $(this).val();
            totalPago = parseDouble(totalPago);
            
        var valADevolver = totalPago - total;
            valADevolver = parseDouble(valADevolver);
            
        valADevolver = (valADevolver > 0) ? valADevolver : 0;
            
        $("#valADevolver").html("$"+valADevolver);
            
    });
    
    
    /**
     * Funcion encargada de modificar los valores del total a pagar y del valor de las retenciones
     */
    $("#actualizarValoresRetenciones").live("click", function(e){
        //primero calcular los nuevos valores de las retenciones si son modificados
        var total               = $("#campoTotalFinFactura").val();
        var totalRetenciones    = 0;
        var totalAPagar         = 0;
        var datosRetenciones    = "";//cadena dividida por ";" y por "|" para ser enviada a la clase factura de compra
        
        $.each($(".camposRetenciones"), function(){
            var valor = $(this).val();
                valor = (valor != "") ? valor : 0;
                valor = parseDouble(valor);                
            totalRetenciones += valor;
            datosRetenciones += $(this).attr("id")+";"+valor+"|";//aqui se va armando la cadena
        });
        
        totalAPagar = total - totalRetenciones;
        totalAPagar = parseDouble(totalAPagar);
        
        //poner lo valores en los campos
        $("#campoTotalRetenciones").val(totalRetenciones);
        $("#textoTotalRetenciones").html('$'+totalRetenciones);
        
        $("#campoTotalAPagar").val(totalAPagar);
        $("#textoTotalAPagar").html('$'+totalAPagar);   
        
        $("input[name='datos[retenciones]']").val(datosRetenciones);//y aqui se agrega a los formularios este nuevo valor
        
    }).live("mouseenter", function(){
        $(this).css("text-decoration", "underline");
        
    }).live("mouseleave", function(){
        $(this).css("text-decoration", "none");
        
    });    