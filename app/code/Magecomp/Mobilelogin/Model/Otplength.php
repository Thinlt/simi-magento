<?php
namespace Magecomp\Mobilelogin\Model;
class Otplength implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        $options = [];
        for($len = 1; $len <=10; $len++)
        {
            $options[] = ['value' => $len,'label' => $len];
        }
        return $options;
    }
}
