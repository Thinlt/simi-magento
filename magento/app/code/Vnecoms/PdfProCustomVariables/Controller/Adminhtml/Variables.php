<?php
/**
 * Created by PhpStorm.
 * User: mrtuvn
 * Date: 23/01/2017
 * Time: 22:54
 */

namespace Vnecoms\PdfProCustomVariables\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Vnecoms\PdfProCustomVariables\Model\PdfproCustomVariables;

abstract class Variables extends Action
{
    /** @var \Magento\Framework\Registry  */
    protected $coreRegistry = null;

    /** @var \Vnecoms\PdfProCustomVariables\Model\PdfproCustomVariablesFactory  */
    protected $customVariablesFactory;

    /**
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
    protected $resultLayoutFactory;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;


    public function __construct(
        Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Vnecoms\PdfProCustomVariables\Model\PdfproCustomVariablesFactory $customVariablesFactory,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->resultLayoutFactory = $resultLayoutFactory;
        $this->customVariablesFactory = $customVariablesFactory;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * Define active menu item in menu block
     *
     * @param string $itemId current active menu item
     * @return $this
     */
    protected function _setActiveMenu($itemId)
    {
        /** @var $menuBlock \Magento\Backend\Block\Menu */
        $menuBlock = $this->_view->getLayout()->getBlock('menu');
        //var_dump($menuBlock);die;
        $menuBlock->setActive($itemId);
        $parents = $menuBlock->getMenuModel()->getParentItems($itemId);
        foreach ($parents as $item) {
            /** @var $item \Magento\Backend\Model\Menu\Item */
            $this->_view->getPage()->getConfig()->getTitle()->prepend($item->getTitle());
        }
        return $this;
    }

    /**
     * Initiate rule
     *
     * @return void
     */
    protected function _initVariable()
    {
        $variable = $this->customVariablesFactory->create();
        $this->coreRegistry->register(
            'current_variable',
            $variable
        );
        $id = (int)$this->getRequest()->getParam('id');

        if (!$id && $this->getRequest()->getParam('custom_variable_id')) {
            $id = (int)$this->getRequest()->getParam('custom_variable_id');
        }

        if ($id) {
            $this->coreRegistry->registry('current_variable')->load($id);
        }
    }

    /**
     * Initiate action
     *
     * @return Variables
     */
    protected function _initAction()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('Vnecoms_PdfProCustomVariables::customvariables_index')
            ->_addBreadcrumb(__('Manager Variables'), __('Manager Variables'));
        return $this;
    }

    /**
     * Returns result of current user permission check on resource and privilege
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Vnecoms_PdfProCustomVariables::customvariables');
    }
}
