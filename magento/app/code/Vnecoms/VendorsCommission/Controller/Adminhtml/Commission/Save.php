<?php
namespace Vnecoms\VendorsCommission\Controller\Adminhtml\Commission;

use Vnecoms\Vendors\Controller\Adminhtml\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\Filter\Date;
use Magento\Framework\Exception\LocalizedException;

class Save extends Action
{

    /**
     * Is access to section allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return parent::_isAllowed() &&
            $this->_authorization->isAllowed(
                'Vnecoms_VendorsCommission::commission_configuration'
            );
    }
    
    /**
     * @return void
     */
    public function execute()
    {
        if ($this->getRequest()->getPostValue()) {
            try {
                /** @var \Magento\CatalogRule\Model\Rule $model */
                $model = $this->_objectManager->create(
                    'Vnecoms\VendorsCommission\Model\Rule'
                );
                $this->_eventManager->dispatch(
                    'adminhtml_controller_vendor_commission_prepare_save',
                    ['request' => $this->getRequest()]
                );
                $data = $this->getRequest()->getParams();

                if (isset($data['website_ids'])) {
                    $data['website_ids'] = implode(",", $data['website_ids']);
                }
                if (isset($data['vendor_group_ids'])) {
                    $data['vendor_group_ids'] = implode(
                        ",",
                        $data['vendor_group_ids']
                    );
                }

                $id = $this->getRequest()->getParam('id');
                if ($id) {
                    $model->load($id);
                    if ($id != $model->getId()) {
                        throw new LocalizedException(__('Wrong rule specified.'));
                    }
                }
                $tmpData = ['conditions'=>$data['rule']['conditions']];
                $tmpRule = $this->_objectManager->create(
                    'Vnecoms\VendorsCommission\Model\TmpRule'
                );
                $tmpRule->loadPost($tmpData);
                
                $data['condition_serialized'] = $tmpRule->getSerializedConditions();
                
                unset($data['rule']);

                $model->addData($data);
        
                $this->_objectManager->get('Magento\Backend\Model\Session')
                    ->setPageData($model->getData());
                $model->save();
                
                $this->messageManager->addSuccess(__('You saved the commission rule.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $this->_redirect(
                        'vendors/commission/edit',
                        ['id' => $model->getId()]
                    );
                }
                return $this->_redirect('vendors/commission/');
            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_redirect('vendors/*/edit', [
                    'id' => $this->getRequest()->getParam('rule_id')
                ]);
            } catch (\Exception $e) {
                $this->messageManager->addError(
                    $e->getMessage()
                );
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData($data);
                $this->_redirect('vendors/*/edit', ['id' => $this->getRequest()->getParam('id')]);
                return;
            }
        }
    }
}
