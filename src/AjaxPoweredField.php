<?php

namespace LeKoala\FormElements;

/**
 * A common interface for fields that can provide
 * options through ajax calls (like select2, autocomplete, etc...)
 */
interface AjaxPoweredField
{
    public function getAjax();

    public function setAjax($url, $opts = []);

    /**
     * Get ajax where
     *
     * @return string
     */
    public function getAjaxWhere();

    /**
     * Set ajax where
     *
     * @param string $ajaxWhere
     * @return $this
     */
    public function setAjaxWhere($ajaxWhere);

    /**
     * Get ajax class
     *
     * @return string
     */
    public function getAjaxClass();

    /**
     * Set ajax class
     *
     * @param string $ajaxClass  Ajax class
     * @return $this
     */
    public function setAjaxClass(string $ajaxClass);

    /**
     * Are we using the ajax source?
     *
     * @return boolean
     */
    public function isAjax();

    /**
     * @return array
     */
    public function getServerVars();
}
