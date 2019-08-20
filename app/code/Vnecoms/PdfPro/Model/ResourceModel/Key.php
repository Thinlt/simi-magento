<?php

namespace Vnecoms\PdfPro\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Key.
 */
class Key extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('ves_pdfpro_key', 'entity_id');
    }

    /**
     * Process page data before saving.
     *
     * @param AbstractModel $object
     * @return $this
     * @throws LocalizedException
     */
    protected function _beforeSave(AbstractModel $object)
    {
        if (!$this->isValidTemplateIdentifier($object)) {
            throw new LocalizedException(
                __('The Identifier contains capital letters or disallowed symbols.')
            );
        }

        /*
         * check unique template identifier
         */
        if (!$this->IsUniqueIdentifierTemplate($object)) {
            throw new LocalizedException(
                __('The identifier must be changed.It can\'t same with others template identifier\'s')
            );
        }

        return parent::_beforeSave($object);
    }

    /**
     *  Check whether identifier is valid.
     *
     * @param AbstractModel $object
     *
     * @return bool
     */
    protected function isValidTemplateIdentifier(AbstractModel $object)
    {
        return preg_match('/^[a-z0-9][a-z0-9_\/-]+(\.[a-z0-9_-]+)?$/', $object->getData('api_key'));
    }

    /**
     * Check for unique of identifier of api key for template
     * @param AbstractModel $object
     * @return bool
     */
    public function IsUniqueIdentifierTemplate(AbstractModel $object)
    {
        $select = $this->getConnection()->select()
            ->from(['cp' => $this->getMainTable()])
            ->where('cp.api_key = ?', $object->getData('api_key'));

        if ($object->getId()) {
            $select->where('cp.entity_id <> ?', $object->getId());
        }

        if ($this->getConnection()->fetchOne($select)) {
            return false;
        } else {
            return true;
        }
    }
}

