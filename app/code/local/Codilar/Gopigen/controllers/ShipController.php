<?php
/**
 * Codilar Technologies Pvt. Ltd.
 * @category    Gopigen Shipping
 * @package     Codilar
 * @copyright   Copyright (c) 2016 Codilar. (http://www.codilar.com)
 * @purpose     Magento controller for tracking
 * @author      Codilar Team
 **/
class Codilar_Gopigen_ShipController extends Mage_Core_Controller_Front_Action {
    public function trackAction(){
        $_id = $this->getRequest()->getParam('id');
        if(!$_id)
           $this->_redirect('');
        $res = Mage::helper('codilar_gopigen')->trackShipments($_id);
        Mage::getModel('core/session')->setData('gopigen',$res);
        $this->loadLayout();
        $this->renderLayout();
    }
}