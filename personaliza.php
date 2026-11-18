
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['csrf_personaliza'])) {
    $_SESSION['csrf_personaliza'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Personaliza | Stellar CD</title>

    <link rel="stylesheet"
         href="assets/css/style.css?v=15">

    <script src="assets/js/main.js" defer></script>
    <script src="assets/js/personaliza.js?v=1" defer></script>
    <?php require_once __DIR__ . '/includes/analytics.php'; ?>

</head>

<body class="pagina-personaliza">

<!-- HEADER COMPARTIDO -->
<?php require __DIR__ . '/includes/header.php'; ?>

<main>

    <!-- PRESENTACIÓN -->
    <section class="personaliza-hero">

        <span class="etiqueta">
            MAKE IT YOURS / 001
        </span>

        <h1>
            TU CD.
            <span>TUS REGLAS.</span>
        </h1>

        <p>
            Tu música, tus imágenes y tus recuerdos.
            Diseña un mini CD completamente tuyo.
        </p>

        <a href="#formulario-personaliza"
           class="boton-principal">
            COMENZAR A DISEÑAR ↗
        </a>

        <span class="personaliza-decoracion"
              aria-hidden="true">✳</span>

    </section>


    <!-- INSTRUCCIONES -->
    <section class="personaliza-seccion" id="instrucciones">

        <span class="etiqueta">
            STEP 01 / INSTRUCCIONES
        </span>

        <h2>¿CÓMO FUNCIONA? ✦</h2>

        <div class="pasos-personaliza">

            <article>
                <span>01</span>

                <h3>ELIGE TUS IMÁGENES</h3>

                <p>
                    Selecciona las fotografías o diseños
                    que quieres utilizar.
                </p>
            </article>

            <article>
                <span>02</span>

                <h3>PERSONALIZA TU CD</h3>

                <p>
                    Agrega las imágenes de portada,
                    contraportada y disco.
                </p>
            </article>

            <article>
                <span>03</span>

                <h3>ENVÍA TU SOLICITUD</h3>

                <p>
                    Revisa tus datos y envía
                    tu diseño para continuar
                    con el pedido.
                </p>
            </article>

        </div>

    </section>


    <!-- MEDIDAS -->
    <section class="personaliza-medidas" id="medidas">

        <span class="etiqueta">
            STEP 02 / MEDIDAS
        </span>

        <h2>
            PEQUEÑO CD.
            <br>
            GRANDES RECUERDOS.
        </h2>

        <p>
            Prepara tus imágenes con buena resolución.
            Procura que los elementos importantes
            estén centrados para evitar recortes.
        </p>

        <div class="medidas-grid">

            <div class="medida-tarjeta">

                <span class="medida-icono">▣</span>

                <h3>PORTADA</h3>

                <p>
                    Imagen frontal de tu mini CD.
                </p>

            </div>

            <div class="medida-tarjeta">

                <span class="medida-icono">▤</span>

                <h3>CONTRAPORTADA</h3>

                <p>
                    Imagen posterior de tu empaque.
                </p>

            </div>

            <div class="medida-tarjeta">

                <span class="medida-icono">◉</span>

                <h3>CD</h3>

                <p>
                    Diseño circular para el disco.
                </p>

            </div>

        </div>

        <p class="nota-medidas">
            Las medidas exactas de impresión
            se agregarán cuando estén confirmadas.
        </p>

    </section>


    <!-- FORMULARIO DE PERSONALIZACIÓN -->
    <section class="personaliza-formulario"
             id="formulario-personaliza">

        <span class="etiqueta">
            STEP 03 / CREATE YOUR CD
        </span>

        <h2>
            HAZLO
            <span>ÚNICO. ✳</span>
        </h2>

        <p>
            Sube tus imágenes y observa
            una vista previa antes de continuar.
        </p>

        <form id="formPersonaliza"
              method="post"
              action="api/personalizacion.php"
              enctype="multipart/form-data">

            <!-- TOKEN DE SEGURIDAD -->
            <input
                type="hidden"
                name="csrf_token"
                value="<?php echo htmlspecialchars(
                    $_SESSION['csrf_personaliza'],
                    ENT_QUOTES,
                    'UTF-8'
                ); ?>">

            <div class="personaliza-campos">

                <!-- NOMBRE DEL DISEÑO -->
                <div class="campo-personaliza">

                    <label for="nombrePersonaliza">
                        NOMBRE DEL DISEÑO
                    </label>

                    <input
                        type="text"
                        id="nombrePersonaliza"
                        name="nombre_diseno"
                        placeholder="Ej. Mi canción favorita"
                        maxlength="120"
                        required>

                </div>


                <!-- CANCIÓN -->
                <div class="campo-personaliza">

                    <label for="cancionPersonaliza">
                        CANCIÓN O PLAYLIST
                    </label>

                    <input
                        type="text"
                        id="cancionPersonaliza"
                        name="cancion"
                        placeholder="Ej. Nombre de la canción"
                        maxlength="255">

                </div>

            </div>


            <!-- SUBIDA DE IMÁGENES -->
            <div class="subidas-personaliza">

                <!-- PORTADA -->
                <div class="subida-tarjeta">

                    <h3>01 / PORTADA</h3>

                    <div class="vista-previa"
                         id="previewPortada">
                        <span>✳</span>
                    </div>

                    <label for="imagenPortada">
                        SUBIR PORTADA ↗
                    </label>

                    <input
                        type="file"
                        id="imagenPortada"
                        name="portada"
                        accept="image/png,image/jpeg,image/webp"
                        required>

                </div>


                <!-- CONTRAPORTADA -->
                <div class="subida-tarjeta">

                    <h3>02 / CONTRAPORTADA</h3>

                    <div class="vista-previa"
                         id="previewContraportada">
                        <span>✦</span>
                    </div>

                    <label for="imagenContraportada">
                        SUBIR CONTRAPORTADA ↗
                    </label>

                    <input
                        type="file"
                        id="imagenContraportada"
                        name="contraportada"
                        accept="image/png,image/jpeg,image/webp"
                        required>

                </div>


                <!-- DISEÑO DEL CD -->
                <div class="subida-tarjeta">

                    <h3>03 / CD</h3>

                    <div class="vista-previa preview-circular"
                         id="previewCD">
                        <span>◉</span>
                    </div>

                    <label for="imagenCD">
                        SUBIR DISEÑO CD ↗
                    </label>

                    <input
                        type="file"
                        id="imagenCD"
                        name="imagen_cd"
                        accept="image/png,image/jpeg,image/webp"
                        required>

                </div>

            </div>


            <!-- BOTÓN -->
            <button
                type="submit"
                class="boton-personaliza">

                REVISAR MI DISEÑO ↗

            </button>

            <p class="aviso-formulario">
                Revisa cuidadosamente tus imágenes
                antes de enviar tu solicitud.
                Nuestro equipo revisará tu diseño
                para continuar con el pedido.
            </p>

        </form>

    </section>

</main>

<?php require __DIR__ . '/includes/footer.php'; ?>

</body>
</html>
