<?php

namespace Vnecoms\VendorsApi\Api\Data\Sale;

/**
 * Vendor Tracking interface.
 *
 * @api
 * @since 100.0.2
 */
interface TrackingInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case.
     */
    /*
     * Entity ID.
     */
    const CARRIER_CODE = 'carrier_code';
    /*
     * Vendor ID.
     */
    const TITLE = 'title';
    /*
     * Vendor ID.
     */
    const NUMBER = 'number';

    /**
     * @return string
     */
    public function getCarrierCode();

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @return float
     */
    public function getNumber();

    /**
     * @param string $carrierCode
     */
    public function setCarrierCode($carrierCode);

    /**
     * @param string $title
     */
    public function setTitle($title);

    /**
     * @param float $number
     */
    public function setNumber($number);

}