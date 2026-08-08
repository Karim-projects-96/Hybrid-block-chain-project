// SPDX-License-Identifier: MIT
pragma solidity ^0.8.0;

contract JewelleryTracker {
    
    struct Jewellery {
        uint256 id;
        string productHash; // IPFS hash or metadata hash
        address manufacturer;
        address currentOwner;
        bool isStolen;
        uint256 timestamp;
    }
    
    mapping(uint256 => Jewellery) public items;
    uint256 public nextJewelleryId;
    
    event JewelleryMinted(uint256 id, string productHash, address manufacturer);
    event OwnershipTransferred(uint256 id, address from, address to);
    event StolenStatusChanged(uint256 id, bool status);
    
    function mintJewellery(string memory _productHash) public {
        uint256 newId = nextJewelleryId++;
        
        items[newId] = Jewellery({
            id: newId,
            productHash: _productHash,
            manufacturer: msg.sender,
            currentOwner: msg.sender,
            isStolen: false,
            timestamp: block.timestamp
        });
        
        emit JewelleryMinted(newId, _productHash, msg.sender);
    }
    
    function transferOwnership(uint256 _id, address _newOwner) public {
        require(_id < nextJewelleryId, "Jewellery does not exist");
        require(items[_id].currentOwner == msg.sender, "Only current owner can transfer");
        require(!items[_id].isStolen, "Cannot transfer stolen item");
        
        address previousOwner = items[_id].currentOwner;
        items[_id].currentOwner = _newOwner;
        
        emit OwnershipTransferred(_id, previousOwner, _newOwner);
    }
    
    function reportStolen(uint256 _id, bool _status) public {
        require(_id < nextJewelleryId, "Jewellery does not exist");
        require(items[_id].currentOwner == msg.sender, "Only current owner can report stolen");
        
        items[_id].isStolen = _status;
        emit StolenStatusChanged(_id, _status);
    }
    
    function getJewellery(uint256 _id) public view returns (
        uint256 id,
        string memory productHash,
        address manufacturer,
        address currentOwner,
        bool isStolen,
        uint256 timestamp
    ) {
        require(_id < nextJewelleryId, "Jewellery does not exist");
        Jewellery memory item = items[_id];
        return (item.id, item.productHash, item.manufacturer, item.currentOwner, item.isStolen, item.timestamp);
    }
}
