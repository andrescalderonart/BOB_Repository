<?php
require_once 'conexion.php';
require_once 'clases.php';

// Obtener la lista de personas
try {
    $conexion = Conexion1::conectar();
    $sql = "SELECT id, correo, contrasenna FROM persona"; // Consulta para obtener vehículos
    $stmt = $conexion->prepare($sql);
    $stmt->execute();
    $persona = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    echo "Error al obtener los usuarios: " . $e->getMessage();
}

// Procesar el formulario enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $Correo = $_POST['correo'] ?? null;
    $Contraseña = $_POST['contrasenna'] ?? null;
    if (!preg_match('/[a-z]/', $this->contrasenna) || !preg_match('/[A-Z]/', $this->contrasenna)) {
        return "La contraseña debe incluir mayúsculas y minúsculas.";
    }
    // Validar la presencia de al menos un carácter especial
        if (!preg_match('/[\W_]/', $this->contrasenna)) {
        return "La contraseña debe incluir al menos un carácter especial (ej. !, @, #, $, etc.).";
    }

    $Tipo = $_POST['tipo_persona'] ?? null;
    $Nombre = $_POST['nombre'] ?? null;
    $Apellido = $_POST['apellido'] ?? null;
    $Telefono = $_POST['telefono'] ?? null;

    // Validar campos obligatorios
    if (empty($id) || empty($Correo) || empty($Contraseña)) {
        die("Todos los campos son obligatorios.");
    }

    try {
        // Crear la conexión
        $conexion = Conexion1::conectar();
        $Tipo = $_POST['tipo_persona'] ?? null;

        // Instanciar la clase correspondiente al tipo de vehículo
        switch ($Tipo) {
            case 'usuario':
                $persona = new Usuario($Nombre, $Apellido, $Cedula, $Correo, $Direccion, $Rol, $Telefono, $Contraseña, $Tipo);
                break;
            case 'proveedor':
                $persona = new Proveedor($Nombre, $Apellido, $Cedula, $Correo, $Direccion, $Rol, $Telefono, $Contraseña, $Tipo);
                break;
            case 'contratista':
                $persona = new Contratista($Nombre, $Apellido, $Cedula, $Correo, $Direccion, $Rol, $Telefono, $Contraseña, $Tipo);
                break;
            default:
                throw new Exception("Tipo de usuario no válido.");
        }

        // Llamar al método para actualizar el usuario
        $persona->ActualizarPersona($conexion, $id);
        echo "¡Personal actualizado exitosamente!";
    } catch (Exception $e) {
        echo "Error al actualizar: " . $e->getMessage();
    }
}
?>

<?php
require_once 'conexion.php';
require_once 'clases.php';

// Obtener la lista de personas
try {
    $conexion = Conexion1::conectar();
    $sql = "SELECT id, correo FROM persona";
    $stmt = $conexion->prepare($sql);
    $stmt->execute();
    $persona = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    echo "Error al obtener los usuarios: " . $e->getMessage();
}

// Procesar el formulario enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $Correo = $_POST['correo'] ?? null;
    $Contraseña = $_POST['contrasenna'] ?? null;
    $Tipo = $_POST['tipo_persona'] ?? null;
    $Nombre = $_POST['nombre'] ?? null;
    $Apellido = $_POST['apellido'] ?? null;
    $Telefono = $_POST['telefono'] ?? null;
    $Direccion = $_POST['direccion'] ?? null;
    $Rol = $_POST['rol'] ?? null;

    if (empty($id) || empty($Correo) || empty($Contraseña)) {
        die("Todos los campos son obligatorios.");
    }

    try {
        if (!preg_match('/[a-z]/', $Contraseña) || !preg_match('/[A-Z]/', $Contraseña)) {
            throw new Exception("La contraseña debe incluir mayúsculas y minúsculas.");
        }
        if (!preg_match('/[\W_]/', $Contraseña)) {
            throw new Exception("La contraseña debe incluir al menos un carácter especial.");
        }

        $conexion = Conexion1::conectar();

        switch ($Tipo) {
            case 'usuario':
                $personaObj = new Usuario($Nombre, $Apellido, null, $Correo, $Direccion, $Rol, $Telefono, $Contraseña, $Tipo);
                break;
            case 'proveedor':
                $personaObj = new Proveedor($Nombre, $Apellido, null, $Correo, $Direccion, $Rol, $Telefono, $Contraseña, $Tipo);
                break;
            case 'contratista':
                $personaObj = new Contratista($Nombre, $Apellido, null, $Correo, $Direccion, $Rol, $Telefono, $Contraseña, $Tipo);
                break;
            default:
                throw new Exception("Tipo de usuario no válido.");
        }

        $personaObj->ActualizarPersona($conexion, $id);
        echo "<p style='color:green; text-align:center;'>¡Usuario actualizado exitosamente!</p>";
    } catch (Exception $e) {
        echo "<p style='color:red; text-align:center;'>Error: " . $e->getMessage() . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Actualizar Usuario</title>
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
    <div class="content-usuario">
        <div class="formulario-act-usuario">
            <div class="diseño-for-act-usuario">
                <h3>Actualizar Usuario</h3>
                <form method="post" action="actualizar.php">
                    <label for="id">Selecciona usuario:</label>
                    <select name="id" id="id" required>
                        <option value="">--Selecciona--</option>
                        <?php foreach ($persona as $p): ?>
                            <option value="<?= $p['id']; ?>">ID: <?= $p['id']; ?> - <?= $p['correo']; ?></option>
                        <?php endforeach; ?>
                    </select><br><br>

                    <label for="correo">Nuevo Correo:</label>
                    <input type="text" name="correo" id="correo" required><br><br>

                    <label for="contrasenna">Nueva Contraseña:</label>
                    <input type="text" name="contrasenna" id="contrasenna" required><br><br>

                    <label for="tipo_persona">Nuevo Tipo:</label>
                    <select id="tipo_persona" name="tipo_persona" required>
                        <option value="proveedor">Proveedor</option>
                        <option value="usuario">Usuario</option>
                        <option value="contratista">Contratista</option>
                    </select><br><br>

                    <label for="nombre">Nuevo nombre:</label>
                    <input type="text" name="nombre" id="nombre" required><br><br>

                    <label for="apellido">Nuevo apellido:</label>
                    <input type="text" name="apellido" id="apellido" required><br><br>

                    <label for="telefono">Nuevo teléfono:</label>
                    <input type="text" name="telefono" id="telefono" required><br><br>

                    <div id="rol-container" style="display: none;">
                        <label for="rol">Rol:</label>
                        <select id="rol" name="rol">
                            <option value="operativo">Operativo</option>
                            <option value="supervisor">Supervisor</option>
                            <option value="director">Directivo</option>
                        </select><br><br>

                        <label for="direccion">Nueva dirección:</label>
                        <input type="text" name="direccion" id="direccion" required><br><br>
                    </div>

                    <script>
                        function toggleRolDropdown() {
                            const tipoPersona = document.getElementById('tipo_persona').value;
                            const rolContainer = document.getElementById('rol-container');
                            const rolSelect = document.getElementById('rol');

                            if (tipoPersona === 'usuario') {
                                rolContainer.style.display = 'block';
                                rolSelect.required = true;
                            } else {
                                rolContainer.style.display = 'none';
                                rolSelect.required = false;
                                rolSelect.value = '';
                            }
                        }

                        document.addEventListener('DOMContentLoaded', toggleRolDropdown);
                        document.getElementById('tipo_persona').addEventListener('change', toggleRolDropdown);
                    </script>

                    <button type="submit">Actualizar</button><br><br>
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
