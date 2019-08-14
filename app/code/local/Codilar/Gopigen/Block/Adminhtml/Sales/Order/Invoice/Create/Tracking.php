<?php
/**
 * Codilar Technologies Pvt. Ltd.
 * @category    Gopigen Shipping
 * @package     Codilar
 * @copyright   Copyright (c) 2016 Codilar. (http://www.codilar.com)
 * @purpose     For shipment stracking
 * @author      Codilar Team
 **/
class Codilar_Gopigen_Block_Adminhtml_Sales_Order_Invoice_Create_Tracking extends Mage_Adminhtml_Block_Sales_Order_Invoice_Create_Tracking {
    public function _construct()
    {
        $this->setTemplate('codilar/gopigen/invoice/tracking.phtml');
    }
}