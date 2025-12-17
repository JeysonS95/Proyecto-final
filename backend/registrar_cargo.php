<?php
// backend/registrar_cargo.php

// 1. CORRECCIÓN DE RUTA: Subimos un nivel para encontrar db_connect.php en la raíz
require_once('../db_connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'cargo') {
        // --- REGISTRAR SOLO EL CARGO ---
        $titulo = $_POST['titulo'] ?? '';
        $descripcion = $_POST['descripcion'] ?? '';

        if (!empty($titulo)) {
            // En PostgreSQL usamos $1, $2 para evitar inyecciones SQL con pg_query_params
            $sql = "INSERT INTO cargos_preguntas (titulo, descripcion) VALUES ($1, $2)";
            $stmt = pg_query_params($conn, $sql, array($titulo, $descripcion));

            if ($stmt) {
                header("Location: ../formularios/registro_cargos.php?msg=Cargo registrado exitosamente");
            } else {
                echo "Error al registrar el cargo: " . pg_last_error($conn);
            }
        }

    } elseif ($action === 'opcion') {
        // --- REGISTRAR CANDIDATO / OPCIÓN ---
        $id_cargo_pregunta = $_POST['id_cargo_pregunta'] ?? '';
        $nombre_opcion = $_POST['nombre_opcion'] ?? '';

        if (!empty($id_cargo_pregunta) && !empty($nombre_opcion)) {
            $sql = "INSERT INTO aspirantes_opciones (id_cargo_pregunta, nombre_opcion) VALUES ($1, $2)";
            $stmt = pg_query_params($conn, $sql, array($id_cargo_pregunta, $nombre_opcion));

            if ($stmt) {
                header("Location: ../formularios/registro_cargos.php?msg=Candidato/Opción guardada correctamente");
            } else {
                echo "Error al registrar la opción: " . pg_last_error($conn);
            }
        }
    }
}

// Cerramos la conexión
pg_close($conn);
?>
