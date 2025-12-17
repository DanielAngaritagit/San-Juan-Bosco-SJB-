import { Router } from 'express';
import PasswordController from '../controllers/passwordController';
import { validateResetEmail, validateChangePassword } from '../middlewares/validationMiddleware';

const router = Router();
const passwordController = new PasswordController();

// Ruta para enviar el correo de restablecimiento de contraseña
router.post('/reset', validateResetEmail, passwordController.sendResetEmail);

// Ruta para cambiar la contraseña
router.post('/change', validateChangePassword, passwordController.changePassword);

export default router;