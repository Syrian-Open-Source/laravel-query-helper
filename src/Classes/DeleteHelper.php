<?php

namespace SOS\QueryHelper\Classes;

use Illuminate\Support\Facades\DB;

/**
 * Class DeleteHelper
 *
 * @author karam mustafa
 * @package SOS\QueryHelper\Classes
 */
class DeleteHelper extends BaseHelper
{
    /**
     * drop multiple tables by their names in the database
     *
     * @return DeleteHelper
     * @author karam mustafa
     */
    public function dropMultiTables()
    {
        $tables = $this->tables;

        $query = "";

        foreach ($tables as $index => $table) {
            $query .= $index == 0 ? "`$table`" : ",`$table`";
        }

        $this->setQuery(sprintf("DROP TABLE %s;", $query));

        return $this;
    }

    /**
     * truncate multiple tables by their names in the database
     *
     * @return DeleteHelper
     * @author karam mustafa
     */
    public function truncateMultiTables()
    {
        $this->disableAndEnableForeignChecks(function () {
            foreach ($this->tables as $index => $table) {
                $this->appendToQuery("TRUNCATE $table;");
            }
        });

        return $this;
    }

    /**
     * this function is divide the process of deleting data into a number of queries,
     * instead of making a large query that may take longer.
     * and you can dump the index for each iteration in the checkIfQueryAllowed function.
     *
     * @param  \Closure|null  $callback
     *
     * @return \SOS\QueryHelper\Classes\DeleteHelper
     * @author karam mustafa
     */
    public function deleteLargeData(\Closure $callback = null)
    {
        $field = $this->getField();

        $table = DB::table($this->getTableName());

        $ids = isset($callback)
            ? $callback($table)
            : $table->pluck($field)->toArray();

        $this->checkIfQueryAllowed($ids, function ($items, $index) use ($field, $table) {
            return $table->whereIn($field, $items)->delete();
        });

        return $this;
    }

    /**
     * drop all tables in the database.
     *
     * @return DeleteHelper
     * @author karam mustafa
     */
    public function prepareDataBaseTablesToDrop()
    {
        $this->getAllTablesFromDatabase()
            ->dropMultiTables();

        return $this;
    }
}
