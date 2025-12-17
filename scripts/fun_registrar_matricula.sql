CREATE OR REPLACE FUNCTION fun_registrar_matricula(p_id_ficha INT,
                                               p_id_curso INT) RETURNS TEXT AS 
$$
DECLARE
    nuevo_id INT;
    mensaje TEXT;
BEGIN
    -- Verificar si el estudiante existe
    IF NOT EXISTS (SELECT 1 FROM tab_ficha_datos_estudiante WHERE id_ficha = p_id_ficha) THEN
        RETURN 'Error: El estudiante no existe';
    END IF;
    
    -- Verificar si el curso existe
    IF NOT EXISTS (SELECT 1 FROM tab_cursos WHERE id_curso = p_id_curso) THEN
        RETURN 'Error: El curso no existe';
    END IF;
    
    -- Insertar la matrícula
    INSERT INTO tab_matriculas (id_matricula, id_ficha, id_curso, fecha_matricula)
         VALUES (nextval('tab_matriculas_id_matricula_seq'), p_id_ficha, p_id_curso, NOW())
            RETURNING id_matricula INTO nuevo_id;
    
              mensaje := 'Matrícula registrada con éxito. ID: ' || nuevo_id;
                 RETURN mensaje;
END;
$$ LANGUAGE plpgsql;
