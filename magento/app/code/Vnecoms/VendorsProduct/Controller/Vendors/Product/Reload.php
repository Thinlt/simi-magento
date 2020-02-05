<?php

namespace Vnecoms\VendorsProduct\Controller\Vendors\Product;

use Magento\Framework\Controller\ResultFactory;

/**
 * Backend reload of product create/edit form
 */
class Reload extends \Vnecoms\VendorsProduct\Controller\Vendors\Product
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    protected $_aclResource = 'Vnecoms_Vendors::product_action_save';
    
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        if (!$this->getRequest()->getParam('set')) {
            return $this->resultFactory->create(ResultFactory::TYPE_FORWARD)->forward('noroute');
        }

        $product = $this->productBuilder->build($this->getRequest());

        /** @var \Magento\Framework\View\Result\Layout $resultLayout */
        $this->_view->loadLayout();
        $this->_view->getLayout()->getUpdate()->addHandle(['catalog_product_' . $product->getTypeId()]);
        $this->_view->getLayout()->getUpdate()->removeHandle('default');
        $this->_view->getPage()->setHeader('Content-Type', 'application/json', true);
        $this->_view->renderLayout();
    }
}
