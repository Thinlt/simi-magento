<?php

namespace Simi\Simistorelocator\Model;

class Factory
{
    /**#@+
     * Allowed object types
     */
    const MODEL_SPECIALDAY = 'specialday';
    const MODEL_HOLIDAY = 'holiday';
    const MODEL_TAG = 'tag';
    const MODEL_SCHEDULE = 'schedule';

    /**
     * Map of types which are references to classes.
     *
     * @var array
     */
    public $typeMap = [
        self::MODEL_SPECIALDAY => 'Simi\Simistorelocator\Model\Specialday',
        self::MODEL_HOLIDAY => 'Simi\Simistorelocator\Model\Holiday',
        self::MODEL_TAG => 'Simi\Simistorelocator\Model\Tag',
        self::MODEL_SCHEDULE => 'Simi\Simistorelocator\Model\Schedule',
    ];

    /**
     * @var ObjectManagerInterface
     */
    public $objectManager;

    /**
     * Constructor.
     *
     * @param ObjectManagerInterface $objectManager
     * @param array                  $typeMap
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $typeMap = []
    ) {
        $this->objectManager = $objectManager;
        $this->mergeTypes($typeMap);
    }

    /**
     * Add or override object types.
     *
     * @param array $typeMap
     */
    protected function mergeTypes(array $typeMap)
    {
        foreach ($typeMap as $typeInfo) {
            if (isset($typeInfo['type']) && isset($typeInfo['class'])) {
                $this->typeMap[$typeInfo['type']] = $typeInfo['class'];
            }
        }
    }

    /**
     * @param $type
     * @param array $arguments
     *
     * @return mixed
     */
    public function create($type, array $arguments = [])
    {
        if (empty($this->typeMap[$type])) {
            throw new \InvalidArgumentException('"' . $type . ': isn\'t allowed');
        }

        $instance = $this->objectManager->create($this->typeMap[$type], $arguments);
        if (!$instance instanceof \Simi\Simistorelocator\Model\AbstractModelManageStores) {
            throw new \InvalidArgumentException(
                get_class($instance)
                . ' isn\'t instance of \Simi\Simistorelocator\Model\AbstractModelManageStores'
            );
        }

        return $instance;
    }
}
