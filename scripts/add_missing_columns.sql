-- Script para añadir columnas faltantes a las tablas de la base de datos

-- Añadir columna 'nacionalidad' a la tabla tab_acudiente
ALTER TABLE tab_acudiente
ADD COLUMN IF NOT EXISTS nacionalidad VARCHAR(100);

-- Añadir columna 'rh' a la tabla tab_acudiente
ALTER TABLE tab_acudiente
ADD COLUMN IF NOT EXISTS rh VARCHAR(10);

-- Añadir columna 'nacionalidad' a la tabla tab_profesores
ALTER TABLE tab_profesores
ADD COLUMN IF NOT EXISTS nacionalidad VARCHAR(100);

-- Añadir columna 'rh' a la tabla tab_profesores
ALTER TABLE tab_profesores
ADD COLUMN IF NOT EXISTS rh VARCHAR(10);

-- Añadir columna 'grado' a la tabla tab_ficha_datos_estudiante
ALTER TABLE tab_ficha_datos_estudiante
ADD COLUMN IF NOT EXISTS grado VARCHAR(50);

-- Añadir columna 'rh' a la tabla tab_ficha_datos_estudiante_salud
-- Se asume que ya existe, pero se asegura el tipo si no
ALTER TABLE tab_ficha_datos_estudiante_salud
ADD COLUMN IF NOT EXISTS rh VARCHAR(10);

-- Añadir columna 'alergias' a la tabla tab_ficha_datos_estudiante_salud
ALTER TABLE tab_ficha_datos_estudiante_salud
ADD COLUMN IF NOT EXISTS alergias TEXT;

-- Añadir columna 'discapacidad' a la tabla tab_ficha_datos_estudiante_salud
ALTER TABLE tab_ficha_datos_estudiante_salud
ADD COLUMN IF NOT EXISTS discapacidad TEXT;

-- Mensaje de confirmación (opcional, depende de cómo ejecutes el script)
SELECT 'Columnas añadidas o ya existentes correctamente.' AS status;