
document.addEventListener("DOMContentLoaded", () => {
    const boton = document.getElementById("obtenerUbicacion");
    const estado = document.getElementById("estadoUbicacion");
    const enlace = document.getElementById("comoLlegar");

    const destino = "IADA UACJ, Av. del Charro 450, Ciudad Juárez, Chihuahua";

    boton.addEventListener("click", () => {
        if (!navigator.geolocation) {
            estado.textContent =
                "Tu navegador no admite geolocalización.";
            return;
        }

        estado.textContent = "Obteniendo tu ubicación...";

        navigator.geolocation.getCurrentPosition(
            (posicion) => {
                const lat = posicion.coords.latitude;
                const lon = posicion.coords.longitude;

                estado.textContent =
                    "¡Ubicación obtenida! Ya puedes consultar la ruta.";

                const parametros = new URLSearchParams({
                    api: "1",
                    origin: `${lat},${lon}`,
                    destination: destino,
                    travelmode: "driving"
                });

                enlace.href =
                    `https://www.google.com/maps/dir/?${parametros}`;
            },
            (error) => {
                const mensajes = {
                    1: "No se concedió permiso para acceder a tu ubicación.",
                    2: "No fue posible determinar tu ubicación.",
                    3: "Se agotó el tiempo de espera."
                };

                estado.textContent =
                    mensajes[error.code] ||
                    "Ocurrió un error al obtener tu ubicación.";
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            }
        );
    });
});
