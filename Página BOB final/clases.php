<?php
//require 'conexion.php';
abstract class Persona {
    protected $Nombre;
    protected $Apellido;
    protected $Correo;
    protected $Direccion;
    protected $Rol;
    protected $Telefono;
    protected $Contraseña;
    protected $Tipo;

//Metodo constructor de Registrar
    public function __construct($Nombre, $Apellido, $Correo, $Direccion, $Rol, $Telefono, $Contraseña, $Tipo) {
        $this->Nombre = $Nombre;
        $this->Apellido = $Apellido;
        $this->Correo = $Correo;
        $this->Direccion = $Direccion;
        $this->Rol = $Rol;
        $this->Telefono = $Telefono;
        $this->Contraseña = $Contraseña;
        $this->Tipo = $Tipo;
    }

    public function getNombre() {
        return $this->Nombre;
    }

    public function setNombre($Nombre) {
        $this->Nombre = $Nombre;
    }

    public function getApellido() {
        return $this->Apellido;
    }

    public function setApellido($Apellido) {
        $this->Apellido = $Apellido;
    }

    public function getCorreo() {
        return $this->Correo;
    }

    public function setCorreo($Correo) {
        $this->Correo = $Correo;
    }

    public function getDireccion() {
        return $this->Direccion;
    }

    public function setDireccion($Direccion) {
        $this->Direccion = $Direccion;
    }

    public function getRol() {
        return $this->Rol;
    }

    public function setRol($Rol) {
        $this->Rol = $Rol;
    }

    public function getTelefono() {
        return $this->Telefono;
    }

    public function setTelefono($Telefono) {
        $this->Telefono = $Telefono;
    }

    public function getContraseña() {
        return $this->Contraseña;
    }

    public function setContraseña($Contraseña) {
        $this->Contraseña = $Contraseña;
    }

    public function getTipo(){
        return $this->Tipo;
    }

    public function setTipo($Tipo){
        $this->Tipo = $Tipo;
    }
    abstract public function conectar($conexion);
    abstract public function ActualizarPersona($conexion, $id);
}

// Clase Usuario
class Usuario extends Persona {
    //public $conexion= Conexion1::conectar();
    public function conectar($conexion): void {
        $sql = "INSERT INTO persona (correo, contrasenna, tipo_persona) VALUES (?, ?, 'usuario')";
        $conexion = Conexion1::conectar();
        $stmt = $conexion->prepare($sql);
        $stmt->bindValue(1, $this->Correo, PDO::PARAM_STR); // Cambia bind_param por bindValue
        $stmt->bindValue(2, $this->Contraseña, PDO::PARAM_STR);
        $stmt->execute();
        $id = $conexion->lastInsertId();


        $sqlUsuario = "INSERT INTO usuario (id, nombre, apellido, direccion, telefono, rol) VALUES (?, ?, ?, ?, ?, ?)";
        $stmtUsuario = $conexion->prepare($sqlUsuario);
        $stmtUsuario->bindValue(1, $id, PDO::PARAM_INT);
        $stmtUsuario->bindValue(2, $this->Nombre, PDO::PARAM_STR);
        $stmtUsuario->bindValue(3, $this->Apellido, PDO::PARAM_STR);
        $stmtUsuario->bindValue(4, $this->Direccion, PDO::PARAM_STR);
        $stmtUsuario->bindValue(5, $this->Telefono, PDO::PARAM_STR);
        $stmtUsuario->bindValue(6, $this->Rol, PDO::PARAM_STR);
        $stmtUsuario->execute();
    }

    public function ActualizarPersona($conexion, $id): void {
        // Actualización específica para usuario
        $sqlPersona = "UPDATE persona SET correo = ?, contrasenna = ?, tipo_persona = ? WHERE id = ?";
        $stmtPersona = $conexion->prepare($sqlPersona);
        $stmtPersona->execute([$this->Correo, $this->Contraseña, $this->Tipo, $id]);

        $sqlUsuario = "UPDATE usuario SET nombre = ?, apellido = ?, direccion = ?, telefono = ?, rol = ? WHERE id = ?";
        $stmtUsuario = $conexion->prepare($sqlUsuario);
        $stmtUsuario->execute([$this->Nombre, $this->Apellido, $this->Direccion, $this->Telefono,$this->Rol, $id]);
    }

    public function eliminarUsuario($conexion, $id) {
        try {
            // Iniciar
            $conexion->beginTransaction();
            // Eliminar de la tabla específica
            $sqlEspecifico = "DELETE FROM usuario WHERE id = ?";
            $stmtEspecifico = $conexion->prepare($sqlEspecifico);
            $stmtEspecifico->execute([$id]);

            // Eliminar de la tabla general
            $sqlGeneral = "DELETE FROM persona  WHERE id = ?";
            $stmtGeneral = $conexion->prepare($sqlGeneral);
            $stmtGeneral->execute([$id]);

            // Confirmar transacción
            $conexion->commit();
            echo "¡Usuario eliminado!";
        } catch (Exception $e) {
            // Revertir transacción si algo falla
            $conexion->rollBack();
            echo "Error al eliminar usuario: " . $e->getMessage();
        }
    }

}

class Proveedor extends Persona {
    public function conectar($conexion): void {
        $sql = "INSERT INTO persona (correo, contrasenna, tipo_persona) VALUES (?, ?, 'proveedor')";
        $conexion = Conexion1::conectar();
        $stmt = $conexion->prepare($sql);
        $stmt->bindValue(1, $this->Correo, PDO::PARAM_STR); // Cambia bind_param por bindValue
        $stmt->bindValue(2, $this->Contraseña, PDO::PARAM_STR);
        $stmt->execute();
        $id = $conexion->lastInsertId();


        $sqlProveedor = "INSERT INTO proveedor (id, nombre, apellido, telefono) VALUES (?, ?, ?, ?)";
        $stmtProveedor = $conexion->prepare($sqlProveedor);
        $stmtProveedor->bindValue(1, $id, PDO::PARAM_INT);
        $stmtProveedor->bindValue(2, $this->Nombre, PDO::PARAM_STR); // Cambia bind_param por bindValue
        $stmtProveedor->bindValue(3, $this->Apellido, PDO::PARAM_STR);
        $stmtProveedor->bindValue(4, $this->Telefono, PDO::PARAM_STR);
        $stmtProveedor->execute();
    }
    public function ActualizarPersona($conexion, $id): void {

        // Actualización específica para el proveedor

        $sqlPersona = "UPDATE proveedor SET correo = ?, contrasenna = ?, telefono = ? WHERE id = ?";
        $stmtPersona = $conexion->prepare($sqlPersona);
        $stmtPersona->execute([$this->Correo, $this->Contraseña, $this->Telefono, $id]);

        $sqlProveedor = "UPDATE contratista SET nombre = ?, apellido = ?, telefono = ?, WHERE id = ?";
        $stmtProveedor = $conexion->prepare($sqlProveedor);
        $stmtProveedor->execute([$this->Nombre, $this->Apellido, $this->Telefono, $id]);
    }

    public function eliminarProveedor($conexion, $id) {
        try {
            // Iniciar
            $conexion->beginTransaction();

            // Eliminar de la tabla específica
            $sqlEspecifico = "DELETE FROM proveedor WHERE id = ?";
            $stmtEspecifico = $conexion->prepare($sqlEspecifico);
            $stmtEspecifico->execute([$id]);

            // Eliminar de la tabla general
            $sqlGeneral = "DELETE FROM persona WHERE id = ?";
            $stmtGeneral = $conexion->prepare($sqlGeneral);
            $stmtGeneral->execute([$id]);

            // Confirmar transacción
            $conexion->commit();
            echo "¡Proveedor borrado exitosamente!";
        } catch (Exception $e) {
            // Revertir transacción si algo falla
            $conexion->rollBack();
            echo "Error al borrar al proveedor: " . $e->getMessage();
        }
    }

}

class Contratista extends Persona {
    public function conectar($conexion): void {
        $sql = "INSERT INTO contratista (correo, contrasenna, tipo_persona) VALUES (?, ?, 'contratista')";
        $conexion = Conexion1::conectar();
        $stmt = $conexion->prepare($sql);
        $stmt->bindValue(1, $this->Correo, PDO::PARAM_STR); // Cambia bind_param por bindValue
        $stmt->bindValue(2, $this->Contraseña, PDO::PARAM_STR);
        $stmt->execute();
        $id = $conexion->lastInsertId();


        $sqlContratista = "INSERT INTO contratista (id, nombre, apellido, telefono) VALUES (?, ?, ?, ?)";
        $stmtContratista = $conexion->prepare($sqlContratista);
        $stmtContratista->bindValue(1, $id, PDO::PARAM_INT);
        $stmtContratista->bindValue(2, $this->Nombre, PDO::PARAM_STR); // Cambia bind_param por bindValue
        $stmtContratista->bindValue(3, $this->Apellido, PDO::PARAM_STR);
        $stmtContratista->bindValue(4, $this->Telefono, PDO::PARAM_STR);
        $stmtContratista->execute();
    }
    public function ActualizarPersona($conexion, $id): void {

        // Actualización específica para Contratista

        $sqlPersona = "UPDATE persona SET correo = ?, contrasenna = ?, telefono = ? WHERE id = ?";
        $stmtPersona = $conexion->prepare($sqlPersona);
        $stmtPersona->execute([$this->Correo, $this->Contraseña, $this->Telefono, $id]);

        $sqlContratista = "UPDATE contratista SET nombre = ?, apellido = ?, telefono = ?, WHERE id = ?";
        $stmtContratista = $conexion->prepare($sqlContratista);
        $stmtContratista->execute([$this->Nombre, $this->Apellido, $this->Telefono, $id]);
    }

    public function eliminarContratista($conexion, $id): void {
        try {
            // Iniciar
            $conexion->beginTransaction();

            // Eliminar de la tabla específica
            $sqlEspecifico = "DELETE FROM contratista WHERE id = ?";
            $stmtEspecifico = $conexion->prepare($sqlEspecifico);
            $stmtEspecifico->execute([$id]);

            // Eliminar de la tabla general
            $sqlGeneral = "DELETE FROM persona WHERE id = ?";
            $stmtGeneral = $conexion->prepare($sqlGeneral);
            $stmtGeneral->execute([$id]);

            // Confirmar transacción
            $conexion->commit();
            echo "¡Contratista borrado exitosamente!";
        } catch (Exception $e) {
            // Revertir transacción si algo falla
            $conexion->rollBack();
            echo "Error al borrar a el contratista: " . $e->getMessage();
        }
    }
}

// Función Factory
function crearPersona($Nombre, $Apellido, $Correo, $Direccion, $Rol, $Telefono, $Contraseña, $Tipo) {
    switch ($Tipo) {
        case 'usuario':
            return new Usuario($Nombre, $Apellido, $Correo, $Direccion, $Rol, $Telefono, $Contraseña, $Tipo);
        case 'proveedor':
            return new Proveedor($Nombre, $Apellido, $Correo, $Direccion, $Rol, $Telefono, $Contraseña, $Tipo);
        case 'contratista':
            return new Contratista($Nombre, $Apellido, $Correo, $Direccion, $Rol, $Telefono, $Contraseña, $Tipo);
        default:
            throw new Exception("Tipo de usuario no válido.");
    }
}
?>