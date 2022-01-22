Delete helper
-----------
Suppose you want to drop multiple tables by their names in the database, you can do it with the following implementation.
```php

    QueryHelperFacade::deleteInstance()
        ->setTables(['posts', 'users' , 'comments']) // tables names.
        ->dropMultiTables()
        ->executeAll();

```
If you have a table that contains a large number of data (maybe millions of records)
and you want to delete everything contained in this table,
if you execute the command with one query,
this query will take a lot of time,
so this function divides the large query into more queries with an easy-to-use structure.
```php

    QueryHelperFacade::deleteInstance()
        ->setField('id') // Set the field that we will query on it.
        ->setAllowedWhereInQueryNumber(10) // Set the number that the query will delete each time
        ->setTableName('tests')
        ->deleteLargeData();

    // and you can implement your custom query by a callback.
    QueryHelperFacade::deleteInstance()
        ->setField('id') // Set the field that we will query on it.
        ->setAllowedWhereInQueryNumber(10) // Set the number that the query will delete each time.
        ->setTableName('tests')
        ->deleteLargeData(function ($table) {
            return $table->where('id', '<', 100)->pluck('id')->toArray();
        }); //  this will implement the delete process only on the result of this callback.
```
If you want to drop all tables from the database.
```php

    QueryHelperFacade::deleteInstance()
        ->prepareDataBaseTablesToDrop()
        ->executeAll();
```
