<?php
require 'config.php';
setCORSHeaders();

$usuario_id = isset($_GET['usuario_id']) ? (int)$_GET['usuario_id'] : 0;
$email = trim($_GET['email'] ?? '');

if (!$usuario_id && $email === '') {
    jsonResponse(false, null, 'Falta usuario_id o email.', 400);
}

$conn = getDB();

if ($usuario_id) {
    $stmt = $conn->prepare("SELECT c.*, d.nombre AS doctor_nombre, d.foto AS doctor_foto, e.nombre AS especialidad FROM citas c LEFT JOIN doctores d ON c.doctor_id = d.id LEFT JOIN especialidades e ON d.especialidad_id = e.id WHERE c.usuario_id = ? ORDER BY c.fecha ASC, c.hora ASC");
    $stmt->bind_param('i', $usuario_id);
} else {
    $stmt = $conn->prepare("SELECT c.*, d.nombre AS doctor_nombre, d.foto AS doctor_foto, e.nombre AS especialidad FROM citas c LEFT JOIN doctores d ON c.doctor_id = d.id LEFT JOIN especialidades e ON d.especialidad_id = e.id WHERE c.paciente_email = ? ORDER BY c.fecha ASC, c.hora ASC");
    $stmt->bind_param('s', $email);
}

$stmt->execute();
$res = $stmt->get_result();
$citas = [];
while ($row = $res->fetch_assoc()) {
    $citas[] = $row;
}

jsonResponse(true, $citas);
?>
