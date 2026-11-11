
<?php
// Iniciar sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Comprobar que el usuario inició sesión
if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../iniciarSesion.php');
    exit;
}

// Solo los clientes pueden acceder
if (($_SESSION['rol'] ?? '') !== 'cliente') {
    http_response_code(403);
    exit('Acceso no autorizado.');
}

// Obtener datos del usuario
$nombre = htmlspecialchars(
    $_SESSION['nombre_usuario'] ?? 'Cliente',
    ENT_QUOTES,
    'UTF-8'
);

$correo = htmlspecialchars(
    $_SESSION['correo'] ?? '',
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
          href="../assets/css/style.css?v=11">

    <style>
        /* CORREGIR LOGO DEL PERFIL */

        .site-header .logo {
            display: flex;
            align-items: center;
            width: 80px;
            flex-shrink: 0;
        }

        .site-header .logo img {
            display: block;
            width: 80px !important;
            height: auto !important;
            max-width: 80px !important;
            max-height: 80px !important;
            object-fit: contain;
        }

        .site-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 7%;
            min-height: 80px;
            box-sizing: border-box;
        }
    </style>

</head>

<body>

<!-- =====================================
     HEADER COMPARTIDO
===================================== -->

<?php require __DIR__ . '/../includes/header.php'; ?>


<!-- =====================================
     PERFIL DEL CLIENTE
===================================== -->

<main class="perfil-contenedor">

    <span class="etiqueta">
        STELLAR CD / MY ACCOUNT
    </span>

    <h1>
        ¡HOLA, <?php echo $nombre; ?>! ✳
    </h1>


    <!-- TARJETA DEL PERFIL -->

    <div class="perfil-tarjeta">

        <h2>MI PERFIL ♡</h2>


        <!-- NOMBRE -->

        <p>
            <strong>Nombre:</strong>
            <?php echo $nombre; ?>
        </p>


        <!-- CORREO -->

        <p>
            <strong>Correo:</strong>
            <?php echo $correo; ?>
        </p>


        <!-- TIPO DE CUENTA -->

        <p>
            <strong>Tipo de cuenta:</strong>
            Cliente
        </p>


        <!-- ENLACE AL CATÁLOGO -->

        <a href="../catalogo.php"
           class="perfil-enlace">

            EXPLORAR CATÁLOGO ↗

        </a>

    </div>

</main>

</body>
</html>
