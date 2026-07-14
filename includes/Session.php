<?php
/**
 * Clase para manejo de sesiones
 */
class Session {
    
    /**
     * Verifica si la sesión es válida
     */
    public static function verify() {
        // Si no está autenticado y no está en login, redirige
        if (!self::isOnPublicPage()) {
            if (!self::isAuthenticated()) {
                self::redirect('login.php');
            }
            
            // Verifica timeout de sesión
            if (self::isSessionExpired()) {
                self::destroy();
                self::redirect('login.php?msg=sesion_expirada');
            }
            
            // Actualiza tiempo de última actividad
            $_SESSION['last_activity'] = time();
        }
    }

    /**
     * Verifica si está en página pública
     */
    private static function isOnPublicPage() {
        $public_pages = ['login.php', 'install.php', 'logout.php'];
        $current_page = basename($_SERVER['SCRIPT_FILENAME']);
        return in_array($current_page, $public_pages);
    }

    /**
     * Verifica si sesión está autenticada
     */
    public static function isAuthenticated() {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    /**
     * Verifica si sesión ha expirado
     */
    private static function isSessionExpired() {
        if (!isset($_SESSION['last_activity'])) {
            $_SESSION['last_activity'] = time();
            return false;
        }
        
        if (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT) {
            return true;
        }
        
        return false;
    }

    /**
     * Destruye la sesión
     */
    public static function destroy() {
        $_SESSION = [];
        session_destroy();
    }

    /**
     * Redirige a una URL
     */
    public static function redirect($page) {
        header('Location: ' . APP_URL . '/' . $page);
        exit();
    }

    /**
     * Establece un mensaje flash
     */
    public static function setFlash($key, $message, $type = 'info') {
        $_SESSION['flash'][$key] = ['message' => $message, 'type' => $type];
    }

    /**
     * Obtiene mensaje flash
     */
    public static function getFlash($key) {
        if (isset($_SESSION['flash'][$key])) {
            $flash = $_SESSION['flash'][$key];
            unset($_SESSION['flash'][$key]);
            return $flash;
        }
        return null;
    }
}
?>
