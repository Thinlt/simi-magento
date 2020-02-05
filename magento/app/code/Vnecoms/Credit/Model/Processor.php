<?php

namespace Vnecoms\Credit\Model;

use Magento\Framework\Exception\LocalizedException;

class Processor
{
    /**
     * Credit Processors
     * @var array
     */
    protected $_creditProcessors;
    
    
    public function __construct(
        array $creditProcessors=[]
    ) {
       $this->_creditProcessors = $creditProcessors;
    }
    
    /**
     * Get processor by type
     * @param string $type
     * @return \Vnecoms\Credit\Model\Processor\ProcessorInterface
     */
    public function getProcessor($type=null){
        if(!$type) return false;
        
        return isset($this->_creditProcessors[$type])? $this->_creditProcessors[$type]:false;
    }
    
    /**
     * Process transaction
     * @param \Vnecoms\Credit\Model\Credit $creditAccount
     * @param array $data
     * @throws LocalizedException
     */
    public function process(\Vnecoms\Credit\Model\Credit $creditAccount, $data = array()){
        $processor = isset($data['type'])?$this->getProcessor($data['type']):false;
        if(!$processor) throw new LocalizedException('The transaction type is not exist');
        $processor->setCreditAccount($creditAccount)->process($data);
    }
    
    /**
     * Get the list of credit processors
     * @return array:
     */
    public function getProcessors(){
        return $this->_creditProcessors;
    }
}
