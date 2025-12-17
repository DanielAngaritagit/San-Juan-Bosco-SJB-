-- =============================================================================
-- SCRIPT PARA ELIMINAR TODAS LAS TABLAS Y FUNCIONES DE LA BASE DE DATOS SJB
-- Motor: PostgreSQL
-- ADVERTENCIA: Este script eliminará PERMANENTEMENTE todos los datos y la estructura.
-- Úsalo con EXTREMA PRECAUCIÓN, preferiblemente en entornos de desarrollo.
-- =============================================================================

-- ----------------------------------------------------------------------------
-- ELIMINACIÓN DE TABLAS EXISTENTES
-- ----------------------------------------------------------------------------

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
DROP TABLE IF EXISTS accesos CASCADE;
DROP TABLE IF EXISTS login CASCADE;
DROP TABLE IF EXISTS periodos_academicos CASCADE;

-- ----------------------------------------------------------------------------
-- ELIMINACIÓN DE FUNCIONES (SI EXISTEN)
-- ----------------------------------------------------------------------------
-- Si tienes funciones personalizadas en tu base de datos, puedes añadirlas aquí.
-- Por ejemplo: DROP FUNCTION IF EXISTS nombre_de_tu_funcion(tipo_arg1, tipo_arg2) CASCADE;
-- Como no tengo acceso a la lista de funciones de tu base de datos,
-- no puedo incluirlas automáticamente.
-- Asegúrate de listar todas las funciones que deseas eliminar.

-- Ejemplo (descomenta y modifica si es necesario):
-- DROP FUNCTION IF EXISTS actualizar_promedio_estudiante(INT) CASCADE;
-- DROP FUNCTION IF EXISTS obtener_calificaciones_por_periodo(INT, INT) CASCADE;

-- ----------------------------------------------------------------------------
-- FIN DEL SCRIPT DE ELIMINACIÓN
-- =============================================================================
