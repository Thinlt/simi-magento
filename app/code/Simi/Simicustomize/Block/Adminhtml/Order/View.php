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

        if ($this->_isAllowedAction('Magento_Sales::emails') && $order->getBaseDepositAmount() > 0) {
            $message = __('Are you sure you want to send an deposit order email to customer?');
            $this->addButton(
                'preorder_deposit',
                [
                    'label' => __('Pre-order Deposit'),
                    'class' => 'preorder-deposit',
                    'onclick' => "confirmSetLocation('{$message}', '{$this->getPreorderDepositUrl()}')",
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
    public function getPreorderDepositUrl()
    {
        return $this->getUrl('simi/preorder/deposit');
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
