<?php

namespace LeKoala\FormElements;

use SilverStripe\i18n\i18nEntityProvider;
use SilverStripe\Forms\HTMLEditor\HTMLEditorConfig;
use SilverStripe\Forms\HTMLEditor\HTMLEditorRuleSet;

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

    private ?HTMLEditorRuleSet $elementRuleSet = null;

    /**
     * @param string $key
     * @return mixed
     */
    public function getOption(string $key): mixed
    {
        if (isset($this->settings[$key])) {
            return $this->settings[$key];
        }
        return null;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return static
     */
    public function setOption(string $key, mixed $value): static
    {
        $this->settings[$key] = $value;
        return $this;
    }

    /**
     * @param array $options
     * @return static
     */
    public function setOptions(array $options): static
    {
        foreach ($options as $key => $value) {
            $this->settings[$key] = $value;
        }
        return $this;
    }

    /**
     * Get all settings
     *
     * @return array<string,mixed>
     */
    public function getSettings(): array
    {
        return $this->settings;
    }

    /**
     * @return array<string,mixed>
     */
    public function getOptions(): array
    {
        return $this->settings;
    }

    /**
     * @return array<string,mixed>
     */
    public function getAttributes(): array
    {
        return parent::getAttributes();
    }

    /**
     * @return array<string,mixed>
     */
    public function getExtraAttributes(): array
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
    protected function getConfig(): array
    {
        $settings = $this->getSettings();
        return $settings;
    }

    public function init(): void
    {
        HTMLEditorConfig::set_active(static::create());
    }

    public function getConfigSchemaData(): array
    {
        $data = parent::getConfigSchemaData();
        return $data;
    }

    public function provideI18nEntities(): array
    {
        $entities = [];
        return $entities;
    }

    /**
     * @return array<int,string>
     */
    public function getRemoveButtons(): array
    {
        return $this->removeButtons;
    }

    /**
     * @param array<int,string> $buttons
     * @return static
     */
    public function setRemoveButtons(array $buttons): static
    {
        $this->removeButtons = $buttons;
        return $this;
    }

    /**
     * @return array<int,string>
     */
    public function getButtons(): array
    {
        return $this->buttons;
    }

    /**
     * @param array<int,string> $buttons
     * @return static
     */
    public function setButtons(array $buttons): static
    {
        $this->buttons = $buttons;
        return $this;
    }

    public function getElementRuleSet(): HTMLEditorRuleSet
    {
        return $this->elementRuleSet ??= new HTMLEditorRuleSet();
    }

    public function setElementRuleSet(HTMLEditorRuleSet $ruleset): static
    {
        $this->elementRuleSet = $ruleset;
        return $this;
    }
}
