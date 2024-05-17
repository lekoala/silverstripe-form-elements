<?php

namespace LeKoala\FormElements\Test;

use LeKoala\FormElements\CleaveField;
use LeKoala\FormElements\CleaveTimeField;
use LeKoala\FormElements\ColorField;
use LeKoala\FormElements\FlatpickrField;
use LeKoala\FormElements\InputMaskField;
use LeKoala\FormElements\TomSelectMultiField;
use LeKoala\FormElements\TomSelectSingleField;
use SilverStripe\Dev\SapphireTest;

class FormElementsTest extends SapphireTest
{
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

    public function testConvertTimeProperly()
    {
        $arr = [
            1 => '00:00:01',
            59 => '00:00:59',
            61 => '00:01:01',
            1800 => '00:30:00',
            3600 => '01:00:00',
            3601 => '01:00:01',
        ];

        foreach ($arr as $seconds => $time) {
            $this->assertEquals($time, CleaveTimeField::secondsToTime($seconds), "Converting $seconds should give $time");
        }
    }
}
