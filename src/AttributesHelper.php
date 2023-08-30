<?php

namespace LeKoala\FormElements;

/**
 */
trait AttributesHelper
{
    public function getPlaceholder(): string
    {
        return $this->getAttribute('placeholder');
    }

    public function setPlaceholder(string $placeholder)
    {
        $this->setAttribute('placeholder', $placeholder);
    }
}
