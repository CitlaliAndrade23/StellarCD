
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$servidor = $_SERVER['HTTP_HOST'] ?? '';

$esLocal = $servidor === 'localhost'
    || $servidor === '127.0.0.1'
    || str_starts_with($servidor, 'localhost:')
    || str_starts_with($servidor, '127.0.0.1:');

$base = $esLocal ? '/stellar_cd/' : '/';
?>

<footer class="stellar-footer">

    <div class="footer-contenido">

        <div class="footer-marca">
            <h2>STELLAR CD <span>✳</span></h2>

            <p class="footer-eslogan">
                MINI CD, MAXI RECUERDOS.
            </p>

            <p class="footer-descripcion">
                Lleva tus canciones y recuerdos
                favoritos a todas partes.
            </p>

            <div class="footer-decoracion">
                ✦ &nbsp; ✳ &nbsp; ✦
            </div>
        </div>

        <div class="footer-columna">
            <h3>EXPLORA</h3>

            <a href="<?php echo $base; ?>index.php">
                Inicio
            </a>

            <a href="<?php echo $base; ?>catalogo.php">
                Catálogo
            </a>

            <a href="<?php echo $base; ?>personaliza.php">
                Personaliza
            </a>

            <a href="<?php echo $base; ?>carrito.php">
                Mi carrito
            </a>
        </div>

        <div class="footer-columna">
            <h3>STELLAR CD</h3>

            <a href="<?php echo $base; ?>nosotros.php">
                Nosotros
            </a>

            <a href="<?php echo $base; ?>contacto.php">
                Contacto
            </a>

            <a href="<?php echo $base; ?>ubicacion.php">
                Ubicación
            </a>
        </div>

        <div class="footer-columna">
            <h3>TU CUENTA</h3>

            <?php if (isset($_SESSION['id_usuario'])): ?>

                <?php if (($_SESSION['rol'] ?? '') === 'administrador'): ?>
                    <a href="<?php echo $base; ?>admin/index.php">
                        Panel administrador
                    </a>
                <?php else: ?>
                    <a href="<?php echo $base; ?>cliente/perfil.php">
                        Mi perfil
                    </a>
                <?php endif; ?>

                <a href="<?php echo $base; ?>cerrarSesion.php">
                    Cerrar sesión
                </a>

            <?php else: ?>

                <a href="<?php echo $base; ?>iniciarSesion.php">
                    Iniciar sesión
                </a>

                <a href="<?php echo $base; ?>registrarse.php">
                    Registrarse
                </a>

            <?php endif; ?>
        </div>

    </div>

    <div class="footer-inferior">
        <p>
            &copy; <?php echo date('Y'); ?> Stellar CD.
            Todos los derechos reservados.
        </p>

        <p>✦ Tu canción al instante.</p>
    </div>

</footer>
