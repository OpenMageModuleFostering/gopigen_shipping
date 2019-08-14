<?php
/**
 * Codilar Technologies Pvt. Ltd.
 * @category    Gopigen Shipping
 * @package     Codilar
 * @copyright   Copyright (c) 2016 Codilar. (http://www.codilar.com)
 * @purpose     Magento admin grid
 * @author      Codilar Team
 **/
class Codilar_Gopigen_Block_Track_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct()
    {
        parent::__construct();
        $this->setId('grid_id');
        // $this->setDefaultSort('COLUMN_ID');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);

    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('codilar_gopigen/track')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {

       $this->addColumn('id',
           array(
               'header'=> $this->__('ID'),
               'width' => '50px',
               'index' => 'id'
           )
       );

        $this->addColumn('awb',
            array(
                'header'=> $this->__('AWB'),
                'width' => '50px',
                'index' => 'awb'
            )
        );

        $this->addColumn('order_id',
            array(
                'header'=> $this->__('Order ID'),
                'width' => '50px',
                'index' => 'order_id'
            )
        );

        $this->addColumn('status',
            array(
                'header'=> $this->__('Status'),
                'width' => '50px',
                'index' => 'status'
            )
        );

        $this->addColumn('time',
            array(
                'header'=> $this->__('Time'),
                'width' => '50px',
                'index' => 'time'
            )
        );

        $this->addColumn('desc',
            array(
                'header'=> $this->__('Description'),
                'width' => '100px',
                'index' => 'desc'
            )
        );

        $this->addColumn('label',
            array(
                'header'=> $this->__('Label'),
                'width' => '50px',
                'index' => 'label',
                'renderer' => 'Codilar_Gopigen_Block_Track_Label'
            )
        );

        $this->addColumn('update_time',
            array(
                'header'=> $this->__('Last updated on'),
                'width' => '50px',
                'index' => 'update_time'
            )
        );

                $this->addExportType('*/*/exportCsv', $this->__('CSV'));
        
                $this->addExportType('*/*/exportExcel', $this->__('Excel XML'));
        
        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
          return '';
    }

    protected function _prepareMassaction()
    {
        $modelPk = Mage::getModel('codilar_gopigen/track')->getResource()->getIdFieldName();
        $this->setMassactionIdField($modelPk);
        $this->getMassactionBlock()->setFormFieldName('ids');
        // $this->getMassactionBlock()->setUseSelectAll(false);
        $this->getMassactionBlock()->addItem('delete', array(
             'label'=> $this->__('Delete'),
             'url'  => $this->getUrl('*/*/massDelete'),
        ));
        $this->getMassactionBlock()->addItem('refresh', array(
            'label'=> $this->__('Refresh selected'),
            'url'  => $this->getUrl('*/*/massRefresh'),
        ));
        return $this;
    }
    }
