<?php

namespace Vnecoms\VendorsApi\Api\Data\Credit;

/**
 * Vendor Withdrawal interface.
 *
 * @api
 * @since 100.0.2
 */

interface WithdrawalInterface
{
    /**#@+
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    /*
     * Withdrawal ID.
     */
    const WITHDRAWAL_ID = 'withdrawal_id';

    /*
     * Vendor ID.
     */
    const VENDOR_ID = 'vendor_id';

    /*
     * Method.
     */
    const METHOD = 'method';

    /*
     * Method title.
     */
    const METHOD_TITLE = 'method_title';

    /*
     * Amount.
     */
    const AMOUNT = 'amount';

    /*
     * Fee.
     */
    const FEE = 'fee';

    /*
     * Net amount.
     */
    const NET_AMOUNT = 'net_amount';

    /*
     * Additional info.
     */
    const ADDITIONAL_INFO = 'additional_info';

    /*
     * Note.
     */
    const NOTE = 'note';

    /*
     * Status.
     */
    const STATUS = 'status';

    /*
     * Code of transfer.
     */
    const CODE_OF_TRANSFER = 'code_of_transfer';

    /*
     * Reason cancel.
     */
    const REASON_CANCEL = 'reason_cancel';

    /*
     * Created-at timestamp.
     */
    const CREATED_AT = 'created_at';

    /*
     * Updated-at timestamp.
     */
    const UPDATED_AT = 'updated_at';



    /**
     * Gets the ID for the withdrawal.
     *
     * @return int|null Withdrawal ID.
     */
    public function getWithdrawalId();

    /**
     * Gets the Vendor ID for the withdrawal.
     *
     * @return int|null Vendor ID.
     */
    public function getVendorId();

    /**
     * Gets the Method for the withdrawal.
     *
     * @return string Method.
     */
    public function getMethod();

    /**
     * Gets the Method title for the withdrawal.
     *
     * @return string Method title.
     */
    public function getMethodTitle();

    /**
     * Gets the Amount for the withdrawal.
     *
     * @return float Amount.
     */
    public function getAmount();

    /**
     * Gets the Fee for the withdrawal.
     *
     * @return float Fee.
     */
    public function getFee();

    /**
     * Gets the Net amount for the withdrawal.
     *
     * @return float Net amount.
     */
    public function getNetAmount();

    /**
     * Gets the Additional info for the withdrawal.
     *
     * @return string Additional info.
     */
    public function getAdditionalInfo();

    /**
     * Gets the Note for the withdrawal.
     *
     * @return string Note.
     */
    public function getNote();

    /**
     * Gets the Status for the withdrawal.
     *
     * @return int|null Status.
     */
    public function getStatus();

    /**
     * Gets the Code of transfer for the withdrawal.
     *
     * @return string Code of transfer.
     */
    public function getCodeOfTransfer();

    /**
     * Gets the Reason cancel for the withdrawal.
     *
     * @return string Reason cancel.
     */
    public function getReasonCancel();

    /**
     * Gets the created-at timestamp for the withdrawal.
     *
     * @return string|null Created-at timestamp.
     */
    public function getCreatedAt();

    /**
     * Gets the updated-at timestamp for the withdrawal.
     *
     * @return string|null Updated-at timestamp.
     */
    public function getUpdatedAt();






    /**
     * Sets Withdrawal ID.
     *
     * @param int $withdrawalId
     * @return \Vnecoms\VendorsApi\Api\Data\Credit\WithdrawalInterface
     */
    public function setWithdrawalId($withdrawalId);

    /**
     * Sets Vendor ID.
     *
     * @param int $vendorId
     * @return \Vnecoms\VendorsApi\Api\Data\Credit\WithdrawalInterface
     */
    public function setVendorId($vendorId);

    /**
     * Sets Method.
     *
     * @param string $method
     * @return \Vnecoms\VendorsApi\Api\Data\Credit\WithdrawalInterface
     */
    public function setMethod($method);

    /**
     * Sets Method title.
     *
     * @param string $methodTitle
     * @return \Vnecoms\VendorsApi\Api\Data\Credit\WithdrawalInterface
     */
    public function setMethodTitle($methodTitle);

    /**
     * Sets Amount.
     *
     * @param float $amount
     * @return \Vnecoms\VendorsApi\Api\Data\Credit\WithdrawalInterface
     */
    public function setAmount($amount);

    /**
     * Sets Fee.
     *
     * @param float $fee
     * @return \Vnecoms\VendorsApi\Api\Data\Credit\WithdrawalInterface
     */
    public function setFee($fee);

    /**
     * Sets Net amount.
     *
     * @param float $netAmount
     * @return \Vnecoms\VendorsApi\Api\Data\Credit\WithdrawalInterface
     */
    public function setNetAmount($netAmount);

    /**
     * Sets Additional info.
     *
     * @param string $additionalInfo
     * @return \Vnecoms\VendorsApi\Api\Data\Credit\WithdrawalInterface
     */
    public function setAdditionalInfo($additionalInfo);

    /**
     * Sets Note.
     *
     * @param string $note
     * @return \Vnecoms\VendorsApi\Api\Data\Credit\WithdrawalInterface
     */
    public function setNote($note);

    /**
     * Sets Status.
     *
     * @param int $status
     * @return \Vnecoms\VendorsApi\Api\Data\Credit\WithdrawalInterface
     */
    public function setStatus($status);

    /**
     * Sets Code of transfer.
     *
     * @param string $codeOfTransfer
     * @return \Vnecoms\VendorsApi\Api\Data\Credit\WithdrawalInterface
     */
    public function setCodeOfTransfer($codeOfTransfer);

    /**
     * Sets Reason cancel.
     *
     * @param string $reasonCancel
     * @return \Vnecoms\VendorsApi\Api\Data\Credit\WithdrawalInterface
     */
    public function setReasonCancel($reasonCancel);

    /**
     * Sets the created-at timestamp.
     *
     * @param string $createdAt timestamp
     * @return \Vnecoms\VendorsApi\Api\Data\Credit\WithdrawalInterface
     */
    public function setCreatedAt($createdAt);

    /**
     * Sets the updated-at timestamp.
     *
     * @param string $timestamp
     * @return \Vnecoms\VendorsApi\Api\Data\Credit\WithdrawalInterface
     */
    public function setUpdatedAt($timestamp);
}
