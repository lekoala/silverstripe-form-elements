<?php

namespace LeKoala\FormElements;

use SilverStripe\Core\Convert;
use SilverStripe\Core\Manifest\ModuleLoader;
use SilverStripe\Core\Manifest\ModuleResource;

/**
 * If you want to have a default_config, it's up to you to set
 * it in the constructor of your classes (by calling mergeDefaultConfig)
 */
trait BaseElement
{
    /**
     * Config array
     *
     * @var array<string,mixed>
     */
    protected $config = [];

    /**
     * @var array<string,mixed>
     */
    protected $elementAttributes = [];

    /**
     * Get a config key value
     *
     * @param string $key
     * @return string
     */
    public function getConfig($key)
    {
        if (isset($this->config[$key])) {
            return $this->config[$key];
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
        $json = json_encode($this->config);
        return $json;
    }

    /**
     * Set a config value
     *
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function setConfig($key, $value)
    {
        if ($value !== null) {
            $this->config[$key] = $value;
        } else {
            unset($this->config[$key]);
        }
        return $this;
    }

    /**
     * @return array<string,mixed>
     */
    public function readConfig()
    {
        return $this->config;
    }

    /**
     * Merge default_config into config
     * @return void
     */
    public function mergeDefaultConfig()
    {
        $this->config = array_merge(self::config()->default_config, $this->config);
    }

    /**
     * @return $this
     */
    public function clearConfig()
    {
        $this->config = [];
        return $this;
    }

    /**
     * @param array $config
     * @return $this
     */
    public function replaceConfig($config)
    {
        $this->config = $config;
        return $this;
    }

    /**
     * @return array<string,mixed>
     */
    public function getElementAttributes()
    {
        return $this->elementAttributes;
    }

    /**
     * @return string
     */
    public function getElementAttributesHTML()
    {
        $attributes = $this->getElementAttributes();
        $parts = [];

        foreach ($attributes as $name => $value) {
            if (is_bool($value)) {
                $value = $value ? "true" : "false";
            } else {
                if (is_scalar($value)) {
                    $value = (string) $value;
                } else {
                    $value = json_encode($value);
                }
            }

            $parts[] = sprintf('%s="%s"', Convert::raw2att($name), Convert::raw2att($value));
        }

        return implode(' ', $parts);
    }

    /**
     * @return mixed
     */
    public function getElementAttribute(string $k)
    {
        return $this->elementAttributes[$k] ?? null;
    }

    /**
     * @param mixed $v
     * @return $this
     */
    public function setElementAttribute(string $k, $v)
    {
        $this->elementAttributes[$k] = $v;
        return $this;
    }

    /**
     * @param string $el
     * @param array<string,mixed> $properties
     * @param string $extraHtml
     * @return string
     */
    protected function wrapInElement($el, $properties = [], string $extraHtml = '')
    {
        if (static::config()->enable_requirements) {
            static::requirements();
        }
        $html = parent::Field($properties);
        $config = $this->getConfigAsJson();

        $attrsHTML = $this->getElementAttributesHTML();

        // Simply wrap with custom element and set config
        $html = "<$el data-config='$config' $attrsHTML>{$html}{$extraHtml}</$el>";
        return $html;
    }

    /**
     * Helper to access this module resources
     *
     * @param string $path
     * @return ModuleResource
     */
    protected static function moduleResource($path)
    {
        return ModuleLoader::getModule('lekoala/silverstripe-form-elements')->getResource($path);
    }
}
