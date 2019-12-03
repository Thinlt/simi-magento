<?php
namespace Magecomp\Mobilelogin\Model;
class Routetype implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 1, 'label' => __('promotinal Message')],
            ['value' => 4, 'label' => __('transactional Message')]
			
        ];
    }
}
