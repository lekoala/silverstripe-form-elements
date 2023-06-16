<?php

namespace LeKoala\FormElements;

use SilverStripe\ORM\Relation;
use SilverStripe\ORM\DataObject;
use SilverStripe\View\Requirements;
use SilverStripe\ORM\DataObjectInterface;

/**
 * Implements AjaxPoweredField + TagsField
 */
trait TomSelect
{
    use BaseElement;
    use Localize;
    use Autocompleter;

    /**
     * @var boolean
     */
    protected $multiple = false;

    /**
     * Callback to create tags
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
     * @link https://github.com/orchidjs/tom-select/blob/master/src/defaults.ts
     * @config
     * @var array
     */
    private static $default_config = [
        "valueField" => "id",
        "labelField" => "text",
        "searchField" => ["text"]
    ];

    public function __construct($name, $title = null, $source = [], $value = null)
    {
        parent::__construct($name, $title, $source, $value);
        $this->setAllowClear(true);
        $this->mergeDefaultConfig();
    }

    public function Type()
    {
        return 'tomselect';
    }

    public function extraClass()
    {
        return 'no-chosen ' . parent::extraClass();
    }

    public function getServerVars()
    {
        return [
            'queryParam' => 'q',
            'dataKey' => 'data',
            'valueField' => 'id',
            'labelField' => 'text',
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

    public function getPlugin($plugin)
    {
        if (isset($this->config['plugins'][$plugin])) {
            return $this->config['plugins'][$plugin];
        }
    }

    public function removePlugin($plugin)
    {
        if (isset($this->config['plugins'][$plugin])) {
            unset($this->config['plugins'][$plugin]);
        }
        return $this;
    }

    public function setPlugin($plugin, $config = [])
    {
        $plugins = $this->config['plugins'] ?? [];
        if (empty($plugins)) {
            $this->config['plugins'] = $plugins;
        }
        $pluginConfig = $plugins[$plugin] ?? [];
        $this->config['plugins'][$plugin] = array_merge($pluginConfig, $config);
        return $this;
    }

    public function getTags()
    {
        return $this->getConfig('create');
    }

    public function setTags($value, $blur = true)
    {
        if ($value) {
            $this->setConfig('createOnBlur', $blur);
        }
        return $this->setConfig('create', $value);
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
        return $this->getConfig('remove_button');
    }

    public function setAllowClear($value)
    {
        // @link https://tom-select.js.org/plugins/remove-button/
        if ($value) {
            return $this->setPlugin('remove_button', ['title' => _t('TomSelect.Remove', 'Remove')]);
        } else {
            return $this->removePlugin('remove_button');
        }
    }

    public function getTokenSeparators()
    {
        return $this->getConfig('delimiter');
    }

    public function setTokenSeparator($value)
    {
        return $this->setConfig('delimiter', $value);
    }

    public function setAjaxLoad($callbackName, $valueField = "id", $labelField = "text", $searchField = "text")
    {
        $this->setConfig('load', $callbackName);
        $this->setConfig('valueField', $valueField);
        $this->setConfig('labelField', $labelField);
        $this->setConfig('searchField', $searchField);
        return $this;
    }

    public function getAjax()
    {
        return $this->getConfig('_ajax');
    }

    public function setAjax($url, $opts = [])
    {
        $ajax = array_merge([
            'url' => $url,
            'paramName' => "q",
            'params' => [
                'SecurityID' => $this->getForm()->getSecurityToken()->getValue()
            ]
        ], $opts);
        return $this->setConfig('_ajax', $ajax);
    }

    /**
     * @return boolean
     */
    public function isAjax()
    {
        return $this->ajaxClass || $this->getConfig('_ajax');
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
        // Set lang based on locale
        $lang = substr($this->getLocale(), 0, 2);
        if ($lang != 'en') {
            $this->setConfig('language', $lang);
        }

        if ($this->isDisabled() || $this->isReadonly()) {
            $this->setConfig('disabled', true);
            $this->setAllowClear(false);
        }

        // Set RTL
        $dir = $this->getScriptDir();
        if ($dir == 'rtl') {
            $this->setConfig('dir', $dir);
        }

        // Ajax wizard, needs a form to get controller link
        if ($this->ajaxClass) {
            $url = $this->Link('autocomplete');
            $this->setAjax($url);
        }

        if (self::config()->enable_requirements) {
            self::requirements();
        }

        $html = parent::Field($properties);
        $config = $this->getConfigAsJson();

        $html = str_replace("form-select", "", $html);

        // Simply wrap with custom element and set config
        $html = "<tom-select data-config='" . $config . "'>" . $html . '</tom-select>';

        return $html;
    }

    public static function requirements()
    {
        Requirements::javascript("lekoala/silverstripe-form-elements: client/custom-elements/tom-select.min.js");
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
