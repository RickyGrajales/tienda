<?php
// eliminar_producto.php
require_once __DIR__ . '/global/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id']) && is_numeric($_POST['id'])) {
        $id = $_POST['id'];

        // Consultar la imagen actual para eliminarla del servidor
        $stmt = $pdo->prepare("SELECT Imagen FROM tblproductos WHERE ID = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $producto = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($producto) {
            // Eliminar la imagen del servidor
            $rutaImagen = $_SERVER['DOCUMENT_ROOT'] . $producto['Imagen'];
            if (file_exists($rutaImagen)) {
                unlink($rutaImagen);
            }

            // Eliminar el producto de la base de datos
            $stmt = $pdo->prepare("DELETE FROM tblproductos WHERE ID = :id");
            $stmt->bindParam(':id', $id);

            if ($stmt->execute()) {
                header("Location: gestion_productos.php");
                exit();
            } else {
                echo "Error al eliminar el producto.";
            }
        } else {
            echo "Producto no encontrado.";
        }
    } else {
        echo "ID de producto no especificado.";
    }
} else {
    header("Location: gestion_productos.php");
}
