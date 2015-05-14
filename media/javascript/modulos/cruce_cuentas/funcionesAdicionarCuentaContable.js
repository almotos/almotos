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

escucharCampoBaseUvt();