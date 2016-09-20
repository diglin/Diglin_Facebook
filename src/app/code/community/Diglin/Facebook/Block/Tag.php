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
 * Class Diglin_Facebook_Block_Tag
 */
class Diglin_Facebook_Block_Tag extends Mage_Core_Block_Template
{
    /**
     * @var array
     */
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
        $this->getViewContentTag();
        $this->getSearchTag();

        $handles = $this->getLayout()->getUpdate()->getHandles();
        foreach ($handles as $handle) {
            $this->getInitiateCheckoutTag($handle);
            $this->getPurchaseTag($handle);
            $this->getCompleteRegistrationTag($handle);
        }

        Mage::dispatchEvent('diglin_facebook_get_tags', array('tags' => $this->_tags));

        return implode("\n", $this->_tags);
    }

    /**
     * @return $this
     */
    public function getViewContentTag()
    {
        /* @var $product Mage_Catalog_Model_Product */
        $product = Mage::registry('product');
        if ($product && $product->getId()) {
            $this->_tags[] = $this->getFBHelper()->getTag('ViewContent', $product);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function getSearchTag()
    {
        $query = Mage::helper('catalogsearch')->getQuery();
        if ($query->getQueryText() != '') {
            /* @var $query Mage_CatalogSearch_Model_Query */
            $query->setStoreId(Mage::app()->getStore()->getId());

            $this->_tags[] = "fbq('track', 'Search', {value : '" . $query->getQueryText() . "'});";
        }
        return $this;
    }

    /**
     * @param $handle
     * @return $this
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

        return $this;
    }

    /**
     * @param $handle
     * @return $this
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
                $name = Mage::helper('diglin_facebook')->escapeHtml($item->getName());

                $this->_tags[] = "fbq('track', 'Purchase', { "
                    . "value: '". round($item->getRowTotalInclTax(), 2) ."', "
                    . "currency: '{$order->getOrderCurrencyCode()}', "
                    . "content_name : '{$name}', "
                    . "content_type : 'product', "
                    . "content_ids : ['{$productId}']});";
            }
        }
        return $this;
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