<?php
namespace Vnecoms\PageBuilder\Model\Component;

class Converter implements \Magento\Framework\Config\ConverterInterface
{
    /**
     * Node paths to process
     *
     * @var array
     */
    protected $_nodeMap = [];

    /**
     * @var array
     */
    protected $_metadata = [];

    /**
     * @param array $nodeMap
     */
    public function __construct(array $nodeMap = [])
    {
        $this->_nodeMap = $nodeMap;
    }

    /**
     * Convert config data
     *
     * @param \DOMDocument $source
     * @return array
     */
    public function convert($source)
    {
        $output = [];
        $xpath = new \DOMXPath($source);
        $this->_metadata = [];

        /** @var $node \DOMNode */
        foreach ($xpath->query(implode(' | ', $this->_nodeMap)) as $node) {
            $output = array_merge($output, $this->_convertNode($node));
        }
        return ['data' => $output, 'metadata' => $this->_metadata];
    }

    /**
     * Convert node oto array
     *
     * @param \DOMNode $node
     * @param string $path
     * @return array|string|null
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function _convertNode(\DOMNode $node, $path = '')
    {
        $output = [];
        if ($node->nodeType == XML_ELEMENT_NODE) {
            $nodeData = [];
            
            /** @var $childNode \DOMNode */
            foreach ($node->childNodes as $childNode) {
                $childrenData = $this->_convertNode($childNode, ($path ? $path . '/' : '') . $childNode->nodeName);
                if ($childrenData == null) {
                    continue;
                }
                if (is_array($childrenData)) {
                    $nodeData = array_merge($nodeData, $childrenData);
                } else {
                    $nodeData = $childrenData;
                }
            }
            if (is_array($nodeData) && empty($nodeData)) {
                $nodeData = null;
            }
            $nodeName = $node->nodeName;
            if($node->hasAttributes()){
                if($node->attributes->getNamedItem('name')){
                    $nodeName = $node->attributes->getNamedItem('name')->value;
                }
                if($sortOrder = $node->attributes->getNamedItem('sortOrder')){
                    $nodeData['sortOrder'] = (int)$sortOrder->value;
                }
                $nodeData['id'] = $nodeName;
            }
            $output[$nodeName] = $nodeData;
        } elseif ($node->nodeType == XML_CDATA_SECTION_NODE || $node->nodeType == XML_TEXT_NODE && trim(
            $node->nodeValue
        ) != ''
        ) {
            return $node->nodeValue;
        }

        return $output;
    }
}
