<?php

namespace Vnecoms\PageBuilder\Model\Section\Compiler;

use Magento\Framework\Module\Dir;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class IncludeElement
 */
class IncludeElement extends \Magento\Config\Model\Config\Compiler\IncludeElement
{

    /**
     * Get content of include file (in adminhtml area)
     *
     * @param string $includePath
     * @return string
     * @throws LocalizedException
     */
    protected function getContent($includePath)
    {
        // <include path="Magento_Payment::my_payment.xml" />
        list($moduleName, $filename) = explode('::', $includePath);

        $path = $filename;
        $directoryRead = $this->readFactory->create(
            $this->moduleReader->getModuleDir(Dir::MODULE_ETC_DIR, $moduleName)
        );

        if ($directoryRead->isExist($path) && $directoryRead->isFile($path)) {
            return $directoryRead->readFile($path);
        }

        throw new LocalizedException(__('The file "%1" does not exist', $path));
    }
}
