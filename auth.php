<?php
/* ============================================================
   auth.php — Autenticación con contraseña encriptada (bcrypt)
   Healthlink | Acepta peticiones POST en JSON
   ============================================================ */
require_once 'config.php';
setCORSHeaders();

session_start();

$input  = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? $_POST['action'] ?? '';

switch ($action) {

    /* ── LOGIN ──────────────────────────────────────────── */
    case 'login':
        $email = trim($input['email'] ?? '');
        $pass  = $input['password'] ?? '';

        if (!$email || !$pass) {
            jsonResponse(false, null, 'Correo y contraseña son obligatorios.', 400);
        }

        $db   = getDB();
        $stmt = $db->prepare("SELECT id, nombre, email, password, rol FROM usuarios WHERE email = ? AND activo = 1 LIMIT 1");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        $db->close();

        if (!$user || !password_verify($pass, $user['password'])) {
            jsonResponse(false, null, 'Correo o contraseña incorrectos.', 401);
        }

        // Actualizar último acceso
        $db2  = getDB();
        $upd  = $db2->prepare("UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = ?");
        $upd->bind_param('i', $user['id']);
        $upd->execute(); $upd->close(); $db2->close();

        // Guardar sesión
        $_SESSION['user_id']  = $user['id'];
        $_SESSION['user_name']= $user['nombre'];
        $_SESSION['user_rol'] = $user['rol'];

        unset($user['password']);
        jsonResponse(true, $user, 'Inicio de sesión exitoso.');
        break;

    /* ── REGISTRO ───────────────────────────────────────── */
    case 'register':
        $nombre = trim($input['nombre'] ?? '');
        $email  = trim($input['email'] ?? '');
        $pass   = $input['password'] ?? '';

        if (!$nombre || !$email || !$pass) {
            jsonResponse(false, null, 'Todos los campos son obligatorios.', 400);
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            jsonResponse(false, null, 'Correo electrónico no válido.', 400);
        }
        if (strlen($pass) < 8) {
            jsonResponse(false, null, 'La contraseña debe tener al menos 8 caracteres.', 400);
        }

        $db   = getDB();
        $chk  = $db->prepare("SELECT id FROM usuarios WHERE email = ?");
        $chk->bind_param('s', $email);
        $chk->execute();
        $chk->store_result();
        if ($chk->num_rows > 0) {
            $chk->close(); $db->close();
            jsonResponse(false, null, 'Este correo ya está registrado.', 409);
        }
        $chk->close();

        // Encriptar contraseña con bcrypt (costo 12)
        $hash = password_hash($pass, PASSWORD_BCRYPT, ['cost' => 12]);

        $ins  = $db->prepare("INSERT INTO usuarios (nombre, email, password, rol, activo, fecha_registro) VALUES (?, ?, ?, 'paciente', 1, NOW())");
        $ins->bind_param('sss', $nombre, $email, $hash);
        $ins->execute();
        $newId = $ins->insert_id;
        $ins->close(); $db->close();

        jsonResponse(true, ['id' => $newId], 'Registro exitoso. Ya puedes iniciar sesión.');
        break;

    /* ── LOGOUT ─────────────────────────────────────────── */
    case 'logout':
        session_destroy();
        jsonResponse(true, null, 'Sesión cerrada.');
        break;

    /* ── CHECK SESSION ──────────────────────────────────── */
    case 'check':
        if (!empty($_SESSION['user_id'])) {
            jsonResponse(true, [
                'id'   => $_SESSION['user_id'],
                'name' => $_SESSION['user_name'],
                'rol'  => $_SESSION['user_rol']
            ], 'Sesión activa.');
        } else {
            jsonResponse(false, null, 'No hay sesión activa.', 401);
        }
        break;

    default:
        jsonResponse(false, null, 'Acción no reconocida.', 400);
}
?>
