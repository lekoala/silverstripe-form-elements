<?php

namespace LeKoala\FormElements;

use SilverStripe\i18n\i18n;
use SilverStripe\Forms\TextField;
use SilverStripe\View\Requirements;

/**
 * @link https://chmln.github.io/flatpickr
 */
class FlatpickrField extends TextField implements LocalizableField
{
    use BaseElement;
    use Localize;
    use HasDateTimeFormat;

    // Formats
    const DEFAULT_DATE_FORMAT = 'Y-m-d';
    const DEFAULT_TIME_FORMAT = 'H:i';
    const DEFAULT_DATETIME_FORMAT = 'Y-m-d H:i';
    const DEFAULT_ALT_DATE_FORMAT = 'l j F Y';
    const DEFAULT_ALT_TIME_FORMAT = 'H:i';
    const DEFAULT_ALT_DATETIME_FORMAT = 'l j F Y H:i';

    /**
     * Override locale. If empty will default to current locale
     *
     * @var string
     */
    protected $locale = null;

    /**
     * Disable description
     *
     * @var boolean
     */
    protected $disableDescription = false;

    /**
     * Array of plugins
     *
     * @var array
     */
    protected $plugins = [];

    /**
     * @var array
     */
    protected $hooks = [];

    /**
     * @var string
     */
    protected $theme;

    /**
     * @config
     * @var boolean
     */
    private static $enable_requirements = true;

    /**
     * @config
     * @link https://flatpickr.js.org/options/
     * @var array
     */
    private static $default_config = [
        'defaultDate' => '',
        'time_24hr' => true,
    ];

    public function __construct($name, $title = null, $value = '', $maxLength = null, $form = null)
    {
        parent::__construct($name, $title, $value, $maxLength, $form);

        $this->config = self::config()->default_config;
        $this->setDatetimeFormat($this->convertDatetimeFormat(self::DEFAULT_ALT_DATE_FORMAT));
        $this->setAltFormat(self::DEFAULT_ALT_DATE_FORMAT);
    }

    /**
     * Get the value of theme
     *
     * @return string
     */
    public function getTheme()
    {
        return $this->theme;
    }

    /**
     * Set the value of theme
     *
     * @param string $theme
     *
     * @return $this
     */
    public function setTheme($theme)
    {
        $this->theme = $theme;
        return $this;
    }

    /**
     * Convert a datetime format from Flatpickr to CLDR
     *
     * This allow to display the right format in php
     *
     * @see https://flatpickr.js.org/formatting/
     * @param string $format
     * @return string
     */
    protected function convertDatetimeFormat($format)
    {
        return str_replace(
            ['F', 'l', 'j', 'd', 'H', 'i', 's'],
            ['MMMM', 'cccc', 'd', 'dd', 'HH', 'mm', 'ss'],
            $format
        );
    }

    public function Type()
    {
        return 'flatpickr';
    }

    public function extraClass()
    {
        return 'text ' . parent::extraClass();
    }

    public function getEnableTime()
    {
        return $this->getConfig('enableTime');
    }

    public function setEnableTime($value)
    {
        $this->setDatetimeFormat($this->convertDatetimeFormat(self::DEFAULT_ALT_DATETIME_FORMAT));
        $this->setAltFormat(self::DEFAULT_ALT_DATETIME_FORMAT);
        $this->setConfirmDate(true);
        return $this->setConfig('enableTime', $value);
    }

    public function getNoCalendar()
    {
        return $this->getConfig('noCalendar');
    }

    public function setNoCalendar($value)
    {
        $this->setDatetimeFormat($this->convertDatetimeFormat(self::DEFAULT_ALT_TIME_FORMAT));
        $this->setAltFormat(self::DEFAULT_ALT_TIME_FORMAT);
        return $this->setConfig('noCalendar', $value);
    }

    /**
     * Show the user a readable date (as per altFormat), but return something totally different to the server.
     *
     * @return string
     */
    public function getAltInput()
    {
        return $this->getConfig('altInput');
    }

    public function setAltInput($value)
    {
        return $this->setConfig('altInput', $value);
    }

    /**
     * Exactly the same as date format, but for the altInput field
     *
     * @return string
     */
    public function getAltFormat()
    {
        return $this->getConfig('altFormat');
    }

    /**
     * Please note that altFormat should match the format for the database
     *
     * @param string $value
     * @return $this
     */
    public function setAltFormat($value)
    {
        return $this->setConfig('altFormat', $value);
    }

    public function getMinDate()
    {
        return $this->getConfig('minDate');
    }

    public function setMinDate($value)
    {
        return $this->setConfig('minDate', $value);
    }

    public function getMaxDate()
    {
        return $this->getConfig('maxDate');
    }

    public function setMaxDate($value)
    {
        return $this->setConfig('maxDate', $value);
    }

    public function getInline()
    {
        return $this->getConfig('inline');
    }

    public function setInline($value)
    {
        return $this->setConfig('inline', (bool)$value);
    }

    public function getDefaultDate()
    {
        return $this->getConfig('defaultDate');
    }

    public function setDefaultDate($value)
    {
        return $this->setConfig('defaultDate', $value);
    }

    public function getDateFormat()
    {
        return $this->getConfig('dateFormat');
    }

    public function setDateFormat($value)
    {
        return $this->setConfig('dateFormat', $value);
    }

    public function getDisabledDates()
    {
        return $this->getConfig('disable');
    }

    /**
     * Accepts:
     * - an array of values:  ["2025-01-30", "2025-02-21", "2025-03-08"]
     * - an array of ranges:  [["from" => "2025-01-30", "to" => "2025-02-10]]
     * Js functions are not supported at this time
     *
     * @param array $value
     * @return $this
     */
    public function setDisabledDates($value)
    {
        return $this->setConfig('disable', $value);
    }

    public function getEnabledDates()
    {
        return $this->getConfig('enable');
    }

    /**
     * Accepts:
     * - an array of values:  ["2025-01-30", "2025-02-21", "2025-03-08"]
     * - an array of ranges:  [["from" => "2025-01-30", "to" => "2025-02-10]]
     * Js functions are not supported at this time
     *
     * @param array $value
     * @return $this
     */
    public function setEnabledDates($value)
    {
        return $this->setConfig('enable', $value);
    }

    /**
     * Get id of the second element
     *
     * @return string
     */
    public function getRange()
    {
        return $this->getElementAttribute('data-range');
    }

    /**
     * Set id of the second element
     *
     * eg: #Form_ItemEditForm_EndDate
     *
     * @param string $range Id of the second element
     * @param bool $confirm
     * @return $this
     */
    public function setRange($range, $confirm = true)
    {
        $this->setElementAttribute('data-range', $range);
        if ($confirm) {
            $this->setConfirmDate(true);
        }
        return $this;
    }

    /**
     * Get add confirm box
     *
     * @return bool
     */
    public function getConfirmDate()
    {
        return $this->getElementAttribute('data-confirm-date');
    }

    /**
     * Set add confirm box
     *
     * @param bool $confirmDate Add confirm box
     *
     * @return $this
     */
    public function setConfirmDate($confirmDate)
    {
        return $this->setElementAttribute('data-confirm-date', $confirmDate);
    }

    /**
     * @return bool
     */
    public function getMonthSelect()
    {
        return $this->getElementAttribute('data-month-select');
    }

    /**
     * @param bool $monthSelect
     *
     * @return $this
     */
    public function setMonthSelect($monthSelect)
    {
        return $this->setElementAttribute('data-month-select', $monthSelect);
    }

    /**
     * @param string $hook
     * @return string
     */
    public function getHook($hook)
    {
        return $this->hooks[$hook] ?? '';
    }

    /**
     * @param string $hook
     * @param string $callbackName
     * @return $this
     */
    public function setHook($hook, $callbackName)
    {
        $this->hooks[$hook] = $callbackName;
        return $this;
    }

    public function setDescription($description)
    {
        // Allows blocking scaffolded UI desc that has no uses
        if ($this->disableDescription) {
            return $this;
        }
        return parent::setDescription($description);
    }

    public function Field($properties = array())
    {
        // Set lang based on locale
        $lang = substr($this->getLocale(), 0, 2);
        if ($lang != 'en') {
            $this->setConfig('locale', $lang);
        }

        if ($this->hooks) {
            // Use replace callback format
            foreach ($this->hooks as $k => $v) {
                $this->setConfig($k, [
                    "__fn" => $v
                ]);
            }
        }

        if (self::config()->enable_requirements) {
            self::requirements($lang);
        }

        if ($this->readonly) {
            if ($this->getNoCalendar() && $this->getEnableTime()) {
                $this->setAttribute('placeholder', _t('FlatpickrField.NO_TIME_SELECTED', 'No time'));
            } else {
                $this->setAttribute('placeholder', _t('FlatpickrField.NO_DATE_SELECTED', 'No date'));
            }
        } else {
            $this->setAttribute('placeholder', _t('FlatpickrField.SELECT_A_DATE', 'Select a date...'));
        }

        // Time formatting can cause value change for no reasons
        $this->addExtraClass('no-change-track');

        return $this->wrapInElement('flatpickr-input', $properties);
    }

    /**
     * Add requirements
     *
     * @param string $lang
     * @return void
     */
    public static function requirements($lang = null)
    {
        if ($lang === null) {
            $lang = substr(i18n::get_locale(), 0, 2);
        }

        // We still need a copy of the cdn js files to load l10n
        $langResource = self::moduleResource("client/cdn/flatpickr/l10n/fr.js");
        Requirements::javascript("lekoala/silverstripe-form-elements: client/custom-elements/flatpickr-input.min.js");

        // Load lang (leverage waitDefined from custom element)
        if ($lang != 'en') {
            $basePath = dirname($langResource->getPath());
            if (!is_file("$basePath/$lang.js")) {
                $lang = 'en'; // revert to en
            }
        }
        if ($lang != 'en') {
            //eg: https://cdn.jsdelivr.net/npm/flatpickr@4/dist/l10n/fr.js
            Requirements::javascript("lekoala/silverstripe-form-elements: client/cdn/flatpickr/l10n/$lang.js");
        }
    }

    /**
     * Get disable description
     *
     * @return  boolean
     */
    public function getDisableDescription()
    {
        return $this->disableDescription;
    }

    /**
     * Set disable description
     *
     * @param boolean $disableDescription
     *
     * @return $this
     */
    public function setDisableDescription($disableDescription)
    {
        $this->disableDescription = $disableDescription;
        return $this;
    }

    public function setReadonly($readonly)
    {
        $this->setConfig('clickOpens', !$readonly);
        $this->setConfig('allowInput', !$readonly);
        return parent::setReadonly($readonly);
    }

    /**
     * Returns a read-only version of this field.
     *
     * @return FormField
     */
    public function performReadonlyTransformation()
    {
        $clone = $this->castedCopy(self::class);
        $clone->replaceConfig($this->config);
        $clone->setReadonly(true);
        return $clone;
    }

    /**
     * Set typical options for a DateTime field
     * @return $this
     */
    public function setDateTimeOptions()
    {
        $this->setEnableTime(true);
        $this->setDisableDescription(true);
        return $this;
    }

    /**
     * Set typical options for a Time field
     * @return $this
     */
    public function setTimeOptions()
    {
        $this->setEnableTime(true);
        $this->setNoCalendar(true);
        return $this;
    }
}
