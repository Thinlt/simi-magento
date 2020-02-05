<?php

namespace Vnecoms\PdfPro\Model\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Processor.
 */
class Processor implements ArrayInterface
{
    protected $_options;
    /**
     * Get options.
     *
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->_options) {
            $this->_options = [];
            $processors = \Magento\Framework\App\ObjectManager::getInstance()
                    ->create('\Vnecoms\PdfPro\Model\Config\ProcessorsConfig')
                    ->getConfig('processor');

            foreach ($processors as $p) {
                $this->_options[] = [
                    'label' => $p['title'],
                    'value' => $p['class'],
                ];
            }
        }

        return $this->_options;
    }
}
