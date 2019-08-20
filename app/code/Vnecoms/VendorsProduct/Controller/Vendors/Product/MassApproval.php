<?php

namespace Vnecoms\VendorsProduct\Controller\Vendors\Product;

use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\Filter\Date;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Vnecoms\VendorsProduct\Model\Source\Approval as ProductApproval;

class MassApproval extends \Vnecoms\VendorsProduct\Controller\Vendors\Product
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    protected $_aclResource = 'Vnecoms_Vendors::product_action_save';
    
    /**
     * @var \Magento\Catalog\Model\Indexer\Product\Price\Processor
     */
    protected $_productPriceIndexerProcessor;

    /**
     * MassActions filter
     *
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     *
     * @param \Vnecoms\Vendors\App\Action\Context $context
     * @param \Vnecoms\Vendors\App\ConfigInterface $config
     * @param Registry $coreRegistry
     * @param Date $dateFilter
     * @param \Magento\Catalog\Controller\Adminhtml\Product\Builder $productBuilder
     * @param \Magento\Catalog\Model\Indexer\Product\Price\Processor $productPriceIndexerProcessor
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        \Vnecoms\Vendors\App\Action\Context $context,
        \Magento\Catalog\Controller\Adminhtml\Product\Builder $productBuilder,
        \Magento\Catalog\Model\Indexer\Product\Price\Processor $productPriceIndexerProcessor,
        Filter $filter,
        CollectionFactory $collectionFactory
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->_productPriceIndexerProcessor = $productPriceIndexerProcessor;
        parent::__construct($context, $productBuilder);
    }

    /**
     * Update product(s) status action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $collection = $this->filter->getCollection(
            $this->collectionFactory->create()->addAttributeToSelect('approval')
        );
        $productIds = $collection->getAllIds();
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        try {
            if(count($productIds)){
                $newProductStatuses = [ProductApproval::STATUS_NOT_SUBMITED, ProductApproval::STATUS_UNAPPROVED];
                $vendorProductHelper =  $this->_objectManager->get('Vnecoms\VendorsProduct\Helper\Data');
                foreach ($collection->getItems() as $product) {
                    $approvalStatus = in_array($product->getApproval(), $newProductStatuses)?
                        ProductApproval::STATUS_PENDING:
                        ProductApproval::STATUS_PENDING_UPDATE;
                    $product->setApproval($approvalStatus)
                        ->getResource()
                        ->saveAttribute($product, 'approval');
                    if($approvalStatus == ProductApproval::STATUS_PENDING){
                        /*Send new product approval notification email to admin*/
                        $vendorProductHelper->sendNewProductApprovalEmailToAdmin($product, $this->_getSession()->getVendor());
                    }else{
                        $vendorProductHelper->sendUpdateProductApprovalEmailToAdmin($product, $this->_getSession()->getVendor());
                    }
                }
                $this->messageManager->addSuccess(__('A total of %1 record(s) have been submited.', count($productIds)));
                $this->_productPriceIndexerProcessor->reindexList($productIds);
            }else{
                $this->messageManager->addSuccess(__('A total of %1 record(s) have been submited.', count($productIds)));
            }

        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->_getSession()->addException($e, __('Something went wrong while updating the product(s) status.'));
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('catalog/*/', ['store' => $storeId]);
    }
}
