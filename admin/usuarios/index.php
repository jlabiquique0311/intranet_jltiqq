<?php
/**
 * Administración de Usuarios - CRUD
 */
define('BASE_PATH', dirname(dirname(dirname(__FILE__))));
require_once BASE_PATH . '/config/db_config.php';
require_once INCLUDES_PATH . '/init.php';

if (!Auth::checkPermission('admin')) {
    Session::redirect('index.php');
}

$page_title = 'Gestión de Usuarios';

// Procesar formulario
$mensaje = '';
$tipo_mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = isset($_POST['accion']) ? $_POST['accion'] : '';

    if ($accion === 'crear') {
        $validator = new Validator();
        $usuario = isset($_POST['usuario']) ? trim($_POST['usuario']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
        $apellido = isset($_POST['apellido']) ? trim($_POST['apellido']) : '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $rol = isset($_POST['rol']) ? $_POST['rol'] : 'operador';

        $validator->required('usuario', $usuario, 'Usuario');
        $validator->required('password', $password, 'Contraseña');
        $validator->minLength('password', $password, 6, 'Contraseña');
        $validator->required('nombre', $nombre, 'Nombre');
        $validator->required('apellido', $apellido, 'Apellido');
        $validator->required('email', $email, 'Email');
        $validator->validateEmail($email);

        if (!$validator->hasErrors()) {
            $usuario_esc = $db->escape($usuario);
            $password_esc = $db->escape($password);
            $nombre_esc = $db->escape($nombre);
            $apellido_esc = $db->escape($apellido);
            $email_esc = $db->escape($email);

            $sql = "INSERT INTO usuarios (usuario, password, nombre, apellido, email, rol) VALUES ('$usuario_esc', '$password_esc', '$nombre_esc', '$apellido_esc', '$email_esc', '$rol')";
            if ($db->query($sql)) {
                $mensaje = 'Usuario creado exitosamente';
                $tipo_mensaje = 'success';
            } else {
                $mensaje = 'Error al crear el usuario';
                $tipo_mensaje = 'danger';
            }
        } else {
            $mensaje = implode(', ', $validator->getErrors());
            $tipo_mensaje = 'danger';
        }
    } elseif ($accion === 'editar') {
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
        $apellido = isset($_POST['apellido']) ? trim($_POST['apellido']) : '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $rol = isset($_POST['rol']) ? $_POST['rol'] : 'operador';
        $estado = isset($_POST['estado']) ? 1 : 0;

        $validator = new Validator();
        $validator->required('nombre', $nombre, 'Nombre');
        $validator->required('apellido', $apellido, 'Apellido');
        $validator->required('email', $email, 'Email');
        $validator->validateEmail($email);

        if (!$validator->hasErrors()) {
            $nombre_esc = $db->escape($nombre);
            $apellido_esc = $db->escape($apellido);
            $email_esc = $db->escape($email);

            $sql = "UPDATE usuarios SET nombre='$nombre_esc', apellido='$apellido_esc', email='$email_esc', rol='$rol', estado=$estado WHERE id=$id";
            if ($db->query($sql)) {
                $mensaje = 'Usuario actualizado exitosamente';
                $tipo_mensaje = 'success';
            } else {
                $mensaje = 'Error al actualizar el usuario';
                $tipo_mensaje = 'danger';
            }
        } else {
            $mensaje = implode(', ', $validator->getErrors());
            $tipo_mensaje = 'danger';
        }
    } elseif ($accion === 'eliminar') {
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if ($id != Auth::getUserId()) { // No permitir eliminar el propio usuario
            $sql = "UPDATE usuarios SET estado=0 WHERE id=$id";
            if ($db->query($sql)) {
                $mensaje = 'Usuario eliminado exitosamente';
                $tipo_mensaje = 'success';
            } else {
                $mensaje = 'Error al eliminar el usuario';
                $tipo_mensaje = 'danger';
            }
        } else {
            $mensaje = 'No puedes eliminar tu propio usuario';
            $tipo_mensaje = 'warning';
        }
    }
}

// Obtener usuarios
$query = "SELECT * FROM usuarios ORDER BY nombre";
$result = $db->query($query);
$usuarios = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $usuarios[] = $row;
    }
}

include BASE_PATH . '/views/layout/header.php';
include BASE_PATH . '/views/layout/sidebar.php';
?>

<main class="main-content">
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="page-title"><i class="fas fa-users"></i> Gestión de Usuarios</h1>
            </div>
        </div>

        <?php if (!empty($mensaje)): ?>
            <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show" role="alert">
                <?php echo Utils::sanitize($mensaje); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Botón para nuevo usuario -->
        <div class="row mb-4">
            <div class="col-12">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuevoUsuario">
                    <i class="fas fa-plus"></i> Nuevo Usuario
                </button>
            </div>
        </div>

        <!-- Tabla de usuarios -->
        <div class="row">
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Usuario</th>
                                <th>Nombre</th>
                                <th>Apellido</th>
                                <th>Email</th>
                                <th>Rol</th>
                                <th>Estado</th>
                                <th>Último Acceso</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($usuarios as $usuario): ?>
                                <tr>
                                    <td><?php echo Utils::sanitize($usuario['usuario']); ?></td>
                                    <td><?php echo Utils::sanitize($usuario['nombre']); ?></td>
                                    <td><?php echo Utils::sanitize($usuario['apellido']); ?></td>
                                    <td><?php echo Utils::sanitize($usuario['email']); ?></td>
                                    <td><span class="badge bg-info"><?php echo ucfirst(str_replace('_', ' ', $usuario['rol'])); ?></span></td>
                                    <td>
                                        <span class="badge <?php echo $usuario['estado'] ? 'bg-success' : 'bg-danger'; ?>">
                                            <?php echo $usuario['estado'] ? 'Activo' : 'Inactivo'; ?>
                                        </span>
                                    </td>
                                    <td><?php echo $usuario['ultimo_acceso'] ? Utils::formatDateTime($usuario['ultimo_acceso']) : 'Nunca'; ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalEditar<?php echo $usuario['id']; ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="accion" value="eliminar">
                                            <input type="hidden" name="id" value="<?php echo $usuario['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Estás seguro?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Modal Nuevo Usuario -->
<div class="modal fade" id="modalNuevoUsuario" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nuevo Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="accion" value="crear">
                    <div class="form-group">
                        <label for="usuario" class="form-label">Usuario</label>
                        <input type="text" class="form-control" id="usuario" name="usuario" required>
                    </div>
                    <div class="form-group">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>
                    <div class="form-group">
                        <label for="apellido" class="form-label">Apellido</label>
                        <input type="text" class="form-control" id="apellido" name="apellido" required>
                    </div>
                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="rol" class="form-label">Rol</label>
                        <select class="form-control" id="rol" name="rol">
                            <option value="operador">Operador</option>
                            <option value="administrador">Administrador</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modales de edición -->
<?php foreach ($usuarios as $usuario): ?>
    <div class="modal fade" id="modalEditar<?php echo $usuario['id']; ?>" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="accion" value="editar">
                        <input type="hidden" name="id" value="<?php echo $usuario['id']; ?>">
                        <div class="form-group">
                            <label class="form-label">Usuario</label>
                            <input type="text" class="form-control" value="<?php echo Utils::sanitize($usuario['usuario']); ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label for="nombre_<?php echo $usuario['id']; ?>" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="nombre_<?php echo $usuario['id']; ?>" name="nombre" value="<?php echo Utils::sanitize($usuario['nombre']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="apellido_<?php echo $usuario['id']; ?>" class="form-label">Apellido</label>
                            <input type="text" class="form-control" id="apellido_<?php echo $usuario['id']; ?>" name="apellido" value="<?php echo Utils::sanitize($usuario['apellido']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="email_<?php echo $usuario['id']; ?>" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email_<?php echo $usuario['id']; ?>" name="email" value="<?php echo Utils::sanitize($usuario['email']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="rol_<?php echo $usuario['id']; ?>" class="form-label">Rol</label>
                            <select class="form-control" id="rol_<?php echo $usuario['id']; ?>" name="rol">
                                <option value="operador" <?php echo $usuario['rol'] === 'operador' ? 'selected' : ''; ?>>Operador</option>
                                <option value="administrador" <?php echo $usuario['rol'] === 'administrador' ? 'selected' : ''; ?>>Administrador</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="estado_<?php echo $usuario['id']; ?>" name="estado" <?php echo $usuario['estado'] ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="estado_<?php echo $usuario['id']; ?>">Activo</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<?php include BASE_PATH . '/views/layout/footer.php'; ?>
