<?php
require 'config.php';
setCORSHeaders();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, null, 'Método no permitido.', 405);
}

$conn = getDB();
$data = json_decode(file_get_contents('php://input'), true) ?? [];

if (($data['action'] ?? '') !== 'login') {
    jsonResponse(false, null, 'Acción no válida.', 400);
}

$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

if ($email === '' || $password === '') {
    jsonResponse(false, null, 'Completa correo y contraseña.', 400);
}

// Esta versión usa la tabla que tienes en phpMyAdmin: users
$stmt = $conn->prepare('SELECT id, name, email, password FROM users WHERE email = ? LIMIT 1');
if (!$stmt) {
    jsonResponse(false, null, 'Error en consulta SQL: ' . $conn->error, 500);
}

$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    jsonResponse(false, null, 'Usuario no encontrado.', 404);
}

$user = $result->fetch_assoc();

if (
    $password !== $user['password'] &&
    !password_verify($password, $user['password'])
) {
    jsonResponse(false, null, 'Contraseña incorrecta.', 401);
}

jsonResponse(true, [
    'id' => (int)$user['id'],
    'name' => $user['name'],
    'nombre' => $user['name'],
    'email' => $user['email'],
    'rol' => 'paciente'
], 'Login correcto.');
