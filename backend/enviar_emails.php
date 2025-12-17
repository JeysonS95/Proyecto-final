<?php
// backend/enviar_emails.php
require_once('db_connect.php'); 

// Función para generar un token único (cadena aleatoria segura)
function generateToken($length = 32) {
    // Genera una cadena hexadecimal aleatoria de 32 caracteres
    return bin2hex(random_bytes($length / 2));
}

// 1. Consultar Participantes que aún no han votado (hasVoted = FALSE)
$sql = "SELECT id, correo FROM Participantes WHERE hasVoted = FALSE";
$result = $conn->query($sql);

$emails_enviados = 0;

echo "<h1>📧 Proceso de Envío de Encuestas</h1>";

if ($result->num_rows > 0) {
    
    // 2. Procesar cada participante
    while($row = $result->fetch_assoc()) {
        $participante_id = $row['id'];
        $correo_destino = $row['correo'];
        
        // Generar el Token Único (Requisito b)
        $token = generateToken();
        
        // 3. Actualizar la base de datos con el token
        // Esto asocia la URL única al registro del participante
        $update_sql = "UPDATE Participantes SET token = '$token' WHERE id = $participante_id";
        
        if ($conn->query($update_sql) === TRUE) {
            
            // 4. Construir la URL de votación (Requisito b)
            // La URL usa el token como identificador seguro
            $url_encuesta = "http://localhost/plataforma_encuestas/votos/encuesta.php?token=" . $token;
            
            // 5. SIMULACIÓN DE ENVÍO (Mostrar la URL)
            echo "<p>✅ Email simulado enviado a: <strong>$correo_destino</strong>";
            echo " | URL Única: <a href='$url_encuesta'>$url_encuesta</a></p>";

            $emails_enviados++;
            
        } else {
            echo "<p style='color:red;'>⚠️ Error al guardar token para $correo_destino: " . $conn->error . "</p>";
        }
    }
    echo "<h3>Resumen: $emails_enviados participantes listos para votar.</h3>";

} else {
    echo "<h2>ℹ️ No hay participantes pendientes de recibir la encuesta.</h2>";
}

echo "<p><a href='../index.html'>← Regresar al Panel</a></p>";
$conn->close();
?>