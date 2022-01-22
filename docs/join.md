Join helper
-----------
We provide some function we think that we can implement joins more faster and easier. 

```php
    // if you need to fast join you can use fastJoin function
    QueryHelperFacade::joinInstance()
        // first parameter is the table will fetch the data from it.
        // second parameter is the selection you want from the database.
        // third parameter is table to join with.
        ->fastJoin('users', ['users.id'], 'posts')
        // you can add more joins.
        // ->fastJoin('other_main_table', ['id'], 'table_to_join')
        // ->fastJoin('other_main_table', ['id'], 'table_to_join')
        // retrieve the query result 
        ->done();

    // if you want to specify all the options and functions,
    // join helper class has a strong declarative process,
    // and this process make you chose what you want to build.
    QueryHelperFacade::joinInstance()
        // set the main table that we will select.
        ->setTableName('users')
        // set the join type, the default value is 'JOIN'.
        ->setJoinType('LEFT JOIN')
        // set the select statement, the default value is ['id'].
        ->setSelection(['users.id'])
        // set the table that you want to join with.
        ->addjoin('tests')
        // build this join.
        ->buildJoin()
        // if you want to get the built query.
        ->getQuery()       
        //  return the result
        ->done();

```
