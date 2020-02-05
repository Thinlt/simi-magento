<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Vnecoms\PdfPro\Component\Template\Form\Element\Wysiwyg;

use Magento\Ui\Component\Wysiwyg\ConfigInterface;

/**
 * Class Config.
 */
class Config implements ConfigInterface
{
    /**
     * Return WYSIWYG configuration.
     *
     * @return \Magento\Framework\DataObject
     */
    public function getConfig()
    {
        $data = [];
        $data['widget_window_url'] = 'test';

        return $data;
    }
}
