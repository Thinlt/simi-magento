<?php

namespace Simi\Simistorelocator\Controller\Adminhtml;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;

abstract class AbstractInlineEdit extends \Simi\Simistorelocator\Controller\Adminhtml\AbstractAction {

    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute() {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $error = false;
        $messages = [];

        if ($this->getRequest()->getParam('isAjax')) {
            $postItems = $this->getRequest()->getParam('items', []);
            if (!count($postItems)) {
                $messages[] = __('Please correct the data sent.');
                $error = true;
            } else {
                /** @var \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection $collection */
                $collection = $this->_createMainCollection();
                $collection->addFieldToFilter($collection->getIdFieldName(),
                        ['in' => array_keys($postItems)]);

                foreach ($collection as $model) {
                    try {
                        $model->setData(array_merge($model->getData(),
                                $postItems[$model->getId()]));
                        $model->save();
                    } catch (\Exception $e) {
                        $messages[] = $this->getErrorWithModelId(
                                $model, __($e->getMessage())
                        );
                        $error = true;
                    }
                }
            }
        }

        return $resultJson->setData([
                    'messages' => $messages,
                    'error' => $error
        ]);
    }

    /**
     * Add Store title to error message.
     *
     * @param \Magento\Framework\Model\AbstractModel $model
     * @param                                        $errorText
     *
     * @return string
     */
    protected function getErrorWithModelId(\Magento\Framework\Model\AbstractModel $model, $errorText) {
        return '[Item ID: ' . $model->getId() . '] ' . $errorText;
    }

}
