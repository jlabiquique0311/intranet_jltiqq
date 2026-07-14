<?php
/**
 * Módulo de Sistemas PJUD
 */
define('BASE_PATH', dirname(dirname(dirname(__FILE__))));
require_once BASE_PATH . '/config/db_config.php';
require_once INCLUDES_PATH . '/init.php';

if (!Auth::isAuthenticated()) {
    Session::redirect('public/login.php');
}

$page_title = 'Sistemas PJUD';

$sistemas = [
    [
        'nombre' => 'SITLA',
        'url' => 'http://www.laboral.pjud',
        'descripcion' => 'Sistema de Información Tributaria - Labor',
        'icono' => 'fas fa-gavel'
    ],
    [
        'nombre' => 'SITCO',
        'url' => 'http://www.cobranza.pjud',
        'descripcion' => 'Sistema de Información Tributaria - Cobranza',
        'icono' => 'fas fa-money-bill-wave'
    ],
    [
        'nombre' => 'SITCI',
        'url' => 'http://www.civil.pjud',
        'descripcion' => 'Sistema de Información Tributaria - Civil',
        'icono' => 'fas fa-file-contract'
    ],
    [
        'nombre' => 'UNIJUD',
        'url' => '#',
        'descripcion' => 'Sistema Unificado de Poder Judicial (Implementación: Septiembre 2026)',
        'icono' => 'fas fa-network-wired',
        'disabled' => true
    ]
];

include BASE_PATH . '/views/layout/header.php';
include BASE_PATH . '/views/layout/sidebar.php';
?>

<main class="main-content">
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="page-title"><i class="fas fa-desktop"></i> Sistemas PJUD</h1>
                <p class="text-muted">Accesos directos a los sistemas principales del Poder Judicial</p>
            </div>
        </div>

        <div class="row">
            <?php foreach ($sistemas as $sistema): ?>
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="card h-100" style="<?php echo isset($sistema['disabled']) ? 'opacity: 0.7;' : ''; ?>">
                        <div class="card-body text-center">
                            <div style="font-size: 3rem; color: #003d7a; margin-bottom: 1rem;">
                                <i class="<?php echo $sistema['icono']; ?}"></i>
                            </div>
                            <h5 class="card-title" style="color: #003d7a;"><?php echo $sistema['nombre']; ?></h5>
                            <p class="card-text text-muted" style="font-size: 0.9rem;"><?php echo $sistema['descripcion']; ?></p>
                            
                            <?php if (isset($sistema['disabled']) && $sistema['disabled']): ?>
                                <button class="btn btn-secondary" disabled>
                                    <i class="fas fa-clock"></i> Próximamente
                                </button>
                            <?php else: ?>
                                <a href="<?php echo $sistema['url']; ?>" target="_blank" class="btn btn-primary w-100">
                                    <i class="fas fa-external-link-alt"></i> Acceder
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Información adicional -->
        <div class="row mt-4">
            <div class="col-lg-8 offset-lg-2">
                <div class="alert alert-info">
                    <h5><i class="fas fa-info-circle"></i> Información</h5>
                    <p class="mb-0">Los sistemas PJUD son plataformas especializadas de gestión judicial. Se abrirán en una nueva ventana del navegador. Asegúrate de contar con tu credencial de acceso institucional.</p>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include BASE_PATH . '/views/layout/footer.php'; ?>
