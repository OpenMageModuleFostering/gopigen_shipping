<?php
/**
 * Created by PhpStorm.
 * User: arsha
 * Date: 6/2/2016
 * Time: 1:21 PM
 */ 
class Codilar_Gopigen_Block_Adminhtml_Sales_Order_Grid extends Mage_Adminhtml_Block_Sales_Order_Grid {

    protected function _prepareColumns()
    {
        $this->addColumnAfter('skus', array(
            'header' => Mage::helper('sales')->__('SKU'),
            'index' => 'skus',
            'width' => '100px',
            'renderer' => 'Codilar_Gopigen_Block_Adminhtml_Sales_Order_Skus',
        ),'shipping_name');
        $this->addColumnAfter('vendor_skus', array(
            'header' => Mage::helper('sales')->__('Vendor SKU'),
            'index' => 'vendor_skus',
            'width' => '100px',
            'renderer' => 'Codilar_Gopigen_Block_Adminhtml_Sales_Order_Vendorskus',
        ),'skus');
        return parent::_prepareColumns();
    }

}