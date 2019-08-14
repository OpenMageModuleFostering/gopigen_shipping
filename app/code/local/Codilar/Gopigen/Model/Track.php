<?php
/**
 * Codilar Technologies Pvt. Ltd.
 * @category    Gopigen Shipping
 * @package     Codilar
 * @copyright   Copyright (c) 2016 Codilar. (http://www.codilar.com)
 * @purpose     Magento Model class
 * @author      Codilar Team
 **/
class Codilar_Gopigen_Model_Track extends Mage_Core_Model_Abstract
{

    protected function _construct()
    {
        $this->_init('codilar_gopigen/track');
    }
    /**
     *
     * Array
    (
    [orders_data] => Array
    (
    [0] => Array
    (
    [success] => 1
    [shipping_label] => https://s3-ap-southeast-1.amazonaws.com/pigen-shipping-labels/indiapost_surat/2016/03/09/TESTEX655706205IN_indiapost_surat.pdf
    [msg] => order with unique_id already processed
    [partner] => indiapost_surat
    [awb] => TESTEX655706205IN
    [unique_id] => 100000005
    )

    )

    )
     */
    public function saveTrack($data){
        $awb = $data['awb'];
        $order_id = $data['unique_id'];
        $label = $data['shipping_label'];
        $model = Mage::getModel('codilar_gopigen/track')->load($awb,'awb');
        if(!$model->getAwb()){
            $model = Mage::getModel('codilar_gopigen/track');
            $model->setAwb($awb);
            $model->setCreatedTime(now());
        }
        $model->setOrderId($order_id);
        $model->setLabel($label);
        $model->setStatus('New');
        $model->setTime(now());
        $model->setDesc('Submitted for shipping');
        $model->setUpdateTime(now());
        $model->save();
    }

}