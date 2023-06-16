<?php

namespace LeKoala\FormElements;

/**
 * Format currency
 * "currency": {
 * prefix: "", //"$ ",
 * groupSeparator: ",",
 * alias: "numeric",
 * digits: 2,
 * digitsOptional: false
 * },
 */
class InputMaskCurrencyField extends InputMaskNumericField
{
    use CurrencyFormatter;

    public function __construct($name, $title = null, $value = null)
    {
        parent::__construct($name, $title, $value);
        $this->setAlias(self::ALIAS_CURRENCY);
        $this->setPrefix($this->getCurrencySymbol() . ' ');
    }

    public function setValue($value, $data = null)
    {
        // otherwise values like 84.4 will be interpreted as 844.00
        if ($value !== null && is_float($value) && strlen($value)) {
            $value = number_format($value, 2, $this->getCurrencyDecimalSeparator(), "");
        }
        return parent::setValue($value, $data);
    }

    /**
     * Create a new class for this field
     */
    public function performReadonlyTransformation()
    {
        $field = $this->castedCopy(InputMaskCurrencyField::class);
        $field->setReadonly(true);
        return $field;
    }
}
