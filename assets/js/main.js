
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

    function actualizarCarrusel() {
        pista.style.transform = `translateX(-${actual * 100}%)`;

        contador.textContent =
            `${String(actual + 1).padStart(2, "0")} / ${String(total).padStart(2, "0")}`;
    }

    function iniciarAutomatico() {
        clearInterval(intervalo);

        intervalo = setInterval(function () {
            actual = (actual + 1) % total;
            actualizarCarrusel();
        }, 4000);
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

    const carrusel = document.querySelector(".carrusel");

    if (carrusel) {
        carrusel.addEventListener("mouseenter", function () {
            clearInterval(intervalo);
        });

        carrusel.addEventListener("mouseleave", function () {
            iniciarAutomatico();
        });
    }

    actualizarCarrusel();
    iniciarAutomatico();

});


/* =====================================
   MOSTRAR Y OCULTAR CONTRASEÑA - LOGIN
===================================== */

document.addEventListener("DOMContentLoaded", function () {

    const botonVer = document.getElementById("mostrarContrasena");
    const campoContrasena = document.getElementById("loginContrasena");

    if (!botonVer || !campoContrasena) {
        return;
    }

    botonVer.addEventListener("click", function () {

        const mostrar = campoContrasena.type === "password";

        campoContrasena.type = mostrar ? "text" : "password";
        botonVer.textContent = mostrar ? "OCULTAR" : "VER";

        botonVer.setAttribute("aria-pressed", String(mostrar));

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


/* =====================================
   MENÚS DESPLEGABLES - STELLAR CD
===================================== */

document.addEventListener("DOMContentLoaded", function () {

    const menus = document.querySelectorAll(
        ".site-nav .nav-dropdown"
    );

    // Cerrar el otro menú al abrir uno
    menus.forEach(function (menu) {

        menu.addEventListener("toggle", function () {

            if (!menu.open) return;

            menus.forEach(function (otroMenu) {

                if (otroMenu !== menu) {
                    otroMenu.open = false;
                }

            });

        });

    });

    // Cerrar menús al hacer clic fuera
    document.addEventListener("click", function (evento) {

        menus.forEach(function (menu) {

            if (!menu.contains(evento.target)) {
                menu.open = false;
            }

        });

    });

    // Cerrar menús con la tecla Escape
    document.addEventListener("keydown", function (evento) {

        if (evento.key === "Escape") {

            menus.forEach(function (menu) {
                menu.open = false;
            });

        }

    });

});
