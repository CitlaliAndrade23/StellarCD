
<?php
// =====================================
// 1. INICIAR SESIÓN
// =====================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// =====================================
// 2. VERIFICAR ADMINISTRADOR
// =====================================

if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../iniciarSesion.php');
    exit;
}

if (($_SESSION['rol'] ?? '') !== 'administrador') {
    http_response_code(403);
    exit('Acceso no autorizado.');
}

// =====================================
// 3. CONEXIÓN A MYSQL
// =====================================

require_once __DIR__ . '/../config/conexion.php';

// Token de seguridad
if (empty($_SESSION['csrf_admin'])) {
    $_SESSION['csrf_admin'] = bin2hex(random_bytes(32));
}

// =====================================
// 4. VARIABLES DEL FORMULARIO
// =====================================

$error = '';

$nombre = '';
$descripcion = '';
$precio = '';
$disponible = 1;

$idCategoria = false;

// =====================================
// 5. OBTENER CATEGORÍA MUSICALES
// =====================================

try {

    $consultaCategoria = $conexion->prepare(
        "SELECT id_categoria
         FROM categorias
         WHERE nombre = :nombre
         LIMIT 1"
    );

    $consultaCategoria->execute([
        'nombre' => 'Musicales'
    ]);

    $idCategoria = $consultaCategoria->fetchColumn();

    if ($idCategoria === false) {
        $error = 'No existe la categoría Musicales en la base de datos.';
    }

} catch (PDOException $e) {

    error_log($e->getMessage());

    $error = 'No se pudo consultar la categoría del producto.';
}

// =====================================
// 6. GUARDAR PRODUCTO
// =====================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $precio = trim($_POST['precio'] ?? '');

    $disponible = isset($_POST['disponible']) ? 1 : 0;

    $imagenNombre = null;
    $imagenRuta = null;

    // VALIDACIONES

    if ($error !== '') {

        // Conservar el error de la categoría.

    } elseif (
        !isset($_POST['csrf_token']) ||
        !is_string($_POST['csrf_token']) ||
        !hash_equals(
            $_SESSION['csrf_admin'],
            $_POST['csrf_token']
        )
    ) {

        $error = 'Solicitud no válida. Actualiza la página.';

    } elseif (
        $nombre === '' ||
        mb_strlen($nombre) > 150
    ) {

        $error = 'Ingresa un nombre válido (máximo 150 caracteres).';

    } elseif (
        $precio === '' ||
        !preg_match('/^\d{1,8}(\.\d{1,2})?$/', $precio) ||
        (float) $precio <= 0
    ) {

        $error = 'Ingresa un precio válido mayor a cero.';
    }

    // =====================================
    // 7. VALIDAR IMAGEN
    // =====================================

    if ($error === '' && isset($_FILES['imagen'])) {

        $archivo = $_FILES['imagen'];

        if ($archivo['error'] !== UPLOAD_ERR_NO_FILE) {

            if ($archivo['error'] !== UPLOAD_ERR_OK) {

                $error = 'No se pudo subir la imagen.';

            } elseif ($archivo['size'] > 3 * 1024 * 1024) {

                $error = 'La imagen no debe superar los 3 MB.';

            } else {

                $finfo = new finfo(FILEINFO_MIME_TYPE);

                $tipo = $finfo->file($archivo['tmp_name']);

                $tiposPermitidos = [
                    'image/jpeg' => 'jpg',
                    'image/png' => 'png',
                    'image/webp' => 'webp'
                ];

                if (!isset($tiposPermitidos[$tipo])) {

                    $error = 'Solo se permiten imágenes JPG, PNG o WebP.';

                } elseif (
                    @getimagesize($archivo['tmp_name']) === false
                ) {

                    $error = 'El archivo no es una imagen válida.';

                } else {

                    $extension = $tiposPermitidos[$tipo];

                    $imagenNombre =
                        'producto_' .
                        bin2hex(random_bytes(12)) .
                        '.' . $extension;

                    $directorio = __DIR__ . '/../assets/img/';

                    if (
                        !is_dir($directorio) ||
                        !is_writable($directorio)
                    ) {

                        $error = 'La carpeta de imágenes no está disponible.';

                    } else {

                        $imagenRuta = $directorio . $imagenNombre;

                        if (!move_uploaded_file(
                            $archivo['tmp_name'],
                            $imagenRuta
                        )) {

                            $error = 'No se pudo guardar la imagen.';
                        }
                    }
                }
            }
        }
    }

    // =====================================
    // 8. INSERTAR EN MYSQL
    // =====================================

    if ($error === '') {

        try {

            $sql = "INSERT INTO productos
                        (nombre, descripcion, precio,
                         imagen, id_categoria, disponible)
                    VALUES
                        (:nombre, :descripcion, :precio,
                         :imagen, :id_categoria, :disponible)";

            $consulta = $conexion->prepare($sql);

            $consulta->execute([
                'nombre' => $nombre,
                'descripcion' => $descripcion,
                'precio' => $precio,
                'imagen' => $imagenNombre,
                'id_categoria' => (int) $idCategoria,
                'disponible' => $disponible
            ]);

            header('Location: productos.php?creado=1');
            exit;

        } catch (PDOException $e) {

            error_log($e->getMessage());

            if (
                $imagenRuta !== null &&
                is_file($imagenRuta)
            ) {
                unlink($imagenRuta);
            }

            $error = 'No fue posible guardar el producto.';
        }
    }
}

// =====================================
// 9. FUNCIÓN PARA ESCAPAR TEXTO
// =====================================

function escapar($valor) {
    return htmlspecialchars(
        (string) $valor,
        ENT_QUOTES,
        'UTF-8'
    );
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Agregar producto | Stellar CD</title>

    <link rel="stylesheet"
          href="../assets/css/style.css?v=13">

    
</head>

<body>

<?php require __DIR__ . '/../includes/header.php'; ?>

<main class="admin-form-contenedor">

    <span class="etiqueta">
        STELLAR CD / ADD PRODUCT
    </span>

    <h1>
        NUEVO <span>PRODUCTO. ✳</span>
    </h1>

    <p class="admin-form-intro">
        Agrega un nuevo mini CD al catálogo de Stellar CD.
    </p>

    <?php if ($error !== ''): ?>

        <div class="admin-error" role="alert">
            <?php echo escapar($error); ?>
        </div>

    <?php endif; ?>

    <form
        method="POST"
        enctype="multipart/form-data"
        class="admin-form-tarjeta">

        <input
            type="hidden"
            name="csrf_token"
            value="<?php echo escapar($_SESSION['csrf_admin']); ?>">

        <!-- NOMBRE -->

        <div class="admin-campo">

            <label for="nombre">
                NOMBRE DEL PRODUCTO
            </label>

            <input
                type="text"
                id="nombre"
                name="nombre"
                maxlength="150"
                value="<?php echo escapar($nombre); ?>"
                placeholder="Ej. Mini CD Edición Especial"
                required>

        </div>

        <!-- DESCRIPCIÓN -->

        <div class="admin-campo">

            <label for="descripcion">
                DESCRIPCIÓN
            </label>

            <textarea
                id="descripcion"
                name="descripcion"
                placeholder="Describe el producto..."><?php
                    echo escapar($descripcion);
                ?></textarea>

        </div>

        <!-- PRECIO -->

        <div class="admin-form-grid">

            <div class="admin-campo">

                <label for="precio">
                    PRECIO (MXN)
                </label>

                <input
                    type="number"
                    id="precio"
                    name="precio"
                    min="0.01"
                    step="0.01"
                    value="<?php echo escapar($precio); ?>"
                    placeholder="Ej. 95.00"
                    required>

            </div>

        </div>

        <!-- IMAGEN -->

        <div class="admin-campo">

            <label for="imagen">
                IMAGEN DEL PRODUCTO
            </label>

            <input
                type="file"
                id="imagen"
                name="imagen"
                accept="image/jpeg,image/png,image/webp">

            <span class="admin-ayuda">
                Formatos permitidos: JPG, PNG o WebP.
                Tamaño máximo: 3 MB.
            </span>

        </div>

        <!-- DISPONIBILIDAD -->

        <div class="admin-check">

            <input
                type="checkbox"
                id="disponible"
                name="disponible"
                value="1"
                <?php echo $disponible === 1 ? 'checked' : ''; ?>>

            <label for="disponible">
                Disponible en el catálogo público
            </label>

        </div>

        <!-- BOTONES -->

        <button
            type="submit"
            class="admin-boton-guardar"
            <?php echo $idCategoria === false ? 'disabled' : ''; ?>>

            GUARDAR PRODUCTO ↗

        </button>

        <a href="productos.php"
           class="admin-boton-cancelar">

            CANCELAR

        </a>

    </form>

</main>

</body>
</html>
