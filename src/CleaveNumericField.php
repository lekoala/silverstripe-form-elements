<?php

namespace LeKoala\FormElements;

use SilverStripe\ORM\DataObjectInterface;

/**
 * Format numeric field
 */
class CleaveNumericField extends CleaveField
{
    /**
     * Set this to true if internal value is seconds
     *
     * @var boolean
     */
    protected $isNumeric = false;

    public function __construct($name, $title = null, $value = null)
    {
        parent::__construct($name, $title, $value);

        $this->setCleaveType('numeral');
        $this->setConfig("numericOnly", true);
    }
}
