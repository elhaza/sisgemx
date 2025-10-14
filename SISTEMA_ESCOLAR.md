# Sistema de Gestión Escolar

## Descripción General

Sistema completo de gestión escolar con roles diferenciados para padres de familia, estudiantes, maestros, administrativos y administradores de finanzas.

## Roles del Sistema

### 1. Admin (Administrador General)
- Acceso total al sistema
- Gestión de usuarios y roles
- Configuración de ciclos escolares
- Gestión de información institucional

### 2. Finance Admin (Administrador de Finanzas)
- Visualización de comprobantes de pago
- Validación/rechazo de pagos
- Asignación de descuentos por ciclo escolar
- Configuración de montos de colegiatura mensuales
- Registro de otros tipos de pagos (libros, uniformes, inscripciones, etc.)

### 3. Teacher (Maestro)
- Captura de calificaciones por materia
- Asignación de tareas con fecha de vencimiento
- Envío de anuncios a estudiantes y/o padres
- Gestión de sus materias

### 4. Parent (Padre de Familia)
- Subida de comprobantes de pago de mensualidad
- Visualización del estado de pagos
- Subida de justificantes médicos
- Consulta de calificaciones y tareas de sus hijos

### 5. Student (Estudiante)
- Acceso a horario de clases
- Visualización de anuncios
- Consulta de calificaciones
- Consulta de tareas pendientes y vencidas

## Estructura de la Base de Datos

### Tablas Principales

#### users
- Gestión de todos los usuarios del sistema
- Campos: name, email, password, role, parent_id
- El campo `role` usa el enum UserRole
- El campo `parent_id` relaciona estudiantes con sus padres

#### school_years
- Ciclos escolares
- Campos: name, start_date, end_date, is_active

#### students
- Información de estudiantes
- Relacionados con: user, school_year
- Campos: enrollment_number, grade_level, group

#### subjects
- Materias/asignaturas
- Relacionados con: teacher (User), school_year
- Campos: name, description, grade_level

#### payments
- Pagos generados para estudiantes
- Relacionados con: student
- Tipos: tuition (colegiatura), books, uniform, enrollment, other
- Campos: payment_type, amount, month, year, due_date, is_paid, paid_at

#### payment_receipts
- Comprobantes de pago subidos por padres
- Relacionados con: payment, parent (User), validated_by (User)
- Estados: pending, validated, rejected
- Campos: payment_date, amount_paid, payment_method, receipt_file_path, status

#### medical_justifications
- Justificantes médicos
- Relacionados con: student, parent (User)
- Campos: absence_date, reason, document_file_path

#### tuition_configs
- Configuración de montos de colegiatura
- Permite definir montos diferentes por mes
- Relacionados con: school_year
- Campos: grade_level, month, amount

#### discounts
- Descuentos especiales por ciclo escolar
- Relacionados con: student, school_year, created_by (User)
- Campos: discount_percentage, reason

#### grades
- Calificaciones de estudiantes
- Relacionados con: student, subject, teacher (User)
- Campos: period, grade, comments

#### assignments
- Tareas asignadas
- Relacionados con: subject, teacher (User)
- Campos: title, description, due_date, max_points

#### announcements
- Anuncios de maestros
- Relacionados con: teacher (User)
- Campos: title, content, target_audience (JSON: students, parents, both)

#### schedules
- Horarios de clases
- Relacionados con: subject
- Campos: grade_level, group, day_of_week, start_time, end_time, classroom

## Enums Definidos

### UserRole (app/UserRole.php)
- Admin
- FinanceAdmin
- Teacher
- Parent
- Student

### PaymentType (app/PaymentType.php)
- Tuition (colegiatura)
- Books (libros)
- Uniform (uniformes)
- Enrollment (inscripción)
- Other (otros)

### PaymentMethod (app/PaymentMethod.php)
- Cash (efectivo)
- Transfer (transferencia)
- Card (tarjeta)
- Check (cheque)

### ReceiptStatus (app/ReceiptStatus.php)
- Pending (pendiente)
- Validated (validado)
- Rejected (rechazado)

### DayOfWeek (app/DayOfWeek.php)
- Monday - Friday

## Modelos Eloquent

Todos los modelos están en `app/Models/` y tienen:
- Relaciones definidas con tipos de retorno
- Casts apropiados para campos especiales (enums, decimales, fechas)
- Arrays fillable configurados

### Modelo User (app/Models/User.php)
Métodos helpers:
- `isAdmin()`: Verifica si es administrador
- `isFinanceAdmin()`: Verifica si es admin de finanzas
- `isTeacher()`: Verifica si es maestro
- `isParent()`: Verifica si es padre de familia
- `isStudent()`: Verifica si es estudiante

## Middleware

### EnsureUserHasRole
- Ubicación: `app/Http/Middleware/EnsureUserHasRole.php`
- Alias: `role`
- Uso en rutas: `->middleware('role:admin,finance_admin')`
- Valida que el usuario tenga uno de los roles especificados

## Controladores Creados

### Parent (Padres)
- `Parent/PaymentReceiptController`: Gestión de comprobantes de pago
- `Parent/MedicalJustificationController`: Gestión de justificantes médicos

### Finance (Finanzas)
- `Finance/PaymentReceiptController`: Validación de comprobantes
- `Finance/TuitionConfigController`: Configuración de colegiaturas

### Teacher (Maestros)
- `Teacher/GradeController`: Captura de calificaciones
- `Teacher/AssignmentController`: Gestión de tareas
- `Teacher/AnnouncementController`: Creación de anuncios

### Student (Estudiantes)
- `Student/DashboardController`: Dashboard del estudiante

### General
- `DashboardController`: Dashboard principal con redirección por rol

## Características de Seguridad

1. **Autenticación**: Sistema de autenticación de Laravel
2. **Autorización**: Middleware de roles para controlar acceso
3. **Validación de archivos**: Los comprobantes y justificantes se almacenan de forma segura
4. **Protección CSRF**: Incluida por defecto en Laravel
5. **Hashing de contraseñas**: Automático con bcrypt

## Flujos de Trabajo Principales

### Flujo de Pagos
1. Admin de finanzas configura montos de colegiatura en `tuition_configs`
2. Sistema genera `payments` automáticos para cada estudiante
3. Padre sube comprobante en `payment_receipts`
4. Admin de finanzas valida o rechaza el comprobante
5. Si se valida, el pago se marca como `is_paid = true`

### Flujo de Calificaciones
1. Maestro captura calificaciones en `grades`
2. Calificaciones quedan asociadas a: student, subject, period
3. Estudiantes y padres pueden consultar las calificaciones
4. Se pueden agregar comentarios por calificación

### Flujo de Tareas
1. Maestro crea tarea en `assignments` con fecha de vencimiento
2. Tarea queda asociada a una materia específica
3. Estudiantes ven sus tareas pendientes agrupadas por materia
4. Sistema identifica tareas vencidas automáticamente

### Flujo de Anuncios
1. Maestro crea anuncio especificando audiencia (students, parents, both)
2. Sistema muestra anuncios según el rol del usuario
3. Se puede implementar sistema de notificaciones (email/push)

## Sistema de Notificaciones

El sistema está preparado para implementar notificaciones mediante:
- Laravel Notifications (email, database, broadcast)
- Notificaciones push
- Alertas en el dashboard

### Eventos que pueden generar notificaciones:
- Nuevo comprobante de pago subido
- Pago validado/rechazado
- Nueva tarea asignada
- Tarea próxima a vencer
- Nuevo anuncio publicado
- Nueva calificación capturada

## Configuración de Almacenamiento

Los archivos se almacenan usando el sistema de storage de Laravel:

```php
// Configuración en config/filesystems.php
'receipts' => [
    'driver' => 'local',
    'root' => storage_path('app/receipts'),
],
'medical_documents' => [
    'driver' => 'local',
    'root' => storage_path('app/medical'),
],
```

## Próximos Pasos de Implementación

### Alta Prioridad
1. **Vistas con Tailwind CSS**: Crear interfaces para cada rol
2. **Form Requests**: Validación de entrada para cada controlador
3. **Políticas**: Implementar políticas de autorización detalladas
4. **Seeders**: Crear datos de prueba completos

### Media Prioridad
5. **Tests con Pest**: Pruebas unitarias y de funcionalidad
6. **API REST**: Para posible aplicación móvil
7. **Sistema de notificaciones**: Implementar envío de emails
8. **Reportes**: Generación de PDFs (pagos, calificaciones, etc.)

### Baja Prioridad
9. **Dashboard con gráficas**: Estadísticas visuales
10. **Exportación de datos**: Excel, CSV
11. **Sistema de chat**: Comunicación entre roles
12. **Calendario de eventos**: Integración de horarios y eventos

## Comandos Artisan Útiles

```bash
# Ejecutar migraciones
php artisan migrate

# Crear datos de prueba
php artisan db:seed

# Limpiar caché
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Crear enlace simbólico para storage
php artisan storage:link
```

## Estructura de Rutas Recomendada

```php
// routes/web.php

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Rutas para Padres
    Route::middleware(['role:parent'])->prefix('parent')->name('parent.')->group(function () {
        Route::resource('payment-receipts', Parent\PaymentReceiptController::class);
        Route::resource('medical-justifications', Parent\MedicalJustificationController::class);
    });

    // Rutas para Administrador de Finanzas
    Route::middleware(['role:finance_admin'])->prefix('finance')->name('finance.')->group(function () {
        Route::get('payment-receipts', [Finance\PaymentReceiptController::class, 'index']);
        Route::post('payment-receipts/{receipt}/validate', [Finance\PaymentReceiptController::class, 'validate']);
        Route::post('payment-receipts/{receipt}/reject', [Finance\PaymentReceiptController::class, 'reject']);
        Route::resource('tuition-configs', Finance\TuitionConfigController::class);
    });

    // Rutas para Maestros
    Route::middleware(['role:teacher'])->prefix('teacher')->name('teacher.')->group(function () {
        Route::resource('grades', Teacher\GradeController::class);
        Route::resource('assignments', Teacher\AssignmentController::class);
        Route::resource('announcements', Teacher\AnnouncementController::class);
    });

    // Rutas para Estudiantes
    Route::middleware(['role:student'])->prefix('student')->name('student.')->group(function () {
        Route::get('dashboard', [Student\DashboardController::class, 'index']);
        Route::get('schedule', [Student\DashboardController::class, 'schedule']);
        Route::get('grades', [Student\DashboardController::class, 'grades']);
        Route::get('assignments', [Student\DashboardController::class, 'assignments']);
    });
});
```

## Notas Técnicas

- **Laravel Version**: 12
- **PHP Version**: 8.4.11
- **Database**: Compatible con MySQL, PostgreSQL, SQLite
- **CSS Framework**: Tailwind CSS v4
- **Testing**: Pest v4
- **Code Style**: Laravel Pint

## Soporte y Mantenimiento

Este sistema debe ser mantenido siguiendo las mejores prácticas de Laravel:
- Actualizar dependencias regularmente
- Ejecutar tests antes de cada deploy
- Mantener backups de la base de datos
- Monitorear logs de errores
- Implementar rate limiting en producción

## Licencia

Sistema propietario para uso institucional.
