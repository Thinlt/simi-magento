<?php
namespace Vnecoms\Vendors\Controller\Adminhtml\Index;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\Filter\Date;
use Magento\Framework\Controller\Result\JsonFactory;
use Vnecoms\Vendors\Controller\Adminhtml\Action;

class ValidateVendor extends Action
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    
    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        Date $dateFilter,
        JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context, $coreRegistry, $dateFilter);
        $this->resultJsonFactory = $resultJsonFactory;
    }
    
    
    public function execute()
    {
        $response   = new \Magento\Framework\DataObject();
        $vendorId   = $this->getRequest()->getParam('vendor_id');
        $key        = $this->getRequest()->getParam('key_val');
        try{
            $resource = $this->_objectManager->create('Vnecoms\Vendors\Model\ResourceModel\Vendor');
            $connection = $resource->getConnection();
            $select = $connection->select();
            $select->from(
                $resource->getTable('ves_vendor_entity'),
                'entity_id'
            )->where(
                'vendor_id = :vendor_id'
            );
            $bind = [
                'vendor_id' => $vendorId,
            ];
            
            $vendorId = $connection->fetchOne($select,$bind);
            
            $data = [
                'valid' => !$vendorId,
                'key_val'   => $key,
            ];
        }catch(\Exception $e){
            $data = [
                'valid' => false,
                'key_val'   => $key,
            ];
        }
        
        
        $response->setData($data);
        return $this->resultJsonFactory->create()->setJsonData($response->toJson());
    }
}
