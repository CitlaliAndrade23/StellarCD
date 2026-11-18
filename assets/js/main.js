
/* =====================================
   1. CARRUSEL STELLAR CD
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
   2. MOSTRAR Y OCULTAR CONTRASEÑA
   INICIO DE SESIÓN
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

        botonVer.setAttribute(
            "aria-pressed",
            String(mostrar)
        );

        botonVer.setAttribute(
            "aria-label",
            mostrar ? "Ocultar contraseña" : "Mostrar contraseña"
        );

    });

});


/* =====================================
   3. MOSTRAR Y OCULTAR CONTRASEÑAS
   REGISTRO
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
   4. MENÚS DESPLEGABLES
   PRODUCTOS E INFORMACIÓN
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

    // Cerrar menús con Escape

    document.addEventListener("keydown", function (evento) {

        if (evento.key === "Escape") {

            menus.forEach(function (menu) {
                menu.open = false;
            });

        }

    });

});


/* =====================================
   5. MENÚ HAMBURGUESA RESPONSIVE
===================================== */

document.addEventListener("DOMContentLoaded", function () {

    const botonMenu = document.getElementById("menuToggle");
    const menu = document.getElementById("menuPrincipal");

    if (!botonMenu || !menu) {
        return;
    }

    // Cerrar menú móvil

    function cerrarMenu() {

        menu.classList.remove("menu-abierto");

        botonMenu.classList.remove("activo");

        botonMenu.setAttribute(
            "aria-expanded",
            "false"
        );

        botonMenu.setAttribute(
            "aria-label",
            "Abrir menú"
        );

        // Cerrar submenús

        menu.querySelectorAll("details").forEach(function (detalle) {
            detalle.open = false;
        });

    }

    // Abrir y cerrar con el botón hamburguesa

    botonMenu.addEventListener("click", function () {

        const abierto = menu.classList.toggle("menu-abierto");

        botonMenu.classList.toggle("activo", abierto);

        botonMenu.setAttribute(
            "aria-expanded",
            String(abierto)
        );

        botonMenu.setAttribute(
            "aria-label",
            abierto ? "Cerrar menú" : "Abrir menú"
        );

        if (!abierto) {

            menu.querySelectorAll("details").forEach(function (detalle) {
                detalle.open = false;
            });

        }

    });

    // Cerrar menú al seleccionar una página

    menu.querySelectorAll("a").forEach(function (enlace) {

        enlace.addEventListener("click", function () {
            cerrarMenu();
        });

    });

    // Cerrar menú con Escape

    document.addEventListener("keydown", function (evento) {

        if (evento.key === "Escape") {
            cerrarMenu();
        }

    });

    // Restablecer al cambiar a escritorio

    window.addEventListener("resize", function () {

        if (window.innerWidth > 768) {
            cerrarMenu();
        }

    });

});
