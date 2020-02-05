<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Simi\Simicustomize\Controller\Adminhtml\Reserve;

use Magento\Backend\App\Action\Context;

class Massaction extends \Magento\Backend\App\Action
{
    /**
     * @var \Simi\Simicustomize\Model\ReserveFactory
     */
    protected $reserveFactory;

    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Simi_Simicustomize::sales_reserve';

    /**
     * Array of actions which can be processed without secret key validation
     *
     * @var string[]
     */
    // protected $_publicActions = ['deposit'];

    /**
     * @param Context $context
     * @param ReserveFactory $reserveFactory
     */
    public function __construct(
        Context $context,
        \Simi\Simicustomize\Model\ReserveFactory $reserveFactory
    ) {
        parent::__construct($context);
        $this->reserveFactory = $reserveFactory;
    }

    /**
     * Orders grid
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data = $this->getRequest()->getPostValue()) {
            $status = $this->getRequest()->getParam('status');
            try {
                if (isset($data['selected']) && is_array($data['selected']) && $status) {
                    foreach($data['selected'] as $id){
                        $model = $this->reserveFactory->create()->load($id);
                        $model->setStatus($status)->save();
                    }
                }
                $this->messageManager->addSuccessMessage(__('Reserve status successfully saved'));
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('Something went wrong while saving the Gift Card code')
                );
            }
        }
        return $resultRedirect->setPath('*/*/');
    }

    /*
	 * Check permission via ACL resource
	 */
	protected function _isAllowed()
	{
		return $this->_authorization->isAllowed('Simi_Simicustomize::sales_reserve');
	}
}
