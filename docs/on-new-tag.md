Some sample code to get you started

```php
$cb = $this->onNewTag;
if ($cb === null) {
    $idList = (is_array($this->value)) ? array_values($this->value) : [];
    if (!$record->ID) {
        // record needs to have an ID in order to set relationships
        $record->write();
        /** @var Relation|null $relation */
        $relation = ($record->hasMethod($fieldName)) ? $record->$fieldName() : null;
    }

    $newIdList = [];

    // Tags will be a list of comma separated tags by title
    $class = $relation->dataClass();
    $filterField = 'Title';
    $newList = $class::get()->filter($filterField, $idList);
    $newListMap = $newList->map($filterField, 'ID');

    // Tag will either already exist or need to be created
    foreach ($idList as $id) {
        if (isset($newListMap[$id])) {
            $newIdList[] = $newListMap[$id];
        } else {
            /** @var DataObject $obj */
            $obj = new $class;
            $obj->Title = trim(str_replace(",", '', (string) $id));
            $obj->write();
            $newIdList[] = $obj->ID;
        }
    }

    $relation->setByIDList($newIdList);
} else {
    $items[$idx] = $cb($item);
}
```
