/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
var contador = 0;
var ejecutar = "";
var proceder = "";
var datosCuentas = "";

////arreglo que almacenara los datos de las cuentas para enviar a la BD
//var arregloCuentas = new Array() ;

//agregar el chosen a la lista desplegable de regimen tributario
$('[name="datos[regimen]"]').chosen();

/**
 * Cuando unas funciones javascript se agregan en vivo no se puede usar la funcion live, solo 
 * la funcion bind
 **/



function validarFormularioCuentas(){
    var banco  = $("#campoNombreBanco");
    var cuenta = $("#campoNumeroCuenta"); 

    if(banco.val() == ""){
        banco.focus();
        banco.addClass("textFieldBordeRojo");
        banco.addClass("campo_obligatorio");
        
    } else if(cuenta.val() == ""){
        cuenta.focus();
        cuenta.addClass("textFieldBordeRojo");  
        cuenta.addClass("campo_obligatorio");

    } else{
        $.ajax({
            type:"POST",
            url:"/ajax/bancos/verificar",
            dataType:"json",
            data: {datos: banco.val()},
            success:verificarExistenciaBanco
        });

    }
    
    return true;       
}

/**
 * Funcion que se encarga de ir recorriendo cada una de las filas
 * agregadas para las cuentas bancarias, captura algunos atributos
 * y con ellos arma la cadena que se envia al servidor. Esta cadena tiene
 * el id_banco, cuenta, tipo_cuenta, separados por |(pipe) y cada cuenta
 * esta separada por [(llave apertura). Para mas info ver
 * /ajax/proveedores/add en la parte que se agregan las cuentas
 **/
function crearCadenaInfoCuentas(){
        var datosCuentas = '';
        
        $(".filaCuentaBanco").each(function(i){
            var idBanco     = $(this).attr("id_banco");
            var cuenta      = $(this).attr("cuenta");
            var tipoCuenta  = $(this).attr('tipo_cuenta');

            datosCuentas += idBanco+"|"+cuenta+"|"+tipoCuenta+"[";            

        });
        
        $("#campoCadenaCuentasProveedor").val(datosCuentas);        
  
}


/**
 * Funcion que recibe la respuesta ajax del ultimo else de la funcion validarformularioCuentas
 * y verifica que se exte verificando la existencia de un banco y que la respuesta haya sido
 * positiva o negativa. En caso de que se este consultando un banco, y este no exista mostrara un alert
 * y detendrá la ejecucion del script
 */
function verificarExistenciaBanco(respuesta){
    
    var campoBanco =  $("#campoNombreBanco");
    
    if(respuesta.verificaExistenciaBanco && !respuesta.consultaExistenciaBanco){        
        campoBanco.addClass("textFieldBordeRojo");
        campoBanco.addClass("autocompletable_obligatorio");
        campoBanco.val("");
        campoBanco.focus();
        
    }else {
        
        contador ++;
        //capturo los valores de los campos de la info de la cuenta
        var banco       = campoBanco.val();
        var idBanco     = campoBanco.next('input').val();
        var cuenta      = $("#campoNumeroCuenta").val();    
        var tipoCuenta  = $("#listaTipoCuenta option:selected").val();
        

        var nombreTipo = "corriente";
        
        if(tipoCuenta == 1){nombreTipo = "ahorros"}

        var codigo  = "<tr id='fila_"+contador+"' class='filaCuentaBanco' id_banco= '"+parseInt(idBanco, 10)+"' cuenta='"+cuenta+"' tipo_cuenta='"+tipoCuenta+"'><td><p class='subtitulo centrado'>"+banco+"</p></td>";
            codigo += "<td><p class='subtitulo centrado'>"+cuenta+"</p></td>";
            codigo += "<td><p class='subtitulo centrado'>"+nombreTipo+"</p></td>";
            codigo += "<td><img src='media/estilos/imagenes/eliminar.png' class='imagenEliminarItem margenIzquierdaDoble' id='imagenEliminarItem"+contador+"' onclick='eliminarFila($(this))' /></td><tr>";
        

        $("#tablaCuentasBancosProveedores tbody").append(codigo);
        $("#textoCuentaAgregada").slideDown(300);        
        $("#campoNumeroCuenta").val("");
        campoBanco.val("");
        campoBanco.focus();
        setTimeout(function(){$("#textoCuentaAgregada").slideUp(300);}, 1300); 
        
        crearCadenaInfoCuentas();

    }
    
}

    
    $("#botonAdicionarcuenta").bind("click", function(e){
        e.preventDefault();
        validarFormularioCuentas();
        
    });
    
    /**
     * Funcion encargada de eliminar un tr con la info de un banco.
     * Es llamada cuando se hace click en la imagen con una X.
     */
    function eliminarFila(obj){
        obj.parents("tr").fadeOut("fast");
        obj.parents("tr").remove();
        crearCadenaInfoCuentas();        
        
    }
    
    
    $("#botonAgregarProveedores").bind("click", function(e){//click en el boton del formulario
        e.preventDefault();
        var pestanas    = $("#pestanasAgregar");
        var formulario  = $(this).parents("form");
        
        if(formulario.find("#nombreProveedor").val() == ""){
            formulario.find("#nombreProveedor").addClass("textFieldBordeRojo");
            formulario.find("#nombreProveedor").addClass("campo_obligatorio");
            formulario.find("#nombreProveedor").focus();
            pestanas.find(".ui-tabs-nav li a:first").trigger("click");
            
        } else if(formulario.find("#idProveedor").val() == ""){
            formulario.find("#idProveedor").addClass("textFieldBordeRojo");
            formulario.find("#idProveedor").addClass("campo_obligatorio");
            pestanas.find(".ui-tabs-nav li a:first").trigger("click");  
            formulario.find("#idProveedor").focus();
            
        } else if(formulario.find("#ciudadSede").val() == ""){
            formulario.find("#ciudadSede").addClass("textFieldBordeRojo");
            formulario.find("#ciudadSede").addClass("campo_obligatorio");
            pestanas.find(".ui-tabs-nav li a:first").trigger("click");
            formulario.find("#ciudadSede").focus();
            
        } else if(formulario.find("#telefonoSede").val() == ""){
            formulario.find("#telefonoSede").addClass("textFieldBordeRojo");
            formulario.find("#telefonoSede").addClass("campo_obligatorio");
            pestanas.find(".ui-tabs-nav li a:first").trigger("click");   
            formulario.find("#telefonoSede").focus();        
            
        } else if(formulario.find("#direccionSede").val() == ""){
            formulario.find("#direccionSede").addClass("textFieldBordeRojo");
            formulario.find("#direccionSede").addClass("campo_obligatorio");
            pestanas.find(".ui-tabs-nav li a:first").trigger("click");  
            formulario.find("#direccionSede").focus();          
            
        } else if(formulario.find("#campoDocumentoPersona").val() == ""){
            formulario.find("#campoDocumentoPersona").addClass("textFieldBordeRojo");
            formulario.find("#campoDocumentoPersona").addClass("campo_obligatorio");
            pestanas.find(".ui-tabs-nav li a:eq(1)").trigger("click");      
            formulario.find("#campoDocumentoPersona").focus();      
            
        } else if(formulario.find("#campoPrimerNombrePersona").val() == ""){
            formulario.find("#campoPrimerNombrePersona").addClass("textFieldBordeRojo");
            formulario.find("#campoPrimerNombrePersona").addClass("campo_obligatorio");
            pestanas.find(".ui-tabs-nav li a:eq(1)").trigger("click");      
            formulario.find("#campoPrimerNombrePersona").focus();       
            
        } else if(formulario.find("#campoPrimerApellidoPersona").val() == ""){
            formulario.find("#campoPrimerApellidoPersona").addClass("textFieldBordeRojo");
            formulario.find("#campoPrimerApellidoPersona").addClass("campo_obligatorio");
            pestanas.find(".ui-tabs-nav li a:eq(1)").trigger("click");     
            formulario.find("#campoPrimerApellidoPersona").focus();       
            
        } else if(formulario.find("#campoCelularPersona").val() == ""){
            formulario.find("#campoCelularPersona").addClass("textFieldBordeRojo");
            formulario.find("#campoCelularPersona").addClass("campo_obligatorio");
            pestanas.find(".ui-tabs-nav li a:eq(1)").trigger("click");     
            formulario.find("#campoCelularPersona").focus();       
            
        } else if(formulario.find("#campoActividadEconomica").val() == ""){
            formulario.find("#campoActividadEconomica").addClass("textFieldBordeRojo");
            formulario.find("#campoActividadEconomica").addClass("campo_obligatorio");
            pestanas.find(".ui-tabs-nav li a:eq(3)").trigger("click");     
            formulario.find("#campoActividadEconomica").focus();       
            
        } else{
            
            $.ajax({
                type:"POST",
                url:"/ajax/inicio/verificar",
                dataType:"json",
                data: {
                    tabla: "lista_ciudades", columna: "cadena", valor: $("#ciudadSede").val()
                },
                success:function(respuesta){
                    if(!respuesta.existeItem){
                        $("#ciudadSede").val("");
                        $("#ciudadSede").addClass("textFieldBordeRojo");
                        $("#ciudadSede").addClass("autocompletable_obligatorio");
                        $(".ui-tabs-nav li a:first").trigger("click");
                        $("#ciudadSede").focus();
                        return false;
                    }else{

                        setTimeout(function(){
                                $("#indicadorEspera").css("display","block");
                                $("#BoxOverlay").css("display","block");
                                
                                destino = $(formulario).attr("action");
                                $(formulario).ajaxForm();
                                $(formulario).ajaxSubmit({
                                    dataType:"json",
                                    success:function(respuesta){
                                        procesaRespuesta(respuesta);
                                    }
                                });
                        }, 200);            

                        return true;
                    }

                }
            });

            
        }
        
            
    });