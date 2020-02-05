<?php
namespace Magecomp\Mobilelogin\Model;
class Design implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'standardlayout', 'label' => __('Standard Layout')],
            ['value' => 'ultimatelayout' , 'label' => __('Ultimate Layout')]

        ];
    }
}