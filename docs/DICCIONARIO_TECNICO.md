# Diccionario Técnico del Proyecto SJB

Este documento sirve como un glosario de términos, tecnologías y conceptos clave utilizados en el proyecto del Sistema de Gestión Bosco (SJB). Su objetivo es proporcionar una referencia rápida para que desarrolladores, administradores y clientes puedan entender la terminología específica del sistema.

---

## 1. Conceptos Generales del Proyecto

| Término | Definición |
| :--- | :--- |
| **SJB** | Acrónimo de **San Juan Bosco**. Es el nombre oficial del proyecto y del sistema de gestión escolar. |
| **PQRSF** | Acrónimo de **Peticiones, Quejas, Reclamos, Sugerencias y Felicitaciones**. Es un módulo de comunicación fundamental en el sistema que permite a los usuarios enviar y gestionar este tipo de solicitudes. |
| **Rol de Usuario** | Define el nivel de acceso y las funcionalidades disponibles para una persona en el sistema. Los roles principales son: `Administrador`, `Profesor`, `Estudiante` y `Padre/Acudiente`. |
| **Dashboard** | También llamado "Panel de Control". Es la pantalla principal que ve un usuario al iniciar sesión. Muestra información relevante y estadísticas personalizadas para su rol. |
| **Profesor Líder** | Término utilizado para referirse al **Director de Grupo**, un profesor que tiene la responsabilidad principal sobre un grado y sección específicos. |

---

## 2. Arquitectura y Desarrollo Web

| Término | Definición |
| :--- | :--- |
| **Frontend** | Es la parte de la aplicación con la que el usuario interactúa directamente en su navegador. En este proyecto, está construido con **HTML, CSS y JavaScript (jQuery)**. |
| **Backend** | Es la parte de la aplicación que se ejecuta en el servidor y que el usuario no ve. Se encarga de la lógica de negocio, el acceso a la base de datos y la seguridad. En este proyecto, está construido principalmente con **PHP**. |
| **API** | Acrónimo de **Application Programming Interface** (Interfaz de Programación de Aplicaciones). Es un conjunto de reglas y endpoints que permiten que el **Frontend** y el **Backend** se comuniquen. En el proyecto, es el directorio `api/` que contiene scripts PHP que devuelven datos en formato JSON. |
| **Endpoint** | Una URL específica dentro de la API que realiza una única acción. Por ejemplo, `api/get_estudiantes.php` es un endpoint para obtener la lista de estudiantes. |
| **AJAX** | Acrónimo de **Asynchronous JavaScript and XML**. Es la tecnología que usa el Frontend (a través de jQuery) para hacer peticiones a la API en segundo plano, sin necesidad de recargar la página. Esto hace que la aplicación se sienta más rápida y fluida. |
| **JSON** | Acrónimo de **JavaScript Object Notation**. Es el formato de texto estándar que utiliza la API para enviar y recibir datos. Es ligero y fácil de leer tanto para humanos como para máquinas. |
| **CRUD** | Acrónimo de **Create, Read, Update, Delete** (Crear, Leer, Actualizar, Borrar). Son las cuatro operaciones básicas de la gestión de datos. La mayoría de los endpoints de la API realizan una de estas operaciones. |
| **Sesión (Session)** | Mecanismo que utiliza el Backend (PHP) para "recordar" a un usuario que ha iniciado sesión. Permite mantener al usuario autenticado mientras navega por las diferentes páginas del módulo que le corresponde. |

---

## 3. Base de Datos (PostgreSQL)

| Término | Definición |
| :--- | :--- |
| **PostgreSQL** | Es el sistema de gestión de bases de datos relacional utilizado en el proyecto para almacenar toda la información. |
| **Esquema (Schema)** | Es el "plano" o la estructura lógica de la base de datos. Define todas las tablas, sus columnas, los tipos de datos y cómo se relacionan entre sí. Está definido en el archivo `scripts/db_institucional.sql`. |
| **Tabla (Table)** | Estructura que almacena datos de un tipo específico, organizada en filas y columnas. Por ejemplo, la tabla `tab_profesores` almacena la información de todos los profesores. |
| **Vista (View)** | Es una tabla virtual basada en el resultado de una consulta SQL. En el proyecto, la vista `v_estudiantes_detalle` simplifica el acceso a la información completa de un estudiante al unir varias tablas en una sola. |
| **Función (Stored Procedure)** | Es un bloque de código SQL que se almacena en la base de datos y se puede ejecutar como un solo comando. En el proyecto, funciones como `fun_eliminar_usuario_completo` encapsulan lógica crítica para garantizar que los datos se manipulen de forma segura y consistente. |
| **`id_log`** | Es un campo numérico clave en la base de datos. Representa el **ID de la cuenta de un usuario en la tabla `login`**. Se utiliza como clave foránea en otras tablas (como `tab_usuarios` o `tab_profesores`) para vincular el perfil de una persona con sus credenciales de acceso. |
| **`id_estud` / `id_ficha`** | Son los identificadores únicos para un estudiante en las tablas de la ficha estudiantil. |
| **`tab_...`** | Es la convención de nombrado utilizada para la mayoría de las tablas en la base de datos (ej. `tab_usuarios`, `tab_calificaciones`). |
| **`pgcrypto`** | Una extensión de PostgreSQL que proporciona funciones de criptografía. Se usa en los datos de ejemplo para hashear contraseñas. |
| **`ENUM`** | Un tipo de dato especial que consiste en una lista de valores predefinidos. Se usa, por ejemplo, para el campo `tipo_evaluacion` para asegurar que solo se puedan introducir valores válidos como 'Examen' o 'Taller'. |

---

## 4. Tecnologías y Librerías

| Término | Definición |
| :--- | :--- |
| **PHP** | Lenguaje de programación del lado del servidor (backend) en el que está escrita la mayor parte de la lógica de negocio del proyecto. |
| **jQuery** | Una librería de JavaScript que simplifica la manipulación de elementos HTML (DOM), la gestión de eventos y la realización de llamadas AJAX. Es la principal herramienta de interactividad en el frontend. |
| **Chart.js** | Una librería de JavaScript utilizada para crear los gráficos y diagramas estadísticos que se muestran en los diferentes dashboards del sistema. |
| **Bootstrap** | Un framework de CSS que proporciona una base de estilos y componentes (botones, formularios, menús) para crear una interfaz de usuario responsiva y visualmente atractiva de forma rápida. |
| **PHPMailer** | Una librería de PHP utilizada en el backend para gestionar el envío de correos electrónicos, como en el caso de la funcionalidad de "recuperar contraseña". |
| **Node.js** | Un entorno de ejecución de JavaScript del lado del servidor. En el proyecto, se utiliza en un microservicio alternativo (`api-rest-cambio-contrasena`) para la recuperación de contraseñas. |
| **Express** | Un framework para Node.js que simplifica la creación de APIs. Usado en el microservicio de recuperación de contraseñas. |
| **Sequelize** | Un **ORM** (Object-Relational Mapper) para Node.js. Permite interactuar con la base de datos usando objetos de JavaScript en lugar de escribir SQL directamente. Usado en el microservicio de recuperación de contraseñas. |
