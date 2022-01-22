<?php


namespace SOS\QueryHelper\Classes;


class UpdateHelper extends BaseHelper
{
    
    /**
     * @var mixed
     */
    public $cases;

    /**
     * @return mixed
     */
    public function getCases()
    {
        return $this->cases;
    }

    /**
     * @param  mixed  $cases
     *
     * @return UpdateHelper|BaseHelper
     * @author karam mustafa
     */
    public function setCases($cases)
    {
        $this->cases = $cases;
        return $this;
    }

    /**
     * this function is execute update multiples rows using case and when statement in sql.
     *
     * @param  string  $key
     *
     * @return UpdateHelper|BaseHelper
     * @throws \Exception
     * @author karam mustafa
     */
    public function executeUpdateMultiRows($key = null)
    {
        try {
            if (isset($key)) {
                $this->setField($key);
            }

            // build a statement, then execute it.
            $this->buildStatement()->executeAll();

            return $this;

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * build query statement
     *
     * @return UpdateHelper|BaseHelper
     * @throws \Exception
     * @author karam mustafa
     */
    public function buildStatement()
    {
        try {
            $query = sprintf(
                "UPDATE %s SET %s =  CASE %s END WHERE id IN (%s)",
                // set the table name to update.
                $this->getTableName(),
                // set the field that we want to update.
                $this->getField(),
                // get the cases sql built statement.
                $this->getCases(),
                // get the ids to select only these items to update.
                $this->getIds()
            );

            $this->setQuery($query);

            return $this;

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * this function will build [when id = ? then ?] sql query statement.
     *
     * @return UpdateHelper|BaseHelper
     * @author karam mustafa
     */
    public function bindIdsWithValues()
    {
        $cases = [];

        foreach ($this->getIds() as $index => $id) {
            $val = $this->checkIfInteger($index)
                // if the index is an integer, then we get the value.
                ? $this->getValues()[$index]
                // else we check if the key dose not have any single quote.
                : '\''.str_replace("'", '"', $this->getValues()[$index]).'\'';

            $cases[] = "WHEN id = {$id} then ".$val;
        }

        $this->setIds(implode(',', $this->getIds()));

        $this->setCases(implode(' ', $cases));

        return $this;
    }

    /**
     * this function will reduce callback functions.
     *
     * @param $tableName
     * @param $ids
     * @param $vales
     * @param $column
     *
     * @return UpdateHelper|BaseHelper
     * @author karam mustafa
     */
    public function fastUpdate($tableName, $ids, $vales, $column)
    {
        $this->setTableName($tableName)
            ->setIds($ids)
            ->setValues($vales)
            ->setField($column)
            ->bindIdsWithValues()
            ->executeUpdateMultiRows();
        return $this;
    }
}
