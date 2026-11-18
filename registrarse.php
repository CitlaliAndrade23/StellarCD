
<?php
session_start();

require_once __DIR__ . '/config/conexion.php';

// Si el usuario ya inició sesión, redirigir a su área
if (isset($_SESSION['id_usuario'])) {

    if (($_SESSION['rol'] ?? '') === 'administrador') {
        header('Location: admin/index.php');
    } else {
        header('Location: cliente/perfil.php');
    }

    exit;
}

$errores = [];
$registroExitoso = false;

$nombre = '';
$correo = '';
$telefono = '';

// Función para mostrar datos de forma segura
function escaparRegistro($valor) {
    return htmlspecialchars(
        (string) $valor,
        ENT_QUOTES,
        'UTF-8'
    );
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre = trim($_POST['nombre_completo'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');

    $contrasena = $_POST['contrasena'] ?? '';
    $confirmar = $_POST['confirmar_contrasena'] ?? '';

    // Validar nombre
    if ($nombre === '' || mb_strlen($nombre) > 100) {
        $errores[] = 'Ingresa un nombre válido (máximo 100 caracteres).';
    }

    // Validar correo
    if (
        !filter_var($correo, FILTER_VALIDATE_EMAIL)
        || strlen($correo) > 150
    ) {
        $errores[] = 'Ingresa un correo electrónico válido.';
    }

    // Validar teléfono
    if (!preg_match('/^[0-9+\s()-]{7,20}$/', $telefono)) {
        $errores[] = 'Ingresa un teléfono válido de 7 a 20 caracteres.';
    }

    // Validar contraseña
    if (strlen($contrasena) < 8) {
        $errores[] = 'La contraseña debe tener al menos 8 caracteres.';
    }

    if (strlen($contrasena) > 72) {
        $errores[] = 'La contraseña no debe superar 72 bytes.';
    }

    // Confirmar contraseña
    if ($contrasena !== $confirmar) {
        $errores[] = 'Las contraseñas no coinciden.';
    }

    // Guardar usuario si no hay errores
    if (empty($errores)) {

        try {

            // Comprobar si el correo ya existe
            $sql = "SELECT id_usuario
                    FROM usuarios
                    WHERE correo = :correo
                    LIMIT 1";

            $consulta = $conexion->prepare($sql);

            $consulta->execute([
                'correo' => $correo
            ]);

            if ($consulta->fetch()) {

                $errores[] = 'Este correo ya está registrado.';

            } else {

                // Proteger la contraseña
                $hash = password_hash(
                    $contrasena,
                    PASSWORD_DEFAULT
                );

                // Registrar siempre como cliente
                $sql = "INSERT INTO usuarios
                        (
                            nombre_completo,
                            correo,
                            telefono,
                            contrasena,
                            rol
                        )
                        VALUES
                        (
                            :nombre,
                            :correo,
                            :telefono,
                            :contrasena,
                            'cliente'
                        )";

                $consulta = $conexion->prepare($sql);

                $consulta->execute([
                    'nombre' => $nombre,
                    'correo' => $correo,
                    'telefono' => $telefono,
                    'contrasena' => $hash
                ]);

                $registroExitoso = true;

                // Limpiar formulario
                $nombre = '';
                $correo = '';
                $telefono = '';
            }

        } catch (PDOException $e) {

            if ($e->getCode() === '23000') {

                $errores[] = 'Este correo ya está registrado.';

            } else {

                error_log($e->getMessage());

                $errores[] =
                    'No se pudo completar el registro. Inténtalo más tarde.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Registrarse | Stellar CD</title>

    <link rel="stylesheet"
          href="assets/css/style.css?v=13">

    <script src="assets/js/main.js?v=4" defer></script>

</head>

<body class="pagina-registro">

<!-- =====================================
     HEADER COMPARTIDO
===================================== -->

<?php require __DIR__ . '/includes/header.php'; ?>


<!-- =====================================
     CONTENIDO PRINCIPAL
===================================== -->

<main class="registro-contenedor">

    <!-- PRESENTACIÓN -->

    <section class="registro-presentacion">

        <span class="etiqueta">
            JOIN THE UNIVERSE / 001
        </span>

        <div class="registro-presentacion-texto">

            <h1>
                JOIN THE
                <span>STELLAR</span>
                UNIVERSE. ✳
            </h1>

            <p>
                Tu música, tus recuerdos y tus
                diseños favoritos en un solo lugar.
            </p>

        </div>

        <div class="registro-disco" aria-hidden="true">
            <div class="registro-disco-centro"></div>
        </div>

        <span class="registro-frase">
            YOUR MUSIC ✦ YOUR STORY ✦ YOUR CD
        </span>

    </section>


    <!-- =====================================
         FORMULARIO DE REGISTRO
    ===================================== -->

    <section class="registro-formulario">

        <span class="etiqueta">
            CREATE ACCOUNT / 002
        </span>

        <h2>
            CREA TU
            <span>CUENTA. ♡</span>
        </h2>

        <p>
            Regístrate para comenzar tu
            experiencia con Stellar CD.
        </p>


        <!-- REGISTRO EXITOSO -->

        <?php if ($registroExitoso): ?>

            <div class="registro-exito" role="status">

                <h3>
                    ¡BIENVENIDO A STELLAR CD! ✳
                </h3>

                <p>
                    Tu cuenta se creó correctamente.
                    Ya puedes iniciar sesión.
                </p>

                <a href="iniciarSesion.php"
                   class="registro-enlace-login">

                    INICIAR SESIÓN ↗

                </a>

            </div>


        <?php else: ?>


            <!-- MENSAJES DE ERROR -->

            <?php if (!empty($errores)): ?>

                <div class="registro-error" role="alert">

                    <?php foreach ($errores as $error): ?>

                        <p>
                            <?php echo escaparRegistro($error); ?>
                        </p>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>


            <!-- FORMULARIO -->

            <form method="POST" action="registrarse.php">

                <!-- NOMBRE -->

                <div class="registro-campo">

                    <label for="registroNombre">
                        NOMBRE COMPLETO *
                    </label>

                    <input
                        type="text"
                        id="registroNombre"
                        name="nombre_completo"
                        maxlength="100"
                        value="<?php echo escaparRegistro($nombre); ?>"
                        placeholder="Tu nombre completo"
                        autocomplete="name"
                        required>

                </div>


                <!-- CORREO -->

                <div class="registro-campo">

                    <label for="registroCorreo">
                        CORREO ELECTRÓNICO *
                    </label>

                    <input
                        type="email"
                        id="registroCorreo"
                        name="correo"
                        maxlength="150"
                        value="<?php echo escaparRegistro($correo); ?>"
                        placeholder="tu@correo.com"
                        autocomplete="email"
                        required>

                </div>


                <!-- TELÉFONO -->

                <div class="registro-campo">

                    <label for="registroTelefono">
                        TELÉFONO *
                    </label>

                    <input
                        type="tel"
                        id="registroTelefono"
                        name="telefono"
                        maxlength="20"
                        value="<?php echo escaparRegistro($telefono); ?>"
                        placeholder="Ej. 6561234567"
                        autocomplete="tel"
                        required>

                </div>


                <!-- CONTRASEÑA -->

                <div class="registro-campo">

                    <label for="registroContrasena">
                        CONTRASEÑA *
                    </label>

                    <div class="registro-password">

                        <input
                            type="password"
                            id="registroContrasena"
                            name="contrasena"
                            minlength="8"
                            maxlength="72"
                            placeholder="Mínimo 8 caracteres"
                            autocomplete="new-password"
                            required>

                        <button
                            type="button"
                            class="registro-ver"
                            data-password-target="registroContrasena"
                            aria-label="Mostrar contraseña"
                            aria-pressed="false">

                            VER

                        </button>

                    </div>

                </div>


                <!-- CONFIRMAR CONTRASEÑA -->

                <div class="registro-campo">

                    <label for="registroConfirmar">
                        CONFIRMAR CONTRASEÑA *
                    </label>

                    <div class="registro-password">

                        <input
                            type="password"
                            id="registroConfirmar"
                            name="confirmar_contrasena"
                            minlength="8"
                            maxlength="72"
                            placeholder="Repite tu contraseña"
                            autocomplete="new-password"
                            required>

                        <button
                            type="button"
                            class="registro-ver"
                            data-password-target="registroConfirmar"
                            aria-label="Mostrar contraseña"
                            aria-pressed="false">

                            VER

                        </button>

                    </div>

                </div>


                <!-- BOTÓN REGISTRO -->

                <button
                    type="submit"
                    class="registro-boton">

                    CREAR MI CUENTA ↗

                </button>


                <p class="registro-aviso">
                    * Todos los campos son obligatorios.
                    Tu contraseña se almacena de forma segura.
                </p>

            </form>


            <!-- ENLACE LOGIN -->

            <p class="registro-login">

                ¿Ya tienes una cuenta?

                <a href="iniciarSesion.php">
                    INICIA SESIÓN ✳
                </a>

            </p>

        <?php endif; ?>

    </section>

</main>


<?php require __DIR__ . '/includes/footer.php'; ?>

</body>
</html>
