<?php

namespace LeKoala\FormElements;

use SilverStripe\Forms\TextField;
use SilverStripe\View\Requirements;

/**
 * @link https://github.com/mdbassit/Coloris
 * @link https://gist.github.com/lekoala/233b0c6246170716c52dbfab342caf22
 * @link https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input/color
 */
class ColorField extends TextField implements LocalizableField
{
    use BaseElement;
    use Localize;

    /**
     * Override locale. If empty will default to current locale
     *
     * @var string
     */
    protected $locale = null;

    /**
     * @config
     * @var array
     */
    private static $default_config = [];

    /**
     * @config
     * @var boolean
     */
    private static $enable_requirements = true;

    public function __construct($name, $title = null, $value = '', $maxLength = null, $form = null)
    {
        parent::__construct($name, $title, $value, $maxLength, $form);
        $this->mergeDefaultConfig();
    }

    public function getInputType()
    {
        return 'text';
    }

    public function Type()
    {
        return 'coloris';
    }

    public function getSwatches()
    {
        return $this->getConfig('swatches');
    }

    public function setSwatches($values)
    {
        $this->setConfig('swatches', $values);
    }

    public function Field($properties = array())
    {
        return $this->wrapInElement('coloris-input', $properties);
    }

    public static function requirements()
    {
        Requirements::javascript("lekoala/silverstripe-form-elements: client/custom-elements/coloris-input.min.js");
    }
}
