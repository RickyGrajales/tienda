<?php
// gestion_productos.php
require_once __DIR__ . '/global/conexion.php';

$stmt = $pdo->query("SELECT * FROM tblproductos");
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($productos as $producto) {
    echo "<p>" . htmlspecialchars($producto['Nombre']) . " - 
          <a href='actualizar_producto.php?id=" . urlencode($producto['ID']) . "'>Actualizar</a> | 
          <form action='eliminar_producto.php' method='post' style='display:inline;'>
              <input type='hidden' name='id' value='" . htmlspecialchars($producto['ID']) . "'>
              <button type='submit' onclick='return confirm(\"¿Estás seguro de que deseas eliminar este producto?\");'>Eliminar</button>
          </form>
          </p>";
}
