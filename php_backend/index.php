<?php include 'header.php'; ?>

    <main class="hero">
        <div class="hero-content">
            <h1>Immutable Authenticity</h1>
            <p>Manage, verify, and transfer ownership of premium jewellery using the power of hybrid blockchain technology.</p>
            <div class="hero-actions">
                <a href="verify.php" class="btn btn-primary">Verify a Product</a>
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <a href="register.php" class="btn btn-secondary">Get Started</a>
                <?php endif; ?>
            </div>
        </div>
        <div class="hero-image">
            <div class="placeholder-image">Luxury Jewellery Showcase</div>
        </div>
    </main>

    <section class="features">
        <div class="feature-card">
            <h3>Verified by Blockchain</h3>
            <p>Every piece is minted as a unique ERC-721 token ensuring provenance.</p>
        </div>
        <div class="feature-card">
            <h3>Role Based Access</h3>
            <p>Seamless management for Manufacturers, Shops, and Customers.</p>
        </div>
        <div class="feature-card">
            <h3>QR Code Verification</h3>
            <p>Instantly check authenticity and ownership history via QR scan.</p>
        </div>
    </section>

    <footer>
        <p>&copy; 2024 Luxe Blockchain Management System. All rights reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/web3@4.2.2/dist/web3.min.js"></script>
    <script src="js/web3App.js"></script>
</body>
</html>
