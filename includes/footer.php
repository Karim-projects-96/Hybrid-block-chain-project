    </main>
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-logo">LuxBlock</div>
            <p>Securing jewellery provenance.</p>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> LuxBlock. All Rights Reserved.</p>
        </div>
    </footer>

    <!-- Main JS -->
    <?php
    $doc_root = str_replace('\', '/', $_SERVER['DOCUMENT_ROOT']);
    $project_root = str_replace('\', '/', dirname(__DIR__));
    $base_url = str_replace($doc_root, '', $project_root);
    if (php_sapi_name() == 'cli-server') { $base_url = ""; }
    ?>
    <script>
        const BASE_URL = "<?php echo $base_url; ?>";
    </script>
    <script src="https://cdn.jsdelivr.net/npm/web3@4.2.2/dist/web3.min.js"></script>
    <script src="<?php echo $base_url; ?>/assets/js/main.js"></script>
</body>
</html>
