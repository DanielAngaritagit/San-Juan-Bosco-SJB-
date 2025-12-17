CREATE OR REPLACE FUNCTION obtener_historial_accesos(p_usuario_id INTEGER,
                                                     p_limite INTEGER DEFAULT 10,
                                                     p_pagina INTEGER DEFAULT 1)RETURNS TABLE (
                                                                                id_ace INT,
                                                                                fecha_acceso TIMESTAMP,
                                                                                direccion_ip VARCHAR(45),
                                                                                agente_usuario TEXT,
                                                                                tipo_acceso VARCHAR(20)) AS
$$
BEGIN
    RETURN QUERY
    SELECT 
        a.id,
        a.fecha_acceso,
        a.direccion_ip,
        a.agente_usuario,
        a.tipo_acceso
    FROM 
        accesos a
    WHERE 
        a.usuario_id = p_usuario_id
    ORDER BY 
        a.fecha_acceso DESC
    LIMIT 
        p_limite
    OFFSET 
        (p_pagina - 1) * p_limite;
END;
$$
LANGUAGE plpgsql;