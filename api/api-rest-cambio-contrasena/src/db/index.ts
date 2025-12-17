import { Pool } from 'pg';
import { config } from 'dotenv';

config();

const pool = new Pool({
  user: process.env.DB_USER,
  host: process.env.DB_HOST,
  database: process.env.DB_NAME,
  password: process.env.DB_PASSWORD,
  port: Number(process.env.DB_PORT),
});

const createPasswordResetTokensTable = async () => {
  await pool.query(`
    CREATE TABLE IF NOT EXISTS password_reset_tokens (
      user_email VARCHAR(255) NOT NULL,
      token VARCHAR(255) NOT NULL UNIQUE,
      expires_at TIMESTAMP NOT NULL
    );
  `);
};

createPasswordResetTokensTable();

const getUserByEmail = async (email: string) => {
  const result = await pool.query('SELECT * FROM login WHERE usuario = $1', [email]);
  return result.rows[0];
};

const updatePassword = async (email: string, newPassword: string) => {
  const result = await pool.query('UPDATE login SET contrasena = $1 WHERE usuario = $2', [newPassword, email]);
  return result.rowCount > 0;
};

const saveResetToken = async (email: string, token: string, expiresAt: Date) => {
  await pool.query('INSERT INTO password_reset_tokens (user_email, token, expires_at) VALUES ($1, $2, $3) ON CONFLICT (user_email) DO UPDATE SET token = $2, expires_at = $3', [email, token, expiresAt]);
};

const getResetToken = async (token: string) => {
  const result = await pool.query('SELECT * FROM password_reset_tokens WHERE token = $1 AND expires_at > NOW()', [token]);
  return result.rows[0];
};

const deleteResetToken = async (token: string) => {
  await pool.query('DELETE FROM password_reset_tokens WHERE token = $1', [token]);
};

export { pool, getUserByEmail, updatePassword, saveResetToken, getResetToken, deleteResetToken };