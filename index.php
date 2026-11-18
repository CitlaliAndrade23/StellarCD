
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config/conexion.php';

// Obtener los últimos 3 productos disponibles
$sql = "SELECT
            id_producto,
            nombre,
            descripcion,
            precio,
            imagen
        FROM productos
        WHERE disponible = 1
        ORDER BY id_producto DESC
        LIMIT 3";

$consulta = $conexion->query($sql);
$productos = $consulta->fetchAll();

// Escapar texto para mostrarlo de forma segura
function escapar($texto)
{
    return htmlspecialchars(
        (string) $texto,
        ENT_QUOTES,
        'UTF-8'
    );
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <meta name="description"
          content="Stellar CD: llaveros mini CD personalizados con tus canciones, fotografías y recuerdos favoritos.">

    <title>Stellar CD | Tu música, tu historia</title>

    <link rel="stylesheet"
          href="assets/css/style.css?v=21">

    <script src="assets/js/main.js?v=4" defer></script>

    <!-- Google Analytics 4 -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-1XZ2S2MXZD"></script>

<script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }

    gtag('js', new Date());
    gtag('config', 'G-1XZ2S2MXZD');
</script>

<link rel="stylesheet" href="assets/css/responsive.css?v=7">
</head>

<body class="pagina-inicio">

<?php require __DIR__ . '/includes/header.php'; ?>

<main>

    <!-- HERO PRINCIPAL -->

    <section class="hero">

        <div class="hero-texto">

            <span class="etiqueta">
                YOUR MUSIC, YOUR STORY / 001
            </span>

            <h1>
                TU MÚSICA
                <span>EN OTRA</span>
                DIMENSIÓN.
            </h1>

            <p>
                Tus canciones favoritas, tus fotografías
                y tus mejores recuerdos convertidos
                en un mini CD que puedes llevar contigo.
            </p>

            <a href="catalogo.php"
               class="boton-principal">
                EXPLORAR CATÁLOGO ↗
            </a>

        </div>

        <div class="hero-grafico">

    <img
        src="assets/img/llavero1.png"
        alt="Llavero mini CD personalizado de BTS"
        class="hero-llavero"
    >

    <span class="estrella estrella-uno" aria-hidden="true">✳</span>
    <span class="estrella estrella-dos" aria-hidden="true">✦</span>
    <span class="hero-sticker">MINI CD!</span>

</div>
    </section>


    <!-- CINTA DECORATIVA -->

    <div class="cinta" aria-hidden="true">

        <div class="cinta-contenido">
            ✦ MINI CD ✦ MAXI RECUERDOS ✦
            YOUR MUSIC ✦ YOUR STORY ✦
            MINI CD ✦ MAXI RECUERDOS ✦
        </div>

    </div>


    <!-- SOBRE NOSOTROS -->

    <section id="nosotros" class="seccion-nosotros">

        <span class="etiqueta">
            ABOUT STELLAR CD / 002
        </span>

        <h2>
            NO ES SOLO
            <span>UN LLAVERO.</span>
            ES TU HISTORIA.
        </h2>

        <p>
            En Stellar CD transformamos canciones,
            fotografías y momentos especiales en
            llaveros mini CD personalizados.
            Un pequeño objeto para llevar contigo
            algo que significa mucho.
        </p>

    </section>


    <!-- GALERÍA STELLAR CD -->

    <section class="seccion-galeria" id="galeria">

        <div class="galeria-encabezado">

            <div>

                <span class="etiqueta">
                    STELLAR GALLERY / 005
                </span>

                <h2>
                    THE CD
                    <span>COLLECTION ✳</span>
                </h2>

            </div>

            <p>
                Una colección de música, recuerdos
                y creatividad. Descubre el universo
                de Stellar CD.
            </p>

        </div>


       
<!-- CARRUSEL -->

<div class="carrusel" aria-label="Galería Stellar CD">

    <div class="carrusel-pista" id="carruselPista">

        <article class="diapositiva">
            <img src="assets/img/BANNERSTELLARCD_1.png"
                 alt="BANNERSTELLARCD_1.png">
        </article>

        <article class="diapositiva">
            <img src="assets/img/BANNERSTELLARCD_2.png"
                 alt="BANNERSTELLARCD_2.png">
        </article>

        <article class="diapositiva">
            <img src="assets/img/BANNERSTELLARCD_3.png"
                 alt="BANNERSTELLARCD_3.png">
        </article>

        <article class="diapositiva">
            <img src="assets/img/BANNERSTELLARCD_4.png"
                 alt="BANNERSTELLARCD_4.png">
        </article>

        <article class="diapositiva">
            <img src="assets/img/BANNERSTELLARCD_5.png"
                 alt="BANNERSTELLARCD_5.png">
        </article>

    </div>

</div>



        <!-- CONTROLES DEL CARRUSEL -->

        <div class="carrusel-controles">

            <button
                type="button"
                class="carrusel-btn"
                id="carruselAnterior"
                aria-label="Diapositiva anterior">
                ←
            </button>

            <span id="carruselContador"
                  aria-live="polite">
                01 / 05
            </span>

            <button
                type="button"
                class="carrusel-btn"
                id="carruselSiguiente"
                aria-label="Diapositiva siguiente">
                →
            </button>

        </div>

    </section>


    <!-- PRODUCTOS DESTACADOS -->

    <section class="seccion-destacados">

        <div class="titulo-seccion">

            <span class="etiqueta">
                THE COLLECTION / 003
            </span>

            <h2>
                ELIGE TU STELLAR CD ✳
            </h2>

        </div>


        <div class="grid-destacados">

            <?php foreach ($productos as $producto): ?>

                <article class="producto-destacado">

                    <div class="producto-visual">

                        <?php if (!empty($producto['imagen'])): ?>

                            <img
                                src="assets/img/<?php
                                    echo escapar(
                                        basename($producto['imagen'])
                                    );
                                ?>"
                                alt="<?php
                                    echo escapar($producto['nombre']);
                                ?>">

                        <?php else: ?>

                            <span class="producto-cd"
                                  aria-hidden="true">
                                ◉
                            </span>

                        <?php endif; ?>

                    </div>


                    <div class="producto-datos">

                        <h3>
                            <?php
                                echo escapar($producto['nombre']);
                            ?>
                        </h3>

                        <p>
                            <?php
                                echo escapar(
                                    $producto['descripcion'] ?? ''
                                );
                            ?>
                        </p>

                        <strong>
                            $<?php
                                echo number_format(
                                    (float) $producto['precio'],
                                    2
                                );
                            ?> MXN
                        </strong>

                    </div>

                </article>

            <?php endforeach; ?>

        </div>


        <a href="catalogo.php"
           class="enlace-catalogo">
            VER TODOS LOS PRODUCTOS ↗
        </a>

    </section>


    <!-- PERSONALIZACIÓN -->

    <section class="seccion-personaliza">

        <span class="etiqueta">
            MAKE IT YOURS / 004
        </span>

        <h2>
            TU ESTILO.
            <span>TU MÚSICA.</span>
            TU CD.
        </h2>

        <p>
            Elige tus imágenes favoritas y crea
            un llavero que represente tu historia.
        </p>

        <a href="personaliza.php"
           class="boton-principal">
            COMENZAR A PERSONALIZAR ↗
        </a>

    </section>


    <!-- UBICACIÓN IADA UACJ -->

    <section class="inicio-ubicacion" id="ubicacion">

        <div class="inicio-ubicacion-texto">

            <span class="etiqueta">
                FIND THE PLACE / 006
            </span>

            <h2>
                ENCUENTRA
                <span>EL LUGAR. ✳</span>
            </h2>

            <p>
                Conoce la ubicación de nuestro local
                y descubre como llegar.
            </p>

            <a href="ubicacion.php"
               class="boton-principal">
                VER UBICACIÓN IADA ↗
            </a>

        </div>

    </section>

</main>


<?php require __DIR__ . '/includes/footer.php'; ?>

</body>
</html>
