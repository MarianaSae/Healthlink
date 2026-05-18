<?php
require 'config.php';
setCORSHeaders();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, null, 'Método no permitido.', 405);
}

$data = json_decode(file_get_contents("php://input"), true) ?? [];

$usuario_id = isset($data['usuario_id']) ? (int)$data['usuario_id'] : null;
$doctor_id = (int)($data['doctor_id'] ?? 0);
$paciente_nombre = trim($data['paciente_nombre'] ?? 'Paciente web');
$paciente_email = trim($data['paciente_email'] ?? '');
$fecha = trim($data['fecha'] ?? '');
$hora = trim($data['hora'] ?? '');
$motivo = trim($data['motivo'] ?? '');

if (!$doctor_id || !$paciente_nombre || !$fecha || !$hora) {
    jsonResponse(false, null, 'Datos incompletos.', 400);
}

$conn = getDB();
$stmt = $conn->prepare("INSERT INTO citas (usuario_id, doctor_id, paciente_nombre, paciente_email, fecha, hora, motivo, estado, creado_en) VALUES (?, ?, ?, ?, ?, ?, ?, 'pendiente', NOW())");
$stmt->bind_param('iisssss', $usuario_id, $doctor_id, $paciente_nombre, $paciente_email, $fecha, $hora, $motivo);
$stmt->execute();

jsonResponse(true, ['cita_id' => $stmt->insert_id], 'Cita guardada.');
?>
