<?php

namespace LeKoala\FormElements;

/**
 * Base interface for fields that can deal with tags (TomSelect, BsTags...)
 */
interface TagsField
{

    public function getTags();
    public function setTags($value, $blur = true);

    /**
     * @return Callable
     */
    public function getOnNewTag();

    /**
     * The callback should return the new id
     *
     * @param Callable $locale
     * @return $this
     */
    public function setOnNewTag($callback);
}
