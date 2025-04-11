<?php
include 'conexion_INV.php';

// Crear instancia de la conexión utilizando la clase Conexion1
$conexion = Conexion1::conectar();

//Captura de filtros desde el formulario

$filtroobra = isset($_POST['obra']) ? $_POST['obra'] :'';
$filtrotipo = isset($_POST['tipo']) ? $_POST['tipo'] :'';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo = $_POST['tipo'] ??'';
}

//Funcion para crear filtros dinamicos.
function generarConsulta($tabla, $obra, $tipo){

// Consultas para las distintas tablas
$sql = "SELECT lm.usuario, lm.obra, lm.tipo, $tabla.fecha, $tabla.cantidad, $tabla.precio, ($tabla.cantidad * $tabla.precio) AS costoTotal
            FROM $tabla
            JOIN lista_materiales lm ON $tabla.id = lm.id";
            $condiciones = [];
            if (!empty($obra)) {
                $condiciones[] = "lm.obra = :obra";
            }
            if (!empty($tipo)) {
                $condiciones[] = "lm.tipo = :tipo";
            }
            if (!empty($condiciones)) {
                $sql .= " WHERE " . implode(" AND ", $condiciones);
            }

            return $sql;
        }

    //Consultas dinamicas
    $sqlIngreso = generarConsulta ('ingreso', $filtroobra, $filtrotipo);
    $sqlConsumo = generarConsulta ('consumo', $filtroobra, $filtrotipo);
    $sqlPresupuesto = generarConsulta ('presupuesto', $filtroobra, $filtrotipo);

    // Generar el reporte
echo "<h1>Reporte de Costos por registro</h1>";

// Función para generar tablas
function generarTabla($conexion, $sql, $titulo, $obra, $tipo) {
    echo "<h2>$titulo</h2>";
    $stmt = $conexion->prepare($sql);

    if (!empty($obra)) {
        $stmt->bindParam(":obra", $obra, PDO::PARAM_STR);
    }
    if (!empty($tipo)) {
        $stmt->bindParam(":tipo", $tipo, PDO::PARAM_STR);
    }
    $stmt->execute();
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);


    if (!empty($resultados)) {
        echo "<table border='1'>
                <tr>
                <th>Usuario</th>
                <th>Obra</th>
                <th>Cantidad</th>
                <th>Precio unitario</th>
                <th>Costo Total</th>
                </tr>";
        foreach ($resultados as $row) {
            echo "<tr>
                    <td>{$row['usuario']}</td>
                    <td>{$row['obra']}</td>
                    <td>{$row['cantidad']}</td>
                    <td>{$row['precio']}</td>
                    <td>{$row['costoTotal']}</td>
                </tr>";
        }
        echo "</table>";
    } else {
        echo "No hay datos disponibles.";
        }
    }

// Mostrar filtros
echo "<form method='POST'>
        <label for='correo'>Nombre de la obra:</label>
        <input type='text' id='obra' name='obra'>
        <button type='submit'>Filtrar</button>
    </form>";
echo "<form method='POST'>
    <label for='correo'>Tipo de registro:</label>
    <input type='text' id='tipo' name='tipo'>
    <button type='submit'>Filtrar</button>
</form>";

// Mostrar tablas
generarTabla($conexion, $sqlIngreso, "Ingresos", $filtroobra, $filtrotipo);
generarTabla($conexion, $sqlConsumo, "Consumo", $filtroobra, $filtrotipo);
generarTabla($conexion, $sqlPresupuesto, "Presupuesto", $filtroobra, $filtrotipo);

?>

<!DOCTYPE html>
<a href="#inventario">Volver al inicio</a>
</html>