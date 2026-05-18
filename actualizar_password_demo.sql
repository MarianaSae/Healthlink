-- Ejecuta esto en phpMyAdmin si ya habías importado la base de datos anterior.
-- Usuario demo: demo@healthlink.mx / password
-- Admin: admin@healthlink.mx / password

UPDATE usuarios
SET password = '$2y$12$7aFrTeiUTPhWGzNtVqbbt.T9I6nCzJU2uJdp0LbF6NFac37erwoHa'
WHERE email IN ('demo@healthlink.mx', 'admin@healthlink.mx');
