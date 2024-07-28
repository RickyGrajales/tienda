<?php
require_once __DIR__ . '/config.php';
$serviddor = "mysql:dbname=" . BD . ";host=" . SERVIDOR;
$usuario = USUARIO;

try {

    $pdo = new PDO(
        $serviddor,
        USUARIO,
        PASSWORD,
        array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
    );
    echo "<script>alert('Conectado...')</script>";
} catch (PDOException $e) {
    echo "<script>alert('Error al conectar...')</script>";
}
