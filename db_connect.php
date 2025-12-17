<?php
$host = getenv('DB_HOST');
$port = getenv('DB_PORT');
$dbname = getenv('DB_NAME');
$user = getenv('DB_USER');
$password = getenv('DB_PASSWORD');

// Conexión a PostgreSQL
$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

if (!$conn) {
    echo "Error de conexión.";
    exit;
}
?>