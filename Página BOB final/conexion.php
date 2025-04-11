<?php

class Conexion1 {

private static $host = "localhost";
private static $dbname = "gestion_usuarios";
private static $username = "root"; // Cambia este valor si tu usuario es diferente
private static $password = "alfonso";     // Cambia este valor si tienes una contraseña

//$conexion = new mysqli($host, $username, $password, $dbname);

public static function conectar() {
    try {
        $conexion = new PDO("mysql:host=localhost;dbname=gestion_usuarios", "root", "alfonso");
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conexion;
    } catch (PDOException $e) {
        die("Error en la conexión: " . $e->getMessage());
    }
}

}
?>