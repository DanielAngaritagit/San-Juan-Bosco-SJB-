CREATE OR REPLACE FUNCTION estadisticas_usuarios() 
RETURNS TABLE (estudiantes BIGINT, profesores BIGINT, padres BIGINT) AS $$
BEGIN
    RETURN QUERY 
    SELECT 
        (SELECT COUNT(*) FROM tab_estudiante) AS estudiantes,
        (SELECT COUNT(*) FROM tab_profesores) AS profesores,
        (SELECT COUNT(*) FROM tab_acudiente) AS padres;
END;
$$ 
LANGUAGE plpgsql;