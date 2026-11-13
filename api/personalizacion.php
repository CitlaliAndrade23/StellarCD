
<?php
session_start();

header('Content-Type: application/json; charset=utf-8');

function responder($ok, $mensaje, $codigo = 200, $extra = [])
{
    http_response_code($codigo);

    echo json_encode(
        array_merge([
            'ok' => $ok,
            'mensaje' => $mensaje
        ], $extra),
        JSON_UNESCAPED_UNICODE
    );

    exit;
}

// Verificar método HTTP
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder(false, 'Método no permitido.', 405);
}

// Verificar sesión del cliente
if (
    empty($_SESSION['id_usuario']) ||
    ($_SESSION['rol'] ?? '') !== 'cliente'
) {
    responder(
        false,
        'Debes iniciar sesión como cliente para enviar tu solicitud.',
        401
    );
}

// Verificar token CSRF
$token = $_POST['csrf_token'] ?? '';

if (
    !is_string($token) ||
    empty($_SESSION['csrf_personaliza']) ||
    !hash_equals($_SESSION['csrf_personaliza'], $token)
) {
    responder(
        false,
        'Token de seguridad inválido.',
        403
    );
}

// Obtener datos del formulario
$nombre = $_POST['nombre_diseno'] ?? '';
$cancion = $_POST['cancion'] ?? '';

if (!is_string($nombre) || !is_string($cancion)) {
    responder(
        false,
        'Los datos del formulario no son válidos.',
        422
    );
}

$nombre = trim($nombre);
$cancion = trim($cancion);

// Validar nombre del diseño
if ($nombre === '' || mb_strlen($nombre) > 120) {
    responder(
        false,
        'El nombre del diseño no es válido.',
        422
    );
}

// Validar canción
if (mb_strlen($cancion) > 255) {
    responder(
        false,
        'La canción es demasiado larga.',
        422
    );
}

// Configuración de imágenes
$campos = [
    'portada',
    'contraportada',
    'imagen_cd'
];

$tiposPermitidos = [
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/webp' => 'webp'
];

$tamanoMaximo = 5 * 1024 * 1024;

$imagenes = [];
$archivosGuardados = [];
$rutas = [];

// Validar imágenes
$finfo = new finfo(FILEINFO_MIME_TYPE);

foreach ($campos as $campo) {

    if (
        !isset($_FILES[$campo]) ||
        !is_array($_FILES[$campo]) ||
        !isset($_FILES[$campo]['error']) ||
        is_array($_FILES[$campo]['error'])
    ) {
        responder(
            false,
            "Falta la imagen: $campo.",
            422
        );
    }

    $archivo = $_FILES[$campo];

    if ($archivo['error'] !== UPLOAD_ERR_OK) {
        responder(
            false,
            "No se pudo cargar la imagen: $campo.",
            422
        );
    }

    if (
        !isset(
            $archivo['size'],
            $archivo['tmp_name']
        ) ||
        !is_int($archivo['size']) ||
        !is_string($archivo['tmp_name'])
    ) {
        responder(
            false,
            'Los datos de la imagen no son válidos.',
            422
        );
    }

    // Verificar archivo vacío
    if ($archivo['size'] <= 0) {
        responder(
            false,
            'La imagen seleccionada está vacía.',
            422
        );
    }

    // Verificar tamaño máximo
    if ($archivo['size'] > $tamanoMaximo) {
        responder(
            false,
            'Cada imagen debe pesar máximo 5 MB.',
            422
        );
    }

    // Verificar carga legítima
    if (!is_uploaded_file($archivo['tmp_name'])) {
        responder(
            false,
            'El archivo seleccionado no es válido.',
            422
        );
    }

    // Detectar tipo MIME real
    $mime = $finfo->file($archivo['tmp_name']);

    if (!isset($tiposPermitidos[$mime])) {
        responder(
            false,
            'Solo se permiten imágenes JPG, PNG o WEBP.',
            422
        );
    }

    // Comprobar que sea una imagen
    if (@getimagesize($archivo['tmp_name']) === false) {
        responder(
            false,
            'El archivo no contiene una imagen válida.',
            422
        );
    }

    $imagenes[$campo] = [
        'temporal' => $archivo['tmp_name'],
        'extension' => $tiposPermitidos[$mime]
    ];
}

// Directorio de almacenamiento
$directorio = dirname(__DIR__)
    . '/uploads/personalizaciones/';

try {

    // Crear directorio si no existe
    if (
        !is_dir($directorio) &&
        !mkdir($directorio, 0755, true) &&
        !is_dir($directorio)
    ) {
        throw new RuntimeException(
            'No se pudo crear la carpeta de imágenes.'
        );
    }

    // Guardar imágenes
    foreach ($imagenes as $campo => $imagen) {

        $nombreArchivo = bin2hex(random_bytes(16))
            . '.'
            . $imagen['extension'];

        $destino = $directorio . $nombreArchivo;

        if (
            !move_uploaded_file(
                $imagen['temporal'],
                $destino
            )
        ) {
            throw new RuntimeException(
                'No se pudo guardar la imagen.'
            );
        }

        $archivosGuardados[] = $destino;

        $rutas[$campo] =
            'uploads/personalizaciones/' . $nombreArchivo;
    }

    // Conectar a MySQL
    require_once dirname(__DIR__)
        . '/config/conexion.php';

    if (
        !isset($conexion) ||
        !($conexion instanceof PDO)
    ) {
        throw new RuntimeException(
            'La conexión a MySQL no está configurada.'
        );
    }

    // Registrar solicitud
    $sql = "INSERT INTO solicitudes_personalizacion
            (
                usuario_id,
                nombre_diseno,
                cancion,
                portada,
                contraportada,
                imagen_cd
            )
            VALUES
            (
                :usuario_id,
                :nombre,
                :cancion,
                :portada,
                :contraportada,
                :imagen_cd
            )";

    $stmt = $conexion->prepare($sql);

    $stmt->execute([
        ':usuario_id' => $_SESSION['id_usuario'],
        ':nombre' => $nombre,
        ':cancion' => $cancion,
        ':portada' => $rutas['portada'],
        ':contraportada' => $rutas['contraportada'],
        ':imagen_cd' => $rutas['imagen_cd']
    ]);

    // Obtener identificador de solicitud
    $solicitudId = $conexion->lastInsertId();

    // Renovar token CSRF
    $_SESSION['csrf_personaliza'] =
        bin2hex(random_bytes(32));

    // Respuesta exitosa
    responder(
        true,
        '¡Tu solicitud se guardó correctamente!',
        200,
        [
            'solicitud_id' => $solicitudId,
            'csrf_token' => $_SESSION['csrf_personaliza']
        ]
    );

} catch (Throwable $error) {

    // Eliminar imágenes si ocurre un error
    foreach ($archivosGuardados as $archivo) {

        if (is_file($archivo)) {
            unlink($archivo);
        }
    }

    // Registrar error en servidor
    error_log($error->getMessage());

    responder(
        false,
        'No se pudo guardar la solicitud. Intenta nuevamente.',
        500
    );
}
