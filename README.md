# Hybrid Blockchain Jewellery Management System

This is a full-stack Hybrid Blockchain Management System for tracking luxury jewellery.

## Features
- **Smart Contracts:** Solidity ERC-721 implementation for tracking asset provenance.
- **Backend:** Node.js, Express, and MongoDB for off-chain metadata storage and role management.
- **Frontend:** Vanilla HTML/CSS/JS with Web3/MetaMask integration.

## Prerequisites
- Node.js
- MongoDB
- MetaMask Extension
- Hardhat (for local blockchain)

## Setup Instructions

1. **Install Dependencies**
   ```bash
   npm install
   ```

2. **Configure Environment**
   Create a `.env` file in the root directory:
   ```env
   PORT=5000
   MONGO_URI=mongodb://localhost:27017/jewellery_db
   JWT_SECRET=your_super_secret_jwt_key
   ```

3. **Deploy Smart Contract (Local Development)**
   ```bash
   npx hardhat node
   # In a new terminal:
   npx hardhat run scripts/deploy.js --network localhost
   ```
   *Note down the deployed contract address and update it in `frontend/public/js/web3App.js`.*

4. **Start the Backend Server**
   ```bash
   npm run dev
   ```

5. **Run the Frontend**
   Serve the `frontend/public` directory using any static file server (e.g., Live Server extension or Python HTTP server).
   ```bash
   npx serve frontend/public
   ```
