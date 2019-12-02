<?php
/**
 * @category    Magento
 * @package     Magento_Sales
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Simi\Simicustomize\Block\Adminhtml\Order;

class View extends \Magento\Backend\Block\Widget\Container
{
    /**
     * Prepare button and grid
     */
    protected function _prepareLayout()
    {
        $order = $this->getParentBlock()->getOrder();

        if (
            $this->_isAllowedAction('Magento_Sales::emails') &&
            $order->getData('order_type') == \Simi\Simicustomize\Ui\Component\Sales\Order\Column\OrderType::ORDER_TYPE_PRE_ORDER_WAITING
        ) {
            $message = __('Are you sure you want to send an deposit order email to customer?');
            $this->addButton(
                'preorder_deposit',
                [
                    'label' => __('Send Pre-order Remaining Email'),
                    'class' => 'preorder-deposit',
                    'onclick' => "confirmSetLocation('{$message}', '{$this->getPreorderDepositUrl($order)}')",
                    // 'data_attribute' => [
                    //     'url' => $this->getPreorderDepositUrl(),
                    // ]
                ]
            );
        }

        return parent::_prepareLayout();
    }

    /**
     * Hold URL getter
     *
     * @return string
     */
    public function getPreorderDepositUrl($order)
    {
        return $this->getUrl('simicustomize/preorder/sendpreorderemail',
            ['order_id' => $order->getId()]);
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
