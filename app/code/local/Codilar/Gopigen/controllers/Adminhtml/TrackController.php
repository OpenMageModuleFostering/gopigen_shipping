<?php
/**
 * Codilar Technologies Pvt. Ltd.
 * @category    Gopigen Shipping
 * @package     Codilar
 * @copyright   Copyright (c) 2016 Codilar. (http://www.codilar.com)
 * @purpose     Magento admin controller for tracking
 * @author      Codilar Team
 **/

class Codilar_Gopigen_Adminhtml_TrackController extends Mage_Adminhtml_Controller_Action {

    protected function _isAllowed()
    {
        return true;
    }
    
    public function indexAction()
    {
        $this->loadLayout();
        $this->_addContent($this->getLayout()->createBlock('codilar_gopigen/track'));
        $this->renderLayout();
    }


    public function massPrintAction(){
        $orderIds = $this->getRequest()->getPost('order_ids');
        $flag = false;
        if (!empty($orderIds)) {
            foreach ($orderIds as $orderId) {
                $order = Mage::getModel('sales/order')->load($orderId);
                $track =  Mage::getModel('codilar_gopigen/track')->load($order->getIncrementId(),'order_id');
                if ($track->getLabel()) {
                    $flag = true;
                    $pdfURL = $track->getLabel();
                    if (!isset($pdf)){
                        $pdf = new Zend_Pdf();
                        $_pdf = file_get_contents($pdfURL);
                        $pdf1 = Zend_Pdf::parse($_pdf);
                        foreach($pdf1->pages as $page){
                            $clonedPage = clone $page;
                            $pdf->pages[] = $clonedPage;
                        }
                        unset($clonedPage);
                    } else {
                        $_pdf = file_get_contents($pdfURL);
                        $pdf2 = Zend_Pdf::parse($_pdf);
                        foreach($pdf2->pages as $page){
                            $clonedPage = clone $page;
                            $pdf->pages[] = $clonedPage;
                        }
                        unset($clonedPage);
                    }
                }
            }
            if ($flag) {
                return $this->_prepareDownloadResponse(
                    'packingslip'.Mage::getSingleton('core/date')->date('Y-m-d_H-i-s').'.pdf', $pdf->render(),
                    'application/pdf'
                );
            } else {
                $this->_getSession()->addError($this->__('There are no printable documents related to selected orders.'));
                $this->_redirect('adminhtml/sales_order');
            }
        }
        $this->_redirect('adminhtml/sales_order');
    }


    public function massRefreshAction(){
        $collection = $this->getRequest()->getParam('ids');
        if (!is_array($collection)) {
            $this->_getSession()->addError($this->__('Please select GoPigen Track Shipment(s).'));
        }else {
            $count = 0;
            try {
                foreach ($collection as $col) {
                    $model = Mage::getModel('codilar_gopigen/track')->load($col);
                    $awb = $model->getAwb();
                    $res = Mage::helper('codilar_gopigen')->trackShipments($awb);
                    $s = $res['results'][0]['status'] ? $res['results'][0]['status'] : $model->getStatus();
                    $t = $res['results'][0]['dt'] ? $res['results'][0]['dt'] : $model->getTime();
                    $d = $res['results'][0]['desc'] ? $res['results'][0]['desc'] : $model->getDesc();
                    $model->setStatus($s);
                    $model->setTime($t);
                    $model->setDesc($d);
                    $model->setUpdateTime(now());
                    $model->save();
                    $count++;
                }
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
            Mage::getSingleton('adminhtml/session')->addSuccess($count . ' Shipping details updated');
        }
        $this->_redirect('*/*');
    }

    public function refreshAction(){
        $collection = Mage::getModel('codilar_gopigen/track')->getCollection()
            ->addFieldToFilter('status',array('neq' => 'Delivered'))
        ;
        $count = 0;
        try{
            foreach($collection as $col){
                $awb = $col->getAwb();
                $res = Mage::helper('codilar_gopigen')->trackShipments($awb);
                $model = Mage::getModel('codilar_gopigen/track')->load($col->getId());
                $s = $res['results'][0]['status']?$res['results'][0]['status']:$col->getStatus();
                $t = $res['results'][0]['dt']?$res['results'][0]['dt']:$col->getTime();
                $d = $res['results'][0]['desc']?$res['results'][0]['desc']:$col->getDesc();
                $model->setStatus($s);
                $model->setTime($t);
                $model->setDesc($d);
                $model->setUpdateTime(now());
                $model->save();
                $count++;
            }
        }catch (Exception $e){
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        Mage::getSingleton('adminhtml/session')->addSuccess( $count . ' Shipping details updated');
        $this->_redirect('*/*');
    }

    public function exportCsvAction()
    {
        $fileName = 'GoPigen_Shipment_export.csv';
        $content = $this->getLayout()->createBlock('codilar_gopigen/track_grid')->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function exportExcelAction()
    {
        $fileName = 'GoPigen_Shipment_export.xml';
        $content = $this->getLayout()->createBlock('codilar_gopigen/track_grid')->getExcel();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function massDeleteAction()
    {
        $ids = $this->getRequest()->getParam('ids');
        if (!is_array($ids)) {
            $this->_getSession()->addError($this->__('Please select GoPigen Track Shipment(s).'));
        } else {
            try {
                foreach ($ids as $id) {
                    $model = Mage::getSingleton('codilar_gopigen/track')->load($id);
                    $model->delete();
                }

                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) have been deleted.', count($ids))
                );
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addError(
                    Mage::helper('codilar_gopigen')->__('An error occurred while mass deleting items. Please review log and try again.')
                );
                Mage::logException($e);
                return;
            }
        }
        $this->_redirect('*/*/index');
    }
}