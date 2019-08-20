<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Vendors\Block\Vendors\Widget\Grid;


/**
 * Grid column block
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Serializer extends \Magento\Backend\Block\Widget\Grid\Serializer
{

    /**
     * Set serializer template
     *
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('Vnecoms_Vendors::widget/grid/serializer.phtml');
    }

}
