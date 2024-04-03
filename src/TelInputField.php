<?php

namespace LeKoala\FormElements;

use SilverStripe\Forms\HiddenField;
use SilverStripe\Forms\TextField;
use SilverStripe\View\Requirements;
use SilverStripe\ORM\FieldType\DBHTMLText;

/**
 * Store phone number in full international format
 * Have a proper db type to format phone number accordingly
 */
class TelInputField extends TextField
{
    const FORMAT_E164 = "E164";
    const FORMAT_INTERNATIONAL = "INTERNATIONAL";
    const FORMAT_NATIONAL = "NATIONAL";
    const FORMAT_RFC3966 = "RFC3966";

    use BaseElement;

    /**
     * @config
     * @var array<string,mixed>
     */
    private static $default_config = [];

    /**
     * @config
     * @var boolean
     */
    private static $enable_requirements = true;

    /**
     * Format to use for storage
     *
     * @var string
     */
    private static $defaut_dataformat = null;

    protected HiddenField $hiddenField;

    public function __construct($name, $title = null, $value = '', $maxLength = null, $form = null)
    {
        $this->hiddenField = new HiddenField($name, $title, $value);
        parent::__construct($name, $title, $value, $maxLength, $form);
        $this->mergeDefaultConfig();
        if (self::config()->default_dataformat) {
            $this->setDataFormat(self::config()->default_dataformat);
        }
    }

    public function getHiddenField(): HiddenField
    {
        return $this->hiddenField;
    }

    public function Type()
    {
        return 'telinput';
    }

    public function extraClass()
    {
        return 'text ' . parent::extraClass();
    }

    /**
     * @return string
     */
    public function getDataFormat()
    {
        return $this->getElementAttribute('data-dataformat');
    }

    /**
     * The value you want when unmasking to hidden field
     *
     * @param string $dataformat The alias or "masked" to get the masked value as is
     * @return $this
     */
    public function setDataFormat($dataformat)
    {
        return $this->setElementAttribute('data-dataformat', $dataformat);
    }

    public function setTitle($title)
    {
        $this->hiddenField->setTitle($title);
        return parent::setTitle($title);
    }

    public function setName($name)
    {
        $this->hiddenField->setName($name);
        return parent::setName($name);
    }

    public function setValue($value, $data = null)
    {
        $this->hiddenField->setValue($value, $data);
        return parent::setValue($value, $data);
    }

    // public function setAttribute($name, $value)
    // {
    //     if (str_starts_with($name, 'data-parsley')) {
    //         $this->hiddenField->setAttribute($name, $value);
    //         return $this;
    //     }
    //     return parent::setAttribute($name, $value);
    // }

    protected function createHiddenInput($properties = [])
    {
        $html = $this->hiddenField->forTemplate();
        return $html;
    }

    /**
     * @param array<string,mixed> $properties
     * @return DBHTMLText|string
     */
    public function Field($properties = [])
    {
        $extraHtml = $this->createHiddenInput($properties);
        return $this->wrapInElement('tel-input', $properties, $extraHtml);
    }

    public static function requirements(): void
    {
        Requirements::javascript("lekoala/silverstripe-form-elements: client/custom-elements/tel-input.min.js");
    }
}
