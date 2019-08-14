<?php
/**
 * Codilar Technologies Pvt. Ltd.
 * @category    Gopigen Shipping
 * @package     Codilar
 * @copyright   Copyright (c) 2016 Codilar. (http://www.codilar.com)
 * @purpose     Magento admin controller for ajax
 * @author      Codilar Team
 **/

class Codilar_Gopigen_Adminhtml_GopigenController extends Mage_Adminhtml_Controller_Action {

    /*
     * Function to get AWB number from API
     */
    public function getAWBAction(){
        $msg = '';
        $post = $this->getRequest();

        if (empty($post)) {
            $msg = "PinCode is not serviceable by GoPigen.";
        }
        else
        {
            $zipcode = $post->getParam('zipcode');
            $orderid = $post->getParam('orderid');
            try{
                do{
                    $res = Mage::helper('codilar_gopigen')->placeOrder($orderid);
                    if(!$res){
                        $msg = "Some network issue, Please try again later";
                        break;
                    }elseif(!$res['orders_data'][0]['success']){
                        $msg = $res['orders_data'][0]['msg'];
                        break;
                    }elseif($res['orders_data'][0]['awb']){
                        $awb = $res['orders_data'][0]['awb'];
                        $data[0]['awb'] = $awb;
                    }
                }while(!$awb);
                if($awb){
                    Mage::getModel('codilar_gopigen/track')->saveTrack($res['orders_data'][0]);
                }
            }
            catch (Exception $e)
            {
                $msg = $e->getMessage();
            }
        }
        if($msg)
        {
            $data = array();
            $data[0]['awb'] = $msg;
            mage::log("AWB Found and sending via ajax with message ".$msg,null,'gopigen.log');
        }
        $output = array();
        $output['resp'] = $data;
        $json = json_encode($output);
        $this->getResponse()
            ->clearHeaders()
            ->setHeader('Content-Type', 'application/json')
            ->setBody($json);
    }
}