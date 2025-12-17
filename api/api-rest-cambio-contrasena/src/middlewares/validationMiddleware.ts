import { Request, Response, NextFunction } from 'express';

const validatePasswordChange = (req: Request, res: Response, next: NextFunction) => {
    const { email, newPassword, confirmPassword } = req.body;

    // Check if email is provided
    if (!email) {
        return res.status(400).json({ message: 'Email is required.' });
    }

    // Check if new password is provided
    if (!newPassword) {
        return res.status(400).json({ message: 'New password is required.' });
    }

    // Check if confirm password is provided
    if (!confirmPassword) {
        return res.status(400).json({ message: 'Confirm password is required.' });
    }

    // Check if new password and confirm password match
    if (newPassword !== confirmPassword) {
        return res.status(400).json({ message: 'Passwords do not match.' });
    }

    // Check password strength (example: at least 8 characters, 1 uppercase, 1 number)
    const passwordStrengthRegex = /^(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{8,}$/;
    if (!passwordStrengthRegex.test(newPassword)) {
        return res.status(400).json({ message: 'Password must be at least 8 characters long, contain at least one uppercase letter and one number.' });
    }

    next();
};

export default validatePasswordChange;