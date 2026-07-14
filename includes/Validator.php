<?php
/**
 * Clase de validación
 */
class Validator {
    private $errors = [];

    /**
     * Valida email
     */
    public function validateEmail($email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->addError('email', 'El email no es válido');
            return false;
        }
        return true;
    }

    /**
     * Valida teléfono
     */
    public function validatePhone($phone) {
        if (!preg_match('/^[0-9\+\-\s\(\)]{8,}$/', $phone)) {
            $this->addError('phone', 'El teléfono no es válido');
            return false;
        }
        return true;
    }

    /**
     * Valida campo requerido
     */
    public function required($field, $value, $label = null) {
        if (empty($value)) {
            $label = $label ? $label : $field;
            $this->addError($field, ucfirst($label) . ' es requerido');
            return false;
        }
        return true;
    }

    /**
     * Valida longitud mínima
     */
    public function minLength($field, $value, $length, $label = null) {
        if (strlen($value) < $length) {
            $label = $label ? $label : $field;
            $this->addError($field, ucfirst($label) . ' debe tener al menos ' . $length . ' caracteres');
            return false;
        }
        return true;
    }

    /**
     * Valida longitud máxima
     */
    public function maxLength($field, $value, $length, $label = null) {
        if (strlen($value) > $length) {
            $label = $label ? $label : $field;
            $this->addError($field, ucfirst($label) . ' no puede exceder ' . $length . ' caracteres');
            return false;
        }
        return true;
    }

    /**
     * Valida fecha
     */
    public function validateDate($field, $value, $format = 'd/m/Y', $label = null) {
        $d = DateTime::createFromFormat($format, $value);
        if (!$d || $d->format($format) != $value) {
            $label = $label ? $label : $field;
            $this->addError($field, ucfirst($label) . ' no tiene un formato válido');
            return false;
        }
        return true;
    }

    /**
     * Valida número
     */
    public function validateNumber($field, $value, $label = null) {
        if (!is_numeric($value)) {
            $label = $label ? $label : $field;
            $this->addError($field, ucfirst($label) . ' debe ser un número');
            return false;
        }
        return true;
    }

    /**
     * Agrega error
     */
    private function addError($field, $message) {
        $this->errors[$field] = $message;
    }

    /**
     * Obtiene errores
     */
    public function getErrors() {
        return $this->errors;
    }

    /**
     * Verifica si hay errores
     */
    public function hasErrors() {
        return !empty($this->errors);
    }

    /**
     * Obtiene error de un campo
     */
    public function getError($field) {
        return isset($this->errors[$field]) ? $this->errors[$field] : null;
    }
}
?>
