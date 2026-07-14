<?php
/**
 * Clase para manejar conexión a BD
 */
class Database {
    private $conn;
    private $error;

    public function __construct() {
        $this->connect();
    }

    private function connect() {
        try {
            $this->conn = new mysqli(
                DB_HOST,
                DB_USER,
                DB_PASS,
                DB_NAME,
                DB_PORT
            );

            if ($this->conn->connect_error) {
                throw new Exception('Error de conexión: ' . $this->conn->connect_error);
            }

            $this->conn->set_charset(DB_CHARSET);
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            $this->log_error($e->getMessage());
            die('Error en la conexión a la base de datos.');
        }
    }

    public function getConnection() {
        return $this->conn;
    }

    public function prepare($query) {
        return $this->conn->prepare($query);
    }

    public function query($query) {
        return $this->conn->query($query);
    }

    public function escape($string) {
        return $this->conn->real_escape_string($string);
    }

    public function lastInsertId() {
        return $this->conn->insert_id;
    }

    public function affectedRows() {
        return $this->conn->affected_rows;
    }

    public function getError() {
        return $this->error;
    }

    private function log_error($message) {
        $log_file = LOGS_PATH . '/database_errors.log';
        if (!is_dir(LOGS_PATH)) {
            mkdir(LOGS_PATH, 0755, true);
        }
        $timestamp = date('Y-m-d H:i:s');
        file_put_contents($log_file, "[$timestamp] $message\n", FILE_APPEND);
    }

    public function close() {
        if ($this->conn) {
            $this->conn->close();
        }
    }

    public function beginTransaction() {
        return $this->conn->begin_transaction();
    }

    public function commit() {
        return $this->conn->commit();
    }

    public function rollback() {
        return $this->conn->rollback();
    }
}
?>
