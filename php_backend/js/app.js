const API_URL = 'http://localhost:8000/api';

// Utility for fetching with Auth token
async function fetchWithAuth(url, options = {}) {
    const token = localStorage.getItem('token');
    const headers = {
        'Content-Type': 'application/json',
        ...options.headers,
    };
    if (token) {
        headers['Authorization'] = `Bearer ${token}`;
    }
    const response = await fetch(url, { ...options, headers });
    return response;
}

// Navigation UI Update based on Auth State
function updateNav() {
    const token = localStorage.getItem('token');
    const userRole = localStorage.getItem('role');
    const authLinks = document.querySelectorAll('.nav-auth');
    const logoutBtns = document.querySelectorAll('#logoutBtnNav, #logoutBtn');

    if (token) {
        // Logged in
        authLinks.forEach(link => {
            if(link.getAttribute('href') === 'login.html' || link.getAttribute('href') === 'register.html') {
                link.style.display = 'none';
            }
            if(link.getAttribute('href') === 'dashboard.html') {
                if (userRole === 'Manufacturer' || userRole === 'Super Admin') {
                    link.style.display = 'inline-block';
                }
            }
        });
        logoutBtns.forEach(btn => btn.style.display = 'inline-block');
    } else {
        // Logged out
        authLinks.forEach(link => {
            if(link.getAttribute('href') === 'login.html' || link.getAttribute('href') === 'register.html') {
                link.style.display = 'inline-block';
            }
            if(link.getAttribute('href') === 'dashboard.html') {
                link.style.display = 'none';
            }
        });
        logoutBtns.forEach(btn => btn.style.display = 'none');
    }

    logoutBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            localStorage.removeItem('token');
            localStorage.removeItem('role');
            window.location.href = 'index.html';
        });
    });
}

document.addEventListener('DOMContentLoaded', () => {
    updateNav();

    // Verify Page Logic
    const verifyBtn = document.getElementById('verifyBtn');
    const tokenIdInput = document.getElementById('tokenIdInput');
    const resultContainer = document.getElementById('resultContainer');

    if (verifyBtn && tokenIdInput && resultContainer) {
        verifyBtn.addEventListener('click', async () => {
            const tokenId = tokenIdInput.value.trim();
            if (!tokenId) {
                alert('Please enter a Token ID');
                return;
            }

            try {
                const response = await fetch(`${API_URL}/jewellery/${tokenId}`);
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
    }

    // Login Page Logic
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const errorDiv = document.getElementById('loginError');
            errorDiv.style.display = 'none';

            try {
                const response = await fetch(`${API_URL}/users/login`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email, password })
                });
                const data = await response.json();

                if (response.ok) {
                    localStorage.setItem('token', data.token);
                    localStorage.setItem('role', data.role);
                    window.location.href = data.role === 'Manufacturer' ? 'dashboard.html' : 'index.html';
                } else {
                    errorDiv.textContent = data.message;
                    errorDiv.style.display = 'block';
                }
            } catch (err) {
                errorDiv.textContent = "Server error. Try again.";
                errorDiv.style.display = 'block';
            }
        });
    }

    // Register Page Logic
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const name = document.getElementById('regName').value;
            const email = document.getElementById('regEmail').value;
            const password = document.getElementById('regPassword').value;
            const role = document.getElementById('regRole').value;
            const errorDiv = document.getElementById('regError');
            errorDiv.style.display = 'none';

            try {
                const response = await fetch(`${API_URL}/users/register`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ name, email, password, role })
                });
                const data = await response.json();

                if (response.ok) {
                    localStorage.setItem('token', data.token);
                    localStorage.setItem('role', data.role);
                    window.location.href = data.role === 'Manufacturer' ? 'dashboard.html' : 'index.html';
                } else {
                    errorDiv.textContent = data.message;
                    errorDiv.style.display = 'block';
                }
            } catch (err) {
                errorDiv.textContent = "Server error. Try again.";
                errorDiv.style.display = 'block';
            }
        });
    }

    // Dashboard Mint Logic is combined with web3App.js in the next chunk
});
