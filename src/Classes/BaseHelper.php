<?php

namespace SOS\QueryHelper\Classes;

use Illuminate\Support\Facades\DB;

abstract class BaseHelper
{
    /**
     * @var string
     */
    private $tableName = '';
    /**
     * @var array
     */
    protected $tables = [];
    /**
     * @var mixed
     */
    private $ids;
    /**
     * @var mixed
     */
    private $values;
    /**
     * @var string
     */
    private $query;
    /**
     * @var string
     */
    private $field = 'id';
    /**
     * @var int
     */
    private $allowedWhereInQueryNumber = 0;

    /**
     * @return int
     */
    protected function getAllowedWhereInQueryNumber()
    {
        return $this->allowedWhereInQueryNumber;
    }

    /**
     * @param  int  $allowedWhereInQueryNumber
     *
     * @return  BaseHelper
     */
    public function setAllowedWhereInQueryNumber($allowedWhereInQueryNumber)
    {
        $this->allowedWhereInQueryNumber = $allowedWhereInQueryNumber;

        return $this;
    }

    /**
     * @var bool
     */
    private $isSelectStatus = false;

    /**
     * @return bool
     * @author karam mustaf
     */
    protected function isSelectStatus()
    {
        return $this->isSelectStatus;
    }

    /**
     * @param  bool  $isSelectStatus
     *
     *
     * @return BaseHelper
     * @author karam mustaf
     */
    protected function setIsSelectStatus($isSelectStatus = true)
    {
        $this->isSelectStatus = $isSelectStatus;

        return $this;
    }

    /**
     * @param  array  $selection
     *
     * @return BaseHelper
     * @author karam mustaf
     */
    public function setSelection($selection)
    {
        if ($selection != ['id']) {
            $this->selection = $selection;
        }

        return $this;
    }

    /**
     * @param  bool  $implode
     *
     * @return string
     * @author karam mustaf
     */
    protected function getSelection($implode = true)
    {
        if ($implode) {
            return implode(',', $this->selection);
        }

        return $this->selection;
    }

    /**
     * @return string
     */
    protected function getField()
    {
        return $this->field;
    }

    /**
     * @param  string  $field
     *
     * @return BaseHelper
     * @author karam mustafa
     */
    public function setField($field)
    {
        $this->field = $field;

        return $this;
    }

    /**
     * @return array
     */
    protected function getTables()
    {
        return $this->tables;
    }

    /**
     * @param $tables
     *
     * @return BaseHelper
     */
    public function setTables($tables)
    {
        // check if the parameter is array, then we will merge the parameters
        if (is_array($tables)) {
            $this->tables = array_merge($this->tables, $tables);
        }

        // if the parameter is string, then push this table name to the tables property.
        if (is_string($tables)) {
            array_push($this->tables, $tables);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @param  string  $query
     */
    protected function setQuery($query)
    {
        $this->query = $query;
    }

    /**
     * @param  string  $query
     */
    protected function appendToQuery($query)
    {
        $this->query .= $query;
    }

    /**
     * set the query string in the first of strings.
     *
     * @param  string  $query
     */
    protected function unshiftInQuery($query)
    {
        $this->query = $query." ".$this->query;
    }

    /**
     * @return array
     * @author karam mustafa
     */
    protected function getValues()
    {
        return $this->values;
    }

    /**
     * @param  mixed  $values
     *
     * @return BaseHelper
     * @author karam mustafa
     */
    public function setValues($values)
    {
        $this->values = $values;

        return $this;
    }

    /**
     * @return string
     */
    protected function getTableName()
    {
        return $this->tableName;
    }

    /**
     * @param  string  $tableName
     *
     * @return BaseHelper
     * @author karam mustafa
     */
    public function setTableName($tableName)
    {
        $this->tableName = $tableName;

        return $this;
    }

    /**
     * @return array
     */
    protected function getIds()
    {
        return $this->ids;
    }

    /**
     * @param  array  $ids
     *
     * @return BaseHelper
     * @author karam mustafa
     */
    public function setIds($ids)
    {
        $this->ids = $ids;

        return $this;
    }

    /**
     * @var array
     */
    public $savedItems = [];

    /**
     * @return array
     */
    public function getSavedItems()
    {
        return $this->savedItems;
    }

    /**
     * @param  array  $savedItems
     *
     * @return BaseHelper
     */
    public function setSavedItems($savedItems)
    {
        $this->savedItems = array_merge($this->savedItems, $savedItems);

        return $this;
    }

    /**
     * this function will chunk set of data to custom size, and each size will apply the callback.
     *
     * @param $ids
     * @param  callable|null  $callbackIfPassed
     * @param  null  $chunkCountAllowed
     *
     * @return mixed
     */
    public function checkIfQueryAllowed($ids, $callbackIfPassed = null, $chunkCountAllowed = null)
    {
        if (!isset($chunckCountAllowed)) {
            $chunkCountAllowed = $this->getAllowedWhereInQueryNumber();
        }

        $items = [];
        $lists = collect($ids)->chunk($chunkCountAllowed + 1);
        if (!is_null($callbackIfPassed)) {
            foreach ($lists as $index => $list) {
                $items[] = $callbackIfPassed($list, $index);
            }
        }
        $this->savedItems = $items;

        return $items;
    }

    /**
     * execute query statement
     *
     * @param  callable|null  $callback
     *
     * @return BaseHelper
     * @throws \Exception
     * @author karam mustafa
     */
    public function executeAll($callback = null)
    {
        try {
            // if the user pass a callback, then execute this callback and inject the query on it.
            if (isset($callback)) {
                return $callback($this->getQuery());
            }

            // check if we are now in selection status
            // so we will execute this selection query.
            if ($this->isSelectStatus()) {
                return DB::select(DB::raw($this->getQuery()));
            }

            // otherwise, then execute what ever this statement.
            DB::statement($this->getQuery());

            return $this;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * execute query statements without preparing them.
     *
     * @return BaseHelper
     * @throws \Exception
     * @author karam mustafa
     */
    public function executeWithoutPrepare()
    {
        try {
            $this->executeAll(function (){
                DB::unprepared($this->getQuery());
            });

            return $this;

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * clear all inserted data in class properties.
     * this function ignore the savedDataItems property,
     * because this package designed to be a strong declarative concept,
     * and we want a node to store all the work on.
     *
     * @return BaseHelper
     * @author karam mustafa
     */
    public function clearAll()
    {
        $this->setIds([]);
        $this->setValues([]);
        $this->setQuery('');
        $this->setField('');
        $this->setSelection([]);
        $this->setTables([]);
        $this->setTableName('');

        return $this;
    }

    /**
     * check if the value is float.
     *
     * @param $index
     *
     * @return bool
     * @author karam mustafa
     */
    protected function checkIfInteger($index)
    {
        return is_int($this->getValues()[$index])
            || (is_float($this->getValues()[$index])
                && floatval($this->getValues()[$index]));
    }

    /**
     * loop through specific array and each iteration will execute by a callback.
     *
     * @param  array  $arr
     * @param  callable  $callback
     *
     * @return void
     * @author karam mustafa
     */
    protected function loopThrough($arr, $callback)
    {
        foreach ($arr as $key => $value) {
            $callback($key, $value);
        }
    }

    /**
     * loop through specific array and each iteration will execute by a callback.
     *
     * @param  callable  $callback
     *
     * @return void
     * @author karam mustafa
     */
    protected function disableAndEnableForeignChecks($callback)
    {
        $this->unshiftInQuery("SET FOREIGN_KEY_CHECKS=0;");

        if (is_callable($callback)) {
            $callback();
        }

        $this->appendToQuery("SET FOREIGN_KEY_CHECKS=1;");
    }

    /**
     * return the saved items.
     *
     * @return array
     * @author karam mustafa
     */
    public function done()
    {
        return $this->getSavedItems();
    }
}
