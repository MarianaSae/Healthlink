-- ============================================================
-- healthlink.sql — Volcado completo de base de datos
-- Healthlink | Ciudad Juárez, Chihuahua
-- Servidor: MySQL 5.7+ / MariaDB 10.3+
-- INSTRUCCIONES:
--   1. En AwardSpace: MySQL Databases → crear BD "healthlink"
--   2. phpMyAdmin → seleccionar BD → Importar → subir este archivo
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "-07:00";  -- America/Mazatlan

-- --------------------------------------------------------
-- Tabla: ciudades
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `ciudades` (
  `id`     INT(11) NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(100) NOT NULL,
  `estado` VARCHAR(100) NOT NULL DEFAULT 'Chihuahua',
  `lat`    DECIMAL(10,7) DEFAULT NULL,
  `lng`    DECIMAL(10,7) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `ciudades` (`id`, `nombre`, `estado`, `lat`, `lng`) VALUES
(1, 'Ciudad Juárez',   'Chihuahua', 31.6904,  -106.4245),
(2, 'Chihuahua',        'Chihuahua', 28.6329,   -106.0691),
(3, 'Delicias',         'Chihuahua', 28.1874,   -105.4685),
(4, 'Cuauhtémoc',       'Chihuahua', 28.4138,   -106.8665),
(5, 'Parral',           'Chihuahua', 26.9335,   -105.6663);

-- --------------------------------------------------------
-- Tabla: especialidades
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `especialidades` (
  `id`     INT(11) NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(100) NOT NULL,
  `icono`  VARCHAR(10)  NOT NULL DEFAULT '🏥',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `especialidades` (`id`, `nombre`, `icono`) VALUES
(1,  'Medicina General',    '🩺'),
(2,  'Cardiología',         '❤️'),
(3,  'Pediatría',           '👶'),
(4,  'Dermatología',        '🌿'),
(5,  'Neurología',          '🧠'),
(6,  'Ginecología',         '🌸'),
(7,  'Traumatología',       '🦴'),
(8,  'Psiquiatría',         '🧘'),
(9,  'Oftalmología',        '👁️'),
(10, 'Nutrición',           '🥗'),
(11, 'Odontología',         '🦷'),
(12, 'Urología',            '⚕️');

-- --------------------------------------------------------
-- Tabla: doctores
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `doctores` (
  `id`              INT(11)         NOT NULL AUTO_INCREMENT,
  `nombre`          VARCHAR(150)    NOT NULL,
  `especialidad_id` INT(11)         NOT NULL,
  `ciudad_id`       INT(11)         NOT NULL,
  `genero`          ENUM('M','F')   NOT NULL DEFAULT 'M',
  `foto`            VARCHAR(255)    DEFAULT NULL,
  `cedula`          VARCHAR(60)     DEFAULT NULL,
  `universidad`     VARCHAR(150)    DEFAULT NULL,
  `anios_exp`       TINYINT(3)      NOT NULL DEFAULT 0,
  `bio`             TEXT            DEFAULT NULL,
  `horario`         VARCHAR(100)    DEFAULT 'Lun–Vie 9–18h',
  `telefono`        VARCHAR(30)     DEFAULT NULL,
  `email`           VARCHAR(150)    DEFAULT NULL,
  `modalidad`       VARCHAR(80)     DEFAULT 'presencial',
  `precio_consulta` DECIMAL(8,2)    NOT NULL DEFAULT 500.00,
  `rating`          DECIMAL(3,2)    NOT NULL DEFAULT 0.00,
  `total_reseñas`   INT(11)         NOT NULL DEFAULT 0,
  `lat`             DECIMAL(10,7)   DEFAULT NULL,
  `lng`             DECIMAL(10,7)   DEFAULT NULL,
  `activo`          TINYINT(1)      NOT NULL DEFAULT 1,
  `created_at`      TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_especialidad` (`especialidad_id`),
  KEY `fk_ciudad`       (`ciudad_id`),
  CONSTRAINT `fk_esp` FOREIGN KEY (`especialidad_id`) REFERENCES `especialidades` (`id`),
  CONSTRAINT `fk_ciu` FOREIGN KEY (`ciudad_id`)       REFERENCES `ciudades`       (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `doctores` (`id`,`nombre`,`especialidad_id`,`ciudad_id`,`genero`,`foto`,`cedula`,`universidad`,`anios_exp`,`bio`,`horario`,`telefono`,`email`,`modalidad`,`precio_consulta`,`rating`,`total_reseñas`,`lat`,`lng`) VALUES
(1,  'Dra. Ana Martínez Reyes',    2, 1,'F','https://randomuser.me/api/portraits/women/44.jpg','4821093','UACH',12,'Cardióloga con 12 años de experiencia. Certificada por el Consejo Mexicano de Cardiología.','Lun–Vie 9–18h','656-100-1001','ana.martinez@healthlink.mx','presencial,online',850.00,4.90,128,31.6904,-106.4245),
(2,  'Dr. Carlos Herrera Soto',    3, 1,'M','https://randomuser.me/api/portraits/men/32.jpg', '3910284','UNAM',  8,'Pediatra con enfoque en nutrición infantil. Atención cálida para los más pequeños.','Lun–Sáb 8–16h','656-100-1002','carlos.herrera@healthlink.mx','presencial',600.00,4.80,95,31.7200,-106.4280),
(3,  'Dra. Sofía Ramos Luna',      4, 1,'F','https://randomuser.me/api/portraits/women/68.jpg','5102847','IPN',  10,'Dermatóloga clínica y estética. Especialista en acné, rosácea y rejuvenecimiento.','Mar–Vie 10–19h','656-100-1003','sofia.ramos@healthlink.mx','presencial,online',750.00,4.70,212,31.6800,-106.4100),
(4,  'Dr. Miguel Torres Vidal',    5, 2,'M','https://randomuser.me/api/portraits/men/55.jpg', '6203918','UACH', 18,'Neurólogo con subespecialidad en epilepsia y trastornos del movimiento.','Lun–Jue 9–17h','614-100-2001','miguel.torres@healthlink.mx','presencial,urgencias',1200.00,4.60,74,28.6329,-106.0691),
(5,  'Dra. Laura Vega Morales',    6, 1,'F','https://randomuser.me/api/portraits/women/22.jpg','4720193','UNAM', 14,'Ginecóloga-Obstetra. Especialista en fertilidad y embarazo de alto riesgo.','Lun–Vie 9–18h','656-100-1004','laura.vega@healthlink.mx','presencial,online',900.00,4.90,301,31.6950,-106.4350),
(6,  'Dr. Javier Molina Cruz',     7, 3,'M','https://randomuser.me/api/portraits/men/77.jpg', '3819204','UACH',  9,'Ortopedista y traumatólogo. Especialista en cirugía de rodilla y columna vertebral.','Lun–Sáb 8–17h','639-100-3001','javier.molina@healthlink.mx','presencial,urgencias',1100.00,4.50,56,28.1874,-105.4685),
(7,  'Dra. Patricia León Ríos',    8, 1,'F','https://randomuser.me/api/portraits/women/55.jpg','5930182','UNAM', 11,'Psiquiatra con formación en terapia cognitivo-conductual. Atención de ansiedad y depresión.','Lun–Vie 10–19h','656-100-1005','patricia.leon@healthlink.mx','presencial,online',950.00,4.80,142,31.6750,-106.4200),
(8,  'Dr. Roberto Jiménez Paz',    9, 2,'M','https://randomuser.me/api/portraits/men/14.jpg', '4108293','IPN',   7,'Oftalmólogo general y cirujano de cataratas. Tecnología de vanguardia para tu visión.','Mar–Sáb 9–17h','614-100-2002','roberto.jimenez@healthlink.mx','presencial',700.00,4.70,88,28.6400,-106.0750),
(9,  'Dra. Elena Castillo Ureña',  2, 1,'F','https://randomuser.me/api/portraits/women/33.jpg','6012847','UACH', 15,'Cardióloga intervencionista. Experta en cateterismo, stents y rehabilitación cardíaca.','Lun–Vie 8–16h','656-100-1006','elena.castillo@healthlink.mx','presencial,online,urgencias',1000.00,4.60,67,31.6850,-106.4150),
(10, 'Dr. Andrés Fuentes Nava',    3, 1,'M','https://randomuser.me/api/portraits/men/41.jpg', '5219384','UNAM',  6,'Pediatra con subespecialidad en alergia e inmunología pediátrica.','Lun–Sáb 9–18h','656-100-1007','andres.fuentes@healthlink.mx','presencial,online',650.00,4.90,198,31.7050,-106.4320),
(11, 'Dra. María Gutiérrez Sosa',  4, 4,'F','https://randomuser.me/api/portraits/women/77.jpg','4609183','IPN',   5,'Dermatóloga clínica. Tratamiento de psoriasis, dermatitis atópica y lesiones pigmentadas.','Lun–Vie 10–18h','625-100-4001','maria.gutierrez@healthlink.mx','presencial',680.00,4.40,43,28.4138,-106.8665),
(12, 'Dr. Santiago Pérez Alva',    5, 1,'M','https://randomuser.me/api/portraits/men/62.jpg', '5803914','UACH', 13,'Neurólogo clínico. Especialista en migraña, esclerosis múltiple y neuropatías.','Mar–Vie 9–17h','656-100-1008','santiago.perez@healthlink.mx','presencial,online',1050.00,4.70,115,31.6970,-106.4380),
(13, 'Dra. Gabriela Rojo Durán',   1, 1,'F','https://randomuser.me/api/portraits/women/88.jpg','4301827','UACH',  4,'Médico general con enfoque en medicina preventiva y atención primaria.','Lun–Vie 8–18h','656-100-1009','gabriela.rojo@healthlink.mx','presencial,online',400.00,4.85,89,31.6810,-106.4090),
(14, 'Dr. Eduardo Salas Ponce',   10, 2,'M','https://randomuser.me/api/portraits/men/88.jpg', '5718294','UNAM', 10,'Nutriólogo clínico. Especialista en obesidad, diabetes y trastornos alimenticios.','Lun–Jue 9–17h','614-100-2003','eduardo.salas@healthlink.mx','presencial,online',700.00,4.75,102,28.6250,-106.0620),
(15, 'Dra. Valeria Cruz Talamante',11, 1,'F','https://randomuser.me/api/portraits/women/11.jpg','6209183','UACH',  3,'Odontóloga general y estética. Ortodoncia, blanqueamiento y rehabilitación oral.','Mar–Sáb 9–18h','656-100-1010','valeria.cruz@healthlink.mx','presencial',550.00,4.95,231,31.7100,-106.4410);

-- --------------------------------------------------------
-- Tabla: usuarios
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id`             INT(11)       NOT NULL AUTO_INCREMENT,
  `nombre`         VARCHAR(150)  NOT NULL,
  `email`          VARCHAR(150)  NOT NULL,
  `password`       VARCHAR(255)  NOT NULL,   -- bcrypt hash
  `rol`            ENUM('paciente','admin','doctor') NOT NULL DEFAULT 'paciente',
  `activo`         TINYINT(1)    NOT NULL DEFAULT 1,
  `fecha_registro` TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ultimo_acceso`  TIMESTAMP     NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Usuario demo (password: Demo1234!) — hash bcrypt costo 12
INSERT INTO `usuarios` (`nombre`, `email`, `password`, `rol`) VALUES
('Usuario Demo',  'demo@healthlink.mx',  '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'paciente'),
('Administrador', 'admin@healthlink.mx', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');
-- NOTA: El hash de arriba corresponde a la contraseña "password"
-- Para producción genera tu propio hash con: php -r "echo password_hash('TuContraseña', PASSWORD_BCRYPT, ['cost'=>12]);"

-- --------------------------------------------------------
-- Tabla: citas
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `citas` (
  `id`               INT(11)       NOT NULL AUTO_INCREMENT,
  `doctor_id`        INT(11)       NOT NULL,
  `paciente_nombre`  VARCHAR(150)  NOT NULL,
  `paciente_email`   VARCHAR(150)  DEFAULT NULL,
  `fecha`            DATE          NOT NULL,
  `hora`             TIME          NOT NULL,
  `motivo`           TEXT          DEFAULT NULL,
  `estado`           ENUM('pendiente','confirmada','cancelada','completada') NOT NULL DEFAULT 'pendiente',
  `creado_en`        TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_cita_doctor` (`doctor_id`),
  CONSTRAINT `fk_cita_doc` FOREIGN KEY (`doctor_id`) REFERENCES `doctores` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Tabla: reseñas
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `resenias` (
  `id`          INT(11)    NOT NULL AUTO_INCREMENT,
  `doctor_id`   INT(11)    NOT NULL,
  `usuario_id`  INT(11)    DEFAULT NULL,
  `nombre`      VARCHAR(100) NOT NULL,
  `calificacion` TINYINT(1) NOT NULL CHECK (`calificacion` BETWEEN 1 AND 5),
  `comentario`  TEXT        DEFAULT NULL,
  `fecha`       TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_res_doctor` (`doctor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `resenias` (`doctor_id`,`nombre`,`calificacion`,`comentario`) VALUES
(1,'Martha López',5,'Excelente doctora, muy atenta y profesional. La recomiendo ampliamente.'),
(1,'José Ramírez',5,'Diagnóstico preciso y rápido. Me dio mucha tranquilidad.'),
(3,'Claudia Torres',4,'Muy buena atención, resolvió mi problema de piel en pocas sesiones.'),
(5,'Sandra Molina',5,'La mejor ginecóloga que he tenido. Muy humana y profesional.');

-- Fin del volcado
