<?php

namespace LeKoala\FormElements;

use Exception;
use SilverStripe\ORM\Relation;
use SilverStripe\ORM\DataObject;
use SilverStripe\Forms\ListboxField;
use SilverStripe\ORM\DataObjectInterface;

/**
 * Tags
 */
class BsTagsMultiField extends ListboxField implements AjaxPoweredField, TagsField
{
    use BsTags;

    /**
     * @param DataObject|DataObjectInterface $record The record to save into
     */
    public function saveInto(DataObjectInterface $record)
    {
        $fieldName = $this->getName();
        if (empty($fieldName) || empty($record)) {
            return;
        }

        /** @var Relation $relation */
        $relation = $record->hasMethod($fieldName) ? $record->$fieldName() : null;

        // Detect DB relation or field
        $items = $this->getValueArray();
        if ($relation && $relation instanceof Relation) {
            foreach ($items as $idx => $item) {
                // If the item is a string, it's not an ID and needs to be created
                if (!is_numeric($item)) {
                    $cb = $this->onNewTag;
                    if (!$cb) {
                        throw new Exception("Please define a onNewTag callback");
                    }
                    $items[$idx] = $cb($item);
                }
            }
            // Save ids into relation
            $relation->setByIDList(array_values($items));
        } elseif ($record->hasField($fieldName)) {
            // Save dataValue into field
            $record->$fieldName = $this->stringEncode($items);
        }
    }

    public function getValueArray()
    {
        $value = $this->Value();
        $validValues = $this->getValidValues();
        if (empty($validValues)) {
            return [];
        }

        // Accepts int or string just the same
        $targetTypes = ['string', 'integer'];

        if (is_array($value) && count($value) > 0) {
            // Disable sanity check that breaks due to placeholder being a string
            $replaced = [];
            foreach ($value as $item) {
                if (!is_array($item)) {
                    $item = json_decode($item, true);
                }

                if (in_array(gettype($item), $targetTypes)) {
                    $replaced[] = $item;
                } elseif (isset($item['Value'])) {
                    $replaced[] = $item['Value'];
                }
            }

            $value = $replaced;
        }

        return $this->getListValues($value);
    }
}
