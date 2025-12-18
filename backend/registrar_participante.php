<?php
// 1. CONEXIÓN A LA BASE DE DATOS
// Usamos ../ para salir de 'backend/' y encontrar db_connect.php en la raíz
require_once('../db_connect.php'); 

// 2. VERIFICACIÓN DE ENVÍO DE FORMULARIO
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Capturamos los datos enviados por el método POST
    // Usamos el operador ?? '' para evitar errores si el campo viene vacío
    $nombre    = $_POST['nombre'] ?? '';
    $apellido  = $_POST['apellido'] ?? '';
    $correo    = $_POST['correo'] ?? '';
    
    // Generamos un TOKEN único de seguridad para este votante
    // Esto evita que alguien vote dos veces o que voten sin estar registrados
    $token = bin2hex(random_bytes(16));

    // Validamos que los campos esenciales no estén vacíos
    if (!empty($nombre) && !empty($correo)) {
        
        // 3. CONSULTA SQL PARA POSTGRESQL
        // Usamos $1, $2, $3, $4 para prevenir Inyección SQL
        $sql = "INSERT INTO participantes (nombre, apellido, correo, token, hasvoted) 
                VALUES ($1, $2, $3, $4, FALSE)";
        
        // Ejecutamos la consulta con los parámetros
        $params = array($nombre, $apellido, $correo, $token);
        $result = pg_query_params($conn, $sql, $params);

        if ($result) {
            // Si todo sale bien, redirigimos de nuevo al formulario con un mensaje de éxito
            header("Location: ../formularios/registro_participantes.php?msg=exito");
            exit();
        } else {
            // Si hay un error en la base de datos (ej: correo duplicado)
            echo "Error al registrar en la base de datos: " . pg_last_error($conn);
        }
    } else {
        echo "Error: El nombre y el correo son campos obligatorios.";
    }
} else {
    // Si alguien intenta entrar a este archivo sin enviar el formulario
    header("Location: ../formularios/registro_participantes.php");
    exit();
}

// 4. CERRAR CONEXIÓN
pg_close($conn);
?>
