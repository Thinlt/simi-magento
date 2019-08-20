<?php

namespace Vnecoms\VendorsProfileNotification\Model\Type;

use Vnecoms\VendorsProfileNotification\Model\Process;
use Vnecoms\Vendors\Model\Vendor;
use Magento\Framework\Data\Form;

interface TypeInterface 
{
    /**
     * Get title of process
     * 
     * @return string
     */
    public function getTitle();
    
    /**
     * Prepare the process edit form to add additional field.
     * 
     * @param Form $form
     * @param Process $process
     */
    public function prepareForm(
        Form $form,
        Process $process
    );
    
    /**
     * Update data of process before save
     * 
     * @param Process $process
     */
    public function beforeSaveProcess(Process $process);
    
    /**
     * Update data of process after load
     *
     * @param Process $process
     */
    public function afterLoadProcess(Process $process);
    
    /**
     * Is complted process
     * 
     * @param Process $process
     * @param Vendor $vendor
     * @return boolean
     */
    public function isCompletedProcess(Process $process, Vendor $vendor);
    
    /**
     * Get process URL
     * 
     * @param Process $process
     * @return string
     */
    public function getUrl(Process $process);
    
}
