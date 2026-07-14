<?php
/**
 * Módulo de Contactos de Instituciones
 */
define('BASE_PATH', dirname(dirname(dirname(__FILE__))));
require_once BASE_PATH . '/config/db_config.php';
require_once INCLUDES_PATH . '/init.php';

if (!Auth::isAuthenticated()) {
    Session::redirect('public/login.php');
}

$page_title = 'Contactos de Instituciones';

include BASE_PATH . '/views/layout/header.php';
include BASE_PATH . '/views/layout/sidebar.php';
?>

<main class="main-content">
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="page-title"><i class="fas fa-building"></i> Contactos de Instituciones</h1>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-link" style="font-size: 4rem; color: #003d7a; margin-bottom: 1rem; display: block;"></i>
                        <h4 style="color: #003d7a; margin-bottom: 1rem;">Directorio de Instituciones PJUD</h4>
                        <p class="text-muted mb-3">Acceso al portal oficial de contactos de instituciones relacionadas</p>
                        <a href="https://instituciones.pjud.cl/" target="_blank" class="btn btn-lg btn-primary">
                            <i class="fas fa-external-link-alt"></i> Ir a Instituciones PJUD
                        </a>
                    </div>
                </div>

                <div class="alert alert-info mt-4">
                    <h5><i class="fas fa-info-circle"></i> Información</h5>
                    <p class="mb-0">Este módulo te permite acceder al portal centralizado de contactos de todas las instituciones del Poder Judicial de Chile.</p>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include BASE_PATH . '/views/layout/footer.php'; ?>
