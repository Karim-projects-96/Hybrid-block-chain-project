let web3;
let userAccount;
let contractInstance;

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

document.addEventListener('DOMContentLoaded', async () => {
    const connectBtn = document.getElementById('connectWalletBtn');
    
    if (connectBtn) {
        connectBtn.addEventListener('click', async () => {
            if (window.ethereum) {
                try {
                    const accounts = await window.ethereum.request({ method: 'eth_requestAccounts' });
                    userAccount = accounts[0];
                    web3 = new Web3(window.ethereum);
                    contractInstance = new web3.eth.Contract(contractABI, contractAddress);
                    connectBtn.textContent = `${userAccount.substring(0, 6)}...${userAccount.substring(38)}`;
                } catch (error) {
                    console.error("User denied account access", error);
                }
            } else {
                alert('Please install MetaMask to use this feature!');
            }
        });
    }

    if (window.ethereum) {
        const accounts = await window.ethereum.request({ method: 'eth_accounts' });
        if (accounts.length > 0) {
            userAccount = accounts[0];
            web3 = new Web3(window.ethereum);
            contractInstance = new web3.eth.Contract(contractABI, contractAddress);
            if (connectBtn) {
                connectBtn.textContent = `${userAccount.substring(0, 6)}...${userAccount.substring(38)}`;
            }
        }
    }

    // Dashboard Mint Form
    const mintForm = document.getElementById('mintForm');
    if (mintForm) {
        mintForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const statusDiv = document.getElementById('mintStatus');
            
            if (!web3 || !userAccount) {
                statusDiv.style.color = 'red';
                statusDiv.textContent = 'Please connect MetaMask first!';
                return;
            }

            const name = document.getElementById('jewelName').value;
            const category = document.getElementById('jewelCategory').value;
            const weight = parseFloat(document.getElementById('jewelWeight').value);
            const purity = document.getElementById('jewelPurity').value;
            const hallmark = document.getElementById('jewelHallmark').value;

            statusDiv.style.color = '#D4AF37';
            statusDiv.textContent = 'Waiting for MetaMask confirmation...';

            try {
                // 1. Mint on Blockchain
                const mockIpfsUri = "ipfs://mockhash" + Date.now();
                const result = await contractInstance.methods.mintJewellery(
                    userAccount, mockIpfsUri, name, "Current Manufacturer", hallmark
                ).send({ from: userAccount });

                // Since we don't have full ABI to read events easily in vanilla JS without parsing,
                // we'll just use a mock Token ID for the backend insert based on transaction timestamp
                const newTokenId = Date.now().toString();

                statusDiv.textContent = 'Blockchain mint successful! Saving to Database...';

                let response;
                if (window.PHP_MODE) {
                    // Hybrid Monolith Mode: Post directly to dashboard.php
                    response = await fetch('dashboard.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            tokenId: newTokenId,
                            name,
                            category,
                            weight,
                            purity,
                            hallmark,
                            ipfsHash: mockIpfsUri
                        })
                    });
                } else {
                    // Fallback to old API if needed
                    response = await fetchWithAuth('http://localhost:8000/api/jewellery', {
                        method: 'POST',
                        body: JSON.stringify({
                            tokenId: newTokenId,
                            name, category, weight, purity, hallmark, ipfsHash: mockIpfsUri
                        })
                    });
                }

                if (response.ok) {
                    statusDiv.style.color = 'green';
                    statusDiv.textContent = `Successfully minted and saved! Internal Token ID: ${newTokenId}`;
                    mintForm.reset();
                } else {
                    const data = await response.json();
                    throw new Error(data.message || 'Failed to save to database');
                }

            } catch (error) {
                console.error(error);
                statusDiv.style.color = 'red';
                statusDiv.textContent = `Error: ${error.message}`;
            }
        });
    }
});
