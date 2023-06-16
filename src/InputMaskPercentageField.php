<?php

namespace LeKoala\FormElements;

/**
 * Format %
 */
class InputMaskPercentageField extends InputMaskNumericField
{

    public function __construct($name, $title = null, $value = null)
    {
        parent::__construct($name, $title, $value);
        // Don't use the alias
        $this->setSuffix(' %');
    }

    /**
     * Create a new class for this field
     */
    public function performReadonlyTransformation()
    {
        $field = $this->castedCopy(NumericReadonlyField::class);
        $field->setSuffix('%');
        return $field;
    }

    /**
     * Get the value of isDecimal
     */
    public function getIsDecimal()
    {
        return $this->getElementAttribute('data-decimal');
    }

    /**
     * Set the value of isDecimal
     *
     * @return $this
     */
    public function setIsDecimal($isDecimal)
    {
        return $this->setElementAttribute('data-decimal', $isDecimal);
    }
}
