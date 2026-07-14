<?php
/**
 * Utilidades generales
 */
class Utils {
    
    /**
     * Formatea fecha
     */
    public static function formatDate($date, $format = 'd/m/Y') {
        if (empty($date)) return '';
        $timestamp = strtotime($date);
        return date($format, $timestamp);
    }

    /**
     * Formatea fecha y hora
     */
    public static function formatDateTime($datetime, $format = 'd/m/Y H:i') {
        if (empty($datetime)) return '';
        $timestamp = strtotime($datetime);
        return date($format, $timestamp);
    }

    /**
     * Convierte fecha de formato DD/MM/YYYY a YYYY-MM-DD
     */
    public static function dateToSQL($date) {
        if (empty($date)) return null;
        $parts = explode('/', $date);
        if (count($parts) == 3) {
            return $parts[2] . '-' . $parts[1] . '-' . $parts[0];
        }
        return $date;
    }

    /**
     * Convierte fecha de formato YYYY-MM-DD a DD/MM/YYYY
     */
    public static function dateFromSQL($date) {
        if (empty($date)) return '';
        $parts = explode('-', $date);
        if (count($parts) == 3) {
            return $parts[2] . '/' . $parts[1] . '/' . $parts[0];
        }
        return $date;
    }

    /**
     * Calcula edad
     */
    public static function calcularEdad($fecha_nacimiento) {
        $hoy = date('Y-m-d');
        $fecha_nac = new DateTime($fecha_nacimiento);
        $fecha_actual = new DateTime($hoy);
        $edad = $fecha_actual->diff($fecha_nac);
        return $edad->y;
    }

    /**
     * Obtiene días faltantes para cumpleaños
     */
    public static function diasParaCumple($fecha_nacimiento) {
        $hoy = date('Y-m-d');
        $cumple_este_año = date('Y') . '-' . date('m-d', strtotime($fecha_nacimiento));
        
        $fecha_cumple = new DateTime($cumple_este_año);
        $fecha_hoy = new DateTime($hoy);
        
        if ($fecha_cumple < $fecha_hoy) {
            $fecha_cumple->modify('+1 year');
        }
        
        $diff = $fecha_hoy->diff($fecha_cumple);
        return $diff->days;
    }

    /**
     * Verifica si es cumpleaños hoy
     */
    public static function esCumpleHoy($fecha_nacimiento) {
        return date('m-d') == date('m-d', strtotime($fecha_nacimiento));
    }

    /**
     * Verifica si es cumpleaños esta semana
     */
    public static function esCumpleSemana($fecha_nacimiento) {
        $dias = self::diasParaCumple($fecha_nacimiento);
        return $dias >= 0 && $dias <= 7;
    }

    /**
     * Genera slug
     */
    public static function slug($text) {
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = trim($text, '-');
        $text = preg_replace('~-+~', '-', $text);
        return strtolower($text);
    }

    /**
     * Trunca texto
     */
    public static function truncate($text, $length = 100, $suffix = '...') {
        if (strlen($text) <= $length) {
            return $text;
        }
        return substr($text, 0, $length - strlen($suffix)) . $suffix;
    }

    /**
     * Limpia entrada HTML
     */
    public static function sanitize($text) {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Genera token CSRF
     */
    public static function generateCSRFToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Verifica token CSRF
     */
    public static function verifyCSRFToken($token) {
        return isset($_SESSION['csrf_token']) && $_SESSION['csrf_token'] === $token;
    }

    /**
     * Obtiene día de la semana en español
     */
    public static function diaSemana($fecha) {
        $dias = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
        $timestamp = strtotime($fecha);
        return $dias[date('w', $timestamp)];
    }

    /**
     * Obtiene nombre del mes en español
     */
    public static function nombreMes($mes) {
        $meses = ['', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 
                  'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        return $meses[(int)$mes];
    }
}
?>
