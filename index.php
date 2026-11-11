
<?php
// Iniciar sesión para reconocer al usuario
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Conexión a la base de datos
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

// Función para mostrar texto de forma segura
function escapar($texto) {
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
          href="assets/css/style.css?v=4">

    <script src="assets/js/main.js" defer></script>
</head>

<body class="pagina-inicio">

<!-- =====================================
     HEADER COMPARTIDO
===================================== -->

<?php require __DIR__ . '/includes/header.php'; ?>


<main>

    <!-- =====================================
         HERO PRINCIPAL
    ===================================== -->

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

        <div class="hero-grafico" aria-hidden="true">

            <div class="cd-decorativo">
                <div class="cd-centro"></div>
            </div>

            <span class="estrella estrella-uno">✳</span>
            <span class="estrella estrella-dos">✦</span>
            <span class="hero-sticker">MINI CD!</span>

        </div>

    </section>


    <!-- =====================================
         CINTA DECORATIVA
    ===================================== -->

    <div class="cinta" aria-hidden="true">

        <div class="cinta-contenido">
            ✦ MINI CD ✦ MAXI RECUERDOS ✦
            YOUR MUSIC ✦ YOUR STORY ✦
            MINI CD ✦ MAXI RECUERDOS ✦
        </div>

    </div>


    <!-- =====================================
         SOBRE NOSOTROS
    ===================================== -->

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


    <!-- =====================================
         GALERÍA STELLAR CD
    ===================================== -->

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

        <div class="carrusel"
             aria-label="Galería Stellar CD">

            <div class="carrusel-pista"
                 id="carruselPista">


                <!-- DIAPOSITIVA 1 -->

                <article class="diapositiva diapositiva-rosa">

                    <span class="numero-slide">
                        01 / 05
                    </span>

                    <div class="slide-contenido">

                        <span class="slide-icono"
                              aria-hidden="true">✳</span>

                        <h3>
                            YOUR<br>MUSIC.
                        </h3>

                        <p>MUSIC EDITION</p>

                    </div>

                    <span class="slide-marca">
                        STELLAR CD ©
                    </span>

                </article>


                <!-- DIAPOSITIVA 2 -->

                <article class="diapositiva diapositiva-turquesa">

                    <span class="numero-slide">
                        02 / 05
                    </span>

                    <div class="slide-contenido">

                        <span class="slide-icono"
                              aria-hidden="true">✦</span>

                        <h3>
                            MAKE IT<br>YOURS.
                        </h3>

                        <p>CUSTOM EDITION</p>

                    </div>

                    <span class="slide-marca">
                        STELLAR CD ©
                    </span>

                </article>


                <!-- DIAPOSITIVA 3 -->

                <article class="diapositiva diapositiva-pastel">

                    <span class="numero-slide">
                        03 / 05
                    </span>

                    <div class="slide-contenido">

                        <span class="slide-icono"
                              aria-hidden="true">♡</span>

                        <h3>
                            KEEP<br>MEMORIES.
                        </h3>

                        <p>MEMORY EDITION</p>

                    </div>

                    <span class="slide-marca">
                        STELLAR CD ©
                    </span>

                </article>


                <!-- DIAPOSITIVA 4 -->

                <article class="diapositiva diapositiva-blanca">

                    <span class="numero-slide">
                        04 / 05
                    </span>

                    <div class="slide-contenido">

                        <span class="slide-icono"
                              aria-hidden="true">★</span>

                        <h3>
                            MADE<br>WITH LOVE.
                        </h3>

                        <p>LOVE EDITION</p>

                    </div>

                    <span class="slide-marca">
                        STELLAR CD ©
                    </span>

                </article>


                <!-- DIAPOSITIVA 5 -->

                <article class="diapositiva diapositiva-oscura">

                    <span class="numero-slide">
                        05 / 05
                    </span>

                    <div class="slide-contenido">

                        <span class="slide-icono"
                              aria-hidden="true">✧</span>

                        <h3>
                            STAY<br>STELLAR.
                        </h3>

                        <p>STELLAR EDITION</p>

                    </div>

                    <span class="slide-marca">
                        STELLAR CD ©
                    </span>

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


    <!-- =====================================
         PRODUCTOS DESTACADOS
    ===================================== -->

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

                    <!-- IMAGEN -->

                    <div class="producto-visual">

                        <?php if (!empty($producto['imagen'])): ?>

                            <img
                                src="assets/img/<?php
                                    echo escapar(
                                        basename($producto['imagen'])
                                    );
                                ?>"
                                alt="<?php
                                    echo escapar(
                                        $producto['nombre']
                                    );
                                ?>">

                        <?php else: ?>

                            <span class="producto-cd"
                                  aria-hidden="true">
                                ◉
                            </span>

                        <?php endif; ?>

                    </div>


                    <!-- INFORMACIÓN -->

                    <div class="producto-datos">

                        <h3>
                            <?php
                                echo escapar(
                                    $producto['nombre']
                                );
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


    <!-- =====================================
         PERSONALIZACIÓN
    ===================================== -->

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

        <a href="catalogo.php"
           class="boton-principal">

            DESCUBRIR OPCIONES ↗

        </a>

    </section>

</main>


<!-- =====================================
     FOOTER
===================================== -->

<footer id="contacto" class="site-footer">

    <div>

        <h2>
            STELLAR CD ✳
        </h2>

        <p>
            Mini CD, maxi recuerdos.
        </p>

    </div>

    <div class="footer-links">

        <a href="catalogo.php">
            Catálogo
        </a>

        <a href="admin/login.php">
            Administrador
        </a>

    </div>

    <p class="copyright">
        © <?php echo date('Y'); ?> Stellar CD.
    </p>

</footer>

</body>
</html>
