# Requerimientos del Sistema de Gestión Escolar SJB

Este documento detalla los requerimientos funcionales y no funcionales del sistema, derivados del análisis del código fuente.

## Módulos Generales del Sistema

### Módulo: Autenticación de Usuarios

#### Requerimientos Funcionales (RF)

- **RF-AUTH-001:** El sistema debe proporcionar una interfaz para que los usuarios inicien sesión.
- **RF-AUTH-002:** El usuario debe seleccionar un rol antes de iniciar sesión. Los roles disponibles son: Administrador, Profesor, Estudiante y Padre.
- **RF-AUTH-003:** El usuario debe ingresar un nombre de usuario y una contraseña.
- **RF-AUTH-004:** El sistema debe validar en el cliente que el usuario ha seleccionado un rol y ha completado los campos de usuario y contraseña antes de enviar la solicitud.
- **RF-AUTH-005:** El sistema debe verificar las credenciales del usuario contra los registros en la base de datos.
- **RF-AUTH-006:** El sistema debe validar que la cuenta del usuario se encuentre en estado "activo" para permitir el acceso.
- **RF-AUTH-007:** El sistema debe verificar que el rol seleccionado por el usuario en la interfaz coincida con el rol asignado en la base de datos.
- **RF-AUTH-008:** Tras una autenticación exitosa, el sistema debe iniciar una sesión de usuario, almacenando el ID, nombre de usuario y rol.
- **RF-AUTH-009:** El sistema debe registrar cada intento de inicio de sesión exitoso en una tabla de auditoría (`accesos`), guardando el ID del usuario, su dirección IP y el agente de usuario.
- **RF-AUTH-010:** El sistema debe redirigir al usuario a su panel de control (dashboard) correspondiente según su rol después de un inicio de sesión exitoso.
- **RF-AUTH-011:** El sistema debe ofrecer una opción para mostrar/ocultar la contraseña en el campo de texto.
- **RF-AUTH-012:** El sistema debe proporcionar un enlace visible a la página de recuperación de contraseña.
- **RF-AUTH-013:** El sistema debe gestionar las sesiones activas para permitir solo una sesión por usuario a la vez, invalidando sesiones anteriores.

#### Requerimientos No Funcionales (RNF)

- **RNF-SEC-001:** Las contraseñas de los usuarios deben almacenarse en la base de datos de forma segura, utilizando un algoritmo de hash robusto (como `password_hash` de PHP).
- **RNF-SEC-002:** El sistema no debe revelar información específica sobre la causa de un fallo de inicio de sesión (por ejemplo, si el usuario no existe o la contraseña es incorrecta, el mensaje debe ser genérico como "Usuario o contraseña incorrectos").
- **RNF-USAB-001:** La interfaz de inicio de sesión debe ser intuitiva y guiar al usuario, mostrando los campos de usuario y contraseña solo después de que se haya seleccionado un rol.
- **RNF-USAB-002:** El sistema debe proporcionar retroalimentación visual clara al usuario durante el proceso (ej. "Iniciando sesión...", "Bienvenido", mensajes de error).
- **RNF-PERF-001:** La comunicación entre el cliente y el servidor para el login debe ser asíncrona (usando AJAX/Fetch) para no recargar la página completa.
- **RNF-COMP-001:** La página de inicio de sesión debe ser compatible con los navegadores web modernos.

### Módulo: Recuperación de Contraseña

#### Requerimientos Funcionales (RF) - Parte 1: Solicitud

- **RF-REC-001:** El sistema debe proporcionar una página para que los usuarios inicien el proceso de recuperación de contraseña.
- **RF-REC-002:** El usuario debe proporcionar su número de documento (que funciona como nombre de usuario) y su dirección de correo electrónico registrada.
- **RF-REC-003:** El sistema debe validar que el número de documento exista en la base de datos.
- **RF-REC-004:** El sistema debe validar que el correo electrónico proporcionado por el usuario coincida con el correo electrónico registrado para ese número de documento.
- **RF-REC-005:** Si la validación es exitosa, el sistema debe generar un token de recuperación de contraseña único y seguro.
- **RF-REC-006:** El sistema debe almacenar el token en la base de datos (`tab_password_reset`), asociándolo al ID del usuario y estableciendo una fecha de expiración.
- **RF-REC-007:** El sistema debe enviar un correo electrónico al usuario con un enlace único que contiene el token para restablecer la contraseña.
- **RF-REC-008:** El sistema debe mostrar un mensaje genérico al usuario (ej. "se han enviado las instrucciones de recuperación a tu correo") para no confirmar si un documento o correo específico existe en el sistema.
- **RF-REC-009:** El sistema debe proporcionar un enlace para volver a la página de inicio de sesión.

#### Requerimientos No Funcionales (RNF) - Parte 1: Solicitud

- **RNF-SEC-003:** El token de recuperación de contraseña debe ser criptográficamente seguro (generado con `random_bytes`).
- **RNF-SEC-004:** El token de recuperación de contraseña debe tener un tiempo de vida limitado (ej. 1 hora) para reducir el riesgo de uso malicioso.
- **RNF-SEC-005:** La comunicación para la solicitud de recuperación debe realizarse a través de un método seguro (POST) y la respuesta no debe filtrar datos sensibles.
- **RNF-USAB-003:** El proceso debe ser claro para el usuario, solicitando solo la información estrictamente necesaria.

#### Requerimientos Funcionales (RF) - Parte 2: Restablecimiento

- **RF-REC-010:** El sistema debe proporcionar una página de restablecimiento de contraseña a la que se accede a través del enlace con el token.
- **RF-REC-011:** La página debe capturar el token de la URL automáticamente.
- **RF-REC-012:** El usuario debe ingresar su nueva contraseña y confirmarla en un segundo campo.
- **RF-REC-013:** El sistema debe validar en el cliente que las contraseñas ingresadas en ambos campos coinciden.
- **RF-REC-014:** Al enviar el formulario, el sistema debe verificar en el backend que el token proporcionado es válido, no ha expirado y no ha sido utilizado previamente.
- **RF-REC-015:** Si el token es válido, el sistema debe actualizar la contraseña del usuario en la tabla `login` con la nueva contraseña proporcionada.
- **RF-REC-016:** Después de actualizar la contraseña, el sistema debe invalidar el token marcándolo como "utilizado" para prevenir su reutilización.
- **RF-REC-017:** El sistema debe mostrar un mensaje de éxito o error al usuario informando el resultado de la operación.

#### Requerimientos No Funcionales (RNF) - Parte 2: Restablecimiento

- **RNF-SEC-006:** La nueva contraseña debe ser almacenada en la base de datos utilizando el mismo algoritmo de hash seguro (`password_hash`) que el resto del sistema.
- **RNF-SEC-007:** El sistema debe tener una validación de la fortaleza de la contraseña (ej. longitud mínima de 8 caracteres).
- **RNF-SEC-008:** Las operaciones de actualización de contraseña e invalidación de token deben ejecutarse dentro de una transacción de base de datos para garantizar la atomicidad (o todo se completa o nada se aplica).

## Rol: Administrador

### Módulo: Dashboard y Estadísticas

#### Requerimientos Funcionales (RF)

- **RF-ADM-001:** El sistema debe presentar un panel de control (dashboard) como página principal para el rol de administrador.
- **RF-ADM-002:** El dashboard debe mostrar estadísticas clave en tarjetas de acceso rápido, incluyendo:
    - El número total de estudiantes.
    - El número total de padres/acudientes.
    - El número total de profesores.
- **RF-ADM-003:** El dashboard debe incluir una sección de navegación (menú lateral) con enlaces a todas las funcionalidades del administrador: Inicio, Agregar Usuario, Asignación de Profesores, Calendario, Perfil, PQRSF, Ayuda, Cambiar Contraseñas y Cerrar Sesión.
- **RF-ADM-004:** El dashboard debe mostrar el nombre y rol del usuario administrador que ha iniciado sesión.
- **RF-ADM-005:** El sistema debe obtener los datos para las estadísticas y gráficos de forma asíncrona desde el backend.
- **RF-ADM-006:** (Observado en `get_chart_data.php`) El sistema debe poder generar datos para gráficos, como el número total de matrículas y el número de estudiantes en un grado específico (ej. 11º grado).

#### Requerimientos No Funcionales (RNF)

- **RNF-SEC-009:** El acceso al panel de administrador debe estar restringido únicamente a usuarios con el rol 'admin'. El sistema debe verificar el rol en el servidor y redirigir a cualquier usuario no autorizado.
- **RNF-USAB-004:** La información en el dashboard debe presentarse de forma visual y fácil de entender, utilizando iconos y tarjetas.
- **RNF-PERF-002:** Los datos de las estadísticas (conteos de usuarios) deben cargarse dinámicamente después de que la página principal se haya renderizado para no bloquear la visualización inicial.

### Módulo: Gestión de Periodos Académicos

#### Requerimientos Funcionales (RF)

- **RF-ADM-007:** El sistema debe permitir al administrador crear nuevos periodos académicos (Bimestrales o Trimestrales).
- **RF-ADM-008:** Para crear un periodo, el administrador debe especificar el nombre del periodo, una fecha de inicio y una fecha de fin.
- **RF-ADM-009:** El sistema debe autocompletar las fechas de inicio y fin sugeridas al seleccionar un nombre de periodo, pero debe permitir la modificación manual.
- **RF-ADM-010:** El sistema debe listar todos los periodos académicos existentes en una tabla, mostrando su nombre, fecha de inicio, fecha de fin y acciones disponibles.
- **RF-ADM-011:** El sistema debe permitir al administrador editar la información (nombre, fecha de inicio, fecha de fin) de un periodo académico existente.
- **RF-ADM-012:** El sistema debe permitir al administrador eliminar un periodo académico existente.
- **RF-ADM-013:** Al eliminar un periodo académico, el sistema también debe eliminar en cascada todas las calificaciones asociadas a ese periodo.
- **RF-ADM-014:** El sistema debe mostrar una confirmación al administrador antes de proceder con la eliminación de un periodo.
- **RF-ADM-015:** Todas las operaciones (crear, leer, actualizar, eliminar) deben realizarse de forma asíncrona, actualizando la lista de periodos sin recargar la página.

#### Requerimientos No Funcionales (RNF)

- **RNF-USAB-005:** El formulario para crear/editar periodos debe ser el mismo, cambiando su estado para facilitar la interacción del usuario.
- **RNF-USAB-006:** El sistema debe proporcionar retroalimentación clara al usuario después de cada operación (ej. "Periodo guardado exitosamente", "Error al eliminar").
- **RNF-DATA-001:** La eliminación de un periodo y sus calificaciones asociadas debe realizarse dentro de una transacción de base de datos para garantizar la integridad de los datos (atomicidad).
- **RNF-DATA-002:** Las fechas de inicio y fin de los periodos deben estar restringidas al año académico en curso (ej. entre el 20 de enero y el 5 de diciembre).

### Módulo: Agenda

#### Requerimientos Funcionales (RF)

- **RF-ADM-016:** El sistema debe mostrar una agenda de eventos en el dashboard del administrador.
- **RF-ADM-017:** El administrador debe poder agregar un nuevo evento a la agenda.
- **RF-ADM-018:** Para cada evento, el administrador debe poder especificar una fecha, hora, título y detalles.
- **RF-ADM-019:** El sistema debe permitir al administrador editar los detalles (fecha, hora, título, detalles) de un evento existente.
- **RF-ADM-020:** El sistema debe permitir al administrador eliminar un evento de la agenda.
- **RF-ADM-021:** Los eventos en la agenda deben mostrarse ordenados cronológicamente por fecha y hora.
- **RF-ADM-022:** El sistema debe impedir que se seleccionen fechas pasadas al crear o editar un evento.

#### Requerimientos No Funcionales (RNF)

- **RNF-DATA-003:** Los datos de la agenda deben persistir entre sesiones del navegador. La persistencia se implementará utilizando el `localStorage` del navegador del cliente.
- **RNF-USAB-007:** La interfaz para agregar y editar eventos debe ser un formulario que se muestra y oculta en la misma página, evitando la navegación.

### Módulo: Gestión de Usuarios (Agregar)

#### Requerimientos Funcionales (RF) - Generales

- **RF-ADM-023:** El sistema debe proporcionar una interfaz con pestañas para registrar diferentes tipos de usuarios: Acudiente, Profesor, Estudiante y Administrador.
- **RF-ADM-024:** El sistema debe crear una credencial de acceso en la tabla `login` para cada usuario nuevo.
- **RF-ADM-025:** El nombre de usuario para el login debe ser el número de documento de la persona.
- **RF-ADM-026:** El sistema debe generar una contraseña por defecto para cada nuevo usuario.
    - La contraseña se compondrá de: las 2 primeras letras del primer nombre + el número de documento + las 2 primeras letras del primer apellido.
- **RF-ADM-027:** El sistema debe verificar que el número de documento del nuevo usuario no exista previamente en la tabla `login` para evitar duplicados.
- **RF-ADM-028:** Tras un registro exitoso, el sistema debe mostrar un mensaje con el nombre de usuario y la contraseña por defecto generada, junto con una advertencia de seguridad para que el usuario la cambie.
- **RF-ADM-029:** Todos los datos de un nuevo usuario (credencial de login y perfil específico) deben guardarse dentro de una transacción de base de datos para garantizar la consistencia.
- **RF-ADM-030:** El usuario debe aceptar una política de tratamiento de datos para poder habilitar el botón de guardado.

#### Requerimientos Funcionales (RF) - Específicos por Rol

- **RF-ADM-031 (Acudiente):** El sistema debe registrar los datos personales y de contacto de un nuevo acudiente en la tabla `tab_acudiente`. El rol en la tabla `login` se guardará como 'padre'.
- **RF-ADM-032 (Profesor):** El sistema debe registrar los datos personales y profesionales de un nuevo profesor en la tabla `tab_profesores`, incluyendo su especialidad y materia principal.
- **RF-ADM-033 (Estudiante):** El sistema debe registrar los datos personales y académicos de un nuevo estudiante en la tabla `tab_estudiante`.
- **RF-ADM-034 (Estudiante):** El formulario de estudiante debe requerir el número de documento de un acudiente previamente registrado para poder asociarlos.
- **RF-ADM-035 (Estudiante):** El sistema debe validar que el acudiente especificado exista antes de crear al estudiante.
- **RF-ADM-036 (Estudiante):** El sistema debe validar que el grado/sección seleccionado para un nuevo estudiante no exceda el límite de 30 alumnos.
- **RF-ADM-037 (Administrador):** El sistema debe registrar los datos personales y el cargo de un nuevo administrador en la tabla `tab_administradores`.

#### Requerimientos No Funcionales (RNF)

- **RNF-SEC-010:** La contraseña por defecto generada debe almacenarse en la base de datos usando un algoritmo de hash seguro (`password_hash`).
- **RNF-USAB-008:** La interfaz debe usar pestañas para separar claramente los formularios de cada rol, cambiando el tema de color para reforzar visualmente qué rol se está editando.
- **RNF-USAB-009:** El sistema debe realizar validaciones en tiempo real en el lado del cliente (ej. solo letras, solo números, formato de email, longitud de documento, rangos de edad) para guiar al usuario.
- **RNF-DATA-003:** Si ocurre un error durante el proceso de guardado, la transacción de la base de datos debe revertirse (`rollBack`) para no dejar datos inconsistentes.

### Módulo: Asignación de Profesores a Grados

#### Requerimientos Funcionales (RF)

- **RF-ADM-038:** El sistema debe proporcionar una interfaz para asignar profesores a grados/secciones específicos.
- **RF-ADM-039:** La interfaz debe contener un formulario con dos menús desplegables: uno para seleccionar un profesor y otro para seleccionar un grado/sección.
- **RF-ADM-040:** El menú de profesores debe cargarse dinámicamente con la lista de todos los profesores registrados, mostrando su nombre completo y especialidad.
- **RF-ADM-041:** El menú de grados/secciones debe cargarse dinámicamente con la lista de todos los grados y secciones disponibles (ej. Grado 1A, Grado 1B, etc.).
- **RF-ADM-042:** El sistema debe permitir guardar una nueva asignación de un profesor a un grado/sección.
- **RF-ADM-043:** El sistema debe impedir que se cree una asignación idéntica si ya existe.
- **RF-ADM-044:** El sistema debe mostrar todas las asignaciones existentes en una tabla.
- **RF-ADM-045:** La tabla de asignaciones debe mostrar el nombre del profesor, su especialidad (que se usa como materia), el grado, la sección y las acciones disponibles.
- **RF-ADM-046:** El sistema debe permitir al administrador eliminar una asignación existente.
- **RF-ADM-047:** El sistema debe solicitar confirmación al usuario antes de eliminar una asignación.

#### Requerimientos No Funcionales (RNF)

- **RNF-USAB-010:** Al seleccionar un profesor, el sistema debe intentar recomendar o resaltar los grados más adecuados para su especialidad en el menú desplegable de grados.
- **RNF-PERF-003:** Todas las operaciones (cargar listas, guardar y eliminar asignaciones) deben realizarse de forma asíncrona sin recargar la página.

### Módulo: Calendario y Gestión de Eventos

#### Requerimientos Funcionales (RF)

- **RF-ADM-048:** El sistema debe mostrar un calendario interactivo con vistas por mes y por semana.
- **RF-ADM-049:** El calendario debe permitir la navegación entre meses/semanas anteriores y posteriores.
- **RF-ADM-050:** El calendario debe permitir volver rápidamente a la fecha actual ("Hoy").
- **RF-ADM-051:** El sistema debe permitir al administrador crear nuevos eventos.
- **RF-ADM-052:** Para cada evento, el administrador debe poder especificar:
    - Nombre del evento.
    - Descripción.
    - Fecha y hora de inicio.
    - Fecha y hora de fin.
    - Un color para identificar el evento visualmente.
    - Roles destinatarios (Padres, Estudiantes, Profesores).
    - Destinatarios específicos (usuarios individuales o cursos/secciones).
- **RF-ADM-053:** El sistema debe validar que la fecha y hora de fin de un evento no sean anteriores a la fecha y hora de inicio.
- **RF-ADM-054:** El sistema debe permitir al administrador editar los detalles de un evento existente.
- **RF-ADM-055:** El sistema debe permitir al administrador eliminar un evento existente.
- **RF-ADM-056:** El sistema debe cargar los eventos existentes desde la base de datos al inicializar el calendario.
- **RF-ADM-057:** El sistema debe filtrar los eventos mostrados en el calendario según el rol y las asignaciones del usuario que ha iniciado sesión (ej. un profesor solo ve sus eventos, un estudiante los de su curso, etc.).
- **RF-ADM-058:** El sistema debe proporcionar listas desplegables de usuarios (profesores) y cursos (grados/secciones) para seleccionar destinatarios específicos de eventos.
- **RF-ADM-059:** El sistema debe permitir seleccionar múltiples destinatarios específicos (usuarios o cursos) para un evento.

#### Requerimientos No Funcionales (RNF)

- **RNF-USAB-011:** La interfaz de creación/edición de eventos debe ser un modal que se superpone al calendario, facilitando la interacción.
- **RNF-USAB-012:** Los eventos deben ser visualmente distinguibles en el calendario (por color y nombre).
- **RNF-PERF-004:** Todas las operaciones de gestión de eventos (CRUD) y carga de datos deben realizarse de forma asíncrona.
- **RNF-SEC-011:** Solo los usuarios autenticados y con los permisos adecuados (ej. administrador) deben poder crear, editar o eliminar eventos.
- **RNF-DATA-004:** Los roles y IDs de los destinatarios de un evento deben almacenarse de manera que permitan una fácil consulta y filtrado (ej. como cadenas separadas por comas).

### Módulo: Perfil de Usuario

#### Requerimientos Funcionales (RF)

- **RF-ADM-060:** El sistema debe permitir al administrador visualizar su información personal (nombres, apellidos, tipo y número de documento, fechas de expedición y nacimiento, teléfono, sexo, estado civil, dirección, email, RH, alergias).
- **RF-ADM-061:** El sistema debe permitir al administrador editar su información personal, incluyendo nombres, apellidos, teléfono, dirección, email, RH y alergias.
- **RF-ADM-062:** Ciertos campos de información personal (tipo y número de documento, fechas de expedición y nacimiento, sexo, estado civil) deben ser de solo lectura.
- **RF-ADM-063:** El sistema debe permitir al administrador cambiar su foto de perfil.
- **RF-ADM-064:** El sistema debe permitir al administrador cambiar su contraseña.
- **RF-ADM-065:** Para cambiar la contraseña, el administrador debe proporcionar su contraseña actual, la nueva contraseña y confirmar la nueva contraseña.
- **RF-ADM-066:** El sistema debe validar que la contraseña actual proporcionada sea correcta antes de permitir el cambio.
- **RF-ADM-067:** El sistema debe validar que la nueva contraseña y la confirmación de la nueva contraseña coincidan.
- **RF-ADM-068:** El sistema debe actualizar la información del perfil en la tabla `login` (para el email) y en la tabla específica del rol (`tab_administradores`) para los demás datos.
- **RF-ADM-069:** El sistema debe actualizar la foto de perfil en la tabla `login` con la nueva URL de la imagen.

#### Requerimientos No Funcionales (RNF)

- **RNF-SEC-012:** La contraseña actual debe ser verificada de forma segura (usando `password_verify`) antes de permitir el cambio.
- **RNF-SEC-013:** La nueva contraseña debe ser almacenada en la base de datos utilizando un algoritmo de hash seguro (`password_hash`).
- **RNF-SEC-014:** La subida de fotos de perfil debe validar el tipo de archivo (solo imágenes) y el tamaño (límite de 5MB).
- **RNF-SEC-015:** Las operaciones de actualización de perfil y cambio de contraseña deben estar protegidas por la sesión del usuario.
- **RNF-DATA-005:** Las actualizaciones del perfil (tanto en la tabla `login` como en la tabla específica del rol) deben realizarse dentro de una transacción de base de datos para garantizar la atomicidad.
- **RNF-PERF-005:** Las operaciones de actualización de perfil, cambio de contraseña y subida de foto deben realizarse de forma asíncrona.
- **RNF-USAB-013:** La interfaz debe proporcionar retroalimentación clara sobre el éxito o fracaso de las operaciones.
- **RNF-USAB-014:** La foto de perfil debe ser comprimida antes de ser guardada para optimizar el almacenamiento y la carga.

### Módulo: Gestión de PQRSF

#### Requerimientos Funcionales (RF)

- **RF-ADM-070:** El sistema debe permitir al administrador visualizar una lista de todas las PQRSF registradas.
- **RF-ADM-071:** La lista de PQRSF debe mostrar información clave como Tipo, Descripción, Sobre (destinatario), Fecha de Envío y Estado.
- **RF-ADM-072:** El sistema debe permitir al administrador filtrar la lista de PQRSF por tipo (Petición, Queja, Reclamo, Sugerencia, Felicitación, Solicitud sobre Datos Personales).
- **RF-ADM-073:** El sistema debe permitir al administrador filtrar la lista de PQRSF por estado (Pendiente, En proceso, Resuelto).
- **RF-ADM-074:** El sistema debe permitir al administrador buscar PQRSF por texto en la descripción o en otros campos relevantes.
- **RF-ADM-075:** El sistema debe permitir al administrador ver los detalles completos de una PQRSF específica en un modal.
- **RF-ADM-076:** Los detalles de la PQRSF deben incluir ID, Tipo, Descripción, Destinatario (categoría y específico si aplica), Fecha de Creación, Estado y un enlace al archivo adjunto si existe.
- **RF-ADM-077:** El sistema debe permitir al administrador editar una PQRSF existente.
- **RF-ADM-078:** Para editar una PQRSF, el administrador debe poder modificar el nombre del solicitante, tipo, contacto, descripción, categoría del destinatario y destinatario específico.
- **RF-ADM-079:** El sistema debe permitir al administrador eliminar una PQRSF existente.
- **RF-ADM-080:** El sistema debe solicitar confirmación al administrador antes de eliminar una PQRSF.
- **RF-ADM-081:** El sistema debe permitir al administrador crear una nueva PQRSF.
- **RF-ADM-082:** Al crear/editar una PQRSF, el administrador debe poder especificar:
    - Nombre del solicitante.
    - Tipo de PQRSF.
    - Contacto del solicitante (email o teléfono).
    - Descripción.
    - Categoría del destinatario (Profesor, Estudiante, Coordinador, Personal del colegio, Estructura del colegio).
    - Destinatario específico (si la categoría lo requiere, ej. un profesor o estudiante de una lista).
    - Adjuntar un archivo (PDF, Word, imagen o video).
- **RF-ADM-083:** El sistema debe cargar dinámicamente listas de usuarios (profesores, estudiantes) para la selección de destinatarios específicos.
- **RF-ADM-084:** El sistema debe requerir la aceptación de la Política de Tratamiento de Datos Personales para enviar/guardar una PQRSF.

#### Requerimientos No Funcionales (RNF)

- **RNF-USAB-015:** La interfaz de gestión de PQRSF debe ser intuitiva, con filtros y búsqueda para facilitar la localización de información.
- **RNF-USAB-016:** La creación y edición de PQRSF debe realizarse a través de un modal.
- **RNF-PERF-006:** Todas las operaciones (carga, filtrado, búsqueda, creación, edición, eliminación) deben realizarse de forma asíncrona.
- **RNF-SEC-016:** La subida de archivos adjuntos debe validar el tipo y tamaño del archivo.
- **RNF-DATA-006:** Los archivos adjuntos deben almacenarse de forma segura y su ruta debe guardarse en la base de datos.

### Módulo: Ayuda

#### Requerimientos Funcionales (RF)

- **RF-ADM-085:** El sistema debe proporcionar una sección de ayuda que describa las funcionalidades y el uso de los módulos disponibles para el administrador.
- **RF-ADM-086:** La sección de ayuda debe incluir descripciones detalladas para los módulos de Inicio, Agregar Usuario, Calendario y PQRSF.

#### Requerimientos No Funcionales (RNF)

- **RNF-USAB-017:** La información de ayuda debe estar organizada de manera clara y fácil de leer, utilizando títulos y listas.
- **RNF-USAB-018:** La sección de ayuda debe ser accesible desde el menú de navegación del administrador.

### Módulo: Restablecimiento de Contraseñas de Usuarios

#### Requerimientos Funcionales (RF)

- **RF-ADM-087:** El sistema debe permitir al administrador restablecer la contraseña de cualquier usuario del sistema.
- **RF-ADM-088:** Para restablecer una contraseña, el administrador debe proporcionar el número de documento del usuario y la nueva contraseña.
- **RF-ADM-089:** El sistema debe validar que el número de documento y la nueva contraseña no estén vacíos.
- **RF-ADM-090:** El sistema debe actualizar la contraseña del usuario en la tabla `login` con la nueva contraseña proporcionada.
- **RF-ADM-091:** El sistema debe informar al administrador si la contraseña se actualizó exitosamente o si el usuario no fue encontrado.

#### Requerimientos No Funcionales (RNF)

- **RNF-SEC-017:** Solo los usuarios con rol de administrador deben tener acceso a esta funcionalidad.
- **RNF-SEC-018:** La nueva contraseña debe ser almacenada en la base de datos utilizando un algoritmo de hash seguro (`password_hash`).
- **RNF-USAB-019:** La interfaz debe proporcionar retroalimentación clara sobre el éxito o fracaso de la operación.
## Rol: Profesor

### Módulo: Dashboard

#### Requerimientos Funcionales (RF)

- **RF-PROF-001:** El sistema debe presentar un panel de control (dashboard) como página principal para el rol de profesor.
- **RF-PROF-002:** El dashboard debe mostrar estadísticas clave para el profesor, incluyendo:
    - El número total de estudiantes a los que imparte clases.
    - El número total de cursos (grados/secciones) a los que está asignado.
    - El promedio general de calificaciones de sus estudiantes.
- **RF-PROF-003:** El dashboard debe identificar si el profesor es director de grupo de algún grado/sección.
- **RF-PROF-004:** Si el profesor es director de grupo, el dashboard debe mostrar el grado/sección a su cargo.
- **RF-PROF-005:** Si el profesor es director de grupo, el dashboard debe listar los estudiantes de su grado/sección, mostrando su nombre, apellido, documento y email.
- **RF-PROF-006:** La lista de estudiantes del grado a cargo debe mostrar inicialmente un número limitado de estudiantes (ej. 5) y ofrecer una opción para ver todos.
- **RF-PROF-007:** El dashboard debe incluir un gráfico de barras que visualice el rendimiento académico de los estudiantes del profesor, mostrando la calificación final por estudiante y materia.
- **RF-PROF-008:** El dashboard debe incluir una sección de navegación (menú lateral) con enlaces a todas las funcionalidades del profesor: Inicio, Tomar Asistencia, Calendario, Calificar, PQRSF, Perfil, Ayuda y Cerrar Sesión.
- **RF-PROF-009:** El dashboard debe mostrar el nombre y rol del profesor que ha iniciado sesión.

#### Requerimientos No Funcionales (RNF)

- **RNF-SEC-019:** El acceso al panel de profesor debe estar restringido únicamente a usuarios con el rol 'profesor'.
- **RNF-PERF-008:** Todos los datos del dashboard (estadísticas, información de director de grupo, lista de estudiantes, datos para gráficos) deben cargarse de forma asíncrona.
- **RNF-USAB-020:** La información en el dashboard debe ser clara y fácil de interpretar, utilizando gráficos para la visualización de rendimiento.

### Módulo: Toma de Asistencia

#### Requerimientos Funcionales (RF)

- **RF-PROF-010:** El sistema debe permitir al profesor seleccionar uno de los grados/secciones que tiene asignados.
- **RF-PROF-011:** El sistema debe cargar y mostrar una lista de todos los estudiantes matriculados en el grado/sección seleccionado.
- **RF-PROF-012:** Para cada estudiante, el profesor debe poder marcar su estado de asistencia como "Presente", "Ausente" o "Justificado".
- **RF-PROF-013:** Si el estado de asistencia es "Justificado", el sistema debe permitir al profesor adjuntar un archivo (excusa médica).
- **RF-PROF-014:** El sistema debe guardar los registros de asistencia para cada estudiante, asociándolos al profesor que toma la asistencia y a la fecha/hora actual.
- **RF-PROF-015:** El sistema debe asociar la URL de la excusa médica adjunta al registro de asistencia correspondiente si el estado es "Justificado".
- **RF-PROF-016:** El sistema debe validar que se haya adjuntado una excusa médica si el estado de asistencia es "Justificado".
- **RF-PROF-017:** El sistema debe permitir guardar la asistencia de múltiples estudiantes a la vez.

#### Requerimientos No Funcionales (RNF)

- **RNF-SEC-021:** Solo los profesores autenticados deben poder acceder y utilizar este módulo.
- **RNF-USAB-021:** La interfaz debe ser clara y fácil de usar para marcar la asistencia de múltiples estudiantes.
- **RNF-PERF-009:** La carga de grados y estudiantes, así como el guardado de la asistencia, deben realizarse de forma asíncrona.
- **RNF-DATA-007:** El guardado de la asistencia debe realizarse dentro de una transacción de base de datos para asegurar la integridad.
- **RNF-DATA-008:** La subida de archivos de excusas médicas debe validar el tipo (imágenes o PDF) y el tamaño (límite de 5MB) del archivo.
- **RNF-DATA-009:** Las imágenes de excusas médicas deben ser comprimidas antes de ser almacenadas.

### Módulo: Calendario y Gestión de Eventos

#### Requerimientos Funcionales (RF)

- **RF-PROF-018:** El sistema debe mostrar un calendario interactivo con vistas por mes y por semana para el profesor.
- **RF-PROF-019:** El calendario debe permitir la navegación entre meses/semanas anteriores y posteriores.
- **RF-PROF-020:** El calendario debe permitir volver rápidamente a la fecha actual ("Hoy").
- **RF-PROF-021:** El sistema debe permitir al profesor crear nuevos eventos.
- **RF-PROF-022:** Para cada evento, el profesor debe poder especificar:
    - Nombre del evento.
    - Descripción.
    - Fecha y hora de inicio.
    - Fecha y hora de fin.
    - Un color para identificar el evento visualmente.
    - Roles destinatarios (Padres, Estudiantes, Profesores).
    - Destinatarios específicos (usuarios individuales o cursos/secciones).
- **RF-PROF-023:** El sistema debe validar que la fecha y hora de fin de un evento no sean anteriores a la fecha y hora de inicio.
- **RF-PROF-024:** El sistema debe permitir al profesor editar los detalles de un evento existente que él haya creado.
- **RF-PROF-025:** El sistema debe permitir al profesor eliminar un evento existente que él haya creado.
- **RF-PROF-026:** El sistema debe cargar los eventos existentes desde la base de datos al inicializar el calendario.
- **RF-PROF-027:** El sistema debe filtrar los eventos mostrados en el calendario según el rol y las asignaciones del profesor (ej. solo ve sus eventos, los dirigidos a su rol o a sus cursos/secciones).
- **RF-PROF-028:** El sistema debe proporcionar listas desplegables de usuarios (profesores) y cursos (grados/secciones) para seleccionar destinatarios específicos de eventos.
- **RF-PROF-029:** El sistema debe permitir seleccionar múltiples destinatarios específicos (usuarios o cursos) para un evento.

#### Requerimientos No Funcionales (RNF)

- **RNF-SEC-022:** Solo los profesores autenticados deben poder acceder a este módulo.
- **RNF-USAB-022:** La interfaz de creación/edición de eventos debe ser un modal que se superpone al calendario, facilitando la interacción.
- **RNF-USAB-023:** Los eventos deben ser visualmente distinguibles en el calendario (por color y nombre).
- **RNF-PERF-010:** Todas las operaciones de gestión de eventos (CRUD) y carga de datos deben realizarse de forma asíncrona.
- **RNF-DATA-010:** Los roles y IDs de los destinatarios de un evento deben almacenarse de manera que permitan una fácil consulta y filtrado (ej. como cadenas separadas por comas).

### Módulo: Calificación

#### Requerimientos Funcionales (RF)

- **RF-PROF-030:** El sistema debe permitir al profesor seleccionar un grado/sección de una lista de grados disponibles.
- **RF-PROF-031:** Una vez seleccionado el grado, el sistema debe cargar dinámicamente las materias que el profesor imparte en ese grado.
- **RF-PROF-032:** Una vez seleccionada la materia, el sistema debe cargar dinámicamente la lista de estudiantes matriculados en ese grado/sección.
- **RF-PROF-033:** El sistema debe permitir al profesor registrar una nueva calificación para un estudiante, especificando:
    - El estudiante.
    - El tipo de evaluación (ej. Examen, Taller, Quiz, Participación).
    - La calificación numérica (entre 0.0 y 5.0).
    - La fecha de la calificación.
    - Comentarios opcionales sobre la calificación.
- **RF-PROF-034:** El sistema debe asociar la calificación al periodo académico correspondiente a la fecha de la calificación.
- **RF-PROF-035:** Si ya existe una calificación para el mismo estudiante, curso, tipo de evaluación y periodo, el sistema debe actualizarla en lugar de crear una nueva.
- **RF-PROF-036:** El sistema debe mostrar una tabla de calificaciones recientes, agrupadas por grado y estudiante.
- **RF-PROF-037:** La tabla de calificaciones recientes debe permitir expandir/contraer la vista para ver los detalles de las calificaciones de cada estudiante.
- **RF-PROF-038:** Los detalles de las calificaciones deben incluir fecha, materia, tipo de evaluación, nota y comentario.
- **RF-PROF-039:** El sistema debe calcular y mostrar el promedio de las calificaciones de cada estudiante en la vista de calificaciones recientes.

#### Requerimientos No Funcionales (RNF)

- **RNF-USAB-024:** La interfaz de calificación debe ser intuitiva, guiando al profesor a través de la selección de grado, materia y estudiante.
- **RNF-USAB-025:** El campo de calificación debe validar la entrada para asegurar que sea un número entre 0.0 y 5.0, permitiendo un decimal.
- **RNF-PERF-011:** La carga de listas (grados, materias, estudiantes) y el guardado/actualización de calificaciones deben realizarse de forma asíncrona.
- **RNF-DATA-011:** El sistema debe asegurar que cada calificación esté asociada a un periodo académico válido.
- **RNF-DATA-012:** La fecha de la calificación no debe ser anterior a la fecha actual.

### Módulo: Gestión de PQRSF

#### Requerimientos Funcionales (RF)

- **RF-PROF-040:** El sistema debe permitir al profesor visualizar una lista de todas las PQRSF registradas.
- **RF-PROF-041:** La lista de PQRSF debe mostrar información clave como Tipo, Descripción, Sobre (destinatario), Fecha de Envío y Estado.
- **RF-PROF-042:** El sistema debe permitir al profesor filtrar la lista de PQRSF por tipo (Petición, Queja, Reclamo, Sugerencia, Felicitación).
- **RF-PROF-043:** El sistema debe permitir al profesor filtrar la lista de PQRSF por estado (Pendiente, En proceso, Resuelto).
- **RF-PROF-044:** El sistema debe permitir al profesor buscar PQRSF por texto en la descripción o en otros campos relevantes.
- **RF-PROF-045:** El sistema debe permitir al profesor ver los detalles completos de una PQRSF específica en un modal.
- **RF-PROF-046:** Los detalles de la PQRSF deben incluir ID, Tipo, Descripción, Destinatario (categoría y específico si aplica), Fecha de Creación, Estado y un enlace al archivo adjunto si existe.
- **RF-PROF-047:** El sistema debe permitir al profesor editar una PQRSF existente.
- **RF-PROF-048:** Para editar una PQRSF, el profesor debe poder modificar el nombre del solicitante, tipo, contacto, descripción, categoría del destinatario y destinatario específico.
- **RF-PROF-049:** El sistema debe permitir al profesor eliminar una PQRSF existente.
- **RF-PROF-050:** El sistema debe solicitar confirmación al profesor antes de eliminar una PQRSF.
- **RF-PROF-051:** El sistema debe permitir al profesor crear una nueva PQRSF.
- **RF-PROF-052:** Al crear/editar una PQRSF, el profesor debe poder especificar:
    - Nombre del solicitante.
    - Tipo de PQRSF.
    - Contacto del solicitante (email o teléfono).
    - Descripción.
    - Categoría del destinatario (Profesor, Estudiante, Coordinador, Personal del colegio, Estructura del colegio).
    - Destinatario específico (si la categoría lo requiere, ej. un profesor o estudiante de una lista).
    - Adjuntar un archivo (PDF, Word, imagen o video).
- **RF-PROF-053:** El sistema debe cargar dinámicamente listas de usuarios (profesores, estudiantes) para la selección de destinatarios específicos.
- **RF-PROF-054:** El sistema debe requerir la aceptación de la Política de Tratamiento de Datos Personales para enviar/guardar una PQRSF.

#### Requerimientos No Funcionales (RNF)

- **RNF-SEC-023:** Solo los profesores autenticados deben poder acceder a este módulo.
- **RNF-USAB-026:** La interfaz de gestión de PQRSF debe ser intuitiva, con filtros y búsqueda para facilitar la localización de información.
- **RNF-USAB-027:** La creación y edición de PQRSF debe realizarse a través de un modal.
- **RNF-PERF-012:** Todas las operaciones (carga, filtrado, búsqueda, creación, edición, eliminación) deben realizarse de forma asíncrona.
- **RNF-SEC-024:** La subida de archivos adjuntos debe validar el tipo y tamaño del archivo.
- **RNF-DATA-013:** Los archivos adjuntos deben almacenarse de forma segura y su ruta debe guardarse en la base de datos.

### Módulo: Perfil de Usuario

#### Requerimientos Funcionales (RF)

- **RF-PROF-055:** El sistema debe permitir al profesor visualizar su información personal (nombres, apellidos, tipo y número de documento, ciudad de expedición, estado civil, fecha de nacimiento, teléfono, email, RH, alergias).
- **RF-PROF-056:** El sistema debe permitir al profesor editar su información personal, incluyendo nombres, apellidos, teléfono, email, RH y alergias.
- **RF-PROF-057:** Ciertos campos de información personal (tipo y número de documento, ciudad de expedición, estado civil, fecha de nacimiento) deben ser de solo lectura.
- **RF-PROF-058:** El sistema debe permitir al profesor cambiar su foto de perfil.
- **RF-PROF-059:** El sistema debe permitir al profesor cambiar su contraseña.
- **RF-PROF-060:** Para cambiar la contraseña, el profesor debe proporcionar su contraseña actual, la nueva contraseña y confirmar la nueva contraseña.
- **RF-PROF-061:** El sistema debe validar que la contraseña actual proporcionada sea correcta antes de permitir el cambio.
- **RF-PROF-062:** El sistema debe validar que la nueva contraseña y la confirmación de la nueva contraseña coincidan.
- **RF-PROF-063:** El sistema debe actualizar la información del perfil en la tabla `login` (para el email) y en la tabla específica del rol (`tab_profesores`) para los demás datos.
- **RF-PROF-064:** El sistema debe actualizar la foto de perfil en la tabla `login` con la nueva URL de la imagen.

#### Requerimientos No Funcionales (RNF)

- **RNF-SEC-025:** La contraseña actual debe ser verificada de forma segura (usando `password_verify`) antes de permitir el cambio.
- **RNF-SEC-026:** La nueva contraseña debe ser almacenada en la base de datos utilizando un algoritmo de hash seguro (`password_hash`).
- **RNF-SEC-027:** La subida de fotos de perfil debe validar el tipo de archivo (solo imágenes) y el tamaño (límite de 5MB).
- **RNF-SEC-028:** Las operaciones de actualización de perfil y cambio de contraseña deben estar protegidas por la sesión del usuario.
- **RNF-DATA-014:** Las actualizaciones del perfil (tanto en la tabla `login` como en la tabla específica del rol) deben realizarse dentro de una transacción de base de datos para garantizar la atomicidad.
- **RNF-PERF-013:** Las operaciones de actualización de perfil, cambio de contraseña y subida de foto deben realizarse de forma asíncrona.
- **RNF-USAB-028:** La interfaz debe proporcionar retroalimentación clara sobre el éxito o fracaso de las operaciones.
- **RNF-DATA-012:** La fecha de la calificación no debe ser anterior a la fecha actual.

### Módulo: Gestión de PQRSF

#### Requerimientos Funcionales (RF)

- **RF-PROF-040:** El sistema debe permitir al profesor visualizar una lista de todas las PQRSF registradas.
- **RF-PROF-041:** La lista de PQRSF debe mostrar información clave como Tipo, Descripción, Sobre (destinatario), Fecha de Envío y Estado.
- **RF-PROF-042:** El sistema debe permitir al profesor filtrar la lista de PQRSF por tipo (Petición, Queja, Reclamo, Sugerencia, Felicitación).
- **RF-PROF-043:** El sistema debe permitir al profesor filtrar la lista de PQRSF por estado (Pendiente, En proceso, Resuelto).
- **RF-PROF-044:** El sistema debe permitir al profesor buscar PQRSF por texto en la descripción o en otros campos relevantes.
- **RF-PROF-045:** El sistema debe permitir al profesor ver los detalles completos de una PQRSF específica en un modal.
- **RF-PROF-046:** Los detalles de la PQRSF deben incluir ID, Tipo, Descripción, Destinatario (categoría y específico si aplica), Fecha de Creación, Estado y un enlace al archivo adjunto si existe.
- **RF-PROF-047:** El sistema debe permitir al profesor editar una PQRSF existente.
- **RF-PROF-048:** Para editar una PQRSF, el profesor debe poder modificar el nombre del solicitante, tipo, contacto, descripción, categoría del destinatario y destinatario específico.
- **RF-PROF-049:** El sistema debe permitir al profesor eliminar una PQRSF existente.
- **RF-PROF-050:** El sistema debe solicitar confirmación al profesor antes de eliminar una PQRSF.
- **RF-PROF-051:** El sistema debe permitir al profesor crear una nueva PQRSF.
- **RF-PROF-052:** Al crear/editar una PQRSF, el profesor debe poder especificar:
    - Nombre del solicitante.
    - Tipo de PQRSF.
    - Contacto del solicitante (email o teléfono).
    - Descripción.
    - Categoría del destinatario (Profesor, Estudiante, Coordinador, Personal del colegio, Estructura del colegio).
    - Destinatario específico (si la categoría lo requiere, ej. un profesor o estudiante de una lista).
    - Adjuntar un archivo (PDF, Word, imagen o video).
- **RF-PROF-053:** El sistema debe cargar dinámicamente listas de usuarios (profesores, estudiantes) para la selección de destinatarios específicos.
- **RF-PROF-054:** El sistema debe requerir la aceptación de la Política de Tratamiento de Datos Personales para enviar/guardar una PQRSF.

#### Requerimientos No Funcionales (RNF)

- **RNF-SEC-023:** Solo los profesores autenticados deben poder acceder a este módulo.
- **RNF-USAB-026:** La interfaz de gestión de PQRSF debe ser intuitiva, con filtros y búsqueda para facilitar la localización de información.
- **RNF-USAB-027:** La creación y edición de PQRSF debe realizarse a través de un modal.
- **RNF-PERF-012:** Todas las operaciones (carga, filtrado, búsqueda, creación, edición, eliminación) deben realizarse de forma asíncrona.
- **RNF-SEC-024:** La subida de archivos adjuntos debe validar el tipo y tamaño del archivo.
- **RNF-DATA-013:** Los archivos adjuntos deben almacenarse de forma segura y su ruta debe guardarse en la base de datos.

### Módulo: Perfil de Usuario

#### Requerimientos Funcionales (RF)

- **RF-PROF-055:** El sistema debe permitir al profesor visualizar su información personal (nombres, apellidos, tipo y número de documento, ciudad de expedición, estado civil, fecha de nacimiento, teléfono, email, RH, alergias).
- **RF-PROF-056:** El sistema debe permitir al profesor editar su información personal, incluyendo nombres, apellidos, teléfono, email, RH y alergias.
- **RF-PROF-057:** Ciertos campos de información personal (tipo y número de documento, ciudad de expedición, estado civil, fecha de nacimiento) deben ser de solo lectura.
- **RF-PROF-058:** El sistema debe permitir al profesor cambiar su foto de perfil.
- **RF-PROF-059:** El sistema debe permitir al profesor cambiar su contraseña.
- **RF-PROF-060:** Para cambiar la contraseña, el profesor debe proporcionar su contraseña actual, la nueva contraseña y confirmar la nueva contraseña.
- **RF-PROF-061:** El sistema debe validar que la contraseña actual proporcionada sea correcta antes de permitir el cambio.
- **RF-PROF-062:** El sistema debe validar que la nueva contraseña y la confirmación de la nueva contraseña coincidan.
- **RF-PROF-063:** El sistema debe actualizar la información del perfil en la tabla `login` (para el email) y en la tabla específica del rol (`tab_profesores`) para los demás datos.
- **RF-PROF-064:** El sistema debe actualizar la foto de perfil en la tabla `login` con la nueva URL de la imagen.

#### Requerimientos No Funcionales (RNF)

- **RNF-SEC-025:** La contraseña actual debe ser verificada de forma segura (usando `password_verify`) antes de permitir el cambio.
- **RNF-SEC-026:** La nueva contraseña debe ser almacenada en la base de datos utilizando un algoritmo de hash seguro (`password_hash`).
- **RNF-SEC-027:** La subida de fotos de perfil debe validar el tipo de archivo (solo imágenes) y el tamaño (límite de 5MB).
- **RNF-SEC-028:** Las operaciones de actualización de perfil y cambio de contraseña deben estar protegidas por la sesión del usuario.
- **RNF-DATA-014:** Las actualizaciones del perfil (tanto en la tabla `login` como en la tabla específica del rol) deben realizarse dentro de una transacción de base de datos para garantizar la atomicidad.
- **RNF-PERF-013:** Las operaciones de actualización de perfil, cambio de contraseña y subida de foto deben realizarse de forma asíncrona.
- **RNF-USAB-028:** La interfaz debe proporcionar retroalimentación clara sobre el éxito o fracaso de las operaciones.
- **RNF-USAB-029:** La foto de perfil debe ser comprimida antes de ser guardada para optimizar el almacenamiento y la carga.

### Módulo: Ayuda

#### Requerimientos Funcionales (RF)

- **RF-PROF-065:** El sistema debe proporcionar una sección de ayuda que describa las funcionalidades y el uso de los módulos disponibles para el profesor.
- **RF-PROF-066:** La sección de ayuda debe incluir descripciones detalladas para los módulos de Inicio, Calendario, Calificar y PQRSF.

#### Requerimientos No Funcionales (RNF)

- **RNF-USAB-030:** La información de ayuda debe estar organizada de manera clara y fácil de leer, utilizando títulos y párrafos.
- **RNF-DATA-016:** Los archivos adjuntos deben almacenarse de forma segura y su ruta debe guardarse en la base de datos.

### Módulo: Perfil de Usuario

#### Requerimientos Funcionales (RF)

- **RF-EST-037:** El sistema debe permitir al estudiante visualizar su información personal (nombres, apellidos, tipo y número de documento, fecha de expedición, fecha de nacimiento, sexo, RH, dirección, email, teléfono, alergias).
- **RF-EST-038:** El sistema debe permitir al estudiante editar su información personal, incluyendo nombres, apellidos, dirección, email, teléfono y alergias.
- **RF-EST-039:** Ciertos campos de información personal (tipo y número de documento, fecha de expedición, fecha de nacimiento, sexo, RH) deben ser de solo lectura.
- **RF-EST-040:** El sistema debe permitir al estudiante cambiar su foto de perfil.
- **RF-EST-041:** El sistema debe permitir al estudiante cambiar su contraseña.
- **RF-EST-042:** Para cambiar la contraseña, el estudiante debe proporcionar su contraseña actual, la nueva contraseña y confirmar la nueva contraseña.
- **RF-EST-043:** El sistema debe validar que la contraseña actual proporcionada sea correcta antes de permitir el cambio.
- **RF-EST-044:** El sistema debe validar que la nueva contraseña y la confirmación de la nueva contraseña coincidan.
- **RF-EST-045:** El sistema debe actualizar la información del perfil en la tabla `login` (para el email) y en la tabla específica del rol (`tab_estudiante`) para los demás datos.
- **RF-EST-046:** El sistema debe actualizar la foto de perfil en la tabla `login` con la nueva URL de la imagen.

#### Requerimientos No Funcionales (RNF)

- **RNF-SEC-037:** La contraseña actual debe ser verificada de forma segura (usando `password_verify`) antes de permitir el cambio.
- **RNF-SEC-038:** La nueva contraseña debe ser almacenada en la base de datos utilizando un algoritmo de hash seguro (`password_hash`).
- **RNF-SEC-039:** La subida de fotos de perfil debe validar el tipo de archivo (solo imágenes) y el tamaño (límite de 5MB).
- **RNF-SEC-040:** Las operaciones de actualización de perfil y cambio de contraseña deben estar protegidas por la sesión del usuario.
- **RNF-DATA-017:** Las actualizaciones del perfil (tanto en la tabla `login` como en la tabla específica del rol) deben realizarse dentro de una transacción de base de datos para garantizar la atomicidad.
- **RNF-PERF-017:** Las operaciones de actualización de perfil, cambio de contraseña y subida de foto deben realizarse de forma asíncrona.
- **RNF-USAB-038:** La interfaz debe proporcionar retroalimentación clara sobre el éxito o fracaso de las operaciones.
- **RNF-USAB-039:** La foto de perfil debe ser comprimida antes de ser guardada para optimizar el almacenamiento y la carga.


## Rol: Padre

### Módulo: Dashboard de Seguimiento Académico

#### Requerimientos Funcionales (RF)

- **RF-PAD-001:** El sistema debe permitir al padre seleccionar a uno de sus hijos asociados para ver su información académica.
- **RF-PAD-002:** El sistema debe cargar y mostrar la información básica del hijo seleccionado, incluyendo su nombre completo, grado, sección, edad y foto de perfil.
- **RF-PAD-003:** El sistema debe calcular y mostrar el promedio general de calificaciones del hijo seleccionado.
- **RF-PAD-004:** El sistema debe identificar y mostrar la materia con la calificación promedio más alta del hijo ("Mejor Materia").
- **RF-PAD-005:** El sistema debe identificar y mostrar la materia con la calificación promedio más baja del hijo ("Área a Mejorar").
- **RF-PAD-006:** El sistema debe proporcionar un resumen cualitativo del desempeño general del hijo (ej. "Excelente", "Bueno", "Promedio", "Bajo") basado en su promedio general.
- **RF-PAD-007:** El sistema debe mostrar un desglose detallado de las calificaciones del hijo, agrupadas por materia.
- **RF-PAD-008:** Para cada materia, el desglose debe incluir el nombre del profesor, el promedio de la materia y una lista de calificaciones individuales (fecha, tipo de evaluación, nota, comentario).
- **RF-PAD-009:** El desglose de calificaciones por materia debe ser interactivo, permitiendo expandir/contraer la vista para cada materia (acordeón).
- **RF-PAD-010:** El dashboard debe incluir una sección de navegación (menú lateral) con enlaces a todas las funcionalidades del padre: Inicio, Calendario, PQRSF, Perfil, Ayuda y Cerrar Sesión.

#### Requerimientos No Funcionales (RNF)

- **RNF-SEC-042:** El acceso al panel de padre debe estar restringido únicamente a usuarios con el rol 'padre'.
- **RNF-SEC-043:** Un padre solo debe poder ver la información académica de los hijos que están asociados a su cuenta.
- **RNF-PERF-018:** Todos los datos del dashboard (lista de hijos, información del hijo, calificaciones, resúmenes) deben cargarse de forma asíncrona.
- **RNF-USAB-042:** La interfaz debe ser clara y fácil de usar, permitiendo al padre seleccionar rápidamente entre sus hijos y visualizar su rendimiento.
- **RNF-USAB-043:** Las calificaciones y promedios deben presentarse de forma visualmente clara (ej. con insignias o colores).

### Módulo: Calendario y Gestión de Eventos

#### Requerimientos Funcionales (RF)

- **RF-PAD-011:** El sistema debe mostrar un calendario interactivo con vistas por mes y por semana para el padre.
- **RF-PAD-012:** El calendario debe permitir la navegación entre meses/semanas anteriores y posteriores.
- **RF-PAD-013:** El calendario debe permitir volver rápidamente a la fecha actual ("Hoy").
- **RF-PAD-014:** El sistema debe permitir al padre crear nuevos eventos.
- **RF-PAD-015:** Para cada evento, el padre debe poder especificar:
    - Nombre del evento.
    - Descripción (aunque el campo no está visible en el HTML, la lógica lo soporta).
    - Fecha y hora de inicio.
    - Fecha y hora de fin.
    - Un color para identificar el evento visualmente.
- **RF-PAD-016:** El sistema debe validar que la fecha y hora de fin de un evento no sean anteriores a la fecha y hora de inicio.
- **RF-PAD-017:** El sistema debe permitir al padre editar los detalles de un evento existente que él haya creado.
- **RF-PAD-018:** El sistema debe permitir al padre eliminar un evento existente que él haya creado.
- **RF-PAD-019:** El sistema debe cargar los eventos existentes desde la base de datos al inicializar el calendario.
- **RF-PAD-020:** El sistema debe filtrar los eventos mostrados en el calendario según el rol y las asignaciones del padre (ej. solo ve sus eventos, los dirigidos a su rol o a la sección de sus hijos).

#### Requerimientos No Funcionales (RNF)

- **RNF-SEC-044:** Solo los padres autenticados deben poder acceder a este módulo.
- **RNF-USAB-044:** La interfaz de creación/edición de eventos debe ser un modal que se superpone al calendario, facilitando la interacción.
- **RNF-USAB-045:** Los eventos deben ser visualmente distinguibles en el calendario (por color y nombre).
- **RNF-PERF-019:** Todas las operaciones de gestión de eventos (CRUD) y carga de datos deben realizarse de forma asíncrona.
- **RNF-DATA-018:** Los eventos creados por el padre no tendrán destinatarios específicos (roles o IDs) ya que la interfaz no lo permite.
### Módulo: Ayuda

#### Requerimientos Funcionales (RF)

- **RF-PROF-065:** El sistema debe proporcionar una sección de ayuda que describa las funcionalidades y el uso de los módulos disponibles para el profesor.
- **RF-PROF-066:** La sección de ayuda debe incluir descripciones detalladas para los módulos de Inicio, Calendario, Calificar y PQRSF.

#### Requerimientos No Funcionales (RNF)

- **RNF-USAB-030:** La información de ayuda debe estar organizada de manera clara y fácil de leer, utilizando títulos y párrafos.
- **RNF-USAB-031:** La sección de ayuda debe ser accesible desde el menú de navegación del profesor.
