--DROP FUNCTION IF EXISTS fun_login
CREATE OR REPLACE FUNCTION fun_login(p_usuario VARCHAR(50),
                                     p_contrasena VARCHAR) RETURNS TABLE (resultado VARCHAR,
                                                                          mensaje VARCHAR,
                                                                          rol_usuario VARCHAR,
                                                                          usuario_id INT) AS 
$$
DECLARE
    v_usuario RECORD;
BEGIN
    -- Buscar usuario activo
    SELECT id_log, usuario, contrasena, rol
    INTO v_usuario
    FROM login
    WHERE usuario = p_usuario AND activo = TRUE;
    
    -- Verificar si existe
    IF NOT FOUND THEN
        RETURN QUERY SELECT 
            'error'::VARCHAR,
            'Usuario no encontrado o cuenta inactiva'::VARCHAR,
            NULL::VARCHAR,
            NULL::INT;
        RETURN;
    END IF;
    
    -- Verificar contraseña (usando crypt para contraseñas hasheadas)
    IF v_usuario.contrasena <> crypt(p_contrasena, v_usuario.contrasena) THEN
        RETURN QUERY SELECT 
            'error'::VARCHAR,
            'Contraseña incorrecta'::VARCHAR,
            NULL::VARCHAR,
            NULL::INT;
        RETURN;
    END IF;
    
    -- Credenciales válidas
    RETURN QUERY SELECT 
        'success'::VARCHAR,
        'Autenticación exitosa'::VARCHAR,
        v_usuario.rol::VARCHAR,
        v_usuario.id_log::INT;
END;
$$ 
LANGUAGE plpgsql;
--SELECT fun_login('admin@colegio.com', 'password123');