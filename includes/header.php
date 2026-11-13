
<?php
// Iniciar sesión solamente si aún no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Detectar el entorno del proyecto
$servidor = $_SERVER['HTTP_HOST'] ?? '';

if (
    $servidor === 'localhost' ||
    $servidor === '127.0.0.1' ||
    str_starts_with($servidor, 'localhost:') ||
    str_starts_with($servidor, '127.0.0.1:')
) {
    // XAMPP
    $base = '/stellar_cd/';
} else {
    // InfinityFree
    $base = '/';
}
?>

<header class="site-header">

    <a href="<?php echo $base; ?>index.php" class="logo">
        <img
            src="<?php echo $base; ?>assets/img/LOGO-STELLAR.png"
            alt="Stellar CD">
    </a>

    <nav class="site-nav" aria-label="Navegación principal">

        <a href="<?php echo $base; ?>index.php">
            INICIO
        </a>

        <a href="<?php echo $base; ?>catalogo.php">
            CATÁLOGO
        </a>

        <a href="<?php echo $base; ?>personaliza.php">
            PERSONALIZA
        </a>

        <a href="<?php echo $base; ?>nosotros.php">
            NOSOTROS
        </a>

        <a href="<?php echo $base; ?>contacto.php">
            CONTACTO
        </a>

        <a href="<?php echo $base; ?>ubicacion.php">
            UBICACIÓN
        </a>

        <a href="<?php echo $base; ?>carrito.php">
            MI CARRITO
        </a>

        <?php if (isset($_SESSION['id_usuario'])): ?>

            <?php if (($_SESSION['rol'] ?? '') === 'administrador'): ?>

                <a href="<?php echo $base; ?>admin/index.php">
                    PANEL ADMIN
                </a>

            <?php else: ?>

                <a href="<?php echo $base; ?>cliente/perfil.php">
                    MI PERFIL
                </a>

            <?php endif; ?>

            <a href="<?php echo $base; ?>cerrarSesion.php">
                CERRAR SESIÓN
            </a>

        <?php else: ?>

            <a href="<?php echo $base; ?>registrarse.php">
                REGISTRARSE
            </a>

            <a href="<?php echo $base; ?>iniciarSesion.php">
                INICIAR SESIÓN
            </a>

        <?php endif; ?>

    </nav>

</header>
