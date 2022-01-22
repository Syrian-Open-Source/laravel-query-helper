<?php


namespace SOS\QueryHelper\Classes;

use \Illuminate\Support\Str;

/**
 * Class JoinHelper
 *
 * @author karam mustafa
 * @package SOS\QueryHelper\Classes
 */
class JoinHelper extends BaseHelper
{

    /**
     *
     * @author karam mustafa
     * @var array
     */
    private $joinType = 'JOIN';
    /**
     *
     * @author karam mustafa
     * @var string
     */
    private $savedKey = 'joinsResults';

    /**
     *
     * @author karam mustafa
     * @var string
     */
    protected $selection = ['id'];

    /**
     * @return array
     * @author karam mustaf
     */
    public function getJoinType()
    {
        return $this->joinType;
    }

    /**
     * @param  string  $joinType
     *
     * @return JoinHelper
     * @author karam mustaf
     */
    public function setJoinType($joinType)
    {
        $this->joinType = $joinType;

        return $this;
    }

    /**
     * get the all inserted tables in tables property,
     * and map these tables, each table will join with the next table in the tables array.
     *
     * @param  bool  $saveItems
     *
     * @return JoinHelper
     * @throws \Exception
     * @author karam mustafa
     */
    public function buildJoin($saveItems = true)
    {
        // set the select query.
        $this->setQuery(
            sprintf("SELECT %s FROM %s %s",
                $this->getSelection(),
                $this->getTableName(),
                $this->getQuery()
            ));

        // change the selection status to isSelectStatus,
        // so we can execute this query as a raw statement.
        $this->setIsSelectStatus();
        // check if we want to execute the query and save the results.
        if ($saveItems) {
            // save this result in savedItems Property
            $this->setSavedItems([
                $this->savedKey => $this->executeAll()
            ]);
        }

        return $this;
    }

    /**
     * this function is take the second parameter and match the relation field with the first table.
     *
     * @param  string  $firstTable
     * @param  string  $secondTable
     *
     * @return string
     * @author karam mustafa
     *
     * @example if we have relation between users and posts
     * this function will return the following 'posts.user_id'
     */
    public function resolveRelation($firstTable, $secondTable)
    {
        return $secondTable.".".Str::singular($firstTable)."_".$this->getField();
    }

    /**
     * fast append the parameters to prepare join queries, instead of write all functions.
     *
     * @param  string  $mainTableName
     * @param  array  $selection
     * @param  array|string  $tables
     * @param  string  $joinTypes
     *
     * @return JoinHelper
     */
    public function fastJoin($mainTableName, $selection, $tables, $joinTypes = 'JOIN')
    {
        $tables = !is_array($tables) ? [$tables] : $tables;

        // this is a important step if the user has been execute multi fastJoin
        // because the clearAll function clear the default field.
        $this->setField('id');

        // build the join query
        $this->buildFastJoin($mainTableName, $selection, $tables, $joinTypes);

        // clear all parameter's, except the saved items.
        $this->clearAll();
        
        return $this;
    }

    /**
     * build the join query between tow tables.
     *
     * @param  string  $table
     *
     * @return JoinHelper
     * @author karam mustafa
     *
     * @example if we have main table that inserted by the setTableName function
     * and lets say that table is users
     * and we want to add join with posts table
     * so this function will build the following query
     * 'JOIN posts on posts.post_id = users.id'
     */
    public function addJoin($table)
    {
        $this->setQuery(sprintf(
            "%s %s %s on %s.%s = %s ",
            // get the previous query
            $this->getQuery(),
            // get the inserted join type
            $this->getJoinType(),
            // set the selection table
            $table,
            // get the main table
            $this->getTableName(),
            // get the relation filed, id for an example
            $this->getField(),
            // build the relation
            $this->resolveRelation($this->getTableName(), $table)
        ));

        return $this;
    }

    /**
     * build join statement.
     *
     * @param $mainTableName
     * @param $selection
     * @param $tables
     * @param $joinTypes
     *
     * @author karam mustafa
     */
    private function buildFastJoin($mainTableName, $selection, $tables, $joinTypes): void
    {
        $this->loopThrough($tables, function ($index, $table) use ($joinTypes, $selection, $mainTableName) {

            // set random key with custom convention for a saved item.
            $savedKey = $mainTableName.'_'.Str::random('4');

            // push the query result on saved items property
            // this make us can handle multi query statement
            $this->setSavedItems([

                $savedKey => $this->setTableName($mainTableName)
                    ->addJoin($table)
                    ->setSelection($selection)
                    ->setJoinType($joinTypes)
                    ->buildJoin(false)
                    ->executeAll()
            ]);

        });
    }
}
