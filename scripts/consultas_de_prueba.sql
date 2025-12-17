--1.TODOS LOS SELECT 
--SELECT * FROM login;
--SELECT * FROM tab_ficha_datos_estudiante_hogar;
--SELECT * FROM tab_ficha_datos_estudiante_salud;
--SELECT * FROM tab_ficha_datos_estudiante_social;
--SELECT * FROM tab_acudiente;
--SELECT * FROM tab_ficha_datos_estudiantes;
--SELECT * FROM tab_ficha_datos_estudiante;
--SELECT * FROM tab_cursos;
--SELECT * FROM tab_matriculas;
--SELECT * FROM tab_materias;
--SELECT * FROM tab_profesores;
--SELECT * FROM tab_calificaciones;
--SELECT * FROM tab_asistencia;
--SELECT * FROM tab_horarios;
--SELECT * FROM tab_comunicaciones;
--SELECT * FROM tab_pqrs;
--SELECT * FROM tab_actividades;

--2. SELECT CON FILTRO WHERE 
--SELECT * FROM login WHERE rol = 'admin';
--SELECT * FROM tab_ficha_datos_estudiante_hogar WHERE gruposisben = 'B1';
--SELECT * FROM tab_ficha_datos_estudiante_salud WHERE eps = 'sanitas';
--SELECT * FROM tab_ficha_datos_estudiante_social WHERE desplazado = 'no';
--SELECT * FROM tab_ficha_datos_estudiantes WHERE id_social = '1';
--SELECT * FROM tab_calificaciones WHERE id_profesor = '12';

--3.SELECT Y ORDER BY 
--SELECT * FROM tab_calificaciones ORDER BY id_profesor ASC;
--SELECT * FROM tab_asistencia ORDER BY estado ASC;

--4. Consulta SELECT con LIMIT
--SELECT * FROM tab_calificaciones LIMIT 10;
--SELECT * FROM tab_asistencia LIMIT 10;
--SELECT * FROM tab_matriculas LIMIT 10;

--5. Consulta SELECT con COUNT
--SELECT COUNT(*) FROM tab_calificaciones;
--SELECT COUNT(*) FROM tab_asistencia;
--SELECT COUNT(*) FROM tab_matriculas;
--SELECT COUNT(*) FROM tab_cursos;
--SELECT COUNT(*) FROM tab_profesores;

--6. Consulta SELECT con SUM
--SELECT SUM(calificacion) FROM tab_calificaciones;


--7. Consulta SELECT con AVG
--SELECT AVG(calificacion) FROM tab_calificaciones;


--8. Consulta SELECT con GROUP BY
--SELECT calificacion, COUNT(*) FROM tab_calificaciones GROUP BY calificacion;

--9. Consulta INSERT
--INSERT INTO login (usuario, contrasena, rol) VALUES ('562', crypt('AMOR1234', gen_salt('bf')),'administrativo');

--10. Consulta UPDATE
--UPDATE login SET contrasena = crypt('EMOR123', gen_salt('bf')) WHERE contrasena = crypt('AMOR1234', gen_salt('bf'));
--UPDATE tab_comunicaciones SET mensaje = '"Buenas tardes estimado pepito, te comunico que pasaste el año :D, ATT: cordinacion academica"' 
-- WHERE mensaje = '"Buenas tardes estimado pepito, te comunico que perdister el año :l, att; tu querido profesor"';

--11. Consulta DELETE
--DELETE FROM login WHERE usuario = '562';

--12. Consulta JOIN
SELECT a.columna1, b.columna2 
FROM tabla_a a 
JOIN tabla_b b ON a.id = b.id;

--13. Consulta con Subconsulta
SELECT * FROM nombre_de_la_tabla WHERE columna IN (SELECT columna FROM otra_tabla WHERE condicion);

--14. Consulta con CASE
SELECT columna, 
       CASE 
           WHEN columna > 10 THEN 'Mayor que 10'
           WHEN columna = 10 THEN 'Igual a 10'
           ELSE 'Menor que 10'
       END AS descripcion
FROM nombre_de_la_tabla;
Utiliza una expresión CASE para asignar una descripción basada en el valor de columna.
--15. Consulta con UNION
SELECT columna FROM tabla_a
UNION
SELECT columna FROM tabla_b;
Combina los resultados de dos consultas SELECT en un solo conjunto de resultados, eliminando duplicados.
--16. Consulta con UNION ALL
SELECT columna FROM tabla_a
UNION ALL
SELECT columna FROM tabla_b;
Combina los resultados de dos consultas SELECT en un solo conjunto de resultados, incluyendo duplicados.
--17. Consulta con HAVING
SELECT columna, COUNT(*) 
FROM nombre_de_la_tabla 
GROUP BY columna 
HAVING COUNT(*) > 1;
Filtra los grupos resultantes de un GROUP BY para incluir solo aquellos que cumplen con la condición HAVING.

--18. Consulta con EXISTS
SELECT * FROM nombre_de_la_tabla a
WHERE EXISTS (SELECT 1 FROM otra_tabla b WHERE a.id = b.id);
Selecciona todos los registros de nombre_de_la_tabla donde existe un registro correspondiente en otra_tabla con el mismo id.
--19. Consulta con LIKE
SELECT * FROM nombre_de_la_tabla WHERE columna LIKE '%patron%';
Selecciona todos los registros de nombre_de_la_tabla donde el valor de columna contiene el patrón 'patron'.
--20. Consulta con BETWEEN
SELECT * FROM nombre_de_la_tabla WHERE columna BETWEEN 10 AND 20;
Selecciona todos los registros de nombre_de_la_tabla donde el valor de columna está entre 10 y 20.










































