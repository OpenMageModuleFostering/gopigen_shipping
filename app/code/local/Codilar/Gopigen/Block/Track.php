<?php
/**
 * Codilar Technologies Pvt. Ltd.
 * @category    Gopigen Shipping
 * @package     Codilar
 * @copyright   Copyright (c) 2016 Codilar. (http://www.codilar.com)
 * @purpose     Magento admin block
 * @author      Codilar Team
 **/
class Codilar_Gopigen_Block_Track extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct()
    {
        $this->_blockGroup      = 'codilar_gopigen';
        $this->_controller      = 'track';
        $this->_headerText      = $this->__('GoPigen Shipping Details');
        $this->_addButtonLabel  = $this->__('Refresh Tracking Information');
        parent::__construct();
    }

    public function getCreateUrl()
    {
        return $this->getUrl('*/*/refresh');
    }

}

