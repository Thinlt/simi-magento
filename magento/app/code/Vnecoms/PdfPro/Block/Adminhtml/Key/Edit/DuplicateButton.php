<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Vnecoms\PdfPro\Block\Adminhtml\Key\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class DeleteButton.
 */
class DuplicateButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        $data = [];
        if ($this->getId()) {
            $data = [
                'label' => __('Duplicate'),
                'class' => 'save',
                'url' => $this->getActionUrl(),
                'style' => '',
                'disabled' => false,
                'sort_order' => 22,
            ];
        }

        return $data;
    }

    /**
     * @return string
     */
    public function getActionUrl()
    {
        return $this->getUrl('vnecoms_pdfpro/key/duplicate', ['id' => $this->getId()]);
    }
}
