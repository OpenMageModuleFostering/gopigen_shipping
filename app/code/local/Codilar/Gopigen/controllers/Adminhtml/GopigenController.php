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

    protected function _isAllowed()
    {
        return true;
    }

    /*
     * Function to get AWB number from API
     */
    public function getAWBAction(){
        $msg = '';
        $post = $this->getRequest();
        $awb = NULL;
        // Notice: Undefined index: awb  in D:\xampp\htdocs\pigen\app\code\local\Codilar\Gopigen\controllers\Adminhtml\GopigenController.php on line 36
        if (empty($post)) {
            $msg = "PinCode is not serviceable by GoPigen.";
        }
        else
        {
            $zipcode = $post->getParam('zipcode');
            $orderid = $post->getParam('orderid');
            $i = 0;
            try{
                do{
                    $res = Mage::helper('codilar_gopigen')->placeOrder($orderid);
                    if(!$res){
                        $msg = "Some network issue, Please try again later";
                        break;
                    }elseif(! array_key_exists('success',$res['orders_data'][0]) ){
                        $msg = $res['orders_data'][0]['msg'];
                        break;
                    }elseif(array_key_exists('awb',$res['orders_data'][0])){
                        $awb = $res['orders_data'][0]['awb'];
                        $data[0]['awb'] = $awb;
                    }elseif(array_key_exists('msg',$res['orders_data'][0])){
                       if($res['orders_data'][0]['msg'] != 'All is Well'){
                           $awb = $res['orders_data'][0]['msg'];
                           $data[0]['awb'] = $awb;
                       }
                    }
                    $i++;
                    if($i>2){
                      $msg = "Some network issue, Please try again later";
                      break;
                    }
                }while(!$awb);
                if(!preg_match_all("/(error|\s)/i", $awb)){
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
            Mage::log("Response message ".$msg,null,'gopigen.log');
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