# API REST para Cambio de Contraseña

Este proyecto proporciona una API REST para gestionar el cambio de contraseñas de usuarios. Permite enviar correos electrónicos de restablecimiento de contraseña y cambiar contraseñas de manera segura.

## Estructura del Proyecto

```
api-rest-cambio-contrasena
├── src
│   ├── app.ts                     # Punto de entrada de la aplicación
│   ├── controllers
│   │   └── passwordController.ts   # Controlador para la gestión de contraseñas
│   ├── routes
│   │   └── passwordRoutes.ts       # Rutas para la gestión de contraseñas
│   ├── services
│   │   ├── emailService.ts         # Servicio para el envío de correos electrónicos
│   │   └── passwordService.ts       # Servicio para la gestión de contraseñas
│   ├── middlewares
│   │   └── validationMiddleware.ts  # Middleware para la validación de datos
│   ├── db
│   │   └── index.ts                # Conexión a la base de datos
│   └── types
│       └── index.ts                # Tipos e interfaces del proyecto
├── package.json                    # Configuración de npm
├── tsconfig.json                   # Configuración de TypeScript
└── README.md                       # Documentación del proyecto
```

## Instalación

1. Clona el repositorio:
   ```
   git clone <URL_DEL_REPOSITORIO>
   ```
2. Navega al directorio del proyecto:
   ```
   cd api-rest-cambio-contrasena
   ```
3. Instala las dependencias:
   ```
   npm install
   ```

## Uso

1. Inicia el servidor:
   ```
   npm start
   ```
2. La API estará disponible en `http://localhost:3000`.

## Endpoints

- `POST /api/password/reset`: Envía un correo electrónico para restablecer la contraseña.
- `POST /api/password/change`: Cambia la contraseña del usuario.

## Contribuciones

Las contribuciones son bienvenidas. Por favor, abre un issue o envía un pull request para discutir cambios.

## Licencia

Este proyecto está bajo la Licencia MIT.