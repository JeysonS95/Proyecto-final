<?php
// formularios/reporte.php
// Sube un nivel (..) y entra a backend/db_connect.php
require_once('../backend/db_connect.php'); 

// 1. Consulta para obtener el total general de votos (para calcular porcentajes)
$total_votos_query = $conn->query("SELECT COUNT(id) AS total FROM Votos");
$total_votos = $total_votos_query ? $total_votos_query->fetch_assoc()['total'] : 0;

// 2. Consulta principal: Contar votos por opción (Aspirante)
// Une Votos, Opciones y Preguntas para obtener nombres legibles.
$sql_reporte = "SELECT 
    CP.titulo AS cargo_titulo,
    AO.nombre_opcion AS aspirante, 
    COUNT(V.id) AS total_votos
FROM Votos V
JOIN Aspirantes_Opciones AO ON V.id_opcion_votada = AO.id
JOIN Cargos_Preguntas CP ON AO.id_cargo_pregunta = CP.id
GROUP BY CP.titulo, AO.nombre_opcion 
ORDER BY CP.titulo, total_votos DESC";

$reporte_result = $conn->query($sql_reporte);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados de la Encuesta</title>
    <link rel="stylesheet" href="../css/style.css"> 
    <style>
        /* Estilos básicos para el reporte visual */
        .barra-progreso {
            height: 30px;
            background-color: #e9ecef;
            border-radius: 4px;
            margin-top: 5px;
            margin-bottom: 15px;
            overflow: hidden;
        }
        .barra-votos {
            height: 100%;
            background-color: #007bff;
            color: white;
            line-height: 30px;
            text-align: right;
            padding-right: 5px;
            transition: width 0.5s;
        }
    </style>
</head>
<body>

    <header>
        <h1>📊 Resultados Oficiales de la Encuesta</h1>
    </header>

    <main>
        <h2>Total de Votos Emitidos: **<?php echo $total_votos; ?>**</h2>
        <p><a href="../index.html">← Volver al Panel</a></p>

        <?php
        $cargo_actual = '';
        if ($reporte_result && $reporte_result->num_rows > 0 && $total_votos > 0) {
            
            while($row = $reporte_result->fetch_assoc()) {
                
                // Muestra el título del cargo/pregunta si es diferente al anterior
                if ($row['cargo_titulo'] != $cargo_actual) {
                    echo '<hr><h3>CARRERA/PREGUNTA: ' . htmlspecialchars($row['cargo_titulo']) . '</h3>';
                    $cargo_actual = $row['cargo_titulo'];
                }
                
                // Cálculo del Porcentaje
                $porcentaje = ($row['total_votos'] / $total_votos) * 100;
                $porcentaje_formateado = number_format($porcentaje, 2);

                echo '<div>';
                echo '<h4>' . htmlspecialchars($row['aspirante']) . '</h4>';
                echo '<p>Votos: <strong>' . $row['total_votos'] . '</strong> | Porcentaje: ' . $porcentaje_formateado . '%</p>';
                
                // Barra de Progreso Visual
                echo '<div class="barra-progreso">';
                echo '<div class="barra-votos" style="width: ' . $porcentaje_formateado . '%">';
                echo $porcentaje_formateado . '%';
                echo '</div>';
                echo '</div>';
                echo '</div>';
            }
        } else {
            echo "<p>Aún no hay votos registrados o la configuración es incompleta.</p>";
        }
        
        $conn->close();
        ?>
    </main>
</body>
</html>