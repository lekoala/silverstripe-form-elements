<?php

namespace LeKoala\FormElements;

/**
 * Format date field using ISO value
 *
 * Locale conversion cannot be done by InputMask and should be provided by a third party service
 *
 * @link https://robinherbots.github.io/Inputmask/#/documentation/datetime
 */
class InputMaskDateField extends InputMaskDateTimeField
{
    public function __construct($name, $title = null, $value = null)
    {
        parent::__construct($name, $title, $value);

        $format = self::getDefaultDateFormat();
        $this->setInputFormat($format);
        $this->setPlaceholder($format);
        // use ISO date format when unmasking to ensure proper data storage in the db
        $this->setOutputFormat('yyyy-mm-dd');
    }

    public function setValue($value, $data = null)
    {
        // Normalize input value according to our format
        if ($value) {
            $value = date(self::convertDateFormatToPhp(self::getDefaultDateFormat()), strtotime($value));
        }
        $this->value = $value;
        return $this;
    }
}
