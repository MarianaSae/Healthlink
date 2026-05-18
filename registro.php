<?php
require 'config.php';
setCORSHeaders();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, null, 'Método no permitido.', 405);
}

$conn = getDB();
$data = json_decode(file_get_contents('php://input'), true) ?? [];

$name = trim($data['name'] ?? '');
$email = trim($data['email'] ?? '');
$password = trim($data['password'] ?? '');

if ($name === '' || $email === '' || $password === '') {
    jsonResponse(false, null, 'Completa todos los campos.', 400);
}

$check = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
$check->bind_param("s", $email);
$check->execute();
$res = $check->get_result();

if ($res->num_rows > 0) {
    jsonResponse(false, null, 'Ese correo ya está registrado.', 409);
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $name, $email, $hashedPassword);

if (!$stmt->execute()) {
    jsonResponse(false, null, 'Error al registrar usuario: ' . $conn->error, 500);
}

jsonResponse(true, [
    'id' => $conn->insert_id,
    'name' => $name,
    'email' => $email
], 'Cuenta creada correctamente.');