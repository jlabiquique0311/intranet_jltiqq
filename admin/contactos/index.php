<?php
/**
 * Administración de Contactos - CRUD
 */
define('BASE_PATH', dirname(dirname(dirname(__FILE__))));
require_once BASE_PATH . '/config/db_config.php';
require_once INCLUDES_PATH . '/init.php';

if (!Auth::checkPermission('admin')) {
    Session::redirect('index.php');
}

$page_title = 'Gestión de Contactos';

$mensaje = '';
$tipo_mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = isset($_POST['accion']) ? $_POST['accion'] : '';

    if ($accion === 'crear' || $accion === 'editar') {
        $validator = new Validator();
        $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
        $apellido = isset($_POST['apellido']) ? trim($_POST['apellido']) : '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $telefono = isset($_POST['telefono']) ? trim($_POST['telefono']) : '';
        $departamento = isset($_POST['departamento']) ? trim($_POST['departamento']) : '';
        $cargo = isset($_POST['cargo']) ? trim($_POST['cargo']) : '';

        $validator->required('nombre', $nombre, 'Nombre');
        $validator->required('apellido', $apellido, 'Apellido');
        if (!empty($email)) {
            $validator->validateEmail($email);
        }

        if (!$validator->hasErrors()) {
            $nombre_esc = $db->escape($nombre);
            $apellido_esc = $db->escape($apellido);
            $email_esc = $db->escape($email);
            $telefono_esc = $db->escape($telefono);
            $departamento_esc = $db->escape($departamento);
            $cargo_esc = $db->escape($cargo);

            if ($accion === 'crear') {
                $sql = "INSERT INTO contactos (nombre, apellido, email, telefono, departamento, cargo) VALUES ('$nombre_esc', '$apellido_esc', '$email_esc', '$telefono_esc', '$departamento_esc', '$cargo_esc')";
            } else {
                $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
                $estado = isset($_POST['estado']) ? 1 : 0;
                $sql = "UPDATE contactos SET nombre='$nombre_esc', apellido='$apellido_esc', email='$email_esc', telefono='$telefono_esc', departamento='$departamento_esc', cargo='$cargo_esc', estado=$estado WHERE id=$id";
            }

            if ($db->query($sql)) {
                $mensaje = $accion === 'crear' ? 'Contacto creado' : 'Contacto actualizado';
                $tipo_mensaje = 'success';
            } else {
                $mensaje = 'Error al procesar el contacto';
                $tipo_mensaje = 'danger';
            }
        } else {
            $mensaje = implode(', ', $validator->getErrors());
            $tipo_mensaje = 'danger';
        }
    } elseif ($accion === 'eliminar') {
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $sql = "UPDATE contactos SET estado=0 WHERE id=$id";
        if ($db->query($sql)) {
            $mensaje = 'Contacto eliminado';
            $tipo_mensaje = 'success';
        }
    }
}

// Obtener contactos
$query = "SELECT * FROM contactos ORDER BY apellido, nombre";
$result = $db->query($query);
$contactos = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $contactos[] = $row;
    }
}

include BASE_PATH . '/views/layout/header.php';
include BASE_PATH . '/views/layout/sidebar.php';
?>

<main class="main-content">
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="page-title"><i class="fas fa-address-book"></i> Gestión de Contactos</h1>
            </div>
        </div>

        <?php if (!empty($mensaje)): ?>
            <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show" role="alert">
                <?php echo Utils::sanitize($mensaje); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Botón para nuevo contacto -->
        <div class="row mb-4">
            <div class="col-12">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuevoContacto">
                    <i class="fas fa-plus"></i> Nuevo Contacto
                </button>
            </div>
        </div>

        <!-- Tabla de contactos -->
        <div class="row">
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Apellido</th>
                                <th>Email</th>
                                <th>Teléfono</th>
                                <th>Departamento</th>
                                <th>Cargo</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($contactos as $contacto): ?>
                                <tr>
                                    <td><?php echo Utils::sanitize($contacto['nombre']); ?></td>
                                    <td><?php echo Utils::sanitize($contacto['apellido']); ?></td>
                                    <td><?php echo !empty($contacto['email']) ? Utils::sanitize($contacto['email']) : '-'; ?></td>
                                    <td><?php echo !empty($contacto['telefono']) ? Utils::sanitize($contacto['telefono']) : '-'; ?></td>
                                    <td><?php echo !empty($contacto['departamento']) ? Utils::sanitize($contacto['departamento']) : '-'; ?></td>
                                    <td><?php echo !empty($contacto['cargo']) ? Utils::sanitize($contacto['cargo']) : '-'; ?></td>
                                    <td><span class="badge <?php echo $contacto['estado'] ? 'bg-success' : 'bg-danger'; ?>"><?php echo $contacto['estado'] ? 'Activo' : 'Inactivo'; ?></span></td>
                                    <td>
                                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalEditar<?php echo $contacto['id']; ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="accion" value="eliminar">
                                            <input type="hidden" name="id" value="<?php echo $contacto['id']; ?>">
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

<!-- Modal Nuevo Contacto -->
<div class="modal fade" id="modalNuevoContacto" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nuevo Contacto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="accion" value="crear">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nombre" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="apellido" class="form-label">Apellido</label>
                                <input type="text" class="form-control" id="apellido" name="apellido" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email">
                    </div>
                    <div class="form-group">
                        <label for="telefono" class="form-label">Teléfono</label>
                        <input type="tel" class="form-control" id="telefono" name="telefono">
                    </div>
                    <div class="form-group">
                        <label for="departamento" class="form-label">Departamento</label>
                        <input type="text" class="form-control" id="departamento" name="departamento">
                    </div>
                    <div class="form-group">
                        <label for="cargo" class="form-label">Cargo</label>
                        <input type="text" class="form-control" id="cargo" name="cargo">
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
<?php foreach ($contactos as $contacto): ?>
    <div class="modal fade" id="modalEditar<?php echo $contacto['id']; ?>" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Contacto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="accion" value="editar">
                        <input type="hidden" name="id" value="<?php echo $contacto['id']; ?>">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Nombre</label>
                                    <input type="text" class="form-control" name="nombre" value="<?php echo Utils::sanitize($contacto['nombre']); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Apellido</label>
                                    <input type="text" class="form-control" name="apellido" value="<?php echo Utils::sanitize($contacto['apellido']); ?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" value="<?php echo Utils::sanitize($contacto['email']); ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Teléfono</label>
                            <input type="tel" class="form-control" name="telefono" value="<?php echo Utils::sanitize($contacto['telefono']); ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Departamento</label>
                            <input type="text" class="form-control" name="departamento" value="<?php echo Utils::sanitize($contacto['departamento']); ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Cargo</label>
                            <input type="text" class="form-control" name="cargo" value="<?php echo Utils::sanitize($contacto['cargo']); ?>">
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="estado_<?php echo $contacto['id']; ?>" name="estado" <?php echo $contacto['estado'] ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="estado_<?php echo $contacto['id']; ?>">Activo</label>
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
