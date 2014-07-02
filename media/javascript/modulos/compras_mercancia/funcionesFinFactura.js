/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

    $("#chkImprimirBarcodes").live("click", function(){
         if($(this).is(":checked")){
             $("#contenedorFormaImpresion").find("form").find("input[name='datos[imprimir_codigos_barras]']").val('1');
             
         } else {
             $("#contenedorFormaImpresion").find("form").find("input[name='datos[imprimir_codigos_barras]']").val('0');
             
         }
         
    });
    
    //funciones para habilitar los campos de medios de pago
    $("#checkEfectivo").live("click",function(){
        $campoEfectivo = $("#campoEfectivo");
        
        if ($(this).is(":checked")) {
            $campoEfectivo.removeAttr("disabled");
            $campoEfectivo.focus();
            
        } else {            
            $campoEfectivo.attr("disabled", "disabled");
            
        }

    });   
    
    
    $("#checkTarjeta").live("click",function(){
        $campoTarjeta = $("#campoTarjeta");
        
        if ($(this).is(":checked")) {
            $campoTarjeta.removeAttr("disabled");
            $campoTarjeta.focus();
            
        } else {            
            $campoTarjeta.attr("disabled", "disabled");
            reajustarValores($("#campoTarjeta"));
        }
       

    }); 
    
    $("#checkCheque").live("click",function(){
        $campoCheque = $("#campoCheque");
        
        if ($(this).is(":checked")) {
            $campoCheque.removeAttr("disabled");
            $campoCheque.focus();
            
        } else {            
            $campoCheque.attr("disabled", "disabled");
            reajustarValores($("#campoCheque"));
        }
       

    }); 
    
    $("#checkCredito").live("click",function(){
        $campoCredito  = $("#campoCredito");
        $campoFechaVto = $(".campoFechaVtoFact");
        
        if ($(this).is(":checked")) {
            $campoCredito.removeAttr("disabled");
            $campoFechaVto.removeClass("oculto");
            $campoCredito.focus();
            
        } else {            
            $campoCredito.attr("disabled", "disabled");
            $campoFechaVto.addClass("oculto");
            reajustarValores($("#campoCredito"));
            
        }
       
    });     
    
    /**
     * funcion encargada de poner el valor de una casilla en cero 
     * y devolver ese valor a credito
     */
    function reajustarValores(campo) {
        var valor1 = $("#campoEfectivo").val();
            valor1 = (valor1 != "") ? parseDouble(valor1) : 0;
            
        var valor2 = campo.val();
            valor2 = (valor2 != "") ? parseDouble(valor2) : 0;            
        
        $("#campoEfectivo").val(valor1 + valor2);
        campo.val("");
        
    }
    
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
    
    /**
     * Funcion encargada de agregar el valor del campo fecha de vencimiento factura a los formularios de envio
     */
    $(".campoFechaVtoFact").live("change", function(){
        var $form           = $(".btnImpresionFactura").parents("form");        
        var $campoOculto    = $('<input type="hidden" value="'+$(this).val()+'" name="datos[fecha_vto_factura]">');
        
        $form.append($campoOculto);
    });
    
    asignarValoresCampos();

    /**
     * Funcion encargada de calcular los valores de los campos de sistemas de pago
     * @returns {undefined}
     */
    function asignarValoresCampos()
    {
        var campoEfectivo    = $("#campoEfectivo");
        var campoTarjeta     = $("#campoTarjeta");
        var campoCheque      = $("#campoCheque");
        var campoCredito     = $("#campoCredito");
        
        var campos           = {campoefectivo : campoEfectivo,
                                campoTarjeta  : campoTarjeta,
                                campoCheque   : campoCheque,
                                campoCredito  : campoCredito
                                };
        
        var valorPrevio = campoEfectivo.attr("valor_maximo");
        
        var that = this;
        
        campoTarjeta.on("keyup", function(e){        
            that.calcularValores();
        });
        
        campoCheque.on("keyup", function(e){            
            that.calcularValores();
        });
        
        campoCredito.on("keyup", function(e){            
            that.calcularValores();
        });        
        
        this.calcularValores = function () 
        {
            var valorTarjeta    = campoTarjeta.val();
                valorTarjeta    = (valorTarjeta != "") ? parseDouble(valorTarjeta) : 0;
                valorTarjeta    = that.limitarValores(campoTarjeta, valorTarjeta);
                
            var valorCheque     = campoCheque.val();
                valorCheque     = (valorCheque != "") ? parseDouble(valorCheque) : 0;
                valorCheque     = that.limitarValores(campoCheque, valorCheque);
            
            var valorCredito    = campoCredito.val();
                valorCredito    = (valorCredito != "") ? parseDouble(valorCredito) : 0;
                valorCredito    = that.limitarValores(campoCredito, valorCredito);
            
            campoEfectivo.val(valorPrevio - (valorTarjeta + valorCheque + valorCredito));
        };
        /**
         * funcion que limita los valores de los campos para que ni individualmente
         * ni la suma de los 4 campos tenga un valor mayor al del total
         * @param {objecto} campo = objeto jquery que representa el campo sobre el cual se esta escribiendo
         * @param {string} valor  = valor que se esta introduciendo en el campo  
         * @returns bool|string
         */
        this.limitarValores = function(campo, valor)
        {
            var valor = campo.val();
                valor = (valor != "") ? parseDouble(valor) : 0;
                //verificar el valor individual del campo
                if (valor >= that.valorPrevio) {
                    return;
                }
                //verificar la suma del resto de campos
//                var cantActual = 0;
//                
//                for (var camp in campos) {
//                    //verificar si el objeto campo del loop es diferente al que recibe como parametro
//                    if(campos[camp][0] != campo[0]){
//                        var tempVal = campos[camp].val();
//                            tempVal = (tempVal != "") ? parseDouble(tempVal) : 0;
//                            
//                        cantActual += tempVal;
//                    }
//                }
//                //sumar a la cantidad actual el valor que se desea introducir
//                cantActual += valor;
//                
//                //verificar el valor individual del campo
//                if (cantActual >= that.valorPrevio) {
//                    return;
//                }                
                
                return valor;
        };
        
    }
    
    
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
    