<?php

namespace Vnecoms\PdfPro\Block\Filter;

use Magento\Weee\Block\Item\Price\Renderer as ItemPriceRenderer;

/**
 * Class Widget.
 *
 * @author Vnecoms team <vnecoms.com>
 */
class Widget extends \Magento\Weee\Block\Adminhtml\Items\Price\Renderer
{
    /**
     * Widget constructor.
     *
     * @param \Magento\Backend\Block\Template\Context                   $context
     * @param \Magento\Sales\Block\Adminhtml\Items\Column\DefaultColumn $defaultColumnRenderer
     * @param \Magento\Tax\Helper\Data                                  $taxHelper
     * @param ItemPriceRenderer                                         $itemPriceRenderer
     * @param array                                                     $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Sales\Block\Adminhtml\Items\Column\DefaultColumn $defaultColumnRenderer,
        \Magento\Tax\Helper\Data $taxHelper,
        ItemPriceRenderer $itemPriceRenderer,
        array $data = []
    ) {
        parent::__construct($context, $defaultColumnRenderer, $taxHelper, $itemPriceRenderer, $data);
    }
}
