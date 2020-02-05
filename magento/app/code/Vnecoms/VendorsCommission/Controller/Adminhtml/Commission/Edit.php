<?php
namespace Vnecoms\VendorsCommission\Controller\Adminhtml\Commission;

use Magento\Backend\App\Action;

class Edit extends Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;
    
    /**
     * Is access to section allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return parent::_isAllowed() && $this->_authorization->isAllowed('Vnecoms_VendorsCommission::commission_configuration');
    }
    
    /**
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\Registry $registry
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context);
    }
    
    /**
     * @return void
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->_objectManager->create('Vnecoms\VendorsCommission\Model\Rule');

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This rule no longer exists.'));
                $this->_redirect('vendors/*');
                return;
            }
        }
        // set entered data if was error when we do save
        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getRuleData(true);
        if (!empty($data)) {
            $model->addData($data);
        }
        
        $this->_coreRegistry->register('commission_rule', $model);

        $this->_view->loadLayout();

        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Manage Commission Rules'));
        $this->_view->getPage()->getConfig()->getTitle()->prepend(
            $model->getId() ? __("Edit Rule '%1'", $model->getName()) : __('New Commission Rule')
        );
        $this->_view->getLayout()->getBlock(
            'commission_rule_edit'
        )->setData(
            'action',
            $this->getUrl('catalog_rule/promo_catalog/save')
        );

        $breadcrumb = $id ? __('Edit Commission Rule') : __('New Commission Rule');
        $this->_addBreadcrumb($breadcrumb, $breadcrumb);
        $this->_view->renderLayout();
    }
}
