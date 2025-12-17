import { getUserByEmail, updatePassword as dbUpdatePassword, saveResetToken } from '../db'; // Importar funciones de la base de datos
import { hash, compare } from 'bcrypt'; // Importar funciones de bcrypt para el hash de contraseñas
import { BadRequestError, NotFoundError } from '../errors'; // Importar errores personalizados
import crypto from 'crypto';

export class PasswordService {
    // Método para validar la existencia del usuario
    async checkUserExists(email: string): Promise<boolean> {
        const user = await getUserByEmail(email);
        return !!user;
    }

    async generateAndSaveResetToken(email: string): Promise<string> {
        const token = crypto.randomBytes(32).toString('hex');
        const expiresAt = new Date(Date.now() + 3600000); // Token válido por 1 hora
        await saveResetToken(email, token, expiresAt);
        return token;
    }

    // Método para verificar que la nueva contraseña cumpla con los requisitos de seguridad
    validateNewPassword(password: string): void {
        const passwordRequirements = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d@$!%*?&]{8,}$/;
        if (!passwordRequirements.test(password)) {
            throw new BadRequestError('La contraseña debe tener al menos 8 caracteres, incluyendo una letra mayúscula, una letra minúscula y un número.');
        }
    }

    // Método para cambiar la contraseña del usuario
    async changePassword(email: string, newPassword: string): Promise<void> {
        const userExists = await this.checkUserExists(email);
        if (!userExists) {
            throw new NotFoundError('Usuario no encontrado');
        }
        this.validateNewPassword(newPassword);
        
        const hashedPassword = await hash(newPassword, 10);
        await dbUpdatePassword(email, hashedPassword);
    }
}