<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsNotification\Model\Type;

interface TypeInterface
{
    /**
     * Get Icon Class
     *
     * @return string
     */
    public function getIconClass();

    /**
     * Get notification message
     *
     * @return string
     */
    public function getMessage();
    
    /**
     * Get notification URL
     *
     * @return string
     */
    public function getUrl();
}
