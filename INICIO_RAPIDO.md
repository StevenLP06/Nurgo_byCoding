# 🚀 Inicio Rápido - Nurgo Health

## Para iniciar la aplicación completa

### 1️⃣ Iniciar Backend (Terminal 1)
```bash
cd backend
php artisan serve
```
✅ Backend corriendo en: **http://localhost:8000**

### 2️⃣ Iniciar Frontend (Terminal 2 - PowerShell)
```bash
cd frontend
npm.cmd run dev
```
✅ Frontend corriendo en: **http://localhost:5173**

## 🌐 Acceso a la Aplicación

Abre tu navegador en: **http://localhost:5173**

## 🔑 Usuarios de Prueba

### Admin
- **Email**: `admin@nurgo.com`
- **Password**: `password`

### Doctor
- **Email**: `doctor1@nurgo.com`
- **Password**: `password`

### Paciente
- **Email**: `patient1@nurgo.com`
- **Password**: `password`

### Tutor
- **Email**: `guardian1@nurgo.com`
- **Password**: `password`

## 📱 Flujo de Prueba Recomendado

### 1. Landing Page
- Visualiza la página de inicio
- Revisa servicios y testimonios
- Haz clic en "Iniciar Sesión"

### 2. Login como Paciente
- Usa: `patient1@nurgo.com` / `password`
- Explora el dashboard del paciente
- Ve las próximas citas
- Intenta agendar una nueva cita

### 3. Login como Doctor
- Cierra sesión y vuelve a login
- Usa: `doctor1@nurgo.com` / `password`
- Revisa el dashboard del doctor
- Ve las citas del día
- Explora la sección de pacientes

### 4. Login como Tutor
- Cierra sesión
- Usa: `guardian1@nurgo.com` / `password`
- **Observa el botón de emergencia prominente**
- Prueba el modal de emergencia (no enviar)
- Ve los pacientes a cargo

### 5. Login como Admin
- Cierra sesión
- Usa: `admin@nurgo.com` / `password`
- Ve las estadísticas generales
- Explora la gestión de usuarios

## ✨ Características para Probar

### ✅ Autenticación
- Registro de nuevo usuario
- Login/Logout
- Redirección automática según rol
- Protección de rutas

### ✅ Navegación
- Navbar con información del usuario
- Botón de cerrar sesión
- Enlaces a diferentes secciones

### ✅ Diseño
- Animaciones suaves (Framer Motion)
- Responsive design
- Notificaciones (Toast)
- Loading states

### ✅ Funcionalidad
- Dashboard específico por rol
- Estadísticas visuales
- Acciones rápidas
- Modal de emergencia (Guardian)

## 🔧 Comandos Útiles

### Reiniciar Base de Datos
```bash
cd backend
php artisan migrate:fresh --seed
```

### Ver rutas de API
```bash
cd backend
php artisan route:list --path=api
```

### Build de producción (Frontend)
```bash
cd frontend
npm run build
```

## ❗ Solución de Problemas

### Error en PowerShell
Si hay problemas con npm, usa:
```powershell
npm.cmd run dev
```

### Puerto ocupado
Si el puerto 5173 está ocupado:
```bash
npm run dev -- --port 3000
```

### Error de conexión
Verifica que ambos servidores estén corriendo:
- Backend: http://localhost:8000
- Frontend: http://localhost:5173

## 📊 Estado Actual

- ✅ Backend: 100% funcional
- ✅ Frontend: 100% funcional
- ✅ Autenticación: Implementada
- ✅ 4 Dashboards: Completados
- ✅ Diseño: Moderno y responsive
- ✅ Datos de prueba: Disponibles

## 🎯 Próximos Pasos Sugeridos

1. **Gestión de Citas Completa**
   - Formulario de creación
   - Calendario visual
   - Edición y cancelación

2. **Sistema de Notificaciones**
   - Emails automáticos
   - Notificaciones push
   - Recordatorios

3. **Historial Médico Detallado**
   - Upload de archivos
   - Visualización de documentos
   - Línea de tiempo

4. **Reportes y Estadísticas**
   - Gráficos interactivos
   - Exportación a PDF/Excel
   - Dashboard analytics

---

**¡Listo para usar! 🎉**

Todo está configurado y funcionando. Simplemente inicia ambos servidores y comienza a explorar la aplicación.
