$(document).ready(function(){

$("#boton_pruebas").click(function(e){
  e.preventDefault();
  var nombre 	= $("#nombre_prueba").val();
  var apellido 	= $("#apellido_prueba").val();
  var telefono 	= $("#telefono_prueba").val();
  if(nombre == 0){
    alert("Nombre esta bacio");
    
  }else if(apellido == 0){
    alert("Apellido esta bacio");
    
  }else if(telefono == 0){
    alert("Telefono esta bacio");
    
  }else{
  alert("Nombre = " + nombre + " Apellido = " + apellido + " Telefono = " + telefono);
  
  }
});
  
});