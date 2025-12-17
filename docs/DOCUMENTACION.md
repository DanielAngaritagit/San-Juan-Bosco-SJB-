# Documentación Integral del Proyecto SJB

Este documento proporciona una visión completa y detallada del Sistema de Gestión Bosco (SJB), su arquitectura, componentes y funcionamiento. Está diseñado para que tanto desarrolladores como clientes puedan entender el proyecto en su totalidad.

## Tabla de Contenidos
1.  [Descripción General del Proyecto](#1-descripción-general-del-proyecto)
2.  [Guía de Instalación y Puesta en Marcha](#2-guía-de-instalación-y-puesta-en-marcha)
3.  [Arquitectura y Tecnologías](#3-arquitectura-y-tecnologías)
    *   [Tecnologías Utilizadas](#31-tecnologías-utilizadas)
    *   [Estructura de Directorios](#32-estructura-de-directorios)
4.  [La Base de Datos (Backend)](#4-la-base-de-datos-backend)
5.  [Módulos por Rol de Usuario (Frontend + Lógica)](#5-módulos-por-rol-de-usuario-frontend--lógica)
    *   [Módulo Administrador](#51-módulo-administrador)
    *   [Módulo Profesor](#52-módulo-profesor)
    *   [Módulo Estudiante](#53-módulo-estudiante)
    *   [Módulo Padre/Acudiente](#54-módulo-padreacudiente)
    *   [Flujos Comunes (Login, Perfil, PQRSF)](#55-flujos-comunes-login-perfil-pqrsf)
6.  [Referencia de la API (Backend)](#6-referencia-de-la-api-backend)
7.  [Scripts y Mantenimiento](#7-scripts-y-mantenimiento)

---

## 1. Descripción General del Proyecto

El Proyecto SJB (San Juan Bosco) es una aplicación web integral diseñada para la gestión académica y administrativa de una institución educativa. El sistema centraliza la información y optimiza los procesos para los diferentes actores de la comunidad escolar: administradores, profesores, estudiantes y padres de familia.

**Funcionalidades Principales:**
*   **Gestión de Usuarios:** Creación y administración de cuentas para todos los roles.
*   **Control Académico:** Registro de materias, cursos, matrículas y calificaciones.
*   **Seguimiento del Rendimiento:** Dashboards visuales con estadísticas y gráficos para monitorear el desempeño de los estudiantes.
*   **Comunicación:** Sistema de Peticiones, Quejas, Reclamos y Sugerencias (PQRSF) y calendario de eventos.
*   **Autoservicio:** Portales para que estudiantes y padres consulten notas, y para que los profesores gestionen sus cursos.

---

## 2. Guía de Instalación y Puesta en Marcha

Para ejecutar el proyecto en un entorno de desarrollo local, se requieren los siguientes componentes y pasos:

*   **Prerrequisitos:**
    *   **Servidor Web:** Apache 2.4 (o similar) con soporte para PHP.
    *   **PHP:** Versión 7.4 o superior.
    *   **Base de Datos:** PostgreSQL.

*   **Pasos de Instalación:**
    1.  **Clonar/Copiar el Proyecto:** Ubicar todos los archivos del proyecto en el directorio `htdocs` de Apache (ej. `C:/Apache24/htdocs/SJB/`).
    2.  **Crear la Base de Datos:**
        *   Crear una nueva base de datos en PostgreSQL (ej. `db_sjb`).
        *   Ejecutar el script `scripts/db_institucional.sql` en la base de datos recién creada. Esto creará todo el esquema de tablas, vistas, funciones y cargará los datos de prueba iniciales.
    3.  **Configurar la Conexión:**
        *   Abrir el archivo `php/conexion.php`.
        *   Modificar las variables `$host`, `$port`, `$dbname`, `$user` y `$password` con los datos de conexión a tu base de datos PostgreSQL.
    4.  **Iniciar el Servidor:** Asegurarse de que el servicio de Apache y el de PostgreSQL estén en ejecución.
    5.  **Acceder a la Aplicación:** Abrir un navegador web y visitar `http://localhost/SJB/`.

---

## 3. Arquitectura y Tecnologías

### 3.1. Tecnologías Utilizadas

*   **Frontend:**
    *   **HTML5:** Estructura de las páginas.
    *   **CSS3 & Bootstrap:** Estilos y diseño responsivo.
    *   **JavaScript & jQuery:** Interactividad del lado del cliente y peticiones AJAX.
    *   **Chart.js:** Visualización de datos y gráficos en los dashboards.
*   **Backend:**
    *   **PHP:** Lenguaje principal para la lógica de negocio y la comunicación con la base de datos.
*   **Base de Datos:**
    *   **PostgreSQL:** Sistema de gestión de bases de datos relacional.

### 3.2. Estructura de Directorios

El proyecto está organizado de forma modular para separar responsabilidades:

| Directorio | Propósito |
|---|---|
| `/` | Contiene las páginas públicas principales (`index.html`, `inicia.html`, `recuperar.html`). |
| `admin/` | **Módulo Administrador.** Contiene todas las páginas y scripts PHP para la gestión del sistema. |
| `profesor/` | **Módulo Profesor.** Páginas para la gestión de cursos, calificaciones y estudiantes. |
| `estudiante/` | **Módulo Estudiante.** Portal para que el estudiante consulte su información académica. |
| `padre/` | **Módulo Padre/Acudiente.** Portal para el seguimiento de los hijos. |
| `api/` | **Endpoints de la API.** Scripts PHP que reciben peticiones del frontend, procesan datos y devuelven respuestas en formato JSON. |
| `php/` | **Lógica de Backend.** Contiene la conexión a la BD (`conexion.php`), gestión de sesiones (`verificar_sesion.php`) y scripts de negocio reutilizables. |
| `js/` | **Lógica de Frontend.** Archivos JavaScript que controlan la interactividad de las páginas. Cada módulo principal tiene su propio archivo JS (ej. `prof.js`, `estu.js`). |
| `scripts/` | **Base de Datos.** Contiene el script de creación de la BD (`db_institucional.sql`) y otras funciones y vistas SQL. |
| `lib/` | Librerías de terceros (Chart.js, PHPMailer, etc.). |
| `style/` | Hojas de estilo CSS personalizadas. |
| `multimedia/` | Recursos gráficos como imágenes e iconos. |

---

## 4. La Base de Datos (Backend)

La base de datos en PostgreSQL es el corazón del sistema. Su diseño se detalla en el script `scripts/db_institucional.sql`.

### Resumen del Esquema

*   **Autenticación y Seguridad:** Tablas `login`, `accesos`, `tab_password_reset` para gestionar el acceso y la seguridad.
*   **Usuarios y Perfiles:** Tablas `tab_usuarios`, `tab_profesores`, `tab_acudiente` que almacenan la información de los diferentes roles.
*   **Ficha del Estudiante:** Un conjunto de tablas (`tab_ficha_datos_estudiantes`, `..._hogar`, `..._salud`, etc.) que guardan de forma normalizada toda la información de un estudiante.
*   **Gestión Académica:** El núcleo del sistema, con tablas como `tab_grados`, `tab_materias`, `tab_cursos`, `tab_matriculas` y `tab_calificaciones`.
*   **Comunicación:** Tablas `tab_pqrsf` y `eventos` para la interacción dentro de la plataforma.

### Lógica en la Base de Datos

El sistema utiliza características avanzadas de PostgreSQL para centralizar la lógica de negocio:

*   **Vistas:** `v_estudiantes_detalle` (`create_estudiantes_view.sql`) simplifica las consultas de estudiantes al unir múltiples tablas en una sola tabla virtual.
*   **Funciones:** `fun_gestionar_matricula.sql` y `fun_eliminar_usuario_completo.sql` encapsulan operaciones complejas y críticas (como matricular o eliminar un usuario con todos sus datos) en transacciones seguras, reduciendo la complejidad del código PHP.

---

## 5. Módulos por Rol de Usuario (Frontend + Lógica)

El sistema presenta diferentes interfaces y funcionalidades según el rol del usuario que ha iniciado sesión.

### 5.1. Módulo Administrador

*   **Propósito:** Ofrece una visión global y control total sobre el sistema.
*   **Dashboard (`admin/admin.php`):** Muestra estadísticas clave (total de usuarios, rendimiento general), gráficos, matrículas recientes y una agenda. La interactividad es manejada por `js/main.js`, que consume datos de endpoints como `api/get_profesor_stats.php`.
*   **Funcionalidades Clave:**
    *   **Creación de Usuarios (`admin/agregar_usuario.php`):** Formulario para registrar nuevos estudiantes, profesores y acudientes. La lógica de guardado está en `php/guardar_usuario.php` y es invocada vía AJAX desde `js/agregar_usuario.js`.
    *   **Asignación de Profesores (`admin/asignacion_profesores.php`):** Interfaz para asignar profesores a grados/secciones. Controlado por `js/asignacion_profesores.js`, que usa la API para guardar y eliminar asignaciones.
    *   **Listados:** Visualización de listas de estudiantes, profesores, etc., que consumen datos de la API (ej. `api/get_estudiantes.php`).

### 5.2. Módulo Profesor

*   **Propósito:** Herramientas para que el profesor gestione sus cursos, califique a sus estudiantes y monitoree su progreso.
*   **Dashboard (`profesor/profesor.php`):** Presenta un resumen de los cursos del profesor, estadísticas de sus estudiantes (aprobados/reprobados) y las últimas calificaciones registradas. La lógica está en `js/prof_dashboard.js`.
*   **Funcionalidades Clave:**
    *   **Calificar (`profesor/calificar.php`):** La herramienta principal del profesor. Permite seleccionar un curso y estudiante para registrar o actualizar calificaciones. Es una interfaz compleja manejada por `js/calificar.js`, que interactúa con `api/get_grades_data.php` para cargar la información y `api/save_grade.php` para guardarla.

### 5.3. Módulo Estudiante

*   **Propósito:** Un portal personal para que el estudiante consulte su propio rendimiento académico.
*   **Dashboard (`estudiante/estudiante.php`):** Muestra el promedio general, un gráfico de evolución del rendimiento y una tabla detallada con todas sus calificaciones por materia. La página es controlada por `js/estu.js`, que obtiene todos los datos del endpoint `api/get_student_performance.php`.

### 5.4. Módulo Padre/Acudiente

*   **Propósito:** Permitir a los padres hacer un seguimiento cercano del progreso académico de sus hijos.
*   **Dashboard (`padre/padre.php`):** Permite al padre seleccionar a uno de sus hijos (si tiene varios). Una vez seleccionado, muestra un informe completo del estudiante, incluyendo promedio, mejor y peor materia, y la lista de notas. La lógica reside en `js/padre.js`, que primero llama a `api/get_children_ids.php` y luego a `api/get_student_grades.php`.

### 5.5. Flujos Comunes (Login, Perfil, PQRSF)

*   **Inicio de Sesión (`inicia.html`):** Formulario donde el usuario elige su rol e ingresa credenciales. `js/ini.js` envía los datos a `php/login.php`, que verifica al usuario y crea una sesión PHP.
*   **Gestión de Perfil (`perfil.php` en cada módulo):** Todos los roles tienen una página de perfil para ver y actualizar su propia información. La lógica es manejada por `js/user_profile_manager.js`, que carga los datos con `api/get_user_profile.php` y los guarda con `api/update_user_profile.php`.
*   **PQRSF (`pqrsf.php` en cada módulo):** Interfaz para crear y consultar PQRSF. `js/pqrsf.js` se comunica con `api/get_pqrsf.php` y `api/save_pqrsf.php`.

---

## 6. Referencia de la API (Backend)

La API es el puente entre el frontend (JavaScript) y el backend (PHP). A continuación se detallan los endpoints, agrupados por funcionalidad.

*Para ver el detalle completo de cada endpoint (parámetros, cuerpo de la solicitud y respuestas de ejemplo), consulte la documentación original que se ha integrado a continuación.*

### Autenticación y Seguridad
*   `POST /api/solicitar_recuperacion.php`: Inicia el proceso de recuperación de contraseña por correo.
*   `POST /api/actualizar_contrasena.php`: Establece una nueva contraseña usando un token.
*   `POST /api/change_password.php`: Permite a un usuario logueado cambiar su contraseña.
*   `POST /api/admin_reset_password.php`: Permite a un admin resetear la contraseña de otro usuario.
*   `POST /api/set_security_question.php`: Guarda la pregunta y respuesta de seguridad del usuario.

### Gestión de Usuarios
*   `GET /api/get_user_profile.php`: Obtiene los datos del perfil del usuario logueado.
*   `POST /api/update_user_profile.php`: Actualiza los datos del perfil.
*   `POST /api/update_profile_pic.php`: Sube y actualiza la foto de perfil.

### Gestión de Estudiantes
*   `GET /api/get_estudiantes.php`: Obtiene la lista completa de estudiantes.
*   `GET /api/get_estudiantes_por_curso.php`: Filtra estudiantes por un curso específico.
*   `GET /api/get_students_by_grade.php`: Filtra estudiantes por nivel (preescolar, primaria, etc.).
*   `GET /api/get_students_by_director_grade.php`: Obtiene estudiantes de un grado/sección específico.
*   `POST /api/export_students.php`: (Simulado) Exporta lista de estudiantes a PDF/Excel.

### Gestión de Profesores
*   `GET /api/get_profesores_list.php`: Obtiene la lista de profesores con su especialidad.
*   `GET /api/get_profesor_cursos.php`: Obtiene los cursos asignados a un profesor.
*   `GET /api/get_profesor_stats.php`: Obtiene estadísticas para el dashboard del profesor.
*   `GET /api/get_profesor_director_info.php`: Verifica si un profesor es director de grupo.

### Gestión Académica (Calificaciones y Estructura)
*   `POST /api/save_grade.php`: Guarda o actualiza una calificación.
*   `GET /api/get_student_grades.php`: Obtiene el informe completo de notas de un estudiante (vista de padre).
*   `GET /api/get_student_performance.php`: Obtiene el informe de notas del estudiante logueado.
*   `GET /api/get_calificaciones_recientes.php`: Obtiene las últimas notas puestas por un profesor.
*   `GET /api/get_grades_data.php`: Carga todos los datos necesarios para la interfaz de "Calificar".
*   `POST /api/update_student_grade.php`: **Importante:** Promueve a un estudiante a un nuevo grado/sección.
*   `GET /api/get_cursos_list.php`, `get_grados_list.php`, `get_materias_list.php`: Obtienen los catálogos de la estructura académica.
*   `GET /api/get_periodos_academicos.php`, `POST /api/save_periodo_academico.php`, `POST /api/delete_periodo_academico.php`: CRUD para los periodos académicos.
*   `GET /api/get_teacher_assignments.php`, `POST /api/save_teacher_assignment.php`, `POST /api/delete_teacher_assignment.php`: CRUD para las asignaciones de profesores a cursos.

### Comunicación y Notificaciones
*   `GET /api/get_pqrsf.php`, `POST /api/save_pqrsf.php`, `POST /api/delete_pqrsf.php`: CRUD para el sistema de PQRSF.
*   `GET /api/get_proximos_eventos.php`: Obtiene eventos del calendario.
*   `GET /api/get_notifications.php`: Obtiene las últimas PQRSF como notificaciones.

### Rol de Padre
*   `GET /api/get_children_ids.php`: Obtiene los hijos asociados a la cuenta de un padre.

### Microservicio Alternativo (Node.js)
El subdirectorio `api/api-rest-cambio-contrasena/` contiene un microservicio en Node.js como una alternativa moderna para la recuperación de contraseñas, utilizando Express y Sequelize.

---

## 7. Scripts y Mantenimiento

El directorio `scripts/` contiene archivos SQL cruciales para el mantenimiento de la base de datos.

*   **`db_institucional.sql`:** El script más importante. Crea toda la estructura de la base de datos y la llena con datos de prueba. Debe ejecutarse en una base de datos vacía durante la instalación.
*   **Archivos de Funciones (`fun_*.sql`):** Crean funciones almacenadas en PostgreSQL que encapsulan lógica de negocio compleja, como `fun_eliminar_usuario_completo`, que borra a un usuario y todos sus registros asociados de forma segura.
*   **Archivos de Vistas (`create_*.sql`):** Crean vistas para simplificar las consultas recurrentes.
*   **Consultas de Prueba:** Archivos como `consultas_de_prueba.sql` contienen ejemplos de sentencias `SELECT` para verificar datos, muy útiles durante el desarrollo.