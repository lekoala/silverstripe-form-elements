<?php

namespace LeKoala\FormElements;

use SilverStripe\i18n\i18n;
use SilverStripe\Forms\TextField;
use SilverStripe\View\Requirements;

/**
 * Format input using input mask
 *
 * Fully decouples formatted field from data field.
 * Formatting is a UI concept that should not be dealt with in PHP outside of the scope of validation.
 * This avoids messy conversion (for date, currency, ...)
 */
class InputMaskField extends TextField implements MaskableField, LocalizableField
{
    use BaseElement;
    use Localize;

    // Base masks
    const MASK_NUMERIC = '9';
    const MASK_ALPHA = 'a';
    const MASK_ALPHANUMERIC = '*';
    // Base alias
    const ALIAS_URL = 'url';
    const ALIAS_IP = 'ip';
    const ALIAS_EMAIL = 'email';
    const ALIAS_DATETIME = 'datetime';
    const ALIAS_NUMERIC = 'numeric';
    const ALIAS_CURRENCY = 'currency';
    const ALIAS_DECIMAL = 'decimal';
    const ALIAS_INTEGER = 'integer';
    const ALIAS_PERCENTAGE = 'percentage';
    const ALIAS_PHONE = 'phone';
    const ALIAS_PHONEBE = 'phonebe';
    const ALIAS_REGEX = 'regex';

    /**
     * @config
     * @var array
     */
    private static $default_config = [];

    /**
     * Override locale. If empty will default to current locale
     *
     * @var string
     */
    protected $locale = null;

    /**
     * Format to use for storage
     *
     * @var string
     */
    private static $defaut_dataformat = null;

    /**
     * Format to use for storage
     *
     * @var string
     */
    protected $dataFormat;

    /**
     * @config
     * @var boolean
     */
    private static $enable_requirements = true;

    public function __construct($name, $title = null, $value = '', $maxLength = null, $form = null)
    {
        parent::__construct($name, $title, $value, $maxLength, $form);
        $this->mergeDefaultConfig();
        $this->dataFormat = self::config()->default_dataformat;
    }

    public function Type()
    {
        return 'inputmask';
    }

    public function extraClass()
    {
        return 'text ' . parent::extraClass();
    }

    public function getPlaceholder()
    {
        return $this->getAttribute('placeholder');
    }

    public function setPlaceholder($value)
    {
        return $this->setAttribute('placeholder', $value);
    }

    public function getDataFormat()
    {
        return $this->getElementAttribute('data-dataformat');
    }

    /**
     * The value you want when unmasking to hidden field
     *
     * @param string $value The alias or "masked" to get the masked value as is
     * @return $this
     */
    public function setDataFormat($dataformat)
    {
        return $this->getElementAttribute('data-dataformat', $dataformat);
    }

    public function getAlias()
    {
        return $this->getConfig('alias');
    }

    public function setAlias($value)
    {
        return $this->setConfig('alias', $value);
    }

    public function getRegex()
    {
        return $this->getConfig('regex');
    }

    /**
     * Use a regular expression as a mask
     *
     * @link https://github.com/RobinHerbots/Inputmask#regex
     * @param string $value
     * @return $this
     */
    public function setRegex($value)
    {
        return $this->setConfig('regex', $value);
    }

    public function getMask()
    {
        return $this->getConfig('mask');
    }

    /**
     * Set the mask
     *
     * 9: numeric, a: alphabetical, *: alphanumeric, (aaa): optional part
     *
     * @param string $value
     * @return $this
     */
    public function setMask($value)
    {
        return $this->setConfig('mask', $value);
    }

    public function getRightAlign()
    {
        return $this->getConfig('rightAlign');
    }

    public function setRighAlign($value)
    {
        return $this->setConfig('rightAlign', $value);
    }

    public function getGroupSeparator()
    {
        return $this->getConfig('groupSeparator');
    }

    public function setGroupSeparator($value)
    {
        return $this->setConfig('groupSeparator', $value);
    }

    public function getRadixPoint()
    {
        return $this->getConfig('radixPoint');
    }

    public function setRadixPoint($value)
    {
        return $this->setConfig('radixPoint', $value);
    }

    public function getDigits()
    {
        return $this->getConfig('digits');
    }

    public function setDigits($value)
    {
        return $this->setConfig('digits', $value);
    }

    public function getAttributes()
    {
        $attributes = parent::getAttributes();
        $attributes['lang'] = i18n::convert_rfc1766($this->getLocale());
        return $attributes;
    }

    public function Field($properties = array())
    {
        if ($this->readonly) {
            $this->setAttribute("disabled", true);
        }
        return $this->wrapInElement('input-mask', $properties);
    }

    public static function requirements()
    {
        Requirements::javascript("lekoala/silverstripe-form-elements: client/custom-elements/input-mask.min.js");
    }
}
