<?php

namespace LeKoala\FormElements;

use SilverStripe\i18n\i18n;

/**
 * Format date field using ISO value
 *
 * Serves as a base field for all date field since we need datetime alias
 *
 * Locale conversion cannot be done by InputMask and should be provided by a third party service
 *
 * @link https://robinherbots.github.io/Inputmask/#/documentation/datetime
 */
class InputMaskDateTimeField extends InputMaskField
{
    use HasDateTimeFormat;

    /**
     * Disable description
     *
     * @var boolean
     */
    protected $disableDescription = false;

    public function __construct($name, $title = null, $value = null)
    {
        parent::__construct($name, $title, $value);

        $this->setAlias(self::ALIAS_DATETIME);
        $format = self::getDefaultDateFormat() . " HH:MM:ss";
        $this->setInputFormat($format);
        $this->setPlaceholder($format);
        // use ISO date format when unmasking to ensure proper data storage in the db
        $this->setOutputFormat('yyyy-mm-dd HH:MM:ss');
    }

    public function setValue($value, $data = null)
    {
        // Normalize input value according to our format
        if ($value) {
            $value = date((self::convertDateFormatToPhp(self::getDefaultDateFormat())) . ' H:i:s', strtotime($value));
        }
        $this->value = $value;
        return $this;
    }

    /**
     * Get the input format for inputmask
     * @return string
     */
    public static function getDefaultDateFormat()
    {
        $config = self::config()->get('default_input_format');
        if (!$config || $config == 'auto') {
            $locale = strtolower(substr(i18n::get_locale(), 0, 2));
            switch ($locale) {
                case 'fr':
                case 'nl':
                    return 'dd/mm/yyyy';
                    break;
                default:
                    return 'yyyy-mm-dd';
            }
        }
        return $config;
    }

    /**
     * @param string $format
     * @return string
     */
    public static function convertDateFormatToPhp($format)
    {
        $format = str_replace('yyyy', 'Y', $format);
        $format = str_replace('mm', 'm', $format);
        $format = str_replace('dd', 'd', $format);
        return $format;
    }

    /**
     * Format used to input the date
     *
     * @return string
     */
    public function getInputFormat()
    {
        return $this->getConfig('inputFormat');
    }

    public function setInputFormat($value)
    {
        return $this->setConfig('inputFormat', $value);
    }

    /**
     * Unmasking format
     *
     * @return string
     */
    public function getOutputFormat()
    {
        return $this->getConfig('outputFormat');
    }


    public function setOutputFormat($value)
    {
        return $this->setConfig('outputFormat', $value);
    }

    /**
     * Visual format when the input looses focus
     *
     * @return string
     */
    public function getDisplayFormat()
    {
        return $this->getConfig('displayFormat');
    }

    public function setDisplayFormat($value)
    {
        return $this->setConfig('displayFormat', $value);
    }

    /**
     * @param int $seconds
     * @return string
     */
    public static function secondsToTime($seconds)
    {
        $t = round($seconds);
        return sprintf('%02d:%02d:%02d', ($t / 3600), ($t / 60 % 60), $t % 60);
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

    public function setDescription($description)
    {
        // Allows blocking scaffolded UI desc that has no uses
        if ($this->disableDescription) {
            return $this;
        }
        return parent::setDescription($description);
    }

    /**
     * Get disable description
     *
     * @return  boolean
     */
    public function getDisableDescription()
    {
        return $this->disableDescription;
    }

    /**
     * Set disable description
     *
     * @param boolean $disableDescription
     *
     * @return $this
     */
    public function setDisableDescription($disableDescription)
    {
        $this->disableDescription = $disableDescription;
        return $this;
    }
}
