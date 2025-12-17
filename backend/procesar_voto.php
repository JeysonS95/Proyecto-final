<?php
// backend/procesar_voto.php
require_once('db_connect.php'); 

// Ya no dependemos de un solo campo 'opcion_votada'
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['token'])) {
    
    $token = $conn->real_escape_string($_POST['token']);

    // 1. Re-validación del Token y obtención de datos del participante
    // Aseguramos que el token sea válido y que el usuario NO haya votado aún.
    $check_sql = "SELECT id, correo FROM Participantes WHERE token = '$token' AND hasVoted = FALSE";
    $result = $conn->query($check_sql);

    if ($result->num_rows == 0) {
        // Fallará si el token es inválido o si ya ha votado (porque hasVoted=TRUE)
        die("<h2>❌ Error: Voto inválido o ya registrado.</h2>");
    }

    $participante = $result->fetch_assoc();
    $participante_id = $participante['id'];
    $correo_participante = $participante['correo'];
    
    $votos_registrados = 0;
    $voto_fallido = false;

    // 2. ITERAR sobre todos los campos POST para buscar los votos individuales
    foreach ($_POST as $key => $value) {
        
        // Buscamos campos que comiencen con 'voto_cargo_' (nombres creados en encuesta.php)
        if (strpos($key, 'voto_cargo_') === 0) {
            
            $id_opcion_votada = (int)$value;
            
            // Insertar Voto en la tabla Votos
            $insert_sql = "INSERT INTO Votos (correo_participante, id_opcion_votada) 
                           VALUES ('$correo_participante', $id_opcion_votada)";

            if ($conn->query($insert_sql) === TRUE) {
                $votos_registrados++;
            } else {
                $voto_fallido = true;
                // Si falla un voto, registramos el error, pero continuamos con los demás
            }
        }
    }

    if ($votos_registrados > 0 && !$voto_fallido) {
        
        // 3. Marcar al Participante como Votado (Garantía de Voto Único - Requisito C)
        // Solo marcamos como votado si se registró al menos un voto sin fallos graves.
        $update_voted_sql = "UPDATE Participantes SET hasVoted = TRUE WHERE id = $participante_id";
        $conn->query($update_voted_sql); 

        echo "<h2>🎉 ¡Votos Registrados Exitosamente!</h2>";
        echo "<p>Se registraron **{$votos_registrados}** votos para los diferentes cargos/preguntas.</p>";
        echo "<p>Gracias por participar en la encuesta.</p>";

    } elseif ($voto_fallido) {
        echo "<h2>⚠️ Error parcial al registrar los votos.</h2><p>Contacte al administrador si el error persiste.</p>";
    } else {
         echo "<h2>❌ Error: No se seleccionó ninguna opción de voto.</h2>";
    }

} else {
    echo "<h2>❌ Acceso no permitido o datos de votación faltantes.</h2>";
}

$conn->close();
?>