<?php

namespace Vnecoms\VendorsProfileNotification\Model\Type;

use Vnecoms\VendorsProfileNotification\Model\Process;
use Vnecoms\Vendors\Model\Vendor;
use Magento\Framework\Data\Form;
use Vnecoms\Vendors\Model\ResourceModel\Attribute\CollectionFactory;
use Vnecoms\Vendors\Model\UrlInterface;

class Attribute extends AbstractType
{
    const CODE = 'type_attribute';
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    
    /**
     * @param UrlInterface $urlBuilder
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        UrlInterface $urlBuilder,
        CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
        parent::__construct($urlBuilder);
    }
    /**
     * (non-PHPdoc)
     * @see \Vnecoms\VendorsProfileNotification\Model\Type\AbstractType::getTitle()
     */
    public function getTitle(){
        return __('Vendor Attribute');
    }
    
    /**
     * (non-PHPdoc)
     * @see \Vnecoms\VendorsProfileNotification\Model\Type\AbstractType::prepareForm()
     */
    public function prepareForm(
        Form $form,
        Process $process
    ){
        $collection = $this->collectionFactory->create()->addVisibleFilter();
        $options = [];
        foreach($collection as $attribute){
            $options[] = [
                'value' => $attribute->getAttributeCode(),
                'label' => $attribute->getFrontendLabel(),
            ];
        }
        $fieldset = $form->getElement('base_fieldset');
        $fieldset->addField(
            'attribute',
            'multiselect',
            [
                'name' => 'attribute',
                'label' => __('Attribute'),
                'title' => __('Attribute'),
                'values' => $options,
                'class' => 'process_type_field '.self::CODE,
                'note' => __('Vendor have to set value for these attributes to complete this profile process.'),
                'required' => true
            ],
            'type'
        );
    }
    
    /**
     * (non-PHPdoc)
     * @see \Vnecoms\VendorsProfileNotification\Model\Type\AbstractType::beforeSaveProcess()
     */
    public function beforeSaveProcess(
        Process $process
    ){
        $attributes = $process->getData('attribute');
        if(!$attributes) return;
        $process->setData('additional_data', implode("|", $attributes));
    }
    
    /**
     * (non-PHPdoc)
     * @see \Vnecoms\VendorsProfileNotification\Model\Type\AbstractType::afterLoadProcess()
     */
    public function afterLoadProcess(
        Process $process
    ){
        $additionalData = $process->getData('additional_data');
        $process->setData('attribute', explode("|", $additionalData));
    }
    
    /**
     * (non-PHPdoc)
     * @see \Vnecoms\VendorsProfileNotification\Model\Type\AbstractType::isCompletedProcess()
     */
    public function isCompletedProcess(Process $process, Vendor $vendor){
        $attributes = explode("|",$process->getAdditionalData());
        foreach($attributes as $attributeCode){
            if(!$vendor->getData($attributeCode)) return false;
        }
        
        return true;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Vnecoms\VendorsProfileNotification\Model\Type\AbstractType::getUrl()
     */
    public function getUrl(Process $process){
        return $this->urlBuilder->getUrl('account');
    }
}
