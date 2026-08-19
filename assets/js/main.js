document.addEventListener('DOMContentLoaded', () => {
    // Basic frontend interactions could go here
    console.log("LuxBlock frontend loaded.");

    // Flash messages dismissal
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    });
});

// --- Blockchain Integration ---
let web3;
let userAccount;
let contractInstance;
// Replace with actual deployed contract address later
const contractAddress = "0x5FbDB2315678afecb367f032d93F642f64180aa3";
const contractABI = [
  {
    "inputs": [
      {"internalType": "address", "name": "to", "type": "address"},
      {"internalType": "string", "name": "uri", "type": "string"},
      {"internalType": "string", "name": "name", "type": "string"},
      {"internalType": "string", "name": "manufacturer", "type": "string"},
      {"internalType": "string", "name": "hallmark", "type": "string"}
    ],
    "name": "mintJewellery",
    "outputs": [{"internalType": "uint256", "name": "", "type": "uint256"}],
    "stateMutability": "nonpayable",
    "type": "function"
  }
];

async function initWeb3() {
    if (window.ethereum) {
        web3 = new Web3(window.ethereum);
        try {
            const accounts = await window.ethereum.request({ method: 'eth_requestAccounts' });
            userAccount = accounts[0];
            contractInstance = new web3.eth.Contract(contractABI, contractAddress);
            console.log("Connected to MetaMask:", userAccount);
            return true;
        } catch(e) {
            console.error("User denied account access");
            return false;
        }
    } else {
        alert("Please install MetaMask to mint on the blockchain!");
        return false;
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const mintForm = document.getElementById('mintForm');
    if (mintForm) {
        mintForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const connected = await initWeb3();
            if (!connected) return;

            const name = document.querySelector('input[name="product_name"]').value;
            const hallmark = document.querySelector('input[name="purity"]').value;
            
            try {
                // Mint transaction
                const btn = mintForm.querySelector('button[type="submit"]');
                btn.textContent = "Confirming in MetaMask...";
                btn.disabled = true;

                const result = await contractInstance.methods.mintJewellery(
                    userAccount, "ipfs://mock", name, "Manufacturer", hallmark
                ).send({ from: userAccount });

                const hash = result.transactionHash;
                
                // Add hash to form and submit
                let hashInput = document.createElement('input');
                hashInput.type = 'hidden';
                hashInput.name = 'real_blockchain_hash';
                hashInput.value = hash;
                mintForm.appendChild(hashInput);
                
                btn.textContent = "Saving to Database...";
                mintForm.submit();
                
            } catch (err) {
                console.error(err);
                alert("Blockchain transaction failed!");
                const btn = mintForm.querySelector('button[type="submit"]');
                btn.textContent = "Mint on Blockchain & Save";
                btn.disabled = false;
            }
        });
    }
});
