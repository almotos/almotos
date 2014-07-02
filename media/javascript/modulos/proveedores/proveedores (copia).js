/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
var contador = 0;
var ejecutar = "";
var proceder = "";

function agregarCamposCuenta(){
    ejecutar = "proceder";
    validarFormularioCuentas();   

}


function validarFormularioCuentas(){
    var banco  = $("#campoNombreBanco").val();
    var cuenta = $("#campoNumeroCuenta").val(); 
//    var prueba = "solo hola";
    if(banco == ""){
        Sexy.alert("Debes escoger un banco");
        $("#campoNombreBanco").focus();
        ejecutar = "detener";
//        return NULL;//machetazo
        
    } else if(cuenta == ""){
        Sexy.alert("Debes ingresar un numero de cuenta");
        $("#campoNumeroCuenta").focus();
        ejecutar = "detener";
//        return NULL;//machetazo
    } else{
        $.ajax({
            type:"POST",
            url:"/ajax/bancos/verificar",
            dataType:"json",
            data: {datos: banco},
            success:procesaRespuestaProveedores
        });

    }
    
    return true;       
}

/**
 * Funcion que recibe la respuesta ajax del ultimo else de la funcion validarformularioCuentas
 * y verifica que se exte verificando la existencia de un banco y que la respuesta haya sido
 * positiva o negativa. En caso de que se este consultando un banco, y este no exista mostrara un alert
 * y detendrá la ejecucion del script
 */
function procesaRespuestaProveedores(respuesta){
    if(respuesta.verificaExistenciaBanco && !respuesta.consultaExistenciaBanco){
        Sexy.alert("Debes escoger un banco de la lista que se muestra");
        ejecutar = "detener";
        return false;
    }else if(respuesta.verificaExistenciaBanco && respuesta.consultaExistenciaBanco){
        setTimeout(function(){
            if(ejecutar == "proceder"){//verifico que en ningun punto de las validaciones se halla cambiado este valor al encontrar un error
                contador ++;
                //capturo los valores de los campos de la info de la cuenta
                var banco  = $("#campoNombreBanco").val();
                var cuenta = $("#campoNumeroCuenta").val();    
                var tipoCuenta = $("#listaTipoCuenta option:selected").val();
                //declaro dos variables para que dependiendo del valor del tipo de la cuenta me agregue la opcion correcta en el select
                var ahorros = "";
                var corriente = "";
                //ejecuto el condicional a ver que opcion va seleccionada
                if(tipoCuenta == 2){ahorros = "selected"}else{corriente="selected"}


                var codigo  = "<tr id='"+contador+"'><td><input value='"+banco+"' id='bancoPreAgregado"+contador+"' class='autocompletable ui-autocomplete-input bancoPreAgregado' type='text' title='/ajax/bancos/listar' maxlength='255' size='40' name='datos[id_banco]' autocomplete='off' role='textbox' aria-autocomplete='list' aria-haspopup='true'></td>";
                    codigo += "<td><input type='text' size='20' name'cuenta_"+contador+"' value= '"+cuenta+"' class='cuentaPreAgregada' id='cuentaPreAgregada"+contador+"'></td>";
                    codigo += "<td><select id='listaTipoCuenta' name'datosTipoCuenta_"+contador+"'><option value='1' "+ahorros+">Ahorros</option><option value='2' "+corriente+">Corriente</option><select></td>";
                    codigo += "<td><img src='media/estilos/imagenes/eliminar.png' class='imagenEliminarItem' id='imagenEliminarItem"+contador+"' /></td><tr>";
                $("#tablaCuentasBancosProveedores tbody").append(codigo);
                $("#textoCuentaAgregada").slideDown(300);
                $("#campoNombreBanco").val("");
                $("#campoNumeroCuenta").val("");
                $("#campoNombreBanco").focus();
                setTimeout(function(){$("#textoCuentaAgregada").slideUp(300);}, 1300);
            }
        }, 200);
        return true;
    }
    
}


$(document).ready(function(){
    
    $("#botonAdicionarcuenta").live("click", function(e){
        e.preventDefault();
        agregarCamposCuenta();
        
    });
    
    $(".imagenEliminarItem").live("click", function(){
        $(this).parents("tr").fadeOut("fast");
//        setTimeout(function(){
        $(this).parents("tr").remove();
//        }, 500);
        
        
    });
    
    $("#checkAutoretenedor").live("click", function(){
        if(this.checked == true){
            $("#checkRetefuente").attr("checked", false);
            $("#checkReteica").attr("checked", false);
        }else{
            $("#checkRetefuente").attr("checked", true);
            $("#checkReteica").attr("checked", true);
        }
        
    });
    
    
    
    
    $("#botonAgregarProveedores").live("click", function(e){//click en el boton del formulario
        e.preventDefault();
        proceder = "verdad";//variable que determina si se envia el formulario via ajax o no
        
        $(".bancoPreAgregado").each(function(){//verifico los nombres de los bancos agregados en las cuentas antes de que se vayan al servidor por si sufren algun tipo de modificacion
            var valor = $(this).val();
            var campo = $(this);
            if(valor == ""){
                proceder = "falso";               
                $(this).addClass("textFieldBordeRojo");
                Sexy.alert("Hay un campo de nombre de banco vacio, si no deseas usar mas<br> este banco, debes eliminarlo haciendo click en la X de la derecha");
            }else{//aqui verifico que efectivamente dicho banco exista
                $.ajax({
                    type:"POST",
                    url:"/ajax/bancos/verificar",
                    dataType:"json",
                    data: {
                        datos: valor
                    },
                    success:function(respuesta){
                        if(!respuesta.consultaExistenciaBanco){
                            proceder = "falso";
                            campo.addClass("textFieldBordeRojo");
                            Sexy.alert("Debes escoger un banco de la lista que se muestra");
                        }
                    }
                });
 
            }
  
        });
         
         
        $(".cuentaPreAgregada").each(function(){//verifico los nombres de los bancos agregados en las cuentas antes de que se vayan al servidor por si sufren algun tipo de modificacion
            var valor = $(this).val();
            if(valor == ""){
                proceder = "falso";               
                $(this).addClass("textFieldBordeRojo");
                Sexy.alert("Hay un campo de numero de cuenta vacio, si no deseas usar mas<br> esta cuenta, debes eliminarlo haciendo click en la X de la derecha");
            }
  
        });         
            
         
        setTimeout(function(){
            if(proceder == "verdad"){
                $("#indicadorEspera").css("display","block");
                $("#BoxOverlay").css("display","block");
                formulario = $("#botonAgregarProveedores").parents("form");
                destino = $(formulario).attr("action");
                console.log(destino);
                $(formulario).ajaxForm();
                $(formulario).ajaxSubmit({
                    dataType:"json",
                    success:function(respuesta){
                        procesaRespuesta(respuesta);
                    }
                });
            }else{
                return false;
            }
        }, 100);


            
    });
        
        
    
});