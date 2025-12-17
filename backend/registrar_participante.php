<?php
// backend/registrar_participante.php
// Incluye el archivo de conexión (está en la misma carpeta 'backend')
require_once('db_connect.php'); 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Recoger y limpiar los datos
    $correo = $conn->real_escape_string($_POST['correo']);
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $apellido = $conn->real_escape_string($_POST['apellido']);
    $campo1 = $conn->real_escape_string($_POST['campo1'] ?? ''); // Uso del operador null coalesce
    $campo2 = $conn->real_escape_string($_POST['campo2'] ?? '');
    $campo3 = $conn->real_escape_string($_POST['campo3'] ?? '');

    // 2. Verificar si el correo ya existe (Validación inicial)
    $check_sql = "SELECT correo FROM Participantes WHERE correo = '$correo'";
    $result = $conn->query($check_sql);

    if ($result->num_rows > 0) {
        echo "<h2>❌ Error: El correo '$correo' ya está registrado.</h2>";
    } else {
        // 3. Insertar el nuevo registro
        $insert_sql = "INSERT INTO Participantes (correo, nombre, apellido, campo1, campo2, campo3) 
                       VALUES ('$correo', '$nombre', '$apellido', '$campo1', '$campo2', '$campo3')";

        if ($conn->query($insert_sql) === TRUE) {
            echo "<h2>✅ Participante registrado exitosamente.</h2>";
        } else {
            echo "<h2>❌ Error al registrar: " . $conn->error . "</h2>";
        }
    }
    
    echo "<p>Regresar al <a href='../index.html'>Panel de Administración</a></p>";

} else {
    echo "Acceso no permitido. Usa el formulario de registro.";
}

$conn->close();
?>