<?php
//require 'conexion.php';
abstract class Lista_Mate {
    protected $usuario;
    protected $obra;
    protected $tipo;
    protected $fecha;
    protected $cantidad;
    protected $unidades;
    protected $material;
    protected $precio;

    public function __construct($usuario, $obra, $tipo, $fecha, $cantidad, $unidades, $material, $precio) {
        $this->usuario = $usuario;
        $this->obra = $obra;
        $this->tipo = $tipo;
        $this->fecha = $fecha;
        $this->cantidad = $cantidad;
        $this->unidades = $unidades;
        $this->material = $material;
        $this->precio = $precio;
    }

    public function getUsuario() {
        return $this->usuario;
    }

    public function setUsuario($usuario) {
        $this->usuario = $usuario;
    }

    public function getObra() {
        return $this->obra;
    }

    public function setObra($obra) {
        $this->obra = $obra;
    }

    public function getTipo() {
        return $this->tipo;
    }

    public function setTipo($tipo) {
        $this->tipo = $tipo;
    }

    public function getUnidades() {
        return $this->unidades;
    }

    public function setUnidades($unidades) {
        $this->unidades = $unidades;
    }

    public function getMaterial() {
        return $this->material;
    }

    public function setMaterial($material) {
        $this->material = $material;
    }

    public function getPrecio() {
        return $this->precio;
    }

    public function setPrecio($precio) {
        $this->precio = $precio;
    }



    abstract public function conectar($conexion);
    abstract public function ActualizarInventario($conexion, $id);

    //abstract public function borrarVehiculo($conexion, $id);
}

class Ingreso extends Lista_Mate {
    //public $conexion= Conexion1::conectar();
    public function conectar($conexion): void {
        $sql = "INSERT INTO lista_materiales (usuario, obra, tipo) VALUES (?, ?, 'ingreso')";
        $conexion = Conexion1::conectar();
        $stmt = $conexion->prepare($sql);
        $stmt->bindValue(1, $this->usuario, PDO::PARAM_STR); // Cambia bind_param por bindValue
        $stmt->bindValue(2, $this->obra, PDO::PARAM_STR);
        $stmt->execute();
        $id = $conexion->lastInsertId();



        $sqlIngreso = "INSERT INTO ingreso (id, fecha, cantidad, unidades, material, precio) VALUES (?, ?, ?, ?, ?, ?)";
        $stmtIngreso = $conexion->prepare($sqlIngreso);
        $stmtIngreso->bindValue(1, $id, PDO::PARAM_INT);
        $stmtIngreso->bindValue(2, $this->fecha, PDO::PARAM_STR); // Cambia bind_param por bindValue
        $stmtIngreso->bindValue(3, $this->cantidad, PDO::PARAM_STR);
        $stmtIngreso->bindValue(4, $this->unidades, PDO::PARAM_STR);
        $stmtIngreso->bindValue(5, $this->material, PDO::PARAM_STR);
        $stmtIngreso->bindValue(6, $this->precio, PDO::PARAM_STR);
        $stmtIngreso->execute();
    }

    public function ActualizarInventario($conexion, $id): void {
        // Actualización específica para Ingreso
        $sqlLista_Mate = "UPDATE lista_materiales SET usuario = ?, obra = ?, tipo = ? WHERE id = ?";
        $stmtLista_Mate = $conexion->prepare($sqlLista_Mate);
        $stmtLista_Mate->execute([$this->usuario, $this->obra, $this->tipo, $id]);

        $sqlIngreso = "UPDATE ingreso SET fecha = ?, cantidad = ?, unidades = ?, material = ?, precio = ? WHERE id = ?";
        $stmtIngreso = $conexion->prepare($sqlIngreso);
        $stmtIngreso->execute([$this->fecha, $this->cantidad, $this->unidades, $this->material, $this->precio, $id]);
    }

    public function borrarInventario($conexion, $id) {
        try {
            // Iniciar transacción
            $conexion->beginTransaction();

            // Eliminar de la tabla específica
            $sqlEspecifico = "DELETE FROM ingreso WHERE id = ?";
            $stmtEspecifico = $conexion->prepare($sqlEspecifico);
            $stmtEspecifico->execute([$id]);

            // Eliminar de la tabla general
            $sqlGeneral = "DELETE FROM lista_materiales WHERE id = ?";
            $stmtGeneral = $conexion->prepare($sqlGeneral);
            $stmtGeneral->execute([$id]);

            // Confirmar transacción
            $conexion->commit();
            echo "¡Registro borrado exitosamente!";
        } catch (Exception $e) {
            // Revertir transacción si algo falla
            $conexion->rollBack();
            echo "Error al borrar el registro: " . $e->getMessage();
        }
    }


}

class Consumo extends Lista_Mate {
    public function conectar($conexion): void {
        $sql = "INSERT INTO lista_materiales (usuario, obra, tipo) VALUES (?, ?, 'consumo')";
        $conexion = Conexion1::conectar();
        $stmt = $conexion->prepare($sql);

        $stmt->bindValue(1, $this->usuario, PDO::PARAM_STR); // Cambia bind_param por bindValue
        $stmt->bindValue(2, $this->obra, PDO::PARAM_STR);
        $stmt->execute();
        $id = $conexion->lastInsertId();


        $sqlConsumo = "INSERT INTO consumo (id, fecha, cantidad, unidades, material, precio) VALUES (?, ?, ?, ?, ?, ?)";
        $sqlConsumo = $conexion->prepare($sqlConsumo);
        $sqlConsumo->bindValue(1, $id, PDO::PARAM_INT);
        $sqlConsumo->bindValue(2, $this->fecha, PDO::PARAM_STR); // Cambia bind_param por bindValue
        $sqlConsumo->bindValue(3, $this->cantidad, PDO::PARAM_STR);
        $sqlConsumo->bindValue(4, $this->unidades, PDO::PARAM_STR);
        $sqlConsumo->bindValue(5, $this->material, PDO::PARAM_STR);
        $sqlConsumo->bindValue(6, $this->precio, PDO::PARAM_STR);
        $sqlConsumo->execute();
    }
    public function ActualizarInventario($conexion, $id): void {
        // Actualización específica para Consumo
        $sqlLista_Mate = "UPDATE lista_materiales SET usuario = ?, obra = ?, tipo = ? WHERE id = ?";
        $stmtLista_Mate = $conexion->prepare($sqlLista_Mate);
        $stmtLista_Mate->execute([$this->usuario, $this->obra, $this->tipo, $id]);

        $sqlConsumo = "UPDATE consumo SET fecha = ?, cantidad = ?, unidades = ?, material = ?, precio = ? WHERE id = ?";
        $stmtConsumo = $conexion->prepare($sqlConsumo);
        $stmtConsumo->execute([$this->fecha, $this->cantidad, $this->unidades, $this->material, $this->precio, $id]);
    }

    public function borrarInventario($conexion, $id) {
        try {
            // Iniciar transacción
            $conexion->beginTransaction();

            // Eliminar de la tabla específica
            $sqlEspecifico = "DELETE FROM consumo WHERE id = ?";
            $stmtEspecifico = $conexion->prepare($sqlEspecifico);
            $stmtEspecifico->execute([$id]);

            // Eliminar de la tabla general
            $sqlGeneral = "DELETE FROM lista_materiales WHERE id = ?";
            $stmtGeneral = $conexion->prepare($sqlGeneral);
            $stmtGeneral->execute([$id]);

            // Confirmar transacción
            $conexion->commit();
            echo "¡Registro borrado exitosamente!";
        } catch (Exception $e) {
            // Revertir transacción si algo falla
            $conexion->rollBack();
            echo "Error al borrar el registro: " . $e->getMessage();
        }
    }

}

class Presupuesto extends Lista_Mate {
    public function conectar($conexion): void {
        $sql = "INSERT INTO lista_materiales (usuario, obra, tipo) VALUES (?, ?, 'presupuesto')";
        $conexion = Conexion1::conectar();
        $stmt = $conexion->prepare($sql);

        $stmt->bindValue(1, $this->usuario, PDO::PARAM_STR); // Cambia bind_param por bindValue
        $stmt->bindValue(2, $this->obra, PDO::PARAM_STR);
        $stmt->execute();
        $id = $conexion->lastInsertId();


        $sqlPresupuesto = "INSERT INTO presupuesto (id, fecha, cantidad, unidades, material, precio) VALUES (?, ?, ?, ?, ?, ?)";
        $sqlPresupuesto = $conexion->prepare($sqlPresupuesto);
        $sqlPresupuesto->bindValue(1, $id, PDO::PARAM_INT);
        $sqlPresupuesto->bindValue(2, $this->fecha, PDO::PARAM_STR); // Cambia bind_param por bindValue
        $sqlPresupuesto->bindValue(3, $this->cantidad, PDO::PARAM_STR);
        $sqlPresupuesto->bindValue(4, $this->unidades, PDO::PARAM_STR);
        $sqlPresupuesto->bindValue(5, $this->material, PDO::PARAM_STR);
        $sqlPresupuesto->bindValue(6, $this->precio, PDO::PARAM_STR);
        $sqlPresupuesto->execute();
    }
    public function ActualizarInventario($conexion, $id): void {
        // Actualización específica para Presupuesto
        $sqlLista_Mate = "UPDATE lista_materiales SET usuario = ?, obra = ?, tipo = ? WHERE id = ?";
        $stmtLista_Mate = $conexion->prepare($sqlLista_Mate);
        $stmtLista_Mate->execute([$this->usuario, $this->obra, $this->tipo, $id]);

        $sqlPresupuesto = "UPDATE presupuesto SET fecha = ?, cantidad = ?, unidades = ?, material = ?, precio = ? WHERE id = ?";
        $stmtPresupuesto = $conexion->prepare($sqlPresupuesto);
        $stmtPresupuesto->execute([$this->fecha, $this->cantidad, $this->unidades, $this->material, $this->precio, $id]);
    }

    public function borrarInventario($conexion, $id) {
        try {
            // Iniciar transacción
            $conexion->beginTransaction();

            // Eliminar de la tabla específica
            $sqlEspecifico = "DELETE FROM presupuesto WHERE id = ?";
            $stmtEspecifico = $conexion->prepare($sqlEspecifico);
            $stmtEspecifico->execute([$id]);

            // Eliminar de la tabla general
            $sqlGeneral = "DELETE FROM lista_materiales WHERE id = ?";
            $stmtGeneral = $conexion->prepare($sqlGeneral);
            $stmtGeneral->execute([$id]);

            // Confirmar transacción
            $conexion->commit();
            echo "¡Registro borrado exitosamente!";
        } catch (Exception $e) {
            // Revertir transacción si algo falla
            $conexion->rollBack();
            echo "Error al borrar el registro: " . $e->getMessage();
        }
    }

}

// Función Factory
function crearInventario($usuario, $obra, $tipo, $fecha, $cantidad, $unidades, $material, $precio) {
    switch ($tipo) {
        case 'ingreso':
            return new Ingreso($usuario, $obra, $tipo, $fecha, $cantidad, $unidades, $material, $precio);
        case 'consumo':
            return new Consumo($usuario, $obra, $tipo, $fecha, $cantidad, $unidades, $material, $precio);
        case 'presupuesto':
            return new Presupuesto($usuario, $obra, $tipo, $fecha, $cantidad, $unidades, $material, $precio);
        default:
            throw new Exception("Tipo de registro no válido.");
    }
}






?>