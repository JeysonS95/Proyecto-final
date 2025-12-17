<?php
// backend/registrar_cargo.php
require_once('db_connect.php'); 

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    
    $action = $_POST['action'];
    $response = "";
    
    // --- LÓGICA PARA REGISTRAR CARGO/PREGUNTA (Parte A) ---
    if ($action == 'cargo' && isset($_POST['titulo'])) {
        $titulo = $conn->real_escape_string($_POST['titulo']);
        $descripcion = $conn->real_escape_string($_POST['descripcion'] ?? '');
        
        $sql = "INSERT INTO Cargos_Preguntas (titulo, descripcion) 
                VALUES ('$titulo', '$descripcion')";
        
        if ($conn->query($sql) === TRUE) {
            // REDIRECCIÓN EN CASO DE ÉXITO (Solución UX)
            $msg = urlencode("Cargo '{$titulo}' registrado exitosamente. Ahora regístrele opciones.");
            header("Location: ../formularios/registro_cargos.php?msg=" . $msg);
            exit(); 
        } else {
            $response = "Error al registrar Cargo/Pregunta: " . $conn->error;
        }
    } 
    
    // --- LÓGICA PARA REGISTRAR OPCIÓN/ASPIRANTE (Parte B) ---
    elseif ($action == 'opcion' && isset($_POST['nombre_opcion']) && isset($_POST['id_cargo_pregunta'])) {
        $nombre_opcion = $conn->real_escape_string($_POST['nombre_opcion']);
        $id_cargo = (int)$_POST['id_cargo_pregunta'];
        
        // Esta es la línea 33 que ahora funciona gracias al fix de la BD:
        $sql = "INSERT INTO Aspirantes_Opciones (id_cargo_pregunta, nombre_opcion) 
                VALUES ($id_cargo, '$nombre_opcion')";
        
        if ($conn->query($sql) === TRUE) {
            // REDIRECCIÓN EN CASO DE ÉXITO
            $msg = urlencode("Opción '{$nombre_opcion}' registrada exitosamente.");
            header("Location: ../formularios/registro_cargos.php?msg=" . $msg); 
            exit();
        } else {
            $response = "Error al registrar Opción/Aspirante: " . $conn->error; 
        }
    } else {
        $response = "Solicitud inválida o faltan datos.";
    }

    // Muestra el mensaje de error si no se pudo redireccionar
    if (isset($response)) {
        echo "<h2>Error de Procesamiento</h2>";
        echo $response;
        echo "<p>Regresar al <a href='../index.html'>Panel de Administración</a></p>";
    }
} else {
    echo "Acceso no permitido. Usa el formulario de registro.";
}

$conn->close();
?>