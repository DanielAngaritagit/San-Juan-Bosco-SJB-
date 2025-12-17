export interface PasswordResetRequest {
    email: string;
}

export interface ChangePasswordRequest {
    userId: number;
    oldPassword: string;
    newPassword: string;
}

export interface ApiResponse<T> {
    success: boolean;
    message: string;
    data?: T;
}