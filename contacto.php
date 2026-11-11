
<?php
// Iniciar sesión para reconocer al usuario
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$mensajeEnviado = false;
$errores = [];

$nombre = '';
$correo = '';
$asunto = '';
$mensaje = '';

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre = trim($_POST['nombre'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $asunto = trim($_POST['asunto'] ?? '');
    $mensaje = trim($_POST['mensaje'] ?? '');

    // Validar nombre
    if ($nombre === '' || mb_strlen($nombre) > 100) {
        $errores[] = 'Ingresa un nombre válido.';
    }

    // Validar correo
    if (
        !filter_var($correo, FILTER_VALIDATE_EMAIL)
        || strlen($correo) > 254
    ) {
        $errores[] = 'Ingresa un correo electrónico válido.';
    }

    // Validar asunto
    if ($asunto === '' || mb_strlen($asunto) > 100) {
        $errores[] = 'Selecciona un asunto válido.';
    }

    // Validar mensaje
    if ($mensaje === '' || mb_strlen($mensaje) > 2000) {
        $errores[] = 'Escribe un mensaje de máximo 2000 caracteres.';
    }

    // Validación exitosa
    if (empty($errores)) {

        // Por ahora solo validamos los datos.
        // Después conectaremos el formulario con MySQL.
        $mensajeEnviado = true;

        $nombre = '';
        $correo = '';
        $asunto = '';
        $mensaje = '';
    }
}

// Función para mostrar texto de forma segura
function escaparContacto($valor) {
    return htmlspecialchars(
        (string) $valor,
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

    <title>Contacto | Stellar CD</title>

    <link rel="stylesheet"
          href="assets/css/style.css?v=7">

    <script src="assets/js/main.js" defer></script>
</head>

<body class="pagina-contacto">

<!-- =====================================
     HEADER COMPARTIDO
===================================== -->

<?php require __DIR__ . '/includes/header.php'; ?>


<main>

    <!-- =====================================
         PRESENTACIÓN
    ===================================== -->

    <section class="contacto-hero">

        <span class="etiqueta">
            LET'S CONNECT / 001
        </span>

        <h1>
            HABLEMOS
            <span>DE TU IDEA. ✳</span>
        </h1>

        <p>
            ¿Tienes una pregunta, una idea para
            personalizar tu mini CD o quieres saber
            más sobre nuestros productos?
            ¡Nos encantará escucharte!
        </p>

        <span class="contacto-estrella"
              aria-hidden="true">
            ✳
        </span>

    </section>


    <!-- =====================================
         FORMULARIO DE CONTACTO
    ===================================== -->

    <section class="contacto-contenido" id="formulario">

        <div class="contacto-intro">

            <span class="etiqueta">
                SEND A MESSAGE / 002
            </span>

            <h2>
                TU MENSAJE
                <span>IMPORTA. ♡</span>
            </h2>

            <p>
                Completa el formulario y cuéntanos
                en qué podemos ayudarte.
            </p>

            <div class="contacto-decoracion"
                 aria-hidden="true">
                ✦ STAY STELLAR ✦
            </div>

        </div>


        <div class="contacto-formulario-caja">

            <h3>ENVÍANOS UN MENSAJE ↗</h3>


            <!-- MENSAJE DE ÉXITO -->

            <?php if ($mensajeEnviado): ?>

                <div class="contacto-exito" role="status">
                    Tus datos se validaron correctamente.
                    El envío real estará disponible cuando
                    conectemos el formulario.
                </div>

            <?php endif; ?>


            <!-- MENSAJES DE ERROR -->

            <?php if (!empty($errores)): ?>

                <div class="contacto-error" role="alert">

                    <?php foreach ($errores as $error): ?>

                        <p>
                            <?php echo escaparContacto($error); ?>
                        </p>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>


            <!-- FORMULARIO -->

            <form method="POST"
                  action="contacto.php#formulario">


                <!-- NOMBRE -->

                <div class="contacto-campo">

                    <label for="contactoNombre">
                        NOMBRE COMPLETO *
                    </label>

                    <input
                        type="text"
                        id="contactoNombre"
                        name="nombre"
                        maxlength="100"
                        value="<?php echo escaparContacto($nombre); ?>"
                        placeholder="Tu nombre"
                        required>

                </div>


                <!-- CORREO -->

                <div class="contacto-campo">

                    <label for="contactoCorreo">
                        CORREO ELECTRÓNICO *
                    </label>

                    <input
                        type="email"
                        id="contactoCorreo"
                        name="correo"
                        maxlength="254"
                        value="<?php echo escaparContacto($correo); ?>"
                        placeholder="ejemplo@correo.com"
                        required>

                </div>


                <!-- ASUNTO -->

                <div class="contacto-campo">

                    <label for="contactoAsunto">
                        ASUNTO *
                    </label>

                    <select
                        id="contactoAsunto"
                        name="asunto"
                        required>

                        <option value="">
                            Selecciona una opción
                        </option>

                        <?php
                        $asuntos = [
                            'Información de productos',
                            'Personalización',
                            'Estado de pedido',
                            'Otro'
                        ];

                        foreach ($asuntos as $opcion):
                        ?>

                            <option
                                value="<?php echo escaparContacto($opcion); ?>"
                                <?php echo $asunto === $opcion ? 'selected' : ''; ?>>

                                <?php echo escaparContacto($opcion); ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>


                <!-- MENSAJE -->

                <div class="contacto-campo">

                    <label for="contactoMensaje">
                        TU MENSAJE *
                    </label>

                    <textarea
                        id="contactoMensaje"
                        name="mensaje"
                        rows="6"
                        maxlength="2000"
                        placeholder="Cuéntanos tu idea..."
                        required><?php echo escaparContacto($mensaje); ?></textarea>

                </div>


                <!-- BOTÓN -->

                <button
                    type="submit"
                    class="contacto-boton">

                    VALIDAR MENSAJE ↗

                </button>

                <p class="contacto-aviso">
                    * Campos obligatorios. Por ahora,
                    el formulario valida los datos,
                    pero todavía no envía mensajes.
                </p>

            </form>

        </div>

    </section>


    <!-- =====================================
         REDES SOCIALES
    ===================================== -->

    <section class="contacto-redes" id="redes">

        <span class="etiqueta">
            FOLLOW THE VIBE / 003
        </span>

        <h2>
            SÍGUENOS
            <span>EN TODAS PARTES. ✦</span>
        </h2>

        <p>
            Descubre novedades, inspiración
            y diseños personalizados.
        </p>


        <div class="contacto-redes-grid">

            <!-- INSTAGRAM -->

            <article class="contacto-red-tarjeta">

                <span class="red-simbolo">◎</span>

                <h3>INSTAGRAM</h3>

                <p>
                    Diseños, fotografías y novedades.
                </p>

                <span class="red-pendiente">
                    PRÓXIMAMENTE ↗
                </span>

            </article>


            <!-- YOUTUBE -->

            <article class="contacto-red-tarjeta">

                <span class="red-simbolo">▶</span>

                <h3>YOUTUBE</h3>

                <p>
                    Videos y contenido creativo.
                </p>

                <span class="red-pendiente">
                    PRÓXIMAMENTE ↗
                </span>

            </article>


            <!-- FACEBOOK -->

            <article class="contacto-red-tarjeta">

                <span class="red-simbolo">f</span>

                <h3>FACEBOOK</h3>

                <p>
                    Noticias y actualizaciones.
                </p>

                <span class="red-pendiente">
                    PRÓXIMAMENTE ↗
                </span>

            </article>

        </div>

    </section>


    <!-- =====================================
         CIERRE
    ===================================== -->

    <section class="contacto-cierre">

        <span class="etiqueta">
            YOUR MUSIC, YOUR STORY / 004
        </span>

        <h2>
            TU HISTORIA
            <span>COMIENZA AQUÍ.</span>
        </h2>

        <p>
            Explora nuestros mini CD y encuentra
            una forma única de llevar tu música contigo.
        </p>

        <a href="catalogo.php"
           class="boton-principal">

            EXPLORAR CATÁLOGO ↗

        </a>

    </section>

</main>


<!-- =====================================
     FOOTER
===================================== -->

<footer class="site-footer">

    <div>

        <h2>STELLAR CD ✳</h2>

        <p>Mini CD, maxi recuerdos.</p>

    </div>

    <div class="footer-links">

        <a href="index.php">Inicio</a>

        <a href="catalogo.php">Catálogo</a>

        <a href="nosotros.php">Nosotros</a>

    </div>

    <p class="copyright">
        © <?php echo date('Y'); ?> Stellar CD.
    </p>

</footer>

</body>
</html>
