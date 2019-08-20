<?php
namespace Simi\Simicustomize\Model\Api;

class Vendors extends \Simi\Simiconnector\Model\Api\Apiabstract
{
    public function setBuilderQuery()
    {
        $data = $this->getData();
        if ($data['resourceid']) {
            $this->builderQuery = $this->simiObjectManager
                ->get('\Vnecoms\Vendors\Model\Vendor')->load($data['resourceid']);
        } else {
            $this->builderQuery = $this->simiObjectManager
                ->get('\Vnecoms\Vendors\Model\Vendor')
                ->getCollection()
                ->addAttributeToSelect('*')
            ;
        }
    }
}
