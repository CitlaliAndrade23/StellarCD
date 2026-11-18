
<?php
// =====================================
// 1. INICIAR SESIÓN
// =====================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// =====================================
// 2. CONEXIÓN A MYSQL
// =====================================

require_once __DIR__ . '/config/conexion.php';

// =====================================
// 3. VERIFICAR SESIÓN EXISTENTE
// =====================================

if (isset($_SESSION['id_usuario'])) {

    if (($_SESSION['rol'] ?? '') === 'administrador') {
        header('Location: admin/index.php');
    } else {
        header('Location: cliente/perfil.php');
    }

    exit;
}

// =====================================
// 4. VARIABLES
// =====================================

$error = '';
$correo = '';

// =====================================
// 5. PROCESAR FORMULARIO
// =====================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $correo = trim($_POST['correo'] ?? '');
    $contrasena = $_POST['contrasena'] ?? '';

    // Validaciones
    if ($correo === '' || $contrasena === '') {

        $error = 'Completa todos los campos.';

    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {

        $error = 'Ingresa un correo válido.';

    } else {

        // Buscar usuario en MySQL
        $sql = "SELECT
                    id_usuario,
                    nombre_completo,
                    correo,
                    contrasena,
                    rol
                FROM usuarios
                WHERE correo = :correo
                LIMIT 1";

        $consulta = $conexion->prepare($sql);

        $consulta->execute([
            'correo' => $correo
        ]);

        $usuario = $consulta->fetch(PDO::FETCH_ASSOC);

        // Verificar contraseña
        if (
            $usuario &&
            password_verify(
                $contrasena,
                $usuario['contrasena']
            )
        ) {

            // Renovar ID de sesión
            session_regenerate_id(true);

            // Guardar información del usuario
            $_SESSION['id_usuario'] = $usuario['id_usuario'];

            $_SESSION['nombre_usuario'] =
                $usuario['nombre_completo'];

            $_SESSION['correo'] = $usuario['correo'];

            $_SESSION['rol'] = $usuario['rol'];

            // Redirigir según el rol
            if ($usuario['rol'] === 'administrador') {

                header('Location: admin/index.php');

            } else {

                header('Location: cliente/perfil.php');

            }

            exit;

        } else {

            $error = 'Correo o contraseña incorrectos.';

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

    <title>Iniciar sesión | Stellar CD</title>

    <!-- GOOGLE ANALYTICS -->
    <?php require_once __DIR__ . '/includes/analytics.php'; ?>

    <!-- ESTILOS -->
    <link rel="stylesheet"
          href="assets/css/style.css?v=15">

    <!-- JAVASCRIPT -->
    <script src="assets/js/main.js?v=2" defer></script>

</head>

<body class="pagina-login">

<!-- =====================================
     HEADER COMPARTIDO
===================================== -->

<?php require __DIR__ . '/includes/header.php'; ?>


<!-- =====================================
     CONTENIDO PRINCIPAL
===================================== -->

<main class="login-contenedor">

    <!-- PRESENTACIÓN -->
    <section class="login-presentacion">

        <span class="etiqueta">
            STELLAR CD / ACCESS 001
        </span>

        <div>

            <h1>
                WELCOME
                <span>BACK. ✳</span>
            </h1>

            <p>
                Tu música, tus recuerdos,
                tu espacio.
            </p>

        </div>

        <div class="login-cd" aria-hidden="true">
            <div class="login-cd-centro"></div>
        </div>

        <span class="login-frase">
            YOUR MUSIC. YOUR STORY.
        </span>

    </section>


    <!-- =====================================
         FORMULARIO DE LOGIN
    ===================================== -->

    <section class="login-formulario">

        <span class="etiqueta">
            MEMBER LOGIN / 002
        </span>

        <h2>
            INICIAR
            <span>SESIÓN.</span>
        </h2>

        <p>
            Ingresa tus datos para acceder
            a tu cuenta de Stellar CD.
        </p>


        <!-- MENSAJE DE ERROR -->

        <?php if ($error !== ''): ?>

            <div class="login-error" role="alert">

                <?php echo htmlspecialchars(
                    $error,
                    ENT_QUOTES,
                    'UTF-8'
                ); ?>

            </div>

        <?php endif; ?>


        <!-- FORMULARIO -->

        <form method="POST" action="iniciarSesion.php">

            <!-- CORREO -->
            <div class="login-campo">

                <label for="loginCorreo">
                    CORREO ELECTRÓNICO
                </label>

                <input
                    type="email"
                    id="loginCorreo"
                    name="correo"
                    maxlength="150"
                    placeholder="tu@correo.com"
                    value="<?php echo htmlspecialchars(
                        $correo,
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?>"
                    autocomplete="username"
                    required>

            </div>


            <!-- CONTRASEÑA -->
            <div class="login-campo">

                <label for="loginContrasena">
                    CONTRASEÑA
                </label>

                <div class="login-password">

                    <input
                        type="password"
                        id="loginContrasena"
                        name="contrasena"
                        placeholder="Tu contraseña"
                        autocomplete="current-password"
                        required>

                    <button
                        type="button"
                        id="mostrarContrasena"
                        aria-label="Mostrar contraseña"
                        aria-pressed="false">

                        VER

                    </button>

                </div>

            </div>


            <!-- BOTÓN LOGIN -->
            <button
                type="submit"
                class="login-boton">

                ENTRAR A MI CUENTA ↗

            </button>

        </form>


        <!-- REGISTRO -->
        <p class="login-registrarse">

            ¿Todavía no tienes cuenta?

            <a href="registrarse.php">
                REGÍSTRATE AQUÍ ✳
            </a>

        </p>

    </section>

</main>

<!-- =====================================
     FOOTER COMPARTIDO
===================================== -->

<?php require __DIR__ . '/includes/footer.php'; ?>

</body>
</html>
