<?php

/**
 * Diglin GmbH - Switzerland
 *
 * @author      Sylvain Rayé <support at diglin.com>
 * @category    Diglin
 * @package     Diglin_Facebook
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

        return implode("\n", $this->_tags);
    }

    /**
     * @param $handle
     */
    public function getViewContentTag($handle)
    {
        if (strpos('catalog_product_view', $handle) !== false) {
            /* @var $this Mage_Catalog_Model_Product */
            $product = Mage::registry('product');

            $this->_tags[] = $this->getFBHelper()->getTag('ViewContent', $product);

//            $category = null;
//            if ($product->getCategory()) {
//                $category = Mage::helper('core')->quoteEscape($product->getCategory()->getName());
//            }
//
//            $productName = Mage::helper('core')->quoteEscape($product->getName());
//
//            $this->_tags[] = "fbq('track', 'ViewContent', {"
//                . "content_name : '{$productName}', "
//                . "content_category : '{$category}', "
//                . "content_type : 'product', "
//                . "value : " . round($product->getPrice(), 2) . ", "
//                . "currency : '{$this->getCurrency()}', "
//                . "content_ids : ['{$this->getFBHelper()->getProductId($product)}']"
//                . "});";
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

            if (!$order->getId()) {
                return;
            }

            /* @var $item Mage_Sales_Model_Order_Item */
            foreach ($order->getAllItems() as $item) {
                $productId = ($this->getFBHelper()->getProductIdType($order->getStore()) == 'sku') ? $item->getSku() : $item->getProductId();

                $this->_tags[] = "fbq('track', 'Purchase', { "
                    . "value: '". round($item->getRowTotalInclTax(), 2) ."', "
                    . "currency: '{$order->getOrderCurrencyCode()}', "
                    . "content_name : '{$item->getName()}', "
                    . "content_type : 'product', "
                    . "content_ids : ['{$productId}']});";
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

    /**
     * @return Diglin_Facebook_Helper_Data
     */
    public function getFBHelper()
    {
        return Mage::helper('diglin_facebook');
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->getFBHelper()->getCurrency();
    }
}