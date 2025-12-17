<?php
// 1. RUTA CORREGIDA: El archivo está en la raíz, subimos un nivel.
require_once('../db_connect.php'); 

// 2. CONSULTA ADAPTADA A POSTGRESQL
// Consultamos los resultados de las votaciones
$sql = "SELECT c.titulo, a.nombre_opcion, COUNT(v.id) as total_votos 
        FROM cargos_preguntas c
        JOIN aspirantes_opciones a ON c.id = a.id_cargo_pregunta
        LEFT JOIN votos v ON a.id = v.id_aspirante_opcion
        GROUP BY c.titulo, a.nombre_opcion
        ORDER BY c.titulo, total_votos DESC";

$result = pg_query($conn, $sql);

if (!$result) {
    die("Error al generar el reporte: " . pg_last_error($conn));
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Resultados</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <h1>Resultados de la Encuesta</h1>
    <table border="1">
        <thead>
            <tr>
                <th>Cargo / Pregunta</th>
                <th>Candidato / Opción</th>
                <th>Total Votos</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = pg_fetch_assoc($result)): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['titulo']); ?></td>
                <td><?php echo htmlspecialchars($row['nombre_opcion']); ?></td>
                <td><?php echo $row['total_votos']; ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <p><a href="../index.html">Volver al Inicio</a></p>
</body>
</html>
<?php pg_close($conn); ?>
