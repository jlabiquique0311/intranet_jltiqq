<?php
/**
 * Script de instalación de la base de datos
 */
define('BASE_PATH', dirname(dirname(__FILE__)));
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'intranet_jlt');
define('DB_PORT', 3306);
define('DB_CHARSET', 'utf8mb4');
define('APP_NAME', 'Intranet JLT Iquique');

$conexion = new mysqli(DB_HOST, DB_USER, DB_PASS);

if ($conexion->connect_error) {
    die('Error de conexión: ' . $conexion->connect_error);
}

$conexion->set_charset(DB_CHARSET);

// Crear base de datos
$sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET " . DB_CHARSET . " COLLATE utf8mb4_unicode_ci";
if (!$conexion->query($sql)) {
    die('Error creando BD: ' . $conexion->error);
}

$conexion->select_db(DB_NAME);

// Tabla de usuarios
$sql = "CREATE TABLE IF NOT EXISTS usuarios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    rol ENUM('administrador', 'operador') DEFAULT 'operador',
    estado TINYINT DEFAULT 1,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    ultimo_acceso DATETIME,
    INDEX idx_usuario (usuario),
    INDEX idx_rol (rol)
) ENGINE=InnoDB";
$conexion->query($sql);

// Tabla de noticias
$sql = "CREATE TABLE IF NOT EXISTS noticias (
    id INT PRIMARY KEY AUTO_INCREMENT,
    titulo VARCHAR(255) NOT NULL,
    contenido LONGTEXT NOT NULL,
    autor_id INT NOT NULL,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME ON UPDATE CURRENT_TIMESTAMP,
    estado TINYINT DEFAULT 1,
    destacada TINYINT DEFAULT 0,
    FOREIGN KEY (autor_id) REFERENCES usuarios(id),
    INDEX idx_fecha (fecha_creacion),
    INDEX idx_estado (estado)
) ENGINE=InnoDB";
$conexion->query($sql);

// Tabla de cumpleaños
$sql = "CREATE TABLE IF NOT EXISTS cumpleanos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT UNIQUE,
    fecha_nacimiento DATE NOT NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB";
$conexion->query($sql);

// Tabla de contactos
$sql = "CREATE TABLE IF NOT EXISTS contactos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    telefono VARCHAR(20),
    departamento VARCHAR(100),
    cargo VARCHAR(100),
    estado TINYINT DEFAULT 1,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_apellido (apellido),
    INDEX idx_nombre (nombre),
    INDEX idx_estado (estado)
) ENGINE=InnoDB";
$conexion->query($sql);

// Tabla de enlaces relevantes
$sql = "CREATE TABLE IF NOT EXISTS enlaces_relevantes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    url VARCHAR(500) NOT NULL,
    descripcion TEXT,
    icono VARCHAR(100),
    categoria ENUM('enlace_interno', 'enlace_general') DEFAULT 'enlace_general',
    destacado TINYINT DEFAULT 0,
    estado TINYINT DEFAULT 1,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_categoria (categoria),
    INDEX idx_estado (estado)
) ENGINE=InnoDB";
$conexion->query($sql);

// Tabla de decretos económicos
$sql = "CREATE TABLE IF NOT EXISTS decretos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    numero_decreto VARCHAR(50) NOT NULL,
    fecha_decreto DATE NOT NULL,
    contenido LONGTEXT NOT NULL,
    archivo_pdf VARCHAR(255),
    estado TINYINT DEFAULT 1,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_numero (numero_decreto),
    INDEX idx_fecha (fecha_decreto)
) ENGINE=InnoDB";
$conexion->query($sql);

// Tabla de biblioteca de documentos
$sql = "CREATE TABLE IF NOT EXISTS biblioteca (
    id INT PRIMARY KEY AUTO_INCREMENT,
    titulo VARCHAR(255) NOT NULL,
    descripcion TEXT,
    archivo VARCHAR(255) NOT NULL,
    tipo_documento VARCHAR(50),
    categoria VARCHAR(100),
    fecha_carga DATETIME DEFAULT CURRENT_TIMESTAMP,
    descargas INT DEFAULT 0,
    estado TINYINT DEFAULT 1,
    INDEX idx_categoria (categoria),
    INDEX idx_estado (estado)
) ENGINE=InnoDB";
$conexion->query($sql);

// Tabla de manuales
$sql = "CREATE TABLE IF NOT EXISTS manuales (
    id INT PRIMARY KEY AUTO_INCREMENT,
    titulo VARCHAR(255) NOT NULL,
    descripcion TEXT,
    contenido LONGTEXT NOT NULL,
    archivo_pdf VARCHAR(255),
    estado TINYINT DEFAULT 1,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_estado (estado)
) ENGINE=InnoDB";
$conexion->query($sql);

// Tabla de agenda
$sql = "CREATE TABLE IF NOT EXISTS agenda (
    id INT PRIMARY KEY AUTO_INCREMENT,
    titulo VARCHAR(255) NOT NULL,
    descripcion TEXT,
    fecha_evento DATE NOT NULL,
    hora_inicio TIME,
    hora_fin TIME,
    ubicacion VARCHAR(255),
    responsable_id INT,
    estado TINYINT DEFAULT 1,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (responsable_id) REFERENCES usuarios(id),
    INDEX idx_fecha (fecha_evento),
    INDEX idx_estado (estado)
) ENGINE=InnoDB";
$conexion->query($sql);

// Tabla de configuración del dashboard
$sql = "CREATE TABLE IF NOT EXISTS dashboard_config (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT NOT NULL,
    widget_tipo VARCHAR(50),
    orden INT DEFAULT 0,
    visible TINYINT DEFAULT 1,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_widget (usuario_id, widget_tipo)
) ENGINE=InnoDB";
$conexion->query($sql);

// Insertar usuarios por defecto
$usuarios = [
    ['admin', 'admin123', 'Administrador', 'Sistema', 'admin@jltiquique.pjud.cl', 'administrador'],
    ['operador', 'operador123', 'Operador', 'Default', 'operador@jltiquique.pjud.cl', 'operador']
];

foreach ($usuarios as $usuario) {
    $sql = "INSERT INTO usuarios (usuario, password, nombre, apellido, email, rol) VALUES (?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE id=id";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param('ssssss', ...$usuario);
    $stmt->execute();
}

// Insertar enlaces relevantes por defecto
$enlaces = [
    ['Carpeta Tribunal', '\\\\saturno.poderjudicial.cl\\TRIBUNALES\\01_iquique\\Laborales\\JLT_Iquique\\', 'Carpeta compartida del tribunal', 'fas fa-folder', 'enlace_interno', 1],
    ['Chat PJUD', 'http://chattest.pjud.cl', 'Comunicación interna de las unidades judiciales', 'fas fa-comment', 'enlace_interno', 1],
    ['Conecta PJUD', 'https://connect.pjud.cl/', 'Sistema de Atención por Videoconferencia', 'fas fa-video', 'enlace_interno', 1],
    ['Consulta FONASA', 'https://frontintegrado.fonasa.cl/', 'Cuenta de convenio para consultar pago de cotizaciones de Salud', 'fas fa-laptop-medical', 'enlace_general', 0],
    ['Correo Web Carbonio', 'https://mail.pjud/static/login/', 'Cliente de Correo WEB', 'fas fa-at', 'enlace_interno', 1],
    ['Intranet PJUD', 'http://www2.intranet.pjud', 'Intranet institucional del Poder Judicial', 'fas fa-building', 'enlace_interno', 1],
    ['Oficina Judicial Virtual', 'https://oficinajudicialvirtual.pjud.cl', 'Consulta de causas y estado de tramitación', 'fas fa-desktop', 'enlace_interno', 1],
    ['Portal de Firmas PJUD', 'https://funpfirmagob.pjud.cl/', 'Portal interno para firma de documentos digitales', 'fas fa-signature', 'enlace_interno', 1],
    ['Portal Personas', 'http://personas.pjud.cl', 'Acceso a información personal y licencias', 'fas fa-id-card', 'enlace_interno', 1],
    ['Quantum', 'https://quantum.pjud.cl/', 'Sistema de información estadística', 'fas fa-link', 'enlace_interno', 1],
    ['Sistema Disciplinario', 'https://disciplinario.pjud.cl/', 'Sistema de Sumarios PJUD', 'fas fa-folder-open', 'enlace_interno', 1]
];

foreach ($enlaces as $enlace) {
    $sql = "INSERT INTO enlaces_relevantes (nombre, url, descripcion, icono, categoria, destacado) VALUES (?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE id=id";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param('sssssi', ...$enlace);
    $stmt->execute();
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Instalación - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #003d7a 0%, #0052a3 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .install-container {
            background: white;
            border-radius: 12px;
            padding: 3rem;
            max-width: 500px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
        }
        .install-title {
            color: #003d7a;
            margin-bottom: 2rem;
            text-align: center;
        }
        .success-message {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1rem;
        }
        .next-steps {
            background: #e7f3ff;
            border-left: 4px solid #003d7a;
            padding: 1rem;
            border-radius: 6px;
            margin-top: 1rem;
        }
        .next-steps h5 {
            color: #003d7a;
            margin-bottom: 0.5rem;
        }
        .next-steps ol li {
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="install-container">
        <h1 class="install-title"><i class="fas fa-check-circle"></i> Instalación Completada</h1>
        
        <div class="success-message">
            <strong>¡Felicidades!</strong> La base de datos ha sido instalada exitosamente.
        </div>

        <div class="alert alert-info">
            <strong>Información Importante:</strong>
            <ul class="mb-0 mt-2">
                <li>Usuarios de prueba creados</li>
                <li>Enlaces relevantes pre-cargados</li>
                <li>Base de datos inicializada</li>
            </ul>
        </div>

        <div class="next-steps">
            <h5><i class="fas fa-arrow-right"></i> Próximos Pasos:</h5>
            <ol>
                <li>Copia <code>config/db_config_example.php</code> a <code>config/db_config.php</code></li>
                <li>Configura tus parámetros de BD en <code>config/db_config.php</code></li>
                <li>Accede a <a href="./public/login.php">login</a> con:<br>
                    <strong>Admin:</strong> admin / admin123<br>
                    <strong>Operador:</strong> operador / operador123
                </li>
            </ol>
        </div>

        <div class="mt-3 text-center">
            <a href="./public/login.php" class="btn btn-primary">Ir al Login</a>
        </div>
    </div>
</body>
</html>
