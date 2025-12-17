CREATE OR REPLACE FUNCTION  registrarAcceso(p_usuario_id INTEGER,
                                            p_direccion_ip VARCHAR(45) DEFAULT NULL,
                                            p_agente_usuario TEXT DEFAULT NULL)RETURNS BOOLEAN AS
$$
DECLARE
    v_resultado BOOLEAN := FALSE;
BEGIN
    -- Registrar el acceso con información detallada
    INSERT INTO accesos (
        usuario_id,
        fecha_acceso,
        direccion_ip,
        agente_usuario,
        tipo_acceso
    ) VALUES (
        p_usuario_id,
        NOW(),
        COALESCE(p_direccion_ip, inet_client_addr()),
        COALESCE(p_agente_usuario, current_setting('application_name')),
        'login'
    ) RETURNING TRUE INTO v_resultado;
    
    -- Actualizar último acceso en la tabla usuarios
    UPDATE usuarios 
    SET ultimo_acceso = NOW(),
        contador_accesos = contador_accesos + 1
    WHERE id = p_usuario_id;
    
    RETURN v_resultado;
EXCEPTION
    WHEN OTHERS THEN
        RAISE WARNING 'Error al registrar acceso: %', SQLERRM;
        RETURN FALSE;
END;
$$ 
LANGUAGE plpgsql;