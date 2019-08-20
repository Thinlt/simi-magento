<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Credit\Block\Adminhtml\Product\Edit\Renderer\Credit;

/**
 * Adminhtml tier price item renderer
 */
class Type extends \Magento\Catalog\Block\Adminhtml\Form\Renderer\Fieldset\Element
{
    /**
     * Retrieve element html
     *
     * @return string
     */
    public function getElementHtml()
    {
        $jsBlock = $this->getLayout()->createBlock('Vnecoms\Credit\Block\Adminhtml\Product\Edit\Renderer\Credit\Type\Js')
            ->setTemplate('catalog/product/edit/credit/type/js.phtml');
        $html = $this->getElement()->getElementHtml();
        $html .= $jsBlock->toHtml();
        return $html;
    }
}
