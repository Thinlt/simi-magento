<?php

namespace Vnecoms\Vendors\Model;

class AclResult
{
    /**
     * The permission can be set from multiple extension
     * 
     * @var array
     */
    protected $results = [true];
    
    /**
     * Force allow the permission
     * 
     * @var boolean
     */
    protected $allowedFlag = false;
    
    /**
     * Push a permission value
     * 
     * @param boolean $result
     * @return \Vnecoms\Vendors\Model\AclResult
     */
    public function push($result){
        $this->results[] = $result;
        return $this;
    }
    
    /**
     * Is allowed
     * 
     * @return boolean
     */
    public function isAllowed(){
        if($this->allowedFlag) return true;
        
        foreach($this->results as $result){
            if(!$result) return false;
        }
        
        return true;
    }
    
    /**
     * Set is allowed flag
     *  
     * @param boolean $flag
     */
    public function setAllowedFlag($flag){
        $this->allowedFlag = $flag;
    }
}
