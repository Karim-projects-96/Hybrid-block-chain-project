<?php
require_once 'db_connect.php';

// Hybrid Processing: Handle AJAX GET from the page
if (isset($_GET['api']) && isset($_GET['tokenId'])) {
    header('Content-Type: application/json');
    $stmt = $pdo->prepare("
        SELECT j.*, 
               m.name as manufacturer_name, m.email as manufacturer_email,
               o.name as current_owner_name, o.email as current_owner_email
        FROM jewellery j
        LEFT JOIN users m ON j.manufacturer_id = m.id
        LEFT JOIN users o ON j.current_owner_id = o.id
        WHERE j.token_id = ?
    ");
    $stmt->execute([$_GET['tokenId']]);
    $jewellery = $stmt->fetch();

    if ($jewellery) {
        http_response_code(200);
        echo json_encode([
            "name" => $jewellery['name'],
            "category" => $jewellery['category'],
            "weight" => $jewellery['weight'],
            "purity" => $jewellery['purity'],
            "manufacturer" => ["name" => $jewellery['manufacturer_name']],
            "currentOwner" => ["name" => $jewellery['current_owner_name']],
            "isStolen" => (bool)$jewellery['is_stolen']
        ]);
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Jewellery not found"]);
    }
    exit();
}
?>
<?php include 'header.php'; ?>
    <style>
        .verify-container { max-width: 600px; margin: 4rem auto; padding: 3rem; background: #fff; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); text-align: center; }
        .verify-container h2 { font-family: var(--font-heading); margin-bottom: 2rem; }
        .verify-input { width: 80%; padding: 1rem; font-size: 1.2rem; border: 2px solid var(--primary); border-radius: 4px; margin-bottom: 1rem; outline: none; }
        #resultContainer { margin-top: 2rem; padding: 1.5rem; border: 1px solid #eee; border-radius: 4px; background: #fdfbf7; text-align: left; }
        .hidden { display: none; }
    </style>

    <main>
        <div class="verify-container">
            <h2>Verify Authenticity</h2>
            <p style="margin-bottom: 2rem;">Enter the Blockchain Token ID or scan the QR code to verify this product's history.</p>
            
            <input type="text" id="tokenIdInput" class="verify-input" placeholder="e.g. 1723405901">
            <br>
            <button id="verifyBtn" class="btn btn-primary" style="padding: 1rem 3rem;">Verify Now</button>

            <div id="resultContainer" class="hidden">
                <!-- Data populated by JS -->
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/web3@4.2.2/dist/web3.min.js"></script>
    <script src="js/web3App.js"></script>
    <script>
        document.getElementById('verifyBtn').addEventListener('click', async () => {
            const tokenIdInput = document.getElementById('tokenIdInput');
            const resultContainer = document.getElementById('resultContainer');
            const tokenId = tokenIdInput.value.trim();
            if (!tokenId) {
                alert('Please enter a Token ID');
                return;
            }

            try {
                // Call this exact PHP file but append ?api=1
                const response = await fetch(`verify.php?api=1&tokenId=${tokenId}`);
                if (!response.ok) {
                    throw new Error('Jewellery not found in database');
                }
                const data = await response.json();

                resultContainer.innerHTML = `
                    <h3>Verification Result</h3>
                    <p><strong>Name:</strong> ${data.name}</p>
                    <p><strong>Category:</strong> ${data.category}</p>
                    <p><strong>Purity:</strong> ${data.purity}</p>
                    <p><strong>Manufacturer:</strong> ${data.manufacturer ? data.manufacturer.name : 'Unknown'}</p>
                    <p><strong>Current Owner:</strong> ${data.currentOwner ? data.currentOwner.name : 'Unknown'}</p>
                    <p><strong>Stolen Status:</strong> ${data.isStolen ? '<span style="color:red">Flagged as Stolen</span>' : '<span style="color:green">Safe</span>'}</p>
                `;
                resultContainer.classList.remove('hidden');
            } catch (error) {
                resultContainer.innerHTML = `<p style="color:red">Error: ${error.message}</p>`;
                resultContainer.classList.remove('hidden');
            }
        });
    </script>
</body>
</html>
