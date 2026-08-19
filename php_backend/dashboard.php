<?php
require_once 'db_connect.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Manufacturer') {
    header("Location: login.php");
    exit();
}

$status = '';
$statusColor = '';

// Hybrid Processing: Handle the AJAX POST from web3App.js after MetaMask mints
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    if ($data && isset($data['tokenId'])) {
        $stmt = $pdo->prepare("INSERT INTO jewellery (token_id, name, category, weight, purity, hallmark, manufacturer_id, current_owner_id, ipfs_hash) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        try {
            $stmt->execute([
                $data['tokenId'], $data['name'], $data['category'], $data['weight'], 
                $data['purity'], $data['hallmark'], $_SESSION['user_id'], $_SESSION['user_id'], $data['ipfsHash']
            ]);
            http_response_code(201);
            echo json_encode(["message" => "Jewellery saved successfully"]);
        } catch(PDOException $e) {
            http_response_code(500);
            echo json_encode(["message" => "Error adding jewellery: " . $e->getMessage()]);
        }
        exit(); // Stop PHP rendering for AJAX request
    }
}
?>
<?php include 'header.php'; ?>
    <style>
        .dashboard-container { max-width: 800px; margin: 4rem auto; padding: 2rem; background: #fff; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .dashboard-container h2 { font-family: var(--font-heading); margin-bottom: 1.5rem; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 600; }
        .form-group input, .form-group select { width: 100%; padding: 0.8rem; border: 1px solid #ccc; border-radius: 4px; }
        .full-width { grid-column: span 2; }
        .auth-btn { margin-top: 1rem; padding: 1rem 2rem; }
    </style>

    <main>
        <div class="dashboard-container">
            <h2>Manufacturer Dashboard</h2>
            <p style="margin-bottom: 2rem;">Mint new jewellery on the blockchain and store metadata.</p>
            
            <!-- Hybrid Form: Intercepted by web3App.js, not directly submitted via POST -->
            <form id="mintForm">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="jewelName">Jewellery Name</label>
                        <input type="text" id="jewelName" required>
                    </div>
                    <div class="form-group">
                        <label for="jewelCategory">Category</label>
                        <select id="jewelCategory" required>
                            <option value="Ring">Ring</option>
                            <option value="Necklace">Necklace</option>
                            <option value="Bracelet">Bracelet</option>
                            <option value="Earrings">Earrings</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="jewelWeight">Weight (grams)</label>
                        <input type="number" id="jewelWeight" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="jewelPurity">Purity</label>
                        <select id="jewelPurity" required>
                            <option value="18K">18K</option>
                            <option value="22K">22K</option>
                            <option value="24K">24K</option>
                        </select>
                    </div>
                    <div class="form-group full-width">
                        <label for="jewelHallmark">Hallmark / Certificate Number</label>
                        <input type="text" id="jewelHallmark" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary auth-btn">Mint to Blockchain & Save</button>
            </form>
            <div id="mintStatus" style="margin-top: 1rem; font-weight: 600;"></div>
        </div>
    </main>

    <script>
        // Tell web3App.js that it should post to dashboard.php via AJAX instead of using JWT
        window.PHP_MODE = true;
    </script>
    <script src="https://cdn.jsdelivr.net/npm/web3@4.2.2/dist/web3.min.js"></script>
    <script src="js/web3App.js"></script>
</body>
</html>
