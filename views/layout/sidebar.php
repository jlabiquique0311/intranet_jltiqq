<?php
/**
 * Barra lateral de navegación
 */
?>
    <aside class="sidebar">
        <div class="logo-section">
            <div class="logo-circle">
                <i class="fas fa-gavel"></i>
            </div>
            <div class="logo-text">JLT Iquique</div>
        </div>

        <nav>
            <ul class="sidebar-menu">
                <li class="sidebar-item">
                    <a href="<?php echo APP_URL; ?>/index.php" class="sidebar-link <?php echo basename($_SERVER['SCRIPT_FILENAME']) === 'index.php' ? 'active' : ''; ?>">
                        <i class="fas fa-home"></i> Home
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="<?php echo APP_URL; ?>/modules/cumpleanos/index.php" class="sidebar-link <?php echo strpos($_SERVER['REQUEST_URI'], 'cumpleanos') !== false ? 'active' : ''; ?>">
                        <i class="fas fa-birthday-cake"></i> Cumpleaños
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="<?php echo APP_URL; ?>/modules/noticias/index.php" class="sidebar-link <?php echo strpos($_SERVER['REQUEST_URI'], 'noticias') !== false ? 'active' : ''; ?>">
                        <i class="fas fa-newspaper"></i> Noticias
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="<?php echo APP_URL; ?>/modules/enlaces/index.php" class="sidebar-link <?php echo strpos($_SERVER['REQUEST_URI'], 'enlaces') !== false ? 'active' : ''; ?>">
                        <i class="fas fa-link"></i> Enlaces Relevantes
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="<?php echo APP_URL; ?>/modules/sistemas/index.php" class="sidebar-link <?php echo strpos($_SERVER['REQUEST_URI'], 'sistemas') !== false ? 'active' : ''; ?>">
                        <i class="fas fa-desktop"></i> Sistemas PJUD
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="<?php echo APP_URL; ?>/modules/gestion_documental/index.php" class="sidebar-link <?php echo strpos($_SERVER['REQUEST_URI'], 'gestion_documental') !== false ? 'active' : ''; ?>">
                        <i class="fas fa-folder"></i> Gestión Documental
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="<?php echo APP_URL; ?>/modules/contactos/index.php" class="sidebar-link <?php echo strpos($_SERVER['REQUEST_URI'], 'contactos') !== false ? 'active' : ''; ?>">
                        <i class="fas fa-address-book"></i> Libreta de Contactos
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="<?php echo APP_URL; ?>/modules/instituciones/index.php" class="sidebar-link <?php echo strpos($_SERVER['REQUEST_URI'], 'instituciones') !== false ? 'active' : ''; ?>">
                        <i class="fas fa-building"></i> Contactos Instituciones
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="<?php echo APP_URL; ?>/modules/decretos/index.php" class="sidebar-link <?php echo strpos($_SERVER['REQUEST_URI'], 'decretos') !== false ? 'active' : ''; ?>">
                        <i class="fas fa-file-pdf"></i> Decretos Económicos
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="<?php echo APP_URL; ?>/modules/biblioteca/index.php" class="sidebar-link <?php echo strpos($_SERVER['REQUEST_URI'], 'biblioteca') !== false ? 'active' : ''; ?>">
                        <i class="fas fa-book"></i> Biblioteca Documentos
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="<?php echo APP_URL; ?>/modules/manuales/index.php" class="sidebar-link <?php echo strpos($_SERVER['REQUEST_URI'], 'manuales') !== false ? 'active' : ''; ?>">
                        <i class="fas fa-book-open"></i> Manual Procedimientos
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="<?php echo APP_URL; ?>/modules/agenda/index.php" class="sidebar-link <?php echo strpos($_SERVER['REQUEST_URI'], 'agenda') !== false ? 'active' : ''; ?>">
                        <i class="fas fa-calendar"></i> Agenda
                    </a>
                </li>
                <?php if (Auth::isAdmin()): ?>
                    <li class="sidebar-item">
                        <a href="<?php echo APP_URL; ?>/admin/index.php" class="sidebar-link <?php echo strpos($_SERVER['REQUEST_URI'], 'admin') !== false ? 'active' : ''; ?>">
                            <i class="fas fa-cog"></i> Administración
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </aside>
