# Proyecto SJB (Sistema de Gestión Escolar)

## 1. Descripción General

El Proyecto SJB es un sistema de gestión escolar basado en la web, diseñado para optimizar y digitalizar los procesos académicos y administrativos del Colegio San Juan Bosco. La plataforma ofrece portales personalizados para diferentes roles de usuario, facilitando la comunicación y el acceso a la información.

## 2. Características Principales

*   **Gestión por Roles:** Sistema de autenticación seguro con roles de usuario definidos:
    *   Administrador
    *   Profesor
    *   Estudiante
    *   Padre de Familia
    *   Administrativo
*   **Portal Público:** Página de inicio con información general del colegio, noticias y eventos.
*   **Interfaz Intuitiva:** Diseño claro y accesible para cada tipo de usuario.
*   **Optimización de Procesos:** Centraliza la información y agiliza las tareas administrativas y académicas.

## 3. Tecnologías Utilizadas

*   **Frontend:**
    *   HTML5
    *   CSS3
    *   JavaScript
*   **Backend:**
    *   PHP
*   **Base de Datos:**
    *   PostgreSQL
*   **Librerías:**
    *   jQuery
    *   Font Awesome

## 4. Estructura del Proyecto

El proyecto está organizado en los siguientes directorios principales:

```
/
|-- admin/                # Panel y lógica para administradores
|-- api/                  # Endpoints de la API para la comunicación backend
|-- css/                  # Estilos CSS globales
|-- estudiante/           # Portal y funcionalidades para estudiantes
|-- js/                   # Scripts de JavaScript para la lógica del cliente
|-- lib/                  # Librerías de terceros
|-- multimedia/           # Recursos gráficos e imágenes
|-- padre/                # Portal y funcionalidades para padres
|-- php/                  # Lógica del backend en PHP (conexión, login, etc.)
|-- profesor/             # Portal y funcionalidades para profesores
|-- scripts/              # Scripts de base de datos y otros
|-- style/                # Otros archivos de estilo
|-- index.html            # Página de inicio pública del colegio
|-- inicia.html           # Página de inicio de sesión
|-- logout.php            # Script para cerrar sesión
```

## 5. Guía de Instalación (Proyecto Web)

1.  **Servidor Web:** Asegúrate de tener un servidor web como Apache o Nginx en funcionamiento.
2.  **Colocar Archivos:** Clona o copia los archivos del proyecto en el directorio raíz de tu servidor (ej. `C:\Apache24\htdocs\SJB`).
3.  **Base de Datos:**
    *   Verifica que tienes PostgreSQL instalado y en ejecución.
    *   Crea una base de datos para el proyecto.
    *   Importa el esquema y los datos iniciales desde el archivo que se encuentre en la carpeta `/scripts`.
4.  **Configuración de Conexión:** Actualiza las credenciales de la base de datos en el archivo de conexión de PHP (probablemente ubicado en `php/conexion.php`).
5.  **Acceso:** Abre tu navegador y accede a la URL correspondiente (ej. `http://localhost/SJB/`).
