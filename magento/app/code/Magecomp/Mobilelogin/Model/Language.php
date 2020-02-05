<?php
namespace Magecomp\Mobilelogin\Model;
class Language implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'N', 'label' => __('English')],
            ['value' => 'LNG', 'label' => __('Other Language')]
        ];
    }
}
