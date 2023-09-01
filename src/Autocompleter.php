<?php

namespace LeKoala\FormElements;

use SilverStripe\ORM\DataList;
use SilverStripe\ORM\Relation;
use SilverStripe\ORM\DataObject;
use SilverStripe\Control\Controller;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\HTTPResponse;
use SilverStripe\Forms\ListboxField;
use SilverStripe\ORM\DataObjectInterface;

/**
 * Provide most of the implementation for the AjaxPoweredField
 * You need to implement yourself:
 * - getAjax
 * - setAjax
 * - isAjax
 */
trait Autocompleter
{
    /**
     * @config
     * @var array
     */
    private static $allowed_actions = [
        'autocomplete'
    ];

    /**
     * Ajax class
     *
     * @var string
     */
    protected $ajaxClass;

    /**
     * Ajax where
     *
     * @var string|array
     */
    protected $ajaxWhere;

    /**
     * Ajax filters
     *
     * @var array
     */
    protected $ajaxFilters = [];

    /**
     * @var boolean
     */
    protected $ajaxFullSearch = false;

    /**
     * @var int
     */
    protected $recordsLimit = 1000;

    /**
     * @var string
     */
    protected $customSearchField;

    /**
     * @var array
     */
    protected $customSearchCols;

    public function autocomplete(HTTPRequest $request)
    {
        if ($this->isDisabled() || $this->isReadonly()) {
            return $this->httpError(403);
        }

        // CSRF check
        $token = $this->getForm()->getSecurityToken();
        if (!$token->checkRequest($request)) {
            return $this->httpError(400, "Invalid token");
        }

        $name = $this->getName();

        $vars = $this->getServerVars();

        // Don't use by default % at the start of term as it prevents use of indexes
        $term = $request->getVar($vars['queryParam']) . '%';
        $term = str_replace(' ', '%', $term);
        if ($this->ajaxFullSearch) {
            $term = "%" . $term;
        }

        $class = $this->ajaxClass;

        $sng = $class::singleton();
        $baseTable = $sng->baseTable();

        $searchField = '';
        $searchCandidates = [
            'Title', 'Name', 'Surname', 'Email', 'ID'
        ];

        // Ensure field exists, this is really rudimentary
        $db = $class::config()->db;
        foreach ($searchCandidates as $searchCandidate) {
            if ($searchField) {
                continue;
            }
            if (isset($db[$searchCandidate])) {
                $searchField = $searchCandidate;
            }
        }

        $searchCols = [$searchField];

        // For Surname, do something better
        if ($searchField == "Surname") {
            // Show first name, surname
            if (isset($db['FirstName'])) {
                $searchField = ['FirstName', 'Surname'];
                $searchCols = ['FirstName', 'Surname'];
            }
            // Also search email
            if (isset($db['Email'])) {
                $searchCols = ['FirstName', 'Surname', 'Email'];
            }
        }


        if ($this->customSearchField) {
            $searchField = $this->customSearchField;
        }
        if ($this->customSearchCols) {
            $searchCols = $this->customSearchCols;
        }

        /** @var DataList $list */
        $list = $sng::get();

        // Make sure at least one field is not null...
        $where = [];
        foreach ($searchCols as $searchCol) {
            $where[] = $searchCol . ' IS NOT NULL';
        }
        $list = $list->whereAny($where);
        // ... and matches search term ...
        $where = [];
        foreach ($searchCols as $searchCol) {
            $where[$searchCol . ' LIKE ?'] = $term;
        }
        $list = $list->whereAny($where);
        // ... and any user set requirements
        $filters = $this->ajaxFilters;
        if (!empty($filters)) {
            $list = $list->filter($filters);
        }
        $where = $this->ajaxWhere;
        if (!empty($where)) {
            // Deal with in clause
            if (is_string($where)) {
                $customWhere = $where;
            } else {
                $customWhere = [];
                foreach ($where as $col => $param) {
                    // For array, we need a IN statement with a ? for each value
                    if (is_array($param)) {
                        $prepValue = [];
                        $params = [];
                        foreach ($param as $paramValue) {
                            $params[] = $paramValue;
                            $prepValue[] = "?";
                        }
                        $customWhere["$col IN (" . implode(',', $prepValue) . ")"] = $params;
                    } else {
                        $customWhere["$col = ?"] = $param;
                    }
                }
            }
            $list = $list->where($customWhere);
        }

        $list = $list->limit($this->recordsLimit);

        $results = iterator_to_array($list);
        $data = [];
        foreach ($results as $record) {
            if (is_array($searchField)) {
                $labelParts = [];
                foreach ($searchField as $sf) {
                    $labelParts[] = $record->$sf ?? "";
                }
                $label = implode(" ", $labelParts);
            } else {
                $label = $record->$searchField ?? "";
            }
            $data[] = [
                $vars['valueField'] => $record->ID,
                $vars['labelField'] => $label ?? "(no label)",
            ];
        }
        $body = json_encode([$vars['dataKey'] => $data]);

        $response = new HTTPResponse($body);
        $response->addHeader('Content-Type', 'application/json');
        return $response;
    }

    /**
     * Add a record to the source
     *
     * Useful for ajax scenarios where the list is not prepopulated but still needs to display
     * something on first load
     *
     * @param DataObject $record
     * @return boolean true if the record has been added, false otherwise
     */
    public function addRecordToSource($record)
    {
        if (!$record || !$this->isAjax()) {
            return false;
        }
        $source = $this->getSource();
        // It's already in the source
        if (isset($source[$record->ID])) {
            return false;
        }
        $source[$record->ID] = $record->getTitle();
        $this->setSource($source);
        return true;
    }

    /**
     * Method copied from MultiSelectField to make sure we call $this->setValue
     *
     * @param DataObject|DataObjectInterface $record
     */
    public function loadFromDataObject(DataObjectInterface $record)
    {
        $fieldName = $this->getName();
        if (empty($fieldName) || empty($record)) {
            return;
        }

        $relation = $record->hasMethod($fieldName)
            ? $record->$fieldName()
            : null;

        $isMulti = $this instanceof ListboxField;

        // Detect DB relation or field
        $value = null;
        if ($relation instanceof Relation) {
            // Load ids from relation
            $value = array_values($relation->getIDList() ?? []);
        } elseif ($record->hasField($fieldName)) {
            $str = $record->$fieldName;
            if ($str) {
                if (strpos($str, '[') === 0) {
                    $value = json_decode($str, JSON_OBJECT_AS_ARRAY);
                } else {
                    $value = explode(',', $str);
                }
            }
        }

        if ($value) {
            if ($isMulti) {
                $this->setValue($value);
            } else {
                $this->setValue(array_pop($value));
            }
        }
    }

    /**
     * Return a link to this field.
     *
     * @param string $action
     * @return string
     */
    public function Link($action = null)
    {
        if ($this->form) {
            return Controller::join_links($this->form->FormAction(), 'field/' . $this->getName(), $action);
        }
        return Controller::join_links(Controller::curr()->Link(), 'field/' . $this->getName(), $action);
    }

    abstract public function getAjax();

    abstract public function setAjax($url, $opts = []);

    abstract public function isAjax();

    /**
     * Define a callback that returns the results as a map of id => title
     *
     * @param string $class
     * @param array $filters
     * @param string|array $where
     * @return $this
     */
    public function setAjaxWizard($class, $filters = [], $where = null)
    {
        $this->ajaxClass = $class;
        $this->ajaxFilters = $filters;
        $this->ajaxWhere = $where;
        return $this;
    }

    /**
     * Get ajax where
     *
     * @return string
     */
    public function getAjaxWhere()
    {
        return $this->ajaxWhere;
    }

    /**
     * Set ajax where
     *
     * @param string $ajaxWhere
     * @return $this
     */
    public function setAjaxWhere($ajaxWhere)
    {
        $this->ajaxWhere = $ajaxWhere;
        return $this;
    }

    /**
     * Get ajax filters
     *
     * @return string
     */
    public function getAjaxFilters()
    {
        return $this->ajaxFilters;
    }

    /**
     * Set ajax filters
     *
     * @param string $ajaxFilters
     * @return $this
     */
    public function setAjaxFilters($ajaxFilters)
    {
        $this->ajaxFilters = $ajaxFilters;
        return $this;
    }

    /**
     * Get ajax class
     *
     * @return string
     */
    public function getAjaxClass()
    {
        return $this->ajaxClass;
    }

    /**
     * Set ajax class
     *
     * @param string $ajaxClass  Ajax class
     * @return $this
     */
    public function setAjaxClass(string $ajaxClass)
    {
        $this->ajaxClass = $ajaxClass;

        return $this;
    }

    /**
     * Get the value of customSearchField
     *
     * @return string
     */
    public function getCustomSearchField(): string
    {
        return $this->customSearchField;
    }

    /**
     * Set the value of customSearchField
     *
     * It must be a valid sql expression like CONCAT(FirstName,' ',Surname)
     *
     * This will be the label returned by the autocomplete
     *
     * @param string $customSearchField
     * @return $this
     */
    public function setCustomSearchField(string $customSearchField)
    {
        $this->customSearchField = $customSearchField;
        return $this;
    }

    /**
     * Get the value of customSearchCols
     *
     * @return array
     */
    public function getCustomSearchCols()
    {
        return $this->customSearchCols;
    }

    /**
     * Set the value of customSearchCols
     *
     * @param array $customSearchCols
     * @return $this
     */
    public function setCustomSearchCols(array $customSearchCols)
    {
        $this->customSearchCols = $customSearchCols;
        return $this;
    }

    /**
     * Get the value of recordsLimit
     * @return int
     */
    public function getRecordsLimit()
    {
        return $this->recordsLimit;
    }

    /**
     * Set the value of recordsLimit
     *
     * @param int $recordsLimit
     */
    public function setRecordsLimit($recordsLimit)
    {
        $this->recordsLimit = $recordsLimit;
        return $this;
    }

    /**
     * Get the value of ajaxFullSearch
     * @return bool
     */
    public function getAjaxFullSearch()
    {
        return $this->ajaxFullSearch;
    }

    /**
     * Set the value of ajaxWildcard
     *
     * @param boolean $ajaxWildcard
     */
    public function setAjaxFullSearch($ajaxFullSearch)
    {
        $this->ajaxFullSearch = $ajaxFullSearch;
        return $this;
    }
}
