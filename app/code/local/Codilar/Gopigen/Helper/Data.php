<?php
/**
 * Codilar Technologies Pvt. Ltd.
 * @category    Gopigen Shipping
 * @package     Codilar
 * @copyright   Copyright (c) 2016 Codilar. (http://www.codilar.com)
 * @purpose     Magento helper class
 * @author      Codilar Team
 **/
class Codilar_Gopigen_Helper_Data extends Mage_Core_Helper_Abstract {
	const API_URL = "http://gopigeon.biz/ecom-api/";
	const API_TEST_URL = "http://test.gopigeon.biz/ecom-api/";
	const XML_PATH = 'carriers/codilar_gopigen';
	const XML_PATH_ENABLED = 'carriers/codilar_gopigen/active';
	const XML_PATH_TEST = 'carriers/codilar_gopigen/test';
	const XML_PATH_MARKET_NAME = 'carriers/codilar_gopigen/marketname';
	const XML_PATH_VENDOR_NAME = 'carriers/codilar_gopigen/vendorname';
	const XML_PATH_PASSWORD = 'carriers/codilar_gopigen/password';
	protected function _getStoreConfig($xmlPath)
	{
		return Mage::getStoreConfig($xmlPath, Mage::app()->getStore()->getId());
	}
	protected function _isEnabled()
	{
		return $this->_getStoreConfig(self::XML_PATH_ENABLED);
	}
	protected function _isTestMode()
	{
		return $this->_getStoreConfig(self::XML_PATH_TEST);
	}
	protected function getPassword()
	{
		return $this->_getStoreConfig(self::XML_PATH_PASSWORD);
	}
	public function getApiUrl()
	{
		if($this->_isTestMode())
			return self::API_TEST_URL;
		else
			return self::API_URL;
	}
	protected function secureData($data = array()) {
		$marketName= $this->_getStoreConfig(self::XML_PATH_MARKET_NAME);
		$vendorName= $this->_getStoreConfig(self::XML_PATH_VENDOR_NAME);
		$password = $this->getPassword();
		if(!$marketName || !$vendorName || !$password)
			$this->log("MarketName or VendorName or Password is not set by admin");
		$data['market_name'] = $marketName;
		$data['vendor_name'] = $vendorName;
		$data['password'] = $password;
		$data['api_version'] = '0.1';
		return $data;
	}
	public function file_get_contents_curl($url)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //Set curl to return the data instead of printing it to the browser.
		$res = curl_exec($ch);
		curl_close($ch);
		return $res;
	}
	public function postCall($url,$data)
	{
		Mage::log($this->getApiUrl().$url,null,'gopigen.log');
		$ch = curl_init($this->getApiUrl().$url);
		$data_string = json_encode($this->secureData($data));
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
						'Content-Type: application/json',
						'Content-Length: ' . strlen($data_string))
		);
		$responses = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		$result=json_decode($responses,true);
		return $result;
	}
	public function checkServiceAvailability($request = array())
	{
		$uri = "check-serviceability";
		$data = array();
		$data["shipment_details"][0] = $request;
		Mage::log($uri,null,'gopigen.log');
		Mage::log($data,null,'gopigen.log');
		return $this->postCall($uri,$data);
		/*
		 * "serviceable_data": [
		 * {
		 * "probable_partner": "fedex",
		 * "success": true,
		 * "to_pin_code": "560034",
		 * "rate": 68,
		 * "serviceable": true,
		 * "msg": "request processed",
		 * "payment_type": "cod"
		 * }
		 * ]
		 */
	}

	public function placeOrder($orderid = NULL)
	{
		if(!$orderid){
			$this->log('Order ID not found');
			return false;
		}
		$order = Mage::getModel('sales/order')->load($orderid);
		$payment_method_code = $order->getPayment()->getMethodInstance()->getCode();
		$length = "10";
		$breadth = "10";
		$height = "10";
		if($payment_method_code == 'cashondelivery'){
			$paymentType = 'cod';
			$codAmount = $order->getSubtotal();
		}
		else{
			$paymentType = 'prepaid';
			$codAmount = '0';
		}
		$goWeight = 0;
		foreach ($order->getAllItems() as $item) {
			$goWeight += $item->getWeight();
		}
		$uri = "place-order";
		$data = array();
		$data["shipment_details"][0] = array(
				"unique_id" => $order->getIncrementId(),
				"invoice" => $order->getIncrementId(),
				"product_detail" => "Default Products",
				"from_name" => $this->_getStoreConfig('general/store_information/name'),
				"from_address" =>  $this->_getStoreConfig('general/store_information/address'),
				"from_city" =>  $this->_getStoreConfig('shipping/origin/city'),
				"from_state" =>  $this->_getStoreConfig('shipping/origin/region_id'),
				"from_email" =>  $this->_getStoreConfig('trans_email/ident_sales/email'),
				"from_mobile_number" =>  $this->_getStoreConfig('general/store_information/phone'),
				"from_pin_code" =>  $this->_getStoreConfig('shipping/origin/postcode'),
				"to_name" => $order->getShippingAddress()->getName(),
				"to_address" => $order->getShippingAddress()->getStreetFull(),
				"to_city" => $order->getShippingAddress()->getCity(),
				"to_state" => $order->getShippingAddress()->getRegion(),
				"to_mobile_number" => $order->getShippingAddress()->getTelephone(),
				"to_pin_code" => $order->getShippingAddress()->getPostcode(),
				"payment_type" => $paymentType,
				"weight" => $goWeight,
				"cod_collection" => $codAmount,
				"declared_value" => $codAmount,
				"declaration" => "I hereby declare that the above mentioned information is true and correct and value declared(value) is for transportation purpose and has no commercial value.Signature:",
				"length" => $length,
				"breadth" => $breadth,
				"height" => $height,
				"to_email" => $order->getShippingAddress()->getEmail()
		);
		return $this->postCall($uri,$data);
		/* for existing order */
		/*
		 * {
		 * "orders_data": [
		 * {
		 * "success": true,
		 * "shipping_label":
		 * "https://s3apsoutheast1.amazonaws.com/pigenshippinglabels/gojavas/2016/01/19/UNIPINC124611TEST_gojavas.pdf",
		 * "msg": "order with unique_id already processed",
		 * "partner": "gojavas",
		 * "awb": "UNIPINC124611TEST",
		 * "unique_id": "R47433070311"
		 * }
		 * ]
		 * }
		 */

		/* for new order */
		/*
		 * {
		 * "orders_data": [
		 * {
		 * "msg": "All is Well",
		 * "success": true,
		 * "unique_id": "R474330703112"
		 * }
		 * ]
		 * }
		 */

	}
	public function fetchWayBills()
	{
		$uri = "fecth-waybills";
		$data = array();
		$data["shipment_details"][0] = array(
				"unique_id" => "200000002"
		);

		var_dump($this->postCall($uri,$data));
		//return $this->postCall($uri,$data);
		/*
		 * {
		 * "waybills_data": [
		 * {
		 * "success": true,
		 * "shipping_label":
		 * "https://s3apsoutheast1.amazonaws.com/pigenshippinglabels/gojavas/2016/01/19/UNIPINC124611TEST_gojavas.pdf",
		 * "processed": true,
		 * "msg": "All is Well",
		 * "partner": "gojavas",
		 * "awb": "UNIPINC124611TEST",
		 * "unique_id": "R47433070311"
		 * }
		 * ]
		 * }
		 */
	}
	public function trackShipments($track)
	{
		$uri = "track/shipments";
		$data = array();
		$data["shipment_details"][0] = array(
				"pigen_id" => "",
				"awb_number" => $track
		);
		return $this->postCall($uri,$data);
		/*
		 * {
		 * "results": [
		 * {
		 * "status": "Delivered",
		 * "details": [
		 * {
		 * "status": "In Transit",
		 * "time": "20160112
		 * 05:11 PM",
		 * "desc": "IN TRANSIT BETWEEN STVLPC AND DHSLPC2"
		 * },
		 * {
		 * "status": "Gone For Delivery vide DRS",
		 * "time": "20160115 05:02 AM",
		 * "desc": "Gone For Delivery vide DRS #: DS/DELP/1516/012324 15 Jan 16"
		 * },
		 * {
		 * "status": "Delivered",
		 * "time": "20160115 01:44 PM",
		 * "desc": "Delivered @DELP On 1/15/2016 1:43:42 PM DS/DELP/1516/012324 Rec. By bd"
		 * }
		 * ],
		 * "dt": "20160115 01:44 PM",
		 * "awb": "UNIPINC137211",
		 * "desc": "Delivered @DELP On 1/15/2016 1:43:42 PM DS/DELP/1516/012324 Rec. By bd"
		 * }
		 * ],
		 * "error": false
		 * }
		 */
	}
	public function log($_msg)
	{
		Mage::log($_msg,null,'gopigen.log');
	}
}