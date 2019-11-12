<?php

namespace Simi\Simistorelocator\Plugin\Config\Structure\Element;

class Field {

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    public $objectManager;

    /**
     * mapping path for field comment.
     *
     * @var array
     */
    public $mapPathFieldComments = [
        'simistorelocator/service/google_api_key' => 'Simi\Simistorelocator\Model\Config\Comment\Google',
        'simistorelocator/service/facebook_api_key' => 'Simi\Simistorelocator\Model\Config\Comment\Facebook',
    ];

    /**
     * Field constructor.
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager) {
        $this->objectManager = $objectManager;
    }

    /**
     * Retrieve comment.
     *
     * @param string $currentValue
     *
     * @return string
     */
    public function aroundGetComment(
    \Magento\Config\Model\Config\Structure\Element\Field $field, \Closure $proceed, $currentValue = ''
    ) {
        if (isset($this->mapPathFieldComments[$field->getPath()])) {
            /** @var \Magento\Config\Model\Config\CommentInterface $commentModel */
            $commentModel = $this->objectManager->create($this->mapPathFieldComments[$field->getPath()]);

            return $commentModel->getCommentText($currentValue);
        }

        return $proceed($currentValue);
    }
}
