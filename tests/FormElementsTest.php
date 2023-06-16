<?php

namespace LeKoala\FormElements\Test;

use LeKoala\FormElements\CleaveField;
use LeKoala\FormElements\ColorField;
use LeKoala\FormElements\FlatpickrField;
use LeKoala\FormElements\InputMaskField;
use LeKoala\FormElements\TomSelectMultiField;
use LeKoala\FormElements\TomSelectSingleField;
use SilverStripe\Dev\SapphireTest;

class FormElementsTest extends SapphireTest
{
    public function testItWorks()
    {
        return $this->assertTrue(true);
    }

    public function testHasRequirements()
    {
        $classes = [
            CleaveField::class,
            ColorField::class,
            FlatpickrField::class,
            InputMaskField::class,
            TomSelectMultiField::class,
            TomSelectSingleField::class,
        ];

        foreach ($classes as $class) {
            $inst = new $class('test');
            $this->assertTrue($inst->hasMethod('requirements'));
        }
    }
}
