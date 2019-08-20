<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Form password element
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Vnecoms\Vendors\Block\Adminhtml\Form\Element;

use Magento\Framework\Escaper;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Factory;
use Magento\Framework\Data\Form\Element\CollectionFactory;


class Password extends AbstractElement
{

    /**
     * Return the attributes for Html.
     *
     * @return string[]
     */
    public function getHtmlAttributes()
    {
        return [
            'data-password-min-character-sets',
            'data-password-min-length',
            'type',
            'title',
            'class',
            'style',
            'onclick',
            'onchange',
            'disabled',
            'readonly',
            'tabindex',
            'placeholder',
            'data-form-part',
            'data-role',
            'data-action',
            'checked',
        ];
    }

    /**
     * @param Factory $factoryElement
     * @param CollectionFactory $factoryCollection
     * @param Escaper $escaper
     * @param array $data
     */
    public function __construct(
        Factory $factoryElement,
        CollectionFactory $factoryCollection,
        Escaper $escaper,
        $data = []
    ) {
        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);
        $this->setType('password');
        $this->setExtType('textfield');
    }

    /**
     * @return mixed
     */
    public function getHtml()
    {
        $this->addClass('input-text');
        return parent::getHtml();
    }
}




