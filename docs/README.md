# Proyecto SJB (Sistema de Gestión Bosco)

## 1. Descripción General

El Proyecto SJB es un sistema de gestión escolar integral basado en la web, diseñado para digitalizar y optimizar los procesos académicos y administrativos del Colegio San Juan Bosco. La plataforma ofrece portales personalizados para administradores, profesores, estudiantes y padres de familia, centralizando la información y facilitando la comunicación y el seguimiento académico en un entorno cohesivo y fácil de usar.

## 2. Tecnologías Utilizadas

El sistema está construido con una pila de tecnologías clásica y robusta, ideal para aplicaciones web dinámicas.

*   **Servidor Web:**
    *   **Apache 2.4:** Servidor HTTP para desplegar la aplicación.

*   **Backend:**
    *   **PHP:** Lenguaje principal para toda la lógica del lado del servidor, incluyendo la gestión de la base de datos, la autenticación de usuarios y el procesamiento de datos.

*   **Frontend:**
    *   **HTML5:** Para la estructura semántica de todas las páginas web.
    *   **CSS3:** Para los estilos visuales, utilizando tanto hojas de estilo personalizadas (`style/`) como el framework **Bootstrap**.
    *   **JavaScript:** Para la interactividad del lado del cliente y la comunicación asíncrona con el backend (AJAX).
        *   **jQuery:** Librería principal para la manipulación del DOM, manejo de eventos y peticiones AJAX.
        *   **Chart.js:** Para la creación de gráficos y visualización de datos estadísticos en los dashboards.
        *   **Otras Librerías:** Se utilizan librerías adicionales como Easing, Owl Carousel, Tempus Dominus y Waypoints para mejorar la experiencia de usuario.

*   **Base de Datos:**
    *   **PostgreSQL:** Sistema de gestión de bases de datos relacional para almacenar toda la información de la aplicación.

## 3. Estructura del Proyecto

El proyecto está organizado de forma modular para separar las responsabilidades y facilitar el mantenimiento.

```
/
|-- admin/              # Panel y funcionalidades para el rol de Administrador.
|-- api/                # Endpoints de la API para la comunicación asíncrona (AJAX).
|-- css/                # Hojas de estilo globales (Bootstrap).
|-- docs/               # Documentación adicional del proyecto.
|-- estudiante/         # Portal y funcionalidades para el rol de Estudiante.
|-- js/                 # Archivos JavaScript globales y específicos por módulo.
|-- lib/                # Librerías de terceros (Chart.js, PHPMailer, etc.).
|-- multimedia/         # Recursos gráficos (imágenes, iconos, PDFs).
|-- padre/              # Portal y funcionalidades para el rol de Padre/Acudiente.
|-- php/                # Lógica principal del backend (conexión a BD, sesiones, etc.).
|-- profesor/           # Portal y funcionalidades para el rol de Profesor.
|-- scripts/            # Scripts SQL para la creación y migración de la base de datos.
|-- style/              # Hojas de estilo CSS personalizadas.
|-- uploads/            # Directorio para archivos subidos por los usuarios.
|-- index.html          # Página de inicio pública del colegio.
|-- inicia.html         # Página de inicio de sesión para todos los roles.
|-- recuperar.html      # Página para la recuperación de contraseña.
|-- logout.php          # Script para cerrar la sesión del usuario.
```

## 4. Funcionalidades Clave por Rol

El sistema ofrece un flujo de usuario claro y funcionalidades específicas para cada rol.

*   **Público General:**
    *   **Página de Inicio (`index.html`):** Presenta información general del colegio, noticias y niveles educativos.
    *   **Inicio de Sesión (`inicia.html`):** Formulario para que los usuarios se autentiquen según su rol.
    *   **Recuperación de Contraseña (`recuperar.html`):** Permite a los usuarios restablecer su contraseña mediante preguntas de seguridad.

*   **Administrador (`admin/`):**
    *   **Dashboard:** Visualización de estadísticas clave (total de estudiantes, profesores, etc.), gráficos de rendimiento académico y agenda de tareas (usando `localStorage`).
    *   **Gestión de Usuarios:** Creación, edición y eliminación de cuentas para todos los roles.
    *   **Gestión Académica:** Asignación de profesores a cursos y materias.
    *   **Listados:** Acceso a listas completas de estudiantes, profesores y personal.

*   **Profesor (`profesor/`):**
    *   **Dashboard:** Resumen de cursos asignados, estadísticas de rendimiento de sus estudiantes y registros de asistencia.
    *   **Gestión de Calificaciones:** Interfaz para registrar y actualizar las notas de los estudiantes en las materias que imparte.
    *   **Gestión de Asistencia:** Registro de la asistencia de los estudiantes a sus clases.

*   **Estudiante (`estudiante/`):**
    *   **Dashboard:** Visualización detallada de su propio rendimiento académico, incluyendo calificaciones por materia y un gráfico de evolución.
    *   **Consulta de Notas:** Acceso a su historial de calificaciones.
    *   **Calendario y PQRSF:** Herramientas de comunicación y organización.

*   **Padre/Acudiente (`padre/`):**
    *   **Dashboard:** Seguimiento del rendimiento académico de sus hijos, mostrando promedios, mejores materias y áreas a mejorar.
    *   **Consulta de Notas:** Acceso a las calificaciones de cada uno de sus hijos matriculados.

## 5. Base de Datos (PostgreSQL)

La base de datos es el núcleo del sistema y está diseñada de manera normalizada para garantizar la integridad de los datos.

*   **Script Principal:** El archivo `scripts/bd_institucional.sql` contiene el esquema completo de la base de datos, incluyendo tablas, relaciones y datos iniciales.
*   **Estructura de Tablas:** Las tablas están agrupadas lógicamente para gestionar:
    *   **Autenticación y Seguridad:** `login`, `accesos`, `tab_seguridad_respuestas`.
    *   **Información de Personas:** `tab_estudiantes`, `tab_profesores`, `tab_acudiente`.
    *   **Gestión Académica:** `tab_materias`, `tab_grados`, `tab_cursos`, `tab_matriculas`, `tab_calificaciones`, `tab_asistencia`.
    *   **Comunicación:** `tab_pqrs`, `eventos`.
*   **Seguridad:** Las contraseñas de los usuarios se almacenan de forma segura utilizando `password_hash()` en PHP, siguiendo las mejores prácticas de seguridad.

## 6. Guía de Instalación y Puesta en Marcha

Para desplegar el proyecto en un entorno de desarrollo local, sigue estos pasos:

1.  **Prerrequisitos:**
    *   Un servidor web como **Apache**.
    *   **PHP** instalado y configurado en el servidor.
    *   Un servidor de bases de datos **PostgreSQL**.

2.  **Instalación:**
    *   **Clonar/Copiar Archivos:** Coloca todos los archivos del proyecto en un directorio servido por Apache (ej. `C:\Apache24\htdocs\SJB`).
    *   **Crear Base de Datos:** Crea una nueva base de datos en PostgreSQL (ej. `sjb_db`).
    *   **Importar Esquema:** Ejecuta el script `scripts/bd_institucional.sql` en tu base de datos para crear todas las tablas y los datos iniciales.
    *   **Configurar Conexión:** Abre el archivo `php/conexion.php` y actualiza los parámetros de conexión (`$host`, `$port`, `$dbname`, `$user`, `$password`) para que coincidan con la configuración de tu base de datos PostgreSQL.

3.  **Acceso:**
    *   Inicia el servidor Apache.
    *   Abre tu navegador web y navega a la URL correspondiente (ej. `http://localhost/SJB/`).