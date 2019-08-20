<?php

namespace Vnecoms\PdfPro\Block;

/**
 * Class Js.
 */
class Css extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;
    
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->coreRegistry = $registry;
    }
    
    /**
     * @return \Vnecoms\PdfPro\Model\Key
     */
    public function getCurrentTemplate(){
        return $this->coreRegistry->registry('current_key');
    }
}
