<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Simi\Simicustomize\Block\Adminhtml\Service\Form\Field;

use Magento\Framework\App\Request\DataPersistorInterface;

/**
 * Standard admin block. Adds admin-specific behavior and event.
 * Should be used when you declare a block in admin layout handle.
 *
 * Avoid extending this class if possible.
 *
 * If you need custom presentation logic in your blocks, use this class as block, and declare
 * custom view models in block arguments in layout handle file.
 *
 * Example:
 * <block name="my.block" class="Magento\Backend\Block\Template" template="My_Module::template.phtml" >
 *      <arguments>
 *          <argument name="view_model" xsi:type="object">My\Module\ViewModel\Custom</argument>
 *      </arguments>
 * </block>
 *
 * Your class object can then be accessed by doing $block->getViewModel()
 *
 * @api
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 * @since 100.0.2
 */
class Images extends \Magento\Backend\Block\Template
{
    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;
    private $collection;
    private $storeManager;

    public $data;
    public $model;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param DataPersistorInterface $dataPersistor
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context, 
        DataPersistorInterface $dataPersistor,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\RequestInterface $request,
        \Simi\Simicustomize\Model\ResourceModel\Service\CollectionFactory $collectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
    )
    {
        $this->dataPersistor = $dataPersistor;
        $this->registry = $registry;
        $this->request = $request;
        $this->collection = $collectionFactory->create();
        $this->storeManager = $storeManager;
        parent::__construct($context, $data);
    }

    public function getFormData(){
        if (!$this->data) {
            if ($this->getModel()) {
                $this->data = $this->getModel()->getData();
            }
        }
        return $this->data;
    }

    public function getModel(){
        if(!$this->model){
            $id = $this->request->getParam('id');
            if ($id) {
                $collection = $this->collection->addFieldToFilter('id', $id)->getItems();
                foreach ($collection as $model) {
                    if ($id == $model->getId()) {
                        $this->model = $model;
                    }
                }
            }
        }
        return $this->model;
    }

    public function getFiles(){
        $model = $this->getModel();
        return explode(',', (string)$model->getFiles());
    }

    public function getFileUrls(){
        $urls = [];
        foreach($this->getFiles() as $file){
            if (trim($file)) {
                $baseUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
                $urls[] = $baseUrl . trim($file);
            }
        }
        return $urls;
    }
}
