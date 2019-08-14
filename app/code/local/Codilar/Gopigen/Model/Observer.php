<?php
/**
 * Codilar Technologies Pvt. Ltd.
 * @category    Gopigen Shipping
 * @package     Codilar
 * @copyright   Copyright (c) 2016 Codilar. (http://www.codilar.com)
 * @purpose     Magento observer
 * @author      Codilar Team
 **/
class Codilar_Gopigen_Model_Observer {
    public function addMassAction($observer)
    {
        $block = $observer->getEvent()->getBlock();
        if(get_class($block) =='Mage_Adminhtml_Block_Widget_Grid_Massaction'
            && $block->getRequest()->getControllerName() == 'sales_order')
        {
            $block->addItem('gopigenlabel', array(
                'label' => 'Print GoPigen Label',
                'url' => Mage::helper("adminhtml")->getUrl('gopigen/track/massPrint'),
            ));
        }
    }
}