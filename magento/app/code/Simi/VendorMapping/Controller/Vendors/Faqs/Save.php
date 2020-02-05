<?php
/**
 * Copyright 2019 SimiCart. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Simi\VendorMapping\Controller\Vendors\Faqs;

use Magento\Framework\View\Result\PageFactory;

/**
 * Class Index
 *
 * @package Simi\VendorMapping\Controller\Vendors\Save
 */
class Save extends \Vnecoms\Vendors\Controller\Vendors\Action
{
    const XML_PATH_STORE_FAQS = 'general/store/faqs';

    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    protected $_aclResource = 'Simi_VendorMapping::store_faqs';

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * Constructor
     *
     * @param Context $context
     * @param DataPersistorInterface $dataPersistor
     */
    public function __construct(
        \Vnecoms\Vendors\App\Action\Context $context
    ) {
        $this->objectManager = $context->getObjectManager();
        $this->dataPersistor = $this->objectManager->get(\Magento\Framework\App\Request\DataPersistorInterface::class);
        parent::__construct($context);
    }

    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $helper = $this->objectManager->get(\Vnecoms\VendorsConfig\Helper\Data::class);
        $vendor = $this->_vendorsession->getVendor();
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data = $this->getRequest()->getPostValue()) {
            try {
                $storeId = 0;
                $this->dataPersistor->clear('vendor_faqs');
                $groups = array('store' => array('fields' => array('faqs' => array('value' => $data['content']))));
                $helper->saveConfig($vendor->getId(), 'general', $groups, $storeId);

                $this->messageManager->addSuccessMessage(__('Successfully saved'));

                if ($vendor && $vendor->getId()) {
                    return $resultRedirect->setPath('*/*/edit/id/'.$vendor->getId());
                }
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('Something went wrong while saving the data')
                );
            }
            $this->dataPersistor->set('vendor_faqs', $data);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
