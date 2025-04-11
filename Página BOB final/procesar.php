<?php
include 'conexion.php';
include 'clases.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $Nombre = $_POST['nombre'];
    $Apellido = $_POST['apellido'];
    $Correo = $_POST['correo'];
    $Direccion = $_POST['direccion'];
    $Rol = $_POST['rol'];
    $Telefono = $_POST['telefono'];

    $Contrasenna = $_POST['contrasenna'];
    //Validar mayusculas y minusculas
    if (!preg_match('/[a-z]/', $Contrasenna) || !preg_match('/[A-Z]/', $Contrasenna)) {
        echo "\nLa contraseña debe incluir mayúsculas y minúsculas.\n";
        exit();
    }

    // Validar la presencia de al menos un carácter especial
    if (!preg_match('/[\W_]/', $Contrasenna)) {
        echo "\nLa contraseña debe incluir al menos un carácter especial (ej. !, @, #, $, etc.).\n";
        exit();
    }


    $Tipo = $_POST['tipo_persona'];



    try {
        $persona = crearPersona($Nombre, $Apellido, $Correo, $Direccion, $Rol, $Telefono, $Contrasenna, $Tipo);
        $conexion1 = Conexion1::conectar();
        $persona->conectar($conexion1);
        echo "¡Usuario registrado exitosamente!";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>