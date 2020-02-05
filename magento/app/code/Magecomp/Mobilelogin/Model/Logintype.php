<?php
namespace Magecomp\Mobilelogin\Model;
class Logintype implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('Ajax Login')],
            ['value' => 1, 'label' => __('Login With OTP')]
			
        ];
    }
}
