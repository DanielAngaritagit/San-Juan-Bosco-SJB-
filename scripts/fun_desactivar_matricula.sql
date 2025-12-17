CREATE OR REPLACE FUNCTION fun_gestionar_matricula(p_id_matricula INT,
                                               p_accion VARCHAR) RETURNS TEXT AS 
$$
BEGIN
    -- Verificar si la matrícula existe
    IF NOT EXISTS (SELECT 1 FROM tab_matriculas WHERE id_matricula = p_id_matricula) THEN
        RETURN 'Error: La matrícula no existe';
    END IF;

    -- Manejar la acción solicitada
    CASE p_accion
        WHEN 'activar' THEN
            UPDATE tab_matriculas 
            SET estado = 'activo' 
            WHERE id_matricula = p_id_matricula;
            RETURN 'Matrícula activada con éxito';

        WHEN 'desactivar' THEN
            UPDATE tab_matriculas 
              SET estado = 'inactivo' 
                WHERE id_matricula = p_id_matricula;
                  RETURN 'Matrícula desactivada con éxito';

         WHEN 'eliminar' THEN
            DELETE FROM tab_matriculas 
               WHERE id_matricula = p_id_matricula;
                RETURN 'Matrícula eliminada con éxito';

        ELSE
            RETURN 'Error: Acción no válida. Usa "activar", "desactivar" o "eliminar".';
    END CASE;
END;
$$ LANGUAGE plpgsql;

