<?php

namespace Simi\Simistorelocator\Model\Config\Comment;

class Google extends \Simi\Simistorelocator\Model\Config\Comment\AbstractComment {

    /**
     * Retrieve element comment by element value.
     *
     * @param string $elementValue
     *
     * @return string
     */
    public function getCommentText($elementValue) {
        return __(
                'To register a Google Map API key, please follow the guide <a href="%1">here</a>',
                $this->url->getUrl('simistorelocatoradmin/guide')
        );
    }

}
