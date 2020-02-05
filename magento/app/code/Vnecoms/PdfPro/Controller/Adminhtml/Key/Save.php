<?php

namespace Vnecoms\PdfPro\Controller\Adminhtml\Key;

use Magento\Framework\Registry;
use Vnecoms\PdfPro\Controller\Adminhtml\Key;
use Magento\Backend\App\Action\Context;

/**
 * Class Save.
 *
 * @author Vnecoms team <vnecoms.com>
 */
class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Catalog\Model\ImageUploader
     */
    protected $imageUploader;

    /**
     * Authorization level of a basic admin session.
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Vnecoms_PdfPro::pdfpro_apikey';

    /**
     * Core registry.
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    public function __construct(
        Registry $registry,
        Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Catalog\Model\ImageUploader $imageUploader
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        $this->imageUploader = $imageUploader;
        parent::__construct($context);
    }

    /**
     * run the action.
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();

       // var_dump($data);die();
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            if (empty($data['entity_id'])) {
                $data['entity_id'] = null;
            }
            $data = $this->imagePreprocessing($data);
            $data = $this->_filterPostData($data);
            $data = $this->transformLogoVariable($data);

            /** @var \Vnecoms\PdfPro\Model\Key $model */
            $model = $this->_objectManager->create('Vnecoms\PdfPro\Model\Key');

            $id = $this->getRequest()->getParam('id');
            if ($id) {
                $model->load($id);
            }

            $data['customer_group_ids'] = implode(',', $data['customer_group_ids']);
            $data['store_ids'] = implode(',', $data['store_ids']);

            // var_dump($data);die();
            $model->setData($data);

            try {
                $model->save();
                if ($model->getLogo() !== null && $model->isObjectNew(true)) {
                    $this->imageUploader->moveFileFromTmp($model->getLogo());
                }
                $this->messageManager->addSuccess(__('You saved the PDF Template.'));
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId(), '_current' => true]);
                }

                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\AlreadyExistsException $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
                $this->_getSession()->setCategoryData($data);
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
                $this->_getSession()->setKeyData($data);
            } catch (\Exception $e) {
                $this->messageManager->addError(__('Something went wrong while saving the key.'));
                $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
                $this->_getSession()->setKeyData($data);
            }

            return $resultRedirect->setPath('*/*/');
        }

        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Image data preprocessing.
     *
     * @param array $data
     *
     * @return array
     */
    public function imagePreprocessing($data)
    {
        foreach (['logo'] as $field) {
            if (empty($data[$field])) {
                unset($data[$field]);
                $data[$field]['delete'] = true;
            }
        }

        return $data;
    }

    /**
     * Filter key data.
     *
     * @param array $rawData
     *
     * @return array
     */
    protected function _filterPostData(array $rawData)
    {
        $data = $rawData;
        // @todo It is a workaround to prevent saving this data in category model and it has to be refactored in future
        foreach (['logo'] as $field) {
            if (isset($data[$field]) && is_array($data[$field])) {
                if (!empty($data[$field]['delete'])) {
                    $data[$field] = null;
                } else {
                    if (isset($data[$field][0]['name']) && isset($data[$field][0]['tmp_name'])) {
                        $data[$field] = $data[$field][0]['name'];
                    } else {
                        unset($data[$field]);
                    }
                }
            }
        }

        return $data;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Vnecoms_PdfPro::pdfpro_apikey');
    }

    public function transformLogoVariable($data)
    {
        $orderTemp = &$data['order_template'];
        $invoiceTemp = &$data['invoice_template'];
        $shipmentTemp = &$data['shipment_template'];
        $creditmemoTemp = &$data['creditmemo_template'];

        $orderTemp = preg_replace('/<img class=\"easypdf-logo\" src=\"(.*?)\" alt=\"\" \/>/si', '{{var MY_LOGO}}', $orderTemp);
        $invoiceTemp = preg_replace('/<img class=\"easypdf-logo\" src=\"(.*?)\" alt=\"\" \/>/si', '{{var MY_LOGO}}', $invoiceTemp);
        $shipmentTemp = preg_replace('/<img class=\"easypdf-logo\" src=\"(.*?)\" alt=\"\" \/>/si', '{{var MY_LOGO}}', $shipmentTemp);
        $creditmemoTemp = preg_replace('/<img class=\"easypdf-logo\" src=\"(.*?)\" alt=\"\" \/>/si', '{{var MY_LOGO}}', $creditmemoTemp);

        return $data;
    }
}

