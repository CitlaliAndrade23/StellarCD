
<?php
// Iniciar sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Generar token de seguridad
if (empty($_SESSION['csrf_carrito'])) {
    $_SESSION['csrf_carrito'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Catálogo | Stellar CD</title>

    <!-- TOKEN CSRF -->
    <meta name="csrf-carrito"
          content="<?php echo htmlspecialchars(
              $_SESSION['csrf_carrito'],
              ENT_QUOTES,
              'UTF-8'
          ); ?>">

    <!-- ESTILOS -->
    <link rel="stylesheet"
          href="assets/css/style.css?v=5">

    <!-- JAVASCRIPT -->
    <script src="assets/js/main.js" defer></script>
    <script src="assets/js/catalogo.js?v=4" defer></script>

</head>

<body>

<?php require __DIR__ . '/includes/header.php'; ?>

<main class="contenedor">

    <h2>Nuestros llaveros</h2>

    <p>
        Descubre nuestros mini CD musicales y personalizados.
    </p>

    <section
        class="productos"
        id="listaProductos"
        aria-live="polite">

        <p>Cargando productos...</p>

    </section>

</main>
<?php require __DIR__ . '/includes/footer.php'; ?>

</body>
</html>
