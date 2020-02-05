<?php
namespace Vnecoms\Vendors\Controller\Seller;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Stdlib\DateTime\DateTime;

class ValidateVendor extends \Magento\Framework\App\Action\Action
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
        JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
    }
    
    
    public function execute()
    {
        $response   = new \Magento\Framework\DataObject();
        $vendorId   = $this->getRequest()->getParam('vendor_id');
        $key        = $this->getRequest()->getParam('key');
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
                'key'   => $key,
            ];
        }catch(\Exception $e){
            $data = [
                'valid' => false,
                'key'   => $key,
            ];
        }
        
        
        $response->setData($data);
        return $this->resultJsonFactory->create()->setJsonData($response->toJson());
    }
}
