<?php

namespace LeKoala\FormElements;

use SilverStripe\ORM\DataObjectInterface;

/**
 * Format time field
 * This allow to store raw seconds instead of formatted time strings
 * eg 00:01:00 is converted to 60
 */
class CleaveTimeField extends CleaveField
{
    /**
     * Set this to true if internal value is seconds
     *
     * @var boolean
     */
    protected $isNumeric = false;

    public function __construct($name, $title = null, $value = null)
    {
        parent::__construct($name, $title, $value);
        $this->setConfig("swapHiddenInput", false);
        $this->setCleaveType('time');
    }

    /**
     * @param int $seconds
     * @return string
     */
    public static function secondsToTime($seconds)
    {
        $hours = floor($seconds / 3600);
        $mins = floor($seconds / 60) % 60;
        $secs = floor($seconds % 60);
        return sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
    }

    /**
     * @param string $time
     * @return int
     */
    public static function timeToSeconds($time)
    {
        sscanf($time, "%d:%d:%d", $hours, $minutes, $seconds);
        $result = isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;
        return (int)$result;
    }

    public function setValue($value, $data = null)
    {
        if ($this->isNumeric && is_numeric($value)) {
            $old = $value;
            $value = self::secondsToTime($value);
        }
        // Don't call parent that can set locale formatted date
        $this->value = $value;
        return $this;
    }

    public function dataValue()
    {
        $value = parent::dataValue();
        // Value is stored in database in seconds
        if ($this->isNumeric) {
            return self::timeToSeconds($value);
        }
        return $value;
    }

    public function saveInto(DataObjectInterface $record)
    {
        return parent::saveInto($record);
    }

    /**
     * Get the value of isNumeric
     * @return mixed
     */
    public function getIsNumeric()
    {
        return $this->isNumeric;
    }

    /**
     * Set the value of isNumeric
     *
     * @param mixed $isNumeric
     * @return $this
     */
    public function setIsNumeric($isNumeric)
    {
        $this->isNumeric = $isNumeric;
        return $this;
    }
}
