<?php

namespace LeKoala\FormElements;

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

    public function __construct($name, $title = null, $value = '', $maxLength = null, $form = null)
    {
        parent::__construct($name, $title, $value, $maxLength, $form);
        $this->mergeDefaultConfig();
        if (self::config()->default_dataformat) {
            $this->setDataFormat(self::config()->default_dataformat);
        }
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

    /**
     * @param array<string,mixed> $properties
     * @return DBHTMLText|string
     */
    public function Field($properties = [])
    {
        return $this->wrapInElement('tel-input', $properties);
    }

    public static function requirements(): void
    {
        Requirements::javascript("lekoala/silverstripe-form-elements: client/custom-elements/tel-input.min.js");
    }
}
