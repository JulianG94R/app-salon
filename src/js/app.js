let paso = 1;
const pasoInicial = 1;
const pasoFinal = 3;

const cita = {
    id: '',
    nombre: '',
    fecha: '',
    hora: '',
    servicios: []
}

document.addEventListener('DOMContentLoaded', function () {
    iniciarApp();
});

function iniciarApp() {
    mostrarSeccion(); //Muestra y Oculta las secciones
    tabs(); //Cambia la seccion a medida que se seleccionen las pestañas
    botonesPaginador(); //Agrega o quita los botones del paginador
    paginaSiguiente();
    paginaAnterior();

    consultarAPI();

    idCliente();
    nombreCliente();
    seleccionarFecha();
    seleccionarHora();

    mostrarResumen();
}

function mostrarSeccion() {
    //Ocultar la seccion que tenga la clase de mostrar
    const seccionAnterior = document.querySelector('.mostrar');
    if (seccionAnterior) {
        seccionAnterior.classList.remove('mostrar');
    }

    //Seleccionar la seecion con el paso
    const seccion = document.querySelector(`#paso-${paso}`);
    seccion.classList.add('mostrar');

    //Quitar la clase de actual
    const tabAnterior = document.querySelector('.actual');
    if (tabAnterior) {
        tabAnterior.classList.remove('actual');
    }

    //Resalta la pestaña actual
    const tab = document.querySelector(`[data-paso="${paso}"]`);
    tab.classList.add('actual');
}

function tabs() {
    const botones = document.querySelectorAll('.tabs button');

    botones.forEach(boton => {
        boton.addEventListener('click', function (e) {
            paso = parseInt(e.target.dataset.paso);
            mostrarSeccion();
            botonesPaginador();
        });
    })
}

function botonesPaginador() {
    const paginaAnt = document.querySelector('#anterior');
    const paginaSig = document.querySelector('#siguiente');

    if (paso === 1) {
        paginaAnt.classList.add('ocultar');
        paginaSig.classList.remove('ocultar');

    } else if (paso === 3) {
        paginaAnt.classList.remove('ocultar');
        paginaSig.classList.add('ocultar');

        mostrarResumen();

    } else {
        paginaAnt.classList.remove('ocultar');
        paginaSig.classList.remove('ocultar');

    }

    mostrarSeccion();
}

function paginaAnterior() {
    const paginaAnterior = document.querySelector('#anterior');
    paginaAnterior.addEventListener('click', function () {

        if (paso <= pasoInicial) return;
        paso--;

        botonesPaginador();
    })
}

function paginaSiguiente() {
    const paginaSiguiente = document.querySelector('#siguiente');
    paginaSiguiente.addEventListener('click', function () {

        if (paso >= pasoFinal) return;
        paso++;

        botonesPaginador();
    })
}

async function consultarAPI() {
    try {
        const url = `${location.origin}/api/servicios`;
        const resultado = await fetch(url);
        const servicios = await resultado.json();
        mostrarServicios(servicios);

    } catch (error) {
        console.log(error);
    }
}


function mostrarServicios(servicios) {
    servicios.forEach(servicio => {
        const { id, nombre, precio } = servicio;

        const nombreServicio = document.createElement('P');
        nombreServicio.classList.add('nombre-servicio');
        nombreServicio.textContent = nombre;

        const precioServicio = document.createElement('P');
        precioServicio.classList.add('precio-servicio');
        precioServicio.textContent = `$${precio}`;

        const servicioDiv = document.createElement('DIV');
        servicioDiv.classList.add('servicio');
        servicioDiv.dataset.idServicio = id;
        servicioDiv.onclick = function () {
            seleccionarServicio(servicio);
        };

        servicioDiv.appendChild(nombreServicio);
        servicioDiv.appendChild(precioServicio);

        document.querySelector('#servicios').appendChild(servicioDiv);
    });
}

function seleccionarServicio(servicio) {
    const { id } = servicio;
    const { servicios } = cita;

    //Identifica el elemento que se da click
    const divServicio = document.querySelector(`[data-id-servicio="${id}"]`);

    //Comprobar si un servicio de agrego o quito
    if (servicios.some(agregado => agregado.id === id)) {
        //Eliminarlo
        cita.servicios = servicios.filter(agregado => agregado.id !== id);
        divServicio.classList.remove('seleccionado');
    } else {
        //Agregarlo
        cita.servicios = [...servicios, servicio];
        divServicio.classList.add('seleccionado');
    }
    //console.log(cita);
}

function idCliente() {
    cita.id = document.querySelector('#id').value;
}

function nombreCliente() {
    const nombre = document.querySelector('#nombre').value;
    cita.nombre = nombre;
}

function seleccionarFecha() {
    const inputFecha = document.querySelector('#fecha');
    inputFecha.addEventListener('input', function (e) {
        const dia = new Date(e.target.value).getUTCDay();

        if ([0, 1, 6].includes(dia)) {
            e.target.value = '';
            mostrarAlerta('Sábado, Domingo y Lunes no abrimos', 'error', '.formulario');
        } else {
            cita.fecha = e.target.value;
        }
        //console.log(dia);
    });
}

function mostrarAlerta(mensaje, tipo, elemento, desaparece = true) {
    //Previene mas de una alerta
    const alertaPrevia = document.querySelector('.alerta');
    if (alertaPrevia) {
        alertaPrevia.remove();
    };

    const alerta = document.createElement('DIV');
    alerta.textContent = mensaje;
    alerta.classList.add('alerta');
    alerta.classList.add(tipo);

    const referencia = document.querySelector(elemento);
    referencia.appendChild(alerta);

    if (desaparece) {
        setTimeout(() => {
            alerta.remove();
        }, 3000);
    }
}
function seleccionarHora() {
    const inputHora = document.querySelector('#hora');
    inputHora.addEventListener('input', function (e) {
        const horaCita = e.target.value;
        const hora = horaCita.split(":")[0];
        if (hora < 9 || hora >= 20) {
            e.target.value = '';
            mostrarAlerta('No trabajamos en la hora seleccionada, Intente con otra', 'error', '.formulario');
        } else {
            cita.hora = e.target.value;
        }

    })
}

function mostrarResumen() {
    const resumen = document.querySelector('.contenido-resumen');

    //Limpiar el contenido del resumen
    while (resumen.firstChild) {
        resumen.removeChild(resumen.firstChild);
    }

    if (Object.values(cita).includes('') || cita.servicios.length === 0) {
        mostrarAlerta('Debe completar todos los campos para poder seguir', 'error', '.contenido-resumen', false);

        return;
    }

    //Formatear el div de resumen
    const { nombre, fecha, hora, servicios } = cita;


    //Heading para Servicios
    const headingServicios = document.createElement('H3');
    headingServicios.textContent = 'Resumen de Servicios';
    resumen.appendChild(headingServicios);

    servicios.forEach(servicio => {
        const { id, precio, nombre } = servicio;
        const contenedorServicio = document.createElement('DIV');
        contenedorServicio.classList.add('contenedor-servicio');

        const textoServicio = document.createElement('P');
        textoServicio.textContent = nombre;

        const precioServicio = document.createElement('P');
        precioServicio.innerHTML = `<span>Precio:</span> $${precio}`;

        contenedorServicio.appendChild(textoServicio);
        contenedorServicio.appendChild(precioServicio);

        resumen.appendChild(contenedorServicio);

    });

    //Heading Cita
    const headingCita = document.createElement('H3');
    headingCita.textContent = 'Resumen de Cita';
    resumen.appendChild(headingCita);

    const nombreCliente = document.createElement('P');
    nombreCliente.innerHTML = `<span>Nombre:</span> ${nombre}`;

    //Formateo de Fecha para usuarios hispanohablantes
    const fechaObj = new Date(fecha);
    const mes = fechaObj.getMonth();
    const dia = fechaObj.getDate() + 2;
    const anio = fechaObj.getFullYear();

    const fechaUTC = new Date(Date.UTC(anio, mes, dia));

    const opciones = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    const fechaFormateada = fechaUTC.toLocaleDateString('es-AR', opciones);

    const fechaCita = document.createElement('P');
    fechaCita.innerHTML = `<span>Fecha:</span> ${fechaFormateada}`;

    const horaCita = document.createElement('P');
    horaCita.innerHTML = `<span>Hora:</span> ${hora} Hs.`;

    //Boton Crear Cita
    const botonReserva = document.createElement('BUTTON');
    botonReserva.classList.add('boton');
    botonReserva.textContent = 'Reservar Cita';
    botonReserva.onclick = reservarCita;

    resumen.appendChild(nombreCliente);
    resumen.appendChild(fechaCita);
    resumen.appendChild(horaCita);

    resumen.appendChild(botonReserva);

    //console.log(cita);
}

async function reservarCita() {

    const { nombre, fecha, hora, servicios, id } = cita;

    const idServicios = servicios.map(servicio => servicio.id);

    const datos = new FormData();

    // datos.append('nombre', nombre);
    datos.append('fecha', fecha);
    datos.append('hora', hora);
    datos.append('usuarioId', id);
    datos.append('servicios', idServicios);

    try {
        //Peticion hacia API
        const url = `${location.origin}/api/citas`

        const respuesta = await fetch(url, {
            method: 'POST',
            body: datos
        });
        
        const resultado = await respuesta.json();
        console.log(resultado);
        if (resultado.resultado) {
            Swal.fire({
                icon: "success",
                title: "Cita Confirmada",
                text: "Tu cita fue confirmada correctamente!",
                button: 'OK'
            }).then(() => {
                setTimeout(() => {
                    window.location.reload();
                }, 3000);
            })
        }

    } catch (error) {
        Swal.fire({
            icon: "error",
            title: "Error",
            text: "Tu cita no pudo ser confirmada. Intenta de nuevo mas tarde",
            //button: 'Cerrar'
        })
    }
}