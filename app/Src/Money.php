<?php

namespace App\Src;

use Illuminate\Contracts\Support\Arrayable;

/**
 * Money representation for price amounts given by flight providers
 */
class Money implements Arrayable
{
    /**
     * Amount of money beign represented
     *
     * @var float|int
     */
    protected $value;

    /**
     * The decimal state of money represented
     * If true then $this->value is cents
     *
     * @var bool
     */
    protected $isCents;

    /**
     * Currency in which the money is counted in
     *
     * @var string
     */
    protected $currency;

    /**
     * Create money object
     *
     * @param float|int $value    The amount we're working with
     * @param string    $currency Currency of the money
     * @param boolean   $isCents  Whether the amount is in cent form
     */
    public function __construct($value, $currency, $isCents = false)
    {
        $this->value = $value;
        $this->currency = $currency;
        $this->isCents = $isCents;
    }

    /**
     * Get the amount represented in decimal value
     * E.g. 3.27
     *
     * @param integer $percision The percision to be used in rounding
     *
     * @return float
     */
    public function getDecimalValue($percision = 2) : float
    {
        $value = $this->value;
        if ($this->isCents) {
            $value = $value/100;
        }

        return round($value, $percision);
    }

    /**
     * Get the array representation of money
     *
     * @return array
     */
    public function toArray() : array
    {
        return [
            'amount' => $this->getDecimalValue(),
            'currency' => $this->currency,
        ];
    }
}
