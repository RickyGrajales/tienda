<?php
// insertar_producto.php

// Incluir archivos de configuración y conexión
include 'global/config.php';
include 'global/conexion.php';

// Verificar si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar y obtener los datos del formulario
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];

    // Validar si el archivo fue cargado correctamente
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        // Obtener detalles del archivo
        $fileTmpPath = $_FILES['imagen']['tmp_name'];
        $fileName = $_FILES['imagen']['name'];
        $fileSize = $_FILES['imagen']['size'];
        $fileType = $_FILES['imagen']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        // Validar tipos de archivo permitidos
        $allowedfileExtensions = array('jpg', 'jpeg', 'png', 'gif',);
        if (in_array($fileExtension, $allowedfileExtensions)) {
            // Crear un nombre único para la imagen
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;

            // Ruta donde se guardará la imagen
            $uploadFileDir = 'imagenes/';
            $dest_path = $uploadFileDir . $newFileName;

            // Mover el archivo a la carpeta de imágenes
            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                // Guardar los datos del producto en la base de datos
                try {
                    $sentencia = $pdo->prepare("INSERT INTO tblproductos (Nombre, Descripcion, Precio, Imagen) VALUES (:nombre, :descripcion, :precio, :imagen)");

                    // Vincular parámetros
                    $sentencia->bindParam(':nombre', $nombre);
                    $sentencia->bindParam(':descripcion', $descripcion);
                    $sentencia->bindParam(':precio', $precio);
                    $sentencia->bindParam(':imagen', $newFileName);

                    // Ejecutar la consulta
                    $sentencia->execute();

                    // Mensaje de éxito
                    $mensaje = "Producto agregado con éxito.";
                } catch (Exception $e) {
                    // Manejar errores
                    $mensaje = "Error al agregar producto: " . $e->getMessage();
                }
            } else {
                $mensaje = "Error al mover el archivo al directorio de destino.";
            }
        } else {
            $mensaje = "Tipo de archivo no permitido.";
        }
    } else {
        $mensaje = "No se pudo cargar la imagen.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insertar Producto</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <div class="container">
        <h1 class="mt-5">Insertar Producto</h1>

        <?php if (isset($mensaje)) { ?>
            <div class="alert alert-info">
                <?php echo $mensaje; ?>
            </div>
        <?php } ?>

        <form action="insertar_producto.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="nombre">Nombre del Producto:</label>
                <input type="text" name="nombre" id="nombre" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="descripcion">Descripción:</label>
                <textarea name="descripcion" id="descripcion" class="form-control" rows="3" required></textarea>
            </div>
            <div class="form-group">
                <label for="precio">Precio:</label>
                <input type="text" name="precio" id="precio" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="imagen">Imagen:</label>
                <input type="file" name="imagen" id="imagen" class="form-control-file" required>
            </div>
            <button type="submit" class="btn btn-primary">Agregar Producto</button>
        </form>
    </div>
</body>

</html>