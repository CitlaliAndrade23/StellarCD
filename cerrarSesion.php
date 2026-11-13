
<?php
// Iniciar la sesión actual
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Eliminar todas las variables de sesión
$_SESSION = [];

// Eliminar la cookie de sesión
if (ini_get('session.use_cookies')) {
    $parametros = session_get_cookie_params();

    setcookie(
        session_name(),
        '',
        [
            'expires' => time() - 42000,
            'path' => $parametros['path'],
            'domain' => $parametros['domain'],
            'secure' => $parametros['secure'],
            'httponly' => $parametros['httponly'],
            'samesite' => $parametros['samesite'] ?? 'Lax'
        ]
    );
}

// Destruir la sesión
session_destroy();

// Detectar si estamos en XAMPP o InfinityFree
$servidor = $_SERVER['HTTP_HOST'] ?? '';

$esLocal = in_array(
    explode(':', $servidor)[0],
    ['localhost', '127.0.0.1'],
    true
);

$rutaBase = $esLocal ? '/stellar_cd/' : '/';

// Redirigir a la página principal
header('Location: ' . $rutaBase . 'index.php');
exit;
?>
