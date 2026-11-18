
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../iniciarSesion.php');
    exit;
}

if (($_SESSION['rol'] ?? '') !== 'cliente') {
    http_response_code(403);
    exit('Acceso no autorizado.');
}

$nombreOriginal = trim(
    $_SESSION['nombre_usuario'] ?? 'Cliente'
);

$nombre = htmlspecialchars(
    $nombreOriginal,
    ENT_QUOTES,
    'UTF-8'
);

$correo = htmlspecialchars(
    $_SESSION['correo'] ?? '',
    ENT_QUOTES,
    'UTF-8'
);

$primerNombre = htmlspecialchars(
    explode(' ', $nombreOriginal)[0],
    ENT_QUOTES,
    'UTF-8'
);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Mi perfil | Stellar CD</title>

    <link rel="stylesheet"
          href="../assets/css/style.css?v=12">

    <script src="../assets/js/main.js?v=5" defer></script>
</head>

<body class="pagina-perfil">

<?php require __DIR__ . '/../includes/header.php'; ?>

<main class="perfil-dashboard">

    <div class="perfil-interior">

        <div class="perfil-bienvenida">

            <span class="perfil-etiqueta">
                STELLAR CD / MY ACCOUNT
            </span>

            <h1>
                ¡HOLA DE NUEVO,
                <span><?php echo $primerNombre; ?>!</span>
                ✳
            </h1>

            <p>
                Tu espacio para descubrir nuevos diseños,
                crear recuerdos y consultar tu cuenta.
            </p>

        </div>

        <section class="perfil-panel">

            <div class="perfil-panel-encabezado">

                <div class="perfil-avatar">
                    ♡
                </div>

                <div>
                    <span class="perfil-subtitulo">
                        TU ESPACIO PERSONAL
                    </span>

                    <h2>MI PERFIL</h2>
                    <p>Cuenta de cliente</p>
                </div>

                <span class="perfil-estrella">✳</span>

            </div>

            <div class="perfil-datos">

                <div class="perfil-dato">
                    <span>NOMBRE COMPLETO</span>
                    <strong><?php echo $nombre; ?></strong>
                </div>

                <div class="perfil-dato">
                    <span>CORREO ELECTRÓNICO</span>
                    <strong><?php echo $correo; ?></strong>
                </div>

                <div class="perfil-dato">
                    <span>TIPO DE CUENTA</span>
                    <strong>Cliente</strong>
                </div>

            </div>

        </section>

        <div class="perfil-seccion-titulo">
            <span>EXPLORE / 001</span>
            <h2>¿QUÉ HACEMOS HOY?</h2>
        </div>

        <div class="perfil-acciones">

            <a href="../catalogo.php"
               class="perfil-accion perfil-accion-catalogo">

                <span class="perfil-accion-icono">◉</span>

                <div>
                    <h3>EXPLORAR CATÁLOGO</h3>

                    <p>
                        Descubre nuestros mini CD
                        con diseños predeterminados.
                    </p>
                </div>

                <span class="perfil-accion-flecha">↗</span>

            </a>

            <a href="../personaliza.php"
               class="perfil-accion perfil-accion-personaliza">

                <span class="perfil-accion-icono">✳</span>

                <div>
                    <h3>PERSONALIZA TU CD</h3>

                    <p>
                        Diseña un mini CD con tus canciones,
                        imágenes y recuerdos favoritos.
                    </p>
                </div>

                <span class="perfil-accion-flecha">↗</span>

            </a>

        </div>

        <div class="perfil-seccion-titulo">
            <span>YOUR ORDERS / 002</span>
            <h2>TUS COMPRAS</h2>
        </div>

        <section class="perfil-pedidos">

            <div class="perfil-pedidos-icono">♫</div>

            <div class="perfil-pedidos-texto">
                <h3>MIS PEDIDOS</h3>

                <p>
                    Consulta los productos de tu carrito
                    y continúa con tu compra.
                </p>
            </div>

            <a href="../carrito.php"
               class="perfil-boton-pedidos">
                IR A MI CARRITO ↗
            </a>

        </section>

        <div class="perfil-salida">
            <a href="../cerrarSesion.php">
                CERRAR SESIÓN ↗
            </a>
        </div>

    </div>

</main>

<?php require __DIR__ . '/../includes/footer.php'; ?>

</body>
</html>
