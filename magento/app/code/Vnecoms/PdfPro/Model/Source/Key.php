<?php

namespace Vnecoms\PdfPro\Model\Source;

use Magento\Framework\Option\ArrayInterface;
use Vnecoms\PdfPro\Model\ResourceModel\Key\CollectionFactory as CollectionFactory;

/**
 * Class Key.
 */
class Key implements ArrayInterface
{
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;
    /**
     * @var array
     */
    protected $options;

    /**
     * constructor.
     *
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(CollectionFactory $collectionFactory)
    {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * To option array.
     *
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->options) {
            $this->options[] = ['label' => '', 'value' => ''];
            $collection = $this->collectionFactory->create();
            foreach ($collection as $key) {
                $data = ['label' => $key->getApiKey(), 'value' => $key->getId()];
                $this->options[] = $data;
            }
        }

        return $this->options;
    }
}
