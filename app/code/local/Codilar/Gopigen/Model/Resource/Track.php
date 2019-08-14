<?php
/**
 * Codilar Technologies Pvt. Ltd.
 * @category    Gopigen Shipping
 * @package     Codilar
 * @copyright   Copyright (c) 2016 Codilar. (http://www.codilar.com)
 * @purpose     Magento model
 * @author      Codilar Team
 **/
class Codilar_Gopigen_Model_Resource_Track extends Mage_Core_Model_Resource_Db_Abstract
{

    protected function _construct()
    {
        $this->_init('codilar_gopigen/codilar_gopigen', 'id');
    }

}