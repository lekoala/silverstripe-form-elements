<?php

namespace LeKoala\FormElements;

use SilverStripe\Forms\TextField;
use SilverStripe\View\Requirements;

/**
 * Format input using cleave.js
 *
 * @link https://nosir.github.io/cleave.js/
 * @link https://github.com/lekoala/cleave-es6
 */
class CleaveField extends TextField implements MaskableField
{
    const TYPE_TIME = "time";
    const TYPE_DATE = "date";
    const TYPE_DATETIME = "datetime";
    const TYPE_NUMERAL = "numeral";

    use BaseElement;

    /**
     * @config
     * @var array
     */
    private static $default_config = [
        "swapHiddenInput" => true,
    ];

    /**
     * @config
     * @var boolean
     */
    private static $enable_requirements = true;

    public function __construct($name, $title = null, $value = '', $maxLength = null, $form = null)
    {
        parent::__construct($name, $title, $value, $maxLength, $form);
        $this->mergeDefaultConfig();
    }

    public function Type()
    {
        return 'cleave';
    }

    public function extraClass()
    {
        return 'text ' . parent::extraClass();
    }

    public function Field($properties = array())
    {
        return $this->wrapInElement('cleave-input', $properties);
    }

    public static function requirements()
    {
        Requirements::javascript("lekoala/silverstripe-form-elements: client/custom-elements/cleave-input.min.js");
    }

    /**
     * Get the value of cleaveType
     * @return string
     */
    public function getCleaveType()
    {
        return $this->getElementAttribute('type');
    }

    /**
     * Set the value of inputType
     *
     * @param string $cleaveType date,time,datetime,numeral
     * @return $this
     */
    public function setCleaveType($cleaveType)
    {
        return $this->setElementAttribute('type', $cleaveType);
    }

    public function getDigits()
    {
        return $this->getConfig('numeralDecimalScale');
    }

    public function setDigits($v)
    {
        return $this->setConfig('numeralDecimalScale', $v);
    }

    public function getRadixPoint()
    {
        return $this->getConfig('numeralDecimalMark');
    }

    public function setRadixPoint($v)
    {
        return $this->setConfig('numeralDecimalMark', $v);
    }

    public function getGroupSeparator()
    {
        return $this->getConfig('delimiter');
    }

    public function setGroupSeparator($value)
    {
        return $this->setConfig('delimiter', $value);
    }

    public function getEnforceDigitsOnBlur()
    {
        return $this->getConfig('numeralDecimalPadding');
    }

    public function setEnforceDigitsOnBlur($value)
    {
        return $this->setConfig('numeralDecimalPadding ', $value);
    }

    public function getPrefix()
    {
        return $this->getConfig('prefix');
    }

    public function setPrefix($value)
    {
        return $this->setConfig('prefix', $value);
    }
}
