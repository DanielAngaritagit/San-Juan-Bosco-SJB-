-- Creación de la tabla para almacenar las conversaciones del chatbot
CREATE TABLE tab_chatbot_conversations (
    id SERIAL PRIMARY KEY,
    id_usuario VARCHAR(255),
    rol VARCHAR(50),
    mensaje TEXT NOT NULL,
    emisor VARCHAR(50) NOT NULL, -- 'usuario' o 'bot'
    fecha_hora TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

COMMENT ON TABLE tab_chatbot_conversations IS 'Almacena el historial de conversaciones del chatbot institucional.';
COMMENT ON COLUMN tab_chatbot_conversations.id IS 'Identificador único de cada mensaje.';
COMMENT ON COLUMN tab_chatbot_conversations.id_usuario IS 'ID del usuario que interactúa con el chatbot (si está logueado).';
COMMENT ON COLUMN tab_chatbot_conversations.rol IS 'Rol del usuario que interactúa con el chatbot (si está logueado).';
COMMENT ON COLUMN tab_chatbot_conversations.mensaje IS 'Contenido del mensaje.';
COMMENT ON COLUMN tab_chatbot_conversations.emisor IS 'Indica si el mensaje fue enviado por el "usuario" o por el "bot".';
COMMENT ON COLUMN tab_chatbot_conversations.fecha_hora IS 'Fecha y hora en que se registró el mensaje.';
