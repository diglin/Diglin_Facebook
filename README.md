Magento Extension to add Custom Tag for Facebook tracking

Facebook Tag API Documentation [https://developers.facebook.com/docs/ads-for-websites/tag-api/]

# Documentation

Normally everything should work fine however regarding the tracking of wishlist and "add to cart" you have to adapt your template. For that, use the following helper methods :

- `Mage::helper()->getAddToCartTag(Mage_Catalog_Model_Product, false);` 
- `Mage::helper()->getAddToWishlistTag(Mage_Catalog_Model_Product, false);` 