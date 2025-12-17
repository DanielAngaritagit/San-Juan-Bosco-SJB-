import nodemailer from 'nodemailer';
import { config } from 'dotenv';

config();

export class EmailService {
    private transporter: nodemailer.Transporter;

    constructor() {
        this.transporter = nodemailer.createTransport({
            host: process.env.EMAIL_HOST,
            port: Number(process.env.EMAIL_PORT),
            secure: process.env.EMAIL_SECURE === 'true',
            auth: {
                user: process.env.EMAIL_USER,
                pass: process.env.EMAIL_PASS,
            },
        });
    }

    async sendResetEmail(to: string, token: string): Promise<void> {
        const mailOptions = {
            from: '"Support" <support@example.com>', // Cambiar por el correo electrónico real
            to,
            subject: 'Restablecimiento de contraseña',
            text: `Haga clic en el siguiente enlace para restablecer su contraseña: ${process.env.RESET_PASSWORD_URL}?token=${token}`,
            html: `<p>Haga clic en el siguiente enlace para restablecer su contraseña:</p><a href="${process.env.RESET_PASSWORD_URL}?token=${token}">Restablecer contraseña</a>`,
        };

        await this.transporter.sendMail(mailOptions);
    }
}