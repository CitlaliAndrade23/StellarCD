
/* =====================================
   CARRUSEL STELLAR CD
===================================== */

document.addEventListener("DOMContentLoaded", function () {

    const pista = document.getElementById("carruselPista");
    const anterior = document.getElementById("carruselAnterior");
    const siguiente = document.getElementById("carruselSiguiente");
    const contador = document.getElementById("carruselContador");

    if (!pista || !anterior || !siguiente || !contador) {
        return;
    }

    const diapositivas = pista.querySelectorAll(".diapositiva");
    const total = diapositivas.length;

    if (total === 0) return;

    let actual = 0;

    let intervalo;

// Avanzar automáticamente cada 4 segundos
function iniciarAutomatico() {
    clearInterval(intervalo);

    intervalo = setInterval(function () {
        actual = (actual + 1) % total;
        actualizarCarrusel();
    }, 4000);
}

    function actualizarCarrusel() {
        pista.style.transform = `translateX(-${actual * 100}%)`;

        contador.textContent =
            `${String(actual + 1).padStart(2, "0")} / ${String(total).padStart(2, "0")}`;
    }

   siguiente.addEventListener("click", function () {
    actual = (actual + 1) % total;
    actualizarCarrusel();
    iniciarAutomatico();
});

anterior.addEventListener("click", function () {
    actual = (actual - 1 + total) % total;
    actualizarCarrusel();
    iniciarAutomatico();
});


    // Pausar el carrusel al pasar el mouse
    const carrusel = document.querySelector(".carrusel");

    if (carrusel) {
        carrusel.addEventListener("mouseenter", function () {
            clearInterval(intervalo);
        });

        carrusel.addEventListener("mouseleave", function () {
            iniciarAutomatico();
        });
    }

    // Mostrar la primera diapositiva
    actualizarCarrusel();

    // Activar el movimiento automático
    iniciarAutomatico();

});



/* =====================================
   MOSTRAR Y OCULTAR CONTRASEÑA
===================================== */

document.addEventListener("DOMContentLoaded", function () {

    const botonVer = document.getElementById("mostrarContrasena");
    const campoContrasena = document.getElementById("loginContrasena");

    if (!botonVer || !campoContrasena) {
        return;
    }

    botonVer.addEventListener("click", function () {

        if (campoContrasena.type === "password") {

            campoContrasena.type = "text";
            botonVer.textContent = "OCULTAR";
            botonVer.setAttribute("aria-pressed", "true");

        } else {

            campoContrasena.type = "password";
            botonVer.textContent = "VER";
            botonVer.setAttribute("aria-pressed", "false");

        }

    });

});


/* =====================================
   MOSTRAR CONTRASEÑAS - REGISTRO
===================================== */

document.addEventListener("DOMContentLoaded", function () {

    const botones = document.querySelectorAll(".registro-ver");

    botones.forEach(function (boton) {

        const idCampo = boton.dataset.passwordTarget;
        const campo = document.getElementById(idCampo);

        if (!campo) return;

        boton.addEventListener("click", function () {

            const mostrar = campo.type === "password";

            campo.type = mostrar ? "text" : "password";

            boton.textContent = mostrar ? "OCULTAR" : "VER";

            boton.setAttribute(
                "aria-pressed",
                String(mostrar)
            );

            boton.setAttribute(
                "aria-label",
                mostrar ? "Ocultar contraseña" : "Mostrar contraseña"
            );

        });

    });

});

