<?php
// 1. CORRECCIÓN DE RUTA: Salimos de 'backend/' para buscar 'db_connect.php' en la raíz
require_once('../db_connect.php'); 

/**
 * NOTA: Para enviar correos reales desde Render, 
 * normalmente necesitarás un servicio como SendGrid, Mailgun o Gmail SMTP.
 */

// 2. CONSULTA ADAPTADA A POSTGRESQL
// Seleccionamos a los participantes que aún no han votado
$sql = "SELECT id, correo, nombre, token FROM participantes WHERE hasvoted = FALSE";
$result = pg_query($conn, $sql);

if (!$result) {
    die("Error al consultar participantes: " . pg_last_error($conn));
}

echo "<h2>Iniciando proceso de envío de correos...</h2>";

// 3. RECORRER RESULTADOS CON pg_fetch_assoc
if (pg_num_rows($result) > 0) {
    while ($row = pg_fetch_assoc($result)) {
        $email = $row['correo'];
        $nombre = $row['nombre'];
        $token = $row['token'];
        
        // Construimos el enlace de votación usando tu URL de Render
$enlace_votacion = "https://proyecto-final-indg.onrender.com/votos/encuesta.php?token=" . $token;
        // --- Lógica de envío (Simulación o PHPMailer) ---
        echo "Preparando correo para: " . htmlspecialchars($nombre) . " ($email)...<br>";
        echo "Enlace: <a href='$enlace_votacion'>Votar aquí</a><br><hr>";

        /* Ejemplo básico con la función mail() de PHP 
           (Requiere configuración de servidor de correo en el Dockerfile)
           
           $asunto = "Invitación a participar en la encuesta";
           $mensaje = "Hola $nombre, participa usando este enlace: $enlace_votacion";
           mail($email, $asunto, $mensaje);
        */
    }
    echo "<h3>Proceso completado.</h3>";
} else {
    echo "No hay participantes pendientes por votar o la tabla está vacía.";
}

// 4. CERRAR CONEXIÓN
pg_close($conn);
?>
