<?php

define('DB_HOST', 'localhost');       // En AwardSpace suele ser 'mysql8.awardspace.info'
define('DB_USER', 'tu_usuario_db');   // Usuario que asigna AwardSpace
define('DB_PASS', 'tu_password_db');  // Contraseña de la BD
define('DB_NAME', 'tu_nombre_bd');    // Nombre de la base de datos

date_default_timezone_set('America/Mazatlan');

function getDB(): mysqli {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        http_response_code(500);
        die(json_encode(['error' => 'Error de conexión a la base de datos.']));
    }
    $conn->set_charset('utf8mb4');
    return $conn;
}

function setCORSHeaders(): void {
    header('Content-Type: application/json; charset=utf-8');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit(0); }
}

function jsonResponse(bool $ok, $data = null, string $msg = '', int $code = 200): void {
    http_response_code($code);
    echo json_encode(['ok' => $ok, 'msg' => $msg, 'data' => $data], JSON_UNESCAPED_UNICODE);
    exit;
}
?>
