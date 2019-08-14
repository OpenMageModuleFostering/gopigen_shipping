<?php
/**
 * Codilar Technologies Pvt. Ltd.
 * @category    Gopigen
 * @package     Codilar_Gopigen
 * @copyright   Copyright (c) 2016 Codilar. (http://www.codilar.com)
 * @purpose     Model class for shipment plugin and tracking
 * @User        Arshad M <arshad@codilar.com
 * @Date        1/29/2016
 * @Time        4:54 PM
 */

class Codilar_Gopigen_Model_Carrier
    extends Mage_Shipping_Model_Carrier_Abstract
    implements Mage_Shipping_Model_Carrier_Interface
{
    /**
     * Carrier's code, as defined in parent class
     *
     * @var string
     */
    protected $_code = 'codilar_gopigen';
    protected $_goweight = 0;
    /**
     * Returns available shipping rates for Gopigen Shipping carrier
     *
     * @param Mage_Shipping_Model_Rate_Request $request
     * @return Mage_Shipping_Model_Rate_Result
     */
    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        if (!Mage::getStoreConfig('carriers/' . $this->_code . '/active')) {
            return false;
        }
        /** @var Mage_Shipping_Model_Rate_Result $result */
        $result = Mage::getModel('shipping/rate_result');
        foreach ($request->getAllItems() as $item) {
            $this->_goweight += $item->getWeight();
        }
        $ship_request = array(
            "to_pin_code" => $request->getDestPostcode(),
            "payment_type" =>  "cod",
            "weight" => "$this->_goweight",
            "cod_amount" => $request->getOrderSubtotal(),
            "from_pin_code" => Mage::getStoreConfig('shipping/origin/postcode'),
            "from_city" => Mage::getStoreConfig('shipping/origin/city')
        );
        $serviceAvailable = $this->_checkAvailability($ship_request);
        if ($serviceAvailable)
            $result->append($this->_getRate($serviceAvailable));
        else
            return false;
        return $result;
    }
    /**
     * Returns Allowed shipping methods
     *
     * @return array
     */
    public function getAllowedMethods()
    {
        return array(
            'standard'    =>  'Standard delivery'
        );
    }
    /**
     * Set if tracking is available with the shipping method
     *
     * @return true/false
     */
    public function isTrackingAvailable() {
        return true;
    }
    /**
     * To return object with tracking URL
     *
     * @return Object
     */
    public function getTrackingInfo($tracking)
    {
        $track = Mage::getModel('shipping/tracking_result_status');
        $_baseUrl = Mage::getUrl('gopigen/track/index/');
        $track->setUrl($_baseUrl . 'id/' . $tracking)
            ->setTracking($tracking)
            ->setCarrierTitle($this->getConfigData('name'));
        return $track;
    }
    /**
     * Get Standard rate object
     *
     * @return Mage_Shipping_Model_Rate_Result_Method
     */
//    protected function _getStandardRate()
//    {
//        /** @var Mage_Shipping_Model_Rate_Result_Method $rate */
//        $rate = Mage::getModel('shipping/rate_result_method');
//        $rate->setCarrier($this->_code);
//        $rate->setCarrierTitle($this->getConfigData('title'));
//        $rate->setMethod('large');
//        $rate->setMethodTitle('Standard delivery');
//        $rate->setPrice(1.23);
//        $rate->setCost(0);
//        return $rate;
//    }
    /**
     * To Check service availability
     */
    protected function _checkAvailability($request){
        $result = Mage::helper('codilar_gopigen')->checkServiceAvailability($request);
        Mage::log($result,null,'gopigen.log');
        if(isset($result['serviceable_data'][0]['serviceable']) && $result['serviceable_data'][0]['serviceable']=="1")
            return $result['serviceable_data'][0];
        return null;
    }
    protected function _getRate($rate)
    {
        $_price = isset($rate['rate'])?$rate['rate']:0;
        $_title  = isset($rate['probable_partner'])?$rate['probable_partner']:"";
        $rate = Mage::getModel('shipping/rate_result_method');
        $rate->setCarrier($this->_code);
        $rate->setCarrierTitle($this->getConfigData('title'));
        $rate->setMethod('large');
        $rate->setMethodTitle($_title);
        $rate->setPrice($_price);
        $rate->setCost(0);
        return $rate;
    }
}