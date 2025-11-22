# Nurgo Health - Sistema de Gestión de Salud

Sistema completo de gestión de salud con Laravel 8 (Backend) y React + Vite (Frontend).

## 🚀 Tecnologías

### Backend
- Laravel 8
- PHP 8.1
- MySQL/MariaDB
- Laravel Sanctum (Autenticación API)
- Composer

### Frontend
- React 18
- Vite
- TailwindCSS 3
- Axios
- React Router DOM
- Framer Motion
- React Hot Toast
- React Icons

## 📋 Requisitos Previos

- PHP 8.1+
- Composer
- Node.js 16+
- MySQL/MariaDB (Laragon recomendado)

## 🔧 Instalación

### Backend Setup

1. Navegar al directorio del backend:
```bash
cd backend
```

2. Instalar dependencias:
```bash
composer install
```

3. Copiar archivo de configuración:
```bash
cp .env.example .env
```

4. Configurar la base de datos en `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nurgo_health
DB_USERNAME=root
DB_PASSWORD=
```

5. Generar clave de aplicación:
```bash
php artisan key:generate
```

6. Ejecutar migraciones:
```bash
php artisan migrate
```

7. Ejecutar seeders (opcional - datos de prueba):
```bash
php artisan db:seed
```

8. Iniciar servidor:
```bash
php artisan serve
```

El backend estará disponible en: **http://localhost:8000**

### Frontend Setup

1. Navegar al directorio del frontend:
```bash
cd frontend
```

2. Instalar dependencias:
```bash
npm install
```

3. Configurar variables de entorno (`.env`):
```env
VITE_API_URL=http://localhost:8000/api
```

4. Iniciar servidor de desarrollo:
```bash
npm run dev
```

El frontend estará disponible en: **http://localhost:5173**

## 👥 Usuarios de Prueba (después de seeders)

### Admin
- Email: admin@nurgo.com
- Password: password

### Doctores
- Email: doctor1@nurgo.com / Password: password
- Email: doctor2@nurgo.com / Password: password

### Pacientes
- Email: patient1@nurgo.com / Password: password
- Email: patient2@nurgo.com / Password: password

### Tutores
- Email: guardian1@nurgo.com / Password: password
- Email: guardian2@nurgo.com / Password: password

## 🏗️ Estructura del Proyecto

### Backend
```
backend/
├── app/
│   ├── Http/
│   │   ├── Controllers/API/
│   │   │   ├── AuthController.php
│   │   │   ├── DoctorController.php
│   │   │   ├── PatientController.php
│   │   │   ├── AppointmentController.php
│   │   │   └── ...
│   │   └── Middleware/
│   │       └── CheckRole.php
│   └── Models/
│       ├── User.php
│       ├── Doctor.php
│       ├── Patient.php
│       ├── Appointment.php
│       └── ...
├── database/
│   ├── migrations/
│   └── seeders/
└── routes/
    └── api.php
```

### Frontend
```
frontend/
├── src/
│   ├── components/
│   │   ├── Layout.jsx
│   │   ├── Navbar.jsx
│   │   └── ProtectedRoute.jsx
│   ├── context/
│   │   └── AuthContext.jsx
│   ├── pages/
│   │   ├── Landing.jsx
│   │   ├── Login.jsx
│   │   ├── Register.jsx
│   │   ├── Dashboard.jsx
│   │   ├── Admin/
│   │   │   └── AdminDashboard.jsx
│   │   ├── Doctor/
│   │   │   └── DoctorDashboard.jsx
│   │   ├── Patient/
│   │   │   └── PatientDashboard.jsx
│   │   └── Guardian/
│   │       └── GuardianDashboard.jsx
│   ├── services/
│   │   ├── api.js
│   │   ├── authService.js
│   │   ├── appointmentService.js
│   │   └── dataService.js
│   └── App.jsx
```

## 🔑 Características Principales

### Autenticación y Autorización
- Login/Register con validación
- Protección de rutas por roles
- Token-based authentication (Laravel Sanctum)
- Middleware de verificación de roles

### Roles y Permisos
1. **Admin**: Gestión completa del sistema
2. **Doctor**: Gestión de citas, pacientes y prescripciones
3. **Paciente**: Agendar citas, ver historial médico
4. **Tutor**: Gestionar pacientes a cargo, botón de emergencia

### Funcionalidades
- ✅ Gestión de citas médicas
- ✅ Validación de citas (no pasadas, no conflictos)
- ✅ Sistema de emergencias
- ✅ Gestión de pacientes y doctores
- ✅ Prescripciones médicas
- ✅ Visitas a domicilio
- ✅ Historial médico

### Validaciones Implementadas
- No se pueden agendar citas en fechas pasadas
- No se permiten citas simultáneas del mismo doctor
- Tutores deben ser mayores de 18 años
- Validación de datos de registro

## 📱 Dashboards Específicos

### Admin Dashboard
- Estadísticas generales del sistema
- Gestión de usuarios (doctores, pacientes, tutores)
- Monitoreo de emergencias
- Vista general de citas

### Doctor Dashboard
- Citas del día
- Alertas de emergencias activas
- Gestión de pacientes
- Prescripciones médicas

### Patient Dashboard
- Próximas citas
- Agendar nueva cita
- Historial médico
- Recetas activas

### Guardian Dashboard
- **Botón de emergencia prominente**
- Pacientes a cargo
- Gestión de citas
- Historial de emergencias

## 🎨 Diseño

- **Framework CSS**: TailwindCSS 3
- **Animaciones**: Framer Motion
- **Notificaciones**: React Hot Toast
- **Iconos**: React Icons (Font Awesome)
- **Tema**: Colores primarios azules, diseño moderno y limpio

## 🔄 API Endpoints

### Autenticación
- POST `/api/register` - Registro de usuarios
- POST `/api/login` - Inicio de sesión
- POST `/api/logout` - Cerrar sesión
- GET `/api/me` - Usuario actual

### Citas
- GET `/api/appointments` - Listar citas
- POST `/api/appointments` - Crear cita
- GET `/api/appointments/{id}` - Ver cita
- PUT `/api/appointments/{id}` - Actualizar cita
- DELETE `/api/appointments/{id}` - Eliminar cita
- GET `/api/appointments-upcoming` - Citas próximas

### Doctores
- GET `/api/doctors` - Listar doctores
- GET `/api/doctors-available` - Doctores disponibles
- CRUD completo para doctores

### Emergencias
- GET `/api/emergencies` - Listar emergencias
- POST `/api/emergencies` - Reportar emergencia
- GET `/api/emergencies-active` - Emergencias activas
- PUT `/api/emergencies/{id}` - Actualizar emergencia

## 🔒 Seguridad

- CORS configurado para desarrollo
- Autenticación con tokens Bearer
- Middleware de verificación de roles
- Validación de datos en backend
- Sanitización de inputs

## 🐛 Troubleshooting

### Error de políticas de ejecución en PowerShell
Si encuentras errores con npm/npx, usa:
```powershell
npm.cmd install
npm.cmd run dev
```

### Error de CORS
Verifica que el backend esté configurado en `config/cors.php`:
```php
'allowed_origins' => ['http://localhost:5173'],
'supports_credentials' => true,
```

### Error de conexión a base de datos
Asegúrate de que MySQL/MariaDB esté corriendo y las credenciales en `.env` sean correctas.

## 📝 Próximas Funcionalidades

- [ ] Sistema de notificaciones por email
- [ ] Generación de PDFs para citas y prescripciones
- [ ] Calendario visual interactivo
- [ ] Chat en tiempo real doctor-paciente
- [ ] Historial médico detallado con archivos adjuntos
- [ ] Sistema de recordatorios automáticos

## 👨‍💻 Desarrollo

### Backend
```bash
# Ejecutar migraciones frescas
php artisan migrate:fresh

# Con seeders
php artisan migrate:fresh --seed

# Crear controlador
php artisan make:controller NombreController --api

# Crear modelo con migración
php artisan make:model Nombre -m
```

### Frontend
```bash
# Build para producción
npm run build

# Preview de producción
npm run preview

# Linting
npm run lint
```

## 📄 Licencia

Este proyecto es parte del curso de desarrollo web y está destinado para fines educativos.

## 🤝 Contribuciones

Desarrollado por el equipo de Nurgo Health.

---

**Estado**: ✅ Backend completamente funcional | ✅ Frontend con autenticación y dashboards implementados

**Última actualización**: 21 de Noviembre de 2025
