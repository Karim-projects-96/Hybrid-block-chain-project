const { expect } = require("chai");
const hre = require("hardhat");

describe("JewelleryTracker", function () {
  let jewelleryTracker;
  let owner;
  let addr1;

  beforeEach(async function () {
    [owner, addr1] = await hre.ethers.getSigners();
    const JewelleryTracker = await hre.ethers.getContractFactory("JewelleryTracker");
    jewelleryTracker = await JewelleryTracker.deploy();
  });

  describe("Deployment", function () {
    it("Should set the right owner", async function () {
      expect(await jewelleryTracker.owner()).to.equal(owner.address);
    });
  });

  describe("Minting", function () {
    it("Should mint a new jewellery item", async function () {
      await jewelleryTracker.mintJewellery(
        owner.address,
        "ipfs://testURI",
        "Gold Ring",
        "Luxe Manufacturer",
        "HLM123"
      );

      const item = await jewelleryTracker.jewelleryItems(1);
      expect(item.name).to.equal("Gold Ring");
      expect(item.manufacturer).to.equal("Luxe Manufacturer");
      expect(item.hallmark).to.equal("HLM123");
      expect(item.isStolen).to.equal(false);
      
      const ownerOfToken = await jewelleryTracker.ownerOf(1);
      expect(ownerOfToken).to.equal(owner.address);
      
      const tokenURI = await jewelleryTracker.tokenURI(1);
      expect(tokenURI).to.equal("ipfs://testURI");
    });

    it("Should fail if non-owner tries to mint", async function () {
      await expect(
        jewelleryTracker.connect(addr1).mintJewellery(
          addr1.address,
          "ipfs://testURI2",
          "Silver Necklace",
          "Silver Co",
          "SLV456"
        )
      ).to.be.revertedWithCustomError(jewelleryTracker, "OwnableUnauthorizedAccount");
    });
  });

  describe("Status Updates", function () {
    it("Should allow owner to mark item as stolen", async function () {
      await jewelleryTracker.mintJewellery(
        owner.address,
        "ipfs://testURI",
        "Diamond Bracelet",
        "Diamond Corp",
        "DIA789"
      );

      await jewelleryTracker.markAsStolen(1, true);
      const item = await jewelleryTracker.jewelleryItems(1);
      expect(item.isStolen).to.equal(true);
    });

    it("Should allow approved user to mark item as stolen", async function () {
        await jewelleryTracker.mintJewellery(
          owner.address,
          "ipfs://testURI",
          "Diamond Bracelet",
          "Diamond Corp",
          "DIA789"
        );
        
        await jewelleryTracker.approve(addr1.address, 1);
        await jewelleryTracker.connect(addr1).markAsStolen(1, true);
        const item = await jewelleryTracker.jewelleryItems(1);
        expect(item.isStolen).to.equal(true);
    });

    it("Should fail if unapproved non-owner tries to mark item as stolen", async function () {
      await jewelleryTracker.mintJewellery(
        owner.address,
        "ipfs://testURI",
        "Diamond Bracelet",
        "Diamond Corp",
        "DIA789"
      );

      // We expect a revert with "Caller is not owner nor approved" because of our custom error message
      await expect(
        jewelleryTracker.connect(addr1).markAsStolen(1, true)
      ).to.be.revertedWith("Caller is not owner nor approved");
    });
  });
});
