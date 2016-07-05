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
 * Class Diglin_Facebook_Model_Config_Source_Id
 */
class Diglin_Facebook_Model_Config_Source_Id
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'entity_id', 'label' => Mage::helper('adminhtml')->__('Entity ID')),
            array('value' => 'sku', 'label' => Mage::helper('adminhtml')->__('SKU')),
        );
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            'entity_id' => Mage::helper('adminhtml')->__('Entity ID'),
            'sku' => Mage::helper('adminhtml')->__('SKU'),
        );
    }

}
