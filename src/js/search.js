document.addEventListener('DOMContentLoaded', function(){
    inicarApp();
});

function inicarApp(){
    buscarFecha();
}

function buscarFecha(){
    const fechaInput = document.querySelector('#fecha');
    fechaInput.addEventListener('input', function(e){
        const fechaSelected = e.target.value;

        window.location = `?fecha=${fechaSelected}`;

        //console.log(fechaSelected);
    });
}