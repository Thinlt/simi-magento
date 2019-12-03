<?php
namespace Magecomp\Mobilelogin\Model;
class Layout implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('')],
            ['value' => 'image', 'label' => __('Image')],
            ['value' => 'template', 'label' => __('Template')]

        ];
    }
}