<?php
namespace Magecomp\Mobilelogin\Model;
class Template implements \Magento\Framework\Option\ArrayInterface
{
       public function toOptionArray()
        {
            $options = [];
            $options[] = ['value' => 0,'label' => __('--Please Select--')];
            for($len = 1; $len <=10; $len++)
            {
                $options[] = ['value' => $len,'label' => __('Template '.$len)];
            }
        return $options;
        }
}