<?php

namespace Simi\Simicustomize\Model\Config\Backend;

class FooterLogo extends \Magento\Config\Model\Config\Backend\File
{
    /**
     * @return string[]
     */
    public function _getAllowedExtensions()
    {
        return ['png', 'jpg', 'jpeg', 'svg'];
    }
}
