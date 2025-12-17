import { Request, Response } from 'express';
import { EmailService } from '../services/emailService';
import { PasswordService } from '../services/passwordService';

export class PasswordController {
    private emailService: EmailService;
    private passwordService: PasswordService;

    constructor() {
        this.emailService = new EmailService();
        this.passwordService = new PasswordService();
    }

    public async sendResetEmail(req: Request, res: Response): Promise<Response> {
        const { email } = req.body;

        try {
            const userExists = await this.passwordService.checkUserExists(email);
            if (!userExists) {
                return res.status(404).json({ message: 'User not found' });
            }

            const token = await this.passwordService.generateAndSaveResetToken(email);
            await this.emailService.sendResetEmail(email, token);
            return res.status(200).json({ message: 'Reset email sent successfully' });
        } catch (error) {
            return res.status(500).json({ message: 'Error sending reset email', error });
        }
    }

    public async changePassword(req: Request, res: Response): Promise<Response> {
        const { email, newPassword } = req.body;

        try {
            const userExists = await this.passwordService.checkUserExists(email);
            if (!userExists) {
                return res.status(404).json({ message: 'User not found' });
            }

            const isValid = this.passwordService.validatePassword(newPassword);
            if (!isValid) {
                return res.status(400).json({ message: 'Password does not meet security requirements' });
            }

            await this.passwordService.updatePassword(email, newPassword);
            return res.status(200).json({ message: 'Password changed successfully' });
        } catch (error) {
            return res.status(500).json({ message: 'Error changing password', error });
        }
    }
}