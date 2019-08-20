<?php

namespace Vnecoms\PdfPro\Controller\Adminhtml\Widget;

/**
 * Class LoadOptions.
 *
 * @author Vnecoms team <vnecoms.com>
 */
class LoadOptions extends \Magento\Backend\App\Action
{
    /**
     * Ajax responder for loading plugin options form.
     */
    public function execute()
    {
        try {
            $this->_view->loadLayout();
            if ($paramsJson = $this->getRequest()->getParam('widget')) {
                $request = $this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonDecode($paramsJson);
                if (is_array($request)) {
                    $optionsBlock = $this->_view->getLayout()->getBlock('easypdf_wysiwyg_widget.options');
                    if (isset($request['widget_type'])) {
                        $optionsBlock->setWidgetType($request['widget_type']);
                    }
                    if (isset($request['values'])) {
                        $optionsBlock->setWidgetValues($request['values']);
                    }
                    if (isset($request['editor'])) {
                        $optionsBlock->setEditor($request['editor']);
                    }
                }
                $this->_view->renderLayout();
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $result = ['error' => true, 'message' => $e->getMessage()];
            $this->getResponse()->representJson(
                $this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($result)
            );
        }
    }

    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Vnecoms_PdfPro::pdfpro_apikey');
    }
}
