<?php
/**
 * Clase de Autenticación
 */
class Auth {
    private static $db;

    public static function setDB($database) {
        self::$db = $database;
    }

    /**
     * Login de usuario
     */
    public static function login($usuario, $password) {
        $usuario = self::$db->escape($usuario);
        
        $query = "SELECT id, usuario, nombre, apellido, email, rol, estado FROM usuarios WHERE usuario = '$usuario' LIMIT 1";
        $result = self::$db->query($query);

        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            // Verificar contraseña (sin encriptar por especificación)
            $stored_password = self::$db->escape($password);
            $query_pwd = "SELECT id FROM usuarios WHERE id = {$user['id']} AND password = '$stored_password' LIMIT 1";
            $pwd_result = self::$db->query($query_pwd);

            if ($pwd_result && $pwd_result->num_rows > 0) {
                if ($user['estado'] == 1) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['usuario'] = $user['usuario'];
                    $_SESSION['nombre'] = $user['nombre'];
                    $_SESSION['apellido'] = $user['apellido'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['rol'] = $user['rol'];
                    $_SESSION['login_time'] = time();
                    
                    // Registrar último acceso
                    self::registrarAcceso($user['id']);
                    
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Logout de usuario
     */
    public static function logout() {
        session_destroy();
        return true;
    }

    /**
     * Verifica si usuario está autenticado
     */
    public static function isAuthenticated() {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    /**
     * Verifica si usuario es administrador
     */
    public static function isAdmin() {
        return self::isAuthenticated() && $_SESSION['rol'] == 'administrador';
    }

    /**
     * Obtiene rol actual
     */
    public static function getRol() {
        return isset($_SESSION['rol']) ? $_SESSION['rol'] : null;
    }

    /**
     * Obtiene ID del usuario actual
     */
    public static function getUserId() {
        return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    }

    /**
     * Obtiene datos del usuario actual
     */
    public static function getUsuario() {
        return isset($_SESSION['usuario']) ? $_SESSION['usuario'] : null;
    }

    /**
     * Registra acceso del usuario
     */
    private static function registrarAcceso($user_id) {
        $query = "UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = $user_id";
        self::$db->query($query);
    }

    /**
     * Verifica permisos
     */
    public static function checkPermission($required_rol) {
        if (!self::isAuthenticated()) {
            return false;
        }
        
        if ($required_rol == 'admin' && !self::isAdmin()) {
            return false;
        }
        
        return true;
    }
}
?>
