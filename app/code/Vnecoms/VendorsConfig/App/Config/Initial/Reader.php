<?php
namespace Vnecoms\VendorsConfig\App\Config\Initial;

class Reader extends \Magento\Framework\App\Config\Initial\Reader
{
    /**
     * 
     * @param \Magento\Framework\Config\FileResolverInterface $fileResolver
     * @param \Magento\Framework\Config\ConverterInterface $converter
     * @param SchemaLocator $schemaLocator
     * @param \Magento\Framework\Config\DomFactory $domFactory
     * @param string $fileName
     */
    public function __construct(
        \Magento\Framework\Config\FileResolverInterface $fileResolver,
        \Magento\Framework\Config\ConverterInterface $converter,
        SchemaLocator $schemaLocator,
        \Magento\Framework\Config\DomFactory $domFactory,
        $fileName = 'vendor_config.xml'
    ) {
        parent::__construct($fileResolver, $converter, $schemaLocator, $domFactory, $fileName);
    }
}
