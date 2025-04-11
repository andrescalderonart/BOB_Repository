<?php
require_once 'conexion_INV.php';
require_once 'clasesmod.php';

// Obtener la lista de registros
try {
    $conexion = Conexion1::conectar();
    $sql = "SELECT id, usuario, obra, tipo FROM lista_materiales";
    $stmt = $conexion->prepare($sql);
    $stmt->execute();
    $registro = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    echo "Error al obtener los registros: " . $e->getMessage();
}

// Procesar formulario para borrar vehículo
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $tipo = $_POST['tipo'] ?? null;

    if (empty($id) || empty($tipo)) {
        echo "<p style='color:red; text-align:center;'>Por favor, selecciona un registro y su tipo para borrar.</p>";
    } else {
        try {
            $conexion = Conexion1::conectar();

            switch ($tipo) {
                case 'ingreso':
                case 'consumo':
                case 'presupuesto':
                    $tablaEspecifica = $tipo;
                    break;
                default:
                    throw new Exception("Tipo de registro no válido.");
            }

            $conexion->beginTransaction();

            $sqlEspecifico = "DELETE FROM $tablaEspecifica WHERE id = ?";
            $stmtEspecifico = $conexion->prepare($sqlEspecifico);
            $stmtEspecifico->execute([$id]);

            $sqlGeneral = "DELETE FROM lista_materiales WHERE id = ?";
            $stmtGeneral = $conexion->prepare($sqlGeneral);
            $stmtGeneral->execute([$id]);

            $conexion->commit();
            echo "<p style='color:green; text-align:center;'>¡Registro borrado exitosamente!</p>";
        } catch (Exception $e) {
            $conexion->rollBack();
            echo "<p style='color:red; text-align:center;'>Error al borrar el registro: " . $e->getMessage() . "</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Borrar Registro</title>
    <link rel="stylesheet" href="CSS/dashboard2.css">
</head>
<body>
    <header>
        <div class="top-bar">
            <div class="logo-container">
                <img src="IMG/imagen bob.png" alt="Logo" class="logo">
                <h1 class="logo-text">Building Optimization Baseline</h1>
            </div>
            <ul class="nav-links">
                <li><a href="dashboard.html">Volver</a></li>
            </ul>
        </div>
    </header>

    <main>
        <div class="content-inventario">
            <div id="borrar-registro" class="formulario-borrar">
                <div class="diseño-borrar-registro">
                    <h3>Borrar registro</h3>
                    <form method="post" action="borrar_INV.php">
                        <label for="id">Selecciona el registro:</label>
                        <select name="id" id="id" required>
                            <option value="">--Selecciona un registro--</option>
                            <?php if (!empty($registro)) : ?>
                                <?php foreach ($registro as $r): ?>
                                    <option value="<?= $r['id']; ?>">
                                        ID: <?= $r['id']; ?> - <?= $r['usuario']; ?> <?= $r['obra']; ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <option value="">No hay registros disponibles</option>
                            <?php endif; ?>
                        </select>
                        <br><br>

                        <label for="tipo">Tipo de registro:</label>
                        <select name="tipo" id="tipo" required>
                            <option value="">--Tipo--</option>
                            <option value="ingreso">Ingreso</option>
                            <option value="consumo">Consumo</option>
                            <option value="presupuesto">Presupuesto</option>
                        </select>
                        <br><br>

                        <button type="submit">Borrar registro</button>
                        <a class="boton-mover" href="dashboard2.html#inventario">← Volver</a>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2025 Building Optimization Baseline. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
