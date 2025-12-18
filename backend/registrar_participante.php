<?php
// 1. CORRECCIÓN DE RUTA
// Salimos de la carpeta 'backend' para encontrar el archivo en la raíz
require_once('../db_connect.php'); 

// 2. PROCESAMIENTO DE DATOS
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recogemos los datos del formulario (Asegúrate que los 'name' en tu HTML coincidan)
    $nombre    = $_POST['nombre'] ?? '';
    $apellido  = $_POST['apellido'] ?? '';
    $correo    = $_POST['correo'] ?? '';
    
    // Generamos un token único para que el usuario pueda votar después
    $token = bin2hex(random_bytes(16));

    // Validamos que los campos obligatorios no estén vacíos
    if (!empty($nombre) && !empty($correo)) {
        
        // 3. CONSULTA PARA POSTGRESQL (Usando parámetros $1, $2, etc. por seguridad)
        $sql = "INSERT INTO participantes (nombre, apellido, correo, token, hasvoted) 
                VALUES ($1, $2, $3, $4, FALSE)";
        
        $params = array($nombre, $apellido, $correo, $token);
        $result = pg_query_params($conn, $sql, $params);

        if ($result) {
            // Si funciona, regresamos al formulario con mensaje de éxito
            header("Location: ../formularios/registro_participantes.php?msg=Participante registrado correctamente");
            exit();
        } else {
            // Si falla (por ejemplo, si el correo ya existe)
            echo "Error al registrar: " . pg_last_error($conn);
        }
    } else {
        echo "Error: El nombre y el correo son obligatorios.";
    }
} else {
    // Si intentan entrar al archivo sin usar el formulario
    header("Location: ../formularios/registro_participantes.php");
    exit();
}

// 4. CERRAR CONEXIÓN
pg_close($conn);
?>
