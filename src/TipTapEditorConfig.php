<?php

namespace LeKoala\FormElements;

use SilverStripe\i18n\i18nEntityProvider;
use SilverStripe\Forms\HTMLEditor\HTMLEditorConfig;

/**
 * Default configuration for HtmlEditor specific to TipTap
 */
class TipTapEditorConfig extends HTMLEditorConfig implements i18nEntityProvider
{
    /**
     * JS settings
     *
     * @link https://tiptap.dev/guide/configuration
     * @var array
     */
    protected $settings = [];

    /**
     * Holder list of removed buttons
     *
     * @var array
     */
    protected $removeButtons = [];

    /**
     * Holder list of enabled buttons
     *
     * @var array
     */
    protected $buttons = [];

    /**
     * @param string $key
     * @return mixed
     */
    public function getOption($key)
    {
        if (isset($this->settings[$key])) {
            return $this->settings[$key];
        }
        return null;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return self
     */
    public function setOption($key, $value)
    {
        $this->settings[$key] = $value;
        return $this;
    }

    /**
     * @param array $options
     * @return self
     */
    public function setOptions($options)
    {
        foreach ($options as $key => $value) {
            $this->settings[$key] = $value;
        }
        return $this;
    }

    /**
     * Get all settings
     *
     * @return array
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        $attrs = [];
        return $attrs;
    }

    /**
     * @return array
     */
    public function getExtraAttributes()
    {
        $attrs = [];
        if (!empty($this->removeButtons)) {
            $attrs['data-remove-buttons'] = implode(",", $this->removeButtons);
        }
        if (!empty($this->buttons)) {
            $attrs['data-buttons'] = implode(",", $this->buttons);
        }
        return $attrs;
    }

    /**
     */
    protected function getConfig()
    {
        $settings = $this->getSettings();
        return $settings;
    }

    public function init()
    {
        HTMLEditorConfig::set_active(new static);
    }

    public function getConfigSchemaData()
    {
        $data = parent::getConfigSchemaData();
        return $data;
    }

    public function provideI18nEntities()
    {
        $entities = [];
        return $entities;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function getRemoveButtons()
    {
        return $this->removeButtons;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return self
     */
    public function setRemoveButtons($v)
    {
        $this->removeButtons = $v;
        return $this;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function getButtons()
    {
        return $this->buttons;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return self
     */
    public function setButtons($v)
    {
        $this->buttons = $v;
        return $this;
    }
}
