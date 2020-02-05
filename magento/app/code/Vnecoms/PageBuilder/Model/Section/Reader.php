<?php
namespace Vnecoms\PageBuilder\Model\Section;

use Magento\Framework\DataObject;
use Magento\Framework\View\TemplateEngine\Xhtml\CompilerInterface;

class Reader extends \Magento\Framework\App\Config\Initial\Reader
{
    /**
     * @var CompilerInterface
     */
    protected $compiler;
    
    /**
     * Id attributes
     * 
     * @var array
     */
    protected $idAttributes =[];
    
    /**
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
        CompilerInterface $compiler,
        $fileName = 'vcms_section.xml',
        $idAttributes = []
    ) {
        parent::__construct($fileResolver, $converter, $schemaLocator, $domFactory, $fileName);
        $this->compiler = $compiler;
        $this->idAttributes = $idAttributes;
    }
    
    /**
     * Read configuration scope
     *
     * @return array
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function read()
    {
        $fileList = [];
        foreach ($this->_scopePriorityScheme as $scope) {
            $directories = $this->_fileResolver->get($this->_fileName, $scope);
            foreach ($directories as $key => $directory) {
                $fileList[$key] = $directory;
            }
        }
    
        if (!count($fileList)) {
            return [];
        }
    
        /** @var \Magento\Framework\Config\Dom $domDocument */
        $domDocument = null;
        foreach ($fileList as $key=>$file) {
            try {
                $file = $this->processingDocument($file);
                if (!$domDocument) {
                    $domDocument = $this->domFactory->createDom(['xml' => $file, 'schemaFile' => $this->_schemaFile, 'idAttributes' => $this->idAttributes]);
                } else {
                    $domDocument->merge($file);
                }
            } catch (\Magento\Framework\Config\Dom\ValidationException $e) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    new \Magento\Framework\Phrase("Invalid XML in file %1:\n%2", [$file, $e->getMessage()])
                );
            }
        }

        $output = [];
        if ($domDocument) {
            $output = $this->_converter->convert($domDocument->getDom());
        }
        return $output;
    }
    
    /**
     * Processing nodes of the document before merging
     *
     * @param string $content
     * @return string
     */
    protected function processingDocument($content)
    {
        $object = new DataObject();
        $document = new \DOMDocument();
    
        $document->loadXML($content);
        $this->compiler->compile($document->documentElement, $object, $object);
    
        return $document->saveXML();
    }
}
