
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Ubicación | Stellar CD</title>

    <link rel="stylesheet"
          href="assets/css/style.css?v=9">

    <script src="assets/js/main.js?v=2" defer></script>
    <script src="assets/js/ubicacion.js" defer></script>
</head>

<body>

<?php require __DIR__ . '/includes/header.php'; ?>

<main class="ubicacion-pagina">

    <section class="ubicacion-hero">
        <span class="etiqueta">STELLAR CD / LOCATION 001</span>

        <h1>
            ENCUENTRA
            <span>EL LUGAR. ✳</span>
        </h1>

        <p>
            Explora la ubicación del Instituto de
            Arquitectura, Diseño y Arte de la UACJ.
        </p>
    </section>

    <section class="ubicacion-contenido">

        <div class="ubicacion-informacion">
            <span class="etiqueta">DESTINATION / 002</span>

            <h2>IADA <span>UACJ.</span></h2>

            <p>
                Instituto de Arquitectura,
                Diseño y Arte.
            </p>

            <h3>DIRECCIÓN</h3>

            <p>
                Av. del Charro 450 Norte,
                Col. Partido Romero, C. P. 32310,
                Ciudad Juárez, Chihuahua.
            </p>

            <button
                type="button"
                id="obtenerUbicacion"
                class="ubicacion-boton">
                OBTENER MI UBICACIÓN ↗
            </button>

            <p id="estadoUbicacion"
               role="status"
               aria-live="polite">
                Puedes compartir tu ubicación
                para obtener indicaciones.
            </p>

            <a
                id="comoLlegar"
                class="ubicacion-boton ubicacion-secundario"
                href="https://www.google.com/maps/search/?api=1&amp;query=IADA%20UACJ%20Av%20del%20Charro%20450%20Ciudad%20Juarez"
                target="_blank"
                rel="noopener noreferrer">
                CÓMO LLEGAR ↗
            </a>
        </div>

        <div class="ubicacion-mapa">
            <iframe
                title="Mapa de IADA UACJ"
                src="https://www.google.com/maps?q=IADA%20UACJ%20Av%20del%20Charro%20450%20Ciudad%20Juarez&amp;output=embed"
                width="100%"
                height="450"
                style="border:0;"
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
                allowfullscreen>
            </iframe>
        </div>

    </section>

</main>

<footer class="site-footer">
    <div>
        <h2>STELLAR CD ✳</h2>
        <p>Mini CD, maxi recuerdos.</p>
    </div>

    <p class="copyright">
        © <?php echo date('Y'); ?> Stellar CD.
    </p>
</footer>

</body>
</html>
