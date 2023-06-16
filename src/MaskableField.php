<?php

namespace LeKoala\FormElements;

interface MaskableField
{
    public function getGroupSeparator();
    public function setGroupSeparator($value);
    public function getRadixPoint();
    public function setRadixPoint($value);
    public function getDigits();
    public function setDigits($v);
}
