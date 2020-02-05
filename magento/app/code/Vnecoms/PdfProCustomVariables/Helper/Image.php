<?php
/**
 * Created by PhpStorm.
 * User: mrtuvn
 * Date: 25/01/2017
 * Time: 10:00
 */

namespace Vnecoms\PdfProCustomVariables\Helper;

class Image extends \Magento\Catalog\Helper\Image
{
    /**
     * Get current Image model
     *
     * @return \Magento\Catalog\Model\Product\Image
     */
    public function getModel()
    {
        return $this->_model;
    }
}
