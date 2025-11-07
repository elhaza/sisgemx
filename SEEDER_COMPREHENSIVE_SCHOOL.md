# Seeder Integral de Escuela 2025-2026

## Descripción General

El `ComprehensiveSchoolSeeder` es un seeder especializado que crea una estructura académica completa y realista para el ciclo escolar 2025-2026, preservando todos los datos existentes en la base de datos.

### Características Principales

✅ **Conservación de datos existentes**: No borra anuncios, settings ni datos de ciclos anteriores
✅ **Estructura académica completa**: 6 grupos (1º a 6º grado), 1 salón por grupo
✅ **Alumnos realistas**: 84 alumnos (14 por grupo, 7 niños y 7 niñas)
✅ **Coherencia familiar**: Apellidos de alumnos coinciden con sus padres/tutores
✅ **Maestros**: 6 titulares + 3 especializados (Educación Física, Inglés, Música)
✅ **Sistema de pagos**: Cuotas mensuales de $3,000 MXN con pagos parciales/atrasados
✅ **Horarios completos**: 7:30 a.m. - 2:00 p.m. con receso 10:30-11:00 a.m.
✅ **Personas autorizadas**: Para recoger estudiantes (abuelos, tíos, etc.)

## Instalación y Uso

### Opción 1: Comando Artisan Personalizado (Recomendado)

```bash
php artisan db:seed-comprehensive-school
```

Este comando:
- Solicita confirmación antes de ejecutar
- Preserva todos los datos existentes
- Crea la estructura del ciclo 2025-2026
- Muestra un resumen detallado al finalizar

### Opción 2: Integración Manual en DatabaseSeeder

Edita `database/seeders/DatabaseSeeder.php` y agrega:

```php
public function run(): void
{
    $this->call([
        // ... otros seeders ...
        ComprehensiveSchoolSeeder::class,
    ]);
}
```

Luego ejecuta:

```bash
php artisan db:seed
```

### Opción 3: Ejecución Directa

```bash
php artisan tinker
> (new Database\Seeders\ComprehensiveSchoolSeeder())->setContainer(app())->setCommand($this)->run();
```

## Estructura Creada

### Ciclo Escolar 2025-2026

| Propiedad | Valor |
|-----------|-------|
| Nombre | 2025-2026 |
| Fecha Inicio | 2025-08-01 |
| Fecha Fin | 2026-07-31 |
| Estado | No activo (datos futuros) |

### Grupos Escolares

```
Grado 1º → Sección: Única → 14 alumnos
Grado 2º → Sección: Única → 14 alumnos
Grado 3º → Sección: Única → 14 alumnos
Grado 4º → Sección: Única → 14 alumnos
Grado 5º → Sección: Única → 14 alumnos
Grado 6º → Sección: Única → 14 alumnos
─────────────────────────────────────
TOTAL: 84 alumnos
```

### Distribución de Género por Grupo

Cada grupo tiene:
- **7 niños** (géneros alternados)
- **7 niñas**
- Nombres variados y realistas en español

### Personal Docente

#### Maestros Titulares (6)
- 1 por cada grado (1º a 6º)
- Responsables de: Español, Matemáticas, Ciencias Naturales, Historia, Formación Cívica, Artes Plásticas

#### Maestros Especializados (3)
1. **Educación Física**
   - Asignado a todos los grados
   - 2 horas por semana por grupo

2. **Inglés**
   - Asignado a todos los grados
   - 2 horas por semana por grupo

3. **Música**
   - Asignado a todos los grados
   - 2 horas por semana por grupo

### Padres/Tutores

- **84 padres** (1 por alumno)
- Nombres coherentes con los alumnos
- Emails únicos con formato: `nombre.padre#@correo.com`
- Algunos con descuentos (20%)
- Algunos con pagos atrasados (30%)

### Personas Autorizadas para Recoger

- **58 registros** (~70% de estudiantes)
- Relaciones incluyen:
  - Abuelo/a (Grandparent)
  - Tío/a (Uncle/Aunt)
  - Familiar (Family)
  - Amigo/a (Friend)
  - Otro (Other)

### Sistema de Cuotas y Pagos

#### Cuotas Mensuales

| Mes | Monto |
|-----|-------|
| Agosto 2025 | $3,000.00 |
| Septiembre 2025 | $3,000.00 |
| ... | $3,000.00 |
| Julio 2026 | $3,000.00 |
| **Total (12 meses)** | **$36,000.00** |

#### Estadísticas de Pagos

- **Transacciones totales**: 1,008 (84 alumnos × 12 meses)
- **Estudiantes con descuento**: ~20%
- **Estudiantes con pagos atrasados**: ~30%
- **Discrepancias simuladas**: Algunos meses pagados, otros pendientes

### Horarios Académicos

```
HORARIO DIARIO
─────────────────────────────────
07:30 - 08:30  Clase 1 (60 min)
08:30 - 09:30  Clase 2 (60 min)
09:30 - 10:30  Clase 3 (60 min)
10:30 - 11:00  ☕ RECESO (30 min)
11:00 - 12:00  Clase 4 (60 min)
12:00 - 13:00  Clase 5 (60 min)
13:00 - 14:00  Clase 6 (60 min)
─────────────────────────────────
Total: 6 bloques de 60 minutos

LUNES A VIERNES
─────────────────────────────────
Rotación de asignaturas por grupo
Cada grupo tiene su propio horario
```

### Asignaturas Asignadas

#### Por Maestro Titular (5 horas/semana cada una)

1. Español
2. Matemáticas
3. Ciencias Naturales
4. Historia y Geografía
5. Formación Cívica
6. Artes Plásticas (2 horas/semana)

#### Por Maestros Especializados (2 horas/semana cada una)

1. Educación Física
2. Inglés
3. Música

## Credenciales de Prueba

Todos los usuarios tienen contraseña: **`password`**

### Maestros Titulares

```
Lucas.García.Navarro@escuela.com
Mateo.Martínez.Gutiérrez@escuela.com
Santiago.González.Chávez@escuela.com
Alejandro.Rodríguez.Olvera@escuela.com
Andrés.Hernández.Fuentes@escuela.com
Carlos.López.Salazar@escuela.com
```

### Maestros Especializados

```
educacion.fisico.martinez@escuela.com
profesor.ingles@escuela.com
profesor.musica@escuela.com
```

### Padres (Muestra)

```
lgarcia.padre1@correo.com
mmartinez.padre2@correo.com
sgonzalez.padre3@correo.com
... (hasta padre84)
```

### Alumnos (Muestra)

```
lucas.1@estudiantes.escuela.com
andrea.2@estudiantes.escuela.com
mateo.3@estudiantes.escuela.com
... (hasta estudiante84)
```

## Características Avanzadas

### CURP Realista

Cada alumno tiene un CURP único de 18 caracteres:
- Basado en apellidos y nombre
- Fecha de nacimiento aproximada por grado
- Género (H/M)
- Estado: EM (Estado de México)
- Acentos normalizados

Ejemplo: `GANALU160101HEMCA67`

### Coherencia Familiar

Los alumnos tienen:
- Apellido paterno = apellido paterno del padre
- Apellido materno = apellido materno del padre (o variante familiar)
- Esto garantiza relaciones realistas

### Transacción de Base de Datos

Todo el seeder se ejecuta dentro de una transacción `DB::transaction()`:
- Si algo falla, se revierte todo
- Garantiza integridad de datos
- Mantiene datos existentes intactos

### Validaciones de Enums

Usa enums Laravel para garantizar datos consistentes:
- `UserRole::Student`, `UserRole::Parent`, `UserRole::Teacher`
- `Gender::Male`, `Gender::Female`
- `StudentStatus::Active`
- `Relationship::Grandparent`, `Relationship::Uncle`, etc.
- `DayOfWeek::Monday` through `DayOfWeek::Friday`

## Verificación Post-Seeding

### Contar Datos Creados

```bash
php artisan tinker

# Ciclo escolar
App\Models\SchoolYear::where('name', '2025-2026')->count()

# Grupos
App\Models\GradeSection::where('school_year_id', 2)->count()

# Alumnos
App\Models\Student::where('school_year_id', 2)->count()

# Padres
App\Models\User::where('role', App\UserRole::Parent)->count()

# Horarios
DB::table('schedules')->count()

# Transacciones de tuición
App\Models\StudentTuition::where('school_year_id', 2)->count()
```

### Verificar Distribución de Género

```bash
php artisan tinker

$students = App\Models\Student::where('school_year_id', 2)
    ->with('user')
    ->get()
    ->groupBy(function($s) { return $s->schoolGrade->grade_level; });

foreach($students as $grade => $list) {
    $males = $list->where('gender', App\Gender::Male)->count();
    $females = $list->count() - $males;
    echo "Grado $grade: $males niños, $females niñas\n";
}
```

## Archivos Modificados

### Creados

- **`database/seeders/ComprehensiveSchoolSeeder.php`** - Seeder principal con toda la lógica
- **`app/Console/Commands/SeedComprehensiveSchool.php`** - Comando Artisan personalizado

### Referenciados (No modificados)

- Modelos: User, Student, SchoolYear, GradeSection, Subject, Schedule, etc.
- Migrations: Existing migrations se ejecutan sin cambios
- Enums: UserRole, Gender, StudentStatus, Relationship, DayOfWeek

## Notas Importantes

⚠️ **No elimina datos existentes**: El seeder agrega datos sin borrar nada
⚠️ **Crea duplicados si se ejecuta varias veces**: Los datos se agregan, no se reemplazan
⚠️ **Usa transacciones**: Garantiza integridad incluso si falla a mitad
✅ **Totalmente personalizable**: Puedes editar nombres, montos, etc.
✅ **Reutilizable**: Puedes ejecutarlo múltiples veces en diferentes ciclos

## Posibles Mejoras Futuras

- [ ] Importar lista de alumnos desde CSV
- [ ] Generar reportes de estructura creada
- [ ] Crear calificaciones iniciales aleatorias
- [ ] Asignar asistencias iniciales
- [ ] Generar anuncios automáticos del ciclo

## Soporte

Si hay errores durante la ejecución:

1. **Error de columna no encontrada**: Verifica que las migraciones estén actualizadas
2. **Error de enum**: Asegúrate que los valores del enum sean válidos
3. **Error de duración**: El seeder puede tomar 1-2 minutos con 84 alumnos

Para problemas específicos, revisa los logs:

```bash
tail -f storage/logs/laravel.log
```

---

**Versión**: 1.0
**Fecha de Creación**: 2025-11-07
**Compatible con**: Laravel 12.x, PHP 8.4.x
