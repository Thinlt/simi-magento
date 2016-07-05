<?php
/**
 * Simi
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Simi.com license that is
 * available through the world-wide-web at this URL:
 * http://www.simi.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Simi
 * @package     Simi_Simirewardpoints
 * @copyright   Copyright (c) 2012 Simi (http://www.simi.com/)
 * @license     http://www.simi.com/license-agreement.html
 */

/**
 * Simirewardpoints Tab on Customer Edit Form Block
 * 
 * @category    Simi
 * @package     Simi_Simirewardpoints
 * @author      Simi Developer
 */
class Simi_Simirewardpoints_Block_Adminhtml_Customer_Edit_Tab_History
    extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('simirewardpointsTransactionGrid');
        $this->setDefaultSort('simirewardpoints_transaction_transaction_id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
    }
    
    /**
     * prepare collection for block to display
     *
     * @return Simi_Simirewardpoints_Block_Adminhtml_Customer_Edit_Tab_History
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getSingleton('simirewardpoints/transaction')->getCollection()
            ->addFieldToFilter('customer_id', $this->getRequest()->getParam('id'));
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    /**
     * prepare columns for this grid
     *
     * @return Simi_Simirewardpoints_Block_Adminhtml_Customer_Edit_Tab_History
     */
    protected function _prepareColumns()
    {
        $this->addColumn('transaction_id', array(
            'header'    => Mage::helper('simirewardpoints')->__('ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'transaction_id',
            'type'      => 'number',
        ));
        
        $this->addColumn('title', array(
            'header'    => Mage::helper('simirewardpoints')->__('Title'),
            'align'     =>'left',
            'index'     => 'title',
        ));
        
        $this->addColumn('action', array(
            'header'    => Mage::helper('simirewardpoints')->__('Action'),
            'align'     => 'left',
            'index'     => 'action',
            'type'      => 'options',
            'options'   => Mage::helper('simirewardpoints/action')->getActionsHash(),
        ));
        
        $this->addColumn('point_amount', array(
            'header'    => Mage::helper('simirewardpoints')->__('Points'),
            'align'     => 'right',
            'index'     => 'point_amount',
            'type'      => 'number',
        ));
        
        $this->addColumn('point_used', array(
            'header'    => Mage::helper('simirewardpoints')->__('Points Used'),
            'align'     => 'right',
            'index'     => 'point_used',
            'type'      => 'number',
        ));
        
        $this->addColumn('created_time', array(
            'header'    => Mage::helper('simirewardpoints')->__('Created On'),
            'index'     => 'created_time',
            'type'      => 'datetime',
        ));
        
        $this->addColumn('expiration_date', array(
            'header'    => Mage::helper('simirewardpoints')->__('Expires On'),
            'index'     => 'expiration_date',
            'type'      => 'datetime',
        ));
        
        $this->addColumn('status', array(
            'header'    => Mage::helper('simirewardpoints')->__('Status'),
            'align'     => 'left',
            'index'     => 'status',
            'type'      => 'options',
            'options'   => Mage::getSingleton('simirewardpoints/transaction')->getStatusHash(),
        ));
        
        $this->addColumn('store_id', array(
            'header'    => Mage::helper('simirewardpoints')->__('Store View'),
            'align'     => 'left',
            'index'     => 'store_id',
            'type'      => 'options',
            'options'   => Mage::getModel('adminhtml/system_store')->getStoreOptionHash(true),
        ));
        
        $this->addExportType('adminhtml/reward_customer/exportCsv'
            , Mage::helper('simirewardpoints')->__('CSV')
        );
        $this->addExportType('adminhtml/reward_customer/exportXml'
            , Mage::helper('simirewardpoints')->__('XML')
        );
        return parent::_prepareColumns();
    }
    
    /**
     * Add column to grid
     *
     * @param   string $columnId
     * @param   array || Varien_Object $column
     * @return  Simi_Simirewardpoints_Block_Adminhtml_Customer_Edit_Tab_History
     */
    public function addColumn($columnId, $column)
    {
        $columnId = 'simirewardpoints_transaction_' . $columnId;
        return parent::addColumn($columnId, $column);
    }
    
    /**
     * get url for each row in grid
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('adminhtml/reward_transaction/edit', array('id' => $row->getId()));
    }
    
    /**
     * get grid url (use for ajax load)
     * 
     * @return string
     */
    public function getGridUrl()
    {
       return $this->getUrl('adminhtml/reward_customer/grid', array('_current' => true));
    }
}
