<?php
/**
 * Codilar Technologies Pvt. Ltd.
 * @category    Gopigen Shipping
 * @package     Codilar
 * @copyright   Copyright (c) 2016 Codilar. (http://www.codilar.com)
 * @purpose     For cron job
 * @author      Codilar Team
 **/
class Codilar_Gopigen_Model_Cron {
    public function refresh(){
        $collection = Mage::getModel('codilar_gopigen/track')->getCollection()
            ->addFieldToFilter('status',array('neq' => 'Delivered'))
            ;
        foreach($collection as $col){
            $awb = $col->getAwb();
            $res = Mage::helper('codilar_gopigen')->trackShipments($awb);
            $model = Mage::getModel('codilar_gopigen/track')->load($col->getId());
            $s = $res['results'][0]['status']?$res['results'][0]['status']:$model->getStatus();
            $t = $res['results'][0]['dt']?$res['results'][0]['dt']:$model->getTime();
            $d = $res['results'][0]['desc']?$res['results'][0]['desc']:$model->getDesc();
            $model->setStatus($s);
            $model->setTime($t);
            $model->setDesc($d);
            $model->setUpdateTime(now());
            $model->save();
        }
    }
}