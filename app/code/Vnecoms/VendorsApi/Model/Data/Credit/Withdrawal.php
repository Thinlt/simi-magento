<?php

namespace Vnecoms\VendorsApi\Model\Data\Credit;

use Magento\Framework\Model\AbstractModel;

/**
 * Class vendor
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class Withdrawal extends AbstractModel implements
    \Vnecoms\VendorsApi\Api\Data\Credit\WithdrawalInterface
{
    /**
     * Gets the ID for the withdrawal.
     *
     * @return int|null Withdrawal ID.
     */
    public function getWithdrawalId(){
        return $this->_getData(self::WITHDRAWAL_ID);
    }

    /**
     * Gets the Vendor ID for the withdrawal.
     *
     * @return int|null Vendor ID.
     */
    public function getVendorId(){
        return $this->_getData(self::VENDOR_ID);
    }

    /**
     * Gets the Method for the withdrawal.
     *
     * @return string Method.
     */
    public function getMethod(){
        return $this->_getData(self::METHOD);
    }

    /**
     * Gets the Method title for the withdrawal.
     *
     * @return string Method title.
     */
    public function getMethodTitle(){
        return $this->_getData(self::METHOD_TITLE);
    }

    /**
     * Gets the Amount for the withdrawal.
     *
     * @return float Amount.
     */
    public function getAmount(){
        return $this->_getData(self::AMOUNT);
    }

    /**
     * Gets the Fee for the withdrawal.
     *
     * @return float Fee.
     */
    public function getFee(){
        return $this->_getData(self::FEE);
    }

    /**
     * Gets the Net amount for the withdrawal.
     *
     * @return float Net amount.
     */
    public function getNetAmount(){
        return $this->_getData(self::NET_AMOUNT);
    }

    /**
     * Gets the Additional info for the withdrawal.
     *
     * @return string Additional info.
     */
    public function getAdditionalInfo(){
        return $this->_getData(self::ADDITIONAL_INFO);
    }

    /**
     * Gets the Note for the withdrawal.
     *
     * @return string Note.
     */
    public function getNote(){
        return $this->_getData(self::NOTE);
    }

    /**
     * Gets the Status for the withdrawal.
     *
     * @return int|null Status.
     */
    public function getStatus(){
        return $this->_getData(self::STATUS);
    }

    /**
     * Gets the Code of transfer for the withdrawal.
     *
     * @return string Code of transfer.
     */
    public function getCodeOfTransfer(){
        return $this->_getData(self::CODE_OF_TRANSFER);
    }

    /**
     * Gets the Reason cancel for the withdrawal.
     *
     * @return string Reason cancel.
     */
    public function getReasonCancel(){
        return $this->_getData(self::REASON_CANCEL);
    }

    /**
     * Gets the created-at timestamp for the withdrawal.
     *
     * @return string|null Created-at timestamp.
     */
    public function getCreatedAt(){
        return $this->_getData(self::CREATED_AT);
    }

    /**
     * Gets the updated-at timestamp for the withdrawal.
     *
     * @return string|null Updated-at timestamp.
     */
    public function getUpdatedAt(){
        return $this->_getData(self::UPDATED_AT);
    }






    /**
     * Sets Withdrawal ID.
     *
     * @param int $withdrawalId
     * @return \Vnecoms\VendorsApi\Api\Data\Credit\WithdrawalInterface
     */
    public function setWithdrawalId($withdrawalId){
        return $this->setData(self::WITHDRAWAL_ID, $withdrawalId);
    }

    /**
     * Sets Vendor ID.
     *
     * @param int $vendorId
     * @return \Vnecoms\VendorsApi\Api\Data\Credit\WithdrawalInterface
     */
    public function setVendorId($vendorId){
        return $this->setData(self::VENDOR_ID, $vendorId);
    }

    /**
     * Sets Method.
     *
     * @param string $method
     * @return \Vnecoms\VendorsApi\Api\Data\Credit\WithdrawalInterface
     */
    public function setMethod($method){
        return $this->setData(self::METHOD, $method);
    }

    /**
     * Sets Method title.
     *
     * @param string $methodTitle
     * @return \Vnecoms\VendorsApi\Api\Data\Credit\WithdrawalInterface
     */
    public function setMethodTitle($methodTitle){
        return $this->setData(self::METHOD_TITLE, $methodTitle);
    }

    /**
     * Sets Amount.
     *
     * @param float $amount
     * @return \Vnecoms\VendorsApi\Api\Data\Credit\WithdrawalInterface
     */
    public function setAmount($amount){
        return $this->setData(self::AMOUNT, $amount);
    }

    /**
     * Sets Fee.
     *
     * @param float $fee
     * @return \Vnecoms\VendorsApi\Api\Data\Credit\WithdrawalInterface
     */
    public function setFee($fee){
        return $this->setData(self::FEE, $fee);
    }

    /**
     * Sets Net amount.
     *
     * @param float $netAmount
     * @return \Vnecoms\VendorsApi\Api\Data\Credit\WithdrawalInterface
     */
    public function setNetAmount($netAmount){
        return $this->setData(self::NET_AMOUNT, $netAmount);
    }

    /**
     * Sets Additional info.
     *
     * @param string $additionalInfo
     * @return \Vnecoms\VendorsApi\Api\Data\Credit\WithdrawalInterface
     */
    public function setAdditionalInfo($additionalInfo){
        return $this->setData(self::ADDITIONAL_INFO, $additionalInfo);
    }

    /**
     * Sets Note.
     *
     * @param string $note
     * @return \Vnecoms\VendorsApi\Api\Data\Credit\WithdrawalInterface
     */
    public function setNote($note){
        return $this->setData(self::NOTE, $note);
    }

    /**
     * Sets Status.
     *
     * @param int $status
     * @return \Vnecoms\VendorsApi\Api\Data\Credit\WithdrawalInterface
     */
    public function setStatus($status){
        return $this->setData(self::STATUS, $status);
    }

    /**
     * Sets Code of transfer.
     *
     * @param string $codeOfTransfer
     * @return \Vnecoms\VendorsApi\Api\Data\Credit\WithdrawalInterface
     */
    public function setCodeOfTransfer($codeOfTransfer){
        return $this->setData(self::CODE_OF_TRANSFER, $codeOfTransfer);
    }

    /**
     * Sets Reason cancel.
     *
     * @param string $reasonCancel
     * @return \Vnecoms\VendorsApi\Api\Data\Credit\WithdrawalInterface
     */
    public function setReasonCancel($reasonCancel){
        return $this->setData(self::REASON_CANCEL, $reasonCancel);
    }

    /**
     * Sets the created-at timestamp.
     *
     * @param string $createdAt timestamp
     * @return \Vnecoms\VendorsApi\Api\Data\Credit\WithdrawalInterface
     */
    public function setCreatedAt($createdAt){
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * Sets the updated-at timestamp.
     *
     * @param string $timestamp
     * @return \Vnecoms\VendorsApi\Api\Data\Credit\WithdrawalInterface
     */
    public function setUpdatedAt($timestamp){
        return $this->setData(self::UPDATED_AT, $timestamp);
    }

}


