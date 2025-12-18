<?php
// Conexión saliendo de la carpeta votos/
require_once('../db_connect.php'); 

$token = $_GET['token'] ?? '';

// 1. Verificar si el usuario existe y no ha votado
$sql_user = "SELECT id, nombre, hasvoted FROM participantes WHERE token = $1";
$res_user = pg_query_params($conn, $sql_user, array($token));
$user = pg_fetch_assoc($res_user);

if (!$user) {
    die("Error: El enlace de votación no es válido o ha expirado.");
}

if ($user['hasvoted'] == 't' || $user['hasvoted'] == 1) {
    die("Hola " . htmlspecialchars($user['nombre']) . ", tu voto ya fue registrado anteriormente.");
}

// 2. Obtener cargos y sus respectivos candidatos
$sql_datos = "SELECT c.id AS cargo_id, c.titulo, a.id AS aspirante_id, a.nombre_opcion 
              FROM cargos_preguntas c 
              JOIN aspirantes_opciones a ON c.id = a.id_cargo_pregunta 
              ORDER BY c.id";
$res_datos = pg_query($conn, $sql_datos);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Encuesta</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <h1>Bienvenido, <?php echo htmlspecialchars($user['nombre']); ?></h1>
    <p>Por favor, selecciona una opción para cada cargo:</p>

    <form action="registrar_voto.php" method="POST">
        <input type="hidden" name="token" value="<?php echo $token; ?>">
        <input type="hidden" name="id_participante" value="<?php echo $user['id']; ?>">

        <?php 
        $cargo_actual = "";
        while ($row = pg_fetch_assoc($res_datos)): 
            if ($cargo_actual != $row['titulo']):
                $cargo_actual = $row['titulo'];
                echo "<h3>" . htmlspecialchars($cargo_actual) . "</h3>";
            endif;
        ?>
            <label style="display: block; margin-bottom: 10px;">
                <input type="radio" name="votos[<?php echo $row['cargo_id']; ?>]" value="<?php echo $row['aspirante_id']; ?>" required>
                <?php echo htmlspecialchars($row['nombre_opcion']); ?>
            </label>
        <?php endwhile; ?>

        <br><br>
        <button type="submit" style="padding: 10px 20px; cursor: pointer;">Finalizar Votación</button>
    </form>
</body>
</html>
<?php pg_close($conn); ?>
