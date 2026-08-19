const hre = require("hardhat");

async function main() {
  const JewelleryTracker = await hre.ethers.getContractFactory("JewelleryTracker");
  const tracker = await JewelleryTracker.deploy();

  await tracker.waitForDeployment();

  console.log("JewelleryTracker deployed to:", await tracker.getAddress());
}

main().catch((error) => {
  console.error(error);
  process.exitCode = 1;
});
