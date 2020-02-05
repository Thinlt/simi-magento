<?php
/**
 * Copyright © 2016 Ubertheme.com All rights reserved.
 */

namespace Vnecoms\PdfProCustomVariables\Setup;

use Magento\Framework\Setup\UninstallInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class Uninstall implements UninstallInterface
{
    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        //uninstall code, drop related tables
        $installer->getConnection()->dropTable($installer->getTable('ves_pdfprocustomvariables_customvariables'));

        $installer->endSetup();
    }
}
