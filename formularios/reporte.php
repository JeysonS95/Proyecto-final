<?php
// 1. CONEXIÓN A LA BASE DE DATOS
// Salimos de 'formularios/' para buscar db_connect.php en la raíz
require_once('../db_connect.php'); 

// 2. CONSULTA SQL PARA CONTAR VOTOS
// Usamos LEFT JOIN para mostrar candidatos aunque tengan 0 votos
$sql = "SELECT 
            c.titulo AS cargo, 
            a.nombre_opcion AS candidato, 
            COUNT(v.id) AS total_votos
        FROM cargos_preguntas c
        JOIN aspirantes_opciones a ON c.id = a.id_cargo_pregunta
        LEFT JOIN votos v ON a.id = v.id_aspirante_opcion
        GROUP BY c.id, c.titulo, a.id, a.nombre_opcion
        ORDER BY c.titulo, total_votos DESC";

$result = pg_query($conn, $sql);

if (!$result) {
    die("Error en la consulta: " . pg_last_error($conn));
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Votaciones</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        table { width: 80%; margin: 20px auto; border-collapse: collapse; font-family: Arial, sans-serif; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #4CAF50; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        .cargo-header { background-color: #e2e2e2; font-weight: bold; }
        h1 { text-align: center; color: #333; }
    </style>
</head>
<body>

    <h1>📊 Resultados en Tiempo Real</h1>

    <table>
        <thead>
            <tr>
                <th>Candidato / Opción</th>
                <th>Votos Recibidos</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $cargo_actual = "";
            while ($row = pg_fetch_assoc($result)): 
                // Si cambiamos de cargo, imprimimos una fila separadora
                if ($cargo_actual != $row['cargo']):
                    $cargo_actual = $row['cargo'];
                    echo "<tr class='cargo-header'><td colspan='2'>" . htmlspecialchars($cargo_actual) . "</td></tr>";
                endif;
            ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['candidato']); ?></td>
                    <td><strong><?php echo $row['total_votos']; ?></strong></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <div style="text-align: center; margin-top: 30px;">
        <a href="../index.html" style="text-decoration: none; background: #333; color: white; padding: 10px 20px; border-radius: 5px;">Volver al Inicio</a>
    </div>

</body>
</html>
<?php pg_close($conn); ?>
