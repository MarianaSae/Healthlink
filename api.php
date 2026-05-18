<?php
/* ============================================================
   api.php — API REST JSON · Healthlink
   Endpoints:
     GET  api.php?resource=doctores             → lista con filtros
     GET  api.php?resource=doctores&id=5        → un doctor
     GET  api.php?resource=especialidades       → catálogo
     GET  api.php?resource=ciudades             → ciudades disponibles
     POST api.php?resource=citas  {usuario_id, doctor_id, paciente_nombre, fecha, hora}
   ============================================================ */
require_once 'config.php';
setCORSHeaders();

$resource = $_GET['resource'] ?? '';
$method   = $_SERVER['REQUEST_METHOD'];

switch ($resource) {

    /* ── DOCTORES ───────────────────────────────────────── */
    case 'doctores':
        if ($method === 'GET') {
            $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

            // Un solo doctor
            if ($id > 0) {
                $db   = getDB();
                $stmt = $db->prepare(
                    "SELECT d.*, e.nombre AS especialidad_nombre, c.nombre AS ciudad_nombre
                     FROM doctores d
                     LEFT JOIN especialidades e ON d.especialidad_id = e.id
                     LEFT JOIN ciudades c ON d.ciudad_id = c.id
                     WHERE d.id = ? AND d.activo = 1"
                );
                $stmt->bind_param('i', $id);
                $stmt->execute();
                $row = $stmt->get_result()->fetch_assoc();
                $stmt->close(); $db->close();

                if (!$row) { jsonResponse(false, null, 'Doctor no encontrado.', 404); }
                jsonResponse(true, $row);
            }

            // Lista con filtros opcionales
            $db     = getDB();
            $where  = ['d.activo = 1'];
            $params = [];
            $types  = '';

            if (!empty($_GET['especialidad_id'])) {
                $where[] = 'd.especialidad_id = ?';
                $params[] = (int)$_GET['especialidad_id'];
                $types .= 'i';
            }
            if (!empty($_GET['ciudad_id'])) {
                $where[] = 'd.ciudad_id = ?';
                $params[] = (int)$_GET['ciudad_id'];
                $types .= 'i';
            }
            if (!empty($_GET['nombre'])) {
                $where[] = 'd.nombre LIKE ?';
                $params[] = '%' . $db->real_escape_string($_GET['nombre']) . '%';
                $types .= 's';
            }
            if (!empty($_GET['genero'])) {
                $where[] = 'd.genero = ?';
                $params[] = $_GET['genero'];
                $types .= 's';
            }
            if (!empty($_GET['precio_max'])) {
                $where[] = 'd.precio_consulta <= ?';
                $params[] = (float)$_GET['precio_max'];
                $types .= 'd';
            }
            if (!empty($_GET['rating_min'])) {
                $where[] = 'd.rating >= ?';
                $params[] = (float)$_GET['rating_min'];
                $types .= 'd';
            }

            // Ordenamiento
            $allowed_sort = ['rating' => 'd.rating DESC', 'precio_asc' => 'd.precio_consulta ASC',
                             'precio_desc' => 'd.precio_consulta DESC', 'exp' => 'd.anios_exp DESC'];
            $sort = $allowed_sort[$_GET['sort'] ?? ''] ?? 'd.rating DESC';

            // Paginación
            $page  = max(1, (int)($_GET['page'] ?? 1));
            $limit = min(20, max(1, (int)($_GET['limit'] ?? 9)));
            $offset = ($page - 1) * $limit;

            $whereStr = implode(' AND ', $where);
            $sql = "SELECT d.id, d.nombre, d.foto, d.rating, d.total_reseñas,
                           d.precio_consulta, d.anios_exp, d.genero,
                           d.modalidad, d.horario,
                           e.nombre AS especialidad, c.nombre AS ciudad
                    FROM doctores d
                    LEFT JOIN especialidades e ON d.especialidad_id = e.id
                    LEFT JOIN ciudades c ON d.ciudad_id = c.id
                    WHERE $whereStr ORDER BY $sort LIMIT ? OFFSET ?";

            $params[] = $limit;
            $params[] = $offset;
            $types .= 'ii';

            $stmt = $db->prepare($sql);
            if ($types) { $stmt->bind_param($types, ...$params); }
            $stmt->execute();
            $result = $stmt->get_result();
            $rows   = [];
            while ($r = $result->fetch_assoc()) { $rows[] = $r; }
            $stmt->close();

            // Total para paginación
            $sqlCount = "SELECT COUNT(*) as total FROM doctores d WHERE $whereStr";
            $stmtC = $db->prepare($sqlCount);
            if ($types && strlen($types) > 2) {
                $typesC = substr($types, 0, -2);
                $paramsC = array_slice($params, 0, -2);
                if ($typesC) { $stmtC->bind_param($typesC, ...$paramsC); }
            }
            $stmtC->execute();
            $total = $stmtC->get_result()->fetch_assoc()['total'];
            $stmtC->close();
            $db->close();

            jsonResponse(true, [
                'doctores' => $rows,
                'total'    => (int)$total,
                'page'     => $page,
                'pages'    => ceil($total / $limit)
            ]);
        }
        break;

    /* ── ESPECIALIDADES ─────────────────────────────────── */
    case 'especialidades':
        $db   = getDB();
        $res  = $db->query("SELECT id, nombre, icono FROM especialidades ORDER BY nombre");
        $rows = [];
        while ($r = $res->fetch_assoc()) { $rows[] = $r; }
        $db->close();
        jsonResponse(true, $rows);
        break;

    /* ── CIUDADES ───────────────────────────────────────── */
    case 'ciudades':
        $db   = getDB();
        $res  = $db->query("SELECT id, nombre, estado FROM ciudades ORDER BY nombre");
        $rows = [];
        while ($r = $res->fetch_assoc()) { $rows[] = $r; }
        $db->close();
        jsonResponse(true, $rows);
        break;

    /* ── CITAS ──────────────────────────────────────────── */
    case 'citas':
        if ($method === 'POST') {
            $body = json_decode(file_get_contents('php://input'), true);
            $usuario_id       = isset($body['usuario_id']) ? (int)$body['usuario_id'] : null;
            $doctor_id        = (int)($body['doctor_id'] ?? 0);
            $paciente_nombre  = trim($body['paciente_nombre'] ?? '');
            $paciente_email   = trim($body['paciente_email'] ?? '');
            $fecha            = $body['fecha'] ?? '';
            $hora             = $body['hora'] ?? '';
            $motivo           = trim($body['motivo'] ?? '');

            if (!$doctor_id || !$paciente_nombre || !$fecha || !$hora) {
                jsonResponse(false, null, 'Datos incompletos para agendar la cita.', 400);
            }

            $db   = getDB();
            $stmt = $db->prepare(
                "INSERT INTO citas (usuario_id, doctor_id, paciente_nombre, paciente_email, fecha, hora, motivo, estado, creado_en)
                 VALUES (?, ?, ?, ?, ?, ?, ?, 'pendiente', NOW())"
            );
            $stmt->bind_param('iisssss', $usuario_id, $doctor_id, $paciente_nombre, $paciente_email, $fecha, $hora, $motivo);
            $stmt->execute();
            $citaId = $stmt->insert_id;
            $stmt->close(); $db->close();

            jsonResponse(true, ['cita_id' => $citaId], 'Cita agendada correctamente.');
        }
        break;

    default:
        jsonResponse(false, null, 'Recurso no encontrado.', 404);
}
?>
