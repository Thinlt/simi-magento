<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\VendorsProduct\Block\Vendors\Product\Edit\Button;

use Vnecoms\VendorsProduct\Model\Source\Approval;

/**
 * Class Back
 */
class Approve extends Generic
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $updateCollection = $om->create('Vnecoms\VendorsProduct\Model\ResourceModel\Product\Update\Collection');
        $updateCollection->addFieldToFilter('product_id',$this->getProduct()->getId())
        ->addFieldToFilter('status',\Vnecoms\VendorsProduct\Model\Product\Update::STATUS_PENDING);


        $helperData =  $om->create('Vnecoms\VendorsProduct\Helper\Data');

        if(!$helperData->isUpdateProductsApproval()) return [];

        if( $this->getProduct()->getId() &&
            (
                in_array($this->getProduct()->getApproval(),[Approval::STATUS_NOT_SUBMITED, Approval::STATUS_UNAPPROVED]) ||
                ($updateCollection->count() && $this->getProduct()->getApproval() == Approval::STATUS_APPROVED)
            )
        ) {
            return [
                'label' => __('Submit for Approval'),
                'on_click' => sprintf("location.href = '%s';", $this->getApprovalUrl()),
                'class' => 'fa fa-cloud-upload btn btn-primary',
                'sort_order' => 10
            ];
        }
        
        return [];
    }
    
    /**
     * @return string
     */
    public function getApprovalUrl()
    {
        return $this->getUrl(
            'catalog/product/approve',
            ['id' => $this->getProduct()->getId(),]
        );
    }
}
