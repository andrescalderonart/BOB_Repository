<?php
include 'conexion.php';

// Crear instancia de la conexión utilizando la clase Conexion1
$conexion = Conexion1::conectar();

// Capturar filtros desde el formulario
$filtroCorreo = isset($_POST['correo']) ? $_POST['correo'] : '';
//$filtroTipo = isset($_POST['tipo_persona']) ? $_POST['tipo_persona'] : '';

//Función para saber qué filas pedir
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
$Tipo = $_POST['tipo_persona'] ?? '';// Este churumbelo necesita esos simbolos al final
}

// Función para crear filtros dinámicos
function generarConsulta($tabla, $correo, $Tipo) {
    $tipo_per = $Tipo;
    if($tipo_per=='usuario'){

        //Si es un usuario
        $sql = "SELECT p.correo, p.tipo_persona, $tabla.nombre, $tabla.apellido, $tabla.direccion, $tabla.telefono, $tabla.rol
                FROM $tabla
                JOIN persona p ON $tabla.id = p.id";
        $condiciones = [];
        if (!empty($correo)) {
            $condiciones[] = "p.correo = :correo";
        }
        if (!empty($condiciones)) {
            $sql .= " WHERE " . implode(" AND ", $condiciones);
        }
        return $sql;

        //Si es otra persona
    }else{
        $sql = "SELECT p.correo, p.tipo_persona, $tabla.nombre, $tabla.apellido, $tabla.telefono
                FROM $tabla
                JOIN persona p ON $tabla.id = p.id";
        $condiciones = [];
        if (!empty($correo)) {
            $condiciones[] = "p.correo = :correo";
        }
        if (!empty($condiciones)) {
            $sql .= " WHERE " . implode(" AND ", $condiciones);
        }
        return $sql;
    }
}

// Consultas dinámicas
$sqlUsuario = generarConsulta('usuario', $filtroCorreo, 'usuario');
$sqlProveedor = generarConsulta('proveedor', $filtroCorreo, 'proveedor');
$sqlContratista = generarConsulta('contratista', $filtroCorreo, 'contratista');

// Generar el reporte
echo "<h1>Reporte de personal asociado</h1>";

function generarTabla($conexion, $sql, $titulo, $correo) {
    echo "<h2>$titulo</h2>";
    $stmt = $conexion->prepare($sql);

    if (!empty($correo)) {
        $stmt->bindParam(':correo', $correo, PDO::PARAM_STR);
    }

    $stmt->execute();
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($resultados)) {
        echo "<table border='1'>
                <tr><th>Correo</th><th>Nombre</th><th>Apellido</th><th>Telefono</th><th>Dirección</th><th>Rol</th></tr>";//titulos de las columnas
        foreach ($resultados as $row) {
            echo "<tr> 
                    <td>{$row['correo']}</td>
                    <td>{$row['nombre']}</td>
                    <td>{$row['apellido']}</td>
                    <td>{$row['direccion']}</td>
                    <td>{$row['telefono']}</td>
                    <td>{$row['rol']}</td>
                </tr>";//las columnas en si
        }
        echo "</table>";
    } else {
        echo "No hay datos disponibles.";
    }
}

// Mostrar filtros
echo "<form method='POST'>
        <label for='correo'>Correo electrónico:</label>
        <input type='text' id='correo' name='correo'>
        <button type='submit'>Filtrar</button>
    </form>";

// Mostrar tablas con filtros
generarTabla($conexion, $sqlUsuario, "Usuarios", $filtroCorreo);
generarTabla($conexion, $sqlProveedor, "Proveedores", $filtroCorreo);
generarTabla($conexion, $sqlContratista, "Contratistas", $filtroCorreo);
?>

<!DOCTYPE html>
<a href="#usuarios">Volver al inicio</a>
</html>