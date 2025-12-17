-- =============================================================================
-- SCRIPT DE CREACIÓN Y POBLACIÓN DE BASE DE DATOS PARA EL PROYECTO SJB
-- Versión: 1.5 (Esquema corregido con datos válidos)
-- Motor: PostgreSQL
-- =============================================================================

-- ----------------------------------------------------------------------------
-- FASE 1: ELIMINACIÓN DE TABLAS Y TIPOS EXISTENTES
-- ----------------------------------------------------------------------------

DROP TABLE IF EXISTS active_sessions CASCADE;
DROP TABLE IF EXISTS tab_administradores CASCADE;
DROP TABLE IF EXISTS tab_acudiente CASCADE;
DROP TABLE IF EXISTS tab_estudiante CASCADE;
DROP TABLE IF EXISTS tab_actividades CASCADE;
DROP TABLE IF EXISTS tab_usuarios CASCADE;
DROP TABLE IF EXISTS eventos CASCADE;
DROP TABLE IF EXISTS tab_pqrsf CASCADE;
DROP TABLE IF EXISTS tab_comunicaciones CASCADE;
DROP TABLE IF EXISTS tab_asistencia CASCADE;
DROP TABLE IF EXISTS tab_calificaciones CASCADE;
DROP TABLE IF EXISTS tab_matriculas CASCADE;
DROP TABLE IF EXISTS profesor_grado CASCADE;
DROP TABLE IF EXISTS tab_cursos CASCADE;
DROP TABLE IF EXISTS tab_grados CASCADE;
DROP TABLE IF EXISTS tab_profesores CASCADE;
DROP TABLE IF EXISTS tab_materias CASCADE;
DROP TABLE IF EXISTS tab_seguridad_respuestas CASCADE;
DROP TABLE IF EXISTS tab_password_reset CASCADE;
DROP TABLE IF EXISTS tab_profesor_curso CASCADE;
DROP TABLE IF EXISTS accesos CASCADE;
DROP TABLE IF EXISTS login CASCADE;
DROP TABLE IF EXISTS periodos_academicos CASCADE;


-- ----------------------------------------------------------------------------
-- FASE 2: CREACIÓN DE EXTENSIONES
-- ----------------------------------------------------------------------------

CREATE EXTENSION IF NOT EXISTS pgcrypto;

CREATE TYPE tipo_evaluacion_enum AS ENUM (
    'Evaluacion Escrita',
    'Evaluacion Oral',
    'Evaluacion Practica',
    'Proyecto',
    'Participacion en Clase',
    'Trabajo en Clase',
    'Tarea o trabajo en casa',
    'Evaluacion Cognitiva'
);

-- ----------------------------------------------------------------------------
-- FASE 3: CREACIÓN DE TIPOS Y TABLAS
-- ----------------------------------------------------------------------------

CREATE TABLE login (
    id_log SERIAL PRIMARY KEY,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    contrasena VARCHAR(255) NOT NULL,
    rol VARCHAR(20) NOT NULL,
    activo BOOLEAN DEFAULT TRUE,
    session_id_actual VARCHAR(255),
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    foto_url VARCHAR(255),
    email VARCHAR(100)
);

CREATE TABLE accesos (
    id_ace          SERIAL PRIMARY KEY,
    usuario_id      INT NOT NULL REFERENCES login(id_log) ON DELETE CASCADE,
    fecha_acceso    TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP,
    direccion_ip    VARCHAR(45),
    agente_usuario  TEXT,
    tipo_acceso     VARCHAR(20) NOT NULL CHECK (tipo_acceso IN ('login', 'logout', 'timeout', 'failed_attempt'))
);

CREATE TABLE tab_usuarios (
   id_usuario           SERIAL               PRIMARY KEY,
   id_log               INT                  NOT NULL REFERENCES login(id_log) ON DELETE CASCADE,
   nombre               VARCHAR(100)         NOT NULL,
   apellido             VARCHAR(100)         NOT NULL,
   email                VARCHAR(100)         NOT NULL,
   telefono             VARCHAR(20)          NULL,
   tipo_documento       VARCHAR(50)          NULL,
   no_documento         VARCHAR(50)          NULL UNIQUE,
   fecha_nacimiento     DATE                 NULL,
   rh                   VARCHAR(5)           NULL,
   alergias             TEXT                 NULL,
   fecha_creacion       TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE tab_administradores (
    id_administrador SERIAL PRIMARY KEY,
    id_log INT UNIQUE NOT NULL REFERENCES login(id_log),
    nombres VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    tipo_documento VARCHAR(50),
    no_documento VARCHAR(50) UNIQUE,
    fecha_expedicion DATE,
    fecha_nacimiento DATE,
    email VARCHAR(100),
    telefono VARCHAR(20),
    direccion VARCHAR(255),
    cargo VARCHAR(100),
    eps VARCHAR(100),
    rh VARCHAR(5),
    alergias TEXT,
    sexo VARCHAR(20),
    estado_civil VARCHAR(50)
);

CREATE TABLE tab_acudiente (
   id_acudiente        SERIAL PRIMARY KEY,
   id_log              INT,
   parentesco          VARCHAR(50)     NOT NULL CHECK (parentesco IN ('Padre', 'Madre', 'Tutor', 'Otro')),
   apellidos           VARCHAR(100)    NOT NULL,
   nombres             VARCHAR(100)    NOT NULL,
   tipo_documento      VARCHAR(50)     NOT NULL,
   no_documento        VARCHAR(20)     NOT NULL UNIQUE,
   ciudad_expedicion   VARCHAR(100)    NOT NULL,
   fecha_expedicion    DATE,
   nacionalidad        VARCHAR(100),
   sexo                VARCHAR(20)     NOT NULL,
   rh                  VARCHAR(5)      NOT NULL,
   alergias            TEXT,
   fecha_nacimiento    DATE            NOT NULL,
   direccionp          VARCHAR(255)    NOT NULL,
   lugar_recidencia    VARCHAR(100)    NOT NULL,
   telefono            VARCHAR(20)     NOT NULL,
   email               VARCHAR(100)    NOT NULL,
   religion            VARCHAR(50)     NOT NULL,
   nivel_estudio       VARCHAR(100)    NOT NULL,
   profesion           TEXT,
   empresa             TEXT,
   ocupacion           TEXT            NOT NULL,
   afiliado            VARCHAR(2)      NOT NULL CHECK (afiliado IN ('Si', 'No')),
   afi_detalles        TEXT,
   barrio              VARCHAR(100),
   estado_civil        VARCHAR(50),
   eps                 VARCHAR(100),
   FOREIGN KEY (id_log) REFERENCES login(id_log)
);

CREATE TABLE tab_estudiante (
    id_ficha SERIAL PRIMARY KEY,
    id_acudiente INT,
    nombres VARCHAR(100) NOT NULL,
    apellido1 VARCHAR(50) NOT NULL,
    apellido2 VARCHAR(50),
    tipo_documento VARCHAR(20) NOT NULL,
    no_documento VARCHAR(20) NOT NULL UNIQUE,
    id_seccion INT,
    grado VARCHAR(50),
    ciudad_expedicion TEXT NOT NULL,
    fecha_nacimiento DATE NOT NULL,
    fecha_expedicion DATE NOT NULL,
    pais_ori VARCHAR(100) NOT NULL,
    nacionalidad VARCHAR(100),
    sexo VARCHAR(20) NOT NULL,
    rh VARCHAR(5) NOT NULL,
    ciudad_nacimiento VARCHAR(100) NOT NULL,
    direccion VARCHAR(255) NOT NULL,
    barrio VARCHAR(100) NOT NULL,
    telefonos VARCHAR(20),
    email VARCHAR(100) NOT NULL,
    vivecon VARCHAR(100) NOT NULL,
    estratosocieconomico INT NOT NULL,
    gruposisben VARCHAR(10) NOT NULL,
    numhermanos INT NOT NULL DEFAULT 0,
    hermanoscole INT NOT NULL DEFAULT 0,
    enfermedad VARCHAR(255) DEFAULT 'Ninguna',
    eps VARCHAR(100) NOT NULL,
    alergias VARCHAR(255) DEFAULT 'Ninguna',
    discapacidad VARCHAR(255) DEFAULT 'Ninguna',
    etnia VARCHAR(50) NOT NULL,
    desplazado VARCHAR(2) NOT NULL CHECK (desplazado IN ('Si', 'No')),
    fecha DATE NOT NULL,
    FOREIGN KEY (id_acudiente) REFERENCES tab_acudiente(id_acudiente) ON DELETE SET NULL
);

CREATE TABLE tab_materias (
    id_materia       SERIAL PRIMARY KEY,
    codigo           VARCHAR(20) UNIQUE NOT NULL,
    nombre           VARCHAR(100)       NOT NULL,
    descripcion      TEXT,
    creditos         INT,
    fecha_creacion   DATE NOT NULL DEFAULT CURRENT_DATE
);

CREATE TABLE tab_profesores (
    id_profesor      SERIAL PRIMARY KEY,
    id_log           INT,
    id_materia       INT,
    nombres          VARCHAR(100) NOT NULL,
    apellidos        VARCHAR(100) NOT NULL,
    tipo_documento   VARCHAR(50),
    no_documento     VARCHAR(50) UNIQUE,
    fecha_expedicion DATE,
    fecha_nacimiento DATE,
    email            VARCHAR(100),
    telefono         VARCHAR(20),
    direccion        VARCHAR(255),
    nacionalidad     VARCHAR(100),
    rh               VARCHAR(10),
    alergias         TEXT,
    sexo VARCHAR(20),
    estado_civil VARCHAR(50),
    especialidad     VARCHAR(100) NOT NULL,
    titulo_academico VARCHAR(100),
    eps              VARCHAR(100),
    FOREIGN KEY (id_log) REFERENCES login(id_log)
);

CREATE TABLE tab_grados (
    id_seccion        SERIAL PRIMARY KEY,
    grado_numero      INT NOT NULL,
    letra_seccion     VARCHAR(20) NOT NULL,
    profesor_lider_id INT REFERENCES tab_profesores(id_profesor),
    CONSTRAINT unique_grado_seccion UNIQUE (grado_numero, letra_seccion)
);

CREATE TABLE tab_cursos (
    id_curso      SERIAL PRIMARY KEY,
    nombre_curso  VARCHAR(100) NOT NULL,
    grado         INT          NOT NULL,
    descripcion   TEXT,
    objetivo      TEXT,
    indicador_logro TEXT
);

CREATE TABLE profesor_grado (
    id_profesor INT NOT NULL,
    id_grado INT NOT NULL,
    PRIMARY KEY (id_profesor, id_grado),
    FOREIGN KEY (id_profesor) REFERENCES tab_profesores(id_profesor) ON DELETE CASCADE,
    FOREIGN KEY (id_grado) REFERENCES tab_grados(id_seccion) ON DELETE CASCADE
);

CREATE TABLE tab_matriculas (
    id_matricula    SERIAL PRIMARY KEY,
    id_estud        INT NOT NULL REFERENCES tab_estudiante(id_ficha),
    id_curso        INT NOT NULL REFERENCES tab_cursos(id_curso),
    fecha_matricula DATE NOT NULL DEFAULT CURRENT_DATE
);

CREATE TABLE periodos_academicos (
    id_periodo SERIAL PRIMARY KEY,
    nombre_periodo VARCHAR(100) NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NOT NULL,
    estado VARCHAR(20) DEFAULT 'Activo'
);

CREATE TABLE tab_calificaciones (
    id_calificacion   SERIAL PRIMARY KEY,
    id_estud          INT NOT NULL REFERENCES tab_estudiante(id_ficha),
    id_curso          INT NOT NULL REFERENCES tab_cursos(id_curso),
    id_profesor       INT NOT NULL REFERENCES tab_profesores(id_profesor),
    calificacion      DECIMAL(3, 2) NOT NULL,
    tipo_evaluacion   tipo_evaluacion_enum NOT NULL,
    fecha             DATE NOT NULL DEFAULT CURRENT_DATE,
    comentario        TEXT,
    id_periodo        INT,
    cognitiva         DECIMAL(3,2),
    autoevaluacion    DECIMAL(3,2),
    coevaluacion      DECIMAL(3,2),
    heteroevaluacion  DECIMAL(3,2),
    FOREIGN KEY (id_periodo) REFERENCES periodos_academicos(id_periodo)
);

CREATE TABLE tab_asistencia (
    id_asistencia SERIAL PRIMARY KEY,
    id_estud      INT NOT NULL REFERENCES tab_estudiante(id_ficha),
    id_profesor   INT NOT NULL REFERENCES tab_profesores(id_profesor),
    fecha_hora    TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP,
    estado        VARCHAR(20) NOT NULL CHECK (estado IN ('presente', 'ausente', 'justificado')),
    excusa_medica_url VARCHAR(255)
);

CREATE TABLE tab_comunicaciones (
    id_comunicacion    SERIAL PRIMARY KEY,
    id_estud           INT NOT NULL REFERENCES tab_estudiante(id_ficha),
    id_profesor        INT NOT NULL REFERENCES tab_profesores(id_profesor),
    mensaje            TEXT NOT NULL,
    fecha_envio        TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE tab_pqrsf (
    id_pqrsf             SERIAL PRIMARY KEY,
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
    id                 SERIAL PRIMARY KEY,
    usuario_id         INT NOT NULL REFERENCES login(id_log) ON DELETE CASCADE,
    tipo_evento        VARCHAR(50) NOT NULL,
    id_profesor        INT REFERENCES tab_profesores(id_profesor),
    nombre             VARCHAR(255) NOT NULL,
    descripcion        TEXT,
    fecha_inicio       DATE NOT NULL,
    fecha_fin          DATE NOT NULL,
    hora_inicio        TIME NOT NULL,
    hora_fin           TIME NOT NULL,
    color              VARCHAR(20),
    target_roles       VARCHAR(255) DEFAULT '',
    target_ids         TEXT DEFAULT '',
    estado             VARCHAR(20) DEFAULT 'Pendiente',
    comentarios        TEXT,
    archivo_adjunto    BYTEA,
    creado             TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS active_sessions (
    id_log INT PRIMARY KEY,
    session_id VARCHAR(255) NOT NULL,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_active_sessions_login FOREIGN KEY (id_log) REFERENCES login(id_log) ON DELETE CASCADE
);

CREATE TABLE tab_seguridad_respuestas (
    id_respuesta SERIAL PRIMARY KEY,
    id_log INT NOT NULL REFERENCES login(id_log) ON DELETE CASCADE,
    pregunta VARCHAR(255) NOT NULL,
    respuesta_hash VARCHAR(255) NOT NULL
);

CREATE TABLE tab_password_reset (
    id_reset SERIAL PRIMARY KEY,
    id_log INT NOT NULL REFERENCES login(id_log) ON DELETE CASCADE,
    token VARCHAR(255) NOT NULL UNIQUE,
    fecha_expiracion TIMESTAMP WITH TIME ZONE NOT NULL,
    utilizado BOOLEAN DEFAULT FALSE
);

CREATE TABLE tab_profesor_curso (
    id_profesor INT NOT NULL REFERENCES tab_profesores(id_profesor) ON DELETE CASCADE,
    id_seccion INT NOT NULL REFERENCES tab_grados(id_seccion) ON DELETE CASCADE,
    PRIMARY KEY (id_profesor, id_seccion)
);

-- ----------------------------------------------------------------------------
-- FASE 4: CREACIÓN DE ÍNDICES PARA OPTIMIZACIÓN
-- ----------------------------------------------------------------------------

CREATE INDEX IF NOT EXISTS idx_login_usuario ON login(usuario);
CREATE INDEX IF NOT EXISTS idx_accesos_usuario_id ON accesos(usuario_id);
CREATE INDEX IF NOT EXISTS idx_estudiantes_no_documento ON tab_estudiante(no_documento);
CREATE INDEX IF NOT EXISTS idx_acudiente_email ON tab_acudiente(email);
CREATE INDEX IF NOT EXISTS idx_calificaciones_estudiante ON tab_calificaciones(id_estud);
CREATE INDEX IF NOT EXISTS idx_asistencia_estudiante ON tab_asistencia(id_estud);

-- ----------------------------------------------------------------------------
-- FASE 5: INSERCIÓN DE DATOS CORREGIDOS
-- ----------------------------------------------------------------------------
-- Poblando la tabla 'login' (todos los usuarios primero)
INSERT INTO login (id_log, usuario, contrasena, rol) VALUES
(1, '45687', crypt('ANJO458', gen_salt('bf')), 'admin'),
(2, '2008', crypt('KDMS1406', gen_salt('bf')), 'admin'),
(3, '67891', crypt('LZQO226', gen_salt('bf')), 'admin'),
(4, '101', crypt('est1293', gen_salt('bf')), 'estudiante'),
(5, '102', crypt('admin456', gen_salt('bf')), 'admin'),
(6, '201', crypt('adm123', gen_salt('bf')), 'admin'),
(7, '202', crypt('adm456', gen_salt('bf')), 'admin'),
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
(19, '005', crypt('profesor', gen_salt('bf')), 'profesor'),
(20, '2508', crypt('KHATE258', gen_salt('bf')), 'admin')
ON CONFLICT (id_log) DO NOTHING;

-- Poblando la tabla 'tab_administradores' (ahora sí, después de login)
INSERT INTO tab_administradores (id_administrador, id_log, nombres, apellidos, tipo_documento, no_documento, fecha_expedicion, fecha_nacimiento, email, telefono, direccion, cargo, eps, rh, alergias, sexo) VALUES
(1, 1, 'Andrea Gabriela', 'Jaimes Oviedo', 'CC', '100000001', '2010-01-01', '1985-05-10', 'andrea.j@example.com', '3001112233', 'Calle 10 #1-1', 'Administrador General', 'EPS Sura', 'O+', 'Ninguna', 'Femenino'),
(2, 2, 'Keiner Daniel', 'Bautista Angarita', 'CC', '100000002', '2012-03-15', '1988-08-20', 'keiner.b@example.com', '3004445566', 'Carrera 20 #2-2', 'Administrador de Sistemas', 'EPS Compensar', 'A-', 'Polen', 'Masculino'),
(3, 3, 'Sofia', 'Gomez', 'CC', '100000003', '2015-07-20', '1992-03-01', 'sofia.g@example.com', '3007778899', 'Avenida 30 #3-3', 'Administrador Financiero', 'EPS Sanitas', 'B+', 'Ninguna', 'Femenino'),
(4, 5, 'Usuario', 'Admin 1', 'CC', '102', '2020-01-01', '1990-01-01', 'admin1@example.com', '3100000001', 'Dirección Admin 1', 'Administrativo', 'EPS Genérica', 'O+', 'Ninguna', 'Otro'),
(5, 6, 'Usuario', 'Admin 2', 'CC', '201', '2020-01-01', '1990-01-01', 'admin2@example.com', '3100000002', 'Dirección Admin 2', 'Administrativo', 'EPS Genérica', 'O+', 'Ninguna', 'Otro'),
(6, 7, 'Usuario', 'Admin 3', 'CC', '202', '2020-01-01', '1990-01-01', 'admin3@example.com', '3100000003', 'Dirección Admin 3', 'Administrativo', 'EPS Genérica', 'O+', 'Ninguna', 'Otro')
ON CONFLICT (id_administrador) DO NOTHING;

-- Poblando la tabla 'tab_acudiente'
INSERT INTO tab_acudiente (id_acudiente, id_log, parentesco, apellidos, nombres, tipo_documento, no_documento, ciudad_expedicion, fecha_expedicion, nacionalidad, sexo, rh, alergias, fecha_nacimiento, direccionp, lugar_recidencia, telefono, email, religion, nivel_estudio, profesion, empresa, ocupacion, afiliado, afi_detalles, barrio, estado_civil, eps) VALUES
(1, 1, 'Tutor', 'Beltran Gomez', 'Pablo Palo', 'Cedula', '192564575', 'Bucaramanga', '2010-01-12', 'Colombiana', 'M', 'O+', 'Ninguna', '1990-01-12', 'Cra avenidas 6-3', 'Puñaladas', '745', 'palitoq@gmail.com', 'no aplica', 'universidad', 'empresario', 'puñosñocos', 'empresario', 'Si', 'no se pa', 'Chapinero', 'Soltero(a)', 'EPS Sanitas'),
(2, 2, 'Madre', 'López Rodríguez', 'Ana María', 'Cédula', '1098554321', 'Medellín', '2005-08-22', 'Colombiana', 'Femenino', 'A+', 'Ninguna', '1985-08-22', 'Carrera 45 #67-89', 'Medellín', '5565431', 'ana.lopez@example.com', 'Cristiana', 'Técnico', 'Diseñadora Gráfica', 'Creative Designs', 'Diseñadora Gráfica', 'Si', 'EPS Sura', 'Poblado', 'Casado(a)', 'EPS Sura'),
(3, 3, 'Tutor', 'Martínez Sánchez', 'Juan Pablo', 'Cédula', '4567891523', 'Cali', '1995-03-10', 'Colombiana', 'Masculino', 'B+', 'Ninguna', '1975-03-10', 'Avenida 6 #78-90', 'Cali', '34556789', 'juan.martinez@example.com', 'Ateo', 'Secundaria', 'Comerciante', 'Tienda Martínez', 'Comerciante', 'No', 'N/A', 'Granada', 'Unión Libre', 'N/A'),
(4, 4, 'Madre', 'Nico Gomez', 'Maria Loza', 'Cedula', '174464575', 'Bucaramanga', '2018-08-01', 'Colombiana', 'F', 'O+', 'Ninguna', '1998-08-01', 'Cra 22# 6-3', 'Patadas', '745', 'paquino@gmail.com', 'no aplica', 'universidad', 'empresaria', 'puñosñocos', 'empresaria', 'No', 'no se pa', 'Sotomayor', 'Soltero(a)', 'N/A'),
(5, 5, 'Padre', 'Martínez Lopez', 'Juan Carlo', 'Cédula', '4567236789', 'California', '2015-03-10', 'Colombiana', 'Masculino', 'O+', 'Ninguna', '1995-03-10', 'Avenida 9 #70-00', 'California', '34556889', 'juan@example.com', 'Ateo', 'Bachiller', 'Comerciante', 'Tienda Esquina', 'Comerciante', 'No', 'N/A', 'El Prado', 'Casado(a)', 'N/A'),
(6, 6, 'Tutor', 'Diaz', 'Marco Ubaldo', 'Cédula', '1098588921', 'Medellín', '2015-04-28', 'Colombiana', 'M', 'B+', 'Ninguna', '1995-04-28', 'Calle 56 #17-09', 'Medellín', '554551', 'diaz@example.com', 'No aplica', 'Técnico', 'Caballero', 'Palace', 'Caballero', 'Si', 'N/A', 'Laureles', 'Soltero(a)', 'EPS Compensar')
ON CONFLICT (id_acudiente) DO NOTHING;

-- Poblando la tabla 'tab_estudiante'
INSERT INTO tab_estudiante (id_ficha, id_acudiente, nombres, apellido1, apellido2, tipo_documento, no_documento, id_seccion, grado, ciudad_expedicion, fecha_nacimiento, fecha_expedicion, pais_ori, nacionalidad, sexo, rh, ciudad_nacimiento, direccion, barrio, telefonos, email, vivecon, estratosocieconomico, gruposisben, numhermanos, hermanoscole, enfermedad, eps, alergias, discapacidad, etnia, desplazado, fecha) VALUES
(1, 1, 'Juan', 'Perez', 'Gomez', 'TI', '1001', 1, '6', 'Bucaramanga', '2010-05-15', '2020-01-01', 'Colombia', 'Colombiana', 'Masculino', 'O+', 'Bucaramanga', 'Calle 1 #1-1', 'Centro', '3101234567', 'juan.perez@example.com', 'Padres', 3, 'A1', 1, 0, 'Ninguna', 'EPS Sura', 'Ninguna', 'Ninguna', 'Mestizo', 'No', '2023-01-01'),
(2, 2, 'Maria', 'Lopez', 'Diaz', 'TI', '1002', 4, '7', 'Medellín', '2009-03-20', '2019-01-01', 'Colombia', 'Colombiana', 'Femenino', 'A+', 'Medellín', 'Carrera 2 #2-2', 'Poblado', '3112345678', 'maria.lopez@example.com', 'Padres', 4, 'B2', 2, 1, 'Asma', 'EPS Compensar', 'Polen', 'Ninguna', 'Mestizo', 'No', '2023-01-01'),
(3, 3, 'Pedro', 'Ramirez', 'Castro', 'TI', '1003', 7, '8', 'Cali', '2008-07-25', '2018-01-01', 'Colombia', 'Colombiana', 'Masculino', 'B+', 'Cali', 'Avenida 3 #3-3', 'Granada', '3123456789', 'pedro.ramirez@example.com', 'Padres', 2, 'C3', 0, 0, 'Ninguna', 'EPS Sanitas', 'Ninguna', 'Ninguna', 'Mestizo', 'No', '2023-01-01'),
(4, 4, 'Ana', 'Gomez', 'Silva', 'TI', '1004', 10, '9', 'Bucaramanga', '2007-09-10', '2017-01-01', 'Colombia', 'Colombiana', 'Femenino', 'O-', 'Bucaramanga', 'Calle 4 #4-4', 'Sotomayor', '3134567890', 'ana.gomez@example.com', 'Padres', 3, 'A1', 1, 0, 'Ninguna', 'EPS Sura', 'Ninguna', 'Ninguna', 'Mestizo', 'No', '2023-01-01'),
(5, 5, 'Luis', 'Diaz', 'Vargas', 'TI', '1005', 13, '10', 'California', '2006-11-30', '2016-01-01', 'Colombia', 'Colombiana', 'Masculino', 'AB+', 'California', 'Carrera 5 #5-5', 'El Prado', '3145678901', 'luis.diaz@example.com', 'Padres', 5, 'D4', 3, 2, 'Diabetes', 'EPS Sanitas', 'Ninguna', 'Ninguna', 'Mestizo', 'No', '2023-01-01'),
(6, 6, 'Sofia', 'Castro', 'Mora', 'TI', '1006', 16, '11', 'Medellín', '2005-01-01', '2015-01-01', 'Colombia', 'Colombiana', 'Femenino', 'B-', 'Medellín', 'Avenida 6 #6-6', 'Laureles', '3156789012', 'sofia.castro@example.com', 'Padres', 4, 'B2', 0, 0, 'Ninguna', 'EPS Compensar', 'Ninguna', 'Ninguna', 'Mestizo', 'No', '2023-01-01'),
(7, 1, 'Carlos', 'Torres', 'Rojas', 'TI', '1007', 1, '6', 'Bucaramanga', '2010-02-02', '2020-02-02', 'Colombia', 'Colombiana', 'Masculino', 'A+', 'Bucaramanga', 'Calle 7 #7-7', 'Centro', '3167890123', 'carlos.torres@example.com', 'Padres', 3, 'A1', 1, 0, 'Ninguna', 'EPS Sura', 'Ninguna', 'Ninguna', 'Mestizo', 'No', '2023-01-01'),
(8, 2, 'Laura', 'Herrera', 'Blanco', 'TI', '1008', 4, '7', 'Medellín', '2009-04-04', '2019-04-04', 'Colombia', 'Colombiana', 'Femenino', 'O+', 'Medellín', 'Carrera 8 #8-8', 'Poblado', '3178901234', 'laura.herrera@example.com', 'Padres', 4, 'B2', 2, 1, 'Ninguna', 'EPS Compensar', 'Ninguna', 'Ninguna', 'Mestizo', 'No', '2023-01-01'),
(9, 3, 'Diego', 'Morales', 'Negro', 'TI', '1009', 7, '8', 'Cali', '2008-06-06', '2018-06-06', 'Colombia', 'Colombiana', 'Masculino', 'B-', 'Cali', 'Avenida 9 #9-9', 'Granada', '3189012345', 'diego.morales@example.com', 'Padres', 2, 'C3', 0, 0, 'Ninguna', 'EPS Sanitas', 'Ninguna', 'Ninguna', 'Mestizo', 'No', '2023-01-01'),
(10, 4, 'Valeria', 'Ruiz', 'Verde', 'TI', '1010', 10, '9', 'Bucaramanga', '2007-08-08', '2017-08-08', 'Colombia', 'Colombiana', 'Femenino', 'AB-', 'Bucaramanga', 'Calle 10 #10-10', 'Sotomayor', '3190123456', 'valeria.ruiz@example.com', 'Padres', 3, 'A1', 1, 0, 'Ninguna', 'EPS Sura', 'Ninguna', 'Ninguna', 'Mestizo', 'No', '2023-01-01')
ON CONFLICT (id_ficha) DO NOTHING;

-- Poblando la tabla 'tab_materias'
INSERT INTO tab_materias (id_materia, codigo, nombre, descripcion, fecha_creacion) VALUES
(1, 'CN-BIO', 'Biología', 'Ciencias Naturales y Educación Ambiental: Biología.', '2023-01-15'),
(2, 'CN-FIS', 'Física', 'Ciencias Naturales y Educación Ambiental: Física.', '2023-01-15'),
(3, 'CN-QUI', 'Química', 'Ciencias Naturales y Educación Ambiental: Química.', '2023-01-15'),
(4, 'CS-HIS', 'Historia', 'Ciencias Sociales: Historia.', '2023-01-15'),
(5, 'CS-GEO', 'Geografía', 'Ciencias Sociales: Geografía.', '2023-01-15'),
(6, 'CS-CON', 'Constitución Política', 'Ciencias Sociales: Constitución Política.', '2023-01-15'),
(7, 'CS-DEM', 'Democracia', 'Ciencias Sociales: Democracia.', '2023-01-15'),
(8, 'EA-ART', 'Artes Plásticas', 'Educación Artística: Artes plásticas.', '2023-01-15'),
(9, 'EA-MUS', 'Música', 'Educación Artística: Música.', '2023-01-15'),
(10, 'EA-DAN', 'Danzas', 'Educación Artística: Danzas.', '2023-01-15'),
(11, 'ET-VAL', 'Educación Ética y en Valores Humanos', 'Educación Ética y en Valores Humanos.', '2023-01-15'),
(12, 'EF-DEP', 'Educación Física, Recreación y Deportes', 'Educación Física, Recreación y Deportes.', '2023-01-15'),
(13, 'ER-REL', 'Educación Religiosa', 'Educación Religiosa.', '2023-01-15'),
(14, 'HU-LCA', 'Lengua Castellana', 'Humanidades: Lengua castellana.', '2023-01-15'),
(15, 'HU-IEX', 'Idiomas Extranjeros', 'Humanidades: Idiomas extranjeros.', '2023-01-15'),
(16, 'MA-ARI', 'Aritmética', 'Matemáticas: Aritmética.', '2023-01-15'),
(17, 'MA-ALG', 'Álgebra', 'Matemáticas: Álgebra.', '2023-01-15'),
(18, 'MA-GEO', 'Geometría', 'Matemáticas: Geometría.', '2023-01-15'),
(19, 'MA-TRI', 'Trigonometría', 'Matemáticas: Trigonometría.', '2023-01-15'),
(20, 'MA-CAL', 'Cálculo', 'Matemáticas: Cálculo.', '2023-01-15'),
(21, 'TE-INF', 'Tecnología e Informática', 'Tecnología e Informática.', '2023-01-15'),
(22, 'CP-PAZ', 'Cátedra de la Paz', 'Cátedra de la Paz.', '2023-01-15')
ON CONFLICT (id_materia) DO NOTHING;

-- Poblando la tabla 'tab_profesores'
INSERT INTO tab_profesores (id_profesor, id_log, id_materia, nombres, apellidos, tipo_documento, no_documento, fecha_expedicion, fecha_nacimiento, email, telefono, direccion, nacionalidad, rh, alergias, sexo, especialidad, titulo_academico, eps) VALUES
(1, 8, 1, 'Carlos', 'Gómez', 'CC', '301', '2000-01-01', '1980-01-01', 'carlos.gomez@example.com', '3011234567', 'Calle Falsa 123', 'Colombiana', 'O+', 'Ninguna', 'Masculino', 'Matemáticas y Estadística', 'Licenciado en Matemáticas', 'EPS Sura'),
(2, 9, 2, 'Laura', 'Fernández', 'CC', '302', '2005-05-05', '1985-05-05', 'laura.fernandez@example.com', '3021234567', 'Avenida Siempre Viva 742', 'Colombiana', 'A+', 'Ninguna', 'Femenino', 'Física Teórica', 'Físico Teórico', 'EPS Compensar'),
(3, 19, 14, 'Julian', 'Sánchez', 'CC', '303', '2010-02-15', '1982-10-20', 'julian.sanchez@example.com', '3031234567', 'Calle 50 #30-45', 'Colombiana', 'B+', 'Ninguna', 'Masculino', 'Literatura', 'Licenciado en Literatura', 'EPS Sura'),
(4, NULL, 4, 'Andrea', 'López', 'CC', '304', '2012-08-20', '1978-06-12', 'andrea.lopez@example.com', '3041234567', 'Carrera 25 #10-10', 'Colombiana', 'O-', 'Ninguna', 'Femenino', 'Historia', 'Licenciada en Historia', 'EPS Compensar'),
(5, NULL, 15, 'David', 'Rojas', 'CC', '305', '2018-03-01', '1990-04-05', 'david.rojas@example.com', '3051234567', 'Calle 70 #8-90', 'Colombiana', 'AB+', 'Ninguna', 'Masculino', 'Idiomas', 'Licenciado en Inglés', 'EPS Sanitas')
ON CONFLICT (id_profesor) DO NOTHING;

-- Poblando la tabla 'tab_grados'
INSERT INTO tab_grados (id_seccion, grado_numero, letra_seccion, profesor_lider_id) VALUES
(1, 1, 'A', 1), (2, 1, 'B', NULL), (3, 1, 'C', NULL),
(4, 2, 'A', 2), (5, 2, 'B', NULL), (6, 2, 'C', NULL),
(7, 3, 'A', NULL), (8, 3, 'B', NULL), (9, 3, 'C', NULL),
(10, 4, 'A', NULL), (11, 4, 'B', NULL), (12, 4, 'C', NULL),
(13, 5, 'A', NULL), (14, 5, 'B', NULL), (15, 5, 'C', NULL),
(16, 6, 'A', NULL), (17, 6, 'B', NULL), (18, 6, 'C', NULL),
(19, 7, 'A', NULL), (20, 7, 'B', NULL), (21, 7, 'C', NULL),
(22, 8, 'A', NULL), (23, 8, 'B', NULL), (24, 8, 'C', NULL),
(25, 9, 'A', NULL), (26, 9, 'B', NULL), (27, 9, 'C', NULL),
(28, 10, 'A', NULL), (29, 10, 'B', NULL), (30, 10, 'C', NULL),
(31, 11, 'A', NULL), (32, 11, 'B', NULL), (33, 11, 'C', NULL),
(34, 0, 'Transición A', NULL), (35, 0, 'Transición B', NULL)
ON CONFLICT (id_seccion) DO NOTHING;

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

-- Poblando la tabla 'tab_calificaciones' (IDs de profesor corregidos)
INSERT INTO tab_calificaciones (id_estud, id_curso, id_profesor, calificacion, tipo_evaluacion) VALUES
(1, 1, 1, 4.50, 'Evaluacion Escrita'),
(2, 2, 2, 3.80, 'Evaluacion Escrita'),
(3, 3, 3, 4.20, 'Evaluacion Escrita'),
(4, 4, 4, 3.90, 'Evaluacion Escrita'),
(5, 5, 5, 4.70, 'Evaluacion Escrita');

-- Poblando la tabla 'tab_asistencia' (IDs de profesor corregidos)
INSERT INTO tab_asistencia (id_estud, id_profesor, fecha_hora, estado) VALUES
(1, 1, '2025-02-24 08:00:00', 'presente'),
(2, 1, '2025-02-24 08:00:00', 'ausente'),
(3, 2, '2025-02-24 09:00:00', 'justificado');

-- Poblando la tabla 'tab_comunicaciones' (IDs de profesor corregidos)
INSERT INTO tab_comunicaciones (id_estud, id_profesor, mensaje) VALUES
(2, 5, 'Buenas tardes estimado acudiente, le comunico que el estudiante ha perdido el año. Atentamente, su querido profesor.');

-- Poblando la tabla 'tab_pqrsf'
INSERT INTO tab_pqrsf (tipo, descripcion, estado, nombre_solicitante, contacto_solicitante, destinatario, usuario_id) VALUES
('Queja', 'Reporte de queja por miradas intimidantes en el receso.', 'Pendiente', 'Acudiente Preocupado', 'acudiente@example.com', 'Coordinación', 4);

-- Poblando la tabla 'tab_actividades'
INSERT INTO tab_actividades (id_profesor, nombre, descripcion, estado, comentarios) VALUES
(1, 'Taller de Aritmética', 'Resolver los problemas de la página 20 del libro.', 'Pendiente', 'Entrega para el próximo viernes.');

-- Poblando la tabla 'eventos'
INSERT INTO eventos (usuario_id, tipo_evento, nombre, fecha_inicio, fecha_fin, hora_inicio, hora_fin, color, target_roles, target_ids) VALUES
(1, 'general', 'Reunión General de Padres', '2025-08-01', '2025-08-01', '18:00:00', '20:00:00', '#0D164B', '', '');

-- =============================================================================
-- FIN DEL SCRIPT
-- ===============================================================================