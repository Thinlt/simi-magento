<?php
namespace Vnecoms\PageBuilder\Model\Section;

class Loader
{
    /**
     * Cache identifier used to store initial config
     */
    const CACHE_ID = 'vnecoms_pagebuilder_section';

    /**
     * Config data
     *
     * @var array
     */
    protected $_data = [];

    /**
     * Config metadata
     *
     * @var array
     */
    protected $_metadata = [];

    /**
     * @param \Vnecoms\PageBuilder\Model\Component\Reader $reader
     * @param \Magento\Framework\App\Cache\Type\Config $cache
     */
    public function __construct(
        \Vnecoms\PageBuilder\Model\Section\Reader $reader,
        \Magento\Framework\App\Cache\Type\Config $cache
    ) {
        $data = $cache->load(self::CACHE_ID);
        if (!$data) {
            $data = $reader->read();
            $cache->save(serialize($data), self::CACHE_ID);
        } else {
            $data = unserialize($data);
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
