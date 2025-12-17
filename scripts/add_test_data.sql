-- =============================================================================
-- SCRIPT PARA AÑADIR DATOS DE PRUEBA (PADRE E HIJO) AL PROYECTO SJB
-- Versión: 1.0
-- Motor: PostgreSQL
-- =============================================================================

-- Nuevo usuario padre para pruebas (id_log = 20, usuario = '192564575' que coincide con no_documento de id_acudiente = 1)
INSERT INTO login (id_log, usuario, contrasena, rol) VALUES
(20, '192564575', crypt('password_padre', gen_salt('bf')), 'padre')
ON CONFLICT (id_log) DO NOTHING;

-- Nuevo estudiante vinculado al acudiente con id_acudiente = 1 (Pablo Palo)
-- Asegúrate de que los id_hogar, id_salud, id_social existan o ajusta según tu base de datos
INSERT INTO tab_ficha_datos_estudiantes (id_ficha, id_hogar, id_salud, id_social, id_acudiente, nombres, apellido1, apellido2, tipo_documento, no_documento) VALUES
(11, 1, 1, 1, 1, 'Hijo', 'De Pablo', 'Palo', 'T.I', '1000000001')
ON CONFLICT (id_ficha) DO NOTHING;

INSERT INTO tab_ficha_datos_estudiante (id_estud, id_ficha, id_seccion, ciudad_expedicion, fecha_nacimiento, fecha_expedicion, f_ven_documento, pais_ori, sexo, rh, ciudad_nacimiento, direccion, barrio, telefonos, celular, email) VALUES
(11, 11, 1, 'Bucaramanga', '2010-03-15', '2020-01-01', '2030-01-01', 'Colombia', 'M', 'O+', 'Bucaramanga', 'Calle Falsa 123', 'Centro', '1234567', '123456789', 'hijo.pablo@example.com')
ON CONFLICT (id_estud) DO NOTHING;

-- Calificaciones de prueba para el nuevo estudiante (id_estud = 11)
-- Asegúrate de que los id_curso y id_profesor existan o ajusta según tu base de datos
INSERT INTO tab_calificaciones (id_calificacion, id_estud, id_curso, id_profesor, calificacion) VALUES
(1000, 11, 1, 1, 4.0),
(1001, 11, 2, 2, 3.5),
(1002, 11, 3, 3, 4.8),
(1003, 11, 4, 4, 3.2);