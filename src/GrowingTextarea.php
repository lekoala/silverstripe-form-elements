<?php

namespace LeKoala\FormElements;

use SilverStripe\Forms\TextareaField;
use SilverStripe\View\Requirements;

/**
 */
class GrowingTextarea extends TextareaField
{
    use BaseElement;

    /**
     * @config
     * @var boolean
     */
    private static $enable_requirements = true;

    /**
     * @var boolean
     */
    protected $trim = false;

    public function __construct($name, $title = null, $value = '', $config = null)
    {
        parent::__construct($name, $title, $value);
        $this->setRows(1);
    }

    public function Field($properties = array())
    {
        return $this->wrapInElement('growing-textarea', $properties);
    }

    public function setValue($value, $data = null)
    {
        parent::setValue($value, $data);
        if ($this->value) {
            $this->setRows(substr_count($this->value, "\n") + 1);
        }
        return $this;
    }

    public static function requirements()
    {
        Requirements::javascript("lekoala/silverstripe-form-elements: client/custom-elements/growing-textarea.min.js");
    }

    /**
     * Get the value of trim
     */
    public function getTrim()
    {
        return $this->getElementAttribute('data-trim');
    }

    /**
     * Set the value of trim
     *
     * @param bool $trim
     */
    public function setTrim($trim)
    {
        return $this->setElementAttribute('data-trim', $trim);
    }
}
