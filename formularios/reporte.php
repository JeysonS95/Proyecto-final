<?php
// 1. CONEXIÓN A LA BASE DE DATOS
require_once('../db_connect.php');

// 2. CONSULTA SQL PARA RESULTADOS
$sql = "SELECT 
            a.nombre_opcion AS candidato, 
            COUNT(v.id) AS total_votos
        FROM aspirantes_opciones a
        LEFT JOIN votos v ON a.id = v.id_aspirante_opcion
        GROUP BY a.id, a.nombre_opcion
        ORDER BY total_votos DESC";

$result = pg_query($conn, $sql);

$nombres = [];
$votos = [];

while ($row = pg_fetch_assoc($result)) {
    $nombres[] = $row['candidato'];
    $votos[] = (int)$row['total_votos'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Resultados</title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .container { width: 80%; margin: auto; text-align: center; }
        canvas { max-width: 600px; margin: 20px auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; }
        th { background-color: #4CAF50; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📊 Resultados de la Encuesta</h1>

        <canvas id="graficoVotos"></canvas>

        <table>
            <tr>
                <th>Candidato</th>
                <th>Total Votos</th>
            </tr>
            <?php for ($i = 0; $i < count($nombres); $i++): ?>
            <tr>
                <td><?php echo htmlspecialchars($nombres[$i]); ?></td>
                <td><?php echo $votos[$i]; ?></td>
            </tr>
            <?php endfor; ?>
        </table>

        <br>
        <a href="../index.html" class="btn">Volver al Inicio</a>
    </div>

    <script>
        // Configuración del Gráfico
        const ctx = document.getElementById('graficoVotos').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($nombres); ?>,
                datasets: [{
                    label: 'Cantidad de Votos',
                    data: <?php echo json_encode($votos); ?>,
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(255, 99, 132, 0.7)',
                        'rgba(75, 192, 192, 0.7)',
                        'rgba(255, 206, 86, 0.7)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } }
                }
            }
        });
    </script>
</body>
</html>
<?php pg_close($conn); ?>
