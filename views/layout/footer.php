<?php
/**
 * Pie de página
 */
?>
    <footer class="footer">
        <div class="footer-text">&copy; <?php echo date('Y'); ?> Juzgado de Letras del Trabajo de Iquique. Todos los derechos reservados.</div>
        <div class="footer-text">Versión 1.0 - Sistema de Intranet</div>
    </footer>

    <script src="<?php echo ASSETS_URL; ?>/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="<?php echo ASSETS_URL; ?>/js/main.js"></script>
    <?php if (isset($extra_js)): ?>
        <?php foreach ($extra_js as $js): ?>
            <script src="<?php echo $js; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
