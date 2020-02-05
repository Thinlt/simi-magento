<?php

namespace Simi\Simistorelocator\Model\Config\Comment;

abstract class AbstractComment implements \Magento\Config\Model\Config\CommentInterface {

    /**
     * @var \Magento\Framework\UrlInterface
     */
    public $url;

    /**
     * Google constructor.
     *
     * @param \Magento\Framework\UrlInterface $url
     */
    public function __construct(\Magento\Framework\UrlInterface $url) {
        $this->url = $url;
    }

    /**
     * Retrieve element comment by element value.
     *
     * @param string $elementValue
     *
     * @return string
     */
    abstract public function getCommentText($elementValue);
}
