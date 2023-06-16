<?php

namespace LeKoala\FormElements;

use SilverStripe\Forms\LiteralField;
use SilverStripe\View\Requirements;

/**
 */
class FormatDateField extends LiteralField
{
    use BaseElement;

    const FORMAT_ISO = "iso";
    const FORMAT_UTC = "utc";
    const FORMAT_DATETIME = "datetime";
    const FORMAT_DATE = "date";
    const FORMAT_TIME = "time";

    /**
     * @config
     * @var boolean
     */
    private static $enable_requirements = true;

    /**
     * @var string
     */
    protected $format = "iso";

    /**
     * @var string
     */
    protected $datestyle;

    /**
     * @var string
     */
    protected $timestyle;

    /**
     * @var string
     */
    protected $lang;

    /**
     * @param string $name
     * @param string|FormField $content
     * @param string $label
     */
    public function __construct($name, $content = "", $title = null)
    {
        if ($content) {
            $this->setContent($content);
        }
        if ($title) {
            $this->setTitle($title);
        }

        parent::__construct($name, $content);
    }

    /**
     * @param array $properties
     * @return string
     */
    public function FieldHolder($properties = array())
    {
        if (self::config()->enable_requirements) {
            self::requirements();
        }

        $content = parent::FieldHolder($properties);
        $attrsHTML = $this->getElementAttributesHTML();
        $content = "<format-date value=\"{$content}\" $attrsHTML></format-date>";

        if ($this->title) {
            $content = "<span>{$this->title}</span> " . $content;
        }
        return $content;
    }

    public static function requirements()
    {
        Requirements::javascript("lekoala/silverstripe-form-elements: client/custom-elements/format-date.min.js");
    }

    /**
     * Get the value of format
     */
    public function getFormat()
    {
        return $this->getElementAttribute('format');
    }

    /**
     * Set the value of format
     *
     * @param string $format
     */
    public function setFormat($format)
    {
        return $this->setElementAttribute('format', $format);
    }

    /**
     * Get the value of datestyle
     */
    public function getDatestyle()
    {
        return $this->getElementAttribute('datestyle');
    }

    /**
     * Set the value of datestyle
     *
     * @param mixed $datestyle
     */
    public function setDatestyle($datestyle)
    {
        return $this->setElementAttribute('datestyle', $datestyle);
    }

    /**
     * Get the value of timestyle
     */
    public function getTimestyle()
    {
        return $this->getElementAttribute('timestyle');
    }

    /**
     * Set the value of timestyle
     *
     * @param mixed $timestyle
     */
    public function setTimestyle($timestyle)
    {
        return $this->setElementAttribute('timestyle', $timestyle);
    }

    /**
     * Get the value of lang
     */
    public function getLang()
    {
        return $this->getElementAttribute('lang');
    }

    /**
     * Set the value of lang
     *
     * @param mixed $lang
     */
    public function setLang($lang)
    {
        return $this->setElementAttribute('lang', $lang);
    }
}
