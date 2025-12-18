<?php
require_once('../db_connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'] ?? '';
    $votos = $_POST['votos'] ?? []; // Array con [id_cargo => id_aspirante]

    if (!empty($token) && !empty($votos)) {
        // 1. Guardar cada voto en la tabla 'votos'
        foreach ($votos as $id_cargo => $id_aspirante) {
            $sql_voto = "INSERT INTO votos (id_aspirante_opcion) VALUES ($1)";
            pg_query_params($conn, $sql_voto, array($id_aspirante));
        }

        // 2. Marcar al participante como que ya votó
        $sql_update = "UPDATE participantes SET hasvoted = TRUE WHERE token = $1";
        pg_query_params($conn, $sql_update, array($token));

        echo "<h2>¡Gracias! Tu voto ha sido registrado con éxito.</h2>";
        echo "<a href='../index.html'>Volver al inicio</a>";
    }
}
pg_close($conn);
?>
