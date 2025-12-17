# Guion de Presentación: Sistema de Gestión Escolar SJB

**Duración total estimada:** 30 minutos
**Presentadores:** Daniel (Frontend) y Gabriela (Backend)

---

### 1. Introducción y Planteamiento del Problema (5 minutos)

**(Diapositiva 1: Título del Proyecto y Logo del Colegio)**

**Daniel (Frontend):** "Buenas tardes a todos. somos el gaes #2, conformado por mi compañera Andrea Gabriela Jaimes Oviedo y mi persona Keiner Daniel Bautista Angartia hoy tenemos el placer de presentarles el proyecto SJB, un Sistema de Gestión Escolar integral que hemos desarrollado para modernizar y optimizar los procesos del Colegio San Juan Bosco."

**(Diapositiva 2: La Necesidad del Colegio)**

**Daniel (Frontend):** "Antes de sumergirnos en la parte técnica, es crucial que vean el sistema desde la perspectiva del usuario para entender la necesidad que lo originó. El colegio enfrentaba desafíos que afectaban la experiencia diaria de la comunidad educativa:"

*   **Procesos Lentos y Manuales:** Imaginen a padres esperando días por las notas, o a profesores invirtiendo horas en pasar registros a mano. La experiencia era lenta y propensa a errores.
*   **Comunicación Fragmentada:** No existía un canal único y directo. La comunicación entre padres y profesores dependía de reuniones o notas en papel, dificultando un seguimiento constante.
*   **Acceso Difícil a la Información:** Para un estudiante, ver su propio progreso, o para un padre, consultar el historial de su hijo, era una tarea complicada. La información no estaba al alcance de la mano.

**Daniel (Frontend):** "La experiencia de usuario era deficiente. La necesidad era clara: crear una plataforma digital intuitiva, rápida y centralizada que pusiera la información importante al alcance de todos, mejorando la interacción y la comunicación."

---

### 2. Requerimientos y Objetivos del Proyecto (5 minutos)

**(Diapositiva 3: Objetivos Clave del Proyecto)**

**Gabriela (Backend):** "Gracias, Daniel. Para resolver los problemas que Daniel describió, establecimos una serie de requerimientos técnicos y funcionales que formarían la columna vertebral del sistema. Nuestro objetivo era construir un motor robusto y seguro."

*   **Centralización de la Información:** El primer requisito era diseñar una base de datos única y bien estructurada en PostgreSQL. Esto nos permitiría consolidar toda la información académica y administrativa, eliminando las islas de datos.
*   **Acceso Basado en Roles:** Debíamos garantizar que cada usuario solo pudiera ver y modificar lo que le corresponde. Implementamos una lógica de negocio en el backend para gestionar permisos y sesiones de forma segura.
*   **Automatización de Procesos:** Tradujimos las tareas manuales en funciones automatizadas. Por ejemplo, creamos scripts en PHP que calculan promedios, generan estadísticas y procesan inscripciones, reduciendo la carga administrativa.
*   **Creación de APIs:** Desarrollamos un conjunto de APIs (Interfaces de Programación de Aplicaciones) que actúan como puentes. Estas APIs permiten que el frontend, que Daniel les mostrará, pueda solicitar y enviar datos a la base de datos de manera segura y eficiente.
*   **Seguridad Integral:** La seguridad fue un pilar desde el inicio. Esto implicó encriptar todas las contraseñas, proteger la base de datos de inyecciones SQL y asegurar que toda la comunicación entre el cliente y el servidor estuviera protegida.

---

### 3. Arquitectura y Tecnologías Utilizadas (5 minutos)

**(Diapositiva 4: Arquitectura Tecnológica del Sistema)**

**Daniel (Frontend):** "Para construir la interfaz que los usuarios ven y con la que interactúan, utilizamos un conjunto de tecnologías modernas enfocadas en la experiencia de usuario."

*   **HTML5 y CSS3:** Son la base de todo lo visual. HTML para la estructura y CSS para el diseño y los estilos.
*   **Bootstrap:** Usamos este framework para asegurar que el sistema se vea bien y sea fácil de usar en cualquier dispositivo, desde un computador de escritorio hasta un teléfono móvil.
*   **JavaScript y jQuery:** Estas tecnologías nos permiten crear una experiencia dinámica. Cuando un usuario hace clic en un botón y ve una actualización instantánea sin que la página se recargue, es gracias a JavaScript, que se comunica con las APIs que Gabriela construyó.
*   **Chart.js:** Para que los datos no fueran solo números, integramos esta librería. Nos permite tomar las estadísticas del backend y presentarlas en forma de gráficos claros y fáciles de interpretar en los dashboards.

**Gabriela (Backend):** "Y para que todo lo que Daniel ha mostrado funcione, el backend se apoya en las siguientes tecnologías:"

*   **PHP:** Es el lenguaje principal del lado del servidor. Cada vez que un usuario inicia sesión, guarda una nota o pide un reporte, hay un script de PHP procesando esa solicitud, aplicando la lógica de negocio y comunicándose con la base de datos.
*   **Servidor Web Apache:** Es el entorno donde vive y se ejecuta nuestro código PHP, encargado de recibir las peticiones de los usuarios y devolverles la página web correspondiente.
*   **PostgreSQL:** Como mencioné, es nuestra base de datos. Su robustez y capacidad para manejar relaciones complejas nos permitieron modelar con precisión la estructura del colegio: estudiantes, padres, profesores, cursos, matrículas, todo está conectado de forma lógica y segura aquí.

---

### 4. ¿Cómo Funciona? Un Recorrido por el Sistema (10 minutos)

**(Diapositiva 5: Flujo de Usuario y Roles)**

**Daniel (Frontend):** "Ahora, hagamos un recorrido visual por los moduloss. Empecemos con el **Administrador**."
*   *[Mostrar captura del dashboard del admin]*
*   "Como pueden ver, el administrador tiene un dashboard muy visual con gráficos que resumen el estado del colegio. Los menús son claros y le permiten navegar fácilmente para agregar usuarios, ver listados o gestionar el calendario. Todo lo que se ve aquí está diseñado para ser intuitivo."

**Gabriela (Backend):** "Y cuando el administrador ve esos gráficos, es porque el frontend le pidió los datos a una API que yo desarrollé. Esa API ejecuta una consulta compleja en PostgreSQL, agrupa los datos y se los devuelve a Chart.js en el formato exacto que necesita para dibujar el gráfico. Lo mismo ocurre cuando se agrega un usuario: el formulario que ven se envía al backend, y PHP se encarga de crear el registro en 3 o 4 tablas diferentes de la base de datos de forma segura."

**Daniel (Frontend):** "Pasemos al **Profesor**."
*   *[Mostrar captura del dashboard del profesor]*
*   "El profesor ve sus cursos y una interfaz sencilla para calificar. Puede hacer clic, poner una nota y ver cómo el promedio se actualiza al instante. La experiencia es fluida, diseñada para ahorrarle tiempo."

**Gabriela (Backend):** "Esa actualización instantánea es un buen ejemplo de nuestra colaboración. Cuando Daniel envía la nueva nota, mi API en PHP no solo la guarda en la tabla `tab_calificaciones`, sino que recalcula el promedio del estudiante y devuelve ese nuevo valor de inmediato, para que el frontend pueda mostrarlo sin recargar la página."

**Daniel (Frontend):** "Ahora, el **Estudiante y el Padre de Familia**."
*   *[Mostrar capturas de los dashboards]*
*   "Ambos portales son principalmente de consulta. Presentan la información de forma muy gráfica y fácil de entender. El padre puede seleccionar a cuál de sus hijos quiere ver, y la interfaz se actualiza para mostrar solo la información de ese estudiante."

**Gabriela (Backend):** "Esa funcionalidad para el padre es interesante. Cuando el padre inicia sesión, mi código primero busca en la base de datos qué estudiantes están asociados a su ID de acudiente. Luego, le presenta esa lista a Daniel. Cuando el padre elige un hijo, el frontend me pide las notas para ese ID de estudiante específico, y el backend se las devuelve. La lógica de negocio asegura que un padre nunca pueda ver datos de estudiantes que no sean sus hijos."

**(Diapositiva 6: Funcionalidades Clave de Seguridad)**

**Gabriela (Backend):** "Finalmente, hablemos de seguridad, un tema de backend. El sistema es seguro por diseño. Cuando un usuario crea una contraseña, no la guardamos directamente. Usamos una función en PHP llamada `password_hash` para convertirla en un código encriptado e irreversible. Al iniciar sesión, hacemos el mismo proceso con la contraseña que ingresa el usuario y solo comparamos los códigos encriptados. Esto significa que nadie, ni siquiera nosotros, puede ver las contraseñas de los usuarios."

---

### 5. Conclusión y Preguntas (5 minutos)

**(Diapositiva 7: Resumen y Beneficios)**

**Daniel (Frontend):** "En conclusión, el proyecto SJB ofrece una interfaz de usuario moderna, limpia y accesible que resuelve los problemas de comunicación y acceso a la información que tenía la comunidad educativa. Hemos creado una experiencia digital que es útil y fácil de usar para todos."

**Gabriela (Backend):** "Y por debajo, hemos construido un sistema seguro, escalable y eficiente que automatiza los procesos clave del colegio. La arquitectura robusta asegura que el sistema no solo funcione hoy, sino que pueda crecer y adaptarse a las futuras necesidades de la institución."

**(Diapositiva 8: ¡Gracias! y Preguntas)**

**Daniel (Frontend):** "Juntos, hemos creado una solución integral que realmente moderniza la gestión del Colegio San Juan Bosco."

**Gabriela (Backend):** "Muchas gracias por su atención. Ahora, si tienen alguna pregunta, estaremos encantados de responderla."