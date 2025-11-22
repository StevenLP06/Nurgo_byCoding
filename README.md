# Aplicativo Web de Salud

## 📌 Descripción del Proyecto

Este proyecto consiste en el desarrollo de un **sitio web de salud** con operaciones completas **CRUD**, manejo avanzado de **roles**, protección de rutas mediante **middleware**, y un sistema de autenticación que incluye **inicio de sesión y registro**. La interfaz estará construida con un diseño moderno, interactivo, con animaciones suaves y colores acordes a la identidad del aplicativo.

### 👥 Roles del Sistema

El sistema contará con los siguientes roles iniciales:

* **Administrador**
* **Acudiente**
* **Paciente**
* **Doctor**

### 🧩 Reglas y Relaciones

* Cada **Paciente** debe tener un **Acudiente**, quien debe ser:

  * Obligatoriamente mayor de edad.
  * Tener un parentesco válido con el paciente.
* Cada **Paciente** debe tener asignado un **Doctor**.
* Cada **Doctor** puede tener múltiples pacientes asociados.

### 🩺 Funcionalidades Principales

#### Para Doctores

* Asignar **citas médicas**.
* Asignar **visitas domiciliarias** a los pacientes.
* **Recetar medicamentos**.
* Notificación automática al paciente y acudiente (y opcionalmente vía correo con PDF) al asignar una cita o visita.
* Validación de citas:

  * No se pueden programar citas en fechas pasadas.
  * No se permiten dos o más citas simultáneas con el mismo doctor.

#### Para Acudientes y Pacientes

* Agendar citas médicas.
* Los acudientes tienen un botón especial de **“Emergencia”**, que enviará notificación por correo al doctor indicando que el paciente requiere atención inmediata.

#### Para Todos los Roles Relacionados

* Visualización clara, ordenada y atractiva de todas las citas asignadas.

### 🏠 Página de Inicio

El sitio contará con una página de bienvenida que muestra:

* Información de la empresa.
* Razón social.
* Servicios ofrecidos.
* Opiniones de clientes.
* Otros elementos informativos.

---

## 🏛️ Arquitectura de la Aplicación

La aplicación utilizará una arquitectura basada en **microservicios**, dividiendo responsabilidades entre backend y frontend.

### 🔧 Backend

* Framework: **Laravel 8**
* Lenguaje: **PHP 8**
* Herramientas:

  * Composer (gestión de dependencias, creación de controladores, modelos, etc.)
  * Eloquent ORM para operaciones CRUD y consultas.

### 🎨 Frontend

* Framework: **React** con **Vite**
* Lenguajes: **HTML**, **CSS** (con TailwindCSS), **JavaScript**
* Estilos personalizados con animaciones suaves mediante librerías JS.

### 🗄️ Base de Datos

* Servidor local: **Laragon**
* DBMS: **HeidiSQL** (MySQL/MariaDB)
* Gestión de:

  * Usuarios
  * Administradores
  * Doctores
  * Acudientes
  * Pacientes
  * Citas
  * Medicamentos
  * Entre otros

### 🧱 Patrón de Diseño

* Se utilizará el patrón **MVC (Modelo - Vista - Controlador)** para mantener el proyecto ordenado y escalable.

---

## 💻 Tecnologías Utilizadas

### Backend

* PHP 8
* Laravel 8
* Composer
* Eloquent ORM

### Frontend

* React Vite
* HTML5
* TailwindCSS
* JavaScript

### Base de Datos

* MySQL/MariaDB (HeidiSQL vía Laragon)

---

## 📁 Estructura de Carpetas

El proyecto usará las estructuras estándar provistas por cada framework:

### Laravel (Backend)

* **app/** – Modelos, controladores y lógica principal.
* **routes/** – Rutas web y API.
* **database/** – Migraciones, seeds y factories.
* **resources/views/** – Plantillas Blade (si aplica).
* **public/** – Archivos accesibles públicamente.

### React (Frontend)

* **src/**

  * **components/** – Componentes reutilizables.
  * **pages/** – Páginas del sitio.
  * **styles/** – Estilos globales.
  * **hooks/** – Hooks personalizados.
  * **services/** – Consumo de APIs.

---

## 🚀 Instalación y Ejecución

### Requisitos

* PHP >= 8.0
* Composer
* Node.js y npm
* Laragon (o equivalente)

### Backend

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

### Frontend

```bash
cd frontend
npm install
npm run dev
```

---

## 📬 Notificaciones y Correo

* El sistema enviará correos electrónicos para:

  * Confirmación de citas.
  * Visitas domiciliarias.
  * Notificación de emergencias.
* Se planea generar **PDFs** para citas importantes.

---

## 🧪 Estado del Proyecto

Aplicativo en fase inicial de diseño y estructuración.

---

## 📝 Licencia

Proyecto para fines académicos o de prueba.
