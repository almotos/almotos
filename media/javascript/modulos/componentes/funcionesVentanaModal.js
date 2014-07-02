
var contador        = 0;
var ejecutar        = "";
var proceder        = "";
var datosCuentas    = "";


/**
 * Cuando unas funciones javascript se agregan en vivo no se puede usar la funcion live, solo 
 * la funcion bind
 **/

function validarFormularioAcciones(){
    var nomAccion       = $("#campoNomAccion");
    var nomAccionMenu   = $("#campoNomAccionMenu");    
    var idModulo        = $("#idModulo").val();

    if(nomAccion.val() == ""){
        nomAccion.focus();
        nomAccion.addClass("textFieldBordeRojo");
        nomAccion.addClass("campo_obligatorio");
        
    } else if(nomAccionMenu.val() == ""){
        nomAccionMenu.focus();
        nomAccionMenu.addClass("textFieldBordeRojo");  
        cuenta.addClass("campo_obligatorio");

    } else{
        $.ajax({
            type:"POST",
            url:"/ajax/acciones/verificarExistenciaAccion",
            dataType:"json",
            data: {
                nomAccion   : nomAccion.val(),
                idModulo    : idModulo
            },
            success:verificarExistenciaAccion
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
function crearCadenaInfoAcciones(){
        var datosAcciones = '';
        
        $(".filaAccionesModulo").each(function(i){
            var nomAccion       = $(this).attr("nom_accion");
            var nomAccionMenu   = $(this).attr("nom_accion_menu");

            datosAcciones += nomAccion+"|"+nomAccionMenu+"[";            

        });
        
        $("#campoCadenaAccionesModulo").val(datosAcciones);        
  
}


/**
 * Funcion que recibe la respuesta ajax del ultimo else de la funcion validarformularioAcciones
 * y verifica que se exte verificando la existencia de una accion en el modulo y que la respuesta haya sido
 * positiva o negativa. En caso de que se este consultando una accion y esta exista ya en el modulo, mostrara un alert
 * y detendrá la ejecucion del script
 */
function verificarExistenciaAccion(respuesta){
    
    var nomAccion       =  $("#campoNomAccion");
    var nomAccionMenu   = $("#campoNomAccionMenu");
    
    if(respuesta.verificaExistenciaAccion && respuesta.consultaExistenciaAccion){        
        $("#textoNotificacionAcciones").html("Esta acción ya existe en este módulo").removeClass("letraVerde").addClass("letraRoja");
        $("#textoNotificacionAcciones").slideDown(300); 
        setTimeout(function(){$("#textoNotificacionAcciones").slideUp(300);}, 2500);         
        nomAccion.addClass("textFieldBordeRojo");
        nomAccion.val("");
        nomAccion.focus();
        
    } else {
        
        contador ++;
        //capturo los valores de los campos de la info de la cuenta   

        
        
        var codigo  = "<tr id='fila_"+contador+"' class='filaAccionesModulo'  nom_accion='"+nomAccion.val()+"' nom_accion_menu='"+nomAccionMenu.val()+"'>";
            codigo += "<td><p class='subtitulo centrado'>"+nomAccion.val()+"</p></td>";
            codigo += "<td><p class='subtitulo centrado'>"+nomAccionMenu.val()+"</p></td>";
            codigo += "<td><img src='media/estilos/imagenes/eliminar.png' class='imagenEliminarItem margenIzquierdaDoble' id='imagenEliminarItem"+contador+"' onclick='eliminarFila($(this))' /></td><tr>";
        

        $("#tablaAccionesModulo tbody").append(codigo);
        
        
        $("#textoNotificacionAcciones").html("Accion pre-agregada").removeClass("letraRoja").addClass("letraVerde");
        $("#textoNotificacionAcciones").slideDown(300); 
        setTimeout(function(){$("#textoNotificacionAcciones").slideUp(300);}, 2500); 
        
        nomAccionMenu.val("");
        nomAccion.val("");
        nomAccion.focus();
        
        
        
        crearCadenaInfoAcciones();

    }
    
}

    
    $("#botonAdicionarAccion").bind("click", function(e){
        e.preventDefault();
        validarFormularioAcciones();
        
    });
    
    /**
     * Funcion encargada de eliminar un tr con la info de un banco.
     * Es llamada cuando se hace click en la imagen con una X.
     */
    function eliminarFila(obj){
        obj.parents("tr").fadeOut("fast");
        obj.parents("tr").remove();
        crearCadenaInfoAcciones();        
        
    }
    

/**
 * funcionalidades para eliminar una accion o editar una acción
 */
$(".editarAccion").on("click", function(){
    var idModulo        = $("#idModulo").val();  
    var id              = $(this).parents("tr").attr("id");

    id = id.split("_");
    id = Number(id[1]);    
    
    $.ajax({
        type:"POST",
        url:"/ajax/acciones/editarAccion",
        dataType:"json",
        data: {
            idModulo    : idModulo,
            id          : id
        },
        success: procesaRespuesta
    });    
});    

$(".eliminarAccion").on("click", function(){
    var id = $(this).parents("tr").attr("id");

    id = id.split("_");
    id = Number(id[1]);

    $.ajax({
        type:"POST",
        url:"/ajax/acciones/delete",
        dataType:"json",
        data: {
            id    : id
        },
        success: procesaRespuesta
    });    
    
}); 