<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsConfig\Model\Config\Structure\Element;

class Field extends \Magento\Config\Model\Config\Structure\Element\Field
{
    /**
     * @var \Vnecoms\VendorsConfig\Model\Config\BackendFactory
     */
    protected $_vendorBackendFactory;
    
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Config\Model\Config\BackendFactory $backendFactory,
        \Magento\Config\Model\Config\SourceFactory $sourceFactory,
        \Magento\Config\Model\Config\CommentFactory $commentFactory,
        \Magento\Framework\View\Element\BlockFactory $blockFactory,
        \Magento\Config\Model\Config\Structure\Element\Dependency\Mapper $dependencyMapper,
        \Vnecoms\VendorsConfig\Model\Config\BackendFactory $vendorBackendFactory
    ) {
        parent::__construct(
            $storeManager,
            $moduleManager,
            $backendFactory,
            $sourceFactory,
            $commentFactory,
            $blockFactory,
            $dependencyMapper
        );
        
        $this->_vendorBackendFactory = $vendorBackendFactory;
    }
    
    /**
     * Retrieve backend model
     *
     * @return \Magento\Framework\App\Config\ValueInterface
     */
    public function getBackendModel()
    {
        return $this->_vendorBackendFactory->create($this->_data['backend_model']);
    }
}
