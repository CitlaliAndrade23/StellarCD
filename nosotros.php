
<?php
// Iniciar sesión para reconocer al usuario
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

    <title>Nosotros | Stellar CD</title>

    <link rel="stylesheet"
          href="assets/css/style.css?v=16">

    <script src="assets/js/main.js" defer></script>
    <?php require_once __DIR__ . '/includes/analytics.php'; ?>
    <link rel="stylesheet" href="assets/css/responsive.css?v=4">
</head>



<body class="pagina-nosotros">

<!-- =====================================
     HEADER COMPARTIDO
===================================== -->

<?php require __DIR__ . '/includes/header.php'; ?>


<main>

    <!-- PRESENTACIÓN -->
    <section class="nosotros-hero">

        <div class="nosotros-hero-texto">

            <span class="etiqueta">
                ABOUT STELLAR CD / 001
            </span>

            <h1>
                MÁS QUE
                <span>MÚSICA.</span>
                SOMOS RECUERDOS.
            </h1>

            <p>
                En Stellar CD creemos que cada canción
                guarda una historia. Transformamos
                tus momentos favoritos en mini CD
                personalizados que puedes llevar contigo.
            </p>

        </div>

        <div class="nosotros-grafico" aria-hidden="true">

            <div class="nosotros-disco">
                <div class="nosotros-disco-centro"></div>
            </div>

            <span class="nosotros-estrella">✳</span>

        </div>

    </section>


    <!-- INTRODUCCIÓN -->
    <section class="nosotros-intro">

        <span class="etiqueta">
            OUR STORY / 002
        </span>

        <h2>
            TU CANCIÓN.
            <span>TU HISTORIA.</span>
            TU STELLAR CD.
        </h2>

        <p>
            Stellar CD es un emprendimiento creativo
            dedicado a la elaboración de llaveros
            mini CD personalizados.

            Combinamos música, diseño y recuerdos
            para crear un accesorio especial,
            pensado para expresar emociones,
            celebrar momentos y conectar personas.
        </p>

    </section>


    <!-- =====================================
         MISIÓN Y VISIÓN
    ===================================== -->

    <section class="nosotros-proposito">

        <!-- MISIÓN -->
        <article class="nosotros-mision" id="mision">

            <span class="etiqueta">
                01 / OUR MISSION
            </span>

            <h2>MISIÓN ✦</h2>

            <p>
                Crear llaveros mini CD personalizados
                que permitan a las personas expresar
                sus gustos, emociones y recuerdos
                a través de la música y el diseño,
                ofreciendo productos creativos,
                significativos y elaborados
                con atención al detalle.
            </p>

            <span class="nosotros-numero">01</span>

        </article>


        <!-- VISIÓN -->
        <article class="nosotros-vision" id="vision">

            <span class="etiqueta">
                02 / OUR VISION
            </span>

            <h2>VISIÓN ✳</h2>

            <p>
                Convertirnos en una marca reconocida
                por transformar experiencias musicales
                en recuerdos personalizados,
                destacando por nuestra creatividad,
                innovación y conexión
                con nuestros clientes.
            </p>

            <span class="nosotros-numero">02</span>

        </article>

    </section>


    <!-- =====================================
         VALORES
    ===================================== -->

    <section class="nosotros-valores" id="valores">

        <span class="etiqueta">
            WHAT WE BELIEVE / 003
        </span>

        <h2>
            LO QUE NOS
            <span>HACE STELLAR. ★</span>
        </h2>

        <div class="valores-grid">

            <!-- CREATIVIDAD -->
            <article class="valor-tarjeta">

                <span>✳</span>

                <h3>CREATIVIDAD</h3>

                <p>
                    Buscamos nuevas formas
                    de convertir ideas en
                    diseños originales.
                </p>

            </article>


            <!-- PERSONALIZACIÓN -->
            <article class="valor-tarjeta">

                <span>♡</span>

                <h3>PERSONALIZACIÓN</h3>

                <p>
                    Cada producto refleja
                    los gustos y la historia
                    de quien lo recibe.
                </p>

            </article>


            <!-- CALIDAD -->
            <article class="valor-tarjeta">

                <span>✦</span>

                <h3>CALIDAD</h3>

                <p>
                    Cuidamos los detalles
                    de diseño y elaboración
                    de nuestros productos.
                </p>

            </article>


            <!-- CERCANÍA -->
            <article class="valor-tarjeta">

                <span>★</span>

                <h3>CERCANÍA</h3>

                <p>
                    Escuchamos las ideas
                    de nuestros clientes
                    para crear algo especial.
                </p>

            </article>

        </div>

    </section>


    <!-- =====================================
         CIERRE
    ===================================== -->

    <section class="nosotros-cierre">

        <span class="etiqueta">
            STAY STELLAR / 004
        </span>

        <h2>
            MINI CD.
            <span>MAXI RECUERDOS.</span>
        </h2>

        <p>
            Descubre cómo transformar
            tu canción favorita
            en algo único.
        </p>

        <a href="catalogo.php" class="boton-principal">
            EXPLORAR CATÁLOGO ↗
        </a>

    </section>

</main>

<?php require __DIR__ . '/includes/footer.php'; ?>

</body>
</html>
