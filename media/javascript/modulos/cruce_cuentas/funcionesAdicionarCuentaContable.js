/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


function escucharCampoBaseUvt() {
    this.forma = $("#formaAdicionarCuenta");
    
    var campoBaseUvt    = this.forma.find("input#baseTotalUvt");
    var textoPesos      = this.forma.find("span#totalPesosUvt");
    var valorUvt        = this.forma.find("span#valorUvt");
    
    campoBaseUvt.keyup(function() {
                            var base    = campoBaseUvt.val();
                            
                            if (base == "") {
                                base = 0;
                            }
                            
                            base = parseInt(base);
                            
                            var valUvt  = parseInt(valorUvt.html());
                            
                            textoPesos.html("$" + ( base *  valUvt) );

                        });
    
}

//mostrar los campos de impuestos
function escucharCheckImpuestos() {
    this.forma = $("#formaAdicionarCuenta");
    
    var campo    = this.forma.find("input.campoImpuesto");
    var texto    = this.forma.find("p.campoImpuesto");
    
    var check    = this.forma.find("input#checkImpuesto");
    
    check.click(function() {
                            campo.val('');
                            campo.toggleClass("oculto");
                            texto.toggleClass("oculto");

                        });
    
}


//mostrar los campos de impuestos
function escucharCheckMedioPago() {
    this.forma = $("#formaAdicionarCuenta");
    
    var campo    = this.forma.find("span.campoMedioPago");
    var texto    = this.forma.find("p.campoMedioPago");
    
    var check    = this.forma.find("input#checkMedioPago");
    
    check.click(function() {
                            campo.toggleClass("oculto");
                            texto.toggleClass("oculto");

                        });
    
}



escucharCampoBaseUvt();
escucharCheckImpuestos();
escucharCheckMedioPago();


