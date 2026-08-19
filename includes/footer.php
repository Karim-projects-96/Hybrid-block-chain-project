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
    <script>
        const BASE_URL = "<?php echo isset($base_url) ? $base_url : '/GitHub/Hybrid block chain project'; ?>";
    </script>
    <script src="https://cdn.jsdelivr.net/npm/web3@4.2.2/dist/web3.min.js"></script>
    <script src="<?php echo isset($base_url) ? $base_url : '/GitHub/Hybrid block chain project'; ?>/assets/js/main.js"></script>
</body>
</html>

