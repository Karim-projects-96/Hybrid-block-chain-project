// SPDX-License-Identifier: MIT
pragma solidity ^0.8.0;

import "@openzeppelin/contracts/token/ERC721/extensions/ERC721URIStorage.sol";
import "@openzeppelin/contracts/access/Ownable.sol";

contract JewelleryTracker is ERC721URIStorage, Ownable {
    uint256 private _nextTokenId;

    struct JewelleryItem {
        uint256 id;
        string name;
        string manufacturer;
        string hallmark;
        uint256 timestamp;
        bool isStolen;
    }

    mapping(uint256 => JewelleryItem) public jewelleryItems;

    event JewelleryMinted(uint256 indexed tokenId, string name, string manufacturer);
    event StatusUpdated(uint256 indexed tokenId, bool isStolen);

    constructor() ERC721("JewelleryTracker", "JWTK") Ownable(msg.sender) {}

    function mintJewellery(
        address to,
        string memory uri,
        string memory name,
        string memory manufacturer,
        string memory hallmark
    ) public onlyOwner returns (uint256) {
        _nextTokenId++;
        uint256 newItemId = _nextTokenId;

        _mint(to, newItemId);
        _setTokenURI(newItemId, uri);

        jewelleryItems[newItemId] = JewelleryItem({
            id: newItemId,
            name: name,
            manufacturer: manufacturer,
            hallmark: hallmark,
            timestamp: block.timestamp,
            isStolen: false
        });

        emit JewelleryMinted(newItemId, name, manufacturer);
        return newItemId;
    }

    function markAsStolen(uint256 tokenId, bool stolenStatus) public {
        require(ownerOf(tokenId) == _msgSender() || getApproved(tokenId) == _msgSender() || owner() == _msgSender(), "Caller is not owner nor approved");
        jewelleryItems[tokenId].isStolen = stolenStatus;
        emit StatusUpdated(tokenId, stolenStatus);
    }
}
