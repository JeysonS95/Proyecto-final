<?php
// votos/encuesta.php
require_once('../backend/db_connect.php'); 

// 1. Obtener el token de la URL
$token = $conn->real_escape_string($_GET['token'] ?? '');

// 2. Validar el Token y el Estado de Voto
$check_sql = "SELECT correo, hasVoted FROM Participantes WHERE token = '$token'";
$result = $conn->query($check_sql);

if ($result->num_rows == 0) {
    // Falla si el token no existe
    die("<h2>❌ Error: Token de votación inválido o expirado.</h2>");
}

$participante = $result->fetch_assoc();
$correo_participante = $participante['correo'];

if ($participante['hasVoted']) {
    // Requisito C: Falla si ya ha votado
    die("<h2>❌ Error: Este correo ya ha emitido su voto.</h2>"); 
}

// 3. Si es válido, cargar las opciones de la encuesta
// Se incluye CP.id (id_cargo_pregunta) en la consulta para agrupar los votos.
$opciones_sql = "SELECT CP.id AS id_cargo_pregunta, CP.titulo AS cargo_titulo, AO.id AS opcion_id, AO.nombre_opcion 
                 FROM Aspirantes_Opciones AO
                 JOIN Cargos_Preguntas CP ON AO.id_cargo_pregunta = CP.id
                 ORDER BY CP.id, AO.id";
$opciones_result = $conn->query($opciones_sql);

// No cerramos la conexión aquí para poder usar $opciones_result en el HTML.
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Voto</title>
    <link rel="stylesheet" href="../css/style.css"> 
</head>
<body>

    <header>
        <h1>Votación de la Encuesta</h1>
        <h2>Por favor, selecciona una opción para cada cargo/pregunta.</h2>
    </header>

    <main>
        <form action="../backend/procesar_voto.php" method="POST">
            
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

            <?php
            $cargo_actual = '';
            if ($opciones_result->num_rows > 0) {
                while($row = $opciones_result->fetch_assoc()) {
                    
                    // Si el cargo/pregunta es diferente, mostramos su título
                    if ($row['cargo_titulo'] != $cargo_actual) {
                        echo '<h3>' . htmlspecialchars($row['cargo_titulo']) . '</h3>';
                        $cargo_actual = $row['cargo_titulo'];
                    }
                    
                    // Muestra cada opción como un radio button.
                    echo '<div>';
                    // IMPORTANTE: El 'name' ahora es único para cada cargo/pregunta (voto_cargo_ID)
                    // Esto permite seleccionar una opción por cada pregunta.
                    echo '<input type="radio" id="opcion_' . $row['opcion_id'] . '" name="voto_cargo_' . $row['id_cargo_pregunta'] . '" value="' . $row['opcion_id'] . '" required>';
                    echo '<label for="opcion_' . $row['opcion_id'] . '">' . htmlspecialchars($row['nombre_opcion']) . '</label>';
                    echo '</div>';
                }
            } else {
                 echo "<p>No hay opciones configuradas para esta encuesta. Contacte al administrador.</p>";
            }
            $conn->close(); // Cerramos la conexión aquí
            ?>
            
            <button type="submit">Registrar Mis Votos</button>
        </form>
    </main>

</body>
</html>