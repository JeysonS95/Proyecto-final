<?php
// 1. CONEXIÓN A LA BASE DE DATOS
// Usamos ../ porque el archivo está en la raíz del proyecto
require_once('../db_connect.php'); 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $nombre    = $_POST['nombre'] ?? '';
    $apellido  = $_POST['apellido'] ?? '';
    $correo    = $_POST['correo'] ?? '';
    
    // Generamos un TOKEN único de seguridad
    $token = bin2hex(random_bytes(16));

    if (!empty($nombre) && !empty($correo)) {
        
        // 2. CONSULTA SQL PARA POSTGRESQL
        $sql = "INSERT INTO participantes (nombre, apellido, correo, token, hasvoted) 
                VALUES ($1, $2, $3, $4, FALSE)";
        
        $params = array($nombre, $apellido, $correo, $token);
        $result = pg_query_params($conn, $sql, $params);

        if ($result) {
            // REDIRECCIÓN CORREGIDA SEGÚN TU GITHUB
            // Cambiamos a registro_participante.html que es el que tienes en formularios/
            header("Location: ../formularios/registro_participante.html?msg=exito");
            exit();
        } else {
            $error = pg_last_error($conn);
            // Manejo amigable de correos duplicados
            if (strpos($error, 'duplicate key') !== false) {
                header("Location: ../formularios/registro_participante.html?msg=error_duplicado");
            } else {
                echo "Error al registrar en la base de datos: " . $error;
            }
            exit();
        }
    } else {
        echo "Error: El nombre y el correo son obligatorios.";
    }
} else {
    header("Location: ../formularios/registro_participante.html");
    exit();
}

pg_close($conn);
?>
