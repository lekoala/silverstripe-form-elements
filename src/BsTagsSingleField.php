<?php

namespace LeKoala\FormElements;

use SilverStripe\ORM\DataObject;
use SilverStripe\Forms\DropdownField;
use SilverStripe\ORM\DataObjectInterface;

/**
 * Tags
 */
class BsTagsSingleField extends DropdownField implements AjaxPoweredField, TagsField
{
    use BsTags;

    /**
     * @param DataObject|DataObjectInterface $record The record to save into
     */
    public function saveInto(DataObjectInterface $record)
    {
        return parent::saveInto($record);
    }
}
