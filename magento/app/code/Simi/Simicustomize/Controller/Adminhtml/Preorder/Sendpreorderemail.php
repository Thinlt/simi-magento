<?php

namespace Simi\Simicustomize\Controller\Adminhtml\Preorder;

use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;

class Sendpreorderemail extends \Magento\Sales\Controller\Adminhtml\Order implements HttpGetActionInterface
{
    public function execute()
    {
        $path = 'sales/order/index';
        $pathParams = [];

        try {
            $orderId = $this->getRequest()->getParam('order_id');
            $order = $this->_objectManager->create('\Magento\Sales\Model\Order')->load($orderId);
            $customerEmail = $order->getData('customer_email');
            $path = 'sales/order/view';
            $pathParams['order_id'] = $order->getId();
            $scopeConfig = $this->_objectManager->get('\Magento\Framework\App\Config\ScopeConfigInterface');
            $transportBuilder = $this->_objectManager->create('\Magento\Framework\Mail\Template\TransportBuilder');
            $sender = [
                'name' => $scopeConfig->getValue('trans_email/ident_sales/name'),
                'email' => $scopeConfig->getValue('trans_email/ident_sales/email'),
            ];
            $pwa_url = $scopeConfig->getValue('simiconnector/general/pwa_studio_url');
            $pwa_url = $this->endsWith($pwa_url, '/')?$pwa_url:$pwa_url.'/';
            $completeUrl = $pwa_url . 'preorder_complete.html?deposit_order_id='.$order->getData('increment_id');
            $customerWithEmail = $this->_objectManager->create('\Magento\Customer\Model\Customer')->getCollection()
                ->addFieldToFilter('email', $customerEmail)
                ->getFirstItem();
            if ($customerWithEmail->getId()) {
                $completeUrl .= '&customer_id='.$customerWithEmail->getId().'&customer_email='.$customerEmail;
            }
            $data = array('complete_preorder_url' => $completeUrl);
            $transport = $transportBuilder
                ->setTemplateIdentifier($scopeConfig->getValue('sales/preorder/emailTemplate'))
                ->setTemplateOptions([
                    'area' => \Magento\Framework\App\Area::AREA_ADMINHTML,
                    'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                ])
                ->setTemplateVars($data)
                ->setFrom($sender)
                ->addTo($order->getData('customer_email'))
                ->getTransport();

            $transport->sendMessage();
            $this->messageManager->addSuccess(__('The email has been sent.'));
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Order email sending got error: %1', $e->getMessage()));
        }

        return $this->resultRedirectFactory->create()->setPath($path, $pathParams);
    }

    protected function _getOrderCreateModel()
    {
        return $this->_objectManager->get(\Magento\Sales\Model\AdminOrder\Create::class);
    }

    function endsWith($string, $endString)
    {
        $len = strlen($endString);
        if ($len == 0) {
            return true;
        }
        return (substr($string, -$len) === $endString);
    }
}
