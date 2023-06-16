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
    public function __construct($name, $title = null, $value = null)
    {
        parent::__construct($name, $title, $value);
        $this->setAlias(self::ALIAS_CURRENCY);
    }

    public function setValue($value, $data = null)
    {
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
