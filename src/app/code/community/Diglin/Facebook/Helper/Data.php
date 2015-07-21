<?php
/**
 * Diglin GmbH - Switzerland
 *
 * @author      Sylvain Rayé <support at diglin.com>
 * @category    Diglin
 * @package     Diglin_Facebook
 * @copyright   Copyright (c) 2011-2015 Diglin (http://www.diglin.com)
 */

/**
 * Class Diglin_Facebook_Helper_Data
 */
class Diglin_Facebook_Helper_Data extends Mage_Core_Helper_Abstract {

    const CFG_ENABLED = 'diglin_facebook/config/enabled';
    const CFG_PIXEL_ID = 'diglin_facebook/config/pixel_id';

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return Mage::getStoreConfigFlag(self::CFG_ENABLED);
    }

    /**
     * @return mixed
     */
    public function getPixelId()
    {
        return Mage::getStoreConfig(self::CFG_PIXEL_ID);
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    public function getAddToCartTag(Mage_Catalog_Model_Product $product, $functionWrapper = false)
    {
        if (!$this->isEnabled()) {
            return '';
        }

        $name = str_replace("'"," ", $product->getName());
        $price = round($product->getPrice(), 2);
        $tag = <<<HTML
fbq('track', 'AddToCart', {content_name: '{$name}', content_ids: ['{$product->getId()}'], content_type: 'product', value: {$price}, currency: '{$this->getCurrency()}'});
HTML;
        if ($functionWrapper) {
            $tag = $this->jsQuoteEscape($tag);
            $tag = "addTag('$tag');";
        }

        return $tag;
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    public function getAddToWishlistTag(Mage_Catalog_Model_Product $product, $functionWrapper = false)
    {
        if (!$this->isEnabled()) {
            return '';
        }

        $category = null;
        if ($product->getCategory()) {
            $category = $product->getCategory()->getName();
        }

        $name = str_replace("'"," ", $product->getName());
        $price = round($product->getPrice(), 2);

        $tag = <<<HTML
fbq('track', 'AddToCart', {content_name: '{$name}', content_category: '{$category}', content_ids: ['{$product->getId()}'], value: {$price}, currency: '{$this->getCurrency()}'});
HTML;

        if ($functionWrapper) {
            $tag = $this->jsQuoteEscape($tag);
            $tag = "addTag('$tag');";
        }

        return $tag;
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return Mage::app()->getStore()->getBaseCurrency()->getCode();
    }
}