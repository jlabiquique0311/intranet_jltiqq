# Intranet Juzgado de Letras del Trabajo de Iquique

## Descripción
Plataforma intranet moderna para el Juzgado de Letras del Trabajo de Iquique con gestión de usuarios, noticias, cumpleaños, contactos, enlaces relevantes y más.

## Requisitos
- PHP 7.3.10 o superior
- MySQL/MariaDB
- Bootstrap 5
- jQuery
- Navegador moderno (Chrome, Firefox, Safari, Edge)

## Estructura del Proyecto
```
intranet_jltiqq/
├── config/              # Configuración de la aplicación
├── assets/              # CSS, JS, imágenes
├── uploads/             # Archivos subidos
├── public/              # Archivos públicos
├── includes/            # Archivos de inclusión reutilizables
├── modules/             # Módulos de la aplicación
├── admin/               # Panel de administración
├── api/                 # Endpoints API
├── database/            # Scripts de BD y migraciones
└── index.php            # Punto de entrada
```

## Instalación

1. Clonar el repositorio
2. Copiar `config/db_config_example.php` a `config/db_config.php`
3. Configurar datos de conexión a BD
4. Ejecutar script de instalación: `database/install.php`
5. Acceder a `http://localhost/intranet_jltiqq`

## Usuarios por Defecto
- Admin: `admin` / `admin123`
- Operador: `operador` / `operador123`

## Funcionalidades
- Dashboard personalizable
- Gestión de cumpleaños
- Blog de noticias
- Libreta de contactos
- Gestión de enlaces relevantes
- Visor de decretos económicos
- Biblioteca de documentos
- Manual de procedimientos
- Agenda de audiencias
- Panel de administración completo
- Gestión de bases de datos

## Notas de Seguridad
Esta es una versión de desarrollo. Para producción:
- Implementar encriptación de contraseñas (bcrypt)
- Validación y sanitización robusta
- HTTPS obligatorio
- Rate limiting
- Auditoría de acciones
