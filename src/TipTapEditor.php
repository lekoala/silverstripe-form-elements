<?php

namespace LeKoala\FormElements;

use SilverStripe\Forms\FormField;
use SilverStripe\View\Requirements;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;

/**
 * @link https://tiptap.dev/
 */
class TipTapEditor extends HTMLEditorField
{
    use BaseElement;

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

    /**
     * ID or instance of editorconfig
     *
     * @var string|TipTapEditorConfig
     */
    protected $editorConfig = null;

    public function __construct($name, $title = null, $value = '', $config = null)
    {
        parent::__construct($name, $title, $value);
        $this->mergeDefaultConfig();

        if (!$config) {
            $this->editorConfig = new TipTapEditorConfig;
        }
    }

    /**
     * Get the config (always as json object)
     * @return string
     */
    public function getConfigAsJson()
    {
        if (empty($this->config)) {
            return '{}';
        }
        $json = json_encode($this->editorConfig->getSettings());
        return $json;
    }

    public function Type()
    {
        return 'tiptap';
    }

    public function extraClass()
    {
        return 'text ' . parent::extraClass();
    }

    public function getAttributes()
    {
        // Fix CSS height based on rows
        $rowHeight = $this->config()->get('fixed_row_height');
        $attributes = [];
        if ($rowHeight) {
            $height = $this->getRows() * $rowHeight;
            $attributes['style'] = sprintf('height: %dpx;', $height);
        }

        return FormField::getAttributes();
    }

    public function getElementAttributes()
    {
        $attrs = $this->elementAttributes;
        $extraAttrs = $this->editorConfig->getExtraAttributes();
        return array_merge($attrs, $extraAttrs);
    }

    public function Field($properties = array())
    {
        return $this->wrapInElement('tiptap-editor', $properties);
    }

    public static function requirements()
    {
        Requirements::javascript("lekoala/silverstripe-form-elements: client/custom-elements/tiptap-editor.min.js");
    }
}
