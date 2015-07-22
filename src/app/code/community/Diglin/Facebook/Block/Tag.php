<?php

/**
 * Diglin GmbH - Switzerland
 *
 * @author      Sylvain Rayé <support at diglin.com>
 * @category    Diglin
 * @package     Diglin_
 * @copyright   Copyright (c) 2011-2015 Diglin (http://www.diglin.com)
 */
class Diglin_Facebook_Block_Tag extends Mage_Core_Block_Template
{
    protected $_tags = array();

    /**
     * @return mixed
     */
    public function getPixelId()
    {
        return Mage::helper('diglin_facebook')->getPixelId();
    }

    /**
     * @return string
     */
    public function getTags()
    {
        $handles = $this->getLayout()->getUpdate()->getHandles();
        foreach ($handles as $handle) {
            $this->getViewContentTag($handle);
            $this->getSearchTag($handle);
            $this->getInitiateCheckoutTag($handle);
            $this->getPurchaseTag($handle);
            $this->getCompleteRegistrationTag($handle);
        }

        Mage::dispatchEvent('diglin_facebook_get_tags', array('tags' => $this->_tags));

        $return = implode("\n", $this->_tags);

        Mage::log($return);

        return $return;
    }

    /**
     * @param $handle
     */
    public function getViewContentTag($handle)
    {
        if (strpos('catalog_product_view', $handle) !== false) {
            /* @var $this Mage_Catalog_Model_Product */
            $product = Mage::registry('product');

            $category = null;
            if ($product->getCategory()) {
                $category = Mage::helper('core')->quoteEscape($product->getCategory()->getName());
            }

            $productName = Mage::helper('core')->quoteEscape($product->getName());

            $this->_tags[] = "fbq('track', 'ViewContent', {"
                . "content_name : '{$productName}',"
                . "content_category : '{$category}',"
                . "value : " . round($product->getPrice(), 2) . ","
                . "currency : {$this->getCurrency()},"
                . "content_ids : ['{$product->getId()}']"
                . "});";
        }
    }

    /**
     * @param $handle
     */
    public function getSearchTag($handle)
    {
        if (strpos('catalogsearch_result_index', $handle) !== false) {
            $query = Mage::helper('catalogsearch')->getQuery();
            /* @var $query Mage_CatalogSearch_Model_Query */
            $query->setStoreId(Mage::app()->getStore()->getId());

            $this->_tags[] = "fbq('track', 'Search', {value : '" . $query->getQueryText() . "'});";
        }
    }

    /**
     * @param $handle
     */
    public function getInitiateCheckoutTag($handle)
    {
        if ( 'checkout_onepage_index' == $handle || 'checkout_multishipping_index' == $handle ) {

            if ('checkout_onepage_index' == $handle) {
                $quote = Mage::getSingleton('checkout/type_onepage')->getQuote();
            } else {
                $quote = Mage::getSingleton('checkout/type_multishipping');
            }

            $numItems = count($quote->getAllItems());
            $this->_tags[] = "fbq('track', 'InitiateCheckout', {num_items : '{$numItems}'});";
        }
    }

    /**
     * @param $handle
     */
    public function getPurchaseTag($handle)
    {
        if ( 'checkout_onepage_success' == $handle || 'checkout_multishipping_success' == $handle ) {

            if ('checkout_onepage_success' == $handle) {
                /* @var $session Mage_Checkout_Model_Session */
                $session = Mage::getSingleton('checkout/type_onepage')->getCheckout();
            } else {
                $session = Mage::getSingleton('checkout/type_multishipping')->getCheckoutSession();
            }
            $order = Mage::getModel('sales/order')->load($session->getLastOrderId());

            if ($order->getId()) {
                return;
            }

            foreach ($order->getAllItems() as $item) {
                $this->_tags[] = "fbq('track', 'Purchase', {value: '{$item->getRowTotalInclTax()}', currency: '{$order->getOrderCurrencyCode()}', content_name : '{$item->getName()}', content_type : 'product', content_ids : ['{$item->getProductId()}']});";
            }
        }
    }

    /**
     * @param $handle
     */
    public function getCompleteRegistrationTag($handle)
    {
        if (strpos('checkout_onepage_success', $handle) !== false) {
            $this->_tags[] = "fbq('track', 'CompleteRegistration', {status : 'order_completed'});";
        }
    }

    public function getCurrency()
    {
        return Mage::helper('diglin_facebook')->getCurrency();
    }
}