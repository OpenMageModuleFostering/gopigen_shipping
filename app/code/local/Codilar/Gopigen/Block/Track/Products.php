<?php
	/**
	 * Codilar Technologies Pvt. Ltd.
	 * @category    Gopigen Shipping
	 * @package     Codilar
	 * @copyright   Copyright (c) 2016 Codilar. (http://www.codilar.com)
	 * @purpose     Admin grid render
	 * @author      Codilar Team
	 **/
	class Codilar_Gopigen_Block_Track_Products extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
	{

		public function render(Varien_Object $row)
		{
			$Order_id =  $row->getOrderId();
			$order = Mage::getModel('sales/order')->loadByIncrementId($Order_id);
			$items = $order->getAllItems();
			$name=array();
			foreach ($items as $itemId => $item)
			{
				$name[] = $item->getName();
				$sku[]=$item->getSku();
			}
			$product_name = implode(", ",$name);
			unset($order);
			return $product_name;
		}

	}