<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Vnecoms\Vendors\Setup;

use Vnecoms\Vendors\Model\Vendor;
use Vnecoms\Vendors\Setup\VendorSetupFactory;
use Magento\Eav\Model\Entity\Attribute\Set as AttributeSet;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    private $vendorSetupFactory;
 
    private $attributeSetFactory;
    
    /**
     * Customer collection
     * @var Magento\Customer\Model\ResourceModel\Customer\Collection
     */
    private $customerCollection;
    
    /**
     * Init
     *
     * @param CustomerSetupFactory $customerSetupFactory
     */
    public function __construct(
        VendorSetupFactory $vendorSetupFactory,
        AttributeSetFactory $attributeSetFactory,
        \Magento\Customer\Model\ResourceModel\Customer\Collection $customerCollection
    ) {
        $this->vendorSetupFactory = $vendorSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
        
        $this->customerCollection = $customerCollection;
    }
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        /** @var CustomerSetup $customerSetup */
        $vendorSetup = $this->vendorSetupFactory->create(['setup' => $setup]);
        $setup->startSetup();
        
        /*Create store view for vendors*/
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $storeModel = $om->create('Magento\Store\Model\Store');
        $storeModel->setData([
            'code'          => 'vendors',
            'website_id'    => 0,
            'group_id'      => 0,
            'name'          => 'Vendor Panel',
            'is_active'     => 1
        ]);
        $storeModel->save();
        
        $vendorSetup->installEntities();
        
        
        // insert default vendor group
        $setup->getConnection()->insertForce(
            $setup->getTable('ves_vendor_group'),
            ['vendor_group_id' => 1, 'vendor_group_code' => 'Default']
        );
        
        /*Insert default fieldset and attributes*/
        /*Profile Form*/
        $setup->getConnection()->insertForce(
            $setup->getTable('ves_vendor_fieldset'),
            ['fieldset_id' => 1,'form'=>\Vnecoms\Vendors\Helper\Data::PROFILE_FORM, 'title' => 'General','sort_order'=>10]
        );
        
        $setup->getConnection()->insertForce(
            $setup->getTable('ves_vendor_fieldset'),
            ['fieldset_id' => 2,'form'=>\Vnecoms\Vendors\Helper\Data::PROFILE_FORM, 'title' => 'Test Tab 1','sort_order'=>20]
        );
        $setup->getConnection()->insertForce(
            $setup->getTable('ves_vendor_fieldset'),
            ['fieldset_id' => 3,'form'=>\Vnecoms\Vendors\Helper\Data::PROFILE_FORM, 'title' => 'Test Tab 2','sort_order'=>30]
        );
        
        /*Registration Form*/
        $setup->getConnection()->insertForce(
            $setup->getTable('ves_vendor_fieldset'),
            ['fieldset_id' => 4,'form'=>\Vnecoms\Vendors\Helper\Data::REGISTRATION_FORM, 'title' => 'General','sort_order'=>10]
        );
        
        $setup->getConnection()->insertForce(
            $setup->getTable('ves_vendor_fieldset'),
            ['fieldset_id' => 5,'form'=>\Vnecoms\Vendors\Helper\Data::REGISTRATION_FORM, 'title' => 'Test Tab 1','sort_order'=>20]
        );
        $setup->getConnection()->insertForce(
            $setup->getTable('ves_vendor_fieldset'),
            ['fieldset_id' => 6,'form'=>\Vnecoms\Vendors\Helper\Data::REGISTRATION_FORM, 'title' => 'Test Tab 2','sort_order'=>30]
        );
        
        
        $vendorSetup->addAttribute(
            Vendor::ENTITY,
            'ves_test_attribute1',
            [
                'label' => 'Test Attribute 1',
                'type' => 'varchar',
                'input' => 'text',
                'position' => 145,
                'visible' => true,
                'required' => false,
                'default' => '',
                'user_defined'=>1,
                'system' => 0,
                'used_in_profile_form' => 1,
                'used_in_registration_form' => 1,
                'visible_in_customer_form' => 0,
            ]
        );
        $vendorSetup->addAttribute(
            Vendor::ENTITY,
            'ves_test_attribute2',
            [
                'label' => 'Test Attribute 2',
                'type' => 'varchar',
                'input' => 'multiselect',
                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Table',
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'position' => 146,
                'visible' => true,
                'required' => false,
                'default' => 0,
                'user_defined'=>1,
                'system' => 0,
                'used_in_profile_form' => 1,
                'used_in_registration_form' => 1,
                'visible_in_customer_form' => 0,
                'option'=> ['values'=> ['Test option 1','Test option 2','Test option 3', 'Test option 4']],
            ]
        );
        
        $vendorSetup->addAttribute(
            Vendor::ENTITY,
            'ves_test_attribute3',
            [
                'label' => 'Test Attribute 3',
                'type' => 'int',
                'input' => 'select',
                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Table',
                'position' => 146,
                'visible' => true,
                'required' => false,
                'default' => 0,
                'user_defined'=>1,
                'system' => 0,
                'used_in_profile_form' => 1,
                'used_in_registration_form' => 1,
                'visible_in_customer_form' => 0,
                'option'=> ['values'=> ['Option 1','Option 2','Option 3','Option 4']],
            ]
        );
        $vendorSetup->addAttribute(
            Vendor::ENTITY,
            'ves_test_attribute4',
            [
                'label' => 'Test Attribute 4',
                'type' => 'varchar',
                'input' => 'file',
                'backend' => 'Vnecoms\Vendors\Model\Entity\Attribute\Backend\File',
                'position' => 146,
                'visible' => true,
                'required' => false,
                'default' => 'jpg,png,gif',
                'user_defined'=>1,
                'visible_in_customer_form' => 0,
                'system' => 0,
                'used_in_profile_form' => 1,
                'used_in_registration_form' => 1,
            ]
        );
        
        $attributeSetId = $vendorSetup->getDefaultAttributeSetId(Vendor::ENTITY);
        
        /** @var $attributeSet AttributeSet */
        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);
        
        $attributes = [
            /*Profile Form*/
            1 => [
                'vendor_id',
                'group_id',
                'status',
                'company',
                'street',
                'city',
                'country_id',
                'region',
                'region_id',
                'postcode',
                'telephone',
            ],
            2 => ['ves_test_attribute1','ves_test_attribute2'],
            3 => ['ves_test_attribute3','ves_test_attribute4'],
            
            /*Registration Form*/
            4 => [
                'vendor_id',
                'company',
                'street',
                'city',
                'country_id',
                'region',
                'region_id',
                'postcode',
                'telephone',
            ],
            5 => ['ves_test_attribute1','ves_test_attribute2'],
            6 => ['ves_test_attribute3','ves_test_attribute4'],
        ];
        
        $sortOrder = 1;
        foreach ($attributes as $fieldsetId => $attrs) {
            foreach ($attrs as $attributeCode) {
                $attributeId = $vendorSetup->getAttributeId(Vendor::ENTITY, $attributeCode);
                $setup->getConnection()->insertForce(
                    $setup->getTable('ves_vendor_fieldset_attr'),
                    ['fieldset_id' => $fieldsetId, 'attribute_id' => $attributeId,'sort_order'=>$sortOrder]
                );
                if (!in_array($attributeCode, ['firstname','lastname','email'])) {
                    $attribute = $om->create('Vnecoms\Vendors\Model\Attribute');
                    $attribute->load($attributeId);
                    $attribute->addData([
                        'attribute_set_id' => $attributeSetId,
                        'attribute_group_id' => $attributeGroupId,
                    ])->save();
                }

                $sortOrder++;
            }
        }
        
        $setup->endSetup();
    }
}
