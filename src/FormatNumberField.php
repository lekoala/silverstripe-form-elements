<?php

namespace LeKoala\FormElements;

use SilverStripe\Forms\LiteralField;
use SilverStripe\View\Requirements;

/**
 */
class FormatNumberField extends LiteralField
{
    use BaseElement;

    /**
     * @config
     * @var boolean
     */
    private static $enable_requirements = true;

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
        $content = "<format-number value=\"{$content}\" $attrsHTML></format-number>";

        if ($this->title) {
            $content = "<span>{$this->title}</span> " . $content;
        }
        return $content;
    }

    public static function requirements()
    {
        Requirements::javascript("lekoala/silverstripe-form-elements: client/custom-elements/format-number.min.js");
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

    /**
     * Get the value of currency
     */
    public function getCurrency()
    {
        return $this->getElementAttribute('currency');
    }

    /**
     * Set the value of currency
     *
     * @param mixed $currency
     */
    public function setCurrency($currency)
    {
        return $this->setElementAttribute('currency', $currency);
    }

    /**
     * Get the value of percent
     */
    public function getPercent()
    {
        return $this->getElementAttribute('percent');
    }

    /**
     * Set the value of percent
     *
     * @param mixed $percent
     */
    public function setPercent($percent)
    {
        return $this->setElementAttribute('percent', $percent);
    }
}
