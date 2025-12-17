CREATE OR REPLACE FUNCTION estadisticas_accesos(p_usuario_id INTEGER DEFAULT NULL)RETURNS TABLE (total_accesos BIGINT,
                                                                                                 primer_acceso TIMESTAMP,
                                                                                                 ultimo_acceso TIMESTAMP,
                                                                                                 ips_distintas BIGINT,
                                                                                                 dispositivos_distintos BIGINT) AS 
$$
BEGIN
    RETURN QUERY
    SELECT
        COUNT(*)::BIGINT AS total_accesos,
        MIN(fecha_acceso) AS primer_acceso,
        MAX(fecha_acceso) AS ultimo_acceso,
        COUNT(DISTINCT direccion_ip)::BIGINT AS ips_distintas,
        COUNT(DISTINCT agente_usuario)::BIGINT AS dispositivos_distintos
    FROM
        accesos
    WHERE
        (p_usuario_id IS NULL OR usuario_id = p_usuario_id);
END;
$$
LANGUAGE plpgsql;