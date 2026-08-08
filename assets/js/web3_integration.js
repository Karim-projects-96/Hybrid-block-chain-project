// Web3 Integration
let web3;
let userAccount = null;

// Replace with your compiled contract ABI and deployed address
const contractAddress = "0x0000000000000000000000000000000000000000"; // Dummy address
const contractABI = []; // Dummy ABI for now

async function initWeb3() {
    if (window.ethereum) {
        web3 = new Web3(window.ethereum);
        try {
            const connectBtn = document.getElementById('connectWalletBtn');
            
            // Check if already connected
            const accounts = await web3.eth.getAccounts();
            if(accounts.length > 0) {
                userAccount = accounts[0];
                updateWalletButton(userAccount);
            }

            connectBtn.addEventListener('click', async () => {
                try {
                    const accounts = await window.ethereum.request({ method: 'eth_requestAccounts' });
                    userAccount = accounts[0];
                    updateWalletButton(userAccount);
                    console.log("Connected:", userAccount);
                } catch (error) {
                    console.error("User denied account access");
                }
            });

            // Listen for account changes
            window.ethereum.on('accountsChanged', function (accounts) {
                userAccount = accounts[0];
                updateWalletButton(userAccount);
            });

        } catch (error) {
            console.error("Web3 Error:", error);
        }
    } else {
        console.warn('Non-Ethereum browser detected. You should consider trying MetaMask!');
        const connectBtn = document.getElementById('connectWalletBtn');
        if(connectBtn) {
            connectBtn.innerText = "Install MetaMask";
            connectBtn.onclick = () => window.open('https://metamask.io', '_blank');
        }
    }
}

function updateWalletButton(account) {
    const connectBtn = document.getElementById('connectWalletBtn');
    if (account && connectBtn) {
        connectBtn.innerText = account.substring(0, 6) + "..." + account.substring(account.length - 4);
        connectBtn.classList.remove('btn-gold');
        connectBtn.classList.add('btn-outline');
    } else if(connectBtn) {
        connectBtn.innerText = "Connect Wallet";
        connectBtn.classList.add('btn-gold');
        connectBtn.classList.remove('btn-outline');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    initWeb3();
});
