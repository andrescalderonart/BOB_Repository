<?php
include 'conexion_INV.php';
include 'clasesmod.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario= $_POST['usuario'];
    $obra = $_POST['obra'];
    $tipo = $_POST['tipo'];
    $fecha = $_POST['fecha'];
    $cantidad = $_POST['cantidad'];
    $unidades = $_POST['unidades'];
    $material = $_POST['material'];
    $precio = $_POST['precio'];

    try {
        $registro = crearInventario($usuario, $obra, $tipo, $fecha, $cantidad, $unidades, $material, $precio);
        $conexion1 = Conexion1::conectar();
        $registro->conectar($conexion1);
        echo "¡Registro guardado exitosamente!";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>