var arbol = $(".vistaArbol");

if(arbol.length){
    arbol.checkboxTree ({
        onCheck: {
            ancestors: 'check',
            descendants: 'uncheck'
        },

        onUncheck: {
            node: 'collapse',
            ancestors: 'check',
            descendants: 'uncheck'
        },

        initializeChecked: 'expanded',
        initializeUnchecked: 'collapsed'
    });
            
}

var inputsSelected = arbol.find("input:checked");

inputsSelected.removeAttr("disabled");

inputsSelected.bind("click", function(e){
   e.preventDefault(); 
});

setTimeout(function(){
    
    $(".imagen_cargando").fadeOut("fast", function(){
        arbol.fadeIn("fast");
    })
}, 1800);