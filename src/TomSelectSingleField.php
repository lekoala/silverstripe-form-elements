<?php

namespace LeKoala\FormElements;

use SilverStripe\Forms\DropdownField;
use SilverStripe\ORM\DataObjectInterface;

/**
 * @link https://tom-select.js.org/
 */
class TomSelectSingleField extends DropdownField implements AjaxPoweredField, LocalizableField, TagsField
{
    use TomSelect;

    /**
     * @param DataObject|DataObjectInterface $record The record to save into
     */
    public function saveInto(DataObjectInterface $record)
    {
        return parent::saveInto($record);
    }
}
