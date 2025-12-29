# Sistema de Gestión de Reportes - Empresa Eléctrica

Sistema web desarrollado en Laravel para la gestión de reportes de fallas eléctricas.

## 🚀 Características

- **Roles y permisos:** Cliente, Técnico, Administrador, Super Admin
- **Gestión de reportes:** Creación, asignación, seguimiento y resolución
- **Límite inteligente:** Máximo 3 reportes activos por cliente
- **Subida de evidencias:** Fotos antes/durante/después de la reparación
- **Dashboard personalizado:** Diferente para cada rol
- **API REST:** Para integración con aplicaciones móviles

## 🛠️ Tecnologías

- **Backend:** Laravel 12.44, PHP 8.1
- **Frontend:** Bootstrap 5, Blade
- **Base de datos:** PostgreSQL
- **Autenticación:** Laravel UI + Spatie Permissions
- **Storage:** Sistema de archivos local (para imágenes)

## 📋 Roles del Sistema

### 👤 Cliente
- Crear reportes de fallas (máximo 3 activos)
- Subir fotos de evidencias
- Ver el estado de sus reportes

### 👷 Técnico
- Ver reportes asignados
- Cambiar estado (en_proceso → resuelto)
- Subir fotos del trabajo realizado

### 👨‍💼 Administrador
- Asignar reportes a técnicos
- Gestionar usuarios y roles
- Ver estadísticas generales

### 👑 Super Admin
- Control total del sistema
- Gestionar todos los recursos

## 🗄️ Estructura de Base de Datos
