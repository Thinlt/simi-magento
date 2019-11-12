<?php

namespace Simi\Simistorelocator\Controller\Adminhtml;

use Magento\Framework\Exception\LocalizedException;

abstract class AbstractAction extends \Magento\Backend\App\Action {

    /**
     * param id for crud action : edit,delete,save.
     */
    const PARAM_CRUD_ID = 'entity_id';

    /**
     * registry name.
     */
    const REGISTRY_NAME = 'registry_model';

    /**
     * main model class name.
     *
     * @var string
     */
    public $mainModelName;

    /**
     * main collection class name.
     *
     * @var string
     */
    public $mainCollectionName;

    /**
     * Core registry.
     *
     * @var \Magento\Framework\Registry
     */
    public $coreRegistry = null;

    /**
     * Escaper.
     *
     * @var \Magento\Framework\Escaper
     */
    public $escaper;

    /**
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    public $massActionFilter;

    /**
     * @var \Magento\Framework\Filesystem\Directory\ReadInterface
     */
    public $mediaDirectory;

    /**
     * @var \Simi\Simistorelocator\Helper\Image
     */
    public $imageHelper;

    /**
     * @var \Magento\Backend\Helper\Js
     */
    public $backendHelperJs;

    /**
     * AbstractAction constructor.
     *
     * @param \Magento\Backend\App\Action\Context     $context
     * @param \Magento\Framework\Escaper              $escaper
     * @param \Magento\Ui\Component\MassAction\Filter $massActionFilter
     * @param \Magento\Framework\Registry             $coreRegistry
     * @param \Simi\Simistorelocator\Helper\Image    $imageHelper
     * @param \Magento\Backend\Helper\Js              $backendHelperJs
     * @param null                                    $mainModelName
     * @param null                                    $mainCollectionName
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Escaper $escaper,
        \Magento\Ui\Component\MassAction\Filter $massActionFilter,
        \Magento\Framework\Registry $coreRegistry,
        \Simi\Simistorelocator\Helper\Image $imageHelper,
        \Magento\Backend\Helper\Js $backendHelperJs,
        $mainModelName = null,
        $mainCollectionName = null
    ) {
        parent::__construct($context);
        $this->coreRegistry = $coreRegistry;
        $this->massActionFilter = $massActionFilter;
        $this->escaper = $escaper;
        $this->imageHelper = $imageHelper;
        $this->backendHelperJs = $backendHelperJs;
        $this->mainModelName = $mainModelName;
        $this->mainCollectionName = $mainCollectionName;
    }

    /**
     * create m.
     *
     * @return \Magento\Framework\Model\AbstractModel
     *
     * @throws LocalizedException
     */
    protected function _createMainModel() {
        /** @var \Magento\Framework\Model\AbstractModel $model */
        $model = $this->_objectManager->create($this->mainModelName);
        if (!$model instanceof \Magento\Framework\Model\AbstractModel) {
            throw new LocalizedException(
            __('%1 isn\'t instance of Magento\Framework\Model\AbstractModel', get_class($model))
            );
        }

        return $model;
    }

    /**
     * @return \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     *
     * @throws LocalizedException
     */
    protected function _createMainCollection() {
        /** @var \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection $collection */
        $collection = $this->_objectManager->create($this->mainCollectionName);
        if (!$collection instanceof \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection) {
            throw new LocalizedException(
            __(
                    '%1 isn\'t instance of Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection', get_class($collection)
            )
            );
        }

        return $collection;
    }

    /**
     * get back result redirect after add/edit.
     *
     * @param \Magento\Backend\Model\View\Result\Redirect $resultRedirect
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    protected function _getBackResultRedirect(
        \Magento\Backend\Model\View\Result\Redirect $resultRedirect,
        $paramCrudId = null
    ) {
        switch ($this->getRequest()->getParam('back')) {
            case 'edit':
                $resultRedirect->setPath(
                        '*/*/edit', [
                    static::PARAM_CRUD_ID => $paramCrudId,
                    '_current' => true,
                        ]
                );
                break;
            case 'new':
                $resultRedirect->setPath('*/*/new');
                break;
            default:
                $resultRedirect->setPath('*/*/');
        }

        return $resultRedirect;
    }

    /**
     * Check if admin has permissions to visit related pages.
     *
     * @return bool
     */
    protected function _isAllowed() {
        return $this->_authorization->isAllowed('Simi\Simistorelocator::storelocator');
    }

}
