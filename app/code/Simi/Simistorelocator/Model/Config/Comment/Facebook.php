<?php

namespace Simi\Simistorelocator\Model\Config\Comment;

class Facebook extends \Simi\Simistorelocator\Model\Config\Comment\AbstractComment {

    /**
     * Retrieve element comment by element value.
     *
     * @param string $elementValue
     *
     * @return string
     */
    public function getCommentText($elementValue) {
        return __(
                'To register a Facebook API key, please follow the guide <a href="%1">here</a>',
                $this->url->getUrl('simistorelocatoradmin/guide')
        );
    }

}
