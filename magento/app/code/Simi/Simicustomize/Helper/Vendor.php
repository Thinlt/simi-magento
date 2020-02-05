<?php

namespace Simi\Simicustomize\Helper;


class Vendor extends \Simi\Simiconnector\Helper\Data
{
    protected function getProfileBlock($vendorId)
    {
        $vendor = $this->simiObjectManager->get('\Vnecoms\Vendors\Model\Vendor')->load($vendorId);
        $registry = $this->simiObjectManager->get('\Magento\Framework\Registry');
        $registry->unregister('vendor');
        $registry->register('vendor', $vendor);
        return $this->simiObjectManager->get('\Magento\Framework\View\LayoutInterface')
            ->createBlock('Vnecoms\Vendors\Block\Profile');
    }

    public function getProfile($vendorId) {
        $profileBlock = $this->getProfileBlock($vendorId);
        return array(
            'logo_width'=> $profileBlock->getLogoWidth(),
            'logo_height'=> $profileBlock->getLogoHeight(),
            'keep_transparency_logo'=> $profileBlock->keepTransparencyLogo(),
            'logo_url'=> $profileBlock->getLogoUrl(),
            'no_logo_url'=> $profileBlock->getNoLogoUrl(),
            'vendor_url'=> $profileBlock->getVendorUrl(),
            'store_name'=> $profileBlock->getStoreName(),
            'description'=> $profileBlock->getStoreDescription(),
            'can_show_vendor_short_description'=> $profileBlock->canShowVendorShortDescription(),
            'can_show_vendor_phone'=> $profileBlock->canShowVendorPhone(),
            'phone_number'=> $profileBlock->getPhoneNumber(),
            'can_show_operation_time'=> $profileBlock->canShowVendorOperationTime(),
            'operation_time'=> $profileBlock->getOperationTime(),
            'country'=> $profileBlock->getCountry(),
            'sales_count'=> $profileBlock->getSalesCount(),
            'joined_date'=> $profileBlock->getJoinedDate(),
            'address'=> $profileBlock->getAddress(),
        );
    }
}
