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
    $script_path = str_replace('\\', '/', $_SERVER['SCRIPT_FILENAME']);
    $root_path = str_replace('\\', '/', dirname(__DIR__));
    $relative_path = str_replace($root_path, '', $script_path);
    $depth = substr_count(trim($relative_path, '/'), '/');
    $base_url = $depth > 0 ? str_repeat('../', $depth) : '.';
    $base_url = rtrim($base_url, '/');
    ?>
    <script>
        const BASE_URL = "<?php echo $base_url; ?>";
    </script>
    <script src="https://cdn.jsdelivr.net/npm/web3@4.2.2/dist/web3.min.js"></script>
    <script src="<?php echo $base_url; ?>/assets/js/main.js"></script>
</body>
</html>
