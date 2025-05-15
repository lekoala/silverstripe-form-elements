<?php

namespace LeKoala\FormElements;

use SilverStripe\ORM\DataObject;
use SilverStripe\View\Requirements;

/**
 * Implements AjaxPoweredField + TagsField
 */
trait BsTags
{
    use BaseElement;
    use Localize;
    use Autocompleter;

    /**
     * Callback to create tags
     * See docs/on-new-tag.md for sample code
     *
     * @var Callable
     */
    protected $onNewTag = null;

    /**
     * @config
     *
     * @var boolean
     */
    private static $enable_requirements = true;

    /**
     * @link https://github.com/lekoala/bootstrap5-tags#options
     * @config
     * @var array
     */
    private static $default_config = [
        'allowClear' => true,
        'suggestionsThreshold' => 0,
    ];

    public function __construct($name, $title = null, $source = [], $value = null)
    {
        parent::__construct($name, $title, $source, $value);
        $this->setAllowClear(true);
        $this->mergeDefaultConfig();
    }

    /**
     * Gets the source array not including any empty default values.
     *
     * @return array|ArrayAccess
     */
    public function getSource()
    {
        $source = $this->source;
        if (!isset($source[""])) {
            $source = ["" => $this->getPlaceholder()] + $source;
        }
        return $source;
    }

    public function Type()
    {
        return 'bstags';
    }

    public function extraClass()
    {
        return 'no-chosen ' . parent::extraClass();
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

    public function setValue($value, $data = null)
    {
        if ($data instanceof DataObject) {
            $this->loadFromDataObject($data);
        } else {
            // For ajax, we need to add the option to the list
            $class = $this->getAjaxClass();
            if ($value && $class) {
                $arr = $value;
                if (!is_array($arr)) {
                    $arr = [$value];
                }
                foreach ($arr as $id) {
                    $record = DataObject::get_by_id($class, $id);
                    $this->addRecordToSource($record);
                }
            }

            $this->value = $value;
        }
        return $this;
    }

    public function setSubmittedValue($value, $data = null)
    {
        return $this->setValue($value, $data);
    }

    /**
     * Check if the given value is disabled
     * Override base function otherwise it returns false when field is disabled
     *
     * @param string $value
     * @return bool
     */
    protected function isDisabledValue($value)
    {
        return in_array($value, $this->getDisabledItems() ?? []);
    }

    public function performReadonlyTransformation()
    {
        $field = $this->castedCopy(get_class($this));
        if ($this->getForm()) {
            $field->setForm($this->getForm());
        }
        $field->setDisabled(true);
        $field->setSource($this->getSource());
        $field->setReadonly(true);
        // Required to properly set value if no source set
        if ($this->ajaxClass) {
            $field->setAjaxClass($this->getAjaxClass());
        }
        return $field;
    }
    public function getTags()
    {
        return $this->getConfig('allowNew');
    }

    public function setTags($value, $blur = true)
    {
        if ($value) {
            $this->setConfig('addOnBlur', $blur);
        }
        return $this->setConfig('allowNew', $value);
    }

    public function getPlaceholder()
    {
        return $this->getConfig('placeholder');
    }

    public function setPlaceholder($value)
    {
        return $this->setConfig('placeholder', $value);
    }

    public function getAllowClear()
    {
        return $this->getConfig('allowClear');
    }

    public function setAllowClear($value)
    {
        return $this->setConfig('allowClear', $value);
    }

    public function getTokenSeparators()
    {
        return $this->getConfig('separator');
    }

    public function setTokenSeparator($value)
    {
        return $this->setConfig('separator', $value);
    }

    public function getAjax()
    {
        return $this->getConfig('server');
    }

    public function setAjax($url, $opts = [])
    {
        $this->setConfig('server', $url);
        $this->setConfig('serverParams', [
            'SecurityID' => $this->getForm()?->getSecurityToken()->getValue()
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

    /**
     * @return Callable
     */
    public function getOnNewTag()
    {
        return $this->onNewTag;
    }

    /**
     * The callback should return the new id
     *
     * @param Callable $locale
     * @return $this
     */
    public function setOnNewTag($callback)
    {
        $this->onNewTag = $callback;
        return $this;
    }

    public function Field($properties = array())
    {
        // Ajax wizard, needs a form to get controller link
        if ($this->ajaxClass) {
            $url = $this->Link('autocomplete');
            $this->setAjax($url);
        }

        if ($this->isDisabled() || $this->isReadonly()) {
            $this->setAllowClear(false);
        }

        return $this->wrapInElement('bs-tags', $properties);
    }

    public static function requirements()
    {
        Requirements::javascript("lekoala/silverstripe-form-elements: client/custom-elements/bs-tags.min.js");
    }

    /**
     * Validate this field
     *
     * @param Validator $validator
     * @return bool
     */
    public function validate($validator)
    {
        // Tags can be created on the fly and cannot be validated
        if ($this->getTags()) {
            return true;
        }

        if ($this->isAjax()) {
            return true;
        }

        return parent::validate($validator);
    }
}
