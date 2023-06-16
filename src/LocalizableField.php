<?php

namespace LeKoala\FormElements;

interface LocalizableField
{
    public function getLocale();
    public function setLocale($locale);
    public function getScriptDir();
}
