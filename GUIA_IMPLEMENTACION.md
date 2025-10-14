# Guía de Implementación - Sistema de Gestión Escolar

## Estado Actual del Proyecto

### COMPLETADO

#### 1. Estructura de Base de Datos
- 13 migraciones creadas y listas para ejecutar
- Todas las relaciones definidas correctamente
- Índices y llaves foráneas configuradas

#### 2. Modelos Eloquent (12 modelos)
- User con métodos helpers (isAdmin(), isTeacher(), etc.)
- SchoolYear, Student, Subject, Payment, PaymentReceipt
- MedicalJustification, TuitionConfig, Discount
- Grade, Assignment, Announcement, Schedule
- Todas las relaciones implementadas
- Casts configurados para enums y tipos especiales

#### 3. Enums (5 enums)
- UserRole: Admin, FinanceAdmin, Teacher, Parent, Student
- PaymentType: Tuition, Books, Uniform, Enrollment, Other
- PaymentMethod: Cash, Transfer, Card, Check
- ReceiptStatus: Pending, Validated, Rejected
- DayOfWeek: Monday-Friday

#### 4. Form Requests (7 requests)
- StorePaymentReceiptRequest
- StoreMedicalJustificationRequest
- ValidatePaymentReceiptRequest
- StoreTuitionConfigRequest
- StoreGradeRequest
- StoreAssignmentRequest
- StoreAnnouncementRequest
- Todos con validación en español

#### 5. Middleware y Autorización
- EnsureUserHasRole middleware creado
- Alias 'role' registrado en bootstrap/app.php
- Políticas para PaymentReceipt y Student

#### 6. Controladores (9 controladores)
- DashboardController (redirección por rol)
- Parent/PaymentReceiptController
- Parent/MedicalJustificationController
- Finance/PaymentReceiptController
- Finance/TuitionConfigController
- Teacher/GradeController
- Teacher/AssignmentController
- Teacher/AnnouncementController
- Student/DashboardController

#### 7. Rutas
- Todas las rutas definidas en routes/web.php
- Agrupadas por rol con middleware correspondiente
- Prefijos y nombres configurados

#### 8. Sistema de Notificaciones
- PaymentReceiptSubmitted (email + database)
- PaymentReceiptValidated
- NewAssignment
- Configuradas para usar cola (ShouldQueue)

#### 9. Seeder Completo
- DatabaseSeeder con datos de prueba:
  - 1 Admin, 1 Finance Admin
  - 2 Maestros (Matemáticas, Español)
  - 2 Padres de familia
  - 2 Estudiantes vinculados a padres
  - 1 Ciclo escolar activo (2024-2025)
  - Materias, horarios, pagos, calificaciones
  - Tareas, anuncios, justificantes
  - Configuración de colegiaturas

#### 10. Documentación
- SISTEMA_ESCOLAR.md: Documentación completa del sistema
- GUIA_IMPLEMENTACION.md: Esta guía

---

## PENDIENTE DE IMPLEMENTAR

### 1. Vistas con Tailwind CSS (ALTA PRIORIDAD)

Necesitas crear las vistas Blade para cada sección:

**Layout Base:**
```bash
resources/views/layouts/app.blade.php
resources/views/layouts/navigation.blade.php
```

**Vistas de Padres:**
```bash
resources/views/parent/payment-receipts/index.blade.php
resources/views/parent/payment-receipts/create.blade.php
resources/views/parent/payment-receipts/show.blade.php
resources/views/parent/medical-justifications/index.blade.php
resources/views/parent/medical-justifications/create.blade.php
```

**Vistas de Finanzas:**
```bash
resources/views/finance/payment-receipts/index.blade.php
resources/views/finance/payment-receipts/show.blade.php
resources/views/finance/tuition-configs/index.blade.php
resources/views/finance/tuition-configs/create.blade.php
resources/views/finance/tuition-configs/edit.blade.php
```

**Vistas de Maestros:**
```bash
resources/views/teacher/grades/index.blade.php
resources/views/teacher/grades/create.blade.php
resources/views/teacher/assignments/index.blade.php
resources/views/teacher/assignments/create.blade.php
resources/views/teacher/announcements/index.blade.php
resources/views/teacher/announcements/create.blade.php
```

**Vistas de Estudiantes:**
```bash
resources/views/student/dashboard.blade.php
resources/views/student/schedule.blade.php
resources/views/student/grades.blade.php
resources/views/student/assignments.blade.php
```

### 2. Implementación de Controladores (ALTA PRIORIDAD)

Cada controlador necesita sus métodos implementados. Ejemplo para ParentPaymentReceiptController:

```php
public function index()
{
    $receipts = PaymentReceipt::query()
        ->where('parent_id', auth()->id())
        ->with(['payment.student'])
        ->latest()
        ->paginate(15);

    return view('parent.payment-receipts.index', compact('receipts'));
}

public function create()
{
    $pendingPayments = Payment::query()
        ->whereHas('student', fn($q) => $q->whereHas('user', fn($q) => $q->where('parent_id', auth()->id())))
        ->where('is_paid', false)
        ->with('student.user')
        ->get();

    return view('parent.payment-receipts.create', compact('pendingPayments'));
}

public function store(StorePaymentReceiptRequest $request)
{
    $file = $request->file('receipt_file');
    $path = $file->store('receipts', 'public');

    $receipt = PaymentReceipt::create([
        'payment_id' => $request->payment_id,
        'parent_id' => auth()->id(),
        'payment_date' => $request->payment_date,
        'amount_paid' => $request->amount_paid,
        'payment_method' => $request->payment_method,
        'receipt_file_path' => $path,
        'status' => ReceiptStatus::Pending,
    ]);

    // Notificar a finance admin
    $financeAdmins = User::where('role', UserRole::FinanceAdmin)->get();
    Notification::send($financeAdmins, new PaymentReceiptSubmitted($receipt));

    return redirect()->route('parent.payment-receipts.index')
        ->with('success', 'Comprobante enviado exitosamente');
}
```

### 3. Sistema de Autenticación (ALTA PRIORIDAD)

Laravel no incluye autenticación por defecto. Opciones:

**Opción A: Laravel Breeze (recomendado)**
```bash
composer require laravel/breeze --dev
php artisan breeze:install blade
npm install && npm run build
php artisan migrate
```

**Opción B: Laravel UI**
```bash
composer require laravel/ui
php artisan ui bootstrap --auth
npm install && npm run build
php artisan migrate
```

### 4. Configuración de Storage (NECESARIO)

```bash
# Crear enlace simbólico para acceder archivos públicos
php artisan storage:link

# Configurar en .env
FILESYSTEM_DISK=public
```

### 5. Configuración de Email (NECESARIO PARA NOTIFICACIONES)

En `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io  # o tu servidor SMTP
MAIL_PORT=2525
MAIL_USERNAME=tu_username
MAIL_PASSWORD=tu_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@escuela.com
MAIL_FROM_NAME="${APP_NAME}"
```

Para desarrollo, usa Mailtrap o Laravel Log:
```env
MAIL_MAILER=log
```

### 6. Configuración de Colas (RECOMENDADO)

En `.env`:
```env
QUEUE_CONNECTION=database
```

Luego:
```bash
php artisan queue:table
php artisan migrate
php artisan queue:work
```

---

## COMANDOS PARA INICIAR

### 1. Ejecutar Migraciones

```bash
# Ejecutar migraciones
php artisan migrate

# O limpiar todo y empezar de cero
php artisan migrate:fresh
```

### 2. Poblar Base de Datos

```bash
php artisan db:seed
```

Esto creará los siguientes usuarios de prueba:
- Admin: admin@escuela.com / password
- Finanzas: finanzas@escuela.com / password
- Maestro 1: maria.garcia@escuela.com / password
- Maestro 2: carlos.lopez@escuela.com / password
- Padre 1: roberto.martinez@correo.com / password
- Padre 2: ana.rodriguez@correo.com / password
- Estudiante 1: pedro.martinez@escuela.com / password
- Estudiante 2: laura.rodriguez@escuela.com / password

### 3. Configurar Storage

```bash
php artisan storage:link
```

### 4. Instalar Dependencias Frontend

```bash
npm install
npm run dev
```

### 5. Servir Aplicación

```bash
php artisan serve
```

---

## ESTRUCTURA DE ARCHIVOS

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── DashboardController.php
│   │   ├── Finance/
│   │   ├── Parent/
│   │   ├── Student/
│   │   └── Teacher/
│   ├── Middleware/
│   │   └── EnsureUserHasRole.php
│   └── Requests/
│       ├── StorePaymentReceiptRequest.php
│       ├── StoreMedicalJustificationRequest.php
│       ├── ValidatePaymentReceiptRequest.php
│       ├── StoreTuitionConfigRequest.php
│       ├── StoreGradeRequest.php
│       ├── StoreAssignmentRequest.php
│       └── StoreAnnouncementRequest.php
├── Models/
│   ├── User.php
│   ├── SchoolYear.php
│   ├── Student.php
│   ├── Subject.php
│   ├── Payment.php
│   ├── PaymentReceipt.php
│   ├── MedicalJustification.php
│   ├── TuitionConfig.php
│   ├── Discount.php
│   ├── Grade.php
│   ├── Assignment.php
│   ├── Announcement.php
│   └── Schedule.php
├── Notifications/
│   ├── PaymentReceiptSubmitted.php
│   ├── PaymentReceiptValidated.php
│   └── NewAssignment.php
├── Policies/
│   ├── PaymentReceiptPolicy.php
│   └── StudentPolicy.php
├── UserRole.php
├── PaymentType.php
├── PaymentMethod.php
├── ReceiptStatus.php
└── DayOfWeek.php

database/
├── migrations/
│   ├── 2025_10_14_200156_add_role_to_users_table.php
│   ├── 2025_10_14_200157_create_school_years_table.php
│   ├── 2025_10_14_200157_create_students_table.php
│   ├── 2025_10_14_200157_create_subjects_table.php
│   ├── 2025_10_14_200158_create_payments_table.php
│   ├── 2025_10_14_200158_create_payment_receipts_table.php
│   ├── 2025_10_14_200158_create_medical_justifications_table.php
│   ├── 2025_10_14_200158_create_tuition_configs_table.php
│   ├── 2025_10_14_200159_create_discounts_table.php
│   ├── 2025_10_14_200159_create_grades_table.php
│   ├── 2025_10_14_200159_create_assignments_table.php
│   ├── 2025_10_14_200159_create_announcements_table.php
│   └── 2025_10_14_200159_create_schedules_table.php
└── seeders/
    └── DatabaseSeeder.php

routes/
└── web.php (configurado)
```

---

## PRÓXIMOS PASOS RECOMENDADOS

### Fase 1: Autenticación y Vistas Básicas (1-2 días)
1. Instalar Laravel Breeze
2. Crear layout base con navegación
3. Implementar vista de dashboard para cada rol
4. Probar login con usuarios del seeder

### Fase 2: Módulo de Padres (2-3 días)
1. Implementar controladores Parent
2. Crear vistas para comprobantes
3. Implementar subida de archivos
4. Crear vistas para justificantes médicos

### Fase 3: Módulo de Finanzas (2-3 días)
1. Implementar controladores Finance
2. Crear vistas para validación de comprobantes
3. Implementar configuración de colegiaturas
4. Crear reportes básicos

### Fase 4: Módulo de Maestros (2-3 días)
1. Implementar controladores Teacher
2. Crear vistas para calificaciones
3. Implementar gestión de tareas
4. Crear sistema de anuncios

### Fase 5: Módulo de Estudiantes (1-2 días)
1. Implementar StudentDashboardController
2. Crear vistas de consulta (calificaciones, tareas)
3. Implementar visualización de horarios

### Fase 6: Notificaciones y Reportes (2-3 días)
1. Configurar email y colas
2. Completar notificaciones
3. Generar PDFs de reportes
4. Dashboard con estadísticas

### Fase 7: Tests y Refinamiento (2-3 días)
1. Crear tests con Pest
2. Refinar UX/UI
3. Optimizar queries
4. Documentar API si es necesario

---

## TIPS IMPORTANTES

### Seguridad
- Siempre validar archivos subidos
- Usar políticas de autorización
- Sanitizar inputs
- Configurar rate limiting en producción

### Performance
- Usar eager loading para evitar N+1
- Implementar caché donde sea apropiado
- Optimizar queries con índices
- Usar colas para tareas pesadas

### Testing
```bash
# Crear test
php artisan make:test PaymentReceiptTest --pest

# Ejecutar tests
php artisan test

# Con coverage
php artisan test --coverage
```

### Debugging
```bash
# Limpiar caché
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Ver queries
DB::enableQueryLog();
// código
dd(DB::getQueryLog());
```

---

## RECURSOS ÚTILES

- Documentación Laravel 12: https://laravel.com/docs/12.x
- Documentación Pest 4: https://pestphp.com/docs
- Tailwind CSS: https://tailwindcss.com/docs
- Laravel Breeze: https://laravel.com/docs/12.x/starter-kits#breeze

---

## SOPORTE

Para dudas o problemas:
1. Revisar documentación en SISTEMA_ESCOLAR.md
2. Consultar logs en storage/logs/laravel.log
3. Verificar configuración en .env
4. Ejecutar `php artisan about` para ver estado del sistema

---

Última actualización: Octubre 2024
Sistema desarrollado con Laravel 12 + Pest 4 + Tailwind CSS 4
