<?php
// Formularios/registro_cargos.php
// Conexión para obtener los cargos para el desplegable.
require_once('../backend/db_connect.php'); 

// 1. Consulta para obtener los cargos dinámicamente
$sql_cargos = "SELECT id, titulo FROM Cargos_Preguntas ORDER BY id ASC";
$resultado_cargos = $conn->query($sql_cargos);

// 2. Cerramos la conexión después de usarla
$conn->close();

// 3. Obtener mensaje de éxito de la URL si existe
$success_message = '';
if (isset($_GET['msg'])) {
    $success_message = htmlspecialchars($_GET['msg']);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Cargos</title>
    <link rel="stylesheet" href="../css/style.css"> 
</head>
<body>

    <header>
        <h1>Registro de Cargos</h1>
        <p>Complete la información del cargo y sus opciones de respuesta (candidatos).</p>
    </header>

    <main>
        
        <?php if ($success_message): ?>
            <div style="background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 15px; margin-bottom: 20px;">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>

        <div class="panel-administracion">
            
            <h2>Información del Cargo/Pregunta</h2>
            
            <form action="../backend/registrar_cargo.php" method="POST">
                <div>
                    <label for="titulo">Título del Cargo / Pregunta *</label>
                    <input type="text" id="titulo" name="titulo" required placeholder="Ej: Presidente, Alcalde, Gobernador...">
                </div>
                <div>
                    <label for="descripcion">Descripción</label>
                    <textarea id="descripcion" name="descripcion" rows="3" placeholder="Describa el cargo o la pregunta de la encuesta..."></textarea>
                </div>
                <button type="submit" name="action" value="cargo" style="background-color: #17a2b8;">Registrar Solo el Cargo</button>
            </form>
            
            <hr>

            <h2>Registro de Candidatos / Opciones</h2>
            <p>Use este formulario para registrar las opciones asociadas a un Cargo ya existente.</p>
            
            <form action="../backend/registrar_cargo.php" method="POST">
                
                <div>
                    <label for="id_cargo_pregunta">Seleccionar Cargo al que pertenece la Opción *</label>
                    
                    <select id="id_cargo_pregunta" name="id_cargo_pregunta" required>
                        <option value="">-- Seleccione un Cargo/Pregunta --</option>
                        
                        <?php
                            // Generar las opciones del desplegable dinámicamente
                            if ($resultado_cargos && $resultado_cargos->num_rows > 0) {
                                while($fila = $resultado_cargos->fetch_assoc()) {
                                    // EL VALOR DEL OPTION ES EL ID REAL DE LA BD
                                    echo '<option value="' . $fila["id"] . '">' . htmlspecialchars($fila["titulo"]) . ' (ID: ' . $fila["id"] . ')</option>';
                                }
                            } else {
                                echo '<option value="" disabled>No hay cargos registrados. Registre uno primero.</option>';
                            }
                        ?>

                    </select>
                </div>

                <div>
                    <label for="nombre_opcion">Nombre del Candidato / Opción *</label>
                    <input type="text" id="nombre_opcion" name="nombre_opcion" required placeholder="Ej: Frandy Jeffry Cepeda">
                </div>
                
                <button type="submit" name="action" value="opcion">Guardar Opción/Candidato</button>
            </form>

        </div>
        <p><a href="../index.html">← Volver al Panel de Administración</a></p>
    </main>
</body>
</html>