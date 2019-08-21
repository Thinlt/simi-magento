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

    public function index() {
        $result = parent::index();
        $vendorHelper = $this->simiObjectManager->get('\Simi\Simicustomize\Helper\Vendor');
        if (isset($result['vendors'])) {
            foreach ($result['vendors'] as $index=>$vendor) {
                $vendor['profile'] = $vendorHelper->getProfile($vendor['entity_id']);
                $result['vendors'][$index] = $vendor;
            }
        }
        return $result;
    }
}
