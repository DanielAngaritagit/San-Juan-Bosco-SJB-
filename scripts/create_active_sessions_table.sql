CREATE TABLE IF NOT EXISTS active_sessions (
    id_log INT PRIMARY KEY,
    session_id VARCHAR(255) NOT NULL,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_active_sessions_login FOREIGN KEY (id_log) REFERENCES login(id_log) ON DELETE CASCADE
);
