<?php
namespace Vnecoms\PdfProCustomVariables\Observer\Adminhtml;

use Magento\Framework\Event\ObserverInterface;

class SetItemAttributeAfter implements ObserverInterface
{

    /** @var \Magento\Eav\Model\Entity\Attribute\Set  */
    protected $eavAttributeSet;

    /** @var \Vnecoms\PdfProCustomVariables\Model\PdfproCustomVariablesFactory  */
    protected $customVariablesFactory;

    public function __construct(
        \Magento\Eav\Model\Entity\Attribute\Set $eavAttributeSet,
        \Vnecoms\PdfProCustomVariables\Model\ResourceModel\PdfproCustomVariables\CollectionFactory $customVariablesFactory
    ) {

        $this->eavAttributeSet = $eavAttributeSet;
        $this->customVariablesFactory = $customVariablesFactory;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $groups = $observer->getAttributes();
        $custom_attributes = $this->customVariablesFactory->create()->addFieldToFilter('variable_type','attribute');

        $attributes = [];
        foreach ($custom_attributes as $_attribute) {
            $_attribute_id = $_attribute->getAttributeId();
            $model = $this->eavAttributeSet->load($_attribute_id);

            $attributes[] = ['title' => $_attribute->getName() , 'code' => 'ves_'.$_attribute->getName()]; //not $model->getAttributeCode()
            //because items in VES_AdvancedPdfProcessor_Model_Template_Filter line 353
            // has product attribute
        }

       // $attributes[] = ['title' => '---Custom' , 'code' => 'ves_custom'];
        $observer->getAttributes()->setData('custom', ['label'=>'Custom Attributes','value' => $attributes]);
    }
}
