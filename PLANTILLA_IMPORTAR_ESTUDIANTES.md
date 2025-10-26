# Plantilla para Importar Estudiantes

## Estructura del Archivo Excel

El archivo debe ser un .xlsx, .xls o .csv con las siguientes columnas:

### Campos Obligatorios:

| Campo | Descripción | Ejemplo |
|-------|-------------|---------|
| nombre_estudiante | Nombre(s) del estudiante | Juan Carlos |
| apellido_paterno_estudiante | Apellido paterno del estudiante | Pérez |
| apellido_materno_estudiante | Apellido materno del estudiante | García |
| correo_estudiante | Email del estudiante | juan.perez@escuela.com |
| nombre_padre | Nombre del padre/tutor | María |
| apellido_paterno_padre | Apellido paterno del padre (OBLIGATORIO) | López |
| apellido_materno_padre | Apellido materno del padre (OBLIGATORIO) | Rodríguez |
| correo_padres | Email del padre/tutor (OBLIGATORIO) | maria.lopez@email.com |
| grado | Grado escolar (1-6) | 2 |
| seccion | Sección (A, B, C, etc.) | A |
| sexo | Sexo (M para masculino, F para femenino) | M |
| curp | CURP de 18 caracteres | PEGARJ950101HDFRNN09 |
| fecha_nacimiento | Fecha de nacimiento en formato DD/MM/YYYY | 01/01/2010 |
| pais_nacimiento | País de nacimiento | México |
| estado_nacimiento | Estado de nacimiento | Jalisco |
| ciudad_nacimiento | Ciudad de nacimiento | Guadalajara |
| telefono | Teléfono de contacto | 3312345678 |
| domicilio | Domicilio/Dirección | Calle Principal 123, Apto 4 |

### Campos Opcionales:

| Campo | Descripción | Ejemplo | Comportamiento |
|-------|-------------|---------|-----------------|
| matricula | Número de matrícula | 20240001 | Se asigna automáticamente si no se proporciona |
| contrasena | Contraseña para el estudiante | MiPassword123 | Se usa "sisgemx123" si no se proporciona |

## Notas Importantes:

1. **Ciclo Escolar**: Los estudiantes se asignarán automáticamente al ciclo escolar activo.
   - Si no hay ciclo activo, el sistema te pedirá que crees uno primero.

2. **Grados y Secciones**: Se crean automáticamente si no existen.
   - Ejemplo: Grado 2, Sección A → Crea "2° Grado - A" si no existe

3. **Número de Matrícula**: 
   - Formato: [AÑO DEL CICLO][3 DÍGITOS]
   - Ejemplo: 2024001, 2024002, 2024003
   - Se asigna automáticamente en orden secuencial

4. **Usuarios**:
   - Se crean usuarios automáticos para estudiantes con rol "student"
   - Se crean usuarios automáticos para padres con rol "parent"
   - Las contraseñas por defecto son "sisgemx123" y deben ser cambiadas en el primer acceso

5. **Pagos/Colegiatura**: 
   - Se crean registros de colegiatura mensual para cada estudiante
   - La cantidad inicial es $0 hasta que se configure la colegiatura en el ciclo escolar

## Ejemplo de Fila:

```
Juan Carlos | Pérez | García | juan.perez@escuela.com | María | López | Rodríguez | maria.lopez@email.com | 20240001 | 2 | A | M | PEGARJ950101HDFRNN09 | 01/01/2010 | México | Jalisco | Guadalajara | 3312345678 | Calle Principal 123 | MiPassword123
```

## Errores Comunes:

- **Campo requerido faltante**: Verifica que todos los campos obligatorios estén completos
- **Fecha inválida**: Asegúrate de usar formato DD/MM/YYYY
- **CURP inválido**: El CURP debe tener exactamente 18 caracteres
- **Email duplicado**: Si un email ya existe en el sistema, no se duplicará el usuario

## Ubicación de la Plantilla:

Accede a la importación en: **Configuración → Importar Estudiantes**

