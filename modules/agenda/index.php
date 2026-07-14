<?php
/**
 * Módulo de Agenda
 */
define('BASE_PATH', dirname(dirname(dirname(__FILE__))));
require_once BASE_PATH . '/config/db_config.php';
require_once INCLUDES_PATH . '/init.php';

if (!Auth::isAuthenticated()) {
    Session::redirect('public/login.php');
}

$page_title = 'Agenda';

// Parámetros
$mes = isset($_GET['mes']) ? (int)$_GET['mes'] : date('m');
$anio = isset($_GET['anio']) ? (int)$_GET['anio'] : date('Y');

// Validar
$mes = max(1, min(12, $mes));
$anio = max(2000, min(2099, $anio));

// Obtener eventos del mes
$fecha_inicio = $anio . '-' . str_pad($mes, 2, '0', STR_PAD_LEFT) . '-01';
$fecha_fin = date('Y-m-t', strtotime($fecha_inicio));

$query = "SELECT * FROM agenda WHERE estado = 1 AND fecha_evento >= '$fecha_inicio' AND fecha_evento <= '$fecha_fin' ORDER BY fecha_evento, hora_inicio";
$result = $db->query($query);
$eventos = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $eventos[] = $row;
    }
}

// Agrupar por día
$eventos_por_dia = [];
foreach ($eventos as $evento) {
    $dia = date('d', strtotime($evento['fecha_evento']));
    if (!isset($eventos_por_dia[$dia])) {
        $eventos_por_dia[$dia] = [];
    }
    $eventos_por_dia[$dia][] = $evento;
}

// Días del mes
$primer_dia = date('w', strtotime($fecha_inicio));
$dias_mes = date('t', strtotime($fecha_inicio));

include BASE_PATH . '/views/layout/header.php';
include BASE_PATH . '/views/layout/sidebar.php';
?>

<main class="main-content">
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="page-title"><i class="fas fa-calendar"></i> Agenda</h1>
            </div>
        </div>

        <!-- Controles de navegación -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="?mes=<?php echo $mes == 1 ? 12 : $mes - 1; ?>&anio=<?php echo $mes == 1 ? $anio - 1 : $anio; ?>" class="btn btn-outline-primary">
                        <i class="fas fa-chevron-left"></i> Anterior
                    </a>
                    <h4 style="color: #003d7a; margin: 0;"><?php echo Utils::nombreMes($mes) . ' ' . $anio; ?></h4>
                    <a href="?mes=<?php echo $mes == 12 ? 1 : $mes + 1; ?>&anio=<?php echo $mes == 12 ? $anio + 1 : $anio; ?>" class="btn btn-outline-primary">
                        Siguiente <i class="fas fa-chevron-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table" style="text-align: center;">
                                <thead>
                                    <tr style="background: #e8f0f8;">
                                        <th style="color: #003d7a;">Domingo</th>
                                        <th style="color: #003d7a;">Lunes</th>
                                        <th style="color: #003d7a;">Martes</th>
                                        <th style="color: #003d7a;">Miércoles</th>
                                        <th style="color: #003d7a;">Jueves</th>
                                        <th style="color: #003d7a;">Viernes</th>
                                        <th style="color: #003d7a;">Sábado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $dia_contador = 1;
                                    $semana_contador = 0;
                                    ?>
                                    <tr style="height: 120px;">
                                        <?php for ($i = 0; $i < $primer_dia; $i++): ?>
                                            <td style="background: #f9f9f9; border: 1px solid #ddd;"></td>
                                        <?php endfor; ?>

                                        <?php for ($i = $primer_dia; $i < 7; $i++): ?>
                                            <td style="background: white; border: 1px solid #ddd; vertical-align: top; padding: 8px;">
                                                <div style="font-weight: 600; color: #003d7a; margin-bottom: 4px;"><?php echo $dia_contador; ?></div>
                                                <?php if (isset($eventos_por_dia[$dia_contador])): ?>
                                                    <div style="font-size: 0.75rem;">
                                                        <?php foreach ($eventos_por_dia[$dia_contador] as $evento): ?>
                                                            <div style="background: #e3f2fd; color: #003d7a; padding: 2px 4px; margin-bottom: 2px; border-radius: 3px; cursor: pointer;" data-bs-toggle="modal" data-bs-target="#modalEvento<?php echo $evento['id']; ?>">
                                                                <?php echo Utils::sanitize(Utils::truncate($evento['titulo'], 20)); ?>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <?php $dia_contador++; ?>
                                        <?php endfor; ?>
                                    </tr>

                                    <?php while ($dia_contador <= $dias_mes): ?>
                                        <tr style="height: 120px;">
                                            <?php for ($i = 0; $i < 7 && $dia_contador <= $dias_mes; $i++): ?>
                                                <td style="background: white; border: 1px solid #ddd; vertical-align: top; padding: 8px;">
                                                    <div style="font-weight: 600; color: #003d7a; margin-bottom: 4px;"><?php echo $dia_contador; ?></div>
                                                    <?php if (isset($eventos_por_dia[$dia_contador])): ?>
                                                        <div style="font-size: 0.75rem;">
                                                            <?php foreach ($eventos_por_dia[$dia_contador] as $evento): ?>
                                                                <div style="background: #e3f2fd; color: #003d7a; padding: 2px 4px; margin-bottom: 2px; border-radius: 3px; cursor: pointer;" data-bs-toggle="modal" data-bs-target="#modalEvento<?php echo $evento['id']; ?>">
                                                                    <?php echo Utils::sanitize(Utils::truncate($evento['titulo'], 20)); ?>
                                                                </div>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </td>
                                                <?php $dia_contador++; ?>
                                            <?php endfor; ?>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de eventos del mes -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-gradient-blue">
                        <h5 class="card-title mb-0"><i class="fas fa-list"></i> Eventos del Mes</h5>
                    </div>
                    <div class="card-body">
                        <?php if (count($eventos) > 0): ?>
                            <div class="list-group">
                                <?php foreach ($eventos as $evento): ?>
                                    <div class="list-group-item">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1" style="color: #003d7a;"><?php echo Utils::sanitize($evento['titulo']); ?></h6>
                                            <small class="text-muted"><?php echo Utils::formatDate($evento['fecha_evento'], 'd/m/Y'); ?></small>
                                        </div>
                                        <p class="mb-1 text-muted"><?php echo !empty($evento['descripcion']) ? Utils::sanitize(Utils::truncate($evento['descripcion'], 100)) : 'Sin descripción'; ?></p>
                                        <small class="text-muted">
                                            <?php if (!empty($evento['hora_inicio'])): ?>
                                                <i class="fas fa-clock"></i> <?php echo $evento['hora_inicio']; ?>
                                                <?php if (!empty($evento['hora_fin'])): ?>
                                                    - <?php echo $evento['hora_fin']; ?>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                            <?php if (!empty($evento['ubicacion'])): ?>
                                                | <i class="fas fa-map-marker-alt"></i> <?php echo Utils::sanitize($evento['ubicacion']); ?>
                                            <?php endif; ?>
                                        </small>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info mb-0">
                                <i class="fas fa-info-circle"></i> No hay eventos registrados para este mes.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include BASE_PATH . '/views/layout/footer.php'; ?>
