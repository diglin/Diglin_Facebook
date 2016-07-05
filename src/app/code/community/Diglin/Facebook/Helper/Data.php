<?php
/**
 * Diglin GmbH - Switzerland
 *
 * @author      Sylvain Rayé <support at diglin.com>
 * @category    Diglin
 * @package     Diglin_Facebook
 * @copyright   Copyright (c) 2011-2016 Diglin (http://www.diglin.com)
 */

/**
 * Class Diglin_Facebook_Helper_Data
 */
class Diglin_Facebook_Helper_Data extends Mage_Core_Helper_Abstract {

    const CFG_ENABLED = 'diglin_facebook/config/enabled';
    const CFG_PIXEL_ID = 'diglin_facebook/config/pixel_id';
    const CFG_PRODUCT_ID = 'diglin_facebook/config/product_id';

    protected $_supportedTags = [
        'AddToCart',
        'AddToWishlist',
        'ViewContent',
        'Purchase',
    ];

    /**
     * @return bool
     */
    public function isEnabled($store = null)
    {
        return Mage::getStoreConfigFlag(self::CFG_ENABLED, $store);
    }

    /**
     * @return mixed
     */
    public function getPixelId($store = null)
    {
        return Mage::getStoreConfig(self::CFG_PIXEL_ID, $store);
    }

    /**
     * @return string
     */
    public function getProductIdType($store = null)
    {
        return (string) Mage::getStoreConfig(self::CFG_PRODUCT_ID, $store);
    }

    /**
     * @return string
     */
    public function getProductId(Mage_Catalog_Model_Product $product)
    {
        return $product->getData($this->getProductIdType());
    }

    /**
     * @param $type
     * @param Mage_Catalog_Model_Product $product
     * @param bool $functionWrapper
     * @param bool $escape
     * @return mixed|string
     */
    public function getTag($type, Mage_Catalog_Model_Product $product, $functionWrapper = false, $escape = false)
    {
        if (!$this->isEnabled()) {
            return '';
        }

        if (!in_array($type, $this->_supportedTags)) {
            return '';
        }

        $category = null;
        if ($product->getCategory()) {
            $category = $this->escapeHtml($product->getCategory()->getName());
        }

        $productName = $this->escapeHtml($product->getName());
        $price = round($product->getPrice(), 2);

        $tag = <<<HTML
fbq('track', '{$type}', {content_name: '{$productName}', content_type: 'product', content_category: '{$category}', content_ids: ['{$this->getProductId($product)}'], value: {$price}, currency: '{$this->getCurrency()}'});
HTML;

        if ($functionWrapper) {
            $tag = $this->jsQuoteEscape($tag);
            $tag = "addTag('$tag');";
        }

        if ($escape) {
            $tag = $this->quoteEscape($tag);
        }

        return $tag;
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    public function getAddToCartTag(Mage_Catalog_Model_Product $product, $functionWrapper = false, $escape = false)
    {
        return $this->getTag('AddToCart', $product, $functionWrapper, $escape);
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    public function getAddToWishlistTag(Mage_Catalog_Model_Product $product, $functionWrapper = false, $escape = false)
    {
        return $this->getTag('AddToWishlist', $product, $functionWrapper, $escape);
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return Mage::app()->getStore()->getBaseCurrency()->getCode();
    }
}