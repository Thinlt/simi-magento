<?php
namespace Vnecoms\PdfProCustomVariables\Observer;

use \Magento\Framework\Event\Observer;
use Vnecoms\PdfProCustomVariables\Model\PdfproCustomVariables;
use Vnecoms\PdfProCustomVariables\Model\ResourceModel\PdfproCustomVariables\CollectionFactory;

class Variables implements \Magento\Framework\Event\ObserverInterface
{
    /** @var \Vnecoms\PdfProCustomVariables\Model\PdfproCustomVariablesFactory  */
    protected $customVariablesFactory;
    
    /**
     * @param CollectionFactory $customVariablesFactory
     */
    public function __construct(
        CollectionFactory $customVariablesFactory
    ) {
    
        $this->customVariablesFactory = $customVariablesFactory;
    }
    
    public function execute(Observer $observer)
    {
        $pdfVariables = $observer->getTransport()->getVariables();
        
        $sortOrder = 100;
        $customAttributes = $this->customVariablesFactory->create();
        foreach ($customAttributes as $attribute) {
            $code = $attribute->getVariableType();
            if(!isset($pdfVariables[$code])){
                $categoryTitle = ($code == 'attribute'?__("Product Attributes")->render():__("Unknow")->render());
                $pdfVariables[$code] = [
                    'code' => $code,
                    'title' => $categoryTitle,
                    'sortOrder' => $sortOrder++,
                    'variables' => [],
                ];
            }
            $codes = $this->getCode($attribute);
            foreach($codes as $pdfCode){
                $pdfVariables[$code]['variables'][] = $pdfCode;
            }
        }
        
        $observer->getTransport()->setVariables($pdfVariables);
    }
    
    /**
     * Get code
     *
     * @param PdfproCustomVariables $variable
     * @return multitype:
     */
    public function getCode(PdfproCustomVariables $variable){
        $codes = [];
        $codeTitle = '';
        switch ($variable->getVariableType()){
            case PdfproCustomVariables::VARIABLE_TYPE_PRODUCT:
                $attributeInfo = $variable->getAttributeInfo();
                $baseVariable = 'item.product.';
                $codeTitle = __('Product - ');
                break;
            case PdfproCustomVariables::VARIABLE_TYPE_CUSTOMER:
                $baseVariable = 'customer.';
                $attributeInfo = $variable->getAttributeInfo($variable->getAttributeIdCustomer());
                break;
            default:
                $attributeInfo = $variable->getAttributeInfo();
                $baseVariable = 'item.product.';
        }
        $codeTitle .= $attributeInfo['frontend_label'];
        if($variable->checkAttributeFrontendType('date')){
            $codes = [
                [
                    'title' => $codeTitle .' - Full',
                    'code' => "{{var ".$baseVariable.$variable->getName().'.full}}',
                    'sort_order' => $variable->getId()
                ],
                [
                    'title' => $codeTitle .' - Long',
                    'code' => "{{var ".$baseVariable.$variable->getName().'.long}}',
                    'sort_order' => $variable->getId()
                ],
                [
                    'title' => $codeTitle .' - Medium',
                    'code' => "{{var ".$baseVariable.$variable->getName().'.medium}}',
                    'sort_order' => $variable->getId()
                ],
                [
                    'title' => $codeTitle .' - Short',
                    'code' => "{{var ".$baseVariable.$variable->getName().'.short}}',
                    'sort_order' => $variable->getId()
                ],
            ];
        }elseif($variable->checkAttributeFrontendType('media_image')){
            $codes = [
                [
                    'title' => $codeTitle .' (Image)',
                    'code' => '<img src="{{var '.$baseVariable.$variable->getName().'}}" width="50" height="50" />',
                    'sort_order' => $variable->getId()
                ],
            ];
        }else{
            $codes = [
                [
                    'title' => $codeTitle,
                    'code' => '{{var '.$baseVariable.$variable->getName().'}}',
                    'sort_order' => $variable->getId()
                ],
            ];
        }
    
        return $codes;
    }
}
