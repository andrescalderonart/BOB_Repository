<?php
require_once 'conexion.php';
require_once 'clases.php';

// Obtener la lista de los usuarios
try {
    $conexion = Conexion1::conectar();
    $sql = "SELECT id, correo, tipo_persona FROM persona";
    $stmt = $conexion->prepare($sql);
    $stmt->execute();
    $people = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    echo "Error al obtener los datos: " . $e->getMessage();
}

// Procesar formulario para borrar usuarios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $tipo = $_POST['tipo_persona'] ?? null;

    if (empty($id) || empty($tipo)) {
        die("Por favor, seleccione un usuario y su tipo para borrar.");
    }

    try {
        $conexion = Conexion1::conectar();

        switch ($tipo) {
            case 'usuario':
                $tablaEspecifica = 'usuario';
                break;
            case 'proveedor':
                $tablaEspecifica = 'proveedor';
                break;
            case 'contratista':
                $tablaEspecifica = 'contratista';
                break;
            default:
                throw new Exception("Tipo de persona no válido.");
        }

        $conexion->beginTransaction();

        // Borrar en la tabla específica
        $sqlEspecifico = "DELETE FROM $tablaEspecifica WHERE id = ?";
        $stmtEspecifico = $conexion->prepare($sqlEspecifico);
        $stmtEspecifico->execute([$id]);

        // Borrar en la tabla general
        $sqlGeneral = "DELETE FROM persona WHERE id = ?";
        $stmtGeneral = $conexion->prepare($sqlGeneral);
        $stmtGeneral->execute([$id]);

        $conexion->commit();
        echo "<p style='color:green; text-align:center;'>¡Sujeto borrado exitosamente!</p>";
    } catch (Exception $e) {
        $conexion->rollBack();
        echo "<p style='color:red; text-align:center;'>Error al borrar el sujeto: " . $e->getMessage() . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Borrar Usuario</title>
    <link rel="stylesheet" href="CSS/dashboard2.css">
</head>
<body>

<!-- Header -->
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
    <div class="content-usuario">
        <div class="formulario-borrar-usuario">
            <div class="diseño-borrar-usuario">
                <h3>Borrar usuario</h3>
                <form method="post" action="borrar.php">
                    <label for="id">Selecciona:</label>
                    <select name="id" id="id" required>
                        <option value="">--Selecciona--</option>
                        <?php if (!empty($people)) : ?>
                            <?php foreach ($people as $p): ?>
                                <option value="<?= $p['id']; ?>">
                                    ID: <?= $p['id']; ?> - <?= $p['correo']; ?>
                                </option>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <option value="">No hay usuarios disponibles</option>
                        <?php endif; ?>
                    </select>
                    <br><br>

                    <label for="tipo_persona">Tipo:</label>
                    <select name="tipo_persona" id="tipo_persona" required>
                        <option value="usuario">Usuario</option>
                        <option value="proveedor">Proveedor</option>
                        <option value="contratista">Contratista</option>
                    </select>
                    <br><br>

                    <button type="submit">Borrar usuario</button><br><br>
                    <a href="dashboard2.html#usuario" class="boton-regreso">← Volver</a>
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
