<?php
/**
 * Initial configuration data container. Provides interface for reading initial config values
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsConfig\App\Config;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Serialize\SerializerInterface;

class Initial
{
    /**
     * Cache identifier used to store initial config
     */
    const CACHE_ID = 'vendor_initial_config';

    /**
     * Config data
     *
     * @var array
     */
    protected $_data = [];

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * Config metadata
     *
     * @var array
     */
    protected $_metadata = [];

    /**
     * @param \Magento\Framework\App\Config\Initial\Reader $reader
     * @param \Magento\Framework\App\Cache\Type\Config $cache
     * @param SerializerInterface|null $serializer
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function __construct(
        \Vnecoms\VendorsConfig\App\Config\Initial\Reader $reader,
        \Magento\Framework\App\Cache\Type\Config $cache,
        SerializerInterface $serializer = null
    ) {
        $this->serializer = $serializer ?: ObjectManager::getInstance()->get(SerializerInterface::class);
        $data = $cache->load(self::CACHE_ID);
        $isNotCached = !$data;

        $data = $isNotCached ? $reader->read() : $this->serializer->unserialize($data);

        if ($isNotCached) {
            $cache->save($this->serializer->serialize($data), self::CACHE_ID);
        }

        $this->_data = $data['data'];
        $this->_metadata = $data['metadata'];
    }

    /**
     * Get initial data by given scope
     *
     * @param string $scope Format is scope type and scope code separated by pipe: e.g. "type|code"
     * @return array
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * Get configuration metadata
     *
     * @return array
     */
    public function getMetadata()
    {
        return $this->_metadata;
    }
}
