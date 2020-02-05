<?php

namespace Vnecoms\PdfPro\Model\Modifier;

use Magento\Framework\App\ObjectManager;
class Pdf implements \Vnecoms\PageBuilder\Model\Modifier\ModifierInterface
{    
    /**
     * @var \Vnecoms\PdfPro\Model\ResourceModel\Variable\CollectionFactory
     */
    protected $variableCollectionFactory;
    
    /**
     * @var \Magento\Framework\Event\Manager
     */
    protected $event;
    
    /**
     * @param \Vnecoms\PdfPro\Model\ResourceModel\Variable\CollectionFactory $variableCollectionFactory
     * @param \Magento\Framework\Event\Manager $event
     */
    public function __construct(
        \Vnecoms\PdfPro\Model\ResourceModel\Variable\CollectionFactory $variableCollectionFactory,
        \Magento\Framework\Event\Manager $event
    ) {
        $this->variableCollectionFactory = $variableCollectionFactory;
        $this->event = $event;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Vnecoms\PageBuilder\Model\Modifier\ModifierInterface::process()
     */
    public function process($data = []){
        if(
            !isset($data['config']['pbResource']) ||
            !in_array('pdf_pro', $data['config']['pbResource'])
        ) {
            return $data;
        }
        $data['config']['fieldsData']['editor']['edit_template'] = 'Vnecoms_PdfPro/field/edit/editor.html';
        $data['config']['fieldsData']['editor']['isCodeEditor'] = 1;        
        
        if(in_array('pdf_pro_order', $data['config']['pbResource'])){
            $variableCategoriesToLoad = [
                'customer',
                'billing_address',
                'shipping_address',
                'payment_information',
                'order',
                'order_item'
            ];
            
        }elseif(in_array('pdf_pro_invoice', $data['config']['pbResource'])){
            $variableCategoriesToLoad = [
                'customer',
                'billing_address',
                'shipping_address',
                'payment_information',
                'order',
                'invoice',
                'invoice_item'
            ];
        }elseif(in_array('pdf_pro_shipment', $data['config']['pbResource'])){
            $variableCategoriesToLoad = [
                'customer',
                'billing_address',
                'shipping_address',
                'payment_information',
                'order',
                'shipment',
                'shipment_item'
            ];
        }elseif(in_array('pdf_pro_creditmemo', $data['config']['pbResource'])){
            $variableCategoriesToLoad = [
                'customer',
                'billing_address',
                'shipping_address',
                'payment_information',
                'order',
                'creditmemo',
                'creditmemo_item'
            ];
        }
        
        $data['config']['pdfVariables'] = $this->loadVariables($variableCategoriesToLoad);
        $om = ObjectManager::getInstance();
        $assetRepo = $om->get('Magento\Framework\View\Asset\Repository');
        $data['config']['productThumbnail'] = $assetRepo->getUrlWithParams('Vnecoms_PdfPro::images/product_thumbnail.png',[]);
        return $data;
    }
    
    /**
     * Load variables by categories
     * 
     * @param unknown $variableCategoriesToLoad
     * @return multitype:
     */
    public function loadVariables($variableCategoriesToLoad = []){
        $variables = [];
        $variableCollection = $this->variableCollectionFactory->create();
        $variableCollection->getSelect()
            ->join([
                'category' => $variableCollection->getTable('ves_pdfpro_category')],
                'main_table.category_id = category.category_id',
                [
                    'category_code' => 'code',
                    'category_title' => 'title',
                    'category_sort_order' => 'sort_order',
                ]
            )->where('category.code in (?)', $variableCategoriesToLoad);
        
        foreach($variableCollection as $variable){
            $code = $variable->getCategoryCode();
            if(!isset($variables[$code])){
                $variables[$code] = [
                    'code' => $code,
                    'title' => $variable->getCategoryTitle(),
                    'sortOrder' => $variable->getCategorySortOrder(),
                    'variables' => [],
                ];
            }
            $variables[$code]['variables'][] = $variable->getData();
        }
        $transport = new \Magento\Framework\DataObject([
            'variables' => $variables
        ]);
        
        $this->event->dispatch('pdfpro_modifier_variable_after',['transport' => $transport]);
        
        $variables = $transport->getVariables();
        
        usort($variables, array($this,'usort'));
        return $variables;
    }
    
    /**
     * sort two object
     *
     * @param array $a
     * @param array $b
     * @return number
     */
    protected function usort($a, $b){
        $aSort = isset($a['sortOrder'])?$a['sortOrder']:0;
        $bSort = isset($b['sortOrder'])?$b['sortOrder']:0;
    
        if ($aSort == $bSort) {
            return 0;
        }
        return ($aSort < $bSort) ? -1 : 1;
    }
}
