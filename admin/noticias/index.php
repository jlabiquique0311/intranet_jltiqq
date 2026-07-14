<?php
/**
 * Administración de Noticias - CRUD
 */
define('BASE_PATH', dirname(dirname(dirname(__FILE__))));
require_once BASE_PATH . '/config/db_config.php';
require_once INCLUDES_PATH . '/init.php';

if (!Auth::checkPermission('admin')) {
    Session::redirect('index.php');
}

$page_title = 'Gestión de Noticias';

$mensaje = '';
$tipo_mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = isset($_POST['accion']) ? $_POST['accion'] : '';

    if ($accion === 'crear') {
        $validator = new Validator();
        $titulo = isset($_POST['titulo']) ? trim($_POST['titulo']) : '';
        $contenido = isset($_POST['contenido']) ? trim($_POST['contenido']) : '';

        $validator->required('titulo', $titulo, 'Título');
        $validator->required('contenido', $contenido, 'Contenido');
        $validator->minLength('contenido', $contenido, 20, 'Contenido');

        if (!$validator->hasErrors()) {
            $titulo_esc = $db->escape($titulo);
            $contenido_esc = $db->escape($contenido);
            $autor_id = Auth::getUserId();

            $sql = "INSERT INTO noticias (titulo, contenido, autor_id, estado) VALUES ('$titulo_esc', '$contenido_esc', $autor_id, 1)";
            if ($db->query($sql)) {
                $mensaje = 'Noticia creada exitosamente';
                $tipo_mensaje = 'success';
            } else {
                $mensaje = 'Error al crear la noticia';
                $tipo_mensaje = 'danger';
            }
        } else {
            $mensaje = implode(', ', $validator->getErrors());
            $tipo_mensaje = 'danger';
        }
    } elseif ($accion === 'editar') {
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $titulo = isset($_POST['titulo']) ? trim($_POST['titulo']) : '';
        $contenido = isset($_POST['contenido']) ? trim($_POST['contenido']) : '';
        $estado = isset($_POST['estado']) ? 1 : 0;

        $validator = new Validator();
        $validator->required('titulo', $titulo, 'Título');
        $validator->required('contenido', $contenido, 'Contenido');

        if (!$validator->hasErrors()) {
            $titulo_esc = $db->escape($titulo);
            $contenido_esc = $db->escape($contenido);

            $sql = "UPDATE noticias SET titulo='$titulo_esc', contenido='$contenido_esc', estado=$estado WHERE id=$id";
            if ($db->query($sql)) {
                $mensaje = 'Noticia actualizada exitosamente';
                $tipo_mensaje = 'success';
            } else {
                $mensaje = 'Error al actualizar la noticia';
                $tipo_mensaje = 'danger';
            }
        } else {
            $mensaje = implode(', ', $validator->getErrors());
            $tipo_mensaje = 'danger';
        }
    } elseif ($accion === 'eliminar') {
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $sql = "UPDATE noticias SET estado=0 WHERE id=$id";
        if ($db->query($sql)) {
            $mensaje = 'Noticia eliminada exitosamente';
            $tipo_mensaje = 'success';
        } else {
            $mensaje = 'Error al eliminar la noticia';
            $tipo_mensaje = 'danger';
        }
    }
}

// Obtener noticias
$query = "SELECT n.*, u.nombre, u.apellido FROM noticias n JOIN usuarios u ON n.autor_id = u.id ORDER BY n.fecha_creacion DESC";
$result = $db->query($query);
$noticias = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $noticias[] = $row;
    }
}

include BASE_PATH . '/views/layout/header.php';
include BASE_PATH . '/views/layout/sidebar.php';
?>

<main class="main-content">
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="page-title"><i class="fas fa-newspaper"></i> Gestión de Noticias</h1>
            </div>
        </div>

        <?php if (!empty($mensaje)): ?>
            <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show" role="alert">
                <?php echo Utils::sanitize($mensaje); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Botón para nueva noticia -->
        <div class="row mb-4">
            <div class="col-12">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuevaNoticia">
                    <i class="fas fa-plus"></i> Nueva Noticia
                </button>
            </div>
        </div>

        <!-- Lista de noticias -->
        <div class="row">
            <div class="col-12">
                <?php if (count($noticias) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Título</th>
                                    <th>Autor</th>
                                    <th>Fecha</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($noticias as $noticia): ?>
                                    <tr>
                                        <td><?php echo Utils::sanitize(Utils::truncate($noticia['titulo'], 50)); ?></td>
                                        <td><?php echo Utils::sanitize($noticia['nombre'] . ' ' . $noticia['apellido']); ?></td>
                                        <td><?php echo Utils::formatDateTime($noticia['fecha_creacion']); ?></td>
                                        <td><span class="badge <?php echo $noticia['estado'] ? 'bg-success' : 'bg-danger'; ?>"><?php echo $noticia['estado'] ? 'Publicada' : 'Borrador'; ?></span></td>
                                        <td>
                                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalEditar<?php echo $noticia['id']; ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="accion" value="eliminar">
                                                <input type="hidden" name="id" value="<?php echo $noticia['id']; ?>">
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
                <?php else: ?>
                    <div class="alert alert-info">No hay noticias registradas.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<!-- Modal Nueva Noticia -->
<div class="modal fade" id="modalNuevaNoticia" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nueva Noticia</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="accion" value="crear">
                    <div class="form-group">
                        <label for="titulo" class="form-label">Título</label>
                        <input type="text" class="form-control" id="titulo" name="titulo" required>
                    </div>
                    <div class="form-group">
                        <label for="contenido" class="form-label">Contenido</label>
                        <textarea class="form-control" id="contenido" name="contenido" rows="8" required></textarea>
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
<?php foreach ($noticias as $noticia): ?>
    <div class="modal fade" id="modalEditar<?php echo $noticia['id']; ?>" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Noticia</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="accion" value="editar">
                        <input type="hidden" name="id" value="<?php echo $noticia['id']; ?>">
                        <div class="form-group">
                            <label for="titulo_<?php echo $noticia['id']; ?>" class="form-label">Título</label>
                            <input type="text" class="form-control" id="titulo_<?php echo $noticia['id']; ?>" name="titulo" value="<?php echo Utils::sanitize($noticia['titulo']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="contenido_<?php echo $noticia['id']; ?>" class="form-label">Contenido</label>
                            <textarea class="form-control" id="contenido_<?php echo $noticia['id']; ?>" name="contenido" rows="8" required><?php echo Utils::sanitize($noticia['contenido']); ?></textarea>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="estado_<?php echo $noticia['id']; ?>" name="estado" <?php echo $noticia['estado'] ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="estado_<?php echo $noticia['id']; ?>">Publicada</label>
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
