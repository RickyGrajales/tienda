<?php
// actualizar_producto.php
require_once __DIR__ . '/global/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    $descripcion = $_POST['descripcion'];

    // Actualización de la imagen si se selecciona un nuevo archivo
    if (!empty($_FILES['imagen']['name'])) {
        // Corregir el directorio de subida
        $directorioSubida = $_SERVER['DOCUMENT_ROOT'] . '/imagenes/';

        // Generar un nombre único para la imagen
        $fileExtension = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
        $nuevoNombreArchivo = md5(time() . $_FILES['imagen']['name']) . '.' . $fileExtension;
        $rutaImagen = $directorioSubida . $nuevoNombreArchivo;

        // Verificar si el archivo es una imagen
        $check = getimagesize($_FILES['imagen']['tmp_name']);
        if ($check === false) {
            echo "El archivo no es una imagen.";
            exit();
        }

        // Verificar si el directorio de subida existe
        if (!is_dir($directorioSubida)) {
            mkdir($directorioSubida, 0777, true); // Crear el directorio si no existe
        }

        // Mover el archivo al directorio de subida
        if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaImagen)) {
            echo "Error al subir la imagen.";
            exit();
        }

        // Ruta relativa de la imagen para la base de datos
        $rutaRelativa = 'imagenes/' . $nuevoNombreArchivo;

        // Actualizar la imagen en la base de datos
        $sql = "UPDATE tblproductos SET Nombre = :nombre, Precio = :precio, Descripcion = :descripcion, Imagen = :imagen WHERE ID = :id";
    } else {
        // Si no se selecciona un nuevo archivo, solo actualizar los otros campos
        $sql = "UPDATE tblproductos SET Nombre = :nombre, Precio = :precio, Descripcion = :descripcion WHERE ID = :id";
    }

    // Preparar la declaración SQL
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':precio', $precio);
    $stmt->bindParam(':descripcion', $descripcion);
    $stmt->bindParam(':id', $id);
    if (!empty($_FILES['imagen']['name'])) {
        $stmt->bindParam(':imagen', $rutaRelativa);
    }

    // Ejecutar la declaración SQL
    if ($stmt->execute()) {
        header("Location: gestion_productos.php");
        exit();
    } else {
        echo "Error al actualizar el producto.";
    }
} else {
    // Obtener el producto a actualizar
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $id = $_GET['id'];
        $stmt = $pdo->prepare("SELECT * FROM tblproductos WHERE ID = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $producto = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$producto) {
            die("Producto no encontrado.");
        }
    } else {
        die("ID de producto no especificado.");
    }
}
?>

<!-- Formulario para editar el producto -->
<form action="actualizar_producto.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?php echo htmlspecialchars($producto['ID']); ?>">
    <label>Nombre: <input type="text" name="nombre" value="<?php echo htmlspecialchars($producto['Nombre']); ?>" required></label><br>
    <label>Precio: <input type="number" name="precio" value="<?php echo htmlspecialchars($producto['Precio']); ?>" step="0.01" required></label><br>
    <label>Descripción: <textarea name="descripcion" required><?php echo htmlspecialchars($producto['Descripcion']); ?></textarea></label><br>
    <label>Imagen actual: <img src="/imagenes/<?php echo htmlspecialchars($producto['Imagen']); ?>" alt="Imagen del Producto" width="100"></label><br>
    <label>Nueva imagen: <input type="file" name="imagen"></label><br>
    <button type="submit">Actualizar Producto</button>
</form>