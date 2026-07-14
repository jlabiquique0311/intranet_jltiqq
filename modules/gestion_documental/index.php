<?php
/**
 * Módulo de Gestión Documental
 */
define('BASE_PATH', dirname(dirname(dirname(__FILE__))));
require_once BASE_PATH . '/config/db_config.php';
require_once INCLUDES_PATH . '/init.php';

if (!Auth::isAuthenticated()) {
    Session::redirect('public/login.php');
}

$page_title = 'Gestión Documental';

include BASE_PATH . '/views/layout/header.php';
include BASE_PATH . '/views/layout/sidebar.php';
?>

<main class="main-content">
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="page-title"><i class="fas fa-folder"></i> Sistema de Gestión Documental</h1>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-file-alt" style="font-size: 4rem; color: #003d7a; margin-bottom: 1rem; display: block;"></i>
                        <h4 style="color: #003d7a; margin-bottom: 1rem;">Acceso a Docus 2</h4>
                        <p class="text-muted mb-3">Plataforma actual de gestión de documentos</p>
                        <a href="http://10.1.32.230/docus2" target="_blank" class="btn btn-lg btn-primary">
                            <i class="fas fa-external-link-alt"></i> Acceder a Docus 2
                        </a>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header bg-gradient-blue">
                        <h5 class="card-title mb-0">Próximamente</h5>
                    </div>
                    <div class="card-body">
                        <h6 style="color: #003d7a;"><i class="fas fa-rocket"></i> Gesdocus 3.0</h6>
                        <p class="text-muted">Se está desarrollando una nueva versión del sistema de gestión documental con interfaces mejoradas y mayor funcionalidad.</p>
                        <p class="text-muted mb-0"><small><i class="fas fa-clock"></i> Estado: En desarrollo</small></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include BASE_PATH . '/views/layout/footer.php'; ?>
