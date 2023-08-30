<?php

namespace LeKoala\FormElements;

use SilverStripe\Forms\TextField;
use SilverStripe\View\Requirements;

/**
 * Autocomplete
 */
class BsAutocompleteField extends TextField implements AjaxPoweredField
{
    use BaseElement;
    use Autocompleter;
    use AttributesHelper;

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

    public function Type()
    {
        return 'bsautocomplete';
    }

    public function extraClass()
    {
        return 'text ' . parent::extraClass();
    }

    public function getServerVars()
    {
        return [
            'queryParam' => 'query',
            'dataKey' => 'data',
            'valueField' => 'value',
            'labelField' => 'label',
        ];
    }

    public function Field($properties = array())
    {
        return $this->wrapInElement('bs-autocomplete', $properties);
    }

    public static function requirements()
    {
        Requirements::javascript("lekoala/silverstripe-form-elements: client/custom-elements/bs-autocomplete.min.js");
    }

    public function getAjax()
    {
        return $this->getConfig('server');
    }

    public function setAjax($url, $opts = [])
    {
        $this->setConfig('server', $url);
        $this->setConfig('serverParams', [
            'SecurityID' => $this->getForm()->getSecurityToken()->getValue()
        ]);
        $this->setConfig('liveServer', true);
        return $this;
    }

    /**
     * @return boolean
     */
    public function isAjax()
    {
        return $this->ajaxClass || $this->getConfig('server');
    }
}
