<?php
class Codilar_Gopigen_Block_Adminhtml_Sales_Order_Vendorskus extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        //$getData = $row->getData();
        $order=Mage::getModel('sales/order')->load($row['entity_id']);
        $str="";
        $i=1;
        foreach($order->getAllItems() as $_order){
            $str.= Mage::getModel('catalog/product')->load($_order->getProductId())->getVendorSku();;
            if($i!=count($order->getAllItems()))
                $str.=", ";
            $i++;
        }
        unset($order);
        return $str;
    }
}