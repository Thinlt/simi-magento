<?php

namespace Vnecoms\VendorsProfileNotification\Model\Type;

use Vnecoms\VendorsProfileNotification\Model\Process;
use Vnecoms\Vendors\Model\Vendor;
use Magento\Framework\Data\Form;
use Vnecoms\Vendors\Model\UrlInterface;

abstract class AbstractType implements TypeInterface
{    
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;
    
    /**
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        UrlInterface $urlBuilder
    ) {
        $this->urlBuilder = $urlBuilder;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Vnecoms\VendorsProfileNotification\Model\Type\TypeInterface::getTitle()
     */
    abstract public function getTitle();
    
    /**
     * (non-PHPdoc)
     * @see \Vnecoms\VendorsProfileNotification\Model\Type\TypeInterface::prepareForm()
     */
    abstract public function prepareForm(
        Form $form,
        Process $process
    );
    
    /**
     * (non-PHPdoc)
     * @see \Vnecoms\VendorsProfileNotification\Model\Type\TypeInterface::beforeSaveProcess()
     */
    abstract public function beforeSaveProcess(Process $process);
    
    /**
     * (non-PHPdoc)
     * @see \Vnecoms\VendorsProfileNotification\Model\Type\TypeInterface::afterLoadProcess()
     */
    abstract public function afterLoadProcess(Process $process);
    
    /**
     * (non-PHPdoc)
     * @see \Vnecoms\VendorsProfileNotification\Model\Type\TypeInterface::isCompletedProcess()
     */
    abstract public function isCompletedProcess(Process $process, Vendor $vendor);
    
    /**
     * (non-PHPdoc)
     * @see \Vnecoms\VendorsProfileNotification\Model\Type\TypeInterface::getUrl()
     */
    abstract public function getUrl(Process $process);
}
