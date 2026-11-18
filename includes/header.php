
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$servidor = $_SERVER['HTTP_HOST'] ?? '';

if (
    $servidor === 'localhost' ||
    $servidor === '127.0.0.1' ||
    str_starts_with($servidor, 'localhost:') ||
    str_starts_with($servidor, '127.0.0.1:')
) {
    $base = '/stellar_cd/';
} else {
    $base = '/';
}
?>

<header class="site-header">

    <a href="<?php echo $base; ?>index.php" class="logo">
        <img
            src="<?php echo $base; ?>assets/img/LOGO-STELLAR.png"
            alt="Stellar CD">
    </a>

    <button
        type="button"
        class="menu-toggle"
        id="menuToggle"
        aria-label="Abrir menú"
        aria-controls="menuPrincipal"
        aria-expanded="false">
        <span></span>
        <span></span>
        <span></span>
    </button>

    <nav
        class="site-nav"
        id="menuPrincipal"
        aria-label="Navegación principal">

        <a href="<?php echo $base; ?>index.php">
            INICIO
        </a>

        <details class="nav-dropdown">
            <summary>
                PRODUCTOS
                <span class="dropdown-flecha">▾</span>
            </summary>

            <div class="dropdown-menu">
                <a href="<?php echo $base; ?>catalogo.php">
                    <span class="dropdown-icono">💿</span>
                    <span class="dropdown-texto">
                        <strong>CATÁLOGO</strong>
                        <small>Diseños predeterminados</small>
                    </span>
                </a>

                <a href="<?php echo $base; ?>personaliza.php">
                    <span class="dropdown-icono">✦</span>
                    <span class="dropdown-texto">
                        <strong>PERSONALIZA</strong>
                        <small>Crea tu propio diseño</small>
                    </span>
                </a>
            </div>
        </details>

        <details class="nav-dropdown">
            <summary>
                INFORMACIÓN
                <span class="dropdown-flecha">▾</span>
            </summary>

            <div class="dropdown-menu">
                <a href="<?php echo $base; ?>nosotros.php">
                    <span class="dropdown-icono">✦</span>
                    <span class="dropdown-texto">
                        <strong>NOSOTROS</strong>
                        <small>Conoce nuestra historia</small>
                    </span>
                </a>

                <a href="<?php echo $base; ?>contacto.php">
                    <span class="dropdown-icono">✉</span>
                    <span class="dropdown-texto">
                        <strong>CONTACTO</strong>
                        <small>Comunícate con nosotros</small>
                    </span>
                </a>

                <a href="<?php echo $base; ?>ubicacion.php">
                    <span class="dropdown-icono">⌖</span>
                    <span class="dropdown-texto">
                        <strong>UBICACIÓN</strong>
                        <small>Encuentra dónde estamos</small>
                    </span>
                </a>
            </div>
        </details>

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
