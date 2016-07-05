<?php
/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_Loyalty
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

/**
 * create loyalty table
 */
$installer->run("

DROP TABLE IF EXISTS {$this->getTable('loyalty')};

CREATE TABLE {$this->getTable('loyalty')} (
  `loyalty_id` int(11) unsigned NOT NULL auto_increment,
  `device_id` varchar(255) NULL,
  `pass_type_id` varchar(255) NULL,
  `serial_number` varchar(255) NULL,
  `push_token` varchar(255) NULL,
  PRIMARY KEY (`loyalty_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

$installer->endSetup();
