<?php
namespace Magecomp\Mobilelogin\Setup;
 
use Magento\Customer\Model\Customer;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
 
class InstallData implements InstallDataInterface
{
    private $customerSetupFactory;
 
    public function __construct(\Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory)
    {
        $this->customerSetupFactory = $customerSetupFactory;
    }
 
  
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
		$customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);
        $customerSetup->addAttribute(
            Customer::ENTITY,
            'mobilenumber',
            [
            'type' => 'varchar',
            'label' => 'mobilenumber',
            'input' => 'text',
            'class' => '',
            'source' => '',
            'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
            'required' => false,
            'sort_order' => 100,
            'system' => false,
            'visible' => true,
            'required' => false,
            'user_defined' => false,
            'searchable' => true,
            'filterable' => true,
            'comparable' => true,
            'visible_on_front' => true,
            'unique' => false,
            'apply_to' => ''
            ]
        );
 
        // add attribute to form
        /** @var  $attribute */
        $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'mobilenumber');
		
		$used_in_forms[]="adminhtml_customer";
		$used_in_forms[]="checkout_register";
		$used_in_forms[]="customer_account_create";
		$used_in_forms[]="customer_account_edit";
		$used_in_forms[]="adminhtml_checkout";
		
        $attribute->setData('used_in_forms', $used_in_forms)
				->setData("is_used_for_customer_segment", true)
				->setData("is_system", 0)
				->setData("is_user_defined", 1)
				->setData("is_visible", 1)
				->setData("sort_order", 100);
        $attribute->save();
 
		
		  $sms_register_otp = $setup->getConnection()->newTable(
            $setup->getTable('sms_register_otp'))
                ->addColumn(
                        'id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true), 'Code ID'
                )->addColumn(
                        'random_code', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, array(), 'Random Code'
                )
                ->addColumn(
                        'created_time', \Magento\Framework\DB\Ddl\Table::TYPE_DATE, 255, array(), 'Created Time'
                )
                ->addColumn(
                        'mobile', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, array(), 'Mobile Number'
                )
				 ->addColumn(
                        'is_verify', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 255, array(), 'Is verify'
                );
           $setup->getConnection()->createTable($sms_register_otp);
		   
		   
		   $sms_login_otp = $setup->getConnection()->newTable(
            $setup->getTable('sms_login_otp'))
                ->addColumn(
                        'id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true), 'Code ID'
                )->addColumn(
                        'random_code', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, array(), 'Random Code'
                )
                ->addColumn(
                        'created_time', \Magento\Framework\DB\Ddl\Table::TYPE_DATE, 255, array(), 'Created Time'
                )
                ->addColumn(
                        'mobile', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, array(), 'Mobile Number'
                )
				 ->addColumn(
                        'is_verify', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 255, array(), 'Is verify'
                );
           $setup->getConnection()->createTable($sms_login_otp);
		   
		   
		    $sms_forgot_otp = $setup->getConnection()->newTable(
            $setup->getTable('sms_forgot_otp'))
                ->addColumn(
                        'id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true), 'Code ID'
                )
                ->addColumn(
                        'created_time', \Magento\Framework\DB\Ddl\Table::TYPE_DATE, 255, array(), 'Created Time'
                )->addColumn(
                        'mobile', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, array(), 'Mobile Number'
                )->addColumn(
                        'email', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, array(), 'Customer Email'
                )
				->addColumn(
                        'ipaddress', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, array(), 'IP Address'
                )
				->addColumn(
                        'random_code', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, array(), 'Random Code'
                )
                
				 ->addColumn(
                        'is_verify', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 255, array(), 'Is verify'
                );
           $setup->getConnection()->createTable($sms_forgot_otp);
        $setup->endSetup();
    }
}