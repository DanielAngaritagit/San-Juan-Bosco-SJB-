-- =============================================================================
-- SCRIPT DE CREACIÓN Y POBLACIÓN DE BASE DE DATOS PARA EL PROYECTO SJB
-- Versión: 1.3 (Esquema limpio + Datos originales formateados)
-- Motor: PostgreSQL
-- =============================================================================

-- -----------------------------------------------------------------------------
-- FASE 1: ELIMINACIÓN DE TABLAS EXISTENTES
-- -----------------------------------------------------------------------------

--DROP TABLE IF EXISTS eventos CASCADE;
--DROP TABLE IF EXISTS tab_actividades CASCADE;
--DROP TABLE IF EXISTS tab_pqrs CASCADE;
--DROP TABLE IF EXISTS tab_comunicaciones CASCADE;
--DROP TABLE IF EXISTS tab_horarios CASCADE;
--DROP TABLE IF EXISTS tab_asistencia CASCADE;
--DROP TABLE IF EXISTS tab_calificaciones CASCADE;
--DROP TABLE IF EXISTS tab_matriculas CASCADE;
--DROP TABLE IF EXISTS tab_cursos CASCADE;
--DROP TABLE IF EXISTS tab_ficha_datos_estudiante CASCADE;
--DROP TABLE IF EXISTS tab_ficha_datos_estudiantes CASCADE;
--DROP TABLE IF EXISTS tab_acudiente CASCADE;
--DROP TABLE IF EXISTS tab_ficha_datos_estudiante_social CASCADE;
--DROP TABLE IF EXISTS tab_ficha_datos_estudiante_salud CASCADE;
--DROP TABLE IF EXISTS tab_ficha_datos_estudiante_hogar CASCADE;
--DROP TABLE IF EXISTS tab_grados CASCADE;
--DROP TABLE IF EXISTS tab_profesores CASCADE;
--DROP TABLE IF EXISTS tab_materias CASCADE;
--DROP TABLE IF EXISTS tab_seguridad_respuestas CASCADE;
--DROP TABLE IF EXISTS tab_password_reset CASCADE;
--DROP TABLE IF EXISTS accesos CASCADE;
--DROP TABLE IF EXISTS login CASCADE;
-- -----------------------------------------------------------------------------
-- FASE 2: CREACIÓN DE EXTENSIONES
-- -----------------------------------------------------------------------------
select * from eventos;
--Usuario: estudiante_test, Contraseña: testpass
--Usuario: padre_test, Contraseña: testpass
--Usuario: profesor_test, Contraseña: testpass

CREATE EXTENSION IF NOT EXISTS pgcrypto;

-- -----------------------------------------------------------------------------
-- FASE 3: CREACIÓN DE TABLAS (Estructura Normalizada)
-- -----------------------------------------------------------------------------

CREATE TABLE login (
  id_log       SERIAL PRIMARY KEY,
  usuario      VARCHAR(50)  NOT NULL UNIQUE,
  contrasena   VARCHAR(255) NOT NULL,
  rol          VARCHAR(20)  NOT NULL CHECK (rol IN ('admin','administrativo','profesor','estudiante','padre')),
  fecha_creacion TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
  activo       BOOLEAN DEFAULT TRUE,
  session_id_actual CHARACTER VARYING(255) -
);

CREATE TABLE accesos (
    id_ace          SERIAL PRIMARY KEY,
    usuario_id      INT NOT NULL REFERENCES login(id_log) ON DELETE CASCADE,
    fecha_acceso    TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP,
    direccion_ip    VARCHAR(45),
    agente_usuario  TEXT,
    tipo_acceso     VARCHAR(20) NOT NULL CHECK (tipo_acceso IN ('login', 'logout', 'timeout', 'failed_attempt'))
);

CREATE TABLE tab_password_reset (
    id               SERIAL PRIMARY KEY,
    id_log           INT NOT NULL REFERENCES login(id_log) ON DELETE CASCADE,
    token            VARCHAR(255) UNIQUE NOT NULL,
    fecha_expiracion TIMESTAMP WITH TIME ZONE NOT NULL,
    utilizado        BOOLEAN DEFAULT FALSE
);

CREATE TABLE tab_seguridad_respuestas (
    id             SERIAL PRIMARY KEY,
    id_log         INT NOT NULL UNIQUE REFERENCES login(id_log) ON DELETE CASCADE,
    pregunta       TEXT NOT NULL,
    respuesta_hash VARCHAR(255) NOT NULL,
    fecha_creacion TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE tab_ficha_datos_estudiante_hogar (
    id_hogar              SERIAL PRIMARY KEY,
    vivecon               VARCHAR(100)  NOT NULL,
    estratosocieconomico  INT           NOT NULL,
    gruposisben           VARCHAR(10)   NOT NULL,
    numhermanos           INT           NOT NULL DEFAULT 0,
    hermanoscole          INT           NOT NULL DEFAULT 0,
    numocupa              INT           NOT NULL DEFAULT 0,
    nucleofami            VARCHAR(100)  NOT NULL
);

CREATE TABLE tab_ficha_datos_estudiante_salud (
    id_salud      SERIAL PRIMARY KEY,
    ars           VARCHAR(50)    NOT NULL,
    ips           VARCHAR(100)   NOT NULL,
    enfermedad    VARCHAR(255)   DEFAULT 'Ninguna',
    eps           VARCHAR(100)   NOT NULL,
    alergias      VARCHAR(255)   DEFAULT 'Ninguna',
    discapacidad  VARCHAR(255)   DEFAULT 'Ninguna',
    capacidad     VARCHAR(255)   DEFAULT 'Ninguna'
);

CREATE TABLE tab_ficha_datos_estudiante_social (
   id_social       SERIAL PRIMARY KEY,
   etnia           VARCHAR(50)  NOT NULL,
   situacionsocial VARCHAR(100) NOT NULL,
   desplazado      VARCHAR(2)   NOT NULL CHECK (desplazado IN ('Si', 'No')),
   poblacion       INT          NOT NULL,
   fecha           DATE         NOT NULL,
   penal           VARCHAR(100) DEFAULT 'Ninguno'
);

CREATE TABLE tab_acudiente (
   id_acudiente        SERIAL PRIMARY KEY,
   parentesco          VARCHAR(50)     NOT NULL CHECK (parentesco IN ('Padre', 'Madre', 'Tutor', 'Otro')),
   apellidos           VARCHAR(100)    NOT NULL,
   nombres             VARCHAR(100)    NOT NULL,
   tipo_documento      VARCHAR(50)     NOT NULL,
   no_documento        VARCHAR(20)     NOT NULL UNIQUE,
   ciudad_expedicion   VARCHAR(100)    NOT NULL,
   sexo                VARCHAR(20)     NOT NULL,
   rh                  VARCHAR(5)      NOT NULL,
   fecha_nacimiento    DATE            NOT NULL,
   direccionp          VARCHAR(255)    NOT NULL,
   lugar_recidencia    VARCHAR(100)    NOT NULL,
   telefonop           VARCHAR(20)     NOT NULL,
   celular             VARCHAR(20)     NOT NULL,
   email               VARCHAR(100)    NOT NULL UNIQUE,
   religion            VARCHAR(50)     NOT NULL,
   nivel_estudio       VARCHAR(100)    NOT NULL,
   ocupacion           TEXT            NOT NULL,
   afiliado            VARCHAR(2)      NOT NULL CHECK (afiliado IN ('Si', 'No')),
   afi_detalles        TEXT,
   empresa             TEXT,
   cargo               TEXT,
   direccion           VARCHAR(255),
   telefonoe           VARCHAR(20)
);

CREATE TABLE tab_ficha_datos_estudiantes (
    id_ficha          SERIAL PRIMARY KEY,
    id_hogar          INT NOT NULL REFERENCES tab_ficha_datos_estudiante_hogar(id_hogar),
    id_salud          INT NOT NULL REFERENCES tab_ficha_datos_estudiante_salud(id_salud),
    id_social         INT NOT NULL REFERENCES tab_ficha_datos_estudiante_social(id_social),
    id_acudiente      INT NOT NULL REFERENCES tab_acudiente(id_acudiente),
    nombres           VARCHAR(100) NOT NULL,
    apellido1         VARCHAR(50)  NOT NULL,
    apellido2         VARCHAR(50),
    tipo_documento    VARCHAR(20)  NOT NULL,
    no_documento      VARCHAR(20)  NOT NULL UNIQUE
);

CREATE TABLE tab_materias (
    id_materia       SERIAL PRIMARY KEY,
    codigo           VARCHAR(20) UNIQUE NOT NULL,
    nombre           VARCHAR(100)       NOT NULL,
    descripcion      TEXT,
    fecha_creacion   DATE NOT NULL DEFAULT CURRENT_DATE
);

CREATE TABLE tab_profesores (
    id_profesor    SERIAL PRIMARY KEY,
    id_materia     INT NOT NULL REFERENCES tab_materias(id_materia),
    nombres        VARCHAR(100) NOT NULL,
    apellidos      VARCHAR(100) NOT NULL,
    especialidad   VARCHAR(100) NOT NULL
);

CREATE TABLE tab_grados (
    id_seccion        SERIAL PRIMARY KEY,
    grado_numero      INT NOT NULL,
    letra_seccion     VARCHAR(10) NOT NULL,
    profesor_lider_id INT REFERENCES tab_profesores(id_profesor),
    CONSTRAINT unique_grado_seccion UNIQUE (grado_numero, letra_seccion)
);

CREATE TABLE tab_ficha_datos_estudiante (
    id_estud          SERIAL PRIMARY KEY,
    id_ficha          INT NOT NULL UNIQUE REFERENCES tab_ficha_datos_estudiantes(id_ficha),
    id_seccion        INT REFERENCES tab_grados(id_seccion),
    ciudad_expedicion TEXT         NOT NULL,
    fecha_nacimiento  DATE         NOT NULL,
    fecha_expedicion  DATE         NOT NULL,
    f_ven_documento   DATE,
    pais_ori          VARCHAR(100) NOT NULL,
    sexo              VARCHAR(20)  NOT NULL,
    rh                VARCHAR(5)   NOT NULL,
    ciudad_nacimiento VARCHAR(100) NOT NULL,
    direccion         VARCHAR(255) NOT NULL,
    barrio            VARCHAR(100) NOT NULL,
    telefonos         VARCHAR(20),
    celular           VARCHAR(20)  NOT NULL,
    email             VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE tab_cursos (
    id_curso      SERIAL PRIMARY KEY,
    nombre_curso  VARCHAR(100) NOT NULL,
    grado         INT          NOT NULL,
    descripcion   TEXT
);

CREATE TABLE tab_matriculas (
    id_matricula    SERIAL PRIMARY KEY,
    id_estud        INT NOT NULL REFERENCES tab_ficha_datos_estudiante(id_estud),
    id_curso        INT NOT NULL REFERENCES tab_cursos(id_curso),
    fecha_matricula DATE NOT NULL DEFAULT CURRENT_DATE
);

CREATE TABLE tab_calificaciones (
    id_calificacion SERIAL PRIMARY KEY,
    id_estud        INT NOT NULL REFERENCES tab_ficha_datos_estudiante(id_estud),
    id_curso        INT NOT NULL REFERENCES tab_cursos(id_curso),
    id_profesor     INT NOT NULL REFERENCES tab_profesores(id_profesor),
    calificacion    DECIMAL(3, 2) NOT NULL
);

CREATE TABLE tab_asistencia (
    id_asistencia SERIAL PRIMARY KEY,
    id_estud      INT NOT NULL REFERENCES tab_ficha_datos_estudiante(id_estud),
    id_profesor   INT NOT NULL REFERENCES tab_profesores(id_profesor),
    fecha_hora    TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP,
    estado        VARCHAR(20) NOT NULL CHECK (estado IN ('presente', 'ausente', 'justificado'))
);

CREATE TABLE tab_horarios (
    id_horario       SERIAL PRIMARY KEY,
    id_curso         INT NOT NULL REFERENCES tab_cursos(id_curso),
    dia_semana       VARCHAR(15) NOT NULL CHECK (dia_semana IN ('Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado')),
    hora_inicio      TIME NOT NULL,
    hora_fin         TIME NOT NULL
);

CREATE TABLE tab_comunicaciones (
    id_comunicacion    SERIAL PRIMARY KEY,
    id_estud           INT NOT NULL REFERENCES tab_ficha_datos_estudiante(id_estud),
    id_profesor        INT NOT NULL REFERENCES tab_profesores(id_profesor),
    mensaje            TEXT NOT NULL,
    fecha_envio        TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE tab_pqrs (
    id_pqr               SERIAL PRIMARY KEY,
    tipo                 VARCHAR(20) NOT NULL,
    descripcion          TEXT NOT NULL,
    fecha_creacion       TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP,
    estado               VARCHAR(20) DEFAULT 'Pendiente',
    nombre_solicitante   VARCHAR(100) NOT NULL,
    contacto_solicitante VARCHAR(100) NOT NULL,
    destinatario         VARCHAR(50) NOT NULL,
    usuario_id           INT REFERENCES login(id_log),
    respuesta            TEXT,
    fecha_respuesta      TIMESTAMP WITH TIME ZONE,
    archivo_adjunto      VARCHAR(255)
);

CREATE TABLE tab_actividades (
    id_actividad       SERIAL PRIMARY KEY,
    id_profesor        INT NOT NULL REFERENCES tab_profesores(id_profesor),
    nombre             VARCHAR(100) NOT NULL,
    descripcion        TEXT,
    fecha_creacion     TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP,
    estado             VARCHAR(20) DEFAULT 'Pendiente',
    comentarios        TEXT,
    archivo_adjunto    BYTEA
);

CREATE TABLE eventos (
    id           SERIAL PRIMARY KEY,
    usuario_id   INT NOT NULL REFERENCES login(id_log) ON DELETE CASCADE,
    nombre       VARCHAR(255) NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin    DATE NOT NULL,
    hora_inicio  TIME NOT NULL,
    hora_fin     TIME NOT NULL,
    color        VARCHAR(20),
    target_roles VARCHAR(255) DEFAULT '',
    target_ids   TEXT DEFAULT '',
    creado       TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- -----------------------------------------------------------------------------
-- FASE 4: CREACIÓN DE ÍNDICES PARA OPTIMIZACIÓN
-- -----------------------------------------------------------------------------

CREATE INDEX IF NOT EXISTS idx_login_usuario ON login(usuario);
CREATE INDEX IF NOT EXISTS idx_accesos_usuario_id ON accesos(usuario_id);
CREATE INDEX IF NOT EXISTS idx_password_reset_token ON tab_password_reset(token);
CREATE INDEX IF NOT EXISTS idx_seguridad_id_log ON tab_seguridad_respuestas(id_log);
CREATE INDEX IF NOT EXISTS idx_estudiantes_no_documento ON tab_ficha_datos_estudiantes(no_documento);
CREATE INDEX IF NOT EXISTS idx_acudiente_email ON tab_acudiente(email);
CREATE INDEX IF NOT EXISTS idx_calificaciones_estudiante ON tab_calificaciones(id_estud);
CREATE INDEX IF NOT EXISTS idx_asistencia_estudiante ON tab_asistencia(id_estud);

-- -----------------------------------------------------------------------------
-- FASE 5: INSERCIÓN DE DATOS ORIGINALES (FORMATEADOS Y NORMALIZADOS)
-- -----------------------------------------------------------------------------

-- ADVERTENCIA: Las contraseñas aquí NO están hasheadas. En una aplicación real,
-- debes hashear las contraseñas antes de insertarlas.

-- Poblando la tabla 'login'
INSERT INTO login (id_log, usuario, contrasena, rol) VALUES
(1, '45687', crypt('ANJO458', gen_salt('bf')), 'admin'),
(2, '2008', crypt('KDMS1406', gen_salt('bf')), 'admin'),
(3, '67891', crypt('LZQO226', gen_salt('bf')), 'admin'),
(4, '101', crypt('est1293', gen_salt('bf')), 'estudiante'),
(5, '102', crypt('admin456', gen_salt('bf')), 'administrativo'),
(6, '201', crypt('adm123', gen_salt('bf')), 'administrativo'),
(7, '202', crypt('adm456', gen_salt('bf')), 'administrativo'),
(8, '301', crypt('prof123', gen_salt('bf')), 'profesor'),
(9, '302', crypt('prof456', gen_salt('bf')), 'profesor'),
(10, '401', crypt('est123', gen_salt('bf')), 'estudiante'),
(11, '402', crypt('est456', gen_salt('bf')), 'estudiante'),
(12, '501', crypt('padre123', gen_salt('bf')), 'padre'),
(13, '502', crypt('padre456', gen_salt('bf')), 'padre'),
(14, '002', crypt('admin', gen_salt('bf')), 'admin'),
(15, '001', crypt('admin', gen_salt('bf')), 'admin'),
(16, 'admin@colegio.com', crypt('password123', gen_salt('bf')), 'admin'),
(17, '003', crypt('padre1', gen_salt('bf')), 'padre'),
(18, '004', crypt('estudiante1', gen_salt('bf')), 'estudiante'),
(19, '005', crypt('profesor', gen_salt('bf')), 'profesor')
ON CONFLICT (id_log) DO NOTHING;

-- Poblando la tabla 'tab_ficha_datos_estudiante_hogar'
INSERT INTO tab_ficha_datos_estudiante_hogar (id_hogar, vivecon, estratosocieconomico, gruposisben, numhermanos, hermanoscole, numocupa, nucleofami) VALUES
(1, 'padre y madre', 2, 'B2', 3, 3, 3, 'no aplica'),
(2, 'solo madre', 1, 'C1', 2, 1, 1, 'monoparental'),
(3, 'padre y madrastra', 3, 'A2', 4, 2, 2, 'reconstituido'),
(4, 'abuelos', 2, 'B1', 1, 1, 1, 'extendido'),
(5, 'solo padre', 4, 'C2', 3, 2, 2, 'monoparental'),
(6, 'padre y madre', 2, 'B2', 2, 2, 2, 'nuclear'),
(7, 'tíos', 1, 'A1', 5, 3, 3, 'extendido'),
(8, 'madre y padrastro', 3, 'B1', 4, 2, 2, 'reconstituido'),
(9, 'solo madre', 2, 'C1', 1, 1, 1, 'monoparental'),
(10, 'madre', 2, 'B2', 0, 0, 0, 'no aplica')
ON CONFLICT (id_hogar) DO NOTHING;

-- Poblando la tabla 'tab_ficha_datos_estudiante_salud'
INSERT INTO tab_ficha_datos_estudiante_salud (id_salud, ars, ips, enfermedad, eps, alergias, discapacidad, capacidad) VALUES
(1, 'NO SE', 'nueva eps', 'ninguna', 'salud total', 'ninguna', 'ninguna', 'ninguna'),
(2, 'SI', 'sura', 'ninguna', 'sanitas', 'ninguna', 'ninguna', 'ninguna'),
(3, 'NO', 'famisanar', 'ninguna', 'coomeva', 'ninguna', 'ninguna', 'ninguna'),
(4, 'NO SE', 'saludcoop', 'ninguna', 'nueva eps', 'ninguna', 'ninguna', 'ninguna'),
(5, 'SI', 'sura', 'ninguna', 'sura', 'ninguna', 'ninguna', 'ninguna'),
(6, 'NO', 'nueva eps', 'ninguna', 'sanitas', 'ninguna', 'ninguna', 'ninguna'),
(7, 'NO SE', 'famisanar', 'ninguna', 'coomeva', 'ninguna', 'ninguna', 'ninguna'),
(8, 'SI', 'saludcoop', 'ninguna', 'nueva eps', 'ninguna', 'ninguna', 'ninguna'),
(9, 'NO', 'sura', 'ninguna', 'sanitas', 'ninguna', 'ninguna', 'ninguna'),
(10, 'NO', 'sura', 'ninguna', 'sanitas', 'ninguna', 'ninguna', 'ninguna')
ON CONFLICT (id_salud) DO NOTHING;

-- Poblando la tabla 'tab_ficha_datos_estudiante_social'
INSERT INTO tab_ficha_datos_estudiante_social (id_social, etnia, situacionsocial, desplazado, poblacion, fecha, penal) VALUES
(1, 'MESTIZO', 'pobre', 'No', 0, '2000-01-01', 'ninguna'),
(2, 'afrodescendiente', 'vulnerable', 'Si', 20000, '2023-02-20', 'no'),
(3, 'ninguna', 'estable', 'No', 10000, '2023-03-10', 'no'),
(4, 'raizal', 'inestable', 'Si', 5000, '2023-04-05', 'si'),
(5, 'gitano', 'vulnerable', 'No', 12000, '2023-05-12', 'no'),
(6, 'ninguna', 'estable', 'No', 18000, '2023-06-18', 'no'),
(7, 'afrodescendiente', 'inestable', 'Si', 7000, '2023-07-22', 'no'),
(8, 'indígena', 'vulnerable', 'No', 9000, '2023-08-30', 'no'),
(9, 'ninguna', 'estable', 'No', 11000, '2023-09-25', 'si'),
(10, 'indígena', 'estable', 'No', 15000, '2023-01-15', 'no')
ON CONFLICT (id_social) DO NOTHING;

-- Poblando la tabla 'tab_acudiente'
INSERT INTO tab_acudiente (id_acudiente, parentesco, apellidos, nombres, tipo_documento, no_documento, ciudad_expedicion, sexo, rh, fecha_nacimiento, direccionp, lugar_recidencia, telefonop, celular, email, religion, nivel_estudio, ocupacion, afiliado, afi_detalles, empresa, cargo, direccion, telefonoe) VALUES
(1, 'Tutor', 'Beltran Gomez', 'Pablo Palo', 'Cedula', '192564575', 'Bucaramanga', 'M', 'O+', '1990-01-12', 'Cra avenidas 6-3', 'Puñaladas', '745', '32687', 'palitoq@gmail.com', 'no aplica', 'universidad', 'empresario', 'Si', 'no se pa', 'puñosñocos', 'presidente', 'cra piso y culebreo1', '5898989'),
(2, 'Madre', 'López Rodríguez', 'Ana María', 'Cédula', '1098554321', 'Medellín', 'Femenino', 'A+', '1985-08-22', 'Carrera 45 #67-89', 'Medellín', '5565431', '3076543', 'ana.lopez@example.com', 'Cristiana', 'Técnico', 'Diseñadora Gráfica', 'Si', 'EPS Sura', 'Creative Designs', 'Diseñadora Senior', 'Calle 34 #56-78', '23678'),
(3, 'Tutor', 'Martínez Sánchez', 'Juan Pablo', 'Cédula', '4567891523', 'Cali', 'Masculino', 'B+', '1975-03-10', 'Avenida 6 #78-90', 'Cali', '34556789', '3004591', 'juan.martinez@example.com', 'Ateo', 'Secundaria', 'Comerciante', 'No', 'N/A', 'Tienda Martínez', 'Propietario', 'Calle 12 #34-56', '8765432'),
(4, 'Madre', 'Nico Gomez', 'Maria Loza', 'Cedula', '174464575', 'Bucaramanga', 'F', 'O+', '1998-08-01', 'Cra 22# 6-3', 'Patadas', '745', '32687', 'paquino@gmail.com', 'no aplica', 'universidad', 'empresaria', 'No', 'no se pa', 'puñosñocos', 'presidente', 'cra piso y culebreo1', '346589'),
(5, 'Padre', 'Martínez Lopez', 'Juan Carlo', 'Cédula', '4567236789', 'California', 'Masculino', 'O+', '1995-03-10', 'Avenida 9 #70-00', 'California', '34556889', '3055591', 'juan@example.com', 'Ateo', 'Bachiller', 'Comerciante', 'No', 'N/A', 'Tienda Esquina', 'Propietario', 'Calle 1 #00-56', '8700432'),
(6, 'Tutor', 'Diaz', 'Marco Ubaldo', 'Cédula', '1098588921', 'Medellín', 'M', 'B+', '1995-04-28', 'Calle 56 #17-09', 'Medellín', '554551', '30765456', 'diaz@example.com', 'No aplica', 'Técnico', 'Caballero', 'Si', 'N/A', 'Palace', 'Caballero', 'Calle 99 #99-78', '2367345')
ON CONFLICT (id_acudiente) DO NOTHING;

-- Poblando la tabla 'tab_ficha_datos_estudiantes'
INSERT INTO tab_ficha_datos_estudiantes (id_ficha, id_hogar, id_salud, id_social, id_acudiente, nombres, apellido1, apellido2, tipo_documento, no_documento) VALUES
(1, 2, 2, 2, 2, 'Carlota', 'Gimenez', 'Rodriguez', 'T.I', '1098098765'),
(2, 1, 1, 1, 3, 'Alejandro', 'Vargas', 'Ochoa', 'T.I', '1099998005'),
(3, 4, 5, 5, 1, 'Juan', 'Lacosta', 'Agilar', 'T.I', '1098000765'),
(4, 6, 7, 8, 6, 'Rodrigo', 'Angarita', 'torrez', 'T.I', '1097798765'),
(5, 5, 3, 6, 2, 'Nestor', 'Quimbayo', 'Hernendez', 'T.I', '1098098712'),
(6, 3, 9, 4, 3, 'Juliana', 'Peña', 'Ortiz', 'T.I', '1099812456'),
(7, 10, 4, 4, 4, 'Andrea carolina', 'Pomares', 'Gimenez', 'T.I', '1098098766'),
(8, 9, 6, 9, 5, 'Andres Felipe', 'Acacio', 'Olave', 'T.I', '1097890342'),
(9, 7, 10, 3, 1, 'Estefania', 'Carreño', 'Rodriguez', 'T.I', '1098033221'),
(10, 8, 8, 10, 2, 'Maria Alejandra', 'Moreno', 'Aguilar', 'T.I', '1098098761')
ON CONFLICT (id_ficha) DO NOTHING;

-- Poblando la tabla 'tab_materias'
INSERT INTO tab_materias (id_materia, codigo, nombre, descripcion, fecha_creacion) VALUES
(1, 'MAT101', 'Matemáticas Básicas', 'Curso introductorio a conceptos matemáticos como aritmética y geometría.', '2023-01-15'),
(2, 'FIS201', 'Física General', 'Estudio de los principios fundamentales de la física, incluyendo mecánica y termodinámica.', '2023-02-10'),
(3, 'QUI301', 'Química Orgánica', 'Introducción a los compuestos orgánicos y sus reacciones químicas.', '2023-03-05'),
(4, 'BIO401', 'Biología Celular', 'Estudio de la estructura y función de las células.', '2023-04-20'),
(5, 'LIT501', 'Literatura Universal', 'Análisis de obras literarias clásicas y contemporáneas de todo el mundo.', '2023-05-12'),
(6, 'HIS601', 'Historia Mundial', 'Exploración de eventos históricos clave que han dado forma al mundo moderno.', '2023-06-30'),
(7, 'ING701', 'Inglés Avanzado', 'Desarrollo de habilidades avanzadas en lectura, escritura y conversación en inglés.', '2023-07-12'),
(8, 'ART801', 'Arte y Diseño', 'Estudio de técnicas artísticas y principios de diseño.', '2023-08-25'),
(9, 'FIL901', 'Filosofía Moderna', 'Análisis de las corrientes filosóficas desde el siglo XVII hasta la actualidad.', '2023-09-14'),
(10, 'PRO1001', 'Programación Básica', 'Introducción a los conceptos fundamentales de la programación.', '2023-10-03')
ON CONFLICT (id_materia) DO NOTHING;

-- Poblando la tabla 'tab_profesores'
INSERT INTO tab_profesores (id_profesor, id_materia, nombres, apellidos, especialidad) VALUES
(1, 1, 'Carlos', 'Gómez', 'Matemáticas y Estadística'),
(2, 2, 'Laura', 'Fernández', 'Física Teórica'),
(3, 3, 'Miguel', 'Rodríguez', 'Química Orgánica'),
(4, 4, 'Ana', 'López', 'Biología Molecular'),
(5, 5, 'Sofía', 'Martínez', 'Literatura y Lingüística'),
(6, 6, 'Jorge', 'Pérez', 'Historia Contemporánea'),
(7, 7, 'Lucía', 'García', 'Enseñanza del Inglés'),
(8, 8, 'Diego', 'Hernández', 'Arte y Diseño Gráfico'),
(9, 9, 'Valeria', 'Díaz', 'Filosofía y Ética'),
(10, 10, 'Andrés', 'Sánchez', 'Programación y Algoritmos'),
(11, 1, 'María', 'Torres', 'Matemáticas Aplicadas'),
(12, 2, 'Pedro', 'Ramírez', 'Física Cuántica'),
(13, 3, 'Camila', 'Vargas', 'Química Analítica'),
(14, 4, 'Juan', 'Moreno', 'Genética y Biotecnología'),
(15, 5, 'Carolina', 'Rojas', 'Literatura Latinoamericana')
ON CONFLICT (id_profesor) DO NOTHING;

-- Poblando la tabla 'tab_grados'
INSERT INTO tab_grados (id_seccion, grado_numero, letra_seccion, profesor_lider_id) VALUES
(1, 1, 'A', 1), (2, 1, 'B', 11), (3, 1, 'C', NULL),
(4, 2, 'A', 2), (5, 2, 'B', 12), (6, 2, 'C', NULL),
(7, 3, 'A', 3), (8, 3, 'B', 13), (9, 3, 'C', NULL),
(10, 4, 'A', 4), (11, 4, 'B', 14), (12, 4, 'C', NULL),
(13, 5, 'A', 5), (14, 5, 'B', 15), (15, 5, 'C', NULL),
(16, 6, 'A', 6), (17, 6, 'B', NULL), (18, 6, 'C', NULL),
(19, 7, 'A', 7), (20, 7, 'B', NULL), (21, 7, 'C', NULL),
(22, 8, 'A', 8), (23, 8, 'B', NULL), (24, 8, 'C', NULL),
(25, 9, 'A', 9), (26, 9, 'B', NULL), (27, 9, 'C', NULL),
(28, 10, 'A', 10), (29, 10, 'B', NULL), (30, 10, 'C', NULL),
(31, 11, 'A', NULL), (32, 11, 'B', NULL), (33, 11, 'C', NULL)
ON CONFLICT (id_seccion) DO NOTHING;

-- Poblando la tabla 'tab_ficha_datos_estudiante'
INSERT INTO tab_ficha_datos_estudiante (id_estud, id_ficha, id_seccion, ciudad_expedicion, fecha_nacimiento, fecha_expedicion, f_ven_documento, pais_ori, sexo, rh, ciudad_nacimiento, direccion, barrio, telefonos, celular, email) VALUES
(1, 1, 1, 'Bogotá', '2017-05-15', '2019-06-20', '2030-06-20', 'Colombia', 'M', 'O+', 'Bogotá', 'Calle 123 #45-67', 'Chapinero', '12347', '30345678', 'juan.perez@example.com'),
(2, 2, 4, 'Cucuta', '2005-06-10', '2015-06-20', '2023-06-20', 'Colombia', 'M', 'O+', 'Bogotá', 'Carrera 4 #45-67', 'Mano', '12309867', '234234578', 'Palitoperez@example.com'),
(3, 3, 7, 'Bucaramanga', '2007-05-15', '2015-06-20', '2025-06-20', 'Colombia', 'F', 'A+', 'Bucaramanga', 'Calle 09 #78-60', 'San pablo', '1234543', '399234568', 'caelot@example.com'),
(4, 4, 10, 'Cali', '2015-05-15', '2019-06-20', '2025-06-20', 'Colombia', 'F', 'O+', 'Cali', 'Carrera 18 #4-55', 'Monterrey', '1239867', '399345678', 'mimi@example.com'),
(5, 5, 13, 'Cucuta', '2011-05-15', '2015-06-20', '2025-06-20', 'Venezuela', 'M', 'B+', 'Bogotá', 'Calle 66 #66-89', 'Campohermoso', '1234007', '12345678', 'loladroz@example.com'),
(6, 6, 16, 'Bogotá', '2009-05-15', '2015-06-20', '2025-06-20', 'Colombia', 'F', 'O-', 'Bogotá', 'Avenida 55 #45-67', 'Chapinero', '1288567', '32345678', 'mrti@example.com'),
(7, 7, 19, 'Santamarta', '2006-05-15', '2015-06-20', '2025-08-24', 'Colombia', 'M', 'A-', 'Bogotá', 'bullevard #45-67', 'Santana', '443467', '32223678', 'mano@example.com'),
(8, 8, 22, 'Valle del cauca', '2008-08-11', '2015-06-29', '2025-06-20', 'Colombia', 'F', 'AB+', 'Cali', 'Villa #45-67', 'Chapinero', '123567', '32446788', 'mali@example.com'),
(9, 9, 25, 'Bogotá', '2010-12-15', '2015-10-20', '2025-09-07', 'Colombia', 'M', 'O+', 'Bogotá', 'Calle 123 #45-67', 'Chapinero', '12347', '30125678', 'jojo@example.com'),
(10, 10, 28, 'Bogotá', '2011-05-15', '2015-06-20', '2030-06-20', 'Colombia', 'F', 'AB-', 'Bucaramanga', 'Calle 13 #5-6', 'Chapinero', '14567', '301345678', 'yona@example.com')
ON CONFLICT (id_estud) DO NOTHING;

-- Poblando la tabla 'tab_cursos'
INSERT INTO tab_cursos (id_curso, nombre_curso, grado, descripcion) VALUES
(1, 'Matemáticas Básicas', 6, 'Curso introductorio a conceptos matemáticos como aritmética y geometría.'),
(2, 'Ciencias Naturales', 7, 'Exploración de biología, química y física para estudiantes de séptimo grado.'),
(3, 'Literatura Universal', 10, 'Estudio de obras literarias clásicas y contemporáneas de todo el mundo.'),
(4, 'Historia Mundial', 9, 'Análisis de eventos históricos clave que han dado forma al mundo moderno.'),
(5, 'Inglés Avanzado', 11, 'Desarrollo de habilidades avanzadas en lectura, escritura y conversación.')
ON CONFLICT (id_curso) DO NOTHING;

-- Poblando la tabla 'tab_matriculas'
INSERT INTO tab_matriculas (id_matricula, id_estud, id_curso, fecha_matricula) VALUES
(1, 1, 1, '2023-01-10'),
(2, 2, 2, '2024-02-15'),
(3, 3, 3, '2019-03-22'),
(4, 4, 4, '2024-04-05'),
(5, 5, 5, '2023-05-18'),
(6, 6, 1, '2023-06-30'),
(7, 7, 2, '2023-07-12'),
(8, 8, 3, '2023-08-25'),
(9, 9, 4, '2023-09-14'),
(10, 10, 5, '2023-10-03')
ON CONFLICT (id_matricula) DO NOTHING;

-- Poblando la tabla 'tab_calificaciones'
-- Nota: Se asume que los IDs corresponden a estudiantes, cursos y profesores existentes.
INSERT INTO tab_calificaciones (id_estud, id_curso, id_profesor, calificacion) VALUES
(1, 1, 1, 4.50),
(2, 2, 2, 3.80),
(3, 3, 3, 4.20),
(4, 4, 4, 3.90),
(5, 5, 5, 4.70);

-- Poblando la tabla 'tab_asistencia'
INSERT INTO tab_asistencia (id_estud, id_profesor, fecha_hora, estado) VALUES
(1, 1, '2025-02-24 08:00:00', 'presente'),
(2, 1, '2025-02-24 08:00:00', 'ausente'),
(3, 2, '2025-02-24 09:00:00', 'justificado');

-- Poblando la tabla 'tab_horarios'
INSERT INTO tab_horarios (id_curso, dia_semana, hora_inicio, hora_fin) VALUES
(1, 'Lunes', '07:00:00', '08:30:00'),
(1, 'Miércoles', '07:00:00', '08:30:00'),
(2, 'Martes', '09:00:00', '10:30:00');

-- Poblando la tabla 'tab_comunicaciones'
INSERT INTO tab_comunicaciones (id_estud, id_profesor, mensaje) VALUES
(2, 5, 'Buenas tardes estimado acudiente, le comunico que el estudiante ha perdido el año. Atentamente, su querido profesor.');

-- Poblando la tabla 'tab_pqrs'
INSERT INTO tab_pqrs (tipo, descripcion, estado, nombre_solicitante, contacto_solicitante, destinatario, usuario_id) VALUES
('Queja', 'Reporte de queja por miradas intimidantes en el receso.', 'Pendiente', 'Acudiente Preocupado', 'acudiente@example.com', 'Coordinación', 4);

-- Poblando la tabla 'tab_actividades'
INSERT INTO tab_actividades (id_profesor, nombre, descripcion, estado, comentarios) VALUES
(1, 'Taller de Aritmética', 'Resolver los problemas de la página 20 del libro.', 'Pendiente', 'Entrega para el próximo viernes.');

-- Poblando la tabla 'eventos'
INSERT INTO eventos (usuario_id, nombre, fecha_inicio, fecha_fin, hora_inicio, hora_fin, color, target_roles, target_ids) VALUES
(1, 'Reunión General de Padres', '2025-08-01', '2025-08-01', '18:00:00', '20:00:00', '#0D164B', '', '');

-- =============================================================================
-- FIN DEL SCRIPT
-- =============================================================================